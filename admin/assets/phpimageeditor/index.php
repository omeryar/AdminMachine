<?php  
	include 'includes/joomla_login_redirect.php';
	header("Cache-Control: no-store"); 
	header('content-type: text/html; charset: utf-8');
	include 'includes/constants.php';
	include 'config.php';
	include 'includes/functions.php';
	include 'classes/phpimageeditor.php';
	global $objPHPImageEditor;
	$objPHPImageEditor = new PHPImageEditor();
?>
<?php if (!$objPHPImageEditor->isAjaxPost) { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>PHP Image Editor</title>
	    <script type="text/javascript" src="javascript/jquery-1.3.2.min.js"></script>
	    <script type="text/javascript" src="javascript/jquery.jcrop.js"></script>
	    <script type="text/javascript" src="javascript/ui.core.js"></script>
	    <script type="text/javascript" src="javascript/ui.slider.js"></script>
	    <script type="text/javascript" src="javascript/ui.resizable.js"></script>
	    <script type="text/javascript" src="javascript/effects.core.js"></script>
	    
	    <link rel="stylesheet" type="text/css" href="css/style.css"/>
	    <link rel="stylesheet" type="text/css" href="css/ui.resizable.css"/>
	    <link rel="stylesheet" type="text/css" href="css/ui.slider.css"/>
	    <link rel="stylesheet" type="text/css" href="css/jquery.jcrop.css"/>
	    
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>
	<body>
		<div id="phpImageEditor">
<?php } ?>

			<?php include 'includes/form_javascript.php'; ?>

			<form id="<?php PIE_Echo($objPHPImageEditor->formName); ?>" name="<?php PIE_Echo($objPHPImageEditor->formName); ?>" method="post" action="<?php PIE_Echo($objPHPImageEditor->GetFormAction()); ?>">
				<?php if (!$objPHPImageEditor->ErrorHasOccurred()) { ?>
					 
					<div class="tabs">
					
						<div id="menu">
							<!-- <div class="<?php PIE_Echo($objPHPImageEditor->inputPanel == MENU_RESIZE ? 'selected' : 'not-selected'); ?>" id="menuitem_<?php PIE_Echo(MENU_RESIZE); ?>">
								<?php PIE_Echo($objPHPImageEditor->texts["RESIZE IMAGE"]); ?>
							</div> -->
							<div class="<?php PIE_Echo($objPHPImageEditor->inputPanel == MENU_ROTATE ? 'selected' : 'not-selected'); ?>" id="menuitem_<?php PIE_Echo(MENU_ROTATE); ?>">
								<?php PIE_Echo($objPHPImageEditor->texts["ROTATE IMAGE"]); ?>
							</div>
							<div class="<?php PIE_Echo($objPHPImageEditor->inputPanel == MENU_CROP ? 'selected' : 'not-selected'); ?>" id="menuitem_<?php PIE_Echo(MENU_CROP); ?>">
								<?php PIE_Echo($objPHPImageEditor->texts["CROP IMAGE"]); ?>
							</div>
							<?php if ($objPHPImageEditor->IsPHP5OrHigher()) { ?>
								<div class="<?php PIE_Echo($objPHPImageEditor->inputPanel == MENU_EFFECTS ? 'selected' : 'not-selected'); ?>" id="menuitem_<?php PIE_Echo(MENU_EFFECTS); ?>">
									<?php PIE_Echo($objPHPImageEditor->texts["EFFECTS"]); ?>
								</div>
							<?php } ?>
						</div>
							
						<div id="actionContainer">
			
							<div id="panel_<?php PIE_Echo(MENU_RESIZE); ?>" class="panel">
								<table cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td>	
											<div class="field widthAndHeight">
												<div class="col-1">
													<label for="width"><?php PIE_Echo($objPHPImageEditor->texts["WIDTH"]); ?></label>
													<input class="input-number" type="text" name="width" id="width" value="<?php PIE_Echo($objPHPImageEditor->GetWidthFinal()); ?>"/>
													<input type="hidden" name="widthoriginal" id="widthoriginal" value="<?php PIE_Echo($objPHPImageEditor->GetWidth()); ?>"/>
												</div>
												<div class="col-2">
													<label for="height"><?php PIE_Echo($objPHPImageEditor->texts["HEIGHT"]); ?></label>
													<input class="input-number" type="text" name="height" id="height" value="<?php PIE_Echo($objPHPImageEditor->GetHeightFinal()); ?>"/>
													<input type="hidden" name="heightoriginal" id="heightoriginal" value="<?php PIE_Echo($objPHPImageEditor->GetHeight()); ?>"/>
												</div>
											</div>
											<div class="field">
												<input id="keepproportions" class="checkbox" type="checkbox" name="<?php PIE_Echo($objPHPImageEditor->fieldNameKeepProportions); ?>" id="<?php PIE_Echo($objPHPImageEditor->fieldNameKeepProportions); ?>" <?php PIE_Echo($objPHPImageEditor->inputKeepProportions ? 'checked="checked"' : ''); ?>/>
												<input type="hidden" name="keepproportionsval" id="keepproportionsval" value="<?php PIE_Echo($objPHPImageEditor->inputKeepProportions ? '1' : '0'); ?>"/>
												<label for="<?php PIE_Echo($objPHPImageEditor->fieldNameKeepProportions); ?>" class="checkbox"><?php PIE_Echo($objPHPImageEditor->texts["KEEP PROPORTIONS"]); ?></label>
											</div>
										</td>
										<td>
											<div class="help" id="resizehelp">
												<div class="help-header" id="resizehelpheader"><?php PIE_Echo($objPHPImageEditor->texts["INSTRUCTIONS"]); ?></div>
												<div class="help-content" id="resizehelpcontent"><?php PIE_Echo($objPHPImageEditor->texts["RESIZE HELP"]); ?></div>
											</div>
										</td>
									</tr>
								</table>
							</div>
		
							<div id="panel_<?php PIE_Echo(MENU_ROTATE); ?>" class="panel">
								<div class="field">
									<input id="btnRotateLeft" type="button" value="<?php PIE_Echo($objPHPImageEditor->texts["LEFT 90 DEGREES"]); ?>"/>
									<input id="btnRotateRight" type="button" value="<?php PIE_Echo($objPHPImageEditor->texts["RIGHT 90 DEGREES"]); ?>"/>
									<input type="hidden" name="rotate" id="rotate" value="-1"/>
								</div>
							</div>
		
							<div id="panel_<?php PIE_Echo(MENU_CROP); ?>" class="panel">
								<div class="field">
									<input class="input-number" type="hidden" name="croptop" id="croptop" value="0"/>
									<input class="input-number" type="hidden" name="cropleft" id="cropleft" value="0"/>
									<input class="input-number" type="hidden" name="cropright" id="cropright" value="0"/>
									<input class="input-number" type="hidden" name="cropbottom" id="cropbottom" value="0"/>
									<div class="help" id="crophelp">
										<div class="help-header" id="crophelpheader"><?php PIE_Echo($objPHPImageEditor->texts["INSTRUCTIONS"]); ?></div>
										<div class="help-content" id="crophelpcontent"><?php PIE_Echo($objPHPImageEditor->texts["CROP HELP"]); ?></div>
									</div>
								</div>
								<div class="field crop-settings">
									<div class="crop-top">
										<?php PIE_Echo($objPHPImageEditor->texts["CROP WIDTH"]); ?>: <span id="cropwidth">0</span>
										<?php PIE_Echo($objPHPImageEditor->texts["CROP HEIGHT"]); ?>: <span id="cropheight">0</span>
									</div>
									<input id="cropkeepproportions" class="checkbox" type="checkbox" name="cropkeepproportions" <?php PIE_Echo($objPHPImageEditor->inputCropKeepProportions ? 'checked="checked"' : ''); ?>/>
									<label class="checkbox" for="cropkeepproportions"><?php PIE_Echo($objPHPImageEditor->texts["CROP KEEP PROPORTIONS"]); ?></label>
									<input id="cropkeepproportionsval" type="hidden" name="cropkeepproportionsval" value="<?php PIE_Echo($objPHPImageEditor->inputCropKeepProportions ? '1' : '0'); ?>"/>									
									<input id="cropkeepproportionsratio" type="hidden" name="cropkeepproportionsratio" value="<?php PIE_Echo($objPHPImageEditor->inputCropKeepProportionsRatio); ?>"/>									
								</div>
							</div>
							<div id="panel_<?php PIE_Echo(MENU_EFFECTS); ?>" class="panel" style="display: <?php PIE_Echo($objPHPImageEditor->IsPHP5OrHigher() ? 'block' : 'none'); ?>;">
								<div class="field">
									<label for="brightness"><?php PIE_Echo($objPHPImageEditor->texts["BRIGHTNESS"]); ?></label>
									<div id="brightness_slider_track"></div>
								</div>
								<input type="hidden" name="brightness" id="brightness" value="<?php PIE_Echo($objPHPImageEditor->inputBrightness); ?>"/>
								<div class="field">
									<label for="contrast"><?php PIE_Echo($objPHPImageEditor->texts["CONTRAST"]); ?></label>
									<div id="contrast_slider_track"></div>
								</div>
								<input type="hidden" name="contrast" id="contrast" value="<?php PIE_Echo($objPHPImageEditor->inputContrast); ?>"/>
								<div class="field">
									<input id="grayscale" class="checkbox" type="checkbox" name="<?php PIE_Echo($objPHPImageEditor->actionGrayscale); ?>" id="<?php PIE_Echo($objPHPImageEditor->actionGrayscale); ?>" <?php PIE_Echo($objPHPImageEditor->inputGrayscale ? 'checked="checked"' : ''); ?>/>
									<label for="<?php PIE_Echo($objPHPImageEditor->actionGrayscale); ?>" class="checkbox"><?php PIE_Echo($objPHPImageEditor->texts["GRAYSCALE"]); ?></label>
									<input type="hidden" name="grayscaleval" id="grayscaleval" value="<?php PIE_Echo($objPHPImageEditor->inputGrayscale ? '1' : '0'); ?>"/>
								</div>
							</div>
							<div id="loading" style="display: none;"><?php PIE_Echo($objPHPImageEditor->texts["LOADING"]); ?>...<div id="loading_bar" style="width: 0px;"></div></div>
		
						</div>
						
						<div class="main-actions">
							<input type="button" id="btnupdate" name="btnupdate" value="<?php PIE_Echo($objPHPImageEditor->texts["UPDATE"]); ?>"/>
							<input type="button" <?php PIE_Echo($objPHPImageEditor->actions == "" ? 'disabled="disabled"' : ''); ?> id="btnsave" name="btnsave" value="<?php PIE_Echo($objPHPImageEditor->texts["SAVE AND CLOSE"]); ?>"/>
							<input type="button" <?php PIE_Echo($objPHPImageEditor->actions == "" ? 'disabled="disabled"' : ''); ?> id="btnundo" name="btnundo" value="<?php PIE_Echo($objPHPImageEditor->texts["UNDO"]); ?>"/>
						</div>
		
					</div>
					<input type="hidden" name="actiontype" id="actiontype" value="<?php PIE_Echo($objPHPImageEditor->actionUpdate); ?>"/>
					<input type="hidden" name="panel" id="panel" value="<?php PIE_Echo($objPHPImageEditor->inputPanel); ?>"/>
					<input type="hidden" name="language" id="language" value="<?php PIE_Echo($objPHPImageEditor->inputLanguage); ?>"/>
					<input type="hidden" name="actions" id="actions" style="width: 1000px;" value="<?php $objPHPImageEditor->GetActions(); ?>"/>
					<input type="hidden" name="widthlast" id="widthlast" value="<?php PIE_Echo($objPHPImageEditor->GetWidthFinal()); ?>"/>
					<input type="hidden" name="heightlast" id="heightlast" value="<?php PIE_Echo($objPHPImageEditor->GetHeightFinal()); ?>"/>
					<input type="hidden" name="widthlastbeforeresize" id="widthlastbeforeresize" value="<?php PIE_Echo($objPHPImageEditor->GetWidthKeepProportions()); ?>"/>
					<input type="hidden" name="heightlastbeforeresize" id="heightlastbeforeresize" value="<?php PIE_Echo($objPHPImageEditor->GetHeightKeepProportions()); ?>"/>
					<input type="hidden" name="userid" id="userid" value="<?php PIE_Echo($objPHPImageEditor->userId); ?>"/>
					<input type="hidden" name="contrastlast" id="contrastlast" value="<?php PIE_Echo($objPHPImageEditor->inputContrast); ?>"/>
					<input type="hidden" name="brightnesslast" id="brightnesslast" value="<?php PIE_Echo($objPHPImageEditor->inputBrightness); ?>"/>
					<input type="hidden" name="isajaxpost" id="isajaxpost" value="false"/>
				<?php } ?>
			</form>
			<?php $objPHPImageEditor->GetErrorMessages(); ?>
			<div id="divJsErrors" class="error" style="display: none;">
				<ul id="ulJsErrors" style="display: none;">
				</ul>
			</div>
			<div><img src="images/empty.gif" alt=""/></div>
			<?php if (!$objPHPImageEditor->ErrorHasOccurred()) { ?>
				<div id="editimage">
					<img id="image" style="position: absolute; left: 0px; top: 0px; width: <?php PIE_Echo($objPHPImageEditor->GetWidthFinal()); ?>px; height: <?php PIE_Echo($objPHPImageEditor->GetHeightFinal()); ?>px;" alt="" src="<?php PIE_Echo($objPHPImageEditor->srcWorkWith); ?>?timestamp=<?php PIE_Echo(time()); ?>"/>
					<div id="imageResizerKeepProportions" style="diplay: <?php PIE_Echo(($objPHPImageEditor->inputKeepProportions && $objPHPImageEditor->inputPanel == MENU_RESIZE) ? 'block' : 'none'); ?>; width: <?php PIE_Echo($objPHPImageEditor->GetWidthFinal()); ?>px; height: <?php PIE_Echo($objPHPImageEditor->GetHeightFinal()); ?>px;"></div>
					<div id="imageResizerNoProportions" style="diplay: <?php PIE_Echo((!$objPHPImageEditor->inputKeepProportions && $objPHPImageEditor->inputPanel == MENU_RESIZE) ? 'block' : 'none'); ?>; width: <?php PIE_Echo($objPHPImageEditor->GetWidthFinal()); ?>px; height: <?php PIE_Echo($objPHPImageEditor->GetHeightFinal()); ?>px;"></div>
				</div>	
			<?php } ?>

<?php if (!$objPHPImageEditor->isAjaxPost) { ?>
		</div>
	</body>
	</html>
<?php } ?>

<?php $objPHPImageEditor->CleanUp(); ?>