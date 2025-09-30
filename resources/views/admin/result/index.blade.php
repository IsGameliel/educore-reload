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

                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.results.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="user_id" class="form-control">
                                    <option value="">All Students</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}" {{ request('user_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }}
                                        </option>
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

                    <!-- Actions -->
                    <div class="mb-3">
                        <a href="{{ route('admin.results.create') }}" class="btn btn-primary">Add Result</a>
                        <a href="{{ route('admin.results.upload') }}" class="btn btn-primary">Upload Results</a>

                        @if(request('session') && request('semester'))
                            <form method="POST" action="{{ route('admin.results.transcripts.bulk', [request('session'), request('semester')]) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">Generate All Transcripts for {{ request('session') }} {{ request('semester') }}</button>
                            </form>
                        @endif
                    </div>

                    <!-- Responsive Results Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Matric Number</th>
                                    <th>Session</th>
                                    <th>Semester</th>
                                    <th>Level</th>
                                    <th>Courses</th>
                                    <th>Transcript</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $groupedResults = $results->groupBy(function($item) {
                                        return $item->user_id . '_' . $item->session . '_' . $item->semester;
                                    });
                                @endphp

                                @foreach ($groupedResults as $group)
                                    @php
                                        $first = $group->first();
                                    @endphp
                                    <tr>
                                        <td>{{ $first->user->name }}</td>
                                        <td>{{ $first->matric_number }}</td>
                                        <td>{{ $first->session }}</td>
                                        <td>{{ $first->semester }}</td>
                                        <td>{{ $first->level }}</td>
                                        <td>
                                            <ul class="mb-0">
                                                @foreach ($group as $result)
                                                    <li>{{ $result->course_code }}: {{ $result->course_title }} ({{ $result->score }} / {{ $result->grade }})</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.results.transcript.generate', [$first->user_id, $first->session, $first->semester]) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    {{ $first->transcript_path ? 'Regenerate' : 'Generate' }}
                                                </button>
                                            </form>

                                            @if($first->transcript_path)
                                                <a href="{{ asset(ltrim($first->transcript_path, '/')) }}" 
                                                    target="_blank" 
                                                    class="btn btn-sm btn-info">
                                                    View Transcript
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.results.edit', [$first->user_id, $first->session, $first->semester]) }}" class="btn btn-sm btn-warning">Edit All</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- End responsive table -->

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
