<?php
/**
 * @package    Admin.Machine
 * @subpackage Components
 * @link 
 * @license    cc-by-sa
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Require the base controller

require_once( JPATH_COMPONENT.DS.'controller.php' );

$controller = JRequest::getWord('controller', 'dataset');
$task = JRequest::getVar('task');

if($controller == 'datarow' && $task == '' )
{
	$controller = 'dataset';
	$task = 'display';
}
// Require specific controller if requested
if($controller ) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

// Create the controller
$classname	= 'adminMachineController'.$controller;
$controller	= new $classname( );

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();

?>