<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004 Tonni Aagesen (typo3@pil.dk)
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
 * Plugin 'TMailform' for the 'pil_mailform' extension.
 *
 * @author	Tonni Aagesen <t3dev@support.pil.dk>
 * 
 * TODO:
 * 
 * General optimization:
 *  - reuse $markers from get_mailform() in sendmail()
 *  - refactoring -> split large chunks of code in to smaller more logical ones  
 *  - Map multiple rows from db to select->options marker
 *  - Find out weather syslog() is compatible with <4.0.0
 */

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('pil_mailform').'phpmailer/class.phpmailer.php');

class tx_pilmailform_pi1 extends tslib_pibase {
	var $prefixId = "tx_pilmailform_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_pilmailform_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "pil_mailform";	// The extension key.
    var $extConf;
	var $localconf;
	var $template;
	var $piVars = array();
	var $errStr = '';
	var $errForm;

	/**
	 * Typo3 call function
	 * @param string	Content
	 * @param string	Conf
	 * @return string 	The output content of the plugin
	 */
	function main($content,$conf) {
		$this->pi_setPiVarDefaults();
		$this->pi_USER_INT_obj=1;
		$this->localconf = $conf;
		$this->errForm = false;

		if ($this->init()) {
			if (isset($this->piVars['submit'])) {
				// We may not actually need the mailform content, but we need to check for form validation errors...
				$content = $this->get_mailform();
				// ... so if no form validation error occured, we send the mail instead
				if (!$this->errForm) {
					$content = $this->sendmail();
				}
			} else {
				$content = $this->get_mailform();
			}
		} else {
			$content = $this->errStr;
		}

		return $this->pi_wrapInBaseClass($content);
	}


