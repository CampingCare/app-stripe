<?php

namespace App;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

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

    /**
     * Handles request input validation on API routes.
     * Throws HttpResponseException when validation fails.
     */
    public static function validation(Request $request, $rules) {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails())
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'data' => $validator->errors()
            ]));
    }
}
