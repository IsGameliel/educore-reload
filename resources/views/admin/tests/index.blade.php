@extends('layouts.dash')

@section('content')

    <div class="main-panel">
        <div class="content-wrapper">
            <div class="page-header">
                <h3 class="page-title">
                    <span class="page-title-icon bg-gradient-primary text-white me-2">
                        <i class="mdi mdi-home"></i>
                    </span> Departments
                </h3>
                <nav aria-label="breadcrumb">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">
                            <span></span>View Department <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Registration Form -->
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('admin.tests.create') }}" class="btn btn-primary mb-4">Create New Test</a>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Subject</th>
                                <th>Duration (mins)</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tests as $test)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $test->name }}</td>
                                    <td>{{ $test->subject }}</td>
                                    <td>{{ $test->duration }}</td>
                                    <td>{{ $test->status ? 'Active' : 'Inactive' }}</td>
                                    <td>
                                        <a href="{{ route('admin.tests.questions', $test->id) }}" class="btn btn-info btn-sm">Manage Questions</a>
                                        <a href="{{ route('admin.tests.responses', $test->id) }}" class="btn btn-secondary btn-sm">View Responses</a>
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
@endsection
