<?php

namespace Seworqs\Json;

use Adbar\Dot;
use Seworqs\Semver\Enum\EnumBumpPreReleaseType;
use Seworqs\Semver\Enum\EnumBumpReleaseType;
use Seworqs\Semver\Semver;

class JsonEditor {

    private Dot $dot;
    private string $filePath;

    ////////////////////
    /// CONSTRUCTOR
    ////////////////////
    private function __construct(string $filePath)
    {
        $this->dot = new Dot();
        $this->filePath = $filePath;
    }

    public static function createNew(string $filePath, $overwrite = false): static {

        if (file_exists($filePath) && !$overwrite) {
            throw new \RuntimeException("File ($filePath) already exists.");
        }

        $editor = new static($filePath);
        $editor->dot->setArray([]);
        $editor->saveAs($filePath);

        return $editor;
    }

    public static function createFromFile(string $filePath): static {

        $editor = new static($filePath);

        $editor->reload();

        return $editor;
    }

    ////////////////////
    /// PUBLIC METHODS
    ////////////////////

    public function add(string $key, mixed $value): static
    {
//        if ($overwrite) {
//            $this->dot->set($key, $value);
//        } else {
//            $this->dot->add($key, $value);
//        }
        if (!$this->has($key)) {
            $this->dot->set($key, $value);
            return $this;
        }

        $existing = $this->dot->get($key);

        if (!is_array($existing)) {
            throw new \InvalidArgumentException("Cannot add to non-array value at [$key].");
        }

        $isList = array_keys($existing) === range(0, count($existing) - 1);

        if ($isList) {
            // Check type consistency: all existing items are assoc arrays?
            $first = reset($existing);
            $addingAssoc = is_array($value) && array_keys($value) !== range(0, count($value) - 1);
            $existingAssoc = is_array($first) && array_keys($first) !== range(0, count($first) - 1);

            if ($existingAssoc && !$addingAssoc) {
                throw new \InvalidArgumentException("Expected associative array to append to [$key] list.");
            }

            if (!$existingAssoc && $addingAssoc) {
                throw new \InvalidArgumentException("Expected scalar to append to [$key] list.");
            }

            $existing[] = $value;
            $this->dot->set($key, $existing);
        } elseif (is_array($value)) {
            $this->dot->set($key, array_merge($existing, $value));
        } else {
            throw new \InvalidArgumentException("Cannot merge scalar into associative array at [$key].");
        }

        return $this;
    }

    public function addObject(string $key): static
    {
        if (!$this->has($key)) {
            $this->set($key, (object)[]);
        }
        return $this;
    }

    public function set(string $key, mixed $value): static {
        $this->dot->set($key, $value);
        return $this;
    }

    public function delete(string $key): static
    {
        if (preg_match('/^(.+)\\.(\\d+)$/', $key, $matches)) {
            $baseKey = $matches[1];
            $index   = (int) $matches[2];
            $list    = $this->dot->get($baseKey);

            if (is_array($list) && array_is_list($list)) {
                unset($list[$index]);
                $this->dot->set($baseKey, array_values($list));
                return $this;
            }
        }

        $this->dot->delete($key);
        return $this;
    }

    public function get(string $key, $default = null): mixed
    {
        return $this->dot->get($key, $default);
    }

    public function has(string $key): bool
    {
        return $this->dot->has($key);
    }

    public function reload(): static
    {
        if (file_exists($this->filePath)) {
            $json           = file_get_contents($this->filePath);
            $data           = json_decode($json, true);
            $this->dot->setArray($data);
        } else {
            throw new \RuntimeException(sprintf('%s does not exist.', $this->filePath));
        }
        return $this;
    }

    public function saveAs(string $filePath): static {

        // Make sure the directory exists (or create it).
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Save JSON.
        $save = file_put_contents($filePath, $this->toString(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // Set filepath!
        $this->filePath = $filePath;

        return $this;
    }

    public function save(): static
    {
        if (file_exists($this->filePath)) {
            return $this->saveAs($this->filePath);
        } else {
            throw new \RuntimeException(sprintf("File (%s) does not exist.", $this->filePath));
        }
    }

    public function bumpVersion($key, EnumBumpReleaseType|EnumBumpPreReleaseType $releaseType, ?EnumBumpPreReleaseType $preReleaseType = null, ?string $preReleaseDelimiter = null
    ): static {
        $version = $this->get($key, '0.0.0');
        $semver = Semver::fromString($version);
        $bumped = $semver->bumpVersion($releaseType, $preReleaseType);

        if ($preReleaseDelimiter !== null) {
            $bumped = $bumped->withPreReleaseDelimiter($preReleaseDelimiter);
        }

        $this->set($key, $bumped->toString());

        return $this;
    }

    public function toString($flags = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES): string
    {
        $data = count($this->dot->all()) > 0 ? $this->dot->all() : (object)[];
        return json_encode($data, $flags);
    }

    public function toArray(): array
    {
        return $this->dot->all();
    }
}