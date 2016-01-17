<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$TYPO3_CONF_VARS['SYS']['sitename'] = 'Abubu';

	// Default password is "joh316" :
$TYPO3_CONF_VARS['BE']['installToolPassword'] = 'bacb98acf97e0b6112b1d1b650b84971';

$TYPO3_CONF_VARS['EXT']['extList'] = 'info,perm,func,filelist,about,version,tsconfig_help,context_help,extra_page_cm_options,impexp,sys_note,tstemplate,tstemplate_ceditor,tstemplate_info,tstemplate_objbrowser,tstemplate_analyzer,func_wizards,wizard_crpages,wizard_sortpages,lowlevel,install,belog,beuser,aboutmodules,setup,taskcenter,info_pagetsconfig,viewpage,rtehtmlarea,css_styled_content,t3skin,t3editor,reports,felogin,form';

$typo_db_extTableDef_script = 'extTables.php';

## INSTALL SCRIPT EDIT POINT TOKEN - all lines after this points may be changed by the install script!

$TYPO3_CONF_VARS['EXT']['extList'] = 'extbase,css_styled_content,info,perm,func,filelist,about,version,tsconfig_help,context_help,extra_page_cm_options,impexp,sys_note,tstemplate,tstemplate_ceditor,tstemplate_info,tstemplate_objbrowser,tstemplate_analyzer,func_wizards,wizard_crpages,wizard_sortpages,lowlevel,install,belog,beuser,aboutmodules,setup,taskcenter,info_pagetsconfig,viewpage,rtehtmlarea,t3skin,t3editor,reports,felogin,form,adodb,dbal,cshmanual,feedit,opendocs,recycler,scheduler,fluid,workspaces,rlmp_tmplselector,automaketemplate,kickstarter,phpmyadmin,tt_news,css_select,moderntab,rzcolorbox,doc_indexed_search,indexed_search,rgmediaimages,povprasevanje,dropdown_sitemap,api_macmade,pil_mailform,jb_gd_resize,realurl,seo_basics,easy_shop,footer,web_shop_registration,kenslider,cookie_control,iconepovezave,easycontact,dd_googlesitemap,dd_googlesitemap_dmf,jh_opengraph_ttnews,ke_dompdf,div2007,transactor,transactor_paymill';	// Modified or inserted by TYPO3 Extension Manager.  Modified or inserted by TYPO3 Core Update Manager.
// Updated by TYPO3 Core Update Manager 22-02-12 09:08:19
//$TYPO3_CONF_VARS['BE']['installToolPassword'] = 'a40786d4aaa25967b8460da6a53eb0e1';	//  Modified or inserted by TYPO3 Install Tool.
$typo_db_username = 'root';	// Modified or inserted by TYPO3 Install Tool. 
$typo_db_host = 'localhost';	//  Modified or inserted by TYPO3 Install Tool.
$typo_db_password = '';
$typo_db = 'zisha_baza';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['BE']['disable_exec_function'] = '0';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['gdlib_png'] = '1';	// Modified or inserted by TYPO3 Install Tool. 
$TYPO3_CONF_VARS['GFX']['im'] = '1';	// Modified or inserted by TYPO3 Install Tool. 
$TYPO3_CONF_VARS['GFX']['im_combine_filename'] = '';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['im_path'] = '/usr/bin/';	// Modified or inserted by TYPO3 Install Tool. 
$TYPO3_CONF_VARS['GFX']['im_path_lzw'] = '';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['BE']['forceCharset'] = 'utf-8';
$TYPO3_CONF_VARS['SYS']['setDBinit'] = 'SET NAMES utf8;';
$TYPO3_CONF_VARS['SYS']['displayErrors'] = '0';
$TYPO3_CONF_VARS['SYS']['devIPmask'] = '';
$TYPO3_CONF_VARS['SYS']['errorHandler'] = '';
$TYPO3_CONF_VARS['SYS']['debugExceptionHandler'] = '';
$TYPO3_CONF_VARS['SYS']['productionExceptionHandler'] = '';
$TYPO3_CONF_VARS['SYS']['systemLog'] = '';
$TYPO3_CONF_VARS['SYS']['enable_errorDLOG'] = '0';
$TYPO3_CONF_VARS['SYS']['enable_exceptionDLOG'] = '0';
$TYPO3_CONF_VARS['SYS']['enableDeprecationLog'] = '0';
// Updated by TYPO3 Install Tool 22-02-12 09:16:28
$TYPO3_CONF_VARS['EXT']['extList_FE'] = 'extbase,css_styled_content,version,install,rtehtmlarea,t3skin,felogin,form,adodb,dbal,feedit,fluid,workspaces,rlmp_tmplselector,automaketemplate,kickstarter,phpmyadmin,tt_news,css_select,moderntab,rzcolorbox,doc_indexed_search,indexed_search,rgmediaimages,povprasevanje,dropdown_sitemap,api_macmade,pil_mailform,jb_gd_resize,realurl,seo_basics,easy_shop,footer,web_shop_registration,kenslider,cookie_control,iconepovezave,easycontact,dd_googlesitemap,dd_googlesitemap_dmf,jh_opengraph_ttnews,ke_dompdf,div2007,transactor,transactor_paymill';	// Modified or inserted by TYPO3 Extension Manager. 
// Updated by TYPO3 Extension Manager 22-02-12 09:16:38
$TYPO3_CONF_VARS['INSTALL']['wizardDone']['tx_coreupdates_installsysexts'] = '1';	//  Modified or inserted by TYPO3 Upgrade Wizard.
// Updated by TYPO3 Upgrade Wizard 22-02-12 09:16:38
// Updated by TYPO3 Extension Manager 22-02-12 09:16:41
$TYPO3_CONF_VARS['INSTALL']['wizardDone']['tx_coreupdates_installnewsysexts'] = '1';	//  Modified or inserted by TYPO3 Upgrade Wizard.
// Updated by TYPO3 Upgrade Wizard 22-02-12 09:16:41
$TYPO3_CONF_VARS['EXT']['extConf']['rlmp_tmplselector'] = 'a:1:{s:12:"templateMode";s:4:"file";}';	//  Modified or inserted by TYPO3 Extension Manager.
$TYPO3_CONF_VARS['EXT']['extConf']['seo_basics'] = 'a:2:{s:10:"xmlSitemap";s:1:"1";s:16:"sourceFormatting";s:1:"1";}';	//  Modified or inserted by TYPO3 Extension Manager.
$TYPO3_CONF_VARS['EXT']['extConf']['tt_news'] = 'a:20:{s:13:"useStoragePid";s:1:"1";s:17:"requireCategories";s:1:"0";s:18:"useInternalCaching";s:1:"1";s:11:"cachingMode";s:6:"normal";s:13:"cacheLifetime";s:1:"0";s:13:"cachingEngine";s:8:"internal";s:11:"treeOrderBy";s:3:"uid";s:13:"prependAtCopy";s:1:"1";s:5:"label";s:5:"title";s:9:"label_alt";s:0:"";s:10:"label_alt2";s:0:"";s:15:"label_alt_force";s:1:"0";s:21:"categorySelectedWidth";s:1:"0";s:17:"categoryTreeWidth";s:1:"0";s:25:"l10n_mode_prefixLangTitle";s:1:"1";s:22:"l10n_mode_imageExclude";s:1:"1";s:20:"hideNewLocalizations";s:1:"0";s:24:"writeCachingInfoToDevlog";s:10:"disabled|0";s:23:"writeParseTimesToDevlog";s:1:"0";s:18:"parsetimeThreshold";s:3:"0.1";}';	//  Modified or inserted by TYPO3 Extension Manager.
// Updated by TYPO3 Extension Manager 28-02-12 11:07:52


