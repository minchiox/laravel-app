<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLibraryRequest;
use App\Http\Requests\UpdateLibraryRequest;
use App\Models\Library;

class LibraryController extends Controller
{
    public function index()
    {
        return view('library.library');
    }

    public function store(StoreLibraryRequest $request)
    {
        $library = new Library();
        $library->fill($request->validated());
        $library->save();

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

    public function update(UpdateLibraryRequest $request, $id)
    {
        $library = Library::findOrFail($id);
        $library->update($request->validated());

        // Reindirizza con un messaggio di successo
        return redirect()->route('library.edit', $id)->with('success', 'Library updated successfully.');
    }

}



