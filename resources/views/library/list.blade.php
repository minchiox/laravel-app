@extends('auth.layouts')
@section('content')
    <div class="container mt-3">
        <h1>Libraries List</h1>
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
                            <th scope="col">Subject</th>
                            <th scope="col">Created:</th>
                            <th scope="col">Actions:</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($availableLibraries as $library)
                            <tr>
                                <td>{{ $library->library_name }}</td>
                                <td>{{ $library->library_subject }}</td>
                                <td>{{ $library->created_at }}</td>
                                <td>
                                    <a href="{{ route ('library.quiz', $library->id) }}" class="btn btn-primary">{{ __('Quiz') }}</a>
                                    <a href="{{ route ('library.edit', $library->id) }}" class="btn btn-primary">{{ __('Edit') }}</a>
                                    <form action="{{ route('library.destroy', $library->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
