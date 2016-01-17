<?php
	/***************************************************************
	*  Copyright notice
	*
	*  (c) 2012 Sivaprasad S, Azeef A S | PIT Solutions Pvt ltd <sivaprasad.s@pitsolutions.com, azeef@pitsolutions.com, typo3support@pitsolutions.com>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   48: class tx_cookiecontrol_pi1 extends tslib_pibase
 *   61:     public function main($content, $conf)
 *  215:     protected function init()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

	require_once(PATH_tslib.'class.tslib_pibase.php');


	/**
	 * Plugin 'Cookie Control' for the 'cookie_control' extension.
	 *
	 * @author Siva prasad s, Azeef A S | PIT Solutions <typo3support@pitsolutions.com>
	 * @package TYPO3
	 * @subpackage tx_cookiecontrol
	 */
	class tx_cookiecontrol_pi1 extends tslib_pibase {
		var $prefixId = 'tx_cookiecontrol_pi1';
		// Same as class name
		var $scriptRelPath = 'pi1/class.tx_cookiecontrol_pi1.php'; // Path to this script relative to the extension dir.
		var $extKey = 'cookie_control'; // The extension key.

		/**
 * The main method of the PlugIn
 *
 * @param	string		$content: The PlugIn content
 * @param	array		$conf: The PlugIn configuration
 * @return	The		content that is displayed on the website
 */
		public function main($content, $conf) {
			$this->conf = $conf;

			$this->pi_setPiVarDefaults();
			$this->pi_loadLL();
			$this->pi_USER_INT_obj = 1;

			$flexformValues = $this->init(); //Initialize the flexform content
			$templateFile = $flexformValues['templateSelection'] ? $flexformValues['templateSelection'] :
			 $this->conf['templateFile'];

			if (!$templateFile) {
				$template = $this->cObj->fileResource('EXT:'.$this->extKey.'/pi1/res/template.html');
			} else {
				$template = $this->cObj->fileResource($templateFile);
			}
			$tmpl = $this->cObj->getSubpart($template, '###COOKIE###');

			switch ($this->conf['themeSelect']) {
				case 'default':
				$this->conf['themeSelect'] = 0;
				break;
				case 'grey':
				$this->conf['themeSelect'] = 1;
				break;
				case 'black':
				$this->conf['themeSelect'] = 2;
				break;
				case 'blue':
				$this->conf['themeSelect'] = 3;
				break;
				case 'custom':
				$this->conf['themeSelect'] = 4;
				break;
			}

			//Configuration for cookieControl plug-in

			$iconPosition = $flexformValues['iconPosition'] ? $flexformValues['iconPosition'] :
			 $this->conf['iconPosition'];
			$iconTheme = $flexformValues['iconTheme'] ? $flexformValues['iconTheme'] :
			 $this->conf['iconTheme'];
			$iconType = $flexformValues['iconType'] ? $flexformValues['iconType'] :
			 $this->conf['iconType'];
			$extraoptions = $flexformValues['extra_options'] ? $flexformValues['extra_options'] :
			 $this->conf['extra_options'];
			$countrysel = $flexformValues['countrySelection'] ? $flexformValues['countrySelection'] :
			 $this->conf['countrySelection'];
			if ($countrysel != "")
			$countries = $flexformValues['countryField'] ? $flexformValues['countryField'] :
			 $this->conf['countryField'];
			$analyticsCode = $flexformValues['anlayticsCode'] ? $flexformValues['anlayticsCode'] :
			 $this->conf['analyticsCode'];
			$consentModel = $flexformValues['consentModel'] ? $flexformValues['consentModel'] :
			 $this->conf['consentModel'];
			 $enableSession = $flexformValues['enableSession'] ? $flexformValues['enableSession'] :
			 $this->conf['enableSession'];

			$flexformValues['introductory_text'] = str_replace(array("\r", "\n"), '', addslashes($flexformValues['introductory_text']));
			$introductory_text = ($flexformValues['introductory_text'] != "") ? $flexformValues['introductory_text'] :
			 addslashes($this->pi_getLL('introductory_text'));

			$flexformValues['additional_text'] = str_replace(array("\r", "\n"), '', addslashes($flexformValues['additional_text']));
			$additional_text = ($flexformValues['additional_text'] != "") ? $flexformValues['additional_text'] :
			 addslashes($this->pi_getLL('additional_text'));
			$themefilepath = $this->conf['themepath'] ? $this->conf['themepath'] :
			 t3lib_extMgm::siteRelPath($this->extKey);
			$themeSelect = $flexformValues['themeSelect'] ? $flexformValues['themeSelect'] :
			 $this->conf['themeSelect'];


			$markerArray = array(
				'###INTROTEXT###' => $introductory_text,
				'###FULLTEXT###' => $additional_text,
				'###POSITION###' => $iconPosition,
				'###SHAPE###' => $iconType,
				'###THEME###' => $iconTheme,
				'###EXTRAOPTION###' => $extraoptions,
				'###COUNTRIES###' => $countries,
				'###COOKIE_OFFTEXT###' => $this->pi_getLL('cookieofftext'),
				'###COOKIE_PREFTEXT###' => $this->pi_getLL('cookiepreftext'),
				'###COOKIE_CHECKBOXLABEL###' => $this->pi_getLL('cookiecheckboxlabel'),
				'###COOKIE_CHECKBOXTEXT###' => $this->pi_getLL('cookiecheckboxtext'),
				'###CLOSE###' => $this->pi_getLL('close'),
				'###COOKIE_TOGGLEOFF###' => $this->pi_getLL('cookieoff'),
				'###COOKIE_TOGGLEON###' => $this->pi_getLL('cookieon'),
				'###READMORE###' => $this->pi_getLL('readmore'),
				'###READLESS###' => $this->pi_getLL('readless'),
				'###PATH###' => $themefilepath,
				'###STR_LEN_ADD_TEXT###' => strlen(htmlentities($additional_text)),
				'###TITLE###' => $flexformValues["themeTitle"] ? $flexformValues["themeTitle"] :
			$this->pi_getLL('titlelabel'),
				'###MODE###' => $consentModel );

			$gatmpl = $this->cObj->getSubpart($tmpl, '###GOOGLECODE###');
			$sub['###GOOGLECODE###'] = $sub['###GACONTROL###'] = array();
			if ($analyticsCode) {
				$m['###GACODE###'] = $analyticsCode;
				$sub['###GOOGLECODE###'] = $this->cObj->substituteMarkerArray($gatmpl, $m);
				$sub['###GACONTROL###'] = $this->cObj->getSubpart($tmpl, '###GACONTROL###');
			}
			// theme css
			switch ($themeSelect) {
				case 0 :
				$markerArray['###THEMEFILE###'] = $this->conf['defaultTheme'] ? $this->conf['defaultTheme'] :
				 t3lib_extMgm::siteRelPath($this->extKey) . 'pi1/res/css/default.css';
				break;
				case 1 :
				$markerArray['###THEMEFILE###'] = $this->conf['greyTheme'] ? $this->conf['greyTheme'] :
				 t3lib_extMgm::siteRelPath($this->extKey) . 'pi1/res/css/grey.css';
				break;
				case 2 :
				$markerArray['###THEMEFILE###'] = $this->conf['blackTheme'] ? $this->conf['blackTheme'] :
				 t3lib_extMgm::siteRelPath($this->extKey) . 'pi1/res/css/black.css';
				break;
				case 3 :
				$markerArray['###THEMEFILE###'] = $this->conf['blueTheme'] ? $this->conf['blueTheme'] :
				 t3lib_extMgm::siteRelPath($this->extKey) . 'pi1/res/css/blue.css';
				break;
				case 4 :
				$markerArray['###THEMEFILE###'] = $flexformValues['customThemeSelect'] ? $flexformValues['customThemeSelect'] :
				 $this->conf['customThemeSelect'];
				break;
			}



		   if($consentModel != 'information_only'){
			session_start();
			// cookie control for login
			if ($_SESSION['disable_session'] == 1) {
				$httpcookie = t3lib_div::getIndpEnv('HTTP_COOKIE');
				if (isset($httpcookie)) {
					$cookies = t3lib_div::trimExplode(';', $httpcookie);
					foreach($cookies as $cookie) {
						$parts = t3lib_div::trimExplode('=', $cookie);
						$name = trim($parts[0]);
						if ($name == 'PHPSESSID'  && $enableSession == 0) {
							setcookie($name, '', time()-(60 * 60 * 24 * 365 * 10));
							setcookie($name, '', time()-(60 * 60 * 24 * 365 * 10), '/', '.'.t3lib_div::getIndpEnv('HTTP_HOST'));
						}else if($name != 'PHPSESSID'){
							setcookie($name, '', time()-(60 * 60 * 24 * 365 * 10));
							setcookie($name, '', time()-(60 * 60 * 24 * 365 * 10), '/', '.'.t3lib_div::getIndpEnv('HTTP_HOST'));
						}
					}
				}
				if ($_REQUEST['user'] != "")
				t3lib_utility_Http::redirect(t3lib_div::getIndpEnv('HTTP_REFERER'));
			}
		     }
			return $this->cObj->substituteMarkerArrayCached($tmpl, $markerArray, $sub);
		}

	/**
	 * Initialize components for flexform
	 *
	 * @return	[array]		...
	 */
		protected function init() {
			$this->pi_initPIflexForm();
			$this->lConf = array();
			// Assign the flexform data to a local variable for easier access
			$piFlexForm = $this->cObj->data['pi_flexform'];
			// Traverse the entire array based on the language and assign each configuration option to $this->lConf array...
			foreach ($piFlexForm['data'] as $sheet => $data ) {
				foreach ($data as $lang => $value ) {
					foreach ($value as $key => $val ) {
						$this->lConf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
					}
				}
			}
			return $this->lConf;
		}
	}



	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cookie_control/pi1/class.tx_cookiecontrol_pi1.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cookie_control/pi1/class.tx_cookiecontrol_pi1.php']);
	}

?>