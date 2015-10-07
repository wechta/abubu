<?php
/***************************************************************
* Copyright notice
* 
* (c) 2008 macmade.net - Jean-David Gadina (info@macmade.net)
* All rights reserved
* 
* This script is part of the TYPO3 project. The TYPO3 project is 
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
* 
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
* 
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* 
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/** 
 * Plugin 'StyleSheet Selector' for the 'css_select' extension.
 *
 * @author      Jean-David Gadina <info@macmade.net>
 * @version     4.0
 */

/**
 * [CLASS/FUNCTION INDEX OF SCRIPT]
 * 
 *   48:    class tx_cssselect_pi1
 *   79:    protected function __construct
 *   93:    protected function _buildIndex
 *  127:    protected function _buildImports
 *  159:    protected function _getCSSFiles
 *  238:    public function main( $content, $conf )
 * 
 *          TOTAL FUNCTIONS: 5
 */

// Requires the TYPO3 FE plugin base
require_once( PATH_tslib . 'class.tslib_pibase.php' );

class tx_cssselect_pi1 extends tslib_pibase
{
    // Extension configuration array
    protected $_extConf   = array();
    
    // TypoScript configuration array
    protected $_conf      = array();
    
    // CSS files to load
    protected $_cssFiles  = array();
    
    // New line character
    protected $_NL        = '';
    
    // Tabulation character
    protected $_TAB       = '';
    
    // Class name
    public $prefixId      = 'tx_cssselect_pi1';
    
    // Path to this script relative to the TYPO3 extension directory
    public $scriptRelPath = 'pi1/class.tx_cssselect_pi1.php';
    
    // The extension key
    public $extKey        = 'css_select';
    
    /**
     * Class constructor
     * 
     * @return  NULL
     */
    public function __construct()
    {
        // Sets the new line character
        $this->_NL  = chr( 10 );
        
        // Sets the tabulation character
        $this->_TAB = chr( 9 );
    }
    
    /**
     * Builds the index of the page stylesheets.
     * 
     * @return  string  An index of the stylesheets
     */
    protected function _buildIndex()
    {
        // Storage
        $index = array();
        
        // Index counter
        $i     = 1;
        
        // Process each stylesheet
        foreach( $this->_cssFiles as $key => $value ) {
            
            // Adds the stylesheet to the index
            $index[] = $this->_TAB
                     . ' * '
                     . $i
                     . ') '
                     . $value[ 'file' ]
                     . ' (page ID: '
                     . $value[ 'pid' ]
                     . ')';
            
            // Increments the index counter
            $i++;
        }
        
        // Returns the index
        return implode( $this->_NL, $index );
    }
    
    /**
     * Builds CSS @import rules.
     * 
     * @return  string A CSS @import rules for each stylesheet
     */
    protected function _buildImports()
    {
        // Storage
        $imports = array();
        
        // Process each stylesheet
        foreach( $this->_cssFiles as $key => $value ) {
            
            // Adds the @import rule
            $imports[] = $this->_TAB
                       . '@import url( "'
                       . $value[ 'file' ]
                       . '" );';
        }
        
        // Returns the import rules
        return implode( $this->_NL, $imports );
    }
    
    /**
     * Returns all the selected CSS file
     * 
     * This function returns the specified CSS files for the current page. It
     * also checks, if needed, for selected stylesheets on the top pages.
     * 
     * @return  mixed   If CSS files are found, an array with the CSS files relative paths. Otherwise false.
     */
    protected function _getCSSFiles()
    {            
        // Storage for the CSS files
        $files = array();
        
        // Checks if the recursive option si set
        if( isset( $this->_conf[ 'recursive' ] ) && $this->_conf[ 'recursive' ] ) {
            
            // Check each top page
            foreach( $GLOBALS[ 'TSFE' ]->config[ 'rootLine' ] as $topPage ) {
                
                // Checks the inheritance mode
                if( $topPage[ 'tx_cssselect_inheritance' ] == 1 && $GLOBALS[ 'TSFE' ]->id != $topPage[ 'uid' ] ) {
                    
                    // Process the next page
                    continue;
                    
                } elseif( $topPage[ 'tx_cssselect_inheritance' ] == 2 ) {
                    
                    // Erase stored styles
                    $files = array();
                    
                    // Checks the current PID
                    if( $GLOBALS[ 'TSFE' ]->id != $topPage[ 'uid' ] ) {
                                                
                        // Process the next page
                        continue;
                    }
                }
                
                // Checks if a stylesheet is specified
                // Thanx to Wolfgang Klinger for the debug
                if( $topPage[ 'tx_cssselect_stylesheets' ] ) {
                    
                    // Gets the selected CSS files for the current page
                    $pageFiles = explode( ',', $topPage[ 'tx_cssselect_stylesheets' ] );
                    
                    // Process each selected file
                    foreach( $pageFiles as $file ) {
                        
                        // Adds the selected stylesheet
                        $files[ $file ] = array(
                            'file' => $file,
                            'pid'  => $topPage[ 'uid' ]
                        );
                    }
                }
            }
            
        } elseif( $GLOBALS[ 'TSFE' ]->page[ 'tx_cssselect_stylesheets' ] ) {
                    
            // Gets the selected CSS files for the current page
            $pageFiles = explode( ',', $GLOBALS[ 'TSFE' ]->page[ 'tx_cssselect_stylesheets' ] );
            
            // Process each selected file
            foreach( $pageFiles as $file ) {
                
                // Adds the selected stylesheet
                $files[ $file ] = array(
                    'file' => $file,
                    'pid'  => $GLOBALS[ 'TSFE' ]->page[ 'uid' ]
                );
            }
        }
        
        // Returns the CSS files if any, otherwise false
        return ( count( $files ) ) ? $files : false;
    }
    
