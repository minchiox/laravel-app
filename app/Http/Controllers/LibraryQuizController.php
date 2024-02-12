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
}
