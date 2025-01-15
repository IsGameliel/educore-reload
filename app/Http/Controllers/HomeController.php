<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\courseMaterial;
use App\Models\courses;
use App\Models\ClassSchedule;






class HomeController extends Controller
{
    public function index(){
        if(Auth::user()->usertype == 'student'){
            $user = Auth::user();
            $courseMaterialsCount = courseMaterial::where('level', $user->level)
                ->where('department_id', $user->department_id)->count();
            $courseCount = Courses::where('department_id', $user->department_id)
                ->where('level', $user->level)->count();

            $student = auth()->user();

            // Check if the student has a department field
            if (!$student->department) {
                return redirect()->back()->with('error', 'No department assigned to the student.');
            }

            // Fetch the class schedules for the student's department
            $schedules = ClassSchedule::with('lecturer')
                ->where('department_id', $student->department->id) // Match the student's department
                ->get()
                ->map(function ($schedule) {
                    $course = Courses::find($schedule->subject); // Assuming subject is a course ID
                    $schedule->subject = $course ? $course->title : 'Unknown Course'; // Replace ID with title
                    return $schedule;
                });
            return view('student.dashboard', compact('courseMaterialsCount', 'courseCount', 'schedules'));
        }
        elseif(Auth::user()->usertype == 'admin'){
            $studentsCount = User::where('usertype', 'student')->count();
            $departmentsCount = Department::count();
            $facultyCount = Faculty::count();
            return view('admin.dashboard', compact('studentsCount', 'departmentsCount', 'facultyCount'));
        }
        else{
            return view('dashboard');
        }
    }
}
