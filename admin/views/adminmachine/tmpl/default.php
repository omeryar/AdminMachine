<?php
/**
 * @package adminMachine
 * @subpackage 
 * @license cc-by-sa
 */

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.mootools'); 
JHTML::_('behavior.modal');

if(!$this->data['components'] || count($this->data['components']) == 0) :
?>
<div class="errorMsg">
	<div class="error"><?php echo JText::_('No Component Datasets Found'); ?></div>
</div>
<?php else : ?>
<?php

$document =& JFactory::getDocument();
$js = "

	window.addEvent('domready', function() 
      { 
          $('adminForm').addEvent('submit', function(e) 
          { 
               // Stop the form from submitting 
               new Event(e).stop(); 
               // Update the page 
               this.send({ update: $('update') }); 
          }); 
      });


	function closeModal()
	{
		$('sbox-window').close();
	}
";

$document->addScriptDeclaration($js);

?>
<form action="index.php" method="post" id="adminForm" name="adminForm">
<div id="editcell">
    <table class="adminlist">
    <thead>
        <tr>
            <th>
                <?php echo JText::_( 'Components' ); ?>
            </th>
        </tr>            
    </thead>
	<tbody>
		<tr>
			<td>
			<?php foreach($this->data['components'] as $name => $component) : ?>
				<table class="adminlist" width="100%">
					<thead>
						<tr>
							<th colspan="2"><?php echo $name; ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($component as $dataset) : ?>
							<?php 
							
							$link =  JRoute::_('index.php?option=com_adminmachine&controller=elements&view=menuselectelement&tmpl=component&task=selectMenu&component='.$name.'&dataset='.$dataset) ;
							$hrefTag = '<a rel="{handler: \'iframe\', size: {x: 250, y: 250}}" href="'.$link.'" class="modal">'.JText::_('Add To Menu').'</a>';	
							$k = 0;?>
					        <tr class="<?php echo "row$k"; ?>">
								<td width="50%"><?php echo $dataset; ?></td>
								<td><?php echo $hrefTag; ?></td>
							</tr>
							<?php $k = 1 - $k; ?>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endforeach; ?>
			</td>
		</tr>
	</tbody>
    </table>
</div>
<input type="hidden" name="option" value="com_adminMachine" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="adminMachine" />
</form>
<?php endif; ?>