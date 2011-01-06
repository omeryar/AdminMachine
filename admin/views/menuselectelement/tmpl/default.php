<?php
/**
 * @package adminMachine
 * @subpackage 
 * @license cc-by-sa
 */

defined('_JEXEC') or die('Restricted access');
foreach ($this->data['menus'] as $menu ) 
{
	$menuOptions[] = JHTML::_('select.option', $menu->admin_menu_alt, $menu->id);
}
$selectHTML =  JHTML::_('select.genericlist', $menuOptions , 'menuSelect', '' , 'text', 'value');

$document =& JFactory::getDocument();
JHTML::_('behavior.mootools');

$js = "	
 	window.addEvent('domready', function() {
		
		  $('adminForm').addEvent('submit', function(e) 
          { 
               // Stop the form from submitting 
               new Event(e).stop(); 
               // Update the page 
               this.send({ onComplete: function (result) {

					res = eval( '(' + result + ')' );
					$('msg').setHTML(res.msg);
					if(res.success)
					{	
						close = $('cancelButton').clone()
						$('adminForm').remove();
						close.setProperty('value', '".JText::_('Close')."').injectInside($('msg'));
					}
					
				}
			}); 
          });

		$('cancelButton').addEvent('click', function(e) {
			window.parent.closeModal();
		});
		
	});

	
";

$document->addScriptDeclaration($js);


?>
<div id="msg" class="col100">
</div>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div class="col100">
    <fieldset class="adminform">
        <legend><?php echo JText::_( 'Add To Menu' ); ?></legend>
        <table class="admintable">
			<tr>
				<td width="100" align="left" class="key">
					<label for="menuSelect"><?php echo JText::_('Add'); ?></label>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $this->data['component'].' => '.$this->data['dataset']; ?>
				</td>
			</tr>
			<tr>
				<td width="100" align="left" class="key">
					<label for="menuSelect"><?php echo JText::_('To Menu'); ?></label>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $selectHTML; ?>
				</td>
			</tr>
			<tr>
				<td width="100" align="left" class="key">
					<label for="nameSelect"><?php echo JText::_('And Name It'); ?></label>
				</td>
			</tr>
			<tr>
				<td>
					<input type="text" id="nameSelect" name="nameSelect" value="<?php echo $this->data['dataset']; ?>" />
				</td>
			</tr>  
			<tr>
				<td width="100" align="left" class="key">
					<input type="submit" id="submitButton" name="submitButton" value="Apply" />
					<input type="button" id="cancelButton" name="cancelButton" value="Cancel" />
				</td>
			</tr>  
		</table>
</div>
 
<div class="clr"></div>
<input type="hidden" name="option" value="com_adminMachine" />
<input type="hidden" name="dataset" value="<?php echo $this->data['dataset']; ?>" />
<input type="hidden" name="component" value="<?php echo $this->data['component']; ?>" />
<input type="hidden" name="task" value="addToMenu" />
<input type="hidden" name="controller" value="adminmachine" />
    </fieldset>
</form>