## Usage examples

## Basic

```php
use Seworqs\Json\JsonEditor;

// Create new file. (an empty json will be created directly!)
$json = JsonEditor::createNew('/path/for/new/file.json');

// Or use existing file.
$json = JsonEditor::createFromFile('/path/to/existing/file.json');

// You can add a key value.
$json->add('name', 'vendor/some-project');

// You can add more levels using dot notation.
$json->add('settings.visibility.show', true);

// Or the same result with an array.
$array = [
    'visibility' => [
        'show' => true
    ]
];
$json->add('settings', $array);

// You can also set a key/value, overwriting the existing one.
$json->set('name', 'vendor/another-project');

// You can check whether key exists.
$json->has('name');
$json->has('level1.level2');

// You can get a key/value.
$value = $json->get('name');
$value = $json->get('level1.level2');

// And you can delete keys.
$json->delete('name');
$json->delete('level1.level2');

// Save the file to known file path.
$json->save();
```

## Advanced

```php
use Seworqs\Json\JsonEditor;
use Seworqs\Semver\Enum\EnumBumpReleaseType;
use Seworqs\Semver\Enum\EnumBumpPreReleaseType;

// When you are editing and messed up, just reload the (existing) file.
$json->reload();

// Want to save it as another file, use saveAs.
$json->saveAs('/new/file/path.json');

/**
 * When you get the value of a key, you can give a default value.
 * The default value will be used when the key had not been found.
 * 
 * Use it with care. The preferred way is to check if the key exists
 * with the has() function, else you don't know if it was an existing value.
 *  
 */

// Get key, using a default value.
$value = $json->get('unknown.key', '123456');

// Add an object (like a require field in a composer.json)
$json->addObject('require'); // same as $editor->set('require', (object)[]);
$json->add('require.php', '^8.1');

/*
 * You can use chaining on most of the functions. 
 */

// Chaining
$json->addObject('require')
    ->add('require.php', '^8.1')
    ->set('name', 'vendor/nice-project');

/*
 * You can also bump any string field that contains a semantic version.
 * 
 * To see what you can do with bumping, please take a look at seworqs/semver
 * 
 * https://github.com/seworqs/semver
 * https://packagist.org/packages/seworqs/semver
 */

// Release bump (PATCH,MINOR,MAJOR)
$json->bumpVersion('version', EnumBumpReleaseType::PATCH);

// Release bump, starting with a pre-release.
$json->bumpVersion('version', EnumBumpReleaseType::MAJOR, EnumBumpPreReleaseType::ALPHA);

// Pre-release bump (if you are already in a pre-release).
$json->bumpVersion('version', EnumBumpPreReleaseType::BETA);

```

> For more examples, you could take a look at the test files.