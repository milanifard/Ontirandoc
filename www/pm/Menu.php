<?php
	include "header.inc.php";
	include "PAS_shared_utils.php";
    HTMLBegin();
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

<table class="table table-sm col-md-11 mx-auto">
<tr>
<td>
<?php
if(UI_LANGUAGE=="FA")
    echo PASUtils::FarsiDayName(date("l"))." ".$CurYear."/".$mm."/".$dd;
else
    echo date("F j, Y, g:i a");
?>
    <br>
</td>
</tr>
<tr><td><? echo C_ACTIVE_USER; ?><b><?php  echo $_SESSION["UserName"] ?></b></td></tr>
</table>
<table style='cursor:pointer' border=0 cellpadding=0 cellspacing=0 width=185>
<tr>
<td valign=bottom height='32' >
	<table class="table table-sm">
	<thead>
        <tr>
		<th width='85%' align='center'>
		<b><? echo C_MAIN_MENU ?></b>
		</th>
        </tr>
	</thead>
	</table>
</td>
</tr>
</table>
<table border=0 cellpadding=0 cellspacing=0 width='185' class="navbar_sub" <?php if(UI_LANGUAGE=="FA") echo "dir=rtl"; ?> height="15">
<tr>
	<td width='83%' height='18'><a href='#' onclick='javascript: parent.document.getElementById("MainContent").src="HomePage.php"'><? echo C_FIRST_PAGE ?></a></td>
</tr>
<tr>
	<td width='83%' height='18'><a href='#' onclick='javascript: parent.document.getElementById("MainContent").src="ChangePassword.php"'><? echo C_CHANGE_PASSWORD ?></a></td>
</tr>
<tr>
	<td width='83%' height='18'><a href='#' onclick='javascript: parent.document.getElementById("MainContent").src="MyActions.php"'><? echo C_MY_ACTIONS ?></a></td>
</tr>
<tr>
	<td width='83%' height='18'><a href='javascript: parent.document.location="SignOut.php?logout=1"'><? echo C_EXIT ?></a></td>
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
		<td <? if(UI_LANGUAGE=="FA") echo "align=right"; ?>
		<a href='#' onclick='javascript: ExpandOrColapse("tr_<?php echo $grec["GroupID"] ?>")'>
		<b><?php if(UI_LANGUAGE=="FA") echo $grec["GroupName"]; else echo $grec["EGroupName"]; ?></b>
		</a>
		</td>
	</tr>
	</table>
</td>
</tr>
<tr id=tr_<?php echo $grec["GroupID"] ?> style="display: none">
<td>
	<table border=0 cellpadding=0 cellspacing=0 width='185' class="navbar_sub" <? if(UI_LANGUAGE=="FA") echo "dir=rtl"; ?> height="15">
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
                if(UI_LANGUAGE=="FA")
                    echo $rec["FacilityName"];
                else
                    echo $rec["EFacilityName"];
                echo "</a></td>";
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
</html>
<head>
