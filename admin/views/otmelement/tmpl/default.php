<?php
/**
 * @package adminMachine
 * @subpackage 
 * @license cc-by-sa
 */

defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm">
<?php

$document =& JFactory::getDocument();

$rows = $this->data['rows'];
$fields = $this->data['fieldsMeta'];

$filters = array();
$returnFields = array();

foreach($fields as $name => &$value)
{
	if(isset($value->filter) && $value->filter == true)
		$filters[] = JHTML::_('select.option', $name, $name);
	if(isset($value->returnAs))
		$returnFields[$name] = $value->returnAs;
}

$selectedId = 0;
if(is_array($this->data['selection']) && isset($this->data['selection'][0]))
	$selectedId = $this->data['selection'][0]->id;

?>
<table class="adminform">
	<tr>
		<td width="100%">
			<?php if(count($filters) > 0) : ?>
				<?php echo JText::_( 'SEARCH' ).' '.JHTML::_('select.genericlist', $filters, 'filter', 'size="1" class="inputbox"', 'value', 'text', $this->filter ); ?>
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			<?php endif; ?>
		</td>
	</tr>
</table>

<?php if(isset($this->data['errors'])) : ?>
	<div class="errorMsg">
		<?php foreach($this->data['errors'] as $error) : ?>
			<div class="error"><?php echo $error; ?></div>
		<?php endforeach;?>
	</div>
<?php elseif(!$this->data['rows'] || count($this->data['rows']) == 0) : ?>
	<div class="errorMsg">
		<div class="error"><?php echo JText::_('No Data Found'); ?></div>
	</div>	
<?php 
	else :
?>
<table class="adminform">
	<tr>
		<th width="100%">
			<?php echo JText::_('Current Selection:'); ?>
		</th>
	</tr>
	<tr>
		<td>
			<?php if($selectedId > 0) : ?>
			<table class="adminlist" cellspacing="1">
				<tbody>
					<tr class="row1">
						<?php $row = array_pop($this->data['selection']); ?>
						<?php foreach($row as $name => $value) : ?>
								<td align="left">
									<?php echo $value; ?>
								</td>
						<?php endforeach; ?>
					</tr>
				</tbody>
			</table>
			<?php endif; ?>
		</td>
	</tr>
</table>


<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<?php foreach($fields as $name => $value) : ?>
				<th align="left" class="title"><?php echo JText::_($name); ?></th>
			<?php endforeach; ?>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td colspan="<?php echo count($fields); ?>">
				<?php echo $this->pageNav->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>

	<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count( $rows ); $i < $n; $i++) :
			$row = &$rows[$i];		
			$returns = array();
			foreach($row as $name => $value)
				if(array_key_exists($name, $returnFields))
					$returns[$returnFields[$name]] = $value;			
			$returnVal = json_encode($returns);
			
			$js = ' function select'.$row->id.'(){ window.parent.elementSelect('.$returnVal.') } ';
			$document->addScriptDeclaration($js);
			
   		?>
		<tr class="<?php echo "row$k"; ?>" style="cursor:pointer" title="<?php echo JText::_('Select'); ?>" onclick="select<?php echo $row->id; ?>()">
			<?php foreach($row as $name => $value) : ?>
					<td align="left">
						<?php echo $value; ?>
					</td>
			<?php endforeach; ?>
		</tr>

		<?php $k = 1 - $k; ?>
		<?php endfor; ?>

	</tbody>

</table>
<?php endif;?>
<input type="hidden" name="option" value="com_adminMachine" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="component" value="<?php echo $this->data['component'];?>" />
<input type="hidden" name="dataset" value="<?php echo $this->data['name'];?>" />
<input type="hidden" name="ids" value="<?php echo $selectedId; ?>" />
<input type="hidden" name="controller" value="elements" />
<input type="hidden" name="view" value="otmelement" />
<input type="hidden" name="tmpl" value="component" />

</form>