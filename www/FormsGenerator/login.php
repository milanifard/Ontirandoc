<?php
include_once 'sys_config.class.php';

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
$Message = "";


$mysql = pdodb::getInstance();
if(isset($_REQUEST["logout"]))
{
	session_unset();
}
if(isset($_REQUEST["UserID"]))
{
	//$mysql->Prepare("select * from formsgenerator.TemporaryUsers where WebUserID=? and WebPassword=?");
	//$res = $mysql->ExecuteStatement(array($_REQUEST["UserID"], $_REQUEST["Passwd"]));
	$mysql->Prepare("select * from formsgenerator.TemporaryUsers where WebUserID=?");
	$res = $mysql->ExecuteStatement(array($_REQUEST["UserID"]));
	if($rec = $res->fetch())
	{
		if($rec["UserStatus"]!="ENABLE")
		{
			$Message = "کاربر غیر فعال است";
		}
		else
		{
			$mysql->Prepare("select * from formsgenerator.TemporaryUsersAccessForms where WebUserID=? and filled='NO'");
			$res = $mysql->ExecuteStatement(array($_REQUEST["UserID"]));
			if($rec = $res->fetch())
			{
				session_start();
				$_SESSION = array();
				$_SESSION["UserID"] = $rec["WebUserID"];
				$u = new EducUser();
				$u->PersonID = 0;
				$_SESSION['User'] = $u; // در سیستم استفاده پرسشنامه استفاده نمی شود
				$_SESSION["PersonID"] = 0;
				$_SESSION["SystemCode"] = 0;
				
				echo "<script>document.location='FillQuestionnaire.php?SelectedFormStructID=".$rec["FormsStructID"]."';</script>";
				die();
			}
			else
			{
				$Message = "قبلا پرسشنامه را پر کرده اید";
			}
		}
	}
	else
	{
		$Message = "چنین کاربری وجود ندارد یا رمز عبور نادرست وارد شده است";
	}
}
?>
<html>
<head>
<meta http-equiv="Content-Language" content="fa">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="expires" content="now">
<meta http-equiv="pragma" content="no-cache">
<title> دانشگاه فردوسی مشهد  -ثبت پرسشنامه  </title>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body onload="LoginForm.UserID.focus()" class="bgmain" topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
<center>
<form name= "LoginForm" method="POST" onSubmit="return ChkForm(this);" >
<input name="UserPassword" type="hidden" value="" >
<input type='hidden' id='pswdStatus' name='pswdStatus' >
<div style='width:430px;text-align:center;' ><p dir="rtl"><font face="Tahoma" size="3" color="red"><?php echo $Message; ?></font> 
</p>
</div>
<br>
<br>
<form class="loginform">
<table style="width: 551px; height: 286px; dir: rtl;" background='images/back_pic.jpg'>
	<tr>
		<td height=60%>
		&nbsp;
		</td>
	</tr>
	<tr>
		<td>
			<table width=100% border=0 dir=rtl>
				<tr>
					<td>
					&nbsp;&nbsp;<label><b><font color=white>شناسه کاربری </font></b></label>
					<input type="text" name="UserID" id="UserID"  maxlength="20" dir="ltr">
					<input type="submit" style="background-image: url(images/btn_pic.png); width: 122px; height: 37px" value=""/></td>					
				</tr>
				<!-- 
				<tr>
					<td><input type="password" name="Passwd" id="Passwd"  maxlength="20" dir="ltr" ></td>
					<td><label>کلمه عبور </label></td>					
				</tr>
				 -->
			</table>
		</td>
	</tr>
	<tr>
		<td align=center>
		
		</td>
	</tr>
</table>
</form>
<br>
</center>
</body>
</html>
