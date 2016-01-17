<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Georg Ringer <typo3 et ringerge dot org>
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
 * Plugin 'Videos & mp3 files' for the 'rgmediaimages' extension.
 *
 * @author    Georg Ringer <typo3 et ringerge dot org>
 * @package    TYPO3
 * @subpackage    tx_rgmediaimages
 */
class tx_rgmediaimages_pi1 extends tslib_pibase {
    var $prefixId      = 'tx_rgmediaimages_pi1';        // Same as class name
    var $scriptRelPath = 'pi1/class.tx_rgmediaimages_pi1.php';    // Path to this script relative to the extension dir.
    var $extKey        = 'rgmediaimages';    // The extension key.
    var $pi_checkCHash = true;
    
	/**
	* The main method of the PlugIn
	*
	* @param    string        $content: The PlugIn content
	* @param    array        $conf: The PlugIn configuration
	* @return    The content that is displayed on the website
	*/
	function main($content,$conf)    {
		$this->init($conf);

		// require the main functions
		require_once( t3lib_extMgm::extPath('rgmediaimages').'/class.tx_rgmediaimages_api.php');
		$this->media = t3lib_div::makeInstance('tx_rgmediaimages_api');
	
		
		$this->media->initSwfObject(''); // Initialize the SWF Object
		$this->media->checkForMootools($this->conf['mootools']); // check for mootools
		$this->media->setNoFlashText($this->pi_getLL('noflash'));
		
		$videoCaption 	= explode(chr(10),$this->config['caption']); // caption
		$height 				= $this->config['height']; //width
		$width 					= $this->config['width'];	// height
		$count 					= 0; // count of videos
		$media 					= array(); // array holding all videos

		
		// internal videos
		if ($this->config['mode']=='INTERNAL') {
			$videos = explode(',',$this->config['internal']);
			foreach ($videos as $key=>$value) {
   			$videos[$key] = t3lib_div::getIndpEnv('TYPO3_SITE_URL').'uploads/tx_rgmediaimages/'.$value;
   		}
		// RTMP files
		} elseif ($this->config['mode']=='RTMP') {
			$videoList = explode(chr(10),$this->config['external']);
			$video = $this->media->getRtmpSwfObj($videoList, $this->conf['conf.'], $width, $height, explode(chr(10),$this->config['rtmpDescription']));			
			$media[] = $this->cObj->stdWrap($video.$this->cObj->stdWrap($videoCaption[$count], $this->conf['caption.']), $this->conf['singleMedia.']);
	
			$found = true;
			$count++;
		
		// external files
		} else {
			$videos = explode(chr(10),$this->config['external']);
		}
		
		// configuration
		$configuration = $this->media->getConfiguration($this->conf['conf.']);
		

		$this->cObj->data['tx_rgmediaimagesWidth'] = $width;
		$this->cObj->data['tx_rgmediaimagesHeight'] = $height;

		if ($this->config['mode']!='RTMP') {
			foreach ($videos as $key=>$mediaFile) {
				$url = trim($mediaFile);
				$found = false;
						
				$this->cObj->data['tx_rgmediaimagesUrl'] = $url;
				
	
			/****************************************
			 * search for the supported file formats: wmv, mov, swf
			 ************************/
				if (!$found && strtolower(substr($url,-4))=='.wmv') {
					$video = $this->media->getWmv($url, $width, $height);
					$media[] = $this->cObj->stdWrap($video.$this->cObj->stdWrap($videoCaption[$count], $this->conf['caption.']), $this->conf['singleMedia.']);				
					$found = true;
					$count++;	
				// mov (quicktime)
				} elseif (!$found && strtolower(substr($url,-4))=='.mov') {
					$video = $this->media->getMov($url, $width, $height);
					$media[] = $this->cObj->stdWrap($video.$this->cObj->stdWrap($videoCaption[$count], $this->conf['caption.']), $this->conf['singleMedia.']);
					$found = true;
					$count++;
				// swf
				} elseif (!$found && strtolower(substr($url,-4))=='.swf') {
					$video = $this->media->getFlash($url, $width, $height, $this->config['swfobj']);
					$media[] = $this->cObj->stdWrap($video.$this->cObj->stdWrap($videoCaption[$count], $this->conf['caption.']), $this->conf['singleMedia.']);
					$found = true;
					$count++;
				}
				
	    /****************************************
	     * search for the supported hosters
	     ************************/
				if (!$found) {
					$swfObj =  $this->media->getVideos($url, $width, $height, $this->conf['conf.']);
	
					if ($swfObj !='') {
						$video = $this->media->getVideoSwfObj($url, $configuration, '', '', $swfObj);			
						$media[] = $this->cObj->stdWrap($video.$this->cObj->stdWrap($videoCaption[$count], $this->conf['caption.']), $this->conf['singleMedia.']);
						$found = true;						
						$count++;
					}   
				}				
	
	    /****************************************
	     * EXTERNAL FILES: FLV & mp3 files, played with the JW FLV Player
	     ************************/
				if (!$found && (strtolower(substr($url,-4))=='.flv' || strtolower(substr($url,-4))=='.mp3' || strtolower(substr($url,-4))=='.swf' )) {
					$this->conf['conf.']['width'] = $width;
					$this->conf['conf.']['height'] = $height;
					
					if ($this->conf['use1PixelOut']==1 && strtolower(substr($url,-4))=='.mp3') {
						$video = $this->media->getMp3($url, $this->conf['use1PixelOut'], $this->conf['confmp3.']);
					} else {
						$configuration = $this->media->getConfiguration($this->conf['conf.']);
						

						$video = $this->media->getVideoSwfObj($url, $configuration, $width, $height);
					}
			
					$media[] = $this->cObj->stdWrap($video.$this->cObj->stdWrap($videoCaption[$count], $this->conf['caption.']), $this->conf['singleMedia.']);
	
					$found = true;
					$count++;
				}
	
				
			}
		}


		// if there is any result, wrap the whole thing.
		if ($count>0) {
				$content.= $this->cObj->stdWrap(implode('',$media), $this->conf['videoWrapIfAny.']);
		}	
		
		return $this->pi_wrapInBaseClass($content);
	}


