<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "automaketemplate".
 *
 * Auto generated 11-09-2013 09:04
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Template Auto-parser',
	'description' => 'Reads an HTML file and all sections which has a certain class or id value set are wrapped in corresponding template subparts. Also relative paths to images, stylesheets etc. are corrected.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.2.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Kasper SkÃ¥rhÃ¸j, alterNET Internet B.V.',
	'author_email' => 'support@alternet.nl',
	'author_company' => 'Curby Soft Multimedia, alterNET Internet B.V.',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'typo3' => '4.3.0-6.2.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:9:{s:9:"ChangeLog";s:4:"9ade";s:20:"class.ext_update.php";s:4:"a13b";s:12:"ext_icon.gif";s:4:"b014";s:17:"ext_localconf.php";s:4:"120e";s:15:"ext_php_api.dat";s:4:"aa1d";s:13:"locallang.xml";s:4:"9af3";s:14:"doc/manual.sxw";s:4:"96af";s:12:"doc/TODO.txt";s:4:"d41d";s:37:"pi1/class.tx_automaketemplate_pi1.php";s:4:"4b9e";}',
);

?>