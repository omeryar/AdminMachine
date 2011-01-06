<?php
//Requires
//PHP: 4.3
//GD: 2.0.28

/*PHP FUNCTIONS REQUIRES THESE VERSIONS
getimagesize PHP 4
image_type_to_mime_type PHP 4.3
imagecopyresized PHP 4
file_exists PHP 4
copy PHP 4
imagecreatetruecolor PHP 4.0.6, GD 2.0.1
imagecopyresized PHP 4
imagecopy PHP 4
imagecreatefromjpeg PHP 4, GD 1.8
imagecreatefromgif PHP 4, GD 2.0.28
imagecreatefrompng PHP 4
imagefilter PHP 5, GD
imagejpeg PHP 4, GD-1.8
imagepng PHP 4
imagegif PHP 4
imagerotate PHP 4.3 GD
imagesx PHP 4
imagesy PHP 4
*/

class PHPImageEditor 
{
	var $srcEdit = "";
	var $srcOriginal = "";
	var $srcPng = "";
	var $srcWorkWith = "";
	var $resourceWorkWith = false;
	var $mimeType = "";
	var $actionSaveAndClose = "save";
	var $actionRotateLeft = "rotate:90";
	var $actionRotateRight = "rotate:-90";
	var $actionGrayscale = "grayscale";
	var $actionContrast = "contrast";
	var $actionBrightness = "brightness";
	var $actionUndo = "undo";
	var $actionUpdate = "update";
	var $actionRotateIsSelected = false;
	var $actionRotate = "";
	var $actionSeparatorSign = "#";
	var $fieldNameKeepProportions = "keepproportions";
	var $errorMessages = array();
	var $formName = "phpimageeditor";
	var $inputWidth = -1;
	var $inputHeight = -1;
	var $inputCropLeft = 0;
	var $inputCropRight = 0;
	var $inputCropTop = 0;
	var $inputCropBottom = 0;
	var $inputKeepProportions = true;
	var $inputCropKeepProportions = false;
	var $inputCropKeepProportionsRatio = 1;
	var $inputPanel = MENU_RESIZE;
	var $inputLanguage = DEFUALT_LANGUAGE;
	var $inputContrast = 0;
	var $inputBrightness = 0;
	var $inputContrastLast = 0;
	var $inputBrightnessLast = 0;
	var $inputGrayscale = false;
	var	$httpImageKey = "imagesrc";
	var	$texts = array();
	var $actions = "";
	var $isPostBack = false;
	var $isAjaxPost = false;
	var $finalWidth = -1;
	var $finalHeight = -1;
	var $widthKeepProportions = -1;
	var $heightKeepProportions = -1;
	var $userId = "";
	
