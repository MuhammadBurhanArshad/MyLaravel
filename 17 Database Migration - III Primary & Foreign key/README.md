# Database Migration Primary & Foreign Keys in Laravel

## Definition
Primary and foreign keys are fundamental database concepts that Laravel migrations handle elegantly. Primary keys uniquely identify records in a table, while foreign keys establish relationships between tables by referencing primary keys in other tables.

## Primary Keys

### Basic Syntax
```php
Schema::create('table_name', function (Blueprint $table) {
    $table->id(); // Auto-incrementing big integer primary key (common default)
    // or
    $table->increments('id'); // Auto-incrementing integer primary key
});
```

### Common Examples

#### Auto-incrementing Primary Key
```php
Schema::create('users', function (Blueprint $table) {
    $table->id(); // Creates 'id' column as bigInteger unsigned auto-increment primary key
    $table->string('name');
});
```

#### Custom Primary Key Name
```php
Schema::create('departments', function (Blueprint $table) {
    $table->uuid('dept_id')->primary(); // Custom primary key name and type
    $table->string('name');
});
```

#### Composite Primary Keys
```php
Schema::create('role_user', function (Blueprint $table) {
    $table->unsignedBigInteger('user_id');
    $table->unsignedBigInteger('role_id');
    $table->primary(['user_id', 'role_id']); // Composite primary key
});
```

#### Non-incrementing Primary Key
```php
Schema::create('countries', function (Blueprint $table) {
    $table->string('code', 3)->primary(); // String primary key
    $table->string('name');
});
```

## Foreign Keys

### Basic Syntax
```php
// Basic foreign key
$table->foreign('local_column')->references('id')->on('related_table');

// Shorthand (Laravel 7+)
$table->foreignId('user_id')->constrained();
```

### Common Examples

#### Basic Foreign Key Relationship
```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->foreignId('user_id')->constrained(); // References 'id' on 'users' table
});
```

#### Foreign Key with Custom References
```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_id')->constrained('users'); // References 'users' table
});
```

#### Foreign Key with Actions
```php
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->text('body');
    $table->foreignId('post_id')
          ->constrained()
          ->cascadeOnDelete()  // Delete comments when post is deleted
          ->cascadeOnUpdate(); // Update comment's post_id when post's id changes
});
```

#### Nullable Foreign Key
```php
Schema::create('articles', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->foreignId('author_id')
          ->nullable()
          ->constrained('users')
          ->nullOnDelete(); // Set author_id to NULL when user is deleted
});
```

## Advanced Key Patterns

### UUID Primary Keys
```php
Schema::create('products', function (Blueprint $table) {
    $table->uuid('id')->primary(); // UUID primary key
    $table->string('name');
});

// Foreign key to UUID
Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignUuid('product_id')->constrained(); // References UUID in products
});
```

### Polymorphic Relationships
```php
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->text('body');
    $table->morphs('commentable'); // Adds commentable_id (unsignedBigInteger) and commentable_type (string)
});
```

### Self-Referencing Foreign Key
```php
Schema::create('employees', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->foreignId('manager_id')
          ->nullable()
          ->constrained('employees')
          ->nullOnDelete(); // Employee can be managed by another employee
});
```

### Multiple Foreign Keys to Same Table
```php
Schema::create('matches', function (Blueprint $table) {
    $table->id();
    $table->foreignId('home_team_id')->constrained('teams');
    $table->foreignId('away_team_id')->constrained('teams');
    $table->date('match_date');
});
```

## Key Modification Examples

### Adding Foreign Key to Existing Table
```php
Schema::table('posts', function (Blueprint $table) {
    $table->foreignId('category_id')
          ->after('user_id')
          ->constrained()
          ->restrictOnDelete(); // Prevent delete if posts exist in category
});
```

### Dropping Foreign Key
```php
Schema::table('posts', function (Blueprint $table) {
    $table->dropForeign(['user_id']); // Convention: array with column name
});
```

### Changing Primary Key
```php
Schema::table('users', function (Blueprint $table) {
    $table->dropPrimary(); // Drop existing primary key
    $table->uuid('uuid')->primary(); // Set new primary key
});
```

## Best Practices

1. **Use consistent naming** for primary and foreign keys
2. **Prefer bigInteger for foreign keys** to match Laravel's default id()
3. **Always define foreign key constraints** at database level
4. **Consider index performance** - foreign keys are automatically indexed
5. **Choose appropriate actions** for onDelete/onUpdate

```php
// Good practice example
Schema::create('orders', function (Blueprint $table) {
    $table->id(); // Auto-incrementing primary key
    $table->foreignId('customer_id')
          ->constrained('users')
          ->restrictOnDelete(); // Prevent user deletion if orders exist
    $table->foreignId('product_id')
          ->constrained()
          ->cascadeOnDelete(); // Delete order if product is deleted
    $table->timestamps();
});
```

## Common Pitfalls

1. **Mismatched data types** between foreign key and referenced primary key
2. **Circular dependencies** between tables
3. **Forgetting to drop constraints** before dropping columns
4. **Overusing cascade deletes** when restrict might be more appropriate
5. **Database-specific behavior** with different database systems

```php
// Problematic example - mismatched types
Schema::create('products', function (Blueprint $table) {
    $table->uuid('id')->primary();
});

Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('product_id'); // Mismatch with uuid
    $table->foreign('product_id')->references('id')->on('products'); // Will fail
});

// Solution
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignUuid('product_id')->constrained(); // Correct type
});
```

## Performance Considerations

1. **Indexes are automatically created** for foreign keys
2. **Consider composite indexes** for frequently queried columns
3. **Batch insert data** before adding foreign key constraints
4. **Be mindful of cascade operations** with large datasets

```php
// Optimized approach for large tables
Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('order_id'); // Add without constraint first
    $table->unsignedBigInteger('product_id');
    // ... other columns
});

// Insert data in batches
// ...

// Then add foreign keys
Schema::table('order_items', function (Blueprint $table) {
    $table->foreign('order_id')->references('id')->on('orders');
    $table->foreign('product_id')->references('id')->on('products');
});
```

## Special Cases

### Multiple Column Foreign Key
```php
Schema::create('employee_department', function (Blueprint $table) {
    $table->unsignedBigInteger('employee_id');
    $table->unsignedBigInteger('department_id');
    $table->date('start_date');
    
    $table->foreign(['employee_id', 'department_id'])
          ->references(['id', 'department_id'])
          ->on('employees')
          ->onDelete('cascade');
});
```

### Partial Foreign Keys (Database Specific)
```php
// PostgreSQL example
Schema::table('orders', function (Blueprint $table) {
    $table->foreign('status_id')
          ->references('id')
          ->on('order_statuses')
          ->where('status_id', '<>', 3); // Only enforce for status_id != 3
});
```

### Deferrable Constraints (PostgreSQL)
```php
Schema::table('orders', function (Blueprint $table) {
    $table->foreign('user_id')
          ->references('id')
          ->on('users')
          ->deferrable(); // Constraint checked at end of transaction
});
```

Remember: Proper use of primary and foreign keys is crucial for maintaining data integrity in your application. Laravel's migration system provides powerful tools to define these relationships while keeping your database schema consistent and well-structured. Always consider the implications of your key constraints on application behavior and performance.