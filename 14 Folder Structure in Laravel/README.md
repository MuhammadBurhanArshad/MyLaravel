# Laravel Folder Structure

## Definition
Laravel follows the Model-View-Controller (MVC) architectural pattern and organizes files into logical directories. Understanding the folder structure is essential for effective Laravel development.

## Root Directory Structure

### Key Directories
```
app/                  # Core application code
bootstrap/            # Framework bootstrapping files
config/               # Configuration files
database/             # Database migrations, seeds, factories
public/               # Publicly accessible files
resources/            # Front-end assets and views
routes/               # Application route definitions
storage/              # Storage for logs, compiled views, caches
tests/                # Automated tests
vendor/               # Composer dependencies
```

## Detailed Breakdown

### app/ Directory
```
app/
├── Console/          # Artisan commands
│   ├── Commands/
│   └── Kernel.php
├── Exceptions/       # Exception handlers
├── Http/             # HTTP-related classes
│   ├── Controllers/  # Application controllers
│   ├── Middleware/   # HTTP middleware
│   ├── Requests/     # Form request classes
│   └── Kernel.php
├── Models/           # Eloquent models
├── Providers/        # Service providers
└── Services/         # Business logic services (optional)
```

### config/ Directory
```
config/
├── app.php           # Application configuration
├── auth.php          # Authentication configuration
├── cache.php         # Cache configuration
├── database.php      # Database configuration
├── filesystems.php   # Filesystem configuration
├── mail.php          # Mail configuration
├── queue.php         # Queue configuration
├── services.php      # Third-party services
└── session.php       # Session configuration
```

### database/ Directory
```
database/
├── factories/        # Model factories
├── migrations/       # Database migrations
├── seeders/          # Database seeders
└── .gitignore
```

### public/ Directory
```
public/
├── css/              # Compiled CSS
├── js/               # Compiled JavaScript
├── storage/          # Symbolic link to storage/app/public
├── favicon.ico
└── index.php         # Application entry point
```

### resources/ Directory
```
resources/
├── css/              # Raw CSS/Sass files
├── js/               # Raw JavaScript files
├── lang/             # Language files
│   └── en/           # English translations
├── views/            # Blade templates
│   ├── layouts/      # Base layouts
│   ├── components/   # Blade components
│   └── *.blade.php   # View files
└── .gitignore
```

### routes/ Directory
```
routes/
├── api.php           # API routes
├── channels.php      # Broadcast channels
├── console.php       # Artisan command routes
└── web.php           # Web routes
```

### storage/ Directory
```
storage/
├── app/              # Application files
│   ├── public/       # Publicly accessible storage
│   └── framework/    # Framework-generated files
├── framework/        # Framework cache and sessions
│   ├── cache/        # Framework cache
│   ├── sessions/     # Session files
│   └── views/        # Compiled views
└── logs/             # Application logs
```

### tests/ Directory
```
tests/
├── Feature/          # Feature tests
├── Unit/             # Unit tests
├── CreatesApplication.php
└── TestCase.php
```

## Best Practices for Organization

1. **Keep controllers thin** - Move business logic to service classes in `app/Services/`
2. **Use subdirectories** for large applications (e.g., `app/Http/Controllers/Admin/`)
3. **Follow PSR-4 autoloading** standards for class organization
4. **Group related models** in subdirectories when needed (e.g., `app/Models/Billing/`)
5. **Separate API and web routes** in different files

## Common Customizations

### Adding Custom Directories
Many developers add these directories:
```
app/
├── DTOs/             # Data Transfer Objects
├── Enums/            # PHP Enums
├── Interfaces/       # Service interfaces
├── Jobs/             # Queueable jobs
├── Listeners/        # Event listeners
├── Policies/         # Authorization policies
├── Repositories/     # Repository classes
└── Traits/           # Reusable traits
```

### Environment-Specific Files
```
.env                  # Environment variables
.env.example          # Example environment file
.env.testing          # Testing environment variables
```

## Special Cases

### Package Development
When developing packages, you might see:
```
packages/
└── vendor/
    └── package-name/
        ├── src/
        ├── tests/
        └── composer.json
```

### Modular Structure
For large applications, some prefer modular structure:
```
app/
└── Modules/
    ├── Blog/
    │   ├── Controllers/
    │   ├── Models/
    │   ├── Requests/
    │   └── routes.php
    └── Shop/
        ├── Controllers/
        ├── Models/
        ├── Services/
        └── routes.php
```

## Performance Considerations

1. **Cache configuration** in production (`php artisan config:cache`)
2. **Route caching** for production (`php artisan route:cache`)
3. **Optimize class loading** (`php artisan optimize`)
4. **Store cache and sessions** in fast drivers (Redis) for production

## Common Pitfalls

1. **Putting assets in wrong places** - Compiled assets should be in `public/`, source files in `resources/`
2. **Version control issues** - Remember to exclude:
   - `.env`
   - `storage/app/public` (but not the directory itself)
   - `vendor/`
3. **Overcrowding controllers** - Leads to maintenance issues
4. **Ignoring the `lang/` directory** - Makes localization harder later

## Advanced Structures

### Domain-Driven Design (DDD)
```
app/
├── Domain/
│   ├── Users/
│   │   ├── Models/
│   │   ├── Repositories/
│   │   └── Services/
│   └── Products/
│       ├── Models/
│       ├── Repositories/
│       └── Services/
└── Http/
    └── Controllers/
        ├── Users/
        └── Products/
```

### Hexagonal Architecture
```
app/
├── Application/      # Use cases
├── Domain/           # Business logic
└── Infrastructure/   # Technical implementation
    ├── Http/
    ├── Database/
    └── Services/
```

Remember: While Laravel provides a sensible default structure, you can adapt it to your project's needs. The key is maintaining consistency within your team. For large projects, consider modular or domain-driven structures early to avoid painful refactoring later. Always keep performance in mind when organizing your files, especially for frequently accessed resources.