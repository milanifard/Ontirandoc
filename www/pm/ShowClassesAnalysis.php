<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : کلاسهای هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-29
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/OntologyClasses.class.php");
include_once("classes/OntologyClassLabels.class.php");
include_once("classes/OntologyClassHirarchy.class.php");
include_once("classes/ontologies.class.php");
HTMLBegin();

function GetNumberOfChilds($LevelNo, $ParentID)
{
  $mysql = pdodb::getInstance();
  $LevelNo++;
  if($LevelNo>20)
    return 0;
  $indent = "";

  $query = "select OntologyClasses.OntologyClassID
    from projectmanagement.OntologyClasses 
    JOIN projectmanagement.OntologyClassHirarchy on (OntologyClassHirarchy.OntologyClassParentID=OntologyClasses.OntologyClassID)
    where OntologyClassHirarchy.OntologyClassID=?";

  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($ParentID));
  $k = 0;

  while($rec = $res->fetch())
  {
    $k++;
    $k=$k+GetNumberOfChilds($LevelNo, $rec["OntologyClassID"]);
  }
  return $k;
}

function GetNumberOfRelations($ClassID)
{
  $mysql = pdodb::getInstance();
  $query = "select count(*) as tcount from projectmanagement.OntologyObjectPropertyRestriction
where RelationStatus='VALID' and (DomainClassID=? or RangeClassID=?)";

  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($ClassID, $ClassID));
  $rec = $res->fetch();
  return $rec["tcount"];
}

function GetNumberOfIndirectRelations($ClassID)
{
  $ParentIDs = GetAllParentsID(1, $ClassID);
  if($ParentIDs=="")
  	return 0;
  $mysql = pdodb::getInstance();
  $query = "select count(*) as tcount from projectmanagement.OntologyObjectPropertyRestriction
where RelationStatus='VALID' and (DomainClassID in (".$ParentIDs.") or RangeClassID in (".$ParentIDs."))";

  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($ClassID, $ClassID));
  $rec = $res->fetch();
  return $rec["tcount"];
}


function GetNumberOfProperties($OntologyID, $ClassTitle)
{
  $mysql = pdodb::getInstance();
  $query = "select count(*) as tcount from projectmanagement.OntologyProperties
where 
(domain like '".$ClassTitle.",%' or domain like '%,".$ClassTitle.",%' or domain like '".$ClassTitle."' or domain like '%,".$ClassTitle."')
and PropertyType='DATATYPE' and OntologyID=?";

  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($OntologyID));
  $rec = $res->fetch();
  return $rec["tcount"];
}

function GetAllParentsID($Level, $OntologyClassID)
{
  $mysql = pdodb::getInstance();
  if($Level>20)
    return "";

 $query = "select OntologyClassID
    from projectmanagement.OntologyClassHirarchy 
    where OntologyClassHirarchy.OntologyClassParentID=?";
  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($OntologyClassID));
  $k = 0;
  $plist = "";
  while($rec = $res->fetch())
  {
  	if($plist!="")
  		$plist .= ", ";
  	$plist .= $rec["OntologyClassID"];
    	$pplist = GetAllParentsID($Level+1, $rec["OntologyClassID"]);
    	if($pplist!="")
    		$plist .= ", ";
    	$plist .= $pplist;
    		
  }
  return $plist;
}

function GetAllChildsID($Level, $OntologyClassID)
{
  $mysql = pdodb::getInstance();
  if($Level>20)
    return "";

 $query = "select OntologyClassParentID
    from projectmanagement.OntologyClassHirarchy 
    where OntologyClassHirarchy.OntologyClassID=?";
  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($OntologyClassID));
  $k = 0;
  $plist = "";
  while($rec = $res->fetch())
  {
  	if($plist!="")
  		$plist .= ", ";
  	$plist .= $rec["OntologyClassParentID"];
    	$pplist = GetAllChildsID($Level+1, $rec["OntologyClassParentID"]);
    	if($pplist!="")
    		$plist .= ", ";
    	$plist .= $pplist;
    		
  }
  return $plist;
}

