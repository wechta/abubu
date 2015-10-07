<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "dd_googlesitemap".
 *
 * Auto generated 16-10-2013 12:30
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Google sitemap',
	'description' => 'High performance Google sitemap implementation that avoids typical errors by other similar extensions',
	'category' => 'fe',
	'shy' => 0,
	'version' => '1.2.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => 'typo3temp/dd_googlesitemap',
	'modify_tables' => 'pages',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Dmitry Dulepov',
	'author_email' => 'dmitry.dulepov@gmail.com',
	'author_company' => 'SIA "ACCIO"',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5.0-6.0.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:20:{s:9:"ChangeLog";s:4:"becb";s:32:"class.tx_ddgooglesitemap_eid.php";s:4:"73ff";s:38:"class.tx_ddgooglesitemap_generator.php";s:4:"3d66";s:34:"class.tx_ddgooglesitemap_pages.php";s:4:"5d3a";s:36:"class.tx_ddgooglesitemap_tcemain.php";s:4:"1a1c";s:35:"class.tx_ddgooglesitemap_ttnews.php";s:4:"ec51";s:16:"ext_autoload.php";s:4:"eed5";s:12:"ext_icon.gif";s:4:"0709";s:17:"ext_localconf.php";s:4:"017f";s:14:"ext_tables.php";s:4:"eada";s:14:"ext_tables.sql";s:4:"239c";s:24:"ext_typoscript_setup.txt";s:4:"71ba";s:13:"locallang.xml";s:4:"c6b5";s:9:"README.md";s:4:"ee4d";s:14:"doc/manual.sxw";s:4:"ecf1";s:56:"renderers/class.tx_ddgooglesitemap_abstract_renderer.php";s:4:"d61c";s:52:"renderers/class.tx_ddgooglesitemap_news_renderer.php";s:4:"15b2";s:54:"renderers/class.tx_ddgooglesitemap_normal_renderer.php";s:4:"42a5";s:63:"scheduler/class.tx_ddgooglesitemap_additionalfieldsprovider.php";s:4:"c129";s:48:"scheduler/class.tx_ddgooglesitemap_indextask.php";s:4:"cb13";}',
);

?>