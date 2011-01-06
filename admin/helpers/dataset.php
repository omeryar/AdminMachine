<?php 
defined('_JEXEC') or die('Restricted access'); 

class AdminMachineDatasetHelper
{
	function &getDataSet($component_name, $dataset_name)
	{
		if(!$component_name || !$dataset_name)
		{
			$empty = null;
			return $empty;
		}
					
						
		$path = strtolower(JPATH_BASE.DS.'components'.DS.'com_'.$component_name.DS.'datasets'.DS.$dataset_name.'.php');
		
		if (file_exists($path))
		{			
			require_once $path;
		} 
		else 
		{
			$nada = null;
			return $nada;		
		}
		
		$classname	= 'AdminMachineDataset'.$dataset_name;
		$newObj = new $classname( );
		return $newObj;
	}
}
?>