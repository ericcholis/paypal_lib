<?php

/**
 * PayPal plugin library for Li3
 *
 * @name        PAYPALSERVICE.PHP
 * @author		Gautam Sathe <gautam@hemisphereinteractive.com>
 * @package     paypal_lib
 * @copyright   Copyright (c) 2012, Gautam Sathe
 * @license		http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace paypal_lib\extensions\net\http;

use paypal_lib\extensions\net\http\DoDirectPaymentService;
use paypal_lib\extensions\net\http\RPProfileService;

class PaypalService {

	protected $_config = array();

	/**
	 * Initializes class configuration (`$_config`)
	 *
	 * @access public
	 * @param array $config The configuration options which will be assigned to the `$_config`
	 *              property.
	 */
	public function __construct(array $config = array()) {
		$defaults = array();
		$this->_config = $config + $defaults;
	}

	public function paypalPayment($postData, $type = 'doDirectPayment') {
		$ddps = new DoDirectPaymentService();
		$rps = new RPProfileService();
		$ack = null;
		switch ($type) {
			case 'doDirectPayment':
				$ack = $ddps->directPayment($postData);
				break;
			case 'createRecurringPaymentsProfile':
				$ack = $rps->createRPProfile($postData);
				break;
			default:
				$ack = $ddps->directPayment($postData);
				break;
		}
		return $ack;
	}

}

?>