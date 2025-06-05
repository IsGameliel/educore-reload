@extends('layouts.dash')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-danger">
                        <h1>404 Not Found</h1>
                    </div>
                    <div class="card-body">
                        <p>{{ $exception->getMessage() ?: 'The requested resource was not found.' }}</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
