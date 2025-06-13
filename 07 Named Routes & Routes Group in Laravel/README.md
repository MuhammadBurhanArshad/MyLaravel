# Named Routes & Route Groups in Laravel

## Named Routes

### Basic Named Route
```php
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
```

### Named Route with Parameters
```php
Route::get('/user/{id}/profile', function ($id) {
    return "Profile for user $id";
})->name('profile');
```

### Generating URLs to Named Routes
```php
// Basic URL generation
$url = route('dashboard');

// With parameters
$url = route('profile', ['id' => 1]);

// With optional parameters
Route::get('/user/{id}/{name?}', function ($id, $name = null) {
    // ...
})->name('user.profile');

$url = route('user.profile', ['id' => 1, 'name' => 'john']);
```

### Redirecting to Named Routes
```php
return redirect()->route('dashboard');
return to_route('profile', ['id' => 1]);
```

### Checking Current Route
```php
if (Route::currentRouteName() === 'dashboard') {
    // Current route is dashboard
}
```

## Route Groups

### Basic Route Group
```php
Route::group([], function () {
    Route::get('/users', function () { /* ... */ });
    Route::get('/posts', function () { /* ... */ });
});
```

### Prefix Groups
```php
Route::prefix('admin')->group(function () {
    Route::get('/users', function () {
        // Matches "/admin/users"
    });
    
    Route::get('/settings', function () {
        // Matches "/admin/settings"
    });
});
```

### Middleware Groups
```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', function () {
        // Requires auth and verified
    });
    
    Route::get('/settings', function () {
        // Requires auth and verified
    });
});
```

### Name Prefix Groups
```php
Route::name('admin.')->group(function () {
    Route::get('/users', function () {
        // Route name: "admin.users"
    })->name('users');
    
    Route::get('/settings', function () {
        // Route name: "admin.settings"
    })->name('settings');
});
```

### Multiple Group Attributes
```php
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        // Matches "/admin/dashboard"
        // Name: "admin.dashboard"
        // Middleware: auth
    })->name('dashboard');
});
```

## Common Group Patterns

### API Versioning
```php
Route::prefix('v1')->group(function () {
    Route::get('/users', 'Api\V1\UserController@index');
    Route::get('/posts', 'Api\V1\PostController@index');
});

Route::prefix('v2')->group(function () {
    Route::get('/users', 'Api\V2\UserController@index');
    Route::get('/posts', 'Api\V2\PostController@index');
});
```

### Localization Groups
```php
Route::prefix('{locale}')->where(['locale' => 'en|fr|de'])->group(function () {
    Route::get('/about', function ($locale) {
        // Handle localized about page
    });
    
    Route::get('/contact', function ($locale) {
        // Handle localized contact page
    });
});
```

### Admin Panel Group
```php
Route::prefix('admin')
     ->name('admin.')
     ->middleware(['auth', 'admin'])
     ->group(function () {
         Route::get('/dashboard', 'AdminController@dashboard')->name('dashboard');
         Route::resource('users', 'Admin\UserController');
         Route::resource('posts', 'Admin\PostController');
     });
```

## Advanced Group Techniques

### Subdomain Routing
```php
Route::domain('{account}.example.com')->group(function () {
    Route::get('user/{id}', function ($account, $id) {
        return "User $id for account $account";
    });
});
```

### Namespace Groups
```php
Route::namespace('Admin')->group(function () {
    // Controllers within "App\Http\Controllers\Admin"
    Route::get('/admin/users', 'UserController@index');
});
```

### Fallback Routes in Groups
```php
Route::prefix('api')->group(function () {
    Route::get('/users', 'ApiController@users');
    Route::fallback(function () {
        return response()->json(['error' => 'Not Found'], 404);
    });
});
```

## Best Practices

1. **Use named routes consistently** for easier URL generation and maintenance
2. **Group related routes** logically (by prefix, middleware, etc.)
3. **Avoid deep nesting** - keep groups to 2-3 levels maximum
4. **Document complex groups** with comments

```php
// Good practice
Route::name('api.v1.')->prefix('api/v1')->middleware('api')->group(function () {
    Route::get('/users', 'Api\V1\UserController@index')->name('users.index');
    Route::get('/posts', 'Api\V1\PostController@index')->name('posts.index');
});

// Potential issues
Route::prefix('a')->prefix('b')->prefix('c')->group(function () {
    // Overly nested prefixes are confusing
});
```

## Common Pitfalls

1. **Name collisions** in nested groups
   ```php
   Route::name('admin.')->group(function () {
       Route::name('users.')->group(function () {
           Route::get('/users', function () {})->name('index');
           // Resulting name: admin.users.index
       });
   });
   ```

2. **Middleware ordering** matters in groups
   ```php
   Route::middleware(['first', 'second'])->group(function () {
       // Middleware runs in order: first, then second
   });
   ```

3. **Overusing groups** can make routes harder to read
   ```php
   // Hard to follow
   Route::group([/* ... */], function () {
       Route::group([/* ... */], function () {
           Route::group([/* ... */], function () {
               // ...
           });
       });
   });
   ```

4. **Forgetting to apply constraints** to group parameters
   ```php
   // Without constraint
   Route::prefix('{locale}')->group(function () {
       // Could match any prefix
   });
   
   // With constraint
   Route::prefix('{locale}')->where(['locale' => 'en|fr|de'])->group(function () {
       // Only matches specified locales
   });
   ```

Remember: Named routes and route groups are powerful tools for organizing your application's routing structure. Use them to:
- Reduce duplication (with middleware, prefixes, etc.)
- Improve code organization
- Make URL generation more maintainable
- Apply common functionality to sets of routes

Always consider readability when structuring your routes file, and document complex route groupings with comments where necessary.
