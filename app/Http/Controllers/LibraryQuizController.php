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
        $library = Library::find($libraryId);
        $quizzes= $library->quiz()->get();

        return view('library.quizlist', ['quizzes' => $quizzes]);
    }

    public function quiz_destroy($quizId)
    {
        $quizzes = Quiz::find($quizId);

        // Retrieve the library ID before detaching the quiz
        $libraryId = $quizzes->library()->first()->id;
        $quizzes->library()->detach();

        $library = Library::with('quiz')->find($libraryId);
        $quizzes = $library->quiz;

        return view('library.quizlist', ['quizzes' => $quizzes]);
    }

    public function getQuizzes($libraryId)
    {
        // Fetch quizzes associated with the selected library
        $library = Library::find($libraryId);
        $quizzes = $library->quiz()->get();

        return response()->json($quizzes);
    }

}
