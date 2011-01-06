<?php
/**
	Sam Gallery 1.0 beta
	Created: 30-Jan-2009
	Author: Dinesh Kumar V
	Email: mail_to_din@yahoo.co.in
	
	Web: www.sam-sys.com and www.4designerz.com	
	License: MIT License
	copyright: (C) 2009 - 2010 sam sys pvt ltd
	
**/

defined('_JEXEC') or die('Restricted access');


class SamImage {

	/**
	* Creates a Thumbnail of an image
 	*
 	* @param string $file The path to the file
	* @param string $save The targetpath
	* @param string $width The with of the image
	* @param string $height The height of the image
	* @return true when success
	*/
	function thumb($file, $save, $width, $height, $ratioby)
	{
		//GD-Lib > 2.0 only!
		@unlink($save);

		//get sizes else stop
		if (!$infos = @getimagesize($file)) {
			return false;
		}

		// keep proportions
		$iWidth = $infos[0];
		$iHeight = $infos[1];
		$iRatioW = $width / $iWidth;
		$iRatioH = $height / $iHeight;

		switch($ratioby)
		{
			case 1:				
				$iNewW = $iWidth * $iRatioW;
				$iNewH = $iHeight * $iRatioW;
				break;
			case 2:				
				$iNewW = $iWidth * $iRatioH;
				$iNewH = $iHeight * $iRatioH;
				break;
			default:			
				if ($iRatioW < $iRatioH) {
					$iNewW = $iWidth * $iRatioW;
					$iNewH = $iHeight * $iRatioW;
				} else {
					$iNewW = $iWidth * $iRatioH;
					$iNewH = $iHeight * $iRatioH;
				}
		}

		//Don't resize images which are smaller than thumbs
		if ($infos[0] < $width && $infos[1] < $height) {
			$iNewW = $infos[0];
			$iNewH = $infos[1];
		}

		if($infos[2] == 1) {
			/*
			* Image is typ gif
			*/
			$imgA = imagecreatefromgif($file);
			$imgB = imagecreate($iNewW,$iNewH);
			
       		//keep gif transparent color if possible
          	if(function_exists('imagecolorsforindex') && function_exists('imagecolortransparent')) {
            	$transcolorindex = imagecolortransparent($imgA);
            		//transparent color exists
            		if($transcolorindex >= 0 ) {
             			$transcolor = imagecolorsforindex($imgA, $transcolorindex);
              			$transcolorindex = imagecolorallocate($imgB, $transcolor['red'], $transcolor['green'], $transcolor['blue']);
              			imagefill($imgB, 0, 0, $transcolorindex);
              			imagecolortransparent($imgB, $transcolorindex);
              		//fill white
            		} else {
              			$whitecolorindex = @imagecolorallocate($imgB, 255, 255, 255);
              			imagefill($imgB, 0, 0, $whitecolorindex);
            		}
            //fill white
          	} else {
            	$whitecolorindex = imagecolorallocate($imgB, 255, 255, 255);
            	imagefill($imgB, 0, 0, $whitecolorindex);
          	}
          	imagecopyresampled($imgB, $imgA, 0, 0, 0, 0, $iNewW, $iNewH, $infos[0], $infos[1]);
			imagegif($imgB, $save);        

		} elseif($infos[2] == 2) {
			/*
			* Image is typ jpg
			*/
			$imgA = imagecreatefromjpeg($file);
			$imgB = imagecreatetruecolor($iNewW,$iNewH);
			imagecopyresampled($imgB, $imgA, 0, 0, 0, 0, $iNewW, $iNewH, $infos[0], $infos[1]);
			imagejpeg($imgB, $save);

		} elseif($infos[2] == 3) {
			/*
			* Image is typ png
			*/
			$imgA = imagecreatefrompng($file);
			$imgB = imagecreatetruecolor($iNewW, $iNewH);
			imagealphablending($imgB, false);
			imagecopyresampled($imgB, $imgA, 0, 0, 0, 0, $iNewW, $iNewH, $infos[0], $infos[1]);
			imagesavealpha($imgB, true);
			imagepng($imgB, $save);
		} else {
			return false;
		}
		return true;
	}

