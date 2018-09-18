<?php 
/*
مقایسه دو هستان نگار
*/
include("header.inc.php");
include "classes/MergeOntology.class.php";

function Normalizing($str)
{
	$new_str=str_replace( "‌"," " ,$str);
	$new_str=str_replace( 'ك', 'ک' ,$new_str);
	// 	$new_str=str_replace( 'ه‌', 'ه' ,$new_str);
	$new_str=str_replace( 'ي', 'ی' ,$new_str);
	
	$new_str=str_replace( 'أ', 'ا' ,$new_str);
	$new_str=str_replace( 'إ', 'ا' ,$new_str);
	// 	$new_str=str_replace( 'ئ', 'ی' ,$new_str);
	$new_str=str_replace( 'ؤ', 'و' ,$new_str);
	$new_str=str_replace( 'ؤ', 'و' ,$new_str);
	// 	$new_str=str_replace( "  "," " ,$new_str);
	
	// 	$new_str=str_replace( "‌"," " ,$new_str);
	$new_str=html_entity_decode(str_replace("&zwj;","",htmlentities($new_str)));
	// 	$new_str=html_entity_decode(str_replace("&zwnj;"," ",htmlentities($new_str)));
	// 	$new_str=str_replace('&lrm;',html_entity_decode('&lrm;') ,$new_str);
	// 	$new_str=str_replace( 'هه','ه ه' ,$new_str);
	$new_str=str_replace( "  "," " ,$new_str);
	
	return($new_str);
	}

function GetOntologyTitle($OntologyID)
{
	$mysql = pdodb::getInstance();
	$mysql->Prepare("select OntologyTitle, comment from projectmanagement.ontologies where OntologyID=?");
	$res = $mysql->ExecuteStatement(array($OntologyID));
	$rec = $res->fetch();
	return $rec["OntologyTitle"]." (".substr($rec["comment"], 0, 100).")";
}

function GetClassListLabels($OntologyID, $ClassListString)
{
  $ret = "";
  $mysql = pdodb::getInstance();
  $ClassList = explode(", ",$ClassListString);
  for($i=0; $i<count($ClassList); $i++)
  {
    $mysql->Prepare("select * from projectmanagement.OntologyClasses JOIN projectmanagement.OntologyClassLabels using (OntologyClassID) where ClassTitle=? and OntologyID=?");
    $res = $mysql->ExecuteStatement(array($ClassList[$i], $OntologyID));
    if($rec = $res->fetch())
    {
      $ret .= $rec["label"]." - ";
    }
  }
  return $ret;
}

function GetOntologyList()
{
	$ret = "";
	$mysql = pdodb::getInstance();
	$res = $mysql->Execute("select OntologyID, OntologyTitle, comment from projectmanagement.ontologies");
	while($rec = $res->fetch())
	{
		$ret .= "<option value='".$rec["OntologyID"]."'>";
		$ret .= $rec["OntologyTitle"]." (".substr($rec["comment"], 0, 100).")";
	}
	return $ret;
}

function ClearMappingCache($OntologyID1, $OntologyID2)
{
	$mysql = pdodb::getInstance();
	$mysql->Prepare("delete from projectmanagement.OntologyClassMapping where OntologyID=? and MappedOntologyID=?");
	$mysql->ExecuteStatement(array($OntologyID1, $OntologyID2));

	$mysql->Prepare("delete from projectmanagement.OntologyPropertyMapping where OntologyID=? and MappedOntologyID=?");
	$mysql->ExecuteStatement(array($OntologyID1, $OntologyID2));

}

function GetTermSynSetIDs($TermTitle)
{
  $ret = "0";
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select synSetID from ferdowsnet.sense 
			    JOIN ferdowsnet.persianwords on (sense.pewordID=persianwords.ID)
			    where word='".$TermTitle."'");
  $i = 0;
  while($rec = $res->fetch())
  {
    $ret .= ",";
    $ret .= $rec["synSetID"];
    $i++;
  }
  return $ret;
}

function IsSimilarBySemantic($Term1, $Term2, $SynSetIDs)
{
  //return "Disabled";
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select distinct TermID, TermTitle from ferdowsnet.sense 
			    JOIN ferdowsnet.persianwords on (sense.pewordID=persianwords.ID)
			    JOIN projectmanagement.terms on (TermTitle=word)
			    where synSetID in (".$SynSetIDs.") and TermTitle='".$Term2."'");
  if($rec = $res->fetch())
    return true;
  return false;
}


function IsSimilarBySyntax($Term1, $Term2, $thereshold)
{
	if($Term1=="" || $Term2=="")
		return false;
// در سه حالت از نظر ساختاری مشابه ارزیابی می کند: ۱- فاصله لونشتین ۲- وجود رشته ی اول در دوم ۳- وجود رشته دوم در اول
    $distance = levenshtein($Term1, $Term2);
    $p = (1-($distance/max(mb_strlen($Term1, 'utf8'), mb_strlen($Term1, 'utf8'))) )*100;
    if($p>$thereshold)
    	return true;
    	// اگر واژه خیلی کوچک باشد احتمالا در واژه های زیادی وجود دارد
    if(mb_strlen($Term1, 'utf8')>3 && mb_strlen($Term2, 'utf8')>3)
    {
	    $pos = strpos($Term1, $Term2);
	    if($pos !== false)
	    {
	    	return true;
	    }
	    $pos = strpos($Term2, $Term1);
	    if($pos !== false)
	    {
	    	return true;
	    }
    }
    return false;
}

