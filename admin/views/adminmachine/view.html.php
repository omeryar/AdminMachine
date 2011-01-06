<?php
/**
 * @package    adminMachine
 * @subpackage 
 * @link 
 * @license    cc-by-sa
*/

// no direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the adminmachine Component
 *
 * @package    adminMachine
 */

class AdminMachineViewAdminMachine extends JView
{

    function display($tpl = null)
	{
		//Get data from model
		$data =& $this->get('Data');

		JToolBarHelper::title(JText::_('Add Menu Item'), 'generic.png');

		$this->assignRef('data', $data);

		parent::display($tpl);
	}
}

?>