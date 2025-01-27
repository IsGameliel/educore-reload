<?php

namespace App\Http\Controllers;

use App\Models\Tests;
use App\Models\Questions;
use App\Models\Responses;
use App\Models\Department;
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
        $questions = $test->questions;

        // Ensure the question index is within range
        if ($questionIndex < 0 || $questionIndex >= $questions->count()) {
            return redirect()->route('student.test.index')->with('error', 'Invalid question index.');
        }

        $question = $questions[$questionIndex];
        $end_time = now()->addMinutes($test->duration)->toISOString();

        return view('student.test.start', compact('test', 'question', 'questionIndex', 'end_time'));
    }


    // public function storeAnswer(Request $request, $testId, $questionIndex)
    // {
    //     $test = Tests::with('questions')->findOrFail($testId);
    //     $questions = $test->questions;

    //     if ($questionIndex < 0 || $questionIndex >= $questions->count()) {
    //         return redirect()->route('student.tests.index')->with('error', 'Invalid question index.');
    //     }

    //     // Save the answer in the session
    //     $submittedAnswer = $request->input('answer');
    //     $currentAnswers = session("test_{$testId}_answers", []);
    //     $currentAnswers[$questions[$questionIndex]->id] = $submittedAnswer;
    //     session(["test_{$testId}_answers" => $currentAnswers]);

    //     $nextIndex = $questionIndex + 1;

    //     // If there are more questions, proceed to the next one
    //     if ($nextIndex < $questions->count()) {
    //         return redirect()->route('student.tests.start', [$testId, $nextIndex])
    //             ->with('success', 'Answer saved. Proceeding to the next question.');
    //     }

    //     // If it's the last question, proceed to submission
    //     return redirect()->route('tests.submit', [$testId]);
    // }

    public function storeAnswer(Request $request, $testId, $questionIndex = 0)
    {
        $test = Tests::findOrFail($testId);

        // Retrieve or initialize session answers
        $sessionKey = "test_{$testId}_answers";
        $answers = session($sessionKey, []);

        // Save the submitted answer for the current question
        $submittedAnswer = $request->input("answers.{$questionIndex}");
        if ($submittedAnswer === null) {
            return back()->with('error', 'Please select an answer before proceeding.');
        }

        // Update session answers
        $answers[$test->questions[$questionIndex]->id] = $submittedAnswer;
        session([$sessionKey => $answers]);

        // Log updated answers for debugging
        \Log::info('Answers updated:', ['answers' => $answers]);

        // Check if there are more questions
        $nextIndex = $questionIndex + 1;
        if ($nextIndex < $test->questions->count()) {
            return redirect()->route('tests.start', [$testId, $nextIndex])
                            ->with('success', 'Answer saved. Moving to the next question.');
        }

        // Redirect to submit the test
        return redirect()->route('tests.submit', [$testId]);
    }

    // Handle final submission of the test
    public function submitTest(Request $request, $testId)
    {
        $test = Tests::with('questions')->findOrFail($testId);

        // Retrieve answers from session
        $answers = session("test_{$testId}_answers", []);

        // Log the submitted answers for debugging
        \Log::info('Submitted Answers:', ['answers' => $answers]);

        $score = 0;

        foreach ($test->questions as $question) {
            $submittedAnswer = $answers[$question->id] ?? null;

            // Log question and answer for debugging
            \Log::info('Evaluating Question:', [
                'question_id' => $question->id,
                'submitted_answer' => $submittedAnswer,
                'correct_option' => $question->correct_option,
            ]);

            // Compare answers (ensure type consistency)
            if ((string)$submittedAnswer === (string)$question->correct_option) {
                $score += $question->marks; // Add the question's marks to the score
            }
        }

        // Log final score
        \Log::info('Final Score:', ['score' => $score]);

        // Save the result to the database
        Responses::create([
            'test_id' => $testId,
            'student_id' => auth()->id(),
            'answers' => json_encode($answers),
            'score' => $score,
        ]);

        // Clear session data for the test
        session()->forget("test_{$testId}_answers");

        // Redirect to the result view
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
        return view('admin.tests.create', compact('departments'));
    }

    public function store(Request $request)
    {
        Tests::create($request->validate([
            'name' => 'required|string',
            'subject' => 'required|string',
            'duration' => 'required|integer',
            'level' => 'required|string',
            'department_id' => 'required|exists:departments,id',
        ]));

        return redirect()->route('admin.tests.index')->with('success', 'Test created successfully');
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

    public function viewResponses($testId)
    {
        $test = Tests::findOrFail($testId);
        $responses = Responses::where('test_id', $testId)->with('student')->get();

        return view('admin.tests.responses', compact('test', 'responses'));
    }
}
