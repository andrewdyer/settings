# Settings

A lightweight, framework-agnostic settings container for managing configuration in PHP applications.

## Introduction

This library provides a lightweight wrapper around a plain PHP array, giving you a clean interface for storing and retrieving application configuration values. It offers a straightforward, dependency-free way to manage configuration without coupling your code to a specific framework, making it easy to drop into any project or architecture.

## Installation

```bash
composer require andrewdyer/settings
```

Requires PHP 8.3 or newer.

## Getting Started

Create a `AndrewDyer\Settings\Settings` instance by passing in your configuration array.

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

## Usage

### Retrieve all settings

Get the entire settings array using the `all()` method.

```php
$all = $settings->all();
```

### Retrieve a setting

Access a top-level setting using its key with the `get()` method.

```php
$appName = $settings->get('app_name'); // 'My Application'
```

The `get()` method can also access nested settings using dot notation.

```php
$host = $settings->get('database.host'); // 'localhost'
```

Dot notation can traverse multiple levels of configuration.

```php
$username = $settings->get('database.credentials.username'); // 'admin'
```

Requesting a parent key with `get()` returns the entire nested configuration array.

```php
$database = $settings->get('database');
```

### Check if a setting exists

Check if a top-level key exists using the `has()` method.

```php
$settings->has('app_name'); // true
```

The `has()` method also supports nested keys using dot notation.

```php
$settings->has('database.credentials.password'); // true
```

### Literal keys containing dots

If a top-level key contains dots, the exact key takes precedence over nested resolution.

```php
$settings = new Settings([
    'database.host' => 'literal',
]);

$settings->get('database.host'); // 'literal'
```

If the exact key does not exist, the `get()` method falls back to resolving nested values using dot notation.

```php
$settings = new Settings([
    'database' => [
        'host' => 'nested',
    ],
]);

$settings->get('database.host'); // 'nested'
```

## License

Licensed under the [MIT license](https://opensource.org/licenses/MIT) and is free for private or commercial projects.
