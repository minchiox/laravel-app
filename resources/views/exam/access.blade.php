@extends('auth.layouts')
@section('content')
    <div class="container mt-3">
        <h1 class="card-title">{{ $exam->exam_name }}</h1>
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
                        <form id="form_user_answer" method="POST" action="{{ route('store.user.answer') }}" enctype="multipart/form-data">
                                @foreach ($quizzes as $quiz)
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $quiz->question }}</h5>
                                            @if($quiz->answer_text == null)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="answer{{ $quiz->id }}" id="answer-bool-true{{ $quiz->id }}" value="1" required checked>
                                                    <label class="form-check-label" for="answer-bool-true{{ $quiz->id }}">{{ __('True') }}</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="answer{{ $quiz->id }}" id="answer-bool-false{{ $quiz->id }}" value="0" required>
                                                    <label class="form-check-label" for="answer-bool-false{{ $quiz->id }}">{{ __('False') }}</label>
                                                </div>
                                            @else
                                                <div class="form-group">
                                                    <input class="form-control" type="text" name="answer_text{{ $quiz->id }}" id="answer-text{{ $quiz->id }}" value="{{ old('answer_text' . $quiz->id, '') }}" required>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                    @csrf
                            <button type="submit" class="btn btn-primary align-items-center">{{ __('Submit') }}</button>
                        </form>

                </div>
            </div>
        </div>
    </div>
@endsection
