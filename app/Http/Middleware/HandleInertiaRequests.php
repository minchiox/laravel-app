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
            // Step F2: Ziggy e' stato introdotto per i link con parametro
            // (route() lato JS, via @routes in app.blade.php). Questa mappa
            // resta solo per il pugno di link senza parametri gia' in uso
            // dalla nav condivisa: per tutto il resto le pagine usano
            // direttamente route() di Ziggy.
            'nav' => [
                'login' => route('login'),
                'register' => route('register'),
                'dashboard' => route('dashboard'),
                'profile' => route('user.profile'),
                'signout' => route('signout'),
                'quizList' => route('quiz.list'),
                'quizCreate' => route('quiz.create'),
                'libraryCreate' => route('library.library'),
                'libraryList' => route('libraryquiz.list'),
                'examCreate' => route('exam.index'),
                'examList' => route('exam.list'),
            ],
        ];
    }
}
