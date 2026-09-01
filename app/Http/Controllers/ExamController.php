<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Models\Exam;
use Inertia\Inertia;

class ExamController extends Controller
{
    public function index()
    {
        return Inertia::render('exam/create');
    }

    public function list()
    {
        $availableExam = Exam::all()->map(fn (Exam $exam) => [
            ...$exam->toArray(),
            'is_open' => $exam->isOpen(),
        ]);

        return Inertia::render('exam/list', compact('availableExam'));
    }

    public function store(StoreExamRequest $request)
    {
        $exam = new Exam();
        $exam->fill($request->validated());
        // Il proprietario si assegna qui, mai da input: e' quello che rende
        // possibile distinguere in seguito il materiale di un docente da
        // quello di un altro.
        $exam->user_id = auth()->id();
        $exam->save();

        return back()->with('success', 'Esame aggiunto con successo.');
    }

    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);
        $this->authorize('delete', $exam);
        $exam->delete();

        // Reindirizza con un messaggio di successo
        return redirect()->route('exam.list')->with('success', 'Esame eliminato con successo.');
    }

    public function edit($id)
    {
        $exam = Exam::findOrFail($id);
        $this->authorize('update', $exam);

        return Inertia::render('exam/edit', [
            // Il form usa <input type="datetime-local">, che si aspetta
            // esattamente questo formato: senza, il campo non si precompilava
            // mai (bug preesistente, mai passato alcun valore alla view).
            'exam' => [
                ...$exam->toArray(),
                'startAt' => $exam->startAt->format('Y-m-d\TH:i'),
                'dueAt' => $exam->dueAt->format('Y-m-d\TH:i'),
            ],
        ]);
    }

    public function update(UpdateExamRequest $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $this->authorize('update', $exam);
        $exam->update($request->validated());

        // Reindirizza con un messaggio di successo
        return redirect()->route('exam.edit', $id)->with('success', 'Esame aggiornato con successo.');
    }


}
