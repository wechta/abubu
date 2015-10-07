<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Georg Ringer <http://www.ringer.it/>
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
 * API to include media files
 *
 * @author	Georg Ringer <http://www.ringer.it/>
 */
class tx_rgmediaimages_api {

		/**
		 * Load the swfobject.js
		 *
		 * @param   string     $path: Path to override original file
		 * 		 		 
		 * @return   void
		 */
	function initSwfObject($path='') {
		if ($path=='') {
			$path = t3lib_div::getIndpEnv('TYPO3_SITE_URL').t3lib_extMgm::siteRelpath('rgmediaimages').'res/swfobject.js'; 
		}
		$GLOBALS['TSFE']->additionalHeaderData['rgmediaimages'] = '<script type="text/javascript" src="'.$path.'"></script>';
		$this->local_cObj = t3lib_div::makeInstance('tslib_cObj'); // Local cObj.	
	}

		/**
		 * Set a unique key which is used e.g. for a unique class name
		 * @param   string     $id The unique key		 		 
		 * @return  void
		 */	
	function setUniqueID($key) {
		$this->uniqueID = $key;
	}

		/**
		 * Get a unique key 		 		 
		 * @return  string unique key
		 */	
	function getUniqueID() {
		return $this->uniqueID;
	}	
	
	function setNoFlashText($text) {
		$this->noFlashText = $text;
	}
	
	function getNoFlashText() {
		return $this->noFlashText;
	}	
	
		/**
		 * Create the for the FLV player understandable configuration
		 * @param   array     $c: Configuration from Constants/Setup	 		 
		 * @return   modified and configuration array
		 */	
	function getConfiguration($c) {
		
			// change magenta to ff00ff with a helping class
		require_once(t3lib_extMgm::extPath('rgmediaimages').'lib/class.tx_rgmediaimages_colormap.php'); 
		$this->color = t3lib_div::makeInstance('tx_rgmediaimages_colormap'); // Create new instance for div class
    
			// trim errors from colors
    $colorConfig = array('backgroundColor', 'foregroundColor', 'highlightColor', 'screenColor', 'youtubeColor1', 'youtubeColor2');		
		foreach ($colorConfig as $key=>$value) {
				// but just if it concerns a color 
			if (in_array($key, $colorConfig) && $value!='') { 
				$value = $this->color->nameToRGB($value);
				$c[$key] = trim($value,'#');
			}
    }
		
		$config = array();  
		$config['width'] 						= 'width='.intval($c['width']);
		$config['height'] 					= 'height='.intval($c['height']);
		$config['backgroundColor'] 	= ($c['backgroundColor']!='FFFFFF') ? 'backcolor=0x'.$c['backgroundColor'] 	: '';
		$config['foregroundColor'] 	= ($c['foregroundColor']!='000000') ? 'frontcolor=0x'.$c['foregroundColor'] : '';
		$config['highlightColor'] 	= ($c['highlightColor']!='000000') 	? 'lightcolor=0x'.$c['highlightColor'] 	: '';
		$config['screenColor'] 			= ($c['screenColor']!='000000') 		? 'screencolor=0x'.$c['screenColor'] 		: '';
		$config['backgroundImage'] 	= ($c['backgroundImage']!='') 			? 'image=http://'.$c['backgroundImage'] : '';
		$config['largeControllBar'] = ($c['largeControllBar']==1) 			? 'largecontrols=true' 									: '';
		$config['showDigits'] 			= ($c['showDigits']!='true') 				? 'showdigits='.$c['showDigits'] 				: '';
		$config['showDownload'] 		= ($c['showDownload']==1) 					? 'showdownload=true'										: '';
		$config['showEqualizer'] 		= ($c['showEqualizer']==1) 					? 'showeq=true' 												: '';
		$config['showLoadPlay'] 		= ($c['showLoadPlay']==0) 					? 'showicons=false' 										: '';
		$config['showStop']      		= ($c['showStop']==1)      					? 'showstop=true' 											: '';
		$config['showVolume'] 			= ($c['showVolume']==0) 						? 'showvolume=false' 										: '';    
		$config['volume'] 					= ($c['volume']!=80) 								? 'volume='.intval($c['volume']) 				: '';
		$config['logo'] 						= ($c['logo']!='') 									? 'logo=http://'.$c['logo'] 						: '';
		$config['showNavigation'] 	= ($c['showNavigation']==0) 				? 'shownavigation=false' 								: '';
		$config['autoStart'] 				= ($c['autoStart']!='false' && $c['autoStart']!='0') ? 'autostart='.$c['autoStart'] 					: '';
		$config['autoRepeat'] 			= ($c['autoRepeat']!='false' && $c['autoRepeat']!='0') 			? 'repeat='.$c['autoRepeat'] 						: '';
		
			// youtube configuration
		$config['youtubeColor1'] 		= ($c['youtubeColor1']!='') 				? 'color1=0x'.$c['youtubeColor1'] 			: '';
		$config['youtubeColor2'] 		= ($c['youtubeColor2']!='') 				? 'color2=0x'.$c['youtubeColor2'] 			: '';
		$config['youtubeBorder'] 		= ($c['youtubeBorder']==1) 					? 'border=1' 														: '';
		
			// callback using EXT:mediaplayerstatistics 
		if (t3lib_extMgm::isLoaded('mediaplayerstatistics') && $c['callback']==1) {
			$config['callback'] = "'/typo3conf/ext/nc_videostatistics/res/mediaPlayerStatisticsMySQL.php'";
		}
		

			// delete the empty entries of the conig array
		foreach ($config as $key=>$value) {
			if($value=='') {
				unset ($config[$key]);
			}
		}
		#t3lib_div::print_array($config);
		
		return $config;
	}	
	
 
	
