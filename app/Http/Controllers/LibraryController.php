<?php

namespace App\Http\Controllers;

use App\Models\Library;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index()
    {
        return view('library.library');
    }

    public function store(Request $request)
    {
        $request->validate([
            'library_name' => 'required',
            'library_difficulty' => 'required',
            'library_subject' => 'required',
        ]);

        $input = $request->all();
        $libraries = new Library();
        $libraries->fill($input);
        $libraries-> save();

        return back()->with('success', 'Library added successfully.');
    }

}



