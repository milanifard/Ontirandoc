<?
  include("header.inc.php");
  HTMLBegin();
  $TableName = $_REQUEST["TableName"];
  if(isset($_REQUEST["Limit"]))
      $Limit = $_REQUEST["Limit"];
  else {
      $Limit = "10";
  }
  $CheckBoxJs = "";
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("desc $TableName");
  $FieldsList = array();
  $i = 0;
  while($rec = $res->fetch())
  {
    if($rec["Type"]!="longblob" && $rec["Type"]!="mediumblob" && $rec["Type"]!="blob")
    {
      $FieldsList[$i] = $rec["Field"];
      $i++;
    }
  }
  echo "<form method=post>";
  echo "<input type=hidden name=TableName id=TableName value='".$TableName."'>";
  echo "<table width=95% align=center border=1 cellspacing=0 cellpadding=3 dir=ltr><tr bgcolor=#cccccc>";
  echo "<tr>";
  echo "<td><select name=FieldName><option value=''>-";
  for($j=0; $j<count($FieldsList); $j++)
  {
    echo "<option value='".$FieldsList[$j]."' ";
    if(isset($_REQUEST["FieldName"]) && $_REQUEST["FieldName"]==$FieldsList[$j])
      echo " selected ";
    echo ">".$FieldsList[$j];
  }
  echo "</select> ";
  echo "<select name=FOperator id=FOperator>";
  echo "<option value='=' ";
  if(isset($_REQUEST["FOperator"]) && $_REQUEST["FOperator"]=="=")
    echo " selected ";
  echo ">=";

  echo "<option value='<>' ";
  if(isset($_REQUEST["FOperator"]) && $_REQUEST["FOperator"]=="<>")
    echo " selected ";
  echo ">&lt;&gt;";

  echo "<option value='<' ";
  if(isset($_REQUEST["FOperator"]) && $_REQUEST["FOperator"]=="<")
    echo " selected ";
  echo ">&lt;";

  echo "<option value='>' ";
  if(isset($_REQUEST["FOperator"]) && $_REQUEST["FOperator"]==">")
    echo " selected ";
  echo ">&gt;";
  
  echo "<option value='LIKE' ";
  if(isset($_REQUEST["FOperator"]) && $_REQUEST["FOperator"]=="LIKE")
    echo " selected ";
  echo ">LIKE";
  
  echo "<option value='is null' ";
  if(isset($_REQUEST["FOperator"]) && $_REQUEST["FOperator"]=="is null")
    echo " selected ";
  echo ">is null";

  echo "<option value='is not null' ";
  if(isset($_REQUEST["FOperator"]) && $_REQUEST["FOperator"]=="is not null")
    echo " selected ";
  echo ">is not null";
  
  echo "</select>";
  echo " <input name=FieldValue type=text ";
  if(isset($_REQUEST["FieldValue"]))
    echo " value = '".$_REQUEST["FieldValue"]."' ";
  echo " size=20> limit <input name=Limit type=text size=2 value='".$Limit."'> ";
  
  echo " Order By ";

  echo "<select name=OrderBy><option value=''>-";
  for($j=0; $j<count($FieldsList); $j++)
  {
    echo "<option value='".$FieldsList[$j]."' ";
    if(isset($_REQUEST["OrderBy"]) && $_REQUEST["OrderBy"]==$FieldsList[$j])
      echo " selected ";
    echo ">".$FieldsList[$j];
  }
  echo "</select>";
  
  echo "<select name=OrderType>";
  echo "<option value=''>-";
  echo "<option value='ASC' ";
  if(isset($_REQUEST["OrderType"]) && $_REQUEST["OrderType"]=="ASC")
    echo " selected ";
  echo ">ASC";
  echo "<option value='DESC' ";
  if(isset($_REQUEST["OrderType"]) && $_REQUEST["OrderType"]=="DESC")
    echo " selected ";
  echo ">DESC";
  
  echo "</select> ";  
  echo "<input type=submit value='Filter'>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>";
  echo "<table>";
  echo "<tr><td colspan=4><b><input type=checkbox name=CheckAll id=CheckAll onchange='javascript: DoCheckAll(this.checked)'>Check All</b></td></tr>";
  for($j=0; $j<count($FieldsList); $j++)
  {
    if($j%4==0)
    {
      if($j>0)
	echo "</tr>";
      echo "<tr>";
    }
    echo "<td>";
    echo "<input type=checkbox name=ch_".$FieldsList[$j]." id=ch_".$FieldsList[$j]." ";
    if(isset($_REQUEST["ch_".$FieldsList[$j]]))
      echo " checked ";
    echo ">".$FieldsList[$j];
    $CheckBoxJs .= " document.getElementById('ch_".$FieldsList[$j]."').checked = status;\r\n";
    echo "</td>";
  }
  echo "</table>";
  echo "</td>";
  echo "</tr>";
  echo "</table>";
  echo "</form>";
  echo "<table width=95% align=center border=1 cellspacing=0 cellpadding=3 dir=ltr><tr bgcolor=#cccccc>";
  for($i=0; $i<count($FieldsList); $i++)
  {
    if(!isset($_REQUEST["Limit"]) || (isset($_REQUEST["Limit"]) && isset($_REQUEST["ch_".$FieldsList[$i]])))
      echo "<td>".$FieldsList[$i]."</td>";
  }
  echo "</tr>";
  $query = "select * from $TableName ";
  if(isset($_REQUEST["FieldName"]) && $_REQUEST["FieldName"]!="")
  {
    $query .= " where ".$_REQUEST["FieldName"];
    if($_REQUEST["FOperator"]=="=" || $_REQUEST["FOperator"]=="<>" || $_REQUEST["FOperator"]==">" || $_REQUEST["FOperator"]=="<")
      $query .= $_REQUEST["FOperator"].= "'".$_REQUEST["FieldValue"]."' ";
    if($_REQUEST["FOperator"]=="LIKE")
      $query .= " LIKE '%".$_REQUEST["FieldValue"]."%' ";
    if($_REQUEST["FOperator"]=="is null")
      $query .= " is null ";
    if($_REQUEST["FOperator"]=="is not null")
      $query .= " is not null ";
  }
  if(isset($_REQUEST["OrderBy"]) && $_REQUEST["OrderBy"]!="")
  {
    $query .= " order by ".$_REQUEST["OrderBy"]." ";
    if($_REQUEST["OrderType"]!="")
      $query .= $_REQUEST["OrderType"];
  }
  $query .= " limit ".$Limit;
  $res2 = $mysql->Execute($query);
  $i = 0;
  while($rec2 = $res2->fetch())
  {
    if($i%2==0)
	    echo "<tr>";
    else
	    echo "<tr bgcolor=#efefef>";
    for($j=0; $j<count($FieldsList); $j++)
	if(!isset($_REQUEST["Limit"]) || (isset($_REQUEST["Limit"]) && isset($_REQUEST["ch_".$FieldsList[$j]])))
	    echo "<td>".$rec2[$FieldsList[$j]]."</td>";
    echo "</tr>";
  }	
  echo "</table>";
?>
<script>
  <? if(!isset($_REQUEST["Limit"])) echo "document.getElementById('CheckAll').checked = true;\r\n DoCheckAll(true);" ?>
  
  function DoCheckAll(status)
  {
    <? echo $CheckBoxJs; ?>
  }
</script>
</body></html>