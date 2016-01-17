<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_easycontact_entry'] = array (
	'ctrl' => $TCA['tx_easycontact_entry']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,subject,content'
	),
	'feInterface' => $TCA['tx_easycontact_entry']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'subject' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easycontact/locallang_db.xml:tx_easycontact_entry.subject',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'content' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easycontact/locallang_db.xml:tx_easycontact_entry.content',		
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
		'0' => array('showitem' => 'hidden;;1;;1-1-1, subject, content;;;richtext[]:rte_transform[mode=ts]')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>