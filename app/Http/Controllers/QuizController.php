<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Models\Quiz;

class QuizController extends Controller
{
    public function create()
    {
        return view('quiz.quiz');
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
    public function store(StoreQuizRequest $request)
    {
        $quiz = new Quiz();
        $quiz->fill($this->normalizeAnswer($request->validated()));
        $quiz->save();

        return back()->with('success', 'Quiz added successfully.');
    }

    public function edit($id)
    {
        $quiz = Quiz::findOrFail($id);
        return view('quiz.edit', compact('quiz'));
    }

    public function update(UpdateQuizRequest $request, $id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->update($this->normalizeAnswer($request->validated()));

        // Reindirizza con un messaggio di successo
        return redirect()->route('quiz.edit', $id)->with('success', 'Quiz updated successfully.');
    }

    public function destroy($id)
    {
        $quiz = Quiz::findOrFail($id);
        $quiz->delete();

        // Reindirizza con un messaggio di successo
        return redirect()->route('quiz.list')->with('success', 'Quiz deleted successfully.');
    }

    /**
     * 'answer-type' e' un controllo di form, non una colonna: sceglie quale
     * delle due risposte e' quella valida e azzera l'altra. Senza, passare un
     * quiz da vero/falso a risposta aperta (o viceversa) lasciava anche il
     * valore del tipo precedente, violando l'invariante "o answer o
     * answer_text" su cui si basa la correzione.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    private function normalizeAnswer(array $input): array
    {
        if ($input['answer-type'] === 'open') {
            $input['answer'] = null;
        } else {
            $input['answer_text'] = null;
        }

        unset($input['answer-type']);

        return $input;
    }
}