		/**
		 * Load a video from an external video hoster by using an swfObject
		 *
		 * @param   string     $url: The url to the video
		 * @param   int     $width: width of the wideo
		 * @param   int     $height: height of the wideo
		 * @param   array     $config: some configuration
		 * @return   string     SwfObject including the video and its parameter
		 */  
  function getVideos($url, $width, $height, $config) {
  	$video = '';

		$url = trim($url);
		$url = str_replace('http://', '', $url);

			// youtube
		if (strpos($url,'outube.com'))  {
			$found = 1;
			$split = explode('=',$url);
			$video = 'new SWFObject("http://www.youtube.com/v/'.$split[1].'", "sfwvideo", "'.$width.'","'.$height.'", "9", "#'.$config['backgroundColor'].'");';
		// Dailymotion
		} elseif (strpos($url,'ailymotion.co'))  {
			$found = 1;
			$video = 'new SWFObject("http://'.$url.'", "sfwvideo", "'.$width.'","'.$height.'", "9", "#'.$config['backgroundColor'].'");';
		// video.google.com/.de
		} elseif (strpos($url,'ideo.google.'))  {
			$found = 1;        
			$split = explode('=',$url);
			$video = 'new SWFObject("http://video.google.com/googleplayer.swf?docId='.$split[1].'&hl='.$GLOBALS['TSFE']->lang.'", "sfwvideo", "'.$width.'","'.$height.'", "9", "#'.$config['backgroundColor'].'");';  
		// Metacafe
		} elseif (strpos($url,'metacafe.'))  {
			$found = 1;
			$split = explode('/',$url);
			$video = 'new SWFObject("http://www.metacafe.com/fplayer/'.$split[2].'/.swf", "sfwvideo", "'.$width.'","'.$height.'", "9", "#'.$config['backgroundColor'].'");';  
		// MyVideo.de
		} elseif (strpos($url,'yvideo.de'))  {
			$found = 1;
			$split = explode('/',$url);
			$video = 'new SWFObject("http://www.myvideo.de/movie/'.$split[2].'", "sfwvideo", "'.$width.'","'.$height.'", "9", "#'.$config['backgroundColor'].'");';  
		// clipfish.de
		} elseif (strpos($url,'lipfish.de'))  {
			$found = 1;
			$split = explode('=',$url);
			$video = 'new SWFObject("http://www.clipfish.de/videoplayer.swf?as=0&videoid='.$split[1].'", "sfwvideo", "'.$width.'","'.$height.'", "9", "#'.$config['backgroundColor'].'");';   
		// sevenload
		} elseif (strpos($url,'sevenload.com'))  {
			$found = 1;
			$split = explode('/',$url);
			$video = 'new SWFObject("http://de.sevenload.com/pl/'.$split[2].'/'.$width.'x'.$height.'/swf", "sfwvideo", "'.$width.'","'.$height.'", "9", "#'.$config['backgroundColor'].'");';  
		// LiveLeak
		} elseif (strpos($url,'iveleak.com'))  {
			$found = 1;
			$split = explode('=',$url);
			$video = 'new SWFObject("http://www.liveleak.com/e/'.$split[1].'", "sfwvideo", "'.$width.'","'.$height.'", "9", "#'.$config['backgroundColor'].'");';  
		// Slideshare
		} elseif (strpos($url,'slideshare'))  {
			$found = 1;
			// WordPress widget url, much easier to process: url = [slideshare id=xxxxxx&doc=nononononononnonono&w=425]
			$url = substr($url, 1, strlen($url)-1);
			$split = explode('=',$url);
			$video = 'new SWFObject("http://static.slideshare.net/swf/ssplayer2.swf?id='.$split[1].'&doc='.$split[2].'&w='.$split[3].'", "sfwvideo", "'.$width.'","'.$height.'", "9", "#'.$config['backgroundColor'].'");';
		// Vimeo
		} elseif (strpos($url,'imeo.com'))  {
			$found = 1;
			$split = explode('vimeo.com/',$url);
			$video = 'new SWFObject("http://vimeo.com/moogaloop.swf?clip_id='.$split[1].'", "sfwvideo", "'.$width.'","'.$height.'", "9", "#'.$config['backgroundColor'].'");';  
		// Slideshare
		} elseif (strpos($url,'sf.tv')) {
			$found = 1;
			$split = explode('videoportal.sf.tv/video?id=', $url);
			$video = 'new SWFObject("http://www.sf.tv/videoplayer/embed/' . $split[1] . '",	"sfwvideo", "' . $width . '","' . $height . '", "9", "#' . $config['backgroundColor'] . '");';
		}
		
		return $video;
	}

   /**
    * Emebed a SwfObject using JS 
    *
    * @param   string     $url: The url to the video
    * @param   array     $config: configuration of the swfobject
    * @param   int     $width: width of the wideo
    * @param   int     $height: height of the wideo  
    * @param   string     $overrideSwfObj: override the swfObject
    * 		 		 
    * @return   string     The video
    */
	function getVideoSwfObj($url, $config, $width, $height, $overrideSwfObj='') {
		$uniqueUid = ' rgmi'.$this->getUniqueID().' ';
		$uniqueKey = md5($url);

		$url = trim($url);
		$url = str_replace('http://', '', $url);
		
			// generate the SWFObject. This can be overriden, by $overrideSwfObj, e.g. if the video is from youtube.
		$videoObject = '';
		if ($overrideSwfObj=='') {
			$videoObject = 'var so = new SWFObject("'.t3lib_div::getIndpEnv('TYPO3_SITE_URL').t3lib_extMgm::siteRelpath('rgmediaimages').'res/mediaplayer.swf","mpl","'.$width.'","'.$height.'","8");';
		} else {
			$videoObject = 'var so = '.$overrideSwfObj;
		}

			// configuration: If there is one, split it up correctly into the addVariable(key, value);
		if (is_array($config)) {
			
				// youtube gets a special treatment 
			if ($overrideSwfObj!='' && strpos($overrideSwfObj,'SWFObject("http://www.youtube.com')) {		
					// because just 2 configs are allowed: youtubeColor1 + youtubeColor2 delete the rest
				foreach ($config as $key=>$value) {
	    		if ($key!='youtubeColor1' && $key!='youtubeColor2' && $key!='youtubeBorder') {
						unset($config[$key]);	
					}
	    	}
				// delete youtube's colors
			} else {
				unset($config['youtubeColor1']);
				unset($config['youtubeColor2']);
				unset($config['youtubeBorder']);
			}

					
				// set the entries in correct syntax
	    foreach ($config as $key=>$value) {
	      $split = explode('=',$value);
	    	$configuration.= 'so.addVariable(\''.$split[0].'\',\''.$split[1].'\');'."\r";
	    }
	    
	    	// download is configured here because it is far easier
	    if ($config['showDownload'] == 'showdownload=true') {
	    	$configuration.= 'so.addVariable(\'link\',\''.t3lib_div::getIndpEnv('TYPO3_SITE_URL').t3lib_extMgm::siteRelpath('rgmediaimages').'saveFile.php?file=http://'.$url.'\');'."\r";
			}
    }

		$video = '<span class="rgmediaimages-player'.$uniqueUid.'" id="player'.$uniqueKey.'">'.$this->getNoFlashText().'</span>
							<script type="text/javascript">
								'.$this->mootools['begin'].'
								'.$videoObject.'
								so.addParam("allowscriptaccess","always");
								so.addParam("allowfullscreen","true");
								so.addParam("wmode", "transparent");
								so.addVariable("file","http://'.$url.'");
								'.$configuration.'
								so.write("player'.$uniqueKey.'");
								'.$this->mootools['end'].'
							</script>';	
		return $video;
	}

