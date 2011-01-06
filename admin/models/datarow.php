<?php
/**
 * Datarow Model for Admin Machines Component
 * 
 * @package    adminMachine
 * @subpackage 
 * @link
 * @license		GNU/GPL
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');
include_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'dataset.php');


/**
 * Admin Machine Datarow Model
 *
 * @package    adminMachines
 * @subpackage 
 */
class AdminMachineModelDatarow extends JModel
{

	/**
     * array of data to return
     *
     * @var array
     */
    var $_data;
	var $_id;
	
	var $_component;
	var $_dataset_name;

	var $_dataset;

	function __construct()
	{
	    parent::__construct();
		$this->_data = array();	
		
		$this->_component = JRequest::getWord('component', null);

		if($this->_component)
		{
			$lang =& JFactory::getLanguage();
			$lang->load('com_'.$this->_component, JPATH_ADMINISTRATOR, $lang->getTag());
		}
		
		$this->_dataset_name = JRequest::getWord('dataset', null);	
		$this->_id = JRequest::getInt('id', 0);
		$this->_setDataset();
	}

	function _setDataset()
	{
		$this->_dataset =& AdminMachineDatasetHelper::getDataSet($this->_component, $this->_dataset_name);		
	}


	function &getData()
	{
		if(empty($this->_data) && $this->_id )
		{
			$this->_data['row'] = $this->_dataset->getRowData($this->_id);
		}
		if(!array_key_exists('row', $this->_data) || !$this->_data['row'])
		{
			$this->_data['row'] = $this->_dataset->getEmptyRow();
		}
		$this->_data['display_name'] = $this->_dataset->_display_name;
		$this->_data['name'] = $this->_dataset_name;
		$this->_data['component'] = $this->_component;		
		$this->_data['fieldsMeta']= $this->_dataset->_getFieldsMeta();
		$this->_data['datasetParams'] = $this->_dataset->_getDatasetFormParameters();
	
		return $this->_data;
	}
	
	function performAction()
	{
		$action_component = JRequest::getWord('actionComponent', '');
		if($action_component == '')
				$action_component = $this->_component;
				
		$action_dataset = JRequest::getWord('actionDataset', '');	
		if($action_dataset == '')
				$action_dataset = $this->_dataset_name;		
							
		if($action_component != $this->_component || $action_dataset != $this->_dataset_name)
		{
			$this->_dataset = AdminMachineDatasetHelper::getDataSet($action_component, $action_dataset);
		}
		
		if(!$this->_dataset)
		{
			$this->_data['errors'][] = JText::_('Could Not Find Dataset');
			return false;
		}
		else
			return $this->_dataset->performAction();	
	}
	
	/**
	 * Method to store a record
	 *
	 * @access    public
	 * @return    boolean    True on success
	 */
	function store()
	{
		return $this->_dataset->store();
	}
	
	/**
	 * Method to delete a record
	 *
	 * @access    public
	 * @return    boolean    True on success
	 */		
	function delete()	
	{
		return $this->_dataset->delete();
	}	
	

}
?>