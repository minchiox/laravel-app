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
        <a class="navbar-brand mr-auto" href="#">Mexam</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                @else
                    <li class="nav-item dropdown">
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
                    @if(Auth::user()->isTeacher)
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle " href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                            {{ __('Quiz') }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a href="{{ route('quiz.index') }}" class="dropdown-item">Quiz List</a>
                            <a href="{{ route('quiz.create') }}" class="dropdown-item">Make Quiz</a>
                        </div>
                    </li>
                    @endif

                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle " href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                            {{ __('Library') }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a href="{{ route('library.library') }}" class="dropdown-item">Make Library</a>
                            <a href="{{ route('libraryquiz.index') }}" class="dropdown-item">Add Quiz to Library</a>
                            <a href="{{ route('libraryquiz.list') }}" class="dropdown-item">show libraries</a>
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