	/**
	* Creates a watermark over an image
 	*
 	* @param string $file The path to the file
	* @param object $samsettings 
	* @return true when success
	*/
	function addWatermark($file,$samsettings,$fontfile,$wimg='')
	{
		if (!$infos = @getimagesize($file)) {
			return false;
		}
		
		$iWidth = $infos[0];
		$iHeight = $infos[1];
		
		if($infos[2] == 1) 	$imgA = imagecreatefromgif($file);
		if($infos[2] == 2) 	$imgA = imagecreatefromjpeg($file);
		if($infos[2] == 3) 	$imgA = imagecreatefrompng($file);		
        imagealphablending($imgA, TRUE);
		
		if($samsettings->watermark_type==1)
		{
			$nol = strlen($samsettings->watermark_text);
			
			$letr = (int) $iWidth/$nol;
			if($letr<40) $fontsize = $letr; else $fontsize = 40;
			
			$color = imagecolorallocate($imgA, 240, 240, 240);
			
			$ofino = imagettftext($imgA,$fontsize,0,0,-40,$color,$fontfile,$samsettings->watermark_text);
			$iX = (int) ($iWidth - $ofino[2])/2;
			$iY = (int) ($iHeight)/2;
			
			imagettftext($imgA,$fontsize,0,$iX,$iY,$color,$fontfile,$samsettings->watermark_text);
		}
		if($samsettings->watermark_type==2 && $wimg!='')
		{
			$infos2 = @getimagesize($wimg);		
			$iWidth2 = $infos2[0];
			$iHeight2 = $infos2[1];
			
			if($infos2[2] == 1) 	$imgB = imagecreatefromgif($wimg);
			if($infos2[2] == 2) 	$imgB = imagecreatefromjpeg($wimg);
			if($infos2[2] == 3) 	$imgB = imagecreatefrompng($wimg);
					
			
			if($iWidth < $iWidth2-40 || $iHeight<$iHeight2-40)
			{
				$newWH = $iWidth< $iHeight ? $iWidth -40 : $iHeight-40;	
				$iRatioW = $newWH / $iWidth;
				$iRatioH = $newWH / $iHeight;
				
				if ($iRatioW < $iRatioH) {
					$iWidth2 = $iWidth2 * $iRatioW;
					$iHeight2 = $iHeight2 * $iRatioW;
				} else {
					$iWidth2 = $iWidth2 * $iRatioH;
					$iHeight2 = $iHeight2 * $iRatioH;
				}
				$imgB2 = imagecreate($iWidth2, $iHeight2);
				imagealphablending($imgB2, TRUE);
				imagecopyresized($imgB2, $imgB,0, 0, 0, 0, $iWidth2, $iHeight2, $infos2[0], $infos2[1]);
				$imgB = $imgB2;
			}
			
			
			
			$iX = (int) ($iWidth - $iWidth2)/2;
			$iY = (int) ($iHeight - $iHeight2)/2;
			
			//imagecopymerge($imgA, $imgB, $iX, $iY, 0, 0, $iWidth2, $iHeight2, 60);
			imagecopy($imgA, $imgB, $iX, $iY, 0, 0, $iWidth2, $iHeight2);			
			imagedestroy($imgB);
			
		}
		
		if($infos[2] == 1) 	imagegif($imgA,$file);
		if($infos[2] == 2) 	imagejpeg($imgA,$file);
		if($infos[2] == 3) 	{
			imagealphablending($imgA,FALSE);
			imagesavealpha($imgA,TRUE);
			imagepng($imgA,$file);
		}
		imagedestroy($imgA);
	}
	
	
	
	
	/**
	* Determine the GD version
	* Code from php.net
	*
	* @return int
	*/
	function gdVersion($user_ver = 0)
	{
		if (! extension_loaded('gd')) {
			return;
		}
		static $gd_ver = 0;

		// Just accept the specified setting if it's 1.
		if ($user_ver == 1) {
			$gd_ver = 1;
			return 1;
		}
		// Use the static variable if function was called previously.
		if ($user_ver !=2 && $gd_ver > 0 ) {
			return $gd_ver;
		}
		// Use the gd_info() function if possible.
		if (function_exists('gd_info')) {
			$ver_info = gd_info();
			preg_match('/\d/', $ver_info['GD Version'], $match);
			$gd_ver = $match[0];
			return $match[0];
		}
		// If phpinfo() is disabled use a specified / fail-safe choice...
		if (preg_match('/phpinfo/', ini_get('disable_functions'))) {
			if ($user_ver == 2) {
				$gd_ver = 2;
				return 2;
			} else {
				$gd_ver = 1;
				return 1;
			}
		}
		// ...otherwise use phpinfo().
		ob_start();
		phpinfo(8);
		$info = ob_get_contents();
		ob_end_clean();
		$info = stristr($info, 'gd version');
		preg_match('/\d/', $info, $match);
		$gd_ver = $match[0];

		return $match[0];
	}

