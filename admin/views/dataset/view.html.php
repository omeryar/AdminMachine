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
include_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'actions.php');

/**
 * HTML View class for the Dataset Component
 *
 * @package    adminMachine
 */

class AdminMachineViewDataset extends JView
{

    function display($tpl = null)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		//Get data from model
		$data =& $this->get('Data');

		JToolBarHelper::title($data['display_name'], 'generic.png');

		if(isset($data['setActions']) && is_array($data['setActions']))
		{
			foreach($data['setActions'] as &$action)
			{
				AdminMachineActionsHelper::addAction($action, $data['component'], $data['name'] );
			}
		}
		
		$dataset_name = get_class($this->getModel()->_dataset);
		$filter 			= $mainframe->getUserStateFromRequest( $data['name'].'.'.$dataset_name.'.filter', 'filter', '', 'string' );
		$filter_state 		= $mainframe->getUserStateFromRequest( $data['name'].'.'.$dataset_name.'.filter_state', 'filter_state', '*', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( $data['name'].'.'.$dataset_name.'.search', 'search', '', 'string' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );
		// search filter
		$lists['search']= $search;		
				
		$total      = & $this->get( 'Total');		
		
		$pageNav 	= & $this->get( 'Pagination' );		
				
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('data'      	, $data);
		$this->assignRef('filter'      	, $filter);
		$this->assignRef('filter_state' , $filter_state);	
		$this->assignRef('pageNav' 		, $pageNav);
		parent::display($tpl);
	}
}

?>