	var $contrastMax = 100;
	var $brightnessMax = 255;
	
	
	function PHPImageEditor()
	{
		$this->LoadLanguage();
		
		if (version_compare(phpversion(), PHP_VERSION_MINIMUM, "<"))
		{
			$this->errorMessages[] = phpversion()." ".$this->texts["OLD PHP VERSION"]." ".PHP_VERSION_MINIMUM;
			return;
		}
		
		$this->isPostBack = isset($_POST["actiontype"]);
		
		$srcEdit = "";
		
		if ($_GET[$this->httpImageKey] != NULL)
		{
			$srcEdit = $_GET[$this->httpImageKey];
		}
			
		if ($srcEdit == "")
		{
			$this->errorMessages[] = $this->texts["NO PROVIDED IMAGE"];
			return;
		}
		
		$this->srcEdit = urldecode($srcEdit);
				
		if (isset($_POST["userid"]))
			$this->userId = $_POST["userid"];
		else
			$this->userId = "_".time()."_".str_replace(".", "_", $_SERVER['REMOTE_ADDR']);
		
		$this->SetSrcOriginal();
		$this->SetSrcPng();
		$this->SetSrcWorkWith();
				
		if (!file_exists($this->srcEdit))
		{
			$this->errorMessages[] = $this->texts["IMAGE DOES NOT EXIST"];
			return;
		}
		
		$info = getimagesize($this->srcEdit);
		
		if (!$info)
		{
			$this->errorMessages[] = $this->texts["INVALID IMAGE TYPE"];
			return;
		}
		
		$this->mimeType = image_type_to_mime_type($info[2]);	
		
		if ($this->mimeType == image_type_to_mime_type(IMAGETYPE_JPEG) || $this->mimeType == image_type_to_mime_type(IMAGETYPE_GIF) || $this->mimeType == image_type_to_mime_type(IMAGETYPE_PNG))
		{	
			if (!$this->isPostBack)
				$this->SaveOriginal();
			
			$this->resourceWorkWith = $this->CreateImage($this->srcOriginal);
			$this->SavePng();
			copy($this->srcPng, $this->srcWorkWith);
			
			$this->resourceWorkWith = $this->CreateImage($this->srcPng);
		}
		else
		{
			$this->errorMessages[] = $this->texts["INVALID IMAGE TYPE"];
			return;
		}
		
		$this->finalWidth = $this->GetWidth();
		$this->finalHeight = $this->GetHeight();
		$this->widthKeepProportions = $this->GetWidth();
		$this->heightKeepProportions = $this->GetHeight();
		
		$this->GrantAllAccess($this->srcOriginal);
		$this->GrantAllAccess($this->srcPng);
		$this->GrantAllAccess($this->srcWorkWith);
		
		if ($this->isPostBack)
		{
			$this->actionRotateIsSelected = ($_POST["rotate"] != "-1");
			$this->actionRotate = $_POST["rotate"];
			$this->actions = $_POST["actions"];
			$this->isAjaxPost = ($_POST["isajaxpost"] == "true");
			
			$this->inputWidth = (int)$_POST["width"];
			$this->inputHeight = (int)$_POST["height"];
			$this->inputCropLeft = (int)$_POST["cropleft"]; 
			$this->inputCropRight = (int)$_POST["cropright"]; 
			$this->inputCropTop = (int)$_POST["croptop"]; 
			$this->inputCropBottom = (int)$_POST["cropbottom"]; 
			$this->inputPanel = (int)$_POST["panel"]; 
			$this->inputLanguage = $_POST["language"]; 
			$this->inputKeepProportions = ($_POST["keepproportionsval"] == "1"); 
			$this->inputCropKeepProportions = ($_POST["cropkeepproportionsval"] == "1");
			$this->inputCropKeepProportionsRatio = (float)$_POST["cropkeepproportionsratio"];
			$this->inputGrayscale = ($_POST["grayscaleval"] == "1"); 
			$this->inputBrightness = (int)$_POST["brightness"]; 
			$this->inputContrast = (int)$_POST["contrast"]; 
			$this->inputBrightnessLast = (int)$_POST["brightnesslast"]; 
			$this->inputContrastLast = (int)$_POST["contrastlast"];

			$this->Action($_POST["actiontype"]);
		}
	}
	
	function LoadLanguage()
	{
		$language = "";
		
		if (isset($_POST["language"]))
		{
			$this->inputLanguage = $_POST["language"]; 
			$language = $this->inputLanguage;
		}
		else if (isset($_GET["language"]))
		{
			$this->inputLanguage = $_GET["language"]; 
			$language = $this->inputLanguage;
		}
		else
			$language = DEFUALT_LANGUAGE;
			
		$tryLanguage = "language/".$language."/".$language.".com_admin.ini";
		if (file_exists($tryLanguage))
			$this->texts = PIE_GetTexts("language/".$language."/".$language.".com_admin.ini");
		else
			$this->texts = PIE_GetTexts("language/".DEFUALT_LANGUAGE."/".DEFUALT_LANGUAGE.".com_admin.ini");
	}
	
	function SetSrcOriginal()
	{
		$arr = explode("/", $this->srcEdit);
		$this->srcOriginal = IMAGE_ORIGINAL_PATH.$this->AddUserIdToImageSrc($arr[count($arr)-1]);
	}
	
	function SetSrcWorkWith()
	{
		$arr = explode("/", $this->srcEdit);
		$srcWorkWith = IMAGE_WORK_WITH_PATH.$this->AddUserIdToImageSrc($arr[count($arr)-1]);
		$srcWorkWith = substr($srcWorkWith, 0, strripos($srcWorkWith, ".")).".png";
		$this->srcWorkWith = $srcWorkWith;
	}
	
	function SetSrcPng()
	{
		$arr = explode("/", $this->srcEdit);
		$srcPng = IMAGE_PNG_PATH.$this->AddUserIdToImageSrc($arr[count($arr)-1]);
		$srcPng = substr($srcPng, 0, strripos($srcPng, ".")).".png";
		$this->srcPng = $srcPng;
	}
	
