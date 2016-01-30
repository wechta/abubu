<?php

require_once(PATH_tslib.'class.tslib_pibase.php');

class tx_easyshop_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_easyshop_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_easyshop_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'easy_shop';	// The extension key.
	var $pi_checkCHash = true;
	var $pi_USER_INT_obj = 0;
	var $cat_level = 1;
	var $select_fields_array = array('parrent','title','subtitle','measure_unit','description','images','images_captions','files');
	var $languages=array();
	var $tx_web_shop_pi2;
	var $feUserGroup=0;
	//var	$feUserDiscountGroup=array();
	var	$allCategoriesSorted=array();
	var $allCategories=array();
	var $allProp3=array();
	var $allProp4=array();
	var $productsBasketStockSession=array();
	
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		
		//$this->deleteUserSession();
		
		
		$this->init($conf);
		//$this->tx_web_shop_pi2 = t3lib_div::makeInstance('tx_easyshop_pi2');
		//$this->tx_web_shop_pi2->cObj = $this->cObj;
		if($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_easyshop_pi1']=='USER_INT'){
			$this->pi_USER_INT_obj=1;
			$this->pi_checkCHash = false;	
		}

//t3lib_div::Debug($GLOBALS['TSFE']->tmpl->setup['tt_content.']);
		
		if($this->conf['includeJQueryFramework']&&$this->conf['jQueryFrameworkFile']){
			//$GLOBALS['TSFE']->additionalHeaderData['web_shop_framework_jQuery'] = '<script src="'.$this->conf['jQueryFrameworkFile'].'" type="text/javascript"></script>';	
			//$GLOBALS['TSFE']->additionalHeaderData['web_shop_framework_jQuery'] .= '<script type="text/javascript">jQuery(document).ready(function(){alert(1);});</script>';
			//$GLOBALS['TSFE']->additionalJavaScript['web_shop_script'] .= 'jQuery(document).ready(function(){alert(1);});';
		}
//t3lib_div::Debug($GLOBALS['TSFE']->config['config']['sys_language_uid']);
//t3lib_utility_Debug::debug($this->conf);
		$content='';
		$arg['session'] = $GLOBALS["TSFE"]->fe_user->getKey('ses','web_shop');
		$GLOBALS['TSFE']->config['config']['forceTypeValue']=$this->conf['ajax.']['typeNum'];
		$basketLink=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array(),1,1);
		$GLOBALS['TSFE']->config['config']['forceTypeValue']=0;
		$GLOBALS['TSFE']->additionalHeaderData['web_shop_script'].= '<script type="text/javascript">';
		//$GLOBALS['TSFE']->additionalHeaderData['web_shop_script'].= "jQuery(document).ready(function(){jQuery(\"A[name='addToBasket']\").click(function(){jQuery('#basketCompactView').load('".$basketLink."&tx_easyshop_pi1[addToBasket]='+jQuery(this).attr('rel'));});})";

		//t3lib_utility_Debug::debug($this->conf['display']);
		foreach(explode(',',$this->conf['display']) as $display){
			switch($display){
				case 1:
					if(!$this->piVars['prod']){
						$arg['records_count']=true;
						$content.=$this->displayCatMenu($arg);
					}
							
				break;
				case 2:
					/*$GLOBALS['TSFE']->additionalHeaderData['web_shop_script'].= "jQuery(document).ready(function(){jQuery(\"A[name='addToBasket']\").click(function(){
							jQuery('#basketCompactView').load('".$basketLink."&tx_easyshop_pi1[addToBasket]='+jQuery(this).attr('rel'));
							var pStock = parseInt(jQuery('#productStock').html())-1;
							if(pStock>0){
								jQuery('#productStock').html(pStock);	
							}else{
								jQuery('#productStock').html(pStock);
								//jQuery(this).css('display','none');
							}
						});
					})";*/
					//pero striku :)
					if(!$this->piVars['prod']){

						$GLOBALS['TSFE']->additionalHeaderData['web_shop_script'].= "jQuery(document).ready(function(){
							$('input[id^=\"category-\"], input[id^=\"size-\"], input[id^=\"age-\"], input[id^=\"gender-\"]').change(function(){
								getAjaxList();
							});
						})

						function getAjaxList() {
							var cats = [],
								props = [];

							$('input[id^=\"category-\"]').each(function(){
								if($(this).is(':checked')) {
									var cName = $(this).attr('id').split('-');
									cats.push(cName[1]);
								}
							});

							$('input[id^=\"size-\"], input[id^=\"age-\"], input[id^=\"gender-\"]').each(function(){
								if($(this).is(':checked')) {
									var pName = $(this).attr('id').split('-');
									props.push(pName[1]);
								}
							});

							var catsStr = cats.join(',');
							var	propsStr = props.join(',');

							jQuery('#listElements').html('');
							jQuery('#listElements').load('".$basketLink."&tx_easyshop_pi1[getProductList]=1&tx_easyshop_pi1[cats]='+cats+'&tx_easyshop_pi1[props]='+props);
						}

						";


						$content.=$this->displayProductsList($arg);
					}else{
						$GLOBALS['TSFE']->additionalHeaderData['web_shop_script'].= "jQuery(document).ready(function(){
							jQuery(\"#addToBasket\").click(function(){
								var prop1 = parseInt($(\"#prop1\").val());
								var prop2 = parseInt($(\"#prop2\").val());
								var prop1mono = parseInt($(\"#prop1mono\").val());
								var prop2mono = parseInt($(\"#prop2mono\").val());
								var selprop1;
								var selprop2;									

								if(prop1) {
									selprop1 = prop1;
								} else if(prop1mono){
									selprop1 = prop1mono;
								} else {
									selprop1 = 0;
								}
								if(prop2) {
									selprop2 = prop2;
								} else if(prop2mono){
									selprop2 = prop1mono;
								} else {
									selprop2 = 0;
								}
								if($('#singleForm').validationEngine('validate')) {
									jQuery('#topBasket').load('".$basketLink."&tx_easyshop_pi1[addToBasket]='+jQuery(this).attr('rel')+'&tx_easyshop_pi1[prop1]='+selprop1+'&tx_easyshop_pi1[prop2]='+selprop2, function() {
									  //jQuery('#basketDropID').show().delay(5000).fadeOut();
									  //jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
									});
								}
							
							
						});
						
						jQuery(\"A[name='addSampleToBasket']\").click(function(){
							if($('#singleForm').validationEngine('validate')) {
								jQuery('#topBasket').load('".$basketLink."&tx_easyshop_pi1[addToBasket]='+jQuery(this).attr('rel')+'&tx_easyshop_pi1[sample]=1', function() {
								  jQuery('#basketDropID').show().delay(5000).fadeOut();
								  jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
								});
							}
						});
						
						$('#prop2').change(function() {
							jQuery('#prizeWrap').load('".$basketLink."&tx_easyshop_pi1[changePrize]='+jQuery('#prod_uid').val()+'&tx_easyshop_pi1[prop2]='+jQuery(this).val(), function() {
								
							});
						});

					})";

						$content.=$this->displayProductSingle($arg);
					}
					
				break;
				case 3:
					$GLOBALS['TSFE']->additionalHeaderData['web_shop_script'].= "jQuery(document).ready(function(){jQuery(\"A[name='addToBasket']\").click(function(){
							var prop1 = parseInt($(\"#prop1\").val());
							var prop2 = parseInt($(\"#prop2\").val());
							var prop1mono = parseInt($(\"#prop1mono\").val());
							var prop2mono = parseInt($(\"#prop2mono\").val());
							var selprop1;
							var selprop2;
													
							if(prop1) {
								selprop1 = prop1;
							} else if(prop1mono){
								selprop1 = prop1mono;
							} else {
								selprop1 = 0;
							}
							if(prop2) {
								selprop2 = prop2;
							} else if(prop2mono){
								selprop2 = prop1mono;
							} else {
								selprop2 = 0;
							}
							//alert('selprop1:'+selprop1);
							if($('#singleForm').validationEngine('validate')) {
								jQuery('#topBasket').load('".$basketLink."&tx_easyshop_pi1[addToBasket]='+jQuery(this).attr('rel')+'&tx_easyshop_pi1[prop1]='+selprop1+'&tx_easyshop_pi1[prop2]='+selprop2, function() {
								  jQuery('#basketDropID').show().delay(5000).fadeOut();
								  jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
								});
							}
							
							
						});
						
						jQuery(\"A[name='addSampleToBasket']\").click(function(){
							if($('#singleForm').validationEngine('validate')) {
								jQuery('#topBasket').load('".$basketLink."&tx_easyshop_pi1[addToBasket]='+jQuery(this).attr('rel')+'&tx_easyshop_pi1[sample]=1', function() {
								  jQuery('#basketDropID').show().delay(5000).fadeOut();
								  jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
								});
							}
						});
						
						$('#prop2').change(function() {
							jQuery('#prizeWrap').load('".$basketLink."&tx_easyshop_pi1[changePrize]='+jQuery('#prod_uid').val()+'&tx_easyshop_pi1[prop2]='+jQuery(this).val(), function() {
								
							});
						});

					})";
//t3lib_div::Debug($GLOBALS['TSFE']->additionalHeaderData['web_shop_script']);
//exit();
					$content.=$this->displayProductSingle($arg);
				break;
				case 5:
					$content.=$this->displayTopMenu($arg);
				break;
				case 4:
					$content.=$this->displayProductsListAction($arg);
				break;
				case 6:
					$content.=$this->displayProductsListSelected($arg);
				break;
				case 7:
					$content.=$this->displayProductsListMostSold($arg);
				break;
				case 8:
					$content.=$this->displayProductTitle($arg);
				break;
				case 9:
					if(!$this->piVars['prod']){
						$content.=$this->displayCatTitle();;
					}
					
				break;
				case 10:
					$content.=$this->displayCatBread($arg);
				break;
				case 11:
					if(!$this->piVars['prod']){
						$arg['records_count']=true;
						$content.=$this->displayProp3menu($arg);
					}
				break;
				case 12:
					if(!$this->piVars['prod']){
						$arg['records_count']=true;
						$content.=$this->displayProp4menu($arg);
					}
				break;
			}	
		}
		$GLOBALS['TSFE']->additionalHeaderData['web_shop_script'].= '</script>';
		return $this->pi_wrapInBaseClass($content);
		//return $this->pi_wrapInBaseClass($content);
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
	  if(!$this->conf['templateFile']){
			$this->conf['templateFile'] = 'typo3conf/ext/tend_shop/templates/shop_catalog.html';
	  }
	  if($this->conf['usergroupTemplateFile']){
	  		$this->conf['templateFile'] = $this->conf['usergroupTemplateFile'];
	  }
	  $this->pi_loadLL();
	  $this->initLanguages();
	  $this->initFeUserGroups();
	  $this->getAllUsersBasketSession();
	}
	function initLanguages(){
		$queryParts = array();
		$queryParts['SELECT'] = 'uid';
		$queryParts['FROM'] = 'sys_language';
		$queryParts['WHERE'] = 'hidden=0 ';
		$queryParts['ORDERBY'] = 'uid ASC';
//t3lib_div::Debug($queryParts);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		if ($res) {
			$i=0;
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$this->languages[$row['uid']] = $i++;
			}
		}	
	}
	function initFeUserGroups(){
		if($GLOBALS['TSFE']->loginUser){			
			$this->feUserGroup = $GLOBALS['TSFE']->fe_user->user['usergroup'];
		}		
	}
	
	function displayCatBread($arg) {
		//t3lib_utility_Debug::debug($this->conf);
		$templateBreadWrap = $this->loadTemplate('###PRODUCTS_LIST_BREAD_TEMPLATE###');
		$template_path = $this->cObj->getSubpart($templateBreadWrap, '###PRODUCT_BREAD_CAT###');
		$pathTrack='';
		$this->allCategoriesSorted=$this->getAllCategories(array());
		foreach($this->allCategoriesSorted as $category){
			if($this->piVars['cat']==$category['uid']){
				$pathTrack=$this->generatePathTrack($category,$this->allCategoriesSorted,$template_path);
				break;
			}	
		}
		$multiMarkBread['###PRODUCT_BREAD_CAT###'] = $pathTrack;
		return $this->cObj->substituteMarkerArrayCached($templateBreadWrap,array(),$multiMarkBread,array());
	}
	
	function displayProp3menu($arg){
		$this->allCategoriesSorted=$this->getAllCategories(array());
		$teaChilds = array();
		$teaCats = explode(',',$this->conf['tea_categories']);
		foreach($teaCats as $t) {
			//echo $this->categoryChildsList(122)."<br>";
			$ch = explode(',',$this->categoryChildsList($t));
			foreach($ch as $c) {
				$teaChilds[] = $c;
			}
		}		
		//t3lib_utility_Debug::debug($teaChilds);

		if(!in_array($this->piVars['cat'], $teaCats)) {
			foreach($this->allCategoriesSorted as $category){
				if($this->piVars['cat']==$category['uid']){
					$selCat=$category;
					break;
				}	
			}
			$this->piVars['cat'] = $selCat['parrent'];
			//echo "parr";
		}
		
		
		$prodUids = array();
		$params=array();		
		$params['cat']=$this->categoryChildsList($this->piVars['cat']);
		//t3lib_utility_Debug::debug($this->categoryChildsList(122));
		
		$params['order_by']=$this->piVars['orderBy'];
		$params['next_prev']=true;
		$products = $this->getProducts($params);
		foreach($products as $p) {
			$prodUids[] = $p['uid'];
		}
		$uidsStr = implode(',', $prodUids);
		//echo $uidsStr;
		$prodProp3 = $this->getProductsProperties3($uidsStr);
				
		//t3lib_utility_Debug::debug($prodProp3);
		if(in_array($this->piVars['cat'], $teaChilds) || $this->piVars['prop3']) {
				
			$template = $this->loadTemplate('###PROPERTY3_MENU_TEMPLATE###');
			$lvl1Item = $this->cObj->getSubpart($template, '###CAT_MENU_LVL1###');
			//$prop3 = $this->getProperties3();
			//t3lib_utility_Debug::debug($this->conf['tea_categories']);
			//t3lib_utility_Debug::debug($this->piVars['cat']);		
			
			foreach($prodProp3 as $p) {
				$singleMarkCat1['###ACTIVE###'] = ($this->piVars['prop3']==$p['uid'])?'active':'';
				$singleMarkCat1['###LVL1_LINK###'] = t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$this->piVars['cat'], 'prop3'=>$p['uid']), 1, 1,$this->conf['page_products_list']);
				$singleMarkCat1['###LVL1_TITLE###'] = $p['display_title'];
				$singleMarkCat1['###CAT_MENU_LVL1_EMPTY###'] = '';
				$cat_menu_items .= $this->cObj->substituteMarkerArrayCached($lvl1Item,$singleMarkCat1,array(),array());
			}
			$multiMark['###CAT_MENU_LVL1###'] = $cat_menu_items;		
			return $this->cObj->substituteMarkerArrayCached($template,array(),$multiMark,array());
		} return '';
	}
	
	function displayProp4menu($arg){
		
		$template = $this->loadTemplate('###PROPERTY4_MENU_TEMPLATE###');
		$lvl1Item = $this->cObj->getSubpart($template, '###CAT_MENU_LVL1###');
		$prop4 = $this->getProperties4();
		foreach($prop4 as $p) {
			$singleMarkCat1['###ACTIVE###'] = ($this->piVars['prop4']==$p['uid'])?'active':'';
			$singleMarkCat1['###LVL1_LINK###'] = t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('prop4'=>$p['uid']), 1, 1,$this->conf['page_products_list']);
			$singleMarkCat1['###LVL1_TITLE###'] = $p['display_title'];
			$singleMarkCat1['###CAT_MENU_LVL1_EMPTY###'] = '';
			$cat_menu_items .= $this->cObj->substituteMarkerArrayCached($lvl1Item,$singleMarkCat1,array(),array());
		}
		$multiMark['###CAT_MENU_LVL1###'] = $cat_menu_items;		
		return $this->cObj->substituteMarkerArrayCached($template,array(),$multiMark,array());
	}
	
	function displayCatMenu($arg){
		$categories = $this->getAllCategories($arg);
		/*
		$categories_tree = array();
		foreach($categories as $key=>$category){
			if($category['parrent']==0){
				$category['childs'] = $this->categoryChilds($category['uid'],$categories);
				$categories_tree[] = $category;
			}
		}*/
		
		foreach($categories as $category){
				if($this->piVars['cat']==$category['uid']){
					$selCat=$category;
					break;
				}	
			}
		
		//$parent = $this->categoryTopParent($selCat, $categories);
		//$categoryChilds = $this->categoryChilds($parent['uid'],$categories);
		$categoryChilds = $this->categoryChilds($selCat['uid'],$categories);
//t3lib_utility_Debug::debug(count($categoryChilds));

		if(!count($categoryChilds)) $categoryChilds = $this->categoryChilds($selCat['parrent'],$categories);
		
		$template = $this->loadTemplate('###CAT_MENU_TEMPLATE###');
		$lvl1Item = $this->cObj->getSubpart($template, '###CAT_MENU_LVL1###');
		$lvl2Item = $this->cObj->getSubpart($lvl1Item, '###CAT_MENU_LVL2###');
		
		$cat_menu_items='';
		foreach($categoryChilds as $key=>$category){
			$singleMarkCat1['###LVL1_LINK###'] = t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$category['uid']), 1, 1,$this->conf['page_products_list']);
			$singleMarkCat1['###LVL1_TITLE###'] =  $category['display_title'];
			
			//if($this->piVars['cat']==$category['uid']){
			$singleMarkCat1['###ACTIVE###'] = '';
			if($this->piVars['cat']==$category['uid']) {
				$singleMarkCat1['###ACTIVE###'] = 'active';
			} else {
				$singleMarkCat1['###ACTIVE###'] = '';
			}
			
			/*
			if($category['childs']){
				$lvl2Cat = $category['childs'];
				$singleMarkCat2 = array();
				$multiMarkCat2 = array();
				$cat2_menu_items = '';
				foreach($lvl2Cat as $cat2){
					$singleMarkCat2['###LVL2_LINK###'] = t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$cat2['uid']), 1, 1,$this->conf['page_products_list']);
					$singleMarkCat2['###LVL2_TITLE###'] =  $cat2['display_title'];
					//$multiMarkCat2['###TOP_MENU_LVL3_ITEMS###'] = '';
					if($this->piVars['cat']==$cat2['uid']) {
						$singleMarkCat2['###ACTIVE###'] = 'active';
					} else {
						$singleMarkCat2['###ACTIVE###'] = '';
					}
					$cat2_menu_items .= $this->cObj->substituteMarkerArrayCached($lvl2Item,$singleMarkCat2,$multiMarkCat2,array());	
				}
				//$multiMarkCat2
				
				$multiMarkCat1['###CAT_MENU_LVL2###'] = $cat2_menu_items;
				$singleMarkCat1['###CAT_MENU_LVL1_EMPTY###'] = '';
				//$multiMarkCat1['###TOP_MENU_LVL2_ITEMS###'] = $this->cObj->substituteMarkerArrayCached($lvl2Item,array(),$multiMarkCat1_2,array());
			} else {
			
				$multiMarkCat1['###CAT_MENU_LVL2###'] = '';
				$singleMarkCat1['###CAT_MENU_LVL1_EMPTY###'] = 'noChild';
			}*/
			$cat_menu_items .= $this->cObj->substituteMarkerArrayCached($lvl1Item,$singleMarkCat1,$multiMarkCat1,array());
			//t3lib_utility_Debug::debug($lvl1Item);
		}
		//t3lib_utility_Debug::debug($selCat);
		$singleMark['###PARRENT_TITLE###'] =  $selCat['subtitle2'];
		$multiMark['###CAT_MENU_LVL1###'] = $cat_menu_items;
		
		return $this->cObj->substituteMarkerArrayCached($template,$singleMark,$multiMark,array());

		
	}
	function displayCatMenuChilds($arg,&$pCat){
		//array('childs'=>$category['childs'],'templates'=>array($template,$cat_item,$cat_prod_count)));
		$cat_menu_items = '';
		foreach($arg['childs'] as $key=>$category){
			$singleMark = array();
			$singleMark['###PRODUCTS_LIST_LINK###'] = t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$category['uid']), 1, 1,$this->conf['page_products_list']);
			$singleMark['###CATEGORY_CLASS###'] = 'cat_child';
			$singleMark['###CATEGORY###'] = $category['display_title'];
			$multiMark['###PRODUCTS_COUNT###'] = '';
			if($category['childs']){
				$singleMark['###CATEGORY_CLASS###'] = 'cat_parrent';
				$category['sum_records_count'] = $category['records_count'];
				$this->cat_level++;
				$singleMark['###CATEGORY_CHILDS###'] = $this->displayCatMenuChilds(array('childs'=>$category['childs'],'templates'=>$arg['templates']),$category);
				$this->cat_level--;	
			}else{
				$category['sum_records_count'] = $category['records_count'];
				$singleMark['###CATEGORY_CHILDS###'] = '';
			}
//t3lib_div::Debug($category);
			if($category['sum_records_count']>0){
				$multiMark['###PRODUCTS_COUNT###'] = $this->cObj->substituteMarkerArrayCached($arg['templates']['item_count'],array('###PRODUCTS_NUM###'=>$category['sum_records_count']),array(),array());
				$pCat['sum_records_count'] += $category['sum_records_count'];
				$cat_menu_items .= $this->cObj->substituteMarkerArrayCached($arg['templates']['item'],$singleMark,$multiMark,array());
			}	
		}
		if($cat_menu_items){
			return $this->cObj->substituteMarkerArrayCached($arg['templates']['template'],array('###CATEGORY_LEVEL_CLASS###'=>$this->cat_level),array('###CAT_MENU_ITEM###'=> $cat_menu_items),array());	
		}else{
			return '';
		}
//t3lib_div::Debug($cat_menu_items);
	}

	function displayDinFilter() {
		$template = $this->loadTemplate('###PRODUCTS_LIST_FILTER###');
		$templateCats = $this->cObj->getSubpart($template, '###FILTER_CAT_SINGLE###');
		$templateSizes = $this->cObj->getSubpart($template, '###FILTER_SIZE_SINGLE###');
		$templateAges = $this->cObj->getSubpart($template, '###FILTER_AGE_SINGLE###');
		$templateGenders = $this->cObj->getSubpart($template, '###FILTER_GENDER_SINGLE###');

		$categories = $this->getAllCategories($arg);
		foreach($categories as $category){
			if($this->piVars['cat']==$category['uid']){
				$selCat=$category;
				break;
			}	
		}
		$categoryChilds = $this->categoryChilds($selCat['uid'],$categories);

		$catMultie = '';
		foreach($categoryChilds as $catChild) {
			$singleMarkCat['###CAT_ID###'] = $catChild['uid'];
			$singleMarkCat['###CAT_TITLE###'] = $catChild['display_title'];
			$catMultie .= $this->cObj->substituteMarkerArrayCached($templateCats,$singleMarkCat,array(),array());
		}
		$multiMark['###FILTER_CAT_SINGLE###'] = $catMultie;
		$params['cat']=$this->categoryChildsList($this->piVars['cat']);
		$params['order_by']=$this->piVars['orderBy'];
		$params['next_prev']=true;
		$products = $this->getProducts($params);

		$sizes = array();
		$genders = array();
		$ages = array();

		foreach($products as $prod) {
			$properties = $this->getProductProperties(array('prod_uid'=>$prod['uid']));		
			foreach($properties as $prop) {
				if($prop['parrent'] == 2 && !$this->checkIfPropsInArray($ages, $prop['uid'])) {
					$ages[] = $prop;
				} else if($prop['parrent'] == 3 && !$this->checkIfPropsInArray($genders, $prop['uid'])) {
					$genders[] = $prop;
				} else if($prop['parrent'] == 4 && !$this->checkIfPropsInArray($sizes, $prop['uid'])) {
					$sizes[] = $prop;
				}
			}
		}

		if(count($sizes)) {
			$sizeMultie = '';
			foreach($sizes as $size) {
				$singleMarkSize['###SIZE_ID###'] = $size['uid'];
				$singleMarkSize['###SIZE_TITLE###'] = ($size['display_title']) ? $size['display_title'] : $size['title'];
				$sizeMultie .= $this->cObj->substituteMarkerArrayCached($templateSizes,$singleMarkSize,array(),array());
			}
			$multiMark['###FILTER_SIZE_SINGLE###'] = $sizeMultie;
		} else {
			$multiMark['###SHOW_FILTER_SIZE###'] = '';
		}

		if(count($genders)) {
			$genderMultie = '';
			foreach($genders as $gender) {
				$singleMarkGender['###GENDER_ID###'] = $gender['uid'];
				$singleMarkGender['###GENDER_TITLE###'] = ($gender['display_title']) ? $gender['display_title'] : $gender['title'];
				$genderMultie .= $this->cObj->substituteMarkerArrayCached($templateGenders,$singleMarkGender,array(),array());
			}
			$multiMark['###FILTER_GENDER_SINGLE###'] = $genderMultie;
		} else {
			$multiMark['###SHOW_FILTER_GENDER###'] = '';
		}

		if(count($ages)) {
			$ageMultie = '';
			foreach($ages as $age) {
				$singleMarkAge['###AGE_ID###'] = $age['uid'];
				$singleMarkAge['###AGE_TITLE###'] = ($age['display_title']) ? $age['display_title'] : $age['title'];
				$ageMultie .= $this->cObj->substituteMarkerArrayCached($templateAges,$singleMarkAge,array(),array());
			}
			$multiMark['###FILTER_AGE_SINGLE###'] = $ageMultie;
		} else {
			$multiMark['###SHOW_FILTER_AGE###'] = '';
		}


/*
		foreach($properties as $key=>$p){
			if($p['parrent']==0){
				$p['childs'] = $this->propertyChilds($p,$properties);
				$properties_tree[] = $p;
			}
		}
*/

		//t3lib_utility_Debug::debug($products);
		//$params['prop3']=$this->piVars['prop3'];

		return $this->cObj->substituteMarkerArrayCached($template,$singleMark,$multiMark,array());
	}

	function checkIfPropsInArray($list, $uid) {
		$found = false;
		foreach($list as $l) {
			if($l['uid'] == $uid) {
				$found = true;
			}
		}
		return $found;
	}
	
	function displayProductsListAction($arg) {
		$params=array();
		$returnContent='';
		
		$params['order_by']=$this->conf['productsFrontSortOrder'];
		$params['asc_desc']=$this->conf['productsFrontSortOrderAscDesc'];
		
		if($this->conf['special_limit']) {
			$params['limit']=$this->conf['special_limit'];
		}
		$params['special']='action';
		$template = $this->loadTemplate('###PRODUCTS_SPECIAL_LIST_TEMPLATE###');
		$templateActionUser = $this->loadTemplate('###AKCIJSKA_NAVADNA###');
		$templateNormalUser = $this->loadTemplate('###AKCIJSKA_NONE###');
		$templateMonthTea = $this->loadTemplate('###MESEC_CAJA_NONE###');
		$templateTypeTea = $this->loadTemplate('###VRSTA_CAJA_NONE###');
		$templateBioTea = $this->loadTemplate('###BIOSTICKER_NONE###');
		if(!$template){return $this->pi_getLL('no_template_error');}
		$singleMark=array();
		$products = $this->getProducts($params);
		//t3lib_utility_Debug::debug($products);
		$returnContent='';
		$counter=0;
		foreach($products as $product){
			if($GLOBALS['TSFE']->loginUser && $this->feUserGroup==2) {
				if($product['price_partner'] || $product['web_price_partner']) {
					$product['price'] = $product['price_partner'];
					$product['web_price'] = $product['web_price_partner'];
				}
			}
			$productCategories = $this->getProductCategories(array('prod_uid'=>$product['uid']));
			$singleMarkProduct['###CAT_UID###']=$singleMarkType['###CAT_UID###']=$singleMarkMonth['###CAT_UID###']=$productCategories[0]['uid'];
			
			$singleMarkProduct['###PRODUCT###']=$product['title'];
			$singleMarkProduct['###PRODUCT_SINGLE_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$productCategories[0]['uid'],'prod'=>$product['uid']), 1, 1,$this->conf['page_single_product']);
			$singleMarkProduct['###PRODUCT_SUBTITLE###']=$product['subtitle'];
			$origImagesArray=explode(',',$product['images']);		
			$leadImage = $origImagesArray[0];
			$imgConfig['file.']['maxH'] = '263px';
			$imgConfig['file.']['height'] = '280px';
			$imgConfig['file.']['XY'] = '[10.w],[10.h]';
			$singleMark['###IMAGE_ORIGINAL###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').'uploads/tx_easyshop/'.$leadImage;
			$imgConfig['file'] = 'uploads/tx_easyshop/'.$leadImage;
			$origImg = $this->cObj->IMAGE($imgConfig);
			$resizedImageInfo = $GLOBALS['TSFE']->lastImageInfo;
			$singleMarkProduct['###IMAGE###']=$resizedImageInfo[3];
			
			$singleMarkProduct['###PRODUCT_PRICE###']=$this->moneyFormat($product['price']);
			if($product['web_price']) {
				//$multieMarkProduct['###AKCIJSKA###']='';
				$singleMarkProduct['###PRODUCT_ACTION_DISCOUNT###']=100 - intval(100*(floatval(str_replace(',','.',$product['web_price'])))/floatval(str_replace(',','.',$product['price'])));
				$singleMarkProduct['###PRODUCT_ACTION_PRICE###']=$this->moneyFormat($product['web_price']);
				$multieMarkProduct['###AKCIJSKA_NAVADNA###']=$this->cObj->substituteMarkerArrayCached($templateActionUser,$singleMarkProduct,array(),array());
				$multieMarkProduct['###AKCIJSKA_NONE###']='';
			} else {
				$multieMarkProduct['###AKCIJSKA_NONE###']=$this->cObj->substituteMarkerArrayCached($templateNormalUser,$singleMarkProduct,array(),array());
				$multieMarkProduct['###AKCIJSKA_NAVADNA###']='';
			}
			
			// CE IMAMO CENO VEZANO NA KOLIČINE!
			if($product['price_prop2']) {
				$priceArray = explode('|',$product['price_prop2']);
				$singleMarkProduct['###PRODUCT_PRICE###']=$this->moneyFormat(trim($priceArray[0]));
				if($product['price_disc_prop2']) {
					$priceDiscArray = explode('|',$product['price_disc_prop2']);
					$singleMarkProduct['###PRODUCT_ACTION_PRICE###']=$this->moneyFormat(trim($priceDiscArray[0]));
					$singleMarkProduct['###PRODUCT_ACTION_DISCOUNT###']=100 - intval(100*(floatval(str_replace(',','.',trim($priceDiscArray[0]))))/floatval(str_replace(',','.',trim($priceArray[0]))));
					$multieMarkProduct['###AKCIJSKA_NAVADNA###']=$this->cObj->substituteMarkerArrayCached($templateActionUser,$singleMarkProduct,array(),array());
					$multieMarkProduct['###AKCIJSKA_NONE###']='';
				} else {
					$multieMarkProduct['###AKCIJSKA_NONE###']=$this->cObj->substituteMarkerArrayCached($templateNormalUser,$singleMarkProduct,array(),array());
					$multieMarkProduct['###AKCIJSKA_NAVADNA###']='';
				}				
			}		
						
			if($product['month_tea']) {
				$singleMarkMonth['###MESEC_CAJA###']=$product['month_tea'];
				$multieMarkProduct['###MESEC_CAJA_NONE###'] = $this->cObj->substituteMarkerArrayCached($templateMonthTea,$singleMarkMonth,array(),array());
			} else {
				$multieMarkProduct['###MESEC_CAJA_NONE###']='';
			}
			
			if($product['type_tea']) {
				$singleMarkType['###VRSTA_CAJA###']=$product['type_tea'];
				$multieMarkProduct['###VRSTA_CAJA_NONE###'] = $this->cObj->substituteMarkerArrayCached($templateTypeTea,$singleMarkType,array(),array());
			} else {
				$multieMarkProduct['###VRSTA_CAJA_NONE###']='';
			}
			
			if($product['bio_tea']) {
				$multieMarkProduct['###BIOSTICKER_NONE###'] = $templateBioTea;
			} else {
				$multieMarkProduct['###BIOSTICKER_NONE###']='';
			}
			
			//t3lib_utility_Debug::debug($singleMarkProduct);
			$returnContent .= $this->cObj->substituteMarkerArrayCached($template,$singleMarkProduct,$multieMarkProduct,array());
			
			$counter++;
			if($counter == intval($this->conf['special_limit'])) {
				return $returnContent;
			}
		}
		
		return $returnContent;
	}
	
	function displayProductsListSelected($arg) {
		$params=array();
		
		$params['order_by']=$this->conf['productsFrontSortOrder'];
		$params['asc_desc']=$this->conf['productsFrontSortOrderAscDesc'];
		/*
		if($this->conf['special_limit']) {
			$params['limit']=$this->conf['special_limit'];
		}
		*/
		
		$params['special']='selected';
		$template = $this->loadTemplate('###PRODUCTS_SPECIAL_LIST_TEMPLATE###');
		$templateActionUser = $this->loadTemplate('###AKCIJSKA_NAVADNA###');
		$templateNormalUser = $this->loadTemplate('###AKCIJSKA_NONE###');
		$templateMonthTea = $this->loadTemplate('###MESEC_CAJA_NONE###');
		$templateTypeTea = $this->loadTemplate('###VRSTA_CAJA_NONE###');
		$templateBioTea = $this->loadTemplate('###BIOSTICKER_NONE###');
if(!$template){return $this->pi_getLL('no_template_error');}
		$singleMark=array();
		$products = $this->getProducts($params);
		$returnContent='';
		$counter=0;
		foreach($products as $product){
			if($GLOBALS['TSFE']->loginUser && $this->feUserGroup==2) {
				if($product['price_partner'] || $product['web_price_partner']) {
					$product['price'] = $product['price_partner'];
					$product['web_price'] = $product['web_price_partner'];
				}
			}
			$productCategories = $this->getProductCategories(array('prod_uid'=>$product['uid']));
			$singleMarkProduct['###CAT_UID###']=$singleMarkType['###CAT_UID###']=$singleMarkMonth['###CAT_UID###']=$productCategories[0]['uid'];
			//t3lib_utility_Debug::debug($productCategories);
			$singleMarkProduct['###PRODUCT###']=$product['title'];
			$singleMarkProduct['###PRODUCT_SINGLE_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$productCategories[0]['uid'],'prod'=>$product['uid']), 1, 1,$this->conf['page_single_product']);
			$singleMarkProduct['###PRODUCT_SUBTITLE###']=$product['subtitle'];
			$origImagesArray=explode(',',$product['images']);		
			$leadImage = $origImagesArray[0];
			$imgConfig['file.']['maxH'] = '263px';
			$imgConfig['file.']['height'] = '280px';
			$imgConfig['file.']['XY'] = '[10.w],[10.h]';
			$singleMark['###IMAGE_ORIGINAL###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').'uploads/tx_easyshop/'.$leadImage;
			$imgConfig['file'] = 'uploads/tx_easyshop/'.$leadImage;
			$origImg = $this->cObj->IMAGE($imgConfig);
			$resizedImageInfo = $GLOBALS['TSFE']->lastImageInfo;
			$singleMarkProduct['###IMAGE###']=$resizedImageInfo[3];
			
			$singleMarkProduct['###PRODUCT_PRICE###']=$this->moneyFormat($product['price']);
			if($product['web_price']) {
				$singleMarkProduct['###PRODUCT_ACTION_DISCOUNT###']=100 - intval(100*(floatval(str_replace(',','.',$product['web_price'])))/floatval(str_replace(',','.',$product['price'])));
				$singleMarkProduct['###PRODUCT_ACTION_PRICE###']=$this->moneyFormat($product['web_price']);	
				$multieMarkProduct['###AKCIJSKA_NAVADNA###']=$this->cObj->substituteMarkerArrayCached($templateActionUser,$singleMarkProduct,array(),array());
				$multieMarkProduct['###AKCIJSKA_NONE###']='';
			} else {
				$multieMarkProduct['###AKCIJSKA_NONE###']=$this->cObj->substituteMarkerArrayCached($templateNormalUser,$singleMarkProduct,array(),array());
				$multieMarkProduct['###AKCIJSKA_NAVADNA###']='';
			}
			
			// CE IMAMO CENO VEZANO NA KOLIČINE!
			if($product['price_prop2']) {
				$priceArray = explode('|',$product['price_prop2']);
				$singleMarkProduct['###PRODUCT_PRICE###']=$this->moneyFormat(trim($priceArray[0]));
				if($product['price_disc_prop2']) {
					$priceDiscArray = explode('|',$product['price_disc_prop2']);
					$singleMarkProduct['###PRODUCT_ACTION_PRICE###']=$this->moneyFormat(trim($priceDiscArray[0]));
					$singleMarkProduct['###PRODUCT_ACTION_DISCOUNT###']=100 - intval(100*(floatval(str_replace(',','.',trim($priceDiscArray[0]))))/floatval(str_replace(',','.',trim($priceArray[0]))));
					$multieMarkProduct['###AKCIJSKA_NAVADNA###']=$this->cObj->substituteMarkerArrayCached($templateActionUser,$singleMarkProduct,array(),array());
					$multieMarkProduct['###AKCIJSKA_NONE###']='';
				} else {
					$multieMarkProduct['###AKCIJSKA_NONE###']=$this->cObj->substituteMarkerArrayCached($templateNormalUser,$singleMarkProduct,array(),array());
					$multieMarkProduct['###AKCIJSKA_NAVADNA###']='';
				}				
			}
			
			if($product['month_tea']) {
				$singleMarkMonth['###MESEC_CAJA###']=$product['month_tea'];
				$multieMarkProduct['###MESEC_CAJA_NONE###'] = $this->cObj->substituteMarkerArrayCached($templateMonthTea,$singleMarkMonth,array(),array());
			} else {
				$multieMarkProduct['###MESEC_CAJA_NONE###']='';
			}
			
			if($product['type_tea']) {
				$singleMarkType['###VRSTA_CAJA###']=$product['type_tea'];
				$multieMarkProduct['###VRSTA_CAJA_NONE###'] = $this->cObj->substituteMarkerArrayCached($templateTypeTea,$singleMarkType,array(),array());
			} else {
				$multieMarkProduct['###VRSTA_CAJA_NONE###']='';
			}
			
			if($product['bio_tea']) {
				$multieMarkProduct['###BIOSTICKER_NONE###'] = $templateBioTea;
			} else {
				$multieMarkProduct['###BIOSTICKER_NONE###']='';
			}
			
			//t3lib_utility_Debug::debug($singleMarkProduct);
			$returnContent .= $this->cObj->substituteMarkerArrayCached($template,$singleMarkProduct,$multieMarkProduct,array());
			$counter++;
			if($counter == intval($this->conf['special_limit'])) {
				return $returnContent;
			}
		}
		
		return $returnContent;
	}
	
	function displayProductsListMostSold($arg) {
		$params=array();
	
		$params['order_by']=$this->conf['productsFrontSortOrder'];
		$params['asc_desc']=$this->conf['productsFrontSortOrderAscDesc'];
		
		$params['special']='mostsold';
		$template = $this->loadTemplate('###PRODUCTS_SPECIAL_LIST_TEMPLATE###');
		$templateActionUser = $this->loadTemplate('###AKCIJSKA_NAVADNA###');
		$templateNormalUser = $this->loadTemplate('###AKCIJSKA_NONE###');
		$templateMonthTea = $this->loadTemplate('###MESEC_CAJA_NONE###');
		$templateTypeTea = $this->loadTemplate('###VRSTA_CAJA_NONE###');
		$templateBioTea = $this->loadTemplate('###BIOSTICKER_NONE###');
if(!$template){return $this->pi_getLL('no_template_error');}
		$singleMark=array();
		$products = $this->getProducts($params);
		$returnContent='';
		$counter=0;
		foreach($products as $product){
			if($GLOBALS['TSFE']->loginUser && $this->feUserGroup==2) {
				if($product['price_partner'] || $product['web_price_partner']) {
					$product['price'] = $product['price_partner'];
					$product['web_price'] = $product['web_price_partner'];
				}
			}
			$productCategories = $this->getProductCategories(array('prod_uid'=>$product['uid']));
			$singleMarkProduct['###CAT_UID###']=$singleMarkType['###CAT_UID###']=$singleMarkMonth['###CAT_UID###']=$productCategories[0]['uid'];
			$singleMarkProduct['###PRODUCT###']=$product['title'];
			$singleMarkProduct['###PRODUCT_SINGLE_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$productCategories[0]['uid'],'prod'=>$product['uid']), 1, 1,$this->conf['page_single_product']);
			$singleMarkProduct['###PRODUCT_SUBTITLE###']=$product['subtitle'];
			$origImagesArray=explode(',',$product['images']);		
			$leadImage = $origImagesArray[0];
			$imgConfig['file.']['maxH'] = '263px';
			$imgConfig['file.']['height'] = '280px';
			$imgConfig['file.']['XY'] = '[10.w],[10.h]';
			$singleMark['###IMAGE_ORIGINAL###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').'uploads/tx_easyshop/'.$leadImage;
			$imgConfig['file'] = 'uploads/tx_easyshop/'.$leadImage;
			$origImg = $this->cObj->IMAGE($imgConfig);
			$resizedImageInfo = $GLOBALS['TSFE']->lastImageInfo;
			$singleMarkProduct['###IMAGE###']=$resizedImageInfo[3];
			
			$singleMarkProduct['###PRODUCT_PRICE###']=$this->moneyFormat($product['price']);
			if($product['web_price']) {
				$singleMarkProduct['###PRODUCT_ACTION_DISCOUNT###']=100 - intval(100*(floatval(str_replace(',','.',$product['web_price'])))/floatval(str_replace(',','.',$product['price'])));
				$singleMarkProduct['###PRODUCT_ACTION_PRICE###']=$this->moneyFormat($product['web_price']);	
				$multieMarkProduct['###AKCIJSKA_NAVADNA###']=$this->cObj->substituteMarkerArrayCached($templateActionUser,$singleMarkProduct,array(),array());
				$multieMarkProduct['###AKCIJSKA_NONE###']='';
			} else {
				$multieMarkProduct['###AKCIJSKA_NONE###']=$this->cObj->substituteMarkerArrayCached($templateNormalUser,$singleMarkProduct,array(),array());
				$multieMarkProduct['###AKCIJSKA_NAVADNA###']='';
			}
			
			// CE IMAMO CENO VEZANO NA KOLIČINE!
			if($product['price_prop2']) {
				$priceArray = explode('|',$product['price_prop2']);
				$singleMarkProduct['###PRODUCT_PRICE###']=$this->moneyFormat(trim($priceArray[0]));
				if($product['price_disc_prop2']) {
					$priceDiscArray = explode('|',$product['price_disc_prop2']);
					$singleMarkProduct['###PRODUCT_ACTION_PRICE###']=$this->moneyFormat(trim($priceDiscArray[0]));
					$singleMarkProduct['###PRODUCT_ACTION_DISCOUNT###']=100 - intval(100*(floatval(str_replace(',','.',trim($priceDiscArray[0]))))/floatval(str_replace(',','.',trim($priceArray[0]))));
					$multieMarkProduct['###AKCIJSKA_NAVADNA###']=$this->cObj->substituteMarkerArrayCached($templateActionUser,$singleMarkProduct,array(),array());
					$multieMarkProduct['###AKCIJSKA_NONE###']='';
				} else {
					$multieMarkProduct['###AKCIJSKA_NONE###']=$this->cObj->substituteMarkerArrayCached($templateNormalUser,$singleMarkProduct,array(),array());
					$multieMarkProduct['###AKCIJSKA_NAVADNA###']='';
				}				
			}
			
			if($product['month_tea']) {
				$singleMarkMonth['###MESEC_CAJA###']=$product['month_tea'];
				$multieMarkProduct['###MESEC_CAJA_NONE###'] = $this->cObj->substituteMarkerArrayCached($templateMonthTea,$singleMarkMonth,array(),array());
			} else {
				$multieMarkProduct['###MESEC_CAJA_NONE###']='';
			}
			
			if($product['type_tea']) {
				$singleMarkType['###VRSTA_CAJA###']=$product['type_tea'];
				$multieMarkProduct['###VRSTA_CAJA_NONE###'] = $this->cObj->substituteMarkerArrayCached($templateTypeTea,$singleMarkType,array(),array());
			} else {
				$multieMarkProduct['###VRSTA_CAJA_NONE###']='';
			}
			
			if($product['bio_tea']) {
				$multieMarkProduct['###BIOSTICKER_NONE###'] = $templateBioTea;
			} else {
				$multieMarkProduct['###BIOSTICKER_NONE###']='';
			}
			
			//t3lib_utility_Debug::debug($singleMarkProduct);
			$returnContent .= $this->cObj->substituteMarkerArrayCached($template,$singleMarkProduct,$multieMarkProduct,array());
			$counter++;
			if($counter == intval($this->conf['special_limit'])) {
				return $returnContent;
			}
		}
		
		return $returnContent;
	}
	
	function displayProductsList($arg){

		if(!$this->piVars['cat'] && !$this->piVars['prop3'] && !$this->piVars['prop4']){return $this->pi_getLL('no_category_error');}
		$params=array();
		$this->allCategoriesSorted=$this->getAllCategories(array());
		
		$this->allProp3 = $this->getProperties3();
		$this->allProp4 = $this->getProperties4();
		foreach($this->allCategoriesSorted as $c){
			if($c['uid']==$this->piVars['cat']){
				$currentCat=$c;
				break;				
			}
		}
		foreach($this->allProp3 as $p){
			if($p['uid']==$this->piVars['prop3']){
				$currentCat=$p;
				break;				
			}
		}
		foreach($this->allProp4 as $p){
			if($p['uid']==$this->piVars['prop4']){
				$currentCat=$p;
				break;				
			}
		}
		
		$params['cat']=$this->categoryChildsList($this->piVars['cat']);
		$params['prop3']=$this->piVars['prop3'];
		$params['prop4']=$this->piVars['prop4'];
		$params['order_by']=$this->piVars['orderBy'];
		
		//DA NI PAGEBROWSINGA
		$params['next_prev']=true;
		
		$template = $this->loadTemplate('###PRODUCTS_LIST_TEMPLATE###');
		if(!$template){return $this->pi_getLL('no_template_error');}
		
		//$template = $this->loadTemplate('###PRODUCTS_SPECIAL_LIST_TEMPLATE###');
		$templateActionUser = $this->loadTemplate('###AKCIJSKA_NAVADNA###');
		$templateNormalUser = $this->loadTemplate('###AKCIJSKA_NONE###');
		$templateMonthTea = $this->loadTemplate('###MESEC_CAJA_NONE###');
		$templateTypeTea = $this->loadTemplate('###VRSTA_CAJA_NONE###');
		$templateBioTea = $this->loadTemplate('###BIOSTICKER_NONE###');
		if(!$template){return $this->pi_getLL('no_template_error');}
		$singleMark=array();
		$singleMarkCE=array();

		
		$products = $this->getProducts($params);
		
		//t3lib_utility_Debug::debug($products);
		$returnContent='';
		$counter=0;
		foreach($products as $product){
			if($GLOBALS['TSFE']->loginUser && $this->feUserGroup==2) {
				if($product['price_partner'] || $product['web_price_partner']) {
					$product['price'] = $product['price_partner'];
					$product['web_price'] = $product['web_price_partner'];
				}
			}
			$productCategories = $this->getProductCategories(array('prod_uid'=>$product['uid']));
			$singleMarkProduct['###CAT_UID###']=$singleMarkType['###CAT_UID###']=$singleMarkMonth['###CAT_UID###']=$productCategories[0]['uid'];
			//t3lib_utility_Debug::debug($productCategories);
			//echo "<br>";
			
			$singleMarkProduct['###PRODUCT###']=$product['title'];
			
			$singleMarkProduct['###PRODUCT_SINGLE_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$productCategories[0]['uid'],'prod'=>$product['uid']), 1, 1,$this->conf['page_single_product']);
			
			$singleMarkProduct['###PRODUCT_SUBTITLE###']=$product['subtitle'];
			
			$origImagesArray=explode(',',$product['images']);
		
			$leadImage = $origImagesArray[0];
			$imgConfig['file.']['maxH'] = '263px';
			$imgConfig['file.']['height'] = '280px';
			$singleMark['###IMAGE_ORIGINAL###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').'uploads/tx_easyshop/'.$leadImage;
			$imgConfig['file'] = 'uploads/tx_easyshop/'.$leadImage;
			$origImg = $this->cObj->IMAGE($imgConfig);
			$resizedImageInfo = $GLOBALS['TSFE']->lastImageInfo;
			$singleMarkProduct['###IMAGE###']=$resizedImageInfo[3];
			
			$singleMarkProduct['###PRODUCT_PRICE###']=$this->moneyFormat($product['price']);
			if($product['web_price']) {
				$singleMarkProduct['###PRODUCT_ACTION_DISCOUNT###']=100 - intval(100*(floatval(str_replace(',','.',$product['web_price'])))/floatval(str_replace(',','.',$product['price'])));
				$singleMarkProduct['###PRODUCT_ACTION_PRICE###']=$this->moneyFormat($product['web_price']);
				$multieMarkProduct['###AKCIJSKA_NAVADNA###']=$this->cObj->substituteMarkerArrayCached($templateActionUser,$singleMarkProduct,array(),array());
				$multieMarkProduct['###AKCIJSKA_NONE###']='';
			} else {
				$multieMarkProduct['###AKCIJSKA_NONE###']=$this->cObj->substituteMarkerArrayCached($templateNormalUser,$singleMarkProduct,array(),array());
				$multieMarkProduct['###AKCIJSKA_NAVADNA###']='';
			}

			if($product['month_tea']) {
				$singleMarkMonth['###MESEC_CAJA###']=$product['month_tea'];
				$multieMarkProduct['###MESEC_CAJA_NONE###'] = $this->cObj->substituteMarkerArrayCached($templateMonthTea,$singleMarkMonth,array(),array());
			} else {
				$multieMarkProduct['###MESEC_CAJA_NONE###']='';
			}
			
			if($product['type_tea']) {
				$singleMarkType['###VRSTA_CAJA###']=$product['type_tea'];
				$multieMarkProduct['###VRSTA_CAJA_NONE###'] = $this->cObj->substituteMarkerArrayCached($templateTypeTea,$singleMarkType,array(),array());
			} else {
				$multieMarkProduct['###VRSTA_CAJA_NONE###']='';
			}
			
			if($product['bio_tea']) {
				$multieMarkProduct['###BIOSTICKER_NONE###'] = $templateBioTea;
			} else {
				$multieMarkProduct['###BIOSTICKER_NONE###']='';
			}
			
			
			// CE IMAMO CENO VEZANO NA KOLIČINE!
			if($product['price_prop2']) {
				$priceArray = explode('|',$product['price_prop2']);
				$singleMarkProduct['###PRODUCT_PRICE###']=$this->moneyFormat(trim($priceArray[0]));
				if($product['price_disc_prop2']) {
					$priceDiscArray = explode('|',$product['price_disc_prop2']);
					$singleMarkProduct['###PRODUCT_ACTION_PRICE###']=$this->moneyFormat(trim($priceDiscArray[0]));
					$singleMarkProduct['###PRODUCT_ACTION_DISCOUNT###']=100 - intval(100*(floatval(str_replace(',','.',trim($priceDiscArray[0]))))/floatval(str_replace(',','.',trim($priceArray[0]))));
					$multieMarkProduct['###AKCIJSKA_NAVADNA###']=$this->cObj->substituteMarkerArrayCached($templateActionUser,$singleMarkProduct,array(),array());
					$multieMarkProduct['###AKCIJSKA_NONE###']='';
				} else {
					$multieMarkProduct['###AKCIJSKA_NONE###']=$this->cObj->substituteMarkerArrayCached($templateNormalUser,$singleMarkProduct,array(),array());
					$multieMarkProduct['###AKCIJSKA_NAVADNA###']='';
				}				
			}
			
			//t3lib_utility_Debug::debug($singleMarkProduct);
			$returnContent .= $this->cObj->substituteMarkerArrayCached($template,$singleMarkProduct,$multieMarkProduct,array());
		}
		$templateWrap = $this->loadTemplate('###PRODUCTS_LIST_TEMPLATE_WRAP###');
		$singleWrap['###LIST_ELEMENTS###']=$returnContent;
		$singleWrap['###CAT_TITLE###']=$this->displayCatTitle();
		$singleWrap['###DINAMIC_FILTER###']=$this->displayDinFilter();
		$retStr = $this->cObj->substituteMarkerArrayCached($templateWrap,$singleWrap,array(),array());
		return $retStr;
	}
	function displayProductsListAjax(){
		if(!$this->piVars['cats'] && !$this->piVars['props']){return $this->pi_getLL('no_category_error');}
		$params=array();
		$this->allCategoriesSorted=$this->getAllCategories(array());
		
		$this->allProp3 = $this->getProperties3();
		$this->allProp4 = $this->getProperties4();
		foreach($this->allCategoriesSorted as $c){
			if($c['uid']==$this->piVars['cat']){
				$currentCat=$c;
				break;				
			}
		}
		foreach($this->allProp3 as $p){
			if($p['uid']==$this->piVars['prop3']){
				$currentCat=$p;
				break;				
			}
		}
		foreach($this->allProp4 as $p){
			if($p['uid']==$this->piVars['prop4']){
				$currentCat=$p;
				break;				
			}
		}
		
		$params['cat']= ($this->piVars['cats']) ? $this->piVars['cats'] : $this->categoryChildsList($this->piVars['cat']);
		$params['prop']=$this->piVars['props'];
		//$params['prop4']=$this->piVars['prop4'];
		//$params['order_by']=$this->piVars['orderBy'];
		
		//DA NI PAGEBROWSINGA
		$params['next_prev']=true;

		$template = $this->loadTemplate('###PRODUCTS_LIST_TEMPLATE###');
		if(!$template){return $this->pi_getLL('no_template_error');}
		
		//$template = $this->loadTemplate('###PRODUCTS_SPECIAL_LIST_TEMPLATE###');
		$templateActionUser = $this->loadTemplate('###AKCIJSKA_NAVADNA###');
		$templateNormalUser = $this->loadTemplate('###AKCIJSKA_NONE###');
		$templateMonthTea = $this->loadTemplate('###MESEC_CAJA_NONE###');
		$templateTypeTea = $this->loadTemplate('###VRSTA_CAJA_NONE###');
		$templateBioTea = $this->loadTemplate('###BIOSTICKER_NONE###');
		if(!$template){return $this->pi_getLL('no_template_error');}
		$singleMark=array();
		$singleMarkCE=array();

		
		$products = $this->getProducts($params);
		
		$returnContent='';
		$counter=0;
		foreach($products as $product){
			if($GLOBALS['TSFE']->loginUser && $this->feUserGroup==2) {
				if($product['price_partner'] || $product['web_price_partner']) {
					$product['price'] = $product['price_partner'];
					$product['web_price'] = $product['web_price_partner'];
				}
			}
			$productCategories = $this->getProductCategories(array('prod_uid'=>$product['uid']));
			$singleMarkProduct['###CAT_UID###']=$singleMarkType['###CAT_UID###']=$singleMarkMonth['###CAT_UID###']=$productCategories[0]['uid'];
			//t3lib_utility_Debug::debug($productCategories);
			//echo "<br>";
			
			$singleMarkProduct['###PRODUCT###']=$product['title'];
			
			$singleMarkProduct['###PRODUCT_SINGLE_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$productCategories[0]['uid'],'prod'=>$product['uid']), 1, 1,$this->conf['page_single_product']);
			
			$singleMarkProduct['###PRODUCT_SUBTITLE###']=$product['subtitle'];
			
			$origImagesArray=explode(',',$product['images']);
		
			$leadImage = $origImagesArray[0];
			$imgConfig['file.']['maxH'] = '263px';
			$imgConfig['file.']['height'] = '280px';
			$singleMark['###IMAGE_ORIGINAL###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').'uploads/tx_easyshop/'.$leadImage;
			$imgConfig['file'] = 'uploads/tx_easyshop/'.$leadImage;
			$origImg = $this->cObj->IMAGE($imgConfig);
			$resizedImageInfo = $GLOBALS['TSFE']->lastImageInfo;
			$singleMarkProduct['###IMAGE###']=$resizedImageInfo[3];
			
			$singleMarkProduct['###PRODUCT_PRICE###']=$this->moneyFormat($product['price']);
			if($product['web_price']) {
				$singleMarkProduct['###PRODUCT_ACTION_DISCOUNT###']=100 - intval(100*(floatval(str_replace(',','.',$product['web_price'])))/floatval(str_replace(',','.',$product['price'])));
				$singleMarkProduct['###PRODUCT_ACTION_PRICE###']=$this->moneyFormat($product['web_price']);
				$multieMarkProduct['###AKCIJSKA_NAVADNA###']=$this->cObj->substituteMarkerArrayCached($templateActionUser,$singleMarkProduct,array(),array());
				$multieMarkProduct['###AKCIJSKA_NONE###']='';
			} else {
				$multieMarkProduct['###AKCIJSKA_NONE###']=$this->cObj->substituteMarkerArrayCached($templateNormalUser,$singleMarkProduct,array(),array());
				$multieMarkProduct['###AKCIJSKA_NAVADNA###']='';
			}

			if($product['month_tea']) {
				$singleMarkMonth['###MESEC_CAJA###']=$product['month_tea'];
				$multieMarkProduct['###MESEC_CAJA_NONE###'] = $this->cObj->substituteMarkerArrayCached($templateMonthTea,$singleMarkMonth,array(),array());
			} else {
				$multieMarkProduct['###MESEC_CAJA_NONE###']='';
			}
			
			if($product['type_tea']) {
				$singleMarkType['###VRSTA_CAJA###']=$product['type_tea'];
				$multieMarkProduct['###VRSTA_CAJA_NONE###'] = $this->cObj->substituteMarkerArrayCached($templateTypeTea,$singleMarkType,array(),array());
			} else {
				$multieMarkProduct['###VRSTA_CAJA_NONE###']='';
			}
			
			if($product['bio_tea']) {
				$multieMarkProduct['###BIOSTICKER_NONE###'] = $templateBioTea;
			} else {
				$multieMarkProduct['###BIOSTICKER_NONE###']='';
			}
			
			
			// CE IMAMO CENO VEZANO NA KOLIČINE!
			if($product['price_prop2']) {
				$priceArray = explode('|',$product['price_prop2']);
				$singleMarkProduct['###PRODUCT_PRICE###']=$this->moneyFormat(trim($priceArray[0]));
				if($product['price_disc_prop2']) {
					$priceDiscArray = explode('|',$product['price_disc_prop2']);
					$singleMarkProduct['###PRODUCT_ACTION_PRICE###']=$this->moneyFormat(trim($priceDiscArray[0]));
					$singleMarkProduct['###PRODUCT_ACTION_DISCOUNT###']=100 - intval(100*(floatval(str_replace(',','.',trim($priceDiscArray[0]))))/floatval(str_replace(',','.',trim($priceArray[0]))));
					$multieMarkProduct['###AKCIJSKA_NAVADNA###']=$this->cObj->substituteMarkerArrayCached($templateActionUser,$singleMarkProduct,array(),array());
					$multieMarkProduct['###AKCIJSKA_NONE###']='';
				} else {
					$multieMarkProduct['###AKCIJSKA_NONE###']=$this->cObj->substituteMarkerArrayCached($templateNormalUser,$singleMarkProduct,array(),array());
					$multieMarkProduct['###AKCIJSKA_NAVADNA###']='';
				}				
			}
			
			//t3lib_utility_Debug::debug($singleMarkProduct);
			$returnContent .= $this->cObj->substituteMarkerArrayCached($template,$singleMarkProduct,$multieMarkProduct,array());
		}
		return $returnContent;
	}
	
	function displayProductTitle($arg){
		$template = $this->loadTemplate('###PRODUCT_TITLE_TEMPLATE###');
		if(!$template){return $this->pi_getLL('no_template_error');}
		$singleMark=array();
		if(!$this->piVars['prod']){
			$singleMark['###TITLE###']=$this->pi_getLL('no_product_uid');
			$singleMark['###SUBTITLE###']='';
		}else{
			$product = $this->getProduct(array('uid'=>$this->piVars['prod']));
			$singleMark['###TITLE###']=$product['title'];
			$singleMark['###SUBTITLE###']=$product['subtitle'];
		}
		return $this->cObj->substituteMarkerArrayCached($template,$singleMark,array(),array());
	}
	
	function displayCatTitle(){
		$cat = $this->getCategory($this->piVars['cat']);
		if($cat){
			$template = $this->loadTemplate('###CAT_TITLE_TEMPLATE###');
			if(!$template){return $this->pi_getLL('no_template_error');}
			$parentCat = $this->getCategory($cat['parrent']);
			$singleMark['###CAT_TITLE###'] = $cat['title_front'];
			$singleMark['###CAT_DESC###']=$this->cObj->parseFunc($cat['description'], $GLOBALS['TSFE']->tmpl->setup['tt_content.']['text.']['20.']['parseFunc.']);
			$singleMark['###PARENT_CAT_TITLE###'] = $parentCat['title_front'];
		}else{
			$singleMark['###TITLE###']=$this->pi_getLL('no_category_error');
			$singleMark['###DESCRIPTION###']='';
		}
		return $this->cObj->substituteMarkerArrayCached($template,$singleMark,array(),array());
	}
	
	
	function displayProductSingle($arg){
		$template = $this->loadTemplate('###PRODUCTS_SINGLE_TEMPLATE###');
		if(!$template){return $this->pi_getLL('no_template_error');}
		$singleMark=array();
		$multiMark=array();
		
		$basket_session = $GLOBALS["TSFE"]->fe_user->getKey('ses','web_shop');
		
		//t3lib_utility_Debug::debug($GLOBALS['TSFE']->loginUser);
		//t3lib_utility_Debug::debug($this->feUserGroup);
		//t3lib_utility_Debug::debug($GLOBALS['TSFE']->fe_user);
		//t3lib_utility_Debug::debug($GLOBALS["TSFE"]->fe_user->getKey('ses','web_shop'));
		//$GLOBALS["TSFE"]->fe_user->setKey('ses','web_shop','');
		/*
		$singleMark['###CATEGORY_PATHTRACK###']='';
		if($this->piVars['cat']){
			$singleMark['###CATEGORY_PATHTRACK###']=$this->displayCategoryPathTrack($this->getAllCategories(array()));
		}*/
		
		$template_categories = $this->cObj->getSubpart($template, '###SINGLE_CATEGORIES###');
		$template_properties = $this->cObj->getSubpart($template, '###SINGLE_PROPERTIES###');
		$template_properties_mono = $this->cObj->getSubpart($template, '###MONO_PROPERTIES###');
		$template_properties2_mono = $this->cObj->getSubpart($template, '###MONO_PROPERTIES2###');
		$template_properties2 = $this->cObj->getSubpart($template, '###SINGLE_PROPERTIES2###');
		$template_images_big = $this->cObj->getSubpart($template, '###PRODUCT_IMAGES_BIG###');
		$template_images_small = $this->cObj->getSubpart($template, '###PRODUCT_IMAGES_SMALL###');
		//$template_files = $this->cObj->getSubpart($template, '###PRODUCT_FILES###');
		$template_basket = $this->cObj->getSubpart($template, '###ADD_TO_BASKET###');
		$template_stock_yes = $this->cObj->getSubpart($template, '###ZALOGAYES###');
		$template_stock_no = $this->cObj->getSubpart($template, '###ZALOGANO###');
		$template_path = $this->cObj->getSubpart($template, '###PRODUCT_BREAD_CAT###');
		$templateMonthTea = $this->cObj->getSubpart($template, '###MESEC_CAJA_NONE###');
		$templateTypeTea = $this->cObj->getSubpart($template, '###VRSTA_CAJA_NONE###');
		$templateBioTea = $this->cObj->getSubpart($template, '###BIOSTICKER_NONE###');
		
    
		
		$singleMark['###ONLY1###']='';		
		$singleMark['###TITLE###']='';
		$singleMark['###SUBTITLE###']='';
		$singleMark['###DESCRIPTION###']='';
		$singleMark['###DESCRIPTION2###']='';
		$singleMark['###PROPERTIES###']='';
		$singleMark['###STOCK###']='';
		$singleMark['###DELIVERY_TIME###']='';
		$singleMark['###CODE###']='';
		//$multiMark['###ADD_TO_BASKET###']='';
		$multiMark['###SINGLE_CATEGORIES###']='';
		$multiMark['###SINGLE_PROPERTIES###']='';
		$multiMark['###SINGLE_PROPERTIES2###']='';
		$multiMark['###PRODUCT_IMAGES_BIG###']='';
		$multiMark['###PRODUCT_IMAGES_SMALL###']='';
		$multiMark['###ZALOGAYES###']='';
		$multiMark['###ZALOGANO###']='';
		$multiMark['###PRODUCT_BREAD_CAT###']='';
		
		if(!$this->piVars['prod']){
			$singleMark['###TITLE###']=$this->pi_getLL('no_product_uid');
		}else{
			$product = $this->getProduct(array('uid'=>$this->piVars['prod']));
			
			//peterw: get star ratings
			$data_file = './ratings.data.txt';
			$all = file_get_contents($data_file);
	    
	    if($all) {
	    	$data = unserialize($all);
	    	$R = 'r'.$product['uid'];
	    	$data = $data[$R];
	    	$singleMark['###NUM_VOTES###']=$data['dec_avg'];
	    	$singleMark['###ALL_VOTES###']=$data['number_votes'];
	    }
			
			$url = $_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
			if(substr($url, -1) != '/'){
				$url = $url.'/';
			}

			$GLOBALS['TSFE']->additionalHeaderData['ogtitle'] = '<meta property="og:title" content="'.$product['title'].'"/>';
			$GLOBALS['TSFE']->additionalHeaderData['ogdescription'] = '<meta property="og:description" content="'.$product['subtitle'].'"/>';
			$GLOBALS['TSFE']->additionalHeaderData['ogurl'] = '<meta property="og:url" content="http://'.$url.'"/>';
			

			$singleMark['###UID###']=$product['uid'];
			$singleMark['###TITLE###']=$product['title'];
			$singleMark['###SUBTITLE###']=$product['subtitle'];
			$singleMark['###CODE###']=$product['code'];
			if($product['delivery_time']) {
				$singleMark['###DELIVERY_TIME###']=$product['delivery_time'];
			} else {
				$multiMark['###NO_DELIVERY_TIME###']='';
			}
						
			//$singleMark['###DESCRIPTION###']=$this->cObj->parseFunc($product['description'], $GLOBALS['TSFE']->tmpl->setup['tt_content.']['text.']['20.']['parseFunc.']);
			$singleMark['###DESCRIPTION###']=$this->pi_RTEcssText($product['description']);

			
			if($product['description2']) {
				//$singleMark['###DESCRIPTION2###']=$this->cObj->parseFunc($product['description2'], $GLOBALS['TSFE']->tmpl->setup['tt_content.']['text.']['20.']['parseFunc.']);
				$singleMark['###DESCRIPTION2###']=$this->pi_RTEcssText($product['description2']);
			} else{
				$multiMark['###DESCRIPTION2_NONE###']='';
			}
//t3lib_div::Debug($GLOBALS['TSFE']->tmpl->setup['tt_content.']['text.']['20.']['parseFunc.']);
//t3lib_div::Debug($GLOBALS['TSFE']->tmpl->setup['tt_content.']);
			$singleMark['###PROPERTIES###']='';
			
			if($product['stock']) {
				$multiMark['###ZALOGAYES###']=$template_stock_yes;
			} else {
				$multiMark['###ZALOGANO###']=$template_stock_no;
			}
			
			$categories=$this->getAllCategories(array());	
			$pathTrack='';
			foreach($categories as $category){
				if($this->piVars['cat']==$category['uid']){
					$pathTrack=$this->generatePathTrack($category,$categories,$template_path);
					break;
				}	
			}
			$multiMark['###PRODUCT_BREAD_CAT###'] = $pathTrack;
			
			$imagesArray=explode(',',$product['images']);
			$imagesNum=count($imagesArray);
			if($imagesNum>0){
				if($imagesNum==1) {
					$singleMark['###ONLY1###']='only1Img';
				} 
			
				for($i=0; $i<$imagesNum; $i++){
					$singleMarkImages['###IMG_FRAGMENT_ID###']=$i+1;
					$uplImage = $imagesArray[$i];
					
					$imgConfigBig['file.']['maxH'] = $this->conf['image.']['maxH'].'c';
					$imgConfigBig['file.']['height'] = $this->conf['image.']['maxH'].'c';
					$imgConfigBig['file.']['XY'] = '[10.w],[10.h]';
					$imgConfigBig['file'] = 'uploads/tx_easyshop/'.$uplImage;
					$origImgBig = $this->cObj->IMAGE($imgConfigBig);
					$resizedImageInfo = $GLOBALS['TSFE']->lastImageInfo;
					$singleMarkImages['###PRODUCT_IMAGE_BIG_SRC###']=$resizedImageInfo[3];
//t3lib_utility_Debug::debug($resizedImageInfo);
					$imgConfigSmall['file.']['maxH'] = $this->conf['thumbnail.']['maxH'].'c';
					$imgConfigSmall['file.']['height'] = $this->conf['thumbnail.']['maxH'].'c';
					$imgConfigSmall['file.']['XY'] = '[10.w],[10.h]';
					$imgConfigSmall['file'] = 'uploads/tx_easyshop/'.$uplImage;
					$origImgSmall = $this->cObj->IMAGE($imgConfigSmall);
					$resizedImageInfo = $GLOBALS['TSFE']->lastImageInfo;
					$singleMarkImages['###PRODUCT_IMAGE_SMALL_SRC###']=$resizedImageInfo[3];
//t3lib_utility_Debug::debug($singleMarkImages);					
					$imageBig .= $this->cObj->substituteMarkerArrayCached($template_images_big,$singleMarkImages,array(),array());
					$imageSmall .= $this->cObj->substituteMarkerArrayCached($template_images_small,$singleMarkImages,array(),array());
				}
			}
			
			$multiMark['###PRODUCT_IMAGES_BIG###'] = $imageBig;
			$singleMark['###PRODUCT_IMAGES_BIG_SRC###'] = 'uploads/tx_easyshop/'.$imagesArray[0];
			$multiMark['###PRODUCT_IMAGES_SMALL###'] = $imageSmall;
			$GLOBALS['TSFE']->additionalHeaderData['ogimage'] = '<meta property="og:image" content="http://www.zisha.si/'.$imgConfigBig['file'].'"/>';
			$GLOBALS['TSFE']->additionalHeaderData['ogimage2'] = '<link rel="image_src" type="image/jpeg" href="http://www.zisha.si/'.$imgConfigBig['file'].'" />';
			
			$params['cat']=$this->categoryChildsList($this->piVars['cat']);
			$params['order_by']=$this->piVars['orderBy'];
			$params['next_prev']=true;
			
			$products = $this->getProducts($params);
			//t3lib_utility_Debug::debug($products);
			for($i=0; $i<count($products); $i++){
			//t3lib_utility_Debug::debug(count($products));
				if(count($products) == 1) {
					$nextUid = $product['uid'];
					$prevUid = $product['uid'];
					
					break;
				}
				if($products[$i]['uid'] == $product['uid']) {
					if($products[$i-1]['uid']) {
						$prevUid = $products[$i-1]['uid'];
					} else {
						$prevUid = $products[count($products)-1]['uid'];
					}
					if($products[$i+1]['uid']) {
						$nextUid = $products[$i+1]['uid'];
					} else {
						$nextUid = $products[0]['uid'];
					}
				}
			}
			
			//t3lib_utility_Debug::debug($nextUid);
			//t3lib_utility_Debug::debug($prevUid);
			
			$singleMark['###PREV_ITEM###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$this->piVars['cat'],'prod'=>$prevUid), 1, 1,$this->conf['page_single_product']);
			$singleMark['###NEXT_ITEM###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$this->piVars['cat'],'prod'=>$nextUid), 1, 1,$this->conf['page_single_product']);
			
			//t3lib_div::_POST('drugPlacnik')
			
			if($GLOBALS['TSFE']->loginUser && $this->feUserGroup==2) {
				if($product['price_partner'] || $product['web_price_partner']) {
					$product['price'] = $product['price_partner'];
					$product['web_price'] = $product['web_price_partner'];
				}
			}
			
			$singleMark['###PRODUCT_PRICE###']=$this->moneyFormat($product['price']);
			
			//t3lib_utility_Debug::debug($product);
			
			if($product['web_price']) {
				$singleMark['###PRODUCT_ACTION_DISCOUNT###']=100 - intval(100*(floatval(str_replace(',','.',$product['web_price'])))/floatval(str_replace(',','.',$product['price'])));
				$singleMark['###PRODUCT_ACTION_PRICE###']=$this->moneyFormat($product['web_price']);
				$singleMark['###AKCIJSKA_YES###']='priceAkcijaYes';
				$singleMark['###AKCIJSKA_NO###']='akcijaYes';
			} else {
				$singleMark['###AKCIJSKA_YES###']='priceAkcijaNo';
				$singleMark['###AKCIJSKA_NO###']='akcijaNo';
				$singleMark['###PRODUCT_ACTION_PRICE###']='';
				if(!$product['price_prop2']) $multiMark['###AKCIJSKA_NAVADNA###']='';				
			}
		
			// CE IMAMO CENO VEZANO NA KOLIČINE!
			if($product['price_prop2']) {
				$priceArray = explode('|',$product['price_prop2']);
				$singleMark['###PRODUCT_PRICE###']=$this->moneyFormat(trim($priceArray[0]));
				if($product['price_disc_prop2']) {
					//t3lib_utility_Debug::debug(explode('|',$product['price_disc_prop2']));
					$priceDiscArray = explode('|',$product['price_disc_prop2']);
					$singleMark['###PRODUCT_ACTION_PRICE###']=$this->moneyFormat(trim($priceDiscArray[0]));
					$singleMark['###PRODUCT_ACTION_DISCOUNT###']=100 - intval(100*(floatval(str_replace(',','.',trim($priceDiscArray[0]))))/floatval(str_replace(',','.',trim($priceArray[0]))));
					$singleMark['###AKCIJSKA_YES###']='priceAkcijaYes';
					$singleMark['###AKCIJSKA_NO###']='akcijaYes';
				} else {
					$singleMark['###AKCIJSKA_YES###']='priceAkcijaNo';
					$singleMark['###AKCIJSKA_NO###']='akcijaNo';
					$singleMark['###PRODUCT_ACTION_PRICE###']='';	
					$multiMark['###AKCIJSKA_NAVADNA###']='';
				}
			}
			
			$productCategories = $this->getProductCategories(array('prod_uid'=>$product['uid']));
			$singleMark['###CAT_UID###']=$singleMarkType['###CAT_UID###']=$singleMarkMonth['###CAT_UID###']=$productCategories[0]['uid'];
			
			if(!$product['has_tester']) {
				$multiMark['###SHOW_TESTER###'] = '';
			}
			
			if($product['month_tea']) {
				$singleMarkMonth['###MESEC_CAJA###']=$product['month_tea'];
				$multiMark['###MESEC_CAJA_NONE###'] = $this->cObj->substituteMarkerArrayCached($templateMonthTea,$singleMarkMonth,array(),array());
			} else {
				$multiMark['###MESEC_CAJA_NONE###']='';
			}
			
			if($product['type_tea']) {
				$singleMarkType['###VRSTA_CAJA###']=$product['type_tea'];
				$multiMark['###VRSTA_CAJA_NONE###'] = $this->cObj->substituteMarkerArrayCached($templateTypeTea,$singleMarkType,array(),array());
			} else {
				$multiMark['###VRSTA_CAJA_NONE###']='';
			}
			
			if($template_properties){
				$productProperties = $this->getProductProperties(array('prod_uid'=>$this->piVars['prod']));
				
				//t3lib_utility_Debug::debug(count($productProperties));
				$propNum = count($productProperties);
				if($propNum == 0) {
					$multiMark['###MULTIE_PROPERTIES###'] = '';
				} else if($propNum == 1) {
					$multiMark['###NO_MONO_PROPERTIES###'] = '';
					$multiMark['###MONO_PROPERTIES###'].=$this->cObj->substituteMarkerArrayCached($template_properties_mono,array('###SINGLE_PROPERTY_TITLE###' => $productProperties[0]['display_title'], '###SINGLE_PROPERTY_UID###' => $productProperties[0]['uid']),array(),array());
					//$multiMark['###MULTIE_PROPERTIES###'] = $productProperties[0]['display_title'];
				} else {
					foreach ($productProperties as $prop) {
						$multiMark['###SINGLE_PROPERTIES###'].=$this->cObj->substituteMarkerArrayCached($template_properties,array('###SINGLE_PROPERTY_TITLE###' => $prop['display_title'], '###SINGLE_PROPERTY_UID###' => $prop['uid']),array(),array());
					}
					$multiMark['###MONO_PROPERTIES###'] = '';
				}
			}
			
			if($template_properties2) {
				$productProperties2 = $this->getProductProperties2(array('prod_uid'=>$this->piVars['prod']));
				//t3lib_utility_Debug::debug($productProperties2);
				$propNum = count($productProperties2);
				if($propNum == 0) {
					$multiMark['###MULTIE_PROPERTIES2###'] = '';
				} else if($propNum == 1) {
				
					$multiMark['###NO_MONO_PROPERTIES2###'] = '';
					$multiMark['###MONO_PROPERTIES2###'].=$this->cObj->substituteMarkerArrayCached($template_properties2_mono,array('###SINGLE_PROPERTY2_TITLE###' => $productProperties2[0]['display_title'], '###SINGLE_PROPERTY_UID###' => $productProperties2[0]['uid']),array(),array());
				}
				else {
					foreach ($productProperties2 as $prop) {
					//t3lib_utility_Debug::debug($prop);
						$multiMark['###SINGLE_PROPERTIES2###'].=$this->cObj->substituteMarkerArrayCached($template_properties2,array('###SINGLE_PROPERTY2_TITLE###' => $prop['display_title'], '###SINGLE_PROPERTY_UID###' => $prop['uid']),array(),array());
					}
					//t3lib_utility_Debug::debug($multiMark['###SINGLE_PROPERTIES2###']);
					$multiMark['###MONO_PROPERTIES2###'] = '';
				}
			}
			
			if($product['connected_products']) {
				$singleMark['###CONNECTED_PRODUCTS###'] = $this->displayConnectedProducts($product['connected_products']);
			} else {
				$singleMark['###CONNECTED_PRODUCTS###'] = '';
				$multiMark['###CONNECTED_PRODUCTS_NONE###'] = '';
			}
		}

		//t3lib_utility_Debug::debug($singleMark);

		return $this->cObj->substituteMarkerArrayCached($template,$singleMark,$multiMark,array());
	}
	
	function displayConnectedProducts($arg) {
		$params=array();
	
		$params['order_by']=$this->conf['productsFrontSortOrder'];
		$params['asc_desc']=$this->conf['productsFrontSortOrderAscDesc'];	
		$params['connectedUids']=$arg;
		
		$template = $this->loadTemplate('###PRODUCTS_CONNECTED_LIST_TEMPLATE###');
		$templateActionUser = $this->loadTemplate('###AKCIJSKA_NAVADNA###');
		$templateNormalUser = $this->loadTemplate('###AKCIJSKA_NONE###');
		$templateMonthTea = $this->cObj->getSubpart($template, '###MESEC_CAJA_NONE###');
		$templateTypeTea = $this->cObj->getSubpart($template, '###VRSTA_CAJA_NONE###');
		$templateBioTea = $this->cObj->getSubpart($template, '###BIOSTICKER_NONE###');
if(!$template){return $this->pi_getLL('no_template_error');}
		$singleMark=array();
		$products = $this->getProducts($params);
		//t3lib_utility_Debug::debug($products);
		$returnContent='';
		$counter=0;
		foreach($products as $product){
			if($GLOBALS['TSFE']->loginUser && $this->feUserGroup==2) {
				if($product['price_partner'] || $product['web_price_partner']) {
					$product['price'] = $product['price_partner'];
					$product['web_price'] = $product['web_price_partner'];
				}
			}
			$productCategories = $this->getProductCategories(array('prod_uid'=>$product['uid']));
			$singleMarkProduct['###PRODUCT###']=$product['title'];
			$singleMarkProduct['###PRODUCT_SINGLE_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$productCategories[0]['uid'],'prod'=>$product['uid']), 1, 1,$this->conf['page_single_product']);
			$singleMarkProduct['###PRODUCT_SUBTITLE###']=$product['subtitle'];
			$origImagesArray=explode(',',$product['images']);		
			$leadImage = $origImagesArray[0];
			$imgConfig['file.']['maxH'] = '263px';
			$imgConfig['file.']['height'] = '280px';
			$imgConfig['file.']['XY'] = '[10.w],[10.h]';
			$singleMark['###IMAGE_ORIGINAL###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').'uploads/tx_easyshop/'.$leadImage;
			$imgConfig['file'] = 'uploads/tx_easyshop/'.$leadImage;
			$origImg = $this->cObj->IMAGE($imgConfig);
			$resizedImageInfo = $GLOBALS['TSFE']->lastImageInfo;
			$singleMarkProduct['###IMAGE###']=$resizedImageInfo[3];
			
			$singleMarkProduct['###PRODUCT_PRICE###']=$this->moneyFormat($product['price']);
			if($product['web_price']) {
				$singleMarkProduct['###PRODUCT_ACTION_DISCOUNT###']=100 - intval(100*(floatval(str_replace(',','.',$product['web_price'])))/floatval(str_replace(',','.',$product['price'])));
				$singleMarkProduct['###PRODUCT_ACTION_PRICE###']=$this->moneyFormat($product['web_price']);	
				$multieMarkProduct['###AKCIJSKA_NAVADNA###']=$this->cObj->substituteMarkerArrayCached($templateActionUser,$singleMarkProduct,array(),array());
				$multieMarkProduct['###AKCIJSKA_NONE###']='';
			} else {
				$multieMarkProduct['###AKCIJSKA_NONE###']=$this->cObj->substituteMarkerArrayCached($templateNormalUser,$singleMarkProduct,array(),array());
				$multieMarkProduct['###AKCIJSKA_NAVADNA###']='';
			}
			
			// CE IMAMO CENO VEZANO NA KOLIČINE!
			if($product['price_prop2']) {
				$priceArray = explode('|',$product['price_prop2']);
				$singleMarkProduct['###PRODUCT_PRICE###']=$this->moneyFormat(trim($priceArray[0]));
				if($product['price_disc_prop2']) {
					$priceDiscArray = explode('|',$product['price_disc_prop2']);
					$singleMarkProduct['###PRODUCT_ACTION_PRICE###']=$this->moneyFormat(trim($priceDiscArray[0]));
					$singleMarkProduct['###PRODUCT_ACTION_DISCOUNT###']=100 - intval(100*(floatval(str_replace(',','.',trim($priceDiscArray[0]))))/floatval(str_replace(',','.',trim($priceArray[0]))));
					$multieMarkProduct['###AKCIJSKA_NAVADNA###']=$this->cObj->substituteMarkerArrayCached($templateActionUser,$singleMarkProduct,array(),array());
					$multieMarkProduct['###AKCIJSKA_NONE###']='';
				} else {
					$multieMarkProduct['###AKCIJSKA_NONE###']=$this->cObj->substituteMarkerArrayCached($templateNormalUser,$singleMarkProduct,array(),array());
					$multieMarkProduct['###AKCIJSKA_NAVADNA###']='';
				}				
			}
			
								
			if($product['month_tea']) {
				$singleMarkMonth['###MESEC_CAJA###']=$product['month_tea'];
				$multieMarkProduct['###MESEC_CAJA_NONE###'] = $this->cObj->substituteMarkerArrayCached($templateMonthTea,$singleMarkMonth,array(),array());
			} else {
				$multieMarkProduct['###MESEC_CAJA_NONE###']='';
			}
			
			if($product['type_tea']) {
				$singleMarkType['###VRSTA_CAJA###']=$product['type_tea'];
				$multieMarkProduct['###VRSTA_CAJA_NONE###'] = $this->cObj->substituteMarkerArrayCached($templateTypeTea,$singleMarkType,array(),array());
			} else {
				$multieMarkProduct['###VRSTA_CAJA_NONE###']='';
			}
			
			if($product['bio_tea']) {
				$multieMarkProduct['###BIOSTICKER_NONE###'] = $templateBioTea;
			} else {
				$multieMarkProduct['###BIOSTICKER_NONE###']='';
			}
			
			//t3lib_utility_Debug::debug($singleMarkProduct);
			$returnContent .= $this->cObj->substituteMarkerArrayCached($template,$singleMarkProduct,$multieMarkProduct,array());
			$counter++;
			if($counter == intval($this->conf['special_limit'])) {
				return $returnContent;
			}
		}
		
		return $returnContent;
	}
	
	function displayProductsFilter($arg){
		$template = $this->loadTemplate('###PRODUCTS_PROPERTIES_TEMPLATE###');
		$items=$this->cObj->getSubpart($template, '###PROPERTIES_ITEMS###');
		$item_parrent=$this->cObj->getSubpart($items, '###PROPERTIES_PARRENT###');
		$item_child=$this->cObj->getSubpart($items, '###PROPERTIES_CHILD###');
		
		$singleMark=array();
		$multiMark=array();
		$multiMark['###PROPERTIES_ITEMS###']='';
		
		$properties_tree=array();
		$properties = $this->getProperties($arg);
		foreach($properties as $key=>$p){
			if($p['parrent']==0){
				$p['childs'] = $this->propertyChilds($p,$properties);
				$properties_tree[] = $p;
			}
		}
//t3lib_div::Debug($properties_tree);
		$prop_menu_items='';
		foreach($properties_tree as $key=>$property){
			$singleMarkProp = array();
			$multiMarkProp = array();
			$singleMarkProp['###PROPERTY_CLASS###'] = 'prop_child';
			$singleMarkProp['###PROPERTY###'] = $property['display_title'];
			$singleMarkProp['###PROPERTY_UID###'] = $property['uid'];
			if($this->cat_level==1){$singleMarkProp['###PROPERTY_CLASS###'] = 'prop_root';}
			$singleMarkProp['###PROPERTY_CHILDS###'] = '';
			if($property['childs']){
				$singleMarkProp['###PROPERTY_CLASS###'] = 'prop_parrent';
				$this->cat_level++;
				$singleMarkProp['###PROPERTY_CHILDS###'] = $this->displayPropertyChilds(array('childs'=>$property['childs'],'templates'=>array('template'=>$items,'item_parrent'=>$item_parrent,'item_child'=>$item_child)));
				$this->cat_level--;
			}
//t3lib_div::Debug($property);
//t3lib_div::Debug($singleMarkProp['###PROPERTY_CHILDS###']);
			$singleMarkProp['###CHILD_PROPERTIES_LIST###']=implode(',',$property['all_child_list']);
			if($property['child_has_products']){
				if(!$property['has_products']){
					
					$prop_menu_items .= $this->cObj->substituteMarkerArrayCached($item_parrent,$singleMarkProp,$multiMarkProp,array());	
				}else{
					$prop_menu_items .= $this->cObj->substituteMarkerArrayCached($item_child,$singleMarkProp,$multiMarkProp,array());				
				}	
			}
		}
		if($prop_menu_items){
			$multiMark['###PROPERTIES_ITEMS###']=$this->cObj->substituteMarkerArrayCached($items,array('###PROPERTY_LEVEL_CLASS###'=>$this->cat_level),array('###PROPERTIES_PARRENT###'=> $prop_menu_items,'###PROPERTIES_CHILD###'=> ''),array());
			return $this->cObj->substituteMarkerArrayCached($template,$singleMark,$multiMark,array());
		}
		return '';
	}
	function displayPropertyChilds($arg){
		//array('childs'=>$category['childs'],'templates'=>array($template,$cat_item,$cat_prod_count)));
		$prop_menu_items = '';
		foreach($arg['childs'] as $key=>$property){
			$singleMark=array();
			$multiMark=array();
			$singleMark['###PROPERTY_CLASS###'] = 'prop_child';
			$singleMark['###PROPERTY###'] = $property['display_title'];
			$singleMark['###PROPERTY_UID###'] = $property['uid'];
			$singleMark['###PROPERTY_CHILDS###'] = '';
			if($property['childs']){
				$singleMark['###PROPERTY_CLASS###'] = 'prop_parrent';
				$this->cat_level++;
				$singleMark['###PROPERTY_CHILDS###'] = $this->displayPropertyChilds(array('childs'=>$property['childs'],'templates'=>$arg['templates']));
				$this->cat_level--;	
			}
			$singleMark['###CHILD_PROPERTIES_LIST###']=implode(',',$property['all_child_list']);
			if($property['child_has_products']){
				if(!$property['has_products']){
					$prop_menu_items .= $this->cObj->substituteMarkerArrayCached($arg['templates']['item_parrent'],$singleMark,$multiMark,array());	
				}else{
					$prop_menu_items .= $this->cObj->substituteMarkerArrayCached($arg['templates']['item_child'],$singleMark,$multiMark,array());				
				}	
			}
							
		}
//t3lib_div::Debug($prop_menu_items);		
		if($prop_menu_items){
			return $this->cObj->substituteMarkerArrayCached($arg['templates']['template'],array('###PROPERTY_LEVEL_CLASS###'=>$this->cat_level),array('###PROPERTIES_PARRENT###'=> $prop_menu_items,'###PROPERTIES_CHILD###'=> ''),array());	
		}
		return '';
	}
	function displayCategoryPathTrack($categories){
//t3lib_div::Debug();
		if(!is_array($categories)){
			$categories=$this->getAllCategories(array());	
		}
		$pathTrack='';
		foreach($categories as $category){
			if($this->piVars['cat']==$category['uid']){
				$pathTrack=$this->generatePathTrack($category,$categories);
				break;
			}	
		}
		return $pathTrack;
	}
	function displayTopMenu($arg){
		$categories = $this->getAllCategories($arg);
		$categories_tree = array();
		foreach($categories as $key=>$category){
			if($category['parrent']==0){
				$category['childs'] = $this->categoryChilds($category['uid'],$categories);
				$categories_tree[] = $category;
			}
		}
//t3lib_div::Debug($categories_tree);
		$template = $this->loadTemplate('###TOP_MENU###');
		if(!$template){return $this->pi_getLL('no_template_error');}
		
		$lvl1Item = $this->cObj->getSubpart($template, '###TOP_MENU_LVL1###');
		$lvl2Items = $this->cObj->getSubpart($lvl1Item, '###TOP_MENU_LVL2_ITEMS###');
		$lvl2Item = $this->cObj->getSubpart($lvl2Items, '###TOP_MENU_LVL2###');
		$lvl3Items = $this->cObj->getSubpart($lvl2Item, '###TOP_MENU_LVL3_ITEMS###');
		$lvl3Item = $this->cObj->getSubpart($lvl3Items, '###TOP_MENU_LVL3###');
		$lvl3More = $this->cObj->getSubpart($lvl3Item, '###SHOW_LVL3_MORE###');
		
		
		//t3lib_utility_Debug::debug($categories_tree);
		$singleMarkCat1 = array();
		$multiMarkCat1 = array();
		$cat_menu_items = '';
		
		
		$imgConfig['file.']['maxW'] = '276c';
		$imgConfig['file.']['width'] = '276c';
		$imgConfig['file.']['XY'] = '[10.w],[10.h]';

			
		
		foreach($categories_tree as $key=>$category){
			$singleMarkCat1['###LVL1_LINK###'] = t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$category['uid']), 1, 1,$this->conf['page_products_list']);
			$singleMarkCat1['###LVL1_TITLE###'] =  $category['display_title'];
			$singleMarkCat1['###LVL1_SUBTITLE###'] =  $category['subtitle'];
			$singleMarkCat1['###LVL1_DESCRIPTION###'] = $this->pi_RTEcssText($category['description']);
			$singleMarkCat1['###NEK_ID/COUNTER###'] =  "key_".$category['uid'];
			
			$origImagesArray=explode(',',$category['image']);		
			$leadImage = $origImagesArray[0];
			$imgConfig['file'] = 'uploads/tx_easyshop/'.$leadImage;
			$origImg = $this->cObj->IMAGE($imgConfig);
			$resizedImageInfo = $GLOBALS['TSFE']->lastImageInfo;
			$singleMarkCat1['###LVL1_IMAGE###']=$resizedImageInfo[3];
			
			//if($this->piVars['cat']==$category['uid']){
			$singleMarkCat1['###ACTIVE###'] = '';
			if($this->piVars['cat']==$category['uid']) {
				$singleMarkCat1['###ACTIVE###'] = 'active';
			}
			
			
			if($category['childs']){
				$lvl2Cat = $category['childs'];
				$singleMarkCat2 = array();
				$multiMarkCat2 = array();
				$cat2_menu_items = '';
				foreach($lvl2Cat as $cat2){
					$singleMarkCat2['###LVL2_LINK###'] = t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$cat2['uid']), 1, 1,$this->conf['page_products_list']);
					$singleMarkCat2['###LVL2_TITLE###'] =  $cat2['display_title'];
					//$multiMarkCat2['###TOP_MENU_LVL3_ITEMS###'] = '';
					if($this->piVars['cat']==$cat2['uid']) {
						$singleMarkCat1['###ACTIVE###'] = 'active';
					}
					if($cat2['childs']){
						$lvl3Cat = $cat2['childs'];
						$singleMarkCat3 = array();
						$multiMarkCat3 = array();
						$multiMarkCat2_3['###SHOW_LVL3_MORE###'] = '';
						$cat3_menu_items = '';
						$lvl3Counter=0;
						foreach($lvl3Cat as $cat3){
							$lvl3Counter++;
							$singleMarkCat3['###LVL3_LINK###'] = t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$cat3['uid']), 1, 1,$this->conf['page_products_list']);
							$singleMarkCat3['###LVL3_TITLE###'] =  $cat3['display_title'];
							if($this->piVars['cat']==$cat3['uid']) {
								$singleMarkCat1['###ACTIVE###'] = 'active';
							}
							if($lvl3Counter == 4) {
								$singleMarkMore['###MORE_LINK###'] = t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$cat2['uid']), 1, 1,$this->conf['page_products_list']);
								$multiMarkCat2_3['###SHOW_LVL3_MORE###'] = $this->cObj->substituteMarkerArrayCached($lvl3More,$singleMarkMore,array(),array());
								break;
							}
							$cat3_menu_items .= $this->cObj->substituteMarkerArrayCached($lvl3Item,$singleMarkCat3,$multiMarkCat3,array());	
						}	
						$multiMarkCat2_3['###TOP_MENU_LVL3###'] = $cat3_menu_items;
						$multiMarkCat2['###TOP_MENU_LVL3_ITEMS###'] = $this->cObj->substituteMarkerArrayCached($lvl3Items,array(),$multiMarkCat2_3,array());
					} else {
						$multiMarkCat2['###TOP_MENU_LVL3_ITEMS###'] = '';
					}
					$cat2_menu_items .= $this->cObj->substituteMarkerArrayCached($lvl2Item,$singleMarkCat2,$multiMarkCat2,array());	
				}
				
				$multiMarkCat1_2['###TOP_MENU_LVL2###'] = $cat2_menu_items;
				$multiMarkCat1['###TOP_MENU_LVL2_ITEMS###'] = $this->cObj->substituteMarkerArrayCached($lvl2Items,$singleMarkCat1,$multiMarkCat1_2,array());
			} else {
				$multiMarkCat1['###TOP_MENU_LVL2_ITEMS###'] = '';
			}
			$cat_menu_items .= $this->cObj->substituteMarkerArrayCached($lvl1Item,$singleMarkCat1,$multiMarkCat1,array());
		}
		//t3lib_utility_Debug::debug($template.'fdsfsedf');
		$multiMark['###TOP_MENU_LVL1###'] = $cat_menu_items;
		
		return $this->cObj->substituteMarkerArrayCached($template,$singleMark,$multiMark,array());
		
		/*
		$multiMark['']
		
		
		$item = $this->cObj->getSubpart($items, '###CAT_MENU_ITEM###');
		$item_count = $this->cObj->getSubpart($item, '###PRODUCTS_COUNT###');
		$cat_menu_items = '';
		$singleMark=array();
		$multiMark=array();
		$multiMark['###CAT_MENU_ITEMS###']='';
		foreach($categories_tree as $key=>$category){
			$singleMarkCat = array();
			$multiMarkCat = array();
			$singleMarkCat['###PRODUCTS_LIST_LINK###'] = t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$category['uid']), 1, 1,$this->conf['page_products_list']);
			$singleMarkCat['###CATEGORY_CLASS###'] = 'cat_child';
			$singleMarkCat['###CATEGORY###'] = $category['display_title'];
			$multiMarkCat['###PRODUCTS_COUNT###'] = '';
			if($this->cat_level==1){$singleMarkCat['###CATEGORY_CLASS###'] = 'cat_root';}
			if($category['childs']){
				$singleMarkCat['###CATEGORY_CLASS###'] = 'cat_parrent';
				$category['sum_records_count'] = $category['records_count'];
				$this->cat_level++;
				$singleMarkCat['###CATEGORY_CHILDS###'] = $this->displayCatMenuChilds(array('childs'=>$category['childs'],'templates'=>array('template'=>$items,'item'=>$item,'item_count'=>$item_count)),$category);
				$this->cat_level--;	
			}else{
				$category['sum_records_count'] = $category['records_count'];
				$singleMarkCat['###CATEGORY_CHILDS###'] = '';
			}
			if($category['sum_records_count']>0){
				$multiMarkCat['###PRODUCTS_COUNT###'] = $this->cObj->substituteMarkerArrayCached($item_count,array('###PRODUCTS_NUM###'=>$category['sum_records_count']),array(),array());
				$cat_menu_items .= $this->cObj->substituteMarkerArrayCached($item,$singleMarkCat,$multiMarkCat,array());
			}
		}
		if($cat_menu_items){
			$multiMark['###CAT_MENU_ITEMS###']=$this->cObj->substituteMarkerArrayCached($items,array('###CATEGORY_LEVEL_CLASS###'=>$this->cat_level),array('###CAT_MENU_ITEM###'=> $cat_menu_items),array());
			return $this->cObj->substituteMarkerArrayCached($template,$singleMark,$multiMark,array());
		}else{
			return $this->pi_getLL('empty_category_menu');
		}
		*/
	}
	function displayCatMenuChildsWithProducts($arg,&$pCat){
		$cat_menu_items = '';
		foreach($arg['childs'] as $key=>$category){
			$singleMark = array();
			$singleMark['###PRODUCTS_LIST_LINK###'] = t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$category['uid']), 1, 1,$this->conf['page_products_list']);
			$singleMark['###CATEGORY_CLASS###'] = 'cat_child';
			$singleMark['###CATEGORY###'] = $category['display_title'];
			$multiMark['###CATEGORY_PRODUCTS###'] = '';
			if($category['childs']){
				$singleMark['###CATEGORY_CLASS###'] = 'cat_parrent';
				$category['sum_records_count'] = $category['records_count'];
				$this->cat_level++;
				$singleMark['###CATEGORY_CHILDS###'] = $this->displayCatMenuChildsWithProducts(array('childs'=>$category['childs'],'templates'=>$arg['templates']),$category);
				$this->cat_level--;	
			}else{
				$category['sum_records_count'] = $category['records_count'];
				$singleMark['###CATEGORY_CHILDS###'] = '';
			}
