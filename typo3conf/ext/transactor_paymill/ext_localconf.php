<?php

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['paymill'] =  'EXT:transactor_paymill/class.tx_transactorpaymill_request.php' ;

?>