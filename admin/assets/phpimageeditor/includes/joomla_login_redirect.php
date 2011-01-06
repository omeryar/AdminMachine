<?php 
	define( '_JEXEC', 1 );
	define('DS', DIRECTORY_SEPARATOR);
	define('JPATH_BASE', dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..");
	//	var_dump(JPATH_BASE); die();
	require_once( JPATH_BASE .DS.'includes'.DS.'defines.php' );
	require_once( JPATH_BASE .DS.'includes'.DS.'framework.php' );
	
	$mainframe =& JFactory::getApplication('administrator');	
	$mainframe->initialise();
	$mainframe->route();
	
	$joomlaUser =& JFactory::getUser();
	
	if ($joomlaUser->get('id') < 1)
	{
		//You must login to be able to edit images.	
		header('Location: ../../../administrator/index.php');
	}
?>