	function SaveOriginal()
	{
		copy($this->srcEdit, $this->srcOriginal);
		
		//Resize to fit in max width/height.
		$imageTmp = $this->CreateImage($this->srcOriginal);
		$finalWidth = $this->GetWidthFromImage($imageTmp);
		$finalHeight = $this->GetHeightFromImage($imageTmp);
		
		$doSave = false;
		
		if ($finalWidth > IMAGE_MAX_WIDTH)
		{
			$widthProp = IMAGE_MAX_WIDTH/$finalWidth;
			$finalWidth = IMAGE_MAX_WIDTH;
			$finalHeight = round($finalHeight*$widthProp);
			$doSave = true;
		}
		
		if ($finalHeight > IMAGE_MAX_HEIGHT)
		{
			$heightProp = IMAGE_MAX_HEIGHT/$finalHeight;
			$finalHeight = IMAGE_MAX_HEIGHT;
			$finalWidth = round($finalWidth*$heightProp);
			$doSave = true;
		}
		
		if ($doSave)
		{	
			$imageTmp = $this->ActionResize($finalWidth, $finalHeight, $imageTmp);
			$this->SaveImage($imageTmp, $this->srcOriginal);
		}
	}
	
	function SavePng()
	{
		$this->SaveImage($this->resourceWorkWith, $this->srcPng, image_type_to_mime_type(IMAGETYPE_PNG));
	}
	
	function ErrorHasOccurred()
	{
		return (count($this->errorMessages) > 0);
	}
	
	function GetWidthFinal()
	{
		return $this->finalWidth;
	}
	
	function GetHeightFinal()
	{
		return $this->finalHeight;
	}
	
	function GetWidth()
	{
		return $this->GetWidthFromImage($this->resourceWorkWith);
	}
	
	function GetWidthLast()
	{
		if ($this->isPostBack)
			return (int)$_POST["widthlast"];
	
		return $this->GetWidth();
	}
	
	function GetHeight()
	{
		return $this->GetHeightFromImage($this->resourceWorkWith);
	}
	
	function GetHeightLast()
	{
		if ($this->isPostBack)
			return (int)$_POST["heightlast"];
	
		return $this->GetWidth();
	}
	
	function GetWidthFromImage($image)
	{
		return imagesx($image);
	}
	
	function GetHeightFromImage($image)
	{
		return imagesy($image);
	}
	
