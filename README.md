# ⚙️ Settings

A simple, framework-agnostic settings container for PHP applications.

## ⚖️ License

Licensed under the [MIT license](https://opensource.org/licenses/MIT) and is free for private or commercial projects.

## ✨ Introduction

This library provides a lightweight wrapper around a plain PHP array, giving you a clean interface for storing and retrieving application configuration values. It offers a straightforward, dependency-free way to manage configuration without coupling your code to a specific framework, making it easy to drop into any project or architecture.

## 📥 Installation

```bash
composer require andrewdyer/php-settings
```

Requires PHP 8.3 or newer.

## 🚀 Getting Started

Create a `Settings` instance by passing in your configuration array.

```php
declare(strict_types=1);

use Anddye\Settings\Settings;

$settings = new Settings([
    'app_name' => 'My Application',
    'database' => [
        'host' => 'localhost',
        'port' => 5432,
        'credentials' => [
            'username' => 'admin',
            'password' => 'secret',
        ],
    ],
]);
```

## 📚 Usage

For the following examples, we'll use this settings configuration:

```php
$settings = new Settings([
    'app_name' => 'My Application',
    'database' => [
        'host' => 'localhost',
        'port' => 5432,
        'credentials' => [
            'username' => 'admin',
            'password' => 'secret',
        ],
    ],
]);
```

### Retrieve all settings

Calling `all()` returns the entire settings array.

```php
$all = $settings->all();
```

### Retrieve a value by key

Provide a key to `get()` to retrieve its value. If the key is not found, a `MissingSettingException` is thrown.

You can access top-level keys directly or use dot notation for nested values.

```php
// Access top-level keys
$appName = $settings->get('app_name'); // 'My Application'

// Access nested values using dot notation
$host = $settings->get('database.host');                    // 'localhost'
$port = $settings->get('database.port');                    // 5432
$username = $settings->get('database.credentials.username'); // 'admin'

// Retrieve entire nested arrays
$dbConfig = $settings->get('database'); // Returns the entire database array
```

### Check if a setting exists

Use `has()` to check for the presence of a key without throwing. Works with both top-level and nested keys.

```php
// Check top-level keys
if ($settings->has('app_name')) {
    // safe to call get()
}

// Check nested keys
if ($settings->has('database.credentials.password')) {
    $password = $settings->get('database.credentials.password'); // 'secret'
}
```

