<?php
namespace Framework\Services\KeyRing\Consumers;

use Framework\Services\KeyRing\KeyInterface;

interface ConsumerInterface {

    public static function listRequiredUsages(): array;

    public function setKey(KeyInterface $key, $keyValue): void;

}
