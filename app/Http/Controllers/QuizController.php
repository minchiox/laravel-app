<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::all();
        return view('quiz.quiz');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required',
        ]);

        $input = $request->all();
        $quiz = new Quiz();
        $quiz->fill($input);
        $quiz-> save();

        return back()->with('success', 'Quiz added successfully.');
    }


}
