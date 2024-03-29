<?php

/**
 * PayPal plugin library for Li3
 *
 * @name        DoDirectPaymentService.php
 * @author		Gautam Sathe <gautam@hemisphereinteractive.com>
 * @package     paypal_lib
 * @copyright   Copyright (c) 2012, Gautam Sathe
 * @license		http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace paypal_lib\extensions\net\http;

/**
 * Submits a credit card transaction to PayPal using a
 * DoDirectPayment request.
 *
 * The code collects transaction parameters from the form
 * displayed by DoDirectPayment.php then constructs and sends
 * the DoDirectPayment request string to the PayPal server.
 * The paymentType variable becomes the PAYMENTACTION parameter
 * of the request string.
 *
 */

use paypal_lib\extensions\net\http\CallerService;

class DirectPaymentService {

	protected $_config = array();

	/**
	 * Initializes class configuration (`$_config`)
	 *
	 * @access public
	 * @param array $config The configuration options which will be assigned to the `$_config`
	 *              property. This method accepts one configuration option:
	 *              - `'currencyCode'` _string_: Sets the default currency code.
	 * 					Defaults to `USD`.
	 */
	public function __construct(array $config = array()) {
		$defaults = array('currencyCode' => 'USD');
		$this->_config = $config + $defaults;
	}

	/**
	 * This method takes the paypal direct payment request and return the
	 * response using CallerService class
	 *
	 * @access public
	 * @param array $postData
	 * @return array $resArray
	 */
	public function directPayment($postData) {
		$callService = new CallerService();
		// Get required parameters from the web form for the request
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
		$currencyCode = $this->_config['currencyCode'];
		$countryCode = isset($postData['countryCode']) ? urlencode($postData['countryCode']) : 'US';
		$paymentType = urlencode($postData['paymentType']);

		$startDateMonth = null;
		$startDateYear = null;

		//3D Secure fields
		$eciFlag = null;
		$cavv = null;
		$xid = null;
		$enrolled = null;
		$pAResStatus = null;

		if (isset($postData['3D-Secure']) && $postData['3D-Secure'] === true) {
			$startDateMonth = urlencode($postData['startDateMonth']);
			$padStartDateMonth = str_pad($startDateMonth, 2, '0', STR_PAD_LEFT);
			$startDateYear = urlencode($postData['startDateYear']);
			//3D Secure fields
			$eciFlag = urlencode($postData['eciFlag']);
			$cavv = urlencode($postData['cavv']);
			$xid = urlencode($postData['xid']);
			$enrolled = urlencode($postData['enrolled']);
			$pAResStatus = urlencode($postData['pAResStatus']);

		}
		// Construct the request string that will be sent to PayPal.
		// The variable $nvpstr contains all the variables and is a
		// name value pair string with & as a delimiter
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
		$nvpstr .= '&COUNTRYCODE=' . $countryCode;
		$nvpstr .= '&CURRENCYCODE=' . $currencyCode;

		if (isset($postData['3D-Secure']) && $postData['3D-Secure'] === '3D-Secure') {
			$nvpstr .= '&STARTDATE=' . $padStartDateMonth . $startDateYear;
			$nvpstr .= '&ECI3DS=' . $eciFlag;
			$nvpstr .= '&CAVV=' . $cavv;
			$nvpstr .= '&XID=' . $xid;
			$nvpstr .= '&MPIVENDOR3DS=' . $enrolled;
			$nvpstr .= '&AUTHSTATUS3DS=' . $pAResStatus;
		}

		// Make the API call to PayPal, using API signature.
		// The API response is stored in an associative array called $resArray
		$resArray = $callService->hashCall('doDirectPayment', $nvpstr);

		return $resArray;
	}

}
?>