//t3lib_div::Debug($category);
			if($category['sum_records_count']>0){
				$category_records='';
				if(is_array($category['records'])){
					foreach($category['records'] as $r){
						$productSingleMark=array();
						$productSingleMark['###PRODUCT###']=$r['title'];
						$productSingleMark['###PRODUCT_SELECTED###']=($this->piVars['prod']==$r['uid'])?'selected':'';
						$productSingleMark['###PRODUCT_CATEGORY_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$category['uid'],'prod'=>$r['uid']), 1, 1,$this->conf['page_single_product']);
						$category_records.=$this->cObj->substituteMarkerArrayCached($arg['templates']['item_record'],$productSingleMark,array(),array());						
					}	
				}
				if($category_records){
					$multiMark['###CATEGORY_PRODUCTS###'] = $this->cObj->substituteMarkerArrayCached($arg['templates']['item_records'],array(),array('###PRODUCT_ITEM###'=>$category_records),array());
				}
				$pCat['sum_records_count'] += $category['sum_records_count'];
				$cat_menu_items .= $this->cObj->substituteMarkerArrayCached($arg['templates']['item'],$singleMark,$multiMark,array());
			}	
		}
		if($cat_menu_items){
			return $this->cObj->substituteMarkerArrayCached($arg['templates']['template'],array('###CATEGORY_LEVEL_CLASS###'=>$this->cat_level),array('###CAT_MENU_ITEM###'=> $cat_menu_items),array());	
		}else{
			return '';
		}