	/**
	* Creates image information of an image
	*
	* @author Christoph Lukes
	* @since 0.9
	*
	* @param string $image The image name
	* @param array $settings
	* @param string $type event or venue
	*
	* @return imagedata if available
	*/
	function flyercreator($image, $type= 'venue')
	{
		$settings = & ELHelper::config();
		
		jimport('joomla.filesystem.file');

		//define the environment based on the type
		if ($type == 'event') {
			$folder		= 'events';
		} else {
			$folder 	= 'venues';
		}

		if ( $image ) {

			//Create thumbnail if enabled and it does not exist already
			if ($settings->gddisabled == 1 && !file_exists(JPATH_SITE.'/images/eventlist/'.$folder.'/small/'.$image)) {

				$filepath 	= JPATH_SITE.'/images/eventlist/'.$folder.'/'.$image;
				$save 		= JPATH_SITE.'/images/eventlist/'.$folder.'/small/'.$image;

				SamImage::thumb($filepath, $save, $settings->imagewidth, $settings->imagehight);
			}

			//set paths
			$dimage['original'] = 'images/eventlist/'.$folder.'/'.$image;
			$dimage['thumb'] 	= 'images/eventlist/'.$folder.'/small/'.$image;

			//get imagesize of the original
			$iminfo = @getimagesize('images/eventlist/'.$folder.'/'.$image);

			//if the width or height is too large this formula will resize them accordingly
			if (($iminfo[0] > $settings->imagewidth) || ($iminfo[1] > $settings->imagehight)) {

				$iRatioW = $settings->imagewidth / $iminfo[0];
				$iRatioH = $settings->imagehight / $iminfo[1];

				if ($iRatioW < $iRatioH) {
					$dimage['width'] 	= round($iminfo[0] * $iRatioW);
					$dimage['height'] 	= round($iminfo[1] * $iRatioW);
				} else {
					$dimage['width'] 	= round($iminfo[0] * $iRatioH);
					$dimage['height'] 	= round($iminfo[1] * $iRatioH);
				}

			} else {

				$dimage['width'] 	= $iminfo[0];
				$dimage['height'] 	= $iminfo[1];

			}

			if (JFile::exists(JPATH_SITE.'/images/eventlist/'.$folder.'/small/'.$image)) {

				//get imagesize of the thumbnail
				$thumbiminfo = @getimagesize('images/eventlist/'.$folder.'/small/'.$image);
				$dimage['thumbwidth'] 	= $thumbiminfo[0];
				$dimage['thumbheight'] 	= $thumbiminfo[1];

			}
			return $dimage;
		}
		return false;
	}

