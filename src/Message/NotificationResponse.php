<?php

namespace Omnipay\TillPayments\Message;

use Omnipay\Common\Exception\InvalidResponseException;

/**
 * Acknowledge the incoming status notification message from Till.
 */
class NotificationResponse extends AbstractResponse
{
    protected string $responseMessage = 'OK';

    /**
     * This method checks on the success of the signature verification. It does not reflect on whether the
     * transaction was authorised or not.
     */
    public function isSuccessful(): bool
    {
        /** @var NotificationRequest $request */
        $request = $this->request;

        return $request->isSignatureValid();
    }

    /**
     * Acknowledge the receipt of the status notification details.
     * Till expects just a simple string and nothing else.
     * @throws InvalidResponseException
     */
    public function acknowledge(bool $exit = true): void
    {
        if (!$this->isSuccessful()) {
            throw new InvalidResponseException('Cannot acknowledge an invalid notification');
        }

        // Only send the OK message if the signature has been successfully verified.
        echo $this->responseMessage;

        // Exit immediately on responding
        if ($exit) {
            exit;
        }
    }
}