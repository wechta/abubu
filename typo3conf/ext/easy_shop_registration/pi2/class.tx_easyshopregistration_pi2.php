<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Mitja Venturini <>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'User infopush' for the 'easy_shop_registration' extension.
 *
 * @author	Mitja Venturini <>
 * @package	TYPO3
 * @subpackage	tx_easyshopregistration
 */
class tx_easyshopregistration_pi2 extends tslib_pibase {
	var $prefixId      = 'tx_easyshopregistration_pi2';		// Same as class name
	var $scriptRelPath = 'pi2/class.tx_easyshopregistration_pi2.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'easy_shop_registration';	// The extension key.
	var $pi_checkCHash = true;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->init($conf);
	
		$content=$this->showHeaderInfoPush();
	
		return $this->pi_wrapInBaseClass($content);
	}
	
	function init($conf){
	  $this->pi_initPIflexForm(); // Init and get the flexform data of the plugin
	  $this->conf = $conf; // Setup our storage array...
	  // Assign the flexform data to a local variable for easier access
	  $piFlexForm = $this->cObj->data['pi_flexform'];
	  // Traverse the entire array based on the language...
	  // and assign each configuration option to $this->conf array...
	  if(is_array($piFlexForm['data'])){
	  	foreach ( $piFlexForm['data'] as $sheet => $data )
	    	foreach ( $data as $lang => $value )
	      		foreach ( $value as $key => $val )
	       			$this->conf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);	
	  }
	}
	
	function showHeaderInfoPush() {
		//$GLOBALS['TSFE']->fe_user->logoff();
		if($GLOBALS['TSFE']->loginUser) {
			$template = $this->loadTemplate('###LOGGED_IN_INFO###');
		} else {
			$template = $this->loadTemplate('###LOGGED_OUT_INFO###');
		}
		
		//t3lib_utility_Debug::debug($GLOBALS['TSFE']->loginUser);
		$singleMark['###USER###'] = $GLOBALS['TSFE']->fe_user->user['first_name'].' '.$GLOBALS['TSFE']->fe_user->user['last_name'];
		$singleMark['###LOGIN_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array(), 1, 1,$this->conf['login_page']);
		$singleMark['###REGISTER_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array(), 1, 1,$this->conf['register_page']);
		
		return $this->cObj->substituteMarkerArrayCached($template,$singleMark,$multiMark,array());
	}
	
	function loadTemplate($marker){
		if($this->conf['templateFile']){
			return $this->cObj->getSubpart($this->cObj->fileResource($this->conf['templateFile']), $marker);
		}		
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/easy_shop_registration/pi2/class.tx_easyshopregistration_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/easy_shop_registration/pi2/class.tx_easyshopregistration_pi2.php']);
}

?>