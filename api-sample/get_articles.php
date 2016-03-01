<?php
include_once("core.php");

//autheticate
$api_sess = &$_SESSION['mt_api_sess'];
$api_user = "api.abubushop";
$api_hash = "i397c6g4e1dc2d22d025b291cd0b2b3a9dLb2buu";

$result = mt_api_call("init", array("api_user" => $api_user, "api_hash" => $api_hash));

//dobis vse kategorije, ce ima pid=0 pol je glavna, upostevas vse active=1, razen Blagajna 01
/*$result = mt_api_call("getCategories", array("all" => "1"));
echo "<pre>";
var_dump($result);
echo "</pre>";*/

//dobis vse propertije
/*$result = mt_api_call("getProps", array("all" => "1"));
echo "<pre>";
var_dump($result);
echo "</pre>";*/

//$result = mt_api_call("getFile",array("file" => "/1u081Kcc1u4c1K891N4b1w8b1Ic71N8b1Ic91P0buc71N081Kcc/articles/-1."));
//var_dump($result);
//dobis vse GLAVNE izdelke.. zraven imas kateri kategoriji spadajo

echo "<pre>";
$i = 0;
$result = mt_api_call("getArticles", array("id" => "166"));
foreach ($result['data'] as $single) {
  if($single['pid']==0 && $single['web']==1 && $single['active']==1) {
    $i++;
    var_dump($single);
  }
}
var_dump('SKUPAJ GLAVNIH IZDELKOV: '.$i);
echo "</pre>";



//dobis podrejene izdelke za posamezen glavni izdelek, te potem zdruzis v en izdelek..
/*echo "<pre>";
$result = mt_api_call("getLinkedArt", array("id" => "2"));
foreach ($result['data'] as $single) {
  if($single['pid'] != 0) {
    $result2 = mt_api_call("getArticles", array("id" => $single['art']));
    var_dump($result2['data']);
  }
}
echo "</pre>";*/

?>
