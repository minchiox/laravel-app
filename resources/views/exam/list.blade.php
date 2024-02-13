@extends('auth.layouts')
@section('content')
    <div class="container mt-3">
        <h1>Exam List</h1>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="scrollable-div" style="max-height: 300px; overflow-y: auto;">

                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Start time:</th>
                            <th scope="col">End time:</th>
                            <th scope="col">Total_points:</th>
                            <th scope="col">Actions:</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($availableExam as $exam)
                            <tr>
                                <td>{{ $exam->exam_name }}</td>
                                <td>{{ $exam->startAt }}</td>
                                <td>{{ $exam->dueAt }}</td>
                                <td>{{ $exam->total_points }}</td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
