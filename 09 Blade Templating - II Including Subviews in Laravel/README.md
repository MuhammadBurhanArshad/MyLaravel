# Routing Parameters & Constraints in Laravel (Blade Templates with Sub-Views)

## Main Template Structure

```php
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
</head>
<body>
    @include('partials.navigation')
    
    <div class="container">
        @yield('content')
    </div>
    
    @include('partials.footer')
</body>
</html>
```

## Parameter Handling in Main Views

```php
{{-- resources/views/users/show.blade.php --}}
@extends('layouts.app')

@section('title', "User {$user->id}")

@section('content')
    <h1>User Profile</h1>
    
    {{-- Basic parameter display --}}
    <p>User ID: {{ $user->id }}</p>
    <p>User Name: {{ $user->name }}</p>
    
    {{-- Include sub-view with parameters --}}
    @include('users.partials.profile', ['user' => $user])
    
    {{-- Route with model binding --}}
    <a href="{{ route('users.posts', $user) }}" class="btn">
        View User's Posts
    </a>
@endsection
```

## Sub-Views with Route Parameters

```php
{{-- resources/views/users/partials/profile.blade.php --}}
<div class="profile-card">
    <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}">
    
    <div class="profile-info">
        <h3>{{ $user->name }}</h3>
        
        {{-- Conditional based on route parameter --}}
        @if(request()->route('verified'))
            <span class="verified-badge">Verified User</span>
        @endif
        
        {{-- Multiple parameters --}}
        <a href="{{ url("/users/{$user->id}/posts/".now()->year) }}">
            View This Year's Posts
        </a>
    </div>
</div>
```

## Dynamic Menu with Route Constraints

```php
{{-- resources/views/partials/navigation.blade.php --}}
<nav>
    <ul>
        <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a></li>
        
        {{-- Menu with constrained parameter --}}
        <li>
            <a href="{{ route('products.index') }}" 
               class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                Products
            </a>
        </li>
        
        {{-- Localized routes --}}
        @foreach(['en', 'fr', 'es'] as $locale)
            <li>
                <a href="{{ url("/$locale") }}"
                   class="{{ request()->segment(1) === $locale ? 'active' : '' }}"
                   hreflang="{{ $locale }}">
                    {{ strtoupper($locale) }}
                </a>
            </li>
        @endforeach
        
        {{-- Dynamic admin sections with constraints --}}
        @auth
            @foreach(['dashboard', 'users', 'settings'] as $section)
                <li>
                    <a href="{{ url("/admin/$section") }}"
                       class="{{ request()->is("admin/$section") ? 'active' : '' }}">
                        {{ ucfirst($section) }}
                    </a>
                </li>
            @endforeach
        @endauth
    </ul>
</nav>
```

## Form Handling with Route Parameters

```php
{{-- resources/views/posts/comments/form.blade.php --}}
@props(['post', 'comment' => null])

<form method="POST" 
      action="{{ $comment ? route('comments.update', [$post, $comment]) : route('comments.store', $post) }}">
    @csrf
    @if($comment)
        @method('PUT')
    @endif
    
    <input type="hidden" name="post_id" value="{{ $post->id }}">
    
    <textarea name="content">{{ old('content', $comment?->content) }}</textarea>
    
    <button type="submit">
        {{ $comment ? 'Update' : 'Create' }} Comment
    </button>
</form>
```

## Dynamic Breadcrumbs with Parameters

```php
{{-- resources/views/partials/breadcrumbs.blade.php --}}
<nav class="breadcrumbs">
    <ol>
        <li><a href="{{ url('/') }}">Home</a></li>
        
        {{-- Handle various parameter patterns --}}
        @isset($category)
            <li><a href="{{ route('categories.show', $category) }}">{{ $category->name }}</a></li>
        @endisset
        
        @isset($product)
            <li><a href="{{ route('products.show', [$category, $product]) }}">{{ $product->name }}</a></li>
        @endisset
        
        {{-- For optional parameters --}}
        @isset($year)
            <li>{{ $year }}</li>
        @endisset
    </ol>
</nav>
```

## Pagination with Route Parameters

```php
{{-- resources/views/users/posts.blade.php --}}
@extends('layouts.app')

@section('content')
    <h1>{{ $user->name }}'s Posts</h1>
    
    @include('partials.breadcrumbs')
    
    <div class="posts">
        @foreach($posts as $post)
            @include('posts.partials.card', compact('post'))
        @endforeach
    </div>
    
    {{-- Pagination with preserved parameters --}}
    {{ $posts->appends(['year' => request('year')])->links() }}
@endsection
```

## Conditional Sub-Views Based on Parameters

```php
{{-- resources/views/products/show.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="product-detail">
        <h2>{{ $product->name }}</h2>
        
        {{-- Different views based on product type --}}
        @if($product->type === 'downloadable')
            @include('products.partials.downloadable', ['product' => $product])
        @elseif($product->type === 'physical')
            @include('products.partials.physical', ['product' => $product])
        @else
            @include('products.partials.generic', ['product' => $product])
        @endif
        
        {{-- Related items with parameter constraints --}}
        @include('products.partials.related', [
            'products' => $relatedProducts,
            'current' => $product->id
        ])
    </div>
@endsection
```

## Error Handling for Invalid Parameters

```php
{{-- resources/views/errors/404.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="error-page">
        <h1>Page Not Found</h1>
        
        @if(isset($exception) && $exception->getMessage())
            <p class="error-message">{{ $exception->getMessage() }}</p>
        @endif
        
        {{-- Show attempted route --}}
        @php
            $attemptedUrl = url()->current();
            $routeName = Route::currentRouteName();
        @endphp
        
        <p>Attempted to access: <code>{{ $attemptedUrl }}</code></p>
        
        @if($routeName)
            <p>Route name: <code>{{ $routeName }}</code></p>
        @endif
        
        <a href="{{ url('/') }}" class="btn">Return Home</a>
    </div>
@endsection
```

## Best Practices for Blade Sub-Views

1. **Explicitly pass all needed parameters** to sub-views
   ```php
   @include('partials.card', [
       'item' => $item,
       'showDetails' => true,
       'class' => 'highlighted'
   ])
   ```

2. **Use route helpers** instead of hardcoded URLs
   ```php
   {{-- Good --}}
   <a href="{{ route('products.show', $product) }}">View</a>
   
   {{-- Bad --}}
   <a href="/products/{{ $product->id }}">View</a>
   ```

3. **Validate parameters** before using them in links
   ```php
   @if(is_numeric($userId))
       <a href="{{ route('users.show', $userId) }}">Profile</a>
   @endif
   ```

4. **Consider using components** for complex parameter handling
   ```php
   <x-product-card :product="$product" type="featured"/>
   ```

5. **Preserve query parameters** in pagination and forms
   ```php
   {{ $items->appends(request()->query())->links() }}
   ```

6. **Handle optional parameters** gracefully
   ```php
   <a href="{{ route('blog.index', ['year' => $year ?? null]) }}">
       Blog Archive
   </a>
   ```

Remember that proper parameter handling in Blade templates ensures your application remains robust and maintainable. Always consider how your route parameters and constraints affect your views and sub-views.
