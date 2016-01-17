<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Georg Ringer <http://www.ringer.it>
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


    // DEFAULT initialization of a module [BEGIN]

$BACK_PATH = '../../../../typo3/';
define('TYPO3_MOD_PATH', '../typo3conf/ext/rgmediaimages/wizard/');
$MCONF['name']='xMOD_tx_rgmediaimages_tt_content_tx_rgmediaimages_alttextwiz';
$MCONF['access']='user,group';
$MCONF['script']='index.php';


require_once ($BACK_PATH.'init.php');
require_once ($BACK_PATH.'template.php');
$LANG->includeLLFile('EXT:rgmediaimages/wizard/locallang.xml');

require_once (PATH_t3lib."class.t3lib_scbase.php");
require_once (PATH_t3lib."class.t3lib_tceforms.php");

/**
 * rgmediaimages module 
 *
 * @author    Georg Ringer <http://www.ringer.it>
 * @package    TYPO3
 * @subpackage    tx_rgmediaimages
 */

class tx_rgmediaimages_tt_content_tx_rgmediaimages_alttextwiz extends t3lib_SCbase {

		// Internal, static: GPvars
	var $P;						// Wizard parameters, coming from TCEforms linking to the wizard.
	var $FORMCFG;				// The array which is constantly submitted by the multidimensional form of this wizard.
	var $special;				// Indicates if the form is of a dedicated type, like "formtype_mail" (for tt_content element "Form")

  /**
   * Adds items to the ->MOD_MENU array. Used for the function menu selector.
   *
   * @return    [type]        ...
   */
  function menuConfig()    {
      global $LANG;
      $this->MOD_MENU = Array (
          'function' => array (
              '1' => $LANG->getLL('function1'),
              '2' => $LANG->getLL('function2'),
    #          '3' => $LANG->getLL('function3'),
          )
      );
      parent::menuConfig();
  }