    /**
     * Adds one or more stylesheet(s) to the TYPO3 page headers.
     * 
     * @param   string  $content    The content object
     * @param   array   $conf       The TS setup
     * @return  string  The additionnal header data
     * @see     _getCSSFiles
     * @see     _buildIndex
     * @see     _buildImports
     */
    public function main( $content, array $conf )
    {
        // Checks for the extension configuration
        if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'css_select' ] ) ) {
            
            // Stores the TS configuration array
            $this->_conf     = $conf;
            
            // Stores the extension configuration
            $this->_extConf  = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'css_select' ] );
            
            // Gets the CSS files
            if( $this->_cssFiles = $this->_getCSSFiles() ) {
                
                // Storage for the header data
                $headerData = array();
                
                // Checks for XHTML rules
                if( isset( $this->_conf[ 'xHTML' ] ) && $this->_conf[ 'xHTML' ] ) {
                    
                    // Start for HTML comments
                    $startComment = '/* <![CDATA[ */';
                    
                    // End for HTML comments
                    $endComment   = '/* ]]> */';
                    
                    // End for tags
                    $endTag       = ' /';
                    
                } else {
                    
                    // Start for HTML comments
                    $startComment = '<!--';
                    
                    // End for HTML comments
                    $endComment   = '-->';
                    
                    // End for tags
                    $endTag       = '';
                }
                
                // Rel parameter
                $rel        = ( isset( $this->_conf[ 'linkRel' ] )     && $this->_conf[ 'linkRel' ] )     ? ' rel="'     . $this->_conf[ 'linkRel' ]     . '"' : '';
                
                // Type parameter
                $type       = ( isset( $this->_conf[ 'cssType' ] )     && $this->_conf[ 'cssType' ] )     ? ' type="'    . $this->_conf[ 'cssType' ]     . '"' : '';
                
                // Media parameter
                $media      = ( isset( $this->_conf[ 'cssMedia' ] )    && $this->_conf[ 'cssMedia' ] )    ? ' media="'   . $this->_conf[ 'cssMedia' ]    . '"' : '';
                
                // Charset parameter
                $charset    = ( isset( $this->_conf[ 'linkCharset' ] ) && $this->_conf[ 'linkCharset' ] ) ? ' charset="' . $this->_conf[ 'linkCharset' ] . '"' : '';
                
                // Checks how to include the stylesheets
                if( isset( $conf[ 'importRules' ] ) && $conf[ 'importRules' ] ) {
                    
                    // Builds the <style> tag
                    $headerData[] = '<style' . $type . $media . '>';
                    $headerData[] = $startComment;
                    
                    // Checks if a comment must be included
                    if( isset( $conf[ 'cssComments' ] ) && $conf[ 'cssComments' ] ) {
                        
                        // Adds the CSS comment
                        $headerData[] = $this->_TAB . '/***************************************************************';
                        $headerData[] = $this->_TAB . ' * Styles added by plugin "tx_cssselect_pi1"';
                        $headerData[] = $this->_TAB . ' * ';
                        $headerData[] = $this->_TAB . ' * Index:';
                        $headerData[] = $this->_buildIndex();
                        $headerData[] = $this->_TAB . ' ***************************************************************/';
                    }
                    
                    // Adds the stylesheets
                    $headerData[] = $this->_buildImports();
                    $headerData[] = $endComment;
                    $headerData[] = '</style>';
                    
                } else {
                    
                    // Checks if a comment must be included
                    if( isset( $conf[ 'cssComments' ] ) && $conf[ 'cssComments' ] ) {
                        
                        // Adds the CSS comment
                        $headerData[] = '<!--';
                        $headerData[] = $this->_TAB . '/***************************************************************';
                        $headerData[] = $this->_TAB . ' * Styles added by plugin "tx_cssselect_pi1"';
                        $headerData[] = $this->_TAB . ' * ';
                        $headerData[] = $this->_TAB . ' * Index:';
                        $headerData[] = $this->_buildIndex();
                        $headerData[] = $this->_TAB . ' ***************************************************************/';
                        $headerData[] = '-->';
                        
                    }
                    
                    // Builds a <link> tag for each stylesheet
                    foreach( $this->_cssFiles as $key => $value ) {
                        
                        // Adds the stylesheet
                        $headerData[] = '<link' . $rel . ' href="' . $value[ 'file' ] . '"' . $type . $media . $charset . $endTag . '>';
                    }
                }
                
                // Return the header data
                return implode( $this->_NL, $headerData ) . $this->_NL;
            }
        }
    }
}

/** 
 * XClass inclusion.
 */
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/css_select/pi1/class.tx_cssselect_pi1.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/css_select/pi1/class.tx_cssselect_pi1.php']);
}
