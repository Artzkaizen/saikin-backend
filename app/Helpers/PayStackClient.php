<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Handle pay stack application programming interface
 */
class PayStackClient
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
     * Set a secret key for pay stack application programming interface
     * @param string $secret_key
     * @return PayStackClient
     */
    public function setSecretKey(string $secret_key): PayStackClient
    {
        $this->secret_key = $secret_key;
        return $this;
    }

    /**
     * Set a public key for pay stack application programming interface
     * @param string $public_key
     * @return PayStackClient
     */
    public function setPublicKey(string $public_key): PayStackClient
    {
        $this->public_key = $public_key;
        return $this;
    }

    /**
     * Find transactions made on pay stack
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
            $url = Str::replaceFirst(':reference',$content->data->reference,$url);

            // Query pay stack
            $response = Http::withToken($this->secret_key)->get($url)->throw();

            // Parse response
            $response = $response->object();

            // Compare key-value against key-value
            $is_authentic = collect(['id','status','reference','amount','currency'])->every(function ($accessor) use ($content, $response) {
                return $content->data->$accessor === $response->data->$accessor;
            });

            return $is_authentic ? $response : false;

        } catch (\Throwable $th) {

            return false;
        }
    }
}