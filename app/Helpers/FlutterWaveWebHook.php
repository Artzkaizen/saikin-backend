<?php

namespace App\Helpers;

/**
 * Handle flutter wave webhook response
 */
class FlutterWaveWebHook
{
    /**
     * @var array $event_types
     */
    protected array $event_types = [
        'charge.completed',
        'transfer.completed',
        'subscription.cancelled',
    ];

    /**
     * @var array $header
     */
    protected array $header;

    /**
     * @var array $body
     */
    protected array $body;

    /**
     * @var string $content
     */
    protected string $content;

    /**
     * @var string $ip
     */
    protected string $ip;

    /**
     * @var string $secret_key
     */
    protected string $secret_key;

    /**
     * @var string $public_key
     */
    protected string $public_key;

    /**
     * @var string $verification_hash
     */
    protected string $verification_hash;

    /**
     * Create a new flutter wave webhook instance.
     * @param array $header
     * @param array $body
     * @param string $content
     * @param string $ip
     * @return FlutterWaveWebHook
     */
    public function __construct(array $header, array $body, string $content, string $ip) {
        $this->header = $header;
        $this->body = $body;
        $this->content = $content;
        $this->ip = $ip;
        return $this;
    }

    /**
     * Set a secret key for pay stack webhook
     * @param string $secret_key
     * @return PayStackWebHook
     */
    public function setSecretKey(string $secret_key): FlutterWaveWebHook
    {
        $this->secret_key = $secret_key;
        return $this;
    }

    /**
     * Set a public key for pay stack webhook
     * @param string $public_key
     * @return PayStackWebHook
     */
    public function setPublicKey(string $public_key): FlutterWaveWebHook
    {
        $this->public_key = $public_key;
        return $this;
    }

    /**
     * Set a verification hash for flutter wave webhook
     * @param string $verification_hash
     * @return FlutterWaveWebHook
     */
    public function setVerificationHash(string $verification_hash): FlutterWaveWebHook
    {
        $this->verification_hash = $verification_hash;
        return $this;
    }

    /**
     * Check if the webhook is authorized
     * @param void
     * @return bool
     */
    public function isAuthorized(): bool
    {
        if (!isset($this->header['verif-hash'][0])) {
            return false;
        }

        if ($this->header['verif-hash'][0] ===  $this->verification_hash) {
            return true;
        }

        return false;
    }

    /**
     * Check if the webhook has the given event type
     * @param string type
     * @return bool
     */
    public function isEventType(string $type): bool
    {
        return in_array($type, $this->event_types) && $this->body['event'] === $type;
    }

    /**
     * Check if the webhook has any of the given event types
     * @param array types
     * @return bool
     */
    public function isEventTypeAnyOf(array $types): bool
    {
        return array_intersect($this->event_types, $types) && in_array($this->body['event'], $types);
    }
}