$TYPO3_CONF_VARS['EXT']['extConf']['t3jquery'] = 'a:11:{s:15:"alwaysIntegrate";s:1:"1";s:17:"integrateToFooter";s:1:"0";s:18:"dontIntegrateOnUID";s:1:"1";s:23:"dontIntegrateInRootline";s:0:"";s:13:"jqLibFilename";s:23:"jquery-###VERSION###.js";s:9:"configDir";s:19:"uploads/tx_t3jquery";s:13:"jQueryVersion";s:5:"1.3.x";s:15:"jQueryUiVersion";s:5:"1.8.x";s:18:"jQueryTOOLSVersion";s:0:"";s:16:"integrateFromCDN";s:1:"0";s:11:"locationCDN";s:6:"google";}';	// Modified or inserted by TYPO3 Extension Manager. 
$TYPO3_CONF_VARS['EXT']['extConf']['css_select'] = 'a:3:{s:6:"CSSDIR";s:23:"fileadmin/template/css/";s:6:"CSSEXT";s:3:"css";s:12:"ALLOWSUBDIRS";s:1:"1";}';	//  Modified or inserted by TYPO3 Extension Manager.
$TYPO3_CONF_VARS['EXT']['extConf']['imagecycle'] = 'a:5:{s:7:"effects";s:233:",none,blindX,blindY,blindZ,cover,curtainX,curtainY,fade,fadeout,fadeZoom,growX,growY,scrollUp,scrollDown,scrollLeft,scrollRight,scrollHorz,scrollVert,shuffle,slideX,slideY,toss,turnUp,turnDown,turnLeft,turnRight,uncover,wipe,zoom,all";s:11:"effectsCoin";s:27:",random,swirl,rain,straight";s:11:"effectsNivo";s:178:",random,sliceDown,sliceDownLeft,sliceUp,sliceUpLeft,sliceUpDown,sliceUpDownLeft,fold,fade,slideInRight,slideInLeft,boxRandom,boxRain,boxRainReverse,boxRainGrow,boxRainGrowReverse";s:15:"nivoThemeFolder";s:34:"EXT:imagecycle/res/css/nivoslider/";s:24:"useSelectInsteadCheckbox";s:1:"0";}';	//  Modified or inserted by TYPO3 Extension Manager.
$TYPO3_CONF_VARS['EXT']['extConf']['rzcolorbox'] = 'a:1:{s:9:"allowHtml";s:1:"0";}';	//  Modified or inserted by TYPO3 Extension Manager.
$TYPO3_CONF_VARS['EXT']['extConf']['realurl'] = 'a:5:{s:10:"configFile";s:26:"typo3conf/realurl_conf.php";s:14:"enableAutoConf";s:1:"1";s:14:"autoConfFormat";s:1:"0";s:12:"enableDevLog";s:1:"0";s:19:"enableChashUrlDebug";s:1:"0";}';	// Modified or inserted by TYPO3 Extension Manager. 
$TYPO3_CONF_VARS['EXT']['extConf']['indexed_search'] = 'a:17:{s:8:"pdftools";s:9:"/usr/bin/";s:8:"pdf_mode";s:2:"20";s:5:"unzip";s:9:"/usr/bin/";s:6:"catdoc";s:9:"/usr/bin/";s:6:"xlhtml";s:9:"/usr/bin/";s:7:"ppthtml";s:9:"/usr/bin/";s:5:"unrtf";s:9:"/usr/bin/";s:9:"debugMode";s:1:"0";s:18:"fullTextDataLength";s:1:"0";s:23:"disableFrontendIndexing";s:1:"0";s:6:"minAge";s:2:"24";s:6:"maxAge";s:1:"0";s:16:"maxExternalFiles";s:1:"5";s:26:"useCrawlerForExternalFiles";s:1:"0";s:11:"flagBitMask";s:3:"192";s:16:"ignoreExtensions";s:0:"";s:17:"indexExternalURLs";s:1:"0";}';	//  Modified or inserted by TYPO3 Extension Manager.
// Updated by TYPO3 Extension Manager 08-03-12 13:40:54


