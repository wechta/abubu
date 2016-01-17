<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Cosmin Stefaniga (cosmin.stefaniga@gmail.com)
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
 * Class 'tx_ttnewsfacebookcomments' for the ttnews_facebookcomments extension.
 *
 * $Id:
 *
 */
 
// the facebook client library
include_once 'facebook-platform/php/facebook.php';

/**
 * Class being included by TCEmain using a hook
 *
 * @author	Cosmin Stefaniga <cosmin.stefaniga@gmail.com>
 * @package TYPO3
 * @subpackage tt_news
 */
class tx_ttnewsfacebookcomments {
	var $conf = array();
	/**
 * This method is called by a hook in the TYPO3 Core Engine (TCEmain) when a command was executed (copy,move,delete...).
 * For tt_news it is used to disable saving of the current record if it has an editlock or if it has categories assigned that are not allowed for the current BE user.
 *
 * @param	string		$command: The TCEmain command, fx. 'delete'
 * @param	string		$table: The table TCEmain is currently processing
 * @param	string		$id: The records id (if any)
 * @param	array		$value: The new value of the field which has been changed
 * @param	object		$pObj: Reference to the parent object (TCEmain)
 * @return	void
 * @access public
 */
	
	function extraItemMarkerProcessor($markerArray, $row, $conf, &$pObj) {

		global $switch_row;
		
		$alt_row = $switch_row % 2;
		
		if ($conf['zebra_enabled'] == 1) $markerArray["###ZEBRA###"] = 'layout'.$alt_row;
		
		if ($conf['facebook_comments_enabled'] == 1) {	
			$markerArray["###FACEBOOK-COMMENTS-TAG###"] = $this->getFacebookCommentsTag($row);
			$markerArray['###FACEBOOK-COMMENTS-NR###'] = $this->getFacebookCommentsNr($row, $pObj->conf['facebook_comments.']);
		}
		
		$switch_row++;
		
		return $markerArray;
		
	}
	
	function extraGlobalMarkerProcessor($newsConf, $markerArray) {

		$markerArray['###FACEBOOK-COMMENTS-JS###'] = $this->getFacebookCommentsJS($newsConf);
		
		return $markerArray;
	}
	
	function getFacebookCommentsTag ($row) {

		$content = '<fb:comments xid="'.$row['uid'].'"></fb:comments>';
		return $content;

	}
	
	function getFacebookCommentsJS($newsConf) {
		
		$apiKey = $newsConf->conf['facebook_comments.']['facebook_api_key'];
		if ($apiKey == '') return '<div style="color: red; font-weight: bold; padding-7px;">Please add facebook api key to your tt_news typoscript conf with plugin.tt_news.facebook_api_key = xxx</div>';
		$output = '
			<script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php/en_US" type="text/javascript"></script>
			<script type="text/javascript">FB.init("'.$apiKey.'");</script>
		';
		return $output;
	}
	
	function getFacebookCommentsNr($row, $conf) {
		$api_key = $conf['facebook_api_key'];
		$secret  = $conf['facebook_api_secret'];
		
		$facebook = new Facebook($api_key, $secret);
		
		try {
			$comments = $facebook->api_client->comments_get($row['uid']);
			
			$nr_of_comments = ($comments != NULL) ? count($comments) : '0';
		
			$output = ($nr_of_comments == 1) ? $nr_of_comments.' '.$conf['comment_label'] : $nr_of_comments.' '.$conf['comments_label'];
		} catch (FacebookRestClientException $e) {
  			$output  = '<span style="color: red; font-weight: bold">Oops! Something went wrong</span>';
		}
		
		return $output;
	
	}
 } 


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ttnews_youtube/class.tx_ttnewsyoutube.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ttnews_youtube/class.tx_ttnewsyoutube.php']);
}

?>