# Nets Easy for MorningMedley & Laravel

## Table of Contents

- [Introduction](#introduction)
- [Installation](#installation)
- [Usage](#usage)
  - [Setup your .env file](#setup-your-env-file)
  - [Running migrations](#running-migrations)
  - [Create payment](#create-payment)
  - [Handle existing payment](#handle-existing-payment)
    - [Get Payment](#get-payment)
    - [Create payment](#create-payment-1)
    - [Terminate payment](#terminate-payment)
    - [Cancel payment](#cancel-payment)
    - [Charge payment](#charge-payment)
  - [Handle webhooks](#handle-webhooks)
    - [List of implemented webhooks](#list-of-implemented-webhooks)
    - [Actions](#actions)
-  [Testing](#testing)
- [Credits](#credits)
- [License](#license)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require morningtrain/nets-easy
```

### For Laravel

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="nets-easy-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="nets-easy-config"
```

This is the contents of the published config file:

```php
return [
    'auth-key' => env('NETS_EASY_AUTH_KEY'),
    'events' => [
        'payment.cancel.created' => \Morningtrain\NETSEasy\Events\PaymentCancelCreated::class,
        'payment.cancel.failed' => \Morningtrain\NETSEasy\Events\PaymentCancelFailed::class,
        'payment.charge.created.v2' => \Morningtrain\NETSEasy\Events\PaymentChargeCreatedV2::class,
        'payment.charge.failed' => \Morningtrain\NETSEasy\Events\PaymentChargeFailed::class,
        'payment.checkout.completed' => \Morningtrain\NETSEasy\Events\PaymentCheckoutCompleted::class,
        'payment.created' => \Morningtrain\NETSEasy\Events\PaymentCreated::class,
        'payment.refund.completed' => \Morningtrain\NETSEasy\Events\PaymentRefundCompleted::class,
        'payment.refund.failed' => \Morningtrain\NETSEasy\Events\PaymentRefundFailed::class,
        'payment.refund.initiated.v2' => \Morningtrain\NETSEasy\Events\PaymentRefundInitiatedV2::class,
        'payment.reservation.created.v2' => \Morningtrain\NETSEasy\Events\PaymentReservationCreatedV2::class,
        'payment.reservation.failed' => \Morningtrain\NETSEasy\Events\PaymentReservationFailed::class,
    ],
    'in_test_mode' => env('NETS_EASY_IN_TEST_MODE', true),
    'secret_key' => env('NETS_EASY_SECRET_KEY'),
];

```

## Usage

### Setup your .env file

```dotenv
NETS_EASY_AUTH_KEY={random_string}
NETS_EASY_IN_TEST_MODE={true|false}
NETS_EASY_SECRET_KEY={SECRET_KEY}
```

### Running migrations
#### For Laravel

```bash
php wp artisan migrate
```
#### For Laravel

```bash
php artisan migrate
```

### Create payment

```php
// Create payment and set payment information and urls
$netsEasyPayment = \Morningtrain\NETSEasy\NETSEasy::makeNetsEasyPaymentFromPaymentDTO(
    new \Morningtrain\NETSEasy\DTOs\Payment(
        reference: $order->id,
        items: [
            new \Morningtrain\NETSEasy\DTOs\Item(
                reference: $orderItem->sku,
                name: $orderItem->name,
                unitPrice: $orderItem->price,
                unit: 'pcs',
                quantity: $orderItem->quantity,
                taxRate: $orderItem->tax_rate,
                taxIncluded: true,
            ),
        ],
        currency: 'DKK',
        termsUrl: 'https://example.com/terms',
        returnUrl: 'https://example.com/success?token=' . $order->token,
        cancelUrl: 'https://example.com/cancel',
        customer: new \Morningtrain\NETSEasy\DTOs\Customer(
            reference: $customer->id, 
            email: $customer->email,
            firstName: $customer->first_name,
            lastName: $customer->last_name,
            phoneNumber: 12345678,
            phoneCountryPrefix: 45,
            billingAddress: new \Morningtrain\NETSEasy\DTOs\Address(
                addressLine1: $customer->address1,
                addressLine2: $customer->address2,
                postalCode: $customer->zip_code,
                city: $order->city,
                country: CountryCode::tryFromName('DK')?->value,
            ),
        ),
        autoCharge: true
    )
);

try {
    $response = $netsEasyPayment->create();
} catch (ConnectionException $e) {
    return new \WP_REST_Response(['errors' => [__('Fejl ved oprettelse af ordre.', 'great-northern')]], 406);
}

if ($response->getStatusCode() !== 201) {
    wp_redirect($checkoutUrl);
    exit();
}

// Save payment id for later use
// $netsEasyPayment->getPaymentId()

// Redirect to payment page
wp_redirect($response->getPaymentPageUrl());
exit();
```

### Handle existing payment

#### Get Payment

```php
$netsEasyPayment = \Morningtrain\NETSEasy\NETSEasy::makeNetsEasyPaymentFromPaymentId($paymentId);
```

#### Create payment

```php
$netsEasyPayment->create();
```

#### Terminate payment
To terminate payment, the customer must not have finished checkout. You can use it on the cancel callback to avoid double payments later.

```php
$netsEasyPayment->terminate();
```

#### Cancel payment

```php
$netsEasyPayment->cancel();
```

#### Charge payment

```php
$netsEasyPayment->charge();
```

### Handle webhooks
The implementation handle webhooks and sets the payment status automatically.

If you need to do something on a specific webhook, you can do that throug actions and filters.

#### List of implemented webhooks

| Name                        | Descritpion                                                     |
|-----------------------------|-----------------------------------------------------------------|
| payment.created             | A payment has been created.                                     |
| payment.reservation.created | The amount of the payment has been reserved.                    |
| payment.reservation.failed  | A reservation attempt has failed.                               |
| payment.checkout.completed  | The customer has completed the checkout.                        |
| payment.charge.created.v2   | The customer has successfully been charged, partially or fully. |
| payment.charge.failed       | A charge attempt has failed.                                    |
| payment.refund.initiated.v2 | A refund has been initiated.                                    |
| payment.refund.failed       | A refund attempt has failed.                                    |
| payment.refund.completed    | A refund has successfully been completed.                       |
| payment.cancel.created      | A reservation has been canceled.                                |
| payment.cancel.failed       | A cancellation has failed.                                      |

#### Actions

| Hook Name                                     | Description                                      |
|-----------------------------------------------|--------------------------------------------------|
| morningtrain/nets-easy/webhook/{$webhookName} | Do something when the webhook is being processed |


## Testing

```bash
composer test
```

## Credits

- [Mathias B](https://github.com/matbaek)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
