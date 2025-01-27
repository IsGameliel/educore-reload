@extends('layouts.dash')

@section('content')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title">
                    <span class="page-title-icon bg-gradient-primary text-white me-2">
                        <i class="mdi mdi-home"></i>
                    </span> Course Registration
                </h3>
                <nav aria-label="breadcrumb">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">
                            <span></span>Register for Courses <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Registration Form -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('student.courses.register') }}" method="POST">
                        @csrf <!-- CSRF token for form submission -->

                        <!-- Semester Field -->
                        <div class="form-group">
                            <label for="semester">Select Semester:</label>
                            <select name="semester" id="semester" class="form-control" required>
                                <option value="First">First</option>
                                <option value="Second">Second</option>
                                <!-- Add other semesters as needed -->
                            </select>
                        </div>

                        <!-- Level Field -->
                        <div class="form-group">
                            <label for="level">Select Level:</label>
                            <select name="level" id="level" class="form-control" required>
                                <option value="100">100 Level</option>
                                <option value="200">200 Level</option>
                                <option value="300">300 Level</option>
                                <option value="400">400 Level</option>
                                <!-- Add other levels as needed -->
                            </select>
                        </div>

                        <!-- Courses Field -->
                        <div class="form-group">
                            <label for="courses">Select Courses:</label>
                            <select name="course_ids[]" id="courses" class="form-control" multiple required>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">
                                        {{ $course->title }} ({{ $course->credit_unit }} credits)
                                        @if($course->prerequisites->isNotEmpty())
                                            - Prerequisite:
                                            @foreach($course->prerequisites as $prerequisite)
                                                {{ $prerequisite->title }}{{ !$loop->last ? ',' : '' }}
                                            @endforeach
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-gradient-primary">Register for Selected Courses</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        </div>
    </div>

@endsection
