@extends('auth.layouts')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Library Maker') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('library.store') }}" enctype="multipart/form-data">
                            @csrf

                            @if (session('success'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="library_name" class="form-label">{{ __('Library') }}:</label>
                                    <input class="form-control" type="text" id="library_name" name="library_name" autofocus required>
                                    @error('question')
                                    <span role="alert" class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="library_subject" class="form-label">{{ __('Subject') }}:</label>
                                    <input class="form-control" type="text" id="library_subject" name="library_subject" autofocus required>
                                    @error('subject')
                                    <span role="alert" class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="library_difficulty" class="form-label">{{ __('Difficulty') }}:</label>
                                    <select name="library_difficulty" class="form-select" id="library_difficulty" required>
                                        <option value="" >{{ __('Select an option') }}</option>
                                        <option value="easy">{{ __('Easy') }}</option>
                                        <option value="medium">{{ __('Medium') }}</option>
                                        <option value="hard" >{{ __('Hard') }}</option>
                                    </select>
                                    @error('question')
                                    <span role="alert" class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">{{ __('Create new Library') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
