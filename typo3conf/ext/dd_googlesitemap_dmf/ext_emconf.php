<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "dd_googlesitemap_dmf".
 *
 * Auto generated 16-10-2013 12:35
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Google Sitemap for plugins',
	'description' => 'Extends dd_googlesitemap that you can easy create your own sitemap.xml for you extensions. Needs only a few line of typoscript configuration - works with realurl or cooluri.',
	'category' => 'fe',
	'shy' => 0,
	'version' => '1.2.2',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => 'uploads/tx_ddgooglesitemap_dmf',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Dominic Garms',
	'author_email' => 'djgarms@gmail.com',
	'author_company' => 'DMFmedia GmbH',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'dd_googlesitemap' => '*',
			'typo3' => '4.5.0-6.1.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:10:{s:32:"class.tx_ddgooglesitemap_dmf.php";s:4:"a284";s:16:"ext_autoload.php";s:4:"c756";s:12:"ext_icon.gif";s:4:"0709";s:17:"ext_localconf.php";s:4:"7c4b";s:14:"ext_tables.php";s:4:"5cd7";s:14:"ext_tables.sql";s:4:"d41d";s:24:"ext_typoscript_setup.txt";s:4:"d588";s:9:"README.md";s:4:"924a";s:44:"Classes/Command/CrawlerCommandController.php";s:4:"a4c3";s:14:"doc/manual.sxw";s:4:"ff2a";}',
);

?>