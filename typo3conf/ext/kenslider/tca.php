<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_kenslider_entry'] = array(
	'ctrl' => $TCA['tx_kenslider_entry']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,starttime,endtime,image,thumb,initialzoom,finalzoom,link,linktarget,iframelink,text1,text2,text3,style'
	),
	'feInterface' => $TCA['tx_kenslider_entry']['feInterface'],
	'columns' => array(
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array(
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array(
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array(
					'upper' => mktime(3, 14, 7, 1, 19, 2038),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'image' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:kenslider/locallang_db.xml:tx_kenslider_entry.image',		
			'config' => array(
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],	
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],	
				'uploadfolder' => 'uploads/tx_kenslider',
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'thumb' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:kenslider/locallang_db.xml:tx_kenslider_entry.thumb',		
			'config' => array(
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],	
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],	
				'uploadfolder' => 'uploads/tx_kenslider',
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'initialzoom' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:kenslider/locallang_db.xml:tx_kenslider_entry.initialzoom',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'finalzoom' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:kenslider/locallang_db.xml:tx_kenslider_entry.finalzoom',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'link' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:kenslider/locallang_db.xml:tx_kenslider_entry.link',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'linktarget' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:kenslider/locallang_db.xml:tx_kenslider_entry.linktarget',		
			'config' => array(
				'type' => 'check',
			)
		),
		'iframelink' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:kenslider/locallang_db.xml:tx_kenslider_entry.iframelink',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'text1' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:kenslider/locallang_db.xml:tx_kenslider_entry.text1',		
			'config' => array(
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
		'text2' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:kenslider/locallang_db.xml:tx_kenslider_entry.text2',		
			'config' => array(
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
		'text3' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:kenslider/locallang_db.xml:tx_kenslider_entry.text3',		
			'config' => array(
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
		'style' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:kenslider/locallang_db.xml:tx_kenslider_entry.style',		
			'config' => array(
				'type' => 'radio',
				'items' => array(
					array('LLL:EXT:kenslider/locallang_db.xml:tx_kenslider_entry.style.I.0', '1'),
					array('LLL:EXT:kenslider/locallang_db.xml:tx_kenslider_entry.style.I.1', '2'),
					array('LLL:EXT:kenslider/locallang_db.xml:tx_kenslider_entry.style.I.2', '3'),
					array('LLL:EXT:kenslider/locallang_db.xml:tx_kenslider_entry.style.I.3', '4'),
				),
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, image, thumb, initialzoom, finalzoom, link, linktarget, iframelink, text1;;;richtext[]:rte_transform[mode=ts], text2;;;richtext[]:rte_transform[mode=ts], text3;;;richtext[]:rte_transform[mode=ts], style')
	),
	'palettes' => array(
		'1' => array('showitem' => 'starttime, endtime')
	)
);
?>