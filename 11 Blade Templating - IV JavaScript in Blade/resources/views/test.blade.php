@php
    $greeting = "Hello, World!";
@endphp

{{-- This will throw an error --}}
{{-- <script>
    var greeting = {{ $greeting }};
    console.log(greeting);
</script> --}}

{{-- This is the correct implementation to pass data from php to js --}}
<script>
    var greeting = @json($greeting);
    console.log(greeting);
</script>

@php
    $fruits = ['Apple', 'Banana', 'Cherry'];
@endphp

<script>
    var fruits = @json($fruits);
    //  this will also works as
    // var fruits = {{ Js::from($fruits) }}

    fruits.forEach(function(fruit) {
        console.log(fruit);
    });
</script>