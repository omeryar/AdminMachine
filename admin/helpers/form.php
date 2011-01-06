<?php 
defined('_JEXEC') or die('Restricted access'); 

class AdminMachineFormHelper
{
	
	function getFieldHTML($fieldName, $fieldValue, $meta, $component = 'com_adminmachine')
	{   
		switch($meta->type)
		{
			case ADMINMACHINE_FIELDTYPES_HIDDEN:
				$class = '';
				if($meta->otmName)
					$class = 'class = "'.$meta->otmName.'" ';
				echo "<input type=\"hidden\" name=\"$fieldName\" id=\"$fieldName\" value=\"$fieldValue\" $class />";
				break;
			case ADMINMACHINE_FIELDTYPES_OTM:
			  if(isset($meta->readonly) && $meta->readonly)
			  {
			    echo "<input type=\"hidden\" value=\"$fieldValue\" name=\"$fieldName\" id=\"$fieldName\" />";		
			    echo $fieldValue;	    
			  }
			  else
			  {
					AdminMachineFormHelper::addIdsJS($fieldName, $meta->idSource, $meta->translationTable);
  				AdminMachineFormHelper::addSelectJS();
  				$mycomponent = $component;
  				if($meta->dataset_component)
  					$mycomponent = $meta->dataset_component;
  				$class= $fieldName;
  				if($meta->otmName)
  					$class = $meta->otmName;
  				$class = " class = \"$class\" ";
  				$link = JRoute::_('index.php?option=com_adminMachine&controller=elements&view=otmelement&tmpl=component&component='.$mycomponent.'&dataset='.$meta->dataset);
  				echo '<a rel="{handler: \'iframe\', size: {x: 750, y: 500}}" href="'.$link.'" class="modal">';
  				echo "<input style=\"width:500px\" style=\"cursor:pointer\" type=\"text\" name=\"$fieldName\" id=\"$fieldName\" value=\"$fieldValue\" READONLY $class />";
  				echo '</a>';		    
			  }
				break;
			case ADMINMACHINE_FIELDTYPES_MTM:
  			if(isset($meta->readonly) && $meta->readonly)
  		  {
  		    echo "<input type=\"hidden\" value=\"$fieldValue\" name=\"$fieldName\" id=\"$fieldName\" />";		
  		    echo $fieldValue;	    
  		  }
  		  else
  		  {
    			AdminMachineFormHelper::addIdsJS($fieldName, $meta->idSource);
    			AdminMachineFormHelper::addMultiSelectJS();
    			$mycomponent = $component;
    			if($meta->dataset_component)
    				$mycomponent = $meta->dataset_component;
    			$link = JRoute::_('index.php?option=com_adminMachine&controller=elements&view=mtmelement&tmpl=component&component='.$mycomponent.'&dataset='.$meta->dataset);
    			echo '<a rel="{handler: \'iframe\', size: {x: 750, y: 500}}" href="'.$link.'" class="modal">';
    			echo "<input style=\"width:500px\" style=\"cursor:pointer\" type=\"text\" name=\"$fieldName\" id=\"$fieldName\" value=\"$fieldValue\" READONLY />";
    			echo '</a>';
    			break;
    		}			
    	case ADMINMACHINE_FIELDTYPES_TEXT:
  			$editor =& JFactory::getEditor();
  			$readonly = '';
  			if(isset($meta->readonly) && $meta->readonly)
  				echo "<input type=\"hidden\" name=\"$fieldName\" id=\"$fieldName\" value=\"$fieldValue\" />".highlight_string($fieldValue, true);
  			else
  			{
  				echo $editor->display( $fieldName,  $fieldValue, '100%;', '350', '75', '20', array('pagebreak', 'readmore') ) ;
  			}		    
				break;			
			case ADMINMACHINE_FIELDTYPES_BOOLEAN:
			  if(isset($meta->readonly) && $meta->readonly)
			  {
			    echo "<input type=\"hidden\" value=\"$fieldValue\" name=\"$fieldName\" id=\"$fieldName\" />";			    
					
  				if($fieldValue == 1)
  					echo JText::_('Y');
  				else
  					echo JText::_('N');
    				
  				break;
			  } 
			  else 
			  {
  				$checked = '';
  				if($fieldValue == 1)
  					$checked = 'CHECKED';
  				echo "<input type=\"checkbox\" value=\"1\" name=\"$fieldName\" id=\"$fieldName\" $checked />";			    
			  }
				break;		
			case ADMINMACHINE_FIELDTYPES_SELECT:
			  if(isset($meta->readonly) && $meta->readonly)
			  {
			    echo "<input type=\"hidden\" value=\"$fieldValue\" name=\"$fieldName\" id=\"$fieldName\" />";		
			    echo $fieldValue;	    
			  }
			  else
			  {
					$html = "<select name=\"$fieldName\" id=\"$fieldName\" >";
  				$options = explode(';', $meta->options);
  				foreach($options as $option){
  				  $label = $option;
  				  $value = $option;
  				  if(strstr($option, '='))
  				  {
  				    $parts = explode('=',$option);
              $value = $parts[0];
  				    $label = $parts[1];
  				  }

  					if($value == $fieldValue)
  						$html .= '<option value="'.$value.'" SELECTED >'.$label.'</option>';
  					else
  						$html .= '<option value="'.$value.'" >'.$label.'</option>';
  				}		
  				$html .= '</select>';
  				echo $html;		    
			  }
				break;
			case ADMINMACHINE_FIELDTYPES_IMAGE:
				JHTML::_('behavior.modal');
				AdminMachineFormHelper::addImageJS();
				preg_match('/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'\s>]*)/i' , $fieldValue, $matches);
				$imgSrc = null;
				if(is_array($matches) && count($matches) == 2)
					$imgSrc = '../../../../..'.$matches[1];

				$folder = '';
				if($meta->folder)
					$folder = '&amp;folder='.urlencode($meta->folder);

				$html = "<div id=\"$fieldName".'_container'."\">$fieldValue</div>&nbsp;&nbsp;".'<a rel="{handler: \'iframe\', size: {x: 570, y: 400}}" href="'.JRoute::_('index.php?option=com_media&amp;view=images&amp;tmpl=component'.$folder.'&amp;e_name='.$fieldName).'" title="Image" class="modal">'.JText::_('Change')."</a>"."\n<input type=\"hidden\" name=\"$fieldName\" id=\"$fieldName\" value=\".".htmlspecialchars($fieldValue).".\" />&nbsp;&nbsp;";
				if($imgSrc)
					$html .= "<a rel=\"{handler: 'iframe', size: {x: 570, y: 400}}\" class=\"modal\" href=\"".JURI::base()."components/com_adminmachine/assets/phpimageeditor/index.php?imagesrc=".urlencode($imgSrc)."&language=he-il\">".JText::_('Edit').'</a>';
				echo $html;
				break;
			case ADMINMACHINE_FIELDTYPES_FILE:
				$readonly = '';
				if(isset($meta->readonly) && $meta->readonly)
				{
					echo $fieldValue;
				}
				else
				{
					echo "<input style=\"width:500px\" class=\"adminMachineString\" type=\"file\" name=\"$fieldName\" id=\"$fieldName\" />";	
				}				
				break;
			case ADMINMACHINE_FIELDTYPES_PASSWORD:
				$readonly = '';
				if(isset($meta->readonly) && $meta->readonly)
				{
					echo '';
				}
				else
				{
					$fieldValue = base64_decode($fieldValue);
				
					echo "<input style=\"width:500px\" class=\"adminMachinePassword\" type=\"password\" name=\"$fieldName\" id=\"$fieldName\" value=\"$fieldValue\" />";	
				}	
				break;	
			case ADMINMACHINE_FIELDTYPES_NUMBER:
			case ADMINMACHINE_FIELDTYPES_STRING:
			default:
				$readonly = '';
				if(isset($meta->readonly) && $meta->readonly)
				{
					echo $fieldValue."\n<input type=\"hidden\" name=\"$fieldName\" id=\"$fieldName\" value=\"$fieldValue\" />";
				}
				else
				{
				echo "<input style=\"width:500px\" class=\"adminMachineString\" type=\"text\" name=\"$fieldName\" id=\"$fieldName\" value=\"$fieldValue\" />";	
				}				
				break;
		}	
		
	}
	
	function addIdsJS($name, $idsField, $translationTable = null)
	{
		$document =& JFactory::getDocument();
		$ttjs = 'null';
		if($translationTable)
		{
		  $tbl = array();
		  foreach($translationTable as $src => $dest) {
		    $tbl []= ' "'.$src.'" : "'.$dest.'"';
		  }
		  $ttjs = '{'.implode(',', $tbl).'}';
		}		
		
		$js = '
	window.addEvent("domready", function() {
			
		$("'.$name.'").addEvent("click", function(e) {
		  amTranslationTable = '.$ttjs.';
			var event = new Event(e);
			$(this).getParent().setProperty("href", $(this).getParent().href+"&ids="+$("'.$idsField.'").getValue());
				});
		
	} );
				';
					
		$document->addScriptDeclaration($js);
	}
	

	function addSelectJS()
	{
	  static $added = false;
	  
	  if(!$added)
	    $added = true;
	  else
	    return;
	    
		$document =& JFactory::getDocument();
		
		$js = '	
	var amTranslationTable = null;
		
	function elementSelect(values)
	{
		for(var item in values) 
		{  	  
		  var fieldName = item;
		  if(amTranslationTable && typeof(amTranslationTable[item]) != "undefined")
		    fieldName = amTranslationTable[item];

			if($(fieldName))
				$(fieldName).setProperty("value", values[item]);
			else
			{ 
				if($$("."+fieldName))
					$$("."+fieldName).setProperty("value", values[item]);
			}
		}
			$("sbox-window").close();
	}
				';		
				
		$document->addScriptDeclaration($js);
	}

	function addMultiSelectJS()
	{
		$document =& JFactory::getDocument();
		
		$js = '
	selectedValues = [];
	function elementMultiSelect(values)
	{
		reset = values.shift();
		properties = [];
		for(property in reset)
		{
			properties[property] = [];
		}
		
		val = values.shift();
		while(val)
		{
			for(property in val)
			{
				properties[property].push(val[property]);
			}
			val = values.shift();
		}
				
		for(property in properties)
		{
			if($(property))
				$(property).setProperty("value", properties[property].join(","));
		}
		$("sbox-window").close();
	}	
		
	function cancelSelection()
	{
		$("sbox-window").close();
	}

				';		
				
		$document->addScriptDeclaration($js);
	}
	
	function addImageJS()
	{
		$document =& JFactory::getDocument();
		
		$js = ' 
			function jInsertEditorText(text, fieldname)
			{
				text = text.replace("src=\"", "src=\"/");
				$(fieldname).value = text;
				$(fieldname+"_container").setHTML(text);
			} 
		';
			
		$document->addScriptDeclaration($js);	
	}
}
?>