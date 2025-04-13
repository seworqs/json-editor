# SEworqs JSON Editor

An easy way to create or edit JSON files.

## Installation

Install via Composer.
```bash
$> composer require seworqs/json-editor
```

## Usage

```php
use Seworqs\Json\JsonEditor;

// Create a new file.
$newJson1 = JsonEditor::createNew("/path/to/save/json/file.json");

// If you want to overwrite a file when it already exists.
$newJson2 = JsonEditor::createNew("/path/to/save/json/file.json", true);

// Create a new file.
$newJson3 = JsonEditor::createFromFile("/path/to/existing/json/file.json");

// Overwrite boolean.
$overwrite = false;

// Add key/value
$newJson3->add('some-key', 'some-value', $overwrite);

// Add key/value in levels deep using dot notation.
$newJson3->add('level1.level2.level3', 'a-deep-value', $overwrite);

// OR Add key/value in levels deep using an array.
$newJson3->add('level1', ['level2' => ['level3' => 'a-deep-level']], $overwrite);

/* Both (using dots or array) will create this:
{
...
    "level1": {
        "level2": {
            "level3": "deep-value"
        }
    },
...
}
*/
```
> [More examples](docs/Examples.md)


## Features
- [X] Create and edit new JSON file
- [X] Edit existing JSON file
- [X] Use easy dot notation to get to your keys
- [X] Add/delete single or multiple keys at once
- [X] Bump version with seworqs/semver integration

> See our [examples](docs/Examples.md)

## Classes and namespaces

| Namespace          | Class      | Description      |
|--------------------|------------|------------------|
| Seworqs\JsonEditor | JsonEditor | Nice JSON editor |


## License

Apache-2.0, see [LICENSE](./LICENSE)

## About SEworqs
Seworqs builds clean, reusable modules for PHP and Mendix developers.

Learn more at [github.com/seworqs](https://github.com/seworqs)

## Badges
[![Latest Version](https://img.shields.io/packagist/v/seworqs/json-editor.svg?style=flat-square)](https://packagist.org/packages/seworqs/json-editor)
[![Total Downloads](https://img.shields.io/packagist/dt/seworqs/json-editor.svg?style=flat-square)](https://packagist.org/packages/seworqs/json-editor)
[![License](https://img.shields.io/packagist/l/seworqs/json-editor?style=flat-square)](https://packagist.org/packages/seworqs/json-editor)
[![PHP Version](https://img.shields.io/packagist/php-v/seworqs/json-editor.svg?style=flat-square)](https://packagist.org/packages/seworqs/json-editor)
[![Made by SEworqs](https://img.shields.io/badge/made%20by-SEworqs-002d74?style=flat-square&logo=https://raw.githubusercontent.com/seworqs/json/main/assets/logo.svg&logoColor=white)](https://github.com/seworqs)

