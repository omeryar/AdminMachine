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
class AdminMachineViewMenuSelectElement extends JView {

	function display($tpl = null)
	{
		global $mainframe, $option;

		//initialise variables
		$db			= & JFactory::getDBO();
		$document	= & JFactory::getDocument();
		
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.modal');

		$template 			= $mainframe->getTemplate();

		//prepare document
		$document->setTitle(JText::_( 'Select Menu' ));
		$document->addStyleSheet("templates/$template/css/general.css");

		// Get data from the model
		$data      	= & $this->get( 'Data');

		//assign data to template
		$this->assignRef('data' , $data);
		parent::display($tpl);
	}
}
?>