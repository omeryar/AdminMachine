<?php 
defined('_JEXEC') or die('Restricted access'); 

class AdminMachineActionsHelper
{
	function addAction($action, $component = null, $dataset = null)
	{
		$bar = & JToolBar::getInstance('toolbar');
		
		switch($action->type)
		{
			case 'add':
				JToolBarHelper::addNew();
				break;
			case 'addX':
				JToolBarHelper::addNewX();
				break;			
			case 'edit':
				JToolBarHelper::editList();
				break;			
			case 'editX':
				JToolBarHelper::editListX();
				break;
			case 'delete':
				JToolBarHelper::deleteList();
				break;
			case 'deleteX':
				JToolBarHelper::deleteListX();
				break;
			case 'back':
				JToolBarHelper::back();
				break;
			case 'preferences':
				JToolBarHelper::preferences('com_'.$component,'400');
				break;
			case 'link':
				if($action->modal)
					$bar->appendButton( 'Popup', $action->icon, $action->name, $action->link, 650, 400 );
				else
					$bar->appendButton( 'Link', $action->icon, $action->name, $action->link);
				break;
			case 'custom':
			default:
				$task = "performAction";
				$link = 'javascript:'.$action->action.'Clicked()';
				$bar->appendButton( 'Link', $action->icon, $action->action, $link);
				
				$document =& JFactory::getDocument();
				$comp = '';
				if($action->component)
					$comp = '$E("input[name=actioncomponent]").setProperty("value", "'.$action->component.'");';
				
				$js = '
						function '.$action->action.'Clicked()
						{
					';
				if($action->requiresConfirmation)
				{
					$js .= ' if(confirm("Are You Sure?"))
					 		{';
				}
				
				if($action->requiresSelection)
				{	
					$js .= '
						if(document.adminForm.boxchecked.value==0)
						{
							alert("Please make a selection from the list");
						}
						else
						{
							';			
				}
				
				$js .= '	$E("input[name=task]").setProperty("value", "performAction");
							$E("input[name=action]").setProperty("value", "'.$action->action.'");
							'.$comp.'
							hideMainMenu(); 
							submitbutton();
						';
				
				if($action->requiresSelection)
				{
					$js .= '
							}';				
				}				
				
				if($action->requiresConfirmation)
				{
					$js .= '
						}';
				}
				
				$js .= '
					}';
		
				$document->addScriptDeclaration($js);
				
				break;
		}
	}
	
	
	function addRowAction($action, $id, $component = null, $dataset = null, $datasetParams = null)
	{
		$linkTemplate = 'index.php?option=com_adminMachine&controller=datarow&component='.$component.'&dataset='.$dataset.'&task=edit';		
		if($datasetParams)
		{
			foreach($datasetParams as $param)
			{
				$linkTemplate .= '&'.$param->name.'='.$param->value;
			}
		}
		$linkTemplate .= '&id=';
		$actionLink = '';
		
		switch($action->type)
		{
			case 'link':
				$actionLink = $action->link.$id;	
				break;
			case 'edit':
				$actionLink = $linkTemplate.$id;
				break;
			case 'custom':
			default:
				$actionLink = str_replace('edit', 'performAction', $linkTemplate).$id.'&action='.$action->action;	
				break;				
		}
		// OMER: ready to make datarows into poups
		global $rbowGroups;
		if($action->type == 'link' && $action->modal)
		{
			JHTML::_('behavior.modal');
			echo '<td><a rel="{handler: \'iframe\', size: {x: 650, y: 500}}" href="'.JRoute::_($actionLink).'" class="modal rowAction">'.JText::_($action->name).'</a></td>';
		}
		elseif($rbowGroups && $rbowGroups->isGroups() && stristr($actionLink, 'controller=datarow'))
		{
			JHTML::_('behavior.modal');
			echo '<td><a rel="{handler: \'iframe\', size: {x: 650, y: 500}}" href="'.JRoute::_($actionLink).'&tmpl=component'.'" class="modal rowAction">'.JText::_($action->name).'</a></td>';
		}
		else
			echo '<td><a class="rowAction" href="'.JRoute::_($actionLink).'">'.$action->name.'</a></td>';
		
	}
	
}
?>