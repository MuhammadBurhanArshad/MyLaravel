<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebPage - @yield('title', 'Page')</title>
    {{-- <link rel="stylesheet" href="../css/style.css"> --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
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
        @hasSection ('content')
            @yield('content')        
        @else
            <h2>No Content Found</h2>
        @endif
    </main>

    <footer>
        <div class="footer-links">
          @section('footer')
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('post') }}">Post</a>    
          @show
        </div>
        <div>example@copyright 2025.</div>
    </footer>

    @stack('scripts')
</body>

</html>
