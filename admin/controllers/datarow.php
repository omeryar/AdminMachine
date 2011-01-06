<?php
/**
 *  Datarow Controller for Admin Machine Component
 * 
 * @package    adminMachine
 * @subpackage 
 * @link
 * @license		cc-by-sa
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * AdminMachine Datarow Controller
 *
 * @package    adminMachine
 * @subpackage 
 */
class AdminMachineControllerDatarow extends AdminMachineController
{
/**
 * constructor (registers additional tasks to methods)
 * @return void
 */
	var $_dataset_name;
	var $_component_name;

	var $_link_to_dataset;
	function __construct()
	{
	    parent::__construct();

	    // Register Extra tasks
	    $this->registerTask( 'add', 'edit' );
		$this->_dataset_name = JRequest::getWord('dataset', '');
		$this->_component_name = JRequest::getWord('component', '');
		$this->_link_to_dataset = 'index.php?option=com_adminMachine&controller=dataset&component='.$this->_component_name.'&dataset='.$this->_dataset_name;
	}

	/**
	 * display the edit form
	 * @return void
	 */
	function edit()
	{
	    JRequest::setVar( 'view', 'datarow' );
	    JRequest::setVar( 'layout', 'form'  );
	    JRequest::setVar('hidemainmenu', 1);
		
	    parent::display();
	}

	/**
	 * save record
	 * @return void
	 */
	function save()
	{
		$model = $this->getModel('datarow');
		if($model->store()) {
			$msg = JText::_('Datarow Saved');
		} else {
			$msg = JText::_('Error Saving Datarow');
		}
		
		$additionalParams = $model->_dataset->getDatasetFormParametersForURI();
//		JError::raiseNotice('SOME_ERROR_CODE', $msg);
		$this->setRedirect($this->_link_to_dataset.$additionalParams, $msg);		
	}

	/**
	 * remove record(s)
	 * @return void
	 */
	function remove()
	{
		$model = $this->getModel('datarow');
		if($model->delete()){
			$msg = JText::_('Datarow(s) Deleted'); 
		} else {
			$msg = JText::_('Error: one or more datarows could not be deleted');
		}
		
		$additionalParams = $model->_dataset->getDatasetFormParametersForURI();
		$this->setRedirect($this->_link_to_dataset.$additionalParams, $msg);
		
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
	    $msg = JText::_( 'Operation Cancelled' );
		$this->setRedirect($this->_link_to_dataset, $msg);
	}

	
	function performAction()
	{	
		$model = $this->getModel('datarow');
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
