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
        $request->all();
        $examId = $request->input('exam_id');
        //$quizId = $request->input('quiz_id');
        $quizId = $request->input('quiz_id');
        $exam = Exam::findOrFail($examId);
        $exam->quiz()->attach($quizId);

        return redirect()->route('examquiz.index')->with('success', 'Quiz aggiunto con successo all esame.');

    }

    public function quiz_list($examId)
    {
        $exam = Exam::find($examId);
        $quizzes= $exam->quiz()->get();

        return view('exam.quizlist', ['quizzes' => $quizzes]);
    }

    public function quiz_destroy($quizId)
    {
        $quizzes = Quiz::find($quizId);

        // Retrieve the library ID before detaching the quiz
        $examId = $quizzes->exam()->first()->id;
        $quizzes->exam()->detach();

        $exam = Exam::with('quiz')->find($examId);
        $quizzes = $exam->quiz;

        return view('exam.quizlist', ['quizzes' => $quizzes]);
    }




}
