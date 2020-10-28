<?php
namespace Framework\Guard;

interface UserInterface {

    /**
     * @return string L'identifiant de l'utilisateur sous forme de chaine de caractère
     */
    public function getId(): string;

    /**
     * @return string Le nom d'utilisateur
     */
    public function getUsername(): string;
}
