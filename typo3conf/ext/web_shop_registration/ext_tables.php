<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:web_shop_registration/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

$tempColumns = array (
	'tx_webshopregistration_id_ddv' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:web_shop_registration/locallang_db.xml:fe_users.tx_webshopregistration_id_ddv',		
		'config' => array (
			'type' => 'input',	
			'size' => '30',
		)
	),
	'tx_webshopregistration_is_ddv' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:web_shop_registration/locallang_db.xml:fe_users.tx_webshopregistration_is_ddv',		
		'config' => array (
			'type' => 'check',
		)
	),
	'tx_webshopregistration_info_reciver' => array (		
		'exclude' => 0,		
		'label' => 'LLL:EXT:web_shop_registration/locallang_db.xml:fe_users.tx_webshopregistration_info_reciver',		
		'config' => array (
			'type' => 'check',
		)
	),
);

//FlexForms
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:web_shop_registration/flexform_ds.xml');

t3lib_div::loadTCA('fe_users');
t3lib_extMgm::addTCAcolumns('fe_users',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('fe_users','tx_webshopregistration_id_ddv;;;;1-1-1, tx_webshopregistration_is_ddv, tx_webshopregistration_info_reciver');
?>