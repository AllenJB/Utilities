<?php
declare(strict_types = 1);

namespace AllenJB\Utilities;

class Json
{

    public static function encode($value, $options = JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS) : string
    {
        $retval = json_encode($value, $options);

        if ($retval === false) {
            $msg = "Failed to encode as JSON";
            if (function_exists('json_last_error_msg')) {
                $msg .= ': ' . json_last_error_msg();
            }

            throw new \InvalidArgumentException($msg, json_last_error());
        }

        return $retval;
    }


    public static function decode(string $value, bool $assoc = false)
    {
        $retval = json_decode($value, $assoc);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $msg = "Failed to decode JSON";
            if (function_exists('json_last_error_msg')) {
                $msg .= ': ' . json_last_error_msg();
            } else {
                $msg .= ': Error code ' . json_last_error();
            }

            throw new \InvalidArgumentException($msg, json_last_error());
        }

        return $retval;
    }

}
