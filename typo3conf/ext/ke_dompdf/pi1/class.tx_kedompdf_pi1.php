<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Nadine Schwingler <schwingler@kennziffer.com>
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
 * Plugin 'create PDF' for the 'ke_dompdf' extension.
 *
 * @author	Nadine Schwingler <schwingler@kennziffer.com>
 * @package	TYPO3
 * @subpackage	tx_kedompdf
 */
class tx_kedompdf_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_kedompdf_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_kedompdf_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ke_dompdf';	// The extension key.
	var $pi_checkCHash = true;
	
	var $extConf = array();
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		$this->pi_USER_INT_obj=1;
		
		$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ke_dompdf']);
		t3lib_div::devLog('conf', $this->prefixId, 0, $conf);
		//t3lib_div::devLog('extconf', $this->prefixId, 0, $this->extConf);
		
		$url = 'http://'.t3lib_div::getIndpEnv('HTTP_HOST').t3lib_div::getIndpEnv('REQUEST_URI');
		$url .= '&type=101';
		t3lib_div::devLog('url '.$url, $this->prefixId, 0, array($html));
		
		if ($conf['activate_pdf'] == 1){		
			$mode = $conf['mode'];
			if (htmlspecialchars(t3lib_div::_GP('dompdf_mode'))) $mode=htmlspecialchars(t3lib_div::_GP('dompdf_mode'));
			
			switch ($mode){
				case 'pdf':
					require_once(t3lib_extMgm::extPath('ke_dompdf')."res/dompdf/dompdf_config.inc.php");
					//set_time_limit(0);
					spl_autoload_register('DOMPDF_autoload');
					//$html = file_get_contents($url);
					$html = $GLOBALS['TSFE']->content;
					t3lib_div::devLog('url/html '.$url, $this->prefixId, 0, array($html));
						
					$dompdf = new DOMPDF();
					$dompdf->load_html($html);
					
					$dompdf->render();
					$dompdf->stream("sample.pdf");
				break;
				case 'icon':
					$icon = '<p><a href="'.$url.'&dompdf_mode=pdf">pdf</a></p>';
					return $icon;	
				break;
			}
		}
		
		/*return 'Hello World!<HR>
			Here is the TypoScript passed to the method:'.
					t3lib_div::view_array($conf);*/
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_dompdf/pi1/class.tx_kedompdf_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_dompdf/pi1/class.tx_kedompdf_pi1.php']);
}

?>