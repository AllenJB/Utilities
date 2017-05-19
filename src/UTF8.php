<?php
declare(strict_types = 1);

namespace AllenJB\Utilities;

class UTF8
{

    public static function trim(?string $value) : string
    {
        if ($value === null) {
            return "";
        }
        return preg_replace('/(^[\s\x{200B}\x{FEFF}]+)|([\s\x{200B}\x{FEFF}]+$)/us', '', $value);
    }

}
