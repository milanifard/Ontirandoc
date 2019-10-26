<?php
	include "sys_config.class.php";
    include "definitions.php";
	include "DateUtils.inc";
	include "SharedClass.class.php";

	function HTMLBegin($bgcolor = '#C8DEF0')
    {
        echo "<!DOCTYPE html>\n<html>\n<head>\n" .

        "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n".
        "<link rel=\"stylesheet\"  href=\"css/login.css\" type=\"text/css\">\n";
        if (UI_LANGUAGE == "EN") {
            echo "<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css\" integrity=\"sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm\" crossorigin=\"anonymous\">\n".
                "<script src=\"https://code.jquery.com/jquery-3.2.1.slim.min.js\" integrity=\"sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN\" crossorigin=\"anonymous\"></script>\n".
                "<script src=\"https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js\" integrity=\"sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q\" crossorigin=\"anonymous\"></script>\n".
                "<script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js\" integrity=\"sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl\" crossorigin=\"anonymous\"></script>\n";
        }
        else if (UI_LANGUAGE == "FA")
        {
            echo "<link rel=\"stylesheet\"  href=\"https://cdn.rtlcss.com/bootstrap/v4.2.1/css/bootstrap.min.css\" integrity=\"sha384-vus3nQHTD+5mpDiZ4rkEPlnkcyTP+49BhJ4wJeJunw06ZAp+wzzeBPUXr42fi8If\"  crossorigin=\"anonymous\">\n" .
            "<script src=\"https://code.jquery.com/jquery-3.2.1.slim.min.js\" integrity=\"sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN\" crossorigin=\"anonymous\"></script>\n".
            "<script src=\"https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js\" integrity=\"sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q\" crossorigin=\"anonymous\"></script>\n".
            "<script  src=\"https://cdn.rtlcss.com/bootstrap/v4.2.1/js/bootstrap.min.js\" integrity=\"sha384-a9xOd0rz8w0J8zqj1qJic7GPFfyMfoiuDjC9rqXlVOcGO/dmRqzMn34gZYDTel8k\" crossorigin=\"anonymous\"></script>\n";
        }
        echo "<link rel=\"stylesheet\" href=\"https://use.fontawesome.com/releases/v5.7.0/css/all.css\" integrity=\"sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ\" crossorigin=\"anonymous\">";
        echo "\n</head>\n";
        echo "<body ";
        if(UI_LANGUAGE=="FA")
            echo " dir = 'rtl' ";
        echo ">";
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