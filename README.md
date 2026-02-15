# EmCore Package

Auto-generated Laravel package.

## Installation

1. Install the package via Composer:
   ```bash
   composer require mestiauqe/em_core
   ```

2. Publish the configuration files:
   ```bash
   php artisan vendor:publish --provider="ME\EmCore\EmCoreServiceProvider"
   ```

   This will publish the config files to `config/em_core/`:
   - `permissions.php` - Permission settings
   - `sidebar.php` - Sidebar configuration
   - `telegram.php` - Telegram bot settings

## Available Publish Tags

- `em_core-config` - Publish only config files
   ```bash
   php artisan vendor:publish --tag=em_core-config
   ```
