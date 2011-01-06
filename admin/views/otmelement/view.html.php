<?php
/**
 * @package adminMachine
 * @subpackage 
 * @license cc-by-sa
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * View class for the AdminMachine otmselect screen
 *
 * @package adminMachine
 * @subpackage 
 * @since 0.9
 */
class AdminMachineViewOTMelement extends JView {

	function display($tpl = null)
	{
		global $mainframe, $option;

		//initialise variables
		$db			= & JFactory::getDBO();
		$document	= & JFactory::getDocument();
		
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.modal');

		//get vars
		$dataset_name = get_class($this->getModel()->_dataset);
		$filter 			= $mainframe->getUserStateFromRequest( $dataset_name.'.filter', 'filter', '', 'string' );
		$filter_state 		= $mainframe->getUserStateFromRequest( $dataset_name.'.filter_state', 'filter_state', '*', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( $dataset_name.'.search', 'search', '', 'string' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );
		$template 			= $mainframe->getTemplate();

		//prepare document
		$document->setTitle(JText::_( 'Select Element' ));
		$document->addStyleSheet("templates/$template/css/general.css");

		// Get data from the model
		$data      	= & $this->get( 'Data');
		$total      = & $this->get( 'Total');
		$pageNav 	= & $this->get( 'Pagination' );


		// search filter
		$lists['search']= $search;

		//assign data to template
		$this->assignRef('lists'      	, $lists);
		$this->assignRef('data'      	, $data);
		$this->assignRef('filter' 		, $filter);
		$this->assignRef('filter_state' , $filter_state);
		$this->assignRef('pageNav' 		, $pageNav);
		

		parent::display($tpl);
	}
}
?>