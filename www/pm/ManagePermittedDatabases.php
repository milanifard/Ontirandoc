<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : پایگا های داده
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-1-26
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/DMDatabases.class.php");
include("classes/DMDatabasesManagers.class.php");
include ("classes/DM_Servers.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"])) 
{
	$Item_DBName=$_REQUEST["Item_DBName"];
	$Item_DBDescription=$_REQUEST["Item_DBDescription"];
	manage_DMDatabases::Update($_REQUEST["UpdateID"] 
			, $Item_DBName
			, $Item_DBDescription
			);
	echo SharedClass::CreateMessageBox(C_INFORMATION_SAVED);
}
$LoadDataJavascriptCode = '';
$DBDescription = "";
$Item_DBName = "";
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_DMDatabases();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_DBName.value='".htmlentities($obj->DBName, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$DBDescription = htmlentities($obj->DBDescription, ENT_QUOTES, 'UTF-8'); 
	$Item_DBName = htmlentities($obj->DBName, ENT_QUOTES, 'UTF-8');
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
//echo manage_DM_Servers::ShowSummary($_REQUEST["DMServersID"]);
//echo manage_DM_Servers::ShowTabs($_REQUEST["DMServersID"], "ManageDMDatabases");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center"><? echo C_DATABASE_DOC ?></td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr id="tr_DM_ServersID" name="tr_DM_ServersID" style='display:'>
	<td width="1%" nowrap>
 
	</td>
	<td nowrap>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 <? echo C_NAME ?>
	</td>
	<td nowrap>
	<input type="hidden" name="Item_DBName" id="Item_DBName" maxlength="45" size="40">
	<? echo $Item_DBName; ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 <? echo C_DESCRIPTION ?>
	</td>
	<td nowrap>
	<textarea name="Item_DBDescription" id="Item_DBDescription" cols="80" rows="5"><? echo $DBDescription; ?></textarea>
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="<? echo C_SAVE ?>">
</td>
</tr>
</table>
<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
<? } ?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_DMServersID" name="Item_DMServersID" value="<? echo htmlentities($_REQUEST["DMServersID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="6">
	<? echo C_DATABASES ?>
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"><? echo C_ROW ?></td>
	<td width="2%"><? echo C_EDIT ?></td>
	<td><? echo C_SERVER ?></td>
	<td><? echo C_NAME ?></td>
	<td><? echo C_DESCRIPTION ?></td>
	<td><? echo C_TABLES ?></td>
</tr>
<?
$res = manage_DMDatabases::GetPermitted($_SESSION["PersonID"]); 
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManagePermittedDatabases.php?UpdateID=".$res[$k]->DMDatabasesID."&DMServersID=".$_REQUEST["DMServersID"]."\"><img src='images/edit.gif' title='".C_EDIT."'></a></td>";
	echo "	<td>".htmlentities($res[$k]->ServerName, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".htmlentities($res[$k]->DBName, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".str_replace("\r", "<br>", htmlentities($res[$k]->DBDescription, ENT_QUOTES, 'UTF-8'))."</td>";
	echo "<td><a href='ManageDMTables.php?DMDatabasesID=".$res[$k]->DMDatabasesID."'>".C_TABLES."</a></td>";
	echo "</tr>";
}
?>
</table>
</form>
</html>
