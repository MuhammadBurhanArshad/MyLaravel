<h1>Users</h1>


{{-- <h3>Name: {{$name}}</h3>

<h3>City: {{ !empty($city) ? $city : 'N/A' }}</h3> --}}

{{-- for printing JS or HTML in the curly brackets we use exclamation mark two times in single curly bracket instead of double curly brackets --}}
{{-- <h3>City: {!! $city !!}</h3> --}}


@foreach ($users as $id => $user)
    <h3>
        Name: {{ $user['name'] }} |
        City: {{ !empty($user['city']) ? $user['city'] : 'N/A' }} |
        Phone: {{ !empty($user['phone']) ? $user['phone'] : 'N/A' }} |
        <a href="{{ route('userView', $id) }}">View</a>
    </h3>
@endforeach