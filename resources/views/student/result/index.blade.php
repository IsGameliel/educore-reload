@extends('layouts.dash')

@section('content')
    <div class="container">
        <h2>My Results</h2>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <table class="table">
            <thead>
                <tr>
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
                        <td>{{ $result->session }}</td>
                        <td>{{ $result->semester }}</td>
                        <td>{{ $result->level }}</td>
                        <td>{{ $result->course_code }}: {{ $result->course_title }}</td>
                        <td>{{ $result->score }}</td>
                        <td>{{ $result->grade }}</td>
                        <td>
                            <a href="{{ route('student.results.show', [$result->user_id, $result->session, $result->semester]) }}" class="btn btn-sm btn-primary">View</a>
                            @if ($result->transcript_path)
                                <td class="a href="{{ Storage::url($result->transcript_path) }}" class="btn btn-sm btn-success" download>Download Transcript</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection