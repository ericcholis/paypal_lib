<?php

/**
 * PayPal plugin library for Li3
 *
 * @name        PAYPAL.PHP
 * @author		Gautam Sathe <gautam@hemisphereinteractive.com>
 * @package     paypal_lib
 * @copyright   Copyright (c) 2012, Gautam Sathe
 * @license		http://opensource.org/licenses/bsd-license.php The BSD License
 */
/**
 *
 * This is the configuration file for the samples.This file
 * defines the parameters needed to make an API call.
 * PayPal includes the following API Signature for making API
 * calls to the PayPal sandbox:
 *
 * API Username 	sdk-three_api1.sdk.com
 * API Password 	QFZCWN5HZM8VBG7Q
 * API Signature 	A.d9eRKfd1yVkRrtmMfCFLTqa6M9AyodL0SJkhYztxUi8W9pCXF6.4NI
 *
 */
/**
 * API user: The user that is identified as making the call. you can
 * also use your own API username that you created on PayPal’s sandbox
 * or the PayPal live site
 *
 * for 3-token -> API_USERNAME,API_PASSWORD,API_SIGNATURE  are needed
 */
define('PAYPAL_API_USERNAME', 'platfo_1255077030_biz_api1.gmail.com');

/**
 * API_password: The password associated with the API user
 * If you are using your own API username, enter the API password that
 * was generated by PayPal below
 * IMPORTANT - HAVING YOUR API PASSWORD INCLUDED IN THE MANNER IS NOT
 * SECURE, AND ITS ONLY BEING SHOWN THIS WAY FOR TESTING PURPOSES
 */
define('PAYPAL_API_PASSWORD', '1255077037');

/**
 * API_Signature:The Signature associated with the API user. which is generated by paypal.
 */
define('PAYPAL_API_SIGNATURE', 'Abg0gYcQyxQvnf2HDJkKtA-p6pqhA1k-KTYE0Gcy1diujFio4io5Vqjf');

/**
 * Endpoint: this is the server URL which you have to connect for submitting your API request.
 */
define('PAYPAL_API_ENDPOINT', 'https://api-3t.sandbox.paypal.com/nvp');

/**
 * Third party Email address that you granted permission to make api call.
 */
define('PAYPAL_SUBJECT', '');

/**
 * If you want to use permission APIs ->token, signature, timestamp  are needed
 * Please uncomment the the 3 line below
 */
// define('AUTH_TOKEN',"4oSymRbHLgXZVIvtZuQziRVVxcxaiRpOeOEmQw");
// define('AUTH_SIGNATURE',"+q1PggENX0u+6vj+49tLiw9CLpA=");
// define('AUTH_TIMESTAMP',"1284959128");
// below three are needed if used permissioning
define('PAYPAL_AUTH_TOKEN', '');
define('PAYPAL_AUTH_SIGNATURE', '');
define('PAYPAL_AUTH_TIMESTAMP', '');

/**
 * PAYPAL_USE_PROXY: Set this variable to TRUE to route all the API requests through proxy.
 * like define('PAYAPL_USE_PROXY',TRUE);
 */
define('PAYPAL_USE_PROXY', FALSE);

/**
 * PAYPAL_PROXY_HOST: Set the host name or the IP address of proxy server.
 * PAYPAL_PROXY_PORT: Set proxy port.
 *
 * PAYPAL_PROXY_HOST and PROXY_PORT will be read only if PAYPAL_USE_PROXY is set to TRUE
 */
define('PAYPAL_PROXY_HOST', '127.0.0.1');
define('PAYPAL_PROXY_PORT', '808');

/**
 * Define the PayPal URL. This is the URL that the buyer is
 * first sent to to authorize payment with their paypal account
 * change the URL depending if you are testing on the sandbox
 * or going to the live PayPal site
 *
 * For the sandbox, the URL is
 * https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=
 * For the live site, the URL is
 * https://www.paypal.com/webscr&cmd=_express-checkout&token=
 */
define('PAYPAL_URL', 'https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=');

/**
 * Version: this is the API version in the request.
 * It is a mandatory parameter for each API request.
 * The only supported value at this time is 2.3
 */
define('PAYPAL_VERSION', '65.1');

/**
 * Auth Mode: this defines the auth mode used by API call
 * 3TOKEN - Merchant's API 3-TOKEN Credential is required to make API Call.
 * FIRSTPARTY - Only merchant Email is required to make EC Calls.
 * THIRDPARTY - Partner's API Credential and Merchant Email as Subject are required.
 * PERMISSION
 */
define('PAYPAL_AUTH_MODE', '');

/**
 * Ack related constants
 */
define('PAYPAL_ACK_SUCCESS', 'SUCCESS');
define('PAYPAL_ACK_SUCCESS_WITH_WARNING', 'SUCCESSWITHWARNING');
?>