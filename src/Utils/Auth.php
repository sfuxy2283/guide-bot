<?php
/**
 * Utility for helping user authentification
 * 
 * PHP version 7.2.7
 */

namespace Linebot\Utils;

/**
 * Auth class has methods used by Line Api to authenticate user
 */
class Auth
{
    /**
     * @param $knownString
     * @param $userString
     * @return bool
     */
    static function hash_equals($knownString, $userString)
    {
        // Compare string Lengths
        if (($length = strlen($knownString) !== strlen($userString))) {

            return false;
        }

        $diff = 0;

        // Calculate differences
        for ($i = 0; $i < $length; $i++) {

            $diff |= ord($knownString[$i]) ^ ord($userString[$i]);

        }

        return $diff === 0;

    }

    /**
     * @param $body
     * @param $channelSecret
     * @return string
     */
    static function sign($body, $channelSecret)
    {
        $hash = hash_hmac('sha256', $body, $channelSecret, true);

        $signature = base64_encode($hash);

        return $signature;
    }
}