<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;
use App\Models\Exam;

class ExamController extends Controller
{
    public function index()
    {
        return view('exam.create');
    }
    public function list()
    {
        $availableExam = Exam::all();

        return view('exam.list', compact('availableExam'));
    }

    public function store(StoreExamRequest $request)
    {
        $exam = new Exam();
        $exam->fill($request->validated());
        $exam->save();

        return back()->with('success', 'Exam added successfully.');
    }

    public function destroy($id)
    {
        $exam = Exam::findOrFail($id);
        $exam->delete();

        // Reindirizza con un messaggio di successo
        return redirect()->route('exam.list')->with('success', 'Exam deleted successfully.');
    }

    public function edit($id)
    {
        $exam = Exam::findOrFail($id);
        return view('exam.edit', compact('exam'));
    }

    public function update(UpdateExamRequest $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $exam->update($request->validated());

        // Reindirizza con un messaggio di successo
        return redirect()->route('exam.edit', $id)->with('success', 'Exam updated successfully.');
    }


}
