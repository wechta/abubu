<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_easyshop_categories'] = array(
	'ctrl' => $TCA['tx_easyshop_categories']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,title,title_front,subtitle,title_overlays,parrent,description,description_overlays,vat,cat_element,image,subtitle2,seo_keywords,seo_description'
	),
	'feInterface' => $TCA['tx_easyshop_categories']['feInterface'],
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
		'fe_group' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array(
				'type'  => 'select',
				'items' => array(
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'title' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_categories.title',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'title_front' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_categories.title_front',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'subtitle' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_categories.subtitle',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'title_overlays' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_categories.title_overlays',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'parrent' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_categories.parrent',		
			'config' => array(
				'type' => 'select',	
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_easyshop_categories',	
				'foreign_table_where' => 'AND tx_easyshop_categories.pid=###CURRENT_PID### ORDER BY tx_easyshop_categories.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'description' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_categories.description',		
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
		'description_overlays' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_categories.description_overlays',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'vat' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_categories.vat',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'cat_element' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_categories.cat_element',		
			'config' => array(
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tt_content',	
				'size' => 3,	
				'minitems' => 0,
				'maxitems' => 10,
			)
		),
		'image' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_categories.image',		
			'config' => array(
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],	
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],	
				'uploadfolder' => 'uploads/tx_easyshop',
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'subtitle2' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_categories.subtitle2',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'seo_keywords' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_categories.seo_keywords',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'seo_description' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_categories.seo_description',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, title_front;;;;3-3-3, subtitle, title_overlays, parrent, description;;;richtext[]:rte_transform[mode=ts], description_overlays, vat, cat_element, image, subtitle2, seo_keywords, seo_description')
	),
	'palettes' => array(
		'1' => array('showitem' => 'starttime, endtime, fe_group')
	)
);



$TCA['tx_easyshop_properties'] = array(
	'ctrl' => $TCA['tx_easyshop_properties']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,title_front,title_overlays'
	),
	'feInterface' => $TCA['tx_easyshop_properties']['feInterface'],
	'columns' => array(
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
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_properties.title',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'title_front' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_properties.title_front',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'title_overlays' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_properties.title_overlays',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, title_front;;;;3-3-3, title_overlays')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);



