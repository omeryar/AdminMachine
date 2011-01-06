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
 * AdminMachine Component MTMelement Model
 *
 * @package adminMachine
 * @subpackage 
 * @since		0.9
 */
class AdminMachineModelMTMelement extends JModel
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

	var $_selected_ids;

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
		
		if($this->_component)
		{
			$lang =& JFactory::getLanguage();
			$lang->load('com_'.$this->_component, JPATH_ADMINISTRATOR, $lang->getTag());
		}
		
		$this->_selected_ids = explode(',', JRequest::getVar('ids', 0));

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
		
		$this->_data['selection'] = array();
		
		if(is_array($this->_selected_ids))
		{
			foreach($this->_selected_ids as $id)
				$this->_data['selection'] []= $this->_dataset->getRowData($id); 
		}
		elseif(isset($this->_selected_ids) && $this->_selected_ids)
			$this->_data['selection'] []= $this->_dataset->getRowData($this->_selected_ids); 
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
	 * Total rows in mtm table
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
	 * Method to get a pagination object for the mtms
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
	 * Method to build the query for the mtms
	 *
	 * @access private
	 * @return string
	 * @since 0.9
	 */
	function _buildQuery()
	{
		return $this->_dataset->getQuery();
	}

	// /**
	//  * Method to build the orderby clause of the query for the mtms
	//  *
	//  * @access private
	//  * @return string
	//  * @since 0.9
	//  */
	// function _buildContentOrderBy()
	// {
	// 	global $mainframe, $option;
	// 
	// 	$filter_order		= $mainframe->getUserStateFromRequest( $option.'.mtmelement.filter_order', 'filter_order', 'a.ordering', 'cmd' );
	// 	$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'.mtmelement.filter_order_Dir', 'filter_order_Dir', '', 'word' );
	// 
	// 	$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', a.ordering';
	// 
	// 	return $orderby;
	// }
	// 

}
?>