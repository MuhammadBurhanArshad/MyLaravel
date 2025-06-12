# Laravel Installation Guide

## Prerequisites

### 1. Install XAMPP
- Download from [Apache Friends](https://www.apachefriends.org/)
- Follow installation wizard
- Ensure Apache and MySQL services are running

### 2. Install Composer
- Download from [getcomposer.org](https://getcomposer.org/)
- Run the installer
- Verify installation:
  ```bash
  composer --version
  ```

## Laravel Installation Methods

### Global Installation (Recommended)
```bash
composer global require laravel/installer
laravel new example-app
```
**Advantages:**
- One-time download of Laravel installer
- Faster project creation
- Consistent installation process

### Per-Project Installation
```bash
composer create-project laravel/laravel example-app
```
**Characteristics:**
- Downloads fresh Laravel copy for each project
- Slower installation process
- Allows different Laravel versions per project

## Post-Installation Steps

1. Navigate to project directory:
   ```bash
   cd example-app
   ```

2. Start development server:
   ```bash
   php artisan serve
   ```

3. Access your application at:
   ```
   http://localhost:8000
   ```

## Recommended VS Code Extensions for Laravel Development

| Extension | Author | Purpose |
|-----------|--------|---------|
| PHP IntelliSense | Damjan Cvetko | Advanced PHP code completion |
| PHP Namespace Resolver | Mehedi Hassan | Namespace importing/organizing |
| Laravel Extra Intellisense | amir | Laravel-specific autocompletion |
| laravel-blade | Christian Howe | Blade template support |
| Laravel Blade Snippets | Winnie Lin | Blade template shortcuts |
| Laravel goto view | codingyu | Quick navigation to Blade views |

### Installation Method for Extensions:
1. Open VS Code Extensions panel (Ctrl+Shift+X)
2. Search for each extension by name
3. Click "Install" for each one

## Environment Configuration

After installation, configure your `.env` file:
```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

## Troubleshooting Tips

1. **Composer issues**:
   ```bash
   composer self-update
   composer clear-cache
   ```

2. **Permission problems**:
   ```bash
   sudo chmod -R 775 storage
   sudo chmod -R 775 bootstrap/cache
   ```

3. **Missing dependencies**:
   ```bash
   composer install
   npm install
   ```

4. **Port conflicts**:
   ```bash
   php artisan serve --port=8080
   ```

Remember to always check the [official Laravel documentation](https://laravel.com/docs) for the most up-to-date installation instructions and requirements.