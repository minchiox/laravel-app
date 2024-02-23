<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <style>
        /* Your existing styles */
        /* Add style to set background to white */
        body {
            background-color: white;
        }

        /* Style for blue buttons */
        .blue-button {
            background-color: #4f46e5;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin: 5px;
        }

        /* Style for blue buttons on hover */
        .blue-button:hover {
            background-color: #4338ca;
        }

        /* Center content vertically and horizontally */
        .center-content {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Ensure content takes up at least the full viewport height */
        }

        /* Style for container holding login and register buttons */
        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px; /* Adjust as needed */
        }
    </style>
</head>
<body class="antialiased">
<div class="center-content">
    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
        <!-- Logo Section -->
        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <div class="flex justify-center">
                <img src="{{ asset('logo/Mexamlogo.png') }}" alt="Mexam Logo" class="logo">
            </div>
        </div>

        <!-- Login and Register Buttons -->
        <div class="button-container">
            @if (Route::has('login'))
                <div>
                    @auth
                        <a href="{{ route('dashboard') }}" class="blue-button">Home</a>
                    @else
                        <a href="{{ route('login') }}" class="blue-button">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="blue-button">Register</a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </div>
</div>
</body>
</html>