   /**
    * Emebed a RtmpSwfObj
    *
    * @param   string/array     $url: The url to the stream
    * @param   array     $config: configuration of the swfobject
    * @param   int     $width: width of the wideo
    * @param   int     $height: height of the wideo  
    * 		 		 
    * @return   string     The video
    */	
	function getRtmpSwfObj($url, $config, $width, $height, $description='') {
		$uniqueUid = ' rgmi'.$this->getUniqueID().' ';
		$backgroundColor = ($config['backgroundColor']) ? $config['backgroundColor'] : 'ffffff';		

		$videoList = '';
		if (is_array($url)) {
			
			// go through the files
			foreach ($url as $key=>$singleUrl) {
				$singleUrl = trim($singleUrl);
				$singleUrl = str_replace(array('http://', 'rtmp://'), array('',''), $singleUrl);

				
				$descriptionSingle = explode('|', $description[$key]);
				
				$count = $key+1;
				
				$videoList.= '
											so.addVariable("videofile'.$count.'", "rtmp://'.$singleUrl.'");
											so.addVariable("videoicon'.$count.'", "");
											so.addVariable("videotext'.$count.'", "'.htmlspecialchars($descriptionSingle[0]).'");';
   		}
   		
   		$videoList.= 'so.addVariable("vidbreite", "400");';
   		$uniqueKey = md5($videoList);
   		
		} else {
		#	$url = 'rtmp://h1340983.stratoserver.net/vod/mp4|AdobeBand_1500K_H264.mp4';
			$url = trim($url);
			$url = str_replace(array('http://', 'rtmp://'), array('',''), $url);
			$uniqueKey = md5($url);
			// split the url into url & id
		#	$url = explode('|',$url);
				
			$videoList = 'so.addVariable("videofile1", "rtmp://'.$url.'");
										so.addVariable("vidbreite", "'.$width.'");'; 
		}

		
		$video = '<span class="rgmediaimages-player'.$uniqueUid.'" id="player'.$uniqueKey.'">'.$this->getNoFlashText().'</span>
							<script type="text/javascript">
							'.$this->mootools['begin'].'
							var so = new SWFObject("'.t3lib_div::getIndpEnv('TYPO3_SITE_URL').t3lib_extMgm::siteRelpath('rgmediaimages').'res/blitzvideoplayer4.swf", "blitzvideoplayer", "'.$width.'", "'.$height.'", "6", "#'.$backgroundColor.'");
				
							so.addVariable("playlistengine", "1");
							so.addVariable("quickstart", "0");
							so.addVariable("mitstartbutton", "1");
							// so.addParam("wmode", "transparent");
															
							'.$videoList.'

							so.addVariable("bgc", "'.$backgroundColor.'");
							so.addVariable("auto_hide_player", "1");
							so.addParam("allowFullScreen", "true");
							so.write("player'.$uniqueKey.'");
							'.$this->mootools['end'].'
							</script>';			

		return $video;
	}	

