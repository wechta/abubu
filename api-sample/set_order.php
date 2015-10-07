<?php
include_once("core.php");

//autheticate
$api_sess = &$_SESSION['mt_api_sess'];
if (!$api_sess)
	{$api_user = "api.abubushop";
	$api_hash = "i397c6g4e1dc2d22d025b291cd0b2b3a9dLb2buu";
	
	$result = mt_api_call("init", array("api_user" => $api_user, "api_user" => $api_user));
	echo "<pre>";
	var_dump($result);
	echo "</pre>";
	}

//get articles sample 1, param id
$params_head = array(
	"partner_title" => "",
	"partner_address",
	"datetime",
	"status"
);

$params_content = array(
	array(
		"art_id" => 10,
		"price" => 10,
		"vat" => 0.22,
		"price_total" => 100
	),
	array(
		"art_id" => 15,
		"price" => 10,
		"vat" => 0.22,
		"price_total" => 100
	)
);

$params = $params_head;
$params["content"] = json_encode($params_content);

$result = mt_api_call("set_order", $params);

echo "<pre>";
var_dump($result);
echo "</pre>";
?>