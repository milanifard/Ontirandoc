<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : کلاسهای هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-29
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/OntologyClasses.class.php");
include("classes/OntologyClassLabels.class.php");
include("classes/OntologyClassHirarchy.class.php");
include("classes/OntologyProperties.class.php");
include("classes/OntologyPropertyLabels.class.php");
include ("classes/ontologies.class.php");
HTMLBegin();

function normalize($str)
{
    $replace=array("۰","۱","۲","۳","۴","۵","۶","۷","۸","۹");
    $pattern=array("0","1","2","3","4","5","6","7","8","9");
    for($i=0;$i<count($pattern);$i++)
    {
	$str=str_replace($pattern[$i],$replace[$i],$str);
    }
    return($str);
}

function GetParentClasses($ClassID)
{
	$ret = "";
	$mysql = pdodb::getInstance();
	$query = "select OntologyClassID, ClassTitle, label from projectmanagement.OntologyClassHirarchy 
	JOIN projectmanagement.OntologyClasses using (OntologyClassID)
	JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)
	where OntologyClassParentID=".$ClassID;
	$res = $mysql->Execute($query);
        if($res->rowCount()>0)
        {
        	$i = 0;
        	while($rec = $res->fetch())
        	{
        		if($i>0)
        			$ret .= " - ";
        		$ret .= $rec["label"];
        		$i++;
        	}
        }
        return $ret;
}


function ShowAttributes($ClassTitle, $OntologyID)
{
	$mysql = pdodb::getInstance();
	$query = "select PropertyTitle, label, 
(select group_concat(PermittedValue,' ') from projectmanagement.OntologyPropertyPermittedValues 
where OntologyProperties.OntologyPropertyID=OntologyPropertyPermittedValues.OntologyPropertyID) as PermittedValues
 from projectmanagement.OntologyProperties 
JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID) 
where OntologyID=? and PropertyType='DATATYPE' and (domain='".$ClassTitle."' or domain like '".$ClassTitle.",%' or domain like '%, ".$ClassTitle."' or domain like '%, ".$ClassTitle.",%')";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($OntologyID));
        if($res->rowCount()>0)
        {
        	$i = 0;
        	while($rec = $res->fetch())
        	{
        		echo $rec["label"];
        		if($rec["PermittedValues"]!="")
        			echo " مقادیر مجاز: ".$rec["PermittedValues"]."";
        		echo "<br>";
        	}
        }
        
}

function ShowRelations($PropertyID)
{
	$mysql = pdodb::getInstance();
	$query = "select  pl.label as pl, l1.label as c1l, l2.label as c2l from projectmanagement.OntologyObjectPropertyRestriction 
JOIN projectmanagement.OntologyProperties using (OntologyPropertyID)
JOIN projectmanagement.OntologyPropertyLabels pl using (OntologyPropertyID)
JOIN projectmanagement.OntologyClasses c1 on (DomainClassID=c1.OntologyClassID)
JOIN projectmanagement.OntologyClasses c2 on (RangeClassID=c2.OntologyClassID)
JOIN projectmanagement.OntologyClassLabels l1 on (c1.OntologyClassID=l1.OntologyClassID)
JOIN projectmanagement.OntologyClassLabels l2 on (c2.OntologyClassID=l2.OntologyClassID)
where RelationStatus='VALID' and OntologyObjectPropertyRestriction.OntologyPropertyID=?";

	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($PropertyID));
        if($res->rowCount()>0)
        {
        	$i = 0;
        	while($rec = $res->fetch())
        	{
        		echo $rec["c1l"]." - ".$rec["c2l"];
        		echo "<br>";
        	}
        }
        
}