// بالای جدول را برای نگاشت یک عنصر می سازد که در آن لینک به صفحه ویرایش آن عنصر و همچنین
// لینک برای انتخاب عنصر معادل در هستان نگار مقصد به صورت دستی وجود دارد
// EntityType: نوع عنصری که قرار است نگاشت شود
function CreateEntityMappingHeader($SourceOntologyID, $EntityType, $EntityID, $Term1, $OntologyID)
{
	$TableID = "table_".$EntityType."_".$EntityID;
	$HeaderOfTable = "<table width=80% align=center style='display: ' id='".$TableID."'>";
	$HeaderOfTable .= "<tr bgcolor=#cccccc>";
	$HeaderOfTable .= "<td> ";

$HeaderOfTable .=  " <img title='معادلی ندارد' src='images/delete.png' border=0 onclick='javascript: Reject(".$SourceOntologyID.", ".$EntityID.", \"".$EntityType."\", ".$OntologyID.");'> ";
	
	$HeaderOfTable .= " ";
	if($EntityType=="CLASS")
		$HeaderOfTable .= "<a target=_blank href='ManageOntologyClasses.php?UpdateID=".$EntityID."&OntologyID=".$SourceOntologyID."&OnlyEditForm=1'>";
	else if($EntityType=="PROPERTY")
		$HeaderOfTable .= "<a target=_blank href='ManageOntologyProperties.php?UpdateID=".$EntityID."&OntologyID=".$SourceOntologyID."&DoNotShowList=1'>";
	$HeaderOfTable .= $Term1;
	$HeaderOfTable .=  "</a> ";
	
	if($EntityType=="CLASS")
		$HeaderOfTable .=  " (کلاس) ";
	if($EntityType=="PROPERTY")
		$HeaderOfTable .=  " (خصوصیت) ";
	
	$HeaderOfTable .=  " به ";
		/*******/
	$HeaderOfTable .=  "<a href='ShowOntologyClassNProps.php?OntologyID=".$OntologyID."&MapOntologyID=".$SourceOntologyID."&MapEntity=".$EntityType."&MapID=".$EntityID."' target=_blank>[انتخاب]</a> ";

	
	
	
	$HeaderOfTable .= "<span id='span_".$EntityID."'></span>";
	$HeaderOfTable .=  "</td>";
	$HeaderOfTable .=  "</tr>";
	return $HeaderOfTable;
}

// فهرست کلاسهایی که با نام مشابه در هستان نگار طرف نگاشت هستند نمایش می دهد
// EntityType: نوع عنصر در هستان نگار مبدا
// EntityID: کد عنصر در هستان نگار مبدا
// Term1: برچسب نرمال سازی شده عنصر در هستان نگار مبدا
// SynSetIDs: کد مجموعه مترادفها در فردوس نت برایبرچسب عنصر مبدا
// OntologyID: هستان نگار مقصد
function CreateEntitySimilarClasses($SourceOntologyID, $EntityType, $EntityID, $Term1, $SynSetIDs, $OntologyID)
{
	$row = 0;
	$mysql = pdodb::getInstance();
	$query = "select OntologyClassID, label from projectmanagement.OntologyClasses 
		JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)
		where OntologyID=? and label<>''";
	$mysql->Prepare($query, true);	
	$res = $mysql->ExecuteStatement(array($OntologyID));		
	
	$TableContent = "";
	while($rec = $res->fetch())
	{
		$Term2 = Normalizing($rec["label"]);
		if(IsSimilarBySyntax($Term1, $Term2, 75) || IsSimilarBySemantic($Term1, $Term2, $SynSetIDs))
		{
			$row++;
			if($EntityType=="CLASS")
			{
				$TrID = "tr_c_".$EntityID."_".$row;	
			}
			else if($EntityType=="PROPERTY")
			{
				$TrID = "tr_p_".$EntityID."_".$row;	
			}
			$TableContent .= "<tr id='".$TrID."' style='display: '>";
			$TableContent .= "<td>";
			$TableContent .= " <a href=\"javascript: Accept('".$TrID."', '".$SourceOntologyID."', '".$EntityID."', '".$EntityType."', '".$OntologyID."', '".$rec["OntologyClassID"]."', 'CLASS');\"> [قبول]</a> ";
			$TableContent .= "<a target=_blank href='ManageOntologyClasses.php?UpdateID=".$rec["OntologyClassID"]."&OntologyID=".$OntologyID."&OnlyEditForm=1'>";
			$TableContent .= $Term2."</a> (کلاس)";

			
			$TableContent .= "</td>";
			$TableContent .= "</tr>";
		}
	}
	return $TableContent;
}


