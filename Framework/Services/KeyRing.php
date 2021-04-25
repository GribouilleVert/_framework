<?php
namespace Framework\Services;

use Framework\Services\KeyRing\KeyInterface;
use Framework\Services\KeyRing\SymmetricKey;
use Exception;

class KeyRing {

    private string $namespace;
    private string $description;
    private array $keys;

    public function __construct($file)
    {
        if (is_string($file)) {
            $content = file_get_contents($file);
        } elseif (is_resource($file) AND get_resource_type($file) === 'stream') {
            $position = ftell($file);
            fseek($file, 0);
            $content = stream_get_contents($file);
            fseek($file, $position);
        } else {
            throw new Exception('KeyRing $file must either be a filename (string) or a readable stream.');
        }

        $data = json_decode($content);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('KeyRing is an invalid json.');
        }

        if (!isset($data->namespace)) {
            throw new Exception('Missing namespace key');
        } elseif (!isset($data->keys) OR !is_array($data->keys)) {
            throw new Exception('Missing keys array');
        }

        $this->namespace = $data->namespace;
        $this->description = $data->description??'';

        $this->keys = [];
        foreach ($data->keys as $key) {
            $this->keys[$key->id] = $key;
        }
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getKey(string $id): KeyInterface
    {
        if (!array_key_exists($id, $this->keys)) {
            throw new Exception('Key with id ' . $id . ' not found.');
        }

        $keyInfos = $this->keys[$id];
        switch ($keyInfos->type) {
            case 'symmetric':
                return new SymmetricKey($keyInfos, $this);

            default:
                throw new Exception('Unsupported key type.');
        }
    }

}