	/**
	 * Send mail to recipient (and optionally copy to user)
	 * @return string 	Succes/failure response 
	 */
	function sendmail() {
		
		// Initialize vairables
		$formMarkers = array();
        $this->load_static_markers($formMarkers);
		
		// Handle file uploads
		$files = $this->get_files();
		foreach($files as $k => $v) {
			// Assign typo3temp file name to file array
			if ((bool) $files[$k]['t3_tmp_name'] = t3lib_div::upload_to_tempfile($v['tmp_name'])) {
				$formMarkers['###' . strtoupper($k) . '_VAL###'] = $v['name'];
			}
		}

		// Set markers from form
		foreach ($this->piVars as $k => $v) {
			if (is_array($v)) {
				while (list ($m,$n) = each($v)) {
					if (is_array($n)) {
						$formMarkers['###' . strtoupper($m) . '_VAL###'] = implode(', ', $n);
					} else {
						$formMarkers['###' . strtoupper($m) . '_VAL###'] = ($k == 'textarea') ? $n : $n;
					}
				}
			}
		}

		// Get recipient option value marker
		if ($this->localconf['typeofRecipient'] == 1) {
			$lines = explode("\n", $this->localconf['dynamicRecipient']);
			$tmp = explode(';', $lines[($this->piVars['select']['multi_recipient'] - 1)]);
			$tmp = array(trim($tmp[0]));
			$formMarkers['###MULTI_RECIPIENT_OPTVAL_VAL###'] = implode(', ', $tmp);
		}

        // Handle 'Store Values'
        $queries = array();
		// Get Default Values config
		$SVConfig = $this->parse_config($this->localconf['storeValues']);
		foreach ($SVConfig as $svc) {
			switch ($svc['method']) {
				case 'db':
					$subparts = $this->trim_explode(':', $svc['pparm'], false, 3);
					$this->array_append($this->trim_explode(',', $subparts[1]), $queries[$svc['proc']][$subparts[0]][$subparts[2]]['fields']);
					$this->array_append($this->trim_explode(',', $svc['fields']), $queries[$svc['proc']][$subparts[0]][$subparts[2]]['markers']);
				break;
				default:
			}
		}
        // Store values to database
        $sql = $this->execQueries($queries, $formMarkers);
        
		// Get recipient
		if ($this->localconf['typeofRecipient'] == 1) {
			$lines = explode("\n", $this->localconf['dynamicRecipient']);
			$To = explode(';', $lines[($this->piVars['select']['multi_recipient'] - 1)]);
			$To = array(trim($To[1]));
		} else {
			$To = $this->trim_explode("\n", $this->localconf['staticRecipient'], true);			
		}

		// Get subject
		$this->localconf['userSubjectPrefix'] = $this->cObj->substituteMarkerArray($this->localconf['userSubjectPrefix'], $formMarkers);
		$this->localconf['staticSubject'] = $this->cObj->substituteMarkerArray($this->localconf['staticSubject'], $formMarkers);
		$Subject = ($this->localconf['overrideSubject'] == 1) ? trim($this->localconf['userSubjectPrefix'] . ' ' . $this->piVars['text']['subject']) : $this->localconf['staticSubject'];

		// Get From headers
		if ($this->localconf['overrideFromHeader'] == 1) {
			if (!empty($this->piVars['text']['surname'])) {
				$FromName = $this->localconf['overrideNameFormat'] == 0 ? $this->piVars['text']['name'] . ' ' . $this->piVars['text']['surname'] : $this->piVars['text']['surname'] . ', ' . $this->piVars['text']['name'];  
			} else {
				$FromName = $this->piVars['text']['name'];
			}
			$FromMail = $this->piVars['text']['email'];
		} else {
			$FromName = $this->localconf['fromName'];
			$FromMail = $this->localconf['fromMail'];			
		}

		// Get Reply-To headers
		if ($this->localconf['overrideReplyToHeader'] == 1) {
			if (!empty($this->piVars['text']['surname'])) {
				$ReplyToName = $this->localconf['overrideNameFormat'] == 0 ? $this->piVars['text']['name'] . ' ' . $this->piVars['text']['surname'] : $this->piVars['text']['surname'] . ', ' . $this->piVars['text']['name'];  
			} else {
				$ReplyToName = $this->piVars['text']['name'];
			}
			$ReplyToMail = $this->piVars['text']['email'];
		} else {
        	$ReplyToName = $this->localconf['replyToName'];
			$ReplyToMail = $this->localconf['replyToMail'];			
		}

		// Get Cc header email addresses
		$Cc = $this->trim_explode("\n", $this->localconf['Cc'], true);

		// Get Bcc header email addresses
		$Bcc = $this->trim_explode("\n", $this->localconf['Bcc'], true);

		// Get Content-Transfer-Encoding header
        switch ($this->localconf['contentTransferEncoding']) {
			case 0:
				$ContentTransferEncoding = '8bit';
			break;
			case 1:
				$ContentTransferEncoding = '7bit';
			break;
			case 2:
				$ContentTransferEncoding = 'base64';
			break;
			case 3:
				$ContentTransferEncoding = 'binary';
			break;
			case 4:
				$ContentTransferEncoding = 'quoted-printable';
			break;
			default:
				$ContentTransferEncoding = '8bit';
			break;
		}

		// Get Content-Type header
        switch ($this->localconf['contentType']) {
			case 0:
				$ContentType = 'text/plain';
			break;
			case 1:
				$ContentType = 'text/html';
			break;
			default:
				$ContentType = 'text/plain';
			break;
		}

		// Get Charset header
        $charset = (empty($this->localconf['charset'])) ? $GLOBALS['TSFE']->metaCharset : $this->localconf['charset'];

		// Compile message
       	if ($this->localconf['contentType'] == 1) {
			$subpart = $this->cObj->getSubpart($this->template, "###TMAIL_MAIL###");
			$altBody = $this->cObj->substituteMarkerArray($subpart, ($this->localconf['testmode'] == 1 ? array_map('htmlspecialchars', $formMarkers) : $formMarkers));
			$subpart = $this->cObj->getSubpart($this->template, "###TMAIL_MAIL_HTML###");
			$body = $this->cObj->substituteMarkerArray($subpart, array_map(array('tx_pilmailform_pi1', 'format_HTML_mail'), $formMarkers));
		} else {
			$subpart = $this->cObj->getSubpart($this->template, "###TMAIL_MAIL###");
			$body = $this->cObj->substituteMarkerArray($subpart, ($this->localconf['testmode'] == 1 ? array_map('htmlspecialchars', $formMarkers) : $formMarkers));
			$altBody = null;
		}

		// Remove any left over markers
		$body = preg_replace("/###.*?###/", '', $body);
		$altBody = preg_replace("/###.*?###/", '', $altBody);
			
		// Init phpmailer class
		$mailer = new PHPMailer();
			
		// Set phpmailer config
		$mailer->SetLanguage('en', t3lib_extMgm::extPath('pil_mailform').'phpmailer/language/');
		switch ($this->localconf['useMailer']) {
			case 0:
				$mailer->Mailer = 'mail';
			break;
			case 1:
				$mailer->Mailer = 'sendmail';
				$mailer->Sendmail = empty($this->localconf['sendmailPath']) ? '/usr/sbin/sendmail' : $this->localconf['sendmailPath'];
			break;
			case 2:
				$mailer->Mailer = 'smtp';
				$mailer->Host = empty($this->localconf['smtpHost']) ? 'localhost' : $this->localconf['smtpHost'];
				$mailer->Port = empty($this->localconf['smtpPort']) ? '25' : $this->localconf['smtpPort'];
				if ($this->localconf['smtpAuth'] == 1) {
					$mailer->SMTPAuth = true;
					$mailer->Username = $this->localconf['smtpUser'];
					$mailer->Password = $this->localconf['smtpPasswd'];
				}
			break;
			case -1:
				$mailer->Mailer = false;
			break;
			default:
				$mailer->Mailer = 'mail';
			break;
		}
		$mailer->IsHTML(($this->localconf['contentType'] == 0) ? false : true);
		$mailer->Encoding = $ContentTransferEncoding;
		$mailer->ContentType = $ContentType;
		$mailer->CharSet = $charset;
			
		// Set "To:" header
		foreach ($To as $to) {
			$mailer->addAddress($to);
		}
		
		// Set "From:" header
		$mailer->From = $FromMail;
		$mailer->FromName = $FromName;
			
		// Set "Reply-To:" header
		$mailer->AddReplyTo($ReplyToMail, $ReplyToName);
				
		// Set "Cc:" header
		foreach ($Cc as $cc) {	
			$mailer->addCC($cc);
		}

		// Set "Bcc:" header
		foreach ($Bcc as $bcc) {
			$mailer->addBCC($bcc);
		}
			
		// Set "Subject:" header
		$mailer->Subject = $Subject;
		
		// Set mail body
		$mailer->Body = $body;
		$mailer->AltBody = $altBody;
		
		// Set attachments from uploaded files
		foreach ($files as $v) {
			$mailer->AddAttachment($v['t3_tmp_name'], $v['name'], 'base64', $v['type']);	
		}
		
		if ($this->localconf['testmode'] == 1) {
			
        	if(!empty($mailer->AltBody)) {
	            $mailer->ContentType = "multipart/alternative";
        	}
        	$mailer->error_count = 0; // reset errors
        	$mailer->SetMessageType();
        	$mailSrc = htmlspecialchars($mailer->CreateHeader());
        	$mailSrc .= "\nSubject: " . $Subject . "\n" . $mailer->CreateBody();
        	$mailSrc = $mailer->Mailer == 'disabled' ? '' : $mailSrc; 
            
			$content = '
				<h2>TMailform is in testmode</h2><hr />
				<p style="font-weight: bold;">Generated (valid) SQL queries if any:</p><pre>' . implode('<br />', $sql) . '</pre><hr />
                <p style="font-weight: bold;">Mail headers and body if any:</p>
				<pre>' . $mailSrc . '</pre><hr />';

		// Send mail
		} elseif ($mailer->Mailer) { 
			if ($mailer->Send()) {
				if (($this->localconf['copyToUser'] == 1) || ($this->localconf['copyToUser'] == 2 && !empty($this->piVars['checkbox']['user_copy']))) {
					// If user email is valid - Compile message
					if ($this->check_email($this->piVars['text']['email'])) {
						// Compile message
						if ($this->localconf['contentType'] == 1) {
							$subpart = $this->cObj->getSubpart($this->template, "###TMAIL_MAIL_USERCOPY###");
							$altBody = $this->cObj->substituteMarkerArray($subpart, $formMarkers);
							$subpart = $this->cObj->getSubpart($this->template, "###TMAIL_MAIL_USERCOPY_HTML###");
							$body = $this->cObj->substituteMarkerArray($subpart, array_map(array('tx_pilmailform_pi1', 'format_HTML_mail'), $formMarkers));
						} else {
							$subpart = $this->cObj->getSubpart($this->template, "###TMAIL_MAIL_USERCOPY###");
							$body = $this->cObj->substituteMarkerArray($subpart, $formMarkers);
							$altBody = null;
						}
						// Remove any left over markers
						$body = preg_replace("/###.*?###/", '', $body);
						$altBody = preg_replace("/###.*?###/", '', $altBody);
						// Set mailer options
						$mailer->clearAllRecipients();
						$mailer->addAddress($this->piVars['text']['email']);
						// Set header from recipient in user copy
						if ($this->localconf['discloseRecipient'] == 1) {
							$mailer->From = $To[0];
							$mailer->FromName = $To[0];
							$mailer->AddReplyTo($To[0], '');
						}
						$mailer->Subject = (empty($this->localconf['userCopySubject'])) ? $Subject : $this->cObj->substituteMarkerArray($this->localconf['userCopySubject'], $formMarkers);
						$mailer->Body = $body;
						$mailer->AltBody = $altBody;
						$mailer->ClearAttachments();
						$mailer->Send();
					}
				}
				$subpart = $this->cObj->getSubpart($this->template, "###TMAIL_THANKS###");
				$content = $this->cObj->substituteMarkerArray($subpart, $formMarkers);
			} else {
				$this->error_handler('Mail send error: ' . $mailer->ErrorInfo);
				$subpart = $this->cObj->getSubpart($this->template, "###TMAIL_ERROR###");
				$content = $this->cObj->substituteMarkerArray($subpart, $formMarkers);
				$content .= $mailer->ErrorInfo;
			}
		} else {
			$subpart = $this->cObj->getSubpart($this->template, "###TMAIL_THANKS###");
			$content = $this->cObj->substituteMarkerArray($subpart, $formMarkers);		    
		}
		// Clean up uploaded files
		foreach ($files as $v) {
			t3lib_div::unlink_tempfile($v['t3_tmp_name']);
		}

		return $content;
	}

