@extends('auth.layouts')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Exam Maker') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('exam.store') }}" enctype="multipart/form-data">
                            @csrf

                            @if (session('success'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="exam_name" class="form-label">{{ __('Exam') }}:</label>
                                    <input class="form-control" type="text" id="exam_name" name="exam_name" autofocus required>
                                    @error('question')
                                    <span role="alert" class="text-danger">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="datetime">Select Date and Time:{{ __('Start time') }}:</label>
                                    <input type="datetime-local" id="datetime" name="startAt" autofocus required>
                                    @error('startAt')
                                    <span role="alert" class="text-danger">
                                            <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="exam_end" class="form-label">Select Date and Time:{{ __('End time') }}:</label>
                                    <input type="datetime-local" id="datetime" step="3600" name="dueAt" autofocus required>
                                    @error('dueAt')
                                    <span role="alert" class="text-danger">
                                            <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">{{ __('Create new Exam') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