$TCA['tx_easyshop_products'] = array(
	'ctrl' => $TCA['tx_easyshop_products']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,starttime,endtime,fe_group,title,subtitle,code,price,web_price,price_partner,web_price_partner,stock,vat,description,description2,categories,properties,properties2,price_prop2,price_disc_prop2,images,images_captions,files,delivery_time,selected_product,action_product,mostsold_product,connected_products,sort_weight,sold_num,properties3,properties4,month_tea,type_tea,seo_keywords,seo_description,bio_tea,stock_val'
	),
	'feInterface' => $TCA['tx_easyshop_products']['feInterface'],
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
		'fe_group' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array(
				'type'  => 'select',
				'items' => array(
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'title' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.title',		
			'config' => array(
				'type' => 'input',	
				'size' => '40',	
				'max' => '255',
			)
		),
		'subtitle' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.subtitle',		
			'config' => array(
				'type' => 'input',	
				'size' => '40',	
				'max' => '255',
			)
		),
		'code' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.code',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'price' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.price',		
			'config' => array(
				'type' => 'input',	
				'size' => '10',
			)
		),
		'web_price' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.web_price',		
			'config' => array(
				'type' => 'input',	
				'size' => '10',
			)
		),
		'price_partner' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.price_partner',		
			'config' => array(
				'type' => 'input',	
				'size' => '10',
			)
		),
		'web_price_partner' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.web_price_partner',		
			'config' => array(
				'type' => 'input',	
				'size' => '10',
			)
		),
		'stock' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.stock',		
			'config' => array(
				'type' => 'check',
				'default' => 1,
			)
		),
		'vat' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.vat',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'description' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.description',		
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
		'description2' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.description2',		
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
		'categories' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.categories',		
			'config' => array(
				'type' => 'select',	
				'foreign_table' => 'tx_easyshop_categories',	
				'foreign_table_where' => 'ORDER BY tx_easyshop_categories.uid',	
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 20,	
				"MM" => "tx_easyshop_products_categories_mm",
			)
		),
		'properties' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.properties',		
			'config' => array(
				'type' => 'select',	
				'foreign_table' => 'tx_easyshop_properties',	
				'foreign_table_where' => 'ORDER BY tx_easyshop_properties.uid',	
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 20,	
				"MM" => "tx_easyshop_products_properties_mm",
			)
		),
		'properties2' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.properties2',		
			'config' => array(
				'type' => 'select',	
				'foreign_table' => 'tx_easyshop_properties2',	
				'foreign_table_where' => 'ORDER BY tx_easyshop_properties2.uid',	
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 20,	
				"MM" => "tx_easyshop_products_properties2_mm",
			)
		),
		'price_prop2' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.price_prop2',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'price_disc_prop2' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.price_disc_prop2',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'images' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.images',		
			'config' => array(
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'gif,png,jpeg,jpg',	
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],	
				'uploadfolder' => 'uploads/tx_easyshop',
				'show_thumbs' => 1,	
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 15,
			)
		),
		'images_captions' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.images_captions',		
			'config' => array(
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
		'files' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.files',		
			'config' => array(
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => '',	
				'disallowed' => 'php,php3',	
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],	
				'uploadfolder' => 'uploads/tx_easyshop',
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 5,
			)
		),
		'delivery_time' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.delivery_time',		
			'config' => array(
				'type' => 'input',	
				'size' => '5',
			)
		),
		'selected_product' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.selected_product',		
			'config' => array(
				'type' => 'check',
			)
		),
		'action_product' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.action_product',		
			'config' => array(
				'type' => 'check',
			)
		),
		'mostsold_product' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.mostsold_product',		
			'config' => array(
				'type' => 'check',
			)
		),
		'connected_products' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.connected_products',		
			'config' => array(
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_easyshop_products',	
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 10,
			)
		),
		'sort_weight' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.sort_weight',		
			'config' => array(
				'type' => 'input',	
				'size' => '5',
			)
		),
		'sold_num' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.sold_num',		
			'config' => array(
				'type' => 'none',
			)
		),
		'properties3' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.properties3',		
			'config' => array(
				'type' => 'select',	
				'foreign_table' => 'tx_easyshop_properties3',	
				'foreign_table_where' => 'ORDER BY tx_easyshop_properties3.uid',	
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 20,	
				"MM" => "tx_easyshop_products_properties3_mm",
			)
		),
		'properties4' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.properties4',		
			'config' => array(
				'type' => 'select',	
				'foreign_table' => 'tx_easyshop_properties4',	
				'foreign_table_where' => 'ORDER BY tx_easyshop_properties4.uid',	
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 20,	
				"MM" => "tx_easyshop_products_properties4_mm",
			)
		),
		'month_tea' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.month_tea',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'type_tea' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.type_tea',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'seo_keywords' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.seo_keywords',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'seo_description' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.seo_description',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'bio_tea' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.bio_tea',		
			'config' => array(
				'type' => 'check',
			)
		),
		'stock_val' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.stock_val',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'has_tester' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_products.has_tester',		
			'config' => array(
				'type' => 'check',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, subtitle;;;;3-3-3, code, price, web_price, price_partner, web_price_partner, has_tester, stock, vat, description;;;richtext[]:rte_transform[mode=ts], description2;;;richtext[]:rte_transform[mode=ts], categories, properties, properties2, price_prop2, price_disc_prop2, images, images_captions, files, delivery_time, selected_product, action_product, mostsold_product, connected_products, sort_weight, sold_num, properties3, properties4, month_tea, type_tea, seo_keywords, seo_description, bio_tea, stock_val')
	),
	'palettes' => array(
		'1' => array('showitem' => 'starttime, endtime, fe_group')
	)
);



