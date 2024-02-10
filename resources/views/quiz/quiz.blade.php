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
                                <div class="alert alert-success" role="alert" class="text-danger">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="row mb-0">
                                <div class="col-md-12 ">
                                    <label for="question" class="form-label">Question: </label>
                                    <input class="form-control" type="text" id="question" name="question" autofocus="" >
                                    @error('question')
                                    <span role="alert" class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-12">
                                    <label for="answer-type" class="form-label">Answer Type: </label>
                                    <select name="answer-type" class="form-label" id="answer-type" autofocus="">
                                        <option value="">Select an option</option>
                                        <option value="open">Open Answer</option>
                                        <option value="close">Close Answer</option>
                                    </select>
                                    @error('answer-type')
                                    <span role="alert" class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-12">
                                    <label for="answer" class="form-label">Answer:</label>
                                    @error('answer')
                                    <span role="alert" class="text-danger">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-12" id="inputToShow">
                                    <!-- guarda la funzione di javascrpit sotto -->
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-12 offset-md-5">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Upload new Quiz') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- funzione di javascrpit per mostrare a schermo le opzioni di risposta alla domanda -->
    <script>
        document.getElementById('answer-type').addEventListener('change', function() {
            var answertype = this.value;
            var inputToShow = document.getElementById('inputToShow');

            // Aggiunge l'input appropriato in base alla selezione
            if (answertype === 'open') {
                inputToShow.innerHTML = '<input class="form-control" type="text" name="answer-text" id="answer-text">';
                //se non è una risposta aperta allora mostrerà i radio button per risposta vera o falsa
            } else if (answertype === 'close') {
                inputToShow.innerHTML = '<label class="form-label" for="true">True</label>' +
                    '<input type="radio" name="answer" id="answer-bool" value="1"> ' +
                    '<br>' +
                   ' <label class="form-label" for="false">False</label>' +
                     '<input  type="radio" name="answer" id="answer-bool" value="0">';

            }else  {
                // nel caso in cui non dovesse essere selezionata nessuna opzione non mostrerà nulla per gestire gli errori
                inputToShow.innerHTML = '';
            }
        });
    </script>
@endsection