    /**
     * Get default values
     */
    function get_defaultValues(&$markers) {
		
		// Get values from eg. GET parameters
		$fake = array();
		foreach ($this->piVars as $type => $field) {
			$markers = array_merge($markers, $this->get_marker($fake, $fake, $type, $field));
		}

		$queries = array();
		// Get Default Values config
		$DVConfig = $this->parse_config($this->localconf['defaultValues']);
		foreach ($DVConfig as $dvc) {
			switch ($dvc['method']) {
				case 'plain':
					$dvc['pparm'] = htmlspecialchars($dvc['pparm']);
					if ($dvc['proc'] == 'text') {
						$markers['###' . strtoupper($dvc['fields']) . '###'] = $dvc['pparm'];
					} elseif ($dvc['proc'] == 'mark') {
						if (array_key_exists('###' . strtoupper($dvc['pparm']) . '###', $markers)) {
							$markers['###' . strtoupper($dvc['fields']) . '###'] = $markers['###' . strtoupper($dvc['pparm']) . '###'];
						}                        	
					} elseif ($dvc['proc'] == 'lang') {
						$markers['###' . strtoupper($dvc['fields']) . '###'] = $this->pi_getLL($dvc['pparm'], 'UNKNOWN LANGUAGE MARKER' , true);
					} 
				break;
				case 'db':
					$subparts = $this->trim_explode(':', $dvc['pparm'], true, 3);
					$this->array_append($this->trim_explode(',', $subparts[1]), $queries[$dvc['proc']][$subparts[0]][$subparts[2]]['fields']);
					$this->array_append($this->trim_explode(',', $dvc['fields']), $queries[$dvc['proc']][$subparts[0]][$subparts[2]]['markers']);
				break;
				default:
			}
		}
        $this->execQueries($queries, $markers);
    }

