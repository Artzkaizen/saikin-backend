<?php

namespace App\Helpers;

/**
 * Handle pay stack webhook response
 */
class PayStackWebHook
{
    /**
     * @var array $whitelisted_ip
     */
    protected array $whitelisted_ip = [
        '52.31.139.75',
        '52.49.173.169',
        '52.214.14.220',
        '127.0.0.1',
    ];

    /**
     * @var array $event_types
     */
    protected array $event_types = [
        'customeridentification.failed',
        'customeridentification.success',
        'charge.dispute.create',
        'charge.dispute.remind',
        'charge.dispute.resolve',
        'dedicatedaccount.assign.failed',
        'dedicatedaccount.assign.success',
        'invoice.create',
        'invoice.payment_failed',
        'invoice.update',
        'paymentrequest.pending',
        'paymentrequest.success',
        'refund.failed',
        'refund.pending',
        'refund.processed',
        'refund.processing',
        'subscription.create',
        'subscription.disable',
        'subscription.not_renew',
        'subscription.expiring_cards',
        'charge.success',
        'transfer.success',
        'transfer.failed',
        'transfer.reversed'
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
     * Create a new pay stack webhook instance.
     * @param array $header
     * @param array $body
     * @param string $content
     * @param string $ip
     * @return PayStackWebHook
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
    public function setSecretKey(string $secret_key): PayStackWebHook
    {
        $this->secret_key = $secret_key;
        return $this;
    }

    /**
     * Set a public key for pay stack webhook
     * @param string $public_key
     * @return PayStackWebHook
     */
    public function setPublicKey(string $public_key): PayStackWebHook
    {
        $this->public_key = $public_key;
        return $this;
    }

    /**
     * Check if the webhook is authorized
     * @param void
     * @return bool
     */
    public function isAuthorized(): bool
    {
        if (!isset($this->header['x-paystack-signature'][0])) {
            return false;
        }

        if (!in_array($this->ip, $this->whitelisted_ip)) {
            return false;
        }

        if ($this->header['x-paystack-signature'][0] === hash_hmac('sha512', $this->content, $this->secret_key)) {
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
