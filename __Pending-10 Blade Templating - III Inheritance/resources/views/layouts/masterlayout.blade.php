<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebPage - {{ $page ?? 'Page' }}</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <header>
        <nav>
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('about') }}">About</a>
            <a href="{{ route('post') }}">Post</a>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <div class="footer-links">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('about') }}">About</a>
            <a href="{{ route('post') }}">Post</a>
        </div>
        <div>example@copyright 2025.</div>
    </footer>
</body>

</html>