	function check($file, $samsettings)
	{
		jimport('joomla.filesystem.file');

		$sizelimit 	= $samsettings->upload_size*1024; //size limit in kb
		$imagesize 	= $file['size'];

		//check if the upload is an image...getimagesize will return false if not
		if (!getimagesize($file['tmp_name'])) {
			JError::raiseWarning(100, JText::_('UPLOAD FAILED NOT AN IMAGE').': '.htmlspecialchars($file['name'], ENT_COMPAT, 'UTF-8'));
			return false;
		}

		//check if the imagefiletype is valid
		$fileext 	= JFile::getExt($file['name']);

		$allowable 	= array ('gif', 'jpg', 'png');
		if (!in_array($fileext, $allowable)) {
			JError::raiseWarning(100, JText::_('WRONG IMAGE FILE TYPE').': '.htmlspecialchars($file['name'], ENT_COMPAT, 'UTF-8'));
			return false;
		}

		//Check filesize
		if ($imagesize > $sizelimit) {
			JError::raiseWarning(100, JText::_('IMAGE FILE SIZE').': '.htmlspecialchars($file['name'], ENT_COMPAT, 'UTF-8'));
			return false;
		}

		//XSS check
		$xss_check =  JFile::read($file['tmp_name'],false,256);
		$html_tags = array('abbr','acronym','address','applet','area','audioscope','base','basefont','bdo','bgsound','big','blackface','blink','blockquote','body','bq','br','button','caption','center','cite','code','col','colgroup','comment','custom','dd','del','dfn','dir','div','dl','dt','em','embed','fieldset','fn','font','form','frame','frameset','h1','h2','h3','h4','h5','h6','head','hr','html','iframe','ilayer','img','input','ins','isindex','keygen','kbd','label','layer','legend','li','limittext','link','listing','map','marquee','menu','meta','multicol','nobr','noembed','noframes','noscript','nosmartquotes','object','ol','optgroup','option','param','plaintext','pre','rt','ruby','s','samp','script','select','server','shadow','sidebar','small','spacer','span','strike','strong','style','sub','sup','table','tbody','td','textarea','tfoot','th','thead','title','tr','tt','ul','var','wbr','xml','xmp','!DOCTYPE', '!--');
		foreach($html_tags as $tag) {
			// A tag is '<tagname ', so we need to add < and a space or '<tagname>'
			if(stristr($xss_check, '<'.$tag.' ') || stristr($xss_check, '<'.$tag.'>')) {
				JError::raiseWarning(100, JText::_('WARN IE XSS'));
				return false;
			}
		}

		return true;
	}


	function sanitize($base_Dir, $filename)
	{
		jimport('joomla.filesystem.file');

		//check for any leading/trailing dots and remove them (trailing shouldn't be possible cause of the getEXT check)
		$filename = preg_replace( "/^[.]*/", '', $filename );
		$filename = preg_replace( "/[.]*$/", '', $filename ); //shouldn't be necessary, see above

		//we need to save the last dot position cause preg_replace will also replace dots
		$lastdotpos = strrpos( $filename, '.' );

		//replace invalid characters
		$chars = '[^0-9a-zA-Z()_-]';
		$filename 	= strtolower( preg_replace( "/$chars/", '_', $filename ) );

		//get the parts before and after the dot (assuming we have an extension...check was done before)
		$beforedot	= substr( $filename, 0, $lastdotpos );
		$afterdot 	= substr( $filename, $lastdotpos + 1 );

		//make a unique filename for the image and check it is not already taken
		//if it is already taken keep trying till success
		$now = time();

		while( JFile::exists( $base_Dir . $beforedot . '_' . $now . '.' . $afterdot ) )
		{
   			$now++;
		}

		//create out of the seperated parts the new filename
		$filename = $beforedot . '_' . $now . '.' . $afterdot;

		return $filename;
	}
}
?>