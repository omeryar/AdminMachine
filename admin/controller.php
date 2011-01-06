<?php
/**
 * Admin Machine default controller
 * 
 * @package    adminMachine
 * @subpackage 
 * @license		cc-by-sa
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Admin Machine default Controller
 *
 * @package    adminMachine
 * @subpackage 
 */


class AdminMachineController extends JController
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display()
	{
		parent::display();
	}

	function addToMenu()
	{
		$model =& $this->getModel( 'adminMachine', 'AdminMachineModel' );
		$res = $model->addToMenu();
	 	echo json_encode($res);
		die();
	}
	
	
}