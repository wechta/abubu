<?php
//$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl']=unserialize('a:1:{s:8:"_DEFAULT";a:4:{s:4:"init";a:6:{s:16:"enableCHashCache";b:1;s:18:"appendMissingSlash";s:18:"ifNotFile,redirect";s:18:"adminJumpToBackend";b:1;s:20:"enableUrlDecodeCache";b:1;s:20:"enableUrlEncodeCache";b:1;s:19:"emptyUrlReturnValue";s:1:"/";}s:8:"pagePath";a:4:{s:4:"type";s:4:"user";s:8:"userFunc";s:68:"EXT:realurl/class.tx_realurl_advanced.php:&tx_realurl_advanced->main";s:14:"spaceCharacter";s:1:"-";s:14:"languageGetVar";s:1:"L";}s:8:"fileName";a:3:{s:25:"defaultToHTMLsuffixOnPrev";i:0;s:16:"acceptHTMLsuffix";i:1;s:5:"index";a:1:{s:5:"print";a:1:{s:9:"keyValues";a:1:{s:4:"type";i:98;}}}}s:7:"preVars";a:1:{i:0;a:3:{s:6:"GETvar";s:1:"L";s:8:"valueMap";a:1:{i:1;s:1:"1";}s:7:"noMatch";s:6:"bypass";}}}}');
$TYPO3_CONF_VARS['EXTCONF']['realurl'] = array(
   '_DEFAULT' => array(
        'init' => array(
            'enableCHashCache' => 0, 
            'appendMissingSlash' => 'ifNotFile',
            'enableUrlDecodeCache' => 0,
            'enableUrlEncodeCache' => 0,
            ),
        'redirects' => array(),
        'preVars' => array(
            '0' => array(
                'GETvar' => 'no_cache',
                'valueMap' => array('nc' => 1),
								'noMatch' => 'bypass',
            ),
             ),
       'pagePath' => array(
	     'type' => 'user',
            'userFunc' => 'EXT:realurl/class.tx_realurl_advanced.php:&tx_realurl_advanced->main',
            'spaceCharacter' => '_',
            'languageGetVar' => 'L',
            'expireDays' => 0,
            'rootpage_id' => $TYPO3_CONF_VARS["SYS"]["rootpage_id"],
            'segTitleFieldList' => 'tx_realurl_pathsegment,alias,nav_title,title',
            'excludeDoktypes' => '254,199'
            ),
        'fixedPostVars' => array(),
        'postVarSets' => array(
            '_DEFAULT' => array(
                // archive
                'period' => array(
                    array(
                        'condPrevValue' => -1,
                        'GETvar' => 'tx_ttnews[pS]' , 
                        ),
                    array(
                        'GETvar' => 'tx_ttnews[pL]' , 
                        ),
                    array(
                        'GETvar' => 'tx_ttnews[arc]' ,
                        'valueMap' => array(
                            'archived' => 1,
                            'non-archived' => -1,
                            )
                         ),
                    ), 
                // pagebrowser
                'browse' => array(
                    array(
                        'GETvar' => 'tx_ttnews[pointer]',
                        ),
                     ),
                'select' => array (
                    array(
                        'GETvar' => 'tx_ttnews[cat]',
                        'lookUpTable' => array(
                            'table' => 'tt_news_cat',
                            'id_field' => 'uid',
                            'alias_field' => 'title',
                            'addWhereClause' => ' AND NOT deleted',
                            'useUniqueCache' => 1,
                            'useUniqueCache_conf' => array(
                                'strtolower' => 1,
                                ),
                            ),
                        ),
                    ),
					'kategorija' => array (
                    array(
                        'GETvar' => 'tx_easyshop_pi1[cat]',
                        'lookUpTable' => array(
                            'table' => 'tx_easyshop_categories',
                            'id_field' => 'uid',
                            'alias_field' => 'title',
                            'addWhereClause' => ' AND NOT deleted',
                            'useUniqueCache' => 1,
                            'useUniqueCache_conf' => array(
                                'strtolower' => 1,
                                ),
                            ),
                        ),
                    ),
					'izdelek' => array (
                    array(
                        'GETvar' => 'tx_easyshop_pi1[prod]',
                        'lookUpTable' => array(
                            'table' => 'tx_easyshop_products',
                            'id_field' => 'uid',
                            'alias_field' => 'title',
                            'addWhereClause' => ' AND NOT deleted',
                            'useUniqueCache' => 1,
                            'useUniqueCache_conf' => array(
                                'strtolower' => 1,
                                ),
                            ),
                        ),
                    ),
		  		'n' => array(
                      array('GETvar' => 'tx_ttnews[backPid]',
                      		'noMatch' => 'bypass',
							'lookUpTable' => array('table' => 'pages',
                            						'id_field' => 'uid',
                            						'alias_field' => 'title',
                            						'addWhereClause' => ' AND NOT deleted',
                            						'useUniqueCache' => 1,
                            						'useUniqueCache_conf' => array('strtolower' => 1,'spaceCharacter' => '-')
                             				)
                        ),
                      array('GETvar' => 'tx_ttnews[tt_news]',
                          	'lookUpTable' => array('table' => 'tt_news',
                            						'id_field' => 'uid',
                            						'alias_field' => 'title',
                            						'addWhereClause' => ' AND NOT deleted',
                            						'useUniqueCache' => 1,
                            						'useUniqueCache_conf' => array('strtolower' => 1,'spaceCharacter' => '-')
                             				)
                        ),
                     array('GETvar' => 'cHash'),
                     array('GETvar' => 'tx_ttnews[swords]')),
		  		'JWPlayer' => array(array('GETvar' => 'playerUid')),
                 )
            ),
        'fileName' => array(
             'index' => array(
                  'rss.xml' => array(
                       'keyValues' => array(
                            'type' => 100,
                        ),
                    ),
                    'print.html' => array(
                       'keyValues' => array(
                            'type' => 98
                        )
                    ),
                    'JWFile.xml' => array(
                       'keyValues' => array(
                            'type' => 66
                        )
                    ),
                ),
            ),
        )
    );
?>