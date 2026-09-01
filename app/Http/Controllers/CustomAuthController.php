<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Hash;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CustomAuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }
    public function customLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('dashboard')
                ->withSuccess('Signed in');
        }
        return redirect("login")->withSuccess('Login details are not valid');
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
            'password' => 'required|min:6',
        ]);
        // Il ruolo non arriva piu' dal form: chiunque poteva registrarsi come
        // docente spuntando una checkbox. Si assegna con
        // `php artisan mexam:make-teacher <email>`.
        $data = $request->only(['name', 'email', 'password']);
        $this->create($data);
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('dashboard')->withSuccess('Signed in');
        }
        //return redirect("dashboard")->withSuccess('You have signed-in');
    }
    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
    public function dashboard()
    {
        if (Auth::check()) {
            return view('auth.dashboard');
        }
        return redirect("login")->withSuccess('You are not allowed to access.blade.php');
    }
    public function signOut()
    {
        Session::flush();
        Auth::logout();
        return Redirect('login');
    }

}



