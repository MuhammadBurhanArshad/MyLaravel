# Model Factories in Laravel

## Definition
Model factories are a way to generate fake data for your Eloquent models, primarily used for testing and database seeding. Laravel's factories provide a convenient way to create model instances with realistic, randomized data.

## Basic Factory Structure

### Creating a Factory
```bash
php artisan make:factory PostFactory --model=Post
```

### Basic Syntax
```php
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
```

## Common Factory Patterns

### Basic Model Attributes
```php
public function definition()
{
    return [
        'name' => $this->faker->name,
        'email' => $this->faker->unique()->safeEmail,
        'phone' => $this->faker->phoneNumber,
        'address' => $this->faker->address,
        'active' => $this->faker->boolean,
    ];
}
```

### Date and Time Attributes
```php
public function definition()
{
    return [
        'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        'updated_at' => now(),
        'expires_at' => $this->faker->dateTimeBetween('now', '+1 year'),
    ];
}
```

### Numeric Values
```php
public function definition()
{
    return [
        'price' => $this->faker->randomFloat(2, 10, 1000),
        'quantity' => $this->faker->numberBetween(1, 100),
        'rating' => $this->faker->numberBetween(1, 5),
    ];
}
```

### Text and Content
```php
public function definition()
{
    return [
        'title' => $this->faker->sentence,
        'body' => $this->faker->paragraphs(3, true),
        'excerpt' => $this->faker->text(200),
    ];
}
```

## Advanced Factory Techniques

### Relationships in Factories

#### Has Many Relationship
```php
// UserFactory.php
public function definition()
{
    return [
        'name' => $this->faker->name,
        'email' => $this->faker->unique()->safeEmail,
    ];
}

public function withPosts()
{
    return $this->has(Post::factory()->count(3));
}

// Usage
User::factory()->withPosts()->create();
```

#### Belongs To Relationship
```php
// PostFactory.php
public function definition()
{
    return [
        'title' => $this->faker->sentence,
        'content' => $this->faker->paragraph,
        'user_id' => User::factory(),
    ];
}

// Usage - creates both post and associated user
Post::factory()->create();
```

#### Many-to-Many Relationship
```php
// PostFactory.php
public function withTags()
{
    return $this->afterCreating(function (Post $post) {
        $post->tags()->attach(
            Tag::factory()->count(3)->create()
        );
    });
}

// Usage
Post::factory()->withTags()->create();
```

### State Modifications

#### Basic State
```php
// UserFactory.php
public function definition()
{
    return [
        'name' => $this->faker->name,
        'admin' => false,
    ];
}

public function admin()
{
    return $this->state([
        'admin' => true,
        'email' => 'admin@example.com',
    ]);
}

// Usage
User::factory()->admin()->create();
```

#### Dynamic States
```php
public function suspended($days = 30)
{
    return $this->state([
        'suspended_until' => now()->addDays($days),
    ]);
}

// Usage
User::factory()->suspended(60)->create();
```

### Callbacks (After Creating/Making)
```php
public function definition()
{
    return [
        'name' => $this->faker->company,
    ];
}

public function configure()
{
    return $this->afterCreating(function (Company $company) {
        $company->departments()->saveMany(
            Department::factory()->count(3)->make()
        );
    });
}

// Usage - automatically creates departments
Company::factory()->create();
```

## Factory Relationships

### Has One Relationship
```php
// UserFactory.php
public function withProfile()
{
    return $this->has(Profile::factory());
}

// Usage
User::factory()->withProfile()->create();
```

### Polymorphic Relationships
```php
// CommentFactory.php
public function definition()
{
    return [
        'body' => $this->faker->paragraph,
        'commentable_id' => Post::factory(),
        'commentable_type' => Post::class,
    ];
}

// Usage
Comment::factory()->create(); // Creates post and comment
```

### Self-Referencing Relationships
```php
// CategoryFactory.php
public function definition()
{
    return [
        'name' => $this->faker->word,
        'parent_id' => null,
    ];
}

public function withParent()
{
    return $this->state([
        'parent_id' => Category::factory(),
    ]);
}

// Usage
Category::factory()->withParent()->create();
```

## Testing with Factories

### Creating Models
```php
// Create a single model and save to database
$user = User::factory()->create();

// Create multiple models
$users = User::factory()->count(5)->create();

// Create model without saving
$user = User::factory()->make();
```

### Overriding Attributes
```php
// Override specific attributes
$user = User::factory()->create([
    'email' => 'specific@example.com',
]);

// Create with relationships
$post = Post::factory()->create([
    'user_id' => $existingUser->id,
]);
```

### Testing Scenarios
```php
// In your test
public function test_admin_can_delete_users()
{
    $admin = User::factory()->admin()->create();
    $users = User::factory()->count(3)->create();
    
    $response = $this->actingAs($admin)
        ->delete('/users/' . $users[0]->id);
    
    $response->assertStatus(204);
    $this->assertDatabaseMissing('users', ['id' => $users[0]->id]);
}
```

## Factory Best Practices

