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
                        <span></span> Register for Courses
                        <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Registration Form -->
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <h1>Test Results</h1>
                    <p>Test Name: {{ $test->name }}</p>
                    <p>Subject: {{ $test->subject }}</p>
                    <p>Your Score: {{ $score }} / {{ $total_marks }}</p>

                    <!-- Conditional Styling for Score -->
                    <div class="alert {{ $score >= $total_marks * 0.5 ? 'alert-success' : 'alert-danger' }}">
                        {{ $score >= $total_marks * 0.5 ? 'Great job! You passed.' : 'Unfortunately, you did not pass. Try again!' }}
                    </div>

                    <!-- Action Buttons -->
                    <a href="" class="btn btn-warning">Retake Test</a>
                    <a href="" class="btn btn-primary">Go Back to Course Registration</a>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection
