<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_footer_entry'] = array (
	'ctrl' => $TCA['tx_footer_entry']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,naslov,seznam'
	),
	'feInterface' => $TCA['tx_footer_entry']['feInterface'],
	'columns' => array (
		'sys_language_uid' => array (		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l10n_parent' => array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
				),
				'foreign_table'       => 'tx_footer_entry',
				'foreign_table_where' => 'AND tx_footer_entry.pid=###CURRENT_PID### AND tx_footer_entry.sys_language_uid IN (-1,0)',
			)
		),
		'l10n_diffsource' => array (		
			'config' => array (
				'type' => 'passthrough'
			)
		),
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'naslov' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:footer/locallang_db.xml:tx_footer_entry.naslov',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'seznam' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:footer/locallang_db.xml:tx_footer_entry.seznam',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, naslov, seznam;;;richtext[]:rte_transform[mode=ts]')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>