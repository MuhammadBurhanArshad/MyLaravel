# Routing Parameters & Constraints in Laravel

## Basic Route Parameters

### Required Parameters
```php
Route::get('/user/{id}', function ($id) {
    return "User ID: " . $id;
});
```

### Multiple Parameters
```php
Route::get('/posts/{post}/comments/{comment}', function ($postId, $commentId) {
    return "Post $postId, Comment $commentId";
});
```

### Optional Parameters
```php
Route::get('/users/{name?}', function ($name = 'Guest') {
    return "Hello, " . $name;
});
```

## Parameter Constraints

### Regular Expression Constraints
```php
// Numeric ID only
Route::get('/user/{id}', function ($id) {
    // ...
})->where('id', '[0-9]+');

// Alphabetic name only
Route::get('/user/{name}', function ($name) {
    // ...
})->where('name', '[A-Za-z]+');

// Multiple constraints
Route::get('/posts/{post}/comments/{comment}', function ($postId, $commentId) {
    // ...
})->where(['post' => '[0-9]+', 'comment' => '[0-9]+']);
```

### Global Constraints (in RouteServiceProvider)
```php
public function boot()
{
    Route::pattern('id', '[0-9]+');
    // Now all {id} parameters must be numeric
    parent::boot();
}
```

## Route Model Binding

### Implicit Binding
```php
// Laravel will automatically inject the model instance
Route::get('/posts/{post}', function (App\Post $post) {
    return $post;
});
```

### Custom Key Binding
```php
// Use slug instead of ID
Route::get('/posts/{post:slug}', function (App\Post $post) {
    return $post;
});
```

### Explicit Binding (in RouteServiceProvider)
```php
public function boot()
{
    parent::boot();
    
    Route::bind('post', function ($value) {
        return App\Post::where('slug', $value)->firstOrFail();
    });
}
```

## Special Parameter Handling

### Encoded Forward Slashes
```php
Route::get('/search/{query}', function ($query) {
    // Allows / in the parameter
})->where('query', '.*');
```

### UUID Constraints
```php
Route::get('/orders/{id}', function ($id) {
    // ...
})->where('id', '[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}');
```

## Common Parameter Patterns

### Date Parameters
```php
Route::get('/archive/{year}/{month}/{day}', function ($year, $month, $day) {
    return "Archive for $year-$month-$day";
})->where([
    'year' => '\d{4}',
    'month' => '\d{2}',
    'day' => '\d{2}'
]);
```

### Localized Parameters
```php
Route::get('/{locale}/products', function ($locale) {
    // Handle locale-specific products
})->where('locale', 'en|fr|de|es');
```

## Best Practices for Parameters

1. **Be specific with constraints** to avoid route conflicts
2. **Use model binding** where appropriate for cleaner code
3. **Consider parameter order** - more specific routes should come first
4. **Document your constraints** with comments

```php
// Good practice
Route::get('/products/{category}/{product:slug}', function (Category $category, Product $product) {
    return view('products.show', compact('category', 'product'));
})->where('category', 'hardware|software|services');

// Potential issues
Route::get('/products/{product}', function ($product) {
    // Could conflict with other similar routes
});
```

## Advanced Parameter Techniques

### Custom Regex Constraints
```php
Route::get('/{username}', function ($username) {
    // Twitter-style username
})->where('username', '@[A-Za-z0-9_]{1,15}');
```

### Conditional Constraints
```php
if (app()->environment('production')) {
    Route::get('/admin/{section}', function ($section) {
        // ...
    })->where('section', 'dashboard|users|settings');
}
```

### Parameter Dependency Injection
```php
Route::get('/api/{version}/users', function (string $version) {
    return "API Version $version Users";
})->where('version', 'v1|v2|v3');
```

## Common Pitfalls

1. **Order matters** - specific routes should come before parameterized routes
   ```php
   // Correct order
   Route::get('/users/create', 'UserController@create');
   Route::get('/users/{user}', 'UserController@show');
   
   // Wrong order (create would never match)
   Route::get('/users/{user}', 'UserController@show');
   Route::get('/users/create', 'UserController@create');
   ```

2. **Overly permissive constraints** can lead to unexpected matches
   ```php
   // Too permissive
   Route::get('/{page}', 'PageController@show')->where('page', '.*');
   
   // Better
   Route::get('/{page}', 'PageController@show')->where('page', 'about|contact|privacy');
   ```

3. **Missing parameter type validation** can cause errors
   ```php
   // Without constraint
   Route::get('/products/{id}', function ($id) {
       $product = Product::find($id); // $id could be non-numeric
   });
   
   // With constraint
   Route::get('/products/{id}', function ($id) {
       $product = Product::find($id); // $id is guaranteed numeric
   })->where('id', '[0-9]+');
   ```

Remember: Proper use of route parameters and constraints makes your application more robust and secure. Always validate parameter input, either through route constraints or additional validation in your controllers. Use model binding where possible to reduce boilerplate code, and be mindful of route ordering to prevent unexpected behavior.
