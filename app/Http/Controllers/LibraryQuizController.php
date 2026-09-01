<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Library;
use App\Models\Quiz;

class LibraryQuizController extends Controller
{
    public function index(Library $library)
    {
        $quiz= $library->quiz()->get();
        $availableQuiz = Quiz::all();
       $availableLibraries = Library::all();


        return view('library.index', compact('library', 'quiz', 'availableQuiz','availableLibraries'));
    }

    public function store(Request $request)
    {
        $libraryId = $request->input('library_id');
        $quizId = $request->input('quiz_id');

        $library = Library::findOrFail($libraryId);

        // Verifica se il quiz è già associato alla libreria
        if (!$library->quiz()->where('quiz_id', $quizId)->exists()) {
            $library->quiz()->attach($quizId, ['created_at' => now()]);

            // Reindirizza l'utente alla route desiderata con un messaggio di successo
            return redirect()->route('libraryquiz.index')->with('success', 'Quiz aggiunto con successo alla libreria.');
        } else {
            // Quiz già associato alla libreria, ritorna con un messaggio di errore
            return redirect()->back()->with('error', 'Il quiz è già associato a questa libreria.');
        }
    }

    public function list()
    {
        $availableLibraries = Library::all();
        return view('library.list', compact('availableLibraries'));
    }

    public function quiz_list($libraryId)
    {
        $library = Library::findOrFail($libraryId);
        $quizzes= $library->quiz()->get();

        return view('library.quizlist', compact('library', 'quizzes'));
    }

    /**
     * `library()->detach()` senza argomenti scollegava il quiz da OGNI
     * libreria a cui era associato, non solo da questa: un quiz condiviso fra
     * piu' librerie spariva ovunque cancellandolo da una sola.
     */
    public function quiz_destroy($libraryId, $quizId)
    {
        $library = Library::findOrFail($libraryId);
        $library->quiz()->detach($quizId);

        $quizzes = $library->quiz()->get();

        return view('library.quizlist', compact('library', 'quizzes'));
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

        return response()->json(
            $library->quiz()->get(['quizzes.id', 'question', 'subject', 'difficulty', 'points'])
        );
    }

}