// فهرست خصوصیات که با نام مشابه در هستان نگار طرف نگاشت هستند نمایش می دهد
// EntityType: نوع عنصر در هستان نگار مبدا
// EntityID: کد عنصر در هستان نگار مبدا
// Term1: برچسب نرمال سازی شده عنصر در هستان نگار مبدا
// SynSetIDs: کد مجموعه مترادفها در فردوس نت برایبرچسب عنصر مبدا
// OntologyID: هستان نگار مقصد
function CreateEntitySimilarProperties($SourceOntologyID, $EntityType, $EntityID, $Term1, $SynSetIDs, $OntologyID)
{
	$row = 0;
	$mysql = pdodb::getInstance();
$query = "select OntologyPropertyID, label, domain from projectmanagement.OntologyProperties 
		JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID)
		where OntologyID=? ";	
	$mysql->Prepare($query, true);
	$res = $mysql->ExecuteStatement(array($OntologyID));
		
	$TableContent = "";
	while($rec = $res->fetch())
	{
		$Term2 = Normalizing($rec["label"]);
		if(IsSimilarBySyntax($Term1, $Term2, 85) || IsSimilarBySemantic($Term1, $Term2, $SynSetIDs))
		{
			$row++;
			if($EntityType=="CLASS")
			{
				$TrID = "tr_c_".$EntityID."_".$row;	
			}
			else if($EntityType=="PROPERTY")
			{
				$TrID = "tr_p_".$EntityID."_".$row;	
			}
			$TableContent .= "<tr id='".$TrID."' style='display: '>";
			$TableContent .= "<td>";
			
			
			$TableContent .= "<a href=\"javascript: Accept('".$TrID."', '".$SourceOntologyID."', '".$EntityID."', '".$EntityType."', '".$OntologyID."', '".$rec["OntologyPropertyID"]."', 'PROP');\"> [قبول]</a> ";
			 $TableContent .= "<a target=_blank href='ManageOntologyProperties.php?UpdateID=".$rec["OntologyPropertyID"]."&OntologyID=".$OntologyID."&DoNotShowList=1'>";
						$TableContent .= $Term2."</a> (خصوصیت کلاس ".GetClassListLabels($OntologyID, $rec["domain"]).") ";			
			
			
			$TableContent .= "</td>";
			$TableContent .= "</tr>";
		}
	}
	return $TableContent;
}


// فهرست مقادیر مجاز خصوصیتها که با نام مشابه در هستان نگار طرف نگاشت هستند نمایش می دهد
// EntityType: نوع عنصر در هستان نگار مبدا
// EntityID: کد عنصر در هستان نگار مبدا
// Term1: برچسب نرمال سازی شده عنصر در هستان نگار مبدا
// SynSetIDs: کد مجموعه مترادفها در فردوس نت برایبرچسب عنصر مبدا
// OntologyID: هستان نگار مقصد
function CreateEntitySimilarPropertyValues($SourceOntologyID, $EntityType, $EntityID, $Term1, $SynSetIDs, $OntologyID)
{
	$row = 0;
	$mysql = pdodb::getInstance();
	$row = 0;
	$query = "select OntologyProperties.OntologyPropertyID,OntologyPropertyPermittedValueID, PermittedValue, label from projectmanagement.OntologyPropertyPermittedValues 
		JOIN projectmanagement.OntologyProperties using (OntologyPropertyID)
		JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID)
		where OntologyID=? ";	
	$mysql->Prepare($query, true);
	$res = $mysql->ExecuteStatement(array($OntologyID));
		
	$TableContent = "";
	
	while($rec = $res->fetch())
	{
		$Term2 = Normalizing($rec["PermittedValue"]);
		if(IsSimilarBySyntax($Term1, $Term2, 70) || IsSimilarBySemantic($Term1, $Term2, $SynSetIDs))
		{
			$row++;
			if($EntityType=="CLASS")
			{
				$TrID = "tr_c_".$EntityID."_".$row;	
			}
			else if($EntityType=="PROPERTY")
			{
				$TrID = "tr_p_".$EntityID."_".$row;	
			}
			
			$TableContent .= "<tr id='".$TrID."' style='display: '>";
			$TableContent .= "<td>";
			$TableContent .= "<a target=_blank href='ManageOntologyProperties.php?UpdateID=".$rec["OntologyPropertyID"]."&OntologyID=".$OntologyID."&DoNotShowList=1'>";
			$TableContent .= $Term2."</a> (داده مجاز خصوصیت ".$rec["label"].") ";
			$TableContent .= "<a href=\"javascript: Accept('".$TrID."', '".$SourceOntologyID."', '".$EntityID."', '".$EntityType."', '".$OntologyID."', '".$rec["OntologyPropertyPermittedValueID"]."', 'DATA_PROP'); \">قبول </a>";			
			$TableContent .= "</td>";
			$TableContent .= "</tr>";
			
		}
	}	
	
	return $TableContent;
}


function FindAndSaveClassMapping($OntologyID1, $ClassID, $ClassLabel, $OntologyID2)
{
	if($ClassLabel=="")
		return;
		
	$mysql = pdodb::getInstance();
	$query = "select OntologyClassID from projectmanagement.OntologyClasses 
		JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)
		where OntologyID=? and label=?";	
	$mysql->Prepare($query, true);
	$res = $mysql->ExecuteStatement(array($OntologyID2, $ClassLabel), PDO::FETCH_ASSOC, true);
	// ابتدا به دنبال برچسب یکسان در کلاسهای هستان نگار دوم می گردد
	if($rec = $res->fetch())
	{
		$mysql->Prepare("insert into projectmanagement.OntologyClassMapping (OntologyID, OntologyClassID, MappedOntologyID, MappedOntologyEntityID, MappedOntologyEntityType) values (?, ?, ?, ?, ?)", true);
		$mysql->ExecuteStatement(array($OntologyID1, $ClassID, $OntologyID2, $rec["OntologyClassID"], 'CLASS'), PDO::FETCH_ASSOC, true);
		return;
	}	
	$row = 0;
	$Term1 = Normalizing($ClassLabel);
	$SynSetIDs = GetTermSynSetIDs($Term1);
	
	$HeaderOfTable = CreateEntityMappingHeader($OntologyID1, "CLASS", $ClassID, $Term1, $OntologyID2);
	
	// اگر کلاس با نام یکسان پیدا نکرد به دنبال کلاس با نام مشابه می گردد
	$TableContent = CreateEntitySimilarClasses($OntologyID1, "CLASS", $ClassID, $Term1, $SynSetIDs, $OntologyID2);
	
	// خصوصیت با برچسب مشابه 
	$TableContent .= CreateEntitySimilarProperties($OntologyID1, "CLASS", $ClassID, $Term1, $SynSetIDs, $OntologyID2);

	$TableContent .= CreateEntitySimilarPropertyValues($OntologyID1, "CLASS", $ClassID, $Term1, $SynSetIDs, $OntologyID2);
	
	echo $HeaderOfTable;	
	if($TableContent!="")
	{
		echo $TableContent;
	}
	else
	{
		echo "<tr><td>-</td></tr>";
	}
	echo "<table>";	
}


