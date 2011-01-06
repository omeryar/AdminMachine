<?php
	function PIE_GetTexts($filePath)
	{
		$texts = array();
		$lines = file($filePath);
		
		foreach($lines as $line_num => $line)
		{
			if (substr_count($line, "#") == 0)
			{
				$keyAndText = explode("=", trim($line));
				$texts[$keyAndText[0]] = $keyAndText[1];
			}
		}
		
		return $texts;
	}
	
	function PIE_Echo($text)
	{
		echo $text;
		//echo utf8_encode($text);
	}	
?>