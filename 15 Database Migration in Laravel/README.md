# Database Migrations in Laravel

## Definition
Migrations in Laravel are like version control for your database, allowing you to modify and share the application's database schema. They are typically stored in the `database/migrations` directory and are paired with Laravel's schema builder to easily build and modify database tables.

## Basic Syntax

### Creating a Migration
```bash
php artisan make:migration create_table_name_table
```

### Migration Structure
```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableNameTable extends Migration
{
    public function up()
    {
        Schema::create('table_name', function (Blueprint $table) {
            // Column definitions
        });
    }

    public function down()
    {
        Schema::dropIfExists('table_name');
    }
}
```

## Common Examples

### Basic Table Creation
```php
class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
```

### Adding Columns
```php
class AddVotesToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('votes');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('votes');
        });
    }
}
```

### Creating Pivot Tables
```php
class CreatePostUserPivotTable extends Migration
{
    public function up()
    {
        Schema::create('post_user', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['post_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('post_user');
    }
}
```

## Migration Commands

Laravel provides several Artisan commands for working with migrations:

```bash
# Create a new migration file
php artisan make:migration create_users_table

# Run all pending migrations
php artisan migrate

# Rollback the last migration operation
php artisan migrate:rollback

# Rollback the specific migration's steps
php artisan migrate:rollback --step=2

# Rollback the specific migration's batch
php artisan migrate:rollback --batch=3

# Rollback all migrations
php artisan migrate:reset

# Drop and re-run all migrations
php artisan migrate:fresh

# Rollback and re-run all migrations
php artisan migrate:refresh

# Show migration status
php artisan migrate:status

# Create and run a migration in one command
php artisan make:migration add_votes_to_users_table --table=users
```

## Best Practices

1. **Keep migrations small and focused** - Each migration should do one thing
2. **Always provide a down method** - For easy rollback
3. **Use descriptive names** - Clearly indicate what the migration does
4. **Avoid business logic in migrations** - Use seeders for data
5. **Don't modify published migrations** - Create new ones instead

```php
// Good practice
class AddApiTokenToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('api_token', 80)->unique()->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('api_token');
        });
    }
}

// Avoid
class ModifyUsersTableAgain extends Migration
{
    public function up()
    {
        // Too many unrelated changes
    }
}
```

## Common Use Cases

### Foreign Key Constraints
```php
Schema::table('posts', function (Blueprint $table) {
    $table->foreignId('user_id')
          ->constrained()
          ->cascadeOnDelete();
});
```

### Indexes
```php
Schema::table('users', function (Blueprint $table) {
    $table->string('email')->unique();       // Unique index
    $table->index('created_at');             // Basic index
    $table->fullText('bio');                 // Full-text index
});
```

### Column Modifiers
```php
Schema::table('users', function (Blueprint $table) {
    $table->string('name')->nullable();      // Allows NULL
    $table->string('email')->unique();       // Must be unique
    $table->decimal('price', 8, 2);          // Precision and scale
    $table->timestamp('created_at')->useCurrent(); // Default to current time
});
```

### Soft Deletes
```php
Schema::table('posts', function (Blueprint $table) {
    $table->softDeletes();  // Adds deleted_at column
});
```

## Special Cases

### JSON Columns
```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->json('options');  // JSON column type
});
```

### Enum Columns
```php
Schema::create('users', function (Blueprint $table) {
    $table->enum('role', ['user', 'editor', 'admin'])->default('user');
});
```

### Spatial Columns
```php
Schema::create('places', function (Blueprint $table) {
    $table->point('location');  // Spatial data type
});
```

### Composite Keys
```php
Schema::create('role_user', function (Blueprint $table) {
    $table->foreignId('user_id')->constrained();
    $table->foreignId('role_id')->constrained();
    $table->primary(['user_id', 'role_id']);  // Composite primary key
});
```

## Performance Considerations

1. **Add indexes for frequently queried columns** but don't over-index
2. **Use batch migrations** for production to reduce downtime
3. **Consider database-specific optimizations** for large tables
4. **Use migrations for schema changes only** - not for large data imports

```php
// Optimized for performance
Schema::create('large_table', function (Blueprint $table) {
    $table->id();
    $table->string('name')->index();  // Index for frequent lookups
    $table->text('content');
    $table->timestamps();
    
    // For very large tables, consider:
    // $table->engine = 'InnoDB';
    // $table->charset = 'utf8mb4';
    // $table->collation = 'utf8mb4_unicode_ci';
});
```

## Common Pitfalls

1. **Forgetting the down method**:
   ```php
   // Always include down() for rollback
   public function down()
   {
       Schema::dropIfExists('flights');
   }
   ```

2. **Running raw SQL without testing**:
   ```php
   // Avoid unless necessary
   DB::statement('ALTER TABLE users MODIFY COLUMN name VARCHAR(255)');
   ```

3. **Modifying published migrations**:
   - Instead of modifying, create a new migration

4. **Not considering database differences**:
   ```php
   // Some column types may behave differently across databases
   $table->text('content');  // Different size limits in MySQL vs PostgreSQL
   ```

## Advanced Patterns

### Conditional Migrations
```php
public function up()
{
    if (Schema::hasTable('users')) {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname')->nullable();
        });
    }
}
```

### Anonymous Migrations (Laravel 8+)
```php
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        // Migration code
    }

    public function down()
    {
        // Rollback code
    }
};
```

### Schema Dumping (Laravel 8+)
```bash
# Generate a schema file to reduce migration count
php artisan schema:dump

# Dump the schema and prune existing migrations
php artisan schema:dump --prune
```

### Table Prefixing
```php
// In AppServiceProvider boot()
Schema::defaultStringLength(191);  // For older MySQL/MariaDB
Schema::enableForeignKeyConstraints();
```

### Migration Groups (Laravel 11+)
```bash
# Create a migration that runs before all others
php artisan make:migration first_migration --first

# Create a migration that runs after all others
php artisan make:migration last_migration --last
```

Remember: Migrations are your database's version control system. Keep them simple, reversible, and test them thoroughly. Always consider how your migrations will work across different database systems if you're developing for multiple database backends. For complex database changes, consider breaking them into multiple smaller migrations to make debugging and rollback easier.