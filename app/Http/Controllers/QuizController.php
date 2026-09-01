<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Models\Quiz;
use Inertia\Inertia;

class QuizController extends Controller
{
    public function create()
    {
        return Inertia::render('quiz/create');
    }

    public function search(Request $request)
    {
        // Solo i quiz del docente che cerca: la colonna "Answer" della view
        // e' la risposta corretta, non e' materiale da poter sfogliare tra
        // docenti diversi.
        $query = Quiz::where('user_id', auth()->id());
        $filters = $request->only(['question', 'difficulty', 'subject']);

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

        return Inertia::render('quiz/index', [
            // Quiz::$hidden esclude answer/answer_text dalla serializzazione
            // JSON (Inertia la rispetta, a differenza dell'accesso Blade via
            // proprieta'): qui vanno riesposte, la pagina e' teacher-only e
            // mostra la risposta corretta di proposito.
            'quizzes' => $quizzes->makeVisible(['answer', 'answer_text']),
            'filters' => $filters,
        ]);
    }

    public function list()
    {
        $quizzes = Quiz::where('user_id', auth()->id())->get();

        return Inertia::render('quiz/index', [
            'quizzes' => $quizzes->makeVisible(['answer', 'answer_text']),
            'filters' => [],
        ]);
    }

    public function store(StoreQuizRequest $request)
    {
        $quiz = new Quiz();
        $quiz->fill($this->normalizeAnswer($request->validated()));
        $quiz->user_id = auth()->id();
        $quiz->save();

        return back()->with('success', 'Quiz aggiunto con successo.');
    }

    public function edit($id)
    {
        $quiz = Quiz::findOrFail($id);
        $this->authorize('update', $quiz);

        return Inertia::render('quiz/edit', [
            'quiz' => $quiz->makeVisible(['answer', 'answer_text']),
        ]);
    }

    public function update(UpdateQuizRequest $request, $id)
    {
        $quiz = Quiz::findOrFail($id);
        $this->authorize('update', $quiz);
        $quiz->update($this->normalizeAnswer($request->validated()));

        // Reindirizza con un messaggio di successo
        return redirect()->route('quiz.edit', $id)->with('success', 'Quiz aggiornato con successo.');
    }

    public function destroy($id)
    {
        $quiz = Quiz::findOrFail($id);
        $this->authorize('delete', $quiz);
        $quiz->delete();

        // Reindirizza con un messaggio di successo
        return redirect()->route('quiz.list')->with('success', 'Quiz eliminato con successo.');
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
