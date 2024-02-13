<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Quiz;
use App\Models\Library;
class ExamQuizController extends Controller
{
    public function index(Exam $exam)
    {
        $quiz= $exam->quiz()->get();
        $availableQuiz = Quiz::all();
        $availableExams = Exam::all();
        $availableLibraries = Library::all();

        return view('exam.addquiz', compact('exam', 'quiz', 'availableQuiz','availableExams','availableLibraries'));
    }

    public function store(Request $request)
    {
        $examId = $request->input('exam_id');
        $quizId = $request->input('quiz_id');

        $exam = Exam::findOrFail($examId);

        // Verifica se il quiz è già associato all'esame
        if (!$exam->quiz()->where('quiz_id', $quizId)->exists()) {
            $exam->quiz()->attach($quizId, ['created_at' => now()]);

            // Reindirizza l'utente alla route desiderata con un messaggio di successo
            return redirect()->route('examquiz.index')->with('success', 'Quiz aggiunto con successo alla libreria.');
        } else {
            // Quiz già associato alla libreria, ritorna con un messaggio di errore
            return redirect()->back()->with('error', 'Il quiz è già associato a questa libreria.');
        }
    }



}
