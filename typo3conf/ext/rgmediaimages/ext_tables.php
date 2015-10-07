<?php
  
  if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

		// new allowed file types
	$GLOBALS['TCA']['tt_content']['columns']['image']['config']['allowed'] .= ',flv,swf,rtmp,mp3,rgg';
	
		// if DAM is used
	if (t3lib_extMgm::isLoaded('dam') && t3lib_extMgm::isLoaded('dam_ttcontent')) {
		$GLOBALS['T3_VAR']['ext']['dam']['TCA']['image_field']['config']['allowed_types'].= ',flv,swf,rtmp,mp3,rgg';
	}
	
		// get extension configuration
	$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['rgmediaimages']);
	
		// rename the fields if allowed
	if ($confArr['rename']==1) {
		
		foreach ($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'] as $key=>$value) {
			if ($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'][$key][1] == 'textpic') {
				$GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'][$key]['0'] = 'LLL:EXT:rgmediaimages/locallang.xml:textpic';
			} 
			if ($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'][$key][1] == 'image') {
				$GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'][$key]['0'] = 'LLL:EXT:rgmediaimages/locallang.xml:pic';
			}
		}
	}
#	echo 'x';
#	print_r($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items']);

	  // Wizard, highly alpha!!
  $GLOBALS['TCA']['tt_content']['columns']['altText']['config']['wizards'] = Array(
                '_PADDING' => 2,
                'example' => Array(
                    'title' => 'rgmediaimages Wizard:',
                    'type' => 'script',
                    'notNewRecords' => 1,
                    'icon' => t3lib_extMgm::extRelPath('rgmediaimages').'wizard/icon.png',
                    'script' => t3lib_extMgm::extRelPath('rgmediaimages').'wizard/index.php?table=tt_content&config=altText&internal=image',
										'JSopenParams' => 'height=750,width=900,status=0,menubar=0,scrollbars=0',
										'notNewRecords' => 1, 
                ),
            );  
	  // add static TS
  t3lib_extMgm::addStaticFile($_EXTKEY,"static","Media files & images");

		// Plugin 
	t3lib_div::loadTCA('tt_content');
	$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';
	t3lib_extMgm::addPlugin(array('LLL:EXT:rgmediaimages/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
	// t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","Videos & mp3 files");		

		// Flexforms
	$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/flexform_ds.xml');


	if (TYPO3_MODE=="BE")    $TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_rgmediaimages_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_rgmediaimages_pi1_wizicon.php';
?>