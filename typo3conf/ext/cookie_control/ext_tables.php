<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';

t3lib_extMgm::addPlugin(array(
	'LLL:EXT:cookie_control/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');


if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_cookiecontrol_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_cookiecontrol_pi1_wizicon.php';
}


$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
#Commented By sivaprasad.s@pitsolutoins.com on 3/09/2012
/*
$TCA['tx_cookiecontrol_data'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:cookie_control/locallang_db.xml:tx_cookiecontrol_data',		
		'label'     => 'uid',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY crdate',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_cookiecontrol_data.gif',
	),
);
*/
 t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/flexform_ds_pi1.xml'); 
 t3lib_extMgm::addStaticFile($_EXTKEY,'static/', 'Cookie Control'); 
?>
