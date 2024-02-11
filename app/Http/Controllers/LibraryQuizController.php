<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Library;
use App\Models\Quiz;

class LibraryQuizController extends Controller
{
    public function addingQuiz(Library $library)
    {
        $quiz= $library->quiz();
        $availableQuiz = Quiz::all();

        return view ('library.index', compact('library', 'quiz', 'availableQuiz'));
    }

    public function addQuizToLibrary(Request $request)
    {
       $request->validate([
           'id' => 'required',
        ]);

        $library = $request->all();

        $library->quiz()->attach($request->id);

        return view ('index', $library)->with('success', 'Quiz added successfully.');
    }
}
