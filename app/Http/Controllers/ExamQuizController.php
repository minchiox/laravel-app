<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CorrectAnswerRequest;
use App\Models\Exam;
use App\Models\Quiz;
use App\Models\Library;
use App\Models\UserAnswer;
use App\Models\User;
use PDF;
use Illuminate\Support\Facades\Storage;


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
        $exam = Exam::findOrFail($examId);
        $quizzes= $exam->quiz()->get();

        return view('exam.quizlist', compact('exam', 'quizzes'));
    }

    /**
     * `exam()->detach()` senza argomenti scollegava il quiz da OGNI esame a
     * cui era associato, non solo da questo: un quiz condiviso fra piu' esami
     * spariva ovunque rimuovendolo da uno solo. Il punteggio totale va
     * ricalcolato dopo la rimozione, come in store().
     */
    public function quiz_destroy($examId, $quizId)
    {
        $exam = Exam::findOrFail($examId);
        $exam->quiz()->detach($quizId);

        $exam->total_points = $exam->quiz()->sum('points');
        $exam->save();

        $quizzes = $exam->quiz()->get();

        return view('exam.quizlist', compact('exam', 'quizzes'));
    }

    /**
     * Apre l'esame per lo studente.
     *
     * Prima l'iscrizione avveniva qui, all'apertura: bastava chiudere la pagina
     * per restare esclusi dall'esame per sempre. Ora aprire e' un'operazione di
     * sola lettura e l'iscrizione avviene alla consegna.
     */
    public function access($examId)
    {
        $exam = Exam::findOrFail($examId);

        if (! $exam->isOpen()) {
            return redirect()->route('exam.list')
                ->with('error', "L'esame non e' al momento disponibile.");
        }

        if ($exam->wasSubmittedBy(Auth::id())) {
            return redirect()->route('exam.list')
                ->with('error', 'Hai gia\' consegnato questo esame.');
        }

        return view('exam.access', [
            'exam' => $exam,
            'quizzes' => $exam->quiz()->inRandomOrder()->get(),
        ]);
    }

    /**
     * Registra la consegna.
     *
     * La versione precedente non validava nulla, non controllava la finestra
     * temporale, non impediva la seconda consegna e iterava su Quiz::all(),
     * cioe' su tutti i quiz del sistema invece che su quelli dell'esame.
     */
    public function storeUserAnswers(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => ['required', 'integer', 'exists:exams,id'],
        ]);

        $exam = Exam::with('quiz')->findOrFail($validated['exam_id']);
        $userId = Auth::id();

        abort_unless($exam->isOpen(), 403, "L'esame non e' al momento disponibile.");
        abort_if($exam->wasSubmittedBy($userId), 403, 'Esame gia\' consegnato.');

        // Una sola transazione: se qualcosa fallisce a meta', non resta una
        // consegna parziale.
        DB::transaction(function () use ($exam, $userId, $request) {
            foreach ($exam->quiz as $quiz) {
                $answer = $request->input('answer'.$quiz->id);
                $answerText = $request->input('answer_text'.$quiz->id);

                if ($answer === null && $answerText === null) {
                    continue;
                }

                UserAnswer::create([
                    'user_id' => $userId,
                    'exam_id' => $exam->id,
                    'quiz_id' => $quiz->id,
                    'answer' => $answer === null ? null : (bool) $answer,
                    'answer_text' => $answerText,
                ]);
            }

            // L'iscrizione avviene qui: e' la consegna a segnare la
            // partecipazione. L'indice unico su (exam_id, user_id) impedisce
            // il doppio invio anche in caso di richieste concorrenti.
            $exam->user()->attach($userId, ['created_at' => now(), 'updated_at' => now()]);
        });

        // Prima restituiva direttamente la view: un refresh rispediva il POST.
        return redirect()->route('dashboard')
            ->with('success', "L'esame e' stato consegnato correttamente.");
    }

    public function indexingResults($examId)
    {
        $exam = Exam::findOrFail($examId);
        $users= $exam->user()->get();

        return view('exam.results', compact('exam','users'));
    }

    public function displayUsersAnswer($userId, $examId){

        $exam = Exam::findOrFail($examId);
        $quizzes = $exam->quiz()->get()->pluck('id');

        $userAnswer = UserAnswer::where('user_id', $userId)
            ->whereIn('quiz_id', $quizzes)
            ->where('exam_id', $examId)
            ->get();

        $quizzes = $exam->quiz()->get();

        return view('exam.resultsuser', compact('userAnswer', 'quizzes', 'exam','userId'));

    }

    /**
     * Correggeva con una Quiz::find() per ogni risposta (N+1) e, se la
     * risposta risultava sbagliata, lasciava il punteggio della correzione
     * precedente invece di azzerarlo: ricorreggere un compito dopo aver
     * cambiato la risposta giusta di un quiz non riduceva mai il punteggio.
     */
    public function correctAnswer(CorrectAnswerRequest $request)
    {
        $validated = $request->validated();
        $examId = $validated['exam_id'];
        $userId = $validated['user_id'];

        $studentAnswers = UserAnswer::where('exam_id', $examId)
            ->where('user_id', $userId)
            ->with('quiz')
            ->get();

        $totalScore = 0;

        foreach ($studentAnswers as $studentAnswer) {
            $quiz = $studentAnswer->quiz;

            $isCorrect = $quiz && (
                ($quiz->answer !== null && $studentAnswer->answer === $quiz->answer)
                || ($quiz->answer_text !== null && $studentAnswer->answer_text === $quiz->answer_text)
            );

            $studentAnswer->points = $isCorrect ? $quiz->points : 0;
            $studentAnswer->save();

            $totalScore += $studentAnswer->points;
        }

        // Aggiorna lo score totale dell'utente nella tabella exam_user
        DB::table('exam_user')->where('exam_id', $examId)->where('user_id', $userId)->update(['user_points' => $totalScore]);

        // Restituisci la vista con un messaggio di successo
        $availableExam = Exam::all();
        return view('exam.list', compact('availableExam'))->with('success', 'L\'esame è stato correttamente valutato.');
    }



    public function printExamUser($examId, $userId)
    {
        // find() lasciava passare un id inesistente fino a $user->name,
        // un errore fatale invece di un 404 pulito.
        $exam = Exam::findOrFail($examId);
        $user = User::findOrFail($userId);
        $userAnswer = UserAnswer::where('user_id', $userId)
            ->where('exam_id', $examId)
            ->get();
        //per stampare il nome dello studente che ha svolto l'esame
        $user = $user->name;
        $quizzes = $exam->quiz()->get();

        // Il nome includeva solo l'id dell'esame, quindi il compito di uno
        // studente sovrascriveva quello di un altro.
        $filename = "esame_{$examId}_studente_{$userId}.pdf";

        $pdf = PDF::loadView('exam.printResult', compact('exam', 'quizzes', 'userId', 'userAnswer', 'user'));

        // Il PDF veniva salvato in public/pdf/ con un nome prevedibile: i
        // compiti svolti erano scaricabili da chiunque, senza autenticazione.
        // Ora viene solo trasmesso al docente che lo ha richiesto.
        return $pdf->stream($filename);
    }

    public function printExam($examId)
    {
        $exam = Exam::findOrFail($examId);
        $quizzes = $exam->quiz()->inRandomOrder()->get();
        $filename = "esame_{$examId}_in_bianco.pdf";

        $pdf = PDF::loadView('exam.printResultBlank', compact('exam', 'quizzes'));

        // Anche qui il salvataggio in public/ rendeva le tracce d'esame
        // scaricabili in anticipo da chiunque conoscesse l'id.
        return $pdf->stream($filename);
    }


}
