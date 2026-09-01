<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Library;
use App\Models\Quiz;
use Inertia\Inertia;

class LibraryQuizController extends Controller
{
    public function index(Library $library)
    {
        $this->authorize('update', $library);

        // Solo il materiale del docente che sta assemblando la libreria: non
        // ha senso poter agganciare un quiz di un collega.
        $availableQuiz = Quiz::where('user_id', auth()->id())->get();

        return Inertia::render('library/addquiz', [
            'library' => $library,
            'availableQuiz' => $availableQuiz,
        ]);
    }

    public function store(Request $request)
    {
        $libraryId = $request->input('library_id');
        $quizId = $request->input('quiz_id');

        $library = Library::findOrFail($libraryId);
        $this->authorize('update', $library);

        $quiz = Quiz::findOrFail($quizId);
        $this->authorize('view', $quiz);

        // Verifica se il quiz è già associato alla libreria
        if (!$library->quiz()->where('quiz_id', $quizId)->exists()) {
            $library->quiz()->attach($quizId, ['created_at' => now()]);

            // Reindirizza l'utente alla route desiderata con un messaggio di successo
            return redirect()->route('libraryquiz.index', $library->id)->with('success', 'Quiz aggiunto con successo alla libreria.');
        } else {
            // Quiz già associato alla libreria, ritorna con un messaggio di errore
            return redirect()->back()->with('error', 'Il quiz è già associato a questa libreria.');
        }
    }

    public function list()
    {
        $availableLibraries = Library::all();

        return Inertia::render('library/list', compact('availableLibraries'));
    }

    public function quiz_list($libraryId)
    {
        $library = Library::findOrFail($libraryId);
        // La view mostra la colonna "Answer": la risposta corretta.
        $this->authorize('view', $library);

        $quizzes = $library->quiz()->get();

        return Inertia::render('library/quizlist', [
            'library' => $library,
            // Quiz::$hidden esclude answer/answer_text dalla serializzazione
            // JSON: qui vanno riesposte, la pagina e' teacher-only.
            'quizzes' => $quizzes->makeVisible(['answer', 'answer_text']),
        ]);
    }

    /**
     * `library()->detach()` senza argomenti scollegava il quiz da OGNI
     * libreria a cui era associato, non solo da questa: un quiz condiviso fra
     * piu' librerie spariva ovunque cancellandolo da una sola.
     */
    public function quiz_destroy($libraryId, $quizId)
    {
        $library = Library::findOrFail($libraryId);
        $this->authorize('update', $library);

        $library->quiz()->detach($quizId);

        // Era un `return view(...)` diretto: non valido per il protocollo
        // Inertia, che su risposte non-GET si aspetta un redirect.
        return redirect()->back()->with('success', 'Quiz rimosso dalla libreria.');
    }

    /**
     * Alimenta la tabella di scelta quiz nella pagina "Add Quiz to Exam".
     *
     * Restituiva l'intero model, quindi anche `answer` e `answer_text`: le
     * risposte corrette erano scaricabili da qualunque utente autenticato.
     * Oltre al $hidden sul model, qui si selezionano esplicitamente le sole
     * colonne che servono alla tabella.
     */
    public function getQuizzes($libraryId)
    {
        $library = Library::findOrFail($libraryId);
        $this->authorize('view', $library);

        return response()->json(
            $library->quiz()->get(['quizzes.id', 'question', 'subject', 'difficulty', 'points'])
        );
    }

}
