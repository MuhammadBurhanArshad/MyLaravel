# Query Builder in Laravel

## Definition
The Query Builder provides a convenient, fluent interface for creating and running database queries. It can be used to perform most database operations in your application and works on all supported database systems.

## Basic Query Structure

### Creating a Query
```php
use Illuminate\Support\Facades\DB;

$users = DB::table('users')->get();
```

### Basic Syntax
```php
$results = DB::table('table_name')
    ->select('column1', 'column2')
    ->where('column', 'value')
    ->orderBy('column', 'asc')
    ->get();
```

## Common Query Patterns

### Basic Select Queries
```php
// Get all rows
$users = DB::table('users')->get();

// Get first row
$user = DB::table('users')->first();

// Get single column
$emails = DB::table('users')->pluck('email');

// Get key-value pairs
$users = DB::table('users')->pluck('name', 'id');
```

### Conditional Queries
```php
$users = DB::table('users')
    ->where('active', 1)
    ->where('age', '>', 18)
    ->orWhere('vip', 1)
    ->get();
```

### Joins
```php
// Basic join
$orders = DB::table('orders')
    ->join('users', 'users.id', '=', 'orders.user_id')
    ->select('orders.*', 'users.name')
    ->get();

// Left join
$posts = DB::table('posts')
    ->leftJoin('users', 'users.id', '=', 'posts.user_id')
    ->get();

// Multiple joins
$results = DB::table('orders')
    ->join('users', 'users.id', '=', 'orders.user_id')
    ->join('products', 'products.id', '=', 'orders.product_id')
    ->select('orders.*', 'users.name', 'products.title')
    ->get();
```

## Advanced Query Techniques

### Subqueries
```php
// Using subquery in select
$users = DB::table('users')
    ->select('name', 'email', function($query) {
        $query->select('count(*)')
            ->from('orders')
            ->whereColumn('orders.user_id', 'users.id');
    })
    ->get();

// Using subquery in where
$users = DB::table('users')
    ->whereExists(function($query) {
        $query->select(DB::raw(1))
            ->from('orders')
            ->whereColumn('orders.user_id', 'users.id');
    })
    ->get();
```

### Unions
```php
$first = DB::table('users')
    ->whereNull('first_name');

$users = DB::table('users')
    ->whereNull('last_name')
    ->union($first)
    ->get();
```

### JSON Where Clauses
```php
$users = DB::table('users')
    ->where('preferences->theme', 'dark')
    ->get();

$users = DB::table('users')
    ->whereJsonContains('options->languages', 'en')
    ->get();
```

### Conditional Clauses
```php
$query = DB::table('users')
    ->when($request->input('search'), function ($query, $search) {
        return $query->where('name', 'like', '%'.$search.'%');
    })
    ->get();
```

## Query Builder Relationships

### Has One Relationship
```php
$usersWithProfile = DB::table('users')
    ->leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
    ->select('users.*', 'profiles.bio', 'profiles.avatar')
    ->get();
```

### Has Many Relationship
```php
$usersWithPosts = DB::table('users')
    ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
    ->select('users.*', DB::raw('COUNT(posts.id) as post_count'))
    ->groupBy('users.id')
    ->get();
```

### Many-to-Many Relationship
```php
$postsWithTags = DB::table('posts')
    ->join('post_tag', 'posts.id', '=', 'post_tag.post_id')
    ->join('tags', 'tags.id', '=', 'post_tag.tag_id')
    ->select('posts.*', DB::raw('GROUP_CONCAT(tags.name) as tag_names'))
    ->groupBy('posts.id')
    ->get();
```

## Query Modifiers

### Ordering
```php
$users = DB::table('users')
    ->orderBy('name', 'asc')
    ->orderBy('email', 'desc')
    ->get();
```

### Grouping
```php
$sales = DB::table('orders')
    ->select('product_id', DB::raw('SUM(amount) as total_sales'))
    ->groupBy('product_id')
    ->get();
```

### Limiting and Pagination
```php
// Simple limit
$users = DB::table('users')->limit(10)->get();

// Offset
$users = DB::table('users')->offset(5)->limit(10)->get();

// Pagination
$users = DB::table('users')->paginate(15);
```

## Aggregates and Calculations

### Basic Aggregates
```php
$count = DB::table('users')->count();
$max = DB::table('orders')->max('amount');
$min = DB::table('orders')->min('amount');
$avg = DB::table('orders')->avg('amount');
$sum = DB::table('orders')->sum('amount');
```

### Conditional Aggregates
```php
$activeUsers = DB::table('users')
    ->select(DB::raw('count(*) as user_count, status'))
    ->where('active', 1)
    ->groupBy('status')
    ->get();
```

### Raw Expressions
```php
$users = DB::table('users')
    ->select(DB::raw('count(*) as user_count, status'))
    ->where(DB::raw('YEAR(created_at)'), '2023')
    ->groupBy('status')
    ->get();
```

## Insert, Update, Delete Operations

### Inserting Records
```php
DB::table('users')->insert([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'created_at' => now(),
    'updated_at' => now(),
]);

// Insert multiple
DB::table('users')->insert([
    ['name' => 'John', 'email' => 'john@example.com'],
    ['name' => 'Jane', 'email' => 'jane@example.com'],
]);

// Insert with ID
$id = DB::table('users')->insertGetId([
    'name' => 'John',
    'email' => 'john@example.com'
]);
```

### Updating Records
```php
DB::table('users')
    ->where('id', 1)
    ->update(['name' => 'John Smith']);

// Increment/decrement
DB::table('users')->increment('login_count');
DB::table('users')->decrement('credits', 5);

// Update or insert
DB::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com'],
        ['name' => 'John', 'votes' => 2]
    );
```