	/**
	* Get mailform subpart template and substitute markers
	* @return string 	The mailform subpart with substituted markers
	*/
	function get_mailform() {

		// Get template subpart
		$subpart = $this->cObj->getSubpart($this->template, "###TMAIL_FORM###");

		// Inject multiple recipients markers into template subpart
		if ($this->localconf['typeofRecipient'] == 1) {
			$subpart = $this->cObj->substituteMarkerArray($subpart, array('###MULTI_RECIPIENT_OPTIONS###' => $this->get_multi_recipient()));
		}

		// Initialize variables
		$markers = array();
		$subMarkers = array();
		$this->load_static_markers($markers);
		
		// Set values if form is submitted
		if (isset($this->piVars['submit'])) {
			
			// Set missing fields (chechboxes etc.) value to empty string, so that they may be validated
			preg_match_all('/tx_pilmailform_pi1\[(.+)\]\[(.+)\]/Ui', $subpart, $missingFields);
			for ($i = 0; $i < count($missingFields[0]); $i++) {
				if (!isset($this->piVars[$missingFields[1][$i]][$missingFields[2][$i]])) {
					$this->piVars[$missingFields[1][$i]][$missingFields[2][$i]] = '';
				}
			}

			// Merge uploaded files info into piVars
			$files = $this->get_files(true);
			foreach ($files as $k => $v) {
				$this->piVars['file'][$k] = $v['type'];
			}

			// Load error handling array
			$requiredFields = array(); 
			$requiredValues = array();
			$tmp = array();
			$lines = $this->trim_explode("\n", $this->localconf['requiredValues'], true);

			// Inject multi_recipients into required fields array
			if ($this->localconf['typeofRecipient'] == 1) {
				$requiredFields['multi_recipient'] = true;
				$requiredValues['multi_recipient'][0]['eval'] = 'notEmpty';
			}

			// Get Required Values config
			$RVConfig = $this->parse_config($this->localconf['requiredValues']);
			foreach ($RVConfig as $rvc) {
				$rvc['fields'] = strtolower(str_replace('_VAL', '', $rvc['fields']));
				$requiredFields[$rvc['fields']] = true;
				$requiredValues[$rvc['fields']][count($tmp[$rvc['fields']])]['eval'] = $rvc['method'];
				$requiredValues[$rvc['fields']][count($tmp[$rvc['fields']])]['exp'] = $rvc['mparm'];
				$requiredValues[$rvc['fields']][count($tmp[$rvc['fields']])]['ekey'] = $rvc['proc'];
				$requiredValues[$rvc['fields']][count($tmp[$rvc['fields']])]['estr'] = $rvc['pparm'];
				$tmp[$rvc['fields']]++;
			}

			// Assume empty fields and preset error markers
			foreach ($requiredFields as $k => $v) {
				$markers['###' . strtoupper($k) . '_ERR###'] = $this->localconf['errorSubstitution'];
			}

			// Get value markers and overwrite error markers
			foreach ($this->piVars as $type => $field) {
				$markers = array_merge($markers, $this->get_marker($requiredFields, $requiredValues, $type, $field));
			}
	
			//	Set form in general error mode
			if (count($requiredFields))	{
				$this->errForm = true;
			}

			// If [email] field not empty, email must be valid
			if (!empty($this->piVars['text']['email'])) {
				if (!$this->check_email($this->piVars['text']['email'])) {
					$this->errForm = true;
					$markers['###EMAIL_ERR###'] = $this->localconf['errorSubstitution'];
				} else {
					$subMarkers["###HEADER_EMAIL_INVALID###"] = '';
				}
			} else {
				$subMarkers["###HEADER_EMAIL_INVALID###"] = '';
			}
		// Form not submitted
		} else {
			$this->get_defaultValues($markers);
			$subMarkers["###HEADER_EMAIL_INVALID###"] = '';
		}

		// Set form url
		$markers['###FORM_URL###'] = $this->pi_getPageLink($GLOBALS['TSFE']->id);

		// Set general error marker (errForm)
		$subMarkers["###HEADER_" . ($this->errForm ? 'STD' : 'ERR') . "###"] = '';

		// Substitute markers
		$content = $this->cObj->substituteMarkerArrayCached($subpart, $markers, $subMarkers);
		
		// Remove any left over markers
		$content = preg_replace("/###.*?###/", '', $content);

		return $content;
	}

	/**
	 * Get markers for substitution
	 * @param array 	required fields
	 * @return array 	markers
	 */
	function get_marker(&$requiredFields, &$requiredValues, $type = '', $field = '', $fKey = '', $markers = array()) {
		if (is_array($field)) {
			foreach ($field as $k => $v) {
				if (is_array($v)) {
					$markers = $this->get_marker($requiredFields, $requiredValues, $type, $v, $k, $markers);
				} else {
					
					$k = !empty($fKey) ? $fKey : $k;
					$reqVal = $this->validate_field($k, $v, $requiredValues[$k], $markers);

					switch ($type) {
						case 'text':
							$markers['###' . strtoupper($k) . '_VAL###'] = !empty($v) || strlen($v) >= 1 ? htmlspecialchars($v) : '';
							$markers['###' . strtoupper($k) . '_ERR###'] = $reqVal['is_error'] ? $this->localconf['errorSubstitution'] : '';
							$markers['###' . strtoupper($k) . '_ERR_TXT###'] = $reqVal['is_error'] ? $reqVal['error_str'] : '';
							// Try to detect header injection
							$this->errForm = (preg_match("\r", $v) || preg_match("\n", $v)) ? true : $this->errForm;
						break;
						case 'textarea':
							$markers['###' . strtoupper($k) . '_VAL###'] = !empty($v) || strlen($v) >= 1 ? htmlspecialchars($v) : '';
							$markers['###' . strtoupper($k) . '_ERR###'] = $reqVal['is_error'] ? $this->localconf['errorSubstitution'] : '';
							$markers['###' . strtoupper($k) . '_ERR_TXT###'] = $reqVal['is_error'] ? $reqVal['error_str'] : '';
						break;
						case 'radio':
						case 'checkbox':
							$markers['###' . strtoupper($k) . '_VAL###'] = !empty($v) || strlen($v) >= 1 ? htmlspecialchars($v) : '';
							$markers['###' . strtoupper($k) . '_' . strtoupper(preg_replace('/[^a-z0-9]/i', '_', $v)) . '_VAL###'] = !empty($v) || strlen($v) >= 1 ? 'checked="checked"' : '';
							$markers['###' . strtoupper($k) . '_ERR###'] = ($requiredFields[$k] && (empty($v) || strlen($v) <= 0)) ? $this->localconf['errorSubstitution'] : '';
							$markers['###' . strtoupper($k) . '_ERR_TXT###'] = $reqVal['is_error'] ? $reqVal['error_str'] : '';
						break;
						case 'select':
							$markers['###' . strtoupper($k) . '_VAL###'] = !empty($v) || strlen($v) >= 1 ? htmlspecialchars($v) : '';
							$markers['###' . strtoupper($k) . '_' . strtoupper(preg_replace('/[^a-z0-9]/i', '_', $v)) . '_VAL###'] = !empty($v) || strlen($v) >= 1 ? 'selected="selected"' : '';
							$markers['###' . strtoupper($k) . '_ERR###'] = ($requiredFields[$k] && (empty($v) || strlen($v) <= 0)) ? $this->localconf['errorSubstitution'] : '';
							$markers['###' . strtoupper($k) . '_ERR_TXT###'] = $reqVal['is_error'] ? $reqVal['error_str'] : '';
						break;
						case 'file':
							$markers['###' . strtoupper($k) . '_VAL###'] = !empty($v) || strlen($v) >= 1 ? htmlspecialchars($v) : '';
							$markers['###' . strtoupper($k) . '_ERR###'] = $reqVal['is_error'] ? $this->localconf['errorSubstitution'] : '';
							$markers['###' . strtoupper($k) . '_ERR_TXT###'] = $reqVal['is_error'] ? $reqVal['error_str'] : '';
						break;
						default:
						break;
					}
					// Remove field from required array
					if (isset($requiredFields[$k]) && (!empty($v) || !$reqVal['is_error'])) {
						unset($requiredFields[$k]);
					}
				}
			}
		}
		return $markers;
	}
	
