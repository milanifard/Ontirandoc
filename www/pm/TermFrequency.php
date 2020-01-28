<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/
include("header.inc.php");
include_once("classes/TermReferenceMapping.class.php");
HTMLBegin();

function ShowCoOccuranceTerms($TermID)
{
  $ret = "";
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select 
  IF(TermID2='".$TermID."', t1.TermTitle, t2.TermTitle) as TermTitle, 
  IF(TermID2='".$TermID."', t1.TermID, t2.TermID) as TermID ,
  frequency
  from projectmanagement.TermsCoOccure 
  JOIN projectmanagement.terms t1 on (t1.TermID=TermsCoOccure.TermID1)
  JOIN projectmanagement.terms t2 on (t2.TermID=TermsCoOccure.TermID2)
  where TermID1='".$TermID."' or TermID2='".$TermID."'
  order by frequency DESC
  "); 
  while($rec = $res->fetch())
  {
    // $ret .= "<a href='?TermID=".$rec["TermID"]."'>";
    $ret .= $rec["TermTitle"]."(".$rec["frequency"].")";
    //$ret .= "</a>";
    $ret .= ", ";
  }
  return $ret;
}

function ShowRelatedTerms($TermID)
{
  $ret = "";
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select TermReferenceID, PageNum, ParagraphNo from projectmanagement.TermReferenceMapping
			    where TermID='".$TermID."'");
  while($rec = $res->fetch())
  {
    $res2 = $mysql->Execute("select TermID, TermTitle from projectmanagement.TermReferenceMapping
			      JOIN projectmanagement.terms using (TermID)
			      where PageNum='".$rec["PageNum"]."' and ParagraphNo='".$rec["ParagraphNo"]."'");
			
    while($rec2 = $res2->fetch())
    {
      echo $rec2["TermTitle"]." - ";
    }
    echo "<br>";
  }
}

$mysql = pdodb::getInstance();

if(isset($_REQUEST["RemoveID"]))
{
  $mysql->Prepare("select TermReferenceMappingID from projectmanagement.TermReferenceMapping where TermID=?");
  $res = $mysql->ExecuteStatement(array($_REQUEST["RemoveID"]));
  while($rec = $res->fetch())
  {
    manage_TermReferenceMapping::Remove($rec["TermReferenceMappingID"]);
  }
}

if(isset($_REQUEST["TargetOntologyID"]))
{
  $mysql->Execute("delete from projectmanagement.TermMappingTargetOntology");
  $mysql->Prepare("insert into projectmanagement.TermMappingTargetOntology (OntologyID) values (?)");
  $mysql->ExecuteStatement(array($_REQUEST["TargetOntologyID"]));
  echo "<p align=center><font color=green>ذخیره شد</font></p>";
}
$res = $mysql->Execute("select * from projectmanagement.TermMappingTargetOntology");
if($rec = $res->fetch())
{
  $TargetOntologyID = $rec["OntologyID"];
}
else
  $TargetOntologyID = 0;
if(isset($_REQUEST["UpdateTF"]))
{
  $res = $mysql->Execute("select count(distinct TermReferenceID, PageNum, ParagraphNo) as tcount from projectmanagement.TermReferenceMapping ");
  $rec = $res->fetch();
  $N = $rec["tcount"];

  $res = $mysql->Execute("select TermID, TermTitle, count(*) as tf from projectmanagement.terms 
  JOIN projectmanagement.TermReferenceMapping using (TermID)  
  group by TermID, TermTitle");
  $i = 0;
  while($rec = $res->fetch())
  {
    $IDF = log($N/$rec["tf"]);
    $mysql->Execute("update projectmanagement.terms set TF='".$rec["tf"]."', IDF='".$IDF."' where TermID='".$rec["TermID"]."'");
  }
  echo "مقادیر tf-idf بروز شد";
}
$ShowType = "All";
if(isset($_REQUEST["ShowType"]))
{
  $ShowType = $_REQUEST["ShowType"];
  $_SESSION["ShowType"]=$_REQUEST["ShowType"];
}
else if(isset($_SESSION["ShowType"]))
{
  $ShowType = $_SESSION["ShowType"];
}
/*
$query = "select distinct TermID, TermTitle, TF, IDF, 
(select concat(EntityType, ' : ', IF(EntityType='CLASS', ClassTitle, PropertyTitle), IF(EntityType='DATA_RANGE', concat(' - ', IF(PermittedValue is null, '', PermittedValue)), ''))  from projectmanagement.TermOntologyElementMapping 
				LEFT JOIN projectmanagement.OntologyClasses on (OntologyEntityID=OntologyClassID)
				LEFT JOIN projectmanagement.OntologyProperties on (OntologyEntityID=OntologyPropertyID)
				LEFT JOIN projectmanagement.OntologyPropertyPermittedValues using (OntologyPropertyPermittedValueID)
				where TermOntologyElementMapping.TermID=terms.TermID) as OntologyElement 
from projectmanagement.TermReferenceMapping 
JOIN projectmanagement.terms using (TermID) ";
if($ShowType=="OnlyNotModeled")
  $query .= " where ((select concat(EntityType, ' : ', IF(EntityType='CLASS', ClassTitle, PropertyTitle), IF(EntityType='DATA_RANGE', concat(' - ', IF(PermittedValue is null, '', PermittedValue)), ''))  from projectmanagement.TermOntologyElementMapping 
				LEFT JOIN projectmanagement.OntologyClasses on (OntologyEntityID=OntologyClassID)
				LEFT JOIN projectmanagement.OntologyProperties on (OntologyEntityID=OntologyPropertyID)
				LEFT JOIN projectmanagement.OntologyPropertyPermittedValues using (OntologyPropertyPermittedValueID)
				where TermOntologyElementMapping.TermID=terms.TermID)) is null ";
$query .= "order by TF*IDF DESC";
*/
$query = "select distinct TermID, TermTitle, TF, IDF, EntityType, 
ClassTitle, OntologyClassLabels.label as ClassLabel, 
PropertyTitle, OntologyPropertyLabels.label as PropertyLabel,
PermittedValue
from projectmanagement.TermReferenceMapping 
JOIN projectmanagement.terms using (TermID)
JOIN projectmanagement.TermOntologyElementMapping using (TermID)
LEFT JOIN projectmanagement.OntologyClasses on (OntologyClasses.OntologyClassID=TermOntologyElementMapping.OntologyEntityID)
LEFT JOIN projectmanagement.OntologyClassLabels on (OntologyClasses.OntologyClassID=OntologyClassLabels.OntologyClassID)
LEFT JOIN projectmanagement.OntologyProperties on (OntologyProperties.OntologyPropertyID=TermOntologyElementMapping.OntologyEntityID)
LEFT JOIN projectmanagement.OntologyPropertyLabels on (OntologyProperties.OntologyPropertyID=OntologyPropertyLabels.OntologyPropertyID)
LEFT JOIN projectmanagement.OntologyPropertyPermittedValues using (OntologyPropertyPermittedValueID)";
if($ShowType=="OnlyNotModeled")
	$query .= " Where OntologyEntityID is null ";
$query .= " order by TF*IDF DESC ";
$res = $mysql->Execute($query);

echo "<table align=center border=1 cellspacing=0>";
echo "<tr bgcolor=#cccccc>";
echo "<td colspan=11>";
echo "نمایش ";
echo "<select name=ShowType id=ShowType onchange='javascript: document.location=\"TermFrequency.php?ShowType=\"+this.value'>";
echo "<option value='All'>تمام واژگان";
echo "<option value='OnlyNotModeled' ";
if($ShowType=="OnlyNotModeled")
  echo " selected ";
echo ">فقط واژگان مدل نشده";
echo "</select>";
echo "</td>";
echo "</tr>";
echo "<tr class=HeaderOfTable><td>ردیف</td><td>حذف</td><td>اصطلاح</td>
<td>عنصر معادل</td>
<td>عنوان</td>
<td>برچسب</td>
<td>مقدار مجاز</td>
<td>TF</td><td>IDF</td><td>TF-IDF</td><td>ارجاعات</td></tR>";
$i = 0;
while($rec = $res->fetch())
{
  $i++;
  echo "<tr>";
  echo "<td>".$i."</td>";
  echo "<td><a href='javascript: RemoveTerm(\"".$rec["TermTitle"]."\", \"".$rec["TermID"]."\")'><img src='images/delete.png' border=0 title='حذف'></td>";
  echo "<td><a href='TermOntologyPage.php?TermID=".$rec["TermID"]."'>".$rec["TermTitle"]."</a></td>";
  echo "<td dir=ltr>".$rec["EntityType"]."</td>";
  if($rec["EntityType"]=="CLASS")
  {
  	echo "<td dir=ltr>".$rec["ClassTitle"]."</td>";
  	echo "<td>".$rec["ClassLabel"]."</td>";
  	echo "<td>&nbsp;</td>";
  }
  else
  {
  	echo "<td dir=ltr>".$rec["PropertyTitle"]."</td>";
  	echo "<td>".$rec["PropertyLabel"]."</td>";
	  if($rec["EntityType"]=="DATA_RANGE")
	    echo "<td bgcolor=#66ffff>".$rec["PermittedValue"];
	  else
	    echo "<td >&nbsp;";
	  echo "</td>";
  }
  echo "<td>".$rec["TF"]."</td>";
  echo "<td>".$rec["IDF"]."</td>";
  echo "<td>".($rec["TF"]*$rec["IDF"])."</td>";
  /*
  echo "<td width=40%>".ShowCoOccuranceTerms($rec["TermID"])."</td>";
  echo "<td width=40%>";
  echo ShowRelatedTerms($rec["TermID"]);
  echo "</td>";
  */
  echo "<td><a href='ShowTermReferenceMapping.php?TermID=".$rec["TermID"]."'>ارجاعات</a></td>";
  echo "</tr>";
}
echo "<tr><td colspan=11 align=center bgcolor=#cccccc>";
echo "<input type=button value='بروزرسانی همرخدادی ها' onclick='javascript: window.open(\"CoOccuranceAnalysis.php\")'>";
echo " &nbsp; ";
echo "<input type=button value='بروزرسانی TF-IDF' onclick='javascript: document.location=\"TermFrequency.php?UpdateTF=1\"; '>";
echo "</td></tr>";
echo "</table>";
?>
<form method=post>
هستان نگار مقصد برای مفهوم سازی واژگان: 
  <select name=TargetOntologyID id=TargetOntologyID dir=ltr>
  <option value=''>-
  <?
    $res = $mysql->Execute("select * from projectmanagement.ontologies");
    while($rec = $res->fetch())
    {
      echo "<option value='".$rec["OntologyID"]."' ";
      if($TargetOntologyID==$rec["OntologyID"])
	echo " selected ";
      echo ">".$rec["OntologyTitle"];
    }
  ?>
  </select>
  <input type=submit value='ذخیره'>  
</form>
<script>
  function RemoveTerm(TermTitle, TermID)
  {
    if(confirm('آیا از حذف واژه "'+TermTitle+'" مطمئن هستید؟'))
    {
      document.location='TermFrequency.php?RemoveID='+TermID;
    }
  }
</script>
</html>
