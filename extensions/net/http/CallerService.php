<?php

/**
 * PayPal plugin library for Li3
 *
 * @name        CallerService.php
 * @author		Gautam Sathe <gautam@hemisphereinteractive.com>
 * @package     paypal_lib
 * @copyright   Copyright (c) 2012, Gautam Sathe
 * @license		http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace paypal_lib\extensions\net\http;

/**
 * This file uses the configuration file (paypal.php) to get parameters needed
 * to make an API call and calls the server.if you want use your
 * own credentials, add your own version of paypal.php in app/config/bootstrap
 *
 * @see paypal_lib\config\bootstrap\paypal.php
 */
class CallerService {

	protected $_config = array();

	/**
	 * Initializes class configuration (`$_config`)
	 *
	 * @access public
	 * @param array $config The configuration options which will be assigned to the `$_config`
	 *              property.
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
		switch ($authMode) {
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
	 * This method performs the API call to PayPal using API signature
	 * 
	 * @access public
	 * @param string $methodName name of API  method
	 * @param string $nvpStr nvp string
	 * @return array $nvpResArray associative array containing the response from the server
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

	/**
	 * This method will take NVPString and convert it to an Associative Array and it will decode the
	 * response. It is useful to search for a particular key and displaying arrays.
	 * 
	 * @access private
	 * @param string $nvpstr NVPString
	 * @return array $nvpArray Associative Array
	 */
	private function _deformatNVP($nvpstr) {

		$intial = 0;
		$nvpArray = array();

		while (strlen($nvpstr)) {
			//position of Key
			$keypos = strpos($nvpstr, '=');
			//position of value
			$valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);

			/* getting the Key and Value values and storing in a Associative Array */
			$keyval = substr($nvpstr, $intial, $keypos);
			$valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
			//decoding the response
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