   /**
    * Emebed a SwfObject using the emebed tag. Not the best way because code is not valid anymore! 
    * You should use JS to emebed the video 
    *
    * @param   string     $url: The url to the video
    * @param   array     $config: configuration of the video
    * @param   int     $width: width of the wideo
    * @param   int     $height: height of the wideo     
    * @return   string     The video
    */
	function getVideoEmbed($url, $config, $width, $height) {
		// configuration 
		if ($configuration) {
			$configuration = '&'.implode('&',$config);
		}
		
		$url = trim($url);
		$url = str_replace('http://', '', $url);		
		$uniqueUid = ' rgmi'.$this->getUniqueID().' '; 
		
		$video = '<span class="rgmediaimages-player'.$uniqueUid.'">
                <embed src="'.t3lib_extMgm::siteRelpath('rgmediaimages').'res/mediaplayer.swf" width="'.$width.'" height="'.$height.'" allowfullscreen="true" allowscriptaccess="always" flashvars="&file=http://'.$url.$configuration.'" />
              </span>';
		return $video;	
	}
	
   /**
    * Check if mootools is anywhere on the website. 
    * Check is based on if t3mootools is configured or if $check is true    
    *
    * @param   boolean     $check: Just an additional value
    * @return   void
    */
	function checkForMootools($check=false) {
		// predefined empty
		$this->mootools['begin'] 	= '';
		$this->mootools['end'] 		= '';

		// if t3mootools is loaded, include it
		if (t3lib_extMgm::isLoaded('t3mootools'))    {
			require_once(t3lib_extMgm::extPath('t3mootools').'class.tx_t3mootools.php');
		} 	 
		
		// if t3mootools is configured or mootools is included manually
		if (defined('T3MOOTOOLS') || $check) {
			// let t3mootools know that it should be included 
			if (defined('T3MOOTOOLS')) {
				tx_t3mootools::addMooJS();
			}
			
			// set the addEvent
			$this->mootools['begin'] 	= 'window.addEvent("load", function(){';
			$this->mootools['end'] 		= ' });';
		}	

	}
	
   /**
    * Load content elements with their ID
    *
    * @param   string     $ceElements: The content elements
    * @param   int     $width: width of the content element
    * @param   int     $height: height of the content element     
    * @return   string    The content element
    */ 
	function getCE($ceElements, $width, $height) {
		$uniqueUid = ' rgmi'.$this->getUniqueID().' ';
		$ceElements = trim($ceElements);

	  $ceConfig = array('tables' => 'tt_content','source' => $ceElements,'dontCheckPid' => 1);    
	  $ce = '<div class="rgmediaimages-content'.$uniqueUid.'" style="width:'.$width.'px; height:'.$height.'px;>'.$this->local_cObj->RECORDS($ceConfig).'</div>';
		
		return $ce;
	}


   /**
    * Displays an Iframe
    *
    * @param   string     $url: url to the iframe
    * @param   int     $width: width of the iframe
    * @param   int     $height: height of the iframe   
    * @return   string    The iframe
    */
	function getIframe($url, $width, $height) {
		$uniqueUid = ' rgmi'.$this->getUniqueID().' ';        
		$url = trim($url);
		$url = str_replace('http://', '', $url);
    
    $iframe = '<iframe class="rgmediaimages-iframe'.$uniqueUid.'" height="'.$height.'" width="'.$width.'" src="http://'.$url.'" scrolling="yes"></iframe> ';
    
    return $iframe;
	}
	
