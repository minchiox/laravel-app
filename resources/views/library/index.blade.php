@extends('auth.layouts')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Library Quiz') }}</div>


                    <div class="card-body">
                        <form method="POST" action="{{ route('library.add') }}" enctype="multipart/form-data">
                            @csrf

                            @if (session('success'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <label for="quiz_id">Add Quiz:</label>
                            <select name="quiz_id" id="quiz_id">
                                @foreach($availableQuiz as $quiz)
                                    <option value="{{ $quiz->id }}">{{ $quiz->question }}</option>
                                @endforeach
                            </select>


                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">{{ __('Add new quiz') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