	/**
	 * The whole preconfiguration: Get the flexform values
	 *
	 * @param	array		$conf: The PlugIn configuration
	 * @return	void
	 */
	function init($conf) {
		$this->conf=$conf;		
		$this->pi_loadLL(); // Loading language-labels
		$this->pi_setPiVarDefaults(); // Set default piVars from TS
		$this->pi_initPIflexForm(); // Init FlexForm configuration for plugin
		
		// add the flexform values
		$this->config['mode']   = $this->getFlexform('sDEF', 'mode', 'mode');
		$this->config['external']   = $this->getFlexform('sDEF', 'url', 'external');
		$this->config['internal']   = $this->getFlexform('sDEF', 'internal');
		$this->config['caption']   = $this->getFlexform('sDEF', 'caption', 'caption');
		$this->config['width']   = $this->getFlexform('sDEF', 'width', 'width');
		$this->config['height']   = $this->getFlexform('sDEF', 'height', 'height');
		$this->config['swfobj']   = $this->getFlexform('sDEF', 'swfobj', 'swfobj');
		$this->config['rtmpDescription']   = $this->getFlexform('sDEF', 'rtmp');

    // add the whole js & css for the header
    $header.= (isset($this->conf['pathToCSS'])) ? '<link rel="stylesheet" href="'.$this->getPath($this->conf['pathToCSS']).'" type="text/css" />' : '';
  	$GLOBALS['TSFE']->additionalHeaderData['rgmediaimages'] = $header;

	}


	/**
	 * Gets the path to a file, needed to translate the 'EXT:extkey' into the real path
	 *
	 * @param	string  $path: Path to the file
	 * @return the real path
	 */
	function getPath($path) {
		if (substr($path,0,4)=='EXT:') {
			$keyEndPos = strpos($path, '/', 6);
			$key = substr($path,4,$keyEndPos-4);
			$keyPath = t3lib_extMgm::siteRelpath($key);
			$newPath = $keyPath.substr($path,$keyEndPos+1);
		return $newPath;
		}	else {
			return $path;
		}
	}

	/**
	 * Get the value out of the flexforms and if empty, take if from TS
	 *
	 * @param	string		$sheet: The sheed of the flexforms
	 * @param	string		$key: the name of the flexform field
	 * @param	string		$confOverride: The value of TS for an override
	 * @return	string	The value of the locallang.xml
	 */
	function getFlexform ($sheet, $key, $confOverride='') {
		// Default sheet is sDEF
		$sheet = ($sheet=='') ? $sheet = 'sDEF' : $sheet;
		$flexform = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], $key, $sheet);
		
		// possible override through TS
		if ($confOverride=='') {
			return $flexform;
		} else {
			$value = $flexform ? $flexform : $this->conf[$confOverride];
			return $value;
		}
	}  


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rgmediaimages/pi1/class.tx_rgmediaimages_pi1.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rgmediaimages/pi1/class.tx_rgmediaimages_pi1.php']);
}

?>