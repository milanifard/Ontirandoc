<?
	include("header.inc.php");
	HTMLBegin();
	
	function CheckTableToInsert($DB, $TableName, $Comment)
	{
	  $mysql = pdodb::getInstance();	
	  $query = "select * from mis.MIS_Tables where DBName=? and name='".$TableName."'";
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array($DB));
	  if($trec = $res->fetch())
	  {
	    $mysql->Prepare("update mis.MIS_Tables set status=1 where ServerName='".$ServerName."' and DBName=? and name='".$TableName."'");
	    $mysql->ExecuteStatement(array($DB));
	  }
	  else
	  {
	    echo $ServerName.".".$DB.".".$TableName."<br>";
	    $mysql->Prepare("insert into mis.MIS_Tables (ServerName, DBName, name, description, status) 
						values('".$ServerName."', ?, '".$TableName."', ?, '1')");
	    $mysql->ExecuteStatement(array($DB, $Comment));
	    $mysql->Prepare("insert into mis.MIS_TableChangeLog (DBName, TableName, ChangeType, ChangeDate) 
						values(?, '".$TableName."', 'ADD', now())");
	    $mysql->ExecuteStatement(array($DB));
	  }
	}

	function CheckTableToRemove($DB, $TableName)
	{
	  $mysql = pdodb::getInstance();	
	  $query = "select count(*) as tcount from information_schema.TABLES where TABLE_TYPE='BASE TABLE' and TABLE_SCHEMA=? and TABLE_NAME='".$TableName."'";
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array($DB));
	  $trec = $res->fetch();
	  if($trec["tcount"]==0)
	  {
	    echo "<font color=red>Remove Table: ".$DB.".".$TableName."</font><br>";
	    $mysql->Prepare("delete from mis.MIS_Tables where ServerName='".$ServerName."' and DBName=? and name='".$TableName."'");
	    $mysql->ExecuteStatement(array($DB));

	    $mysql->Prepare("insert into mis.MIS_TableChangeLog (DBName, TableName, ChangeType, ChangeDate) 
						values(?, '".$TableName."', 'REMOVE', now())");
	    $mysql->ExecuteStatement(array($DB));
	    
	    $mysql->Prepare("delete from mis.MIS_TableFields where DBName=? and TableName='".$TableName."'");
	    $mysql->ExecuteStatement(array($DB));
	  }
	}
	
	function CheckTableFieldsToInsert($DB, $TableName)
	{
	  $mysql = pdodb::getInstance();	
	  $mysql->Prepare("select * from information_schema.COLUMNS where TABLE_SCHEMA=? and TABLE_NAME='".$TableName."'");
	  $res = $mysql->ExecuteStatement(array($DB));

	  while($frec = $res->fetch())
	  {
	    $FieldName = $frec["COLUMN_NAME"];
	    $FieldType = str_replace("'", "", $frec["COLUMN_TYPE"]);
	    $KeyType = $frec["COLUMN_KEY"];
	    $Comment = $frec["COLUMN_COMMENT"];
	    $Extra = $frec["EXTRA"];
	    $AutoInc = "NO";
	    if($Extra=="auto_increment")
	      $AutoInc = "YES";
	    
	    $mysql->Prepare("select * from mis.MIS_TableFields where DBName=? and TableName='".$TableName."' and FieldName='".$FieldName."'");
	    $temp = $mysql->ExecuteStatement(array($DB));
	    if($tmrec = $temp->fetch())
	    {
	      $query = "update mis.MIS_TableFields set status='1', ValuesCount=-1, FieldType='".$FieldType."', KeyType='".$KeyType."', AutoInc='".$AutoInc."'  where id='".$tmrec["id"]."' ";
	      $mysql->Execute($query);
	      
	      if($FieldType!=$tmrec["FieldType"])
	      {
		$mysql->Prepare("insert into mis.MIS_TableFieldsChangeLog (DBName, TableName, FieldName, ChangeType, ChangeDate) 
						    values(?, '".$TableName."', '".$FieldName."', 'UPDATE', now())");
		$mysql->ExecuteStatement(array($DB));
	      }
	    }
	    else
	    {
	      $query = "insert into mis.MIS_TableFields (DBName, TableName, FieldName, FieldType, KeyType, status, description, AutoInc) 
	      values(?, '".$TableName."', '".$FieldName."', '".$FieldType."', '".$KeyType."', '1', ?, '".$AutoInc."')";
	      $mysql->Prepare($query);
	      $mysql->ExecuteStatement(array($DB, $Comment));
	      
	      $mysql->Prepare("insert into mis.MIS_TableFieldsChangeLog (DBName, TableName, FieldName, ChangeType, ChangeDate) 
						  values(?, '".$TableName."', '".$FieldName."', 'ADD', now())");
	      $mysql->ExecuteStatement(array($DB));
	      
	      echo $ServerName.".".$DB.".".$TableName.".<font color=green>".$FieldName."</font><br>";
	    }
	  }
	}
	
	function CheckTableFieldsToRemove($DB)
	{
	  $mysql = pdodb::getInstance();	
	  $mysql->Prepare("select * from mis.MIS_TableFields where DBName=?");
	  $res = $mysql->ExecuteStatement(array($DB));

	  while($frec = $res->fetch())
	  {
	    $FieldName = $frec["FieldName"];
	    $TableName = $frec["TableName"];
	    $mysql->Prepare("select count(*) as tcount from information_schema.COLUMNS where TABLE_SCHEMA=? and TABLE_NAME='".$TableName."' and COLUMN_NAME='".$FieldName."'");
	    $temp = $mysql->ExecuteStatement(array($DB));
	    $tmrec = $temp->fetch();
	    if($tmrec["tcount"]==0)
	    {
	      $query = "delete from mis.MIS_TableFields where DBName=? and TableName='".$TableName."' and FieldName='".$FieldName."' ";
	      $mysql->Prepare($query);
	      $mysql->ExecuteStatement(array($DB));
	      
	      $mysql->Prepare("insert into mis.MIS_TableFieldsChangeLog (DBName, TableName, FieldName, ChangeType, ChangeDate) 
						  values(?, '".$TableName."', '".$FieldName."', 'REMOVE', now())");
	      $mysql->ExecuteStatement(array($DB));
	      
	      echo "<font color=red>Remove Field: ".$DB.".".$TableName.".".$FieldName."</font><br>";
	    }
	  }	
	 }
	
	
	$mysql = pdodb::getInstance();
	$Permitted = false;
	if(isset($_REQUEST["Confirm"]) || isset($_REQUEST["AllDBs"]))
	{
	  $ServerName = "educ";	
	  $dblist = $mysql->Execute("Show databases;");
	  while($drec = $dblist->fetch())
	  {
	    $DB = $drec["Database"];
	 //   echo $DB."<br>";
	    if(isset($_REQUEST["ch_".$DB]) || isset($_REQUEST["AllDBs"]))
	    {
		$mysql->Prepare("update mis.MIS_Tables set status=0 where DBName=?");
		$mysql->ExecuteStatement(array($DB));
		$mysql->Prepare("update mis.MIS_TableFields set status=0 where DBName=?");
		$mysql->ExecuteStatement(array($DB));


		$mysql->Prepare("select * from information_schema.TABLES where TABLE_TYPE='BASE TABLE' and TABLE_SCHEMA=?");
		$TablesList = $mysql->ExecuteStatement(array($DB));
		
		while($rec = $TablesList->fetch())
		{
		  $TableName = $rec["TABLE_NAME"];
		  $Comment = $rec["TABLE_COMMENT"];
		  CheckTableToInsert($DB, $TableName, $Comment);
		  CheckTableFieldsToInsert($DB, $TableName);
		}

		$mysql->Prepare("select * from mis.MIS_Tables where DBName=?");
		$TablesList = $mysql->ExecuteStatement(array($DB));
		while($rec = $TablesList->fetch())
		{
		  $TableName = $rec["name"];
		  CheckTableToRemove($DB, $TableName);
		}
		CheckTableFieldsToRemove($DB);
		echo "<p align=center><font color=green face=tahoma>انتقال ساختار جداول  ".$DB." به سیستم با موفقیت انجام گرفت</font></p>";
	    }
	  }
	  
	  
	}
	else
	{
	  echo "<form method=post>";
	  echo "<input type=hidden name=Confirm value=1>";

	  $res = $mysql->Execute("Show databases;");
	  $i=0;
	  $TotalRowsCount = 0;
	  while($rec = $res->fetch())
	  {
	    if($i%3==0)
	    {
	      if($i>0)
		$DBList .= "</tr>";
	      $DBList .= "<tr>";
	    }
	    $i++;
	    $DBList .= "<td>";
	    $DBList .= $i;
	    $DBList .= "</td>";
	    $DBList .= "<td>";
	    $DBList .= "<input type=checkbox name='ch_".$rec["Database"]."' id='ch_".$rec["Database"]."'>".$rec["Database"];
	    //$DBList .= "</td>";
	    $query = "select count(TABLE_TYPE) as tcount from information_schema.TABLES where TABLE_TYPE='BASE TABLE' and TABLE_SCHEMA='".$rec["Database"]."'";
	    $res2 = $mysql->Execute($query);
	    $rec2 = $res2->fetch();
	    $DBList .= " (".$rec2["tcount"].")</td>";
	    $TotalRowsCount += $rec2["tcount"];
	  }
	  echo "<table>";
	  echo "<tr><td>لطفا پایگاه داده های مورد نظر را برای انتقال فراداده اولیه انتخاب کنید: </td></tr>";
	  
	  echo "<TR><td><table dir=ltr border=1 cellspacing=0 cellpadding=5>";
	  echo "<tr class=HeaderOfTable>";
	  echo "<td>ردیف</td><td>(تعداد جداول)پایگاه داده</td>";
	  echo "<td>ردیف</td><td> (تعداد جداول)پایگاه داده</td>";
	  echo "<td>ردیف</td><td> (تعداد جداول)پایگاه داده</td>";
	  echo "</tr>";
	  echo $DBList."</table></td></tr>";
	  echo "<tr bgcolor=#cccccc><td><b>مجموع جداول: ".$TotalRowsCount;
	  echo "</td>";
	  echo "</tr>";
	  echo "<tr class=FooterOfTable><td colspan=2 align=center><input type=submit value='انتقال'></td></tr>";
	  echo "</table>";
	  echo "</form>";
	}
?></body></html>