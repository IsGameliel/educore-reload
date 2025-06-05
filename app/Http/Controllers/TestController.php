<?php

namespace App\Http\Controllers;

use App\Models\{
    Tests, Questions, Responses, Department, Courses
};
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $student = auth()->user();
        $tests = Tests::where('level', $student->level)
                  ->where('department_id', $student->department_id)
                  ->where('status', true)
                  ->get();
        return view('student.test.index', compact('tests'));
    }

    // Display a single question for the test (GET)
   public function startTest($testId, $questionIndex = 0)
    {
        \Log::info('startTest called', ['testId' => $testId, 'questionIndex' => $questionIndex]);

        $test = Tests::with('questions')->findOrFail($testId);

        // Check if already submitted
        $existing = Responses::where('test_id', $testId)
            ->where('student_id', auth()->id())
            ->first();

        if ($existing) {
            $score = $existing->score;
            return view('student.test.result', compact('test', 'score'))
                ->with('total_marks', $test->questions->sum('marks'));
        }

        $questions = $test->questions;

        // Check if question index is valid
        if (!is_numeric($questionIndex) || $questionIndex < 0 || $questionIndex >= $questions->count()) {
            \Log::info('Redirecting to confirmation page', ['testId' => $testId]);
            return view('student.test.confirm_submission', compact('test'));
        }

        $question = $questions[$questionIndex];
        $end_time = now()->addMinutes($test->duration)->toISOString();

        return view('student.test.start', compact('test', 'question', 'questionIndex', 'end_time'));
    }

