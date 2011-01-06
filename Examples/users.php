<?php
/**
 * @package    adminMachine
 * @subpackage 
 * @link 
 * @license    cc-by-sa
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

include_once(JPATH_BASE.DS.'components'.DS.'com_adminmachine'.DS.'datasets'.DS.'adminMachineDataset.php');

class AdminMachineDatasetUsers extends AdminMachineDataset
{
	function _getQueryBase()
	{
		return "SELECT `id`, `name`, `username`, `email`, `usertype`, `block` FROM `#__users` ";
	}
	
	function getQuery()
	{
		$query = $this->_getQueryBase();
		$where = $this->_buildContentWhere();
		return $query.$where;
	}
	
	function &getData()
	{
		global $mainframe;
		global $option;
		$query = $this->getQuery();
		
		$limit		= $mainframe->getUserStateFromRequest( $option.'limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 'int' );
		
		
		return $this->_getList($query, $limitstart, $limit);
	}

	function &getRowData($row_id = 0)
	{
		$query = $this->_getQueryBase(). " WHERE `id` = $row_id ";
		return $this->_getList($query);
	}

	function &_getFieldsMeta()
	{
		$fields = array();
		$obj = new stdClass();
		$obj->type = ADMINMACHINE_FIELDTYPES_NUMBER;	
		$obj->link = true;
		$obj->returnAs = 'user_id';
		$fields['id'] = $obj;
		
		$obj = new stdClass();
		$obj->type = ADMINMACHINE_FIELDTYPES_STRING;
		$obj->filter = true;	
		$fields['name'] = $obj;	

		$obj = new stdClass();
		$obj->type = ADMINMACHINE_FIELDTYPES_STRING;	
		$obj->link = true;
		$obj->filter = true;
		$obj->returnAs = 'username';	
		$fields['username'] = $obj;

		$obj = new stdClass();
		$obj->type = ADMINMACHINE_FIELDTYPES_STRING;	
		$obj->filter = true;	
		$fields['email'] = $obj;

		$obj = new stdClass();
		$obj->type = ADMINMACHINE_FIELDTYPES_STRING;	
		$fields['usertype'] = $obj;
		
		$obj = new stdClass();
		$obj->type = ADMINMACHINE_FIELDTYPES_STRING;	
		$fields['block'] = $obj;	
		
		return $fields;		
	}

	function _buildContentWhere()
	{
		global $mainframe, $option;
	
		$filter 			= $mainframe->getUserStateFromRequest( $option.'.otmelement.filter', 'filter', '', 'string' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.otmelement.search', 'search', '', 'string' );
		$search 			= $this->_db->getEscaped( trim(JString::strtolower( $search ) ) );
	
		$where = array();

		if ($search && $filter == 'name') {
			$where[] = ' LOWER(`name`) LIKE \'%'.$search.'%\' ';
		}

		if ($search && $filter == 'username') {
			$where[] = ' LOWER(`username`) LIKE \'%'.$search.'%\' ';
		}

		if ($search && $filter == 'email') {
			$where[] = ' LOWER(`email`) LIKE \'%'.$search.'%\' ';
		}
	
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
	
		return $where;
	}
	
}

?>