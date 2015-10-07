<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013  <>
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

// require_once(PATH_tslib . 'class.tslib_pibase.php');

/**
 * Plugin 'kenslider' for the 'kenslider' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_kenslider
 */
class tx_kenslider_pi1 extends tslib_pibase {
	public $prefixId      = 'tx_kenslider_pi1';		// Same as class name
	public $scriptRelPath = 'pi1/class.tx_kenslider_pi1.php';	// Path to this script relative to the extension dir.
	public $extKey        = 'kenslider';	// The extension key.
	public $pi_checkCHash = TRUE;
	
	/**
	 * The main method of the Plugin.
	 *
	 * @param string $content The Plugin content
	 * @param array $conf The Plugin configuration
	 * @return string The content that is displayed on the website
	 */
	public function main($content, array $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
	    //t3lib_utility_Debug::debug($this->cObj->data["pid"]);
		$this->template = $this->cObj->fileResource('fileadmin/template/ext/kenslider.html');
    
    $templateMain = $this->cObj->getSubpart($this->template, '###MAIN###');
    $templateTitleSingle = $this->cObj->getSubpart($this->template, '###TITLE_SINGLE###');
		$templateVsebina1 = $this->cObj->getSubpart($this->template, '###VSEBINA_STYLE1###');
		$templateVsebina2 = $this->cObj->getSubpart($this->template, '###VSEBINA_STYLE2###');
		$templateVsebina3 = $this->cObj->getSubpart($this->template, '###VSEBINA_STYLE3###');
		$templateVsebina4 = $this->cObj->getSubpart($this->template, '###VSEBINA_STYLE4###');
		$templateDataLink = $this->cObj->getSubpart($this->template, '###DATA_LINK###');
		
		$entries = $this->getAllEntries();
		foreach($entries as $entry){
			//t3lib_utility_Debug::debug($entry);
			$singleMark['###IMAGE_INITIAL_ZOOM###'] = $entry['initialzoom'];
			$singleMark['###IMAGE_FINAL_ZOOM###'] = $entry['finalzoom'];
			$singleMark['###IMAGE_THUMB_SRC###'] = 'uploads/tx_kenslider/'.$entry['thumb'];
			$singleMark['###IMAGE_SRC###'] = 'uploads/tx_kenslider/'.$entry['image'];
			$singleMark['###IMAGE_TEXT_ID###'] = $entry['uid'];
			$size = getimagesize('uploads/tx_kenslider/'.$entry['image']);
			$singleMark['###IMAGE_SIZE###'] = $size[3];
			if($entry['link']){
				$singleMark['###DATA_LINK_URL###'] = $entry['link'];
				if($entry['linktarget']){$singleMark['###DATA_LINK_TARGET###'] = '_blank';}
				else{$singleMark['###DATA_LINK_TARGET###'] = '_top';}
				$link = $this->cObj->substituteMarkerArrayCached($templateDataLink, $singleMark, $multiMark, array()); 
				$singleMark['###LINKDATA###'] = $link;
			}
			if($entry['iframelink']){
				$singleMark['###DATA_IFRAME###'] = '<iframe width="100%" height="100%" src="'.$entry['iframelink'].'" frameborder="0" allowfullscreen></iframe>';
				$singleMark['###DATA_IFRAME_STATUS###'] = 'true';
			}else{
				$singleMark['###DATA_IFRAME###'] ='';
				$singleMark['###DATA_IFRAME_STATUS###'] ='false';
				}
			
			$titles .= $this->cObj->substituteMarkerArrayCached($templateTitleSingle, $singleMark, $multiMark, array()); 
			
			$singleMark['###TEXT1###'] = $this->pi_RTEcssText($entry['text1']);
			$singleMark['###TEXT2###'] = $this->pi_RTEcssText($entry['text2']);
			$singleMark['###TEXT3###'] = $this->pi_RTEcssText($entry['text3']);
			if(!$entry['text1']){$multiMark['###VSEBINA_TEXT1###']='';}
			if(!$entry['text2']){$multiMark['###VSEBINA_TEXT2###']='';}
			if(!$entry['text3']){$multiMark['###VSEBINA_TEXT3###']='';}
			
			if($entry['style']==1){$vsebine .= $this->cObj->substituteMarkerArrayCached($templateVsebina1, $singleMark, $multiMark, array()); }
			if($entry['style']==2){$vsebine .= $this->cObj->substituteMarkerArrayCached($templateVsebina2, $singleMark, $multiMark, array()); }
			if($entry['style']==3){$vsebine .= $this->cObj->substituteMarkerArrayCached($templateVsebina3, $singleMark, $multiMark, array()); }
			if($entry['style']==4){$vsebine .= $this->cObj->substituteMarkerArrayCached($templateVsebina4, $singleMark, $multiMark, array()); }
		}
		
		$singleMark['###TITLES###'] = $titles;
		$singleMark['###VSEBINE###'] = $vsebine;
		
		
		return $this->cObj->substituteMarkerArrayCached($templateMain, $singleMark, $multiMark, array()); 
	}
	function getAllEntries(){
		
		$this->internal['results_at_a_time'] = 1000;
		$this->conf['pidList'] = $this->cObj->data["pid"];
		$res = $this->pi_exec_query('tx_kenslider_entry','',$where,'','',' sorting','');
		$outArr = array();
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$outArr[] = $row;
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
		return $outArr;
	}
}



if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/kenslider/pi1/class.tx_kenslider_pi1.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/kenslider/pi1/class.tx_kenslider_pi1.php']);
}

?>