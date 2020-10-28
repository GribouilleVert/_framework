<?php
namespace Framework\Guard;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Framework\Guard\Exceptions\NotLoggedException;
use Framework\Guard\UserInterface;

interface AuthenticationInterface {

    /**
     * Renvoie l'utilisateur actif, si l'utilisateur n'est pas connecté renvoie
     * une NotLoggedException
     *
     * @param ServerRequestInterface|null $request If null use global vars instead
     * @return UserInterface
     * @throws NotLoggedException
     */
    public function getUser(?ServerRequestInterface $request = null): UserInterface;

    /**
     * Indique si l'utilisateur est connecté
     *
     * @param ServerRequestInterface|null $request If null use global vars instead
     * @return bool
     */
    public function isLogged(?ServerRequestInterface $request = null): bool;

    /**
     * Permet de connecter l'utilisateur (session)
     * N'EFFECTUE PAS VERIFICATION
     *
     * @param string $uid Identifiant unique de l'utilisateur
     * @param ResponseInterface $response Réponse à modifier
     * @param array $options Options de connexion
     * @return UserInterface|null Renvoi null si l'utilisateur n'existe pas
     */
    public function login(string $uid, ResponseInterface &$response, array $options = []): ?UserInterface;

    /**
     * Permet de déconnecter la sessions
     *
     * @param ResponseInterface $response Réponse à modifier
     */
    public function logout(ResponseInterface &$response): void;
}
