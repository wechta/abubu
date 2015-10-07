<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Franz Holzinger (franz@ttproducts.de)
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * store for variables used in your extensions
 *
 * $Id: class.tx_div2007_store.php 207 2013-09-26 07:21:48Z franzholz $
 *
 * class tslib_cObj	All main TypoScript features, rendering of content objects (cObjects). This class is the backbone of TypoScript Template rendering.
 *
 * @package    TYPO3
 * @subpackage div2007
 * @author	Franz Holzinger <franz@ttproducts.de>
 */


class tx_div2007_store {
	protected $cObj; // currently stored cObject

	/**
	 * Storage of the currently used cObject
	 * You set the cObject in your extension at the beginning.
	 * Then you can fetch it in your functions from here if you call
	 * $obj = t3lib_div::getUserObj('&tx_div2007_cobj');
	 * $cObj = $obj->getCurrent();
	 *
	 * @param	array		$data	the record data that is rendered.
	 * @param	string		$table	the table that the data record is from.
	 * @return	void
	 */
	public function setCobj(tslib_cObj $cObject) {
		$this->cObj = $cObject;
	}

	public function getCobj() {
		return $this->cObj;
	}
}


?>