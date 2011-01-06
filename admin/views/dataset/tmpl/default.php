<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

include_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'view.php');

?>
<form action="index.php" method="post" name="adminForm">
<?php if(isset($this->data['errors'])) : ?>
	<div class="errorMsg">
		<?php foreach($this->data['errors'] as $error) : ?>
			<div class="error"><?php echo $error; ?></div>
		<?php endforeach;?>
	</div>	
<?php 
	else :
$rows = $this->data['rows']; 
$fields = $this->data['fieldsMeta'];
$numActions = 0;
$filters = array();

foreach($fields as $name => $value)
{
	if($value->filter == true)
	{
		if(!$value->title)
			$filters[] = JHTML::_('select.option', $name, $name);
		else
			$filters[] = JHTML::_('select.option', $name, $value->title);
	}		
}
?>


	<div id="editcell">
		<form>
		 <table class="adminform">
			<tr>
				<td width="100%">
					<?php if(count($filters) > 0) : ?>
						<?php echo JText::_( 'SEARCH' ).' '.JHTML::_('select.genericlist', $filters, 'filter', 'size="1" class="inputbox"', 'value', 'text', $this->filter ); ?>
						<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" />
						<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
						<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
					<?php endif; ?>
				</td>
			</tr>
		</table>
	    <table class="adminlist">
	    <thead>
	        <tr>
				<th width="20">
				    <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows ); ?>);" />
				</th>
				<?php foreach($fields as $name => $value) : ?>
					<?php if($value->type != ADMINMACHINE_FIELDTYPES_HIDDEN) : ?>
						<?php if(!$value->title) : ?>
							<th align="left" class="title"><?php echo JText::_($name); ?></th>
						<?php else: ?>
							<th align="left" class="title"><?php echo JText::_($value->title); ?></th>
						<?php endif; ?>
					<?php endif; ?>
				<?php endforeach; ?>
				<?php if(isset($this->data['rowActions']) && is_array($this->data['rowActions'])) : ?>
				<?php 	$numActions = count($this->data['rowActions']);?>				
				<?php 	for($i=0; $i < $numActions; $i++) : echo '<th class="xxx"></th>'; endfor; ?>
				<?php endif; ?>
	        </tr>            
	    </thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo count($fields) + $numActions + 1; ?>">
					<?php echo $this->pageNav->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<?php if(!$this->data['rows'] || count($this->data['rows']) == 0) : ?>
			<div class="errorMsg">
				<div class="error"><?php echo JText::_('No Data Found'); ?></div>
			</div>
		<?php else: ?>
	    <?php
	    $k = 0;
	    for ($i=0, $n=count($rows); $i < $n; $i++)
	    {
	        $row =& $rows[$i];
			$checked = JHTML::_( 'grid.id', $i, $row->id );
			$properties = get_object_vars($row);
	        ?>
	        <tr class="<?php echo "row$k"; ?>">
			<?php echo '<td>'.$checked.'</td>'; ?>
			<?php foreach($fields as $name => $meta) : ?>				
				<?php if($meta->type != ADMINMACHINE_FIELDTYPES_HIDDEN) : ?>
					<td>
					    <?php AdminMachineViewHelper::getFieldHTML($name, $properties[$name], $meta); ?>
					</td>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php if(isset($this->data['rowActions']) && is_array($this->data['rowActions'])) : ?>
				<?php foreach($this->data['rowActions'] as &$action) : ?>
					<?php AdminMachineActionsHelper::addRowAction($action, $row->id, $this->data['component'], $this->data['name'], $this->data['datasetParams']); ?>
				<?php endforeach; ?>
			<?php endif; ?>
 			</tr>
	        <?php
	        $k = 1 - $k;
	    }
	    ?>
		<?php endif;?>
	
	    </table>
	</div>
<?php endif;?>
	<input type="hidden" name="option" value="com_adminMachine" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="action" value="" />
	<input type="hidden" name="actionComponent" value="" />
	<input type="hidden" name="actionDataset" value="" />
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="component" value="<?php echo $this->data['component'];?>" />
	<input type="hidden" name="dataset" value="<?php echo $this->data['name'];?>" />
	<input type="hidden" name="controller" value="datarow" />
	<?php if($this->data['datasetParams']) : ?>
		<?php foreach($this->data['datasetParams'] as $param) : ?>
			<input type="hidden" name="<?php echo $param->name; ?>" value="<?php echo $param->value; ?>" />
		<?php endforeach; ?>
	<?php endif; ?>
</form>

