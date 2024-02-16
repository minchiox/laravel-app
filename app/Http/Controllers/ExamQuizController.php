<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Exam;
use App\Models\Quiz;
use App\Models\Library;
use App\Models\UserAnswer;
use App\Models\User;

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
        $quizId = $request->input('quiz_id');
        $exam = Exam::findOrFail($examId);
        //$exam->quiz()->attach($quizId);
        if (!$exam->quiz()->where('quiz_id', $quizId)->exists()) {
            $exam->quiz()->attach($quizId, ['created_at' => now()]);
            $totalPoints = $exam->quiz()->sum('points');
            $exam->total_points = $totalPoints;
            $exam->save();

            // Reindirizza l'utente alla route desiderata con un messaggio di successo
            return redirect()->route('examquiz.index')->with('success', 'Quiz aggiunto con successo all\'esame.');
        } else {
            // Quiz già associato alla libreria, ritorna con un messaggio di errore
            return redirect()->back()->with('error', 'Il quiz è già associato a questo esame.');
        }
        //return redirect()->route('examquiz.index')->with('success', 'Quiz aggiunto con successo all esame.');
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

    public function access($examId)
    {
        $exam = Exam::find($examId);
        $now = now();
        if ($now < $exam->startAt || $now > $exam->dueAt) {
            return redirect()->back()->with('error', 'L\'esame non è al momento disponibile.');
        }

        $user = Auth::user();
        if ($exam->user()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('error', 'Hai già partecipato a questo esame.');
        }

        $quizzes = $exam->quiz()->get();

        $exam->user()->attach($user->id);
        return view('exam.access', compact('quizzes','exam'));
    }

    public function storeUserAnswers(Request $request)
    {
        $examid = $request->input('exam_id');

        $quizzes = Quiz::all();
        foreach ($quizzes as $quiz) {
            $quizId = $quiz->id;

            // Check if the answer is submitted as a radio button or a text input
            if ($request->has('answer' . $quizId)) {
                // If it's a radio button answer
                $answer = $request->input('answer' . $quizId);
                $answer_text=null;
            } elseif ($request->has('answer_text' . $quizId)) {
                // If it's a text input answer
                $answer_text = $request->input('answer_text' . $quizId);
                $answer = null;
            } else {
                // Handle the case if no answer is submitted for the quiz
                continue; // Skip this quiz and move to the next one
            }

            // Now you have the $quizId and $answer, you can save them to the database
            $userAnswer = new UserAnswer();
            $userAnswer->user_id = auth()->id();
            $userAnswer->quiz_id = $quizId;
            $userAnswer->answer = $answer;
            $userAnswer->answer_text = $answer_text;
            $userAnswer->exam_id = $examid;
            $userAnswer->save();
        }

        return view('auth.dashboard')->with('success', 'L\'esame è stato consegnato correttamente.');
    }

    public function indexingResults($examId)
    {
        $exam = Exam::find($examId);
        $users= $exam->user()->get();

        return view('exam.results', compact('exam','users'));
    }

    public function displayUsersAnswer($userId, $examId){

        $exam = Exam::find($examId);
        $quizzes = $exam->quiz()->get()->pluck('id');

        $userAnswer = UserAnswer::where('user_id', $userId)
            ->whereIn('quiz_id', $quizzes)
            ->where('exam_id', $examId)
            ->get();

        $quizzes = $exam->quiz()->get();

        return view('exam.resultsuser', compact('userAnswer', 'quizzes', 'exam','userId'));

    }

    public function correctAnswer(Request $request)
    {
        $examId = $request->input('exam_id');
        $userId = $request->input('user_id');

        // Ottieni le risposte degli studenti per l'esame specificato dall'utente
        $studentAnswers = UserAnswer::where('exam_id', $examId)->where('user_id', $userId)->get();

        // Inizializza il punteggio totale dello studente a 0
        $totalScore = 0;

        // Itera attraverso le risposte degli studenti e aggiorna il punteggio per ogni risposta
        foreach ($studentAnswers as $studentAnswer) {
            // Ottieni il quiz associato alla risposta dello studente
            $quiz = Quiz::find($studentAnswer->quiz_id);

            // Assicurati che il quiz sia stato trovato e che abbia un punteggio
            if ($quiz && isset($quiz->points)) {

                // Controlla se la risposta dello studente è corretta confrontandola con la risposta corretta del quiz
                if ($studentAnswer->answer == $quiz->answer && $quiz->answer != null) {
                    // Aggiorna il punteggio per questa risposta
                    $studentAnswer->points = $quiz->points;
                    $studentAnswer->save();

                    // Aggiungi il punteggio di questa risposta al punteggio totale dello studente
                    $totalScore += $quiz->points;
                }

                if ($studentAnswer->answer_text == $quiz->answer_text && $quiz->answer_text != null) {
                    // Aggiorna il punteggio per questa risposta
                    $studentAnswer->points = $quiz->points;
                    $studentAnswer->save();

                    // Aggiungi il punteggio di questa risposta al punteggio totale dello studente
                    $totalScore += $quiz->points;
                }
            }
        }

        // Aggiorna lo score totale dell'utente nella tabella exam_user
        \DB::table('exam_user')->where('exam_id', $examId)->where('user_id', $userId)->update(['user_points' => $totalScore]);

        // Restituisci la vista con un messaggio di successo
        $availableExam = Exam::all();
        return view('exam.list', compact('availableExam'))->with('success', 'L\'esame è stato correttamente valutato.');
    }






}
