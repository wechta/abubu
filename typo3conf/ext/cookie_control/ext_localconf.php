<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_cookiecontrol_pi1.php', '_pi1', 'list_type', 1);

$TYPO3_CONF_VARS['FE']['eID_include']['cookieDelete'] = 'typo3conf/ext/cookie_control/pi1/cookieHandler.php';

?>