$TCA['tx_easyshop_language_overlay'] = array(
	'ctrl' => $TCA['tx_easyshop_language_overlay']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,overlay_language,overlay_parrent,overlay_title,overlay_subtitle,overlay_description,overlay_images,overlay_images_captions,overlay_files'
	),
	'feInterface' => $TCA['tx_easyshop_language_overlay']['feInterface'],
	'columns' => array(
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'overlay_language' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_language_overlay.overlay_language',		
			'config' => array(
				'type' => 'select',	
				'foreign_table' => 'sys_language',	
				'foreign_table_where' => 'ORDER BY sys_language.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'overlay_parrent' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_language_overlay.overlay_parrent',		
			'config' => array(
				'type' => 'select',	
				'foreign_table' => 'tx_easyshop_products',	
				'foreign_table_where' => 'AND tx_easyshop_products.pid=###CURRENT_PID### ORDER BY tx_easyshop_products.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'overlay_title' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_language_overlay.overlay_title',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'overlay_subtitle' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_language_overlay.overlay_subtitle',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'overlay_description' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_language_overlay.overlay_description',		
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
		'overlay_images' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_language_overlay.overlay_images',		
			'config' => array(
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'gif,png,jpeg,jpg',	
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],	
				'uploadfolder' => 'uploads/tx_easyshop',
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 15,
			)
		),
		'overlay_images_captions' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_language_overlay.overlay_images_captions',		
			'config' => array(
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
		'overlay_files' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_language_overlay.overlay_files',		
			'config' => array(
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => '',	
				'disallowed' => 'php,php3',	
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],	
				'uploadfolder' => 'uploads/tx_easyshop',
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 5,
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, overlay_language, overlay_parrent, overlay_title, overlay_subtitle, overlay_description;;;richtext[]:rte_transform[mode=ts], overlay_images, overlay_images_captions, overlay_files')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);



$TCA['tx_easyshop_payment_log'] = array(
	'ctrl' => $TCA['tx_easyshop_payment_log']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'basket,user,status,response_value,buyer,reciver,products,payment_type,total,discount,payment_note'
	),
	'feInterface' => $TCA['tx_easyshop_payment_log']['feInterface'],
	'columns' => array(
		'basket' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_payment_log.basket',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'user' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_payment_log.user',		
			'config' => array(
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'fe_users',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'status' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_payment_log.status',		
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_payment_log.status.I.0', '1'),
					array('LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_payment_log.status.I.1', '2'),
					array('LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_payment_log.status.I.2', '3'),
				),
				'size' => 1,	
				'maxitems' => 1,
			)
		),
		'response_value' => array(		
			'exclude' => 1,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_payment_log.response_value',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'buyer' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_payment_log.buyer',		
			'config' => array(
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_easyshop_buyers',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'reciver' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_payment_log.reciver',		
			'config' => array(
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_easyshop_recivers',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'products' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_payment_log.products',		
			'config' => array(
				'type' => 'group',	
				'internal_type' => 'db',	
				'allowed' => 'tx_easyshop_products',	
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 100,	
				"MM" => "tx_easyshop_payment_log_products_mm",
			)
		),
		'payment_type' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_payment_log.payment_type',		
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_payment_log.payment_type.I.0', '0'),
					array('LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_payment_log.payment_type.I.1', '1'),
					array('LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_payment_log.payment_type.I.2', '2'),
					array('LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_payment_log.payment_type.I.3', '3'),
				),
				'size' => 1,	
				'maxitems' => 1,
			)
		),
		'total' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_payment_log.total',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'discount' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_payment_log.discount',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'payment_note' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_payment_log.payment_note',		
			'config' => array(
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'basket;;;;1-1-1, user, status, response_value, buyer, reciver, products, payment_type, total, discount, payment_note')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);



