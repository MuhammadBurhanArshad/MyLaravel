# Database Seeding in Laravel

## Definition
Database seeding is the process of populating your database with initial or test data. Laravel provides a simple way to seed your database using seed classes. Seeders are particularly useful for:
- Initial application setup data
- Testing scenarios
- Demonstration purposes
- Development environments

## Basic Seeder Structure

### Creating a Seeder
```bash
php artisan make:seeder UserSeeder
```

### Basic Syntax
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
        ]);
    }
}
```

## Common Seeding Patterns

### Single Model Insertion
```php
use App\Models\User;

public function run()
{
    User::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => bcrypt('password'),
    ]);
}
```

### Multiple Records
```php
public function run()
{
    $users = [
        [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ],
        [
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]
    ];
    
    foreach ($users as $user) {
        User::create($user);
    }
}
```

### Using Factories
```php
use App\Models\User;

public function run()
{
    // Create 10 random users
    User::factory()->count(10)->create();
}
```

### Conditional Seeding
```php
public function run()
{
    if (User::count() == 0) {
        User::factory()->count(5)->create();
    }
}
```

## Advanced Seeding Techniques

### Relationship Seeding
```php
use App\Models\Post;
use App\Models\User;

public function run()
{
    // Create 5 users, each with 3 posts
    User::factory()
        ->count(5)
        ->has(
            Post::factory()->count(3)
        )
        ->create();
}
```

### Many-to-Many Relationship
```php
use App\Models\Role;
use App\Models\User;

public function run()
{
    // Create 3 roles
    $roles = Role::factory()->count(3)->create();
    
    // Create 10 users and attach random roles
    User::factory()
        ->count(10)
        ->create()
        ->each(function ($user) use ($roles) {
            $user->roles()->attach(
                $roles->random(rand(1, 3))->pluck('id')->toArray()
            );
        });
}
```

### Polymorphic Relationships
```php
use App\Models\Comment;
use App\Models\Post;
use App\Models\Video;

public function run()
{
    // Create 5 posts and 5 videos
    $posts = Post::factory()->count(5)->create();
    $videos = Video::factory()->count(5)->create();
    
    // Create comments for random posts and videos
    Comment::factory()
        ->count(20)
        ->create()
        ->each(function ($comment) use ($posts, $videos) {
            if (rand(0, 1)) {
                $comment->commentable()->associate($posts->random())->save();
            } else {
                $comment->commentable()->associate($videos->random())->save();
            }
        });
}
```

### Seeding with Images/Files
```php
use Illuminate\Support\Facades\Storage;

public function run()
{
    $image = file_get_contents('https://example.com/default-profile.jpg');
    $path = 'profiles/' . Str::random(40) . '.jpg';
    Storage::put($path, $image);
    
    User::create([
        'name' => 'Photo User',
        'email' => 'photo@example.com',
        'password' => bcrypt('password'),
        'avatar' => $path,
    ]);
}
```

## DatabaseSeeder Organization

### Calling Specific Seeders
```php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            PostSeeder::class,
            CommentSeeder::class,
        ]);
    }
}
```

### Conditional Seeder Execution
```php
public function run()
{
    if (app()->environment('local')) {
        $this->call(DevSeeder::class);
    } else {
        $this->call(ProductionSeeder::class);
    }
}
```

### Ordered Seeder Execution
```php
public function run()
{
    // Run these first
    $this->call(RoleSeeder::class);
    $this->call(PermissionSeeder::class);
    
    // Then these
    $this->call(UserSeeder::class);
    
    // Finally these
    $this->call(PostSeeder::class);
    $this->call(CommentSeeder::class);
}
```

## Production Considerations

### Safe Production Seeding
```php
public function run()
{
    // Only seed if table is empty
    if (DB::table('settings')->count() == 0) {
        DB::table('settings')->insert([
            ['key' => 'app_name', 'value' => config('app.name')],
            ['key' => 'timezone', 'value' => config('app.timezone')],
        ]);
    }
}
```

### Environment-Specific Seeds
```php
public function run()
{
    $this->call([
        RequiredSettingsSeeder::class,
    ]);
    
    if (app()->environment('local')) {
        $this->call(DevDataSeeder::class);
    }
    
    if (app()->environment('staging')) {
        $this->call(StagingDataSeeder::class);
    }
}
```

## Testing with Seeders

### Seeding for Tests
```php
// In your test
public function setUp(): void
{
    parent::setUp();
    $this->seed(UserSeeder::class);
}

