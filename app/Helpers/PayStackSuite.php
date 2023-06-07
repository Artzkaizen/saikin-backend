<?php

namespace App\Helpers;

use App\Helpers\PayStackWebHook;
use App\Helpers\PayStackClient;

/**
 * Use pay stack applications 
 */
class PayStackSuite
{
    /**
     * Handle pay stack webhook response
     * @param array $header
     * @param array $body
     * @param string $content
     * @param string $ip
     * @return PayStackWebHook
     */
    public static function webhook(array $header, array $body, string $content, string $ip)
    {
        return new PayStackWebHook($header, $body, $content, $ip);
    }

    /**
     * Handle pay stack application programming interface.
     * @param void
     * @return PayStackClient
     */
    public static function client()
    {
        return new PayStackClient();
    }
}