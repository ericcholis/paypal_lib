<?php

/**
 * PayPal plugin library for Li3
 *
 * @name        CALLERSERVICE.PHP
 * @author		Gautam Sathe <gautam@hemisphereinteractive.com>
 * @package     paypal_lib
 * @copyright   Copyright (c) 2012, Gautam Sathe
 * @license		http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace paypal_lib\extensions\net\http;

class RPProfileService {

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
	 * This method takes the paypal create recurring payment profile request
	 * and return the response using CallerService class
	 *
	 * @access public
	 * @param array $postData
	 * @return array $resArray
	 */
	public function createRPProfile($postData) {
		// Get required parameters from the request post data
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

		$profileDesc = urlencode($postData['profileDesc']);
		$billingPeriod = urlencode($postData['billingPeriod']);
		$billingFrequency = urlencode($postData['billingFrequency']);
		$totalBillingCycles = urlencode($postData['totalBillingCycles']);

		$profileStartDateDay = $postData['profileStartDateDay'];
		// Day must be padded with leading zero
		$padprofileStartDateDay = str_pad($profileStartDateDay, 2, '0', STR_PAD_LEFT);
		$profileStartDateMonth = $postData['profileStartDateMonth'];
		// Month must be padded with leading zero
		$padprofileStartDateMonth = str_pad($profileStartDateMonth, 2, '0',
				STR_PAD_LEFT);
		$profileStartDateYear = $postData['profileStartDateYear'];

		$profileStartDate = urlencode($profileStartDateYear . '-' . $padprofileStartDateMonth . '-' . $padprofileStartDateDay . 'T00:00:00Z');

		// Construct the request string that will be sent to PayPal.
		// The variable $nvpstr contains all the variables and is a
		// name value pair string with & as a delimiter
		$nvpstr = '&AMT=' . $amount;
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
		$nvpstr .= '&PROFILESTARTDATE=' . $profileStartDate;
		$nvpstr .= '&DESC=' . $profileDesc;
		$nvpstr .= '&BILLINGPERIOD=' . $billingPeriod;
		$nvpstr .= '&BILLINGFREQUENCY=' . $billingFrequency;
		$nvpstr .= '&TOTALBILLINGCYCLES=' . $totalBillingCycles;

		// Make the API call to PayPal, using API signature.
		// The API response is stored in an associative array called $resArray
		$resArray = $this->_callService->hashCall('CreateRecurringPaymentsProfile',
				$nvpstr);

		return $resArray;
	}

	/**
	 * This method sends a GetRecurringPaymentsProfileDetails NVP API
	 * request to PayPal and return the response using CallerService class
	 *
	 * @access public
	 * @param array $postData
	 * @return array $resArray
	 */
	public function getRPProfileDetails($postData) {
		$profileID = urlencode($postData['profileID']);

		// Construct the request string that will be sent to PayPal.
		// The variable $nvpstr contains all the variables and is a
		// name value pair string with & as a delimiter
		$nvpStr = "&PROFILEID=$profileID";

		// Make the API call to PayPal, using API signature.
		// The API response is stored in an associative array called $resArray
		$resArray = $this->_callService->hashCall('GetRecurringPaymentsProfileDetails',
				$nvpStr);

		return $resArray;
	}

	/**
	 * This method sends a ManageRecurringPaymentsProfileStatus NVP API request
	 * to PayPal and return the response using CallerService class
	 *
	 * The action to be performed to the recurring payments profile.
	 * Must be one of the following:
	 * - Cancel – Only profiles in Active or Suspended state can be canceled.
	 * - Suspend – Only profiles in Active state can be suspended.
	 * - Reactivate – Only profiles in a suspended state can be reactivated.
	 *
	 * @access public
	 * @param array $postData
	 * @return array $resArray
	 */
	public function manageRPProfileStatus($postData) {
		$profileID = urlencode($postData['profileID']);
		$action = urlencode($postData['action']);

		// Construct the request string that will be sent to PayPal.
		// The variable $nvpstr contains all the variables and is a
		// name value pair string with & as a delimiter
		$nvpStr = "&PROFILEID=$profileID&ACTION=$action";

		// Make the API call to PayPal, using API signature.
		// The API response is stored in an associative array called $resArray
		$resArray = hash_call("ManageRecurringPaymentsProfileStatus", $nvpStr);

		return $resArray;
	}

}
?>
