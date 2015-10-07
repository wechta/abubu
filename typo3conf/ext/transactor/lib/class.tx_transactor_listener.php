<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2014 Franz Holzinger (franz@ttproducts.de)
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
 * Called by the payment gateway after the payment has been made or an error has occured.
 *
 * $Id: class.tx_transactor_listener.php 87110 2014-12-06 14:08:32Z franzholz $
 *
 * @author	Franz Holzinger <franz@ttproducts.de>
 * @package TYPO3
 * @subpackage transactor
 */



abstract class tx_transactor_listener {

	/**
	 * Main function which creates the transaction record
	 * This must be overridden
	 * @return	void
	 */
	abstract public function main ();

	/**
	 * Main function which processes the tasks connected to the listener.
	 * E.g an order in the shop is finalized.
	 *
	 * @return	void
	 */
	public function execute ($params) {

		if (is_array ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['transactor']['listener'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['transactor']['listener'] as $classRef) {
				$hookObj = t3lib_div::makeInstance($classRef);
				if (method_exists($hookObj, 'execute')) {
					$hookObj->execute(
						$this,
						$params
					);
				}
			}
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/transactor/class.tx_transactor_listener.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/transactor/class.tx_transactor_listener.php']);
}


?>