function FindAndSavePropertyMapping($OntologyID1, $PropertyID, $PropertyLabel, $OntologyID2)
{
	if($PropertyLabel=="")
		return;
	$mysql = pdodb::getInstance();
	$row = 0;
	$Term1 = Normalizing($PropertyLabel);
	$SynSetIDs = GetTermSynSetIDs($Term1);

	$query = "select OntologyClassID, label from projectmanagement.OntologyClasses 
		JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)
		where OntologyID=? and label<>''";
	$mysql->Prepare($query, true);	
	$res = $mysql->ExecuteStatement(array($OntologyID2), PDO::FETCH_ASSOC, true);		
	
	$Term1 = Normalizing($PropertyLabel);
	$SynSetIDs = GetTermSynSetIDs($Term1);
	
	$HeaderOfTable = CreateEntityMappingHeader($OntologyID1, "PROPERTY", $PropertyID, $Term1, $OntologyID2);
	
	//  به دنبال کلاس با نام مشابه می گردد
	$TableContent = CreateEntitySimilarClasses($OntologyID1, "PROPERTY", $PropertyID, $Term1, $SynSetIDs, $OntologyID2);
	
	// خصوصیت با برچسب مشابه 
	$TableContent .= CreateEntitySimilarProperties($OntologyID1, "PROPERTY", $PropertyID, $Term1, $SynSetIDs, $OntologyID2);

	$TableContent .= CreateEntitySimilarPropertyValues($OntologyID1, "PROPERTY", $PropertyID, $Term1, $SynSetIDs, $OntologyID2);
	
	echo $HeaderOfTable;	
	if($TableContent!="")
	{
		echo $TableContent;
	}
	else
	{
		echo "<tr><td>-</td></tr>";
	}
	echo "<table>";	
	
}

function AddToClassMapping($OntologyID1, $ClassID, $OntologyID2, $EntityID, $EntityType)
{
	$mysql = pdodb::getInstance();
	
	$mysql->Prepare("select count(*) as tcount from projectmanagement.OntologyClassMapping where OntologyID=? and OntologyClassID=? and MappedOntologyID=? and  MappedOntologyEntityID=? and MappedOntologyEntityType=?", true);
	$res = $mysql->ExecuteStatement(array($OntologyID1, $ClassID, $OntologyID2, $EntityID, $EntityType));
	$rec = $res->fetch();
	if($rec["tcount"]==0)
	{
		$mysql->Prepare("insert into projectmanagement.OntologyClassMapping (OntologyID, OntologyClassID, MappedOntologyID, MappedOntologyEntityID, MappedOntologyEntityType) values (?, ?, ?, ?, ?)", true);
		$mysql->ExecuteStatement(array($OntologyID1, $ClassID, $OntologyID2, $EntityID, $EntityType));
	}
}

function AddToPropertyMapping($OntologyID1, $PropertyID, $OntologyID2, $EntityID, $EntityType)
{
	$mysql = pdodb::getInstance();
	
	$mysql->Prepare("select count(*) as tcount from projectmanagement.OntologyPropertyMapping where OntologyID=? and OntologyPropertyID=? and MappedOntologyID=? and  MappedOntologyEntityID=? and MappedOntologyEntityType=?", true);
	$res = $mysql->ExecuteStatement(array($OntologyID1, $PropertyID, $OntologyID2, $EntityID, $EntityType));
	$rec = $res->fetch();
	if($rec["tcount"]==0)
	{
		$mysql->Prepare("insert into projectmanagement.OntologyPropertyMapping (OntologyID, OntologyPropertyID, MappedOntologyID, MappedOntologyEntityID, MappedOntologyEntityType) values (?, ?, ?, ?, ?)", true);
		$mysql->ExecuteStatement(array($OntologyID1, $PropertyID, $OntologyID2, $EntityID, $EntityType));
	}
}

function AddToMapping($OntologyID1, $SourceID, $SourceEntityType, $OntologyID2, $EntityID, $EntityType)
{
	if($SourceEntityType=="CLASS")
		AddToClassMapping($OntologyID1, $SourceID, $OntologyID2, $EntityID, $EntityType);
	else if($SourceEntityType=="PROPERTY")
		AddToPropertyMapping($OntologyID1, $SourceID, $OntologyID2, $EntityID, $EntityType);
}

function FindAndSaveAllClassMappings($OntologyID1, $OntologyID2)
{
	$mysql = pdodb::getInstance();
	// ابتدا فهرست کلاسهایی که تاکنون نگاشت نشده اند را بدست می آورد
	$query = "select OntologyClassID, label from projectmanagement.OntologyClasses 
		JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)
		where OntologyID=? and OntologyClasses.OntologyClassID not in (
		select OntologyClassID from projectmanagement.OntologyClassMapping where
		OntologyID=? and MappedOntologyID=?)";
	$mysql->Prepare($query, true);
	$res = $mysql->ExecuteStatement(array($OntologyID1, $OntologyID1, $OntologyID2));

	while($rec = $res->fetch())
	{
		FindAndSaveClassMapping($OntologyID1, $rec["OntologyClassID"], $rec["label"], $OntologyID2);
	}
}

