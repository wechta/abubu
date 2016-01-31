<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Mitja Venturini <>
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
 * Plugin 'HubTie sync' for the 'hubtie_sync' extension.
 *
 * @author	Mitja Venturini <>
 * @package	TYPO3
 * @subpackage	tx_hubtiesync
 */
include_once("core.php");

class tx_hubtiesync_pi1 extends tslib_pibase {
	public $prefixId      = 'tx_hubtiesync_pi1';		// Same as class name
	public $scriptRelPath = 'pi1/class.tx_hubtiesync_pi1.php';	// Path to this script relative to the extension dir.
	public $extKey        = 'hubtie_sync';	// The extension key.
	
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
		$this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
		$this->initApi();

		$content = '';
		$content .= $this->doCats();
		$content .= $this->doProps();
		$content .= $this->doProds();

		return $this->pi_wrapInBaseClass($content);
	}

	private function doCats() {
		$returnStr = '';
		$udpated = $inserted = 0;
		$result = mt_api_call("getCategories", array("all" => "1"));
		foreach($result['data'] as $cat) {
			if(intval($cat['id']) != 1 && intval($cat['pid']) != 1) {
				$catId = $this->checkIf('tx_easyshop_categories', 'syncId', intval($cat['id']));
				//t3lib_utility_Debug::debug($cat);
				if ($catId){
					$this->updateCat($catId, $cat);
					$udpated++;
				} else {
					$this->insertCat($cat);
					$inserted++;
				}
			}
		}
		return "Inserted ".$inserted.", updated ".$udpated." categories.<br>";
	}

	private function doProds() {
		$returnStr = '';
		$udpated = $inserted = 0;		
		$result = mt_api_call("getArticles", array("all" => "1"));
		foreach ($result['data'] as $single) {
	 		$prodProp = $propCats = array();		
			if($single['pid']==0 && $single['web']==1 && $single['active']==1) {
				// parent prod props
				if($single['props']) {
					foreach($single['props'] as $prop) {
						if($prop['prop_id']) {
							$prodProp[] = $prop['prop_id'];
						}
					}
				}
				// parent prod cats
				if($single['cats']) {
					foreach($single['cats'] as $cat) {
						if($cat['cat_id']) {
							$propCats[] = $cat['cat_id'];
						}
					}
				}

				// get child products
				//t3lib_utility_Debug::debug($single);
				$subArt = $this->getSub($single['id']);
				if($single['id'] == 1324) {
					t3lib_utility_Debug::debug($single);
					t3lib_utility_Debug::debug($subArt);
				}
				//t3lib_utility_Debug::debug($single);
				if($subArt) {
					foreach($subArt as $sub) {
						// child prod properties
						if($sub['props']) {
							foreach($sub['props'] as $sProp) {
								if(!in_array($sProp['prop_id'], $prodProp)) {
									if($sProp['prop_id']) {
										$prodProp[] = $sProp['prop_id'];
									}
								}
							}
						}

						// child prod categories
						if($sub['cats']) {
							foreach($sub['cats'] as $sCat) {
								if(!in_array($sCat['cat_id'], $propCats)) {
									if($sCat['cat_id']) {
										$propCats[] = $sCat['cat_id'];
									}
								}
							}
						}
					}
				}

				// get prod images
				//TODO
				/*
				if($single['images']) {
					
					foreach($single['images'] as $img) {
						
					}
				}*/

				$wsCatIds = array();
				$wsPropIds = array();

				foreach($prodProp as $p) {
					$pro = $this->getPropertie($p);
					$wsPropIds[] = $pro['uid'];
				}

				foreach($propCats as $c) {
					 $cat = $this->getCategory($c);
					 $wsCatIds[] = $cat['uid'];
				}

				$imgFiles = ['no-product-image-available.png'];
				$prodId = $this->checkIf('tx_easyshop_products', 'syncId', intval($single['id']));
				if ($prodId){
					$this->updateProd($prodId, $single, $wsPropIds, $wsCatIds, $imgFiles);
					$udpated++;
				} else {
					$this->insertProd($single, $wsPropIds, $wsCatIds, $imgFiles);
					$inserted++;
				}

				//t3lib_utility_Debug::debug($prodProp);
				//t3lib_utility_Debug::debug($propCats);
				//t3lib_utility_Debug::debug($single);
			}
		}
		return "Inserted ".$inserted.", updated ".$udpated." products.<br>";
	}

	private function doProps() {
		$returnStr = '';
		$udpated = $inserted = 0;
		$result = mt_api_call("getProps", array("all" => "1"));
		foreach($result['data'] as $prop) {
			$propId = $this->checkIf('tx_easyshop_properties', 'syncId', intval($prop['id']));
			if ($propId){
				$this->updateProp($propId, $prop);
				$udpated++;
			} else {
				$this->insertProp($prop);
				$inserted++;
			}
		}
		return "Inserted ".$inserted.", updated ".$udpated." properties.<br>";
	}

	private function getSub($prodId) {
		$resultArr = [];
		$result = mt_api_call("getLinkedArt", array("id" => $prodId));
		foreach ($result['data'] as $single) {
		  if($single['pid'] != 0) {
		    $result2 = mt_api_call("getArticles", array("id" => $single['art']));
		    $resultArr[] = $result2['data'][0];
		  }
		}
		return $resultArr;
	}

	private function checkIf($tName, $fName, $id) {
		//$where = " AND ".$fName." = '".$id."'";
		//$res = $this->pi_exec_query($tName,'', $where,'','',' ','');
		$queryParts['SELECT'] = $tName.'.*';
		$queryParts['FROM'] = $tName;
		$queryParts['WHERE'] = $fName.'='.$id;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		$outArr = array();
		if ($res) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$outArr[] = $row;
			}
			//t3lib_utility_Debug::debug($outArr);
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			if($outArr[0]!=''){
				return $outArr[0]['uid'];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	private function updateProd($prodId, $prod, $prodProp, $propCats, $imgArray) {
		//t3lib_utility_Debug::debug($prodProp);
		$images = implode(',', $imgArray);
		//t3lib_utility_Debug::debug($images);
		$updArray['tstamp'] = time();		
		$updArray['syncId'] = intval($prod['id']);
		$updArray['code'] = $prod['code'];
		$updArray['title'] = $prod['title'];
		$updArray['price'] = $prod['price'];
		$updArray['vat'] = $prod['vat_percents'];
		$updArray['images'] = $images;
		$updArray['hidden'] = (intval($prod['active'])) ? 0 : 1;
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_easyshop_products','uid=' . $prodId, $updArray);
		$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_easyshop_products_categories_mm','uid_local=' . $prodId);
		$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_easyshop_products_properities_mm','uid_local=' . $prodId);
		$this->insertCatMM($prodId, $propCats);
		$this->insertPropMM($prodId, $prodProp);
	}

	private function insertProd($prod, $prodProp, $propCats, $imgArray) {
		$images = implode(',', $imgArray);
		$insertArray['pid'] = 15;
		$insertArray['tstamp'] = time();
		$insertArray['crdate'] = time();
		$insertArray['syncId'] = intval($prod['id']);
		$insertArray['code'] = $prod['code'];
		$insertArray['title'] = $prod['title'];
		$insertArray['price'] = $prod['price'];
		$insertArray['images'] = $images;
		$insertArray['vat'] = $prod['vat_percents'];
		$insertArray['hidden'] = (intval($prod['active'])) ? 0 : 1;
		$query = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_easyshop_products', $insertArray);
		$insId = $GLOBALS['TYPO3_DB']->sql_insert_id();
		$this->insertCatMM($insId, $propCats);
		$this->insertPropMM($insId, $prodProp);
	}

	private function insertCatMM($idProd, $idCats){
		if($idCats) {
			foreach($idCats as $idCat){
				$fields_values['uid_local'] = $idProd;
				$fields_values['uid_foreign'] = $idCat;
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_easyshop_products_categories_mm',$fields_values,$no_quote_fields=FALSE);
			}
		}
	}

	private function insertPropMM($idProd, $idProps){
		if($idProps) {
			foreach($idProps as $idProp){
				$fields_values['uid_local'] = $idProd;
				$fields_values['uid_foreign'] = $idProp;
				$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_easyshop_products_properties_mm',$fields_values,$no_quote_fields=FALSE);
			}
		}
	}

	private function insertProp($prop) {
		$insertArray['pid'] = 99;
		$insertArray['tstamp'] = time();
		$insertArray['crdate'] = time();
		$insertArray['title'] = $prop['title'];
		$insertArray['title_front'] = $prop['title'];
		$insertArray['syncId'] = intval($prop['id']);
		$insertArray['parrent'] = $this->checkIf('tx_easyshop_properties', 'syncId', intval($prop['pid']));
		$query = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_easyshop_properties', $insertArray);
		return $GLOBALS['TYPO3_DB']->sql_insert_id();
	}

	private function updateProp($propId, $prop) {
		$updArray['tstamp'] = time();		
		$updArray['title'] = $prop['title'];
		$updArray['title_front'] = $prop['title'];
		$updArray['syncId'] = intval($prop['id']);
		$updArray['parrent'] = $this->checkIf('tx_easyshop_properties', 'syncId', intval($prop['pid']));
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_easyshop_properties','uid=' . $catId, $updArray);
	}

	private function insertCat($cat) {
		$insertArray['pid'] = 18;
		$insertArray['tstamp'] = time();
		$insertArray['crdate'] = time();
		$insertArray['title'] = $cat['title'];
		$insertArray['title_front'] = $cat['title'];
		$insertArray['syncId'] = intval($cat['id']);
		$insertArray['parrent'] = $this->checkIf('tx_easyshop_categories', 'syncId', intval($cat['pid']));
		$insertArray['hidden'] = (intval($cat['active'])) ? 0 : 1;
		$query = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_easyshop_categories', $insertArray);
		return $GLOBALS['TYPO3_DB']->sql_insert_id();
	}

	private function updateCat($catId, $cat) {
		$updArray['tstamp'] = time();		
		$updArray['title'] = $cat['title'];
		$updArray['title_front'] = $cat['title'];
		$updArray['syncId'] = intval($cat['id']);
		$updArray['parrent'] = $this->checkIf('tx_easyshop_categories', 'syncId', intval($cat['pid']));
		$updArray['hidden'] = (intval($cat['active'])) ? 0 : 1;	
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_easyshop_categories','uid=' . $catId, $updArray);
	}

	private	function getProduct($syncId){
		$queryParts=$product=$productOverlay=$queryPartsOverlay=array();
		$queryParts['SELECT'] = 'tx_easyshop_products.*';
		$queryParts['FROM'] = 'tx_easyshop_products';
		$queryParts['WHERE'] = 'tx_easyshop_products.syncId='.$syncId.' AND tx_easyshop_products.hidden=0  AND tx_easyshop_products.deleted=0 ';
		$product = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts));
		return $product;
	}

	private	function getCategory($syncId){
		$queryParts=array();
		$queryParts['SELECT'] = 'tx_easyshop_categories.*';
		$queryParts['FROM'] = 'tx_easyshop_categories';
		$queryParts['WHERE'] = 'tx_easyshop_categories.syncId='.$syncId.' AND tx_easyshop_categories.hidden=0  AND tx_easyshop_categories.deleted=0 ';
		$cat = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts));
		return $cat;
	}

	private	function getPropertie($syncId){
		$queryParts=array();
		$queryParts['SELECT'] = 'tx_easyshop_properties.*';
		$queryParts['FROM'] = 'tx_easyshop_properties';
		$queryParts['WHERE'] = 'tx_easyshop_properties.syncId='.$syncId.' AND tx_easyshop_properties.hidden=0  AND tx_easyshop_properties.deleted=0 ';
		$cat = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts));
		return $cat;
	}

	private function initApi() {
		$api_sess = &$_SESSION['mt_api_sess'];
		$api_user = "api.abubushop";
		$api_hash = "i397c6g4e1dc2d22d025b291cd0b2b3a9dLb2buu";
		mt_api_call("init", array("api_user" => $api_user, "api_hash" => $api_hash));
	}
}



if (defined('TYPO3_MODE') && isset($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/hubtie_sync/pi1/class.tx_hubtiesync_pi1.php'])) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/hubtie_sync/pi1/class.tx_hubtiesync_pi1.php']);
}

?>