<?php
//$Id: ext_tables.php 87110 2014-12-06 14:08:32Z franzholz $

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE == 'BE') {

	$TCA['tx_transactor_transactions'] = Array (
		'ctrl' => Array (
			'title' => 'LLL:EXT:transactor/locallang_db.xml:tx_transactor_transactions',
			'label' => 'reference',
			'crdate' => 'crdate',
			'default_sortby' => 'ORDER BY crdate',
			'dividers2tabs' => TRUE,
			'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
			'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif',
			'searchFields' => 'uid,reference,orderuid',
		),
	);
}

?>