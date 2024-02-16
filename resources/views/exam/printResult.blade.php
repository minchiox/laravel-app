<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
@section('content')
    <div class="container mt-3">
        <h1 class="card-title"></h1>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="scrollable-div" style="max-height: 500px; overflow-y: auto;">

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
                    <form id="form_user_answer" method="POST" action="{{ route('display.users.answerP') }}" enctype="multipart/form-data">
                        <input class="form-control" type="hidden" name="exam_id" id="exam_id" value="{{ $exam->id }}">
                        <input class="form-control" type="hidden" name="user_id" id="user_id" value="{{ $userId }}">
                        @foreach ($quizzes as $quiz)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $quiz->question }}</h5>
                                    @foreach ($userAnswer as $userans)
                                        @if ($userans->quiz_id == $quiz->id)
                                            @if ($userans->answer_text == null)
                                                @if ($userans->answer == 1)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="answer{{ $quiz->id }}" id="answer-bool-true{{ $quiz->id }}" value="1" required checked>
                                                        <label class="form-check-label" for="answer-bool-true{{ $quiz->id }}">{{ __('True') }}</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="answer{{ $quiz->id }}" id="answer-bool-false{{ $quiz->id }}" value="0" required>
                                                        <label class="form-check-label" for="answer-bool-false{{ $quiz->id }}">{{ __('False') }}</label>
                                                    </div>
                                                @else
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="answer{{ $quiz->id }}" id="answer-bool-true{{ $quiz->id }}" value="1" required>
                                                        <label class="form-check-label" for="answer-bool-true{{ $quiz->id }}">{{ __('True') }}</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="answer{{ $quiz->id }}" id="answer-bool-false{{ $quiz->id }}" value="0" required checked>
                                                        <label class="form-check-label" for="answer-bool-false{{ $quiz->id }}">{{ __('False') }}</label>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="form-group">
                                                    <input class="form-control" type="text" name="answer_text{{ $quiz->id }}" id="answer-text{{ $quiz->id }}" value="{{ $userans->answer_text }}" required>
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach


                        @csrf
                        <button type="submit" class="btn btn-primary align-items-center">{{ __('Correct') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
<div class="container mt-5">
    @yield('content')
</div>
</body>
</html>
