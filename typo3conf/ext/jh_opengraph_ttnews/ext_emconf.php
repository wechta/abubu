<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "jh_opengraph_ttnews".
 *
 * Auto generated 12-11-2013 09:42
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Open Graph Protocol for tt_news',
	'description' => 'Generates the facebook Open Graph Protocol parameters from tt_news item in single-view and adds them to the html-header.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.1.1',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Jonathan Heilmann',
	'author_email' => 'mail@jonathan-heilmann.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5.0-6.1.9',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:9:{s:9:"ChangeLog";s:4:"70d6";s:48:"class.tx_jhopengraphttnews_displaySingleHook.php";s:4:"04a9";s:12:"ext_icon.gif";s:4:"0a87";s:17:"ext_localconf.php";s:4:"964d";s:24:"ext_typoscript_setup.txt";s:4:"a47a";s:10:"README.txt";s:4:"84a6";s:14:"doc/manual.sxw";s:4:"00a8";s:38:"pi1/class.tx_jhopengraphttnews_pi1.php";s:4:"4cbb";s:17:"res/img/nopic.jpg";s:4:"f5a5";}',
);

?>