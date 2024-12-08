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
                @if($courses->isEmpty())
                    <div class="alert alert-warning">No courses registered for the selected semester.</div>
                @else
                    <table class="table table-bordered mt-4">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Credit Unit</th>
                                <th>Semester</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                                <tr>
                                    <td>{{ $course['course_code'] }}</td>
                                    <td>{{ $course['course_title'] }}</td>
                                    <td>{{ $course['credit_unit'] }}</td>
                                    <td>{{ $course['semester'] }}</td>
                                    <td>{{ ucfirst($course['status']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-3">
                        <a href="{{ route('student.courses.download.pdf', ['semester' => $semester]) }}" class="btn btn-primary">
                            Download PDF
                        </a>

                        <a href="{{ route('student.courses.download.excel', ['semester' => $semester]) }}" class="btn btn-success">
                            Download Excel
                        </a>
                    </div>
                @endif
                </div>
            </div>
        </div>
        </div>
    </div>

@endsection
