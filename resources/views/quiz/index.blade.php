@extends('auth.layouts')

@section('content')
    <!DOCTYPE html>
<html>
<head>
    <title>Lista Quiz</title>
</head>
<body>
<form action="{{ route('quiz.search') }}" method="GET">
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="search_question" class="form-label">{{ __('Question') }}</label>
            <input type="text" class="form-control" id="search_question" name="question">
        </div>
        <div class="col-md-4">
            <label for="search_difficulty" class="form-label">{{ __('Difficulty') }}</label>
            <select class="form-select" id="search_difficulty" name="difficulty">
                <option value="">{{ __('Select Difficulty') }}</option>
                <option value="easy">{{ __('Easy') }}</option>
                <option value="medium">{{ __('Medium') }}</option>
                <option value="hard">{{ __('Hard') }}</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="search_subject" class="form-label">{{ __('Subject') }}</label>
            <input type="text" class="form-control" id="search_subject" name="subject">
        </div>
    </div>
    <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
</form>

<div class="container mt-3">
    <h1>Lista Quiz</h1>
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
                        <th scope="col">Created at</th>
                        <th scope="col">Actions</th> <!-- Nuova colonna per le azioni -->
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($quizzes as $quiz)
                        <tr>
                            <td>{{ $quiz->question }}</td>
                            @if (!is_null($quiz['answer-text']))
                                <td>{{ $quiz['answer-text'] }}</td>
                            @else
                                <td>{{ $quiz['answer-bool'] ? 'True' : 'False' }}</td>
                            @endif
                            <td>{{ $quiz->difficulty }}</td>
                            <td>{{ $quiz->subject }}</td>
                            <td>{{ $quiz->created_at }}</td>
                            <td>
                                <a href="{{ route('quiz.edit', $quiz->id) }}" class="btn btn-primary">{{ __('Edit') }}</a>
                                <form action="{{ route('quiz.destroy', $quiz->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Elimina</button>
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
</body>
</html>
@endsection
