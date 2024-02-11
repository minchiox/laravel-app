@extends('auth.layouts')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Quiz Maker') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('quiz.store') }}" enctype="multipart/form-data">
                            @csrf

                            @if (session('success'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="question" class="form-label">{{ __('Question') }}:</label>
                                    <input class="form-control" type="text" id="question" name="question" autofocus required>
                                    @error('question')
                                    <span role="alert" class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="answer-type" class="form-label">{{ __('Answer Type') }}:</label>
                                    <select name="answer-type" class="form-select" id="answer-type" required>
                                        <option value="">{{ __('Select an option') }}</option>
                                        <option value="open">{{ __('Open Answer') }}</option>
                                        <option value="close">{{ __('Close Answer') }}</option>
                                    </select>
                                    @error('answer-type')
                                    <span role="alert" class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="answer" class="form-label">{{ __('Answer') }}:</label>
                                    <div id="inputToShow"></div>
                                    @error('answer')
                                    <span role="alert" class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="subject" class="form-label">{{ __('Subject') }}:</label>
                                    <input class="form-control" type="text" id="subject" name="subject" autofocus required>
                                    @error('subject')
                                    <span role="alert" class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="difficulty" class="form-label">{{ __('Difficulty') }}:</label>
                                    <select name="difficulty" class="form-select" id="difficulty" required>
                                        <option value="" >{{ __('Select an option') }}</option>
                                        <option value="easy">{{ __('Easy') }}</option>
                                        <option value="medium">{{ __('Medium') }}</option>
                                        <option value="hard" >{{ __('Hard') }}</option>
                                    </select>
                                    @error('question')
                                    <span role="alert" class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="point" class="form-label">{{ __('Points') }}:</label>
                                    <input class="form-control" type="number" id="points" name="points" autofocus required>
                                    @error('points')
                                    <span role="alert" class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">{{ __('Upload new Quiz') }}</button>
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
        document.getElementById('answer-type').addEventListener('change', function() {
            var answertype = this.value;
            var inputToShow = document.getElementById('inputToShow');

            // Aggiunge l'input appropriato in base alla selezione
            if (answertype === 'open') {
                inputToShow.innerHTML = '<input class="form-control" type="text" name="answer_text" id="answer-text" required>';
            } else if (answertype === 'close') {
                inputToShow.innerHTML = '<label class="form-check-label" for="answer-bool-true">{{ __('True') }}</label>' +
                    '<input class="form-check-input" type="radio" name="answer" id="answer-bool-true" value="1" required> ' +
                    '<br>' +
                    '<label class="form-check-label" for="answer-bool-false">{{ __('False') }}</label>' +
                    '<input class="form-check-input" type="radio" name="answer" id="answer-bool-false" value="0" required>';
            } else {
                // Non mostra nulla se non è selezionata nessuna opzione
                inputToShow.innerHTML = '';
            }
        });
    </script>
@endsection