	/**
	 * Validate field
	 */
	function validate_field($fkey, $fval, $reqVal, &$markers) {
		$result = array();
		$result['is_error'] = false;
		$result['error_str'] = '';
		if (is_array($reqVal)) {
			foreach ($reqVal as $k => $v) {
				$v['estr'] = $v['ekey'] == 'lang' ? $this->pi_getLL(trim($v['estr']), 'UNKNOWN LANGUAGE MARKER', true) : $v['estr'];   
				switch ($v['eval']) {
					case 'notEmpty':
						if (empty($fval)) {
							$result['is_error'] = true;
							$result['error_str'] = $v['estr'];
							$this->errForm = true;
							break 2;
						}
					break;
					case 'notEquals':
						if ($fval != $markers['###' . $v['exp'] . '###']) {
							$result['is_error'] = true;
							$result['error_str'] = $v['estr'];
							$this->errForm = true;
							break 2;							
						}
					break;
					case 'inList':
						if ((!empty($v) || strlen($v) >= 0) && !in_array($fval, $this->trim_explode(',', $v['exp'], true))) {
							$result['is_error'] = true;
							$result['error_str'] = $v['estr'];
							$this->errForm = true;
							break 2;
						} 
					break;
					case 'regex':
						if ((!empty($v) || strlen($v) >= 0)) {
							if ($v['exp'][0] == '!') {
								$v['exp'] = substr($v['exp'], 1);
								$eval = !@preg_match($v['exp'], $fval);
							} else {
								$eval = @preg_match($v['exp'], $fval);
							}
							if (!(bool) $eval) {
								$result['is_error'] = true;
								$result['error_str'] = $v['estr'];
								$this->errForm = true;
								break 2;
							}
						}
					break;
					default:
						$result['is_error'] = false;
						$result['error_str'] = ''; 
					break;
				}
			}
		}

		return $result;
	} 


	/*
	 * 
	 * 
	 * DATABASE FUNCTIONALITY
	 * 
	 * 
	 * 
	 */


