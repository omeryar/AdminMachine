<?php
/**
 * @package    adminMachine
 * @subpackage 
 * @link 
 * @license    cc-by-sa
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

define('ADMINMACHINE_FIELDTYPES_NUMBER', 1);
define('ADMINMACHINE_FIELDTYPES_STRING', 2);
define('ADMINMACHINE_FIELDTYPES_TEXT', 3);
define('ADMINMACHINE_FIELDTYPES_BOOLEAN', 4);
define('ADMINMACHINE_FIELDTYPES_DATE', 5);
define('ADMINMACHINE_FIELDTYPES_SELECT', 6);
define('ADMINMACHINE_FIELDTYPES_IMAGE', 7);
define('ADMINMACHINE_FIELDTYPES_FILE', 8);
define('ADMINMACHINE_FIELDTYPES_PASSWORD', 9);
define('ADMINMACHINE_FIELDTYPES_HIDDEN', 10);
define('ADMINMACHINE_FIELDTYPES_OTM', 20);
define('ADMINMACHINE_FIELDTYPES_MTM', 30);

class AdminMachineDataset
{
	var $_db;
	var $_display_name;
	var $_total;
	
	function __construct()
	{
		$this->_db =& JFactory::getDBO();
	}

/////////////////////////////////////
//
//  HELPER FUNCTIONS
//  to be implemented by inheritors
/////////////////////////////////////	
	
	/**
	 * gets a list of objects
	 *
	 * @param string query: SQL query to perform
	 * @param int optional limitstart
	 * @param int optional limit
	 * @return mixed array of objects or null 
	 */
    function &_getList($query, $limitstart=0, $limit=0)
    {
        $this->_db->setQuery( $query, $limitstart, $limit );
        $result = $this->_db->loadObjectList();

        return $result;
    }

	/**
	 * gets a db object
	 *
	 * @param string query: SQL query to perform
	 * @return mixed object or null 
	 */
    function &_getObject($query)
    {
        $this->_db->setQuery($query);
        $result = $this->_db->loadObject();

        return $result;
    }

/////////////////////////////////////
//
//  OPTIONAL FUNCTIONS
//  can be implemented by inheritors
/////////////////////////////////////

	/**
	 * returns the total number of records (in case the normal getTotal is not enough)
	 *
	 * @return int (-1 when not implemented, 0 or above when is)
	 */
	function getTotal()
	{
		return -1;
	}

