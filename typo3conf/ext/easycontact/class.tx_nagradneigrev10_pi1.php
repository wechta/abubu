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
 * Plugin 'FB nagradne igre v7' for the 'nagradneigrev7' extension.
 *
 * @author	Peter Wechtersbach <peter@naprej.net>
 * @package	TYPO3
 * @subpackage	tx_nagradneigrev7
 */
class tx_nagradneigrev10_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_nagradneigrev10_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_nagradneigrev10_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'nagradneigrev10';	// The extension key.
	var $pi_checkCHash = true;
	var $fbUser;
	var $fbMe;
	var $template;
	var $facebook;
	var $step;
	var $user;
	var $requestid;
	
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
		$this->init($this->conf);
		//t3lib_div::debug($this->conf);
		//t3lib_div::debug($_REQUEST,'$_REQUEST');
		//if we got from invite, redirect to fan page...
		//$atmUrl = $this->curPageURL();
		//t3lib_div::debug($_SERVER,$_SERVER);
		
		//t3lib_utility_Debug::debug($this->conf);
		
		$this->facebook = new Facebook(array(
		  'appId'  => $this->conf['appid'],
		  'secret' => $this->conf['secret'],
		));
		$this->template = $this->cObj->fileResource($this->conf['templateFile']);
		
		//ce smo povabljeni preko requesta ga bomo enkrat hotli zbrisat
		if($_REQUEST['requestid']){
			$this->requestid = $_REQUEST['requestid'];
			//t3lib_utility_Debug::debug($this->requestid);
		}
		
		
		if($_REQUEST['request_ids'] || strpos($_SERVER['HTTP_REFERER'], 'apps.facebook.com')){
			$GLOBALS['TSFE']->pSetup['headerData.']['98'] = 'TEXT';
			$GLOBALS['TSFE']->pSetup['headerData.']['98.']['value'] = '<script type="text/javascript">top.location.href = "'.$this->conf['frameApp'].'";</script>';
			return '';
		}
		
		
		if($this->conf['cssFile']){
			$GLOBALS['TSFE']->pSetup['headerData.']['99'] = 'TEXT';
			$GLOBALS['TSFE']->pSetup['headerData.']['99.']['value'] = '<link rel="stylesheet" href="'.$this->conf['cssFile'].'" type="text/css" />
			<html xmlns:fb="http://www.facebook.com/2008/fbml">
			<script src="https://connect.facebook.net/en_US/all.js"></script>
			';
		}

		
		$decodedSignedRequest = $this->parse_signed_request($_REQUEST['signed_request'], $this->conf['secret']);
		//t3lib_utility_Debug::debug($decodedSignedRequest,'decodedSignedRequest');
		//t3lib_utility_Debug::debug($this->piVars,'$this->piVars');

		//ce ni lijka vrnemo slikco za like
		if(!$decodedSignedRequest['page']['liked'] && !$this->piVars['step']){
			
			$GLOBALS['TSFE']->pSetup['headerData.']['97'] = 'TEXT';
			$GLOBALS['TSFE']->pSetup['headerData.']['97.']['value'] = '
				<script type="text/javascript">
				window.fbAsyncInit = function() {
					FB.init( { appId: "", status: true, cookie: true, xfbml: true} );
					//FB.Canvas.setAutoGrow(7);
					FB.Canvas.scrollTo(0,0);
					FB.Canvas.setSize({height:600});
					setTimeout("FB.Canvas.setAutoGrow()",500);
					$(window).load(function(){
						FB.Canvas.setSize();
					});
				};
			</script>
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
			return '<img src="/uploads/tx_nagradneigre/'.$this->conf['image_nolike'].'" />';
		}
		
		//probamo najti userja
		
		$user = $this->facebook->getUser();
		
		//t3lib_div::debug($user,'aaa');
		if(!$user){
			$templateConfirm = $this->cObj->getSubpart($this->template, '###CONNFIRMAPP###');
			$singleMark['###FB_ID###'] = $this->conf['appid'];
			$singleMark['###FRAME_APP###'] = $this->conf['frameApp'];
			if($this->conf['splosni_pogoji']){
				$singleMark['###POGOJI###'] ='http://fbapps.promplac.si/uploads/tx_nagradneigre/'.$this->conf['splosni_pogoji'];
			}else{
				$singleMark['###POGOJI###'] =$this->conf['splosni_pogoji_link'];
			}
			return $this->cObj->substituteMarkerArrayCached($templateConfirm, $singleMark, $multiMark, array());
		}
		
		if ($user) {
		  try {
		    $user_profile = $this->facebook->api('/me');
		    $this->deleteAppRequests($this->facebook->api('/me/apprequests'));
		  } catch (FacebookApiException $e) {
		  	//t3lib_utility_Debug::debug($e,'xxxxx');
		    $templateConfirm = $this->cObj->getSubpart($this->template, '###CONNFIRMAPP###');
				$singleMark['###FB_ID###'] = $this->conf['appid'];
				$singleMark['###FRAME_APP###'] = $this->conf['frameApp'];
				if($this->conf['splosni_pogoji']){
					$singleMark['###POGOJI###'] ='http://fbapps.promplac.si/uploads/tx_nagradneigre/'.$this->conf['splosni_pogoji'];
				}else{
					$singleMark['###POGOJI###'] =$this->conf['splosni_pogoji_link'];
				}
				return $this->cObj->substituteMarkerArrayCached($templateConfirm, $singleMark, $multiMark, array());
		  }
		}
		
		
		
		//check ce je user v bazi!
		if($user_profile){
			$this->user = $this->checkUserDatabase($user_profile);
		}
		
		if($this->piVars['step']==1 || !$this->piVars['step']){
			$template = $this->cObj->getSubpart($this->template, '###STEP1###');
			$singleMark['###FB_ID###'] = $this->conf['appid'];
			$singleMark['###FRAME_APP###'] = $this->conf['frameApp'];
			$singleMark['###POINTS###'] =$this->user['points'];
			if($this->conf['splosni_pogoji']){
				$singleMark['###POGOJI###'] ='http://fbapps.promplac.si/uploads/tx_nagradneigre/'.$this->conf['splosni_pogoji'];
			}else{
				$singleMark['###POGOJI###'] =$this->conf['splosni_pogoji_link'];
			}
			$singleMark['###STEP2_HREF###'] =$this->pi_linkTP_keepPIvars_url(array('step' => 2));
			$singleMark['###STEP3_HREF###'] =$this->pi_linkTP_keepPIvars_url(array('step' => 3));
			$content = $this->cObj->substituteMarkerArrayCached($template, $singleMark, $multiMark, array());
		}
		
		if($this->piVars['step']==2){
			//t3lib_utility_Debug::debug($this->checkInvitedEmails());
			$allEmails = $this->checkInvitedEmails();
			$numEmails = (count(explode(',',$allEmails)))-1;

			if($allEmails && $numEmails > 0){
				$fields_values['emails'] = $this->user['emails'].$allEmails;
				$fields_values['points'] = $this->user['points']+($numEmails*$this->conf['invitefriendpointsemail']);

				$this->updateUser($this->user['uid'], $fields_values);
				
				$allEmailsExploded = explode(',',$allEmails);
				//posljemo emaile
				foreach($allEmailsExploded as $email){
					if(t3lib_div::validEmail($email)){
						$this->sendEmail($this->user['name'],$this->user['surname'], $this->user['email'], $email);
					}
				}
				
			}else{$fields_values['points'] = $this->user['points'];}
			
			
			
			if($this->conf['splosni_pogoji']){
				$singleMark['###POGOJI###'] ='http://fbapps.promplac.si/uploads/tx_nagradneigre/'.$this->conf['splosni_pogoji'];
			}else{
				$singleMark['###POGOJI###'] =$this->conf['splosni_pogoji_link'];
			}
			$singleMark['###FB_ID###'] = $this->conf['appid'];
			$singleMark['###FBWALL_TITLE###'] = $this->conf['wallNaslov'];
			$singleMark['###FBWALL_LINK###'] = $this->conf['frameApp'];
			$singleMark['###FBWALL_IMAGE###'] = 'http://fbapps.promplac.si/uploads/tx_nagradneigre/'.$this->conf['image_wall'];
			//$singleMark['###FBWALL_IMAGE###'] = 'http://naprej.net/fileadmin/template/skupno/logo.png';
			$singleMark['###FBWALL_CAPTION###'] = $this->conf['wallPodnaslov'];
			$singleMark['###APP_URL###'] = $this->conf['linkApp'];
			$singleMark['###POINTS###'] =$fields_values['points'];
			$singleMark['###STEP3_HREF###'] =$this->pi_linkTP_keepPIvars_url(array('step' => 3));
			$template = $this->cObj->getSubpart($this->template, '###STEP2###');
			$content = $this->cObj->substituteMarkerArrayCached($template, $singleMark, $multiMark, array());
		}
		if($this->piVars['step']==3){
			$template = $this->cObj->getSubpart($this->template, '###STEP3###');
			$templateInvitedFriend = $this->cObj->getSubpart($this->template, '###INVITED_FRIEND###');
			
			//t3lib_utility_Debug::debug($this->piVars,'aaa');
			//ce je povabljal frende - EMAIL
			$allEmails = $this->checkInvitedEmails();
			$numEmails = (count(explode(',',$allEmails)))-1;
			if($allEmails && $numEmails > 0){
				$fields_values['emails'] = $this->user['emails'].$allEmails;
				$fields_values['points'] = $this->user['points']+($numEmails*$this->conf['invitefriendpointsemail']);
				$this->updateUser($this->user['uid'], $fields_values);
				
				$allEmailsExploded = explode(',',$allEmails);
				//posljemo emaile
				foreach($allEmailsExploded as $email){
					if(t3lib_div::validEmail($email)){
						$this->sendEmail($this->user['name'],$this->user['surname'], $this->user['email'], $email);
					}
				}
			}
			
			//ce je povabljal frende - REQUEST
			elseif($this->piVars['invite_ids']){
				$invited = explode(',',$this->piVars['invite_ids']);
				
				foreach($invited as $one){
					if($one){
						$pos = strpos($this->user['friendids'], $one);
						if($pos === false){
							$points = $points+$this->conf['invitefriendpointsfb'];
							$friendids .= $one.',';
						}
					}
				}
				$fields_values['friendinvite'] = 1;
				$fields_values['friendids'] = $this->user['friendids'].$friendids;
				//ne pristevamo vec tock samo za povabila!
				//$fields_values['points'] = $this->user['points']+$points;
				$fields_values['points'] = $this->user['points'];
				$this->updateUser($this->user['uid'], $fields_values);
			}
			
			//ce je preskocil korak
			else{$fields_values['points'] = $this->user['points'];}
			
			//izpis povabljenih frendov
			$frendi = explode(',',$this->user['friendresponse']);
			foreach($frendi as $frend){
				if($frend){
					$singleMark['###IMGSRC###'] = 'http://graph.facebook.com/'.$frend.'/picture?type=square';
					$out .= $this->cObj->substituteMarkerArrayCached($templateInvitedFriend, $singleMark, $multiMark, array());
					$multiMark['###NOFIRENDS_FRIEND###'] = '';
				}
			}
			
			
			
			$multiMark['###INVITED_FRIEND###'] = $out;
			if($this->conf['splosni_pogoji']){
				$singleMark['###POGOJI###'] ='http://fbapps.promplac.si/uploads/tx_nagradneigre/'.$this->conf['splosni_pogoji'];
			}else{
				$singleMark['###POGOJI###'] =$this->conf['splosni_pogoji_link'];
			}
			$singleMark['###POINTS###'] =$fields_values['points'];
			$singleMark['###FB_ID###'] = $this->conf['appid'];
			$singleMark['###APP_URL###'] = $this->conf['linkApp'];
			$singleMark['###INVITED_IDS###'] = $this->user['friendids'];
			$content = $this->cObj->substituteMarkerArrayCached($template, $singleMark, $multiMark, array());
		}
		

		return $this->pi_wrapInBaseClass($content);
	}
	function checkInvitedEmails(){
		$allEmails = '';
		if(t3lib_div::validEmail($this->piVars['emailfriend1'])){
			$pos = strpos($this->user['emails'], $this->piVars['emailfriend1']);
			if($pos === false){$allEmails = $allEmails.$this->piVars['emailfriend1'].',';}
		}
		if(t3lib_div::validEmail($this->piVars['emailfriend2'])){
			$pos = strpos($this->user['emails'], $this->piVars['emailfriend2']);
			if($pos === false){$allEmails = $allEmails.$this->piVars['emailfriend2'].',';}
		}
		if(t3lib_div::validEmail($this->piVars['emailfriend3'])){
			$pos = strpos($this->user['emails'], $this->piVars['emailfriend3']);
			if($pos === false){$allEmails = $allEmails.$this->piVars['emailfriend3'].',';}
		}
		if(t3lib_div::validEmail($this->piVars['emailfriend4'])){
			$pos = strpos($this->user['emails'], $this->piVars['emailfriend4']);
			if($pos === false){$allEmails = $allEmails.$this->piVars['emailfriend4'].',';}
		}
		if(t3lib_div::validEmail($this->piVars['emailfriend5'])){
			$pos = strpos($this->user['emails'], $this->piVars['emailfriend5']);
			if($pos === false){$allEmails = $allEmails.$this->piVars['emailfriend5'].',';}
		}
		if(t3lib_div::validEmail($this->piVars['emailfriend6'])){
			$pos = strpos($this->user['emails'], $this->piVars['emailfriend6']);
			if($pos === false){$allEmails = $allEmails.$this->piVars['emailfriend6'].',';}
		}
		if(t3lib_div::validEmail($this->piVars['emailfriend7'])){
			$pos = strpos($this->user['emails'], $this->piVars['emailfriend7']);
			if($pos === false){$allEmails = $allEmails.$this->piVars['emailfriend7'].',';}
		}
		if(t3lib_div::validEmail($this->piVars['emailfriend8'])){
			$pos = strpos($this->user['emails'], $this->piVars['emailfriend8']);
			if($pos === false){$allEmails = $allEmails.$this->piVars['emailfriend8'].',';}
		}
		if(t3lib_div::validEmail($this->piVars['emailfriend9'])){
			$pos = strpos($this->user['emails'], $this->piVars['emailfriend9']);
			if($pos === false){$allEmails = $allEmails.$this->piVars['emailfriend9'].',';}
		}
		if(t3lib_div::validEmail($this->piVars['emailfriend10'])){
			$pos = strpos($this->user['emails'], $this->piVars['emailfriend10']);
			if($pos === false){$allEmails = $allEmails.$this->piVars['emailfriend10'].',';}
		}

		return $allEmails;
		
	}
	function deleteAppRequests($all){
		
		foreach($all['data'] as $data){
			$parent = $this->findUserByFBID($data['from']['id']);
			$userData = $this->facebook->api('/'.$data['to']['id']);
			//za vsak slucaj ceknemo ce ima ze ta response med svojimi povabili..
			$pos = strstr($parent['friendresponse'],$data['to']['id']);
			if(!$pos){
				$allParentRequest = $parent['friendresponse'].$data['to']['id'].',';
				$fields_values['friendresponse'] = $allParentRequest;
				$fields_values['points'] = $parent['points']+$this->conf['invitefriendpointsfb'];
				$this->updateUser($parent['uid'], $fields_values);
			}
			//posljemo email!
			//$this->sendEmail($userData['first_name'],$userData['last_name'],$userData['email'], $parent['fbusername'].'@facebook.com');
			
			//sent notification
			$text = '@['.$data['to']['id'].'] '.$this->conf['notifyText'];

			$this->sendNotify($data['from']['id'],'', $text);
			
			//zbrisemo request
			$delete_success = $this->facebook->api('/'.$data['id'],'DELETE');

		}
		
		/*foreach($all['data'] as $data){
			$delete_success = $this->facebook->api('/'.$data['id'],'DELETE');
		}*/
	}
	function sendNotify($toUser, $href, $text){
		$token_url =    "https://graph.facebook.com/oauth/access_token?" .
                "client_id=" . $this->conf['appid'] .
                "&client_secret=" . $this->conf['secret'] .
                "&grant_type=client_credentials";
		$app_token = file_get_contents($token_url);
		$app_token = str_replace("access_token=", "", $app_token);
		
		$data = array(
	    'href'=> $href,
	    'access_token'=> $app_token,
	    'template'=> $text
		);

		$sendnotification = $this->facebook->api('/'.$toUser.'/notifications', 'post', $data);
	}
	function sendEmail($fromName,$fromSurname,$fromEmail, $toEmail){
		$template = $this->cObj->getSubpart($this->template, '###INIVTE_EMAIL###');
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'From: '.$fromName.' '.$fromSurname.' <'.$fromEmail.'>';
		$subject = $this->conf['emailSubject'];
		
		$singleMark['###INVITE_URL###'] = $this->conf['promplacUrl'];
		$singleMark['###FROM_NAME###'] = $fromName;
		
		$message_body = $this->cObj->substituteMarkerArrayCached($template, $singleMark, $multiMark, array());
		
		mail($toEmail,$subject,$message_body,$headers);
		
		/*$apikey = 'fac1448ea8556b4ca733ab1282a6afac-us2';
		 
		$to_emails = array('wechta@gmail.com', 'wechta2@gmail.com');
		$to_names = array('You', 'Your Mom');
		 
		$message = array(
		    'html'=>'Yo, this is the <b>html</b> portion',
		    'text'=>'Yo, this is the *text* portion',
		    'subject'=>'This is the subject',
		    'from_name'=>'Me!',
		    'from_email'=>'verifed@example.com',
		    'to_email'=>$to_emails,
		    'to_name'=>$to_names
		);
		 
		$tags = array('WelcomeEmail');
		 
		$params = array(
		    'apikey'=>$apikey,
		    'message'=>$message,
		    'track_opens'=>true,
		    'track_clicks'=>false,
		    'tags'=>$tags
		);
		 
		$url = "http://us1.sts.mailchimp.com/1.0/SendEmail";
		 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		 
		$result = curl_exec($ch);
		echo $result;
		curl_close ($ch);
		 
		$data = json_decode($result);
		echo "Status = ".$data->status."\n";
		
		*/
	}
	function updateUser($idUser, $fields_values){
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_nagradneigrev10_user', '  uid='.$idUser, $fields_values, FALSE);
	}
	function checkUserDatabase($user_profile){
		$user = $this->findUserByFBID($user_profile['id']);
		if(!$user){
			$userfbid = $this->insertFBUser($user_profile);
			$user = $this->findUserByFBID($userfbid);
		}
		return $user;
	}
	function findUserByFBID($fb_ID){
		$where = ' AND fbid='.$fb_ID;
		$res = $this->pi_exec_query('tx_nagradneigrev10_user','',$where,'','',' ','');
		$outArr = array();
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$outArr[] = $row;
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
		return $outArr[0];
	}
	function insertFBUser($fbUser){
		$fields_values['pid'] = $this->conf['pidList'];
		$fields_values['tstamp'] = time();
		$fields_values['crdate'] = time();
		$fields_values['name'] = $fbUser['first_name'];
		$fields_values['surname'] = $fbUser['last_name'];
		$fields_values['email'] = $fbUser['email'];
		$fields_values['points'] = 10;
		$fields_values['fbid'] = $fbUser['id'];
		$fields_values['fblink'] = $fbUser['link'];
		$fields_values['fbusername'] = $fbUser['username'];
		$birthday = explode('/',$fbUser['birthday']);
		$fields_values['fbbirthday'] = mktime(0,0,0,$birthday[0],$birthday[1],$birthday[2]);
		if($fbUser['gender'] == 'male'){
			$gender=0;
		}else{
			$gender=1;
		}
		$fields_values['fbgender'] = $gender;
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_nagradneigrev10_user', $fields_values);
		//return $GLOBALS['TYPO3_DB']->sql_insert_id();
		return $fbUser['id'];
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
  function curPageURL() {
	 $pageURL = 'http';
	 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	 $pageURL .= "://";
	 if ($_SERVER["SERVER_PORT"] != "80") {
	  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 } else {
	  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	 }
	 return $pageURL;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/nagradneigrev10/pi1/class.tx_nagradneigrev10_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/nagradneigrev10/pi1/class.tx_nagradneigrev10_pi1.php']);
}

?>