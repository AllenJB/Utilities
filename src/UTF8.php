<?php

namespace AllenJB\Utilities;

class UTF8
{

    public static function trim($value)
    {
        if ($value === null) {
            return null;
        }
        return preg_replace('/(^\s+)|(\s+$)/us', '', $value);
    }

}