1. **Keep factories focused** - Each factory should handle one model type
2. **Use realistic fake data** - Makes tests more meaningful
3. **Leverage states** - For variations of the same model
4. **Define relationships clearly** - Makes complex model creation easier
5. **Consider performance** - Use `make()` when persistence isn't needed

```php
// Good practice example
class ProductFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->words(3, true),
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'stock' => $this->faker->numberBetween(0, 100),
            'category_id' => Category::factory(),
        ];
    }

    public function outOfStock()
    {
        return $this->state([
            'stock' => 0,
        ]);
    }

    public function withReviews()
    {
        return $this->afterCreating(function (Product $product) {
            Review::factory()
                ->count($this->faker->numberBetween(1, 5))
                ->for($product)
                ->create();
        });
    }
}

// Usage examples
$availableProduct = Product::factory()->create();
$outOfStockProduct = Product::factory()->outOfStock()->create();
$popularProduct = Product::factory()->withReviews()->create();
```

## Common Pitfalls

1. **Forgetting unique constraints** - Can cause errors with duplicate data
2. **Overcomplicating factories** - Keep them simple and focused
3. **Ignoring database constraints** - Like required fields or foreign keys
4. **Creating unnecessary models** - Can slow down tests

```php
// Problematic example
class BadUserFactory extends Factory
{
    public function definition()
    {
        return [
            'email' => 'test@example.com', // Not unique
            // Missing required 'name' field
        ];
    }
}

// Better approach
class GoodUserFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
        ];
    }
}
```

## Performance Optimization

### Lazy Relationships
```php
// Instead of creating related models immediately
User::factory()->has(Post::factory()->count(3))->create();

// Use lazy loading when possible
$user = User::factory()->create();
$posts = Post::factory()->for($user)->count(3)->create();
```

### Reusing Models
```php
// Create once, use many times
$category = Category::factory()->create();

Product::factory()
    ->count(10)
    ->for($category)
    ->create();
```

### Using `make()` When Possible
```php
// When you don't need to persist to database
$users = User::factory()->count(5)->make();
```

## Special Cases

### UUID Primary Keys
```php
class ProductFactory extends Factory
{
    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'name' => $this->faker->word,
        ];
    }
}
```

### Enum Attributes
```php
class OrderFactory extends Factory
{
    public function definition()
    {
        return [
            'status' => $this->faker->randomElement(['pending', 'completed', 'cancelled']),
        ];
    }
}
```

### JSON Data
```php
class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'preferences' => [
                'notifications' => $this->faker->boolean,
                'theme' => $this->faker->randomElement(['light', 'dark']),
            ],
        ];
    }
}
```

## Faker Library Methods

### Common Faker Methods
```php
$this->faker->name; // Random name
$this->faker->email; // Random email
$this->faker->url; // Random URL
$this->faker->imageUrl(); // Random image URL
$this->faker->dateTime; // Random DateTime
$this->faker->hexColor; // Random color
$this->faker->randomElement(['a', 'b', 'c']); // Random array element
$this->faker->randomDigit; // Random digit 0-9
$this->faker->text(200); // Random text (200 chars)
$this->faker->uuid; // Random UUID
```

### Localized Fake Data
```php
// In your AppServiceProvider
$this->app->singleton('Faker\Generator', function () {
    return \Faker\Factory::create('fr_FR'); // French data
});

// Then in factories
$this->faker->name; // French names
$this->faker->address; // French addresses
```

## Factory Sequences

### Basic Sequence
```php
public function definition()
{
    return [
        'name' => $this->faker->name,
        'status' => $this->faker->sequence(
            'pending',
            'processing',
            'completed'
        ),
    ];
}

// Usage - each created model gets next sequence value
$orders = Order::factory()->count(3)->create();
// $orders[0]->status = 'pending'
// $orders[1]->status = 'processing'
// $orders[2]->status = 'completed'
```

### Advanced Sequence
```php
public function definition()
{
    return [
        'discount' => $this->faker->sequence(
            fn ($sequence) => $sequence->index * 10
        ),
    ];
}

// Usage
$products = Product::factory()->count(5)->create();
// Discounts will be 0, 10, 20, 30, 40
```

## Factory Helpers

### Creating Related Models
```php
// Create user with 3 posts
$user = User::factory()
    ->hasPosts(3)
    ->create();

// Create post with specific user
$post = Post::factory()
    ->forUser($existingUser)
    ->create();

// Create post with new user having specific attributes
$post = Post::factory()
    ->forUser([
        'name' => 'Specific User',
    ])
    ->create();
```

### Multiple Relationships
```php
// Create user with posts and comments
$user = User::factory()
    ->hasPosts(3)
    ->hasComments(5)
    ->create();

// Create post with tags and author
$post = Post::factory()
    ->forAuthor()
    ->hasTags(3)
    ->create();
```

Remember: Model factories are powerful tools that can greatly simplify your testing and seeding workflows. By defining comprehensive factories, you can easily create complex model graphs with realistic data, making your tests more reliable and your development process more efficient.