function FindAndSaveAllPropertyMappings($OntologyID1, $OntologyID2)
{
	$mysql = pdodb::getInstance();
	// ابتدا فهرست خصوصیاتی که تاکنون نگاشت نشده اند را بدست می آورد
	$query = "select OntologyPropertyID, label from projectmanagement.OntologyProperties 
		JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID)
		where OntologyID=? and OntologyProperties.OntologyPropertyID not in (
		select OntologyPropertyID from projectmanagement.OntologyPropertyMapping where
		OntologyID=? and MappedOntologyID=?)";
	$mysql->Prepare($query, true);
	$res = $mysql->ExecuteStatement(array($OntologyID1, $OntologyID1, $OntologyID2));

	while($rec = $res->fetch())
	{
		FindAndSavePropertyMapping($OntologyID1, $rec["OntologyPropertyID"], $rec["label"], $OntologyID2);
	}
}

function RemoveCachedData($OntologyID1, $OntologyID2)
{
	$mysql = pdodb::getInstance();
	$query = "delete from projectmanagement.OntologyClassMapping where
		OntologyID=? and MappedOntologyID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($OntologyID1, $OntologyID2));
}

function ShowCompareHeader($OntologyID1, $OntologyID2)
{
	echo "<table width=80% border=1 cellspacing=0 cellpadding=5 align=center>";
	echo "<tr>";
	echo "<td>";
	echo "نگاشت عناصر ";
	echo "<a href='ShowOntologyClassTree.php?OntologyID=".$OntologyID1."&OnlyView=1' target=_blank>";
	echo GetOntologyTitle($OntologyID1);
	echo "</a>";
	echo " به عناصر ";
	echo "<a href='ShowOntologyClassTree.php?OntologyID=".$OntologyID2."&OnlyView=1' target=_blank>";
	echo GetOntologyTitle($OntologyID2);
	echo "</a>";
	echo "</td>";
	echo "</tr>";
	echo "<tr><td align=center>";
	echo ShowBackButton();
	echo "</td></tr>";
	echo "</table>";
}

function CalculateClassCoveragePercentage($OntologyID1, $OntologyID2)
{
$mysql = pdodb::getInstance();
	$query = "select count(*) as tcount from projectmanagement.OntologyClasses where OntologyID=? and
		 OntologyClassID in (
		 select OntologyClassID from projectmanagement.OntologyClassMapping where
		OntologyClassMapping.OntologyID=? and MappedOntologyID=? and MappedOntologyEntityID<>'0')";
	$mysql->Prepare($query, true);
	$res = $mysql->ExecuteStatement(array($OntologyID1, $OntologyID1, $OntologyID2), PDO::FETCH_ASSOC, true);
	$rec = $res->fetch();
	$MappedClassCount = $rec["tcount"];
	$query = "select count(*) as tcount from projectmanagement.OntologyClasses where OntologyID=? ";
	$mysql->Prepare($query, true);
	$res = $mysql->ExecuteStatement(array($OntologyID1), PDO::FETCH_ASSOC, true);
	$rec = $res->fetch();
	$TotalClassCount = $rec["tcount"];
	return ($MappedClassCount*100)/$TotalClassCount;
}

function CalculatePropertyCoveragePercentage($OntologyID1, $OntologyID2)
{
$mysql = pdodb::getInstance();
	$query = "select count(*) as tcount from projectmanagement.OntologyProperties where OntologyID=? and
		 OntologyPropertyID in (
		 select OntologyPropertyID from projectmanagement.OntologyPropertyMapping where
		OntologyPropertyMapping.OntologyID=? and MappedOntologyID=? and MappedOntologyEntityID<>'0')";
	$mysql->Prepare($query, true);
	$res = $mysql->ExecuteStatement(array($OntologyID1, $OntologyID1, $OntologyID2), PDO::FETCH_ASSOC, true);
	$rec = $res->fetch();
	$MappedClassCount = $rec["tcount"];
	$query = "select count(*) as tcount from projectmanagement.OntologyProperties where OntologyID=? ";
	$mysql->Prepare($query, true);
	$res = $mysql->ExecuteStatement(array($OntologyID1), PDO::FETCH_ASSOC, true);
	$rec = $res->fetch();
	$TotalClassCount = $rec["tcount"];
	return ($MappedClassCount*100)/$TotalClassCount;
}