	function Action($actionType)
	{
		$doSave = false;
		
		if ($actionType == $this->actionUndo)
		{
			$this->ActionUndo();
			$doSave = true;
		}
		
		if ($actionType == $this->actionUpdate || $actionType == $this->actionSaveAndClose)
		{
			if ($this->inputWidth != $this->GetWidthLast() || $this->inputHeight != $this->GetHeightLast())
				$this->actions .= $this->GetActionSeparator()."resize:".$this->inputWidth.",".$this->inputHeight;
				
			if ($this->inputCropLeft != 0 || $this->inputCropRight != 0 || $this->inputCropTop != 0 || $this->inputCropBottom != 0)
				$this->actions .= $this->GetActionSeparator()."crop:".$this->inputCropLeft.",".$this->inputCropRight.",".$this->inputCropTop.",".$this->inputCropBottom;
				
			$doSave = true;
		}
		
		if ($actionType == $this->actionUpdate && $this->inputGrayscale)
		{
			if (strpos($this->actions, $this->actionGrayscale) === false)
			{
				$this->actions .= $this->GetActionSeparator().$this->actionGrayscale.":0";
				$doSave = true;
			}
		}
		else if ($actionType == $this->actionUpdate && !$this->inputGrayscale)
		{
			if (!(strpos($this->actions, $this->actionGrayscale) === false))
			{
				$this->actions = str_replace($this->actionGrayscale.":0".$this->GetActionSeparator(), "", $this->actions);
				$this->actions = str_replace($this->GetActionSeparator().$this->actionGrayscale.":0", "", $this->actions);
				$this->actions = str_replace($this->actionGrayscale.":0", "", $this->actions);
				$doSave = true;
			}
		}
		
		if ($this->inputContrast != $this->inputContrastLast)
		{
			$this->actions .= $this->GetActionSeparator().$this->actionContrast.":".$this->inputContrast;
			$doSave = true;
		}
		
		if ($this->inputBrightness != $this->inputBrightnessLast)
		{
			$this->actions .= $this->GetActionSeparator().$this->actionBrightness.":".$this->inputBrightness;
			$doSave = true;
		}
		
		if ($this->actionRotateIsSelected)
		{
			if ($this->actionRotate == $this->actionRotateLeft)
			{
				$this->actions .= $this->GetActionSeparator().$this->actionRotateLeft;
				$doSave = true;
			}
			else if ($this->actionRotate == $this->actionRotateRight)
			{
				$this->actions .= $this->GetActionSeparator().$this->actionRotateRight;
				$doSave = true;
			}
		}
		
		$finalContrast = 0;
		$finalBrightness = 0;
		$finalContrastFound = false;
		$finalBrightnessFound = false;
		$finalGrayscale = false;
		
		if ($doSave && $this->actions != "")
		{
			$allActions = explode($this->actionSeparatorSign, $this->actions);
			
			$finalRotate = 0;
			$finalCropLeft = 0;
			$finalCropRight = 0;
			$finalCropTop = 0;
			$finalCropBottom = 0;
			
			$doSwitch = false;
			
			foreach ($allActions as $loopAction)
			{
				$actionDetail = explode(":", $loopAction);
				$actionValues = explode(",", $actionDetail[1]);
				
				if ($actionDetail[0] == "resize")
				{
					$this->finalWidth = (int)$actionValues[0];
					$this->finalHeight = (int)$actionValues[1];
				}
				else if ($actionDetail[0] == "crop")
				{
					$actionValueLeft = (int)$actionValues[0];
					$actionValueRight = (int)$actionValues[1];
					$actionValueTop = (int)$actionValues[2];
					$actionValueBottom = (int)$actionValues[3];
					
					$widthProp = 1;
					$heightProp = 1;
					
					if ($doSwitch)
					{
						$widthProp = (($this->GetHeight()-($finalCropTop + $finalCropBottom)) / $this->finalWidth);
						$heightProp = (($this->GetWidth()-($finalCropLeft + $finalCropRight)) / $this->finalHeight);
					}
					else 
					{
						$widthProp = (($this->GetWidth()-($finalCropLeft + $finalCropRight)) / $this->finalWidth);
						$heightProp = (($this->GetHeight()-($finalCropTop + $finalCropBottom)) / $this->finalHeight);
					}
					
					$cropLeft = $actionValueLeft * $widthProp;
					$cropRight = $actionValueRight * $widthProp;
					$cropTop = $actionValueTop * $heightProp;
					$cropBottom = $actionValueBottom * $heightProp;
					
					$cropValues = array();
					$cropValues[] = $cropRight;
					$cropValues[] = $cropBottom;
					$cropValues[] = $cropLeft;
					$cropValues[] = $cropTop;
					
					if ($finalRotate != 0)
						$cropValues = $this->RotateArray(($finalRotate/-90), $cropValues);
					
					$finalCropRight += $cropValues[0];
					$finalCropBottom += $cropValues[1];
					$finalCropLeft += $cropValues[2];
					$finalCropTop += $cropValues[3];

					$this->finalWidth -= ($actionValueLeft + $actionValueRight);
					$this->finalHeight -= ($actionValueTop + $actionValueBottom);
				}
				else if ($actionDetail[0] == $this->actionGrayscale && $this->inputGrayscale)
				{
					$finalGrayscale = true;
				}
				else if ($actionDetail[0] == "contrast")
				{
					$finalContrastFound = true;
					$finalContrast = $actionValues[0];
				}
				else if ($actionDetail[0] == "brightness")
				{
					$finalBrightnessFound = true;
					$finalBrightness = $actionValues[0];
				}
				else if ($actionDetail[0] == "rotate")
				{
					$finalRotate += (int)$actionValues[0];
					$finalWidthTmp = $this->finalWidth;
					$this->finalWidth = $this->finalHeight;
					$this->finalHeight = $finalWidthTmp;						
				}
				
				if ($finalRotate == -360 || $finalRotate == 360)
					$finalRotate = 0;
					
				$doSwitch = ($finalRotate != 0 && ($finalRotate == 90 || $finalRotate == 270 || $finalRotate == -90 || $finalRotate == -270));
			}
			
			//1. All effects.
			if ($finalGrayscale)
				$this->ActionGrayscale();
						
			if ($finalBrightnessFound)
				$this->ActionBrightness($finalBrightness);
				
			if ($finalContrastFound)
				$this->ActionContrast($finalContrast*-1);
				
			//2. Do cropping.
			$finalCropLeft = round($finalCropLeft);
			$finalCropRight = round($finalCropRight);
			$finalCropTop = round($finalCropTop);
			$finalCropBottom = round($finalCropBottom);
			if ($finalCropLeft != 0 || $finalCropRight != 0 || $finalCropTop != 0 || $finalCropBottom != 0)
				$this->ActionCrop($finalCropLeft, $finalCropRight, $finalCropTop, $finalCropBottom);	
			
			//3. Rotate
			if ($finalRotate != 0)
				$this->ActionRotate($finalRotate);
			
			//Calculate keep proportions values.
			if (round($this->finalWidth/$this->finalHeight,1) == round($this->GetWidth()/$this->GetHeight(),1))
			{
				//It seems to have the same proportions as the original. Use the original proportions value.
				$this->widthKeepProportions = $this->GetWidth();
				$this->heightKeepProportions = $this->GetHeight();
			}
			else 
			{
				//The proportions has been changed. Use the new width and height instead.
				$this->widthKeepProportions = $this->finalWidth;
				$this->heightKeepProportions = $this->finalHeight;
			}
				
			//4. Resize
			if ($this->finalWidth > 0 && $this->finalHeight > 0)
				$this->resourceWorkWith = $this->ActionResize($this->finalWidth, $this->finalHeight, $this->resourceWorkWith);
				
			$this->SaveImage($this->resourceWorkWith, $this->srcWorkWith, image_type_to_mime_type(IMAGETYPE_PNG));
		}

		$this->inputBrightness = $finalBrightness;
		$this->inputContrast = $finalContrast;
		$this->inputGrayscale = $finalGrayscale;
		
		if ($actionType == $this->actionSaveAndClose)
		{
			$this->SaveImage($this->resourceWorkWith, $this->srcEdit, $this->mimeType);
			unlink($this->srcOriginal);
			unlink($this->srcPng);
			unlink($this->srcWorkWith);
			$this->DeleteOldImages(IMAGE_ORIGINAL_PATH);
			$this->DeleteOldImages(IMAGE_PNG_PATH);
			$this->DeleteOldImages(IMAGE_WORK_WITH_PATH);
			$reloadParentBrowser = RELOAD_PARENT_BROWSER_ON_SAVE ? 'window.opener.location.reload();' : '';
			PIE_Echo('<script language="javascript" type="text/javascript">'.$reloadParentBrowser.'window.open(\'\',\'_parent\',\'\');window.close();</script>');
		}
	}
	
