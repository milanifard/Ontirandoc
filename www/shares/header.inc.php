<?php
	include "sys_config.class.php";
	include "DateUtils.inc";
	include "SharedClass.class.php";

	function HTMLBegin($bgcolor = '#C8DEF0') 
	{
		echo '<style type="text/css" > INPUT, SELECT { font-family: Tahoma }'.
			'</style>'.
			'<link rel="stylesheet"  href="css/login.css" type="text/css">'.
			"<body  dir=rtl link=\"#0000FF\" alink=\"#0000FF\" vlink=\"#0000FF\">";
	}
	
	function HTMLEnd()
	{
		echo "</body></html>";
	}
	
	session_start();
	if(!isset($_SESSION["UserID"]))
	{
		echo "<script>document.location='SignOut.php?logout=1&SessionExpired=1';</script>";;
		die();
	}
	$mysql = pdodb::getInstance();
	
    $scriptURI = $_SERVER['REQUEST_URI'];
    
    if(strpos($scriptURI, '?') >0)
      $scriptName = substr($scriptURI, 0, strpos($scriptURI, '?'));
    else
      $scriptName = $scriptURI;
    $start = strrpos($scriptName, '/');
    $FileName = $start ? substr($scriptName, $start) : $scriptName;
    // ابتدا چک می کند آیا صفحه جزو صفحات خاص که نیاز به تعریف دسترسی ندارند می باشد یا خیر
    $res = $mysql->Execute("select * from SpecialPages where PageName='".$FileName."'");
    if(!($rec = $res->fetch()))
    {
    	// شماره امکانی که این صفحه به آن متصل است را بدست می آورد
    	$res = $mysql->Execute("select * from FacilityPages where PageName='".$FileName."'");
    	if($rec = $res->fetch())
    	{
    		// کنترل می کند آیا کاربر به امکان مربوطه دسترسی دارد یا خیر
    		$res = $mysql->Execute("select * from UserFacilities where UserID='".$_SESSION["UserID"]."' and FacilityID='".$rec["FacilityID"]."'");
    		if(!($rec = $res->fetch()))
    		{
    			echo "<p align=center><font color=red>شما مجوز فراخوانی این صفحه را ندارید</font></p>";
    			die();
    		}
    	}
    	else
    	{
		echo "<p align=center><font color=red>این صفحه در لیست صفحات سیستم تعریف نشده است $FileName</font></p>";
    		//die();
    	}
    }
?>