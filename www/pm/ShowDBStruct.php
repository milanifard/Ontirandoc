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
$DMServersID = $_REQUEST["DMServersID"];
$DMDatabasesID = $_REQUEST["DMDatabasesID"];
$mysql = pdodb::getInstance();
?>
<table class="table table-bordered">
<tr class="warning" align="center">
<td>
	<input class="btn-primary" type=button value=<? echo C_RETURN ?> onclick='javascript: document.mf.submit()'>
</td>
</tr>
<?
$mysql->Prepare("select * from projectmanagement.DMTables where DMDatabasesID=? and DeleteFlag='NO'");
$res = $mysql->ExecuteStatement(array($DMDatabasesID));
while($rec = $res->fetch())
{
  echo "<tr class=\"warning\" align=\"center\">";
  echo "<td>";
  echo "<br>";
  echo "<table width=80%  class=\"warning\" align=\"center\">";
  echo "<tr class=\"warning\" align=\"center\">";
  echo "<td colspan=3>";
    echo $rec["TableName"];
    if($rec["description"]!="")
      echo " : ".$rec["description"];
    if($rec["SchemaComment"]!="")
      echo " [".$rec["SchemaComment"]."]";
  echo "</td>";
  echo "</tr>";
  $mysql->Prepare("select * from projectmanagement.DMFields where DMTablesID=? and DeleteFlag='NO'");
  $fres = $mysql->ExecuteStatement(array($rec["DMTablesID"]));
  while($frec = $fres->fetch())
  {
    echo "<tr>";
    echo "<td width=20% dir=ltr>".$frec["FieldName"]."</td>";
    echo "<td width=50%>".$frec["FieldDescription"]."</td>";
    echo "<td width=30%>".$frec["FieldSchemaComment"]."</td>";
    echo "</tr>";
  }
  echo "</table>";
  echo "</tr class=\"warning\" align=\"center\">";
}
  
?>
<tr class="warning" align="center">
<td>
	<input class="btn-primary" type=button value=<? echo C_RETURN ?> onclick='javascript: document.mf.submit()'>
</td>
</tr>
</table>
</form>
	<form class="table table-bordered" method=post id=mf name=mf action='ManageDMDatabases.php'>
	<input class="btn-primary" type=hidden name='DMServersID' id='DMServersID' value='<? echo $_REQUEST["DMServersID"] ?>'>
	</form>
</html>
