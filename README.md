# PayPal Plugin for Li3

payapl_lib is a **Lithium plugin**, NOT a Lithium app. This plugin is written to work only with Li3 and is based on code from PayPal PHP SDK ([PayPal API: Name-Value Pair Interfacecode](https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/library_download_sdks)) samples.

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
To override the default configuration create app/config/bootstrap/paypal.php similar to [paypal_lib/config/bootstrap/paypal.php](https://github.com/matuag/paypal_lib/blob/master/config/bootstrap/paypal.php)

* include the plugin in the php file which will use the library

```php
use paypal_lib\extensions\net\http\PaypalService;
```

* create an instance of class PaypalService

```php
$paypalService = new PaypalService();
```

* Following services are supported

DirectPayment
```php
$resArray = $paypalService->paypalPayment($postData, 'doDirectPayment');
```

CreateRecurringPaymentsProfile
```php
$resArray = $paypalService->paypalPayment($postData, 'createRecurringPaymentsProfile');
```

GetRecurringPaymentsProfileDetails
```php
$resArray = $paypalService->paypalPayment($postData, 'getRecurringPaymentsProfileDetails');
```

ManageRecurringPaymentsProfileStatus
```php
$resArray = $paypalService->paypalPayment($postData, 'manageRecurringPaymentsProfileStatus');
```

GetTransactionDetails
```php
$resArray = $paypalService->paypalPayment($postData, 'getTransactionDetails');
```


### Useful links

* [PayPal Sample Code](https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/library_code)

* [PayPal Documentation](https://www.x.com/developers/paypal/documentation-tools)

* [PayPal API Reference](https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/howto_api_reference)

* [PHP Lithium Framework Docs](http://lithify.me/docs)