### Deleting Records
```php
DB::table('users')->where('id', 1)->delete();

// Truncate table
DB::table('users')->truncate();
```

## Testing with Query Builder

### Basic Test Examples
```php
public function test_user_count()
{
    DB::table('users')->insert([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
    
    $count = DB::table('users')->count();
    $this->assertEquals(1, $count);
}

public function test_complex_query()
{
    // Setup test data
    $userId = DB::table('users')->insertGetId([...]);
    DB::table('orders')->insert([...]);
    
    // Run query
    $results = DB::table('users')
        ->join('orders', 'users.id', '=', 'orders.user_id')
        ->where('users.id', $userId)
        ->get();
        
    // Assertions
    $this->assertCount(1, $results);
    $this->assertEquals(...);
}
```

## Query Builder Best Practices

1. **Use parameter binding** - Prevents SQL injection
2. **Select only needed columns** - Improves performance
3. **Use proper indexing** - For frequently queried columns
4. **Chunk large results** - For memory efficiency
5. **Use transactions** - For multiple related operations

```php
// Good practice example
DB::transaction(function () {
    $userId = DB::table('users')->insertGetId([
        'name' => 'John',
        'email' => 'john@example.com',
    ]);
    
    DB::table('orders')->insert([
        'user_id' => $userId,
        'amount' => 100,
    ]);
});

// Chunking example
DB::table('users')->orderBy('id')->chunk(100, function ($users) {
    foreach ($users as $user) {
        // Process user
    }
});
```

## Common Pitfalls

1. **N+1 query problem** - Avoid multiple queries in loops
2. **Over-fetching data** - Select only needed columns
3. **Not using indexes** - Can lead to slow queries
4. **Raw SQL injection** - Always sanitize raw inputs

```php
// Problematic example
$search = $_GET['search']; // Unsafe!
$users = DB::select("SELECT * FROM users WHERE name = '$search'");

// Better approach
$users = DB::table('users')
    ->where('name', $request->input('search'))
    ->get();
```

## Performance Optimization

### Indexing
```php
// Ensure your tables have proper indexes
Schema::table('users', function (Blueprint $table) {
    $table->index('email');
    $table->index(['last_name', 'first_name']);
});
```

### Caching Queries
```php
$users = Cache::remember('active_users', 3600, function () {
    return DB::table('users')->where('active', 1)->get();
});
```

### Eager Loading (when combining with Eloquent)
```php
// Instead of multiple queries in a loop
$users = User::all();
foreach ($users as $user) {
    $posts = $user->posts; // Query executed here
}

// Use eager loading
$users = User::with('posts')->get();
```

## Special Cases

### UUID Primary Keys
```php
$user = DB::table('users')
    ->where('id', '=', '550e8400-e29b-41d4-a716-446655440000')
    ->first();
```

### JSON Data Queries
```php
$users = DB::table('users')
    ->where('meta->is_admin', true)
    ->get();

$users = DB::table('users')
    ->whereJsonContains('options->languages', 'en')
    ->get();
```

### Full-Text Search
```php
$posts = DB::table('posts')
    ->whereFullText('content', 'database')
    ->get();
```

## Raw Expressions

### Select Raw
```php
$users = DB::table('users')
    ->select(DB::raw('count(*) as user_count, status'))
    ->groupBy('status')
    ->get();
```

### Where Raw
```php
$orders = DB::table('orders')
    ->whereRaw('price > IF(state = "TX", ?, 100)', [200])
    ->get();
```

### Having Raw
```php
$sales = DB::table('orders')
    ->select('department', DB::raw('SUM(price) as total_sales'))
    ->groupBy('department')
    ->havingRaw('SUM(price) > 2500')
    ->get();
```

## Query Builder Macros

### Creating Macros
```php
// In a service provider
DB::macro('toUpper', function (string $value) {
    return DB::raw("UPPER('$value')");
});

// Usage
$users = DB::table('users')
    ->select('name', DB::toUpper('name'))
    ->get();
```

## Query Builder vs Eloquent

### When to Use Query Builder
1. **Performance-critical operations** - Less overhead than Eloquent
2. **Complex joins/subqueries** - Often simpler with Query Builder
3. **Reporting queries** - When you don't need models
4. **Database operations** - That don't map to models

### When to Use Eloquent
1. **Working with model entities** - When you need model features
2. **Simple relationships** - Easier to work with
3. **Model events/observers** - Need to trigger model events
4. **Accessors/mutators** - Need to transform attributes

## Query Builder for Database Administration

### Table Operations
```php
// Check if table exists
if (DB::getSchemaBuilder()->hasTable('users')) {
    // Table exists
}

// Get column listing
$columns = DB::getSchemaBuilder()->getColumnListing('users');

// Drop table
DB::getSchemaBuilder()->drop('users');
```

### Index Operations
```php
// Check for index
if (DB::getSchemaBuilder()->hasIndex('users', 'users_email_index')) {
    // Index exists
}

// Drop index
DB::getSchemaBuilder()->dropIndex('users_email_index');
```

## Query Logging

### Enable Query Log
```php
DB::enableQueryLog();

// Run some queries
$users = DB::table('users')->get();

// Get query log
$queries = DB::getQueryLog();
```

### Debugging Queries
```php
// Get SQL with bindings
$query = DB::table('users')->where('name', 'John');
$sql = $query->toSql();
$bindings = $query->getBindings();

// Debug output
dd($sql, $bindings);
```

Remember: The Query Builder is a powerful tool that provides a database-agnostic way to work with your data. It offers a good balance between raw SQL and Eloquent's abstraction, giving you control while maintaining security and portability across database systems.