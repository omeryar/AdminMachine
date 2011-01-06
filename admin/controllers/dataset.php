<?php
/* Check to ensure this file is included in Joomla! */
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );


/**
 * adminMachine Component Dataset Controller
 *
 * @author		Omer Yariv
 * @package		adminMachine
 * @subpackage	
 * @since 1.5
 */
class AdminMachineControllerDataset extends JController
{

	var $_link_to_dataset;

	/*
	 * constructor (registers additional tasks to methods)
	 */
	function __construct()
	{
		parent :: __construct();
		$this->_link_to_dataset = 'index.php?option=com_adminMachine&controller=dataset&component='.JRequest::getString('component').'&dataset='.JRequest::getString('dataset');
		
	}	

	/**
	 * Display the view
	 */
	function display()
	{
		$document =& JFactory::getDocument();
		$viewName = JRequest::getVar('view', 'dataset');
		$viewType = $document->getType();
		$view =& $this->getView($viewName, $viewType);
		$model =& $this->getModel( 'dataset', 'AdminMachineModel' );

		if (!JError::isError( $model )) 
		{
			$view->setModel( $model, true );
		}
		$layout = JRequest::getVar('layout', 'default');

		$view->setLayout($layout);
		$view->display();
	}

	function performAction()
	{
		$model = $this->getModel('dataset');
		if($model->performAction()) {
			$msg = JText::_('Action Performed');
		} else {
			$msg = JText::_('Problem Performing Action');
		}

		$additionalParams = $model->_dataset->getDatasetFormParametersForURI();
		
		$this->setRedirect($this->_link_to_dataset.$additionalParams, $msg);
	}

	//OMER:
	//this is a dirty dirty hack. but at least it's not in the core.
	//I don't like it. but what to do?
	//it's done for the groups. 
	function setRedirect( $url, $msg = null, $type = 'message' )
	{
		$this->_redirect = str_replace('&amp;','&',JRoute::_($url));

		if ($msg !== null) {
			// controller may have set this directly
			$this->_message	= $msg;
		}
		$this->_messageType	= $type;
		// 
		// global $rbowGroups;
		// $group = '';
		// if(isset($rbowGroups) && $rbowGroups && $rbowGroups->isGroups())
		// 	$group = urlencode($rbowGroups->getGroupName()).'/';
		// 	
		// if(strpos($url, '&amp;') !== false)
		//    $url = str_replace('&amp;','&',JRoute::_($url));
		// 	
		// $this->_redirect = JURI::root().$group.'administrator/'.$url;
		// 
		// if ($msg !== null) {
		// 	// controller may have set this directly
		// 	$this->_message	= $msg;
		// }
		// $this->_messageType	= $type;
	}

}
?>