# JavaScript in Laravel Blade Templates

## Base Layout with JavaScript Setup

```php
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <!-- Head Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
            'appName' => config('app.name'),
            'env' => config('app.env'),
            'userId' => Auth::id(),
            'baseUrl' => url('/')
        ]) !!};
    </script>
    @stack('head-scripts')
</head>
<body>
    <!-- Header Section -->
    @include('partials.header')
    
    <!-- Main Content -->
    <main class="container py-4" id="app">
        @yield('content')
    </main>
    
    <!-- Footer Section -->
    @include('partials.footer')
    
    <!-- Main App Script -->
    <script src="{{ mix('js/app.js') }}"></script>
    
    <!-- Page-Specific Scripts -->
    @stack('scripts')
    
    <!-- Inline Scripts -->
    @hasSection('javascript')
        @yield('javascript')
    @endif
</body>
</html>
```

## Child Template with Vue Component

```php
{{-- resources/views/users/show.blade.php --}}
@extends('layouts.app')

@section('title', "Profile - {$user->name}")

@push('head-scripts')
    <script>
        window.userData = {!! json_encode([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'isAdmin' => $user->isAdmin()
        ]) !!};
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-3">
            <!-- Sidebar Section -->
            @include('users.partials.sidebar', ['user' => $user])
        </div>
        
        <div class="col-md-9">
            <!-- User Profile Vue Component -->
            <user-profile 
                :initial-user="userData"
                :auth-user-id="{{ Auth::id() }}"
                api-endpoint="{{ route('api.user.profile', $user) }}"
            ></user-profile>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ mix('js/user-profile.js') }}"></script>
@endpush

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize any page-specific JS here
            console.log('User profile page loaded');
        });
    </script>
@endsection
```

## Nested Child Template with Alpine.js

```php
{{-- resources/views/users/admin/show.blade.php --}}
@extends('users.show')

@section('title', "Admin View - {$user->name}")

@push('head-scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush

@section('content')
    @parent
    
    <div x-data="adminUserDashboard()" x-init="init()">
        <!-- Admin Tools Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h3>Admin Tools</h3>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <button @click="impersonateUser" class="btn btn-warning">
                            Impersonate User
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button @click="resetPassword" class="btn btn-danger">
                            Reset Password
                        </button>
                    </div>
                </div>
                
                <!-- Activity Log -->
                <div class="mt-4" x-show="showActivityLog">
                    @include('users.admin.partials.activity-log')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function adminUserDashboard() {
            return {
                showActivityLog: false,
                
                init() {
                    // Initialize the dashboard
                    console.log('Admin dashboard initialized');
                },
                
                impersonateUser() {
                    axios.post('/admin/impersonate', {
                        user_id: {{ $user->id }}
                    }).then(response => {
                        window.location.href = '/dashboard';
                    });
                },
                
                resetPassword() {
                    if (confirm('Are you sure you want to reset this user\'s password?')) {
                        axios.post('/admin/reset-password', {
                            user_id: {{ $user->id }}
                        }).then(response => {
                            alert('Password reset email sent!');
                        });
                    }
                }
            }
        }
    </script>
@endpush
```

## Component with Livewire

```php
{{-- resources/views/users/partials/status-updater.blade.php --}}
<div>
    <!-- Livewire Component -->
    @livewire('user-status-updater', ['userId' => $user->id])
    
    <!-- Alternative Vue Component -->
    <user-status-updater 
        :user-id="{{ $user->id }}"
        current-status="{{ $user->status }}"
        api-endpoint="{{ route('api.user.status.update', $user) }}"
    ></user-status-updater>
</div>
```

## Dynamic Script Loading

```php
{{-- resources/views/products/show.blade.php --}}
@extends('layouts.app')

@section('title', $product->name)

@push('scripts')
    @if($product->has3DViewer())
        <script src="{{ asset('js/threejs-viewer.js') }}"></script>
        <script>
            init3DViewer('product-3d-viewer', {
                modelUrl: '{{ $product->get3DModelUrl() }}',
                textures: {!! json_encode($product->get3DTextures()) !!}
            });
        </script>
    @endif
    
    @if($product->hasARSupport())
        <script src="{{ asset('js/ar-viewer.js') }}" defer></script>
    @endif
@endpush

@section('content')
    <div class="product-container">
        <!-- Product content -->
        
        @if($product->has3DViewer())
            <div id="product-3d-viewer" class="product-viewer"></div>
        @endif
    </div>
@endsection
```

## Admin Dashboard with Chart.js

```php
{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@push('head-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-8">
            <canvas id="userGrowthChart" height="300"></canvas>
        </div>
        <div class="col-md-4">
            <canvas id="userActivityChart" height="300"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // User Growth Chart
            const growthCtx = document.getElementById('userGrowthChart').getContext('2d');
            new Chart(growthCtx, {
                type: 'line',
                data: {!! json_encode($userGrowthData) !!},
                options: { responsive: true }
            });
            
            // User Activity Chart
            const activityCtx = document.getElementById('userActivityChart').getContext('2d');
            new Chart(activityCtx, {
                type: 'doughnut',
                data: {!! json_encode($userActivityData) !!},
                options: { responsive: true }
            });
        });
    </script>
@endpush
```

## Best Practices for JavaScript in Blade Templates

1. **CSRF Protection**: Always include CSRF token in AJAX requests
   ```javascript
   axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
   ```

2. **Data Passing**: Use JSON for passing PHP data to JavaScript
   ```php
   <script>
       window.appData = {!! json_encode($data) !!};
   </script>
   ```

3. **Asset Management**: Use Laravel Mix for compiling assets
   ```javascript
   <script src="{{ mix('js/app.js') }}"></script>
   ```

4. **Component Architecture**: Prefer Vue/React/Livewire components for complex UI

5. **Event Handling**: Use DOM events for communication between scripts
   ```javascript
   window.dispatchEvent(new CustomEvent('user-updated', { detail: userData }));
   ```

6. **Lazy Loading**: Load heavy scripts only when needed
   ```php
   @push('scripts')
       @if($needsMap)
           <script src="https://maps.api.com" async defer></script>
       @endif
   @endpush
   ```

7. **Error Handling**: Wrap scripts in try-catch blocks
   ```javascript
   try {
       // Your code here
   } catch (error) {
       console.error('Error:', error);
       if (window.Laravel.env === 'local') {
           alert('Debug: ' + error.message);
       }
   }
   ```

8. **DOM Ready**: Always wait for DOM to be fully loaded
   ```javascript
   document.addEventListener('DOMContentLoaded', function() {
       // Your code here
   });
   ```

This structure provides a clean way to integrate JavaScript with Laravel Blade templates while maintaining organization and performance.