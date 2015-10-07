<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Jan Bednarik (info@bednarik.org)
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
 * @author	Jan Bednarik <info@bednarik.org>
 */ 
 
class ux_t3lib_stdGraphic extends t3lib_stdGraphic {
  
  /**
	 * Converts $imagefile to another file in temp-dir of type $newExt (extension).
	 *
	 * @param	string		The image filepath
	 * @param	string		New extension, eg. "gif", "png", "jpg", "tif". If $newExt is NOT set, the new imagefile will be of the original format. If newExt = 'WEB' then one of the web-formats is applied.
	 * @param	string		Width. $w / $h is optional. If only one is given the image is scaled proportionally. If an 'm' exists in the $w or $h and if both are present the $w and $h is regarded as the Maximum w/h and the proportions will be kept
	 * @param	string		Height. See $w
	 * @param	string		Additional ImageMagick parameters.
	 * @param	string		Refers to which frame-number to select in the image. '' or 0 will select the first frame, 1 will select the next and so on...
	 * @param	array		  An array with options passed to getImageScale (see this function).
	 * @param	boolean		If set, then another image than the input imagefile MUST be returned. Otherwise you can risk that the input image is good enough regarding messures etc and is of course not rendered to a new, temporary file in typo3temp/. But this option will force it to.
	 * @return	array		[0]/[1] is w/h, [2] is file extension and [3] is the filename.
	 * @see getImageScale(), typo3/show_item.php, fileList_ext::renderImage(), tslib_cObj::getImgResource(), SC_tslib_showpic::show(), maskImageOntoImage(), copyImageOntoImage(), scale()
	 */
	function imageMagickConvert($imagefile,$newExt='',$w='',$h='',$params='',$frame='',$options='',$mustCreate=0)	{
    if ($this->NO_IMAGE_MAGICK)	{
			if ($info = $this->getImageDimensions($imagefile)) {
          $origW = $info[0];
          $origH = $info[1];

          $data = $this->getImageScale($info,$w,$h,$options);
          $w = $data['origW'];
				  $h = $data['origH'];

          // if no convertion should be performed
  				$wh_noscale = (!$w && !$h) || ($data[0]==$info[0] && $data[1]==$info[1]);		// this flag is true if the width / height does NOT dictate the image to be scaled!! (that is if no w/h is given or if the destination w/h matches the original image-dimensions....
          if ($wh_noscale && !$data['crs'] && !$params && ($newExt==$info[2] || $newExt=='web') && !$mustCreate) {
            $info[3] = $imagefile;
  					return $info;
  				}

  				$info[0] = $data[0]; // w
				  $info[1] = $data[1]; // h

          $ext = $info[2];
          $path = $info[3];

			    $frame = $this->noFramePrepended ? '' : '['.intval($frame).']';
			    $command = $this->scalecmd.' '.$info[0].'x'.$info[1].'! '.$params.' ';
				  $cropscale = ($data['crs'] ? 'crs-V'.$data['cropV'].'H'.$data['cropH'] : '');

          if ($this->alternativeOutputKey)	{
          	$theOutputName = t3lib_div::shortMD5($command.$cropscale.basename($imagefile).$this->alternativeOutputKey.$frame);
          } else {
          	$theOutputName = t3lib_div::shortMD5($command.$cropscale.$imagefile.filemtime($imagefile).$frame);
          }
          if ($this->imageMagickConvert_forceFileNameBody)	{
          	$theOutputName = $this->imageMagickConvert_forceFileNameBody;
          	$this->imageMagickConvert_forceFileNameBody='';
          }

          	// Making the temporary filename:
          $this->createTempSubDir('pics/');
          $output = $this->absPrefix.$this->tempPath.'pics/'.$this->filenamePrefix.$theOutputName.'.'.$ext;

           	// Register temporary filename:
  				$GLOBALS['TEMP_IMAGES_ON_PAGE'][] = $output;

  				if ($this->dontCheckForExistingTempFile || !$this->file_exists_typo3temp_file($output, $imagefile))	{
             if (($ext=='jpg')||($ext=='jpeg')||($ext=='png')||($ext=='gif')) {
                $newIm = imagecreatetruecolor($info[0],$info[1]);
                if ($ext == 'jpg' || $ext=='jpeg') {
                  $im = imagecreatefromjpeg($path);
                } elseif ($ext == 'gif') {
                  $im = imagecreatefromgif($path);
                  $transparentcolor = imagecolortransparent($im);
                  if ($transparentcolor>=0) {
                    imagefill($newIm,0,0,$transparentcolor);
                    imagecolortransparent($newIm,$transparentcolor);
                  }
                } else {
                  $im = imagecreatefrompng($path);

                  $trnprt_indx = imagecolortransparent($im);
                  // If we have a specific transparent color
                  if ($trnprt_indx >= 0) {
                    $trnprt_color = imagecolorsforindex($im, $trnprt_indx);
                    $trnprt_indx = imagecolorallocate($newIm, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
                    imagefill($newIm, 0, 0, $trnprt_indx);
                    imagecolortransparent($newIm, $trnprt_indx);
                  } else {
                    imagealphablending($newIm, false);
                    $color = imagecolorallocatealpha($newIm, 0, 0, 0, 127);
                    imagefill($newIm, 0, 0, $color);
                    imagesavealpha($newIm, true);
                  }
                }

                imagecopyresampled($newIm,$im,0,0,0,0,$info[0],$info[1],$origW,$origH);

                $this->createTempSubDir('pics/');

                if ($ext == 'jpg' || $ext=='jpeg') {
                  imagejpeg($newIm,$output);
                } elseif ($ext == 'gif') {
                  if ($transparentcolor>=0) {
                    imagetruecolortopalette($newIm, false, 255);
                    imagecolortransparent($newIm,$transparentcolor);
                  }
                  imagegif($newIm,$output);
                } else {
                  imagepng($newIm,$output);
                }
             }
          }

          if (@file_exists($output))	{
            $info[3] = $output;
  					$info[2] = $newExt;
  					if ($params) { // params could realisticly change some imagedata!
  						$info = $this->getImageDimensions($info[3]);
  					}
            return $info;
          }
      } else {
          $x = Array($w,$h,pathinfo($imagefile,PATHINFO_EXTENSION),$imagefile);
          return $x;
      }
		}
    return parent::imageMagickConvert($imagefile,$newExt,$w,$h,$params,$frame,$options,$mustCreate);
	}
  
}
?>
