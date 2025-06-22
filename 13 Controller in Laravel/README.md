# Controllers in Laravel

## Definition
Controllers in Laravel serve as the intermediary between routes and application logic. They group related HTTP request handling logic into a single class, typically stored in the `app/Http/Controllers` directory.

## Basic Syntax

### Creating a Controller
```bash
php artisan make:controller ControllerName
```

### Controller Structure
```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ControllerName extends Controller
{
    // Controller methods
}
```

## Common Examples

### Basic Controller
```php
class UserController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function show($id)
    {
        return view('users.show', ['user' => User::findOrFail($id)]);
    }
}
```

### Resource Controller
```php
// Generate with all resource methods
php artisan make:controller PhotoController --resource
```

```php
class PhotoController extends Controller
{
    public function index() {}
    public function create() {}
    public function store(Request $request) {}
    public function show($id) {}
    public function edit($id) {}
    public function update(Request $request, $id) {}
    public function destroy($id) {}
}
```

### Single Action Controller
```php
php artisan make:controller ShowProfile --invokable
```

```php
class ShowProfile extends Controller
{
    public function __invoke($id)
    {
        return view('profile', ['user' => User::findOrFail($id)]);
    }
}
```

## Controller Commands

Laravel provides several Artisan commands for working with controllers:

```bash
# Create a basic controller
php artisan make:controller UserController

# Create a resource controller
php artisan make:controller PhotoController --resource

# Create an API resource controller (excludes create/edit methods)
php artisan make:controller API/PhotoController --api

# Create an invokable controller
php artisan make:controller ProvisionServer --invokable

# Create a controller with model binding
php artisan make:controller PhotoController --model=Photo
```

## Best Practices

1. **Keep controllers thin** - Move business logic to service classes
2. **Use dependency injection** for required services
3. **Follow RESTful conventions** for resource controllers
4. **Type-hint models** for implicit binding
5. **Use form requests** for complex validation

```php
// Good practice
class PostController extends Controller
{
    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update($request->validated());
        return redirect()->route('posts.show', $post);
    }
}

// Avoid
class PostController extends Controller
{
    public function update(Request $request, $id)
    {
        // 50 lines of validation and logic
    }
}
```

## Common Use Cases

### Dependency Injection
```php
class OrderController extends Controller
{
    public function __construct(
        protected OrderRepository $orders
    ) {}

    public function index()
    {
        return view('orders', ['orders' => $this->orders->all()]);
    }
}
```

### Resource Controllers with Relationships
```php
class PostCommentController extends Controller
{
    public function index(Post $post)
    {
        return view('comments.index', ['comments' => $post->comments]);
    }

    public function store(StoreCommentRequest $request, Post $post)
    {
        $post->comments()->create($request->validated());
        return redirect()->route('posts.comments.index', $post);
    }
}
```

### API Resource Controllers
```php
class APIPhotoController extends Controller
{
    public function index()
    {
        return PhotoResource::collection(Photo::all());
    }

    public function store(StorePhotoRequest $request)
    {
        $photo = Photo::create($request->validated());
        return new PhotoResource($photo);
    }
}
```

## Special Cases

### Implicit Model Binding
```php
// Route: /posts/{post}
public function show(Post $post)
{
    return view('posts.show', compact('post'));
}

// Custom key
public function show(Post $post:slug)
{
    return view('posts.show', compact('post'));
}
```

### Nested Controllers
```php
// app/Http/Controllers/Admin/UserController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    // Admin-specific user methods
}
```

### Controller Middleware
```php
class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('log')->only('index');
        $this->middleware('subscribed')->except('store');
    }
}
```

## Performance Considerations

1. **Use method injection** rather than instantiating services manually
2. **Cache expensive operations** that don't change often
3. **Eager load relationships** to avoid N+1 problems
4. **Paginate results** for large datasets

```php
// Optimized for performance
class ProductController extends Controller
{
    public function index()
    {
        return view('products.index', [
            'products' => Product::with('category')
                                ->latest()
                                ->paginate(20)
        ]);
    }
}
```

## Common Pitfalls

1. **Fat controllers**:
   ```php
   // Avoid
   public function store(Request $request)
   {
       // 100 lines of validation, business logic, and response handling
   }
   ```

2. **Direct DB access in controllers**:
   ```php
   // Avoid
   public function index()
   {
       return DB::table('users')->get();
   }
   ```

3. **Not using route model binding**:
   ```php
   // Avoid
   public function show($id)
   {
       $user = User::find($id);
   }
   
   // Better
   public function show(User $user) {}
   ```

## Advanced Patterns

### Controller Traits
```php
trait FiltersUsers
{
    protected function filterActive($query)
    {
        return $query->where('active', true);
    }
}

class UserController extends Controller
{
    use FiltersUsers;

    public function index()
    {
        return User::query()
            ->when(request('active'), $this->filterActive(...))
            ->get();
    }
}
```

### Action Classes
```php
class UpdateUserProfile
{
    public function __construct(
        protected UserRepository $users
    ) {}

    public function execute(User $user, array $data)
    {
        $this->users->update($user, $data);
        return $user->fresh();
    }
}

class UserController extends Controller
{
    public function update(UpdateUserRequest $request, User $user)
    {
        $user = (new UpdateUserProfile($this->users))->execute(
            $user, $request->validated()
        );
        return new UserResource($user);
    }
}
```

### Resource Controllers with Scopes
```php
class ProjectController extends Controller
{
    public function index()
    {
        return view('projects.index', [
            'projects' => Project::query()
                ->when(request('status'), fn($q, $status) => 
                    $q->where('status', $status))
                ->when(request('search'), fn($q, $search) => 
                    $q->where('name', 'like', "%{$search}%"))
                ->paginate()
        ]);
    }
}
```

Remember: Controllers should primarily be responsible for handling HTTP requests and returning responses. Keep business logic in dedicated service classes or action classes. Use resource controllers for CRUD operations, and consider single action controllers for isolated actions. Always type-hint your dependencies and leverage Laravel's powerful features like route model binding and form request validation.