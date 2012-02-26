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

class PaypalService {

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
		$defaults = array('init' => true);
		$this->_config = $config + $defaults;
	}

	public function paypalPayment($postData, $type = 'doDirectPayment') {
		$ddps = new DoDirectPaymentService();
		$ack = null;
		switch ($type) {
			case 'doDirectPayment':
				$ack = $ddps->directPayment($postData);
				break;
			default:
				$ack = $ddps->directPayment($postData);
				break;
		}
		return $ack;
	}


}

?>