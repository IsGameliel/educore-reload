<?php

namespace App\Http\Controllers;

use App\Imports\ResultsImport;
use App\Models\Result;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ResultController extends Controller
{
    // Removed constructor with middleware calls

    public function index(Request $request)
    {
        $query = Result::with('user');

        if (Auth::user()->usertype === 'student') {
            $query->where('user_id', Auth::id());
        }

        if ($request->has('user_id') && $request->user_id && Auth::user()->usertype !== 'student') {
            $query->where('user_id', $request->user_id);
        }
        if ($request->has('session') && $request->session) {
            $query->bySessionAndSemester($request->session, $request->semester ?? '');
        }
        if ($request->has('level') && $request->level) {
            $query->where('level', $request->level);
        }

        $results = $query->get();
        $students = User::where('usertype', 'student')->get();
        $view = Auth::user()->usertype === 'student' ? 'student.result.index' : 'admin.result.index';
        return view($view, compact('results', 'students'));
    }

    public function show($userId, $session, $semester)
    {
        $user = User::findOrFail($userId);
        if (Auth::user()->usertype === 'student' && Auth::id() !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $results = Result::bySessionAndSemester($session, $semester)
                        ->where('user_id', $userId)
                        ->get();

        if ($results->isEmpty()) {
            return redirect()->route('student.results.index')->with('error', 'No results found.');
        }

        $totalCreditUnits = $results->sum('credit_unit');
        $weightedSum = $results->sum(function ($result) {
            return $result->credit_unit * $result->grade_point;
        });
        $gpa = $totalCreditUnits > 0 ? round($weightedSum / $totalCreditUnits, 2) : 0;

        $allResults = Result::where('user_id', $userId)->get();
        $totalAllCreditUnits = $allResults->sum('credit_unit');
        $totalWeightedSum = $allResults->sum(function ($result) {
            return $result->credit_unit * $result->grade_point;
        });
        $cgpa = $totalAllCreditUnits > 0 ? round($totalWeightedSum / $totalAllCreditUnits, 2) : null;

        return view('student.result.show', compact('user', 'results', 'totalCreditUnits', 'gpa', 'cgpa', 'session', 'semester'));
    }

    public function create()
    {
        $students = User::where('usertype', 'student')->get();
        $departments = Department::all();
        return view('admin.result.create', compact('students', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'session' => 'required|string|max:255',
            'semester' => 'required|in:First,Second',
            'course_code' => 'required|string|max:255',
            'course_title' => 'required|string|max:255',
            'credit_unit' => 'required|integer|min:1',
            'score' => 'required|numeric|min:0|max:100',
            'department_id' => 'required|numeric|min:0',
        ]);

        $user = User::where('usertype', 'student')->findOrFail($request->user_id);
        $gradeData = Result::calculateGradeAndPoint($request->score);
        $result = Result::create([
            'user_id' => $request->user_id,
            'matric_number' => $user->matric_number,
            'session' => $request->session,
            'semester' => $request->semester,
            'level' => $user->level,
            'course_code' => $request->course_code,
            'course_title' => $request->course_title,
            'credit_unit' => $request->credit_unit,
            'score' => $request->score,
            'grade' => $gradeData['grade'],
            'grade_point' => $gradeData['grade_point'],
            'department_id' => $request->department_id,
        ]);

        $this->generateTranscript($result);

        return redirect()->route('admin.results.index')->with('success', 'Result added successfully.');
    }

    public function edit(Result $result)
    {
        $students = User::where('usertype', 'student')->get();
        return view('admin.result.edit', compact('result', 'students'));
    }

    public function update(Request $request, Result $result)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'session' => 'required|string|max:255',
            'semester' => 'required|in:First,Second',
            'course_code' => 'required|string|max:255',
            'course_title' => 'required|string|max:255',
            'credit_unit' => 'required|integer|min:1',
            'score' => 'required|numeric|min:0|max:100',
            'department_id' => 'required|numeric|min:0',
        ]);

        $user = User::where('usertype', 'student')->findOrFail($request->user_id);
        $gradeData = Result::calculateGradeAndPoint($request->score);
        $result->update([
            'user_id' => $request->user_id,
            'matric_number' => $user->matric_number,
            'session' => $request->session,
            'semester' => $request->semester,
            'level' => $user->level,
            'course_code' => $request->course_code,
            'course_title' => $request->course_title,
            'credit_unit' => $request->credit_unit,
            'score' => $request->score,
            'grade' => $gradeData['grade'],
            'grade_point' => $gradeData['grade_point'],
            'department_id' => $request->department_id,
        ]);

        $this->generateTranscript($result);

        return redirect()->route('admin.results.index')->with('success', 'Result updated successfully.');
    }

    public function upload()
    {
        $students = User::where('usertype', 'student')->get();
        return view('admin.result.upload', compact('students'));
    }

    public function storeUpload(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'file' => 'required|file|mimes:csv,xlsx|max:2048',
        ]);

        $user = User::where('usertype', 'student')->findOrFail($request->user_id);
        Excel::import(new ResultsImport($request->user_id), $request->file('file'));

        $results = Result::where('user_id', $request->user_id)
                        ->where('session', $request->input('session', ''))
                        ->where('semester', $request->input('semester', ''))
                        ->get();

        if ($results->isNotEmpty()) {
            $this->generateTranscript($results->first());
        }

        return redirect()->route('admin.results.index')->with('success', 'Results uploaded and transcript generated.');
    }

    protected function generateTranscript(Result $result)
    {
        $user = User::findOrFail($result->user_id);
        $results = Result::bySessionAndSemester($result->session, $result->semester)
                        ->where('user_id', $result->user_id)
                        ->get();
        $department = Department::find($user->department_id);
        $totalCreditUnits = $results->sum('credit_unit');
        $weightedSum = $results->sum(function ($res) {
            return $res->credit_unit * $res->grade_point;
        });
        $gpa = $totalCreditUnits > 0 ? round($weightedSum / $totalCreditUnits, 2) : 0;

        $allResults = Result::where('user_id', $result->user_id)->get();
        $totalAllCreditUnits = $allResults->sum('credit_unit');
        $totalWeightedSum = $allResults->sum(function ($res) {
            return $res->credit_unit * $res->grade_point;
        });
        $cgpa = $totalAllCreditUnits > 0 ? round($totalWeightedSum / $totalAllCreditUnits, 2) : null;

        $pdf = Pdf::loadView('documents.transcript', [
            'student' => $user,
            'results' => $results,
            'totalCreditUnits' => $totalCreditUnits,
            'gpa' => $gpa,
            'cgpa' => $cgpa,
            'department' => $department,
        ]);

        $transcriptName = "transcript_{$result->user_id}_{$result->session}_{$result->semester}_" . time() . '.pdf';
        $transcriptPath = 'documents/' . $transcriptName;
        Storage::disk('public')->put($transcriptPath, $pdf->output());

        Result::where('user_id', $result->user_id)
              ->where('session', $result->session)
              ->where('semester', $result->semester)
              ->update(['transcript_path' => $transcriptPath]);
    }
}
