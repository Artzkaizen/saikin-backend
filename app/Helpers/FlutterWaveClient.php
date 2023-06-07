<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Handle flutter wave application programming interface
 */
class FlutterWaveClient
{
    /**
     * @var string $secret_key
     */
    protected string $secret_key;

    /**
     * @var string $public_key
     */
    protected string $public_key;

    /**
     * Set a secret key for flutter wave application programming interface
     * @param string $secret_key
     * @return FlutterWaveClient
     */
    public function setSecretKey(string $secret_key): FlutterWaveClient
    {
        $this->secret_key = $secret_key;
        return $this;
    }

    /**
     * Set a public key for flutter wave application programming interface
     * @param string $public_key
     * @return FlutterWaveClient
     */
    public function setPublicKey(string $public_key): FlutterWaveClient
    {
        $this->public_key = $public_key;
        return $this;
    }

    /**
     * Find transactions made on flutter wave
     * @param string $url
     * @param string $content
     * @return array|false
     */
    public function verifyPayment(string $url, string $content)
    {
        try {

            // Parse content
            $content = json_decode($content,false);

            // Parse url
            $url = Str::replaceFirst(':id',$content->data->id,$url);

            // Query flutter wave
            $response = Http::withToken($this->secret_key)->get($url)->throw();

            // Parse response
            $response = $response->object();

            // Compare key-value against key-value
            $is_authentic = collect(['id','tx_ref','flw_ref','amount','currency','status'])->every(function ($accessor) use ($content, $response) {
                return $content->data->$accessor === $response->data->$accessor;
            });

            return $is_authentic ? $response : false;

        } catch (\Throwable $th) {

            return false;
        }
    }
}