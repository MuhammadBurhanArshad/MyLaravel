# Joins in Laravel Query Builder

## Introduction to Joins in Laravel

Laravel's Query Builder provides a fluent, convenient interface for creating and running database queries, including powerful join operations. Joins allow you to combine rows from two or more tables based on related columns between them.

### Why Use Joins in Laravel?

✔ **Database agnostic** - Works with MySQL, PostgreSQL, SQLite, SQL Server  
✔ **Fluent syntax** - Chainable, readable methods  
✔ **Type safety** - Avoid SQL injection vulnerabilities  
✔ **Performance optimized** - Efficient query generation  

## Basic Join Types

### 1. Inner Join

```php
DB::table('users')
    ->join('posts', 'users.id', '=', 'posts.user_id')
    ->select('users.*', 'posts.title')
    ->get();

    //SELECT users.name, posts.title
    //FROM users
    //INNER JOIN posts ON users.id = posts.user_id
```

### 2. Left Join

```php
DB::table('users')
    ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
    ->get();

    //SELECT *
    //FROM users
    //LEFT JOIN posts ON users.id = posts.user_id
```

### 3. Right Join

```php
DB::table('posts')
    ->rightJoin('users', 'posts.user_id', '=', 'users.id')
    ->get();

    //SELECT *
    //FROM posts
    //RIGHT JOIN users ON posts.user_id = users.id
```

### 4. Cross Join

```php
DB::table('sizes')
    ->crossJoin('colors')
    ->get();

    //SELECT *
    //FROM sizes
    //CROSS JOIN colors
```

## Advanced Join Techniques

### Multiple Join Conditions

```php
DB::table('users')
    ->join('contacts', function($join) {
        $join->on('users.id', '=', 'contacts.user_id')
             ->where('contacts.active', '=', 1);
    })
    ->get();

    //SELECT *
    // FROM users
    //  INNER JOIN contacts ON users.id = contacts.user_id AND contacts.active = 1
```

### Subquery Joins

```php
$latestPosts = DB::table('posts')
    ->select('user_id', DB::raw('MAX(created_at) as last_post_created_at'))
    ->groupBy('user_id');

DB::table('users')
    ->joinSub($latestPosts, 'latest_posts', function($join) {
        $join->on('users.id', '=', 'latest_posts.user_id');
    })
    ->get();
```

### Multiple Table Joins

```php
DB::table('users')
    ->join('posts', 'users.id', '=', 'posts.user_id')
    ->join('comments', 'posts.id', '=', 'comments.post_id')
    ->select('users.name', 'posts.title', 'comments.body')
    ->get();
```

## Practical Examples

### E-Commerce Product Query

```php
DB::table('products')
    ->join('categories', 'products.category_id', '=', 'categories.id')
    ->leftJoin('discounts', function($join) {
        $join->on('products.id', '=', 'discounts.product_id')
             ->where('discounts.expires_at', '>', now());
    })
    ->select('products.*', 'categories.name as category', 'discounts.amount')
    ->where('products.stock', '>', 0)
    ->get();
```

### User Activity Dashboard

```php
DB::table('users')
    ->leftJoin('logins', 'users.id', '=', 'logins.user_id')
    ->leftJoin('orders', 'users.id', '=', 'orders.user_id')
    ->select(
        'users.name',
        DB::raw('COUNT(DISTINCT logins.id) as login_count'),
        DB::raw('SUM(orders.total) as total_spent')
    )
    ->groupBy('users.id')
    ->get();
```

## Performance Considerations

1. **Select only needed columns** - Avoid `select('*')`
2. **Use proper indexing** - Ensure join columns are indexed
3. **Limit results** - Add `->take(100)` when appropriate
4. **Eager loading** - Consider Eloquent relationships for complex scenarios

```php
// Optimized join
DB::table('orders')
    ->join('customers', 'orders.customer_id', '=', 'customers.id')
    ->select('orders.id', 'orders.total', 'customers.name')
    ->where('orders.created_at', '>', now()->subDays(30))
    ->get();
```

## Common Pitfalls

1. **Ambiguous column names**:

   ```php
   // Bad - 'id' could be from either table
   DB::table('users')->join('posts', 'id', '=', 'user_id')->get();
   
   // Good - specify table
   DB::table('users')->join('posts', 'users.id', '=', 'posts.user_id')->get();
   ```

2. **Cartesian products**:

   ```php
   // Accidentally creates all possible combinations
   DB::table('users')->join('posts', true)->get();
   ```

3. **N+1 query problems**:

   ```php
   // Inefficient - queries inside loop
   $users = DB::table('users')->get();
   foreach ($users as $user) {
       $posts = DB::table('posts')->where('user_id', $user->id)->get();
   }
   
   // Better - single join query
   DB::table('users')->join('posts', 'users.id', '=', 'posts.user_id')->get();
   ```

## Comparison with Eloquent Relationships

| Query Builder Joins | Eloquent Relationships |
|---------------------|-----------------------|
| More control over SQL | Higher level abstraction |
| Better for complex reporting | Better for model operations |
| Manual relationship handling | Automatic relationship loading |
| Raw performance focus | Developer convenience |

## Advanced Techniques

### Conditional Joins

```php
$query = DB::table('users');

if ($request->has('with_posts')) {
    $query->leftJoin('posts', 'users.id', '=', 'posts.user_id');
}

$results = $query->get();
```

### Join with Raw Expressions

```php
DB::table('users')
    ->join('contacts', function($join) {
        $join->on('users.id', '=', 'contacts.user_id')
             ->whereRaw('contacts.updated_at > users.last_login');
    })
    ->get();
```

### Join with Query Builder

```php
$activePosts = DB::table('posts')->where('active', 1);

DB::table('users')
    ->joinSub($activePosts, 'active_posts', function($join) {
        $join->on('users.id', '=', 'active_posts.user_id');
    })
    ->get();
```

## Best Practices

1. **Use explicit column names** in select to avoid ambiguity
2. **Add type hints** for join conditions (`=`, `>`, etc.)
3. **Consider readability** - break complex joins into multiple lines
4. **Test query performance** - especially with large datasets
5. **Use query logging** during development:

```php
DB::enableQueryLog();
// Your join queries
dd(DB::getQueryLog());
```

## Real-World Example: Reporting Query

```php
$salesReport = DB::table('orders')
    ->join('customers', 'orders.customer_id', '=', 'customers.id')
    ->join('products', 'orders.product_id', '=', 'products.id')
    ->leftJoin('discounts', function($join) {
        $join->on('orders.id', '=', 'discounts.order_id')
             ->where('discounts.valid_until', '>', now());
    })
    ->select(
        'customers.name as customer',
        'products.name as product',
        'orders.quantity',
        'products.price',
        'discounts.amount as discount',
        DB::raw('(products.price * orders.quantity - IFNULL(discounts.amount, 0)) as total')
    )
    ->whereBetween('orders.created_at', [$startDate, $endDate])
    ->orderBy('total', 'desc')
    ->get();
```

Remember: Laravel's Query Builder joins provide powerful database querying capabilities while maintaining security and readability. For complex applications, consider combining joins with Eloquent relationships for the best balance of power and maintainability.
