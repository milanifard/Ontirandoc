<?php
	include "sys_config.class.php";
	session_start();
	function getRealIpAddr()
	{
	    $ip = 0;
	    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
	    {
	      $ip=$_SERVER['HTTP_CLIENT_IP'];
	    }
	    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
	    {
	      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	    }
	    else
	    {
	      $ip=$_SERVER['REMOTE_ADDR'];
	    }
	    return $ip;
	}	

	$message = "";
	if(isset($_REQUEST["UserID"]))
	{
		$mysql = pdodb::getInstance();
		$mysql->Prepare("select * from projectmanagement.AccountSpecs 
				JOIN projectmanagement.persons on (persons.PersonID=AccountSpecs.PersonID) 
				where UserID=? and UserPassword=sha1(md5(?))");
		$res = $mysql->ExecuteStatement(array($_REQUEST["UserID"], $_REQUEST["UserPassword"]), PDO::FETCH_ASSOC, true);
		if($trec = $res->fetch())
		{
			$_SESSION["UserID"] = $trec["UserID"];
			$_SESSION["SystemCode"] = 1;
			$_SESSION["PersonID"] = $trec["PersonID"];
			$_SESSION["UserName"] = $trec["pfname"]." ".$trec["plname"];
			$_SESSION["LIPAddress"] = ip2long(getRealIpAddr());
            if($_SESSION["LIPAddress"]=="")
                $_SESSION["LIPAddress"] = "0";
			if(isset($_REQUEST["Referer"]))
			  echo "<script>document.location='".$_REQUEST["Referer"]."';</script>";
			else
			  echo "<script>document.location='main.php';</script>";
			die();
		}
		else
			$message = "<font color=red>نام کاربر یا کلمه عبور نادرست است</font>";
		//echo $_REQUEST["UserName"];
	}
	if(isset($_SESSION["UserID"]))
	{
	    echo "<script>document.location='main.php';</script>";
	    die();
	}
	/*
	if(strpos($_SERVER["HTTP_REFERER"], "http://pm.falinoos.com/pm/")!== false)
	{
	  echo $_SERVER["HTTP_REFERER"];
	}
	*/
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css" > INPUT, SELECT { font-family: Tahoma }'.
</style>
<link rel="stylesheet"  href="../template/css/login.css" type="text/css">
</head>
<body  dir=rtl link="#0000FF" alink="#0000FF" vlink="#0000FF">
<br>
<p align=center>
<?php echo $message; ?>
</p>
<form method=post>
<?php if(isset($_REQUEST["Referer"])) { ?>
<input type=hidden name=Referer id=Referer value='<?php echo $_REQUEST["Referer"]; ?>'>
<? } ?>
<p align=center><font face=tahoma size=1>سامانه توسعه هستان نگار</p>
<table with=80% align=center border=1 celspacing=0>
<tr>
	<td>
	<img src='images/login.gif'>
	</td>
</tr>
<tr>
	<td>
	<table width=100% border=0>
		<tr>
			<td>نام کاربری:</td>
			<td><input type=text name=UserID id=UserID></td>
		</tr>
		<tr>
			<td>کلمه رمز:</td>
			<td><input type=password id=UserPassword name=UserPassword></td>
		</tr>
		<tr class=FooterOfTable>
			<td colspan=2 align=center>
			<input type=submit value='ورود'>
			</td>
		</tr>
	</table>
	</td>
</tr>
</table>
<br>
<br>
<br>
<table border=0 align=center>
<tr>
  <td>
  <font face=tahoma size=1>
  اگر به عنوان خبره قصد ارزیابی یک هستان نگار را دارید و کد ورود در اختیار شما قرار گرفته است لطفا 
  <a href='ValidateOntology.php'><b>اینجا</b></a>
  را کلیک کنید
  </font>
  </td>
</tr>
</table>
</form>
</body>
