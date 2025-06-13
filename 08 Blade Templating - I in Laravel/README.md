# Routing Parameters & Constraints in Laravel (Blade Template)

```php
{{-- Basic Route Parameters --}}

{{-- Required Parameters --}}
<a href="{{ url('/user/123') }}">User Profile</a>
{{-- Outputs: <a href="/user/123">User Profile</a> --}}

{{-- Multiple Parameters --}}
<a href="{{ route('post.comment', ['post' => 5, 'comment' => 12]) }}">View Comment</a>
{{-- Assuming named route: Route::get('/posts/{post}/comments/{comment}', ...)->name('post.comment') --}}

{{-- Optional Parameters --}}
<a href="{{ url('/users') }}">Default User</a> {{-- Shows "Guest" --}}
<a href="{{ url('/users/John') }}">John's Page</a> {{-- Shows "John" --}}

{{-- Parameter Constraints --}}

{{-- Regular Expression Constraints --}}
@if(preg_match('/^[0-9]+$/', $userId))
    <a href="{{ url('/user/'.$userId) }}">Valid User</a>
@else
    <span class="error">Invalid User ID</span>
@endif

{{-- Route Model Binding --}}

{{-- Implicit Binding --}}
@foreach($posts as $post)
    <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
    {{-- Equivalent to: /posts/{post} --}}
@endforeach

{{-- Custom Key Binding --}}
@foreach($posts as $post)
    <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
    {{-- Uses slug instead of ID if route defined as /posts/{post:slug} --}}
@endforeach

{{-- Special Parameter Handling --}}

{{-- Encoded Forward Slashes --}}
<a href="{{ url('/search/'.urlencode('Laravel 10')) }}">Search</a>
{{-- Outputs: /search/Laravel%2010 --}}

{{-- Common Parameter Patterns --}}

{{-- Date Parameters --}}
<a href="{{ url('/archive/'.now()->format('Y/m/d')) }}">Today's Archive</a>

{{-- Localized Parameters --}}
@foreach(['en', 'fr', 'es'] as $locale)
    <a href="{{ url("/$locale/products") }}" hreflang="{{ $locale }}">
        {{ strtoupper($locale) }} Products
    </a>
@endforeach

{{-- Best Practices --}}

{{-- Specific constraints in links --}}
<a href="{{ url('/products/hardware/laptop') }}">Laptop</a>
{{-- Will match /products/{category}/{product:slug} --}}

{{-- Avoid overly permissive links --}}
{{-- Instead of: --}}
{{-- <a href="/{{ $page }}">Page</a> --}}
{{-- Do: --}}
@if(in_array($page, ['about', 'contact', 'privacy']))
    <a href="/{{ $page }}">{{ ucfirst($page) }}</a>
@endif

{{-- Advanced Techniques --}}

{{-- Conditional Constraints in Blade --}}
@env('production')
    <a href="/admin/dashboard">Dashboard</a>
@endenv

{{-- Parameter Dependency Injection --}}
<select onchange="window.location.href='/api/'+this.value+'/users'">
    <option value="v1">v1</option>
    <option value="v2">v2</option>
    <option value="v3">v3</option>
</select>

{{-- Common Pitfalls --}}

{{-- Route Order Matters --}}
<a href="/users/create">Create User</a> {{-- Should come before /users/{user} --}}

{{-- Proper Parameter Validation --}}
@if(is_numeric($productId))
    <a href="/products/{{ $productId }}">Product #{{ $productId }}</a>
@else
    <span class="error">Invalid Product ID</span>
@endif

{{-- Generating URLs with Constraints --}}
@php
    $validCategories = ['hardware', 'software', 'services'];
@endphp

@foreach($products as $product)
    @if(in_array($product->category, $validCategories))
        <a href="{{ url("/products/{$product->category}/{$product->slug}") }}">
            {{ $product->name }}
        </a>
    @endif
@endforeach

{{-- UUID Links --}}
@foreach($orders as $order)
    <a href="/orders/{{ $order->uuid }}">Order {{ $order->number }}</a>
@endforeach

{{-- Twitter-style usernames --}}
@if(preg_match('/^@[A-Za-z0-9_]{1,15}$/', $username))
    <a href="/{{ $username }}">{{ $username }}</a>
@endif

{{-- Using route() helper with parameters --}}
<a href="{{ route('user.profile', ['id' => Auth::id()]) }}">My Profile</a>

{{-- Named routes with constraints --}}
{{-- Route definition would be: --}}
{{-- Route::get('/categories/{category}', ...)->name('category.show')->where('category', 'books|movies|music'); --}}

@if(in_array($category, ['books', 'movies', 'music']))
    <a href="{{ route('category.show', $category) }}">{{ ucfirst($category) }}</a>
@endif
```

## Blade-Specific Routing Tips

1. **Always use helpers** (`url()`, `route()`) instead of hardcoding URLs
2. **Validate parameters before using them in links**
3. **Leverage named routes** for better maintainability
4. **Consider route-model binding** for cleaner templates
5. **Use conditionals** to match route constraints in your views

```php
{{-- Good practice in Blade --}}
@foreach($posts as $post)
    <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
@endforeach

{{-- Bad practice in Blade --}}
@foreach($posts as $post)
    <a href="/posts/{{ $post->id }}">{{ $post->title }}</a>
@endforeach
```

Remember that proper URL generation in Blade templates works hand-in-hand with your route definitions. Always ensure your template links match the expected parameter formats and constraints defined in your routes.
