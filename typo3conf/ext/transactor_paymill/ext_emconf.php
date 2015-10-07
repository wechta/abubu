<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "transactor_paymill".
 *
 * Auto generated 25-05-2015 17:08
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Transactor Paymill Gateway',
	'description' => 'Provides the possibility to transact payments via Paymill using the Payment Transactor extension.',
	'category' => 'misc',
	'shy' => 0,
	'version' => '0.0.2',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'alpha',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Franz Holzinger',
	'author_email' => 'franz@ttproducts.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-5.4.99',
			'typo3' => '4.5.0-4.7.99',
			'div2007' => '0.10.1-0.0.0',
			'transactor' => '0.2.2-',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:29:{s:9:"ChangeLog";s:4:"5207";s:38:"class.tx_transactorpaymill_request.php";s:4:"9f29";s:16:"ext_autoload.php";s:4:"f410";s:21:"ext_conf_template.txt";s:4:"8e38";s:12:"ext_icon.gif";s:4:"9e6e";s:17:"ext_localconf.php";s:4:"9921";s:13:"locallang.xml";s:4:"9e74";s:18:"paymentmethods.xml";s:4:"a33f";s:46:"control/class.tx_transactorpaymill_control.php";s:4:"6d4e";s:14:"doc/manual.sxw";s:4:"937f";s:29:"lib/Services/Paymill/Base.php";s:4:"7e76";s:32:"lib/Services/Paymill/Clients.php";s:4:"f9bc";s:32:"lib/Services/Paymill/Coupons.php";s:4:"c80e";s:34:"lib/Services/Paymill/Exception.php";s:4:"9bef";s:31:"lib/Services/Paymill/Offers.php";s:4:"28b2";s:33:"lib/Services/Paymill/Payments.php";s:4:"dd8e";s:42:"lib/Services/Paymill/Preauthorizations.php";s:4:"af4c";s:32:"lib/Services/Paymill/Refunds.php";s:4:"6ace";s:38:"lib/Services/Paymill/Subscriptions.php";s:4:"e8f5";s:37:"lib/Services/Paymill/Transactions.php";s:4:"fb6d";s:39:"lib/Services/Paymill/Apiclient/Curl.php";s:4:"b8c7";s:44:"lib/Services/Paymill/Apiclient/Interface.php";s:4:"3493";s:42:"lib/Services/Paymill/Apiclient/paymill.crt";s:4:"f85d";s:44:"model/class.tx_transactorpaymill_gateway.php";s:4:"761c";s:21:"res/logo-dunkel75.png";s:4:"529d";s:19:"res/logo-hell75.png";s:4:"76e7";s:19:"res/paymill-RGB.png";s:4:"09f0";s:20:"res/paymill_logo.png";s:4:"8842";s:34:"template/paymill_payment_form.html";s:4:"955b";}',
);

?>