function ShowClassOrigin($ElementID)
{
	$mysql = pdodb::getInstance();
	/*
	$mysql->Prepare("select distinct OntologyTitle, label from projectmanagement.OntologyMergeEntities 
	JOIN projectmanagement.OntologyClasses on (OntologyClasses.OntologyClassID=OntologyMergeEntities.EntityID)
	JOIN projectmanagement.OntologyClassLabels on (OntologyClasses.OntologyClassID=OntologyClassLabels.OntologyClassID)
	JOIN projectmanagement.ontologies using (OntologyID)
	where OntologyMergeEntities.EntityType='CLASS' and TargetEntityType='CLASS' and TargetEntityID=?");
	  $res = $mysql->ExecuteStatement(array($ElementID));
	  while($rec = $res->fetch())
	  {
	    echo $rec["OntologyTitle"]." (".$rec["label"].")<br>";
	  }
	$mysql->Prepare("select distinct OntologyTitle, label from projectmanagement.OntologyMergeEntities 
	JOIN projectmanagement.OntologyProperties on (OntologyProperties.OntologyPropertyID=OntologyMergeEntities.EntityID)
	JOIN projectmanagement.OntologyPropertyLabels on (OntologyProperties.OntologyPropertyID=OntologyPropertyLabels.OntologyPropertyID)
	JOIN projectmanagement.ontologies using (OntologyID)
	where OntologyMergeEntities.EntityType='PROPERTY' and TargetEntityType='CLASS' and TargetEntityID=?");
	  $res = $mysql->ExecuteStatement(array($ElementID));
	  while($rec = $res->fetch())
	  {
	    echo "<font color=red>".$rec["OntologyTitle"]." (".$rec["label"].")</font><br>";
	  }
	*/
	$mysql->Prepare("select distinct OntologyTitle from projectmanagement.OntologyMergeEntities 
	JOIN projectmanagement.OntologyClasses on (OntologyClasses.OntologyClassID=OntologyMergeEntities.EntityID)
	JOIN projectmanagement.ontologies using (OntologyID)
	where OntologyMergeEntities.EntityType='CLASS' and TargetEntityType='CLASS' and TargetEntityID=?
	union 
	select distinct OntologyTitle from projectmanagement.OntologyMergeEntities 
	JOIN projectmanagement.OntologyProperties on (OntologyProperties.OntologyPropertyID=OntologyMergeEntities.EntityID)
	JOIN projectmanagement.ontologies using (OntologyID)
	where OntologyMergeEntities.EntityType='PROPERTY' and TargetEntityType='CLASS' and TargetEntityID=?
	");
	$References = array();
	$References[0] = "&nbsp;";
	$References[1] = "&nbsp;";
	$References[2] = "&nbsp;";
	$References[3] = "&nbsp;";
	$References[4] = "&nbsp;";
	$References[5] = "&nbsp;";
	$References[6] = "&nbsp;";
	  $res = $mysql->ExecuteStatement(array($ElementID, $ElementID));
	  $k = 0;
	  //echo "<td>";
	  while($rec = $res->fetch())
	  {
	  	/*if($k>0) echo " - ";
		echo $rec["OntologyTitle"];
		$k++;		  	*/
	  	
	    if($rec["OntologyTitle"]=="hes ontology")
	    	$References[0] = "X";
	    if($rec["OntologyTitle"]=="saham ontology")
	    	$References[1] = "X";
	    if($rec["OntologyTitle"]=="semat ontology")
	    	$References[2] = "X";
	    	
	    	/*
	    if($rec["OntologyTitle"]=="HERO")
	    	$References[3] = "X";
	    if($rec["OntologyTitle"]=="LUMB")
	    	$References[4] = "X";
	    if($rec["OntologyTitle"]=="SWRC")
	    	$References[5] = "X";
	    if($rec["OntologyTitle"]=="univ-cs")
	    	$References[6] = "X";
	    if($rec["OntologyTitle"]=="university")
	    	$References[7] = "X";*/
	  }
	 // echo "</td>";
	for($i=0; $i<3; $i++)
		echo "<td>".$References[$i]."</td>";
}

function ShowPropertyOrigin($ElementID)
{
	$mysql = pdodb::getInstance();
	/*
	$mysql->Prepare("select distinct OntologyTitle, label from projectmanagement.OntologyMergeEntities 
	JOIN projectmanagement.OntologyClasses on (OntologyClasses.OntologyClassID=OntologyMergeEntities.EntityID)
	JOIN projectmanagement.OntologyClassLabels on (OntologyClasses.OntologyClassID=OntologyClassLabels.OntologyClassID)
	JOIN projectmanagement.ontologies using (OntologyID)
	where OntologyMergeEntities.EntityType='CLASS' and TargetEntityType='PROPERTY' and TargetEntityID=?");
	  $res = $mysql->ExecuteStatement(array($ElementID));
	  while($rec = $res->fetch())
	  {
	    echo $rec["OntologyTitle"]." (".$rec["label"].")<br>";
	  }
	  
	$mysql->Prepare("select distinct OntologyTitle, label from projectmanagement.OntologyMergeEntities 
	JOIN projectmanagement.OntologyProperties on (OntologyProperties.OntologyPropertyID=OntologyMergeEntities.EntityID)
	JOIN projectmanagement.OntologyPropertyLabels on (OntologyProperties.OntologyPropertyID=OntologyPropertyLabels.OntologyPropertyID)
	JOIN projectmanagement.ontologies using (OntologyID)
	where OntologyMergeEntities.EntityType='PROPERTY' and TargetEntityType='PROPERTY' and TargetEntityID=?");
	  $res = $mysql->ExecuteStatement(array($ElementID));
	  while($rec = $res->fetch())
	  {
	    echo $rec["OntologyTitle"]." (".$rec["label"].")<br>";
	  }
	 	*/
	 $mysql->Prepare("select distinct OntologyTitle from projectmanagement.OntologyMergeEntities 
	JOIN projectmanagement.OntologyClasses on (OntologyClasses.OntologyClassID=OntologyMergeEntities.EntityID)
	JOIN projectmanagement.ontologies using (OntologyID)
	where OntologyMergeEntities.EntityType='CLASS' and TargetEntityType='PROPERTY' and TargetEntityID=?
	union
	select distinct OntologyTitle from projectmanagement.OntologyMergeEntities 
	JOIN projectmanagement.OntologyProperties on (OntologyProperties.OntologyPropertyID=OntologyMergeEntities.EntityID)
	JOIN projectmanagement.ontologies using (OntologyID)
	where OntologyMergeEntities.EntityType='PROPERTY' and TargetEntityType='PROPERTY' and TargetEntityID=?
	");

	$References = array();
	$References[0] = "&nbsp;";
	$References[1] = "&nbsp;";
	$References[2] = "&nbsp;";
	$References[3] = "&nbsp;";
	$References[4] = "&nbsp;";
	$References[5] = "&nbsp;";
	$References[6] = "&nbsp;";
	  $res = $mysql->ExecuteStatement(array($ElementID, $ElementID));
	  while($rec = $res->fetch())
	  {
	    if($rec["OntologyTitle"]=="hes ontology")
	    	$References[0] = "X";
	    if($rec["OntologyTitle"]=="saham ontology")
	    	$References[1] = "X";
	    if($rec["OntologyTitle"]=="semat ontology")
	    	$References[2] = "X";
	    /*if($rec["OntologyTitle"]=="HERO")
	    	$References[3] = "X";
	    if($rec["OntologyTitle"]=="LUMB")
	    	$References[4] = "X";
	    if($rec["OntologyTitle"]=="SWRC")
	    	$References[5] = "X";
	    if($rec["OntologyTitle"]=="univ-cs")
	    	$References[6] = "X";
	    if($rec["OntologyTitle"]=="university")
	    	$References[7] = "X";*/
	  }
	for($i=0; $i<3; $i++)
		echo "<td>".$References[$i]."</td>";
}


function ShowClassRows($OntologyClassID, $level = 0)
{
	if($level>5)
		return;
	$mysql = pdodb::getInstance();
	$query = "select * from projectmanagement.OntologyClasses JOIN projectmanagement.OntologyClassLabels using (OntologyClassID) where OntologyClassID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($OntologyClassID));
	if($rec = $res->fetch())
	{
		echo "<tr>";
		echo "<td>";
		echo $rec["label"];
		$pl = GetParentClasses($rec["OntologyClassID"]);
		/*
		if($pl!="")
		{
			echo "<br>";
			echo "نوعی از ".$pl." است";
		}
		*/
		echo "</td>";
		echo "<td>";
		if($pl!="")
			echo $pl;
		else
			echo "&nbsp;";
		echo "</td>";
		/*
		echo "<td>";
		ShowAttributes($rec["ClassTitle"], $_REQUEST["OntologyID"]);
		echo "</td>";
		echo "<td>";
		ShowRelations($rec["OntologyClassID"]);
		echo "</td>";
		*/
		ShowClassOrigin($rec["OntologyClassID"]);
		echo "</tr>";
	}
	$query = "select OntologyClassParentID from projectmanagement.OntologyClassHirarchy 
	JOIN projectmanagement.OntologyClasses using (OntologyClassID)
	JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)
	where OntologyClassID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($OntologyClassID));	
	while($rec = $res->fetch())
	{
		ShowClassRows($rec["OntologyClassParentID"], $level+1); 
	}
}

