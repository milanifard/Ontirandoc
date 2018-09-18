<? 
include "header.inc.php";
include "FormsGeneratorDB.class.php";  
$mysql = pdodb::getInstance();

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

if(isset($_REQUEST["GetValue"]))
{
  $mysql->Prepare("select ShowValue, count(*)  from mis.FieldsDataMapping where ActualValue=? group by ShowValue order by count(*) DESC");
  $res = $mysql->ExecuteStatement(array($_REQUEST["GetValue"]));
  if($rec = $res->fetch())
  {
    echo $rec["ShowValue"];
  }
  else {
      echo $_REQUEST["GetValue"];
  }
  die();
}

HTMLBegin();
?>
<p align=center><span id=MessageSpan name=MessageSpan>
<? if(isset($_REQUEST["EnumSave"]) || isset($_REQUEST["IntSave"])) { echo "<font color=green>ذخیره سازی انجام شد</font>"; } ?>
</span></p>
<?php 
$LevelNo = "1";
$DBName = $TableName = $FieldName = "";
if(isset($_REQUEST["DBName"]))
{
  $DBName = $_REQUEST["DBName"];
  $TableName = $_REQUEST["TableName"];
  $FieldName = $_REQUEST["FieldName"];
}
$i= 0 ;
$query = "select * from mis.MIS_TableFields where DBName=? and TableName=? and FieldName=?";
$mysql->Prepare($query);
$res = $mysql->ExecuteStatement(array($DBName, $TableName, $FieldName));
if($rec = $res->fetch())
{
  
  echo "<form method=post>";
  echo "<input type=hidden name=DBName id=DBName value='".$DBName."'>";
  echo "<input type=hidden name=TableName id=TableName value='".$TableName."'>";
  echo "<input type=hidden name=FieldName id=FieldName value='".$FieldName."'>";
  echo "<table width=80% align=center border=1>";
  echo "<tr class=HeaderOfTable>";
  echo "<td colspan=2>".$rec["description"]." ".$TableName.".".$FieldName."</td>";
  echo "</tr>";
  
// برای فیلدهای شمارشی تمام اعضا را بر اساس ساختار جدول برای معنادهی لیست می کند
  if(strpos($rec["FieldType"], "enum(")!==false)
  {
    echo "<input type=hidden name=EnumSave value=1>";  
    $EnumItems = str_replace("enum(", "", $rec["FieldType"]);
    $EnumItems = substr($EnumItems, 0, strlen($EnumItems)-1);    
    $EnumItemsList = split(",", $EnumItems);
    
    for($i=0; $i<count($EnumItemsList); $i++)
    {
      if(isset($_REQUEST["EnumSave"]))
      {
	$CurrentValue = $_REQUEST["FieldValue"."_".$i];
	$mysql->Prepare("delete from mis.FieldsDataMapping where DBName=? and TableName=? and FieldName=? and ActualValue=?");
	$mysql->ExecuteStatement(array($DBName, $TableName, $FieldName, $EnumItemsList[$i]));
	
	$mysql->Prepare("insert into mis.FieldsDataMapping (DBName, TableName, FieldName, ActualValue, ShowValue) values (?,?,?,?,?)");
	$mysql->ExecuteStatement(array($DBName, $TableName, $FieldName, $EnumItemsList[$i], $CurrentValue));
	echo "<script>window.close();</script>";
      }
      else 
      {
	$mysql->Prepare("select ShowValue from mis.FieldsDataMapping 
				  where DBName=? and TableName=? and FieldName=? and ActualValue=?");
	$res2 = $mysql->ExecuteStatement(array($DBName, $TableName, $FieldName, $EnumItemsList[$i]));
	$CurrentValue = "";
	if($rec2 = $res2->fetch())
	  $CurrentValue = $rec2["ShowValue"];
      }
      echo "<tr>";
      echo "<td dir=ltr>".$EnumItemsList[$i]."</td>";
      echo "<td>";
      echo "<input type=text name=FieldValue_".$i." id=FieldValue_".$i." value='".$CurrentValue."'> ";
      echo "<a href='#' onclick='javascript: GetValue(\"FieldValue_".$i."\", \"".$EnumItemsList[$i]."\")'>";
      echo "<img width=20 src='images/Execute.png' border=0 title='دریافت مقدار از میان داده های پیشین'></a>";
      echo "</td>";
      echo "</tr>";
    }
    if(isset($_REQUEST["EnumSave"]))
    {
	$mysql->Prepare("update mis.MIS_TableFields set LastUpdateYear='".$yy."', LastUpdateMonth='".$mm."', LastUpdateDay='".$dd."', LastUpdateUser='".$_SESSION["UserID"]."' where DBName=? and TableName=? and FieldName=? ");
	$mysql->ExecuteStatement(array($DBName, $TableName, $FieldName));
    }
  }
  else
  {
    // نوع فیلد عددی است و مقادیر گسسته و با کاردینالیتی پایین دارد
    echo "<input type=hidden name=IntSave value=1>";
    echo "<tr><td colspan=2 bgcolor=#cccccc>این فیلد از نوع عددی می باشد و در جدول اطلاعاتی موجود مقادیر منحصر بفرد زیر را دارد:</td></tr>";
    $EnumItemsList = array();
    $query = "select distinct(".$FieldName.") as ".$FieldName." from ".$DBName.".".$TableName." order by ".$FieldName;
    $res = $mysql->Execute($query);
    $i=0;
    while($rec = $res->fetch())
    {
      $EnumItemsList[$i] = $rec[$FieldName];
      $i++;
      if($i>100)
	break;
    }
    
    for($i=0; $i<count($EnumItemsList); $i++)
    {
      if(isset($_REQUEST["IntSave"]))
      {
	$CurrentValue = $_REQUEST["FieldValue"."_".$i];
	$mysql->Prepare("delete from mis.FieldsDataMapping where DBName=? and TableName=? and FieldName=? and ActualValue=?");
	$mysql->ExecuteStatement(array($DBName, $TableName, $FieldName, $EnumItemsList[$i]));
	
	$mysql->Prepare("insert into mis.FieldsDataMapping (DBName, TableName, FieldName, ActualValue, ShowValue) values (?,?,?,?,?)");
	$mysql->ExecuteStatement(array($DBName, $TableName, $FieldName, $EnumItemsList[$i], $CurrentValue));
	echo "<script>window.close();</script>";
      }
      else 
      {
	$mysql->Prepare("select ShowValue from mis.FieldsDataMapping 
				  where DBName=? and TableName=? and FieldName=? and ActualValue=?");
	$res2 = $mysql->ExecuteStatement(array($DBName, $TableName, $FieldName, $EnumItemsList[$i]));
	$CurrentValue = "";
	if($rec2 = $res2->fetch())
	  $CurrentValue = $rec2["ShowValue"];
      }
      echo "<tr>";
      echo "<td dir=ltr>".$EnumItemsList[$i]."</td>";
      echo "<td>";
      echo "<input type=text name=FieldValue_".$i." value='".$CurrentValue."'>";
      echo "</td>";
      echo "</tr>";
    }
    
    if(isset($_REQUEST["IntSave"]))
    {
	$mysql->Prepare("update mis.MIS_TableFields set LastUpdateYear='".$yy."', LastUpdateMonth='".$mm."', LastUpdateDay='".$dd."', LastUpdateUser='".$_SESSION["UserID"]."' where DBName=? and TableName=? and FieldName=? ");
	$mysql->ExecuteStatement(array($DBName, $TableName, $FieldName));
    }
    
  }
  
  echo "<tr class=FooterOfTable>";
  echo "<td align=center colspan=2>";
  echo "<input type=submit value='ذخیره'>";
  echo " &nbsp; ";
  echo "<input type=button value='بستن' onclick='javascript: window.close();'>";
  echo "</td>";
  echo "</tr>";
  
}
?>
<script>
  function GetValue(InputName, ActualValue)
  {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (xhttp.readyState == 4 && xhttp.status == 200) {
	//alert(xhttp.responseText);
	document.getElementById(InputName).value = xhttp.responseText;	
      }
    };
    xhttp.open("GET", "ManageFieldsDataSematics.php?GetValue="+ActualValue, true);
    xhttp.send();    
  }
</script>
</html>