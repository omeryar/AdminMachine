<?php
/**
 * adminMachine Model for Admin Machines Component
 * 
 * @package    adminMachine
 * @subpackage 
 * @license		cc-by-sa
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');
jimport('joomla.filesystem.folder');

/**
 * Admin Machine adminmachine Model
 *
 * @package    adminMachines
 * @subpackage 
 */
class AdminMachineModelAdminMachine extends JModel
{
	var $_data;
	
	function __construct()
	{
		parent::__construct();
		$this->_data = array();
	}
	
	function &getData()
	{
		$path = JPATH_BASE.DS.'components';
		$components = JFolder::folders($path);
		
		foreach($components as $component)
		{
			$tryPath = $path.DS.$component.DS.'datasets';
			if(JFolder::exists($tryPath))
			{
				$this->_data['components'][$component] = array();
				foreach(JFolder::files($tryPath, '(php)$') as $file)
				{
					$this->_data['components'][$component][] = preg_replace('/(.+)\..*$/', '$1', $file); ;
				}
				
			}

		}	
		
		return $this->_data;
	}
	
	
	function addToMenu()
	{
	
		$parent_id = JRequest::getInt('menuSelect', 0);
		
		if($parent_id < 1)
			return array("success" => 0, "msg" => JText::_('No Parent Menu Selected'));
			
		$name = JRequest::getString('nameSelect', '');
		if($name == '')
			return array("success" => 0, "msg" => JText::_('You Must Name The Menu Item'));		
		
		$component_name = JRequest::getWord('component', '');
		$dataset_name = JRequest::getWord('dataset', '');
		
		$link = 'option=com_adminMachine&component='.str_replace('com_', '', $component_name).'&dataset='.$dataset_name;
		
		$query = "INSERT INTO `#__components` (`name`, `parent`, `admin_menu_link`, `admin_menu_alt`, `ordering`)  VALUES ('$name', $parent_id, '$link', '$name', 1); ";	
		$this->_db->setQuery($query);
		if(!$this->_db->query()){
			$this->setError($this->_db->getErrorMsg());
			return array("success" => 0, "msg" => JText::_('Problem Inserting Into Database'));		
		}	

		return array("success" => 1, "msg" => JText::_('Menu Item Inserted'));
	}
	
	
	
	
}
