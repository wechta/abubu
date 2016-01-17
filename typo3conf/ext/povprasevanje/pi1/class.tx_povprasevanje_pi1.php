<?php
			/***************************************************************
			*  Copyright notice
			*
			*  (c) 2011  <>
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
			 * Plugin 'Povprasevanje' for the 'povprasevanje' extension.
			 *
			 * @author	 <>
			 * @package	TYPO3
			 * @subpackage	tx_povprasevanje
			 */
			class tx_povprasevanje_pi1 extends tslib_pibase {
				var $prefixId      = 'tx_povprasevanje_pi1';		// Same as class name
				var $scriptRelPath = 'pi1/class.tx_povprasevanje_pi1.php';	// Path to this script relative to the extension dir.
				var $extKey        = 'povprasevanje';	// The extension key.
				var $pi_checkCHash = true;
				
				
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
						function main($content,$conf)	{
							$this->conf=$conf;
							$this->pi_setPiVarDefaults();
							$this->pi_loadLL();
							$this->init();
							//t3lib_div::debug($this->conf);
							$template = $this->cObj->fileResource($this->conf['templateFile1']);

							$templateOut = $this->cObj->getSubpart($template, '###POVPRASEVANJE###');
							
							//t3lib_div::debug($templateOut,'sdf');
							
							if($this->piVars['submit']){
								//t3lib_div::debug($this->conf);
								//t3lib_div::debug($this->piVars);
								t3lib_utility_Debug::debug($this->conf);
								$templateEmail = $this->cObj->getSubpart($template, '###EMAIL###');
								$emailBody = $this->cObj->substituteMarkerArrayCached($templateEmail, $singleMark, $multiMark, array());
								$emailSubject = 'Subject emaila';
								$this->sendEmail('Promplac - Nagradna igra 123','info@promplac.si', 'To name', 'aljosa.borstnar@gmail.com',$emailSubject,$emailBody);
								$templateOut = $this->cObj->getSubpart($template, '###POVPRASEVANJE_KONCANO###');
								
							}
							
							return $templateOut;
						}
						function sendEmail($fromName,$fromEmail, $toName, $toEmail, $subject, $body){
							$mail = t3lib_div::makeInstance('t3lib_mail_Message');
							$mail->setSubject($subject);
							$mail->setFrom(array($fromEmail => $fromName));
							$mail->setTo(array($toEmail => $toName));
							$mail->setBody($body,'text/html');
							$mail->send();
						}
						
			}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/povprasevanje/pi1/class.tx_povprasevanje_pi1.php'])	{
				include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/povprasevanje/pi1/class.tx_povprasevanje_pi1.php']);
			}
			
			?>