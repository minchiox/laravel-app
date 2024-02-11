<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quiz;

class QuizController extends Controller
{
    /*public function index()
    {
        $quizzes = Quiz::all();
        return view('quiz.quiz');
    }*/
    public function create()
    {
        return view('quiz.quiz');
    }
    public function index(Request $request)
    {
        $query = Quiz::query();

        // Applica i filtri di ricerca
        if ($request->filled('question')) {
            $query->where('question', 'like', '%' . $request->input('question') . '%');
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->input('difficulty'));
        }

        if ($request->filled('subject')) {
            $query->where('subject', 'like', '%' . $request->input('subject') . '%');
        }

        $quizzes = $query->get();

        return view('quiz.index', compact('quizzes'));
    }
    public function search(Request $request)
    {
        $query = Quiz::query();

        // Applica i filtri di ricerca
        if ($request->filled('question')) {
            $query->where('question', 'like', '%' . $request->input('question') . '%');
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->input('difficulty'));
        }

        if ($request->filled('subject')) {
            $query->where('subject', 'like', '%' . $request->input('subject') . '%');
        }

        $quizzes = $query->get();

        return view('quiz.index', compact('quizzes'));
    }
    public function list()
    {
        $quizzes = Quiz::all();
        return view('quiz.index', ['quizzes' => $quizzes]);
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

    public function edit($id)
    {
        $quiz = Quiz::findOrFail($id);
        return view('quiz.edit', compact('quiz'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'question' => 'required',
            'answer-type' => 'required',
            // Aggiungi altre regole di validazione qui se necessario
        ]);

        $quiz = Quiz::findOrFail($id);
        //$quiz->question = $request->question;
        $input = $request->all();

        //Se la risposta è booleana
        if($request->filled('answer')){
            unset($input['answer_text']);
            $input['answer_text'] = null;

        }
        else
        {
            unset($input['answer']);
            $input['answer'] = null;
        }

        // Salva le modifiche
        $quiz->update($input);

        // Reindirizza con un messaggio di successo
        return redirect()->route('quiz.edit', $id)->with('success', 'Quiz updated successfully.');
    }
    public function destroy($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->delete();

        // Reindirizza con un messaggio di successo
        return redirect()->route('quiz.index')->with('success', 'Quiz deleted successfully.');
    }

}
