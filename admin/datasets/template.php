<?php
/**
 * @package    adminMachine
 * @subpackage 
 * @link 
 * @license    GPL
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
include_once(JPATH_BASE.DS.'components'.DS.'com_adminmachine'.DS.'datasets'.DS.'adminmachinedataset.php');

class AdminMachineDatasetTemplate extends AdminMachineDataset
{

	function __construct()
	{
		parent::__construct();
		$this->_display_name = "Dataset Name";
	}

	function _buildQueryBase()
	{
		return ' SELECT * FROM `#__sometable` ';
	}

	function getQuery($getWhere = true)
	{
		$query = $this->_buildQueryBase();
		if($getWhere)
			return $query.$this->_buildContentWhere();
		else
			return $query;
	}

	/**
	 * retrieves the dataset data from the db
	 *
	 * @return mixed array of objects or null
	 */	
	function &getData($limitStart = 0, $limit = 0)
	{
		$query = $this->getQuery();
		$data = $this->_getList($query, $limitStart, $limit);
		$this->_addMTMData(&$data);
		return $data;
	}

	/**
	 * retrieves an empty datarow
	 *   to be used when adding new item
	 * @return object
	 */
	function &getEmptyRow()
	{
		$empty = new stdClass();
		$empty->id = null;
		$empty->something = '';
		return $empty;
	}

	/**
	 * retrieves row(s) data
	 *   
	 * @param mixed row_id: either int id or array of ids of row(s) to retrieve
	 * @return mixed object, array of objects, or null
	 */	
	function &getRowData($row_id)
	{
	
		if($row_id < 1)
			return $this->getEmptyRow();
		
		$query = $this->getQuery(false).' WHERE `id` = '.$row_id;
		$data =  $this->_getObject($query);	
		return $data;

	}

	/**
	 * adds the many to many relation data to the basic data
	 *   
	 * @param array of objects data: pointer to current data rows
	 * @return void
	 */	
	function _addMTMData(&$data)
	{
		return;
	}

	/**
	 * get the meta data of the field structure of the dataset
	 *   
	 * @return array of objects representing dataset fields
	 */	
	function &_getFieldsMeta()
	{
		$fields = array();
	
		$obj = new AdminMachineDatasetFieldMeta(ADMINMACHINE_FIELDTYPES_NUMBER);
		$obj->readonly = true;
		$fields['id'] = $obj;
		
		return $fields;
	}

	/**
	 * get extra parameters for the dataset view (to be added as hidden fields of the form)
	 *   
	 * @return array of objects representing parameters
	 */
	function &_getDatasetFormParameters()
	{
		$params = array();
		
		// $obj = new AdminMachineDatasetFormParam();
		// $obj->name = 'type_id';
		// $obj->value = $this->_topics_id;
		// $params[] = $obj;
		
		return $params;		
	}

	/**
	 * stores datarow in database
	 *  parameters are to be found in the request   
	 *
	 * @return boolean success
	 */
	function store()
	{
		$res = array();
		$res['id'] = JRequest::getInt('id', 0);
		$res['some_column'] = JRequest::getString('some_column', null);
				
		$query = '';
		if($res['id'] > 0)
			$query = ' UPDATE `#__rbow_xxx` SET `some_column` = \''.$res['some_column'].'\' WHERE `id` = '.$res['id'];
		else
			$query = ' INSERT INTO `#__rbow_xxx` (`some_column`) VALUES (\''.$res['question'].'\') ';
					
		$this->_db->setQuery($query);
		$this->_db->query();
		if($this->_db->getErrorNum() != 0)
		{
			JError::raiseWarning('ERROR_CODE', JText::_('Problem Saving To Database:').$this->_db->getErrorMsg());
			return false;
		}
		
		return true;		
	}

	/**
	 * deletes datarow(s) from database
	 *  parameters are to be found in the request   
	 *   
	 * @return boolean success
	 */	
	function delete()
	{
		$id = JRequest::getInt('id', 0);
		$cid = JRequest::getVar('cid', 0, '', 'array');
		
		if($id < 1 && !is_array($cid))
		{
			JError::raiseWarning('ERROR_CODE', JText::_('Problem Deleting:').JText::_('Parameter Problem'));
			return false;	
		}
		
		if($id > 0)
			$cid = array($id);	
		
		$query = ' DELETE FROM `#__rbow_xxx` WHERE `id` IN ('.implode(',',$cid).') ';
		$this->_db->setQuery($query);
		$this->_db->query();
		if($this->_db->getErrorNum() != 0)
		{
			JError::raiseWarning('ERROR_CODE', JText::_('Problem Deleting Row(s):').$this->_db->getErrorMsg());
			return false;
		}
		
		return true;	
	}

	/**
	 * gets actions that can be performed on the dataset
	 *   
	 * @return array of object with action properties or null
	 */	
	function &getDatasetActions()
	{
		return parent::getDatasetActions();
	}

	/**
	 * gets actions that can be performed on a datarow
	 *   
	 * @return string html representations of actions (link?)
	 */	
	function &getDatarowActions()
	{
		return parent::getDatarowActions();
	}
	
	function performAction()
	{
		parent::performAction();
		// $action = JRequest::getVar('action');
		// switch($action)
		// {
		// 	case 'updateAll':
		// 		print_r('updateall');
		// 		return true;
		// 		break;
		// 	case 'update':
		// 		print_r('update');
		// 		return true;
		// 		break;
		// 	default:
		// 		return false;
		// 		break;
		// }	
	}

	function _buildContentWhere()
	{
		global $mainframe, $option;
	
		$filter = $mainframe->getUserStateFromRequest( $option.'.'.get_class($this).'.filter', 'filter', '', 'string' );
		$search = $mainframe->getUserStateFromRequest( $option.'.'.get_class($this).'.search', 'search', '', 'string' );
		$search = $this->_db->getEscaped( trim(JString::strtolower( $search ) ) );
	
		$where = array();

		if ($search && $filter == 'name') {
			$where[] = ' LOWER(`name`) LIKE \'%'.$search.'%\' ';
		}
	
		if ((strtolower($search) == 'y' || strtolower($search) == 'n') && $filter == 'published') {
			if(strtolower($search) == 'y')
				$where[] = '`published` = 1 ';
			else
				$where[] = '`published` = 0 ';
		}
	
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	
		return $where;
	}	
	
}

?>