<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010  <>
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

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Modern Tab' for the 'moderntab' extension.
 *
 * @author	 <>
 * @package	TYPO3
 * @subpackage	tx_moderntab
 */
class tx_moderntab_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_moderntab_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_moderntab_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'moderntab';	// The extension key.
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
		//$this->init();
		
    $this->template = $this->cObj->fileResource('fileadmin/template/ext/modernTab.html');
    
    $templateMain = $this->cObj->getSubpart($this->template, '###MAIN###');
    $templateTitleSingle = $this->cObj->getSubpart($this->template, '###TITLE_SINGLE###');
	$templateVsebina = $this->cObj->getSubpart($this->template, '###VSEBINA###');

		$tabs = $this->getAllTabs();
    //t3lib_utility_Debug::debug($tabs);
    
    $i=0;
    foreach($tabs as $tab){
		$i++;
		
		if (strpos($tab['link'],'_blank') !== false){
			$singleMark['###POVEZAVA_TARGET###'] = '_blank';
			}
		else {$singleMark['###POVEZAVA_TARGET###'] = '_top';}
		
    	$singleMark['###TITLE###'] = $tab['title'];
    	$singleMark['###COUNTNUMBER###'] = $i;
		$singleMark['###POVEZAVA###'] = $this->pi_linkTP_keepPIvars_url(array(),0,0,$tab['link']);
		$singleMark['###TEXT###'] = $this->pi_RTEcssText($tab['vsebina']);
		$singleMark['###IMAGE###'] = 'uploads/tx_moderntab/'.$tab['slika'];
    	$insertedTabs .= $this->cObj->substituteMarkerArrayCached($templateTitleSingle, $singleMark, $multiMark, array());
		$insertedContent .= $this->cObj->substituteMarkerArrayCached($templateVsebina, $singleMark, $multiMark, array());
    }
		$singleMark['###TITLES###'] = $insertedTabs;
		$singleMark['###VSEBINE###'] = $insertedContent;
		
		$multiMark['###VSEBINA###'] = '';
		$multiMark['###TITLE_SINGLE###'] = '';

		return $this->cObj->substituteMarkerArrayCached($templateMain, $singleMark, $multiMark, array());
	}
	function getAllTabs(){
		$this->internal['results_at_a_time'] = 1000;
		$this->conf['pidList'] = $this->cObj->data["pages"];
		$res = $this->pi_exec_query('tx_moderntab_entry','',$where,'','',' sorting','');
		$outArr = array();
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$outArr[] = $row;
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
		return $outArr;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/moderntab/pi1/class.tx_moderntab_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/moderntab/pi1/class.tx_moderntab_pi1.php']);
}

?>