<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type"
content="text/html; charset=utf-8"/>
<link href="https://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/css/bootstrap-combined.min.css" rel="stylesheet">
<?php

//
// Please download the Paymill PHP Wrapper at
// https://github.com/Paymill/Paymill-PHP
// and put the containing "lib" folder into your project
//

	tx_div2007_alpha::initFE();

	$conf = array();
	$extKey = tx_transactorpaymill_control::getExtKey();
	$prefixId = tx_transactorpaymill_control::getPiVar();
	tx_transactor_model_control::setPrefixId($prefixId);
	$piVars = tx_transactor_model_control::getPiVars();
	$callingExtension = $piVars[tx_transactor_model_control::getCallingExtensionVar()];
	$returi= $piVars[tx_transactor_model_control::getReturiVar()];
	$faillink= $piVars[tx_transactor_model_control::getFaillinkVar()];
	$successlink= $piVars[tx_transactor_model_control::getSuccesslinkVar()];
	$orderUid = $piVars[tx_transactor_model_control::getOrderVar()];

	if ($callingExtension != '') {

		if ($orderUid) {
			$confScript = array();

			$confScript['extName'] = $extKey;
			$confScript['paymentMethod'] = 'paymill_any';

			$reference = tx_transactor_api::getReferenceUid(
				'transactor',
				$confScript,
				$callingExtension,
				$orderUid
			);

			$gatewayProxyObject =
				tx_transactor_api::getGatewayProxyObject(
					'transactor',
					$confScript
				);

			$conf = $gatewayProxyObject->getConf();
			$transactionRow = $gatewayProxyObject->getTransaction($reference);
		}
	}

	define('PAYMILL_API_HOST', $conf['provideruri']);
	define('PAYMILL_API_KEY', $conf['privatekey']);

	set_include_path(
		implode(PATH_SEPARATOR, array(
			realpath(realpath(dirname(__FILE__)) . '/lib'),
			get_include_path())
		)
	);

	if ($token = $_POST['paymillToken']) {
		require "Services/Paymill/Transactions.php";
		$transactionsObject =
			new Services_Paymill_Transactions(PAYMILL_API_KEY, PAYMILL_API_HOST);

		$params = array(
			'amount' => intval($transactionRow['amount'] * 100), // E.g. "15" for 0.15 EUR!
			'currency' => $transactionRow['currency'], // ISO 4217
			'token' => $token,
			'description' => 'Test Transaction order: ' . $orderUid
		);

		$transaction = $transactionsObject->create($params);

		if (
			$transaction['status'] == 'closed' &&
			$transaction['response_code'] == '20000'
		) {

			$row = $transactionRow;

			$row['message'] = $transaction['description'];
			if ($transaction['payment']['type'] == 'debit') {
				$row['user'] = $transaction['payment']['holder'] . ':' . $transaction['payment']['account'] . ':' . $transaction['payment']['code'];
			} else if ($transaction['payment']['type'] == 'creditcard') {
				$row['user'] = $transaction['payment']['card_type'] . ':' . $transaction['payment']['expire_month'] . ':' . $transaction['payment']['expire_year'] . ':' . $transaction['payment']['last4'];
			} else {
				$row['user'] = 'ERROR: unknown payment type';
			}
			$row['state'] = TX_TRANSACTOR_TRANSACTION_STATE_APPROVE_OK;
			$row['gatewayid'] = $transaction['id'] . ': ' .
			$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'tx_transactor_transactions',
				'reference = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($reference, 'tx_transactor_transactions'),
				$row
			);
		}
	}

	header('Location: ' . t3lib_div::locationHeaderUrl($successlink));

?>
</head>
<body>
<div class="container">
<h1>We appreciate your purchase!</h1>

<h4>You transaction has been processed.</h4>
</div>
<script src="https://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.1/js/bootstrap.min.js"></script>
</body>
</html>