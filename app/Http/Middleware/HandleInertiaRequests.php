<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'isTeacher' => $user->isTeacher,
                    'avatarUrl' => $user->avatar ? route('user.avatar', $user->avatar) : null,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            // Non c'e' ancora Ziggy/wayfinder: finche' la nav React collega
            // solo pagine Blade, le poche URL che le servono bastano cosi'.
            // Da rivedere se il numero cresce parecchio durante lo Step F2.
            'nav' => [
                'login' => route('login'),
                'register' => route('register'),
                'dashboard' => route('dashboard'),
                'profile' => route('user.profile'),
                'signout' => route('signout'),
                'quizList' => route('quiz.list'),
                'quizCreate' => route('quiz.create'),
                'libraryCreate' => route('library.library'),
                'libraryAddQuiz' => route('libraryquiz.index'),
                'libraryList' => route('libraryquiz.list'),
                'examCreate' => route('exam.index'),
                'examAddQuiz' => route('examquiz.index'),
                'examList' => route('exam.list'),
            ],
        ];
    }
}
