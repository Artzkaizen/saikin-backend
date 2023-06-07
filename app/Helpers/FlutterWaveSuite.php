<?php

namespace App\Helpers;

use App\Helpers\FlutterWaveWebHook;
use App\Helpers\FlutterWaveClient;

/**
 * Use flutter wave applications 
 */
class FlutterWaveSuite
{
    /**
     * Handle flutter wave webhook response
     * @param array $header
     * @param array $body
     * @param string $content
     * @param string $ip
     * @return FlutterWaveWebHook
     */
    public static function webhook(array $header, array $body, string $content, string $ip)
    {
        return new FlutterWaveWebHook($header, $body, $content, $ip);
    }

    /**
     * Handle flutter wave application programming interface.
     * @param void
     * @return FlutterWaveClient
     */
    public static function client()
    {
        return new FlutterWaveClient();
    }
}