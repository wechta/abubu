<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_iconepovezave_entry'] = array(
	'ctrl' => $TCA['tx_iconepovezave_entry']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,title,title2,image,link'
	),
	'feInterface' => $TCA['tx_iconepovezave_entry']['feInterface'],
	'columns' => array(
		'sys_language_uid' => array(		
			'exclude' => 1,
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array(
				'type'                => 'select',
				'foreign_table'       => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
				)
			)
		),
		'l10n_parent' => array(		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude'     => 1,
			'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config'      => array(
				'type'  => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table'       => 'tx_iconepovezave_entry',
				'foreign_table_where' => 'AND tx_iconepovezave_entry.pid=###CURRENT_PID### AND tx_iconepovezave_entry.sys_language_uid IN (-1,0)',
			)
		),
		'l10n_diffsource' => array(		
			'config' => array(
				'type' => 'passthrough'
			)
		),
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:iconepovezave/locallang_db.xml:tx_iconepovezave_entry.title',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'title2' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:iconepovezave/locallang_db.xml:tx_iconepovezave_entry.title2',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'image' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:iconepovezave/locallang_db.xml:tx_iconepovezave_entry.image',		
			'config' => array(
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],	
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],	
				'uploadfolder' => 'uploads/tx_iconepovezave',
				'show_thumbs' => 1,	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'link' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:iconepovezave/locallang_db.xml:tx_iconepovezave_entry.link',		
			'config' => array(
				'type'     => 'input',
				'size'     => '15',
				'max'      => '255',
				'checkbox' => '',
				'eval'     => 'trim',
				'wizards'  => array(
					'_PADDING' => 2,
					'link'     => array(
						'type'         => 'popup',
						'title'        => 'Link',
						'icon'         => 'link_popup.gif',
						'script'       => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
					)
				)
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title;;;;2-2-2, title2;;;;3-3-3, image, link')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);
?>