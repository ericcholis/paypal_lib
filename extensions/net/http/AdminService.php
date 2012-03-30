<?php

/**
 * PayPal plugin library for Li3
 *
 * @name        AdminService.php
 * @author		Gautam Sathe <gautam@hemisphereinteractive.com>
 * @package     paypal_lib
 * @copyright   Copyright (c) 2012, Gautam Sathe
 * @license		http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace paypal_lib\extensions\net\http;

class AdminService {

	protected $_config = array();
	protected $_callService;

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
		$this->_callService = new CallerService();
	}

	/**
	 * This method sends a GetTransactionDetails NVP API request to PayPal
	 * and return the response using CallerService class
	 *
	 * @access public
	 * @param array $postData
	 * @return array $resArray
	 */
	public function getTransactionDetails($postData) {
		$transactionID = urlencode($postData['transactionID']);

		// Construct the request string that will be sent to PayPal.
		// The variable $nvpstr contains all the variables and is a
		// name value pair string with & as a delimiter
		$nvpStr = '&TRANSACTIONID=' . $transactionID;

		// Make the API call to PayPal, using API signature.
		// The API response is stored in an associative array called $resArray
		$resArray = $this->_callService->hashCall('GetTransactionDetails', $nvpStr);

		return $resArray;
	}

}
?>