function ShowPropertyCompareResult($OntologyID1, $OntologyID2)
{
	$mysql = pdodb::getInstance();
	$query = "select OntologyPropertyMappingID, MappedOntologyEntityID, MappedOntologyEntityType, cl.label as clabel, cl2.label as clabel2, pl.label as plabel, pv.PermittedValue from projectmanagement.OntologyPropertyMapping 
		JOIN projectmanagement.OntologyProperties oc on (oc.OntologyPropertyID=OntologyPropertyMapping.OntologyPropertyID)
		JOIN projectmanagement.OntologyPropertyLabels cl on (cl.OntologyPropertyID=oc.OntologyPropertyID)
		LEFT JOIN projectmanagement.OntologyClasses oc2 on (oc2.OntologyClassID=OntologyPropertyMapping.MappedOntologyEntityID)
		LEFT JOIN projectmanagement.OntologyClassLabels cl2 on (cl2.OntologyClassID=oc2.OntologyClassID)
		LEFT JOIN projectmanagement.OntologyProperties op on (op.OntologyPropertyID=OntologyPropertyMapping.MappedOntologyEntityID)
		LEFT JOIN projectmanagement.OntologyPropertyLabels pl on (pl.OntologyPropertyID=op.OntologyPropertyID)
		LEFT JOIN projectmanagement.OntologyPropertyPermittedValues pv on (pv.OntologyPropertyPermittedValueID=OntologyPropertyMapping.MappedOntologyEntityID) 
		where
		OntologyPropertyMapping.OntologyID=? and MappedOntologyID=? and MappedOntologyEntityID<>'0'";
	$mysql->Prepare($query, true);
	$res = $mysql->ExecuteStatement(array($OntologyID1, $OntologyID2), PDO::FETCH_ASSOC, true);
	ShowCompareHeader($OntologyID1, $OntologyID2);
	echo "<table align=center><tr><td>";
	echo round(CalculatePropertyCoveragePercentage($OntologyID1, $OntologyID2), 2)."% از خصوصیات هستان نگار اول توسط هستان نگار دوم پوشش داده شده اند";
	echo "</td></tr></table>";
	echo "<table border=1 cellspacing=0 cellpadding=4 align=center width=80%>";
	echo "<tr class=HeaderOfTable>";
	echo "<td width=5%>ردیف</td><td>نام خصوصیت</td><td>عنصر نگاشت شده</td><td>نوع عنصر نگاشت شده</td>";
	echo "</tr>";
	$i=0;
	while($rec = $res->fetch())
	{
		$i++;
		echo "<tr>";
		echo "<td>".$i."</td>";
		echo "<td>";
		echo "<a href='CompareOntologies.php?OntologyID1=".$_REQUEST["OntologyID1"]."&OntologyID2=".$_REQUEST["OntologyID2"]."&ActionType=RemovePropMap&RemoveMapID=".$rec["OntologyPropertyMappingID"]."'><img src='images/delete.png'></a> ";
		echo $rec["clabel"]."</td>";
		echo "<td>";
		if($rec["MappedOntologyEntityType"]=="CLASS")
			echo $rec["clabel2"];
		else if($rec["MappedOntologyEntityType"]=="PROP")
			echo $rec["plabel"];
		else if($rec["MappedOntologyEntityType"]=="DATA_PROP")
			echo $rec["PermittedValue"];	
		echo "</td>";
		echo "<td>".$rec["MappedOntologyEntityType"]."</td>";
		echo "</tr>";
	}
	
$query = "select OntologyPropertyMappingID, MappedOntologyEntityID, MappedOntologyEntityType, cl.label as clabel from projectmanagement.OntologyPropertyMapping 
		JOIN projectmanagement.OntologyProperties oc on (oc.OntologyPropertyID=OntologyPropertyMapping.OntologyPropertyID)
		JOIN projectmanagement.OntologyPropertyLabels cl on (cl.OntologyPropertyID=oc.OntologyPropertyID)
		where
		OntologyPropertyMapping.OntologyID=? and MappedOntologyID=? and MappedOntologyEntityID='0'";
	$mysql->Prepare($query, true);
	$res = $mysql->ExecuteStatement(array($OntologyID1, $OntologyID2), PDO::FETCH_ASSOC, true);	
	
	while($rec = $res->fetch())
	{
		$i++;
		echo "<tr bgcolor=#efefef>";
		echo "<td>".$i."</td>";
		echo "<td>".$rec["clabel"]."</td>";
		echo "<td colspan=2>";
		echo "<a href='CompareOntologies.php?OntologyID1=".$_REQUEST["OntologyID1"]."&OntologyID2=".$_REQUEST["OntologyID2"]."&ActionType=RemovePropMap&RemoveMapID=".$rec["OntologyPropertyMappingID"]."'><img src='images/delete.png'></a> ";
		echo "معادلی ندارد</td>";
		echo "</tr>";
	}
		
	echo "<tr class=FooterOfTable><td align=center colspan=4>";
	echo ShowBackButton();
	echo "</td></tr>";
}

