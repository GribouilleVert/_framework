<?php
namespace Framework\Services\KeyRing\Consumers;

use Framework\Services\KeyRing\KeyInterface;
use Exception;
use Firebase\JWT\JWT;

class JwtConsumer implements ConsumerInterface {

    private KeyInterface $key;
    private string $secret;
    private bool $asymmetric;
    private bool $instancied = false;

    public function setKey(KeyInterface $key, $keyValue): void
    {
        $this->key = $key;
        $this->secret = $keyValue;
        $this->asymmetric = $this->key->getKeyType() !== KeyInterface::TYPE_SYMMETRIC;
        $this->instancied = true;
    }

    public static function listRequiredUsages(): array
    {
        return [KeyInterface::USAGES_JWT];
    }

    public function encode($datas): string
    {
        if (!$this->instancied) {
            throw new Exception('Key has not been set yet.');
        }

        if ($this->asymmetric) {
            throw new Exception('Asymmetric key not supported yet.');
        }

        return JWT::encode($datas, $this->secret, 'HS512');
    }

    public function decode(string $token, bool $returnArray = false)
    {
        if (!$this->instancied) {
            throw new Exception('Key has not been set yet.');
        }

        if ($this->asymmetric) {
            throw new Exception('Asymmetric key not supported yet.');
        }

        $result = JWT::decode($token, $this->secret, ['HS512']);
        if ($returnArray) {
            $result = json_decode(json_encode($result), true);;
        }
        return $result;
    }

}
