<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Peter Wechtersbach <>
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
//if (!class_exists('PHP_Mailer')) {require_once('ext_lib/class.phpmailer.php');}

/**
 * Plugin 'User registration' for the 'web_shop_registration' extension.
 *
 * @author	Peter Wechtersbach <>
 * @package	TYPO3
 * @subpackage	tx_webshopregistration
 */
class tx_webshopregistration_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_webshopregistration_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_webshopregistration_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'web_shop_registration';	// The extension key.
	
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
		$this->init($conf);
		$this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		
		//t3lib_utility_Debug::debug($this->conf);
		
		/*		
		if($this->piVars['show']=='forgot_password'){
			return $this->pi_wrapInBaseClass($this->forgotPassword(array()));
		}*/
		if($this->conf['showForm']=='infopush'){
			return $this->showUserInfoPush();
		}
		else{
			if($this->piVars['fe_user']&&$this->piVars['ident']){
				$this->feUserConfirmation($this->piVars['ident'], $this->piVars['fe_user']);
				return $this->loadTemplate('###POTRDITEV_UPORABNIKA###');		
			}
			return $this->pi_wrapInBaseClass($this->registrationForm(array()));	
		}
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
	  if(!$this->conf['templateFile']){
			$this->conf['templateFile'] = 'typo3conf/ext/web_shop_registration/templates/registration_fe.html';
	  }
	  
	  $this->pi_loadLL();
	}
	function showUserInfoPush(){
		if($GLOBALS["TSFE"]->fe_user->user["uid"]){
			$template = $this->loadTemplate('###INFO_USER###');
			$singleMark['###URL_PROFIL###']=$this->pi_linkTP_keepPIvars_url ($overrulePIvars=array(), $cache=0, $clearAnyway=1, $this->conf['usereditpage']);
			$singleMark['###URL_PRIJAVA###']=$this->pi_linkTP_keepPIvars_url ($overrulePIvars=array(), $cache=0, $clearAnyway=1, $this->conf['login_page']);
			$singleMark['###USER_NAME###']=$GLOBALS["TSFE"]->fe_user->user["first_name"].' '.$GLOBALS["TSFE"]->fe_user->user["last_name"];
			return $this->cObj->substituteMarkerArrayCached($template,$singleMark,$multiMark,array());
		}
		else{
			$template = $this->loadTemplate('###INFO_NO_USER###');
			$url=$this->pi_linkTP_keepPIvars_url ($overrulePIvars=array(), $cache=0, $clearAnyway=1, $this->conf['loginpage']);
			$singleMark['###URL###']=$url;
			$singleMark['###REGISTER_LINK###']=$this->pi_linkTP_keepPIvars_url ($overrulePIvars=array(), $cache=0, $clearAnyway=1, $this->conf['userregisterpage']);
			return $this->cObj->substituteMarkerArrayCached($template,$singleMark,$multiMark,array());
		}
	}
	function registrationForm($arg){
		$template = $this->loadTemplate('###REGISTRATION_FORM###');
		
		
		//ce je user ze registriran prikazemo formo za spremembo uporabniksih podatkov!
		if($GLOBALS["TSFE"]->fe_user->user["uid"]){
			
			$template = $this->loadTemplate('###REGISTRATION_FORM_EDIT###');
			$first_name=$GLOBALS["TSFE"]->fe_user->user["first_name"];
			$last_name=$GLOBALS["TSFE"]->fe_user->user["last_name"];
			$naslov=$GLOBALS["TSFE"]->fe_user->user["address"];
			$posta=$GLOBALS["TSFE"]->fe_user->user["zip"];
			$kraj=$GLOBALS["TSFE"]->fe_user->user["city"];
			$ustanova=$GLOBALS["TSFE"]->fe_user->user["company"];
			$telefon=$GLOBALS["TSFE"]->fe_user->user["telephone"];
			$password=$password_repeat=$GLOBALS["TSFE"]->fe_user->user["password"];
			$email = $GLOBALS["TSFE"]->fe_user->user["email"];
			$id_ddv = $GLOBALS["TSFE"]->fe_user->user["tx_webshopregistration_id_ddv"];
			$is_ddv = $GLOBALS["TSFE"]->fe_user->user["tx_webshopregistration_is_ddv"];
			$newsletter = $GLOBALS["TSFE"]->fe_user->user["tx_webshopregistration_info_reciver"];
		}else{	
			$first_name=t3lib_div::_POST('first_name');
			$last_name=t3lib_div::_POST('last_name');
			$naslov=t3lib_div::_POST('naslov');
			$posta=t3lib_div::_POST('posta');
			$kraj=t3lib_div::_POST('kraj');			
			$ustanova=t3lib_div::_POST('ustanova');
			$telefon=t3lib_div::_POST('telefon');
			$password=t3lib_div::_POST('password');
			$password_repeat=t3lib_div::_POST('password_repeat');
			$usergroup = $this->conf['fe_group'];
			$email = t3lib_div::_POST('email');
			$id_ddv = t3lib_div::_POST('id_ddv');
			$is_ddv = (t3lib_div::_POST('is_ddv'))?1:0;
			$newsletter = (t3lib_div::_POST('newsletter'))?1:0;
		}
		
		if(t3lib_div::_POST('edit') || t3lib_div::_POST('edit_x')){
			if(!t3lib_div::_POST('first_name') || !t3lib_div::_POST('last_name') || !t3lib_div::_POST('naslov') || !t3lib_div::_POST('posta') || !t3lib_div::_POST('kraj')  || !t3lib_div::_POST('email') || !t3lib_div::_POST('password') || !t3lib_div::_POST('password_repeat')){
				return $this->loadTemplate('###EDIT_ERROR_JS###');
			}
			$first_name=t3lib_div::_POST('first_name');
			$last_name=t3lib_div::_POST('last_name');
			$naslov=t3lib_div::_POST('naslov');
			$posta=t3lib_div::_POST('posta');
			$kraj=t3lib_div::_POST('kraj');			
			$ustanova=t3lib_div::_POST('ustanova');
			$telefon=t3lib_div::_POST('telefon');
			$password=t3lib_div::_POST('password');
			$password_repeat=t3lib_div::_POST('password_repeat');
			$usergroup = $this->conf['fe_group'];
			$email = t3lib_div::_POST('email');
			$id_ddv = t3lib_div::_POST('id_ddv');
			$is_ddv = (t3lib_div::_POST('is_ddv'))?1:0;
			$newsletter = (t3lib_div::_POST('newsletter'))?1:0;
			$this->editFEUser($GLOBALS["TSFE"]->fe_user->user["uid"], $first_name, $last_name, $password, $naslov, $posta, $kraj, $ustanova, $telefon, $email, $id_ddv, $is_ddv, $newsletter);
		}
		
		//Set default or posted values
		$singleMark['###THIS_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array(), 1, 0);
		$singleMark['###FIRST_NAME###']=$first_name;
		$singleMark['###LAST_NAME###']=$last_name;
		$singleMark['###NASLOV###']=$naslov;
		$singleMark['###POSTA###']=$posta;
		$singleMark['###KRAJ###']=$kraj;
		$singleMark['###USTANOVA###']=$ustanova;
		$singleMark['###TELEFON###']=$telefon;
		$singleMark['###PASSWORD###']=$password;
		$singleMark['###PASSWORD_REPEAT###']=$password_repeat;

		$singleMark['###EMAIL###']=$email;
		$singleMark['###ID_DDV###']=$id_ddv;
		$singleMark['###IS_DDV_CHECKED###']=($is_ddv)?'checked':'';
		$singleMark['###NEWSLETTER_CHECKED###']=($newsletter)?'checked':'';
		
		//Form was submitted - register
		if(t3lib_div::_POST('register') || t3lib_div::_POST('register_x')){
			//t3lib_utility_Debug::debug(t3lib_div::_POST());
			if(!$first_name || !$last_name || !$naslov || !$posta || !$kraj || !$email || !$password || !$password_repeat || !$_POST["agree"] ){
				return $this->loadTemplate('###REGISTRACIJA_ERROR_JS###');
			}
			if($password != $password_repeat){
				return $this->loadTemplate('###REGISTRACIJA_ERROR_JS###');
			}
			if(!$this->checkUserExists($email)){
				$fe_user=$this->insertFEUser($usergroup, $first_name, $last_name, $password, $naslov, $posta, $kraj, $ustanova, $telefon, $email, $id_ddv, $is_ddv, $newsletter);
				if($fe_user['uid']){
					$registrationMark=array();
					$registrationMark['###CONFIRMATION_URL###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('fe_user'=>$fe_user['uid'],'ident'=>$fe_user['crdate']), 1, 1);
					$registrationMark['###SITE_URL###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL');
					$registrationMark['###USERNAME###']=$fe_user['username'];
					$registrationMark['###PASSWORD###']=$fe_user['password'];
					$registrationMark['###FIRST_NAME###']=$first_name;
					$registrationMark['###LAST_NAME###']=$last_name;
					
					$this->sendEmail($this->conf['from_name'],$this->conf['from'], $email, $email, $this->pi_getLL('registration_mail_subject'), $this->cObj->substituteMarkerArrayCached($this->loadTemplate('###REGISTRATION_MAIL###'),$registrationMark,array(),array()));
					//$this->sendMail($email ,$this->pi_getLL('registration_mail_subject') ,$this->cObj->substituteMarkerArrayCached($this->loadTemplate('###REGISTRATION_MAIL###'),$registrationMark,array(),array()));
					/*
					if($usergroup==$this->conf['notify_usergroup']){
						$this->sendMail($email ,$this->pi_getLL('registration_mail_subject') ,$this->cObj->substituteMarkerArrayCached($this->loadTemplate('###NOTIFY_USERGROUP_MAIL###'),$registrationMark,array(),array()));
					}*/
					return $this->loadTemplate('###REGISTRATION_SUCCESS###');
				}else{
					return $this->loadTemplate('###REGISTRATION_CREATION_ERROR###');
				}	
			}else{
				return $this->cObj->substituteMarkerArrayCached($this->loadTemplate('###USER_EXISTS_ERROR###'),array('###FORGOT_PASSWORD_URL###'=>t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('show'=>'forgot_password'), 1, 1)),array(),array());
			}
		}
		
		return $this->cObj->substituteMarkerArrayCached($template,$singleMark,$multiMark,array());
	}
	function forgotPassword($arg){
		$template=$this->loadTemplate('###FORGOT_PASSWORD_FORM###');
		$email=t3lib_div::_POST('email');
		
		$singleMark['###THIS_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array(), 1, 0);
		$singleMark['###EMAIL###']=$email;
		$multiMark['###SUBMIT_SUCCESS###']='';
		if(t3lib_div::_POST('forgot_password')){
			if(t3lib_div::validEmail($email)){
				$fe_user_uid=$this->checkUserExists($email);
				$fe_user=$this->getUser(md5($fe_user_uid));
				//t3lib_div::Debug($fe_user);
				//exit();
				if($fe_user['password']){
					$this->cObj->substituteMarkerArrayCached($this->loadTemplate('###FORGOT_PASSWORD_FORM###'),$singleMark,array(),array());
					//$this->sendMail($email,$this->pi_getLL('forgot_password_mail_subject'),$this->cObj->substituteMarkerArrayCached($this->loadTemplate('###FORGOT_PASSWORD_MAIL###'),array('###PASSWORD###'=>$fe_user['password'],'###LOGIN_LINK###'=>t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array(), 1, 1,$this->conf['login_page'])),array(),array()));
					$this->sendEmail($this->conf['from_name'],$this->conf['from'], $email, $email, $this->pi_getLL('forgot_password_mail_subject'), $this->cObj->substituteMarkerArrayCached($this->loadTemplate('###FORGOT_PASSWORD_MAIL###'),array('###PASSWORD###'=>$fe_user['password'],'###LOGIN_LINK###'=>t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array(), 1, 1,$this->conf['login_page'])),array(),array()));
					return $this->cObj->getSubpart($template,'###SUBMIT_SUCCESS###');	
				}
				
			}
		}else{
			$multiMark['###EMAIL_ERROR###']='';
		}
		return $this->cObj->substituteMarkerArrayCached($template,$singleMark,$multiMark,array());
	}
	function feUserConfirmation($crdate, $user_uid){
		$where = ' crdate = \''.$crdate.'\' AND uid = \''.$user_uid.'\'';
		$fields_values['disable'] = 0;
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users',$where,$fields_values);

	}
	function checkUserExists($email){
		$queryParts = array();
		$queryParts['SELECT'] = 'uid';
		$queryParts['FROM'] = 'fe_users';
		$queryParts['WHERE'] = ' pid IN ('.$this->conf['writeinto'].') AND email=\''.$email.'\' AND deleted=0';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		//exit();
		if ($res){
			$u=$GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			return $u['uid'];
		}
	}
	function getUser($user_uid){
		$queryParts = array();
		$queryParts['SELECT'] = '*';
		$queryParts['FROM'] = 'fe_users';
		$queryParts['WHERE'] = ' MD5(uid)=\''.$user_uid.'\' AND deleted=0';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		if ($res) {
			return $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		}
	}
	function insertFEUser($usergroup, $first_name, $last_name, $password, $naslov, $posta, $kraj, $ustanova, $telefon, $email, $id_ddv, $is_ddv, $newsletter){
		$time=time();
		
		$insertArray = array(
     'pid' => $this->conf['writeinto'],
     'tstamp' => $time,
     'crdate' => $time,
     'disable' => 1,
     'first_name' => $first_name,
     'last_name' => $last_name,
     'username' => $email,
     'email' => $email,
     'password' => $password,
     'usergroup' => $usergroup,
     'address' => $naslov,
     'zip' => $posta,
     'city' => $kraj,
     'company' => $ustanova,
     'telephone' => $telefon,
     'tx_webshopregistration_id_ddv' => $id_ddv,
	 'tx_webshopregistration_is_ddv' => $is_ddv,
	 'tx_webshopregistration_info_reciver' => $newsletter
		);
		//t3lib_utility_Debug::debug($insertArray);
		//exit();
		$query = $GLOBALS['TYPO3_DB']->exec_INSERTquery('fe_users', $insertArray);
		$insertArray['uid']=$GLOBALS['TYPO3_DB']->sql_insert_id();
		
		return $insertArray;
	}

	function editFEUser($userId, $first_name, $last_name, $password, $naslov, $posta, $kraj, $ustanova, $telefon, $email, $id_ddv, $is_ddv, $newsletter){
		$time=time();
		$where = ' uid='.$userId;
		
		$insertArray = array(
     'tstamp' => $time,
     'disable' => 0,
     'first_name' => $first_name,
     'last_name' => $last_name,
     'username' => $email,
     'email' => $email,
     'password' => $password,
     'address' => $naslov,
     'zip' => $posta,
     'city' => $kraj,
	 'company' => $ustanova,
     'telephone' => $telefon,
     'tx_webshopregistration_id_ddv' => $id_ddv,
	 'tx_webshopregistration_is_ddv' => $is_ddv,
	 'tx_webshopregistration_info_reciver' => $newsletter
		);
//t3lib_utility_Debug::debug($insertArray);
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', $where, $insertArray);
	}
	function confirmUser($user_uid, $crdate){
		$confirmation=array();
		$where = ' MD5(crdate) = \''.$crdate.'\' AND MD5(uid) = \''.$user_uid.'\'';
		$fields_values['disable'] = 0;
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users',$where,$fields_values);
		//$confirmation['success']=$GLOBALS['TYPO3_DB']->sql_affected_rows();
		//$confirmation['user']=$this->getUser($user_uid);
		//t3lib_div::Debug($confirmation);
		//exit();
		return $confirmation;
	}
	
	function sendEmail($fromName,$fromEmail, $toName, $toEmail, $subject, $body){
		$mail = t3lib_div::makeInstance('t3lib_mail_Message');
		$mail->setSubject($subject);
		$mail->setFrom(array($fromEmail => $fromName));
		$mail->setTo(array($toEmail => $toName));
		$mail->setBody($body,'text/html');
		$mail->send();
	}
	
	function sendMail($sendTo, $subject, $message){
		/*t3lib_utility_Debug::debug($this->conf,'$this->conf');
		t3lib_utility_Debug::debug($subject,'$subject');
		t3lib_utility_Debug::debug($message,'$message');
		t3lib_utility_Debug::debug($sendTo,'$sendTo');*/

		//$mail->Send();
		
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		
		// Additional headers
		$headers .= 'To: '.$this->conf['from'].' <'.$this->conf['from'].'>' . "\r\n";
		$headers .= 'From: '.$this->conf['from_name'].' <'.$this->conf['from'].'>' . "\r\n";
		
		// Mail it
		$a = mail($to, $this->conf['subject'], $message, $headers);
		//t3lib_utility_Debug::debug($a,'$a');
	}
	function loadTemplate($marker){
		if($this->conf['templateFile']){
			return $this->cObj->getSubpart($this->cObj->fileResource($this->conf['templateFile']), $marker);
		}		
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/web_shop_registration/pi1/class.tx_webshopregistration_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/web_shop_registration/pi1/class.tx_webshopregistration_pi1.php']);
}

?>