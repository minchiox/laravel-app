@extends('auth.layouts')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Edit Quiz') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('quiz.update', $quiz->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            @if (session('success'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="question" class="form-label">{{ __('Question') }}:</label>
                                    <input class="form-control" type="text" id="question" name="question" autofocus required value="{{ old('question', $quiz->question) }}">
                                    @error('question')
                                    <span role="alert" class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="answer" class="form-label">{{ __('Answer') }}:</label>
                                    <div id="inputToShow">
                                        <label class="form-check-label" for="answer-bool-true">{{ __('True') }}</label>
                                        <input class="form-check-input" type="radio" name="answer-bool" id="answer-bool-true" value="1" {{ old('answer-bool', $quiz->answer_bool) == '1' ? 'checked' : '' }} required>
                                        <br>
                                        <label class="form-check-label" for="answer-bool-false">{{ __('False') }}</label>
                                        <input class="form-check-input" type="radio" name="answer-bool" id="answer-bool-false" value="0" {{ old('answer-bool', $quiz->answer_bool) == '0' ? 'checked' : '' }} required>
                                    </div>
                                    @error('answer-bool')
                                    <span role="alert" class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">{{ __('Update Quiz') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript per mostrare l'input appropriato in base alla selezione dell'utente -->
    <script>
        document.getElementById('answer-bool-true').addEventListener('change', function() {
            var inputToShow = document.getElementById('inputToShow');

            // Mostra l'input solo se la risposta è True
            if (this.checked) {
                inputToShow.style.display = 'block';
            } else {
                inputToShow.style.display = 'none';
            }
        });
    </script>
@endsection
