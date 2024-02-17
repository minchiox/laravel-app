@extends('auth.layouts')
@section('content')
    <div class="container mt-3">
        <h1>Exam List</h1>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="scrollable-div" style="max-height: 300px; overflow-y: auto;">
                    {{ $exam->exam_name }}
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Actions:</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>
                                    @if(Auth::user()->isTeacher)
                                        <a href="{{ route ('display.users.answer', [ 'iduser' => $user->id, 'idexam' => $exam->id]) }}" class="btn btn-primary">{{ __('Result') }}</a>
                                        <a type="submit" target="_blank" href="{{ route('print.exam',['idexam' => $exam->id, 'iduser' => $user->id]) }}" class="btn btn-primary align-items-center">{{ __('Print') }}</a>
                                    @endif
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
