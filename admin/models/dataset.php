<?php
/**
 * @package		adminMachine
 * @subpackage	
 * @license		cc-by-sa
 */

/* Check to ensure this file is included in Joomla! */
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
include_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'dataset.php');

/**
 * adminMachine Component Dataset Model
 *
 * @author		Omer Yariv
 * @package		adminMachine
 * @subpackage	
 * @since 1.5
 */
class AdminMachineModelDataset extends JModel
{

	/**
     * array of data to return
     *
     * @var array
     */
    var $_data;

	var $_component;
	var $_dataset_name;

	var $_dataset;

	function __construct()
	{
		global $mainframe, $option;
		
	    parent::__construct();
		$this->_data = array();	
		
		$this->_component = JRequest::getWord('component', null);
		$this->_dataset_name = JRequest::getWord('dataset', null);	
		
		if($this->_component)
		{
			$lang =& JFactory::getLanguage();
			$lang->load('com_'.$this->_component, JPATH_ADMINISTRATOR, $lang->getTag());
		}
		
		$this->_setDataset();
		
		$limit		= $mainframe->getUserStateFromRequest( $this->_component.'_'.$this->_dataset_name.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $this->_component.'_'.$this->_dataset_name.'limitstart', 'limitstart', 'int' );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);		
		
	}

	function _setDataset()
	{
		$this->_dataset =& AdminMachineDatasetHelper::getDataSet($this->_component, $this->_dataset_name);		
	}

	function _getDatasetData()
	{
		$this->_data['rows'] = $this->_dataset->getData($this->getState('limitstart'), $this->getState('limit'));	
		$this->_data['name'] = $this->_dataset_name;
		$this->_data['display_name'] = $this->_dataset->_display_name;
		$this->_data['component'] = $this->_component;
		$this->_data['fieldsMeta']= $this->_dataset->_getFieldsMeta();
		$this->_data['setActions'] = $this->_dataset->getDatasetActions();
		$this->_data['rowActions'] = $this->_dataset->getDataRowActions();	
		$this->_data['datasetParams'] = $this->_dataset->_getDatasetFormParameters();
	}
	
	
	function &getData()
	{		
		if(!$this->_dataset)
			$this->_data['errors'][] = JText::_('Could Not Find Dataset');
		else
			$this->_getDatasetData();	
		return $this->_data;
	}	
			
	function performAction()
	{
		if(!$this->_dataset)
		{
			$this->_data['errors'][] = JText::_('Could Not Find Dataset');
			return false;
		}
		else
			return $this->_dataset->performAction();	
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
			$this->_total = $this->_dataset->getTotal();
			if($this->_total == -1)//meaning it wasnt implemented
			{
				$query = $this->_buildQuery();
				$this->_total = $this->_getListCount($query);				
			}
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
	 * Method to build the query
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