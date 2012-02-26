<?php
namespace paypal_lib\extensions\net\http;
/****************************************************
 CallerService.php

 This file uses the constants.php to get parameters needed
 to make an API call and calls the server.if you want use your
 own credentials, you have to change the constants.php

 Called by TransactionDetails.php, ReviewOrder.php,
 DoDirectPaymentReceipt.php and DoExpressCheckoutPayment.php.

 ****************************************************/

class CallerService {
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
		$defaults = array(
			'paypalApiUsername' => PAYPAL_API_USERNAME,
			'paypalApiPassword' => PAYPAL_API_PASSWORD,
			'paypalApiSignature' => PAYPAL_API_SIGNATURE,
			'paypalApiEndpoint' => PAYPAL_API_ENDPOINT,
			'paypalVersion' => PAYPAL_VERSION,
			'paypalSubject' => PAYPAL_SUBJECT,
			'paypalUseProxy' => PAYPAL_USE_PROXY,
			'paypalProxyHost' => PAYPAL_PROXY_HOST,
			'paypalProxyPort' => PAYPAL_PROXY_PORT,
			'paypalUrl' => PAYPAL_URL,
			'paypalAuthToken' => PAYPAL_AUTH_TOKEN,
			'paypalAuthSignature' => PAYPAL_AUTH_SIGNATURE,
			'paypalAuthTimestamp' => PAYPAL_AUTH_TIMESTAMP,
			'paypalAuthMode' => PAYPAL_AUTH_MODE,
			'paypalAckSuccess' => PAYPAL_ACK_SUCCESS,
			'paypalAckSuccessWithWarning' => PAYPAL_ACK_SUCCESS_WITH_WARNING
		);
		$this->_config = $config + $defaults;
	}

	private function _nvpHeader() {
		$nvpHeaderStr = '';
		//$authMode = "3TOKEN"; //Merchant's API 3-TOKEN Credential is required to make API Call.
		//$authMode = "FIRSTPARTY"; //Only merchant Email is required to make EC Calls.
		//$authMode = "THIRDPARTY";Partner's API Credential and Merchant Email as Subject are required.
		if (!empty($this->_config['paypalAuthMode'])) {
			$authMode = $this->_config['paypalAuthMode'];
		} else {
			if ((!empty($this->_config['paypalApiUsername'])) && (!empty($this->_config['paypalApiPassword'])) && (!empty($this->_config['paypalApiSignature'])) && (!empty($this->_config['paypalSubject']))) {
				$authMode = "THIRDPARTY";
			} else if ((!empty($this->_config['paypalApiUsername'])) && (!empty($this->_config['paypalApiPassword'])) && (!empty($this->_config['paypalApiSignature']))) {
				$authMode = "3TOKEN";
			} elseif ((!empty($this->_config['paypalAuthToken'])) && (!empty($this->_config['paypalAuthSignature'])) && (!empty($this->_config['paypalAuthTimestamp']))) {
				$authMode = "PERMISSION";
			} elseif ((!empty($subject))) {
				$authMode = "FIRSTPARTY";
			}
		}
		switch($authMode) {
			case "3TOKEN":
				$nvpHeaderStr = '&PWD=' . urlencode($this->_config['paypalApiPassword']);
				$nvpHeaderStr .= '&USER=' . urlencode($this->_config['paypalApiUsername']);
				$nvpHeaderStr .= '&SIGNATURE=' . urlencode($this->_config['paypalApiSignature']);
				break;
			case "FIRSTPARTY":
				$nvpHeaderStr = '&SUBJECT=' . urlencode($this->_config['paypalSubject']);
				break;
			case "THIRDPARTY":
				$nvpHeaderStr = '&PWD=' . urlencode($this->_config['paypalApiPassword']);
				$nvpHeaderStr .= '&USER=' . urlencode($this->_config['paypalApiUsername']);
				$nvpHeaderStr .= '&SIGNATURE=' . urlencode($this->_config['paypalApiSignature']);
				$nvpHeaderStr .= '&SUBJECT=' . urlencode($this->_config['paypalSubject']);
				break;
			case "PERMISSION":
				$nvpHeaderStr = $this->_formAutorization($this->_config['paypalAuthToken'], $this->_config['paypalAuthSignature'], $this->_config['paypalAuthTimestamp']);
				break;
		}
		return $nvpHeaderStr;
	}

	/**
	 * hash_call: Function to perform the API call to PayPal using API signature
	 * @methodName is name of API  method.
	 * @nvpStr is nvp string.
	 * returns an associtive array containing the response from the server.
	 */
	public function hashCall($methodName, $nvpStr) {
		// form header string
		$nvpheader = $this->_nvpHeader();
		//setting the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_config['paypalApiEndpoint']);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		//turning off the server and peer verification(TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);

		//in case of permission APIs send headers as HTTPheders
		if (!empty($this->_config['paypalAuthToken']) && !empty($this->_config['paypalAuthSignature']) && !empty($this->_config['paypalAuthTimestamp'])) {
			$headersArray[] = "X-PP-AUTHORIZATION: " . $nvpheader;

			curl_setopt($ch, CURLOPT_HTTPHEADER, $headersArray);
			curl_setopt($ch, CURLOPT_HEADER, false);
		} else {
			$nvpStr = $nvpheader . $nvpStr;
		}
		//if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
		//Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php
		if ($this->_config['paypalUseProxy'])
			curl_setopt($ch, CURLOPT_PROXY, $this->_config['paypalProxyHost'] . ":" . $this->_config['paypalProxyPort']);

		//check if version is included in $nvpStr else include the version.
		if (strlen(str_replace('VERSION=', '', strtoupper($nvpStr))) == strlen($nvpStr)) {
			$nvpStr = '&VERSION=' . urlencode($this->_config['paypalVersion']) . $nvpStr;
		}

		$nvpreq = "METHOD=" . urlencode($methodName) . $nvpStr;

		//setting the nvpreq as POST FIELD to curl
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

		//getting response from server
		$response = curl_exec($ch);

		//converting NVPResponse to an Associative Array
		$nvpResArray = $this->_deformatNVP($response);
		$nvpReqArray = $this->_deformatNVP($nvpreq);
		//$_SESSION['nvpReqArray'] = $nvpReqArray;

		if (curl_errno($ch)) {
			// moving to display page to display curl errors
			$nvpResArray['curl_error_no'] = curl_errno($ch);
			$nvpResArray['curl_error_msg'] = curl_error($ch);
		} else {
			//closing the curl
			curl_close($ch);
		}

		return $nvpResArray;
	}

	/** This function will take NVPString and convert it to an Associative Array and it will decode the
	 * response.
	 * It is usefull to search for a particular key and displaying arrays.
	 * @nvpstr is NVPString.
	 * @nvpArray is Associative Array.
	 */
	private function _deformatNVP($nvpstr) {

		$intial = 0;
		$nvpArray = array();

		while (strlen($nvpstr)) {
			//postion of Key
			$keypos = strpos($nvpstr, '=');
			//position of value
			$valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);

			/*getting the Key and Value values and storing in a Associative Array*/
			$keyval = substr($nvpstr, $intial, $keypos);
			$valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
			//decoding the respose
			$nvpArray[urldecode($keyval)] = urldecode($valval);
			$nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
		}
		return $nvpArray;
	}

	private function _formAutorization($auth_token, $auth_signature, $auth_timestamp) {
		$authString = "token=" . $auth_token . ",signature=" . $auth_signature . ",timestamp=" . $auth_timestamp;
		return $authString;
	}

}
?>
