<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::allowTableOnStandardPages('tx_iconepovezave_entry');


t3lib_extMgm::addToInsertRecords('tx_iconepovezave_entry');

$TCA['tx_iconepovezave_entry'] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:iconepovezave/locallang_db.xml:tx_iconepovezave_entry',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'languageField'            => 'sys_language_uid',	
		'transOrigPointerField'    => 'l10n_parent',	
		'transOrigDiffSourceField' => 'l10n_diffsource',	
		'sortby' => 'sorting',	
		'delete' => 'deleted',	
		'enablecolumns' => array(		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_iconepovezave_entry.gif',
	),
);
//FlexForms
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
// NOTE: Be sure to change sampleflex to the correct directory name of your extension!
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:iconepovezave/flexform_ds.xml');

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1'] = 'layout,select_key,pages';


t3lib_extMgm::addPlugin(array(
	'LLL:EXT:iconepovezave/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');
?>