<?php
namespace Framework\Services\KeyRing;

use Framework\Services\KeyRing\Consumers\ConsumerInterface;

interface KeyInterface {

    public const USAGES_JWT = 'jwt';

    public const TYPE_SYMMETRIC = 'symmetric';
    public const TYPE_KEYPAIR = 'keypair';
    public const TYPE_PUBLIC = 'public';
    public const TYPE_PRIVATE = 'private';

    public function getDescription(): string;

    public function getAllowedUsages(): array;

    public function getKeyType(): string;

    public function getName(): string;

    public function makeConsumer(string $className): ConsumerInterface;

}
