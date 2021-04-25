<?php
namespace Framework\Services\Democracy;

use Exception;

class Citizen {

    public const MODE_ABSOLUTE = 0x1;
    public const MODE_MAJORITY = 0x2;
    public const MODE_AT_LEAST_ONE = 0x3;

    public function choose(array $urn, int $mode = self::MODE_AT_LEAST_ONE): bool
    {
        if (empty($urn)) {
            throw new Exception('No vote in urn.');
        }

        switch ($mode) {
            case self::MODE_AT_LEAST_ONE:
                foreach ($urn as $vote) {
                    if ((bool)$vote === true) {
                        return true;
                    }
                }
                return false;

            case self::MODE_ABSOLUTE:
                foreach ($urn as $vote) {
                    if ((bool)$vote === false) {
                        return false;
                    }
                }
                return true;

            case self::MODE_MAJORITY:
                [$for, $against] = [0, 0];
                foreach ($urn as $vote) {
                    if ($vote) {
                        $for++;
                    } else {
                        $against++;
                    }
                }

                return $for >= $against;
        }
    }

}