// درصد خصوصیات یک کلاس را که در هستان نگار دوم معادل خصوصیات همان کلاس هستندبر می گرداند
function CalculatedPropertiesSimilarityPercentage($OntologyClassID, $ClassTitle, $OntologyClassID2, $ClassTitle2)
{
// تکمیل نشده است
	$mysql = pdodb::getInstance();
	$res = $mysql->Execute("select * from projectmanagement.OntologyProperties where 
				(domain='".$ClassTitle."' or domain like '".$ClassTitle.",%' or domain like '%, ".$ClassTitle."' or domain like '%, ".$ClassTitle.",%') or
				(`range`='".$ClassTitle."' or `range` like '".$ClassTitle.",%' or `range` like '%, ".$ClassTitle."' or `range` like '%, ".$ClassTitle.",%') ");
	while($rec = $res->fetch())
	{
	}
}

function ShowClassCompareResult($OntologyID1, $OntologyID2)
{
	$mysql = pdodb::getInstance();
	$query = "select oc.OntologyClassID, OntologyClassMappingID, MappedOntologyEntityID, MappedOntologyEntityType, cl.label as clabel, cl2.label as clabel2, pl.label as plabel, pv.PermittedValue from projectmanagement.OntologyClassMapping 
		JOIN projectmanagement.OntologyClasses oc on (oc.OntologyClassID=OntologyClassMapping.OntologyClassID)
		JOIN projectmanagement.OntologyClassLabels cl on (cl.OntologyClassID=oc.OntologyClassID)
		LEFT JOIN projectmanagement.OntologyClasses oc2 on (oc2.OntologyClassID=OntologyClassMapping.MappedOntologyEntityID)
		LEFT JOIN projectmanagement.OntologyClassLabels cl2 on (cl2.OntologyClassID=oc2.OntologyClassID)
		LEFT JOIN projectmanagement.OntologyProperties op on (op.OntologyPropertyID=OntologyClassMapping.MappedOntologyEntityID)
		LEFT JOIN projectmanagement.OntologyPropertyLabels pl on (pl.OntologyPropertyID=op.OntologyPropertyID)
		LEFT JOIN projectmanagement.OntologyPropertyPermittedValues pv on (pv.OntologyPropertyPermittedValueID=OntologyClassMapping.MappedOntologyEntityID) 
		where
		OntologyClassMapping.OntologyID=? and MappedOntologyID=? and MappedOntologyEntityID<>'0'";
	$mysql->Prepare($query, true);
	$res = $mysql->ExecuteStatement(array($OntologyID1, $OntologyID2), PDO::FETCH_ASSOC, true);
	ShowCompareHeader($OntologyID1, $OntologyID2);
	echo "<table align=center><tr><td>";
	echo round(CalculateClassCoveragePercentage($OntologyID1, $OntologyID2), 2)."% از کلاسهای هستان نگار اول توسط هستان نگار دوم پوشش داده شده اند";
	echo "</td></tr></table>";
	echo "<table border=1 cellspacing=0 cellpadding=4 align=center width=80%>";
	echo "<tr class=HeaderOfTable>";
	echo "<td width=5%>ردیف</td><td>نام کلاس</td><td>عنصر نگاشت شده</td><td>نوع عنصر نگاشت شده</td>";
	echo "</tr>";
	$i=0;
	while($rec = $res->fetch())
	{
		$i++;
		echo "<tr>";
		echo "<td>".$i."</td>";
		echo "<td>";
		echo "<a href='CompareOntologies.php?OntologyID1=".$_REQUEST["OntologyID1"]."&OntologyID2=".$_REQUEST["OntologyID2"]."&ActionType=RemoveClassMap&RemoveMapID=".$rec["OntologyClassMappingID"]."'><img src='images/delete.png'></a> ";
		echo $rec["clabel"]."</td>";
		echo "<td>";
		if($rec["MappedOntologyEntityType"]=="CLASS")
			echo $rec["clabel2"];
		else if($rec["MappedOntologyEntityType"]=="PROP")
			echo $rec["plabel"];
		else if($rec["MappedOntologyEntityType"]=="DATA_PROP")
			echo $rec["PermittedValue"];	
		echo "</td>";
		echo "<td>".$rec["MappedOntologyEntityType"]."</td>";
		echo "</tr>";
	}
	
	// فهرست مواردی که کاربر عنوان کرده معادلی ندارند نشان می دهد
 	$query = "select OntologyClassMappingID, MappedOntologyEntityID, MappedOntologyEntityType, cl.label as clabel from projectmanagement.OntologyClassMapping 
		JOIN projectmanagement.OntologyClasses oc on (oc.OntologyClassID=OntologyClassMapping.OntologyClassID)
		JOIN projectmanagement.OntologyClassLabels cl on (cl.OntologyClassID=oc.OntologyClassID)
		where
		OntologyClassMapping.OntologyID=? and MappedOntologyID=? and MappedOntologyEntityID='0'";
	$mysql->Prepare($query, true);
	$res = $mysql->ExecuteStatement(array($OntologyID1, $OntologyID2), PDO::FETCH_ASSOC, true);
	//echo "<br>".$OntologyID2."<br>".$OntologyID1;
	while($rec = $res->fetch())
	{
		$i++;
		echo "<tr bgcolor=#efefef>";
		echo "<td>".$i."</td>";
		echo "<td>".$rec["clabel"]."</td>";
		echo "<td colspan=2>";
echo "<a href='CompareOntologies.php?OntologyID1=".$_REQUEST["OntologyID1"]."&OntologyID2=".$_REQUEST["OntologyID2"]."&ActionType=RemoveClassMap&RemoveMapID=".$rec["OntologyClassMappingID"]."'><img src='images/delete.png'></a> ";		
		echo "معادلی ندارد</td>";
		echo "</tr>";
	}		
	echo "<tr class=FooterOfTable><td align=center colspan=4>";
	echo ShowBackButton();
	echo "</td></tr>";
}

function ShowBackButton()
{
	echo "<input type=button value='بازگشت' onclick='javascript: document.location=\"";
	if(isset($_REQUEST["ActionType"]) && $_REQUEST["ActionType"]=="Show")
		echo "CompareAllOntos.php";
	else
		echo "CompareOntologies.php";
	echo "\";'>";
}

function RemovePropMapping($MapID)
{
	$mysql = pdodb::getInstance();
	$query = "delete from projectmanagement.OntologyPropertyMapping 
		where OntologyPropertyMappingID=?";
	$mysql->Prepare($query, true);
	$mysql->ExecuteStatement(array($MapID), PDO::FETCH_ASSOC, true);
}

function RemoveClassMapping($MapID)
{
	$mysql = pdodb::getInstance();
	$query = "delete from projectmanagement.OntologyClassMapping 
		where OntologyClassMappingID=?";
	$mysql->Prepare($query, true);
	$mysql->ExecuteStatement(array($MapID), PDO::FETCH_ASSOC, true);
}


HTMLBegin();

if(isset($_REQUEST["Ajax"]))
{
	AddToMapping($_REQUEST["OntologyID1"], $_REQUEST["SourceID"], $_REQUEST["SourceEntityType"], $_REQUEST["OntologyID2"], $_REQUEST["EntityID"], $_REQUEST["EntityType"]);
	die();
}

if(isset($_REQUEST["ActionType"]))
{
	$OntologyID1 = $_REQUEST["OntologyID1"];
	$OntologyID2 = $_REQUEST["OntologyID2"];
	if($_REQUEST["ActionType"]=="MAPPING")
	{
		if(isset($_REQUEST["RemoveCache"]))
		{
			RemoveCachedData($OntologyID1, $OntologyID2);	
		}
		ShowCompareHeader($OntologyID1, $OntologyID2);
		FindAndSaveAllClassMappings($OntologyID1, $OntologyID2);
		FindAndSaveAllPropertyMappings($OntologyID1, $OntologyID2);
	}
	else if($_REQUEST["ActionType"]=="APPLY")
	{
		// تا زمانیکه کلاسهایی پیدا کند که از طریق رابطه تعدی با هم معادلند آنها را ذخیره می کند
		while(MergeOntology::FindAllIndirectEqualClasses());
		while(MergeOntology::FindAllIndirectEqualProps());
		// متقارن ها را هم اضافه می کند
		MergeOntology::ApplySymmetryRuleOnMappings();
		echo " نگاشت کلاسها و خصوصیات بر اساس روابط تعدی و تقارنی تکمیل شد";
	}
	else
	{
		if($_REQUEST["ActionType"]=="RemovePropMap")
			RemovePropMapping($_REQUEST["RemoveMapID"]);
		if($_REQUEST["ActionType"]=="RemoveClassMap")
			RemoveClassMapping($_REQUEST["RemoveMapID"]);
			
		ShowClassCompareResult($OntologyID1, $OntologyID2);
		ShowPropertyCompareResult($OntologyID1, $OntologyID2);
	}
?>
<script>
// OntologyID1: هستان نگاری که عناصر آن باید نگاشت شود
// SourceID: کد عنصری که قرار است نگاشت شود (می تواند کلاس یا خصوصیت باشد)
// OntologyID2: هستان نگاری که نگاشت به عناصر آن صورت می گیرد
// EntityID: عنصری که به آن نگاشت شده است
// SourceEntityType: نوع عنصری که نگاشت شده است (کلاس یا خصوصیت)
// EntityType: نوع عنصری که به آن نگاشت شده است
function Accept(tr_id, OntologyID1, SourceID, SourceEntityType, OntologyID2, EntityID, EntityType)
{
	document.getElementById(tr_id).style.display = 'none';
	var params = "Ajax=1&OntologyID1="+OntologyID1+"&";
	params += "SourceEntityType="+SourceEntityType+"&";
	params += "SourceID="+SourceID+"&";
	params += "OntologyID2="+OntologyID2+"&";
	params += "EntityID="+EntityID+"&";
	params += "EntityType="+EntityType;
	var http = new XMLHttpRequest();
	http.open("POST", "CompareOntologies.php", true);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.setRequestHeader("Content-length", params.length);
	http.setRequestHeader("Connection", "close");
	  
	http.onreadystatechange = function()
	{
		if(http.readyState == 4 && http.status == 200)
		{  }
	}
	http.send(params);	
}

function Reject(OntologyID1, SourceID, SourceEntityType, OntologyID2)
{
	TableID = "table_"+SourceEntityType+"_"+SourceID;
	//alert(TableID);
	EntityLabel = "<font color=red>معادل ندارد</font>";
	var params = "Ajax=1&OntologyID1="+OntologyID1+"&";
	params += "SourceEntityType="+SourceEntityType+"&";
	params += "SourceID="+SourceID+"&";
	params += "OntologyID2="+OntologyID2+"&";
	params += "EntityID=0&EntityType=CLASS";
	var http = new XMLHttpRequest();
	http.open("POST", "CompareOntologies.php", true);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.setRequestHeader("Content-length", params.length);
	http.setRequestHeader("Connection", "close");
	  
	http.onreadystatechange = function()
	{
		if(http.readyState == 4 && http.status == 200)
		{ 
			//document.getElementById('span_'+SourceID).innerHTML='<b>'+EntityLabel+'</b>';
			document.getElementById(TableID).style.display='none';
		}
	}
	http.send(params);	
}
	
</script>
<?
	//CompareOntologies($_REQUEST["OntologyID1"], $_REQUEST["OntologyID2"]);
	die();
}
$OntologyList = GetOntologyList();
echo "<form method=post id=f1 name=f1>";
echo "<input type=hidden name=ActionType id=ActionType value='MAPPING'>";
echo "<table width=80% border=1 cellspacing=0 cellpadding=5>";
echo "<tr class=HeaderOfTable>";
echo "<td align=center>";
echo "مقایسه دو هستان نگار";
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td>مقایسه عناصر ";
echo "<select name=OntologyID1 dir=ltr>";
echo $OntologyList;
echo "</select> با "; 
echo "<select name=OntologyID2 dir=ltr>";
echo $OntologyList;
echo "</select>";
echo "<br>";
echo "<input type=checkbox name=RemoveCache id=RemoveCache value=1>نگاشت از پیش موجود پاک شود";
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td align=center>";
echo "<input type=submit value='انجام نگاشت نیمه خودکار'>";
echo " <input type=button value='نمایش نتایج مقایسه' onclick='javascript: ShowResult()'>";
echo " <input type=button value='اعمال تعدی و تقارنی' onclick='javascript: ApplyTransitive_Symmetry()'>";

echo "</td>";
echo "</tr>";
echo "</table>";
echo "</form>";
?>
<script>
	function ShowResult()
	{
		document.getElementById('ActionType').value='RESULT';
		document.getElementById('f1').submit();
	}
	function ApplyTransitive_Symmetry()
	{
		document.getElementById('ActionType').value='APPLY';
		document.getElementById('f1').submit();
	}

</script>