$TCA['tx_easyshop_buyers'] = array(
	'ctrl' => $TCA['tx_easyshop_buyers']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,name,surname,tel,email,address,post,city,company,id_ddv'
	),
	'feInterface' => $TCA['tx_easyshop_buyers']['feInterface'],
	'columns' => array(
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'name' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_buyers.name',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'surname' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_buyers.surname',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'tel' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_buyers.tel',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'email' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_buyers.email',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'address' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_buyers.address',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'post' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_buyers.post',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'city' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_buyers.city',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'company' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_buyers.company',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'id_ddv' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_buyers.id_ddv',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name, surname, tel, email, address, post, city, company, id_ddv')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);



$TCA['tx_easyshop_recivers'] = array(
	'ctrl' => $TCA['tx_easyshop_recivers']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,name,surname,address,post,city'
	),
	'feInterface' => $TCA['tx_easyshop_recivers']['feInterface'],
	'columns' => array(
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'name' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_recivers.name',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'surname' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_recivers.surname',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'address' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_recivers.address',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'post' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_recivers.post',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'city' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_recivers.city',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, name, surname, address, post, city')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);



$TCA['tx_easyshop_coupons'] = array(
	'ctrl' => $TCA['tx_easyshop_coupons']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,code,discount,onetime,forproducts'
	),
	'feInterface' => $TCA['tx_easyshop_coupons']['feInterface'],
	'columns' => array(
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_coupons.title',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'code' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_coupons.code',		
			'config' => array(
				'type' => 'input',	
				'size' => '20',	
				'max' => '20',
			)
		),
		'discount' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_coupons.discount',		
			'config' => array(
				'type' => 'input',	
				'size' => '5',	
				'max' => '2',
			)
		),
		'onetime' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_coupons.onetime',		
			'config' => array(
				'type' => 'check',
			)
		),
		'forproducts' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_coupons.forproducts',		
			'config' => array(
				'type' => 'select',	
				'foreign_table' => 'tx_easyshop_products',	
				'foreign_table_where' => 'ORDER BY tx_easyshop_products.uid',	
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 100,
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, code;;;;3-3-3, discount, onetime, forproducts')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);



$TCA['tx_easyshop_properties2'] = array(
	'ctrl' => $TCA['tx_easyshop_properties2']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,title_front'
	),
	'feInterface' => $TCA['tx_easyshop_properties2']['feInterface'],
	'columns' => array(
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_properties2.title',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'title_front' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_properties2.title_front',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, title_front;;;;3-3-3')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);



$TCA['tx_easyshop_properties3'] = array(
	'ctrl' => $TCA['tx_easyshop_properties3']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,title_front'
	),
	'feInterface' => $TCA['tx_easyshop_properties3']['feInterface'],
	'columns' => array(
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_properties3.title',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'title_front' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_properties3.title_front',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, title_front;;;;3-3-3')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);



$TCA['tx_easyshop_properties4'] = array(
	'ctrl' => $TCA['tx_easyshop_properties4']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,title_front'
	),
	'feInterface' => $TCA['tx_easyshop_properties4']['feInterface'],
	'columns' => array(
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_properties4.title',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'title_front' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_properties4.title_front',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, title_front;;;;3-3-3')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);



$TCA['tx_easyshop_coupons2'] = array(
	'ctrl' => $TCA['tx_easyshop_coupons2']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,code,discount,onetime'
	),
	'feInterface' => $TCA['tx_easyshop_coupons2']['feInterface'],
	'columns' => array(
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_coupons2.title',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',
			)
		),
		'code' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_coupons2.code',		
			'config' => array(
				'type' => 'input',	
				'size' => '20',	
				'max' => '20',
			)
		),
		'discount' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_coupons2.discount',		
			'config' => array(
				'type' => 'input',	
				'size' => '5',	
				'max' => '5',
			)
		),
		'onetime' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:easy_shop/locallang_db.xml:tx_easyshop_coupons2.onetime',		
			'config' => array(
				'type' => 'check',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, code;;;;3-3-3, discount, onetime')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);
?>