<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLibraryRequest;
use App\Http\Requests\UpdateLibraryRequest;
use App\Models\Library;
use Inertia\Inertia;

class LibraryController extends Controller
{
    public function index()
    {
        return Inertia::render('library/create');
    }

    public function store(StoreLibraryRequest $request)
    {
        $library = new Library();
        $library->fill($request->validated());
        $library->user_id = auth()->id();
        $library->save();

        return back()->with('success', 'Libreria aggiunta con successo.');
    }

    public function destroy($id)
    {
        $library = Library::findOrFail($id);
        $this->authorize('delete', $library);
        $library->delete();

        // Reindirizza con un messaggio di successo
        return redirect()->route('libraryquiz.list')->with('success', 'Libreria eliminata con successo.');
    }

    public function edit($id)
    {
        $library = Library::findOrFail($id);
        $this->authorize('update', $library);

        return Inertia::render('library/edit', compact('library'));
    }

    public function update(UpdateLibraryRequest $request, $id)
    {
        $library = Library::findOrFail($id);
        $this->authorize('update', $library);
        $library->update($request->validated());

        // Reindirizza con un messaggio di successo
        return redirect()->route('library.edit', $id)->with('success', 'Libreria aggiornata con successo.');
    }

}



