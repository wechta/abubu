<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2008 Dmitry Dulepov <dmitry@typo3.org>
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
 * This class implements any extension sitemap like tx_news
 *
 * The following URL parameters are expected:
 * - sitemap=dmf
 * - singlePid=<uid of the "single view" commerce product>
 * - pidList=pid where products are stored
 * http://example.com/?eID=dd_googlesitemap&sitemap=dmf&singlePid=100&pidList=115,116
 *
 * If you need to show products on different single view pages, make several sitemaps
 * (it is possible with Google).
 *
 * @author        Dmitry Dulepov <dmitry@typo3.org>
 * @author        Dominic Garms <djgarms@gmail.com>
 * @package       TYPO3
 * @subpackage    tx_ddgooglesitemap_dmf
 */
class tx_ddgooglesitemap_dmf extends tx_ddgooglesitemap_ttnews {

	/**
	 * Creates an instance of this class
	 *
	 * @return    void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Generates extension site map.
	 *
	 * @return    void
	 */
	protected function generateSitemapContent() {

		$selector = trim(t3lib_div::_GP('selector'));
		t3lib_div::loadTCA($selector);
		$typoscriptSelector = $selector . '.';
		$currentSetup = $GLOBALS['TSFE']->tmpl->setup['plugin.']['dd_googlesitemap_dmf.'][$typoscriptSelector];
		
		//peterw: posebno za cajcke...
		if($selector == 'caji'){
			$categories = $this->getAllCategories();
			foreach($categories as $category){
				$frequency = ($currentSetup['frequency']) ? $currentSetup['frequency'] : $this->getChangeFrequency($row[$currentSetup['sqlLastUpdated']]);
				$urltitle =  strtolower($category['title']);
				$urltitle = str_replace("č", "c", $urltitle);
				$urltitle = str_replace("š", "s", $urltitle);
				$urltitle = str_replace("ž", "z", $urltitle);
				$urltitle = str_replace("Č", "c", $urltitle);
				$urltitle = str_replace("Š", "s", $urltitle);
				$urltitle = str_replace("Ž", "z", $urltitle);
				$urltitle = str_replace(" - ", "_", $urltitle);
				$urltitle = str_replace(" ", "_", $urltitle);
				$url = 'http://www.zisha.si/izdelki_'.$urltitle;	
				echo $this->renderer->renderEntry(
					$url,
					$row[$category['title']],
					$row[$category['tstamp']],
					'weekly',
					$row[$category['seo_keywords']]);
				$products = $this->getAllProducts($category['uid']);
				foreach($products as $product){
					$urltitle =  strtolower($product['title']);
					$urltitle = str_replace("č", "c", $urltitle);
					$urltitle = str_replace("š", "s", $urltitle);
					$urltitle = str_replace("ž", "z", $urltitle);
					$urltitle = str_replace("Č", "c", $urltitle);
					$urltitle = str_replace("Š", "s", $urltitle);
					$urltitle = str_replace("Ž", "z", $urltitle);
					$urltitle = str_replace(" & ", "", $urltitle);
					$urltitle = str_replace(" - ", "_", $urltitle);
					$urltitle = str_replace(" ", "_", $urltitle);
					$url2 = $url.'/caj_'.$urltitle;	
					//t3lib_utility_Debug::debug($product,'product');
					echo $this->renderer->renderEntry(
						$url2,
						$row[$product['title']],
						$row[$product['tstamp']],
						'weekly',
						$row[$product['seo_keywords']]);
				}	
			}
			
		}
		

		$pidList = ($currentSetup['pidList']) ? t3lib_div::intExplode(',', $currentSetup['pidList']) : $this->pidList;


		$catList = (t3lib_div::_GP('catList')) ? t3lib_div::intExplode(',', t3lib_div::_GP('catList')) : t3lib_div::intExplode(',', $currentSetup['catList']);
		$catMMList = (t3lib_div::_GP('catMMList')) ? t3lib_div::intExplode(',', t3lib_div::_GP('catMMList')) : t3lib_div::intExplode(',', $currentSetup['catMMList']);
		$currentSetup['singlePid'] = (t3lib_div::_GP('singlePid')) ? intval(t3lib_div::_GP('singlePid')) : intval($currentSetup['singlePid']);

		$currentSetup['languageUid'] = '';
		if (!$currentSetup['disableLanguageCheck']) {
			if (is_int($GLOBALS['TSFE']->sys_language_uid)) {
				// set language through TSFE checkup
				$currentSetup['languageUid'] = intval($GLOBALS['TSFE']->sys_language_uid);
			}
			if (t3lib_div::_GP('L')) {
				// overwrites if L param is set
				$currentSetup['languageUid'] = intval(t3lib_div::_GP('L'));
			}
		}

		if (count($pidList) > 0 && isset($selector) && isset($currentSetup)) {
			$table = $currentSetup['sqlMainTable'];
			$mmTable = $currentSetup['sqlMMTable'];
			$catColumn = $currentSetup['sqlCatColumn'];

			$sqlCondition = ($catColumn && count($catList) > 0 && $catList[0] > 0) ? ' AND ' . $catColumn . ' IN (' . implode(',', $catList) . ')' : '';

			$sqlMMCondition = $sqlMMTable = '';
			if ($mmTable != '' && count($catMMList) > 0 && $catMMList[0] > 0) {
				$sqlMMTable = ',' . $mmTable;
				$sqlMMCondition = ' AND ' . $table . '.uid = ' . $mmTable . '.uid_local AND ' . $mmTable . '.uid_foreign IN (' . implode(',', $catMMList) . ')';
			}

			$newsSelect = (t3lib_div::_GP('type') == 'news') ? ',' . $currentSetup['sqlTitle'] . ',' . $currentSetup['sqlKeywords'] : '';

			$languageWhere = (is_int($currentSetup['languageUid'])) ? ' AND ' . $table . '.sys_language_uid=' . $currentSetup['languageUid'] : '';
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid,' . $currentSetup['sqlLastUpdated'] . $newsSelect,
				$table . $sqlMMTable,
				'pid IN (' . implode(',', $pidList) . ')' . $sqlCondition . $sqlMMCondition . $this->cObj->enableFields($table) . $languageWhere,
				'uid',
				$currentSetup['sqlOrder'] ? $currentSetup['sqlOrder'] : ''
			);

			$rowCount = $GLOBALS['TYPO3_DB']->sql_num_rows($res);

			while (FALSE !== ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
				if ($url = $this->getVariousItemUrl($row['uid'], $currentSetup)) {
					$frequency = ($currentSetup['frequency']) ? $currentSetup['frequency'] : $this->getChangeFrequency($row[$currentSetup['sqlLastUpdated']]);
					echo $this->renderer->renderEntry(
						$url,
						$row[$currentSetup['sqlTitle']],
						$row[$currentSetup['sqlLastUpdated']],
						$frequency,
						$row[$currentSetup['sqlKeywords']]);
				}
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);

			if ($rowCount === 0) {
				echo '<!-- It appears that there are no extension entries. If your ' .
					'storage sysfolder is outside of the rootline, you may ' .
					'want to use the dd_googlesitemap.skipRootlineCheck=1 TS ' .
					'setup option. Beware: it is insecure and may cause certain ' .
					'undesired effects! Better move your pid sysfolder ' .
					'inside the rootline! -->';
			} elseif (!$rowCount) {
				echo '<!-- There is an sql error. please check all corresponding sql fields in your typoscript setup. -->';
			}

		} else {
			echo 'There is something wrong with the config. Please check your selector and pidList elements. You may ' .
				'want to use the dd_googlesitemap.skipRootlineCheck=1 TS ' .
				'setup option if your storage sysfolder is outside the rootline. Beware: it is insecure and may cause certain ' .
				'undesired effects! Better move your pid sysfolder ' .
				'inside the rootline! -->';
		}

	}
	
	function getAllCategories(){
		$queryParts = array();
		$queryParts['SELECT'] = 'uid,title, tstamp, seo_keywords';
		$queryParts['FROM'] = 'tx_easyshop_categories';
		$queryParts['WHERE'] = " deleted=0 AND hidden=0 ";
		//$res = $this->pi_exec_query('tx_nagradneigrev1_podatki','',$where,'','',' ','');
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		$outArr = array();
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$outArr[] = $row;
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
		return $outArr;
	}
	function getAllProducts($cat){
		$queryParts = array();
		$queryParts['SELECT'] = 'tx_easyshop_products_categories_mm.uid_local, tx_easyshop_products.uid, tx_easyshop_products.title, tx_easyshop_products.tstamp, tx_easyshop_products.seo_keywords';
		$queryParts['FROM'] = ' tx_easyshop_products LEFT JOIN tx_easyshop_products_categories_mm ON tx_easyshop_products_categories_mm.uid_local=tx_easyshop_products.uid';
		$queryParts['WHERE'] = " tx_easyshop_products_categories_mm.sorting = 1 AND tx_easyshop_products_categories_mm.uid_foreign = ".$cat;
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		$outArr = array();
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
			$outArr[] = $row;
		}

		$GLOBALS['TYPO3_DB']->sql_free_result($res);
		return $outArr;
	}
	
	
	/**
	 * Creates a link to the news item
	 *
	 * @param    int $newsId    News item uid
	 *
	 * @return    string
	 */
	protected function getVariousItemUrl($showUid, $currentSetup) {
		$languageParam = (is_int($currentSetup['languageUid'])) ? '&L=' . $currentSetup['languageUid'] : '';

		$conf = array(
			'parameter'        => $currentSetup['singlePid'],
			'additionalParams' => '&' . $currentSetup['linkParams'] . '=' . $showUid . $languageParam,
			'returnLast'       => 'url',
			'useCacheHash'     => TRUE,
		);
		$link = htmlspecialchars($this->cObj->typoLink('', $conf));

		return t3lib_div::locationHeaderUrl($link);
	}


	/**
	 * @param $lastChange
	 *
	 * @return string
	 */
	protected function getChangeFrequency($lastChange) {

		$timeValues[] = $lastChange;
		$timeValues[] = time();
		sort($timeValues, SORT_NUMERIC);
		$sum = 0;
		for ($i = count($timeValues) - 1; $i > 0; $i--) {
			$sum += ($timeValues[$i] - $timeValues[$i - 1]);
		}
		$average = ($sum / (count($timeValues) - 1));

		return ($average >= 180 * 24 * 60 * 60 ? 'yearly' :
			($average <= 24 * 60 * 60 ? 'daily' :
				($average <= 60 * 60 ? 'hourly' :
					($average <= 14 * 24 * 60 * 60 ? 'weekly' : 'monthly'))));
	}

}

/** @noinspection PhpUndefinedVariableInspection */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dd_googlesitemap_dmf/class.tx_googlesitemap_dmf.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/dd_googlesitemap_dmf/class.tx_googlesitemap_dmf.php']);
}

?>