function ShowClassLbels($ClassListString, $OntologyID)
{
  $mysql = pdodb::getInstance();
  $ClassList = explode(", ",$ClassListString);
  for($i=0; $i<count($ClassList); $i++)
  {
    $mysql->Prepare("select * from projectmanagement.OntologyClasses JOIN projectmanagement.OntologyClassLabels using (OntologyClassID) where ClassTitle=? and OntologyID=?");
    $res = $mysql->ExecuteStatement(array($ClassList[$i], $OntologyID));
    if($rec = $res->fetch())
    {
    	if($i>0)
    		echo " - ";
        echo $rec["label"];
    }
  }
}

function ShowDataPropertyRows($OntologyID, $OntologyPropertyID, $label, $domain, $PermittedValues)
{
	echo "<tr>";
	echo "<td>";
	echo $label;
	echo "</td>";
	echo "<td>";
	ShowClassLbels($domain, $OntologyID);
	echo "</td>";
	echo "<td>";
	if($PermittedValues!="")
		echo " ".str_replace(',',' - ',$PermittedValues);
	else
		echo "&nbsp;";
	echo "</td>";
	ShowPropertyOrigin($OntologyPropertyID);
	echo "</tr>";
}

function ShowObjectPropertyRows($OntologyID, $OntologyPropertyID, $label, $domain, $range)
{
	echo "<tr>";
	echo "<td>";
	echo $label;
	echo "</td>";
	echo "<td>";
	ShowRelations($OntologyPropertyID);
	echo "</td>";
	ShowPropertyOrigin($OntologyPropertyID);
	echo "</tr>";
}

