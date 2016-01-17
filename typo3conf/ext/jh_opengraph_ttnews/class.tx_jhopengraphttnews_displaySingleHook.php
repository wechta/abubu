<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Bernhard Kraft <kraft@webconsulting.at>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 * Hook for tt_news to remember the rendering of a tt_news SINGLE view
 *
 * @author	Bernhard Kraft <kraft@webconsulting.at>
 * @package TYPO3
 * @subpackage jh_opengraphp_ttnews
 */
class tx_jhopengraphttnews_displaySingleHook implements t3lib_Singleton {
	protected $singleViewDisplayed = false;

	/*
	 * This method gets called by the tt_news single view method
	 * It simply returns the passed $selectConf array unmodified and remembers that
	 * a single view was requested
	 *
	 * @param object	$parentObject: A reference to a instance of the parent object (tt_news)
	 * @param array	$selectConf: An array containing the SELECT parameters for a tt_news single view
	 * @return array	Returns the unmodified $selectConf array
	 */
	public function processSViewSelectConfHook(&$parentObject, $selectConf) {
		$this->singleViewDisplayed = true;
		return $selectConf;
	}

	/*
	 * This method gets called from jh_opengrapprotocol user function. It returns whether the
	 * tt_news method "displaySingle" has been called before which would have invoked above hook method.
	 *
	 * @return boolean	True if the method "displaySingle" of tt_news has been called before
	 */
	public function singleViewDisplayed() {
		return $this->singleViewDisplayed;
	}


}

?>
