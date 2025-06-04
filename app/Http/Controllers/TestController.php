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
        $test = Tests::with('questions')->findOrFail($testId);

        // Check if already submitted
        $existing = Responses::where('test_id', $testId)
            ->where('student_id', auth()->id())
            ->first();

        if ($existing) {
            // Always show result if already submitted
            $score = $existing->score;
            return view('student.test.result', compact('test', 'score'))
                ->with('total_marks', $test->questions->sum('marks'));
        }

        $questions = $test->questions;

        // Ensure the question index is within range
        if ($questionIndex < 0 || $questionIndex >= $questions->count()) {
            // If answers exist in session, redirect to submit
            $answers = session("test_{$testId}_answers", []);
            if (!empty($answers)) {
                return redirect()->route('student.tests.submit', [$testId]);
            }
            // Otherwise, show a friendly message or redirect to index
            return redirect()->route('student.tests.index')->with('error', 'Invalid question index.');
        }

        $question = $questions[$questionIndex];
        $end_time = now()->addMinutes($test->duration)->toISOString();

        return view('student.test.start', compact('test', 'question', 'questionIndex', 'end_time'));
    }

   public function storeAnswer(Request $request, $testId, $questionIndex = 0)
    {
        \Log::info('storeAnswer started', ['testId' => $testId, 'questionIndex' => $questionIndex]);

        $test = Tests::findOrFail($testId);

        // Prevent retake: check if already submitted
        $existing = Responses::where('test_id', $testId)
            ->where('student_id', auth()->id())
            ->first();
        if ($existing) {
            return response()->json(['success' => false, 'message' => 'You have already taken this test.']);
        }

        // Retrieve or initialize session answers
        $sessionKey = "test_{$testId}_answers";
        $answers = session($sessionKey, []);

        // Log answers and incoming data
        \Log::info('Session answers:', ['answers' => $answers]);

        // Explicitly check the correct input key for the submitted answer
        $submittedAnswer = $request->input("answers.{$test->questions[$questionIndex]->id}");
        \Log::info('Submitted Answer:', ['submittedAnswer' => $submittedAnswer]);

        if ($submittedAnswer === null) {
            \Log::warning('No answer selected');
            return response()->json(['success' => false, 'message' => 'Please select an answer before proceeding.']);
        }

        // Update session answers
        $answers[$test->questions[$questionIndex]->id] = $submittedAnswer;
        session([$sessionKey => $answers]);

        \Log::info('Answers updated in session', ['answers' => $answers]);

        // Check if there are more questions
        $nextIndex = $questionIndex + 1;
        if ($nextIndex < $test->questions->count()) {
            $nextUrl = route('student.tests.start', [$testId, $nextIndex]);
            return response()->json(['success' => true, 'nextUrl' => $nextUrl]);
        }


        \Log::info('Test submission', ['testId' => $testId]);
        $submitUrl = route('student.tests.submit', [$testId]);
        return response()->json(['success' => true, 'nextUrl' => $submitUrl]);
    }



    // Handle final submission of the test
    public function submitTest(Request $request, $testId)
    {
        $test = Tests::with('questions')->findOrFail($testId);

        // Check if already submitted
        $existing = Responses::where('test_id', $testId)
            ->where('student_id', auth()->id())
            ->first();

        if ($existing) {
            // Show result if already submitted
            $score = $existing->score;
            return view('student.test.result', compact('test', 'score'))
                ->with('total_marks', $test->questions->sum('marks'));
        }

        // Retrieve answers from session
        $answers = session("test_{$testId}_answers", []);

        // If no answers in session and no previous submission, show error
        if (empty($answers)) {
            return redirect()->route('student.tests.index')->with('error', 'No answers found for submission.');
        }

        // Calculate score
        $score = 0;
        foreach ($test->questions as $question) {
            $submittedAnswer = $answers[$question->id] ?? null;
            if ((string)$submittedAnswer === (string)$question->correct_option) {
                $score += $question->marks;
            }
        }

        // Save the result to the database
        Responses::create([
            'test_id' => $testId,
            'student_id' => auth()->id(),
            'answers' => json_encode($answers),
            'score' => $score,
        ]);

        // Clear session data for the test
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

        $test->questions()->create([
            'question_text' => $request->question_text,
            'options' => $request->options,
            'correct_option' => $request->correct_option,
            'marks' => $request->marks,
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
