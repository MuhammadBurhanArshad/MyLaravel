# Passing Data: Route to View in Laravel

## Basic Data Passing

### Simple Data Passing
```php
Route::get('/welcome', function () {
    return view('welcome', ['name' => 'John']);
});
```

### Using with() Method
```php
Route::get('/dashboard', function () {
    return view('dashboard')->with('title', 'Dashboard');
});
```

### Multiple with() Calls
```php
Route::get('/profile', function () {
    return view('profile')
        ->with('name', 'John')
        ->with('age', 30);
});
```

## Advanced Data Passing Techniques

### Compact Helper
```php
Route::get('/posts/{post}', function ($postId) {
    $post = Post::find($postId);
    $comments = $post->comments;
    $author = $post->author;
    
    return view('posts.show', compact('post', 'comments', 'author'));
});
```

### Using with() + compact()
```php
Route::get('/products/{product}', function ($productId) {
    $product = Product::find($productId);
    $related = $product->relatedItems();
    
    return view('products.show')
        ->with(compact('product'))
        ->with('related', $related);
});
```

## Array Data Passing

### Associative Array
```php
Route::get('/settings', function () {
    return view('settings', [
        'user' => Auth::user(),
        'preferences' => Preferences::all(),
        'notifications' => Notification::unread()
    ]);
});
```

### Dynamic Data
```php
Route::get('/reports/{type}', function ($type) {
    $data = [
        'sales' => ['title' => 'Sales Report', 'data' => Sales::all()],
        'inventory' => ['title' => 'Inventory Report', 'data' => Inventory::all()]
    ];
    
    return view('reports', $data[$type]);
});
```

## Conditional Data Passing

### Environment-Based Data
```php
Route::get('/debug', function () {
    $data = [
        'debug_info' => Debug::getInfo()
    ];
    
    if (app()->environment('local')) {
        $data['extra_debug'] = Debug::getExtraInfo();
    }
    
    return view('debug', $data);
});
```

### Role-Based Data
```php
Route::get('/admin', function () {
    $data = [
        'stats' => Stats::basic()
    ];
    
    if (auth()->user()->isAdmin()) {
        $data['advanced_stats'] = Stats::advanced();
    }
    
    return view('admin', $data);
});
```

## View Composers (Alternative Approach)

### Using View Composers
```php
// In a service provider
View::composer('profile', function ($view) {
    $view->with('user', auth()->user());
});

// Then in routes
Route::get('/profile', function () {
    return view('profile'); // User data automatically available
});
```

## Best Practices for Data Passing

1. **Keep controllers thin** - pass only necessary data
2. **Use meaningful variable names** in views
3. **Consider view composers** for shared data
4. **Validate data** before passing to views
5. **Use type hints** where possible

```php
// Good practice
Route::get('/products/{product}', function (Product $product) {
    return view('products.show', [
        'product' => $product,
        'related' => $product->related()->limit(4)->get()
    ]);
});

// Avoid
Route::get('/products/{id}', function ($id) {
    $data = Product::find($id);
    $more_data = /* complex query */;
    $extra = /* more processing */;
    
    return view('products.show', [
        'data' => $data,
        'more' => $more_data,
        'x' => $extra
    ]);
});
```

## Common Data Passing Patterns

### Pagination Data
```php
Route::get('/articles', function () {
    return view('articles.index', [
        'articles' => Article::paginate(10)
    ]);
});
```

### Flash Messages
```php
Route::post('/contact', function () {
    // Process contact form
    
    return redirect('/')
        ->with('success', 'Your message has been sent!');
});
```

### Form Data
```php
Route::get('/users/create', function () {
    return view('users.create', [
        'roles' => Role::all(),
        'departments' => Department::all()
    ]);
});
```

## Error Handling in Data Passing

### Handling Missing Data
```php
Route::get('/products/{product}', function (Product $product) {
    if (!$product->isActive()) {
        abort(404);
    }
    
    return view('products.show', compact('product'));
});
```

### Fallback Data
```php
Route::get('/profile', function () {
    return view('profile', [
        'notifications' => auth()->user()->notifications ?? []
    ]);
});
```

## Performance Considerations

### Eager Loading
```php
Route::get('/posts/{post}', function (Post $post) {
    $post->load(['comments.user', 'tags']);
    
    return view('posts.show', compact('post'));
});
```

### Caching Data
```php
Route::get('/stats', function () {
    $stats = Cache::remember('dashboard_stats', 3600, function () {
        return [
            'user_count' => User::count(),
            'order_count' => Order::thisMonth()->count(),
            'revenue' => Order::thisMonth()->sum('total')
        ];
    });
    
    return view('stats', $stats);
});
```

## Advanced Techniques

### Dynamic View Selection
```php
Route::get('/pages/{page}', function ($page) {
    $view = "pages.{$page}";
    
    if (view()->exists($view)) {
        return view($view, [
            'content' => Page::where('slug', $page)->firstOrFail()
        ]);
    }
    
    abort(404);
});
```

### JSON Data to Views
```php
Route::get('/api-docs', function () {
    $spec = json_decode(file_get_contents(resource_path('docs/api.json')));
    
    return view('api.docs', [
        'endpoints' => $spec->endpoints,
        'version' => $spec->version
    ]);
});
```

## Security Considerations

### Sanitizing Data
```php
Route::get('/user/{id}', function ($id) {
    return view('profile', [
        'user' => User::findOrFail(clean_input($id))
    ]);
});
```

### Authorization Checks
```php
Route::get('/reports/{report}', function (Report $report) {
    if (!auth()->user()->can('view', $report)) {
        abort(403);
    }
    
    return view('reports.show', compact('report'));
});
```

Remember: When passing data from routes to views, always consider:
1. **What data the view actually needs**
2. **The performance impact of data loading**
3. **Security implications of exposed data**
4. **Maintainability of your data passing structure**

The most common and recommended approaches are using the associative array format or the `compact()` helper for cleaner code, especially when dealing with multiple variables. For application-wide data, view composers can help reduce duplication.