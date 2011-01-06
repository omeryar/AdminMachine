<?php 
defined('_JEXEC') or die('Restricted access'); 

class AdminMachineViewHelper
{
	
	function getFieldHTML($fieldName, $fieldValue, $meta)
	{   
		switch($meta->type)
		{
			case ADMINMACHINE_FIELDTYPES_HIDDEN:
				break;
			case ADMINMACHINE_FIELDTYPES_BOOLEAN:
				if($fieldValue == 1)
					echo JText::_('Y');
				else
					echo JText::_('N');
				break;
			case ADMINMACHINE_FIELDTYPES_PASSWORD:
				echo JText::_('SECRET');
				break;
			case ADMINMACHINE_FIELDTYPES_OTM:
			case ADMINMACHINE_FIELDTYPES_MTM:
			case ADMINMACHINE_FIELDTYPES_NUMBER:
			case ADMINMACHINE_FIELDTYPES_TEXT:
			case ADMINMACHINE_FIELDTYPES_STRING:
			default:
				echo strip_tags($fieldValue);
				break;
		}	
		
	}

	
}
?>