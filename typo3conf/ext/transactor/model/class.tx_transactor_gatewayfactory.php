<?php
/***************************************************************
*
*  Copyright notice
*
*  (c) 2011 Franz Holzinger (franz@ttproducts.de)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 *
 * $Id: class.tx_transactor_gatewayfactory.php 73382 2013-03-21 12:53:46Z franzholz $
 *
 *
 * @package 	TYPO3
 * @subpackage	tx_transactor
 * @author	Franz Holzinger <franz@ttproducts.de>
 * @author	Robert Lemke <robert@typo3.org>
 */

// require_once(t3lib_extMgm::extPath('transactor') . 'model/class.tx_transactor_gatewayproxy.php');


final class tx_transactor_gatewayfactory {

	private static $instance = FALSE;					// Holds an instance of this class
	private static $gatewayProxyObjects = array();		// An array of proxy objects, each pointing to a registered gateway object
	private static $errorMessage = '';
	private static $errorStack;


	/**
	 * This constructor is private because you may only instantiate this class by calling
	 * the function getInstance() which returns a unique instance of this class (Singleton).
	 *
	 * @return		void
	 * @access		private
	 */
	private function __construct () {
		// do nothing
	}


	/**
	 * Returns a unique instance of this class. Call this function instead of creating a new
	 * instance manually!
	 *
	 * @return		object		Unique instance of tx_transactor_factory
	 * @access		public
	 */
	public static function getInstance () {
		if (self::$instance === FALSE) {
			self::$instance = new tx_transactor_gatewayfactory;
		}
		return self::$instance;
	}


	/**
	 * Registers the given extension as a payment gateway (concrete product). This method will
	 * be called by the gateway implementation itself.
	 *
	 * @param		string		$extKey: Extension key of the payment implementation.
	 * @return		mixed		Proxied instance of the given extension or FALSE if an error occurred.
	 * @access		public
	 */
	public static function registerGatewayExt ($extKey) {

		if (t3lib_extMgm::isLoaded($extKey)) {
			$gatewayProxy = t3lib_div::getUserObj('tx_transactor_gatewayproxy');
			$gatewayProxy->init($extKey);
			self::$gatewayProxyObjects[$extKey] = $gatewayProxy;
			$result = self::$gatewayProxyObjects[$extKey];
		} else {
			$result = FALSE;
		}
		return $result;
	}


	/**
	 * Returns an array of instantiated payment implementations wrapped by a proxy
	 * object. We use this proxy as a smart reference: All function calls and access
	 * to variables are redirected to the real gateway object but in some cases
	 * some additional operation is done.
	 *
	 * @return		array		Array of payment implementations (objects)
	 * @access		public
	 */
	public static function getGatewayProxyObjects () {
		return self::$gatewayProxyObjects;
	}


	/**
	 * Returns instance of the payment implementations (wrapped by a proxy
	 * object) which offers the specified payment method.
	 *
	 * @param		string		$paymentMethod: Payment method key
	 * @return		mixed		Reference to payment proxy object or FALSE if no matching object was found
	 * @access		public
	 */
	public static function getGatewayProxyObjectByPaymentMethod ($paymentMethod) {
		$result = FALSE;

		if (is_array (self::$gatewayProxyObjects)) {
			foreach (self::$gatewayProxyObjects as $extKey => $gatewayProxyObject) {
				$paymentMethodsArray = $gatewayProxyObject->getAvailablePaymentMethods();

				if (
					is_array($paymentMethodsArray) &&
					array_key_exists($paymentMethod, $paymentMethodsArray)
				) {
					$result = $gatewayProxyObject;
					break;
				} else {
					if ($paymentMethodsArray != FALSE) {
						self::addError(
							'tx_transactor_gatewayfactory::getGatewayObjectByPaymentMethod ' . $paymentMethodsArray
						);
					}
				}
			}
		}
		return $result;
	}


	/**
	 * Returns an array of transaction records which match the given extension key
	 * and optionally the given extension reference string and or booking status.
	 * Use this function instead accessing the transaction records directly.
	 *
	 * @param		string		$ext_key: Extension key
	 * @param		int			$gatewayid: (optional) Filter by gateway id
	 * @param		string		$reference: (optional) Filter by reference
	 * @param		string		$state: (optional) Filter by transaction state
	 * @return		array		Array of transaction records, FALSE if no records where found or an error occurred.
	 * @access		public
	 */
	public static function getTransactionsByExtKey (
		$ext_key,
		$gatewayid = NULL,
		$reference = NULL,
		$state = NULL
	) {
		global $TYPO3_DB;

		$transactionsArray = FALSE;

		$additionalWhere = '';
		$additionalWhere .= (isset ($gatewayid)) ? ' AND gatewayid="'.$gatewayid.'"' : '';
		$additionalWhere .= (isset ($invoiceid)) ? ' AND reference="'.$reference.'"' : '';
		$additionalWhere .= (isset ($state)) ? ' AND state="'.$state.'"' : '';

		$res = $TYPO3_DB->exec_SELECTquery (
			'*',
			'tx_transactor_transactions',
			'ext_key="' . $ext_key . '"' . $additionalWhere,
			'',
			'crdate DESC'
		);

		if ($res && $TYPO3_DB->sql_num_rows($res)) {
			$transactionsArray = array();
			while ($row = $TYPO3_DB->sql_fetch_assoc ($res)) {
				$row['user'] = self::field2array($row['user']);
				$transactionsArray[$row['uid']] = $row;
			}
		}
		return $transactionsArray;
	}


	/**
	 * Returns a single transaction record which matches the given uid
	 *
	 * @param		integer		$uid: UID of the transaction
	 * @access		public
	 */
	public static function getTransactionByUid ($uid) {
		global $TYPO3_DB;

		$res = $TYPO3_DB->exec_SELECTquery (
			'*',
			'tx_transactor_transactions',
			'uid=' . $uid
		);

		if (!$res || !$TYPO3_DB->sql_num_rows($res)) {
			return FALSE;
		}

		$row = $TYPO3_DB->sql_fetch_assoc($res);
		$row['user'] = self::field2array($row['user']);

		return $row;
	}


	/**
	 * Return an array with either a single value or an unserialized array
	 *
	 * @param		mixed		$field: some value from a database field
	 * @return 	array
	 * @access		private
	 */
	private static function field2array ($field) {
		if (!$field = @unserialize ($field)) {
		    $field = array($field);
		}
		return $field;
	}


	public static function clearErrors () {
		self::$errorStack = array();
	}


	public static function addError ($error) {
		self::$errorStack[] = $error;
	}


	public static function hasErrors () {
		$result = (count(self::$errorStack) > 0);
	}


	public static function getErrors () {
		return self::$errorStack;
	}
}

?>