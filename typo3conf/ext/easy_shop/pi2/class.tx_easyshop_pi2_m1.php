<?php
define('ext_lib','ext_lib/');
require_once(PATH_tslib.'class.tslib_pibase.php');
if (!class_exists('PHP_Mailer')) {require_once(ext_lib.'class.phpmailer.php');}

class tx_easyshop_pi2 extends tslib_pibase {
	var $prefixId      = 'tx_easyshop_pi2';		// Same as class name
	var $scriptRelPath = 'pi2/class.tx_easyshop_pi2.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'easy_shop';	// The extension key.
	var $pi_checkCHash = true;
	var $pi_USER_INT_obj = 0;
	var $tx_web_shop_pi1;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
/*
t3lib_div::Debug($conf);
exit();
*/
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->init($conf);
		/*
		t3lib_div::Debug($this->conf);
		exit();
		*/

		$this->tx_web_shop_pi1 = t3lib_div::makeInstance('tx_easyshop_pi1');
		$this->tx_web_shop_pi1->init($this->conf);
		$this->tx_web_shop_pi1->cObj = $this->cObj;

		//$this->tx_web_shop_pi1->getAllUsersBasketSession();
		
		if($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_easyshop_pi2']=='USER_INT'){
			$this->pi_USER_INT_obj=1;
			$this->pi_checkCHash = false;	
		}
		if($this->conf['includeJQueryFramework']&&$this->conf['jQueryFrameworkFile']){
			//$GLOBALS['TSFE']->additionalHeaderData['web_shop_framework_jQuery'] = '<script src="'.$this->conf['jQueryFrameworkFile'].'" type="text/javascript"></script>';
		}
		
