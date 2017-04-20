<?php
declare(strict_types = 1);

namespace AllenJB\Utilities;

class File
{

    public static function countLines($fh, string $lineEnding = "\n") : int
    {
        $pointerPosition = ftell($fh);
        $lines = 0;
        while (! feof($fh)) {
            $lines += substr_count(fread($fh, 8192), $lineEnding);
        }
        fseek($fh, $pointerPosition);

        return $lines;
    }


    public static function human2bytes(string $val) : int
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }


    public static function bytes2human(?int $val) : ?string
    {
        if ($val === null) {
            return null;
        }

        if ($val < 1024) {
            return number_format($val) . ' bytes';
        }
        if ($val < (1024 * 1024)) {
            return number_format($val / 1024) . ' KB';
        }
        return number_format($val / (1024 * 1024)) . ' MB';
    }


    /**
     * Returns the maximum file upload size (taking both upload_max_filesize and post_max_size into account) in bytes.
     * @return int
     */
    public static function getUploadMaxFilesize() : int
    {
        $file = static::human2bytes(ini_get('upload_max_filesize'));
        $post = static::human2bytes(ini_get('post_max_size'));

        return ($post < $file ? $post : $file);
    }

}
