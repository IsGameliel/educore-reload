@extends('layouts.dash')

@section('content')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title">
                    <span class="page-title-icon bg-gradient-primary text-white me-2">
                        <i class="mdi mdi-home"></i>
                    </span> Students Results Management
                </h3>
                <nav aria-label="breadcrumb">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">
                            <span></span>View Students Results <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Registration Form -->
            <div class="card">
                <div class="card-body">
                        <div class="container">
                            <h2>Manage Student Results</h2>
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <form method="GET" action="{{ route('admin.results.index') }}" class="mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <select name="user_id" class="form-control">
                                            <option value="">All Students</option>
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}" {{ request('user_id') == $student->id ? 'selected' : '' }}>{{ $student->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="session" class="form-control" placeholder="Session (e.g., 2023/2024)" value="{{ request('session') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="semester" class="form-control">
                                            <option value="">All Semesters</option>
                                            <option value="First" {{ request('semester') == 'First' ? 'selected' : '' }}>First</option>
                                            <option value="Second" {{ request('semester') == 'Second' ? 'selected' : '' }}>Second</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="level" class="form-control" placeholder="Level (e.g., 200)" value="{{ request('level') }}">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mt-2">Filter</button>
                            </form>
                            <a href="{{ route('admin.results.create') }}" class="btn btn-primary mb-3">Add Result</a>
                            <a href="{{ route('admin.results.upload') }}" class="btn btn-primary mb-3">Upload Results</a>

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Matric Number</th>
                                        <th>Session</th>
                                        <th>Semester</th>
                                        <th>Level</th>
                                        <th>Course</th>
                                        <th>Score</th>
                                        <th>Grade</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($results as $result)
                                        <tr>
                                            <td>{{ $result->user->name }}</td>
                                            <td>{{ $result->matric_number }}</td>
                                            <td>{{ $result->session }}</td>
                                            <td>{{ $result->semester }}</td>
                                            <td>{{ $result->level }}</td>
                                            <td>{{ $result->course_code }}: {{ $result->course_title }}</td>
                                            <td>{{ $result->score }}</td>
                                            <td>{{ $result->grade }}</td>
                                            <td>
                                                <a href="{{ route('admin.results.edit', $result) }}" class="btn btn-sm btn-warning">Edit</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </div>
        </div>
    </div>
@endsection
