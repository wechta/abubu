<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Mitja Venturini <mitja.venturini@gmail.com>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */


$LANG->includeLLFile('EXT:easy_shop/mod1/locallang.xml');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
require_once(t3lib_extMgm::extPath('ke_dompdf')."res/dompdf/dompdf_config.inc.php");
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]



/**
 * Module 'Easy shop Narocila' for the 'easy_shop' extension.
 *
 * @author	Mitja Venturini <mitja.venturini@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_easyshop
 */
class  tx_easyshop_module1 extends t3lib_SCbase {
				var $pageinfo;

				/**
				 * Initializes the Module
				 * @return	void
				 */
				function init()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					parent::init();

					/*
					if (t3lib_div::_GP('clear_all_cache'))	{
						$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
					}
					*/
				}

				/**
				 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
				 *
				 * @return	void
				 */
				function menuConfig()	{
					global $LANG;
					$this->MOD_MENU = Array (
						'function' => Array (
							'1' => $LANG->getLL('function1'),
							'2' => $LANG->getLL('function2'),
							'3' => $LANG->getLL('function3'),
						//	'4' => 'TEST',
						)
					);
					parent::menuConfig();
				}

				/**
				 * Main function of the module. Write the content to $this->content
				 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
				 *
				 * @return	[type]		...
				 */
				function main()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					// Access check!
					// The page will show only if there is a valid page and if this page may be viewed by the user
					$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
					$access = is_array($this->pageinfo) ? 1 : 0;
				
					if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

							// Draw the header.
						$this->doc = t3lib_div::makeInstance('mediumDoc');
						$this->doc->backPath = $BACK_PATH;
						$this->doc->form='<form action="" method="post" enctype="multipart/form-data">';

							// JavaScript
						$this->doc->JScode = '
							<script language="javascript" type="text/javascript">
								script_ended = 0;
								function jumpToUrl(URL)	{
									document.location = URL;
								}
								function confirmURL(text,URL){
									var agree=confirm(text);
									if (agree) {
										jumpToUrl(URL);
									}
								}
							</script>
						';
						$this->doc->postCode='
							<script language="javascript" type="text/javascript">
								script_ended = 1;
								if (top.fsMod) top.fsMod.recentIds["web"] = 0;
							</script>
						';

						$headerSection = $this->doc->getHeader('pages', $this->pageinfo, $this->pageinfo['_thePath']) . '<br />'
							. $LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path') . ': ' . t3lib_div::fixed_lgd_cs($this->pageinfo['_thePath'], -50);

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);
						$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
						$this->content.=$this->doc->divider(5);


						// Render content:
						$this->moduleContent();


						// ShortCut
						if ($BE_USER->mayMakeShortcut())	{
							$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
						}

						$this->content.=$this->doc->spacer(10);
					} else {
							// If no access or if ID == zero

						$this->doc = t3lib_div::makeInstance('mediumDoc');
						$this->doc->backPath = $BACK_PATH;

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);
						$this->content.=$this->doc->spacer(10);
					}
				
				}

				/**
				 * Prints out the module HTML
				 *
				 * @return	void
				 */
				function printContent()	{

					$this->content.=$this->doc->endPage();
					echo $this->content;
				}

				/**
				 * Generates the module content
				 *
				 * @return	void
				 */
				function moduleContent()	{
				global $LANG;
					switch((string)$this->MOD_SETTINGS['function'])	{
						case 1:
							if(t3lib_div::_POST('status')) {
								$this->changeOrderStatus(t3lib_div::_POST('uid'), t3lib_div::_POST('status'));
								$content=$this->drawAllOrders(1);
							}
							else if(t3lib_div::_GP('action') == 'show_single'){								
								$content=$this->drawSingleOrder(t3lib_div::_GP('p_uid'));
							} else {
								$content=$this->drawAllOrders(1);
							}
							$this->content.=$this->doc->section($LANG->getLL('function1'),$content,0,1);
						break;
						case 2:
							if(t3lib_div::_POST('status')) {
								$this->changeOrderStatus(t3lib_div::_POST('uid'), t3lib_div::_POST('status'));
								$content=$this->drawAllOrders(0);
							}
							else if(t3lib_div::_GP('action') == 'show_single'){								
								$content=$this->drawSingleOrder(t3lib_div::_GP('p_uid'));
							} else {
								$content=$this->drawAllOrders(0);
							}
							$this->content.=$this->doc->section($LANG->getLL('function2'),$content,0,1);
						break;
						case 3:
							if(t3lib_div::_POST('status')) {
								$this->changeOrderStatus(t3lib_div::_POST('uid'), t3lib_div::_POST('status'));
								$content=$this->drawAllOrders(2);
							}
							else if(t3lib_div::_GP('action') == 'show_single'){								
								$content=$this->drawSingleOrder(t3lib_div::_GP('p_uid'));
							} else {
								$content=$this->drawAllOrders(2);
							}
							$this->content.=$this->doc->section($LANG->getLL('function3'),$content,0,1);
						break;
						/*
						case 4:
						
							$this->content = "bla";
							spl_autoload_register('DOMPDF_autoload');
							$content = "TESTING PDF";
							//$html = $GLOBALS['TSFE']->content;
							//t3lib_div::devLog('url/html '.$url, $this->prefixId, 0, array($content));
								
							$dompdf = new DOMPDF();
							
							$html = $this->utf8_to_html($content);
							$html = str_replace('&#269;','c',str_replace('&#268;','C',$html));
							$dompdf->load_html($html);
							//echo $html;
							$dompdf->render();
							$dompdf->stream("admin_kupona_".$this->piVars['cUid'].".pdf");
							$this->content.=$this->doc->section($LANG->getLL('function1'),$content,0,1);
							//exit();
						break;
						*/
					}
				}
				
				function drawSingleOrder($uid) {
					$ord = $this->getOrder($uid);
					$ordProd = $this->getOrderProducts($uid);
					$ordBuyer = $this->getBuyer($ord['buyer']);
					if($ord['reciver']) {
						$ordReciver = $this->getReciver($ord['reciver']);
					}
					if(count($ord)>0){					
						switch($ord['status']) {
							case 0:
								$statusRej='selected="selected"';
								$statusSent='';
								$statusOpen='';
								$sentDate = '';
							break;
							case 1:
								$statusOpen='selected="selected"';
								$statusSent='';
								$statusRej='';
								$sentDate = '';
							break;
							case 2:
								$statusOpen='';
								$statusRej='';
								$statusSent='selected="selected"';
								$sentDate = date("j. n. Y", $ord['tstamp']);
							break;
						}
						switch($ord['payment_type']) {
							case 0:
								$paymentType='Osebni prevzem';
							break;
							case 1:
								$paymentType='Po povzetju';
							break;
							case 2:
								$paymentType='Plačilni nalog';
							break;
						}
						
						//t3lib_utility_Debug::debug($ord);
						//t3lib_utility_Debug::debug($ordProd);
					}
					
					//$ord['totalnotax'] = number_format(0.8*$ord['total'], 2, ',', '.');
					//$ord['tax'] = number_format(0.2*$ord['total'], 2, ',', '.');
					
					$returnString = '<table class="typo3-TCEforms">
	<tbody>
    	<tr>
        	<td colspan="2">
            	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="wrapperTable1">
                	<tbody>
                    	<tr class="class-main12">
                            <td colspan="2" class="formField-header"><span style="font-size:13px;" class="class-main14"><strong>ID Naročila: '.$uid.'</strong></span></td>
                        </tr>
                        
                        <tr class="class-main11">
                            <td colspan="2" nowrap="nowrap" valign="top">
                            	<fieldset class="t3-form-palette-fieldset">
                                	<span class="t3-form-palette-field-container">
                                		<label class="t3-form-palette-field-label class-main13">Datum naročila:</label>
                                    	<input type="text" id="" class="formField1 tceforms-textfield tceforms-datefield hasDefaultValue" name="" value="'.date("j. n. Y", $ord['crdate']).'" style="width: 77px;">
                                    </span>
                                    <span class="t3-form-palette-field-container">
                                        <label class="t3-form-palette-field-label class-main13">Datum posiljke:</label>
                                        <input type="text" id="" class="formField1 tceforms-textfield tceforms-datefield hasDefaultValue" name="" value="'.$sentDate.'" style="width: 77px;">
                                    </span>
                                    <span class="t3-form-palette-field-container">
                                        <label class="t3-form-palette-field-label class-main13">Status:</label>
                                        <span class="t3-form-palette-field class-main15">
                                            <select id="" name="status" class="select" onchange="this.form.submit()">
                                                <option value="1" '.$statusOpen.'">Odprto</option>
                                                <option value="2" '.$statusSent.'>Zaprto</option>												
												<option value="3" '.$statusRej.'>Zavrnjeno</option>
                                            </select>
                                        </span>
                                    </span>
                                    <span class="t3-form-palette-field-container">
                                        <label class="t3-form-palette-field-label class-main13">Način plačila:</label>
                                        <input type="text" id="" class="formField1 tceforms-textfield tceforms-datefield hasDefaultValue" name="" value="'.$paymentType.'" style="width:107px;">
                                    </span>
                               </fieldset>
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
            </td>
        </tr>
        
        <tr>
        	<td colspan="2">
            	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="wrapperTable1">
                	<tbody>
                    
                    	<tr class="class-main12">
                            <td colspan="2" class="formField-header"><span style="font-size:13px;" class="class-main14"><strong>Naročeni izdelki</strong></span></td>
                        </tr>
                        
                    	<tr class="class-main12">
                            <td colspan="4" style="padding:0px 10px;"><span class="class-main14"><strong>IZDELEK:</strong></span></td>
                            <td colspan="2" style="padding:0px 10px;"><span class="class-main14"><strong>Količina:</strong></span></td>
                            <td colspan="2" style="padding:0px 10px;"><span class="class-main14"><strong>Barva:</strong></span></td>
							<td colspan="2" style="padding:0px 10px;"><span class="class-main14"><strong>Velikost:</strong></span></td>
                            <td colspan="2" style="padding:0px 10px;"><span class="class-main14"><strong>Cena:</strong></span></td>
                            <td colspan="2" style="padding:0px 10px;"><span class="class-main14"><strong>Popust:</strong></span></td>
                            <td colspan="2" style="padding:0px 10px;"><span class="class-main14"><strong>Skupaj:</strong></span></td>                            
                        </tr>';
                    
					//t3lib_utility_Debug::debug($ordProd);
					
					foreach($ordProd as $prod) {
						//t3lib_utility_Debug::debug($prod);
						/*
						// ZA CENE VEZANE NA KOLIČINE
						if($prod['price_prop2'] && $prod['prop2']) {
							$priceArray = explode('|',$prod['price_prop2']);
							$prod['price'] = trim($priceArray[$prod['prop2']-1]);	
						}
						
						// ZA CENE VEZANE NA KOLIČINE
						if($prod['price_disc_prop2'] && $prod['prop2']) {
							$priceDiscArray = explode('|',$prod['price_disc_prop2']);
							$prod['web_price'] = trim($priceDiscArray[$prod['prop2']-1]);	
						}
						*/
						if($prod['web_price']) {
							$prod['discount']=100 - intval(100*(floatval(str_replace(',','.',$prod['web_price'])))/floatval(str_replace(',','.',$prod['price'])));
							$prod['price']=number_format($prod['web_price'], 2, ',', '.');
							$prod['total']=number_format(floatval(str_replace(',','.',$prod['web_price']))*$prod['num'], 2, ',', '.');
						} else {
							$prod['price']=number_format(str_replace(',','.',$prod['price']), 2, ',', '.');
							$prod['discount'] = 0;
							$prod['total']=number_format(floatval(str_replace(',','.',$prod['price']))*$prod['num'], 2, ',', '.');
						}
						
						if($prod['prop']) {
							$prodProp = $this->getProperty($prod['prop']);
							//t3lib_utility_Debug::debug($prodProp);
							$prop=$prodProp['title'];
						} else {
							$prop='/';
						}
						if($prod['prop2']) {
							$prodProp2 = $this->getProperty2($prod['prop2']);
							//t3lib_utility_Debug::debug($prodProp);
							$prop2=$prodProp2['title'];
						} else {
							$prop2='/';
						}
						
						//$prodProp = $this->getProperty($prod['prop']);
						$returnString .=  '<tr class="class-main12">
								<td colspan="4" style="padding:0px 10px;"><span class="class-main14">'.$prod['title'].'</span></td>
								<td colspan="2" style="padding:0px 10px;"><span class="class-main14">'.$prod['num'].'</span></td>
								<td colspan="2" style="padding:0px 10px;"><span class="class-main14">'.$prop.'</span></td>
								<td colspan="2" style="padding:0px 10px;"><span class="class-main14">'.$prop2.'</span></td>
								<td colspan="2" style="padding:0px 10px;"><span class="class-main14">'.$prod['price'].' €</span></td>
								<td colspan="2" style="padding:0px 10px;"><span class="class-main14">'.$prod['discount'].' %</span></td>
								<td colspan="2" style="padding:0px 10px;"><span class="class-main14">'.$prod['total'].' €</span></td>                            
							</tr>';
					}
                                                
                    $returnString .= '</tbody>
                </table>
            </td>
        </tr>
        
        <tr>
        	<td colspan="2">
            	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="wrapperTable1">
                	<tbody>
                    
                    	<tr class="class-main12">
                            <td colspan="2" class="formField-header"><span style="font-size:13px;" class="class-main14"><strong>Naročnik in prejemnik</strong></span></td>
                        </tr>
                        
                    	<tr class="class-main12">
                            <td colspan="8" style="padding:0px 10px;"><span class="class-main14"><strong>Naročnik:</strong></span></td>
                            <td colspan="8" style="padding:0px 10px;"><span class="class-main14"><strong>Drug prejemnik:</strong></span></td>                         
                        </tr>
                        
                        <tr class="class-main12">
                            <td colspan="8" style="padding:0px 10px;">
                            	<span class="class-main14">
                                	'.$ordBuyer['name'].'&nbsp;'.$ordBuyer['surname'].'<br>
                                    '.$ordBuyer['tel'].'<br>
                                    '.$ordBuyer['email'].'<br>
                                    '.$ordBuyer['address'].'<br>
                                    '.$ordBuyer['post'].'&nbsp;'.$ordBuyer['city'].'<br>
                                    '.$ordBuyer['company'].'<br>
                                    '.$ordBuyer['id_ddv'].'<br>                                	
                                </span>
                            </td>
                            <td colspan="8" style="padding:0px 10px;">
                            	<span class="class-main14">
                                	'.$ordReciver['name'].'&nbsp;'.$ordReciver['surname'].'<br>
                                    '.$ordReciver['address'].'<br>
                                    '.$ordReciver['post'].'&nbsp;'.$ordReciver['city'].'<br>                              	
                                </span>
                            </td>                       
                        </tr>
                                                
                    </tbody>
                </table>
            </td>
        </tr>
        
        
        <tr>
        	<td colspan="2">
            	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="wrapperTable1">
                	<tbody>
                    	<tr class="class-main12">
                            <td colspan="2" class="formField-header"><span class="class-main14">Poštnina: <strong>'.number_format($ord['postage'], 2, ',', '.').'€</strong></span></td>
                        </tr>
                        <tr class="class-main12">
                            <td colspan="2" class="formField-header"><span class="class-main14">Popust (kupon): <strong>'.$ord['discount'].'%</strong></span></td>
                        </tr>					
                        <tr class="class-main12">
                            <td colspan="2" class="formField-header"><span class="class-main14">DDV 9,5%: <strong>'.number_format($ord['total_ddv2'], 2, ',', '.').' €</strong></span></td>
                        </tr>
						<tr class="class-main12">
                            <td colspan="2" class="formField-header"><span class="class-main14">DDV 22%: <strong>'.number_format($ord['total_ddv1'], 2, ',', '.').' €</strong></span></td>
                        </tr>
						<tr class="class-main12">
                            <td colspan="2" style="font-size:13px;" class="formField-header"><span class="class-main14">Skupaj brez DDV: <strong>'.number_format($ord['total']+$ord['postage']-$ord['total_ddv1']-$ord['total_ddv2'], 2, ',', '.').'€</strong></span></td>
                        </tr>						
                        <tr class="class-main12">
                            <td colspan="2" style="font-size:13px;" class="formField-header"><span class="class-main14">Skupaj: <strong>'.number_format($ord['total']+$ord['postage'], 2, ',', '.').'€</strong></span></td>
                        </tr>
                        <input type="hidden" name="uid" value="'.$uid.'">				
                    </tbody>
                </table>
            </td>
        </tr>        
    </tbody>
</table>';
					
					return $returnString;
				}
				
				function drawAllOrders($status) {
					$content = '<table class="typo3-TCEforms" style="width:650px !important;"><tbody><tr><td colspan="2"><table border="0" cellspacing="1" cellpadding="0" width="100%" class="wrapperTable1"><tbody>';
					$ord = $this->getAllOrders($status);
					//t3lib_utility_Debug::debug($ord);
					if(count($ord)>0){
						$content .= '<tr class="class-main12" style="background-color:#6f6d6d;">
										<td width="50px" style="padding:4px 10px;"><span class="class-main14"><strong style="color:#fff;">ID Naročila:</strong></span></td>
										<td width="80px" style="padding:4px 10px;"><span class="class-main14"><strong style="color:#fff;">Datum Naročila:</strong></span></td>
										<td width="150px" style="padding:4px 10px;"><span class="class-main14"><strong style="color:#fff;">Plačnik:</strong></span></td>
										<td width="100px" style="padding:4px 10px;"><span class="class-main14"><strong style="color:#fff;">Način plačila:</strong></span></td>
										<td width="80px" style="padding:4px 10px;"><span class="class-main14"><strong style="color:#fff;">Status:</strong></span></td> 
										<td width="80px" style="padding:4px 10px;"><span class="class-main14"><strong style="color:#fff;">Povezava:</strong></span></td> 
									</tr>';
						foreach($ord as $un){
							$buyer = $this->getBuyer($un['buyer']);
							switch($un['payment_type']) {
								case 0:
									$paymentType='Osebni prevzem';
								break;
								case 1:
									$paymentType='Po povzetju';
								break;
								case 2:
									$paymentType='Plačilni nalog';
								break;
							}
							switch($un['status']) {								
								case 1:
									$paymentStatus='V postopku';
								break;
								case 0:
									$paymentStatus='Zavrnjeno';
								break;
								case 2:
									$paymentStatus='Zaključeno';
								break;
							}
							//$content .= '<tr height="18px" bgcolor="#dddddd" onmouseover="this.style.backgroundColor=\'#cccccc\';" onmouseout="this.style.background=\'#dddddd\';" onclick="location.href(\'mod.php?id=0&M=web_txeasyshopM1&SET[function]=2&action=show_single&p_uid='.$un['uid'].'\');">"><td>&nbsp;'.$un['uid'].'&nbsp;</td><td>&nbsp;'.date("j. n. Y", $un['crdate']).'&nbsp;</td><td>&nbsp;'.$buyer['email'].'&nbsp;</td><td>&nbsp;'.$paymentType.'&nbsp;</td><td>&nbsp;'.$paymentStatus.'&nbsp;</td></tr>';
							$content .= '<tr class="class-main11" style=" font-size:11px; background-color:#d4d4d4; ">
											<td style="padding:3px 10px;"><span class="class-main14">'.$un['uid'].'</span></td>
											<td style="padding:3px 10px;"><span class="class-main14">'.date("j. n. Y", $un['crdate']).'</span></td>
											<td style="padding:3px 10px;"><span class="class-main14">'.$buyer['email'].'</span></td>
											<td style="padding:3px 10px;"><span class="class-main14">'.$paymentType.'</span></td>
											<td style="padding:3px 10px;"><span class="class-main14">'.$paymentStatus.'</span></td>
											<td style="padding:3px 10px;"><span class="class-main14"><a href="mod.php?id=0&M=web_txeasyshopM1&SET[function]=1&action=show_single&p_uid='.$un['uid'].'" style="text-decoration:underline;">Poglej</a></span></td>
										</tr>';
						}	
					}else{
						$content .= '<tr class="class-main11" style="border-bottom:1px solid #FFF;"><td>&nbsp;<b>Ni naročil</b></td></tr>';						
					}
					$content .= '</tbody></table></td></tr></tbody></table>';
					return $content;
				}
				
				function changeOrderStatus($uid, $status) {
					if($status == 3) $status=0;
					if($status == 2) {
						$fields_values['tstamp'] = time();
						$this->updateStock($uid);
					}		
					else {$fields_values['tstamp'] = '';}		
									
					$fields_values['status'] = $status;		
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_easyshop_payment_log','uid='.$uid,$fields_values,$no_quote_fields=FALSE);
				}
				
				function updateStock($uid) {
					$ordProd = $this->getOrderProducts($uid);
					foreach($ordProd as $prod) {
						$faktor=1;
						if($prod['prop2']) {
							switch($prod['prop2']) {
								case 1:
									$faktor=50;
								break;
								case 2:
									$faktor=100;
								break;
								case 3:
									$faktor=200;
								break;
								case 4:
									$faktor=500;
								break;
								case 5:
									$faktor=1000;
								break;
							
							}
						}
						$soldStock = $faktor*intval($prod['num']);
						$oldStock = intval($prod['stock_val']);
						$newStock = ($oldStock>$soldStock)?$oldStock-$soldStock:0;
						$this->insertNewStock($prod['uid'], $newStock);
					}
				}
				
				function insertNewStock($uid, $newStock) {
					$fields_values['stock_val'] = $newStock;		
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_easyshop_products','uid='.$uid,$fields_values,$no_quote_fields=FALSE);
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
				
				function getOrderProducts($uid) {
					$output = array();
					$queryParts['SELECT'] = "DISTINCT tx_easyshop_products.uid, tx_easyshop_products.stock_val, tx_easyshop_products.title, tx_easyshop_products.price_prop2, tx_easyshop_products.price_disc_prop2, tx_easyshop_payment_log_products_mm.num, tx_easyshop_payment_log_products_mm.prop, tx_easyshop_payment_log_products_mm.prop2, tx_easyshop_payment_log_products_mm.price, tx_easyshop_payment_log_products_mm.web_price";
					$queryParts['FROM'] = "tx_easyshop_products, tx_easyshop_payment_log_products_mm";
					$queryParts['WHERE'] = 'tx_easyshop_payment_log_products_mm.uid_foreign = tx_easyshop_products.uid';
					$queryParts['WHERE'] .= ' AND tx_easyshop_payment_log_products_mm.uid_local = '.$uid;
					//$queryParts['WHERE'] .= " AND valid_date < ".time();
					$queryParts['ORDERBY'] = "tx_easyshop_products.title desc";
					
					$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
					if ($res){
						while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
							$output[] = $row;
						}
					}
					return $output;
				}
				
				function getAllOrders($status) {
					$output = array();
					$queryParts['SELECT'] = "*";
					$queryParts['FROM'] = "tx_easyshop_payment_log";
					$queryParts['WHERE'] = "status=".$status." AND deleted=0";
					//$queryParts['WHERE'] .= " AND valid_date < ".time();
					$queryParts['ORDERBY'] = "crdate desc";
					$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
					if ($res){
						while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
							$output[] = $row;
						}
					}
					return $output;
				}
				
				function getOrder($uid) {
					$queryParts['SELECT'] = "*";
					$queryParts['FROM'] = "tx_easyshop_payment_log";
					$queryParts['WHERE'] = "tx_easyshop_payment_log.uid=".$uid;
					$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
					if ($res){
						return $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
					}
					else{
						return array();	
					}
				}
				
				function getBuyer($uid) {
					$queryParts['SELECT'] = "*";
					$queryParts['FROM'] = "tx_easyshop_buyers";
					$queryParts['WHERE'] = "tx_easyshop_buyers.uid=".$uid;
					$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
					if ($res){
						return $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
					}
					else{
						return array();	
					}	
				}
				
				function getReciver($uid) {
					$queryParts['SELECT'] = "*";
					$queryParts['FROM'] = "tx_easyshop_recivers";
					$queryParts['WHERE'] = "tx_easyshop_recivers.uid=".$uid;
					$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryParts);
					if ($res){
						return $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
					}
					else{
						return array();	
					}	
				}		
		}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/easy_shop/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/easy_shop/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_easyshop_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>