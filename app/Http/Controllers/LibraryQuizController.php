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

        // Esegui le operazioni per aggiungere il quiz alla libreria
        // Ad esempio:
        $library = Library::findOrFail($libraryId);
        $library->quiz()->attach($quizId,['created_at' => now()]);

        // Reindirizza l'utente alla route desiderata con un messaggio di successo
        return redirect()->route('libraryquiz.index')->with('success', 'Quiz aggiunto con successo alla libreria.');
    }

    public function list()
    {
        $availableLibraries = Library::all();
        return view('library.list', compact('availableLibraries'));
    }

    public function quiz_list($libraryId)
    {
        //$quizzes = $library->quiz()->get();
        $library = Library::find($libraryId);
        $quizzes= $library->quiz()->get();

        return view('library.quizlist', ['quizzes' => $quizzes]);
    }

    public function quiz_destroy($quizId)
    {
        $quizzes = Quiz::find($quizId);

        // Retrieve the library ID before detaching the quiz
        $libraryId = $quizzes->library()->first()->id;
        $quizzes ->library()->detach();

        $library = Library::with('quiz')->find($libraryId);
        $quizzes = $library->quiz;

        return view('library.quizlist', ['quizzes' => $quizzes]);
    }

}