//t3lib_div::Debug($cat_menu_items);
	}
	
	function getProperties3($arg){
		$queryParts = array();
		$queryParts['SELECT'] = 'tx_easyshop_properties3.*';
		$queryParts['FROM'] = 'tx_easyshop_properties3 ';
		$queryParts['WHERE'] = 'tx_easyshop_properties3.hidden=0  AND tx_easyshop_properties3.deleted=0 ';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		$properties = array();
		if ($res) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$row['display_title']=($row['title_front'])?$row['title_front']:$row['title'];
				$properties[] = $row;
			}
		}
		return $properties;
	}
	
	function getProperties4($arg){
		$queryParts = array();
		$queryParts['SELECT'] = 'tx_easyshop_properties4.*';
		$queryParts['FROM'] = 'tx_easyshop_properties4 ';
		$queryParts['WHERE'] = 'tx_easyshop_properties4.hidden=0 AND tx_easyshop_properties4.deleted=0 ';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		$properties = array();
		if ($res) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$row['display_title']=($row['title_front'])?$row['title_front']:$row['title'];
				$properties[] = $row;
			}
		}
		return $properties;
	}
	
	
	function getAllCategories($arg){
		$queryParts = array();
		$queryParts['SELECT'] = 'tx_easyshop_categories.*';
		$queryParts['FROM'] = 'tx_easyshop_categories';
		$queryParts['WHERE'] = 'tx_easyshop_categories.deleted=0 AND tx_easyshop_categories.hidden=0';
		if($this->conf['categoryMenuSortOrder']){
			$queryParts['ORDERBY'] = 'tx_easyshop_categories.'.$this->conf['categoryMenuSortOrder'].' '.$this->conf['categoryMenuSortOrderAscDesc'];	
		}
		$recordsCount=array();
		if($arg['records_count']){
			$queryPartsCount['SELECT'] = 'tx_easyshop_products_categories_mm.uid_foreign, COUNT(tx_easyshop_products_categories_mm.uid_foreign) AS records_count';
			$queryPartsCount['FROM'] = 'tx_easyshop_products LEFT JOIN tx_easyshop_products_categories_mm ON tx_easyshop_products_categories_mm.uid_local=tx_easyshop_products.uid';
			$queryPartsCount['WHERE'] = 'tx_easyshop_products.deleted=0 AND tx_easyshop_products.hidden=0';
			if($this->conf['language']){
				$queryPartsCount['FROM'] .= ' LEFT JOIN tx_easyshop_language_overlay ON tx_easyshop_products.uid=tx_easyshop_language_overlay.overlay_parrent';
				$queryPartsCount['WHERE'] .= ' AND tx_easyshop_language_overlay.overlay_language='.$this->conf['language'].' AND tx_easyshop_language_overlay.hidden=0  AND tx_easyshop_language_overlay.deleted=0 ';
			}
			$queryPartsCount['GROUPBY'] = 'tx_easyshop_products_categories_mm.uid_foreign';
			$resCount = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryPartsCount);
			if ($resCount) {
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resCount)){
					$recordsCount[$row['uid_foreign']] = $row['records_count'];
				}	
				$GLOBALS['TYPO3_DB']->sql_free_result($resCount);
			}
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		$categories = array();
		if ($res) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$row['display_title']=($row['title_front'])?$row['title_front']:$row['title'];
				if($this->conf['language']){
					$row=$this->categoryLanguageOverlay($row);
				}
				if($arg['records_count']){
					$row['records_count']=$recordsCount[$row['uid']];
				}
				$categories[] = $row;
			}	
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}
//t3lib_div::Debug($categories);
		if(!$this->conf['categoryMenuSortOrder']){
			usort($categories, 'compareCategoriesByDisplayTitle');	
		}
