<?php

/**
 * PayPal plugin library for Li3
 *
 * @name        DODIRECTPAYMENTSERVICE.PHP
 * @author		Gautam Sathe <gautam@hemisphereinteractive.com>
 * @package     paypal_lib
 * @copyright   Copyright (c) 2012, Gautam Sathe
 * @license		http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace paypal_lib\extensions\net\http;
/***********************************************************
 DoDirectPaymentReceipt.php

 Submits a credit card transaction to PayPal using a
 DoDirectPayment request.

 The code collects transaction parameters from the form
 displayed by DoDirectPayment.php then constructs and sends
 the DoDirectPayment request string to the PayPal server.
 The paymentType variable becomes the PAYMENTACTION parameter
 of the request string.

 After the PayPal server returns the response, the code
 displays the API request and response in the browser.
 If the response from PayPal was a success, it displays the
 response parameters. If the response was an error, it
 displays the errors.

 Called by DoDirectPayment.php.

 Calls CallerService.php and APIError.php.

 ***********************************************************/

use paypal_lib\extensions\net\http\CallerService;

class DoDirectPaymentService {
	protected $_config = array();

	/**
	 * Initializes class configuration (`$_config`), and assigns object properties using the
	 * `_init()` method, unless otherwise specified by configuration. See below for details.
	 *
	 * @see lithium\core\Object::$_config
	 * @see lithium\core\Object::_init()
	 * @param array $config The configuration options which will be assigned to the `$_config`
	 *              property. This method accepts one configuration option:
	 *              - `'init'` _boolean_: Controls constructor behavior for calling the `_init()`
	 *                method. If `false`, the method is not called, otherwise it is. Defaults to
	 *                `true`.
	 */
	public function __construct(array $config = array()) {
		$defaults = array('currencyCode' => 'USD');
		$this->_config = $config + $defaults;
	}

	public function directPayment($postData) {
		$callService = new CallerService();
		/**
		 * Get required parameters from the web form for the request
		 */
		$paymentType = urlencode($postData['paymentType']);
		$firstName = urlencode($postData['firstName']);
		$lastName = urlencode($postData['lastName']);
		$creditCardType = urlencode($postData['creditCardType']);
		$creditCardNumber = urlencode($postData['creditCardNumber']);
		$expDateMonth = urlencode($postData['expDateMonth']);

		// Month must be padded with leading zero
		$padDateMonth = str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);

		$expDateYear = urlencode($postData['expDateYear']);
		$cvv2Number = urlencode($postData['cvv2Number']);
		$address1 = urlencode($postData['address1']);
		$address2 = urlencode($postData['address2']);
		$city = urlencode($postData['city']);
		$state = urlencode($postData['state']);
		$zip = urlencode($postData['zip']);
		$amount = urlencode($postData['amount']);
		//$currencyCode=urlencode($_POST['currency']);
		$currencyCode = $this->_config['currencyCode'];
		$paymentType = urlencode($postData['paymentType']);

		/* Construct the request string that will be sent to PayPal.
		 The variable $nvpstr contains all the variables and is a
		 name value pair string with & as a delimiter */
		$nvpstr = '&PAYMENTACTION=' . $paymentType;
		$nvpstr .= '&AMT=' . $amount;
		$nvpstr .= '&CREDITCARDTYPE=' . $creditCardType;
		$nvpstr .= '&ACCT=' . $creditCardNumber;
		$nvpstr .= '&EXPDATE=' . $padDateMonth . $expDateYear;
		$nvpstr .= '&CVV2=' . $cvv2Number;
		$nvpstr .= '&FIRSTNAME=' . $firstName;
		$nvpstr .= '&LASTNAME=' . $lastName;
		$nvpstr .= '&STREET=' . $address1;
		$nvpstr .= '&CITY=' . $city;
		$nvpstr .= '&STATE=' . $state;
		$nvpstr .= '&ZIP=' . $zip;
		$nvpstr .= '&COUNTRYCODE=US';
		$nvpstr .= '&CURRENCYCODE=' . $currencyCode;

		/* Make the API call to PayPal, using API signature.
		 The API response is stored in an associative array called $resArray */
		$resArray = $callService->hashCall('doDirectPayment', $nvpstr);

		/* Display the API response back to the browser.
		 If the response from PayPal was a success, display the response parameters'
		 If the response was an error, display the errors received using APIError.php.
		 */
		//$ack = strtoupper($resArray["ACK"]);

		// if ($ack != "SUCCESS") {
			// $_SESSION['reshash'] = $resArray;
			// $location = "APIError.php";
			// header("Location: $location");
		// }
		//var_dump($resArray);
		return $resArray;
	}

}
?>

