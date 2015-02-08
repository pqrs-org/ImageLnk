<?php //-*- Mode: php; indent-tabs-mode: nil; -*-

class ImageLnk_Encode
{
    public static function sjis_to_utf8($string) {
        return @iconv("SHIFT_JIS", "UTF-8//IGNORE", $string);
    }
}
