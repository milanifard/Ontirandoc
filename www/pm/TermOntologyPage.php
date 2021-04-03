<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/
include("header.inc.php");
include_once("classes/OntologyClasses.class.php");
include_once("classes/OntologyProperties.class.php");
include_once("classes/OntologyClassHirarchy.class.php");

$mysql = pdodb::getInstance();
$TargetOntologyID = 0;
$res = $mysql->Execute("select * from projectmanagement.TermMappingTargetOntology");
if($rec = $res->fetch())
{
  $TargetOntologyID = $rec["OntologyID"];
}
if($TargetOntologyID==0)
{
  echo "لطفا ابتدا هستان نگار مقصد برای مفهوم سازی واژگان را انتخاب کنید";
  die();
}

function CreatePermittedValueList($OntologyPropertyID)
{
  $mysql = pdodb::getInstance();
  $mysql->Prepare("select OntologyPropertyPermittedValueID, PermittedValue from projectmanagement.OntologyPropertyPermittedValues where OntologyPropertyID=?");
  $res = $mysql->ExecuteStatement(array($OntologyPropertyID));
  //echo "<select id='OntologyPropertyPermittedValueID' name='OntologyPropertyPermittedValueID'>";
  while($rec = $res->fetch())
  {
    echo "<option value='".$rec["OntologyPropertyPermittedValueID"]."'>".$rec["PermittedValue"]."</option>";
  }
  //echo "</select>";
}

function ShowSimilarTerm($TermID, $TermTitle, $thereshold)
{
  $mysql = pdodb::getInstance();
  $mysql->Prepare("select TermID, TermTitle from projectmanagement.terms where TermID<>?");
  $res = $mysql->ExecuteStatement(array($_REQUEST["TermID"]));
  while($rec = $res->fetch())
  {
    $distance = levenshtein($TermTitle, $rec["TermTitle"]);
    $p = (1-($distance/max(strlen($TermTitle), strlen($rec["TermTitle"]))) )*100;
    if($p>$thereshold)
      echo "<a href='TermOntologyPage.php?TermID=".$rec["TermID"]."'>".$rec["TermTitle"]."</a>, ";
  }
}

