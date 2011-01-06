<?php
/**
 * @package    adminMachine
 * @subpackage 
 * @link 
 * @license    cc-by-sa
*/

// no direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Datarow Component
 *
 * @package    adminMachine
 */

class AdminMachineViewDatarow extends JView
{

	function display($tpl = null)
	{
		
		//get the data
		$data =& $this->get('Data');
		
		$isNew = ($data['row']->id < 1);

		$text = $isNew ? JText::_('New') : JText::_('Edit');
		JToolBarHelper::title($data['display_name'].': <small> '.$text.'</small>'.'<small>(Admin Machine)</small>' );
		JToolBarHelper::save();
		
		$backName = 'Cancel';
	
		// $link = str_replace('/administrator/', '', JRequest::getURI());
		// if($link == "index.php")
		// {
		// 	$link .= '?option=com_adminMachine&controller=dataset&dataset='.$data['name'].'&component='.$data['component'];
		// }
		// else
		// {
		// 	$link = str_replace('controller=datarow', 'controller=dataset', $link);
		// 	$link = str_replace('task=edit', '', $link);
		// 	$link = str_replace('task=add', '', $link);	
		// }
		// 
		// if(isset($data['datasetParams']) && count($data['datasetParams']) > 0)
		// {
		// 	foreach($data['datasetParams'] as $param)
		// 		$link .= '&'.$param->name.'='.$param->value;
		// }
		// $bar = & JToolBar::getInstance('toolbar');	
		// $bar->appendButton( 'Link','cancel', JText::_('Cancel'), JRoute::_($link));

		$bar = & JToolBar::getInstance('toolbar');	
		$bar->appendButton( 'Link','cancel', JText::_('Cancel'), 'javascript:history.back();');		
		
		
		// if($isNew){
		// 	JToolBarHelper::cancel();
		// } else {
		// 	//for existing items call the button 'close'
		// 	JToolBarHelper::cancel( 'cancel', 'Close');
		// }
		
		JHTML::_('behavior.modal', 'a.modal');

		
		$this->assignRef('data', $data);

		parent::display($tpl);

	}
}

?>