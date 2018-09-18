<?
  include("header.inc.php");
  HTMLBegin();
  $mysql = pdodb::getInstance();
  ini_set("display_errors", "On");
  ini_set("error_reporting", "22527");
  // با هربار فراخوانی جدول پیشنهادات بروز می شود
  function UpdateFKSuggests()
  {
    $mysql = pdodb::getInstance();
    $res = $mysql->Execute("select * from mis.MIS_TableFields where (FieldType like 'INT%' or FieldType like 'TINY%' or FieldType like 'SMALL%')
		      and KeyType='PRI' and AutoInc='YES' 
		      and FieldName<>'id' and FieldName<>'RecID' and FieldName<>'RowID' and FieldName<>'ItemID'
		      ");
    while($rec = $res->fetch())
    {
      $res2 = $mysql->Execute("select * from mis.MIS_TableFields where 
				FieldName='".$rec["FieldName"]."' and TableName<>'".$rec["TableName"]."'
				and (FieldType like 'INT%' or FieldType like 'TINY%' or FieldType like 'SMALL%')
				and (RelatedTable is null or RelatedTable='')");
      $i = 0;
      while($rec2 = $res2->fetch())
      {
	$sres = $mysql->Execute("select count(*) as tcount from mis.MIS_SuggestedFK where PKDBName='".$rec["DBName"]."' and PKTableName='".$rec["TableName"]."' and PKFieldName='".$rec["FieldName"]."' and FKDBName='".$rec2["DBName"]."' and FKTableName='".$rec2["TableName"]."' and FKFieldName='".$rec2["FieldName"]."'");
	$srec = $sres->fetch();
	if($srec["tcount"]==0)
	{
	  $mysql->Execute("insert into mis.MIS_SuggestedFK (PKDBName, FKDBName, PKTableName, PKFieldName, FKTableName, FKFieldName) values 
	  ('".$rec["DBName"]."', '".$rec2["DBName"]."', '".$rec["TableName"]."', '".$rec["FieldName"]."','".$rec2["TableName"]."', '".$rec2["FieldName"]."')");
	}
      }
    }
  }
  if(isset($_REQUEST["SuggestFK"]))
  {
    UpdateFKSuggests();
  }
  $SelectedDB = "";
  if(isset($_REQUEST["SelectedDB"]))
    $SelectedDB = $_REQUEST["SelectedDB"];
  echo "<table align=center dir=ltr width=98%>";
  echo "<tr><td colspan=3 dir=rtl>کلیدهای خارجی پیشنهادی که به جداول پایگاه داده:  ";
  echo "<select dir=ltr name=SelectedDB onchange='document.location=\"SuggestedForeignKeys.php?SelectedDB=\"+this.value;'>";
  $res = $mysql->Execute("select distinct DBName from mis.MIS_Tables order by DBName");
  while($rec = $res->fetch())
  {
    echo "<option value='".$rec["DBName"]."' ";;
    if($SelectedDB==$rec["DBName"]) echo "selected";
    echo ">".$rec["DBName"];
  }
  echo "</select>";
  echo " مرتبط می شوند ";  
  echo "</td></tr>";
  if($SelectedDB!="")
  {
    if(isset($_REQUEST["status"]))
    {
      $FKDBName = $_REQUEST["FKDBName"];
      $FKTableName = $_REQUEST["FKTableName"];
      $FKFieldName = $_REQUEST["FKFieldName"];
      $PKDBName = $_REQUEST["PKDBName"];
      $PKTableName = $_REQUEST["PKTableName"];
      $PKFieldName = $_REQUEST["PKFieldName"];
      if($_REQUEST["status"]=="ACCEPT")
	$status = "ACCEPT";
      else 
        $status = "REJECT";

      if($status=="ACCEPT")
      {
	$mysql->Prepare("update mis.MIS_TableFields set RelatedDBName=?, RelatedTable=?, RelatedField=?
							  where
							  DBName=? and TableName=? and FieldName=? ");
	$mysql->ExecuteStatement(array($PKDBName, $PKTableName, $PKFieldName, $FKDBName, $FKTableName, $FKFieldName));
      }
        
      $mysql->Prepare("update mis.MIS_SuggestedFK set status='".$status."' where
							FKDBName=? and FKTableName=? and FKFieldName=? and
							PKDBName=? and PKTableName=? and PKFieldName=?");
      $mysql->ExecuteStatement(array($FKDBName, $FKTableName, $FKFieldName, $PKDBName, $PKTableName, $PKFieldName));
    }
    $mysql->Prepare("select MIS_SuggestedFK.*, fk.description as FieldDesc, MIS_Tables.description as TableDesc
    from mis.MIS_SuggestedFK
    LEFT JOIN mis.MIS_TableFields fk on (FKDBName=fk.DBName and FKTableName=fk.TableName and FKFieldName=fk.FieldName)
    LEFT JOIN mis.MIS_Tables on (FKDBName=MIS_Tables.DBName and FKTableName=MIS_Tables.name) 
    where MIS_SuggestedFK.status='UNDECIDE' and PKDBName=? order by PKDBName, FKDBName");
    $res = $mysql->ExecuteStatement(array($SelectedDB));
    $i = 0;
    while($rec = $res->fetch())
    {
      $i++;
      $link = "SuggestedForeignKeys.php?SelectedDB=".$SelectedDB."&";;
      $link .= "FKDBName=".$rec["FKDBName"]."&";
      $link .= "FKTableName=".$rec["FKTableName"]."&";
      $link .= "FKFieldName=".$rec["FKFieldName"]."&";
      $link .= "PKDBName=".$rec["PKDBName"]."&";
      $link .= "PKTableName=".$rec["PKTableName"]."&";
      $link .= "PKFieldName=".$rec["PKFieldName"]."&";
      
      $RejectLink = $link."status=REJECT";
      $AcceptLink = $link."status=ACCEPT";
      
      echo "<tr>";
      echo "<td width=1%>".$i."</td>";
      echo "<td width=10% nowrap>";
      echo "<a href='".$AcceptLink."'>ACCEPT</a>";
      echo "&nbsp;&nbsp;";
      echo "<a href='".$RejectLink."'>REJECT</a>";
      echo "</td>";
      echo "<td>".$rec["FKDBName"].".".$rec["FKTableName"].".".$rec["FKFieldName"];
      echo " (".$rec["TableDesc"]." - ".$rec["FieldDesc"].")";
      echo " => ".$rec["PKDBName"].".".$rec["PKTableName"].".".$rec["PKFieldName"];
      echo "</td>";
      echo "</tr>";
    }
    if($_SESSION["UserID"]=="omid")
    {
      echo "<tr class=FooterOfTable>";
      echo "<td colspan=2 align=center><input type=button value='تولید مجدد پیشنهادات' onclick='document.location=\"SuggestedForeignKeys.php?SuggestFK=1\"'></td>";
      echo "</tr>";
    }
  }
  echo "</table>";
  
?>

</body>
</html>
