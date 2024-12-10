<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Department;
use App\Models\Faculty;



class HomeController extends Controller
{
    public function index(){
        if(Auth::user()->usertype == 'student'){
            return view('student.dashboard');
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
