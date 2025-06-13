@php
    $menus = ['home', 'about', 'categories'];
@endphp

@include('pages.header', ['name' => 'ABC', 'menus' => $menus]) {{--  for passing value in it we use second parameter as array --}}

<h1>Home Page</h1>

@include('pages.footer')


{{-- for safe include we use include if --}}
@includeIf('pages.content')


{{-- for conditional include we use include when --}}
@includeWhen(1 == 1, 'pages.footer') {{-- third parameter for value --}}
@includeUnless(1 == 0, 'pages.footer') {{-- third parameter for value --}}

{{-- Include When and Include Unless they both are vise versa to each other,--}}
