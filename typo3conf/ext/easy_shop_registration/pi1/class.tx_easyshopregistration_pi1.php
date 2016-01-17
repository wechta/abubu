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
 * Plugin 'User registration' for the 'easy_shop_registration' extension.
 *
 * @author	Mitja Venturini <>
 * @package	TYPO3
 * @subpackage	tx_easyshopregistration
 */
class tx_easyshopregistration_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_easyshopregistration_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_easyshopregistration_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'easy_shop_registration';	// The extension key.
	
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
		$this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
	t3lib_utility_Debug::debug($this->conf);
		if($this->piVars['fe_user']&&$this->piVars['ident']){
			return $this->pi_wrapInBaseClass($this->feUserConfirmation(array()));			
		}elseif($this->piVars['show']=='forgot_password'){
			return $this->pi_wrapInBaseClass($this->forgotPassword(array()));
		}
		else{
			return $this->pi_wrapInBaseClass($this->registrationForm(array()));	
		}
	
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
	
	function registrationForm($arg){
		if($GLOBALS["TSFE"]->fe_user->user["uid"]){
			return '';
		}
		
		$first_name=t3lib_div::_POST('first_name');
		$last_name=t3lib_div::_POST('last_name');
		
		$password=t3lib_div::_POST('password');
		$password_repeat=t3lib_div::_POST('password_repeat');
		
		$title=t3lib_div::_POST('title');
		
		$address=t3lib_div::_POST('address');
		$ddv=t3lib_div::_POST('ddv');
		$zavezanec_DDV=t3lib_div::_POST('zavezanec_DDV');
		$zip=t3lib_div::_POST('zip');
		$city=t3lib_div::_POST('city');
		$country=t3lib_div::_POST('country');
		$tel=t3lib_div::_POST('tel');
		$email=(t3lib_div::validEmail(t3lib_div::_POST('email')))?t3lib_div::_POST('email'):'';
		$agreement=(t3lib_div::_POST('agreement')=='on')?1:0;
		$template = $this->loadTemplate('###REGISTRATION_FORM###');
		$singleMark=$multiMark=array();
		
		$singleMark['###THIS_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array(), 1, 1);
		
		//Set default or posted values
		$singleMark['###FIRST_NAME###']=$first_name;
		
		
		$singleMark['###LAST_NAME###']=$last_name;
		$singleMark['###TITLE###']=$title;
		
		$singleMark['###PASSWORD###']=$password;
		$singleMark['###PASSWORD_REPEAT###']=$password_repeat;
	
		//$singleMark['###USERGROUP###']=$this->conf['fe_group'];
		$singleMark['###REDIRECTURL###']=$_GET["redirecturl"];

		
		$singleMark['###ADDRESS###']=$address;
		$singleMark['###DDV###']=$ddv;
		if($zavezanec_DDV){
			$singleMark['###CHECKED_DDV_DA###']='checked';
			$singleMark['###CHECKED_DDV_NE###']='';
		}
		else{
			$singleMark['###CHECKED_DDV_DA###']='';
			$singleMark['###CHECKED_DDV_NE###']='checked';
		}
		
		$singleMark['###CITY###']=$city;
		$singleMark['###ZIP###']=$zip;

		$singleMark['###TEL###']=$tel;
		
		$singleMark['###EMAIL###']=$email;
		$singleMark['###EMAIL_REPEAT###']=$email_repeat;
		if($agreement==1){
			$singleMark['###AGREEMENT###']='checked="yes"';
		}
		//Form was submitted
		if(t3lib_div::_POST('register_x')){
			//t3lib_div::debug($error);
			
			$error=false;
			if($first_name){$multiMark['###FIRST_NAME_ERROR###']='';}
			else{$error=true;}
			
			if($last_name){$multiMark['###LAST_NAME_ERROR###']='';}
			else{$error=true;}
			
			
			if($password){$multiMark['###PASSWORD_ERROR###']='';}
				else{$error=true;}
				
			if($password_repeat){$multiMark['###PASSWORD_REPEAT_ERROR###']='';}
			else{$error=true;}
			
			
			
			if($password&&$password_repeat&&$password==$password_repeat){
				$multiMark['###PASSWORDS_NOTSAME_ERROR###']='';
			}
			else{
				$error=true;
			}
			
			if($email){$multiMark['###EMAIL_ERROR###']='';}
			else{$error=true;}
			
			//if(!$usergroup){return $this->pi_getLL('no_user_group');}
			$usergroup=1;
			//t3lib_div::debug($error);
			
			if(!$error){
				if(!$this->checkUserExists($email)){
					$fe_user=$this->insertFEUser($usergroup, $first_name, $last_name, $password, $title, $address, $delivery_address, $zip, $city, $country, $tel, $email, $agreement, $ddv, $zavezanec_DDV, $www, $tel1, $tel2, $gsm1, $gsm2);
					if($fe_user['uid']){
						$registrationMark=array();
						$registrationMark['###CONFIRMATION_URL###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('fe_user'=>md5($fe_user['uid']),'ident'=>md5($fe_user['crdate'])), 1, 1);
						$registrationMark['###SITE_URL###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL');
						$registrationMark['###USERNAME###']=$fe_user['username'];
						$registrationMark['###PASSWORD###']=$fe_user['password'];
						$registrationMark['###FIRST_NAME###']=$first_name;
						$registrationMark['###LAST_NAME###']=$last_name;
						$registrationMark['###TITLE###']=$title;
						$registrationMark['###ADDRESS###']=$address;
						$registrationMark['###DELIVERY_ADDRESS###']=$delivery_address;
						$registrationMark['###ZIP###']=$zip;
						$registrationMark['###CITY###']=$city;
						$registrationMark['###COUNTRY###']=$country;
						$registrationMark['###DDV###']=$ddv;
						$registrationMark['###GSM2###']=$gsm2;
						$registrationMark['###TEL2###']=$tel2;
						
						
						$loginData=array('uname' => $email,'uident'=> $password,'status' =>'login');
						$GLOBALS['TSFE']->fe_user->checkPid=0; //do not use a particular pid
						$info= $GLOBALS['TSFE']->fe_user->getAuthInfoArray();
						$user=$GLOBALS['TSFE']->fe_user->fetchUserRecord($info['db_user'],$loginData['uname']);
						$ok=$GLOBALS['TSFE']->fe_user->compareUident($user,$loginData);
						if($ok) {
						  //login successfull
						  $GLOBALS['TSFE']->fe_user->createUserSession($user);
						} else {
						  echo 'login failed';
						}
						
					}else{
						return $this->loadTemplate('###REGISTRATION_CREATION_ERROR###');
					}	
				}else{
					return $this->cObj->substituteMarkerArrayCached($this->loadTemplate('###USER_EXISTS_ERROR###'),array('###FORGOT_PASSWORD_URL###'=>t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('show'=>'forgot_password'), 1, 1)),array(),array());
				}
			}
		}else{
			$multiMark['###FIRST_NAME_ERROR###']='';
			$multiMark['###LAST_NAME_ERROR###']='';
			$multiMark['###PASSWORD_ERROR###']='';
			$multiMark['###PASSWORD_REPEAT_ERROR###']='';
			$multiMark['###TITLE_ERROR###']='';
			$multiMark['###ADDRESS_ERROR###']='';
			$multiMark['###CITY_ERROR###']='';
			$multiMark['###COUNTRY_ERROR###']='';
			$multiMark['###EMAIL_ERROR###']='';
			$multiMark['###EMAIL_REPEAT_ERROR###']='';
			$multiMark['###DDV_ERROR###']='';
			$multiMark['###PASSWORDS_NOTSAME_ERROR###']='';
			$multiMark['###TELEFON_ERROR###']='';
			$multiMark['###GSM_ERROR###']='';
			$multiMark['###AGREEMENT_ERROR###']='';
			$multiMark['###TITLE_ERROR###']='';
		}
		
		//exit();
		
		return $this->cObj->substituteMarkerArrayCached($template,$singleMark,$multiMark,array());
	}
	
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/easy_shop_registration/pi1/class.tx_easyshopregistration_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/easy_shop_registration/pi1/class.tx_easyshopregistration_pi1.php']);
}

?>