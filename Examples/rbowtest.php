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

class AdminMachineDatasetRbowTest extends AdminMachineDataset
{
	
	function getQuery()
	{
		return "SELECT a.`id`, a.`user_test`, b.`username`
					FROM `#__rbow_test` as a
					JOIN `#__users` as b
					ON a.`user_id` = b.`id` ";
	}
	
	function &getData()
	{
		$query = $this->getQuery();
		$data = $this->_getList($query);
		$this->_addMTMData(&$data);
		return $data;
	}

	function &getEmptyRow()
	{
		$empty = new stdClass();
		$empty->id = null;
		$empty->user_test = '';
		$empty->user_id = null;
		$empty->username = '';
		$empty->something_ids = null;
		$empty->something = '';
		
		return $empty;		
	}

	function &getRowData($row_id = 0)
	{
		if($row_id < 1)
			return null;

		$query = "SELECT a.`id`, a.`user_test`, a.`user_id`, b.`username` 
					FROM `#__rbow_test` as a
					JOIN `#__users` as b
					ON a.`user_id` = b.`id`
					 WHERE a.`id` = $row_id";
		$data =  $this->_getObject($query);	
		$this->_addMTMData(array($data));
		return $data;		
	}
	
	function _addMTMData($data)
	{
		foreach($data as $row)
		{
			$query = 'SELECT a.`id`, a.`something`
			 			FROM `#__rbow_test_something` as a
						JOIN `#__rbow_tests_somethings` as b
						ON a.`id` = b.`something_id`
						WHERE b.`test_id` = '.$row->id;
				
			$list = $this->_getList($query);
			
			if(!$list)
			{
				$row->something_ids = null;
				$row->something = '';
				continue;
			}	
			
			$ids = array();
			$somethings = array();
			
			foreach($list as $item)
			{
				$ids[] = $item->id;
				$somethings[] = $item->something;
			}
			$row->something_ids = implode(',', $ids);
			$row->something = implode(',', $somethings);			
		}
	}
	
	function &_getFieldsMeta()
	{
		$fields = array();
		
		$obj = new AdminMachineDatasetFieldMeta(ADMINMACHINE_FIELDTYPES_NUMBER);
		$obj->readonly = true;
		$fields['id'] = $obj;
		
		$obj = new AdminMachineDatasetFieldMeta(ADMINMACHINE_FIELDTYPES_STRING);
		$fields['user_test'] = $obj;	

		$obj = new AdminMachineDatasetFieldMeta(ADMINMACHINE_FIELDTYPES_OTM);
		$obj->dataset = 'users';
		$obj->idSource = 'user_id';
		$fields['username'] = $obj;

		$obj = new AdminMachineDatasetFieldMeta(ADMINMACHINE_FIELDTYPES_HIDDEN);
		$fields['user_id'] = $obj;
		
		$obj = new AdminMachineDatasetFieldMeta(ADMINMACHINE_FIELDTYPES_MTM);
		$obj->dataset = 'somethings';
		$obj->idSource = 'something_ids';
		$fields['something'] = $obj;
		
		$obj = new AdminMachineDatasetFieldMeta(ADMINMACHINE_FIELDTYPES_HIDDEN);
		$fields['something_ids'] = $obj;		
		
		return $fields;
		
	}
	
	function &getDatasetActions()
	{
		$actions = array();
		
		$action = new AdminMachineDatasetAction();
		$action->name = JText::_('Update All');
		$action->action = 'updateAll';
		$action->icon = 'assign';

		$actions[] = $action;
		
		return $actions;
		
	}
	
	function &getDataRowActions()
	{
		$actions = array();
		
		$action = new AdminMachineDatasetAction();
		$action->name = JText::_('Update');
		$action->action = 'update';

		$actions[] = $action;
		
		return $actions;
		
	}	
	
	function performAction()
	{
		$action = JRequest::getVar('action');
		switch($action)
		{
			case 'updateAll':
				print_r('updateall');
				return true;
				break;
			case 'update':
				print_r('update');
				return true;
				break;
			default:
				return false;
				break;
		}
	}
	
	function store()
	{
		
		$res = array();
		$res['id'] = JRequest::getInt('id', 0);
		$res['user_test'] = JRequest::getVar('user_test', null);
		$res['user_id'] = JRequest::getInt('user_id', 0);
		$res['something_ids'] = JRequest::getVar('something_ids', '');
				
		$query = '';
		if($res['id'] > 0)
			$query = ' UPDATE `#__rbow_test` SET `user_test` = \''.$res['user_test'].'\', `user_id` = '.$res['user_id'].' WHERE `id` = '.$res['id'];
		else
			$query = ' INSERT INTO `#__rbow_test` (`user_test`, `user_id`) VALUES (\''.$res['user_test'].'\', '.$res['user_id'].') ';
					
		$this->_db->setQuery($query);
		$this->_db->query();
		if($this->_db->getErrorNum() != 0)
		{
			JError::raiseWarning('ERROR_CODE', JText::_('Problem Saving To Database:').$this->_db->getErrorMsg());
			return false;
		}

		if($res['id'] == 0)
			$res['id'] = $this->_db->insertid();
		else
		{
			$query = ' DELETE FROM `#__rbow_tests_somethings` WHERE `test_id` = '.$res['id'];
			$this->_db->setQuery($query);
			$this->_db->query();
			if($this->_db->getErrorNum() != 0)
			{
				JError::raiseWarning('ERROR_CODE', JText::_('Problem Deleting Relations:').$this->_db->getErrorMsg());
				return false;
			}		
		}
		
		$ids = explode(',', $res['something_ids']);
		$values = array();
		foreach($ids as $id)
			$values[] = '('.$res['id'].','.$id.')';
		
		$query = ' INSERT INTO `#__rbow_tests_somethings` (`test_id`, `something_id`) VALUES '.implode(',', $values);

		$this->_db->setQuery($query);
		$this->_db->query();
		if($this->_db->getErrorNum() != 0)
		{
			JError::raiseWarning('ERROR_CODE', JText::_('Problem Inserting Relations:').$this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
	
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
			
		$query = ' DELETE FROM `#__rbow_tests_somethings` WHERE `test_id` IN ('.implode(',',$cid).') ';
		$this->_db->setQuery($query);
		$this->_db->query();
		if($this->_db->getErrorNum() != 0)
		{
			JError::raiseWarning('ERROR_CODE', JText::_('Problem Deleting Relations:').$this->_db->getErrorMsg());
			return false;
		}		

		$query = ' DELETE FROM `#__rbow_test` WHERE `id` IN ('.implode(',',$cid).') ';
		$this->_db->setQuery($query);
		$this->_db->query();
		if($this->_db->getErrorNum() != 0)
		{
			JError::raiseWarning('ERROR_CODE', JText::_('Problem Deleting Row(s):').$this->_db->getErrorMsg());
			return false;
		}
		
		return true;
	}
	
}

?>