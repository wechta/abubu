<?php
if (!defined ('TYPO3_MODE')) {
     die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['extraItemMarkerHook'][] =  'typo3conf/ext/jh_opengraph_ttnews/pi1/class.tx_jhopengraphttnews_pi1.php:tx_jhopengraphttnews_pi1';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_news']['sViewSelectConfHook']['jh_opengraph_ttnews'] = 'EXT:jh_opengraph_ttnews/class.tx_jhopengraphttnews_displaySingleHook.php:tx_jhopengraphttnews_displaySingleHook';

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_jhopengraphttnews_pi1.php', '_pi1', 'includeLib', 1);
?>