//t3lib_div::Debug($categories);			
		if($this->conf['categories_display_type']==1){
			return $categories;	
		}else{
			$filtered_categories=array();
			if($this->conf['root_categories']){
				$root_categories = explode(',',$this->conf['root_categories']);
					foreach($categories as $category){
						if(!$category['parrent']){//Root category
							if($this->conf['categories_display_type']==2){//Exclude categories
								if(!in_array($category['uid'],$root_categories)){
									$filtered_categories[]=$category;	
								}
							}else{//Display categories
								if(in_array($category['uid'],$root_categories)){
									$filtered_categories[]=$category;	
								}	
							}
						}else{
							$filtered_categories[]=$category;
						}
					}
			}
			return $filtered_categories;
		}
	}
	function getAllCategoriesWithDiscount(){
		$queryParts = array();
		$queryParts['SELECT'] = 'uid, discount, discount_type';
		$queryParts['FROM'] = 'tx_easyshop_categories';
		$queryParts['WHERE'] = 'tx_easyshop_categories.deleted=0 AND tx_easyshop_categories.hidden=0';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		if ($res) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$this->allCategories[$row['uid']] = $row;
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}
	}
	function getCategory($uid){
		$queryParts = array();
		$queryParts['SELECT'] = '*';
		$queryParts['FROM'] = 'tx_easyshop_categories';
		$queryParts['WHERE'] = 'tx_easyshop_categories.uid='.$uid;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		if ($res) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			return $row;
		}
	}
	function getAllCategoriesWithProducts($arg){
		$queryParts=$recInCat=$products=$categoryRecords=array();
		$queryParts['SELECT'] = 'tx_easyshop_categories.*';
		$queryParts['FROM'] = 'tx_easyshop_categories';
		$queryParts['WHERE'] = 'tx_easyshop_categories.deleted=0 AND tx_easyshop_categories.hidden=0';
		if($this->conf['categoryMenuSortOrder']){
			$queryParts['ORDERBY'] = 'tx_easyshop_categories.'.$this->conf['categoryMenuSortOrder'].' '.$this->conf['categoryMenuSortOrderAscDesc'];	
		}
		$queryPartsRecords['SELECT'] = 'tx_easyshop_products_categories_mm.uid_foreign, tx_easyshop_products_categories_mm.uid_local';
		$queryPartsRecords['FROM'] = 'tx_easyshop_products LEFT JOIN tx_easyshop_products_categories_mm ON tx_easyshop_products_categories_mm.uid_local=tx_easyshop_products.uid';
		$queryPartsRecords['WHERE'] = 'tx_easyshop_products.deleted=0 AND tx_easyshop_products.hidden=0 AND tx_easyshop_products_categories_mm.uid_local AND tx_easyshop_products_categories_mm.uid_foreign';
		if($this->conf['language']){
			$queryPartsRecords['FROM'] .= ' LEFT JOIN tx_easyshop_language_overlay ON tx_easyshop_products.uid=tx_easyshop_language_overlay.overlay_parrent';
			$queryPartsRecords['WHERE'] .= ' AND tx_easyshop_language_overlay.overlay_language='.$this->conf['language'].' AND tx_easyshop_language_overlay.hidden=0  AND tx_easyshop_language_overlay.deleted=0 ';
		}
		$resProd = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryPartsRecords);
		if ($resProd) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resProd)){
				$recInCat[$row['uid_foreign']][$row['uid_local']] = $row['uid_local'];
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($resProd);
		}
		if($recInCat){
			$params=array();
			$params['cat']=implode(',',array_keys($recInCat));
			$params['no_pagination']=1;
			$products = $this->getProducts($params);
			foreach($recInCat as $catUid=>$r){
				foreach($r as $pUid=>$p){
					$categoryRecords[$catUid][$products['prod_pos'][$pUid]]=$products['products'][$products['prod_pos'][$pUid]];
				}
				ksort($categoryRecords[$catUid]);
			}
			unset($products);
			unset($recInCat);
		}
