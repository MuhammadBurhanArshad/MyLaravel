<h1>Home Page</h1>


{{-- Comment Statement --}}


{{ 5 + 7}} {{--will return 7 --}}


<br />
<br />


{[ "Hello World" ]}


<br />
<br />


{{ "<h1>Hellow World</h1>" }} {{-- will print the <h1>Hellow World </h1> --}}


<br />
<br />


{{-- For HTML and JS we use like this --}}
{!! "<h1>Hellow World</h1>" !!} {{-- will print the Hellow World as Heading 1  --}}


<br />
<br />


{{-- For HTML and JS we use like this --}}
{!! "<script>document.write('Hellow World')</script>" !!} {{-- will print the Hellow World  --}}


<br />
<br />


{{-- Writing Variable --}}
@php
    $user = "Muhamamd Burhan Arshad";
@endphp

{{ $user }}


<br />
<br />


{{-- Writing Variable From Loop --}}
@php
    $names = ["First", "Second", "Third"];
@endphp


<ul>
@foreach ($names as $name)
    <li>{{ $name }}</li>
@endforeach
</ul>


<br />
<br />


{{-- For printing the name of the variable we use --}}

@{{ $name }} {{-- like this we can print all the blade syntax by adding @ before them --}}


{{-- Loop Variables --}}


{{-- $loop->index --}}
<ul>
@foreach ($names as $name)
    <li>{{ $loop->index }}{{ $name }}</li>
@endforeach
</ul>

{{-- $loop->iteration --}}
<ul>
@foreach ($names as $name)
    <li>{{ $loop->iteration }}{{ $name }}</li>
@endforeach
</ul>

{{-- $loop->count --}}
<ul>
@foreach ($names as $name)
    <li>{{ $loop->count }}{{ $name }}</li>
@endforeach
</ul>

{{-- $loop->first --}}
<ul>
@foreach ($names as $name)
    @if($loop->first)
    <li style="color: red">{{ $loop->count }}{{ $name }}</li>
    @else
    <li>{{ $loop->count }}{{ $name }}</li>
    @endif
@endforeach
</ul>

{{-- $loop->last --}}
<ul>
@foreach ($names as $name)
    @if($loop->last)
    <li style="color: red">{{ $loop->count }}{{ $name }}</li>
    @else
    <li>{{ $loop->count }}{{ $name }}</li>
    @endif
@endforeach
</ul>

{{-- $loop->even --}}
<ul>
@foreach ($names as $name)
    @if($loop->even)
    <li style="color: red">{{ $loop->count }}{{ $name }}</li>
    @else
    <li>{{ $loop->count }}{{ $name }}</li>
    @endif
@endforeach
</ul>

{{-- $loop->odd --}}
<ul>
@foreach ($names as $name)
    @if($loop->odd)
    <li style="color: red">{{ $loop->count }}{{ $name }}</li>
    @else
    <li>{{ $loop->count }}{{ $name }}</li>
    @endif
@endforeach
</ul>

