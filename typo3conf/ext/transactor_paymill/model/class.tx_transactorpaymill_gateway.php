<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Franz Holzinger (franz@ttproducts.de)
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
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
 * This script handles payment via the Paymill gateway.
 *
 *
 * Paymill:	http://www.paymill.com
 *
 * $Id$
 *
 * @author	Franz Holzinger <franz@ttproducts.de>
 * @package TYPO3
 * @subpackage transactor_paymill
 *
 *
 */



class tx_transactorpaymill_gateway extends tx_transactor_gateway {
	protected $gatewayKey = 'transactor_paymill';
	protected $extKey = 'transactor_paymill';
	protected $supportedGatewayArray = array(TX_TRANSACTOR_GATEWAYMODE_AJAX);
	protected $sendBasket = FALSE;	// Submit detailled basket informations like single products
	protected $bTaxIncluded = TRUE;


		// Setup array for modifying the inputs
	public function __construct () {

		$result = parent::__construct();

		return $result;
	}


	/**
	 * Displays the form on which the user will finally submit the transaction to the payment gateway
	 *
	 *
	 * @return	string		HTML form and javascript
	 * @access	public
	 */
	public function transaction_getForm ($lConf) {

		$cObj = t3lib_div::getUserObj('&tx_div2007_cobj');
		$conf = $this->getConf();

		$detailsArray = $this->getDetails();

		$templateFilename = ($lConf['templateFile'] ? $lConf['templateFile'] : 'EXT:transactor_paymill/template/paymill_payment_form.html');

		$localTemplateCode = $cObj->fileResource($templateFilename);

		$markerArray = array();
		$markerArray['###YOUR_PAYMILL_PUBLIC_KEY###'] = $conf['publickey'];
		$markerArray['###YOUR_PAYMILL_PRIVATE_KEY###'] = $conf['privatekey'];
		$markerArray['###PAYMILL_BRIDGE###'] = $conf['bridgeuri'];
		$markerArray['###JQUERY_LIBRARY###'] = $conf['jqueryuri'];
		$markerArray['###CARD_AMOUNT###'] = intval($detailsArray['transaction']['amount'] * 100);
		$markerArray['###CARD_CURRENCY###'] = $detailsArray['transaction']['currency'];

		if ($conf['testMode']) {
			$markerArray['###CARD_NUMBER###'] = '4111111111111111';
			$markerArray['###CARD_CVC###'] = '111';
			$markerArray['###CARD_HOLDERNAME###'] = 'Max Mustermann';
			$markerArray['###CARD_EXPIRY_MONTH###'] = '12';
			$markerArray['###CARD_EXPIRY_YEAR###'] = '2015';
			$markerArray['###ELV_HOLDERNAME###'] = 'Max Mustermann';
			$markerArray['###ELV_ACCOUNT###'] = '1234567890';
			$markerArray['###ELV_BANKCODE###'] = '40050150';
		} else {
			$markerArray['###CARD_NUMBER###'] = '';
			$markerArray['###CARD_CVC###'] = '';
			$markerArray['###CARD_HOLDERNAME###'] = '';
			$markerArray['###CARD_EXPIRY_MONTH###'] = '';
			$markerArray['###CARD_EXPIRY_YEAR###'] = '';
			$markerArray['###ELV_HOLDERNAME###'] = '';
			$markerArray['###ELV_ACCOUNT###'] = '';
			$markerArray['###ELV_BANKCODE###'] = '';
		}

		tx_transactor_api::getMarkers (
			$cObj,
			$conf,
			$markerArray
		);

		$pid = $GLOBALS['TSFE']->id;

		$url = tx_div2007_alpha5::getTypoLink_URL_fh003(
			$cObj,
			$pid,
			array('eID' => 'paymill')
		);
		$markerArray['###FORM_URL###'] = $url;

		$transactionArray = $detailsArray['transaction'];
		$prefixId = tx_transactorpaymill_control::getPiVar();
		$hiddenFields = '<input name="'. $prefixId . '[' . tx_transactor_model_control::getCallingExtensionVar() . ']" type="hidden" value="' . $detailsArray['calling_extension'] . '"/>
';
		$hiddenFields .= '<input name="'. $prefixId . '[' . tx_transactor_model_control::getOrderVar() . ']" type="hidden" value="' . $transactionArray['orderuid'] . '"/>
';
		$hiddenFields .= '<input name="'. $prefixId . '[' . tx_transactor_model_control::getReturiVar() . ']" type="hidden" value="' . $transactionArray['returi'] . '"/>
';
		$hiddenFields .= '<input name="'. $prefixId . '[' . tx_transactor_model_control::getFaillinkVar() . ']" type="hidden" value="' . $transactionArray['faillink'] . '"/>
';
		$hiddenFields .= '<input name="'. $prefixId . '[' . tx_transactor_model_control::getSuccesslinkVar() . ']" type="hidden" value="' . $transactionArray['successlink'] . '"/>
';

		$markerArray['###HIDDENFIELDS###'] = $hiddenFields;
		$result = strtr($localTemplateCode, $markerArray);

		return $result;
	}


	/**
	 * Returns the form action URI to be used in mode TX_TRANSACTOR_GATEWAYMODE_FORM.
	 *
	 * @return	string		Form action URI
	 * @access	public
	 */
	public function transaction_formGetActionURI () {
		$result = FALSE;
		$formActionURI = FALSE;

		if ($this->getGatewayMode() == TX_TRANSACTOR_GATEWAYMODE_FORM) {
			$conf = $this->getConf();
			$result = $conf['provideruri'];
		}

		return $result;
	}


	/**
	 * Returns the results of a processed transaction
	 *
	 * @param	string		$reference
	 * @return	array		Results of a processed transaction
	 * @access	public
	 */
	public function transaction_getResults ($reference) {

		$dbRow = $this->getTransaction($reference);
		$result = $dbRow;
		return $result;
	}
}


?>