	function ActionResize($width, $height, $image)
	{
		$newImage = @imagecreatetruecolor($width, $height);
		imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, $this->GetWidthFromImage($image), $this->GetHeightFromImage($image));
		return $newImage;
	}
	
	function ActionCrop($cropLeft, $cropRight, $cropTop, $cropBottom)
	{
		$cropWidth = $this->GetWidth() - $cropLeft - $cropRight;
		$cropHeight = $this->GetHeight() - $cropTop - $cropBottom;
		
		$newImageCropped = @imagecreatetruecolor($cropWidth, $cropHeight);
		imagecopy($newImageCropped, $this->resourceWorkWith, 0, 0, $cropLeft, $cropTop, $cropWidth, $cropHeight); 
	
		$this->resourceWorkWith = $newImageCropped;
	}
	
	function ActionUndo()
	{
		$separatorPos = strrpos($this->actions, $this->actionSeparatorSign);
		if (!($separatorPos === false)) 
		{
			$this->actions = substr($this->actions, 0, $separatorPos);
		}
		else
		{
			$this->actions = "";
		}
	}
	
	function CreateImage($srcEdit)
	{
		$info = getimagesize($srcEdit);

		if (!$info)
			return NULL;
				
		$mimeType = image_type_to_mime_type($info[2]);	
		
		if ($mimeType == image_type_to_mime_type(IMAGETYPE_JPEG))
		{	
			return imagecreatefromjpeg($srcEdit);
		}
		else if ($mimeType == image_type_to_mime_type(IMAGETYPE_GIF))
		{	
			return imagecreatefromgif($srcEdit);
		}
		else if ($mimeType == image_type_to_mime_type(IMAGETYPE_PNG))
		{
			return imagecreatefrompng($srcEdit);
		}
		
		return NULL;
	}
	
	function ActionRotate($Degrees)
	{
		$this->resourceWorkWith = imagerotate($this->resourceWorkWith, $Degrees, 0);
	}
	
	function ActionGrayscale()
	{
		imagefilter($this->resourceWorkWith, IMG_FILTER_GRAYSCALE);
	}

	function ActionContrast($contrast)
	{
		//-100 = max contrast, 0 = no change, +100 = min contrast
		imagefilter($this->resourceWorkWith, IMG_FILTER_CONTRAST, $contrast);
	}
	
	function ActionBrightness($light)
	{
		//-255 = min brightness, 0 = no change, +255 = max brightness
		imagefilter($this->resourceWorkWith, IMG_FILTER_BRIGHTNESS, $light);
	}
	
	function GetErrorMessages()
	{
		if (count($this->errorMessages))
		{		
			PIE_Echo('<div class="error">');
			PIE_Echo('<ul>');
			
			foreach ($this->errorMessages as $errorMessage)
				PIE_Echo ('<li>'.$errorMessage.'</li>');
			
			PIE_Echo("</ul>");
			PIE_Echo('</div>');
		}
	}
	
	function GetActions()
	{
		PIE_Echo($this->actions);
	}
	
	function GetActionSeparator()
	{
		if ($this->actions != "")
			return $this->actionSeparatorSign;
			
		return "";
	}
	
	function SaveImage($image, $toSrc, $mimeType = -1)
	{
		if ($mimeType == -1)
			$mimeType = $this->mimeType;
		
		if ($mimeType == image_type_to_mime_type(IMAGETYPE_JPEG))
		{	
			imagejpeg($image, $toSrc);		
		}
		else if ($mimeType == image_type_to_mime_type(IMAGETYPE_GIF))
		{	
			imagegif($image, $toSrc);		
		}
		else if ($mimeType == image_type_to_mime_type(IMAGETYPE_PNG))
		{
			imagepng($image, $toSrc);		
		}
	}
	
	function CleanUp()
	{
		if ($this->resourceWorkWith)
			imagedestroy($this->resourceWorkWith);	
	}
	
	function RotateArray($numberOfSteps, $arr)
	{
		$finalArray = array();
		
		//-3 to 3
		$finalArray[] = $arr[$this->NumberOfStepsCalculator($numberOfSteps + 0)];
		$finalArray[] = $arr[$this->NumberOfStepsCalculator($numberOfSteps + 1)];
		$finalArray[] = $arr[$this->NumberOfStepsCalculator($numberOfSteps + 2)];
		$finalArray[] = $arr[$this->NumberOfStepsCalculator($numberOfSteps + 3)];
		
		return $finalArray;
	}
	
	function NumberOfStepsCalculator($sum)
	{
		$maxIndex = 3;
		if ($sum > $maxIndex)
			return ($sum-$maxIndex)-1; 
		else if ($sum < 0)
		{
			return ($sum+$maxIndex)+1; 
		}
			
		return $sum;
	}
	
	function AddUserIdToImageSrc($imageSrc)
	{
		return str_replace(".", $this->userId.".", $imageSrc);
	}

	function IsPHP5OrHigher()
	{
		return version_compare(phpversion(), "5", ">=");		
	}
	
	function GetFormAction()
	{
		return "index.php?".$this->httpImageKey."=".$this->srcEdit;
	}
	
	function GetWidthKeepProportions()
	{
		return $this->widthKeepProportions;
	}
	
	function GetHeightKeepProportions()
	{
		return $this->heightKeepProportions;
	}

	function DeleteOldImages($srcdir) 
	{
		if($curdir = opendir($srcdir)) 
		{
			while($file = readdir($curdir)) 
			{
				if($file != '.' && $file != '..') 
				{
					//$srcfile = $srcdir . '\\' . $file;
					//$srcfile = $srcdir.'/'.$file;
					$srcfile = $srcdir.$file;
					$srcfileLower = strtolower($srcfile);
					
					if (stripos($srcfile, ".svn") === false)
					{
						if(is_file($srcfile)) 
						{
							$doDelete = true;

							if (substr_count($srcfile, "sample.jpg") > 0 || substr_count($srcfile, "sample.png") > 0  || (substr_count($srcfileLower, ".jpg") == 0 && substr_count($srcfileLower, ".gif") == 0 && substr_count($srcfileLower, ".png") == 0))
								$doDelete = false; 
				            	
							if ($doDelete)
							{
							
								$deleteTime = mktime(0, 0, 0, date("m"), date("d")-2, date("Y"));
								if (fileatime($srcfile) < $deleteTime)
								{
									//Image is old and will be deleted. Or else the server space will be filled up with not needed images.
									//echo "<h2>DELETE $srcfile".date("F d Y H:i:s.", fileatime($srcfile))."</h2>";
									unlink($srcfile);
								}
							}
						}
					}
				}
			}
			closedir($curdir);
		}
	}

	function GrantAllAccess($fileName)
	{
		chmod($fileName, 0777);
	}
}

?>