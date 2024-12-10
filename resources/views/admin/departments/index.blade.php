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
                    <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">Add New Department</a>
            
                    <!-- Table container for horizontal scrolling -->
                    <div style="overflow-x: auto; margin-top: 20px;">
                        <table class="table mt-3">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    {{-- <th>Description</th> --}}
                                    <th>Faculty</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departments as $department)
                                    <tr>
                                        <td>{{ $department->name }}</td>
                                        {{-- <td>{{ $department->description }}</td> --}}
                                        <td>{{ $department->faculty->name ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('admin.departments.edit', $department->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            <form action="{{ route('admin.departments.destroy', $department->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-container">
                        {{ $departments->links() }}                
                    </div>
                </div>
            </div>
            
        </div>
        </div>
    </div>
@endsection