  /**
   * Main function of the module. Write the content to $this->content
   *
   * @return   the wizard
   */
  function main()    {
      global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

				// Draw the header.
			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;

 				// Instance of tceforms
			$this->tceforms = t3lib_div::makeInstance("t3lib_TCEforms"); 
			$this->tceforms->backPath = $BACK_PATH; 
			$this->tceforms->initDefaultBEMode();

				// GPvars:
			$this->P = t3lib_div::_GP('P');
			$this->GET = t3lib_div::_GET();
			
				// JavaScript && CSS
			$this->doc->JScode = '
					<script type="text/javascript">
						script_ended = 0;
						function jumpToUrl(URL)	{
							document.location = URL;
						}
						function gid(name) {
							return document.getElementById(name);
						}
						function toggleMenu(name) {
							if (gid(name).style.display == "block") {
								gid(name).style.display = "none";
							} else {
								gid(name).style.display = "block";
							}
						}					
				</script>
				<script src="res/cp2/color_functions.js"></script>		
				<script type="text/javascript" src="res/cp2/js_color_picker_v2.js"></script>
				
				<link rel="stylesheet" href="res/cp2/js_color_picker_v2.css" media="screen">
				<link rel="stylesheet" type="text/css" href="res/styles.css" />				
			';
		
				
			$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
			$access = is_array($this->pageinfo) ? 1 : 0;
			if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id) || ($BE_USER->user["uid"] && !$this->id))    {
				if (($BE_USER->user['admin'] && !$this->id) || ($BE_USER->user["uid"] && !$this->id))   {
								
					$this->pageinfo = array(
						'title' => '[root-level]',
						'uid'   => 0,
						'pid'   => 0
					);
				}
				
					// try to save the whole configuration
				$this->saveConfig();
				
					// set up the wizard
				$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />';
				
				$this->content.=$this->doc->startPage($LANG->getLL('header1'));

				$this->content.=$this->doc->spacer(5);
			#	$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
				
					// Render content:
				$this->content .= $this->tceforms->printNeededJSFunctions_top();
				$this->moduleContent();
				$this->content.= $this->tceforms->printNeededJSFunctions();
				
					// ShortCut
				if ($BE_USER->mayMakeShortcut())    {
					$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
				}
			}
			
			$this->content.=$this->doc->spacer(10);
			
    }

	   /**
	    * Try to save the wizard
	    *
			* @return  void
	    */    
    function saveConfig() {
				// If save command found, save
			if ($_POST['savedok_x'] || $_POST['saveandclosedok_x'])	{
				$saveTo = array();
				$saveTo = t3lib_div::_POST('rg');

				# t3lib_div::print_array($saveTo); #debug
				$savedConfig = array();

					// browse through every media files' config 
				foreach ($saveTo as $i=>$singleLine) {
   				unset($singleLine['info']);
   				
   				if($singleLine['nonvalid']==1) {
					 $savedConfig[$i].=''; 
					 } 
					// if there is something to save
   				elseif (is_array($singleLine)) {
						 // delete nonuses fields
						 foreach ($singleLine as $key=>$value) {
         			if ($key=='nonvalid') {
							 	$savedConfig[$i].='';
							
								// go on if there is a value, no need to save just keys
							} elseif($value!='') {
							 	
								// if it a string, go on
								if (!is_array($value)) {
							 		$default = $this->getDefaultValues($key);
									if ($default!=$value) {
										$savedConfig[$i].=$key.':'.$value.',';
									}
							 	
								// if it is an array and its value is'nt empty, presume it is an url and modify it to get correct syntax 
								} elseif ($value['file']!='') {
									$newValue = $value['file'];
							 		$newValue = str_replace(t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT'), t3lib_div::getIndpEnv('TYPO3_SITE_URL'),$newValue);
							 		$newValue = str_replace('http://', '', $newValue);
							 		$newValue = str_replace('//', '/', $newValue);								 		
								 	$savedConfig[$i].=$key.':'.$newValue.',';
								}
							} 
							
         		}
         		
         		// trim the , at the end
         		$savedConfig[$i] = trim($savedConfig[$i],'\,');
       		}
   			}
   			
   			# t3lib_div::print_array($savedConfig); #debug
   			
				// save the whole config to the record
				$saveArray = array();
				$saveArray[$this->GET['config']] = implode(chr(10),$savedConfig);
				$saveArray['tstamp'] = time();
				
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery ($this->GET['table'], 'uid='.intval($this->P['uid']), $saveArray); 
			}
			
			// if the wizard should be closed, return to the record
			if ($_POST['saveandclosedok_x']) {
				header('Location: '.t3lib_div::locationHeaderUrl($this->P['returnUrl']));
				exit;
			}		
		}

	   /**
	    * The main wizard function
	    *
			* @return  everything of the wizard
	    */    
    function getMainConfig() {
			global $LANG;
			$error = array();
			$imageList = array();
			
			// db query to get the record
			$fieldArray = array('uid');
			if (isset($this->GET['config'])) $fieldArray[] = $this->GET['config'];
			if (isset($this->GET['internal'])) $fieldArray[] = $this->GET['internal'];
			if (isset($this->GET['external'])) $fieldArray[] = $this->GET['external'];
			
			// get all the necessary fields
			$fields = implode(',',$fieldArray);
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($fields, $this->P['table'], 'deleted=0 AND uid='.intval($this->P['uid']));
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);		
 
			// if there is a record
			if ($row['uid']=='') {
				$error[] = 'No content element found!';
			} 

			if (!$found) {
				$error[] = 'None of the files is a media file with the endings like '.implode(', ', $this->getFileEndings());
			}

			// mix external & internal files
			$newFiles = array();
			
			// internal files
			if (isset($this->GET['internal'])) {
				$tmp = t3lib_div::trimExplode(',', $row[$this->GET['internal']]);
				foreach ($tmp as $key=>$value) {
						if ($value!='') $newFiles[] = $value;	
				}
			}

			// external files			
			if (isset($this->GET['external'])) {
				$tmp =  t3lib_div::trimExplode(chr(10), $row[$this->GET['external']]);
				foreach ($tmp as $key=>$value) {
						if ($value!='') $newFiles[] = $value;	
				}
			}
			


			// configuration is for both
			$configuration = explode(chr(10),$row[$this->GET['config']]);
			
				// Setting form tag:
			list($rUri) = explode('#',t3lib_div::getIndpEnv('REQUEST_URI'));
			$content.= '<form action="'.htmlspecialchars($rUri).'" method="post"  name="editform" id="editform" >';
			
			// browse through every file
			foreach ($newFiles as $key=>$singleImg) {
				$content.= $this->getSingleMediaFile($singleImg, $row, $key, $configuration[$key]);
			}
			
				// Add a help set
			$content.= '<div class="help">'.$this->getFieldset($this->getHelp(),'helpset', 0).'</div>';				

				// Add saving buttons in the bottom:
			$content.= '<div id="c-saveButtonPanel">
										<input type="image" class="c-inputButton" name="savedok"'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/savedok.gif','').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:rm.saveDoc',1).'" /> 
										<input type="image" class="c-inputButton" name="saveandclosedok"'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/saveandclosedok.gif','').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:rm.saveCloseDoc',1).'" /> 
										<a href="#" onclick="'.htmlspecialchars('jumpToUrl(unescape(\''.rawurlencode($this->P['returnUrl']).'\')); return false;').'"> 
											<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/closedok.gif','width="21" height="16"').' class="c-inputButton" title="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:rm.closeDoc',1).'" alt="" />
										</a>
									</div>
									</form>';

			# $content.= 'GET:'.t3lib_div::view_array($_GET).'<br />'.'POST:'.t3lib_div::view_array($_POST).'<br />'; // debug

			return $content;
		}


	   /**
	    * Render every single media file
	    *
			* @param   string      $file: The original file	    

			* @param   int      $key: The key of the file    
			* @param   array      $config: The configuration array
			* @return  the single media file with all its options
	    */		
		function getSingleMediaFile($file, $row, $key, $config) {
				// no valid file
			if (!in_array(strtolower(substr($file,-4)), $this->getFileEndings())&&1==2) {
				$content = '<div class="singlefileWrong">'.$this->getFileHeader($file, $key).'<br />This is no valid file</div>
										<input type="hidden" name="rg['.$key.'][info]" value="'.$file.'" />
										<input type="hidden" name="rg['.$key.'][nonvalid]" value="1" />		';
			
				// proceed
			} else {
			
					// config
				$configRaw = explode(',',$config);
				$newConfig = array();
				foreach ($configRaw as $key2=>$value) {
					$value = str_replace('http://','',$value);
	   			$split = explode(':', $value);
	   			$newConfig[$split[0]] = $split[1];
	   		}
	   		
	   			// true / false dropdown
				$conf[0] = $this->getLL('false');
				$conf[1] = $this->getLL('true');				

	   		
					// Set the different options
				$content.= $this->getFileHeader($file, $key);
				$tab.= $this->getUrl($key, $file, $newConfig);
				$content.= $this->getFieldset($tab, 'defaultset', $key, 1);
				
				$allTabs = '';

	   			// youtube gets a special treatment
				if (strpos($newConfig['file'],'outube.com/') || strpos($file,'outube.com/') ) {
					$tab = $this->getColorPicker($key, 'youtubeColor1', $newConfig);
					$tab.= $this->getColorPicker($key, 'youtubeColor2', $newConfig);
					$tab.= $this->getCheckbox($key, 'youtubeBorder', $newConfig);										
					$allTabs.= $this->getFieldset($tab, 'youtubeset', $key, 1);
				
					// FLV
				} elseif (in_array(strtolower(substr($file,-4)), $this->getFileEndings()) || (in_array(strtolower(substr($url,-4)), $this->getFileEndings())) )  {
				
					// color fieldset
					$tab = $this->getColorPicker($key, 'screenColor', $newConfig);
					$tab.= $this->getColorPicker($key, 'foregroundColor', $newConfig);
					$tab.= $this->getColorPicker($key, 'backgroundColor', $newConfig);
					$tab.= $this->getColorPicker($key, 'highlightColor', $newConfig);				
					$allTabs.= $this->getFieldset($tab, 'colorset', $key);
					
					// display fieldset
					$tab = $this->getThumbnailPicker($key, 'backgroundImage', $newConfig);
					$tab .= $this->getDropdown($key, 'showEqualizer', $conf, $newConfig);
					$allTabs.= $this->getFieldset($tab, 'displayset', $key);
					
					// control fieldset
					$tab = $this->getDropdown($key, 'showNavigation', $conf, $newConfig);
					$tab.= $this->getDropdown($key, 'showStop', $conf, $newConfig);
					$tab.= $this->getDropdown($key, 'showDigits', $conf, $newConfig);
					$tab.= $this->getDropdown($key, 'showDownload', $conf, $newConfig);
					$allTabs.= $this->getFieldset($tab, 'controlset', $key);
					
					// playpack fielset
					$tab = $this->getDropdown($key, 'autoStart', $conf, $newConfig);
					$tab.= $this->getDropdown($key, 'autoRepeat', $conf, $newConfig);
					$tab.= $this->getTextField($key, 'volume', $newConfig);
					$tab.= $this->getCheckbox($key, 'callback', $newConfig);
					$allTabs.= $this->getFieldset($tab, 'playbackset', $key);
					
					// mp3
					if (strtolower(substr($file,-4)=='.mp3') || strtolower(substr($url,-4)=='.mp3') ) {
						if ($newConfig['use1PixelOut']!=1)  {
							$tab = $this->getDropdown($key, 'use1PixelOut', $conf, $newConfig);
							$allTabs.= $this->getFieldset($tab, 'mp3set', $key,1);
						} else {
							
							$tab = $this->getDropdown($key, 'use1PixelOut', $conf, $newConfig);
							$tab.= $this->getColorPicker($key, 'leftbg', $newConfig);
							$tab.= $this->getColorPicker($key, 'lefticon', $newConfig);
							$tab.= $this->getColorPicker($key, 'rightbg', $newConfig);
							$tab.= $this->getColorPicker($key, 'rightbghover', $newConfig);
							$tab.= $this->getColorPicker($key, 'righticon', $newConfig);
							$tab.= $this->getColorPicker($key, 'text', $newConfig);
							$tab.= $this->getColorPicker($key, 'slider', $newConfig);
							$tab.= $this->getColorPicker($key, 'track', $newConfig);
							$tab.= $this->getDropdown($key, 'border', $conf, $newConfig);
							$tab.= $this->getDropdown($key, 'loader', $conf, $newConfig);
							
							$allTabs = '';
							$allTabs.= $this->getFieldset($tab, 'mp3set', $key,1);
						
						}
					}
 
				}
				
				$content.= $allTabs; 

				#$content.= t3lib_div::view_array($newConfig); #debug
				
					// wrap everything into one div
				$content = '<div class="singlefile">	
											<input type="hidden" name="rg['.$key.'][info]" value="'.$file.'" />	
											'.$content.'
										</div>';
			}
			
			return $content;
		}

	   /**
	    * Get the default values of each parameter
	    *
			* @param   string      $key: The key for the lookup
			* @return  -1 if key not found, otherwise its default value
	    */			
		function getDefaultValues($key) {			
			$default = array();
			$default['volume'] 					= 80;
			$default['showNavigation'] 	= 1 ;
			$default['showStop'] 				= 0 ;
			$default['showDigits'] 			= 1;
			$default['showDownload'] 		= 0;
			$default['showEqualizer'] 	= 0;
			$default['autoStart'] 			= 0;
			$default['autoRepeat'] 			= 0;
			$default['backgroundColor'] = '#FFFFFF';
			$default['foregroundColor'] = '#000000';
			$default['highlightColor'] 	= '#000000';
			$default['screenColor'] 		= '#000000';
			$default['youtubeColor1'] 	= '';
			$default['youtubeColor2'] 	= '';
			$default['youtubeBorder'] 	= 0;
			$default['callback'] 	= 0;
			#$default['use1PixelOut'] 	= 1;
			
			$default['bg']							= '#f8f8f8';
			$default['leftbg']					= '#eeeeee';
			$default['lefticon']				= '#666666';
			$default['rightbg']					= '#cccccc';
			$default['rightbghover']		= '#999999';
			$default['righticon']				= '#666666';
			$default['righticonhover']	= '#ffffff';
			$default['text']						= '#666666';
			$default['slider']					= '#666666';
			$default['track']						= '#ffffff';
			$default['border']					= '#666666';
			$default['loader']					= '#9FFFB8';
						
			
			if (array_key_exists($key, $default)) {
				return $default[$key];
			} 

			return '';
		}
		
		
	   /**
	    * Get a simple textfield
	    *
	    * @param   int      $key: The key of the file    
			* @param   string      $type: Type of field
			* @param   array      $options: The options of the dropdown
			* @param   array      $config: The configuration array
			* @return  the select element renderd
	    */	
		function getCheckbox($key, $type, $config) {
			$default = ($config[$type]) ? $config[$type] : $this->getDefaultValues($type);	
			$default = ($default==1) ? ' checked="checked" ' : '';	
			$content.= '<div class="field checkbox">
										<label for="url'.$type.$key.'">'.$this->getLL($type).'</label>
										<input type="checkbox" name="rg['.$key.']['.$type.']" id="'.$type.$key.'" value="1" '.$default.' />
									</div>';			
			return $content;
		}
		
		
	   /**
	    * Get a simple textfield
	    *
	    * @param   int      $key: The key of the file    
			* @param   string      $type: Type of field
			* @param   array      $options: The options of the dropdown
			* @param   array      $config: The configuration array
			* @return  the select element renderd
	    */	
		function getTextField($key, $type, $config) {
			$default = ($config[$type]) ? $config[$type] : $this->getDefaultValues($type);		
			$content.= '<div class="field fieldlong">
										<label for="url'.$type.$key.'">'.$this->getLL($type).'</label>
										<input name="rg['.$key.']['.$type.']" id="'.$type.$key.'" value="'.$default.'" />
									</div>';			
			return $content;
		}


	   /**
	    * Get a Dropdown form element
	    *
	    * @param   int      $key: The key of the file    
			* @param   string      $type: Type of field
			* @param   array      $options: The options of the dropdown
			* @param   array      $config: The configuration array
			* @return  the select element renderd
	    */	
		function getDropdown($key, $type, $options, $config) {
			// default value
			$default = (isset($config[$type])) ? $config[$type] : $this->getDefaultValues($type);
			// render the options
			foreach ($options as $oKey=>$value) {
				$sel = ($default==$oKey ) ? ' selected="selected" ' : '';
				$elements.= '<option '.$sel.' value="'.$oKey.'">'.$value.'</option>';
			}
		
			$content.= '<div class="field fieldlong">
										<label for="url'.$type.$key.'">'.$this->getLL($type).'</label>
										<select name="rg['.$key.']['.$type.']" id="'.$type.$key.'">
											'.$elements.$key.'
										</select>
									</div>';			
			return $content;
		}


	   /**
	    * Get a input field for theurl
	    *
	    * @param   int      $key: The key of the file    
			* @param   string      $file: The original file
			* @param   array      $config: The configuration array
			* @return  the input field
	    */			
		function getUrl($key, $file, $config) {		
			$closed = (substr($file,-4)!='.rgg') ? ' disabled="disabled" ' : '';
			
			$content.= '<div class="field fieldlong">
										<label for="url'.$key.'">'.$this->getLL('url').'</label>
										<input '.$closed.' type="text" name="rg['.$key.'][file]" id="url'.$key.'" value="'.$config['file'].'" />
									</div>';			
			return $content;
		}


	   /**
	    * Get a thumbnail picker, working with tceforms
	    *
	    * @param   int      $key: The key of the file    
			* @param   string      $type: Type of field
			* @param   array      $config: The configuration array
			* @return  the tceform rendered
	    */		
		function getThumbnailPicker($key, $type, $config) {
			$default =  $config['backgroundImage'];
			// configuration of the Thumbnail Picker
			$conf =Array(
				'fieldConf' => array(
					'label' => $type,
					'config' => array(
						'type' => 'group',
						'prepend_tname' => 1,
						'internal_type' => 'file',
						'allowed' => 'png,jpg,gif',
						'show_thumbs'=>0,
						'size'=>1,
						'maxitems'=>1,
						'minitems'=>0,
						'disable_controls' =>'upload'
					),
				),
				'onFocus' => '',
				'label' => 'Plugin Options',
				'itemFormElName' => 'rg['.$key.']['.$type.'][file]',
				'itemFormElValue' => $default,
			);
			
			$element = $this->tceforms->getSingleField_typeGroup('', '', array(), $conf );

			$content .= '<div class="field fieldsmall">
										<label for="bgImg'.$type.$key.'">'.$this->getLL($type).'</label>
										'.$element.'
									</div>';
			
			return $content;		
		}


	   /**
	    * Get a colorpicker and the input field for it 
	    *
	    * @param   int      $key: The key of the file    
			* @param   string      $type: Type of field
			* @param   array      $config: The configuration array
			* @return  the color picker fields
	    */			
		function getColorPicker($key, $type, $config) {
			$value = $config[$type];
			$css = ($value!='') ? 'style="background-color:'.$value.';"' : '';
			$content.= '<div class="field fieldsmall">
										<label for="'.$type.$key.'">'.$this->getLL($type).'</label>
										<input type="text" name="rg['.$key.']['.$type.']" id="'.$type.$key.'" value="'.$value.'" '.$css.' />
										<span class="colorpicker">
											<img alt="'.$this->getLL('colorpicker').'" title="'.$this->getLL('colorpicker').'" src="res/colorpicker.png" onclick="showColorPicker(this,'.$type.$key.')"  />
										</span>
									</div>';
			return $content;
		}


	   /**
	    * Get the file header of every file  
	    *
	    * @param   string      $title: The title
	    * @param   int      $key: The key of the file    
			* @return  formatted header
	    */	
		function getFileHeader($title, $key) {
				$key++;
				$content = '<h4>#'.$key.': '.htmlspecialchars($title).'</h4>';
				return $content;
		}


	   /**
	    * Set content into a fieldset and div to toggle the div
	    *
	    * @param   string      $tabContent: The content which should get toggled
			* @param   string      $title: Title of fieldset which serves as link
			* @param   int      $key: Key of the file
			* @param   boolean      $defaultShow: Should the div shown on default
			* @return  the input field
	    */		
		function getFieldset ($tabContent, $title, $key, $defaultShow=0) {
			$show = ($defaultShow==1) ? ' style="display:block;" ' : ' style="display:none;" ';
			$content.= '<fieldset>
										<legend onclick="toggleMenu(\''.$title.$key.'\')">'.$this->getLL($title).'</legend>
										<div id="'.$title.$key.'" '.$show.' >
											'.$tabContent.'
										</div>
									</fieldset>';
			return $content;	
		}

	   /**
	    * Get some help text for the wizard
	    *
			* @return  help text
	    */		
		function getHelp() {
			$text = array();
			$text[] = $this->getLL('helpFLV');
			$text[] = $this->getLL('help1PixelOut');
			
			$content.= implode('',$text);
			
			return $content;
		}


   /**
    * Gets the allowed file endings  
    *
    * @return  array of file endings
    */
		function getFileEndings () {
			$files =  array('.rgg', '.mp3', '.flv', '.mov', '.swf', '.wmv');
			return $files;
		}
		
		
   /**
    * Gets a translation from the locallang.xml
    * If nothing is found, the key will be returned.  
    *
    * @param   string      $key: The key for the translation
    * @return  translated string
    */
  function getLL($key) {
		global $LANG;
		$value = $LANG->getLL($key);
		$value = ($value) ? $value : $key;
		return $value;
	}
  
  /**
   * [Describe function...]
   *
   * @return    [type]        ...
   */
  function moduleContent()    {
      switch((string) $this->MOD_SETTINGS['function'])    {
          case 1:
              $content = $this->getMainConfig();
              $this->content.=$this->doc->section($this->getLL('header1'),$content,0,1);
          break;
          case 2:
              $content = $this->getHelp();
              $this->content.=$this->doc->section($this->getLL('header2'),$content,0,1);
          break;
      }
  }
  
	/**
	 * Outputting the accumulated content to screen
	 *
	 * @return	void
	 */
    function printContent()    {
        $this->content.=$this->doc->endPage();
        echo $this->content;
    }

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rgmediaimages/wizard/index.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/rgmediaimages/wizard/index.php']);
}


// Make instance:
$SOBE = t3lib_div::makeInstance('tx_rgmediaimages_tt_content_tx_rgmediaimages_alttextwiz');
$SOBE->init();
$SOBE->main();
$SOBE->printContent();

?>
