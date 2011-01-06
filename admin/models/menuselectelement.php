<?php
/**
 * selectMenuElement Model for Admin Machines Component
 * 
 * @package    adminMachine
 * @subpackage 
 * @license		cc-by-sa
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 * Admin Machine selectMenuElement Model
 *
 * @package    adminMachines
 * @subpackage 
 */
class AdminMachineModelMenuSelectElement extends JModel
{
	var $_data;
	
	function &getData()
	{
		$query = ' SELECT `id`, `admin_menu_alt` FROM `#__components` 
					WHERE `parent` = 0 
						AND `admin_menu_link` != \'\'
						AND `enabled` = 1 
						AND `iscore` = 0 ';
						
		$this->_db->setQuery($query);
		$menus = $this->_db->loadObjectList();
		
		$this->_data = array();
		$this->_data['menus'] =& $menus;
		$this->_data['component'] = JRequest::getWord('component', '');
		$this->_data['dataset'] = JRequest::getWord('dataset', '');
		return $this->_data;
	}
	
	function addToMenu()
	{
		print_r('ok');
		die();
	}
	
}
