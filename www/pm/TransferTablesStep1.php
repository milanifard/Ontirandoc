<?php
	include("header.inc.php");
	include_once("classes/DM_Servers.class.php");
	HTMLBegin();
	$res = manage_DM_Servers::GetList(); 
	$options = "<option value='0'>-";
	if(isset($_REQUEST["DMServersID"]))
	  $SelectID = $_REQUEST["DMServersID"];
	for($k=0; $k<count($res); $k++)
	{
	  $options .= "<option value='".$res[$k]->DMServersID."' ";
	  if($SelectID==$res[$k]->DMServersID)
	    $options .= " selected ";
	  $options .= ">".$res[$k]->ServerName;
	}
?>
  <!---
	<br>
	<form method=post id=mf name=mf>
	<table dir=ltr width=50% align=center border=1 cellspacing=0>
	<tr>
	<td colspan=2 class=HeaderOfTable>Server: 
	<select name='DMServersID' id='DMServersID' onchange='document.mf.submit();'><?php echo $options; ?></select>
	</td>
	</tr>
	</form>
  --->
<?php
	function InsertDBIfNotExist($server, $db)
	{
	  $mysql = pdodb::getInstance();
	  $mysql->Prepare("select * from projectmanagement.DMDatabases where DBName=? and DM_ServersID=? and DeleteFlag='NO'");
	  $res = $mysql->ExecuteStatement(array($db, $server));
	  if(!($rec = $res->fetch()))
	  {
	    $mysql->Prepare("insert into projectmanagement.DMDatabases (DM_ServersID, DBName) values (?, ?)");
	    $mysql->ExecuteStatement(array($server, $db));
	    return true;
	  }
	  return false;
	}

	function InsertTableIfNotExist($DBID, $table, $TableComment)
	{
	  $mysql = pdodb::getInstance();
	  $mysql->Prepare("select * from projectmanagement.DMTables where DMDatabasesID=? and TableName=? and DeleteFlag='NO'");
	  $res = $mysql->ExecuteStatement(array($DBID, $table));
	  if(!($rec = $res->fetch()))
	  {
	    $mysql->Prepare("insert into projectmanagement.DMTables (DMDatabasesID, TableName, SchemaComment) values (?, ?, ?)");
	    $mysql->ExecuteStatement(array($DBID, $table, $TableComment));
	    return true;
	  }
	  return false;
	}

	function InsertFieldIfNotExist($TableID, $field, $FieldComment, $FieldType)
	{
	  $mysql = pdodb::getInstance();
	  $mysql->Prepare("select * from projectmanagement.DMFields where DMTablesID=? and FieldName=? and DeleteFlag='NO'");
	  $res = $mysql->ExecuteStatement(array($TableID, $field));
	  if(!($rec = $res->fetch()))
	  {
	    $mysql->Prepare("insert into projectmanagement.DMFields (DMTablesID, FieldName, FieldSchemaComment, FieldType) values (?, ?, ?, ?)");
	    $mysql->ExecuteStatement(array($TableID, $field, $FieldComment, $FieldType));
	    return true;
	  }
	  return false;
	}
	
	function GetDBId($server, $db)
	{
	  $mysql = pdodb::getInstance();
	  $mysql->Prepare("select * from projectmanagement.DMDatabases where DBName=? and DM_ServersID=? and DeleteFlag='NO'");
	  
	  $res = $mysql->ExecuteStatement(array($db, $server));
	  if($rec = $res->fetch())
	  {
	    return $rec["DMDatabasesID"];
	  }
	  return 0;
	}

	function GetTableId($DBId, $TableName)
	{
	  $mysql = pdodb::getInstance();
	  $mysql->Prepare("select * from projectmanagement.DMTables where DMDatabasesId=? and TableName=? and DeleteFlag='NO'");
	  
	  $res = $mysql->ExecuteStatement(array($DBId, $TableName));
	  if($rec = $res->fetch())
	  {
	    return $rec["DMTablesID"];
	  }
	  return 0;
	}
	
	function ImportAllTablesOfDB($mysql, $ServerID, $DBName)
	{
	    $DBId = GetDBId($ServerID, $DBName);
	    $query = "select * from information_schema.TABLES where TABLE_SCHEMA=?";
	    $mysql->Prepare($query);
	    $res = $mysql->ExecuteStatement(array($DBName));
	    echo "<table dir=ltr width=50% align=center border=1 cellspacing=0>";
	    echo "<tr><td colspan=2 class=HeaderOfTable>Database: <b>".$DBName." (".$DBId.")</b></td></tr>";
	    while($rec = $res->fetch())
	    {
	      echo "<tr><td>".$rec["TABLE_NAME"]."</td><td>";
	      if(InsertTableIfNotExist($DBId, $rec["TABLE_NAME"], $rec["TABLE_COMMENT"]))
		echo "<font color=red>Inserted</font>";
	      else 
		echo "Exists";
	      echo "<br>";
	      $TableID = GetTableId($DBId, $rec["TABLE_NAME"]);
	      $query = "select * from information_schema.COLUMNS where TABLE_SCHEMA=? and TABLE_NAME=?";
	      $mysql->Prepare($query);
	      $fres = $mysql->ExecuteStatement(array($DBName, $rec["TABLE_NAME"]));
	      while($frec = $fres->fetch())
	      {
		  echo $frec["COLUMN_NAME"].":";
		  if(InsertFieldIfNotExist($TableID, $frec["COLUMN_NAME"], $frec["COLUMN_COMMENT"], $frec["COLUMN_TYPE"]))
		    echo "<font color=red>Inserted</font>";
		  else
		    echo "Exists";
		  echo "<br>";
	      }
	      echo "</td></tr>";
	    }
	    echo "</table>";
	}
	
	if(isset($_REQUEST["DMServersID"]) && $_REQUEST["DMServersID"]!="0")
	{
	  $obj = new be_DM_Servers();
	  $obj->LoadDataFromDatabase($_REQUEST["DMServersID"]); 

	  $username = $obj->UserName;
	  $password = $obj->UserPassword;
	  $address = $obj->address;
	  $ServerID = $obj->DMServersID;
	  $mysql = pdodb::getInstance($address, $username, $password, "information_schema");
	  
	  $res = $mysql->Execute("select SCHEMA_NAME from information_schema.SCHEMATA");
	  echo "<table dir=ltr width=50% align=center border=1 cellspacing=0>";
	  echo "<tr><td colspan=2 class=HeaderOfTable>Import Database Schema: </td></tr>";
	  while($rec = $res->fetch())
	  {
	    if($rec["SCHEMA_NAME"]!="phpmyadmin" && $rec["SCHEMA_NAME"]!="performance_schema" && $rec["SCHEMA_NAME"]!="information_schema" && $rec["SCHEMA_NAME"]!="mysql" && $rec["SCHEMA_NAME"]!="lost+found" && $rec["SCHEMA_NAME"]!="phpmyadmin")
	    {
	      echo "<tr><td>".$rec["SCHEMA_NAME"]."</td><td>";
	      if(InsertDBIfNotExist($ServerID, $rec["SCHEMA_NAME"]))
		echo "<font color=red>Inserted</font>";
	      else 
		echo "Exists";
	      ImportAllTablesOfDB($mysql, $ServerID, $rec["SCHEMA_NAME"]);
	      echo "</td></tr>";
	    }
	  }
	  echo "<tr class=FooterOfTable><td align=center colspan=2>";
	  echo "<input type=button value='بازگشت' onclick='javascript: location=\"ManageDMDatabases.php?DMServersID=";
	  echo $_REQUEST["DMServersID"]."\"'>";
	  echo "</td></tr>";
	  echo "</table>";	  
?>
  <form method=post name=sf id=sf>
  <input type=hidden id='DMServersID' name='DMServersID' value='<?php if(isset($_REQUEST["DMServersID"])) echo $_REQUEST["DMServersID"]; ?>'>
  <input type=hidden id='DBName' name='DBName' value=''>
  </form>
  <script>
    function ShowTables(DBName)
    {
      document.getElementById('DBName').value=DBName;
      document.getElementById('sf').submit();
    }
  </script>
<?php
	
	}
?>
</body></html>