<?php

/**
 * PayPal plugin library for Li3
 *
 * @name        PaypalService.php
 * @author		Gautam Sathe <gautam@hemisphereinteractive.com>
 * @package     paypal_lib
 * @copyright   Copyright (c) 2012, Gautam Sathe
 * @license		http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace paypal_lib\extensions\net\http;

use paypal_lib\extensions\net\http\DirectPaymentService;
use paypal_lib\extensions\net\http\RPProfileService;
use paypal_lib\extensions\net\http\AdminService;

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
		$ddps = new DirectPaymentService();
		$rps = new RPProfileService();
		$as = new AdminService();
		$ack = null;
		switch ($type) {
			case 'doDirectPayment':
				$ack = $ddps->directPayment($postData);
				break;
			case 'createRecurringPaymentsProfile':
				$ack = $rps->createRPProfile($postData);
				break;
			case 'getRecurringPaymentsProfileDetails':
				$ack = $rps->getRPProfileDetails($postData);
				break;
			case 'manageRecurringPaymentsProfileStatus':
				$ack = $rps->manageRPProfileStatus($postData);
				break;
			case 'getTransactionDetails':
				$ack = $as->getTransactionDetails($postData);
				break;
			default:
				$ack = $ddps->directPayment($postData);
				break;
		}
		return $ack;
	}

}

?>