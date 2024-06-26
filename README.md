# Omnipay: Till Payments

**Till Payments driver for the Omnipay PHP payment processing library**

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements eWAY support for Omnipay.

[Till Payments](https://tillpayments.com/) was founded in 2012 with the primary focus of helping technology vendors and their customers streamline their payments acceptance and operations.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "visualr/omnipay-tillpayments": "~2.0"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

### Initializing Gateway

```php
// Create a gateway
$gateway = Omnipay::create('TillPayments');

// Initialise the gateway
$gateway->initialize(array(
    'apiKey' => 'your-api-key',
    'secretKey' => 'your-secret-key',
    'username' => 'your-username',
    'password' => 'your-password',
    'testMode'  => TRUE, // or FALSE when you are ready for live transactions
    'defaultProxy' => null, // or instance of \Omnipay\TillPayments\Proxy 
    'defaultMerchantTransactionIdPrefix'  => 'omnipay-', // prefix of the merchantTransactionId (optional)
));
```

**Remember!** You will have different **API Key** for different connectors


### Purchase Transaction

This is called **Debit** on Till Documentation

```php
try {
    // You need to define your own $formData array
    $card = new CreditCard($formData);
    
    $request = $gateway->purchase([
        'amount' => '10.00', // this represents $10.00
        'currency' => 'AUD',
        'card' => $card,
    ]);
    
    $response = $transaction->send();

    if ($response->isSuccessful()) {

        // Do stuff here
        // Use $response->getTransactionReference() to get transaction uuid

    } else {

        // Handle errors

    }

} catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {

    // Handle Exception Errors (use $e->getResponse()->getBody() to get JSON data)

} catch (\Exception $e) {

    // Handle Exception Errors (use $e->getMessage() to get data)

}
```

Required Fields | Notes
--- | ---
`merchantTransactionId` | Your unique transaction ID. If left blank, default one will be used |
`amount` | Decimals separated by ., max. 3 decimals
`currency` | 3 letter currency code

You can also pass `transactionToken` on the payload if you receive a token using Till Payment's `payment.js`

For more information about the payload, refer to: [Till Payment Documentation](https://gateway.tillpayments.com/documentation/apiv3#transaction-request-debit)

For more information about the card data, refer to: [Omnipay Credit Card Documentation](https://github.com/thephpleague/omnipay/tree/v2.3.1#credit-card--payment-form-input)


### Authorize Transaction

This is called **Preauthorize** on Till Documentation

A Preauthorize reserves the payment amount on the customer's payment instrument.

Depending on the payment method you have up to 7 days until you must Capture the transaction before the authorization expires.

```php
try {
    // You need to define your own $formData array
    $card = new CreditCard($formData);
    
    $request = $gateway->authorize([
        'amount' => '10.00', // this represents $10.00
        'currency' => 'AUD',
        'card' => $card,
    ]);
    
    $response = $transaction->send();

    if ($response->isSuccessful()) {

        // Do stuff here
        // Use $response->getTransactionReference() to get transaction uuid

    } else {

        // Handle errors

    }

} catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {

    // Handle Exception Errors (use $e->getResponse()->getBody() to get JSON data)

} catch (\Exception $e) {

    // Handle Exception Errors (use $e->getMessage() to get data)

}
```


Required Fields | Notes
--- | ---
`merchantTransactionId` | Your unique transaction ID. If left blank, default one will be used |
`amount` | Decimals separated by ., max. 3 decimals
`currency` | 3 letter currency code

You can also pass `transactionToken` on the payload if you receive a token using Till Payment's `payment.js`

For more information about the payload, refer to: [Till Payment Documentation](https://gateway.tillpayments.com/documentation/apiv3#transaction-request-preauthorize)

For more information about the card data, refer to: [Omnipay Credit Card Documentation](https://github.com/thephpleague/omnipay/tree/v2.3.1#credit-card--payment-form-input)


### Capture Transaction

A Capture completes the payment which was previously authorized with the Preauthorize method.

Depending on the payment method you can even capture only a partial amount of the authorized amount.

```php
try {
    // You need to define your own $formData array
    $card = new CreditCard($formData);
    
    $request = $gateway->capture([
        'amount' => '10.00', // this represents $10.00
        'currency' => 'AUD',
        'card' => $card,
        'referenceUuid' => 'bcdef23456bcdef23456', // UUID / transaction reference of a preauthorize
    ]);
    
    $response = $transaction->send();

    if ($response->isSuccessful()) {

        // Do stuff here
        // Use $response->getTransactionReference() to get transaction uuid

    } else {

        // Handle errors

    }

} catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {

    // Handle Exception Errors (use $e->getResponse()->getBody() to get JSON data)

} catch (\Exception $e) {

    // Handle Exception Errors (use $e->getMessage() to get data)

}
```

Required Fields | Notes
--- | ---
`merchantTransactionId` | Your unique transaction ID. If left blank, default one will be used |
`amount` | Decimals separated by ., max. 3 decimals
`currency` | 3 letter currency code
`referenceUuid` | UUID / transaction reference of a preauthorize

For more information about the payload, refer to: [Till Payment Documentation](https://gateway.tillpayments.com/documentation/apiv3#transaction-request-capture)

For more information about the card data, refer to: [Omnipay Credit Card Documentation](https://github.com/thephpleague/omnipay/tree/v2.3.1#credit-card--payment-form-input)


### Purchase Transaction

This is called **Debit** on Till Documentation

```php
try {
    // You need to define your own $formData array
    $card = new CreditCard($formData);
    
    $request = $gateway->purchase([
        'amount' => '10.00', // this represents $10.00
        'currency' => 'AUD',
        'card' => $card,
        'returnUrl' => 'https://www.example.com/return', // optional
    ]);
    
    $response = $transaction->send();

    if ($response->isSuccessful()) {

        // Do stuff here
        // Use $response->getTransactionReference() to get transaction uuid

    } else {

        // Handle errors

    }

} catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {

    // Handle Exception Errors (use $e->getResponse()->getBody() to get JSON data)

} catch (\Exception $e) {

    // Handle Exception Errors (use $e->getMessage() to get data)

}
```

Required Fields | Notes
--- | ---
`merchantTransactionId` | Your unique transaction ID. If left blank, default one will be used |
`amount` | Decimals separated by ., max. 3 decimals
`currency` | 3 letter currency code

For more information about the payload, refer to: [Till Payment Documentation](https://gateway.tillpayments.com/documentation/apiv3#transaction-request-debit)

For more information about the card data, refer to: [Omnipay Credit Card Documentation](https://github.com/thephpleague/omnipay/tree/v2.3.1#credit-card--payment-form-input)


### Authorize Transaction

This is called **Preauthorize** on Till Documentation

A Preauthorize reserves the payment amount on the customer's payment instrument.

Depending on the payment method you have up to 7 days until you must Capture the transaction before the authorization expires.

```php
try {
    // You need to define your own $formData array
    $card = new CreditCard($formData);
    
    $request = $gateway->authorize([
        'amount' => '10.00', // this represents $10.00
        'currency' => 'AUD',
        'card' => $card,
        'returnUrl' => 'https://www.example.com/return', // optional
    ]);
    
    $response = $transaction->send();

    if ($response->isSuccessful()) {

        // Do stuff here
        // Use $response->getTransactionReference() to get transaction uuid

    } else {

        // Handle errors

    }

} catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {

    // Handle Exception Errors (use $e->getResponse()->getBody() to get JSON data)

} catch (\Exception $e) {

    // Handle Exception Errors (use $e->getMessage() to get data)

}
```


Required Fields | Notes
--- | ---
`merchantTransactionId` | Your unique transaction ID. If left blank, default one will be used |
`amount` | Decimals separated by ., max. 3 decimals
`currency` | 3 letter currency code

For more information about the payload, refer to: [Till Payment Documentation](https://gateway.tillpayments.com/documentation/apiv3#transaction-request-preauthorize)

For more information about the card data, refer to: [Omnipay Credit Card Documentation](https://github.com/thephpleague/omnipay/tree/v2.3.1#credit-card--payment-form-input)


### Capture Transaction

A Capture completes the payment which was previously authorized with the Preauthorize method.

Depending on the payment method you can even capture only a partial amount of the authorized amount.

```php
try {

    $request = $gateway->capture([
        'amount' => '10.00', // this represents $10.00
        'currency' => 'AUD',
        'referenceUuid' => 'bcdef23456bcdef23456', // UUID / transaction reference of a preauthorize
    ]);
    
    $response = $transaction->send();

    if ($response->isSuccessful()) {

        // Do stuff here
        // Use $response->getTransactionReference() to get transaction uuid

    } else {

        // Handle errors

    }

} catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {

    // Handle Exception Errors (use $e->getResponse()->getBody() to get JSON data)

} catch (\Exception $e) {

    // Handle Exception Errors (use $e->getMessage() to get data)

}
```

Required Fields | Notes
--- | ---
`merchantTransactionId` | Your unique transaction ID. If left blank, default one will be used |
`amount` | Decimals separated by ., max. 3 decimals
`currency` | 3 letter currency code
`referenceUuid` | UUID / transaction reference of a preauthorize

For more information about the payload, refer to: [Till Payment Documentation](https://gateway.tillpayments.com/documentation/apiv3#transaction-request-capture)


### Void Transaction

A Void cancels a previously performed authorization made with the Preauthorize method.

```php
try {

    $request = $gateway->void([
        'referenceUuid' => 'bcdef23456bcdef23456', // UUID / transaction reference of a preauthorize
    ]);
    
    $response = $transaction->send();

    if ($response->isSuccessful()) {

        // Do stuff here
        // Use $response->getTransactionReference() to get transaction uuid

    } else {

        // Handle errors

    }

} catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {

    // Handle Exception Errors (use $e->getResponse()->getBody() to get JSON data)

} catch (\Exception $e) {

    // Handle Exception Errors (use $e->getMessage() to get data)

}
```

Required Fields | Notes
--- | ---
`merchantTransactionId` | Your unique transaction ID. If left blank, default one will be used |
`referenceUuid` | UUID / transaction reference of a preauthorize

For more information about the payload, refer to: [Till Payment Documentation](https://gateway.tillpayments.com/documentation/apiv3#transaction-request-void)


### Create Card Transaction

This is called **Register** on Till Documentation

Registers a customer's payment instrument for future charges (Debits or Preauthorizations)

```php
try {

    // You need to define your own $formData array
    $card = new CreditCard($formData);

    $request = $gateway->createCard([
        'card' => $card,
    ]);
    
    $response = $transaction->send();

    if ($response->isSuccessful()) {

        // Do stuff here
        // Use $response->getTransactionReference() to get transaction uuid

    } else {

        // Handle errors

    }

} catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {

    // Handle Exception Errors (use $e->getResponse()->getBody() to get JSON data)

} catch (\Exception $e) {

    // Handle Exception Errors (use $e->getMessage() to get data)

}
```

Required Fields | Notes
--- | ---
`merchantTransactionId` | Your unique transaction ID. If left blank, default one will be used |

For more information about the payload, refer to: [Till Payment Documentation](https://gateway.tillpayments.com/documentation/apiv3#transaction-request-capture)

For more information about the card data, refer to: [Omnipay Credit Card Documentation](https://github.com/thephpleague/omnipay/tree/v2.3.1#credit-card--payment-form-input)


### Delete Card Transaction

This is called **Deregister** on Till Documentation

A Deregister deletes a previously registered payment instrument using Register.

```php
try {

    // You need to define your own $formData array
    $card = new CreditCard($formData);

    $request = $gateway->deleteCard([
        'referenceUuid' => 'ccf0ddd790db9d7ef41b',
    ]);
    
    $response = $transaction->send();

    if ($response->isSuccessful()) {

        // Do stuff here
        // Use $response->getTransactionReference() to get transaction uuid

    } else {

        // Handle errors

    }

} catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {

    // Handle Exception Errors (use $e->getResponse()->getBody() to get JSON data)

} catch (\Exception $e) {

    // Handle Exception Errors (use $e->getMessage() to get data)

}
```

Required Fields | Notes
--- | ---
`merchantTransactionId` | Your unique transaction ID. If left blank, default one will be used |
`referenceUuid` | UUID of a register, debit-with-register or preauthorize-with-register

For more information about the payload, refer to: [Till Payment Documentation](https://gateway.tillpayments.com/documentation/apiv3#transaction-request-capture)

For more information about the card data, refer to: [Omnipay Credit Card Documentation](https://github.com/thephpleague/omnipay/tree/v2.3.1#credit-card--payment-form-input)


### Refund Transaction

A Refund reverses a payment which was previously performed with Debit or Capture.

Depending on the payment method you can even refund only a partial amount of the original transaction amou

```php
try {

    $request = $gateway->refund([
        'amount'                => '1.25',
        'currency'              => 'AUD',
        'referenceUuid'         => '48bbd15cdf9e5b81985d',
    ]);
    
    $response = $transaction->send();

    if ($response->isSuccessful()) {

        // Do stuff here
        // Use $response->getTransactionReference() to get transaction uuid

    } else {

        // Handle errors

    }

} catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {

    // Handle Exception Errors (use $e->getResponse()->getBody() to get JSON data)

} catch (\Exception $e) {

    // Handle Exception Errors (use $e->getMessage() to get data)

}
```

Required Fields | Notes
--- | ---
`merchantTransactionId` | Your unique transaction ID. If left blank, default one will be used |
`amount` | Decimals separated by ., max. 3 decimals
`currency` | 3 letter currency code
`referenceUuid` | UUID of a register, debit-with-register or preauthorize-with-register

For more information about the payload, refer to: [Till Payment Documentation](https://gateway.tillpayments.com/documentation/apiv3#transaction-request-capture)

For more information about the card data, refer to: [Omnipay Credit Card Documentation](https://github.com/thephpleague/omnipay/tree/v2.3.1#credit-card--payment-form-input)

### Accepting Notification

```php

$gateway = Omnipay\Omnipay::create('TillPayments');

// The security key must be provided, either by setting it directly or as a param in the initialize function.
// It is used to verify the X-Signature header sent by Till to ensure the request hasn't been tampered with.
$gateway->setSecurityKey();

// $request will capture the POST data sent by Till
$request = $gateway->acceptNotification(); 

// The raw data sent is available:
$data = $request->getData();

// Provides the result of the signature verification.
// If this returns false then the data cannot be trusted.
$request->isSignatureValid();

// Process payment here...

// Responds to Till with a 200 OK
$notificationResponse = $notificationRequest->send();
$notificationResponse->acknowledge();
```

### Payout Transaction

Coming soon


### Incremental Authorization

Coming soon


## The Payment Responses

### Successful Response

For a successful responses, a reference will normally be generated, which can be used to capture or refund the transaction at a later date. The following methods are always available:

```php
$response = $gateway->purchase([
    'amount' => '10.00', 
    'currency' => 'AUD', 
    'card' => $card
])->send();

$response->isSuccessful(); // is the response successful?
$response->isRedirect(); // is the response a redirect?
$response->getTransactionReference(); // a transaction uuid generated by the payment gateway
$response->getMessage(); // a message generated by the payment gateway
```

If you are going to implement refund and void, you will need to always save the transaction reference since it is often used as `referenceUuid`.


### Redirect Response

After processing a payment, the cart should check whether the response requires a redirect, and if so, redirect accordingly:

```php
$response = $gateway->purchase([
    'amount' => '10.00', 
    'currency' => 'AUD', 
    'card' => $card
])->send();

if ($response->isSuccessful()) {
    // payment is complete
} elseif ($response->isRedirect()) {
    $response->redirect(); // this will automatically forward the customer
} else {
    // not successful
}
```

The customer isn't automatically forwarded on, because often the cart or developer will want to customize the redirect method (or if payment processing is happening inside an AJAX call they will want to return JS to the browser instead).

To display your own redirect page, simply call `getRedirectUrl()` on the response, then display it accordingly:

```php
$url = $response->getRedirectUrl();
```


### Error Response

You can test for a successful response by calling `isSuccessful()` on the response object. If there was an error communicating with the gateway, or your request was obviously invalid, an exception will be thrown. In general, if the gateway does not throw an exception, but returns an unsuccessful response, it is a message you should display to the customer. If an exception is thrown, it is either a bug in your code (missing required fields), or a communication error with the gateway.

The simplest way is to wrap the entire request in a try-catch block.

```php
try {

    $request = $gateway->refund([
        'amount'                => '1.25',
        'currency'              => 'AUD',
        'referenceUuid'         => '48bbd15cdf9e5b81985d',
    ]);
    
    $response = $transaction->send();

    if ($response->isSuccessful()) {

        // Do stuff here like marking order as complete

    } else {

        // Handle errors

    }

} catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {

    // Handle Exception Errors (use $e->getResponse()->getBody() to get JSON data)

} catch (\Exception $e) {

    // Handle Exception Errors (use $e->getMessage() to get data)

}
```


## Advanced Usage

### Using Proxy
There might be times when the user is required to route the outbound traffic through a proxy. Below is an example to implement a proxy route for Till Payments.
```php
// Create Proxy configuration
$proxy = new \Omnipay\TillPayments\Proxy([
    'enabled' => TRUE,
    'url' => 'my-proxy-url',
    'port' => '8080',
    'username' => 'your-proxy-username',
    'password' => 'your-proxy-password',
    'certUrl' => 'your-certificate', // If your proxy has a certificate, otherwise leave blank
    'noVerifySSLPeer' => FALSE // or TRUE if you are using VerifySSLPeer, usually used for production
]);

// Create gateway
$gateway = \Omnipay\Omnipay::create('TillPayments');

// Initialise the gateway
$gateway->initialize(array(
    'apiKey' => 'your-api-key',
    'secretKey' => 'your-secret-key',
    'username' => 'your-username',
    'password' => 'your-password',
    'testMode'  => TRUE, // or FALSE when you are ready for live transactions
    'defaultProxy' => $proxy, // or instance of \Omnipay\TillPayments\Proxy 
    'defaultMerchantTransactionIdPrefix'  => 'omnipay-', // prefix of the merchantTransactionId (optional)
));
```

### Using the PCI Direct API
Some situations may arise where raw credit card data needs to be sent to Till directly without the use of a hosted payment form or payment.js tokenization. This flag enables the use of this API. The API functions exactly the same as the regular API except it can now take the `cardData` element.
```php
// Create credit card object
$card = new \Omnipay\Common\CreditCard([
    'firstName' => 'Lionel',
    'lastName' => 'Messi',
    'number' => '4111111111111111',
    'cvv' => '123',
    'expiryMonth' => '3',
    'expiryYear' => '2024'
]);

// Create gateway
$gateway = \Omnipay\Omnipay::create('TillPayments');

// Initialise the gateway
$gateway->initialize(array(
    'apiKey' => 'your-api-key',
    'secretKey' => 'your-secret-key',
    'username' => 'your-username',
    'password' => 'your-password',
    'testMode'  => TRUE, // or FALSE when you are ready for live transactions
    'pciDirect' => TRUE, // enables PCI direct API
    'defaultMerchantTransactionIdPrefix'  => 'omnipay-', // prefix of the merchantTransactionId (optional)
));

// Register the card with Till
$gateway->createCard([
    'card' => $card,
]);
```

### Using Customer instead of CreditCard

Coming soon

### Schedule

Coming soon

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release announcements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/teamvisualr/omnipay-tillpayments/issues),
or better yet, fork the library and submit a pull request.
