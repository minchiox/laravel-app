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
                        <form id="formexam" method="POST" action="{{ route('examquiz.store') }}" enctype="multipart/form-data">
                            @csrf
                            <label class='form-label' for="exam_id">Select Exam:</label>
                            <select class="form-select" name="exam_id" id="exam_id"> <!-- qui devo mettere $library->id tra 2 parentesi graffe -->
                                @foreach($availableExams as $exam)
                                    <option value="{{ $exam->id }}">{{ $exam->exam_name }}</option>
                                @endforeach
                            </select><br>

                            <label class='form-label' for="lib_id">Select Library:</label>
                            <select  class="form-select" name="lib_id" id="lib_id">
                                @foreach($availableLibraries as $lib)
                                    <option value="{{ $lib->id }}">{{ $lib->library_name }}</option>
                                @endforeach
                            </select><br>
                            <br>

                            <table class="table" id="quiz_table">
                                <thead>
                                <tr>
                                    <th scope="col">Question</th>
                                    <th scope="col">Difficulty</th>
                                    <th scope="col">Subject</th>
                                    <th scope="col">Points</th>
                                    <th scope="col">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr></tr>
                                </tbody>
                            </table>

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

        // Add event listener to detect changes in the library select element
        libSelect.addEventListener('change', function() {
            var selectedLibId = this.value;
            // Send an AJAX request to fetch quizzes associated with the selected library
            axios.get('/libraries/' + selectedLibId + '/quizzes')
                .then(function(response) {
                    console.log(response.data);
                    console.log(tableBody);
                    var quizzes = response.data;
                    var tableBody = document.querySelector('#quiz_table tbody');
                    // Cancella tutte le righe esistenti nella tabella tranne l'intestazione
                    while (tableBody.firstChild) {
                        tableBody.removeChild(tableBody.firstChild);
                    }
                    // Aggiungi le righe alla tabella per ciascun quiz
                    quizzes.forEach(function(quiz) {
                        var row = document.createElement('tr');

                        // Aggiungi la cella per la domanda
                        var questionCell = document.createElement('td');
                        questionCell.textContent = quiz.question;
                        row.appendChild(questionCell);

                        // Aggiungi la cella per la difficoltà
                        var difficultyCell = document.createElement('td');
                        difficultyCell.textContent = quiz.difficulty;
                        row.appendChild(difficultyCell);

                        // Aggiungi la cella per il soggetto
                        var subjectCell = document.createElement('td');
                        subjectCell.textContent = quiz.subject;
                        row.appendChild(subjectCell);

                        // Aggiungi la cella per i punti
                        var pointsCell = document.createElement('td');
                        pointsCell.textContent = quiz.points;
                        row.appendChild(pointsCell);

                        // Aggiungi la cella per le azioni (ad esempio, pulsante di eliminazione)
                        var actionsCell = document.createElement('td');
                        var addButton = document.createElement('button');
                       addButton.textContent = 'Add';
                        addButton.classList.add('btn', 'btn-primary');
                        // Assegna un gestore di eventi per gestire la cancellazione del quiz
                      addButton.addEventListener('click', function() {
                          var form = document.getElementById('formexam');

                          var quizIdInput = document.createElement('input');
                          quizIdInput.type = 'hidden';
                          quizIdInput.name = 'quiz_id'; // Assicurati che il nome del campo corrisponda a quello atteso dalla route
                          quizIdInput.value = quiz.id;
                          form.appendChild(quizIdInput);


                          form.submit();
                        });
                        actionsCell.appendChild(addButton);
                        row.appendChild(actionsCell);

                        // Aggiungi la riga alla tabella
                        tableBody.appendChild(row);
                    });
                })
                .catch(function(error) {
                    console.error('Error fetching quizzes:', error);
                });
        });
    });
</script>

