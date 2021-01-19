<?php
namespace Framework\Services;

use Framework\Services\Session\SessionInterface;

class Neon {

    public const TYPE_MESSAGE = 'message';
    public const TYPE_INFO = 'info';
    public const TYPE_IMPORTANT = 'important';
    public const TYPE_SUCCESS = 'success';
    public const TYPE_WARNING = 'warning';
    public const TYPE_ERROR = 'error';

    private const SESSION_KEY = 'services.neon';

    /**
     * @var SessionInterface
     */
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function info(string $message, bool $rawHtml = false): void
    {
        $this->set(self::TYPE_INFO, $message, $rawHtml);
    }

    public function message(string $message, bool $rawHtml = false): void
    {
        $this->set(self::TYPE_MESSAGE, $message, $rawHtml);
    }

    public function important(string $message, bool $rawHtml = false): void
    {
        $this->set(self::TYPE_IMPORTANT, $message, $rawHtml);
    }

    public function success(string $message, bool $rawHtml = false): void
    {
        $this->set(self::TYPE_SUCCESS, $message, $rawHtml);
    }

    public function warning(string $message, bool $rawHtml = false): void
    {
        $this->set(self::TYPE_WARNING, $message, $rawHtml);
    }

    public function error(string $message, bool $rawHtml = false): void
    {
        $this->set(self::TYPE_ERROR, $message, $rawHtml);
    }

    private function set(string $type, string $message, bool $rawHtml): void
    {
        $flash = $this->session->get(self::SESSION_KEY, []);
        $flash[] = [
            'type' => $type,
            'message' => $message,
            'raw' => $rawHtml
        ];
        $this->session->set(self::SESSION_KEY, $flash);
    }

    public function get(bool $keep = false): ?array
    {
        $flashs = $this->session->get(self::SESSION_KEY, []);
        if (!$keep) {
            $this->session->delete(self::SESSION_KEY);
        }

        return $flashs;
    }
}
