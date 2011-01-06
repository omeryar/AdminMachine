<?php
/**
 * @package adminMachine
 * @subpackage 
 * @license cc-by-sa
 */

defined('_JEXEC') or die('Restricted access');
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

$emptySelection = array();
foreach($returnFields as $returnField)
{
	$emptySelection[$returnField] = '';
}		
$emptySelection = json_encode($emptySelection);

$selectedIds = array();

if(is_array($this->data['selection']))
{
	foreach($this->data['selection'] as &$row)
	{
		$selectedIds[] = $row->id;	
	}
}
$selectedIds = implode(',', $selectedIds);

JHTML::_('behavior.mootools');

$js = '
	
	function refreshIds()
	{
		returnVals = [];
		$("selectionTable").getChildren().each(function(el){ returnVals.push( el.getProperty("id").substring("selection".length));})
		
		$("ids").setProperty("value", returnVals.join(","));
	}
	
	function addToSelection(id)
	{
		//check if already in selection
		if($("selection"+id))
		{
			alert("'.JText::_('Already Selected').'");
			return;
		}
		newSelection = $("row"+id).clone().removeProperty("style").removeProperty("onclick");
		removeLink = new Element("td");
		removeLink.setProperty("width", "40px").setHTML("<a href=\"#\" onclick=\"removeFromSelection("+id+")\">'.JText::_("Remove").'</a>").injectInside(newSelection);
		newSelection.setProperty("class", "row1").setProperty("id", "selection"+id).injectInside($("selectionTable"));
		
		refreshIds();
	}
	
	function removeFromSelection(id)
	{
		if(!$("selection"+id))
			return;
			
		$("selection"+id).remove();
		
		refreshIds();
		
	}
	
	function setSelection()
	{
		returnVals = [];
		returnVals.push('.$emptySelection.');
		$("selectionTable").getChildren().each(function(el){ id = el.getProperty("id").substring("selection".length); returnVals.push( eval("getVal"+id+"()")); })
		
		window.parent.elementMultiSelect(returnVals);
	}
	
	';

$document->addScriptDeclaration($js);

?>

<form action="index.php" method="post" name="adminForm" >

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
			<td colspan="<?php echo count($fields) + 1; ?>">
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
			
			$js = ' function select'.$row->id.'(){ addToSelection('.$row->id.')} ';
			$document->addScriptDeclaration($js);
			
			$js = ' function getVal'.$row->id.'(){ return '.$returnVal.'; } ';
			$document->addScriptDeclaration($js);
   		?>
		<tr id="row<?php echo $row->id; ?>" class="<?php echo "row$k"; ?>" style="cursor:pointer" onclick="select<?php echo $row->id; ?>()">
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

<table class="adminform">
	<tr>
		<th width="100%">
			<?php echo JText::_('Current Selection:'); ?>
		</th>
	</tr>
	<tr>
		<td>
			<table class="adminlist" cellspacing="1">
				<tbody id="selectionTable" >
					<?php if(is_array($this->data['selection'])) : ?>
						<?php foreach($this->data['selection'] as &$row) : 
							$returns = array();
							foreach($row as $name => $value)
								if(array_key_exists($name, $returnFields))
									$returns[$returnFields[$name]] = $value;			
							$returnVal = json_encode($returns);

							$js = ' function getVal'.$row->id.'(){ return '.$returnVal.'; } ';
							$document->addScriptDeclaration($js);	
						?>
						<tr class="row1" id="selection<?php echo $row->id; ?>">
							<?php foreach($row as $name => $value) : ?>
									<td align="left">
										<?php echo $value; ?>
									</td>
							<?php endforeach; ?>
							<td width="40px"><a href="#" onclick="removeFromSelection(<?php echo $row->id; ?>)"><?php echo JText::_('Remove'); ?></a></td>
						</tr>
						<?php endforeach;?>
					<?php endif; ?>
				</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td width="100%">
			<div style="width: 100%; text-align:right">
				<input type="button" name="apply" id="apply" value="<?php echo JText::_('Set Selection'); ?>" onclick="setSelection()" />
				<input type="button" name="cancel" id="cancel" value="<?php echo JText::_('Cancel'); ?>" onclick="window.parent.cancelSelection()" />
			</div>
		</td>
	</tr>
</table>


<?php endif;?>
<input type="hidden" name="option" value="com_adminMachine" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="component" value="<?php echo $this->data['component'];?>" />
<input type="hidden" name="dataset" value="<?php echo $this->data['name'];?>" />
<input type="hidden" name="controller" value="elements" />
<input type="hidden" name="ids" id="ids" value="<?php echo $selectedIds; ?>" />
<input type="hidden" name="view" value="mtmelement" />
<input type="hidden" name="tmpl" value="component" />

</form>