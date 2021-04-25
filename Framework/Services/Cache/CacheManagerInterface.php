<?php
namespace Framework\Services\Cache;

interface CacheManagerInterface extends \ArrayAccess {

    public function has(string $key): bool;

    public function get(string $key, $default = null);

    public function set(string $key, $value, ?int $expiration = null): void;

    public function delete(string $key): void;

}