// Or specify in the test method
public function test_user_count()
{
    $this->seed();
    $this->assertDatabaseCount('users', 10);
}
```

### Specific Test Seeding
```php
public function test_admin_user_exists()
{
    $this->seed([
        RoleSeeder::class,
        AdminUserSeeder::class,
    ]);
    
    $this->assertDatabaseHas('users', [
        'email' => 'admin@example.com',
    ]);
}
```

## Performance Optimization

### Chunking Large Datasets
```php
public function run()
{
    $users = [];
    
    for ($i = 0; $i < 10000; $i++) {
        $users[] = [
            'name' => "User $i",
            'email' => "user$i@example.com",
            'password' => bcrypt('password'),
        ];
        
        if ($i % 1000 == 0) {
            User::insert($users);
            $users = [];
        }
    }
    
    if (!empty($users)) {
        User::insert($users);
    }
}
```

### Disabling Events During Seeding
```php
public function run()
{
    // Disable model events to improve performance
    User::withoutEvents(function () {
        User::factory()->count(1000)->create();
    });
}
```

### Transaction-Based Seeding
```php
public function run()
{
    DB::transaction(function () {
        // All seeding happens in a single transaction
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            PostSeeder::class,
        ]);
    });
}
```

## Special Cases

### Seeding Encrypted Data
```php
use Illuminate\Support\Facades\Crypt;

public function run()
{
    User::create([
        'name' => 'Secure User',
        'email' => 'secure@example.com',
        'password' => bcrypt('password'),
        'api_token' => Crypt::encrypt(Str::random(60)),
    ]);
}
```

### Seeding Localized Data
```php
public function run()
{
    $locales = ['en', 'fr', 'es', 'de'];
    
    for ($i = 0; $i < 100; $i++) {
        $locale = $locales[array_rand($locales)];
        
        Post::create([
            'title' => json_encode([
                'en' => "Title in English $i",
                $locale => "Title in " . strtoupper($locale) . " $i"
            ]),
            'content' => "Content for post $i",
        ]);
    }
}
```

### Seeding with Dependencies
```php
public function run()
{
    // First create categories
    $categories = Category::factory()
        ->count(5)
        ->create();
    
    // Then create products with those categories
    Product::factory()
        ->count(50)
        ->create()
        ->each(function ($product) use ($categories) {
            $product->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')
            );
        });
}
```

## Best Practices

1. **Keep seeders focused** - Each seeder should handle one specific area
2. **Use factories for complex data** - Especially when you need many records
3. **Make seeders idempotent** - They should be safe to run multiple times
4. **Separate production and development seeds** - Use environment checks
5. **Document your seeders** - Add comments explaining the purpose
6. **Consider data privacy** - Don't seed real personal information
7. **Order dependencies properly** - Seed parent tables before child tables

```php
// Good practice example
class OrderSeeder extends Seeder
{
    public function run()
    {
        // Only seed if no orders exist
        if (Order::count() > 0) {
            return;
        }
        
        // Get existing users and products
        $users = User::limit(10)->get();
        $products = Product::limit(50)->get();
        
        // Create 100 orders
        Order::factory()
            ->count(100)
            ->make()
            ->each(function ($order) use ($users, $products) {
                $order->user_id = $users->random()->id;
                $order->save();
                
                // Add 1-5 random products to each order
                $order->products()->attach(
                    $products->random(rand(1, 5))->pluck('id'),
                    ['quantity' => rand(1, 3), 'price' => rand(1000, 5000)]
                );
            });
    }
}
```

## Common Pitfalls

1. **Forgetting to call parent seeders** - Missing dependency seeders
2. **Creating duplicate data** - Not checking for existing records
3. **Performance issues with large datasets** - Not chunking inserts
4. **Breaking foreign key constraints** - Wrong seeding order
5. **Overlooking model events** - Which can slow down seeding

```php
// Problematic example
class BadUserSeeder extends Seeder
{
    public function run()
    {
        // Creates duplicates if run multiple times
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        
        // No chunking - memory issues with large numbers
        User::factory()->count(100000)->create();
    }
}

// Better approach
class GoodUserSeeder extends Seeder
{
    public function run()
    {
        // Prevents duplicates
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
            ]
        );
        
        // Chunked creation
        $chunks = 1000;
        $total = 100000;
        
        for ($i = 0; $i < $total; $i += $chunks) {
            User::factory()
                ->count(min($chunks, $total - $i))
                ->create();
        }
    }
}
```

## Running Seeders

### Basic Command
```bash
php artisan db:seed
```

### Running Specific Seeders
```bash
php artisan db:seed --class=UserSeeder
```

### Refresh and Seed
```bash
php artisan migrate:fresh --seed
```

### Isolated Environment Seeding
```bash
php artisan db:seed --env=testing
```

Remember: Seeders are powerful tools that can greatly simplify your development and testing process. Use them wisely to maintain a consistent database state across different environments while keeping your production data safe and secure.