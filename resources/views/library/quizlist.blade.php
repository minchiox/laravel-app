@extends('auth.layouts')

@section('content')
    <!DOCTYPE html>
<html>
<head>
    <title>Quiz List</title>
</head>
<body>
<div class="container mt-3">
    <h1>Quiz List</h1>
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="scrollable-div" style="max-height: 300px; overflow-y: auto;">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Question</th>
                        <th scope="col">Answer</th>
                        <th scope="col">Difficulty</th>
                        <th scope="col">Subject</th>
                        <th scope="col">Points</th>
                        <th scope="col">Created at</th>
                        <th scope="col">Actions</th> <!-- Nuova colonna per le azioni -->
                    </tr>
                    </thead>
                    <tbody>
                    @if($quizzes-> count()>0)
                        @foreach ($quizzes as $quiz)
                            <tr>
                                <td>{{ $quiz->question }}</td>
                                @if (!is_null($quiz['answer_text']))
                                    <td>{{ $quiz['answer_text'] }}</td>
                                @else
                                    <td>{{ $quiz['answer'] ? 'True' : 'False' }}</td>
                                @endif
                                <td>{{ $quiz->difficulty }}</td>
                                <td>{{ $quiz->subject }}</td>
                                <td>{{ $quiz->points }}</td>
                                <td>{{ $quiz->created_at }}</td>
                                <td>
                                    <form action="{{ route('library.quiz.destroy', $quiz->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td>
                                <label class="form-label">The library doesn't have Quiz!</label>
                            </td>
                        </tr>
                    </tbody>
                    @endif

                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
@endsection