		$content='';
		$arg=$GLOBALS["TSFE"]->fe_user->getKey('ses','web_shop');
		if(!is_array($arg['session']['products'])){$arg['session']['products']=array();}
		foreach(explode(',',$this->conf['display']) as $display){
			switch($display){
				case 1:
					$content=$this->displayBasketFull($arg);
				break;
				case 2:
					$content=$this->displayBasketCompact($arg);
				break;
				case 3:
					$content=$this->displayBoxOffice($arg);	
				break;
			}	
		}
		return $this->pi_wrapInBaseClass($content);
	}
	function init($conf){
	  $this->pi_initPIflexForm(); // Init and get the flexform data of the plugin
	  $this->conf = $conf; // Setup our storage array...
	  // Assign the flexform data to a local variable for easier access
	  $piFlexForm = $this->cObj->data['pi_flexform'];
	  // Traverse the entire array based on the language...
	  // and assign each configuration option to $this->conf array...
	  if(is_array($piFlexForm['data'])){
	  	foreach ( $piFlexForm['data'] as $sheet => $data )
	    	foreach ( $data as $lang => $value )
	      		foreach ( $value as $key => $val )
	       			$this->conf[$key] = $this->pi_getFFvalue($piFlexForm, $key, $sheet);	
	  }
	  /*if(!$this->conf['templateFile']){
			$this->conf['templateFile'] = 'typo3conf/ext/tend_shop/templates/shop_basket.html';
	  }
	  if($this->conf['usergroupTemplateFile']){
	  		$this->conf['templateFile'] = $this->conf['usergroupTemplateFile'];
	  }*/
	  $this->pi_loadLL();
	  $this->piVars['payment_type']=t3lib_div::_POST('payment_type');
	  $this->refreshBasket();
	}
	
	function displayBoxOffice($arg){
		$products_keys_list=array_keys($arg['session']['products']);
		$template='';
		$template = $this->loadTemplate('###PRODUCTS_BASKET_CHECKOUT_TEMPLATE###');
		if($this->piVars['checkBasket']){
			if(t3lib_div::_POST('tipPlacila') == 1) {
				$templateMail = $this->loadTemplateMail('###EMAIL_POVZETJU###');
			} 
			if(t3lib_div::_POST('tipPlacila') == 2) {
				$templateMail = $this->loadTemplateMail('###EMAIL_UPN###');
			}
			$templateRacun = $this->loadTemplateRacun('###MAIL_RACUN###');
		}
		//$templateRacun = $this->loadTemplateRacun('###MAIL_RACUN###');
		//t3lib_utility_Debug::debug($templateRacun);
		
		if(!$template){return $this->pi_getLL('no_template_error');}
		
		$singleMark=array();
		$singleMark['###cUID###']=$this->cObj->data['uid'];
		$multiMark=array();
		
		$products=array();
		$prodIsSample=array();
		$products['count']=0;
		
		//t3lib_utility_Debug::debug($products_keys_list);
		
		foreach($products_keys_list as $prodPropKey) {
			$propUidArray = explode('_', $prodPropKey);
			$prodUids[] = $propUidArray[0];
			$propProp1Array[] = $propUidArray[1];
			$propProp2Array[] = $propUidArray[2];
			$prodIsSample[] = (isset($propUidArray[3]))?1:0;
		}
		
		if(count($prodUids)>0){
			$params=array();
			$params['order_by']=$this->piVars['orderBy'];
			$params['products']=implode(',',$prodUids);
			$products = $this->getProducts($params);	
		}
		
//t3lib_div::Debug($products);
		if($products['count']==0){return $this->pi_getLL('empty_basket_list');}
		else{
			$template_single = $this->cObj->getSubpart($template, '###BASKET_SINGLE_PRODUCT###');
			if ($templateMail) {
				$template_mail_single = $this->cObj->getSubpart($templateMail, '###SINGLE_IZDELEK###');
				$template_mail_reciver = $this->cObj->getSubpart($templateMail, '###RECIVER_DATA###');
			}
			
			if ($templateRacun) {
				$template_racun_single = $this->cObj->getSubpart($templateRacun, '###SINGLE_IZDELEK###');
			}
			
			$singleMarkProduct=array();
			$singleMarkProduct['###cUID###']=$this->cObj->data['uid'];
			//if($GLOBALS['TSFE']->loginUser){
// DA SAE POLJA PREDIZPOLNIJO			 
			//}
			$products_items='';
			$mail_items='';
			$racun_items='';
			$sum_total_price=$sum_total_member_price=0.00;
			$sum_total_tax=0.00;
			$sum_total_tax_other=0.00;
			$sum_total_tax_def=0.00;
			
			$quantity_sum=0;
			//t3lib_utility_Debug::debug($products);
			
			$discountVal = 0;
			
			//$promoDiscount = $this->getPromoDiscount($this->piVars['promoCode']);
			//$fixedDiscount = $this->getPromoFixDiscount($this->piVars['promoCode']);
		
			//if(count($promoDiscount)) {
			//	$discountVal = intval($promoDiscount['discount']);
			//} 
			
			$discountVal = 0;
			$promoValue = 0;
			$promoVal = 0.00;
			
			if($this->piVars['promoCode']){
				$discountVal = 0;
				$promoDiscount = $this->getPromoDiscount($this->piVars['promoCode']);
				$discountVal = intval($promoDiscount['discount']);
				if($discountVal) {
					$multiMark['###PROMO_TEXT###'] = '';
					$multiMark['###PROMO_FIX###'] = '';
				} else {
					$promoValue = $this->getPromoValue($this->piVars['promoCode']);
					//t3lib_utility_Debug::debug($promoValue);
					if(count($promoValue)) {$promoVal = intval($promoValue['discount']);}
				}
			} else {
				$multiMark['###PROMO_TEXT###'] = '';
				$multiMark['###PROMO_FIX###'] = '';
			}		
			$productsLog = array();
			
			for($j=0; $j<count($prodUids); $j++){
			//foreach($prodUids as $prodUid){
				$product = $this->tx_web_shop_pi1->getProduct(array('uid'=>$prodUids[$j]));
				if($GLOBALS['TSFE']->loginUser && $GLOBALS['TSFE']->fe_user->user['usergroup']==2) {
					if($product['price_partner'] || $product['web_price_partner']) {
						$product['price'] = $product['price_partner'];
						$product['web_price'] = $product['web_price_partner'];
					}
				}
				
				$productProperties2 = $this->tx_web_shop_pi1->getProductProperties2(array('prod_uid'=>$prodUids[$j]));
				$position = 0;
				
				for($i=0; $i < count($productProperties2); $i++) {
					if($productProperties2[$i]['uid'] == $propProp2Array[$j]) {
						$position = $i;	
					}
				}
				
				// ZA CENE VEZANE NA KOLIÈINE
				if($product['price_prop2'] && $propProp2Array[$j]) {
					$priceArray = explode('|',$product['price_prop2']);
					$product['price'] = trim($priceArray[$position]);	
				}
				
				// ZA CENE VEZANE NA KOLIÈINE
				if($product['price_disc_prop2'] && $propProp2Array[$j]) {
					$priceDiscArray = explode('|',$product['price_disc_prop2']);
					$product['web_price'] = trim($priceDiscArray[$position]);	
				}
				
				//Log array za shranjevanje nakupa v bazo
				$productsLog[$j]['uid'] = $prodUids[$j];
				$productsLog[$j]['prop'] = $propProp1Array[$j];
				$productsLog[$j]['prop2'] = $propProp2Array[$j];
				$productsLog[$j]['sample'] = ($prodIsSample[$j])?1:0;
				$productsLog[$j]['num'] = ($prodIsSample[$j])?1:$arg['session']['products'][$product['uid'].'_'.$propProp1Array[$j].'_'.$propProp2Array[$j]]['quantity'];
				$productsLog[$j]['price'] = ($prodIsSample[$j])?0:$product['price'];
				$productsLog[$j]['web_price'] = ($prodIsSample[$j])?0:$product['web_price'];

				//$singleMarkProduct['###PRODUCT###']=$product['title'];
				$singleMarkProduct['###PRODUCT###']=($prodIsSample[$j])?$product['title'].'&nbsp;(VZOREC)':$product['title'];
				$singleMarkProduct['###PRODUCT_UID###']=$product['uid'];
				$singleMarkProduct['###PRODUCT_SINGLE_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->tx_web_shop_pi1->pi_linkTP_keepPIvars_url(array('cat'=>$this->piVars['cat'],'prod'=>$product['uid']), 1, 1,$this->conf['page_single_product']);
				$singleMarkProduct['###PRODUCT_SUBTITLE###']=$product['subtitle'];	
				$singleMarkProduct['###DESCRIPTION###']=$product['description'];				
							
				if($product['web_price']) {
					$prodDisc = 100 - intval(100*(floatval(str_replace(',','.',$product['web_price'])))/floatval(str_replace(',','.',$product['price'])));
					if($discountVal) {
						if($discountVal >= $prodDisc) {
							$prodDisc = $discountVal;
						}
					}
					$itemPrice = ($prodIsSample[$j])?0:floatval(str_replace(',','.',$product['price']))*((100-$prodDisc)/100);
					$singleMarkProduct['###PRODUCT_PRICE###']=($prodIsSample[$j])?0:$product['web_price'];
					$singleMarkProduct['###PRODUCT_ACTION_DISCOUNT###']=100 - intval(100*(floatval(str_replace(',','.',$product['web_price'])))/floatval(str_replace(',','.',$product['price'])));
					$sum_total_price_item=floatval(str_replace(',','.',$itemPrice))*$arg['session']['products'][$product['uid'].'_'.$propProp1Array[$j].'_'.$propProp2Array[$j]]['quantity'];		
					$singleMarkProduct['###PRODUCT_TOTAL_PRICE###']=($prodIsSample[$j])?0:number_format(floatval(str_replace(',','.',$product['web_price']))*$arg['session']['products'][$product['uid'].'_'.$propProp1Array[$j].'_'.$propProp2Array[$j]]['quantity'], 2, ',', '.');
				} else {
					$itemPrice = ($prodIsSample[$j])?0:floatval(str_replace(',','.',$product['price']))*((100-$discountVal)/100);
					$singleMarkProduct['###PRODUCT_PRICE###']=($prodIsSample[$j])?0:$product['price'];
					$singleMarkProduct['###PRODUCT_ACTION_DISCOUNT###'] = 0;
					$sum_total_price_item=floatval(str_replace(',','.',$itemPrice))*$arg['session']['products'][$product['uid'].'_'.$propProp1Array[$j].'_'.$propProp2Array[$j]]['quantity'];					
					$singleMarkProduct['###PRODUCT_TOTAL_PRICE###']=($prodIsSample[$j])?0:number_format(floatval(str_replace(',','.',$product['price']))*$arg['session']['products'][$product['uid'].'_'.$propProp1Array[$j].'_'.$propProp2Array[$j]]['quantity'], 2, ',', '.');
				}
								
				/*
				$itemTax = 0;
				if(!$product['vat']) {
					$itemTax = $sum_total_price_item * floatval(str_replace(',','.',$this->conf['defTax'])) / 100;
				} else {
					$itemTax = $sum_total_price_item * floatval(str_replace(',','.',$product['vat'])) / 100;
				}*/
				
				if(!$product['vat']) {
					$singleMarkProduct['###PRODUCT_TAX###']=$this->conf['defTax'];
					$itemTax = $sum_total_price_item - ($sum_total_price_item*100)/(floatval(str_replace(',','.',$this->conf['defTax']))+100);
					$sum_total_tax_def+=$itemTax;
				} else {
					$singleMarkProduct['###PRODUCT_TAX###']=$product['vat'];
					$itemTax = $sum_total_price_item - ($sum_total_price_item*100)/(floatval(str_replace(',','.',$product['vat']))+100);
					if(intval($product['vat']) > 19) {
						$sum_total_tax_def+=$itemTax;					
					} else {
						$sum_total_tax_other+=$itemTax;					
					}
					
				}
				
				$singleMarkProduct['###PRODUCT_QUANTITY###']=($prodIsSample[$j])?1:$arg['session']['products'][$product['uid'].'_'.$propProp1Array[$j].'_'.$propProp2Array[$j]]['quantity'];
				//$quantity_sum+=$arg['session']['products'][$product['uid'].'_'.$propProp1Array[$j]]['quantity'];
				
				//$singleMarkProduct['###PRODUCT_QUANTITY###']=$arg['session']['products'][$product['uid']]['quantity'];
				$sum_total_price+=$sum_total_price_item;
				$sum_total_tax+=$itemTax;
				//$sum_total_member_price+=floatval(str_replace(',','.',$product['web_price']))*$arg['session']['products'][$product['uid']]['quantity'];
													
				$template_prop1=$this->cObj->getSubpart($template, '###SINGLE_PROPERTY_DISPLAY###');
				$template_prop2=$this->cObj->getSubpart($template, '###SINGLE_PROPERTY2_DISPLAY###');
				$template_prop2_racun=$this->cObj->getSubpart($templateRacun, '###SINGLE_PROPERTY2_DISPLAY###');
				
				if(intval($propProp1Array[$j])) {
					$prodProp = $this->tx_web_shop_pi1->getProperty($propProp1Array[$j]);
					$singleMarkItemProp1['###SINGLE_PROPERTY_TITLE###']=$prodProp['title'];
					$multiMarkItem['###SINGLE_PROPERTY_DISPLAY###']=$this->cObj->substituteMarkerArrayCached($template_prop1,$singleMarkItemProp1,array(),array());
				
				} else {
					$multiMarkItem['###SINGLE_PROPERTY_DISPLAY###']='';
				}
				
				if(intval($propProp2Array[$j])) {
					$prodProp2 = $this->tx_web_shop_pi1->getProperty2(intval($propProp2Array[$j]));
					$singleMarkItemProp2['###SINGLE_PROPERTY2_TITLE###']=$prodProp2['title'];
					$multiMarkItem['###SINGLE_PROPERTY2_DISPLAY###']=$this->cObj->substituteMarkerArrayCached($template_prop2,$singleMarkItemProp2,array(),array());
					$multiMarkItemRac['###SINGLE_PROPERTY2_DISPLAY###']=$this->cObj->substituteMarkerArrayCached($template_prop2_racun,$singleMarkItemProp2,array(),array());
				} else {
					$multiMarkItem['###SINGLE_PROPERTY2_DISPLAY###']='';
					$multiMarkItemRac['###SINGLE_PROPERTY2_DISPLAY###']='';
				}
								
				$products_items.=$this->cObj->substituteMarkerArrayCached($template_single,$singleMarkProduct,$multiMarkItem,array());
				if ($templateMail) {
					//$argProp = implode(',',$arg['session']['products'][$product['uid']]['prop1']);
					//t3lib_utility_Debug::debug($arg['products']);
					//$arg['products']
					//if($argProp) {
						//$prodProperties = $this->getProductProperties($argProp);
						//t3lib_utility_Debug::debug($prodProperties);
						//$singleMarkProduct['###SINGLE_PROPERTY_TITLE###'] = '';
						//foreach($prodProperties as $prop){
							//$singleMarkProduct['###SINGLE_PROPERTY_TITLE###'].=$prop['title_front'].' ';
						//}
					//} else {
					//	$singleMarkProduct['###SINGLE_PROPERTY_TITLE###'] = '/';
					//}

					
					$origImagesArray=explode(',',$product['images']);		
					$leadImage = $origImagesArray[0];
					$imgConfig['file.']['maxW'] = '190c';
					//$imgConfig['file.']['maxH'] = $this->conf['image.']['maxH'].'c';
					$imgConfig['file.']['width'] = '190c';
					//$imgConfig['file.']['height'] = $this->conf['image.']['maxH'].'c';
					$imgConfig['file.']['XY'] = '[10.w],[10.h]';
					$singleMark['###IMAGE_ORIGINAL###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').'uploads/tx_easyshop/'.$leadImage;
					$imgConfig['file'] = 'uploads/tx_easyshop/'.$leadImage;
					$origImg = $this->cObj->IMAGE($imgConfig);
					$resizedImageInfo = $GLOBALS['TSFE']->lastImageInfo;
					$singleMarkProduct['###MAIL_IMAGE_SRC###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$resizedImageInfo[3];
					$mail_items.=$this->cObj->substituteMarkerArrayCached($template_mail_single,$singleMarkProduct,$multiMarkItem,array());
					$racun_items.=$this->cObj->substituteMarkerArrayCached($template_racun_single,$singleMarkProduct,$multiMarkItemRac,array());
				}
				
//t3lib_div::Debug($index+1);
			}
			
			//t3lib_utility_Debug::debug($productsLog);
			//exit();
			
			$multiMark['###BASKET_SINGLE_PRODUCT###']=$products_items;

			$static_country=array();
			if($GLOBALS['TSFE']->loginUser && $GLOBALS['TSFE']->fe_user->user['country']){
				$static_country=$this->getStaticCountry($GLOBALS['TSFE']->fe_user->user['country']);	
			}
			$multiMark['###POSTAGE_PRICE###']='';
			$sum_total_price_no_postage=$sum_total_price;
			
			$sum_total_member_price_no_postage=$sum_total_member_price;

			//Check if after possetion payment is selected
			if($this->piVars['payment_type']&&$this->piVars['payment_type']!=$this->conf['check_out_page']){
				$postage_price+=$this->calculateAddPrice($this->conf['addPrice.'][$quantity_postage_price.'.'],$sum_total_price);
			}
			if($sum_total_price<$this->conf['postage.']['no_postage_price.'][$quantity_postage_price] || !$this->conf['postage.']['no_postage_price.'][$quantity_postage_price]){
				$multiFreePostage=array();
				if(!$this->conf['postage.']['no_postage_price.'][$quantity_postage_price]){
					$multiFreePostage=array('###FREE_POSTAGE###'=>'');
				}
				if($postage_array_count>0){
					$price_till_free_postage=$this->tx_web_shop_pi1->moneyFormat(floatval($this->conf['postage.']['no_postage_price.'][$quantity_postage_price])-$sum_total_price);       						
       				$sum_total_price+=$postage_price;
       				$multiMark['###POSTAGE_PRICE###']=$this->cObj->substituteMarkerArrayCached($this->cObj->getSubpart($template, '###POSTAGE_PRICE###'),array('###POSTAGE###'=>$postage_price,'###PRICE_TILL_FREE_POSTAGE###'=>$price_till_free_postage),$multiFreePostage,array());
				}
				$multiMark['###NO_POSTAGE_TEXT###']='';
			}
		
			if($sum_total_member_price<$this->conf['postage.']['no_postage_price.'][$quantity_postage_price] || !$this->conf['postage.']['no_postage_price.'][$quantity_postage_price]){
				$multiFreePostage=array();
				if(!$this->conf['postage.']['no_postage_price.'][$quantity_postage_price]){
					$multiFreePostage=array('###FREE_POSTAGE###'=>'');
				}
				//$quantity_sum
				if($postage_array_count>0){
					$price_till_free_postage=$this->tx_web_shop_pi1->moneyFormat(floatval($this->conf['postage.']['no_postage_price.'][$quantity_postage_price])-$sum_total_member_price);
	   				$sum_total_member_price+=$postage_price;
	   				$multiMark['###POSTAGE_PRICE###']=$this->cObj->substituteMarkerArrayCached($this->cObj->getSubpart($template, '###POSTAGE_PRICE###'),array('###POSTAGE###'=>$postage_price,'###PRICE_TILL_FREE_POSTAGE###'=>$price_till_free_postage),$multiFreePostage,array());	
				}
				$multiMark['###NO_POSTAGE_MEMBER_TEXT###']='';
			}
			
			if($discountVal) {
				$discountValDecimal = (100-$discountVal)/100;
				//$sum_total_price_no_postage = ($sum_total_price_no_postage*$discountValDecimal);
				//$sum_total_tax = ($sum_total_tax*$discountValDecimal);
				//$sum_total_tax_other = ($sum_total_tax_other*$discountValDecimal);
				//$sum_total_tax_def = ($sum_total_tax_def*$discountValDecimal);
			}
			
			$clientPickup = (intval(t3lib_div::_POST('prevzem')) == 2)?1:0;
			
			if($clientPickup){
				$postageTax = 0;
			} else {
				$postageTax = $this->conf['postageValue'] - ($this->conf['postageValue']*100)/(floatval(str_replace(',','.',$this->conf['defTax']))+100);
			}
			
			//$postageTax = $this->conf['postageValue'] - ($this->conf['postageValue']*100)/(floatval(str_replace(',','.',$this->conf['defTax']))+100);
			$postageWOTax = $this->conf['postageValue']-$postageTax;
			
			$singleMark['###SUM_TOTAL_PRICE_NO_POSTAGE###']=number_format($sum_total_price_no_postage, 2, ',', '.');
			$singleMark['###SUM_TOTAL_MEMBER_PRICE_NO_POSTAGE###']=number_format($sum_total_member_price_no_postage, 2, ',', '.');
			
			$singleMark['###SUM_TOTAL_PRICE_NO_TAX###']=number_format($sum_total_price_no_postage-$sum_total_tax, 2, ',', '.');
			$singleMark['###SUM_TOTAL_TAX###']=number_format($sum_total_tax, 2, ',', '.');
			$singleMark['###SUM_TOTAL_TAX_OTHER###']=number_format($sum_total_tax_other, 2, ',', '.');
			if($sum_total_price_no_postage >= $this->conf['postageLimit']) {
				$singleMark['###SUM_TOTAL_TAX_DEF###']=number_format($sum_total_tax_def, 2, ',', '.');
				$singleMark['###SUM_TOTAL_TAX_DEF_2###']=number_format($sum_total_tax_def, 2, ',', '.');
				$def_tax_log = $sum_total_tax_def;
			} else {
				$singleMark['###SUM_TOTAL_TAX_DEF###']=number_format($sum_total_tax_def+$postageTax, 2, ',', '.');
				$singleMark['###SUM_TOTAL_TAX_DEF_2###']=number_format($sum_total_tax_def, 2, ',', '.');
				$def_tax_log = ($clientPickup)?$sum_total_tax_def:$sum_total_tax_def+$postageTax;
			}	
				
			$singleMark['###SUM_TOTAL_PRICE###']=number_format($sum_total_price, 2, ',', '.');
			$singleMark['###SUM_TOTAL_MEMBER_PRICE###']=number_format($sum_total_member_price, 2, ',', '.');			
								
			//$clientPickup = (intval(t3lib_div::_POST('prevzem')) == 2)?1:0;
			
			$GLOBALS['TSFE']->additionalHeaderData['web_shop_script']= "<script type='text/javascript'>
			
			jQuery(document).ready(function(){
				$('#sumPersonal').hide();
				$('#prevzem2').change(function() {
					$('#sumPost').hide();
					$('#sumPersonal').show();
				});
				$('#prevzem1').change(function() {
					$('#sumPersonal').hide();
					$('#sumPost').show();
				});
			})</script>";
			
			
			if($clientPickup){
				$multiMark['###RECIVER_NOPICKUP###']='';
				$this->conf['postageLimit'] = 0;
			} else {
				$multiMark['###RECIVER_PICKUP###']='';
			}
			
			if($sum_total_price_no_postage >= $this->conf['postageLimit']) {
				$singleMark['###POSTAGE_VAL###']=number_format(0, 2, ',', '.');
				$finalPrice = (($sum_total_price_no_postage-$promoVal)<0)?0:$sum_total_price_no_postage-$promoVal;
				$singleMark['###SUM_TOTAL_PRICE_POSTAGE###']=number_format($finalPrice, 2, ',', '.');
				$singleMark['###SUM_TOTAL_PRICE_NO_POSTAGE_FIXED###']=number_format($finalPrice, 2, ',', '.');
				$postageLog = 0;
			} else {
				$singleMark['###POSTAGE_VAL###']=number_format($postageWOTax, 2, ',', '.');
				$finalPrice = (($sum_total_price_no_postage+$this->conf['postageValue']-$promoVal)<0)?0:$sum_total_price_no_postage+$this->conf['postageValue']-$promoVal;
				$finalPriceNo = (($sum_total_price_no_postage-$promoVal)<0)?0:$sum_total_price_no_postage-$promoVal;
				$singleMark['###SUM_TOTAL_PRICE_POSTAGE###']=number_format($finalPrice, 2, ',', '.');
				$singleMark['###SUM_TOTAL_PRICE_NO_POSTAGE_FIXED###']=number_format($finalPriceNo, 2, ',', '.');
				$postageLog = $this->conf['postageValue'];
			}
			
			// popust za kupone
			$singleMark['###TOTAL_DISCOUNT###']=intval($discountVal);
			$singleMark['###FIXED_DISCOUNT###']=$promoVal;
			
			if($this->piVars['promoCode']) {
				$singleMark['###PAYMENT_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('checkBasket' => 1, 'promoCode' => $this->piVars['promoCode']), 1, 1);
			} else {
				$singleMark['###PAYMENT_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('checkBasket' => 1), 1, 1);
			}
						
			$singleMark['###USER_NAME###']='';
			$singleMark['###USER_LAST_NAME###']='';
			$singleMark['###COMPANY###']='';
			$singleMark['###ID_DDV###']='';
			$singleMark['###TEL###']='';
			$singleMark['###EMAIL###']='';
			$singleMark['###USER_ADDRESS###']='';
			$singleMark['###USER_ZIP###']='';
			$singleMark['###USER_CITY###']='';
			$singleMark['###PRAVNA_CHECKED###']='nocheck';
			
			if($GLOBALS['TSFE']->loginUser) {
				$singleMark['###USER_NAME###']=$GLOBALS['TSFE']->fe_user->user['first_name'];
				$singleMark['###USER_LAST_NAME###']=$GLOBALS['TSFE']->fe_user->user['last_name'];
				$singleMark['###COMPANY###']=$GLOBALS['TSFE']->fe_user->user['company'];
				$singleMark['###ID_DDV###']=$GLOBALS['TSFE']->fe_user->user['tx_webshopregistration_id_ddv'];
				$singleMark['###TEL###']=$GLOBALS['TSFE']->fe_user->user['telephone'];
				$singleMark['###EMAIL###']=$GLOBALS['TSFE']->fe_user->user['email'];
				$singleMark['###USER_ADDRESS###']=$GLOBALS['TSFE']->fe_user->user['address'];
				$singleMark['###USER_ZIP###']=$GLOBALS['TSFE']->fe_user->user['zip'];
				$singleMark['###USER_CITY###']=$GLOBALS['TSFE']->fe_user->user['city'];
				
				if($GLOBALS['TSFE']->fe_user->user['company']) {
					$singleMark['###PRAVNA_CHECKED###']='checked';
				}
			}
			//t3lib_utility_Debug::debug($this->conf);
			if($this->piVars['checkBasket']){
				//t3lib_div::debug(t3lib_div::_POST('hasInviters_'.$product['uid']));
				
				
				//t3lib_utility_Debug::debug(t3lib_div::_POST(),'POST');
				//t3lib_utility_Debug::debug($this->piVars,'PIVARS');
				$multiMark['###SINGLE_IZDELEK###'] = $mail_items;
				$multiMarkRacun['###SINGLE_IZDELEK###'] = $racun_items;
				if(t3lib_div::_POST('tipPlacila') && t3lib_div::_POST('buyer_email')) {					
					$singleMark['###BUYER_NAME###']=t3lib_div::_POST('buyer_name');
					$singleMark['###BUYER_SURNAME###']=t3lib_div::_POST('buyer_surname');
					$singleMark['###COMPANY###']=t3lib_div::_POST('company');
					$singleMark['###ID_DDV###']=t3lib_div::_POST('id_ddv');
					$singleMark['###BUYER_TEL###']=t3lib_div::_POST('buyer_tel');
					$singleMark['###BUYER_EMAIL###']=t3lib_div::_POST('buyer_email');
					$singleMark['###BUYER_ADDRESS###']=t3lib_div::_POST('buyer_address');
					$singleMark['###BUYER_POST###']=t3lib_div::_POST('buyer_post');
					$singleMark['###BUYER_CITY###']=t3lib_div::_POST('buyer_city');
					
					if(t3lib_div::_POST('drugPlacnik') == 'on') {
						$singleMark['###RECIVER_NAME###']=t3lib_div::_POST('reciver_name');
						$singleMark['###RECIVER_SURNAME###']=t3lib_div::_POST('reciver_surname');
						$singleMark['###RECIVER_ADDRESS###']=t3lib_div::_POST('reciver_address');
						$singleMark['###RECIVER_POST###']=t3lib_div::_POST('reciver_post');
						$singleMark['###RECIVER_CITY###']=t3lib_div::_POST('reciver_city');
					} else {
						$singleMark['###RECIVER_NAME###']=t3lib_div::_POST('buyer_name');
						$singleMark['###RECIVER_SURNAME###']=t3lib_div::_POST('buyer_surname');
						$singleMark['###RECIVER_ADDRESS###']=t3lib_div::_POST('buyer_address');
						$singleMark['###RECIVER_POST###']=t3lib_div::_POST('buyer_post');
						$singleMark['###RECIVER_CITY###']=t3lib_div::_POST('buyer_city');
					}
					$singleMark['###DATE_DUE###'] = date('d.m.Y', mktime(0, 0, 0, date('m'), date('d')+8, date('Y')));
					$singleMark['###DATE_NOW###'] = date('d.m.Y');
					if(t3lib_div::_POST('tipPlacila') == 1) {
						$singleMark['###PAYMENT_TEXT###']="PlaÄilo po povzetju.";
						if($clientPickup) $singleMark['###PAYMENT_TEXT###'].="&nbsp;Osebni prevzem.";
						
						$singleMark['###USER_NAME###']=t3lib_div::_POST('buyer_name');
						$singleMark['###USER_LAST_NAME###']=t3lib_div::_POST('buyer_surname');
						$singleMark['###USER_ADDRESS###']=t3lib_div::_POST('buyer_address');
						$singleMark['###USER_ZIP###']=t3lib_div::_POST('buyer_post');
						$singleMark['###USER_CITY###']=t3lib_div::_POST('buyer_city');
						$buyerId = $this->storeBuyer(t3lib_div::_POST());
						if(t3lib_div::_POST('drugPlacnik') == 'on') {
							$reciverId = $this->storeReciver(t3lib_div::_POST());
						}
						$basket_val = $arg;						
						$pType = ($clientPickup)?0:t3lib_div::_POST('tipPlacila');						
						$logId=$this->savePaymentLog($pType,serialize($basket_val),$promoVal,$buyerId,$reciverId,$discountVal,$sum_total_price_no_postage, $postageLog, $def_tax_log, $sum_total_tax_other);			
						$this->savePaymentproducts($logId, $productsLog);
						$singleMark['###REFERENCE###']=str_pad($logId, 6, '0', STR_PAD_LEFT);
						$mailBody = $this->cObj->substituteMarkerArrayCached($templateMail,$singleMark,$multiMark,array());
						//$mailBodyRacun = $this->cObj->substituteMarkerArrayCached($templateRacun,$singleMark,$multiMarkRacun,array());
						
						$this->sendEmail($this->conf['from_name'],$this->conf['from'], t3lib_div::_POST('buyer_email'), t3lib_div::_POST('buyer_email'), $this->conf['subject'], $mailBody);
						$this->sendEmail($this->conf['from_name'],$this->conf['from'], $this->conf['send_to'], $this->conf['send_to'], $this->conf['subject'], $mailBody);
						//$this->sendEmail($this->conf['from_name'],$this->conf['from'], $this->conf['send_to'], $this->conf['send_to'], 'RaÄun', $mailBodyRacun);
						/*
						$this->sendEmail($this->conf['from_name'],$this->conf['from'], 'mitja.venturini@gmail.com', 'mitja.venturini@gmail.com', $this->conf['subject'], $mailBody);
						$this->sendEmail($this->conf['from_name'],$this->conf['from'], 'mitja.venturini@gmail.com', 'mitja.venturini@gmail.com', $this->conf['subject'], $mailBody);
						$this->sendEmail($this->conf['from_name'],$this->conf['from'], 'mitja.venturini@gmail.com', 'mitja.venturini@gmail.com', 'RaÄun', $mailBodyRacun);
						*/
					} 
					if(t3lib_div::_POST('tipPlacila') == 2) {
						$singleMark['###PAYMENT_TEXT###']="PlaÄilo po predraÄunu.";
						if($clientPickup) $singleMark['###PAYMENT_TEXT###'].="&nbsp;Osebni prevzem.";
						$singleMark['###USER_NAME###']=t3lib_div::_POST('buyer_name');
						$singleMark['###USER_LAST_NAME###']=t3lib_div::_POST('buyer_surname');
						$singleMark['###USER_ADDRESS###']=t3lib_div::_POST('buyer_address');
						$singleMark['###USER_ZIP###']=t3lib_div::_POST('buyer_post');
						$singleMark['###USER_CITY###']=t3lib_div::_POST('buyer_city');
						
						$buyerId = $this->storeBuyer(t3lib_div::_POST());
						if(t3lib_div::_POST('drugPlacnik') == 'on') {
							$reciverId = $this->storeReciver(t3lib_div::_POST());
						} 
						$basket_val = $arg;
						$pType = ($clientPickup)?0:t3lib_div::_POST('tipPlacila');
						$logId=$this->savePaymentLog($pType,serialize($basket_val),$promoVal,$buyerId,$reciverId,$discountVal,$sum_total_price_no_postage, $postageLog, $def_tax_log, $sum_total_tax_other);			
						$this->savePaymentproducts($logId, $productsLog);
						$singleMark['###REFERENCE###']=str_pad($logId, 6, '0', STR_PAD_LEFT);
						$mailBody = $this->cObj->substituteMarkerArrayCached($templateMail,$singleMark,$multiMark,array());
						//$mailBodyRacun = $this->cObj->substituteMarkerArrayCached($templateRacun,$singleMark,$multiMarkRacun,array());

						$this->sendEmail($this->conf['from_name'],$this->conf['from'], t3lib_div::_POST('buyer_email'), t3lib_div::_POST('buyer_email'), $this->conf['subject'], $mailBody);
						$this->sendEmail($this->conf['from_name'],$this->conf['from'], $this->conf['send_to'], $this->conf['send_to'], $this->conf['subject'], $mailBody);
						//$this->sendEmail($this->conf['from_name'],$this->conf['from'], $this->conf['send_to'], $this->conf['send_to'], 'RaÄun', $mailBodyRacun);
/*						
						$this->sendEmail($this->conf['from_name'],$this->conf['from'], 'mitja.venturini@gmail.com', 'mitja.venturini@gmail.com', $this->conf['subject'], $mailBody);
						$this->sendEmail($this->conf['from_name'],$this->conf['from'], 'mitja.venturini@gmail.com', 'mitja.venturini@gmail.com', $this->conf['subject'], $mailBody);
						$this->sendEmail($this->conf['from_name'],$this->conf['from'], 'mitja.venturini@gmail.com', 'mitja.venturini@gmail.com', 'RaÄun', $mailBodyRacun);
*/						
					}				
				$this->tx_web_shop_pi1->deleteUserSession();
				//peterw
				if($this->piVars['promoCode']){
					$promoDiscount = $this->getPromoDiscount($this->piVars['promoCode']);
					if($promoDiscount['onetime']){
						$this->hidePromoDiscount($promoDiscount['uid']);
					}
				}
				
				header('Location:'.t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array(),0,1,$this->conf['page_end_payment']));
				}
			}
			return $this->cObj->substituteMarkerArrayCached($template,$singleMark,$multiMark,array());	
		}
	}
	
	function displayBasketFull($arg){
		$products_keys_list=array_keys($arg['session']['products']);

//t3lib_utility_Debug::debug($_POST);
//t3lib_utility_Debug::debug($this->conf);
//exit();
		$refresh=false;
		if(count($products_keys_list)>0){
		//if(t3lib_div::_POST('basket_update')&&count($products_keys_list)>0){
			$post_list_keys=implode(',',array_keys($_POST));
			foreach($arg['session']['products'] as $k=>$p){
				if(t3lib_div::inList($post_list_keys,'product_input_'.$k)){
					$q=intval(t3lib_div::_POST('product_input_'.$k));
					//t3lib_utility_Debug::debug($q);
					if($q>0){
						if(intval($q) != intval($arg['session']['products'][$k]['quantity'])) {
							$refresh=true;
						}
						$arg['session']['products'][$k]['quantity']=$q;
					}
					else{
						unset($arg['session']['products'][$k]);
						$products_keys_list=array_keys($arg['session']['products']);
					}
					$GLOBALS["TSFE"]->fe_user->setKey('ses','web_shop',$arg);
					$GLOBALS["TSFE"]->storeSessionData();
				}
//t3lib_div::Debug();
			}
			$this->tx_web_shop_pi1->getAllUsersBasketSession();
		}
		if(@$this->piVars['removeFromBasket']){
			unset($arg['session']['products'][$this->piVars['removeFromBasket']]);
			$products_keys_list=array_keys($arg['session']['products']);
			$GLOBALS["TSFE"]->fe_user->setKey('ses','web_shop',$arg);
			$GLOBALS["TSFE"]->storeSessionData();
			$refresh=true;
		}
		
		if($refresh) {
			header('Location:'.t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array(),0,1,$this->conf['page_basket_view']));
		}
		
		$template='';
		//if($this->piVars['basket_preview']){
		//	$template = $this->loadTemplate('###PRODUCTS_BASKET_PREVIEW_TEMPLATE###');
		//}else{
			$template = $this->loadTemplate('###PRODUCTS_BASKET_LIST_TEMPLATE###');
		//}
		//t3lib_utility_Debug::debug($template);
		if(!$template){return $this->pi_getLL('no_template_error');}
		$singleMark=array();
		$singleMark['###cUID###']=$this->cObj->data['uid'];
		$multiMark=array();
		
		$products=array();
		$products['count']=0;
		
		//t3lib_utility_Debug::debug($products_keys_list);
		//$prodPropKeys = explode('_', $products_keys_list);
		$prodUids = array();
		$propUidArray = array();
		$propProp1Array = array();
		$propProp2Array = array();
		$prodIsSample = array();
		foreach($products_keys_list as $prodPropKey) {
			$propUidArray = explode('_', $prodPropKey);
			$prodUids[] = $propUidArray[0];
			$propProp1Array[] = $propUidArray[1];
			$propProp2Array[] = $propUidArray[2];
			$prodIsSample[] = (isset($propUidArray[3]))?1:0;
		}
		
		if(count($prodUids)>0){
			$params=array();
			$params['order_by']=$this->piVars['orderBy'];
			$params['products']=implode(',',$prodUids);
			
			//DA NI PAGEBROWSINGA
			$params['next_prev']=true;
			$products = $this->getProducts($params);	
		}
		//t3lib_utility_Debug::debug($products);
		
		
//t3lib_div::Debug($products);
		if($products['count']==0){return $this->pi_getLL('empty_basket_list');}
		else{
			$template_single = $this->cObj->getSubpart($template, '###BASKET_SINGLE_PRODUCT###');
			$template_stock_yes = $this->cObj->getSubpart($template_single, '###ZALOGAYES###');
			$template_stock_no = $this->cObj->getSubpart($template_single, '###ZALOGANO###');

//t3lib_div::Debug($template_body);		
			$multiMark['###PRODUCTS_PAGE_TEMPLATE###']='';

			$singleMarkProduct=array();
			$singleMarkProduct['###cUID###']=$this->cObj->data['uid'];
			
			if(t3lib_div::_POST('promoCode')){			
				$singleMark['###PAYMENT_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('promoCode' => t3lib_div::_POST('promoCode')), 1, 1, $this->conf['check_out_page']);
			} else {
				$singleMark['###PAYMENT_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array(), 1, 1, $this->conf['check_out_page']);
			}
			
			$products_items='';
			$sum_total_price=$sum_total_member_price=0.00;
			$sum_total_tax=0.00;
			$sum_total_tax_other=0.00;
			$sum_total_tax_def=0.00;
			$quantity_sum=0;
			//t3lib_utility_Debug::debug($products);
			
			$discountVal = 0;
			if(t3lib_div::_POST('promoCode')){
				$discountVal = 0;
				$promoVal = 0;
				$promoDiscount = $this->getPromoDiscount(t3lib_div::_POST('promoCode'));
				$discountVal = intval($promoDiscount['discount']);
				
				//t3lib_utility_Debug::debug($this->conf);
				if($discountVal) {
					$multiMark['###PROMO_AFTER2###'] = '';
					$multiMark['###PROMO_TEXT###'] = '';
					$multiMark['###PROMO_FIX###'] = '';
				} else {
					$promoValue = $this->getPromoValue(t3lib_div::_POST('promoCode'));
					//t3lib_utility_Debug::debug($promoValue);
					if(count($promoValue)) {$promoVal = intval($promoValue['discount']);}
					$multiMark['###PROMO_AFTER###'] = '';
				}
				$multiMark['###PROMO_BEFORE###'] = '';
			} else {
				$multiMark['###PROMO_AFTER###'] = '';
				$multiMark['###PROMO_AFTER2###'] = '';
				$multiMark['###PROMO_TEXT###'] = '';
				$multiMark['###PROMO_FIX###'] = '';
			}
			
			//t3lib_utility_Debug::debug($promoValue);
			
			for($j=0; $j<count($prodUids); $j++){
			//foreach($prodUids as $prodUid){
				$product = $this->tx_web_shop_pi1->getProduct(array('uid'=>$prodUids[$j]));
				if($GLOBALS['TSFE']->loginUser && $GLOBALS['TSFE']->fe_user->user['usergroup']==2) {
					if($product['price_partner'] || $product['web_price_partner']) {
						$product['price'] = $product['price_partner'];
						$product['web_price'] = $product['web_price_partner'];
					}
				}
				
				$productProperties2 = $this->tx_web_shop_pi1->getProductProperties2(array('prod_uid'=>$prodUids[$j]));
				$position = 0;
				
				for($i=0; $i < count($productProperties2); $i++) {
					if($productProperties2[$i]['uid'] == $propProp2Array[$j]) {
						$position = $i;	
					}
				}
				
				// ZA CENE VEZANE NA KOLIÈINE
				if($product['price_prop2'] && $propProp2Array[$j]) {
					$priceArray = explode('|',$product['price_prop2']);
					$product['price'] = trim($priceArray[$position]);	
				}
				
				// ZA CENE VEZANE NA KOLIÈINE
				if($product['price_disc_prop2'] && $propProp2Array[$j]) {
					$priceDiscArray = explode('|',$product['price_disc_prop2']);
					$product['web_price'] = trim($priceDiscArray[$position]);	
				}
				
				$multiMarkItem['###ZALOGAYES###']='';
				$multiMarkItem['###ZALOGANO###']='';
			//t3lib_utility_Debug::debug($product);
				$singleMarkProduct['###PRODUCT###']=($prodIsSample[$j])?$product['title'].'&nbsp;(VZOREC)':$product['title'];
				$singleMarkProduct['###PRODUCT_UID###']=$product['uid'];
				$singleMarkProduct['###PRODUCT_SINGLE_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->tx_web_shop_pi1->pi_linkTP_keepPIvars_url(array('cat'=>$this->piVars['cat'],'prod'=>$product['uid']), 1, 1,$this->conf['page_single_product']);
				$singleMarkProduct['###PRODUCT_SUBTITLE###']=$product['subtitle'];
				$singleMarkProduct['###PRODUCT_DESCRIPTION###']=$this->cObj->parseFunc($product['description'], $GLOBALS['TSFE']->tmpl->setup['tt_content.']['text.']['20.']['parseFunc.']);
				$singleMarkProduct['###PRODUCT_PROPERTIES###']='';
				$singleMarkProduct['###PRODUCT_CATEGORIES###']='';
				
				$singleMarkProduct['###PRODUCT_MEMBER_PRICE###']=$product['web_price'];
				
				$origImagesArray=explode(',',$product['images']);		
				$leadImage = $origImagesArray[0];
				//$imgConfig['file.']['maxW'] = '98c';
				$imgConfig['file.']['maxH'] = $this->conf['image.']['maxH'].'c';
				//$imgConfig['file.']['width'] = '98c';
				$imgConfig['file.']['height'] = $this->conf['image.']['maxH'].'c';
				$imgConfig['file.']['XY'] = '[10.w],[10.h]';
				$singleMark['###IMAGE_ORIGINAL###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').'uploads/tx_easyshop/'.$leadImage;
				$imgConfig['file'] = 'uploads/tx_easyshop/'.$leadImage;
				$origImg = $this->cObj->IMAGE($imgConfig);
				$resizedImageInfo = $GLOBALS['TSFE']->lastImageInfo;
				$singleMarkProduct['###IMAGE_THUMBNAIL_SRC###']=$resizedImageInfo[3];
				
				//$singleMarkProduct['###PRODUCT_PRICE###']=$product['price'];				
				
				if($product['web_price']) {
					$prodDisc = 100 - intval(100*(floatval(str_replace(',','.',$product['web_price'])))/floatval(str_replace(',','.',$product['price'])));
					if($discountVal) {
						if($discountVal >= $prodDisc) {
							$prodDisc = $discountVal;
						}
					}
					$itemPrice = ($prodIsSample[$j])?0:floatval(str_replace(',','.',$product['price']))*((100-$prodDisc)/100);
					$singleMarkProduct['###PRODUCT_PRICE###']=($prodIsSample[$j])?0:$product['web_price'];					
					$singleMarkProduct['###PRODUCT_ACTION_DISCOUNT###']=100 - intval(100*(floatval(str_replace(',','.',$product['web_price'])))/floatval(str_replace(',','.',$product['price'])));
					$sum_total_price_item=floatval(str_replace(',','.',$itemPrice))*$arg['session']['products'][$product['uid'].'_'.$propProp1Array[$j].'_'.$propProp2Array[$j]]['quantity'];		
					$singleMarkProduct['###PRODUCT_TOTAL_PRICE###']=($prodIsSample[$j])?0:number_format(floatval(str_replace(',','.',$product['web_price']))*$arg['session']['products'][$product['uid'].'_'.$propProp1Array[$j].'_'.$propProp2Array[$j]]['quantity'], 2, ',', '.');
				} else {
					$itemPrice = ($prodIsSample[$j])?0:floatval(str_replace(',','.',$product['price']))*((100-$discountVal)/100);
					$singleMarkProduct['###PRODUCT_PRICE###']=($prodIsSample[$j])?0:$product['price'];		
					$singleMarkProduct['###PRODUCT_ACTION_DISCOUNT###'] = 0;
					$sum_total_price_item=floatval(str_replace(',','.',$itemPrice))*$arg['session']['products'][$product['uid'].'_'.$propProp1Array[$j].'_'.$propProp2Array[$j]]['quantity'];					
					$singleMarkProduct['###PRODUCT_TOTAL_PRICE###']=($prodIsSample[$j])?0:number_format(floatval(str_replace(',','.',$product['price']))*$arg['session']['products'][$product['uid'].'_'.$propProp1Array[$j].'_'.$propProp2Array[$j]]['quantity'], 2, ',', '.');
				}
				
				//t3lib_utility_Debug::debug($product);				
				$itemTax = 0;
				/*
				if(!$product['vat']) {
					$itemTax = $sum_total_price_item - ($sum_total_price_item*100)/(floatval(str_replace(',','.',$this->conf['defTax']))+100);
				} else {
					$itemTax = $sum_total_price_item - ($sum_total_price_item*100)/(floatval(str_replace(',','.',$product['vat']))+100);			
				}*/
				
				if(!$product['vat']) {
					$itemTax = $sum_total_price_item - ($sum_total_price_item*100)/(floatval(str_replace(',','.',$this->conf['defTax']))+100);
					$sum_total_tax_def+=$itemTax;
				} else {
					$itemTax = $sum_total_price_item - ($sum_total_price_item*100)/(floatval(str_replace(',','.',$product['vat']))+100);
					if(intval($product['vat']) > 19) {
						$sum_total_tax_def+=$itemTax;
					} else {
						$sum_total_tax_other+=$itemTax;
					}
						
				}
				
				if($product['stock']) {
					$multiMarkItem['###ZALOGAYES###']=$template_stock_yes;
				} else {
					$multiMarkItem['###ZALOGANO###']=$template_stock_no;
				}
				/*$singleMarkProduct['###PRODUCT_STOCK###']=$product['stock'];
				$singleMarkProduct['###PRODUCT_MEASURE_UNIT###']=$product['measure_unit'];*/
				if(($prodIsSample[$j])) {
					$singleMarkProduct['###PRODUCT_QUANTITY###'] = 1;
				}else {
					$singleMarkProduct['###PRODUCT_QUANTITY###']=$arg['session']['products'][$product['uid'].'_'.$propProp1Array[$j].'_'.$propProp2Array[$j]]['quantity'];
				}				
				$quantity_sum+=$arg['session']['products'][$product['uid'].'_'.$propProp1Array[$j].'_'.$propProp2Array[$j]]['quantity'];
				
				$template_prop1=$this->cObj->getSubpart($template, '###SINGLE_PROPERTY_DISPLAY###');
				$template_prop2=$this->cObj->getSubpart($template, '###SINGLE_PROPERTY2_DISPLAY###');
				
				if($propProp1Array[$j]) {
					$prodProp = $this->tx_web_shop_pi1->getProperty($propProp1Array[$j]);
					//t3lib_utility_Debug::debug($prodProp);
					$singleMarkItemProp1['###SINGLE_PROPERTY_TITLE###']=$prodProp['title'];
						$multiMarkItem['###SINGLE_PROPERTY_DISPLAY###']=$this->cObj->substituteMarkerArrayCached($template_prop1,$singleMarkItemProp1,array(),array());
					
					} else {
						$multiMarkItem['###SINGLE_PROPERTY_DISPLAY###']='';
					}
					
				if($propProp2Array[$j]) {
					$prodProp2 = $this->tx_web_shop_pi1->getProperty2($propProp2Array[$j]);
					$singleMarkItemProp2['###SINGLE_PROPERTY2_TITLE###']=$prodProp2['title'];
					$multiMarkItem['###SINGLE_PROPERTY2_DISPLAY###']=$this->cObj->substituteMarkerArrayCached($template_prop2,$singleMarkItemProp2,array(),array());
				} else {
					$multiMarkItem['###SINGLE_PROPERTY2_DISPLAY###']='';
				}
				
				$singleMarkProduct['###PRODUCT_QUANTITY_INPUT###']=($prodIsSample[$j])?'product_input_'.$product['uid'].'_'.$propProp1Array[$j].'_'.$propProp2Array[$j].'_1':'product_input_'.$product['uid'].'_'.$propProp1Array[$j].'_'.$propProp2Array[$j];
				
				if(($prodIsSample[$j])) {
					$singleMarkProduct['###REMOVE_PRODUCT_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('removeFromBasket'=>$product['uid'].'_'.$propProp1Array[$j].'_'.$propProp2Array[$j].'_1'), 1, 1);
				}else {
					$singleMarkProduct['###REMOVE_PRODUCT_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('removeFromBasket'=>$product['uid'].'_'.$propProp1Array[$j].'_'.$propProp2Array[$j]), 1, 1);
				}
				
				$sum_total_price+=$sum_total_price_item;
				$sum_total_tax+=$itemTax;
				//$sum_total_member_price+=floatval(str_replace(',','.',$product['web_price']))*$arg['session']['products'][$product['uid']]['quantity'];
				
				if($i++==$products['count']){$last=true;}
				$products_items.=$this->cObj->substituteMarkerArrayCached($template_single,$singleMarkProduct,$multiMarkItem,array());
//t3lib_div::Debug($index+1);
			}
			$multiMark['###BASKET_SINGLE_PRODUCT###']=$products_items;
			//$template_sum_total = $this->cObj->getSubpart($template_body, '###SUM_TOTAL###');

			$static_country=array();
			if($GLOBALS['TSFE']->loginUser && $GLOBALS['TSFE']->fe_user->user['country']){
				$static_country=$this->getStaticCountry($GLOBALS['TSFE']->fe_user->user['country']);	
			}
			/*
			$quantity_postage_price=($static_country['cn_tldomain'])?$static_country['cn_tldomain']:'default';			
			$postage_array=$this->conf['postage.']['quantity_postage_price.'][$quantity_postage_price.'.'];
			$postage_array_count=0;
			
			if(is_array($postage_array)){$postage_array_count=count($postage_array);}
			else{$postage_array=array();}
			*/
			$multiMark['###POSTAGE_PRICE###']='';
			$sum_total_price_no_postage_promo=(($sum_total_price-$promoVal)<0)?0:$sum_total_price-$promoVal;
			$sum_total_price_no_postage = $sum_total_price;			
			$sum_total_member_price_no_postage=$sum_total_member_price;
/*
t3lib_div::Debug($this->conf['postage.']['no_postage_price.'][$quantity_postage_price]);
t3lib_div::Debug($this->conf['postage.']['no_postage_price.']);
exit();
*/
		
			//$postage_price=$this->calculatePostage($postage_array,$quantity_sum);
			
			//Check if after possetion payment is selected
			if($this->piVars['payment_type']&&$this->piVars['payment_type']!=$this->conf['check_out_page']){
				$postage_price+=$this->calculateAddPrice($this->conf['addPrice.'][$quantity_postage_price.'.'],$sum_total_price);
			}
			if($sum_total_price<$this->conf['postage.']['no_postage_price.'][$quantity_postage_price] || !$this->conf['postage.']['no_postage_price.'][$quantity_postage_price]){
				$multiFreePostage=array();
				if(!$this->conf['postage.']['no_postage_price.'][$quantity_postage_price]){
					$multiFreePostage=array('###FREE_POSTAGE###'=>'');
				}
				if($postage_array_count>0){
					$price_till_free_postage=$this->tx_web_shop_pi1->moneyFormat(floatval($this->conf['postage.']['no_postage_price.'][$quantity_postage_price])-$sum_total_price);       						
       				$sum_total_price+=$postage_price;
       				$multiMark['###POSTAGE_PRICE###']=$this->cObj->substituteMarkerArrayCached($this->cObj->getSubpart($template, '###POSTAGE_PRICE###'),array('###POSTAGE###'=>$postage_price,'###PRICE_TILL_FREE_POSTAGE###'=>$price_till_free_postage),$multiFreePostage,array());
				}
				$multiMark['###NO_POSTAGE_TEXT###']='';
			}
		
			if($sum_total_member_price<$this->conf['postage.']['no_postage_price.'][$quantity_postage_price] || !$this->conf['postage.']['no_postage_price.'][$quantity_postage_price]){
				$multiFreePostage=array();
				if(!$this->conf['postage.']['no_postage_price.'][$quantity_postage_price]){
					$multiFreePostage=array('###FREE_POSTAGE###'=>'');
				}
				//$quantity_sum
				if($postage_array_count>0){
					$price_till_free_postage=$this->tx_web_shop_pi1->moneyFormat(floatval($this->conf['postage.']['no_postage_price.'][$quantity_postage_price])-$sum_total_member_price);
	   				$sum_total_member_price+=$postage_price;
	   				$multiMark['###POSTAGE_PRICE###']=$this->cObj->substituteMarkerArrayCached($this->cObj->getSubpart($template, '###POSTAGE_PRICE###'),array('###POSTAGE###'=>$postage_price,'###PRICE_TILL_FREE_POSTAGE###'=>$price_till_free_postage),$multiFreePostage,array());	
				}
				$multiMark['###NO_POSTAGE_MEMBER_TEXT###']='';
			}
/*			
			$discountVal = 0;
			if(t3lib_div::_POST('promoCode')){
				$discountVal = 0;
				$promoVal = 0;
				$promoDiscount = $this->getPromoDiscount(t3lib_div::_POST('promoCode'));
				//t3lib_utility_Debug::debug($promoDiscount);
				//t3lib_utility_Debug::debug($this->conf);
				if(count($promoDiscount)) {
					$discountVal = intval($promoDiscount['discount']);
					$multiMark['###PROMO_AFTER2###'] = '';
				} else {
					$promoValue = $this->getPromoValue(t3lib_div::_POST('promoCode'));
					if(count($promoValue)) {$promoVal = intval($promoValue['discount']);}
					$multiMark['###PROMO_AFTER###'] = '';
				}
				$multiMark['###PROMO_BEFORE###'] = '';
			} else {
				$multiMark['###PROMO_AFTER###'] = '';
				$multiMark['###PROMO_AFTER2###'] = '';
			}
*/			
			if($discountVal) {
				$discountValDecimal = (100-$discountVal)/100;
				/*$sum_total_price_no_postage = ($sum_total_price_no_postage*$discountValDecimal);
				$sum_total_tax = ($sum_total_tax*$discountValDecimal);
				$sum_total_tax_other = ($sum_total_tax_other*$discountValDecimal);
				$sum_total_tax_def = ($sum_total_tax_def*$discountValDecimal);*/
			}
			
			//t3lib_utility_Debug::debug($this->conf['postageLimit']);
			//t3lib_utility_Debug::debug($this->conf['postageValue']);
			
			$singleMark['###SUM_TOTAL_PRICE_NO_POSTAGE###']=number_format($sum_total_price_no_postage_promo, 2, ',', '.');
			$singleMark['###SUM_TOTAL_MEMBER_PRICE_NO_POSTAGE###']=number_format($sum_total_member_price_no_postage, 2, ',', '.');
			
			$singleMark['###SUM_TOTAL_PRICE_NO_TAX###']=number_format($sum_total_price_no_postage-$sum_total_tax, 2, ',', '.');
			$singleMark['###SUM_TOTAL_TAX###']=number_format($sum_total_tax, 2, ',', '.');
			$singleMark['###SUM_TOTAL_TAX_OTHER###']=number_format($sum_total_tax_other, 2, ',', '.');
			$singleMark['###SUM_TOTAL_TAX_DEF###']=number_format($sum_total_tax_def, 2, ',', '.');
			$singleMark['###TOTAL_PLUS###']=0;
			
			if($sum_total_price_no_postage >= $this->conf['postageLimit']) {
				$singleMark['###POSTAGE_VAL###']=number_format(0, 2, ',', '.');
				$singleMark['###SUM_TOTAL_PRICE_POSTAGE###']=number_format($sum_total_price_no_postage, 2, ',', '.');
			} else {
				$singleMark['###POSTAGE_VAL###']=number_format($this->conf['postageValue'], 2, ',', '.');
				$singleMark['###SUM_TOTAL_PRICE_POSTAGE###']=number_format($sum_total_price_no_postage+$this->conf['postageValue'], 2, ',', '.');
			}
			
			// popust za kupone
			$singleMark['###TOTAL_DISCOUNT###']=intval($discountVal);
			$singleMark['###FIXED_DISCOUNT###']=$promoVal;
			
			/*if($this->piVars['promoCode']) {
				$singleMark['###PAYMENT_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('checkBasket' => 1, 'promoCode' => $this->piVars['promoCode']), 1, 1);
			} else {
				$singleMark['###PAYMENT_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('checkBasket' => 1), 1, 1);
			}*/

			$singleMark['###SUM_TOTAL_PRICE###']=number_format($sum_total_price, 2, ',', '.');
			$singleMark['###SUM_TOTAL_MEMBER_PRICE###']=number_format($sum_total_member_price, 2, ',', '.');
//exit();
//t3lib_div::Debug($GLOBALS['TSFE']->fe_user);
//t3lib_div::Debug($template_sum_total);
			//$multiMark['###PRODUCTS_PAGE_TEMPLATE###'].=$this->cObj->substituteMarkerArrayCached($template_body,$singleMarkProduct,$multiMarkProduct,array());
			return $this->cObj->substituteMarkerArrayCached($template,$singleMark,$multiMark,array());	
		}
	}
	function displayBasketCompact($arg){
		$basketProducts = $arg['session']['products'];
		$prodNum = 0;
		$prodSum = 0;
		$template = $this->loadTemplate('###BASKET_COMPACT_VIEW###');
		$template_item = $this->cObj->getSubpart($template, '###COMPACT_BASKET_ITEM###');
		$multieMark['###COMPACT_BASKET_ITEM###']='';
		if(count($basketProducts)) {
		//t3lib_utility_Debug::debug($basketProducts);
			foreach($basketProducts as $key=>$prod) {
				$keyArray=array();
				$prodNum = $prodNum + intval($prod['quantity']);
				$userPrice = ($prod['web_price'])?$prod['web_price']:$prod['price'];
				//$memberPrice = ($prod['web_price'])?$prod['web_price']:$prod['price'];				
				$prodSum+=floatval(str_replace(',','.',$userPrice))*$prod['quantity'];
				//$sum_total_member_price+=floatval(str_replace(',','.',$product['web_price']))*$arg['session']['products'][$product['uid']]['quantity'];
				
				$keyArray=explode('_',$key);
				$product = $this->tx_web_shop_pi1->getProduct(array('uid'=>$keyArray[0]));
				//t3lib_utility_Debug::debug($product);
				//t3lib_utility_Debug::debug($key);
				$imagesArray=explode(',',$product['images']);
				$uplImage = $imagesArray[0];				
				$imgConfigBig['file.']['maxH'] = '80c';
				$imgConfigBig['file.']['height'] = '80c';
				$imgConfigBig['file.']['XY'] = '[10.w],[10.h]';
				$imgConfigBig['file'] = 'uploads/tx_easyshop/'.$uplImage;
				$origImgBig = $this->cObj->IMAGE($imgConfigBig);
				$resizedImageInfo = $GLOBALS['TSFE']->lastImageInfo;
				$singleMarkItem['###ITEM_IMG_SRC###']=$resizedImageInfo[3];
				//$singleMarkItem['###ITEM_TITLE###']=$product['title'];
				$singleMarkItem['###ITEM_TITLE###']=(isset($keyArray[3]))?$product['title'].'&nbsp;(VZOREC)':$product['title'];
				$singleMarkItem['###ITEM_Q###']=$prod['quantity'];
				$singleMarkItem['###ITEM_PRICE###']=$this->tx_web_shop_pi1->moneyFormat($userPrice);
				$singleMarkItem['###ITEM_TOTAL###']=$this->tx_web_shop_pi1->moneyFormat(floatval(str_replace(',','.',$userPrice))*$prod['quantity']);
				
				$productCategories = $this->tx_web_shop_pi1->getProductCategories(array('prod_uid'=>$product['uid']));
				$singleMarkItem['###PRODUCT_SINGLE_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->tx_web_shop_pi1->pi_linkTP_keepPIvars_url(array('cat'=>$productCategories[0]['uid'],'prod'=>$product['uid']), 1, 1,$this->conf['page_single_product']);
				
				$template_prop1=$this->cObj->getSubpart($template, '###SINGLE_PROPERTY_DISPLAY###');
				$template_prop2=$this->cObj->getSubpart($template, '###SINGLE_PROPERTY2_DISPLAY###');
				
				if(intval($keyArray[1])) {
					$prodProp = $this->tx_web_shop_pi1->getProperty(intval($keyArray[1]));
					$singleMarkItemProp1['###SINGLE_PROPERTY_TITLE###']=$prodProp['title'];
					$multieMarkItem['###SINGLE_PROPERTY_DISPLAY###']=$this->cObj->substituteMarkerArrayCached($template_prop1,$singleMarkItemProp1,array(),array());
				
				} else {
					$multieMarkItem['###SINGLE_PROPERTY_DISPLAY###']='';
				}
				
				if(intval($keyArray[2])) {
					$prodProp2 = $this->tx_web_shop_pi1->getProperty2(intval($keyArray[2]));
					$singleMarkItemProp2['###SINGLE_PROPERTY2_TITLE###']=$prodProp2['title'];
					$multieMarkItem['###SINGLE_PROPERTY2_DISPLAY###']=$this->cObj->substituteMarkerArrayCached($template_prop2,$singleMarkItemProp2,array(),array());
				} else {
					$multieMarkItem['###SINGLE_PROPERTY2_DISPLAY###']='';
				}			
				
				$multieMark['###COMPACT_BASKET_ITEM###'].=$this->cObj->substituteMarkerArrayCached($template_item,$singleMarkItem,$multieMarkItem,array());
			}
			$multieMark['###EMPTY_BASKET###']='';
		} else {
			$multieMark['###FULL_BASKET###']='';
		}
		
		$singleMark['###BASKET_PRODUCTS_COUNT###']=$prodNum;
		$singleMark['###BASKETITEMSSUM###']=$this->tx_web_shop_pi1->moneyFormat($prodSum);
		//t3lib_utility_Debug::debug($this->conf);
		$singleMark['###BOXOFFICE_VIEW_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array(), 1, 1,$this->conf['check_out_page']);
		$singleMark['###BASKET_VIEW_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array(), 1, 1,$this->conf['page_basket_view']);
		//$str=$this->pi_getLL('login_text');
		//if($GLOBALS['TSFE']->loginUser){$str=$this->pi_getLL('logout_text');}
		//$singleMark['###LOGIN_LOGOUT_URL###']=$this->pi_linkTP_keepPIvars($str,array(), 1, 1,$this->conf['login_page']);
		return $this->cObj->substituteMarkerArrayCached($template,$singleMark,$multieMark,array());
	}
	function refreshBasket(){
		$basket_session = $GLOBALS["TSFE"]->fe_user->getKey('ses','web_shop');
		if(!is_array($basket_session['session']['products'])){$basket_session['session']['products']=array();}
		if($this->piVars['payment_type']){
			$basket_session['session']['payment']=$this->piVars['payment_type'];
		}
		$GLOBALS["TSFE"]->fe_user->setKey('ses','web_shop',$basket_session);
		$GLOBALS["TSFE"]->storeSessionData();
	}
	/*
	function savePaymentLog($basket_val){
		$insertArray['pid'] = $GLOBALS['TSFE']->id;
		$insertArray['tstamp'] = time();
		$insertArray['crdate'] = time();
		$insertArray['basket'] = $basket_val;
		$insertArray['user'] = $GLOBALS['TSFE']->fe_user->user['uid'];
		$insertArray['status'] = 1;
		$insertArray['response_value'] = '';
		$query = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_easyshop_payment_log', $insertArray);
		return $GLOBALS['TYPO3_DB']->sql_insert_id();
	}*/
	function getStaticCountry($country_name){
		$queryParts['SELECT'] = 'uid,cn_tldomain';
		$queryParts['FROM'] = 'static_countries';
		$queryParts['WHERE'] = 'cn_official_name_local like \'%'.$country_name.'%\' OR cn_official_name_en like \'%'.$country_name.'%\' OR cn_short_local like \'%'.$country_name.'%\'';
		$queryParts['LIMIT'] = 1;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		if($res){
			return $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		}
	}
	function calculatePostage($postage_array,$quantity){
		$postage_price=0.00;
		if($postage_array&&is_array($postage_array)){
			$last_array_key=end(array_keys($postage_array));
			while($quantity>$last_array_key){
				$postage_price+=$postage_array[$last_array_key];
				$quantity-=$last_array_key;
			}
			foreach ($postage_array as $q => $p_price){if($quantity<=$q){return $postage_price+$p_price;}}
		}
		return $postage_price;
	}
	function calculateAddPrice($price_array,$price){
		$add_price=0.00;
		if($price_array&&is_array($price_array)){
			$last_array_key=end(array_keys($price_array));
			if($price>=$last_array_key){return $price_array[$last_array_key];}
			foreach($price_array as $p => $a_price){
				if($price>=$p){$add_price=$a_price;}
				else{return $add_price;}
			}
		}
		return $add_price;
	}
	
	function getProductProperties($uids) {
		$queryParts['SELECT'] = "tx_easyshop_properties.*";
		$queryParts['FROM'] = "tx_easyshop_properties";
		$queryParts['WHERE'] = "tx_easyshop_properties.uid IN (".$uids.")";
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		if ($res){
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$result[] = $row;
			}
			return $result;
		}
		else{
			return array();	
		}
	}
	
	function getProducts($arg){
		if(!$arg['order_by']){
			$arg['order_by']='title';
			if($this->conf['productsDefaultSortOrder']){
				$arg['order_by']=$this->conf['productsDefaultSortOrder'];
			}
		}
		if(!$arg['asc_desc']){
			$arg['asc_desc']='ASC';
			if($this->conf['productsDefaultSortOrderAscDesc']){
				$arg['asc_desc']=$this->conf['productsDefaultSortOrderAscDesc'];
			}
		}
		$products=$products_overlay=$queryParts=$prod_pos=array();
		$queryParts['SELECT'] = 'tx_easyshop_products.*';
		$queryParts['FROM'] = 'tx_easyshop_products';
		$queryParts['WHERE'] = 'tx_easyshop_products.deleted=0 AND tx_easyshop_products.hidden=0 AND tx_easyshop_products.uid IN ('.$arg['products'].')';
		$queryParts['ORDERBY'] = 'tx_easyshop_products.'.$arg['order_by'].' '.$arg['asc_desc'];
		
		if($this->conf['language']){
			//Left Join added for sorting product overlay
			$queryParts['SELECT'] .= ', tx_easyshop_language_overlay.overlay_'.implode(', tx_easyshop_language_overlay.overlay_',$this->tx_web_shop_pi1->select_fields_array);
			$queryParts['FROM'] .= ' LEFT JOIN tx_easyshop_language_overlay ON tx_easyshop_products.uid=tx_easyshop_language_overlay.overlay_parrent';
			$queryParts['WHERE'] .= ' AND tx_easyshop_language_overlay.overlay_language='.$this->conf['language'].' AND tx_easyshop_language_overlay.hidden=0  AND tx_easyshop_language_overlay.deleted=0 ';
			if(in_array($arg['order_by'],$this->tx_web_shop_pi1->select_fields_array)){
				$queryParts['ORDERBY'] = 'tx_easyshop_language_overlay.overlay_'.$arg['order_by'].' '.$arg['asc_desc'];	
			}
			$queryPartsLan['SELECT'] = 'tx_easyshop_language_overlay.overlay_'.implode(', tx_easyshop_language_overlay.overlay_',$this->tx_web_shop_pi1->select_fields_array);
			$queryPartsLan['FROM'] = 'tx_easyshop_language_overlay';
			$queryPartsLan['WHERE'] = 'tx_easyshop_language_overlay.overlay_language='.$this->conf['language'].' AND tx_easyshop_language_overlay.hidden=0  AND tx_easyshop_language_overlay.deleted=0 ';
			$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryPartsLan);
			if($res){
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
					$products_overlay[$row['overlay_parrent']]=$row;
				}
			}
		}

//t3lib_div::Debug($queryParts);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		
		$p_count=0;
		if ($res){
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$products[]=$row;
				$prod_pos[$row['uid']]=$p_count++;
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}
		return array('products'=>$products,'count'=>$p_count, 'keys_list'=>$arg['products'], 'prod_pos'=>$prod_pos);
	}
	function getTTcontent($uid){
		$queryParts = array();
		$queryParts['SELECT'] = '*';
		$queryParts['FROM'] = 'tt_content';
		$queryParts['WHERE'] = 'uid='.$uid;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		$record = array();
		if ($res) {
			$record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}
		return $record;
	}
	
	function getPromoDiscount($code) {
		$queryParts = array();
		$queryParts['SELECT'] = '*';
		$queryParts['FROM'] = 'tx_easyshop_coupons';
		$queryParts['WHERE'] = 'code=\''.$code.'\' AND deleted=0 and hidden=0';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		$record = array();
		if ($res) {
			$record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}
		return $record;
	}
	
	function getPromoValue($code) {
		$queryParts = array();
		$queryParts['SELECT'] = '*';
		$queryParts['FROM'] = 'tx_easyshop_coupons2';
		$queryParts['WHERE'] = 'code=\''.$code.'\' AND deleted=0 and hidden=0';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		$record = array();
		if ($res) {
			$record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}
		return $record;
	}
	
	function hidePromoValue($uid){
		$fields_values['hidden'] = 1;		
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_easyshop_coupons2','uid='.$uid,$fields_values,$no_quote_fields=FALSE);
	}
	
	function hidePromoDiscount($uid){
		$fields_values['hidden'] = 1;		
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_easyshop_coupons','uid='.$uid,$fields_values,$no_quote_fields=FALSE);
	}
	
	function sendEmail($fromName,$fromEmail, $toName, $toEmail, $subject, $body){
		$mail = t3lib_div::makeInstance('t3lib_mail_Message');
		$mail->setSubject($subject);
		$mail->setFrom(array($fromEmail => $fromName));
		$mail->setTo(array($toEmail => $toName));
		$mail->setBody($body,'text/html');
		$mail->send();
	}
	
	function sendMail($sendTo, $subject, $message){
		$mail = new PHP_Mailer();
		$mail->IsHTML(true);
		$mail->Priority = $this->conf['priority'];
		$mail->CharSet = $this->conf['charset'];
		$mail->From = $this->conf['from'];
		$mail->FromName = $this->conf['from_name'];
		$mail->Subject = $this->conf['subject'];
		if($subject){$mail->Subject = $subject;}
		$mail->Body = $message;
		$mail->AltBody = $message;
		$mail->Mailer = $this->conf['mailer'];
		$mail->Sendmail = $this->conf['sendmail_path'];
		$mail->Host = $this->conf['host'];
		$mail->Port = $this->conf['port'];
		$mail->SMTPAuth = $this->conf['smtp_auth'];
		$mail->Username = $this->conf['username'];
		$mail->Password = $this->conf['password'];
		$mail->SMTPDebug = $this->conf['smtp_debug'];
		if($sendTo){
			foreach(explode(',',$sendTo) as $m){
				$mail->AddAddress($m);
			}
		}
		//t3lib_utility_Debug::debug($mail);
		return $mail->Send();
	}
	
	function savePaymentLog($payment_type, $basket_val, $fixDiscount, $buyerId, $reciverId, $discountVal, $sum_total_price_no_postage, $postageLog, $def_tax_log, $sum_total_tax_other){
		$insertArray['pid'] = $this->conf['storage_page'];
		$insertArray['tstamp'] = time();
		$insertArray['crdate'] = time();
		$insertArray['basket'] = $basket_val;
		$insertArray['user'] = $GLOBALS['TSFE']->fe_user->user['uid'];
		$insertArray['crdate'] = time();
		$insertArray['buyer'] = $buyerId;
		$insertArray['reciver'] = $reciverId;
		$insertArray['payment_type'] = $payment_type;
		$insertArray['fix_discount'] = $fixDiscount;
		$insertArray['discount'] = $discountVal;
		$insertArray['total'] = $sum_total_price_no_postage;
		$insertArray['total_ddv1'] = $def_tax_log;
		$insertArray['total_ddv2'] = $sum_total_tax_other;
		$insertArray['postage'] = $postageLog;
		$insertArray['status'] = 1;
		$insertArray['response_value'] = '';
		$query = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_easyshop_payment_log', $insertArray);
		return $GLOBALS['TYPO3_DB']->sql_insert_id();
	}
	
	function storeBuyer($post_array) {
		$insertArray['pid'] = $this->conf['storage_page'];
		$insertArray['tstamp'] = time();
		$insertArray['crdate'] = time();
		$insertArray['name'] = $post_array['buyer_name'];
		$insertArray['surname'] = $post_array['buyer_surname'];
		$insertArray['tel'] = $post_array['buyer_tel'];
		$insertArray['email'] = $post_array['buyer_email'];
		$insertArray['address'] = $post_array['buyer_address'];
		$insertArray['post'] = $post_array['buyer_post'];
		$insertArray['city'] = $post_array['buyer_city'];
		$insertArray['company'] = $post_array['company'];
		$insertArray['id_ddv'] = $post_array['id_ddv'];
		$query = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_easyshop_buyers', $insertArray);
		return $GLOBALS['TYPO3_DB']->sql_insert_id();
	}
	
	function storeReciver($post_array) {
		$insertArray['pid'] = $this->conf['storage_page'];
		$insertArray['tstamp'] = time();
		$insertArray['crdate'] = time();
		$insertArray['name'] = $post_array['reciver_name'];
		$insertArray['surname'] = $post_array['reciver_surname'];
		$insertArray['address'] = $post_array['reciver_address'];
		$insertArray['post'] = $post_array['reciver_post'];
		$insertArray['city'] = $post_array['reciver_city'];
		$query = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_easyshop_recivers', $insertArray);
		return $GLOBALS['TYPO3_DB']->sql_insert_id();
	}
	
	function savePaymentproducts($logId, $productsLog) {
		foreach($productsLog as $prod) {
			$insertArray['uid_local'] = $logId;
			$insertArray['uid_foreign'] = $prod['uid'];
			$insertArray['num'] = $prod['num'];
			$insertArray['prop'] = $prod['prop'];
			$insertArray['prop2'] = $prod['prop2'];
			$insertArray['price'] = $prod['price'];
			$insertArray['sample'] = $prod['sample'];
			$insertArray['web_price'] = $prod['web_price'];
			//t3lib_utility_Debug::debug($insertArray);
			$query = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_easyshop_payment_log_products_mm', $insertArray);
		}
	}
	
	function loadTemplate($marker){
		if($this->conf['templateFile']){
			return $this->cObj->getSubpart($this->cObj->fileResource($this->conf['templateFile']), $marker);
		}		
	}
	
	function loadTemplateMail($marker){
		if($this->conf['templateFile']){
			return $this->cObj->getSubpart($this->cObj->fileResource($this->conf['templateFileMail']), $marker);
		}		
	}
	
	function loadTemplateRacun($marker){
		//echo "TEST1";
		if($this->conf['racunTemplateFile']){
			//echo "TEST";
			return $this->cObj->getSubpart($this->cObj->fileResource($this->conf['racunTemplateFile']), $marker);
		}
	}
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tend_shop/pi2/class.tx_easyshop_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tend_shop/pi2/class.tx_easyshop_pi2.php']);
}
?>