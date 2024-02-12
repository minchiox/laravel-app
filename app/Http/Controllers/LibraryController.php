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

    public function destroy($id)
    {
        $library = Library::findOrFail($id);
        $library->delete();

        // Reindirizza con un messaggio di successo
        return redirect()->route('libraryquiz.list')->with('success', 'Library deleted successfully.');
    }

    public function edit($id)
    {
        $library = Library::findOrFail($id);
        return view('library.edit', compact('library'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'library_name' => 'required',
            'library_subject' => 'required',
            'library_difficulty' => 'required',
            // Aggiungi altre regole di validazione qui se necessario
        ]);

        $library = Library::findOrFail($id);
        $input = $request->all();

        //Se la risposta è booleana

        // Salva le modifiche
        $library->update($input);

        // Reindirizza con un messaggio di successo
        return redirect()->route('library.edit', $id)->with('success', 'Library updated successfully.');
    }

}