   /**
    * Emebed a plain Flash file
    *
    * @param   string     $url: The url to the video
    * @param   int     $width: width of the wideo
    * @param   int     $height: height of the wideo  
    * @param   boolean     $swfObj: Usage of swfObj
    * @param   array     $mootools: If mootools is on the page, the array is filled with some code to use it
    * 		 		 
    * @return   string     The video
    */
	function getFlash($url, $width, $height, $swfObj) {
		$uniqueUid = ' rgmi'.$this->getUniqueID().' ';
		$uniqueKey = md5($url);
		
		$url = trim($url);
		$url = str_replace('http://', '', $url);

		if ($swfObj == 1) {
			$video = '<span class="rgmediaimages-player'.$uniqueUid.'" id="player'.$uniqueKey.'">'.$this->getNoFlashText().'</span>
			          <script type="text/javascript">
									'.$this->mootools['begin'].'
									var so = new SWFObject("http://'.$url.'","mpl","'.$width.'","'.$height.'","8");
									so.addParam(\'allowscriptaccess\',\'always\');
									so.addParam(\'allowfullscreen\',\'true\');
									so.addParam("wmode", "transparent");
									so.addVariable("file","http://'.$url.'");
									so.addVariable("width","'.$width.'");
									so.addVariable("height","'.$height.'");
									so.write("player'.$uniqueKey.'");
			            '.$this->mootools['end'].'
			          </script>';	
		} else {
			$video = '<span class="rgmediaimages-player'.$uniqueUid.'">
                <embed src="http://'.$url.'" width="'.$width.'" height="'.$height.'" allowfullscreen="true" allowscriptaccess="always" flashvars="&file='.$url.'" />
              </span>';		
		}
		return $video;
	}	
	
   /**
    * Load an external MOV video
    *
    * @param   string     $url: The url
    * @param   int     $width: width of the wideo
    * @param   int     $height: height of the wideo     
    * @return   string     Embed object including the video and its parameter
    */  
	function getMov($url, $width, $height) {
		$url = trim($url);
		$url = str_replace('http://', '', $url);
	
		$height = $height+16;
    $video = '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase="http://www.apple.com/qtactivex/qtplugin.cab" width="'.$width.'" height="'.$height.'" >
              <param name="src" value="http://'.$url.'">
              <param name="autoplay" value="true">
              <param name="type" value="video/quicktime" width="'.$width.'" height="'.$height.'">      
              <embed src="http://'.$url.'" width="'.$width.'" height="'.$height.'" autoplay="false" type="video/quicktime" pluginspage="http://www.apple.com/quicktime/download/">
            </object>';
    return $video;
	}

   /**
    * Load an external WMV video
    *
    * @param   string     $url: The url
    * @param   int     $width: width of the wideo
    * @param   int     $height: height of the wideo     
    * @return   string     Embed object including the video and its parameter
    */  
	function getWmv ($url, $width, $height) {
		$url = trim($url);
		$url = str_replace('http://', '', $url);

		$video = '<object id="MediaPlayer" width='.$width.' height='.$height.' classid="CLSID:22D6f312-B0F6-11D0-94AB-0080C74C7E95" standby="Loading Windows Media Player components…" type="application/x-oleobject" codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,7,1112">
              <param name="filename" value="http://'.$url.'">
              <param name="Showcontrols" value="true">
              <param name="autoStart" value="false">
              <embed type="application/x-mplayer2" src="http://'.$url.'" name="MediaPlayer" width="'.$width.'" height="'.$height.'"></embed>
            </object>';
		
		return $video; 	
	} 

   /**
    * Emebed a mp3 file using the 1pixelout player. Possible to embed it as an 
    * object or by using the SWFObject
    *
    * @param   string     $url: The url to the mp3 file
    * @param   boolean     $swfObject: If set, the SWFObject is used
    * @param   array     $mootools: If mootools is on the page, the array is filled with some code to use it
    * @param   array     $config: configuration of the 1pixeloutplayer
    * 		 		 
    * @return   string     The video
    */	
	function getMp3($url, $swfObject, $config) {
		$url = trim($url);
		$url = str_replace('http://', '', $url);
		
		// unqiue keys
		$uniqueKey = md5($url);
		$uniqueUid = ' rgmi'.$this->getUniqueID().' ';

		// get the necessary js
		$playerPath = t3lib_div::getIndpEnv('TYPO3_SITE_URL').t3lib_extMgm::siteRelpath('rgmediaimages').'res/audio/';
		$GLOBALS['TSFE']->additionalHeaderData['rgmediaimagesaudio'] = '<script type="text/javascript" src="'.$playerPath.'audio-player.js"></script>';
		
		// get widht/height and unset it because not needed in following configuration
		$width = $config['width'];
		$height = $config['height'];
		unset($config['width']);
		unset($config['height']);
		
		// change magenta to ff00ff with a helping class
		require_once(t3lib_extMgm::extPath('rgmediaimages').'lib/class.tx_rgmediaimages_colormap.php'); 
		$this->color = t3lib_div::makeInstance('tx_rgmediaimages_colormap'); // Create new instance for div class
    
		// trim errors from colors		
		foreach ($config as $key=>$value) {
				$value = $this->color->nameToRGB($value);
				$config[$key] = trim($value,'#');
    }
		
		// color configuration
		if ($config['bg']=='f8f8f8') 							unset($config['bg']);
		if ($config['leftbg']=='eeeeee') 					unset($config['leftbg']);
		if ($config['lefticon']=='666666') 				unset($config['lefticon']);
		if ($config['rightbg']=='cccccc') 				unset($config['rightbg']);
		if ($config['rightbghover']=='999999') 		unset($config['rightbghover']);
		if ($config['righticon']=='666666')				unset($config['righticon']);
		if ($config['righticonhover']=='ffffff') 	unset($config['righticonhover']);
		if ($config['text']=='666666')						unset($config['text']);
		if ($config['slider']=='666666') 					unset($config['slider']);
		if ($config['track']=='ffffff') 					unset($config['track']);
		if ($config['border']=='666666') 					unset($config['border']);
		if ($config['loader']=='9FFFB8') 					unset($config['loader']);
		if ($config['loop']=='no' || $config['loop']==0) 						unset($config['loop']);
		if ($config['autostart']=='no' || $config['autostart']== 0) unset($config['autostart']);
		
		if (is_array($config)) {
			foreach ($config as $key=>$value) {
	  		// if loop || autostart, no color prefix (0x) is needed
				$prefix = ($key != 'loop' && $key != 'autostart')  ? '0x' : ''; 
				$configuration.= '&amp;'.$key.'='.$prefix.$value;
	  	}
	  }
	  
	  # t3lib_div::print_array($config);

		// set the mp3 player as embedded object
		if ($swfObject!=1) { 
			$mxp3 = '<object type="application/x-shockwave-flash" data="'.$playerPath.'player.swf" id="audioplayer'.$uniqueKey.'" height="'.$height.'" width="'.$width.'">
								<param name="movie" value="'.$url.'player.swf">
								<param name="FlashVars" value="playerID='.$uniqueKey.'&amp;soundFile=http://'.$url.$configuration.'">
								<param name="quality" value="high">
								<param name="menu" value="false">
								<param name="wmode" value="transparent">
							</object>';		
		
		// set the mp3 player as SWFObj
		} else {			
			$mp3 = '<span class="rgmediaimages-player'.$uniqueUid.'" id="player'.$uniqueKey.'">'.$this->getNoFlashText().'</span>
							<script type="text/javascript">
								'.$this->mootools['begin'].'
								var so = new SWFObject("'.$playerPath.'player.swf", "audioplayer", "'.$width.'", "'.$height.'", "7", "#FFFFFFF");
								so.addParam("http://'.$url.'");
								so.addParam("quality", "high");
								so.addParam("wmode", "transparent");
								so.addParam("menu", "false");
								so.addParam("pluginurl","http://www.macromedia.com/go/getflashplayer");
								so.addParam("flashvars","playerID='.$uniqueKey.'&amp;soundFile=http://'.$url.$configuration.'");
								so.write("player'.$uniqueKey.'");
								'.$this->mootools['end'].'
							</script>';
		}

		return $mp3;	
	}

}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS']['XCLASS']['ext/rgmediaimages/class.tx_rgmediaimages_api.php'])	{
	include_once($GLOBALS['TYPO3_CONF_VARS']['XCLASS']['ext/rgmediaimages/class.tx_rgmediaimages_api.php']);
}

?>