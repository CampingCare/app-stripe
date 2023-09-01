<?php

namespace App;

class Helpers
{
    public static function isJson($string) {
        if (is_array($string))
            return false;

        if (is_object($string))
            return false;

        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }


    public static function isUrl($url, $isHttps = true) {
        $arrParsedUrl = parse_url($url);

        if ($isHttps) {

            if (!isset($arrParsedUrl['scheme']) || $arrParsedUrl['scheme'] == "https") {
                return true;
            }
        } else if (isset($arrParsedUrl['host'])) {

            return true;
        }

        return false;
    }

    public static function log($data, $showType = true) {

        if ($showType)
            return error_log(gettype($data) . ' -- ' . json_encode($data));

        return error_log(json_encode($data));
    }
}
