<?php
include_once("core.php");

//autheticate
$api_sess = &$_SESSION['mt_api_sess'];
if (!$api_sess)
	{$api_user = "api.abubushop";
	$api_hash = "i397c6g4e1dc2d22d025b291cd0b2b3a9dLb2buu";
	
	$result = mt_api_call("init", array("api_user" => $api_user, "api_hash" => $api_hash));
	echo "<pre>";
	var_dump($result);
	echo "</pre>";
	}

//get articles sample 1, param id
$result = mt_api_call("get_articles", array("id" => "661"));

echo "<pre>";
var_dump($result);
echo "</pre>";

//get articles sample 2, param code
$result = mt_api_call("get_articles", array("code" => "1509668"));

echo "<pre>";
var_dump($result);
echo "</pre>";

//get articles sample 3, param bar_code
$result = mt_api_call("get_articles", array("bar_code" => "1509668"));

echo "<pre>";
var_dump($result);
echo "</pre>";

//get articles sample 4, param id/code/bar_code can return multi articles with ',' separator
$result = mt_api_call("get_articles", array("id" => "672,670,660"));
//$result = mt_api_call("get_articles", array("code" => "1509668,1509664,1509661"));
//$result = mt_api_call("get_articles", array("bar_code" => "1509668,1509664,1509661"));

//get articles sample 5, all articles
//$result = mt_api_call("get_articles", array("all" => "1"));

$result = mt_api_call("get_list");

echo "<pre>";
var_dump($result);
echo "</pre>";
?>