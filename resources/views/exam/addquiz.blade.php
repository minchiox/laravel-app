@extends('auth.layouts')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Exam Quiz') }}</div>

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

                    <div class="card-body">
                        <form method="POST" action="{{ route('examquiz.store') }}" enctype="multipart/form-data">
                            @csrf
                            <label class='form-label' for="exam_id">Select Exam:</label>
                            <select class="form-select" name="exam_id" id="exam_id"> <!-- qui devo mettere $library->id tra 2 parentesi graffe -->
                                @foreach($availableExams as $exam)
                                    <option value="{{ $exam->id }}">{{ $exam->exam_name }}</option>
                                @endforeach
                            </select><br>

                            <label class='form-label' for="lib_id">Select Quiz:</label>
                            <select  class="form-select" name="lib_id" id="lib_id">
                                @foreach($availableLibraries as $lib)
                                    <option value="{{ $lib->id }}">{{ $lib->library_name }}</option>
                                @endforeach
                            </select><br>

                            <label class='form-label' for="quiz_id">Select Quiz:</label>
                            <select  class="form-select" name="quiz_id" id="quiz_id">
                                @foreach($availableQuiz as $quiz)
                                    <option value="">Seleziona un quiz</option>
                                @endforeach
                            </select><br>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">{{ __('Add Quiz') }}</button>
                                </div>
                            </div>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var libSelect = document.getElementById('lib_id');
        var quizSelect = document.getElementById('quiz_id');

        // Add event listener to detect changes in the library select element
        libSelect.addEventListener('change', function() {
            var selectedLibId = this.value;
            // Send an AJAX request to fetch quizzes associated with the selected library
            axios.get('/libraries/' + selectedLibId + '/quizzes')
                .then(function(response) {
                    // Clear existing options
                    var quizzes = response.data;
                    // Ottieni il menu a discesa della quiz
                    var quizSelect = document.getElementById('quiz_id');
                    // Cancella tutte le opzioni esistenti nel menu a discesa della quiz
                    quizSelect.innerHTML = '';
                    // Aggiungi un'opzione predefinita al menu a discesa della quiz
                    var defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'Seleziona una quiz';
                    quizSelect.appendChild(defaultOption);
                    // Aggiungi le nuove opzioni del menu a discesa della quiz
                    // Populate the quiz select element with options based on the response
                    response.data.forEach(function(quiz) {
                        var option = document.createElement('option');
                        option.value = quiz.id;
                        option.textContent = quiz.question;
                        quizSelect.appendChild(option);
                    });
                })
                .catch(function(error) {
                    console.error('Error fetching quizzes:', error);
                });
        });
    });
</script>

