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
    'timezone' => 'UTC',
    'debug'    => true,
]);
```

## 📚 Usage

### Retrieve a value by key

```php
$timezone = $settings->get('timezone'); // 'UTC'
```

### Retrieve all settings

Calling `get()` without a key returns the entire settings array.

```php
$all = $settings->get();
// ['app_name' => 'My Application', 'timezone' => 'UTC', 'debug' => true]
```
