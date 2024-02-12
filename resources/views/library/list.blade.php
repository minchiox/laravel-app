@extends('auth.layouts')
@section('content')
    <div class="container mt-3">
        <h1>Lista delle Librerie</h1>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="scrollable-div" style="max-height: 300px; overflow-y: auto;">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col">Materia</th>
                            <th scope="col">Creato il</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($availableLibraries as $library)
                            <tr>
                                <td>{{ $library->library_name }}</td>
                                <td>{{ $library->library_subject }}</td>
                                <td>{{ $library->created_at }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
