@extends('auth.layouts')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Library Quiz') }}</div>

                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="card-body">
                        <form method="POST" action="{{ route('libraryquiz.store') }}" enctype="multipart/form-data">
                            @csrf
                            <label class='form-label' for="library_id">Select Library:</label>
                            <select class="form-select" name="library_id" id="library_id"> <!-- qui devo mettere $library->id tra 2 parentesi graffe -->
                                @foreach($availableLibraries as $library)
                                    <option value="{{ $library->id }}">{{ $library->library_name }}</option>
                              @endforeach
                            </select><br>
                            <label class='form-label' for="quiz_id">Select Quiz:</label>
                            <select  class="form-select" name="quiz_id" id="quiz_id">
                                @foreach($availableQuiz as $quiz)
                                    <option value="{{ $quiz->id }}">{{ $quiz->question }}</option> <!-- qui invece quiz sempre tra parentesi graffe -->
                                @endforeach
                            </select><br>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">{{ __('Add Quiz') }}</button>
                                </div>
                            </div>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