function GetNumberOfIndirectProperties($OntologyID, $OntologyClassID)
{
  $ParentList = GetAllParentsID(1, $OntologyClassID);
  if($ParentList=="")
  	return 0;
  $mysql = pdodb::getInstance();
  $query = "select * from projectmanagement.OntologyClasses where OntologyClassID in (".$ParentList.")";
  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array());
  $tcount = 0;
  while($rec = $res->fetch())
  {
  	$ClassTitle = $rec["ClassTitle"];
	  $query = "select count(*) as tcount from projectmanagement.OntologyProperties
	where 
	(domain like '".$ClassTitle.",%' or domain like '%,".$ClassTitle.",%' or domain like '".$ClassTitle."' or domain like '%,".$ClassTitle."')
	and PropertyType='DATATYPE' and OntologyID=?";
	
	  $mysql->Prepare($query);
	  $res2 = $mysql->ExecuteStatement(array($OntologyID));
	  $rec2 = $res2->fetch();
	  $tcount += $rec2["tcount"];
  }
  return $tcount;
}

function ResetSubGraphs($OntologyID)
{
	$mysql = pdodb::getInstance();
	$query = "delete from projectmanagement.OntologySubGraph where OntologyID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($OntologyID));
}

// آیا یک کلاس در یک زیر گراف مشخص قرار دارد یا خیر - با ارسال کد سرگروه و کد کلاس مربوطه
function IsClassExistInSubGraph($OntologyID, $MainClassID, $ClassID)
{
	  $mysql = pdodb::getInstance();
	$mysql->Prepare("select * from projectmanagement.OntologySubGraph where OntologyID=? and SubGraphClassMember=?");
	$res = $mysql->ExecuteStatement(array($OntologyID, $ClassID));
	if($rec = $res->fetch())
		return true;
	return false;
}

function GetClassLabel($ClassID)
{
	$mysql = pdodb::getInstance();
	$mysql->Prepare("select * from projectmanagement.OntologyClassLabels where OntologyClassID=?");
	$res = $mysql->ExecuteStatement(array($ClassID));
	if($rec = $res->fetch())
	{
		return $rec["label"];
	}
	return "";
}

function InsertAllRelatedClass($OntologyID, $MainClassID, $ClassID, $Level)
{
	if($Level>2000)
	{
		echo "****************";
		return;
	}
	echo "<br>Level: ".$Level."<br>";
	echo "افزودن نود به زیر گراف: ".GetClassLabel($MainClassID)."-".GetClassLabel($ClassID)."<br>";
	  $mysql = pdodb::getInstance();

	$mysql->Prepare("insert into projectmanagement.OntologySubGraph (OntologyID, SubGraphClassHeader, SubGraphClassMember) values (?, ?, ?)");
	$mysql->ExecuteStatement(array($OntologyID, $MainClassID, $ClassID));
	  
	  $query = "select * from projectmanagement.OntologyObjectPropertyRestriction
	where RelationStatus='VALID' and DomainClassID=?"; // گراف جهت دار است. فقط نودهایی که از این نود می توان به آنها حرکت کرد
	
	  $mysql->Prepare($query);
	  $res = $mysql->ExecuteStatement(array($ClassID));
	  // کلاسهایی که دارای روابط معنایی با کلاس انتخاب شده هستند به گروه اضافه می کند
	  echo "کلاسهای مرتبط با [".GetClassLabel($ClassID)."] رابطه غیر سلسله مراتبی: <br>";
	  while($rec = $res->fetch())
	  {
	  	$OtherClassID = $rec["RangeClassID"];
	  	echo GetClassLabel($OtherClassID);	  		
		if(!IsClassExistInSubGraph($OntologyID, $MainClassID, $OtherClassID))
		{
			echo "(+) <br>";
		  	InsertAllRelatedClass($OntologyID, $MainClassID, $OtherClassID, $Level+1);
		}
		else
			echo "<br>";		
	  }

// برای فرزندان کلاس انتخاب شده هم بررسی می کند اگر وجود داشتند در گروه کلاس اضافه می شوند
	$ChildsList = GetAllChildsID(1, $ClassID);
	if($ChildsList!="")
	{

	  $query = "select * from projectmanagement.OntologyClasses 
	  LEFT JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)
	  where OntologyClassID in (".$ChildsList.")";
	  $res = $mysql->Execute($query);
	  echo "فرزندان: [".GetClassLabel($ClassID)."]<br>";
	  while($rec = $res->fetch())
	  {
	  	echo $rec["label"]." ";
		if(!IsClassExistInSubGraph($OntologyID, $MainClassID, $rec["OntologyClassID"]))
		{
			echo "(+) <br>";
		  	InsertAllRelatedClass($OntologyID, $MainClassID, $rec["OntologyClassID"], $Level+1);
		}
		else	
			echo "<br>";			
	  }
	  echo "<br>";
	}
