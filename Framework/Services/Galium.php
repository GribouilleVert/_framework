<?php
namespace Framework\Services;

use Framework\Services\Session\SessionInterface;

final class Galium {

    /**
     * @var SessionInterface
     */
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function makeState(?array $data = null): string
    {
        $id = bin2hex(random_bytes(16));

        $state = [
            'id' => $id,
            'data' => $data
        ];
        $states = $this->session->get('state', []);
        $states[] = $state;
        $this->session['states'] = $states;

        return $id;
    }

    public function checkState(string $id, bool $use = false): bool
    {
        $states = $this->session->get('states', []);
        foreach ($states as $state) {
            if ($state['id'] === $id) {
                if ($use) {
                    $this->useState($id);
                }

                return true;
            }
        }

        return false;
    }

    public function getState(string $id, bool $use = false): ?array
    {
        $states = $this->session->get('states', []);
        foreach ($states as $state) {
            if ($state['id'] === $id) {
                if ($use) {
                    $this->useState($id);
                }

                return $state;
            }
        }
        return null;
    }

    public function useState(string $id): void
    {
        $this->session['states'] = array_filter(
            $this->session->get('states', []),
            function (array $state) use ($id) {
                return $state['id'] !== $id;
            }
        );
    }

}
