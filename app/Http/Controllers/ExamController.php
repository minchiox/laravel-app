<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
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

    public function store(Request $request)
    {
        $request->validate([
            'exam_name' => 'required',
            'startAt' => 'required',
            'dueAt' => 'required',
        ]);

        $input = $request->all();
        $exams= new Exam();
        $exams->fill($input);
        $exams-> save();

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

    public function update(Request $request, $id)
    {
        $request->validate([
            'exam_name' => 'required',
            // Aggiungi altre regole di validazione qui se necessario
        ]);

        $exam = Exam::findOrFail($id);
        $input = $request->all();


        // Salva le modifiche
        $exam->update($input);

        // Reindirizza con un messaggio di successo
        return redirect()->route('exam.edit', $id)->with('success', 'Exam updated successfully.');
    }


}
