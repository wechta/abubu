<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['extraItemMarkerHook'][] =  'EXT:ttnews_facebookcomments/class.tx_ttnewsfacebookcomments.php:tx_ttnewsfacebookcomments';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['extraGlobalMarkerHook'][] =  'EXT:ttnews_facebookcomments/class.tx_ttnewsfacebookcomments.php:tx_ttnewsfacebookcomments';

?>