/////////////////////////////////////
//
//  ABSTRACT FUNCTIONS
//  to be implemented by inheritors
/////////////////////////////////////


	/**
	 * retrieves the dataset query base (with no wheres)
	 *
	 * @return string SQL query
	 */
	function _buildQueryBase()
	{
		return '#get query not implemented';
	}

	/**
	 * retrieves the dataset query, with or without the wheres
	 *
	 * @param boolean $withFilters: add the where clause or not
	 * @return string SQL query
	 */
	function getQuery($withFilters = true)
	{
		$query = $this->_buildQueryBase();
		if($withFilters)
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
		$res = null;
		return $res;

		////////////////////////////////////////////
		//
		//	Example getData 
		//
		///////////////////////
		//		
		// $query = $this->getQuery();
		// $data = $this->_getList($query);
		// $this->_addMTMData(&$data);
		// return $data;
		//
		////////////////////////////////////////////	
	}

	/**
	 * retrieves an empty datarow
	 *   to be used when adding new item
	 * @return object
	 */
	function &getEmptyRow()
	{
		$res = null;
		return $res;
		
		////////////////////////////////////////////
		//
		//	Example getEmptyRow 
		//
		///////////////////////
		//		
		// $empty = new stdClass();
		// $empty->id = null;
		// $empty->something = '';
		// return $empty;
		// 
		//
		////////////////////////////////////////////		

		
	}

	/**
	 * retrieves row(s) data
	 *   
	 * @param mixed row_id: either int id or array of ids of row(s) to retrieve
	 * @return mixed object, array of objects, or null
	 */	
	function &getRowData($row_id)
	{
		$res = null;
		return $res;
		
		////////////////////////////////////////////
		//
		//	Example getEmptyRow 
		//
		///////////////////////
		//		
		// if($row_id < 1)
		// 	return null;
		// 
		// $query = $this->getQuery.' WHERE `id` = '.$row_id;
		// $data =  $this->_getObject($query);	
		// return $data;
		//
		////////////////////////////////////////////		

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
		$res = null;
		return $res;
	}

	/**
	 * get extra parameters for the dataset view (to be added as hidden fields of the form)
	 *   
	 * @return array of objects representing parameters
	 */
	function &_getDatasetFormParameters()
	{
		$res = null;
		return $res;		
	}

	/**
	 * get extra parameters for the dataset view (in URL encoding)
	 *   
	 * @return string of URI encoded parameters & values, with leading ampersand (&)
	 */
	function getDatasetFormParametersForURI()
	{
		$paramStr = '';
		$params =& $this->_getDatasetFormParameters();
		if(!$params)
			return '';
		foreach($params as $param)
			$paramStr .= '&'.$param->name.'='.urlencode($param->value);
			
		return $paramStr;
	}

	/**
	 * stores datarow in database
	 *  parameters are to be found in the request   
	 *
	 * @return boolean success
	 */
	function store()
	{
		return false;
		////////////////////////////////////////////
		//
		//	Example Store 
		//
		///////////////////////
		//
		// $res = array();
		// $res['id'] = JRequest::getInt('id', 0);
		// $res['some_column'] = JRequest::getString('some_column', null);
		// 		
		// $query = '';
		// if($res['id'] > 0)
		// 	$query = ' UPDATE `#__rbow_xxx` SET `some_column` = \''.$res['some_column'].'\' WHERE `id` = '.$res['id'];
		// else
		// 	$query = ' INSERT INTO `#__rbow_xxx` (`some_column`) VALUES (\''.$res['question'].'\') ';
		// 			
		// $this->_db->setQuery($query);
		// $this->_db->query();
		// if($this->_db->getErrorNum() != 0)
		// {
		// 	JError::raiseWarning('ERROR_CODE', JText::_('Problem Saving To Database:').$this->_db->getErrorMsg());
		// 	return false;
		// }
		//
		// return true;
		//
		////////////////////////////////////////////
		
	}

	/**
	 * deletes datarow(s) from database
	 *  parameters are to be found in the request   
	 *   
	 * @return boolean success
	 */	
	function delete()
	{
		return false;
		////////////////////////////////////////////
		//
		//	Example Delete 
		//
		///////////////////////
		//
		// $id = JRequest::getInt('id', 0);
		// $cid = JRequest::getVar('cid', 0, '', 'array');
		// 
		// if($id < 1 && !is_array($cid))
		// {
		// 	JError::raiseWarning('ERROR_CODE', JText::_('Problem Deleting:').JText::_('Parameter Problem'));
		// 	return false;	
		// }
		// 
		// if($id > 0)
		// 	$cid = array($id);	
		// 
		// $query = ' DELETE FROM `#__rbow_xxx` WHERE `id` IN ('.implode(',',$cid).') ';
		// $this->_db->setQuery($query);
		// $this->_db->query();
		// if($this->_db->getErrorNum() != 0)
		// {
		// 	JError::raiseWarning('ERROR_CODE', JText::_('Problem Deleting Row(s):').$this->_db->getErrorMsg());
		// 	return false;
		// }
		// 
		// return true;	
		//
		////////////////////////////////////////////
		
	}

	/**
	 * gets actions that can be performed on the dataset
	 *   
	 * @return array of object with action properties or null
	 */	
	function &getDatasetActions()
	{
		$defaultActions = array();
		
		$action = new AdminMachineDatasetAction('addX');
		$defaultActions[] = $action;
		
		$action = new AdminMachineDatasetAction('editX');
		$defaultActions[] = $action;		
		
		$action = new AdminMachineDatasetAction('deleteX');
		$action->requiresSelection = true;
		$defaultActions[] = $action;		

		$action = new AdminMachineDatasetAction('back');
		$defaultActions[] = $action;
		
		return $defaultActions;
	}

	/**
	 * gets actions that can be performed on a datarow
	 *   
	 * @return string html representations of actions (link?)
	 */	
	function &getDatarowActions()
	{
		$actions = array();
		
		$action = new AdminMachineDatasetAction('edit');
		$action->name = JText::_('Edit');
		$action->action = 'edit';
		$actions[] = $action;

		return $actions;
	}
	
	function performAction()
	{
		print_r(JText::_('No Actions Defined'));
		return false;	
	}
	
	
	function _buildContentWhere()
	{
		return '';
	}
	
	function _store($tableName, $id, $values, $forcePrefix = true)
	{
		//check that the tablename, id and values are ok
		if(trim($tableName) == '' || $id < 0 || !is_numeric($id) || !is_array($values) || count($values) == 0)
			return false;
		
		//if the table name doesn't start with the db prefix #__, add it 
		if ($forcePrefix && substr($tableName,0,3) != '#__')
			$tableName = '#__'.$tableName;
			
		$query = '';		

		if($id > 0)
		{
			//build query for updating row
			$query = ' UPDATE `'.$tableName.'` SET ';
			$isfirst = true;
			
			foreach($values as $column => $value)
			{
				if(!$isfirst)
					$query .= ', ';
				else
					$isfirst = false;
					
				if($value == 'NULL')
				  $query .= '`'.$column.'` = NULL ';
				else	
				  $query .= '`'.$column.'` = '.$this->_db->Quote($value).' ';
			}
			$query .= ' WHERE `id` = '.$id.' ';

		}
		else
		{
			//build query for new row
			$columns = '';
			$column_values = '';
			$isfirst = true;
			foreach($values as $column => $value)
			{
				if(!$isfirst)
				{
					$columns .= ', ';
					$column_values .= ', ';
				}
				else
					$isfirst = false;
					
				$columns .= '`'.$column.'`';
				if(!is_numeric($value) && $value == 'NULL')
				  $column_values .= 'NULL';
				else
				  $column_values .= $this->_db->Quote($value);
			}
			
			
			$query = ' INSERT INTO `'.$tableName.'` ('.$columns.') VALUES ('.$column_values.') ';
		}

		$this->_db->setQuery($query);
		$this->_db->query();
		if($this->_db->getErrorNum() != 0)
		{
			JError::raiseWarning('ERROR_CODE', JText::_('Problem Saving To Database:').$this->_db->getErrorMsg());
			return false;
		}
		
		return true;
	}
	
}


