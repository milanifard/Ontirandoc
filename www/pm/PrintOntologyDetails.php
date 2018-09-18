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

function ShowParentClasses($ClassID)
{
	$mysql = pdodb::getInstance();
	$query = "select OntologyClassID, ClassTitle, label from projectmanagement.OntologyClassHirarchy 
	JOIN projectmanagement.OntologyClasses using (OntologyClassID)
	JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)
	where OntologyClassParentID=".$ClassID;
	$res = $mysql->Execute($query);
        if($res->rowCount()>0)
        {
        	echo "مفهوم بالاتر: ";
        	$i = 0;
        	while($rec = $res->fetch())
        	{
        		if($i>0)
        			echo " - ";
        		echo $rec["label"];
        		$i++;
        	}
       		echo "<br>";
        }
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
        	echo "خصوصیات: <br>";
        	$i = 0;
        	while($rec = $res->fetch())
        	{
        		echo "- ".$rec["label"]." (".$rec["PropertyTitle"].") ";
        		if($rec["PermittedValues"]!="")
        			echo " مقادیر مجاز: ".$rec["PermittedValues"];
        		echo "<br>";
        	}
        	echo "<br>";
        }
        
}

function ShowRelations($ClassID)
{
	$mysql = pdodb::getInstance();
	$query = "select  pl.label as pl, l1.label as c1l, l2.label as c2l from projectmanagement.OntologyObjectPropertyRestriction 
JOIN projectmanagement.OntologyProperties using (OntologyPropertyID)
JOIN projectmanagement.OntologyPropertyLabels pl using (OntologyPropertyID)
JOIN projectmanagement.OntologyClasses c1 on (DomainClassID=c1.OntologyClassID)
JOIN projectmanagement.OntologyClasses c2 on (RangeClassID=c2.OntologyClassID)
JOIN projectmanagement.OntologyClassLabels l1 on (c1.OntologyClassID=l1.OntologyClassID)
JOIN projectmanagement.OntologyClassLabels l2 on (c2.OntologyClassID=l2.OntologyClassID)
where RelationStatus='VALID' and (c1.OntologyClassID=".$ClassID." or c2.OntologyClassID=".$ClassID.")";

	$res = $mysql->Execute($query);
        if($res->rowCount()>0)
        {
        	echo "روابط با دیگر مفاهیم: <br>";
        	$i = 0;
        	while($rec = $res->fetch())
        	{
        		echo "- ".$rec["c1l"]." <b>".$rec["pl"]."</b> ".$rec["c2l"];
        		echo "<br>";
        	}
        }
        
}


echo "<table width=800 align=center border=1 cellspacing=0 cellpadding=10>";
$mysql = pdodb::getInstance();
$query = "select * from projectmanagement.OntologyClasses JOIN projectmanagement.OntologyClassLabels using (OntologyClassID) where OntologyID=? order by label";
$mysql->Prepare($query);
$res = $mysql->ExecuteStatement(array($_REQUEST["OntologyID"]));
while($rec = $res->fetch())
{
	echo "<tr>";
	echo "<td>";
	echo "<table border=0 width=100%>";
	echo "عنوان مفهوم: ".$rec["label"]." (".$rec["ClassTitle"].")<br>";
	
	ShowParentClasses($rec["OntologyClassID"]);

	ShowAttributes($rec["ClassTitle"], $_REQUEST["OntologyID"]);

	ShowRelations($rec["OntologyClassID"]);
	echo "</table>";
	echo "</td>";
	echo "</tr>";
	
}
echo "</table>";
?>
</table>
</html>
