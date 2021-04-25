<?php
namespace Framework\Services;

use Framework\Guard\UserInterface;
use Framework\Services\Democracy\Citizen;
use Framework\Services\Democracy\VoterInterface;

class Democracy {

    /**
     * @var Citizen
     */
    private Citizen $citizen;

    /**
     * @var VoterInterface[]
     */
    private static array $voters = [];

    public function __construct(Citizen $citizen, array $voters)
    {
        $this->citizen = $citizen;

        foreach ($voters as $voter) {
            if ($voter instanceof VoterInterface) {
                self::$voters[] = $voter;
            }
        }
    }

    public function addVoter(VoterInterface $voter): void
    {
        self::$voters[] = $voter;
    }

    /**
     * @param UserInterface|null $user L'utilisateur faisant la demande, si null utilisateur anonyme (non connecté)
     * @param string $permission La permission demandée
     * @param null $target L'objet sur lequel la permission est demandé, si null alors c'est une demande globale
     * @param int $countMode Manière donc le résultat final est obtenu
     * @return bool Le choix final
     */
    public function referendum(
        ?UserInterface $user,
        string $permission,
        $target = null,
        $countMode = Citizen::MODE_AT_LEAST_ONE
    ): bool {
        $urn = [];
        foreach (self::$voters as $voter) {
            if ($voter->willVote($permission, $target)) {
                $urn[] = $voter->vote($user, $permission, $target);
            }
        }

        return $this->citizen->choose($urn, $countMode);
    }

}
