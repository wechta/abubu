<?php
session_start();

function mt_api_call($action = "", $params = array())
	{	
	//api url
	$api_url = "http://dev.minitron-apps.si/abubu/";
	
	//session
	$api_sess = &$_SESSION['mt_api_sess'];
	
	//get request params
	$lnk = "";
	$lnk .= "&mod=api";
	if ($action) {$lnk .= "&action=" . $action;}
	if ($api_sess) {$lnk .= "&sess=" . $api_sess;}
	$lnk .= "&mode=json";
	$lnk = substr($lnk, 1);
	
	//post request params	
	$post_request = "";
	if (is_array($params))
		{foreach(array_keys($params) as $key)
			{$post_request .= "&" . $key . "=" . urlencode($params[$key]);}
		}
	
	//prevent cache
	$api_url .= "?" . $lnk . "&f=" . time();
	
	//curl init
	$ch = curl_init($api_url); 
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch,CURLOPT_POST,true);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$post_request);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch,CURLOPT_TIMEOUT,30);
	
	$response = curl_exec($ch);
	
	$response_parsed = json_decode($response);
	$response_parsed = mt_api_stdclass2array($response_parsed);
		
	if (curl_errno($ch) || !$response)
		{$response_parsed["status"] = 0;}
	
	//session
	if (!$api_sess) {$api_sess = $response_parsed["sess"];}
	
	curl_close($ch);
	
	return $response_parsed;
	}

function mt_api_stdclass2array($std_object) 
	{
	if (is_object($std_object)) 
		{$std_object = get_object_vars($std_object);}
	
	if (is_array($std_object))
		{return array_map(__FUNCTION__, $std_object);}
	else 
		{return $std_object;}
	}
?>