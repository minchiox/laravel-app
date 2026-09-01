<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    //create controller instance
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('profile');
    }


    //change value in profile page validation
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'confirm_password' => 'required_with:password|same:password',
            'avatar' => 'image',
        ]);

        // $request->all() lasciava passare qualunque campo presente nel POST:
        // con isTeacher fra i $fillable, uno studente si promuoveva docente
        // aggiungendo un input al form. Qui si accetta solo cio' che il profilo
        // puo' davvero modificare.
        $input = $request->only(['name', 'email', 'phone', 'city']);

        if ($request->hasFile('avatar')) {
            // time() come nome collideva fra due upload nello stesso secondo
            $avatarName = uniqid('avatar_', true).'.'.$request->avatar->getClientOriginalExtension();
            $request->avatar->move(public_path('avatars'), $avatarName);

            $input['avatar'] = $avatarName;
        }

        if ($request->filled('password')) {
            $input['password'] = Hash::make($request->input('password'));
        }

        auth()->user()->update($input);

        return back()->with('success', 'Profile updated successfully.');
    }

}