// برای پدران کلاس انتخاب شده هم بررسی می کند اگر وجود داشتند در گروه کلاس اضافه می شوند
	$ParentsList = GetAllParentsID(1, $ClassID);
	if($ParentsList!="")
	{
	  echo "پدران: [".GetClassLabel($ClassID)."]<br>";
	  $query = "select * from projectmanagement.OntologyClasses 
	  LEFT JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)
	  where OntologyClassID in (".$ParentsList.")";
	  $res = $mysql->Execute($query);
	  while($rec = $res->fetch())
	  {
		  echo $rec["label"]." ";
		if(!IsClassExistInSubGraph($OntologyID, $MainClassID, $rec["OntologyClassID"]))
		{
			echo "(+) <br>";
		  	InsertAllRelatedClass($OntologyID, $MainClassID, $rec["OntologyClassID"], $Level+1);
		 }
		 else
			 echo "<br>";
	  }
	  echo "<br>";
	}
	
	echo "----- خاتمه بررسی نودهای مرتبط با کلاس [".GetClassLabel($ClassID)."]------<br>";
}


function CreateSubGraph($OntologyID)
{
	$mysql = pdodb::getInstance();
	$query = "select * from projectmanagement.OntologyClasses 
	LEFT JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)
	where OntologyID=? and OntologyClassID not in 
	(select SubGraphClassMember from projectmanagement.OntologySubGraph where OntologyID=?) and  OntologyClassID not in 
	(select SubGraphClassHeader from projectmanagement.OntologySubGraph where OntologyID=?) ";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($OntologyID, $OntologyID, $OntologyID));
	// برای یکی از مفاهیمی که جزو هیچ زیر گرافی نیست
	if($rec = $res->fetch())
	{
		echo "سر گروه زیر گراف: ".$rec["label"]."<br>";
		
		// کلاس اول را به عنوان نماینده گروه ثبت می کند و اولین عضو آن گروه هم خود کلاس است
		InsertAllRelatedClass($OntologyID, $rec["OntologyClassID"], $rec["OntologyClassID"], 1);
		return true;
	}
	return false;
}


function CaculateSubGraphs($OntologyID)
{
	// تا زمانیکه کلاسی باقی مانده باشد که جزو زیر گراف های شناسایی شده نباشد
	// حداکثر تا ۱۰۰ زیر گراف
	while(CreateSubGraph($OntologyID) && $i<200)
	{
		$i++;
	}
}


