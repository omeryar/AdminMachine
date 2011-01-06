<?php // no direct access
defined('_JEXEC') or die('Restricted access'); 

include_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'form.php');

?>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div class="col100">
    <fieldset class="adminform">
        <legend><?php echo JText::_( 'Details' ); ?></legend>
        <table class="admintable">
		<?php foreach($this->data['row'] as $name => $value) : ?>
			<?php if($this->data['fieldsMeta'][$name]->type == ADMINMACHINE_FIELDTYPES_HIDDEN) : ?>
				<?php AdminMachineFormHelper::getFieldHTML($name, $value, $this->data['fieldsMeta'][$name])?>
			<?php else : ?>
			<tr>
				<td width="100" align="left" class="key">
					<?php if(!$this->data['fieldsMeta'][$name]->title): ?>
						<label for="<?php echo $name; ?>"><?php echo JText::_($name); ?></label>
					<?php else: ?>
						<label for="<?php echo $name; ?>"><?php echo JText::_($this->data['fieldsMeta'][$name]->title); ?></label>	
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php AdminMachineFormHelper::getFieldHTML($name, $value, $this->data['fieldsMeta'][$name], $this->data['component']);?>
				</td>
			</tr>
			<?php endif;?>
		<?php endforeach; ?>
    </table>
</div>
 
<div class="clr"></div>
<input type="hidden" name="option" value="com_adminMachine" />
<input type="hidden" name="dataset" value="<?php echo $this->data['name']; ?>" />
<input type="hidden" name="component" value="<?php echo $this->data['component']; ?>" />
<input type="hidden" name="id" value="<?php echo $this->data['row']->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="datarow" />
<?php if($this->data['datasetParams']) : ?>
	<?php foreach($this->data['datasetParams'] as $param) : ?>
		<input type="hidden" name="<?php echo $param->name; ?>" value="<?php echo $param->value; ?>" />
	<?php endforeach; ?>
<?php endif; ?>
    </fieldset>
</form>
