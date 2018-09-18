<?php
	include "header.inc.php";
	include "PAS_shared_utils.php";
	$now = date("Ymd"); 
	$yy = substr($now,0,4); 
	$mm = substr($now,4,2); 
	$dd = substr($now,6,2);
	$CurrentDay = $yy."/".$mm."/".$dd;
	list($dd,$mm,$yy) = ConvertX2SDate($dd,$mm,$yy);
	$yy = substr($yy, 2, 2);
	$CurYear = 1300+$yy;	
	$mysql = pdodb::getInstance();
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css" > INPUT, SELECT { font-family: Tahoma }'.
</style>
<link rel="stylesheet"  href="css/right.css" type="text/css">
</head>
<body  dir=rtl link="#0000FF" alink="#0000FF" vlink="#0000FF">
<table cellspacing=0 cellpadding=3 >
<tr>
<td>
<?php echo PASUtils::FarsiDayName(date("l"))." ".$CurYear."/".$mm."/".$dd."<br>" ?>
</td>
</tr>
<tr><td>کاربر فعال: <b><?php  echo $_SESSION["UserName"] ?></b></td></tr>
</table>
<table style='cursor:pointer' border=0 cellpadding=0 cellspacing=0 width=185>
<tr>
<td valign=bottom height='32' background='images/title_header_main.gif'>
	<table width=100% border=0 cellpadding=0 cellspacing=0 class="navbar_main" height="25">	
	<tr>
		<td width='85%' align='center'>
		<font color="#FFFFFF"><b>منوهای اصلی</b></font>
		</td>
		<td  width='20%'>&nbsp;</td>
	</tr>
	</table>
</td>
</tr>
</table>
<table border=0 cellpadding=0 cellspacing=0 width='185' class="navbar_sub" dir="rtl" height="15">
<tr>
	<td width='83%' height='18'><a href='#' onclick='javascript: parent.document.getElementById("MainContent").src="HomePage.php"'>صفحه اول</a></td>
</tr>
<tr>
	<td width='83%' height='18'><a href='#' onclick='javascript: parent.document.getElementById("MainContent").src="ChangePassword.php"'>تغییر رمز عبور</a></td>
</tr>
<tr>
	<td width='83%' height='18'><a href='#' onclick='javascript: parent.document.getElementById("MainContent").src="MyActions.php"'>کارهایی که انجام دادم</a></td>
</tr>
<tr>
	<td width='83%' height='18'><a href='javascript: parent.document.location="SignOut.php?logout=1"'>خروج</a></td>
</tr>
</table>
<?php 

	$gres = $mysql->Execute("select * from SystemFacilityGroups 
				  where GroupID in (select GroupID from SystemFacilities JOIN UserFacilities using (FacilityID) where UserID='".$_SESSION["UserID"]."') 
				  order by OrderNo");
	while($grec = $gres->fetch())
	{ 
 ?>
 <br>
<table style='cursor:pointer' border=0 cellpadding=0 cellspacing=0 width=185>
<tr>
<td valign=bottom height='25' background='images/title_header.gif'>
	<table width=100% border=0 cellpadding=0 cellspacing=0 class="navbar_main" height="25">	
	<tr>
		<td  width='25'>&nbsp;</td>
		<td align='right'>
		<a href='#' onclick='javascript: ExpandOrColapse("tr_<?php echo $grec["GroupID"] ?>")'>
		<b><?php echo $grec["GroupName"]; ?></b>
		</a>
		</td>
	</tr>
	</table>
</td>
</tr>
<tr id=tr_<?php echo $grec["GroupID"] ?> style="display: none">
<td>
	<table border=0 cellpadding=0 cellspacing=0 width='185' class="navbar_sub" dir="rtl" height="15">
		<?php 
			$res = $mysql->Execute("select * from SystemFacilities JOIN UserFacilities using (FacilityID) where UserID='".$_SESSION["UserID"]."' and GroupID=".$grec["GroupID"]." order by OrderNo");
			
			while($rec = $res->fetch())
			{
				echo "<tr>";
				echo "<td height='18'>";
				if(strpos($rec["PageAddress"], "http:")===0)
				  echo "<a href='".$rec["PageAddress"]."' target=_blank>";
				else
				  echo "<a href='#' onclick='javascript: parent.document.getElementById(\"MainContent\").src=\"".$rec["PageAddress"]."\"'>";
				echo "&nbsp;";
				echo $rec["FacilityName"]."</a></td>";
				echo "</tr>";
			}
		?>
	</table>
</td>
</tr>
</table>
<?php } ?>
<br>
<script>
	function ColapseAll()
	{
	  <?
	    $gres = $mysql->Execute("select * from SystemFacilityGroups where GroupID in (select GroupID from SystemFacilities JOIN UserFacilities using (FacilityID) where UserID='".$_SESSION["UserID"]."') order by OrderNo");
	    while($grec = $gres->fetch())
	    { 
	      echo "document.getElementById('tr_".$grec["GroupID"]."').style.display = 'none';\r\n";
	    }
	  ?>
	}
	
	function ExpandOrColapse(tr_id)
	{
	  ColapseAll();
		if(document.getElementById(tr_id).style.display=='')
			document.getElementById(tr_id).style.display = 'none';
		else
			document.getElementById(tr_id).style.display = '';
	}
</script>
</body>