    /**
     * Execute queries
     * @param array $queries Queries stored as 'methods'=>'tables'=>'where clauses'=>'fields, markers'
     * @param array $markers Previously defined markers
     * @return void  
     */
    function execQueries($queries, &$markers) {
        $sql = array();
        $fn = array();

        // Build access table array        
        $acl = $this->trim_explode(';', $this->extConf['db.']['tables']);
        foreach ($acl as $ai) {
            $tmp = $this->trim_explode(':', $ai, true, 2);
            $aclTable[$tmp[0]] = $this->trim_explode(',', $tmp[1]);
        }
        unset($acl, $ai, $tmp);
       
        // Loop through query types
        foreach ($queries as $type => $queryArr) {
       
            // Check that query type is allowed
            if ($this->extConf['db.']['mode.'][$type] == 0) {
                $this->error_handler('Method is not allowed: ' . $type);
                unset($queries[$type]);
                continue;
            }
       
            // Loop through tables
            foreach ($queryArr as $table => $whereclause) {
                // check if table is allowed
                if (!array_key_exists($table, $aclTable)) {
                    $this->error_handler('Table is not allowed: ' . $table);
                    unset($queryArr[$type][$table]);
                    continue;
                }

                // Loop through where clauses
                foreach ($whereclause as $wc => $parms) {
                    
                    // Get fields/types for the table in question for WHERE clause parsing and validation                        
                    if (!is_array($fn[$table])) {
                        $res = $GLOBALS['TYPO3_DB']->admin_get_fields($table);
                        if ($res) {
                            while (list(, $row) = each($res)) {
                                $fn[$table][$row['Field']] = $row['Type'];
                            }
                        } else {
                            $fn[$table][] = array();
                        }
                    }
                    // Parse and validate WHERE clause
                    if (!$wcParsed = $this->parseWhereClause($wc, $table, $fn, $markers)) {
                        unset($queries[$type][$table][$wc]);
                        continue;                       
                    }
                    // Check that numbers of fields/values are consistent
                    if (count($parms['fields']) != count($parms['markers'])) {
                        $this->error_handler('Inconstistent number of fields/values: ' . implode(',', $parms['fields']) . ' => ' . implode(',', $parms['markers']));
                        unset($queryArr[$table][$wc]);
                        continue;
                    }
                    // Check that we only have allowed fields
                    if (count($tmp = array_diff($parms['fields'], $aclTable[$table])) > 0) {
                        $this->error_handler('Field(s) are not allowed: ' . $table . ': ' .  implode(',', $tmp));
                        unset($queryArr[$table][$wc]);
                        continue;
                    }
                    // SELECT queries
                    if ($type == 'get') {
                        if ($res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(implode(',', $parms['fields']), $table, $wcParsed)) {
                            while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_row($res)) {
                                for ($i = 0; $i < count($row); $i++) {
                                    $markers['###' . strtoupper($parms['markers'][$i]) . '###'] = $row[$i];
                                }
                            }
                        }
                        if ($this->localconf['testmode'] == 1) {
                        	$sql[] = $this->query2sql('SELECT', $table, $parms, $wcParsed);
                        }
                    // UPDATE/INSERT queries
                    } else {
                        // Make sure we don't overwrite any uid in typo3 controlled tables
                        if (isset($GLOBALS['TCA'][$table]) && in_array('uid', $parms['fields'])) {
                            $this->error_handler('The field \'uid\' of table ' . $table . ' is proteced and may not be set or altered');
                            unset($queryArr[$table][$wc]);
                            continue;
                        }
                        // Prepare field=>value array
                        $fv = array();
                        for ($i = 0; $i < count($parms['markers']); $i++) {
                            if (array_key_exists('###' . $parms['markers'][$i] . '###', $markers)) {
                                $fv[$parms['fields'][$i]] = $markers['###' . $parms['markers'][$i] . '###'];
                            } else {
                                $fv[$parms['fields'][$i]] = $parms['markers'][$i];
                            }
                        }
                        // If is typo3 controlled table, set some default fields/values
                        if (isset($GLOBALS['TCA'][$table])) {
                        	$fv['tstamp'] = time();
                        }
                        // Execute queries
                        if ($type == 'new') {
                            if ($this->localconf['testmode'] == 1) {
                            	$sql[] = $this->query2sql('INSERT', $table, $parms, $wcParsed, $fv);
                            } else {
                            	$GLOBALS['TYPO3_DB']->exec_INSERTquery($table, $fv);
                            }
                        } elseif ($type == 'add') {
                        	// Check weather record(s) exists to determine wether to INSERT or UPDATE
                        	if ($res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $table, $wcParsed)) {
                        		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {
                                    if ($this->localconf['testmode'] == 1) {
                                    	$sql[] = $this->query2sql('UPDATE', $table, $parms, $wcParsed, $fv);
                                    } else {
                                    	$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, $wcParsed, $fv);
                                    }
                                } else {
                                    if ($this->localconf['testmode'] == 1) {
                                    	$sql[] = $this->query2sql('INSERT', $table, $parms, $wcParsed, $fv);
                                    } else {
                                    	$GLOBALS['TYPO3_DB']->exec_INSERTquery($table, $fv);
                                    }
                                }
                            }
                        } elseif ($type == 'update') {
                            if ($this->localconf['testmode'] == 1) {
                            	$sql[] = $this->query2sql('UPDATE', $table, $parms, $wcParsed, $fv);
                            } else {
                            	$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, $wcParsed, $fv);
                            }
                        }
                    }
                }
            }
        }
        return $sql;
    }
    
    /**
     * Parse and validate SQL WHERE clause
     * @param string $wc SQL WHERE clause
     * @param string $table The database table in question
     * @param array $fn Array of valid field names pr. table
     * @return mixed Returns a parsed an validated where clause - if where clause not valid, false is returned
     */
    function parseWhereClause($wc, $tbl, &$fn, &$markers) {
        $eval = false;
        // Construct a never true where clause, since '*' or '' is only valid for INSERT, for which a where clause isn't used. Thus the query will fail for other query types keeping users data safe.
        if ($wc[0] == '*') {
        	$parts[0] = 0;
            $parts[1] = 1;
            $eval = true;
        } else {
            $parts = $this->trim_explode('=', $wc);
            if (count($parts) == 2) {
                // Left side argument has to be the field name
                if (array_key_exists($parts[0], $fn[$tbl])) {
                	if (array_key_exists('###' . $parts[1] . '###', $markers)) {
                		$parts[1] = $markers['###' . $parts[1] . '###'];
                	}
                    // Check values against field type since MySQL < 5.x does not support strict sql_mode and strings are cast to zero
                    if (preg_match('/^(tinyint|smallint|int|mediumint|bigint|float|double|decimal|numeric)/i', $fn[$tbl][$parts[0]]) && !is_numeric($parts[1])) {
                        $this->error_handler('Field type/value mismatch in where clause. (table: ' . $tbl . ', field: ' . $parts[0] .  ', type: ' . $fn[$tbl][$parts[0]] . ', value: ' . $parts[1] . ')');
                    } else {
                        $parts[1] = $GLOBALS['TYPO3_DB']->fullQuoteStr($parts[1], $tbl);
                        $eval = true;
                    }
                } else {
                    $this->error_handler('WHERE clause parse error - Field does not exists or field name has to be on the left side of the expression. (table: ' . $tbl . ', expression: \'' . $parts[0] . '=' . $parts[1] . '\')');
                }
            } else {
                $this->error_handler('WHERE clause parse error - wrong syntax. (table: ' . $tbl . ', expression: \'' . $wc . '\')');
            }
        }
        return ($eval) ? implode('=', $parts) : $eval;
    }
    
    /**
     * Convert queries array to SQL for debugging.
     * @param string $type SQL query type (SELECT|INSERT|UPDATE)
     * @param string $table The database table  
     * @param array $param Array of fields/markers (two-dimensional)
     * @param string $wc SQL WHERE clause
     * @param array $fv Field/values array as field=>value pairs
     * @return string The query SQL
     */
    function query2sql ($type, $table, $parms, $wc, $fv = array()) {
        switch ($type) {
            case 'SELECT':
                $query = 'SELECT ' . implode(',', $parms['fields']) . ' FROM ' . $table . ' WHERE ' . $wc . ';';
            break;
            case 'INSERT':
                $keys = array_keys($fv);
                $query = 'INSERT INTO ' . $table . ' (' . implode(',', $keys) . ') VALUES (\'' . implode('\',\'', $fv) . '\');';
            break;
            case 'UPDATE':
                $set = array();
                foreach ($fv as $f => $v) {
                    $set[] = $f . '=\'' . $v . '\'';
                }
                $query = 'UPDATE ' . $table . ' SET ' . implode(',', $set) . ' WHERE ' . $wc . ';';
            break;
            default:
                $query = '';
        }
        return $query;
    }

    /*
     * 
     * 
     * 
     * HELPER FUNCTIONS AND UTILS
     * 
     * 
     * 
     */
    
	/**
	 * Get options for multiple choise recipients selectbox
	 * @return string 	The options
	 */
	function get_multi_recipient() {
		$opt = '';
		$lines = $this->trim_explode("\n", $this->localconf['dynamicRecipient'], true);
		for ($i = 0; $i < count($lines); $i++) {
			$items = $this->trim_explode(';', $lines[$i], true);
			if (count($items) >= 2) {
				$opt .= '<option value="' . ($i + 1) . '" ###MULTI_RECIPIENT_' . ($i + 1) . '_VAL###>' . $items[0] . '</option>';
			} else {
				$opt .= '<option value="">' . $items[0] . '</option>';
			}
		}
		return $opt;
	}

	/**
	 * File upload helper function
	 * @param bool $includeErrors Wether to include errors in file upload
	 * @return array	
	 */
	function get_files($includeErrors = false) {
		// Set default variables and values
		$files = array();
		// Rearrange $_FILES array to a more logical structure
		if (count($_FILES) >= 1) {
			foreach ($_FILES['tx_pilmailform_pi1'] as $k => $v) {
				foreach($v['file'] as $p => $q) {
					if (!$includeErrors) {
						if ($_FILES['tx_pilmailform_pi1']['error']['file'][$p] == 0) {
							if (!empty($q)) {
								$files[$p][$k] = $q;
							}
						}
					} else {
						$files[$p][$k] = $q;
					}
				}
			}
		}
		return $files;
	}

 	/**
	 * UTF-8 charset detection helper function
	 * @see http://w3.org/International/questions/qa-forms-utf-8.html
	 * @param string
	 * @return bool true if string is utf-8, otherwise false
	 */
	function is_utf8($str) {
		//return (preg_match('/^([\x00-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xec][\x80-\xbf]{2}|\xed[\x80-\x9f][\x80-\xbf]|[\xee-\xef][\x80-\xbf]{2}|f0[\x90-\xbf][\x80-\xbf]{2}|[\xf1-\xf3][\x80-\xbf]{3}|\xf4[\x80-\x8f][\x80-\xbf]{2})*$/', $str) === 1);
		return false;
	}

	/**
	 * Validate an email address using regular expressions
	 * @param string 	email address
	 * @return bool 	true if email is valid - otherwise false
	 */
	function check_email($email) {
		return t3lib_div::validEmail($email);
	}

	/**
	 * HTML mail helper function
	 * @param string form field 
	 * @return string HTML formatted form field
	 */
	function format_HTML_mail($str) {
		$str = htmlspecialchars($str);
		$str = nl2br($str);
		$str = str_replace("\r", '', $str);
		$str = str_replace("\n", '', $str);
		return $str; 	
	}

    /**
     * Helper function - appends items from one array to another
     * @param mixed $subj If variable is an array, the items will be appended one by one
     * @param array &$arr The array to append items to
     * @return int Returns the new number of elements in the array.
     */
    function array_append($subj, &$arr) {
        $arr = is_array($arr) ? $arr : array();
        if (is_array($subj)) {
            foreach ($subj as $s) {
                array_push($arr, $s);
            }
        } else { 
            array_push($arr, $subj);
        }
        return count($arr);
    }

    /**
     * Helper function: explode() + trim() combined
     * @param string $dlm delimiter
     * @param string $str string to deal with
     * @param int $max Max number of elements in array
     * @param bool $rmEmpty Remove empty elements from array
     * @return array
     */
    function trim_explode($dlm, $str, $rmEmpty = false, $max = false) {
    	if (empty($dlm)) {
    	    $res = array($str);
    	} else {
	    	$res = is_int($max) ? explode($dlm, $str, $max) : explode($dlm, $str);
	        $res = array_map('trim', $res);
	        if (is_array($res) && $rmEmpty) {
	        	while (list($k) = each($res)) {
	                if (empty($res[$k])) {
	                    unset($res[$k]);
	                }
	            }
	            $res = array_values($res);
	        }
    	}
        return $res;
    }

    /**
     * Error handling helper function
     * @param string $str Error message
     */
    function error_handler($str) {
        t3lib_div::sysLog($str, $this->extKey);
    }
    
    /**
     * Parse TMailform configuration
     * @param string $config configuration
     * @return array parsed config
     */
    function parse_config($config) {
		$res = array();
		$config = $this->trim_explode("\n", $config, true);
	    for ($i = 0; $i < count($config); $i++) {
	        if ($config[$i][0] != '#' && !empty($config[$i])) {
	            if (preg_match('/^([a-zA-Z0-9_\s,]+);([a-zA-Z\s]+):?(.*)?;([a-z\s,]+)?:?(.+)?$/', $config[$i], $matches)) {
	            	$matches = array_map('trim', $matches);
	            	$res[$i]['fields'] = $matches[1];
	            	$res[$i]['method'] = $matches[2];
	            	$res[$i]['mparm'] = $matches[3];
	            	$res[$i]['proc'] = $matches[4];
	            	$res[$i]['pparm'] = $matches[5];
	            } else {
	                $this->error_handler('Failed to parse config line: ' . $config[$i]);
	            }
	        }
	    }
		return $res;
    }
    
    /*
     * 
     * 
     * 
     * INITIALIZATION
     * 
     * 
     * 
     */
    
    /**
     * Loads static markers
     * @param array $markers marker array
     * @return void
     */
    function load_static_markers(&$markers) {
        // Set some general info markers
        $markers['###_DATE###'] = strftime($this->localconf['dateMarkerFormat'], time()); // Current date in the format YY-MM-DD (allows natural sorting in the mail client)
        $markers['###_TIME###'] = strftime($this->localconf['timeMarkerFormat'], time()); // Current time in the format HH:MM
        $markers['###_SITE###'] = $_SERVER['SERVER_NAME']; // Site name
        $markers['###_PATH###'] = $_SERVER['REQUEST_URI'];; // Page path
        $markers['###_PAGE###'] = $GLOBALS['TSFE']->page['title']; // Page title
        $markers['###_PID###'] = $GLOBALS['TSFE']->id; // Page ID
        $markers['###_IP###'] = $_SERVER['REMOTE_ADDR']; // Client IP
        // Set FE-user markers
        if (is_array($GLOBALS["TSFE"]->fe_user->user)) {
            foreach ($GLOBALS["TSFE"]->fe_user->user as $k => $v) {
                $markers['###_FEUSER_' . strtoupper($k) . '###'] = $v;
            }
        }
        // Set LL markers
        if ($this->localconf['useLL'] == 1) {
	        if (array_key_exists($this->LLkey, $this->LOCAL_LANG)) {
	        	foreach ($this->LOCAL_LANG[$this->LLkey] as $k => $v) {
					$markers['###_LL_' . strtoupper($k) . '###'] = $this->pi_getLL($k, 'UNKNOWN LANGUAGE MARKER', true);
	        	}
	        }
        }
        
    }
    
	/**
	 * Initialize flexform values and template
	 * @return void
	 */
	function init(){
		// Set default variables and values
		$err = array();
		$eAll = false;

		// Get flexform values
		$this->pi_initPIflexForm();
		$piFlexForm = $this->cObj->data['pi_flexform'];
		foreach ($piFlexForm['data'] as $sheet => $data) {
			foreach ($data as $lang => $value) {
				foreach ($value as $key => $val) {
					$this->localconf[$key] = !empty($this->localconf[$key]) ? $this->localconf[$key] : trim($this->pi_getFFvalue($piFlexForm, $key, $sheet));
				}
			}
		}

		// Check template config
		$this->template = trim($this->cObj->fileResource($this->localconf['template']));
		if ($GLOBALS['TSFE']->renderCharset == 'utf-8' && !$this->is_utf8($this->template)) {
			$localCharset = !empty($GLOBALS['TSFE']->csConvObj->charSetArray[$this->LLkey]) ?  $GLOBALS['TSFE']->csConvObj->charSetArray[$this->LLkey] : 'iso-8859-1';
			$this->template = trim($GLOBALS['TSFE']->csConvObj->utf8_encode($this->template, $localCharset));
		}
		if (empty($this->template)) {
			$err[] = 'No template defined (Tab: General settings).';
			$eAll = true;
		}

		// Check language settings
		if ($this->localconf['useLL'] == 1) {
			$this->LOCAL_LANG = t3lib_div::readLLfile($this->localconf['LLFile'], $this->LLkey);
			if (count($this->LOCAL_LANG) <= 0) {
				$err[] = 'Multiple language support enabled, but locallang file not found';
				$eAll = true;
			}
		}

		// Check static recipient config
		if ($this->localconf['typeofRecipient'] == 0 && empty($this->localconf['staticRecipient'])) {
			$err[] = 'Config is set to use static recipient, but no static recipient is defined (Tab: General settings).';
			$eAll = true;
		} elseif ($this->localconf['typeofRecipient'] == 0 && !$this->check_email($this->localconf['staticRecipient'])) {
			$lines = $this->trim_explode("\n", $this->localconf['staticRecipient'], true);
			for ($i = 0; $i < count($lines); $i++) {
				if (!$this->check_email($lines[$i])) {
					$err[] = 'At least one static recipient email address is not valid (Tab: General settings).';
					$eAll = true;
					break;
				}
			}
		}

		// Check dynamic recipient
		if ($this->localconf['typeofRecipient'] == 1) {
			$lines = $this->trim_explode("\n", $this->localconf['dynamicRecipient'], true);
			for ($i = 0; $i < count($lines); $i++) {
				$items = $this->trim_explode(';', $lines[$i], true);
				if (count($items) >= 2) {
					if (!$this->check_email($items[1])) {
						$err[] = 'At least one dynamic recipient email address is not valid (Tab: General settings).';
						$eAll = true;
						break;
					}
				}
			}
		}

		// Check From header config
		if ($this->localconf['overrideFromHeader'] == 0 && (empty($this->localconf['fromName']) || empty($this->localconf['fromMail']))) {
			$err[] = 'Mail headers "From:" not defined and user override is switched off (Tab: Mail headers).';
			$eAll = true;
		}

		// Check Reply-To header config
		if ($this->localconf['overrideReplyToHeader'] == 0 && (empty($this->localconf['replyToName']) || empty($this->localconf['replyToMail']))) {
			$err[] = 'Mail headers "Reply-To:" not defined and user override is switched off (Tab: Mail headers).';
			$eAll = true;
		}

		// Check if an email can be set in "From:" and "Reply-To:" headers
		if (!strpos($this->cObj->getSubpart($this->template, '###TMAIL_FORM###'), '[text][email]') && ($this->localconf['overrideFromHeader'] != 0 || $this->localconf['overrideReplyToHeader'] != 0)) {
			$err[] = 'Either override "From:" or "Reply-To:" headers with [text][email] form field are switched on, but no [text][email] found in template TMAIL_FORM subpart (Template file, Tab: Mail headers).';
			$eAll = true;			
		}

		// Check Cc header email addresses
		$lines = $this->trim_explode("\n", $this->localconf['Cc'], true);
		for ($i = 0; $i < count($lines); $i++) {
			if (!$this->check_email($lines[$i])) {
				$err[] = 'At least one "Cc:" email address is not valid (Tab: Mail headers).';
				$eAll = true;
				break;
			}
		}

		// Check Bcc header email addresses
		$lines = $this->trim_explode("\n", $this->localconf['Bcc'], true);
		for ($i = 0; $i < count($lines); $i++) {
			if (!$this->check_email($lines[$i])) {
				$err[] = 'At least one "Bcc:" email address is not valid (Tab: Mail headers).';
				$eAll = true;
				break;
			}
		}

		// Check SMTP auth settings
		if ($this->localconf['smtpAuth'] == 1 && ($this->localconf['smtpUser'] == '' || $this->localconf['smtpPasswd'] == '')) {
			$err[] = 'SMTP authentification is chosen, but either username or password is empty (Tab: SMTP settings).';
			$eAll = true;			
		}

		if ($eAll) {
			$this->errStr = '<b>TMailform configuration error(s):</b><br /><br />- ' . implode('<br />- ', $err);
		}

        $this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['pil_mailform']);

		return !$eAll;
	}

}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/pil_mailform/pi1/class.tx_pilmailform_pi1.php"]) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/pil_mailform/pi1/class.tx_pilmailform_pi1.php"]);
}

?>
