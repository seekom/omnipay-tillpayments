<?php

namespace Omnipay\TillPayments\Message;

use DateTime;
use Exception;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Omnipay\Common\Message\NotificationInterface;
use GuzzleHttp\ClientInterface;

/**
 * Capture the incoming status notification from Till.
 */
class NotificationRequest extends AbstractRequest implements NotificationInterface
{
    /**
     * Copy of the POST data sent in.
     */
    protected array $data = [];

    public function __construct(ClientInterface $httpClient, HttpRequest $httpRequest)
    {
        parent::__construct($httpClient, $httpRequest);

        $this->data = $this->getData();
    }

    /**
     * Parse the JSON data from the request body.
     * Throws an exception if the JSON is invalid.
     * @return array The parsed JSON data.
     */
    public function getData(): array
    {
        if ($this->data) {
            return $this->data;
        }

        $content = $this->httpRequest->getContent();
        if (!$content) {
            throw new RuntimeException('Missing JSON data');
        }

        try {
            $this->data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new RuntimeException('Invalid JSON data: ' . $e->getMessage());
        }

        return $this->data;
    }

    /**
     * Send an acknowledgement that we have successfully got the data.
     * Here we also check perform signature verification on the signature sent and raise appropriate exceptions if
     * the signature does not look right.
     * The response is a very simple message for returning an acknowledgement to Till.
     */
    public function sendData($data): NotificationResponse
    {
        return $this->response = new NotificationResponse($this, $data);
    }

    /**
     * Checks whether the received signature is matches the signature generated from the data within the request headers
     */
    public function isSignatureValid(): bool
    {
        $signature = $this->httpRequest->headers->get('X-Signature');
        $date = $this->httpRequest->headers->get('X-Date');
        $contentType = $this->httpRequest->headers->get('content-type');
        $requestUri = $this->httpRequest->getRequestUri();
        $method = $this->httpRequest->getMethod();

        if (!$signature || !$date || !$contentType) {
            return false;
        }

        $jsonBody = $this->httpRequest->getContent();
        $hashedJsonBody = hash('sha512', $jsonBody);

        $parts = [$method, $hashedJsonBody, $contentType, $date, $requestUri];
        $str = implode("\n", $parts);

        $digest = hash_hmac('sha512', $str, $this->getSecretKey(), true);
        $expectedSignature = base64_encode($digest);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Get the value of a parameter from the notification data array.
     * If the parameter does not exist in the data array, return the default value.
     */
    protected function getValue($name, $default = null)
    {
        $data = $this->getData();
        $keys = explode('.', $name);

        foreach ($keys as $key) {
            if (!isset($data[$key])) {
                return $default;
            }

            $data = $data[$key];
        }

        return $data;
    }

    /**
     * Returns the unique identifier for this transaction.
     */
    public function getTransactionReference(): ?string
    {
        return $this->getValue('uuid');
    }

    public function getTransactionId(): ?string
    {
        return $this->getValue('merchantTransactionId');
    }

    /**
     * Get the transaction status
     * Returns 'OK', 'PENDING' or 'ERROR'.
     *
     * @return string The transaction status, or null if it is not available.
     */
    public function getTransactionStatus(): string
    {
        return $this->getValue('result');
    }

    /**
     * Get a card / payment reference for createCard requests. This can be used on next payment by assigning
     * this reference into 'referenceUuid' on the next transaction payload
     */
    public function getCardReference(): string
    {
        return $this->getTransactionReference();
    }

    /**
     * Returns an error message if there was a problem with the transaction
     */
    public function getMessage(): ?string
    {
        return $this->getValue('message');
    }

    public function getCode(): ?string
    {
        return $this->getValue('code');
    }

    /**
     * Returns one of DEBIT, CAPTURE, DEREGISTER, PREAUTHORIZE, REFUND,
     * REGISTER, VOID, CHARGEBACK, CHARGEBACK-REVERSAL, PAYOUT
     */
    public function getTransactionType(): ?string
    {
        return $this->getValue('transactionType');
    }

    public function getPaymentMethod(): ?string
    {
        return $this->getValue('paymentMethod');
    }

    public function getAmount(): ?string
    {
        return $this->getValue('amount');
    }

    public function getCurrency(): ?string
    {
        return $this->getValue('currency');
    }

    public function getCardHolder(): ?string
    {
        return $this->getValue('returnData.cardHolder');
    }

    public function getCardType(): ?string
    {
        return $this->getValue('returnData.type');
    }

    public function getExpiryMonth(): ?string
    {
        return $this->getValue('returnData.expiryMonth');
    }

    public function getExpiryYear(): ?string
    {
        return $this->getValue('returnData.expiryYear');
    }

    public function getExpiryDate(): ?DateTime
    {
        $expiryDate = new DateTime();
        $expiryDate->setDate($this->getExpiryMonth(), $this->getExpiryYear(), 1);
        $expiryDate->modify('last day of this month');

        return $expiryDate;
    }

    public function getFirstSixDigits(): ?string
    {
        return $this->getValue('returnData.firstSixDigits');
    }

    public function getLastFourDigits(): ?string
    {
        return $this->getValue('returnData.lastFourDigits');
    }

    public function getBinDigits(): ?string
    {
        return $this->getValue('returnData.binDigits');
    }

    public function getFirstName(): ?string
    {
        return $this->getValue('customer.firstName');
    }

    public function getLastName(): ?string
    {
        return $this->getValue('customer.lastName');
    }

    public function getEmail(): ?string
    {
        return $this->getValue('customer.email');
    }

    public function getBillingPhone(): ?string
    {
        return $this->getValue('customer.billingPhone');
    }
}