//t3lib_div::Debug($categoryRecords);
//exit();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		$categories = array();
		if ($res) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$row['display_title']=($row['title_front'])?$row['title_front']:$row['title'];
				if($this->conf['language']){
					$row=$this->categoryLanguageOverlay($row);
//t3lib_div::Debug($row);
				}
				$row['records']=$categoryRecords[$row['uid']];
				$row['records_count']=count($categoryRecords[$row['uid']]);
				$categories[] = $row;
			}	
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}
		if(!$this->conf['categoryMenuSortOrder']){
			usort($categories, 'compareCategoriesByDisplayTitle');	
		}
		if($this->conf['categories_display_type']==1){
			return $categories;	
		}else{
			$filtered_categories=array();
			if($this->conf['root_categories']){
				$root_categories = explode(',',$this->conf['root_categories']);
					foreach($categories as $category){
						if(!$category['parrent']){//Root category
							if($this->conf['categories_display_type']==2){//Exclude categories
								if(!in_array($category['uid'],$root_categories)){
									$filtered_categories[]=$category;	
								}
							}else{//Display categories
								if(in_array($category['uid'],$root_categories)){
									$filtered_categories[]=$category;	
								}	
							}
						}else{
							$filtered_categories[]=$category;
						}
					}
			}
			return $filtered_categories;
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
		$products =$products_overlay=$queryParts=$prod_pos=array();
		
		if(!$arg['special'] && !$arg['connectedUids']){	
			$queryParts['SELECT'] = 'DISTINCT tx_easyshop_products.*';
			if($arg['prop']) {
				$queryParts['FROM'] = 'tx_easyshop_products LEFT JOIN tx_easyshop_products_properties_mm ON tx_easyshop_products.uid=tx_easyshop_products_properties_mm.uid_local';
				$queryParts['FROM'] .= ' LEFT JOIN tx_easyshop_products_categories_mm ON tx_easyshop_products.uid=tx_easyshop_products_categories_mm.uid_local';
				$queryParts['WHERE'] = 'tx_easyshop_products.deleted=0 AND tx_easyshop_products.hidden=0 AND tx_easyshop_products_properties_mm.uid_foreign IN ('.$arg['prop'].')';
				$queryParts['WHERE'] .= ' AND tx_easyshop_products_categories_mm.uid_foreign IN ('.$arg['cat'].')';			
				}
			else if($arg['prop4']) {
				$queryParts['FROM'] = 'tx_easyshop_products LEFT JOIN tx_easyshop_products_properties4_mm ON tx_easyshop_products.uid=tx_easyshop_products_properties4_mm.uid_local';
				$queryParts['WHERE'] = 'tx_easyshop_products.deleted=0 AND tx_easyshop_products.hidden=0 AND tx_easyshop_products_properties4_mm.uid_foreign IN ('.$arg['prop4'].')';
			} else {
				$queryParts['FROM'] = 'tx_easyshop_products LEFT JOIN tx_easyshop_products_categories_mm ON tx_easyshop_products.uid=tx_easyshop_products_categories_mm.uid_local';
				$queryParts['WHERE'] = 'tx_easyshop_products.deleted=0 AND tx_easyshop_products.hidden=0 AND tx_easyshop_products_categories_mm.uid_foreign IN ('.$arg['cat'].')';
			}
		} else {
			$queryParts['SELECT'] = 'tx_easyshop_products.*';
			$queryParts['FROM'] = 'tx_easyshop_products ';
			$queryParts['WHERE'] = 'tx_easyshop_products.deleted=0 AND tx_easyshop_products.hidden=0 ';
			switch($arg['special']) {
				case 'action':
					$queryParts['WHERE'] .= ' AND tx_easyshop_products.action_product = 1 ';
					break;
				case 'selected':
					$queryParts['WHERE'] .= ' AND tx_easyshop_products.selected_product = 1 ';
					break;
				case 'mostsold':
					$queryParts['WHERE'] .= ' AND tx_easyshop_products.mostsold_product = 1 ';
					break;
			}
			
		}
		
		if($arg['connectedUids']){	
			$queryParts['WHERE'] .= ' AND tx_easyshop_products.uid IN ('.$arg['connectedUids'].')';
		}
		
		
		$queryParts['WHERE'] .= ' AND tx_easyshop_products.starttime < '.time().' AND (tx_easyshop_products.endtime=0 OR tx_easyshop_products.endtime > '.time().')';
		
		$queryParts['ORDERBY'] = 'tx_easyshop_products.'.$arg['order_by'].' '.$arg['asc_desc'];
		
//t3lib_div::Debug($queryParts);		
		if($this->conf['language']){
			
			//Left Join added for sorting product overlay
			$queryParts['SELECT'] .= ', tx_easyshop_language_overlay.overlay_'.implode(', tx_easyshop_language_overlay.overlay_',$this->select_fields_array);
			$queryParts['FROM'] .= ' LEFT JOIN tx_easyshop_language_overlay ON tx_easyshop_products.uid=tx_easyshop_language_overlay.overlay_parrent';
			$queryParts['WHERE'] .= ' AND tx_easyshop_language_overlay.overlay_language='.$this->conf['language'].' AND tx_easyshop_language_overlay.hidden=0  AND tx_easyshop_language_overlay.deleted=0 ';
			if(in_array($arg['order_by'],$this->select_fields_array)){
				$queryParts['ORDERBY'] = 'tx_easyshop_language_overlay.overlay_'.$arg['order_by'].' '.$arg['asc_desc'];	
			}
			
			$queryPartsLan['SELECT'] = 'tx_easyshop_language_overlay.overlay_'.implode(', tx_easyshop_language_overlay.overlay_',$this->select_fields_array);
			$queryPartsLan['FROM'] = 'tx_easyshop_language_overlay';
			$queryPartsLan['WHERE'] = 'tx_easyshop_language_overlay.overlay_language='.$this->conf['language'].' AND tx_easyshop_language_overlay.hidden=0  AND tx_easyshop_language_overlay.deleted=0 ';
			$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryPartsLan);
			if($res){
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
					$products_overlay[$row['overlay_parrent']]=$row;
				}
			}
		}
		
