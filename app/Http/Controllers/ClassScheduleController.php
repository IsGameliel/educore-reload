<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassSchedule;
use App\Models\Department;
use App\Models\Courses;
use App\Models\User;

class ClassScheduleController extends Controller
{
    /**
     * Display a listing of the class schedules with optional filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $department = $request->get('department');
        $level = $request->get('level');
        $semester = $request->get('semester');

        // Eager load the 'lecturer' relationship
        $schedules = ClassSchedule::with('lecturer') // Ensure lecturer relationship is defined in the model
            ->when($department, fn($query) => $query->where('department', $department))
            ->when($level, fn($query) => $query->where('level', $level))
            ->when($semester, fn($query) => $query->where('semester', $semester))
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        return view('class_schedules.index', compact('schedules'));
    }

    /**
     * Show the form for creating a new class schedule.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $departments = Department::all();
        $levels = ['100', '200', '300', '400', '500'];
        $semesters = ['First Semester', 'Second Semester'];
        $courses = Courses::all();
        $lecturers = User::where('userType', 'lecturer')->get(); // Ensure userType for lecturers is 'lecturer'

        return view('class_schedules.create', compact('departments', 'levels', 'courses', 'lecturers', 'semesters'));
    }

    /**
     * Store a newly created class schedule in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'department' => 'required|string|max:255',
            'level' => 'required|string|max:255',
            'semester' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'lecturer_id' => 'required|exists:users,id',
            'day' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'required|string|max:255',
        ]);

        // Create the schedule
        ClassSchedule::create($validated);

        // Redirect to the schedule list with a success message
        return redirect()->route('class-schedules.index')->with('success', 'Schedule created successfully.');
    }

    /**
     * Show the details of a specific class schedule.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $schedule = ClassSchedule::with('lecturer')->findOrFail($id); // Eager load lecturer for optimization
        return view('class_schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing an existing class schedule.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $schedule = ClassSchedule::findOrFail($id);
        $departments = Department::all();
        $levels = ['100', '200', '300', '400', '500'];
        $semesters = ['First Semester', 'Second Semester'];
        $courses = Courses::all();
        $lecturers = User::where('userType', 'lecturer')->get();

        return view('class_schedules.edit', compact('schedule', 'departments', 'levels', 'semesters', 'courses', 'lecturers'));
    }

    /**
     * Update an existing class schedule.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'department' => 'required|string|max:255',
            'level' => 'required|string|max:255',
            'semester' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'lecturer_id' => 'required|exists:users,id',
            'day' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'required|string|max:255',
        ]);

        $schedule = ClassSchedule::findOrFail($id);
        $schedule->update($validated);

        return redirect()->route('class-schedules.index')->with('success', 'Schedule updated successfully.');
    }

    /**
     * Delete a class schedule from the database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $schedule = ClassSchedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('class-schedules.index')->with('success', 'Schedule deleted successfully.');
    }
}
