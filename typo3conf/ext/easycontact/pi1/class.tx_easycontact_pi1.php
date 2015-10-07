<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Naprej.net d.o.o <peter@naprej.net>
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
 * Plugin 'Easy contact' for the 'easycontact' extension.
 *
 * @author	Naprej.net d.o.o <peter@naprej.net>
 * @package	TYPO3
 * @subpackage	tx_easycontact
 */
class tx_easycontact_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_easycontact_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_easycontact_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'easycontact';	// The extension key.
	var $pi_checkCHash = true;
	
	
	function init(){
	  $this->pi_initPIflexForm(); 
	  $this->conf = array();
	  $piFlexForm = $this->cObj->data['pi_flexform'];
	  foreach ( $piFlexForm['data'] as $sheet => $data )
		foreach ( $data as $lang => $value )
		  foreach ( $value as $key => $val )
		   $this->conf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);

	 }
	
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
		$this->init();
		$template = $this->cObj->fileResource($this->conf['templateFile']);
		//t3lib_utility_Debug::debug($this->conf);
		
		$thisurl = "http://".$_SERVER['HTTP_HOST']."/";
		$thanks = $this->pi_getPageLink($this->conf['pidThanks']);
		$thaturl = $thisurl . $thanks;
			
		if($this->piVars['submit']){
			$templateEmail = $this->cObj->getSubpart($template, '###EMAIL###');
			$templateEmailEntry = $this->cObj->getSubpart($template, '###EMAIL_ENTRY###');
			
			foreach($this->piVars as $key=>$value){
				if($key != 'submit'){
					$singleMark['###KEY###'] = $key;
					$singleMark['###VALUE###'] = $value;
					$emailEntry .= $this->cObj->substituteMarkerArrayCached($templateEmailEntry, $singleMark, $multiMark, array());
				}
			}
			$multiMark['###EMAIL_ENTRY###'] = $emailEntry;
			$emailBody = $this->cObj->substituteMarkerArrayCached($templateEmail, $singleMark, $multiMark, array());
			$singleMark['###NAME###'] = $this->piVars['name'];
			$singleMark['###EMAIL###'] = $this->piVars['email'];
			$emailsubject = $this->cObj->substituteMarkerArrayCached($this->conf['subject'], $singleMark, $multiMark, array());
			$this->sendEmail($this->conf['fromName'],$this->conf['fromEmail'],$this->conf['toName'],$this->conf['toEmail'],$emailsubject,$emailBody);
			$this->insertEntry($emailsubject, $emailBody);
			if($this->conf['sendToUser']){
				$emailBody = $this->cObj->substituteMarkerArrayCached($this->cObj->getSubpart($template, '###EMAIL_USER###'), $singleMark, $multiMark, array());
				$emailsubject = $this->cObj->substituteMarkerArrayCached($this->conf['subjectUser'], $singleMark, $multiMark, array());
				$this->sendEmail($this->conf['fromName'],$this->conf['fromEmail'],$this->conf['toName'],$this->conf['toEmail'],$emailsubject,$emailBody);
			}
			if($this->conf['pidThanks']){
				
				$GLOBALS['TSFE']->pSetup['headerData.']['98'] = 'TEXT';
				$GLOBALS['TSFE']->pSetup['headerData.']['98.']['value'] = '<script type="text/javascript">top.location.href = "'.$thaturl.'";</script>';
				return '';
			}else{
				return $this->cObj->getSubpart($template, '###EMAIL_TENX###');
			}
		}
		$singleMark['###LEGAL_URL###'] = $this->pi_getPageLink($this->conf['pidLegal'],$target='',$urlParameters=array());
		return $this->cObj->substituteMarkerArrayCached($this->cObj->getSubpart($template, '###MAIN###'), $singleMark, $multiMark, array());
	}
	function sendEmail($fromName,$fromEmail, $toName, $toEmail, $subject, $body){
		$mail = t3lib_div::makeInstance('t3lib_mail_Message');
		$mail->setSubject($subject);
		$mail->setFrom(array($fromEmail => $fromName));
		$mail->setTo(array($toEmail => $toName));
		$mail->setBody($body,'text/html');
		$mail->send();
	}
	function insertEntry($subject, $body){
		$insertArray = array(
     'pid' => $this->conf['pidList'],
     'tstamp' => $time,
     'crdate' => $time,
     'subject' => $subject,
     'content' => $body
		);
		$query = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_easycontact_entry', $insertArray);
	}
	function full_url()
	{
		$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
		$sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
		$protocol = substr($sp, 0, strpos($sp, "/")) . $s;
		$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
	}
	
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/easycontact/pi1/class.tx_easycontact_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/easycontact/pi1/class.tx_easycontact_pi1.php']);
}
?>