function CalculateClasses($OntologyID)
{
	$mysql = pdodb::getInstance();
	$mysql->Execute("delete from projectmanagement.OntologyClassAnalysis");
	
	$query = "select * from projectmanagement.OntologyClasses 
	JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)
	where OntologyID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($OntologyID));
	
	while($rec = $res->fetch())
	{
	
		$childs = GetNumberOfChilds(1, $rec["OntologyClassID"]);
		$relations = GetNumberOfRelations($rec["OntologyClassID"]);
		$props = GetNumberOfProperties($OntologyID, $rec["ClassTitle"]);
		$IndirectRelations = GetNumberOfIndirectRelations($rec["OntologyClassID"]);
		$IndirectProps = GetNumberOfIndirectProperties($rec["OntologyID"], $rec["OntologyClassID"]);
		$query = "insert into projectmanagement.OntologyClassAnalysis (OntologyClassID, OntologyID, NumberOfChilds, NumberOfRelations, NumberOfProperties, NumberOfIndirectRelations, NumberOfIndirectProperties) values (?, ?, ?, ?, ?, ?, ?)";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($rec["OntologyClassID"], $OntologyID, $childs, $relations, $props, $IndirectRelations, $IndirectProps));
	
	}
	
}

function ShowCalculationResult()
{
echo "<table>";
echo "<tr class=HeaderOfTable>";
echo "<td>مفهوم</td><td>تعداد فرزند</td><td>تعداد روابط مستقیم</td><td>تعداد روابط غیر مستقیم</td><td>تعداد خصوصیت مستقیم</td><td>تعداد خصوصیت غیر مستقیم</td><td>مجموع روابط</td><td>مجموع خصوصیات</td>";
echo "</tr>";
$mysql = pdodb::getInstance();
$res = $mysql->Execute("select * from projectmanagement.OntologyClassAnalysis
JOIN projectmanagement.OntologyClassLabels using (OntologyClassID) order by NumberOfChilds DESC, NumberOfIndirectRelations+NumberOfRelations DESC
");
while($rec = $res->fetch())
{
	echo "<tr>";
	echo "<td>";
	echo $rec["label"];
	echo "</td>";
	echo "<td>";
	echo $rec["NumberOfChilds"];
	echo "</td>";
	echo "<td>";
	echo $rec["NumberOfRelations"];
	echo "</td>";
	echo "<td>";
	echo $rec["NumberOfIndirectRelations"];
	echo "</td>";
	echo "<td>";
	echo $rec["NumberOfProperties"];
	echo "</td>";
	echo "<td>";
	echo $rec["NumberOfIndirectProperties"];
	echo "</td>";
	echo "<td>";
	echo $rec["NumberOfIndirectRelations"]+$rec["NumberOfRelations"];
	echo "</td>";
	echo "<td>";
	echo $rec["NumberOfProperties"]+$rec["NumberOfIndirectProperties"];
	echo "</td>";
	echo "</tr>";
}
echo "</table>";
}

function ShowSubGraphs($OntologyID)
{
	$mysql = pdodb::getInstance();
	$mysql->Prepare("select SubGraphClassHeader, label, count(*) as tcount from projectmanagement.OntologySubGraph 
	LEFT JOIN projectmanagement.OntologyClassLabels on (OntologyClassID=SubGraphClassHeader)
	where OntologyID=? group by SubGraphClassHeader");
	$res = $mysql->ExecuteStatement(array($OntologyID));
	while($rec = $res->fetch())
	{
		echo $rec["label"]." - ".$rec["SubGraphClassHeader"]." (".$rec["tcount"].")<br>";
	} 
}

function ShowRootNodes($OntologyID)
{
	$mysql = pdodb::getInstance();
	$mysql->Prepare("select * from projectmanagement.OntologyClasses 
JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)
where OntologyID=?
and OntologyClassID not in 
(select OntologyClassParentID from projectmanagement.OntologyClassHirarchy)
and OntologyClassID not in
(select RangeClassID from projectmanagement.OntologyObjectPropertyRestriction)");
	$res = $mysql->ExecuteStatement(array($OntologyID));
	while($rec = $res->fetch())
	{
		echo $rec["label"]."<br>";
	} 

}

$OntologyID = $_REQUEST["OntologyID"];
ShowRootNodes($OntologyID);
die();

ResetSubGraphs($OntologyID);
CaculateSubGraphs($OntologyID, 1);
ShowSubGraphs($OntologyID);
die();

CalculateClasses($OntologyID);
ShowCalculationResult();


?>

</html>