$TYPO3_CONF_VARS['EXT']['extConf']['rgmediaimages'] = 'a:1:{s:6:"rename";s:1:"1";}';	//  Modified or inserted by TYPO3 Extension Manager.
$TYPO3_CONF_VARS['EXT']['extConf']['em'] = 'a:1:{s:17:"selectedLanguages";s:2:"sl";}';	//  Modified or inserted by TYPO3 Extension Manager.
// Updated by TYPO3 Extension Manager 02-04-12 10:56:34
$TYPO3_CONF_VARS['GFX']['thumbnails'] = '1';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['imagefile_ext'] = 'gif,jpg,jpeg,tif,tiff,bmp,pcx,tga,png,pdf,ai';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['BE']['versionNumberInFilename'] = '0';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['BE']['warning_email_addr'] = 'wechta@gmail.com';	//  Modified or inserted by TYPO3 Install Tool.
// Updated by TYPO3 Install Tool 02-04-12 11:05:49
$TYPO3_CONF_VARS['EXT']['extConf']['pil_mailform'] = 'a:1:{s:3:"db.";a:2:{s:5:"mode.";a:4:{s:3:"get";s:1:"0";s:3:"new";s:1:"0";s:3:"add";s:1:"0";s:6:"update";s:1:"0";}s:6:"tables";s:0:"";}}';	//  Modified or inserted by TYPO3 Extension Manager.
// Updated by TYPO3 Extension Manager 06-04-12 10:18:20
$TYPO3_CONF_VARS['SYS']['compat_version'] = '4.7';	//  Modified or inserted by TYPO3 Install Tool.
// Updated by TYPO3 Install Tool 24-05-12 09:38:02
// Updated by TYPO3 Extension Manager 08-01-13 10:25:32
$TYPO3_CONF_VARS['MAIL']['transport'] = 'mail';	// Modified or inserted by TYPO3 Install Tool. 
$TYPO3_CONF_VARS['MAIL']['transport_smtp_server'] = 'smtp.mandrillapp.com:587';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['MAIL']['transport_smtp_encrypt'] = '';	// Modified or inserted by TYPO3 Install Tool. 
$TYPO3_CONF_VARS['MAIL']['transport_smtp_username'] = 'naprej';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['MAIL']['transport_smtp_password'] = '25c4c726-6fb2-4cc2-8b53-b2499d1ba78f';	//  Modified or inserted by TYPO3 Install Tool.
// Updated by TYPO3 Install Tool 09-01-13 11:11:35
// Updated by TYPO3 Extension Manager 11-09-13 10:37:01
$TYPO3_CONF_VARS['FE']['pageNotFound_handling'] = '404';	//  Modified or inserted by TYPO3 Install Tool.
// Updated by TYPO3 Install Tool 13-10-13 13:22:23
// Updated by TYPO3 Extension Manager 12-11-13 10:43:12
// Updated by TYPO3 Install Tool 27-11-13 18:00:59
$TYPO3_CONF_VARS['EXT']['extConf']['ke_dompdf'] = 'a:0:{}';	// Modified or inserted by TYPO3 Extension Manager. 
$TYPO3_CONF_VARS['EXT']['extConf']['rtehtmlarea'] = 'a:8:{s:21:"noSpellCheckLanguages";s:23:"ja,km,ko,lo,th,zh,b5,gb";s:15:"AspellDirectory";s:15:"/usr/bin/aspell";s:20:"defaultConfiguration";s:105:"Typical (Most commonly used features are enabled. Select this option if you are unsure which one to use.)";s:12:"enableImages";s:1:"1";s:20:"enableInlineElements";s:1:"0";s:19:"allowStyleAttribute";s:1:"1";s:24:"enableAccessibilityIcons";s:1:"0";s:16:"forceCommandMode";s:1:"0";}';	//  Modified or inserted by TYPO3 Extension Manager.
//$TYPO3_CONF_VARS['EXT']['extConf']['transactor_paymill'] = 'a:4:{s:10:"privatekey";s:32:"a5bb9cd2a13ee35d536b79a309a070cc";s:9:"publickey";s:32:"463412031171a07ce76a922cd82ec805";s:11:"provideruri";s:27:"https://api.paymill.com/v2/";s:8:"testMode";s:1:"1";}';	//  Modified or inserted by TYPO3 Extension Manager.
// Updated by TYPO3 Extension Manager 25-05-15 17:21:42
// Updated by TYPO3 Install Tool 28-11-15 03:56:09
?>