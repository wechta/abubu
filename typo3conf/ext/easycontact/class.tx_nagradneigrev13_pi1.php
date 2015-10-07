<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Peter Wechtersbach <peter@naprej.net>
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
include_once "fb/facebook.php";

/**
 * Plugin 'Nagradne igre v13' for the 'nagradneigrev13' extension.
 *
 * @author	Peter Wechtersbach <peter@naprej.net>
 * @package	TYPO3
 * @subpackage	tx_nagradneigrev13
 */
class tx_nagradneigrev13_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_nagradneigrev13_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_nagradneigrev13_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'nagradneigrev13';	// The extension key.
	var $pi_checkCHash = true;
	var $template;
	var $step;
	
	function init(){
	  $this->pi_initPIflexForm(); // Init and get the flexform data of the plugin
	  $this->conf = array(); // Setup our storage array...
	  // Assign the flexform data to a local variable for easier access
	  $piFlexForm = $this->cObj->data['pi_flexform'];
	  // Traverse the entire array based on the language...
	  // and assign each configuration option to $this->conf array...
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
		
		if($this->conf['cssFile']){
			$GLOBALS['TSFE']->pSetup['headerData.']['99'] = 'TEXT';
			$GLOBALS['TSFE']->pSetup['headerData.']['99.']['value'] = '<link rel="stylesheet" href="'.$this->conf['cssFile'].'" type="text/css" />
			<html xmlns:fb="http://www.facebook.com/2008/fbml">
			<script src="https://connect.facebook.net/en_US/all.js"></script>
			';
		}
		
		
		$this->template = $this->cObj->fileResource($this->conf['templateFile']);
		//t3lib_utility_Debug::debug($this->conf);
	
		if($this->conf['fbapp']){
			$decodedSignedRequest = $this->parse_signed_request($_REQUEST['signed_request'], $this->conf['secret']);
			$GLOBALS['TSFE']->pSetup['headerData.']['97'] = 'TEXT';
				$GLOBALS['TSFE']->pSetup['headerData.']['97.']['value'] = '
				<script type="text/javascript">
					window.fbAsyncInit = function() {
						FB.init( { appId: "", status: true, cookie: true, xfbml: true} );
						//FB.Canvas.setAutoGrow(7);
						FB.Canvas.scrollTo(0,0);
						FB.Canvas.setSize({height:0});
						setTimeout(0,0);
						$(window).load(function(){
							FB.Canvas.setSize();
						});
					};
				</script>
				
				';
			
			
			//ce ni lijka vrnemo slikco za like
			if(!$decodedSignedRequest['page']['liked'] && !$this->piVars['step']){
				return '<img src="/uploads/tx_nagradneigre/'.$this->conf['image_nolike'].'" />';
			}
			
			
			if($this->piVars['step']==1 || !$this->piVars['step']){
				$template = $this->cObj->getSubpart($this->template, '###FBKORAK1###');
				$singleMark['###FBSTEP2###'] =$this->pi_linkTP_keepPIvars_url(array('step' => 2));
				$singleMark['###POGOJI###'] = $this->getSplosnePogoje();
				$content = $this->cObj->substituteMarkerArrayCached($template, $singleMark, $multiMark, array());
			}
			elseif($this->piVars['step']==2){
				$template = $this->cObj->getSubpart($this->template, '###FBKORAK2###');
				$singleMark['###POGOJI###'] = $this->getSplosnePogoje();
				$content = $this->cObj->substituteMarkerArrayCached($template, $singleMark, $multiMark, array());
			}
			elseif($this->piVars['step']==3){
				$template = $this->cObj->getSubpart($this->template, '###FBKORAK3###');
				$templateEmail = $this->cObj->getSubpart($this->template, '###EMAIL_SODELUJOC###');
				$userID = $this->insertSodelujoc($this->piVars['ime'], $this->piVars['email']);
				$singleMark['###POGOJI###'] = $this->getSplosnePogoje();
				$singleMark['###USERID###'] = $userID;
				$singleMark['###IME###'] = $this->piVars['ime'];
				$singleMark['###NEXTSTEP###'] = $this->pi_linkTP_keepPIvars_url(array('step' => 4),$cache=0,$clearAnyway=1,'');
				$emailBody = $this->cObj->substituteMarkerArrayCached($templateEmail, $singleMark, $multiMark, array());
				$this->sendEmail('Promplac - Nagradna igra 123','info@promplac.si', $this->piVars['ime'], $this->piVars['email'],$this->conf['emailSubjectSodelujoc'],$emailBody);
				$content = $this->cObj->substituteMarkerArrayCached($template, $singleMark, $multiMark, array());
			}
			elseif($this->piVars['step']==4){
				$template = $this->cObj->getSubpart($this->template, '###FBKORAK4###');
				$templateEmail = $this->cObj->getSubpart($this->template, '###EMAIL_POVABLJEN###');
				$povabil = $this->pi_getRecord('tx_nagradneigrev13_sodelujoc',$this->piVars['userid'],$checkPage=0);
				
				if($this->piVars['email1']){
					$id = $this->insertPovabljen($this->piVars['ime1'], $this->piVars['email1'], $this->piVars['userid']);
					$singleMark['###IME###'] = $povabil['ime'];
					$singleMark['###IMEPREJEMNIK###'] = $this->piVars['ime1'];
					$singleMark['###PREVZEM_URL###'] = 'http://fbapps.promplac.si/'.$this->pi_linkTP_keepPIvars_url(array('step' => 1,'povabljen' => $id, 'povabil' => $povabil['uid']),$cache=0,$clearAnyway=1,$this->conf['wwwapp']);
					$emailBody = $this->cObj->substituteMarkerArrayCached($templateEmail, $singleMark, $multiMark, array());
					$emailSubject = $this->cObj->substituteMarkerArrayCached($this->conf['emailSubjectPovabljen'], $singleMark, $multiMark, array());
					$this->sendEmail('Promplac - Nagradna igra 123','info@promplac.si', $this->piVars['ime1'], $this->piVars['email1'],$emailSubject,$emailBody);
				}
				if($this->piVars['email2']){
					$id = $this->insertPovabljen($this->piVars['ime2'], $this->piVars['email2'], $this->piVars['userid']);
					$singleMark['###IME###'] = $povabil['ime'];
					$singleMark['###IMEPREJEMNIK###'] = $this->piVars['ime2'];
					$singleMark['###PREVZEM_URL###'] = 'http://fbapps.promplac.si/'.$this->pi_linkTP_keepPIvars_url(array('step' => 1,'povabljen' => $id, 'povabil' => $povabil['uid']),$cache=0,$clearAnyway=1,$this->conf['wwwapp']);
					$emailBody = $this->cObj->substituteMarkerArrayCached($templateEmail, $singleMark, $multiMark, array());
					$emailSubject = $this->cObj->substituteMarkerArrayCached($this->conf['emailSubjectPovabljen'], $singleMark, $multiMark, array());
					$this->sendEmail('Promplac - Nagradna igra 123','info@promplac.si', $this->piVars['ime2'], $this->piVars['email2'],$emailSubject,$emailBody);
				}
				if($this->piVars['email3']){
					$id = $this->insertPovabljen($this->piVars['ime3'], $this->piVars['email3'], $this->piVars['userid']);
					$singleMark['###IME###'] = $povabil['ime'];
					$singleMark['###IMEPREJEMNIK###'] = $this->piVars['ime3'];
					$singleMark['###PREVZEM_URL###'] = 'http://fbapps.promplac.si/'.$this->pi_linkTP_keepPIvars_url(array('step' => 1,'povabljen' => $id, 'povabil' => $povabil['uid']),$cache=0,$clearAnyway=1,$this->conf['wwwapp']);
					$emailBody = $this->cObj->substituteMarkerArrayCached($templateEmail, $singleMark, $multiMark, array());
					$emailSubject = $this->cObj->substituteMarkerArrayCached($this->conf['emailSubjectPovabljen'], $singleMark, $multiMark, array());
					$this->sendEmail('Promplac - Nagradna igra 123','info@promplac.si', $this->piVars['ime3'], $this->piVars['email3'],$emailSubject,$emailBody);
				}
				$singleMark['###POGOJI###'] = $this->getSplosnePogoje();
				$singleMark['###FB_ID###'] = $this->conf['appid'];
				$singleMark['###FB_SEND_NAME###'] = $this->conf['fbSendName'];
				$singleMark['###FB_SEND_DESCRIPTION###'] = $this->conf['fbSendDescription'];
				$singleMark['###FB_SEND_PICTURE###'] = 'http://fbapps.promplac.si/uploads/tx_nagradneigre/'.$this->conf['fbSendImage'];
				$content = $this->cObj->substituteMarkerArrayCached($template, $singleMark, $multiMark, array());
			}
		}
		else{
			if($this->piVars['step']==1 || !$this->piVars['step']){
				if($this->piVars['povabljen']){
					$povabljen = $this->pi_getRecord('tx_nagradneigrev13_povabljen',$this->piVars['povabljen'],$checkPage=0);
					$fields_values['sodeluje'] = 1;
					$this->updatePovabljen($this->piVars['povabljen'], $fields_values);
					$template = $this->cObj->getSubpart($this->template, '###WWWKORAK1_POVABLJEN###');
				}else{
					$template = $this->cObj->getSubpart($this->template, '###WWWKORAK1###');
				}
				$singleMark['###WWWSTEP2###'] =$this->pi_linkTP_keepPIvars_url(array('step' => 2));
				$singleMark['###POGOJI###'] = $this->getSplosnePogoje();
				$content = $this->cObj->substituteMarkerArrayCached($template, $singleMark, $multiMark, array());
			}
			elseif($this->piVars['step']==2){
				$template = $this->cObj->getSubpart($this->template, '###WWWKORAK2###');
				$singleMark['###POGOJI###'] = $this->getSplosnePogoje();
				$content = $this->cObj->substituteMarkerArrayCached($template, $singleMark, $multiMark, array());
			}
			elseif($this->piVars['step']==3){
				$template = $this->cObj->getSubpart($this->template, '###WWWKORAK3###');
				$templateEmail = $this->cObj->getSubpart($this->template, '###EMAIL_SODELUJOC###');
				$userID = $this->insertSodelujoc($this->piVars['ime'], $this->piVars['email']);
				$singleMark['###POGOJI###'] = $this->getSplosnePogoje();
				$singleMark['###USERID###'] = $userID;
				$singleMark['###IME###'] = $this->piVars['ime'];
				$singleMark['###NEXTSTEP###'] = $this->pi_linkTP_keepPIvars_url(array('step' => 4),$cache=0,$clearAnyway=1,'');
				$emailBody = $this->cObj->substituteMarkerArrayCached($templateEmail, $singleMark, $multiMark, array());
				$this->sendEmail('Promplac - Nagradna igra 123','info@promplac.si', $this->piVars['ime'], $this->piVars['email'],$this->conf['emailSubjectSodelujoc'],$emailBody);
				$content = $this->cObj->substituteMarkerArrayCached($template, $singleMark, $multiMark, array());
			}
			elseif($this->piVars['step']==4){
				$template = $this->cObj->getSubpart($this->template, '###WWWKORAK4###');
				$templateEmail = $this->cObj->getSubpart($this->template, '###EMAIL_POVABLJEN###');
				$povabil = $this->pi_getRecord('tx_nagradneigrev13_sodelujoc',$this->piVars['userid'],$checkPage=0);
				if($this->piVars['email1']){
					$id = $this->insertPovabljen($this->piVars['ime1'], $this->piVars['email1'], $this->piVars['userid']);
					$singleMark['###IME###'] = $povabil['ime'];
					$singleMark['###IMEPREJEMNIK###'] = $this->piVars['ime1'];
					$singleMark['###PREVZEM_URL###'] = 'http://fbapps.promplac.si/'.$this->pi_linkTP_keepPIvars_url(array('step' => 1,'povabljen' => $id, 'povabil' => $povabil['uid']),$cache=0,$clearAnyway=1,$altPageId=0);
					$emailBody = $this->cObj->substituteMarkerArrayCached($templateEmail, $singleMark, $multiMark, array());
					$emailSubject = $this->cObj->substituteMarkerArrayCached($this->conf['emailSubjectPovabljen'], $singleMark, $multiMark, array());
					$this->sendEmail('Promplac - Nagradna igra 123','info@promplac.si', $this->piVars['ime1'], $this->piVars['email1'],$emailSubject,$emailBody);
				}
				if($this->piVars['email2']){
					$id = $this->insertPovabljen($this->piVars['ime2'], $this->piVars['email2'], $this->piVars['userid']);
					$singleMark['###IME###'] = $povabil['ime'];
					$singleMark['###IMEPREJEMNIK###'] = $this->piVars['ime2'];
					$singleMark['###PREVZEM_URL###'] = 'http://fbapps.promplac.si/'.$this->pi_linkTP_keepPIvars_url(array('step' => 1,'povabljen' => $id, 'povabil' => $povabil['uid']),$cache=0,$clearAnyway=1,$altPageId=0);
					$emailBody = $this->cObj->substituteMarkerArrayCached($templateEmail, $singleMark, $multiMark, array());
					$emailSubject = $this->cObj->substituteMarkerArrayCached($this->conf['emailSubjectPovabljen'], $singleMark, $multiMark, array());
					$this->sendEmail('Promplac - Nagradna igra 123','info@promplac.si', $this->piVars['ime2'], $this->piVars['email2'],$emailSubject,$emailBody);
				}
				if($this->piVars['email3']){
					$id = $this->insertPovabljen($this->piVars['ime3'], $this->piVars['email3'], $this->piVars['userid']);
					$singleMark['###IME###'] = $povabil['ime'];
					$singleMark['###IMEPREJEMNIK###'] = $this->piVars['ime3'];
					$singleMark['###PREVZEM_URL###'] = 'http://fbapps.promplac.si/'.$this->pi_linkTP_keepPIvars_url(array('step' => 1,'povabljen' => $id, 'povabil' => $povabil['uid']),$cache=0,$clearAnyway=1,$altPageId=0);
					$emailBody = $this->cObj->substituteMarkerArrayCached($templateEmail, $singleMark, $multiMark, array());
					$emailSubject = $this->cObj->substituteMarkerArrayCached($this->conf['emailSubjectPovabljen'], $singleMark, $multiMark, array());
					$this->sendEmail('Promplac - Nagradna igra 123','info@promplac.si', $this->piVars['ime3'], $this->piVars['email3'],$emailSubject,$emailBody);
				}
				$singleMark['###POGOJI###'] = $this->getSplosnePogoje();
				$content = $this->cObj->substituteMarkerArrayCached($template, $singleMark, $multiMark, array());
			}
		}
	
		return $this->pi_wrapInBaseClass($content);
	}
	function sendEmail($fromName,$fromEmail, $toName, $toEmail, $subject, $body){
		$mail = t3lib_div::makeInstance('t3lib_mail_Message');
		$mail->setSubject($subject);
		$mail->setFrom(array($fromEmail => $fromName));
		$mail->setTo(array($toEmail => $toName));
		$mail->setBody($body,'text/html');
		$mail->send();
	}
	function insertSodelujoc($ime, $email){
		$fields_values['pid'] = $this->conf['pidList'];
		$fields_values['tstamp'] = time();
		$fields_values['crdate'] = time();
		$fields_values['ime'] = $ime;
		$fields_values['email'] = $email;
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_nagradneigrev13_sodelujoc', $fields_values);
		return $GLOBALS['TYPO3_DB']->sql_insert_id();
	}
	function insertPovabljen($ime, $email, $povabil){
		$fields_values['pid'] = $this->conf['pidList'];
		$fields_values['tstamp'] = time();
		$fields_values['crdate'] = time();
		$fields_values['ime'] = $ime;
		$fields_values['email'] = $email;
		$fields_values['povabil'] = $povabil;
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_nagradneigrev13_povabljen', $fields_values);
		return $GLOBALS['TYPO3_DB']->sql_insert_id();
	}
	function updatePovabljen($idUser, $fields_values){
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_nagradneigrev13_povabljen', '  uid='.$idUser, $fields_values, FALSE);
	}
	function getSplosnePogoje(){
		if($this->conf['splosni_pogoji']){
			return 'http://fbapps.promplac.si/uploads/tx_nagradneigre/'.$this->conf['splosni_pogoji'];
		}else{
			return $this->conf['splosni_pogoji_link'];
		}
	}
	function parse_signed_request($signed_request, $secret) {
		
    list($encoded_sig, $payload) = explode('.', $signed_request, 2);

    // decode the data
    $sig = $this->base64_url_decode($encoded_sig);
    $data = json_decode($this->base64_url_decode($payload), true);

    if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
        //echo('Unknown algorithm. Expected HMAC-SHA256');
        return null;
    }

    // check sig
    $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
    if ($sig !== $expected_sig) {
        //echo('Bad Signed JSON signature!');
        return null;
    }

    return $data;
  }
  function base64_url_decode($input) {
     return base64_decode(strtr($input, '-_', '+/'));
  }
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/nagradneigrev13/pi1/class.tx_nagradneigrev13_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/nagradneigrev13/pi1/class.tx_nagradneigrev13_pi1.php']);
}

?>