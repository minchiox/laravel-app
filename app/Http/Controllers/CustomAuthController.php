<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class CustomAuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function customLogin(Request $request)
    {
        $request->validate([
            // era 'required' e basta: si poteva tentare il login con
            // qualunque stringa
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            // Prima un login fallito faceva redirect('login')->withSuccess(...),
            // cioe' comunicava un successo e non popolava $errors: la view
            // mostrava una pagina di login muta.
            return back()
                ->withErrors(['email' => 'Credenziali non valide.'])
                ->onlyInput('email');
        }

        // Senza rigenerazione l'id di sessione sopravvive all'autenticazione:
        // e' la premessa della session fixation.
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function registration()
    {
        return view('auth.register');
    }

    public function customRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            // min:6 senza altri vincoli era piu' debole della politica usata
            // sul cambio password dal profilo: le due vanno allineate.
            'password' => ['required', Password::min(8)],
        ]);

        // Il ruolo non arriva piu' dal form: chiunque poteva registrarsi come
        // docente spuntando una checkbox. Si assegna con
        // `php artisan mexam:make-teacher <email>`.
        $user = $this->create($request->only(['name', 'email', 'password']));

        // Mancava il ramo else: se Auth::attempt falliva dopo aver creato
        // l'utente, il metodo ritornava null e il browser riceveva una pagina
        // vuota. Qui si autentica direttamente l'utente appena creato.
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function dashboard()
    {
        return Inertia::render('dashboard');
    }

    public function signOut(Request $request)
    {
        Auth::logout();

        // Session::flush() svuotava i dati ma lasciava vivo l'id di sessione e
        // il token CSRF.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
