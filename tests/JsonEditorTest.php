<?php

namespace Seworqs\Json\Test;

use PHPUnit\Framework\TestCase;
use Seworqs\Json\JsonEditor;
use Seworqs\Semver\Enum\EnumBumpPreReleaseType;
use Seworqs\Semver\Enum\EnumBumpReleaseType;

class JsonEditorTest extends TestCase
{
    private string $_pathToTestFile = __DIR__ . '/json/test.json';
    private string $_pathToTemplateFile = __DIR__ . '/json/template.json';

    public function tearDown(): void
    {
        // Clean up test.json.
        //unlink($this->_pathToTestFile);
        parent::tearDown();
    }

    public function testCreateNewJsonFile() {

        // === CREATE NEW FILE ===
        $editor = JsonEditor::createNew($this->_pathToTestFile, true);

        // Add simple key/value fields.
        $editor->add('name', 'seworqs/some-module');
        $this->assertTrue($editor->has('name'));
        $this->assertEquals('seworqs/some-module', $editor->get('name'));

        $editor->add('description', 'Some description');
        $this->assertTrue($editor->has('description'));
        $this->assertEquals('Some description', $editor->get('description'));

        $editor->add('type', 'library');
        $this->assertTrue($editor->has('type'));
        $this->assertEquals('library', $editor->get('type'));

        $editor->add('version', '0.0.1-alpha1');
        $this->assertTrue($editor->has('version'));
        $this->assertEquals('0.0.1-alpha1', $editor->get('version'));

        // === BUMP VERSION ===
        $editor->bumpVersion('version', EnumBumpPreReleaseType::ALPHA);
        $this->assertEquals('0.0.1-alpha2', $editor->get('version'));

        $editor->bumpVersion('version', EnumBumpPreReleaseType::BETA);
        $this->assertEquals('0.0.1-beta1', $editor->get('version'));

        $editor->bumpVersion('version', EnumBumpPreReleaseType::RC);
        $this->assertEquals('0.0.1-rc1', $editor->get('version'));

        $editor->bumpVersion('version', EnumBumpReleaseType::STABLE);
        $this->assertEquals('0.0.1', $editor->get('version'));

        $editor->bumpVersion('version', EnumBumpReleaseType::PATCH);
        $this->assertEquals('0.0.2', $editor->get('version'));

        $editor->bumpVersion('version', EnumBumpReleaseType::MINOR);
        $this->assertEquals('0.1.0', $editor->get('version'));

        $editor->bumpVersion('version', EnumBumpReleaseType::MAJOR);
        $this->assertEquals('1.0.0', $editor->get('version'));

        $editor->bumpVersion('version', EnumBumpReleaseType::MAJOR, EnumBumpPreReleaseType::ALPHA);
        $this->assertEquals('2.0.0-alpha1', $editor->get('version'));

        $editor->bumpVersion('version', EnumBumpReleaseType::MINOR);
        $this->assertEquals('2.1.0', $editor->get('version'));

        // === ADD AND SET ===
        $editor->add('license', 'MIT');
        $this->assertTrue($editor->has('license'));
        $this->assertEquals('MIT', $editor->get('license'));

        $editor->set('license', 'Apache-2.0');
        $this->assertTrue($editor->has('license'));
        $this->assertEquals('Apache-2.0', $editor->get('license'));

        // ADDING ARRAY AND DOT.
        // Set authors with array.
        $editor->add('authors', [['name' => 'author1', 'value' => 'author1@somecompany.com'],['name' => 'author2', 'value' => 'auhtor2@somecompany.com']]);
        $this->assertTrue($editor->has('authors.0'));
        $this->assertTrue($editor->has('authors.1'));

        // Adding with dot notation.
        $editor->add('authors.2.name', 'J. Doe');
        $this->assertTrue($editor->has('authors.2.name'));
        $this->assertEquals('J. Doe', $editor->get('authors.2.name'));

        $editor->add('authors.2.email', 'jd@somecompany.com');
        $this->assertTrue($editor->has('authors.2.email'));
        $this->assertEquals('jd@somecompany.com', $editor->get('authors.2.email'));

        // Add to an array.
        $editor->add('authors', ['name' => 'author3', 'email' => 'author3@somecompany.com']);
        $this->assertTrue($editor->has('authors.3'));
        $this->assertEquals('author3', $editor->get('authors.3.name'));
        $this->assertEquals('author3@somecompany.com', $editor->get('authors.3.email'));

        // Adding with array.
        $editor->add('autoload', ['psr-4' => ['Seworqs\\Some\\Module\\' => 'src/' ]]);
        $this->assertTrue($editor->has('autoload.psr-4'));
        $this->assertEquals('src/', $editor->get('autoload.psr-4.Seworqs\\Some\\Module\\'));

        // Adding with array.
        $editor->add('autoload-dev', ['psr-4' => ['Seworqs\\Some\\Module\\Test\\' => 'tests/' ]]);
        $this->assertTrue($editor->has('autoload-dev.psr-4'));
        $this->assertEquals('tests/', $editor->get('autoload-dev.psr-4.Seworqs\\Some\\Module\\Test\\'));

        // === DELETING ===
        // When deleting an index, the index has changed!! The second delete should have index 0, not 1!
        $editor->delete('authors.0');
        $editor->delete('authors.0');
        $editor->delete('authors.1');

        // === ADD OBJECT ===
        $editor->add('require.php', '^8.1');

        $editor->add('require-dev.phpunit/phpunit', '^11');
        $editor->add('require.seworqs/commons-string', '*');

        $editor->add('scripts.test', 'phpunit');

        // === SAVE ===
        $editor->save();

        // Check file existence and valid JSON.
        $this->assertFileExists($this->_pathToTestFile);
        $this->assertJson(file_get_contents($this->_pathToTestFile));

        // Compare with template file (and testing createFromFile also...)
        $tmplEditor = JsonEditor::createFromFile($this->_pathToTemplateFile);
        $this->assertEquals($tmplEditor->toArray(), $editor->toArray());
    }
}