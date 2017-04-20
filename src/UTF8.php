<?php
declare(strict_types = 1);

namespace AllenJB\Utilities;

class UTF8
{

    public static function trim(?string $value)
    {
        if ($value === null) {
            return null;
        }
        return preg_replace('/(^\s+)|(\s+$)/us', '', $value);
    }

}
