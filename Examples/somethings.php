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

class AdminMachineDatasetSomethings extends AdminMachineDataset
{
	
	function _getQueryBase()
	{
		return "SELECT `id`, `value`, `something` FROM `#__rbow_test_something` ";
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

	function &getRowData($row_ids = array())
	{
		if(count($row_ids) == 0)
			return null;
		
		$ids = implode(', ', $row_ids);
		
		$query = $this->_getQueryBase(). " WHERE `id` IN ($ids) ";
		return $this->_getList($query);
	}

	function &_getFieldsMeta()
	{
		$fields = array();
		$obj = new stdClass();
		$obj->type = ADMINMACHINE_FIELDTYPES_NUMBER;	
		$obj->link = true;
		$obj->returnAs = 'something_ids';
		$fields['id'] = $obj;
		
		$obj = new stdClass();
		$obj->type = ADMINMACHINE_FIELDTYPES_STRING;
		$obj->filter = true;	
		$fields['value'] = $obj;	

		$obj = new stdClass();
		$obj->type = ADMINMACHINE_FIELDTYPES_STRING;	
		$obj->link = true;
		$obj->filter = true;
		$obj->returnAs = 'something';	
		$fields['something'] = $obj;
		
		return $fields;		
	}

	function _buildContentWhere()
	{		
		global $mainframe, $option;
	
		$filter 			= $mainframe->getUserStateFromRequest( $option.'.mtmelement.filter', 'filter', '', 'string' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.mtmelement.search', 'search', '', 'string' );
		$search 			= $this->_db->getEscaped( trim(JString::strtolower( $search ) ) );
	
	
		$where = array();

		if ($search && $filter == 'value') {
			$where[] = ' LOWER(`value`) LIKE \'%'.$search.'%\' ';
		}

		if ($search && $filter == 'something') {
			$where[] = ' LOWER(`something`) LIKE \'%'.$search.'%\' ';
		}

	
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		
		return $where;
	}
	
}

?>