/*
		if($arg['prop3']){
			//$arg['properties']='1,3';
			//Used for product filter
			$queryParts['SELECT'] .= ', GROUP_CONCAT(tx_easyshop_products_properties3_mm.uid_foreign) as grouped_properties';
			$queryParts['FROM'] .= ' LEFT JOIN tx_easyshop_products_properties3_mm ON tx_easyshop_products.uid=tx_easyshop_products_properties3_mm.uid_local';
			$queryParts['GROUPBY']='tx_easyshop_products_properties3_mm.uid_local';	
		}
		
		if($arg['prop4']){
			//$arg['properties']='1,3';
			//Used for product filter
			$queryParts['SELECT'] .= ', GROUP_CONCAT(tx_easyshop_products_properties4_mm.uid_foreign) as grouped_properties';
			$queryParts['FROM'] .= ' LEFT JOIN tx_easyshop_products_properties4_mm ON tx_easyshop_products.uid=tx_easyshop_products_properties4_mm.uid_local';
			$queryParts['GROUPBY']='tx_easyshop_products_properties4_mm.uid_local';	
		}
t3lib_utility_Debug::debug($queryParts);
*/

		//t3lib_utility_Debug::debug($queryParts);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		if($arg['special'] || $arg['next_prev'] || $arg['connectedUids']) {
			if ($res) {
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
					$specialItems[] = $row;
				}
			}
			return $specialItems;
		}
		
		
		$p_count=0;
		$p_keys_list='';
		
		if ($res){
			$i=$page=1;
			if($arg['properties']){//Products selected by properties
				$properties = explode(',',$arg['properties']);
				if($this->conf['language']){
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
	//t3lib_div::Debug($row);
						if(!empty($products_overlay[$row['uid']])&&$this->arrayInArray($properties,explode(',',$row['grouped_properties']))){
							$this->productSpecificFieldsFormats($row);
							if($i>$this->conf['pageBrowser.']['resultsAtATime']){
								$page++;
								$i=1;
							}
							$row=$this->productLanguageOverlay($row,$products_overlay[$row['uid']]);
							if($arg['no_pagination']){$products[]=$row;}
							else{$products[$page][]=$row;}
							if($p_count==0){$p_keys_list=$row['uid'];}
							else{$p_keys_list.=','.$row['uid'];}
							$prod_pos[$row['uid']]=$p_count++;
							$i++;	
						}
					}
				}else{
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
						if($this->arrayInArray($properties,explode(',',$row['grouped_properties']))){
							$this->productSpecificFieldsFormats($row);
							if($i>$this->conf['pageBrowser.']['resultsAtATime']){
								$page++;
								$i=1;
							}
							if($arg['no_pagination']){$products[]=$row;}
							else{$products[$page][]=$row;}
							if($p_count==0){$p_keys_list=$row['uid'];}
							else{$p_keys_list.=','.$row['uid'];}
							$prod_pos[$row['uid']]=$p_count++;
							$i++;	
						}
					}
				}
			}else{
				if($this->conf['language']){
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
						if(!empty($products_overlay[$row['uid']])){
							$this->productSpecificFieldsFormats($row);
							if($i>$this->conf['pageBrowser.']['resultsAtATime']){
								$page++;
								$i=1;
							}
							$row=$this->productLanguageOverlay($row,$products_overlay[$row['uid']]);
							if($arg['no_pagination']){$products[]=$row;}
							else{$products[$page][]=$row;}
							if($p_count==0){$p_keys_list=$row['uid'];}
							else{$p_keys_list.=','.$row['uid'];}
							$prod_pos[$row['uid']]=$p_count++;
							$i++;
						}
					}
				}else{
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
						$this->productSpecificFieldsFormats($row);
						if($i>$this->conf['pageBrowser.']['resultsAtATime']){
							$page++;
							$i=1;
						}
						if($arg['no_pagination']){$products[]=$row;}
						else{$products[$page][]=$row;}
						if($p_count==0){$p_keys_list=$row['uid'];}
						else{$p_keys_list.=','.$row['uid'];}
						$prod_pos[$row['uid']]=$p_count++;
						$i++;
					}	
				}
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}
		t3lib_utility_Debug::debug('aaa');
		return array('products'=>$products,'count'=>$p_count, 'keys_list'=>$p_keys_list, 'prod_pos'=>$prod_pos);
	}
	function getProduct($arg){
		$queryParts=$product=$productOverlay=$queryPartsOverlay=array();
		$queryParts['SELECT'] = 'tx_easyshop_products.*';
		$queryParts['FROM'] = 'tx_easyshop_products';
		$queryParts['WHERE'] = 'tx_easyshop_products.uid='.$arg['uid'].' AND tx_easyshop_products.hidden=0  AND tx_easyshop_products.deleted=0 ';
		$product = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts));
		$this->productSpecificFieldsFormats($product);
		/*
		$product['price']=$this->moneyFormat($product['price']);
		if($product['discount_type']!=2){
			$product['catDiscount']=$this->getProductCategoryDiscount($product['discount_category']);
			$product['discount']=$this->getProductDiscount($product);	
		}else{
			$product['discount']=$product['discount'];
		}
		$product['web_price']=$this->getProductWebPrice($product['price'],$product['web_price'],$product['discount']);
		*/
		if($this->conf['language']){
			$queryPartsOverlay['SELECT'] = 'tx_easyshop_language_overlay.overlay_'.implode(', tx_easyshop_language_overlay.overlay_',$this->select_fields_array);
			$queryPartsOverlay['FROM'] = 'tx_easyshop_language_overlay';
			$queryPartsOverlay['WHERE'] = 'tx_easyshop_language_overlay.overlay_language='.$this->conf['language'].' AND tx_easyshop_language_overlay.hidden=0  AND tx_easyshop_language_overlay.deleted=0 AND tx_easyshop_language_overlay.overlay_parrent='.$arg['uid'];
			$productOverlay = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryPartsOverlay));
			$product=$this->productLanguageOverlay($product,$productOverlay);
		}
//t3lib_div::Debug($properties);
		return $product;
	}
	function getProductCategoryDiscount($cUid){
		$disVal=0;
		$disType=0;
		
		if($cUid){
			if(count($this->allCategories)==0){$this->getAllCategoriesWithDiscount();}
			if(@$this->allCategories[$cUid]){
				$disVal=$this->allCategories[$cUid]['discount'];
				$disType=$this->allCategories[$cUid]['discount_type'];
//t3lib_div::Debug($this->allCategories[$cUid]);				
			}
		}
		return array('value'=>$disVal,'type'=>$disType);
	}
	function getProductCategories($arg){
		$queryParts = array();
		$queryParts['SELECT'] = 'tx_easyshop_categories.*';
		$queryParts['FROM'] = 'tx_easyshop_products_categories_mm LEFT JOIN tx_easyshop_categories ON tx_easyshop_products_categories_mm.uid_foreign=tx_easyshop_categories.uid';
		$queryParts['WHERE'] = 'tx_easyshop_products_categories_mm.uid_local='.$arg['prod_uid'].' AND tx_easyshop_categories.hidden=0  AND tx_easyshop_categories.deleted=0 ';
		$queryParts['ORDERBY'] = 'tx_easyshop_categories.sorting ASC';
	//t3lib_utility_Debug::debug($queryParts);
		//$queryParts['ORDERBY'] = 'tx_easyshop_products_categories_mm.uid asc';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		$categories = array();
		if ($res) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$row['display_title']=($row['title_front'])?$row['title_front']:$row['title'];
				if($this->conf['language']){
					$title_overlays=explode('|',$row['title_overlays']);
					$title_overlay=$title_overlays[$this->languages[$this->conf['language']]];
					if($title_overlay){
						$row['display_title']=$title_overlay;	
					}
				}
				$categories[] = $row;
			}
		}
//t3lib_div::Debug($properties);
		return $categories;
	}	
	function getProductProperties($arg){
		$queryParts = array();
		$queryParts['SELECT'] = 'tx_easyshop_properties.*';
		$queryParts['FROM'] = 'tx_easyshop_products_properties_mm LEFT JOIN tx_easyshop_properties ON tx_easyshop_products_properties_mm.uid_foreign=tx_easyshop_properties.uid';
		$queryParts['WHERE'] = 'tx_easyshop_products_properties_mm.uid_local='.$arg['prod_uid'].' AND tx_easyshop_properties.hidden=0  AND tx_easyshop_properties.deleted=0 ';
		//t3lib_utility_Debug::debug($queryParts);
		//t3lib_div::Debug($queryParts);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		$properties = array();
		if ($res) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$row['display_title']=($row['title_front'])?$row['title_front']:$row['title'];
				if($this->conf['language']){
					$title_overlays=explode('|',$row['title_overlays']);
					$title_overlay=$title_overlays[$this->languages[$this->conf['language']]];
					if($title_overlay){
						$row['display_title']=$title_overlay;	
					}
				}
				$properties[] = $row;
			}
		}
//t3lib_div::Debug($properties);
		return $properties;
	}
	function getProperties($arg){
		
		$products_properties=array();
		$queryParts = array();
		$queryParts['SELECT'] = 'tx_easyshop_products_properties_mm.uid_foreign';
		$queryParts['FROM'] = 'tx_easyshop_products_properties_mm';
		$queryParts['WHERE'] = 'tx_easyshop_products_properties_mm.uid_local IN ('.$arg['products'].')';
		$queryParts['GROUPBY'] = 'tx_easyshop_products_properties_mm.uid_foreign';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		if ($res) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$products_properties[$row['uid_foreign']] = 1;
			}
		}
		$queryParts = array();
		$queryParts['SELECT'] = 'tx_easyshop_properties.*';
		$queryParts['FROM'] = 'tx_easyshop_properties';
		$queryParts['WHERE'] = 'tx_easyshop_properties.deleted=0 AND tx_easyshop_properties.hidden=0';
		$queryParts['ORDERBY'] = 'tx_easyshop_properties.title ASC';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		$properties = array();
		if ($res) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$row['display_title']=($row['title_front'])?$row['title_front']:$row['title'];
				$row['has_products']=$products_properties[$row['uid']];
				if($this->conf['language']){
					$title_overlays=explode('|',$row['title_overlays']);
					$title_overlay=$title_overlays[$this->languages[$this->conf['language']]];
					if($title_overlay){
						$row['display_title']=$title_overlay;	
					}
				}
				$properties[] = $row;
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}
		return $properties;
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
	function generatePathTrack($cObj,$categories,$tpl){
		$singleMark['###CAT_TITLE###']=$cObj['display_title'];
		$singleMark['###CAT_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$cObj['uid']), 1, 1,$this->conf['page_products_list']);
		$pathTrack = $this->cObj->substituteMarkerArrayCached($tpl,$singleMark,array(),array());
		//$pathTrack=$cObj['display_title'];
		if($cObj['parrent']){
			//$pathTrack=$this->conf['categoryPathTrackDelimiter'].$cObj['display_title'];
			//$singleMark['###CAT_TITLE###']=$this->conf['categoryPathTrackDelimiter'].$cObj['display_title'];
			$pathTrack = $this->cObj->substituteMarkerArrayCached($tpl,$singleMark,array(),array());
			foreach($categories as $category){
				if($cObj['parrent']==$category['uid']){
					$pathTrack=$this->generatePathTrack($category,$categories,$tpl).$pathTrack;					
					break;
				}
			}
		}
		return $pathTrack;
	}
	function productLanguageOverlay($product,$overlay){
		if($overlay['overlay_title']){$product['title']=$overlay['overlay_title'];}
		if($overlay['overlay_subtitle']){$product['subtitle']=$overlay['overlay_subtitle'];}
		if($overlay['overlay_measure_unit']){$product['measure_unit']=$overlay['overlay_measure_unit'];}
		if($overlay['overlay_description']){$product['description']=$overlay['overlay_description'];}
		if($overlay['overlay_images']){
			$product['images']=$overlay['overlay_images'];
			$product['images_captions']=$overlay['overlay_images_captions'];
		}		
		if($overlay['overlay_files']){$product['files']=$overlay['overlay_files'];}
		return $product;
	}
	function categoryLanguageOverlay($row){
		$title_overlays=explode('|',$row['title_overlays']);
		$title_overlay=$title_overlays[$this->languages[$this->conf['language']]];
		if($title_overlay){
			$row['display_title']=$title_overlay;	
		}
		
		$description_overlays=explode('|',$row['description_overlays']);
		$description_overlay=$description_overlays[$this->languages[$this->conf['language']]];
		if($description_overlay){
			$description=$this->getTTcontent($description_overlay);
			$row['description']=$description['bodytext'];
		}
		return $row;
	}
	function propertyChilds(&$parrent,$properties){
		$childs = array();
		$parrent['all_child_list']=array();
		foreach($properties as $key=>$p){
			if($p['parrent']==$parrent['uid']){
				if($p['has_products']){
					$parrent['child_has_products']=1;
					$p['child_has_products']=1;
				}
				$p['childs'] = $this->propertyChilds($p,$properties);
				$parrent['all_child_list']=array_merge($parrent['all_child_list'], $p['all_child_list']);
				if($p['child_has_products']){
					$parrent['child_has_products']=1;
					$p['child_has_products']=1;
				}
				$parrent['all_child_list'][]=$p['uid'];
				$childs[] = $p;
			}
		}
		return $childs;
	}
	

	function getProductProperties2($arg){
		$queryParts = array();
		$queryParts['SELECT'] = 'tx_easyshop_properties2.*, tx_easyshop_products_properties2_mm.sorting as propOrder';
		$queryParts['FROM'] = 'tx_easyshop_products_properties2_mm LEFT JOIN tx_easyshop_properties2 ON tx_easyshop_products_properties2_mm.uid_foreign=tx_easyshop_properties2.uid';
		$queryParts['WHERE'] = 'tx_easyshop_products_properties2_mm.uid_local='.$arg['prod_uid'].' AND tx_easyshop_properties2.hidden=0  AND tx_easyshop_properties2.deleted=0 ';
		$queryParts['ORDERBY'] = 'propOrder ASC';
//t3lib_utility_Debug::debug($queryParts);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		$properties = array();
		if ($res) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$row['display_title']=($row['title_front'])?$row['title_front']:$row['title'];
				/*if($this->conf['language']){
					$title_overlays=explode('|',$row['title_overlays']);
					$title_overlay=$title_overlays[$this->languages[$this->conf['language']]];
					if($title_overlay){
						$row['display_title']=$title_overlay;	
					}
				}*/
				$properties[] = $row;
			}
		}
//t3lib_div::Debug($properties);
		return $properties;
	}
	
	function getProductsProperties3($uids){
		$queryParts = array();
		$queryParts['SELECT'] = 'DISTINCT tx_easyshop_properties3.*';
		$queryParts['FROM'] = 'tx_easyshop_products_properties3_mm LEFT JOIN tx_easyshop_properties3 ON tx_easyshop_products_properties3_mm.uid_foreign=tx_easyshop_properties3.uid';
		$queryParts['WHERE'] = 'tx_easyshop_products_properties3_mm.uid_local IN ('.$uids.') AND tx_easyshop_properties3.hidden=0  AND tx_easyshop_properties3.deleted=0 ';
		$queryParts['ORDERBY'] = 'tx_easyshop_properties3.sorting ASC';
//t3lib_utility_Debug::debug($queryParts);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		$properties = array();
		if ($res) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$row['display_title']=($row['title_front'])?$row['title_front']:$row['title'];
				$properties[] = $row;
			}
		}
