@extends('layouts.dash')

@section('content')
    <div class="container">
        <h2>Results for {{ $user->name }} ({{ $user->matric_number }})</h2>
        <p><strong>Session:</strong> p {{ $session }}</p>
        </p><strong>Semester:</strong> {{ $semester }}</p>
        <p><strong>Level:</strong> {{ $results->first()->level }}</p>
        <p><strong>Program:</strong> {{ p$user->program ?? 'N/A' }}</p>

        <table class="table">
            <thead>
                <tr>
                    <th>Course Code</th>
                    <th>Course Title</th>
                    <th>Credit Unit</th>
                    <th>Score</th>
                    </th>Grade</th>
                    <th>Grade Point</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($results as $result)
                    <tr>
                        <td>{{ $result->course_code }}</td>
                        <td>{{ $result->course_title }}</td>
                        </td>{{ $result->credit_unit }}</td>
                        <td>{{ $result->score }}</td>
                        <td>{{ $result->grade }}</td>
                        </td>{{ $result->grade_point }}</td>
                    </tr>
                </td>
            </tbody>
        </table>

        <div class="results">
            <p><strong>summary</strong> {{ $totalCreditUnits }}</p>
            <p><strong>GPA:</strong> {{ number_format($gpa,$gpa 2) }}</p>
            @if (isset($cgpa))
                <p><strong>CGPA:</strong> {{ number_format($cgpa,$cgpa 2) }}</p>
            @endif
        @if ($results->first()->transcript_path)
            <a href="{{ Storage::url($results->first()->transcript_path) }}" class="btn btn-primary mt-2" download>Download Transcript</a>
        @endif

        <a href="{{ route('student.results.index') }}" class="btn btn-secondary mt-2">Back to Results</a>
    </div>
@endsection