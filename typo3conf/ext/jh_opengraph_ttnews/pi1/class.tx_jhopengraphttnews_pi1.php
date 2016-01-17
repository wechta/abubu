<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012-2013 Jonathan Heilmann <mail@jonathan-heilmann.de>
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
 *
 *
 * @author    Jonathan Heilmann <mail@jonathan-heilmann.de>
 * @package    TYPO3
 * @subpackage    tx_jhopengraphttnews_pi1
 */
class tx_jhopengraphttnews_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_jhopengraphttnews_pi1';        // Same as class name
	var $scriptRelPath = 'pi1/class.tx_jhopengraphttnews_pi1.php';    // Path to this script relative to the extension dir.
	var $extKey        = 'jh_opengraph_ttnews';    // The extension key.
	var $pi_checkCHash = true;

	/**
	 * The extraItemMarkerProcessor function from tt_news
	 *
	 * @return    markerArray
	 */
	function extraItemMarkerProcessor($markerArray, $row, $conf, &$pObj) {
		$this->pObj = $pObj;
		$this->conf = $conf;
		$this->row = $row;

		if($pObj->config['code'] == 'SINGLE') {
			$link = t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'); //now compatibel with CoolURI - thanks to thomas@chaschperli.ch

			if($row['image'] != '') {
				if(strstr($row['image'], ',')) {
					$imagename =  substr($row['image'], 0, strpos($row['image'], ','));
				} else {
					$imagename =  $row['image'];
				}
				$image = $GLOBALS['TSFE']->tmpl->getFileName('uploads/pics/' . $imagename);
			} elseif(isset($row['tx_damnews_dam_images']) && $row['tx_damnews_dam_images'] > 0) {
				/* cpl @ wd */
				$image = $GLOBALS['TSFE']->tmpl->getFileName($this->getDAMImage($row));
			} else {
				$image = $GLOBALS['TSFE']->tmpl->getFileName($pObj->conf['tx_jhopengraphttnews_pi1.']['nopic_path']);
			}
			$image = t3lib_div::locationHeaderUrl($image);

			if ($markerArray['###NEWS_SUBHEADER###'] != '') {
				$description = t3lib_div::fixed_lgd_cs(strip_tags($markerArray['###NEWS_SUBHEADER###']), 100);
			} else {
				$description = t3lib_div::fixed_lgd_cs(strip_tags($markerArray['###NEWS_CONTENT###']), 100);
			}

			$title = $markerArray['###NEWS_TITLE###'];
			if($title == '') {
				$title = $row['title'];
			}

			$sitename = $GLOBALS['TSFE']->tmpl->setup['sitetitle'];
			if($sitename == '') {
				$sitename = $GLOBALS['TSFE']->TYPO3_CONF_VARS['SYS']['sitename'];
			}

			$GLOBALS['TSFE']->additionalHeaderData[$this->extKey.'1'] = '<meta property="og:title" content="'.htmlspecialchars($title).'"/>';
			$GLOBALS['TSFE']->additionalHeaderData[$this->extKey.'2'] = '<meta property="og:type" content="article"/>';
			$GLOBALS['TSFE']->additionalHeaderData[$this->extKey.'3'] = '<meta property="og:image" content="'.htmlspecialchars($image).'"/>';
			$GLOBALS['TSFE']->additionalHeaderData[$this->extKey.'4'] = '<meta property="og:url" content="'.htmlspecialchars($link).'"/>';
			$GLOBALS['TSFE']->additionalHeaderData[$this->extKey.'5'] = '<meta property="og:site_name" content="'.htmlspecialchars($sitename).'"/>';
			$GLOBALS['TSFE']->additionalHeaderData[$this->extKey.'6'] = '<meta property="og:description" content="'.htmlspecialchars($description).'"/>';
		}
		return $markerArray;
	}

	function getDAMImage($row) {
		// workspaces
		if (isset($row['_ORIG_uid']) && ($row['_ORIG_uid'] > 0)) {
			// draft workspace
			$uid = $row['_ORIG_uid'];
		} else {
			// live workspace
			$uid = $row['uid'];
		}
		$damData = tx_dam_db::getReferencedFiles('tt_news', $uid, 'tx_damnews_dam_images');
		#print_r($damData);
		foreach($damData['files'] as $file)
		return $file;
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tx_jhopengraphttnews_pi1/pi1/class.tx_jhopengraphttnews_pi1.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tx_jhopengraphttnews_pi1/pi1/class.tx_jhopengraphttnews_pi1.php']);
}

?>
