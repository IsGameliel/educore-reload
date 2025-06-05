@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-danger">
                        <h1>Error {{ $statusCode }}</h1>
                    </div>
                    <div class="card-body">
                        <p>{{ $exception->getMessage() ?: 'An unexpected error occurred.' }}</p>
                        <a href="{{ url()->previous() }}" class="btn btn-primary">Go Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
