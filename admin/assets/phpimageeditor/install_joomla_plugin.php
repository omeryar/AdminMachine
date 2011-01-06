<?php
	define( '_JEXEC', 1 );
	define('DS', DIRECTORY_SEPARATOR);
	define('JPATH_BASE', dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..");
	require_once( JPATH_BASE .DS.'includes'.DS.'defines.php' );
	require_once( JPATH_BASE .DS.'includes'.DS.'framework.php' );
	
	$mainframe =& JFactory::getApplication('administrator');	
	$mainframe->initialise();
	$mainframe->route();
	
	$db =& JFactory::getDBO();
	$dbTablePrefix = $mainframe->getCfg('dbprefix');
?>
<html>
	<head>
		<title>Installation PHP Image Editor as a plugin in Joomla</title>
	</head>
	<h1>Install PHP Image Editor as a plugin in Joomla</h1>
	<?php
		$db->setQuery("SELECT * FROM ".$dbTablePrefix."plugins WHERE element = 'joomla_phpimageeditor'");
		$result = $db->query();
		
		if (!$result) 
		{
			echo '<p style="color: red;">'.$db->stderr().'</p>';
		}
		else
		{
			if ($db->getNumRows($result) > 0)
			{
				echo '<p style="color: red;">The plugin is already installed.</p>';
			}
			else
			{
				$db->setQuery("INSERT INTO ".$dbTablePrefix."plugins (id ,name ,element ,folder ,access ,ordering ,published ,iscore ,client_id ,checked_out ,checked_out_time ,params )VALUES (NULL , 'PHP Image Editor - Edit your images directly in Joomla!', 'joomla_phpimageeditor', 'system', '0', '0', '1', '0', '0', '0', '0000-00-00 00:00:00', '');");
				$result = $db->query();
				if (!$result) 
				{
					echo '<p style="color: red;">An error occurred during the installation: '.$db->stderr().'</p>';
				}
				else 
				{
					echo '<p style="color: green;">Successfull installation</p>';
				}
			}
		}
	?>
</html>