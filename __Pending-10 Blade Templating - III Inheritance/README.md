# Template Inheritance in Laravel Blade

Here's a recreation of the template inheritance structure from the routing parameters example:

## Base Layout Template

```php
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Scripts -->
    @stack('head-scripts')
</head>
<body>
    <!-- Header Section -->
    @include('partials.header')
    
    <!-- Main Content -->
    <main class="container py-4">
        @yield('content')
    </main>
    
    <!-- Footer Section -->
    @include('partials.footer')
    
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('footer-scripts')
</body>
</html>
```

## Child Template Example

```php
{{-- resources/views/users/show.blade.php --}}
@extends('layouts.app')

@section('title', "Profile - {$user->name}")

@push('head-scripts')
    <link rel="stylesheet" href="{{ asset('css/user-profile.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-md-3">
            <!-- Sidebar Section -->
            @include('users.partials.sidebar', ['user' => $user])
        </div>
        
        <div class="col-md-9">
            <!-- Main Profile Content -->
            <div class="card">
                <div class="card-header">
                    <h2>User Profile</h2>
                </div>
                
                <div class="card-body">
                    @include('users.partials.profile-header', ['user' => $user])
                    
                    <div class="profile-content mt-4">
                        @yield('profile-content')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-scripts')
    <script src="{{ asset('js/user-profile.js') }}"></script>
@endpush
```

## Nested Child Template

```php
{{-- resources/views/users/admin/show.blade.php --}}
@extends('users.show')

@section('title', "Admin View - {$user->name}")

@section('profile-content')
    <!-- Admin-specific content sections -->
    <div class="admin-details">
        @include('users.admin.partials.stats', ['user' => $user])
        
        <div class="row mt-4">
            <div class="col-md-6">
                @include('users.admin.partials.activity', ['activities' => $activities])
            </div>
            <div class="col-md-6">
                @include('users.admin.partials.permissions', ['permissions' => $permissions])
            </div>
        </div>
    </div>
@endsection
```

## Component-like Subview

```php
{{-- resources/views/users/partials/profile-header.blade.php --}}
@props(['user', 'size' => 'lg'])

<div class="profile-header profile-header-{{ $size }}">
    <div class="profile-avatar">
        <img src="{{ $user->avatarUrl() }}" 
             alt="{{ $user->name }}" 
             class="avatar-{{ $size }}">
             
        @if($user->isVerified())
            <span class="verified-badge" title="Verified User">
                <i class="fas fa-check-circle"></i>
            </span>
        @endif
    </div>
    
    <div class="profile-info">
        <h1>{{ $user->name }}</h1>
        <p class="text-muted">Member since {{ $user->created_at->format('M Y') }}</p>
        
        <div class="profile-actions mt-2">
            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                Edit Profile
            </a>
            
            @can('message', $user)
                <a href="{{ route('messages.create', $user) }}" class="btn btn-sm btn-primary">
                    Send Message
                </a>
            @endcan
        </div>
    </div>
</div>
```

## Conditional Layout Inheritance

```php
{{-- resources/views/products/show.blade.php --}}
@if(request()->is('admin/*'))
    @extends('layouts.admin')
@else
    @extends('layouts.app')
@endif

@section('title', $product->name)

@section('content')
    <div class="product-container">
        @include('products.partials.gallery', ['product' => $product])
        
        <div class="product-details">
            <h1>{{ $product->name }}</h1>
            <div class="price">{{ $product->formattedPrice() }}</div>
            
            @includeWhen($product->hasDiscount(), 'products.partials.discount-badge', [
                'discount' => $product->discountPercentage()
            ])
            
            @include('products.partials.add-to-cart', ['product' => $product])
            
            <div class="product-description">
                {!! $product->description !!}
            </div>
        </div>
    </div>
@endsection
```

## Advanced Inheritance with Stacks

```php
{{-- resources/views/layouts/admin.blade.php --}}
@extends('layouts.app')

@push('head-scripts')
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="admin-layout">
        <!-- Admin Sidebar -->
        @include('admin.partials.sidebar')
        
        <!-- Admin Main Content -->
        <div class="admin-content">
            <!-- Breadcrumbs -->
            @section('breadcrumbs')
                @include('admin.partials.breadcrumbs')
            @show
            
            <!-- Page Header -->
            @hasSection('page-header')
                <div class="page-header">
                    @yield('page-header')
                </div>
            @endif
            
            <!-- Main Content -->
            <div class="content-wrapper">
                @yield('admin-content')
            </div>
        </div>
    </div>
@endsection
```

## Using the Admin Layout

```php
{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'User Management')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Users</li>
@endsection

@section('page-header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>User Management</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            Create New User
        </a>
    </div>
@endsection

@section('admin-content')
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <!-- Table content -->
            </table>
            
            {{ $users->links() }}
        </div>
    </div>
@endsection
```

## Best Practices for Template Inheritance

1. **Three-level maximum depth** for inheritance (base → section → subsection)
2. **Use components** for reusable UI elements
3. **Name sections clearly** (avoid generic names like 'section1')
4. **Provide default content** where appropriate:
   ```php
   @section('sidebar')
       Default sidebar content
   @show
   ```
5. **Use stacks for assets** to properly organize CSS/JS
6. **Consider view composers** for shared data across multiple views
7. **Document complex inheritance** with comments
8. **Avoid business logic** in templates - keep it in controllers/services

This structure provides a clean, maintainable way to handle template inheritance in Laravel Blade while maintaining all the functionality from the original routing parameters example.