echo "<table width=80% align=center border=1 cellspacing=0 cellpadding=10>";
echo "<tr class=HeaderOfTable>";
echo "<td rowspan=2>مفهوم</td>";
echo "<td rowspan=2>مفاهیم بالاتر</td>";
//echo "<td rowspan=2>خصوصیات</td>";
//echo "<td rowspan=2>روابط</td>";
echo "<td colspan=3>منابع</td>";
//echo "<td>منابع</td>";

echo "</tr>";

echo "<tr class=HeaderOfTable>";
for($i=1; $i<4; $i++)
	echo "<td>".$i."</td>";
echo "</tr>";

$mysql = pdodb::getInstance();
$query = "select * from projectmanagement.OntologyClasses JOIN projectmanagement.OntologyClassLabels using (OntologyClassID) where OntologyID=?
and OntologyClassID not in (select OntologyClassParentID from projectmanagement.OntologyClassHirarchy) 
 order by label";
$mysql->Prepare($query);
$res = $mysql->ExecuteStatement(array($_REQUEST["OntologyID"]));
while($rec = $res->fetch())
{
	ShowClassRows($rec["OntologyClassID"], 1);	
}
echo "</table>";
?>
</table>
<br>
<br>
<?
echo "<table width=80% align=center border=1 cellspacing=0 cellpadding=10>";

echo "<tr class=HeaderOfTable>";
echo "<td rowspan=2>خصوصیت</td>";
echo "<td rowspan=2>مفاهیم دارای این خصوصیت</td>";
echo "<td rowspan=2>مقادیر مجاز</td>";
echo "<td colspan=3>منابع</td>";
echo "</tr>";

echo "<tr class=HeaderOfTable>";
for($i=1; $i<4; $i++)
	echo "<td>".$i."</td>";
echo "</tr>";

$mysql = pdodb::getInstance();
$query = "select *,
(select group_concat(PermittedValue,' ') from projectmanagement.OntologyPropertyPermittedValues 
where OntologyProperties.OntologyPropertyID=OntologyPropertyPermittedValues.OntologyPropertyID) as PermittedValues
 from projectmanagement.OntologyProperties JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID) 
where OntologyID=? and PropertyType='DATATYPE' order by PropertyType, label";
$mysql->Prepare($query);
$res = $mysql->ExecuteStatement(array($_REQUEST["OntologyID"]));
while($rec = $res->fetch())
{
	ShowDataPropertyRows($_REQUEST["OntologyID"], $rec["OntologyPropertyID"], $rec["label"], $rec["domain"], $rec["PermittedValues"]);	
}
echo "</table>";


echo "<table width=80% align=center border=1 cellspacing=0 cellpadding=10>";
echo "<tr class=HeaderOfTable>";
echo "<td rowspan=2>رابطه</td>";
echo "<td rowspan=2>مفاهیم دارای این رابطه</td>";

echo "<td colspan=3>منابع</td>";
echo "</tr>";

echo "<tr class=HeaderOfTable>";
for($i=1; $i<4; $i++)
	echo "<td>".$i."</td>";
echo "</tr>";

$mysql = pdodb::getInstance();
$query = "select *,
(select group_concat(PermittedValue,' ') from projectmanagement.OntologyPropertyPermittedValues 
where OntologyProperties.OntologyPropertyID=OntologyPropertyPermittedValues.OntologyPropertyID) as PermittedValues
 from projectmanagement.OntologyProperties JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID) 
where OntologyID=? and PropertyType='OBJECT' order by PropertyType, label";
$mysql->Prepare($query);
$res = $mysql->ExecuteStatement(array($_REQUEST["OntologyID"]));
while($rec = $res->fetch())
{
	ShowObjectPropertyRows($_REQUEST["OntologyID"], $rec["OntologyPropertyID"], $rec["label"], $rec["domain"], $rec["range"]);	
}
echo "</table>";

?>
</table>

</html>
