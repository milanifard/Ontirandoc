<?
  $NotAddSlashes = "1";
  include("header.inc.php");
  $thereshold = 80;
  $DBRange = "'hrmstotal', 'educ', 'research', 'nutrition', 'accountancy', 'baseinfo', 'ease', 'framework', 'nazar', 'library', 'sanjesh', 'pas', 'photo', 'studentcultural'";
  
  function GetSimilarityPercent($st1, $st2)
  {
    $distance = levenshtein($st1, $st2);
    $p = (1-($distance/max(strlen($st1), strlen($st2))) )*100;
      return $p;
  }
  
  function ShowRow($prefix, $yy, $mm, $dd, $rec, $RowColor)
  {
    $mysql = pdodb::getInstance();
    //$RelatedName = $prefix.$rec["DBName"]."_".$rec["name"]."_".$rec["FieldName"];
    $RelatedName = $prefix.$rec["id"];
    $FieldDesc = $rec["fdesc"];
    $RelatedDBName = $rec["RelatedDBName"];
    $RelatedTable = $rec["RelatedTable"];
    $RelatedField = $rec["RelatedField"];
    $RelationCondition = $rec["RelationCondition"];
    //echo "<br>".$RelatedName."<br>";
    if(isset($_REQUEST[$RelatedName]))
    {
      $FieldDesc = $_REQUEST[$RelatedName];
      $RelationCondition = $_REQUEST["rc_".$RelatedName];
      $data = explode(".",$_REQUEST["rd_".$RelatedName]);
      if(count($data)>0)
      {
	$RelatedDBName = $data[0];
	$RelatedTable = $data[1];
	$RelatedField = $data[2];
      }
      $query = "update mis.MIS_TableFields 
			set description=?, LastUpdateUser='".$_SESSION["UserID"]."', 
			LastUpdateYear='13$yy', LastUpdateMonth='$mm', LastUpdateDay='$dd'
			,RelatedDBName=? , RelatedTable=?, RelatedField=?, RelationCondition=?
			where id='".$rec["id"]."'";
      $mysql->Prepare($query);
      
      $mysql->ExecuteStatement(array($FieldDesc, $RelatedDBName, $RelatedTable, $RelatedField, $RelationCondition));
    }
    echo "<tr bgcolor=".$RowColor.">";
    echo "<td dir=ltr>";
    echo " <a target=_blank href='EditTableInfo.php?sTableName=&sdescription=&DB=".$rec["DBName"]."&TableName=".$rec["name"]."&DBName=".$rec["DBName"]."&ServerName='>";
    echo $rec["DBName"].".".$rec["name"]."</a></td>";
    echo "<td dir=ltr>".$rec["FieldName"]."</td>";
    echo "<td>".$rec["tdesc"]."</td>";
    echo "<td><input type=text name='".$RelatedName."' value='".$FieldDesc."'></td>";
    echo "<td><input dir=ltr size=30 type=text name='rd_".$RelatedName."' value='".$RelatedDBName.".".$RelatedTable.".".$RelatedField."'>";
    echo "<br>شرط: <input dir=ltr size=15 type=text name='rc_".$RelatedName."' value=\"".$RelationCondition."\"></td>";
    //echo "<td><input dir=ltr size=15 type=text name='rc_".$RelatedName."' value=\"".$RelationCondition."\"></td>";
    echo "</tr>";
  }
  
  $mysql = pdodb::getInstance();
  $DBName = $sTableName = $sdescription = $SearchType = "";
  
  if(isset($_REQUEST["FieldName"]))
  {
    //$DBName = $_REQUEST["DBName"];
    //$TableName = $_REQUEST["TableName"];
    $FieldName = $_REQUEST["FieldName"];
  }
  
  HTMLBegin();
  $now = date("Ymd"); 
  $yy = substr($now,0,4); 
  $mm = substr($now,4,2); 
  $dd = substr($now,6,2);
  list($dd,$mm,$yy) = ConvertX2SDate($dd,$mm,$yy);
  if(strlen($mm)==1)
	  $mm = "0".$mm;
  if(strlen($dd)==1)
	  $dd = "0".$dd;
  $yy = substr($yy, 2, 2);
  
  // Find synsets of the filedname title
  $query = "select synsetid from wordnet.senses 
			    JOIN wordnet.words using (wordid)
			    where lemma='".$FieldName."'";
  $sres = $mysql->Execute($query);
  $synsetlist = "";
  while($srec = $sres->fetch())
  {
    if($synsetlist!="")
      $synsetlist .= ", ";
    $synsetlist .= $srec["synsetid"];
  }
  
  $query = "select MIS_TableFields.id, MIS_Tables.DBName, MIS_Tables.name, MIS_Tables.description as tdesc, FieldName, MIS_TableFields.description as fdesc 
  , RelatedDBName, RelatedTable, RelatedField, RelationCondition
  from mis.MIS_TableFields 
	    JOIN mis.MIS_Tables on (MIS_TableFields.DBName=MIS_Tables.DBName and MIS_TableFields.TableName=MIS_Tables.name) 
	    where FieldName=? and MIS_Tables.DBName in (".$DBRange.") and TempOrCompletelyTransactional='NO'";
  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($FieldName));
  if(isset($_REQUEST["Save"]))
    echo "<table border=0 cellspacing=0 cellpadding=5><tr><td><font color=green>ذخیره سازی انجام شد</font></td></tr></table>";
  echo "<form method=post><input type=hidden name='FieldName' id='FieldName' value='".$FieldName."'>";
  echo "<input type=hidden name='Save'>";
  echo "<table border=1 cellspacing=0 cellpadding=5>";
  echo "<tr class=HeaderOfTable>";
  echo "<td colspan=8>تمامی فیلدها با نام مشابه ".$FieldName." در پایگاه های  داده</td>";
  echo "</tr>";
  echo "<tr class=HeaderOfTable>";
  echo "<td>نام جدول</td><td>نام فیلد</td><td>شرح جدول</td><td>شرح فیلد</td><td>کلید خارجی</td>";
  echo "</tr>";
  
  while($rec = $res->fetch())
    ShowRow("f_", $yy, $mm, $dd, $rec, "#b3ffec");
  
  $query = "select MIS_TableFields.id, MIS_Tables.DBName, MIS_Tables.name, MIS_Tables.description as tdesc, FieldName, MIS_TableFields.description as fdesc
	    , RelatedDBName, RelatedTable, RelatedField, RelationCondition
	    from mis.MIS_TableFields 
	    JOIN mis.MIS_Tables on (MIS_TableFields.DBName=MIS_Tables.DBName and MIS_TableFields.TableName=MIS_Tables.name) 
	    where FieldName<>?  and MIS_Tables.DBName in (".$DBRange.")  and TempOrCompletelyTransactional='NO'";
  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($FieldName));
  while($rec = $res->fetch())
    if(GetSimilarityPercent($rec["FieldName"], $FieldName)>$thereshold)
	ShowRow("f_", $yy, $mm, $dd, $rec, "#ffffb3");
  
  $WordnetSimilars = "";      
  if($synsetlist!="")
  {
    $query = "select MIS_TableFields.id, MIS_Tables.DBName, MIS_Tables.name, MIS_Tables.description as tdesc, FieldName, MIS_TableFields.description as fdesc
			      , RelatedDBName, RelatedTable, RelatedField, RelationCondition
			      from wordnet.senses 
			      JOIN wordnet.words using (wordid)
			      JOIN mis.MIS_TableFields on (FieldName=lemma COLLATE utf8_persian_ci)
			      JOIN mis.MIS_Tables on (MIS_TableFields.DBName=MIS_Tables.DBName and MIS_TableFields.TableName=MIS_Tables.name) 
			      where synsetid in (".$synsetlist.")
			      and lemma<>'".$FieldName."'  and MIS_Tables.DBName in (".$DBRange.")";
    $res = $mysql->Execute($query);
    while($rec = $res->fetch())
    {
	ShowRow("s_", $yy, $mm, $dd, $rec, "#ff8080");
    }
  }

  
  echo "<tr class=FooterOfTable>";
  echo "<td colspan=8 align=center>";
  echo "<input type=submit value='ذخیره'> &nbsp; ";
  echo "<input type=button value='بستن' onclick='javascript: window.close();'> </td>";
  echo "</table></form>";
?>
</body>
</html>