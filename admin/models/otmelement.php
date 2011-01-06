<?php
/**
 * @package adminMachine
 * @subpackage 
 * @license cc-by-sa
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
include_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'dataset.php');

/**
 * AdminMachine Component OTMelement Model
 *
 * @package adminMachine
 * @subpackage 
 * @since		0.9
 */
class AdminMachineModelOTMelement extends JModel
{
	/**
	 * Category data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Category total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	var $_component;
	var $_dataset_name;
	var $_dataset;


	var $_selected_id;
	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();

		global $mainframe, $option;
		
		$this->_component = JRequest::getWord('component', null);
		$this->_dataset_name = JRequest::getWord('dataset', null);	
		$this->_selected_id = JRequest::getVar('ids', 0);	
		
		if($this->_component)
		{
			$lang =& JFactory::getLanguage();
			$lang->load('com_'.$this->_component, JPATH_ADMINISTRATOR, $lang->getTag());
		}
		
		$limit		= $mainframe->getUserStateFromRequest( $this->_component.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $this->_component.'limitstart', 'limitstart', 'int' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		
		$this->_setDataset();
		
	}

	function _setDataset()
	{
		$this->_dataset =& AdminMachineDatasetHelper::getDataSet($this->_component, $this->_dataset_name);		
	}


	function _getDatasetData()
	{
		$this->_data['rows'] = $this->_dataset->getData($this->getState('limitstart'), $this->getState('limit'));	
		$this->_data['name'] = $this->_dataset_name;
		$this->_data['component'] = $this->_component;
		$this->_data['fieldsMeta']= $this->_dataset->_getFieldsMeta();	
		
		$this->_data['selection'][] = $this->_dataset->getRowData($this->_selected_id); 
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		if(!$this->_dataset)
			$this->_data['errors'][] = JText::_('Could Not Find Dataset');
		else
			$this->_getDatasetData();	
		return $this->_data;		
	}

	/**
	 * Total rows in otm table
	 *
	 * @access public
	 * @return integer
	 * @since 0.9
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the otms
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}


		return $this->_pagination;
	}

	/**
	 * Method to build the query for the otms
	 *
	 * @access private
	 * @return string
	 * @since 0.9
	 */
	function _buildQuery()
	{
		return $this->_dataset->getQuery();
	}


}
?>