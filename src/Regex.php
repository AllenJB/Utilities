<?php

namespace AllenJB\Utilities;

/**
 * Provide PCRE regular expressions handling that throws exceptions
 */
class Regex
{

    public static function match($search, $subject, &$matches = [])
    {
        $prevErrLevel = error_reporting(E_ALL);
        $lastPhpError = error_get_last();
        $test = @preg_match($search, $subject, $matches);
        $thisPhpError = error_get_last();
        error_reporting($prevErrLevel);

        if (($thisPhpError !== null) && (serialize($lastPhpError) !== serialize($thisPhpError))) {
            throw new \InvalidArgumentException($thisPhpError['message']);
        }

        $lastError = static::lastErrorMsg();
        if ($lastError !== null) {
            throw new \InvalidArgumentException($lastError);
        }
        if ($test === false) {
            throw new \InvalidArgumentException("Regular expression failed for an unknown reason");
        }

        return $test;
    }


    public static function replace($search, $replace, $subject)
    {
        $prevErrLevel = error_reporting(E_ALL);
        $lastPhpError = error_get_last();
        $test = @preg_replace($search, $replace, $subject);
        $thisPhpError = error_get_last();
        error_reporting($prevErrLevel);

        if (($thisPhpError !== null) && (serialize($lastPhpError) !== serialize($thisPhpError))) {
            throw new \InvalidArgumentException($thisPhpError['message']);
        }

        $lastError = static::lastErrorMsg();
        if ($lastError !== null) {
            throw new \InvalidArgumentException($lastError);
        }
        if ($test === false) {
            throw new \InvalidArgumentException("Regular expression failed for an unknown reason");
        }

        return $test;
    }


    protected static function lastErrorMsg($errorCode = null)
    {
        if ($errorCode === null) {
            $errorCode = preg_last_error();
        }

        switch ($errorCode) {
            case PREG_NO_ERROR:
                return null;
            case PREG_INTERNAL_ERROR:
                return "Internal PCRE error";
            case PREG_BACKTRACK_LIMIT_ERROR:
                return "Backtrack limit exhausted";
            case PREG_RECURSION_LIMIT_ERROR:
                return "Recursion limit exhausted";
            case PREG_BAD_UTF8_ERROR:
                return "Malformed UTF-8 data";
            case PREG_BAD_UTF8_OFFSET_ERROR:
                return "Offset value did not correspond to a valid UTF-8 code point";
            default:
                return "Unhandled Error Code: " . $errorCode;
        }
    }
}