class AdminMachineDatasetFieldMeta
{
	var $type; // type of field
	var $readonly; // can field be edited?
	var $link; // will be a link to edit?
	var $title; //title of field to show as its name
	var $filter; // filterable?
	var $options; //used only for select types. gives options as string with ; as delimiter (val1;val2;...). if there's a need for labels, seperate label and value with = (val1=label1;val2=label2;...) 

// used only for otm and mtm relations
	var $dataset; // which dataset described the otm/mtm
	var $dataset_component; //allows connection to other components datasets
	var $idSource; // which column holds the id for that dataset
	var $returnAs; //how to call the value when returning it (useful for otm datasets)
	var $translationTable; //for OTM: if specified, the parameters returned from the OTM table will be 
	                        //put into fields according to the table, overriding elementSelect function;

	function __construct($type = 0)
	{
		$this->type = $type;
		$this->readonly = false;
		$this->link = false;
		$this->title = null;
		$this->filter = false;
		$this->options = null;
		
		$this->dataset = null;
		$this->dataset_component = null;
		$this->idSource = 'id';
		$this->otmName = null;
		$this->returnAs = null;
		$this->translationTable = null;
	}
}

class AdminMachineDatasetAction
{
	var $type;
	var $name;
	var $action;
	var $icon;
	var $link; //used only for type 'link'
	var $modal; //used only for type 'link'. used if new window is to opened as modal
	
	var $requiresSelection;
	var $requiresConfirmation;
	
	//these two should be used when you wish to call an action on a different dataset/component
	var $dataset;
	var $component;
	
	function __construct($type = 'custom')
	{
		$this->type = $type;
		$this->name = '';
		$this->action = '';
		$this->icon = '';
		$this->link = null;
		$this->modal = false;
		
		$this->requiresSelection = false;
		$this->requiresConfirmation = false;
		
		$this->dataset = null;
		$this->component = null;
	}
}

class AdminMachineDatasetFormParam
{
	var $name;
	var $value;
	
	function __construct()
	{
		$this->name = '';
		$this->value = '';		
	}
}

?>