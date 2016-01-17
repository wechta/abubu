<?php

/**
 * Class for transport and delivering of request parameters.
 *
 * PHP version 5
 *
 * Copyright (c) 2006-2011 Elmar Hinz
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package    TYPO3
 * @subpackage div2007
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @copyright  2006-2011 Elmar Hinz
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    SVN: $Id: class.tx_div2007_parameters.php 197 2013-09-10 09:42:56Z franzholz $
 * @since      0.1
 */

/**
 * Transport and deliver the request parameters.
 *
 * Member of the central quad: $controller, $parameters, $configurations, $context.	<br>
 * Address it from everywhere as: $this->controller->parameters
 *
 * All request parameters should be accessed from all other objects by a
 * $parameters object. Either use this class directly or as parent for your own
 * inherited class.
 *
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @package    TYPO3
 * @subpackage div2007
 */
class tx_div2007_parameters extends tx_div2007_object {

	/**
	 * Contructs a new tx_div2007_parameters object associated with the given controller
	 *
	 * The controller has to be set in a second step.
	 *
	 * @param		string		cObj
	 * @return		void
	 */
	public function tx_div2007_parameters ($controller) {
		parent::tx_div2007_object($controller);
		$this->setArray(tx_div2007_div::_GPmerged($controller->getDesignator()));
		// Initialize the cHash system if there are parameters available
		if ($GLOBALS['TSFE'] && count($parameters)) {
			$GLOBALS['TSFE']->reqCHash();
		}
	}

	/**
	 * Returns a string representation of this object where all parameters are
	 * encapsulated into HTML input fields of type="hidden".
	 *
	 * @return		string		HTML code
	 */
	public function toHiddenFields () {
		$out = '';
		for($this->rewind(); $this->valid(); $this->next()){
			if(!is_array($this->current())) {  // TODO: use also arrays
				$out .= sprintf('%s<input type="hidden" name="%s[%s]" value="%s">', chr(10),
					$this->getDesignator(), $this->key(), htmlspecialchars($this->current()));
			}
		}
		return $out;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/div2007/class.tx_div2007_parameters.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/div2007/class.tx_div2007_parameters.php']);
}

?>