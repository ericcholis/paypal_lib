# PayPal Plugin for Li3

payapl_lib is a **Lithium plugin**, NOT a Lithium app. This plugin is written to work only with Li3 and is based on code from PayPal PHP SDK code samples.

### Usage

* Install paypal_lib plugin in your existing application using git submodule in libraries/paypal_lib folder


```
git submodule add git://github.com/matuag/paypal_lib.git libraries/paypal_lib
git submodule init
git submodule update
```

* add plugin reference to config/bootstrap/libraries.php

```php
Libraries::add('paypal_lib');
```

* by default the configuration files contains PayPal API Signature for making API calls to the PayPal sandbox.
To override the default configuration create config/bootstrap/paypal.php similar to [paypal_lib/config/bootstrap/paypal.php](https://github.com/matuag/paypal_lib/blob/master/config/bootstrap/paypal.php)

* indicate at the top of the class or php file where you want to use the plugin

```php
use paypal_lib\extensions\net\http\PaypalService;
```

* create an instance of class PaypalService

```php
$paypalService = new PaypalService();
```

* at present only DoDirectPaymentService is supported

```php
$resArray = $paypalService->paypalPayment($postData, 'doDirectPayment');
```
