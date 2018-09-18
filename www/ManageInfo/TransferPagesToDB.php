<? 
include_once 'sys_config.class.php';
include_once 'definitions.inc';
 
function TotalFiles($FolderPath)
{
	$mysql = pdodb::getInstance();
	$mysql->Execute("SET @@session.wait_timeout=1000");
	$mysql->Execute("delete from mis.SystemTotalPages");
	$count = 0;
	if ($handle = opendir($FolderPath)) 
	{
	    while (false !== ($file = readdir($handle))) 
	    {
	    	if(is_dir($FolderPath.'/'.$file))
	    	{
	    		if($file!="." && $file!="..")
	    			$count += TotalFiles($FolderPath.'/'.$file);
	    	}
	    	else 
	    	{
	    		//if((strpos($file, '.php',1)||strpos($file, '.inc',1) ) )
	    		if(strlen($file)>3)
	    		{
	    			$extension = substr($file, strlen($file)-3, 3);
	    			if($extension=="php" || $extension=="inc")
	    			{
	    				echo $FolderPath."/".$file.":";
	    				echo date ("Y-m-d H:i:s", filemtime($FolderPath."/".$file));
	    				echo "<br>";
	    				$mysql->Execute("insert into mis.SystemTotalPages (FolderPath, PageName, LastModificationDate) values ('".$FolderPath."','".$file."', '".date ("Y-m-d H:i:s", filemtime($FolderPath."/".$file))."')");
	    				$count++;
	    			}

	    		}
	    	}
	    }
		closedir($handle);
	}
	return $count;
}

echo TotalFiles("/var/www/sadaf");
?>
</html>