public function storeAnswer(Request $request, $testId, $questionIndex = 0)
{
    \Log::info('storeAnswer started', [
        'testId' => $testId,
        'questionIndex' => $questionIndex,
        'request' => $request->all()
    ]);

    $test = Tests::with('questions')->findOrFail($testId);

    // Prevent retake
    $existing = Responses::where('test_id', $testId)
        ->where('student_id', auth()->id())
        ->first();
    if ($existing) {
        \Log::info('Test already submitted', ['testId' => $testId]);
        return response()->json(['success' => false, 'message' => 'You have already taken this test.']);
    }

    // Validate question index
    if (!is_numeric($questionIndex) || (int)$questionIndex < 0 || (int)$questionIndex >= $test->questions->count()) {
        \Log::warning('Invalid question index', ['questionIndex' => $questionIndex]);
        if ($questionIndex === 'submit') {
            \Log::info('Redirecting to submitTest due to submit index', ['testId' => $testId]);
            // Redirect to submitTest to handle the confirmation form submission
            return response()->json([
                'success' => false,
                'message' => 'Invalid submission attempt.',
                'nextUrl' => route('student.tests.submit', $testId)
            ]);
        }
        $nextIndex = $test->questions->count(); // Point to confirmation page
        return response()->json([
            'success' => true,
            'message' => 'No more questions.',
            'nextUrl' => route('student.tests.start', [$testId, $nextIndex])
        ]);
    }

    // Retrieve or initialize session answers
    $sessionKey = "test_{$testId}_answers";
    $answers = session($sessionKey, []);

    // Get submitted answer
    $submittedAnswer = $request->input("answers.{$test->questions[(int)$questionIndex]->id}");
    \Log::info('Submitted Answer:', ['submittedAnswer' => $submittedAnswer]);

    if ($submittedAnswer === null) {
        \Log::warning('No answer selected');
        return response()->json(['success' => false, 'message' => 'Please select an answer before proceeding.']);
    }

    // Update session answers
    $answers[$test->questions[(int)$questionIndex]->id] = $submittedAnswer;
    session([$sessionKey => $answers]);
    \Log::info('Answers updated in session', ['answers' => $answers]);

    // Check if there are more questions
    $nextIndex = (int)$questionIndex + 1;
    if ($nextIndex < $test->questions->count()) {
        $nextUrl = route('student.tests.start', [$testId, $nextIndex]);
        return response()->json(['success' => true, 'nextUrl' => $nextUrl]);
    }

    // Redirect to confirmation page
    \Log::info('Test ready for submission', ['testId' => $testId]);
    $submitUrl = route('student.tests.start', [$testId, $nextIndex]);
    return response()->json(['success' => true, 'nextUrl' => $submitUrl]);
}
    // Handle final submission of the test
    public function submitTest(Request $request, $testId)
    {
        \Log::info('submitTest called', ['testId' => $testId, 'request' => $request->all()]);

        $test = Tests::with('questions')->findOrFail($testId);

        // Check if already submitted
        $existing = Responses::where('test_id', $testId)
            ->where('student_id', auth()->id())
            ->first();

        if ($existing) {
            \Log::info('Test already submitted', ['testId' => $testId, 'studentId' => auth()->id()]);
            $score = $existing->score;
            return view('student.test.result', compact('test', 'score'))
                ->with('total_marks', $test->questions->sum('marks'));
        }

        // Retrieve answers from session
        $answers = session("test_{$testId}_answers", []);
        \Log::info('Session answers retrieved', ['answers' => $answers]);

        // If no answers, show error
        if (empty($answers)) {
            \Log::error('No answers found for submission', ['testId' => $testId]);
            return redirect()->route('student.tests.index')->with('error', 'No answers found for submission.');
        }

        // Calculate score
        $score = 0;
        foreach ($test->questions as $question) {
            $submittedAnswer = $answers[$question->id] ?? null;
            $correctOption = $question->correct_option;

            \Log::info('Comparing answer', [
                'question_id' => $question->id,
                'submitted_answer' => $submittedAnswer,
                'submitted_type' => gettype($submittedAnswer),
                'correct_option' => $correctOption,
                'correct_type' => gettype($correctOption),
                'options' => $question->options,
                'marks' => $question->marks
            ]);

            if ((string)$submittedAnswer === (string)$correctOption) {
                $score += $question->marks;
                \Log::info('Correct answer', ['question_id' => $question->id, 'marks_added' => $question->marks]);
            } else {
                \Log::warning('Incorrect answer', ['question_id' => $question->id]);
            }
        }

        \Log::info('Score calculated', ['score' => $score, 'total_marks' => $test->questions->sum('marks')]);

        // Save result
        Responses::create([
            'test_id' => $testId,
            'student_id' => auth()->id(),
            'answers' => json_encode($answers),
            'score' => $score,
        ]);

        // Clear session
        \Log::info('Clearing session data', ['sessionKey' => "test_{$testId}_answers"]);
        session()->forget("test_{$testId}_answers");

        // Show result
        return view('student.test.result', compact('test', 'score'))
            ->with('total_marks', $test->questions->sum('marks'));
    }
    // Admin: Create Test
    public function adminIndex()
    {
        $tests = Tests::all();
        return view('admin.tests.index', compact('tests'));
    }

    public function create()
    {
        $departments = Department::all();
        $courses = Courses::all();
        return view('admin.tests.create', compact('departments', 'courses'));
    }

    public function store(Request $request)
    {
        Tests::create($request->validate([
            'name' => 'required|string',
            'subject' => 'required|string',
            'duration' => 'required|integer',
            'level' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'status' => 'required|boolean',
        ]));

        return redirect()->route('admin.tests.index')->with('success', 'Test created successfully');
    }

    // Edit Test
    public function edit($id)
    {
        $test = Tests::findOrFail($id);
        $departments = Department::all();
        return view('admin.tests.edit', compact('test', 'departments'));
    }

    // Update Test
    public function update(Request $request, $id)
    {
        $test = Tests::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string',
            'subject' => 'required|string',
            'duration' => 'required|integer',
            'level' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'status' => 'required|boolean',
        ]);

        $test->update($validatedData);

        return redirect()->route('admin.tests.index')->with('success', 'Test updated successfully');
    }

    public function manageQuestions($testId)
    {
        $test = Tests::with('questions')->findOrFail($testId);
        return view('admin.tests.questions', compact('test'));
    }

    public function storeQuestions(Request $request, $testId)
    {
        $test = Tests::findOrFail($testId);

        $validated = $request->validate([
            'question_text' => 'required|string',
            'options' => 'required|array|min:2',
            'correct_option' => 'required|in:' . implode(',', array_keys($request->options)),
            'marks' => 'required|integer|min:1',
        ]);

        $test->questions()->create([
            'question_text' => $validated['question_text'],
            'options' => $validated['options'],
            'correct_option' => $validated['correct_option'],
            'marks' => $validated['marks'],
        ]);

        return back()->with('success', 'Question added successfully');
    }

    // Edit Question
    public function editQuestion($testId, $questionId)
    {
        $test = Tests::findOrFail($testId);
        $question = $test->questions()->findOrFail($questionId);

        return view('admin.tests.edit-question', compact('test', 'question'));
    }

    // Update Question
    public function updateQuestion(Request $request, $testId, $questionId)
    {
        $test = Tests::findOrFail($testId);
        $question = $test->questions()->findOrFail($questionId);

        $validatedData = $request->validate([
            'question_text' => 'required|string',
            'options' => 'required|array',
            'correct_option' => 'required|string|in:' . implode(',', array_keys($request->options)),
            'marks' => 'required|integer',
        ]);

        $question->update($validatedData);

        return redirect()->route('admin.tests.questions', $testId)->with('success', 'Question updated successfully');
    }


    public function viewResponses($testId)
    {
        $test = Tests::findOrFail($testId);
        $responses = Responses::where('test_id', $testId)->with('student')->get();

        return view('admin.tests.responses', compact('test', 'responses'));
    }
}
