<!DOCTYPE html>
<html>
<head>
    <title>{{ config('layouts.name', 'Mexam') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
<nav class="navbar navbar-light navbar-expand-lg mb-5" style="background-color: #e3f2fd;">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="navbar-brand">
                        <img src="{{ asset('logo/Mexamlogo.png') }}" height="50" alt="Logo Mexam" style="cursor: pointer;">
                </li>
                @guest
                    <li class="nav-item mt-2">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item mt-2">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                @else
                    <li class="nav-item dropdown mt-2">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle " href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                            <img src="/avatars/{{ Auth::user()->avatar }}" style="width: 30px; border-radius: 10%">
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a href="{{ route('dashboard') }}" class="dropdown-item">Dashboard</a>
                            <a href="{{ route('user.profile') }}" class="dropdown-item">Profile</a>
                            <a class="dropdown-item" href="" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('signout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>

                    <li class="nav-item dropdown mt-2">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle " href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                            {{ __('Quiz') }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a href="{{ route('quiz.list') }}" class="dropdown-item">Quiz List</a>
                            @if(Auth::user()->isTeacher)
                            <a href="{{ route('quiz.create') }}" class="dropdown-item">Make Quiz</a>
                            @endif
                        </div>
                    </li>



                    <li class="nav-item dropdown mt-2">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle " href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                            {{ __('Library') }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            @if(Auth::user()->isTeacher)
                            <a href="{{ route('library.library') }}" class="dropdown-item">Make Library</a>
                            <a href="{{ route('libraryquiz.index') }}" class="dropdown-item">Add Quiz to Library</a>
                            @endif
                            <a href="{{ route('libraryquiz.list') }}" class="dropdown-item">Libraries List</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown mt-2">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle " href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                            {{ __('Exam') }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            @if(Auth::user()->isTeacher)
                                <a href="{{ route('exam.store') }}" class="dropdown-item">Make Exam</a>
                                <a href="{{ route('examquiz.store') }}" class="dropdown-item">Add Quiz to Exam</a>
                            @endif
                            <a href="{{ route('exam.list') }}" class="dropdown-item">Exam List</a>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
    @yield('content')
</div>
</body>
</html>
