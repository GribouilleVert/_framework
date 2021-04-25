<?php
namespace Framework\Services\KeyRing;

use Framework\Services\KeyRing;
use Framework\Services\KeyRing\Consumers\ConsumerInterface;
use Exception;
use stdClass;
use function Framework\every_item_in_array;

class SymmetricKey implements KeyInterface {

    private string $description;
    private string $key;
    private array $usages;
    private string $name;

    public function __construct(stdClass $datas, KeyRing $keyRing)
    {
        $this->description = $datas->description;
        $this->key = $this->parseKey($datas->value);

        $this->usages = [];
        foreach ($datas->usages as $usage) {
            if (in_array($usage, [self::USAGES_JWT])) {
                $this->usages[] = $usage;
            }
        }

        $this->name = $keyRing->getNamespace() . '/' . $datas->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAllowedUsages(): array
    {
        return $this->usages;
    }

    public function getKeyType(): string
    {
        return self::TYPE_SYMMETRIC;
    }

    public function getName(): string
    {
        return $this->name;
    }

    private function parseKey(stdClass $value): string
    {
        if (isset($value->string)) {
            return $value->string;
        } elseif (isset($value->base64)) {
            if (!$result = base64_decode($value->base64)) {
                throw new Exception('Unable to decode base64 key');
            }
            return $result;
        }

        throw new Exception('Unable to parse key value.');
    }

    public function makeConsumer(string $className): ConsumerInterface
    {
        $instance = new $className();
        if (!$instance instanceof ConsumerInterface) {
            throw new Exception('Class must implement ConsumerInterface');
        }

        $requiredUsages = call_user_func([$className, 'listRequiredUsages']);
        if (!every_item_in_array($requiredUsages, $this->usages)) {
            throw new Exception('This key cannot be used by this consumer.');
        }

        $instance->setKey($this, $this->key);
        return $instance;
    }
}
