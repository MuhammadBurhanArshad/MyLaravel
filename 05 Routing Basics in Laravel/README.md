# Routing in Laravel

## Definition
Laravel's routing system allows you to define application endpoints and their corresponding actions. Routes are typically defined in `routes/web.php` for web routes and `routes/api.php` for API routes.

## Basic Syntax

### Standard Route Definition
```php
Route::method('uri', function () {
    // Action
});
```

### Controller Route
```php
Route::method('uri', 'Controller@method');
```

### Named Routes
```php
Route::method('uri', function () {
    // ...
})->name('route.name');
```

## Common Examples

### Basic Routes
```php
Route::get('/', function () {
    return view('welcome');
});

Route::post('/submit', function () {
    // Handle form submission
});

Route::put('/update/{id}', function ($id) {
    // Update resource
});

Route::delete('/delete/{id}', function ($id) {
    // Delete resource
});
```

### Controller Routes
```php
Route::get('/users', 'UserController@index');
Route::get('/users/{id}', 'UserController@show');
Route::post('/users', 'UserController@store');
Route::put('/users/{id}', 'UserController@update');
Route::delete('/users/{id}', 'UserController@destroy');
```

## Route Commands

Laravel provides several Artisan commands for working with routes:

```bash
# List all available route commands
php artisan route -h

# Display a list of all registered routes
php artisan route:list

# List only application routes (excluding vendor packages)
php artisan route:list --except-vendor

# Filter routes by path
php artisan route:list --path=post

# Show routes in JSON format
php artisan route:list --json

# Filter routes by method (GET, POST, etc.)
php artisan route:list --method=GET
```

### Route Parameters
```php
Route::get('/posts/{post}', function ($postId) {
    // ...
});

// Optional parameters
Route::get('/users/{name?}', function ($name = 'Guest') {
    // ...
});

// Regular expression constraints
Route::get('/users/{id}', function ($id) {
    // ...
})->where('id', '[0-9]+');
```

## Best Practices

1. **Use resource controllers** for CRUD operations
2. **Name your routes** for easier URL generation
3. **Group related routes** together
4. **Use middleware** for authentication/authorization
5. **Keep routes file organized** with proper comments

```php
// Good practice
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::resource('posts', 'PostController');
});

// Avoid
Route::get('dashboard', 'DashboardController@index');
Route::get('posts', 'PostController@index');
// ... other ungrouped routes
```

## Common Use Cases

### Resource Routes
```php
Route::resource('photos', 'PhotoController');
// Equivalent to:
// GET /photos - index
// GET /photos/create - create
// POST /photos - store
// GET /photos/{photo} - show
// GET /photos/{photo}/edit - edit
// PUT/PATCH /photos/{photo} - update
// DELETE /photos/{photo} - destroy
```

### Route Groups
```php
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    Route::get('/dashboard', 'AdminController@dashboard');
    Route::resource('users', 'AdminUserController');
});
```

### API Routes
```php
Route::prefix('api')->middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::apiResource('products', 'ProductController');
});
```

## Special Cases

### Route Model Binding
```php
// Implicit binding
Route::get('/posts/{post}', function (App\Post $post) {
    return $post;
});

// Custom key
Route::get('/posts/{post:slug}', function (App\Post $post) {
    return $post;
});
```

### Fallback Routes
```php
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
```

### Rate Limiting
```php
Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/profile', function () {
        // ...
    });
});
```

## Performance Considerations

1. **Use route caching** in production: `php artisan route:cache`
2. **Avoid complex logic** in route closures - use controllers
3. **Minimize middleware** on frequently accessed routes
4. **Group routes** with similar middleware

```php
// Optimized for performance
Route::middleware(['cache.headers:max_age=3600'])->group(function () {
    Route::get('/about', 'PageController@about');
    Route::get('/contact', 'PageController@contact');
});
```

## Common Pitfalls

1. **Route parameter conflicts**:
   ```php
   Route::get('/posts/{post}', 'PostController@show');
   Route::get('/posts/create', 'PostController@create'); // This should come before the {post} route
   ```

2. **Missing route names**:
   ```php
   // Hard to maintain
   return redirect('/users/' . $user->id . '/edit');
   
   // Better
   return redirect()->route('users.edit', $user);
   ```

3. **Overusing closures**:
   ```php
   // Avoid for complex logic
   Route::get('/reports', function () {
       // 50 lines of logic
   });
   ```

## Advanced Patterns

### Subdomain Routing
```php
Route::domain('{account}.myapp.com')->group(function () {
    Route::get('user/{id}', function ($account, $id) {
        // ...
    });
});
```

### Localized Routes
```php
Route::group([
    'prefix' => '{locale}',
    'where' => ['locale' => '[a-zA-Z]{2}'],
    'middleware' => 'setlocale'
], function () {
    Route::get('/', 'HomeController@index');
    Route::get('/about', 'PageController@about');
});
```

### Signed URLs
```php
Route::get('/unsubscribe/{user}', function (Request $request) {
    if (! $request->hasValidSignature()) {
        abort(401);
    }
    // ...
})->name('unsubscribe');
```

Remember: Laravel's routing system is powerful and flexible. Always consider using controller methods for complex logic rather than route closures. Take advantage of route caching in production for better performance, and use named routes to make your application more maintainable. For API development, consider using API resource controllers and proper versioning from the start.
