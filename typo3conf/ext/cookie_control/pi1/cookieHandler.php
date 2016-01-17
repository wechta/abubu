<?php
session_start();
class tx_test_cookies{
	function main(){

		if($_REQUEST['tx_cookie_control_pi1']['in'] == 1){
			$_SESSION['disable_session'] = FALSE;
		}
		if($_REQUEST['tx_cookie_control_pi1']['in'] == 2){ 
			$_SESSION['disable_session'] = TRUE;
			//$fe_user->setKey("ses","data", $uid);
			//$fe_user->storeSessionData();
			if (isset($_SERVER['HTTP_COOKIE'])) {
				$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
				foreach($cookies as $cookie) {
					$parts = explode('=', $cookie);
					$name = trim($parts[0]);
					if($name != 'PHPSESSID'){
						setcookie($name, '', time()-(60*60*24*365*10));
						setcookie($name, '', time()-(60*60*24*365*10), '/','.'.$_SERVER['HTTP_HOST']);
					}
				}
			}
			return;
		}

	}
}
$obj = new tx_test_cookies();
$obj->main();
exit;
?>
