<?php
namespace Framework\Services\Democracy;


use Framework\Guard\UserInterface;
interface VoterInterface {

    /**
     * @param string $permission La permission demandée
     * @param null $target L'objet sur lequel la permission est demandé, si null alors c'est une demande globale
     * @return bool Si le Voter va donner son avis sur cette demande
     */
    public function willVote(string $permission, $target = null): bool;

    /**
     * @param UserInterface|null $user L'utilisateur faisant la demande, si null utilisateur anonyme (non connecté)
     * @param string $permission La permission demandée
     * @param null $target L'objet sur lequel la permission est demandé, si null alors c'est une demande globale
     * @return bool La réponse du Voter
     */
    public function vote(?UserInterface $user, string $permission, $target = null): bool;

}