function ShowOntologyElement($TermTitle, $thereshold)
{
  //return "Disabled";
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select * from projectmanagement.OntologyClassLabels 
				JOIN projectmanagement.OntologyClasses using (OntologyClassID)
				JOIN projectmanagement.ontologies using (OntologyID) where OntologyID in (1,23,25,29,33,35,38,41,42,46)");
  echo "<br><b>کلاس: </b>";
  while($rec = $res->fetch())
  {
    $distance = levenshtein($TermTitle, $rec["label"]);
    $p = (1-($distance/max(strlen($TermTitle), strlen($rec["label"]))) )*100;
    if($p>$thereshold)
    {
      if($rec["OntologyID"]==$GLOBALS['TargetOntologyID'])
            echo "<font color=red>";

      echo $rec["OntologyTitle"].": ".$rec["ClassTitle"]." (".$rec["label"].") <br> ";
      if($rec["OntologyID"]==$GLOBALS['TargetOntologyID'])
            echo "</font>";
    }
  }
  echo "<br><b>خصوصیت/رابطه: </b>";
  $res = $mysql->Execute("select * from projectmanagement.OntologyPropertyLabels 
				JOIN projectmanagement.OntologyProperties using (OntologyPropertyID)
				JOIN projectmanagement.ontologies using (OntologyID)  where OntologyID in (1,23,25,29,33,35,38,41,42,46)");
  while($rec = $res->fetch())
  {
    $distance = levenshtein($TermTitle, $rec["label"]);
    $p = (1-($distance/max(strlen($TermTitle), strlen($rec["label"]))) )*100;
    if($p>$thereshold)
    {
      if($rec["OntologyID"]==$GLOBALS['TargetOntologyID'])
            echo "<font color=red>";
      echo $rec["OntologyTitle"].": ".$rec["PropertyTitle"]." (".$rec["label"].") <br> ";
      if($rec["OntologyID"]==$GLOBALS['TargetOntologyID'])
            echo "</font>";
    }
  }
  
}

function ShowCoOccuranceTerms($TermID)
{
  $ret = "";
  $mysql = pdodb::getInstance();
  $query = "select 
  IF(TermID2='".$TermID."', t1.TermTitle, t2.TermTitle) as TermTitle, 
  IF(TermID2='".$TermID."', t1.TermID, t2.TermID) as TermID ,
  frequency
  from projectmanagement.TermsCoOccure 
  JOIN projectmanagement.terms t1 on (t1.TermID=TermsCoOccure.TermID1)
  JOIN projectmanagement.terms t2 on (t2.TermID=TermsCoOccure.TermID2)
  where TermID1='".$TermID."' or TermID2='".$TermID."'
  order by frequency DESC";
  $res = $mysql->Execute($query); 
  while($rec = $res->fetch())
  {
    //echo "+";
    // $ret .= "<a href='?TermID=".$rec["TermID"]."'>";
    $ret .= $rec["TermTitle"]."(".$rec["frequency"].")";
    //$ret .= "</a>";
    $ret .= ", ";
  }
  echo $ret;
}

function ShowRelatedTerms($TermID)
{
  $ret = "";
  $mysql = pdodb::getInstance();
  echo "<table width=99% border=1 cellspacing=0 cellpadding=5>";
  $query = "select TermReferences.title, TermReferenceID, PageNum, ParagraphNo from projectmanagement.TermReferenceMapping
			      LEFT JOIN projectmanagement.TermReferences using (TermReferenceID)	
			    where TermID='".$TermID."'";
  $res = $mysql->Execute($query);
			    
  while($rec = $res->fetch())
  {
    $res2 = $mysql->Execute("select TermReferenceID, TermReferenceMappingID, TermID, TermTitle, IF(EntityType='CLASS', ClassTitle, PropertyTitle) as EntityTitle from projectmanagement.TermReferenceMapping 
			      JOIN projectmanagement.terms using (TermID)
			      LEFT JOIN projectmanagement.TermOntologyElementMapping using (TermID)
			      LEFT JOIN projectmanagement.OntologyProperties on (OntologyPropertyID=OntologyEntityID)
			      LEFT JOIN projectmanagement.OntologyClasses on (OntologyClassID=OntologyEntityID)
			      where PageNum='".$rec["PageNum"]."' and ParagraphNo='".$rec["ParagraphNo"]."' and TermReferenceID='".$rec["TermReferenceID"]."' order by TermReferenceID");
    echo "<tr>";
    echo "<td width=1% nowrap><b>".$rec["title"]."</b></td>"; 
    echo "<td>";
    while($rec2 = $res2->fetch())
    {
      $OntologyElement = "-";
      if($rec2["EntityTitle"]!="")
           $OntologyElement = $rec2["EntityTitle"];
      echo "<a target=_blank href='ManageTermReferenceMapping.php?UpdateID=".$rec2["TermReferenceMappingID"]."&TermReferenceID=".$rec2["TermReferenceID"]."'>";
      if($rec2["TermID"]==$TermID)
	echo "<font color=green><b>";
      echo $rec2["TermTitle"];
      if($rec2["TermID"]==$TermID)
	echo "</b></font>";
      echo "</a> ";
      echo "<a href='TermOntologyPage.php?TermID=".$rec2["TermID"]."'>(".$OntologyElement.")</a>";
      echo "<br>";
    }
    echo "</td>";
    echo "</tr>";
  }
  echo "</table>";
}

function ShowHypoTermsByStruct($TermID, $TermTitle)
{
  //return "Disabled";
  $ret = "";
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select TermID, TermTitle from projectmanagement.terms where TermID<>'".$TermID."' and TermTitle like '".$TermTitle."%' and TermID in (select TermID from projectmanagement.TermReferenceMapping)");
  echo "<b>- پیشوندی: </b>";
  while($rec = $res->fetch())
  {
    echo "<a target=_blank href='TermOntologyPage.php?TermID=".$rec["TermID"]."'>".$rec["TermTitle"]."</a> - ";
  }
  //echo "<br>";
  $res = $mysql->Execute("select TermID, TermTitle from projectmanagement.terms where TermID<>'".$TermID."' and TermTitle like '%".$TermTitle."' and TermTitle not like '".$TermTitle."%'  and TermID in (select TermID from projectmanagement.TermReferenceMapping )");
  echo "<br><b>- پسوندی: </b>";
  while($rec = $res->fetch())
  {
    echo "<a target=_blank href='TermOntologyPage.php?TermID=".$rec["TermID"]."'>".$rec["TermTitle"]."</a> - ";
  }
  //echo "<br>";
  $res = $mysql->Execute("select TermID, TermTitle from projectmanagement.terms where TermID<>'".$TermID."' and TermTitle like '%".$TermTitle."%' and (TermTitle not like '".$TermTitle."%' and TermTitle not like '%".$TermTitle."')  and TermID in (select TermID from projectmanagement.TermReferenceMapping )");
  echo "<br><b>- محتوی: </b>";
  while($rec = $res->fetch())
  {
    echo "<a target=_blank href='TermOntologyPage.php?TermID=".$rec["TermID"]."'>".$rec["TermTitle"]."</a> - ";
  }

}

function ShowHyperTermsByStruct($TermID, $TermTitle)
{
  $ret = "";
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select TermID, TermTitle from projectmanagement.terms where TermID<>'".$TermID."'  and TermID in (select TermID from projectmanagement.TermReferenceMapping )");
			    
  while($rec = $res->fetch())
  {
    if($TermTitle!="" && $rec["TermTitle"]!="" && strpos($TermTitle, $rec["TermTitle"])>-1)
      echo "<a target=_blank href='TermOntologyPage.php?TermID=".$rec["TermID"]."'>".$rec["TermTitle"]."</a> - ";
  }
  echo "<br>";
}

function GetTermSynSetIDs($TermTitle)
{
  //return "Disabled";
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

function GetHyponymSynSets($SynSetIDs)
{
  //return "Disabled";
  $ret = "0";
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select synSetID2 as ID from ferdowsnet.relationsynset where 
			    synSetID1 in (".$SynSetIDs.") 
			    and relationType='hyponym'
			   union select synSetID1 as ID  from ferdowsnet.relationsynset where 
			    synSetID2 in (".$SynSetIDs.") 
			    and relationType='hypernym'
			    ");
  $i = 0;
  while($rec = $res->fetch())
  {
    $ret .= ",";
    $ret .= $rec["ID"];
    $i++;
  }
  return $ret;
}

function GetHypernymSynSets($SynSetIDs)
{
  //return "Disabled";
  $ret = "0";
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select synSetID2 as ID from ferdowsnet.relationsynset where 
			    synSetID1 in (".$SynSetIDs.") 
			    and relationType='hypernym'
			   union select synSetID1 as ID  from ferdowsnet.relationsynset where 
			    synSetID2 in (".$SynSetIDs.") 
			    and relationType='hyponym'
			    ");
  $i = 0;
  while($rec = $res->fetch())
  {
    $ret .= ",";
    $ret .= $rec["ID"];
    $i++;
  }
  return $ret;
}


function ShowHypernymTerms($HyperSynSetIDs, $TermTitle)
{
  //return "Disabled";
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select distinct TermID, TermTitle from ferdowsnet.sense 
			    JOIN ferdowsnet.persianwords on (sense.pewordID=persianwords.ID)
			    JOIN projectmanagement.terms on (TermTitle=word)
			    where synSetID in (".$HyperSynSetIDs.") and TermTitle<>'".$TermTitle."'");
  $i = 0;
  while($rec = $res->fetch())
  {
    echo "<a target=_blank href='TermOntologyPage.php?TermID=".$rec["TermID"]."'>".$rec["TermTitle"]."</a> - ";
  }
}

function ShowHyponymTerms($HypoSynSetIDs, $TermTitle)
{
  //return "Disabled";
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select distinct TermID, TermTitle from ferdowsnet.sense 
			    JOIN ferdowsnet.persianwords on (sense.pewordID=persianwords.ID)
			    JOIN projectmanagement.terms on (TermTitle=word)
			    where synSetID in (".$HypoSynSetIDs.") and TermTitle<>'".$TermTitle."'");
  $i = 0;
  while($rec = $res->fetch())
  {
    echo "<a target=_blank href='TermOntologyPage.php?TermID=".$rec["TermID"]."'>".$rec["TermTitle"]."</a> - ";
  }
}

function ShowSameSynSetTerms($SynSetIDs, $TermTitle)
{
  //return "Disabled";
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select distinct TermID, TermTitle from ferdowsnet.sense 
			    JOIN ferdowsnet.persianwords on (sense.pewordID=persianwords.ID)
			    JOIN projectmanagement.terms on (TermTitle=word)
			    where synSetID in (".$SynSetIDs.") and TermTitle<>'".$TermTitle."'");
  $i = 0;
  while($rec = $res->fetch())
  {
    echo "<a target=_blank href='TermOntologyPage.php?TermID=".$rec["TermID"]."'>".$rec["TermTitle"]."</a> - ";
  }
}

function ShowValidRelations($ClassID, $OntologyID)
{
  $mysql = pdodb::getInstance();
  $query = "select OntologyProperties.OntologyPropertyID
	    , DomainClassID, RangeClassID
	    , (select group_concat(label) from projectmanagement.OntologyClassLabels dl where dl.OntologyClassID=DomainClassID) as DomainClassLabel
	    , (select group_concat(label) from projectmanagement.OntologyClassLabels rl where rl.OntologyClassID=RangeClassID) as RangeClassLabel
	    , (select group_concat(label) from projectmanagement.OntologyPropertyLabels where OntologyPropertyID=OntologyObjectPropertyRestriction.OntologyPropertyID) as PropertyLabel
	    from projectmanagement.OntologyObjectPropertyRestriction 
	    JOIN projectmanagement.OntologyProperties using (OntologyPropertyID)
	    where RelationStatus='VALID' and (DomainClassID=? or RangeClassID=?)";
  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($ClassID, $ClassID));
  while($rec = $res->fetch())
  {
    echo "رابطه: ";
    echo "<a target=_blank href='ManageOntologyClasses.php?UpdateID=".$rec["DomainClassID"]."&OntologyID=".$OntologyID."'> ";
    echo $rec["DomainClassLabel"];
    echo "</a> &nbsp;";
    echo " <b><a href=# onclick=\"window.open('ManageOntologyProperties.php?UpdateID=".$rec["OntologyPropertyID"]."&OntologyID=".$OntologyID."');\" >".$rec["PropertyLabel"]."</a></b> &nbsp;";    
    echo " <a target=_blank href='ManageOntologyClasses.php?UpdateID=".$rec["RangeClassID"]."&OntologyID=".$OntologyID."'>";
    echo $rec["RangeClassLabel"];
    echo "</a>";
    echo "<br>";
  }
  
}

function ShowPropertyList($plist, $OntologyID)
{
  $mysql = pdodb::getInstance();
    for($m=0; $m<count($plist); $m++)
    {
	/*
	if($plist[$m]["PropertyType"]=="OBJECT")
	{
	    echo "رابطه: ";	                
	    $DomainList = explode(", ",$plist[$m]["domain"]);
	    for($j=0; $j<count($DomainList); $j++)
	    {
	      $ClassIDLabel = manage_OntologyClasses::GetClassIDAndLabel($OntologyID, $DomainList[$j]);
	      if($j>0)
		echo " - ";
	      echo "<a target=_blank href='ManageOntologyClasses.php?UpdateID=".$ClassIDLabel["OntologyClassID"]."&OntologyID=".$OntologyID."'>";
	      echo $ClassIDLabel["label"];
	      echo "</a>";
	    }
	    echo " ";
	}
	*/
	if($plist[$m]["PropertyType"]=="DATATYPE")
	{
	  echo "<tr>";
	  echo "<td >";
	  echo "<b><a href=# onclick=\"window.open('ManageOntologyProperties.php?UpdateID=".$plist[$m]["PropertyID"]."&OntologyID=".$OntologyID."');\" >".$plist[$m]["PropertyLabel"]."</a></b>";
	/*
	if($plist[$m]["PropertyType"]=="OBJECT")
	{
	  echo " ";
	    $RangeList = explode(", ",$plist[$m]["range"]);
	    for($j=0; $j<count($RangeList); $j++)
	    {
	      $ClassIDLabel = manage_OntologyClasses::GetClassIDAndLabel($OntologyID, $RangeList[$j]);
	      if($j>0)
		echo " - ";
	      echo "<a target=_blank href='ManageOntologyClasses.php?UpdateID=".$ClassIDLabel["OntologyClassID"]."&OntologyID=".$OntologyID."'>";
	      echo $ClassIDLabel["label"];
	      echo "</a>";
	    }
	    echo " ";
	}
	*/
	  $query = "select distinct PermittedValue from projectmanagement.OntologyPropertyPermittedValues where OntologyPropertyID='".$plist[$m]["PropertyID"]."'";
	  $res = $mysql->Execute($query);
	  $j = 0;
	  while($rec = $res->fetch())
	  {
	    if($j==0)
	      echo " (";
	    else
	      echo " - ";
	    echo $rec["PermittedValue"];
	    $j++;
	  }
	  if($j>0)
	    echo ")";
	  echo "</td></tr>";    	    
	}
    }
}

function ShowTFIDFCategory($TF_IDF)
{
	$CategoryHeight = 0;
	$mysql = pdodb::getInstance();
	$res = $mysql->Execute("select round((max(TF*IDF)-min(TF*IDF))/4) as CategoryHeight
				from projectmanagement.terms");	
	if($rec = $res->fetch())
	{
		$CategoryHeight = $rec["CategoryHeight"];
	}
	for($i=1; $i<6; $i++)
	{
//echo 	($i-1)*$CategoryHeight." --- ".$i*$CategoryHeight."<br>";
		if(($TF_IDF>=($i-1)*$CategoryHeight) && ($TF_IDF<$i*$CategoryHeight))
			return 6-$i;
	}
	return 0;
}

$thereshold = 80;

if(isset($_REQUEST["Ajax"]))
{
  if(isset($_REQUEST["LoadPermittedData"])) 
  {
    CreatePermittedValueList($_REQUEST["LoadPermittedData"]);
    die();
  }
  if(isset($_REQUEST["ShowObject"])) 
  {
      $mysql->Prepare("select TermID, TermTitle, TF, IDF from projectmanagement.terms where TermID=?");
      $res = $mysql->ExecuteStatement(array($_REQUEST["TermID"]));
      $rec = $res->fetch();
  
      if($_REQUEST["ShowObject"]=="FerdosNet")
      {
	echo "<b>";
	echo "واژگان همرده: ";
	echo "</b>";
	$SynSetIDs = GetTermSynSetIDs($rec["TermTitle"]);
	//print_r($SynSetIDs);
	ShowSameSynSetTerms($SynSetIDs, $rec["TermTitle"]); 
	echo "<br>";
	echo "<b>";
	echo "واژگان عام تر: ";
	echo "</b>";
	$HypoSynSetIDs = GetHyponymSynSets($SynSetIDs);
	ShowHyponymTerms($HypoSynSetIDs, $rec["TermTitle"]);
	echo "<br>";    
	echo "<b>";
	echo "واژگان خاص تر: ";
	echo "</b>";
	$HyperSynSetIDs = GetHypernymSynSets($SynSetIDs);
	ShowHypernymTerms($HyperSynSetIDs, $rec["TermTitle"]);
      }
      else if($_REQUEST["ShowObject"]=="Syntactic") 
      {
      	echo "<b>";
	echo "واژگان مشابه (بر اساس فاصله ویرایشی): ";
	echo "</b>";
	echo ShowSimilarTerm($rec["TermID"], $rec["TermTitle"], $thereshold);
	echo "<br>";

echo "<b>";
	echo "واژگان حاوی این واژه: <br>";
	echo "</b>";
	echo ShowHypoTermsByStruct($rec["TermID"], $rec["TermTitle"]);
	echo "<br>";
echo "<b>";
	echo "واژگان زیر مجموعه این واژه: ";
	echo "</b>";
	echo ShowHyperTermsByStruct($rec["TermID"], $rec["TermTitle"]);
      }
      else if($_REQUEST["ShowObject"]=="CoOccurance") 
      {
      echo ShowCoOccuranceTerms($rec["TermID"]);  
      }
      else if($_REQUEST["ShowObject"]=="CoOccurance2") 
      {
	echo ShowRelatedTerms($rec["TermID"]);
      }
      else if($_REQUEST["ShowObject"]=="Ontology") 
      {
	echo ShowOntologyElement($rec["TermTitle"], $thereshold);
      }
  }
  else if(isset($_REQUEST["ObjectName"])) 
  {
      if($_REQUEST["CurValue"]=="0")
	die();
      if($_REQUEST["ObjectName"]=="Class") 
      {
	  $obj = new be_OntologyClasses();
	  $obj->LoadDataFromDatabase($_REQUEST["CurValue"]); 
	  echo "<a href=# onclick=\"window.open('ManageOntologyClasses.php?UpdateID=".$_REQUEST["CurValue"]."&OntologyID=".$obj->OntologyID."');\" >ویرایش کلاس</a><br>";
	  $plist = manage_OntologyClasses::GetClassRelatedProperties($obj->ClassTitle, $obj->OntologyID);
	  if(count($plist)>0)
	    echo "خصوصیات: <br><table>";
	  ShowPropertyList($plist, $obj->OntologyID);
	  ShowValidRelations($_REQUEST["CurValue"], $obj->OntologyID);
	  if(count($plist)>0)
	    echo "</table>";

	  $ParentClasses = manage_OntologyClassHirarchy::GetParentListArray($_REQUEST["CurValue"]);
	  for($j=0; $j<count($ParentClasses); $j++)
	  {
	    $plist = manage_OntologyClasses::GetClassRelatedProperties($ParentClasses[$j]["ClassTitle"], $obj->OntologyID);
	    if(count($plist)>0)
	    {
	      echo "<font color=green>";
	      echo "خصوصیات به واسطه کلاس پدر (".$ParentClasses[$j]["label"]."): ";
	      echo "</font>";
	      echo "<br><table>";
	    }
	    ShowPropertyList($plist, $obj->OntologyID);
	    ShowValidRelations($ParentClasses[$j]["OntologyClassID"], $obj->OntologyID);
	    if(count($plist)>0)
	      echo "</table>";
	    echo "<br>";
	    
	    $ParentLevel2Classes = manage_OntologyClassHirarchy::GetParentListArray($ParentClasses[$j]["OntologyClassID"]);	    
	    for($k=0; $k<count($ParentLevel2Classes); $k++)
	    {
	      $plist = manage_OntologyClasses::GetClassRelatedProperties($ParentLevel2Classes[$k]["ClassTitle"], $obj->OntologyID);
	      if(count($plist)>0)
	      {
		echo "<font color=green>";
		echo "خصوصیات به واسطه کلاس پدربزرگ (".$ParentLevel2Classes[$k]["label"]."): ";
		echo "</font>";
		echo "<br><table>";
	      }
	      ShowPropertyList($plist, $obj->OntologyID);
	      ShowValidRelations($Parent2Classes[$k]["OntologyClassID"], $obj->OntologyID);
	      if(count($plist)>0)
		echo "</table>";
	      echo "<br>";
	    }
	    	    
	  }
      }
      if($_REQUEST["ObjectName"]=="DataProp" || $_REQUEST["ObjectName"]=="ObjectProp") 
      {
	echo "<a target=_blank href='ManageOntologyProperties.php?UpdateID=".$_REQUEST["CurValue"]."&OntologyID=".$TargetOntologyID."'>ویرایش خصوصیت</a><br>";
      }
      if($_REQUEST["ObjectName"]=="DataProp" || $_REQUEST["ObjectName"]=="ObjectProp") 
      {
	  $obj = new be_OntologyProperties();
	  $obj->LoadDataFromDatabase($_REQUEST["CurValue"]); 
          echo "دامنه: ";
	  $DomainList = explode(", ", $obj->domain);
	  for($i=0; $i<count($DomainList); $i++)
	  {
	    if($i>0)
	      echo ", ";
	    $rec = manage_OntologyClasses::GetClassIDAndLabel($obj->OntologyID, $DomainList[$i]);
	    if($DomainList[$i]!="")
	    {
	      echo "<a href='ManageOntologyClasses.php?UpdateID=".$rec["OntologyClassID"]."&OntologyID=".$obj->OntologyID."' target=_blank>";
	      echo $rec["label"]." (".$DomainList[$i].") ";
	      echo "</a>";
	    }
	  }
          echo "<br>";
          echo "برد: ";
	  $RangeList = explode(", ", $obj->range);
	  for($i=0; $i<count($RangeList); $i++)
	  {
	    if($i>0)
	      echo ", ";
	    $rec = manage_OntologyClasses::GetClassIDAndLabel($obj->OntologyID, $RangeList[$i]);
	    if($RangeList[$i]!="")
	    {
	      echo "<a href='ManageOntologyClasses.php?UpdateID=".$rec["OntologyClassID"]."&OntologyID=".$obj->OntologyID."' target=_blank>";
	      echo $rec["label"]." (".$RangeList[$i].") ";
	      echo "</a>";
	    }
	  }

          echo "<br>";
      }
      if($_REQUEST["ObjectName"]=="DataProp")
      {
	echo "مقادیر: ";
	$query = "select distinct PermittedValue from projectmanagement.OntologyPropertyPermittedValues where OntologyPropertyID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($_REQUEST["CurValue"]));
	while($rec = $res->fetch())
		echo $rec["PermittedValue"]." - ";
	echo "<br>";
      }
  }


  die();
}

if(isset($_REQUEST["SetEntity"]))
{
  $mysql->Prepare("delete from projectmanagement.TermOntologyElementMapping where TermID=?");
  $mysql->ExecuteStatement(array($_REQUEST["TermID"]));
  $query = "insert into projectmanagement.TermOntologyElementMapping (TermID, OntologyEntityID, EntityType, OntologyPropertyPermittedValueID) values (?, ?, ?, ?)";
  $mysql->Prepare($query);
  $EntityType = $_REQUEST["EntityType"];
  $OntologyPropertyPermittedValueID = $_REQUEST["OntologyPropertyPermittedValueID"];
  if($EntityType=="CLASS")
    $EntityID = $_REQUEST["RelatedClassID"];
  else if($EntityType=="OBJECT_PROPERTY")
    $EntityID = $_REQUEST["RelatedObjPropID"];
  else if($EntityType=="DATA_PROPERTY")
    $EntityID = $_REQUEST["RelatedDataPropID"];
  else if($EntityType=="DATA_RANGE")
    $EntityID = $_REQUEST["RelatedDataPropID"];
  else
    $EntityID = 0;
  if($OntologyPropertyPermittedValueID=="")
      $OntologyPropertyPermittedValueID = 0;
  $mysql->ExecuteStatement(array($_REQUEST["TermID"], $EntityID, $EntityType, $OntologyPropertyPermittedValueID));
}

$mysql->Prepare("select * from projectmanagement.TermOntologyElementMapping 
				LEFT JOIN projectmanagement.OntologyClasses on (OntologyEntityID=OntologyClassID)
				LEFT JOIN projectmanagement.OntologyProperties on (OntologyEntityID=OntologyPropertyID)
				LEFT JOIN projectmanagement.OntologyPropertyPermittedValues using (OntologyPropertyPermittedValueID)
				where TermID=?");
$res = $mysql->ExecuteStatement(array($_REQUEST["TermID"]));
$EntityType = "";
$EntityID = 0;
$EntityName = "";
$DataValue = "";
$OntologyPropertyPermittedValueID = 0;
$PermittedValue = "";
if($rec = $res->fetch())
{
  $EntityType = $rec["EntityType"];
  $EntityID = $rec["OntologyEntityID"];
  if($EntityType=="CLASS")
    $EntityName = $rec["ClassTitle"];
  else if($EntityType=="OBJECT_PROPERTY" || $EntityType=="DATA_PROPERTY" || $EntityType=="DATA_RANGE")
    $EntityName = $rec["PropertyTitle"];
  if($EntityType=="DATA_RANGE")
  {
    $EntityName .= " - ".$rec["DataValue"];
    $DataValue = $rec["DataValue"];
    $OntologyPropertyPermittedValueID = $rec["OntologyPropertyPermittedValueID"];
    $PermittedValue = $rec["PermittedValue"];
  }
}
HTMLBegin();
?>
	<script>
	  function LoadPermittedValueList(OntologyPropertyID)
	  {
	    //document.getElementById('PermittedValueSpan').innerHTML = '<img src="images/ajax-loader.gif">';
	    var params = "Ajax=1&LoadPermittedData="+OntologyPropertyID+"&TermID=<? echo $_REQUEST["TermID"]; ?>";
	    //alert('TermOntologyPage.php'+'&'+params);
	    var http = new XMLHttpRequest();
	    http.open("POST", "TermOntologyPage.php", true);
	    //Send the proper header information along with the request
	    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	    http.setRequestHeader("Content-length", params.length);
	    http.setRequestHeader("Connection", "close");
	    
	    http.onreadystatechange = function()
	    {//Call a function when the state changes.
	    if(http.readyState == 4 && http.status == 200)
	    {
	      //document.getElementById('PermittedValueSpan').innerHTML = http.responseText;
	      document.getElementById('OntologyPropertyPermittedValueID').innerHTML = http.responseText;
	      document.getElementById('OntologyPropertyPermittedValueID').value='<? echo $OntologyPropertyPermittedValueID;  ?>';
	    }
	    }
	    http.send(params);
	  }

	
	  function ShowBox(ObjectName)
	  {
	    document.getElementById(ObjectName+'Span').innerHTML = '<img src="images/ajax-loader.gif">';
	    var params = "Ajax=1&ShowObject="+ObjectName+"&TermID=<? echo $_REQUEST["TermID"]; ?>";
	    //alert('TermOntologyPage.php'+'&'+params);
	    var http = new XMLHttpRequest();
	    http.open("POST", "TermOntologyPage.php", true);
	    //Send the proper header information along with the request
	    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	    http.setRequestHeader("Content-length", params.length);
	    http.setRequestHeader("Connection", "close");
	    
	    http.onreadystatechange = function()
	    {//Call a function when the state changes.
	    if(http.readyState == 4 && http.status == 200)
	    {
	      document.getElementById(ObjectName+'Span').innerHTML = http.responseText;
	    }
	    }
	    http.send(params);
	  }

	  function ShowDetails(ObjectName, CurValue)
	  {
	    document.getElementById('DetailsSpan').innerHTML = '<img src="images/ajax-loader.gif">';
	    var params = "Ajax=1&ObjectName="+ObjectName+"&CurValue="+CurValue;
	    
	    var http = new XMLHttpRequest();
	    http.open("POST", "TermOntologyPage.php", true);
	    //Send the proper header information along with the request
	    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	    http.setRequestHeader("Content-length", params.length);
	    http.setRequestHeader("Connection", "close");
	    
	    http.onreadystatechange = function()
	    {//Call a function when the state changes.
	      if(http.readyState == 4 && http.status == 200)
	      {
		document.getElementById('DetailsSpan').innerHTML = http.responseText;
	      }
	    }
	    http.send(params);
	  }
	</script>
	
<?
$mysql->Prepare("select TermID, TermTitle, TF, IDF from projectmanagement.terms where TermID=?");
$res = $mysql->ExecuteStatement(array($_REQUEST["TermID"]));
$rec = $res->fetch();
$TF_IDF = $rec["TF"]*$rec["IDF"];
echo "<table width=90% align=center border=1 cellspacing=0>";
echo "<form method=post>";
echo "<input type=hidden name='SetEntity' id='SetEntity' value='1'>";
echo "<input type=hidden name='TermID' id='TermID' value='".$_REQUEST["TermID"]."'>";
echo "<tr>";
echo "<td><font size=3><b>";
echo "واژه: ".$rec["TermTitle"]." ";
if($EntityType!="")
{
  echo " - ";
  echo "موجودیت مرتبط: ";
if($EntityType=="CLASS")
	echo "کلاس ";
if($EntityType=="OBJECT_PROPERTY")
	echo "خصوصیت شیء ";	
if($EntityType=="DATA_PROPERTY")
	echo "خصوصیت داده ";	
if($EntityType=="DATA_RANGE")
	echo "مقدار خصوصیت ";	
	
  echo "<a href=# onclick='";
  if($EntityType=="CLASS")
  	echo "window.open(\"ManageOntologyClasses.php?UpdateID=".$EntityID."&OntologyID=".$TargetOntologyID."\");";
  else
  	echo "window.open(\"ManageOntologyProperties.php?UpdateID=".$EntityID."&OntologyID=".$TargetOntologyID."\");";
  echo "'>".$EntityName."</a>";
}
echo " [<a href='#' onclick='javascript: RemoveTerm()'>حذف واژه</a>]";
echo "<br></b></font>";
echo " مقدار ";
echo " TF-IDF=".$TF_IDF;
//echo " در طبقه ".ShowTFIDFCategory($TF_IDF);
//echo " قرار می گیرد.";
echo "<br>";
echo "<select name='EntityType' id='EntityType' onchange='javascript: ShowHideClassProp(this.value)'>";
echo "<option value=''>-";
echo "<option value='CLASS' ";
if($EntityType=="CLASS") echo " selected ";
echo ">CLASS";
echo "<option value='OBJECT_PROPERTY' ";
if($EntityType=="OBJECT_PROPERTY") echo " selected ";
echo ">OBJECT_PROPERTY";
echo "<option value='DATA_PROPERTY' ";
if($EntityType=="DATA_PROPERTY") echo " selected ";
echo ">DATA_PROPERTY";
echo "<option value='DATA_RANGE' ";
if($EntityType=="DATA_RANGE") echo " selected ";
echo ">DATA_RANGE";
echo "</select>";
echo "<select name='RelatedClassID' id='RelatedClassID' style='display: none' onchange='javascript: ShowDetails(\"Class\", this.value)'>";
echo "<option value='0'>-";
$classes = $mysql->Execute("select OntologyClasses.OntologyClassID, ClassTitle, label from projectmanagement.OntologyClasses 
LEFT JOIN projectmanagement.OntologyClassLabels using (OntologyClassID)
where OntologyID=".$TargetOntologyID." order by label");
while($crec = $classes->fetch())
{
  echo "<option value='".$crec["OntologyClassID"]."' ";
  if($EntityID==$crec["OntologyClassID"]) echo " selected ";
  echo ">".$crec["label"]." (".$crec["ClassTitle"].")";
}
echo "</select>";

echo "<select name='RelatedObjPropID' id='RelatedObjPropID' style='display: none'  onchange='javascript: ShowDetails(\"ObjectProp\", this.value)'>";
echo "<option value='0'>-";
$classes = $mysql->Execute("select OntologyProperties.OntologyPropertyID, PropertyTitle, label from projectmanagement.OntologyProperties 
LEFT JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID)
where OntologyID=".$TargetOntologyID." and PropertyType='OBJECT' order by label");
while($crec = $classes->fetch())
{
  echo "<option value='".$crec["OntologyPropertyID"]."' ";
  if($EntityID==$crec["OntologyPropertyID"]) echo " selected ";
  echo ">".$crec["label"]." (".$crec["PropertyTitle"].")";
}
echo "</select>";

echo "<select name='RelatedDataPropID' id='RelatedDataPropID' style='display: none'  onchange='javascript: ShowDetails(\"DataProp\", this.value); LoadPermittedValueList(this.value);'>";
echo "<option value='0'>-";
$classes = $mysql->Execute("select OntologyProperties.OntologyPropertyID, PropertyTitle, label from projectmanagement.OntologyProperties 
LEFT JOIN projectmanagement.OntologyPropertyLabels using (OntologyPropertyID)
where OntologyID=".$TargetOntologyID." and PropertyType='DATATYPE' order by label");
while($crec = $classes->fetch())
{
  echo "<option value='".$crec["OntologyPropertyID"]."' ";
  if($EntityID==$crec["OntologyPropertyID"]) echo " selected ";
  echo ">".$crec["label"]." (".$crec["PropertyTitle"].")";
}
echo "</select>";
echo "<input type=hidden name='DataValue' id='DataValue' style='display: none' value='".$DataValue."'>";
echo "<select id='OntologyPropertyPermittedValueID' name='OntologyPropertyPermittedValueID' style='display: none'></select>";
//echo "<span id='PermittedValueSpan' name='PermittedValueSpan'></span>";
//$tres = $mysql->Execute("select * from projectmanagement.OntologyPropertyPermittedValues where OntologyPropertyID=");
echo "<span id='EditRangeLink' name='EditRangeLink' style='display: none'>&nbsp; <a href='#' onclick='javascript: GoEditRangePage();'>ویرایش مقادیر</a> &nbsp;&nbsp;</span>";
echo "<span id='NewObjPropLink' name='NewObjPropLink' style='display: none'><a href='ManageOntologyProperties.php?FromTermOnto=1&OntologyID=".$TargetOntologyID."' target=_blank>ایجاد خصوصیت جدید</a></span>";
echo "<span id='NewDataPropLink' name='NewDataPropLink' style='display: none'><a href='ManageOntologyProperties.php?DataProp=1&FromTermOnto=1&OntologyID=".$TargetOntologyID."' target=_blank>ایجاد خصوصیت جدید</a></span>";
echo "<span id='NewClassLink' name='NewClassLink' style='display: none'><a href='ManageOntologyClasses.php?FromTermOnto=1&OntologyID=".$TargetOntologyID."' target=_blank>ایجاد کلاس جدید</a></span>";

echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td>";
echo "<span id=DetailsSpan name=DetailsSpan></span>";
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td bgcolor=#cccccc align=center>";
echo "<input type=submit value='ذخیره'>";
echo " &nbsp; ";
echo "<input type=button value='بازگشت' onclick='document.location=\"TermFrequency.php\"'>";
echo "</td>";
echo "</tr>";
echo "</form>";
echo "<tr><td>";
echo "<table border=2 cellspacing=0 cellpadding=5 width=500>";
echo "<tr><td bgcolor=#cccccc><a href='#' onclick='javascript: ShowBox(\"FerdosNet\")'>بررسی واژگان مرتبط از نظر معنایی</a></td></tr>";
echo "<tr>";
echo "<td>";
echo "<span name=FerdosNetSpan id=FerdosNetSpan></span>";
echo "</td>";

echo "</table></td></tr>";

echo "<tr><td><table border=2 cellspacing=0 cellpadding=5 width=500>";
echo "<tr><td bgcolor=#cccccc><a href='#' onclick='javascript: ShowBox(\"Syntactic\")'>بررسی واژگان مشابه از نظر شکلی</a></td></tr>";


echo "<tr>";
echo "<td>";
echo "<span name=SyntacticSpan id=SyntacticSpan></span>";
echo "</td>";
echo "</tr>";

echo "</table></td></tr>";

echo "<tr>";
echo "<td>";
echo "<a href='#' onclick='javascript: ShowBox(\"Ontology\")'>";
echo "عنصر هستان نگار موجود در مخزن با برچسب یکسان: <br>";
echo "</a>";
echo "<span name=OntologySpan id=OntologySpan></span>";
echo "</td>";
echo "</tr>";

echo "<tr>";
echo "<td>";
echo "<a href='#' onclick='javascript: ShowBox(\"CoOccurance\")'>";
echo "<b>اصطلاحات همرخداد به ترتیب میزان همرخدادی: </b> ";
echo "</a>";
echo "<span name=CoOccuranceSpan id=CoOccuranceSpan></span>";
echo "<br>";
echo "<a href='#' onclick='javascript: ShowBox(\"CoOccurance2\")'>";
echo "<b>اصطلاحات همرخداد بر اساس کنار هم بودن در یک سند: </b> <br>";
echo "</a>";
echo "<span name=CoOccurance2Span id=CoOccurance2Span></span>";
echo "</td>";
echo "</table>";
?>
<script>
  ShowHideClassProp(document.getElementById('EntityType').value);
  function ShowHideClassProp(EntityType)
  {
    document.getElementById('DataValue').style.display = 'none';
    document.getElementById('OntologyPropertyPermittedValueID').style.display = 'none';
    document.getElementById('RelatedClassID').style.display = 'none';
    document.getElementById('RelatedObjPropID').style.display = 'none';
    document.getElementById('RelatedDataPropID').style.display = 'none';
    document.getElementById('NewClassLink').style.display = 'none';
    document.getElementById('NewDataPropLink').style.display = 'none';
    document.getElementById('NewObjPropLink').style.display = 'none';
    document.getElementById('EditRangeLink').style.display = 'none';
    if(EntityType=="CLASS")
    {
      document.getElementById('RelatedClassID').style.display = '';
      document.getElementById('NewClassLink').style.display = '';      
      ShowDetails("Class", document.getElementById('RelatedClassID').value);
    }
    else
    if(EntityType=="OBJECT_PROPERTY")
    {
      document.getElementById('RelatedObjPropID').style.display = '';
      document.getElementById('NewObjPropLink').style.display = '';
      ShowDetails("ObjectProp", document.getElementById('RelatedObjPropID').value);
    }
    else
    if(EntityType=="DATA_PROPERTY")
    {
      document.getElementById('RelatedDataPropID').style.display = '';
      document.getElementById('NewDataPropLink').style.display = '';
      ShowDetails("DataProp", document.getElementById('RelatedDataPropID').value);
    }
    else
    if(EntityType=="DATA_RANGE")
    {
      document.getElementById('RelatedDataPropID').style.display = '';
      document.getElementById('NewDataPropLink').style.display = '';
      document.getElementById('DataValue').style.display = '';
      document.getElementById('OntologyPropertyPermittedValueID').style.display = '';
      LoadPermittedValueList(document.getElementById('RelatedDataPropID').value);
      document.getElementById('EditRangeLink').style.display = '';
      
      ShowDetails("DataProp", document.getElementById('RelatedDataPropID').value);
    }
    
  }
  ShowBox('CoOccurance2');
  <? if($EntityType=="CLASS") { ?>
    ShowDetails('Class', document.getElementById('RelatedClassID').value);
  <? } ?>
  <? if($EntityType=="OBJECT_PROPERTY") { ?>
    ShowDetails('ObjectProp', document.getElementById('RelatedObjPropID').value);
  <? } ?>
  <? if($EntityType=="DATA_PROPERTY" || $EntityType=="DATA_RANGE") { ?>
    ShowDetails('DataProp', document.getElementById('RelatedDataPropID').value);
  <? } ?>
  <? if($EntityType=="DATA_RANGE") { ?>
    LoadPermittedValueList(<? echo $EntityID ?>);
    document.getElementById('OntologyPropertyPermittedValueID').value='<? echo $OntologyPropertyPermittedValueID;  ?>';
  <? } ?>
  function RemoveTerm()
  {
    if(confirm('با حذف واژه کلیه ارجاعات آن نیز از همه منابع حذف خواهد شد. مطمئن هستید؟'))
    {
      document.location='TermFrequency.php?RemoveID=<? echo $_REQUEST["TermID"]; ?>';
    }
  }
  function GoEditRangePage()
  {
    window.open('ManageOntologyPropertyPermittedValues.php?FromTermOnto=1&OntologyID=<? echo $TargetOntologyID ?>&OntologyPropertyID='+document.getElementById('RelatedDataPropID').value);
  }
  </script>
</body>
</html>
