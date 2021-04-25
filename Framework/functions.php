<?php
namespace Framework;

use DateTime;
use Psr\Container\ContainerInterface;

if (!function_exists('Framework\array_resolve')) {
    /**
     * @param string[] $classes
     * @param ContainerInterface $container
     * @return object[]
     */
    function array_resolve(array $classes, ContainerInterface $container): array
    {
        //Filter non classes out
        $classes = array_filter(
            $classes,
            function ($className) {
                return !is_string($className) or class_exists($className);
            }
        );

        //Instantiate classes
        $instances = array_map(
            function (string $className) use ($container) {
                return $container->get($className);
            },
            $classes
        );

        return $instances;
    }
}

if (!function_exists('Framework\detect_date')) {
    /**
     * Transforme une string ou un int en Datetime
     *
     * @param mixed $source La source de la date, les int (timestamps unix) les strings et les DateTime  sont acceptés.
     * @return DateTime|null Null si la date n'a pu être détectée
     */
    function detect_date($source): ?DateTime
    {
        if (is_int($source)) {
            return (new \DateTime())->setTimestamp($source);
        } elseif (is_string($source)) {
            return new \DateTime($source);
        } elseif ($source instanceof \DateTime) {
            return $source;
        }
        return null;
    }
}

if (
    !function_exists('Framework\time_diff_text')
    AND !function_exists('Framework\ago')
    AND !function_exists('Framework\in')
) {
    function time_diff_text(\DateTime $date): string
    {
        if ($date->getTimestamp() > time()) {
            return in($date);
        } elseif ($date->getTimestamp() < time()) {
            return ago($date);
        } else {
            return "maintenant";
        }
    }

    function ago(\DateTime $date): string
    {
        $time = $date->getTimestamp();
        $diff_time = time() - $time;

        if ($diff_time < 5) {
            return 'à l\'instant';
        } elseif ($diff_time < 30) {
            return 'il y a quelques secondes';
        }

        $units = [
            'an' => 31557600,
            'mois' => 2629800,
            'jour' => 86400,
            'heure' => 3600,
            'minute' => 60,
            'seconde' => 1

        ];

        foreach ($units as $unit => $value) {
            $count = $diff_time / $value;
            if ($count >= 1) {
                $unit_count = round($count);
                return 'il y a ' . $unit_count . ' ' . $unit . (($unit_count > 1 && $unit != 'mois') ? 's' : '');
            }
        }
    }

    function in(\DateTime $date): string
    {
        $time = $date->getTimestamp();
        $diff_time = $time - time();

        if ($diff_time < 5) {
            return 'dans un instant';
        } elseif ($diff_time < 30) {
            return 'dans quelques secondes';
        }

        $units = [
            'an' => 31557600,
            'mois' => 2629800,
            'jour' => 86400,
            'heure' => 3600,
            'minute' => 60,
            'seconde' => 1

        ];

        foreach ($units as $unit => $value) {
            $count = $diff_time / $value;
            if ($count >= 1) {
                $unit_count = round($count);
                return 'dans ' . $unit_count . ' ' . $unit . ($count > 1 ? 's' : '');
            }
        }
    }
}

if (!function_exists('Framework\generate_code')) {
    function generate_code(int $length, string $possibilities = "0123456789abcdefghijklmopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"): string
    {
        $res = '';
        for ($i = 0; $i < $length; $i++) {
            $res .= $possibilities[random_int(0, strlen($possibilities) - 1)];
        }
        return $res;
    }
}

if (!function_exists('Framework\parse_phone_number')) {
    function parse_phone_number(string $number): string
    {
        if (!preg_match('/^[\d]{11,13}$/i', $number)) {
            return $number;
        }

        $text = "";

        $indicative = substr($number, 0, 2);
        $number = substr(implode(' ', str_split('0' . substr($number, 2), 2)), 1);

        return "+$indicative $number";
    }
}

if (!function_exists('Framework\privcay_hide')) {
    /**
     * Permet de replacer partiellement un texte sensible par des étoiles
     * @param $text string Texte a modifier
     * @param $start int début des étoiles
     * @param $end int fin des étoiles
     * @return string Texte modifié
     */
    function privacy_hide(string $text, int $start, int $end): string
    {

        if ($end < 0) {
            $end = (strlen($text) - $start - 1) + $end;
        }

        $modified_text = substr_replace($text, '', $start, $end);

        $placeholder_text = '';
        for ($x = 0; $x < strlen($text) - strlen($modified_text); $x++) {
            if ($text[$x + $start] !== "@") {
                $placeholder_text .= '*';
            } else {
                $placeholder_text .= '@';
            }
        }

        $result_text = substr_replace($modified_text, $placeholder_text, $start, 0);
        return $result_text;
    }
}

if (!function_exists('Framework\transform_html_compatible')) {
    function transform_html_compatible($content): string
    {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', str_replace('€', '&euro;', $content));
    }
}

if (!function_exists('Framework\one_of_array_in_array')) {
    function one_of_array_in_array(array $needles, array $haystack): bool
    {
        foreach ($needles as $needle) {
            if (in_array($needle, $haystack)) {
                return true;
            }
        }
        return false;
    }

}

if (!function_exists('Framework\every_item_in_array')) {

    function every_item_in_array(array $needles, array $haystack): bool
    {
        foreach ($needles as $needle) {
            if (!in_array($needle, $haystack)) {
                return false;
            }
        }
        return true;
    }

}

if (!function_exists('Framework\format_byte_size')) {

    function format_byte_size(int $size, bool $bytes = true): string
    {
        $prefixes = [
            2**50 => 'Pio',
            2**40 => 'Tio',
            2**30 => 'Gio',
            2**20 => 'Mio',
            2**10 => 'Kio',
            2**0 => 'o',
        ];
        krsort($prefixes);

        if (!$bytes) {
            $size = round($size/8, 0);
        }

        $r = $size . 'o';
        foreach ($prefixes as $minSize => $prefix) {
            if ($size >= $minSize) {
                $r =  round($size/$minSize, 2) . $prefix;
                break;
            }
        }
        return $r;
    }

}
