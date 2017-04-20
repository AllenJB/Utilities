<?php

namespace SubTech\Utility;

class Json {

    public static function encode($value, $options = 0) {
        $retval = json_encode($value, $options);

        if ($retval === FALSE) {
            $msg = "Failed to encode as JSON";
            if (function_exists('json_last_error_msg')) {
                $msg .= ': '. json_last_error_msg();
            }

            throw new \Exception($msg, json_last_error());
        }

        return $retval;
    }


    public static function decode($value, $assoc = FALSE) {
        $retval = json_decode($value, $assoc);

        if (json_last_error() != JSON_ERROR_NONE) {
            $msg = "Failed to decode JSON";
            if (function_exists('json_last_error_msg')) {
                $msg .= ': '. json_last_error_msg();
            } else {
                $msg .= ': Error code '. json_last_error();
            }

            throw new \Exception($msg, json_last_error());
        }

        return $retval;
    }

}