//t3lib_div::Debug($properties);
		return $properties;
	}
	
	function categoryTopParent($child,$categories){
	//t3lib_utility_Debug::debug($child);
		//$childs = array();
		if($child['parrent']) {
			foreach($categories as $category){
				if($category['uid']==$child['parrent']){
				
					$child = $this->categoryTopParent($category,$categories);
				}
				
			}
		}
		//t3lib_utility_Debug::debug($child);
		return $child;
	}	
	
	function categoryChilds($parrent,$categories){
		$childs = array();
		foreach($categories as $category){
			if($category['parrent']==$parrent){
				$category['childs'] = $this->categoryChilds($category['uid'],$categories);
				$childs[] = $category;	
			}
		}
		return $childs;
	}
	function categoryChildsList($parrent, $noParent = false){
		$catList = ($noParent) ? '' : $parrent;		
		foreach($this->allCategoriesSorted as $c){
			if($c['parrent']==$parrent){
				$catList .= ','.$this->categoryChildsList($c['uid']);	
			}
		}
		return $catList;
	}
	function categoryChildsListNew($parrent,&$catArray=array()){
		$catArray[$parrent['uid']]=$parrent;
		foreach($this->allCategoriesSorted as $c){
			if($c['parrent']==$parrent['uid']){
				$this->categoryChildsList($c,$catArray);
			}
		}
		return $catArray;
	}
	function dataArrayKey($dataArray,$key){
		$dataArrayKey=array();
		foreach($dataArray as $d){
			$dataArrayKey[]=$d[$key];		
		}
		return $dataArrayKey;
	}
	function arrayInArray($needles,$haystack){
		foreach($needles as $needle){
			if(!in_array($needle,$haystack))
				return false;
		}
		return true;
	}
	function productSpecificFieldsFormats(&$p){
		//$p['price']=$this->moneyFormat($p['price']);
		//$p['price_partner']=$this->moneyFormat($p['price_partner']);
		if($p['discount_type']!=2){
			$p['catDiscount']=$this->getProductCategoryDiscount($p['discount_category']);
			$p['discount']=$this->getProductDiscount($p);	
		}else{
			$p['discount']=$p['discount'];
		}
		//$p['web_price']=$this->getProductWebPrice($p['price'],$p['web_price'],$p['discount']);
		$p['stock']-=$this->productsBasketStockSession[$p['uid']];
	}
	function productImages($arg){
		$images='';
		$imgConfig = $thumbConfig =  array();
        $imgConfig['file.']['maxW'] = $this->conf['image.']['maxW'];
        $imgConfig['file.']['maxH'] = $this->conf['image.']['maxH'];
        if($this->conf['images.']['class']){$imgConfig['params'] = 'class="'.$this->conf['image.']['class'].'"';}
        $thumbConfig['file.']['maxW'] = $this->conf['thumbnail.']['maxW'];
        $thumbConfig['file.']['maxH'] = $this->conf['thumbnail.']['maxH'];
        if($this->conf['images.']['class']){$thumbConfig['params'] = 'class="'.$this->conf['thumbnail.']['class'].'"';}
		foreach($arg['images'] as $k=>$img){
			$image=array();
			if($arg['templates'][$k+1]){
				$image['###IMAGE_ORIGINAL###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').'uploads/tx_easyshop/'.$img;
				$image['###PRODUCT_UID###']=$arg['product_uid'];
				$image['###IMAGE_POS###']=$k+1;
				$image['###CAPTION###']=$arg['captions'][$k];
				$imgConfig['altText'] = '';
				$imgConfig['titleText'] = '';
				$thumbConfig['altText'] = '';
				$thumbConfig['titleText'] = '';
				if($image['###CAPTION###']){
					$imgConfig['altText'] = $image['###CAPTION###'];
    				$imgConfig['titleText'] = $image['###CAPTION###'];
    				$thumbConfig['altText'] = $image['###CAPTION###'];
    				$thumbConfig['titleText'] = $image['###CAPTION###'];
				}
				$imgConfig['file'] = 'uploads/tx_easyshop/'.$img;
				$image['###IMAGE###']=$this->cObj->IMAGE($imgConfig);
				$thumbConfig['file'] = 'uploads/tx_easyshop/'.$img;
				$image['###IMAGE_THUMBNAIL###']=$this->cObj->IMAGE($thumbConfig);
				$images.=$this->cObj->substituteMarkerArrayCached($arg['templates'][$k+1],$image,array(),array()); 
			}
		}
		return $images;
	}
	function productFiles($arg){
		if($arg['template']){
			if ($arg['files']){
				$fileArr = explode(',', $arg['files']);
				$files = $filelinks = $rss2Enclousres = '';
				$this->conf['file.']['path']='uploads/tx_easyshop/';
	//t3lib_div::Debug($this->conf['file.']);
				while (list(, $val) = each($fileArr)) {
					$filelinks .= $this->cObj->filelink($val, $this->conf['file.']) ;
					// <enclosure> support for RSS 2.0
					if($this->theCode == 'XML') {
						$path    = trim($this->conf['file.']['path']);
						$theFile = $path.$val;
						if (@is_file($theFile))	{
							$fileURL      = $this->config['siteUrl'].$theFile;
							$fileSize     = filesize($theFile);
							$fileMimeType = t3lib_htmlmail::getMimeType($fileURL);
							$rss2Enclousres .= '<enclosure url="'.$fileURL.'" ';
							$rss2Enclousres .= 'length ="'.$fileSize.'" ';
							$rss2Enclousres .= 'type="'.$fileMimeType.'" />'."\n\t\t\t";
						}
						$markerArray['###NEWS_RSS2_ENCLOSURES###'] = trim($rss2Enclousres);
					}
				}		
			}
			if($filelinks){
				return $this->cObj->substituteMarkerArrayCached($arg['template'],array('###FILES###'=>$filelinks),array(),array());
			}	
		}
		return '';
	}
	function loadTemplate($marker){
		if($this->conf['templateFile']){
			return $this->cObj->getSubpart($this->cObj->fileResource($this->conf['templateFile']), $marker);
		}		
	}
	
	function getProperty($uid) {
		$queryParts['SELECT'] = "tx_easyshop_properties.*";
		$queryParts['FROM'] = "tx_easyshop_properties";
		$queryParts['WHERE'] = "tx_easyshop_properties.uid=".$uid;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		if ($res){
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			return $row;
		}
		else{
			return false;
		}
	}
	
	function getProperty2($uid) {
		$queryParts['SELECT'] = "tx_easyshop_properties2.*";
		$queryParts['FROM'] = "tx_easyshop_properties2";
		$queryParts['WHERE'] = "tx_easyshop_properties2.uid=".$uid;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		if ($res){
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			return $row;
		}
		else{
			return false;
		}
	}
	
	function productHasPrice($uid){
		$queryParts['SELECT'] = 'title,price,web_price,price_partner,web_price_partner,price_prop2,price_disc_prop2';
		$queryParts['FROM'] = 'tx_easyshop_products';
		$queryParts['WHERE'] = 'uid='.$uid;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		if ($res) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$this->productSpecificFieldsFormats($row);
			return $row;
		}
		return false;
	}
	function productOverlayCompare($a,$b){
		if($this->ascDesc=='ASC'){
			return strcasecmp($a[$this->sortBy],$b[$this->sortBy]);	
		}else{
			return !strcasecmp($a[$this->sortBy],$b[$this->sortBy]);
		}
	}
	function getProductDiscount($prod){
		$dis=$prod['discount'];
		$disType=$prod['discount_type'];
//t3lib_div::Debug(array($dis,$disType));
//t3lib_div::Debug($prod['catDiscount']);

		if($prod['catDiscount']['type']==2){
			$dis=$prod['catDiscount']['value'];
		}else{
			if($GLOBALS['TSFE']->loginUser){
				if($this->feUserDiscount['type']==2){
					$dis=$this->feUserDiscount['value'];
//t3lib_div::Debug($dis);
//exit();					
				}else{
					if($this->feUserDiscount['type']==3){
						$dis+=$this->feUserDiscount['value'];
					}else{
						if($prod['discount_type']==1){
							if($this->feUserDiscount['value']>$dis){
								$dis=$this->feUserDiscount['value'];	
							}
						}else{
							$dis+=$this->feUserDiscount['value'];
						}
					}
					
					if($prod['catDiscount']['type']==3){
						$dis+=$prod['catDiscount']['value'];
					}else{
						
						if($prod['discount_type']==1){
							if($prod['catDiscount']['value']>$dis){
								$dis=$prod['catDiscount']['value'];	
							}
						}else{
							$dis+=$prod['catDiscount']['value'];
						}
					}	
				}
			}else{
				if($prod['catDiscount']['type']==3){
					$dis+=$prod['catDiscount']['value'];
				}else{
					if($prod['discount_type']==1){
						if($prod['catDiscount']['value']>$dis){
							$dis=$prod['catDiscount']['value'];	
						}
					}else{
						$dis+=$prod['catDiscount']['value'];
					}
				}
			}	
		}
		return intval($dis);
	}
	function getProductWebPrice($price,$web_price,$dis){
		$price=str_replace(',','.',$price);
		$web_price=str_replace(',','.',$web_price);
		$moneyFormat=floatval($price);
//t3lib_div::Debug($price);
		if($web_price>0){
			$moneyFormat=number_format(round($web_price, 2), 2, ',', '.');
			//$moneyFormat=round($web_price, 2);
		}		
		else{
			if($dis){$price-=$price*$dis/100;}
			$moneyFormat=number_format(round($price, 2), 2, ',', '.');
			//$moneyFormat=round($price, 2);
		}
		//t3lib_utility_Debug::debug($moneyFormat);
		return $moneyFormat;
	}
	function moneyFormat($mString){
		$moneyFormat=$mString;
		if($mString){
//t3lib_div::Debug($mString);
			$tmp=str_replace(',','.',$mString);
			$moneyFormat=number_format($tmp, 2, ',', '.');	
		}
		return $moneyFormat;
	}
	function getAllUsersBasketSession(){
		$queryParts['SELECT'] = 'content';
		$queryParts['FROM'] = 'fe_session_data';
		$queryParts['WHERE'] = 'tstamp>'.(time()-1*3600);//records newer than 2 h
//t3lib_div::Debug($queryParts);
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
		if ($res){
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$ses_dat=unserialize($row['content']);
				if(count($ses_dat['web_shop']['session']['products'])>0){
					foreach($ses_dat['web_shop']['session']['products'] as $p_k=>$p_v){
						$this->productsBasketStockSession[$p_k]=$this->productsBasketStockSession[$p_k]+$p_v['quantity'];
					}	
				}
			}
		}
	}
	function deleteUserSession(){
		$GLOBALS["TSFE"]->fe_user->setKey('ses','web_shop',NULL);
		$GLOBALS["TSFE"]->storeSessionData();
		//t3lib_div::Debug($GLOBALS["TSFE"]->fe_user->id);
//		$GLOBALS['TYPO3_DB']->exec_DELETEquery('fe_session_data', 'hash='.$GLOBALS['TYPO3_DB']->fullQuoteStr($GLOBALS["TSFE"]->fe_user->id, 'fe_session_data'));
	}
	
	function getCE($id)
	{
		$conf['tables'] = 'tt_content';
		$conf['source'] = $id;
		$conf['dontCheckPid'] = 1;
		return $this->cObj->RECORDS($conf); 
	}
	
	function ajaxCall($content,$conf){
		if($this->piVars['addToBasket']){
			$this->conf=$conf;
			$this->pi_setPiVarDefaults();
			$this->init($conf);
			$basket_session = $GLOBALS["TSFE"]->fe_user->getKey('ses','web_shop');
			if(!is_array($basket_session['session']['products'])){$basket_session['session']['products']=array();}

			$products_keys=array_keys($basket_session['session']['products']);
			$p=$this->productHasPrice($this->piVars['addToBasket']);
			
			$productProperties2 = $this->getProductProperties2(array('prod_uid'=>$this->piVars['addToBasket']));
			$position = 0;
			for($i=0; $i < count($productProperties2); $i++) {
				if($productProperties2[$i]['uid'] == trim($this->piVars['prop2'])) {
					$position = $i;	
				}
			}
			
			//t3lib_utility_Debug::debug($p);
			if($GLOBALS['TSFE']->loginUser && $this->feUserGroup==2) {
				if($p['price_partner'] || $p['web_price_partner']) {
					$p['price'] = $p['price_partner'];
					$p['web_price'] = $p['web_price_partner'];
				}
			}
			
			// ZA CENE VEZANE NA KOLIČINE
			if($p['price_prop2'] && $this->piVars['prop2']) {
				$priceArray = explode('|',$p['price_prop2']);
				$p['price'] = trim($priceArray[$position]);	
			}
			
			// ZA CENE VEZANE NA KOLIČINE
			if($p['price_disc_prop2'] && $this->piVars['prop2']) {
				$priceDiscArray = explode('|',$p['price_disc_prop2']);
				$p['web_price'] = trim($priceDiscArray[$position]);	
			}
			
			//t3lib_utility_Debug::debug($this->piVars);
			
			if(($p['web_price'] || $p['price']) && intval($this->piVars['sample']) != 1){
				if(!in_array($this->piVars['addToBasket'].'_'.intval($this->piVars['prop1']).'_'.intval($this->piVars['prop2']),$products_keys)){
					$basket_session['session']['products'][$this->piVars['addToBasket'].'_'.intval($this->piVars['prop1']).'_'.intval($this->piVars['prop2'])]=array('quantity'=>1,'price'=>$p['price'], 'web_price'=>$p['web_price']);
					ksort($basket_session['session']['products']);
					$products_keys=array_keys($basket_session['session']['products']);
				}else{
					$basket_session['session']['products'][$this->piVars['addToBasket'].'_'.intval($this->piVars['prop1']).'_'.intval($this->piVars['prop2'])]['quantity']++;
					$basket_session['session']['products'][$this->piVars['addToBasket'].'_'.intval($this->piVars['prop1']).'_'.intval($this->piVars['prop2'])]['price']=$p['price'];
					$basket_session['session']['products'][$this->piVars['addToBasket'].'_'.intval($this->piVars['prop1']).'_'.intval($this->piVars['prop2'])]['web_price']=$p['web_price'];
					//$basket_session['session']['products'][$this->piVars['addToBasket']]['prop1'][]=intval($this->piVars['prop1']);
				}	
			} else if(intval($this->piVars['sample']) == 1) {
				$basket_session['session']['products'][$this->piVars['addToBasket'].'_'.intval($this->piVars['prop1']).'_'.intval($this->piVars['prop2']).'_1']=array('quantity'=>1,'price'=>0, 'web_price'=>0, 'sample'=>1);
				ksort($basket_session['session']['products']);
				$products_keys=array_keys($basket_session['session']['products']);
				//t3lib_utility_Debug::debug($basket_session);
			}
			
			
			
			$this->conf['templateFile']=$this->conf['basketTemplateFile'];
			$singleMark=$singleMarkPopup=$multiMark=array();
			$template_compact=$this->cObj->getSubpart($this->loadTemplate('###BASKET_COMPACT_VIEW###'), '###BASKET_HEADER_COUNT###');			
			$GLOBALS["TSFE"]->fe_user->setKey('ses','web_shop',$basket_session);
			$GLOBALS["TSFE"]->storeSessionData();
			//t3lib_utility_Debug::debug($this->conf);
//t3lib_div::Debug($this->cObj->substituteMarkerArrayCached($template_header_count,array('###BASKET_PRODUCTS_COUNT###'=>count($products_keys)),array(),array()).$this->cObj->substituteMarkerArrayCached($template_popup,$singleMarkPopup,$multiMark,array()));
			$basketProducts = $basket_session['session']['products'];
			//t3lib_utility_Debug::debug($basketProducts);
			$template = $this->loadTemplate('###BASKET_COMPACT_VIEW###');
			$template_item = $this->cObj->getSubpart($template, '###COMPACT_BASKET_ITEM###');
			$multieMark['###COMPACT_BASKET_ITEM###']='';
			
			$prodNum = 0;
			$prodSum = 0;
			if(count($basketProducts)) {
				foreach($basketProducts as $key=>$prod) {
					$keyArray=array();
					$prodNum = $prodNum + intval($prod['quantity']);
					$userPrice = ($prod['web_price'])?$prod['web_price']:$prod['price'];
					//$memberPrice = ($prod['web_price'])?$prod['web_price']:$prod['price'];				
					$prodSum+=floatval(str_replace(',','.',$userPrice))*$prod['quantity'];
					//$sum_total_member_price+=floatval(str_replace(',','.',$product['web_price']))*$arg['session']['products'][$product['uid']]['quantity'];
					
					$keyArray=explode('_',$key);
					$product = $this->getProduct(array('uid'=>$keyArray[0]));
					//t3lib_utility_Debug::debug($product);
					//t3lib_utility_Debug::debug($key);
					//t3lib_utility_Debug::debug($keyArray);
					$imagesArray=explode(',',$product['images']);
					$uplImage = $imagesArray[0];				
					$imgConfigBig['file.']['maxH'] = '80c';
					$imgConfigBig['file.']['height'] = '80c';
					$imgConfigBig['file.']['XY'] = '[10.w],[10.h]';
					$imgConfigBig['file'] = 'uploads/tx_easyshop/'.$uplImage;
					$origImgBig = $this->cObj->IMAGE($imgConfigBig);
					$resizedImageInfo = $GLOBALS['TSFE']->lastImageInfo;
					$singleMarkItem['###ITEM_IMG_SRC###']=$resizedImageInfo[3];
					$singleMarkItem['###ITEM_TITLE###']=(isset($keyArray[3]))?$product['title'].'&nbsp;(VZOREC)':$product['title'];
					$singleMarkItem['###ITEM_Q###']=$prod['quantity'];
					$singleMarkItem['###ITEM_PRICE###']=$this->moneyFormat($userPrice);
					$singleMarkItem['###ITEM_TOTAL###']=$this->moneyFormat(floatval(str_replace(',','.',$userPrice))*$prod['quantity']);
					$template_prop1=$this->cObj->getSubpart($template, '###SINGLE_PROPERTY_DISPLAY###');
					$template_prop2=$this->cObj->getSubpart($template, '###SINGLE_PROPERTY2_DISPLAY###');
					
					if(intval($keyArray[1])) {
						$prodProp = $this->getProperty(intval($keyArray[1]));
						$singleMarkItemProp1['###SINGLE_PROPERTY_TITLE###']=$prodProp['title'];
						$multieMarkItem['###SINGLE_PROPERTY_DISPLAY###']=$this->cObj->substituteMarkerArrayCached($template_prop1,$singleMarkItemProp1,array(),array());
					
					} else {
						$multieMarkItem['###SINGLE_PROPERTY_DISPLAY###']='';
					}
					
					if(intval($keyArray[2])) {
						$prodProp2 = $this->getProperty2(intval($keyArray[2]));
						$singleMarkItemProp2['###SINGLE_PROPERTY2_TITLE###']=$prodProp2['title'];
						$multieMarkItem['###SINGLE_PROPERTY2_DISPLAY###']=$this->cObj->substituteMarkerArrayCached($template_prop2,$singleMarkItemProp2,array(),array());
					} else {
						$multieMarkItem['###SINGLE_PROPERTY2_DISPLAY###']='';
					}
						
					$productCategories = $this->getProductCategories(array('prod_uid'=>$product['uid']));
					$singleMarkItem['###PRODUCT_SINGLE_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array('cat'=>$productCategories[0]['uid'],'prod'=>$product['uid']), 1, 1,$this->conf['page_single_product']);
					//t3lib_utility_Debug::debug($multieMarkItem['###SINGLE_PROPERTY_DISPLAY###']);
					$multieMark['###COMPACT_BASKET_ITEM###'].=$this->cObj->substituteMarkerArrayCached($template_item,$singleMarkItem,$multieMarkItem,array());
				}
				$multieMark['###EMPTY_BASKET###']='';
			} else {
				$multieMark['###FULL_BASKET###']='';
			}
			
			$singleMark['###BASKET_PRODUCTS_COUNT###']=$prodNum;
			$singleMark['###BASKETITEMSSUM###']=$this->moneyFormat($prodSum);
			//$basketPid = ($this->conf['page_basket_view'])?$this->conf['page_basket_view']:$this->conf['page_basket_view']
			$singleMark['###BASKET_VIEW_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array(), 1, 1,$this->conf['page_basket_view']);
			$singleMark['###BOXOFFICE_VIEW_LINK###']=t3lib_div::getIndpEnv('TYPO3_SITE_URL').$this->pi_linkTP_keepPIvars_url(array(), 1, 1,$this->conf['page_boxoffice_view']);
			//$str=$this->pi_getLL('login_text');
			//if($GLOBALS['TSFE']->loginUser){$str=$this->pi_getLL('logout_text');}
			//$singleMark['###LOGIN_LOGOUT_URL###']=$this->pi_linkTP_keepPIvars($str,array(), 1, 1,$this->conf['login_page']);
			return $this->cObj->substituteMarkerArrayCached($template,$singleMark,$multieMark,array());
			
			
			
			
			
			//return $this->conf['basketTemplateFile'];
			//return $this->cObj->substituteMarkerArrayCached($template_header_count,array('###BASKET_PRODUCTS_COUNT###'=>count($products_keys)),array(),array()).$this->cObj->substituteMarkerArrayCached($template_popup,$singleMarkPopup,$multiMark,array());
		}else if($this->piVars['changePrize']){
			$this->conf=$conf;
			$this->pi_setPiVarDefaults();
			$this->init($conf);
			$p=$this->productHasPrice($this->piVars['changePrize']);
			$product = $this->getProduct(array('uid'=>$this->piVars['changePrize']));
			$this->conf['templateFile']=$this->conf['catalogTemplateFile'];
			$template = $this->loadTemplate('###PRIZE_WRAP###');
			//t3lib_utility_Debug::debug($this->conf);
			//t3lib_utility_Debug::debug($product);
			$productProperties2 = $this->getProductProperties2(array('prod_uid'=>$this->piVars['changePrize']));
			$position = 0;
			for($i=0; $i < count($productProperties2); $i++) {
				if($productProperties2[$i]['uid'] == trim($this->piVars['prop2'])) {
					$position = $i;	
				}
			}
			if($product['price_prop2'] && $this->piVars['prop2']) {				
				//t3lib_utility_Debug::debug($productProperties2);
				$priceArray = explode('|',$product['price_prop2']);
				$product['price'] = trim($priceArray[$position]);	
			}
			
			if($product['price_disc_prop2']) {
				//t3lib_utility_Debug::debug(explode('|',$product['price_disc_prop2']));
				$priceDiscArray = explode('|',$product['price_disc_prop2']);
				$singleMark['###PRODUCT_ACTION_PRICE###']=$this->moneyFormat(trim($priceDiscArray[$position]));
				$singleMark['###AKCIJSKA_YES###']='priceAkcijaYes';
				$singleMark['###AKCIJSKA_NO###']='akcijaYes';
			} else {
				$singleMark['###AKCIJSKA_YES###']='priceAkcijaNo';
				$singleMark['###AKCIJSKA_NO###']='akcijaNo';
				$singleMark['###PRODUCT_ACTION_PRICE###']='';
			}
			$singleMark['###PRODUCT_PRICE###']=$this->moneyFormat($product['price']);
			
			
		//	$singleMark['###PRODUCT_PRICE###']=$this->moneyFormat($product['price']);
		//	$singleMark['###PRODUCT_PRICE###']=$this->moneyFormat($product['price']);			
			return $this->cObj->substituteMarkerArrayCached($template,$singleMark,$multieMark,array());			
		} else if($this->piVars['getProductList']) {
			$this->conf=$conf;
			$this->pi_setPiVarDefaults();
			$this->init($conf);
			return $this->displayProductsListAjax();
		} else {
			return "ERROR";
		}
	}
}
function compareCategoriesByDisplayTitle($a, $b){
	//if($a['display_title']==$b['display_title']){return 0;}
	//return ($a['display_title']<$b['display_title'])? 1 : -1;
	return strcasecmp($a['display_title'],$b['display_title']);
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tend_shop/pi1/class.tx_easyshop_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/tend_shop/pi1/class.tx_easyshop_pi1.php']);
}
?>