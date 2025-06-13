<h1>{{ $name }} Header</h1>

@forelse ($menus as $menu)
    <p>{{ $menu }}</p>
@empty
    <p>No Value Found</p>
@endforelse
