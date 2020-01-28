<?php 
include("header.inc.php");
include_once("classes/OntologyClasses.class.php");
include_once("classes/OntologyClassLabels.class.php");
include_once("classes/OntologyClassHirarchy.class.php");
include_once("classes/ontologies.class.php");
include_once("classes/OntologyPropertyPermittedValues.class.php");
include_once("classes/terms.class.php");
include_once("classes/OntologyValidationExperts.class.php");

function ShowChilds($LevelNo, $ParentID, $PreIndent)
{
  $mysql = pdodb::getInstance();
  $LevelNo++;
  if($LevelNo>6)
    return;
  $query = "select OntologyClasses.OntologyClassID, ClassTitle, 
    (select group_concat(label, ' ') from projectmanagement.OntologyClassLabels where OntologyClassLabels.OntologyClassID=OntologyClasses.OntologyClassID group by OntologyClassID) as ClassLabel
    from projectmanagement.OntologyClasses 
    JOIN projectmanagement.OntologyClassHirarchy on (OntologyClassHirarchy.OntologyClassParentID=OntologyClasses.OntologyClassID)
    where OntologyClassHirarchy.OntologyClassID=? order by ClassLabel";
  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($ParentID));
  $totalcount = $res->rowCount();
  $i=0;
  while($rec = $res->fetch())
  {
    $i++;
    echo $PreIndent;
    if($i==$totalcount)
      echo "<img src='images/l.gif'>";
    else 
      echo "<img src='images/t.gif'>";    
    echo "<a href='ShowExpertsResult.php?SelectedClass=".$rec["OntologyClassID"]."&OntologyID=".$_REQUEST["OntologyID"]."&OntologyTitle=".$_REQUEST["OntologyTitle"];
    if(isset($_REQUEST["ExpertID"]))
      echo "&ExpertID=".$_REQUEST["ExpertID"];
    echo "'>";      
    echo "<font color=black  title='".$rec["ClassTitle"]."'>";
    
    echo $rec["ClassLabel"];
    echo "</font>";
    echo "</a>";
    
    ShowClassvalidation($rec["OntologyClassID"]);  
    
    echo "<br>";
    if($i<$totalcount)
      $indent = $PreIndent."<img src='images/i.gif'>";
    else 
      $indent = $PreIndent."<img src='images/e.gif'>";
    ShowChilds($LevelNo, $rec["OntologyClassID"], $indent);
  }
}

function ShowClassValidation($OntologyClassID)
{
  $mysql = pdodb::getInstance();
  $query = "select OntologyClassesValidation.ExpertOpinion, count(*) as tcount
			    from projectmanagement.OntologyClasses 
			    JOIN projectmanagement.OntologyClassesValidation on (OntologyClassesValidation.OntologyClassID=OntologyClasses.OntologyClassID)
			    LEFT JOIN projectmanagement.OntologyClassLabels on (OntologyClasses.OntologyClassID=OntologyClassLabels.OntologyClassID)
			    where OntologyClasses.OntologyClassID=? ";
  if(isset($_REQUEST["ExpertID"]))
    $query .= " and OntologyValidationExpertID=? ";
  $query .= " group by ExpertOpinion order by ExpertOpinion";
  $mysql->Prepare($query);
  if(isset($_REQUEST["ExpertID"]))
    $res = $mysql->ExecuteStatement(array($OntologyClassID, $_REQUEST["ExpertID"]));
  else $res = $mysql->ExecuteStatement(array($OntologyClassID));
  echo " &nbsp;[";
  while($rec = $res->fetch())
  {
    if($rec["ExpertOpinion"]=="UNKNOWN")
      echo "<font color=#cc9900> نمی دانم ";
    else if($rec["ExpertOpinion"]=="ACCEPT")
      echo "<font color=green> موافق ";
    else if($rec["ExpertOpinion"]=="REJECT")         
      echo "<font color=red> مخالف ";
    else
      echo " بدون نظر ";
    echo "(".$rec["tcount"].") "; 
    echo "</font> ";
  }
  echo " ]";
}

function ShowClassRelationValidation($PropertyID, $DomainOntologyClassID, $RangeOntologyClassID)
{
  $mysql = pdodb::getInstance();
  $query = "select ExpertOpinion, count(*) as tcount from projectmanagement.OntologyClassRelationValidation where 
			    OntologyPropertyID=? and 
			    DomainOntologyClassID=? and 
			    RangeOntologyClassID=? ";
  if(isset($_REQUEST["ExpertID"]))
    $query .= " and OntologyValidationExpertID=? ";
  $query .= " group by ExpertOpinion order by ExpertOpinion";
  $mysql->Prepare($query);
  if(isset($_REQUEST["ExpertID"]))
    $res = $mysql->ExecuteStatement(array($PropertyID, $DomainOntologyClassID, $RangeOntologyClassID, $_REQUEST["ExpertID"]));
  else $res = $mysql->ExecuteStatement(array($PropertyID, $DomainOntologyClassID, $RangeOntologyClassID));
  echo " &nbsp;[";
  while($prec = $res->fetch())
  {
      if($prec["ExpertOpinion"]=="UNKNOWN")
	echo "<font color=#cc9900> نمی دانم ";
      else if($prec["ExpertOpinion"]=="ACCEPT")
	echo "<font color=green> موافق ";
      else if($prec["ExpertOpinion"]=="REJECT")         
	echo "<font color=red> مخالف ";
      else
	echo " بدون نظر ";
      echo "(".$prec["tcount"].") </font>"; 
  }
  echo " ]";
}

function ShowPropertyValidation($PropertyID, $OntologyClassID)
{
  $mysql = pdodb::getInstance();
  $query = "select ExpertOpinion, count(*) as tcount from projectmanagement.OntologyPropertyValidation where 
			    OntologyPropertyID=? and 
			    RelatedOntologyClassID=? ";
  if(isset($_REQUEST["ExpertID"]))
    $query .= " and OntologyValidationExpertID=? ";
  $query .= " group by ExpertOpinion order by ExpertOpinion";
  $mysql->Prepare($query);
  if(isset($_REQUEST["ExpertID"]))
    $res = $mysql->ExecuteStatement(array($PropertyID, $OntologyClassID, $_REQUEST["ExpertID"])); 
  else $res = $mysql->ExecuteStatement(array($PropertyID, $OntologyClassID)); 
  echo " &nbsp;[";
  while($prec = $res->fetch())
  {
      if($prec["ExpertOpinion"]=="UNKNOWN")
	echo "<font color=#cc9900> نمی دانم ";
      else if($prec["ExpertOpinion"]=="ACCEPT")
	echo "<font color=green> موافق ";
      else if($prec["ExpertOpinion"]=="REJECT")         
	echo "<font color=red> مخالف ";
      else
	echo " بدون نظر ";
      echo "(".$prec["tcount"].") </font>"; 
  }
  echo " ]";
}

function ShowClassHirarchyValidation($ParentOntologyClassID, $OntologyClassID)
{
  $mysql = pdodb::getInstance();
  $query = "select ExpertOpinion, count(*) as tcount from projectmanagement.OntologyClassHirarchyValidation where 
			    ParentOntologyClassID=? and 
			    OntologyClassID=? ";
  if(isset($_REQUEST["ExpertID"]))
    $query .= " and OntologyValidationExpertID=? ";
  $query .= " group by ExpertOpinion order by ExpertOpinion";
  $mysql->Prepare($query);
  if(isset($_REQUEST["ExpertID"]))
    $res = $mysql->ExecuteStatement(array($ParentOntologyClassID, $OntologyClassID, $_REQUEST["ExpertID"]));
  else $res = $mysql->ExecuteStatement(array($ParentOntologyClassID, $OntologyClassID));
  echo " &nbsp;[";
  while($prec = $res->fetch())
  {
      if($prec["ExpertOpinion"]=="UNKNOWN")
	echo "<font color=#cc9900> نمی دانم ";
      else if($prec["ExpertOpinion"]=="ACCEPT")
	echo "<font color=green> موافق ";
      else if($prec["ExpertOpinion"]=="REJECT")         
	echo "<font color=red> مخالف ";
      else
	echo " بدون نظر ";
      echo "(".$prec["tcount"].") </font>"; 
  }
  echo " ]";
}

function GetClassComments($OntologyClassID)
{
  $ret = "";
  $mysql = pdodb::getInstance();
  $query = "select ExpertFullName, ExpertComment from projectmanagement.OntologyClassesValidation 
			    JOIN projectmanagement.OntologyValidationExperts using (OntologyValidationExpertID)
			    where 
			    OntologyClassID=? and ExpertComment<>'' ";
  if(isset($_REQUEST["ExpertID"]))
    $query .= " and OntologyValidationExpertID=? ";
  $mysql->Prepare($query);
  if(isset($_REQUEST["ExpertID"]))
    $res = $mysql->ExecuteStatement(array($OntologyClassID, $_REQUEST["ExpertID"]));
  else
    $res = $mysql->ExecuteStatement(array($OntologyClassID));
  while($rec = $res->fetch())
  {
    $ret .= $rec["ExpertFullName"]." : ".$rec["ExpertComment"]."<br>";
  }
  return $ret;
}

function GetClassExtraComments($OntologyClassID)
{
  $ret = "";
  $mysql = pdodb::getInstance();
  $query = "select ExpertFullName, ExtraComment from projectmanagement.OntologyClassesValidation 
			    JOIN projectmanagement.OntologyValidationExperts using (OntologyValidationExpertID)
			    where 
			    OntologyClassID=? and ExtraComment<>'' ";
  if(isset($_REQUEST["ExpertID"]))
    $query .= " and OntologyValidationExpertID=? ";
  $mysql->Prepare($query);
  if(isset($_REQUEST["ExpertID"]))
    $res = $mysql->ExecuteStatement(array($OntologyClassID, $_REQUEST["ExpertID"]));
  else
    $res = $mysql->ExecuteStatement(array($OntologyClassID));
  while($rec = $res->fetch())
  {
    $ret .= $rec["ExpertFullName"]." : ".$rec["ExtraComment"]."<br>";
  }
  return $ret;
}

function GetPropertyComments($OntologyPropertyID)
{
  $ret = "";
  $mysql = pdodb::getInstance();
  $query = "select ExpertFullName, ExpertDescription from projectmanagement.OntologyPropertyValidation 
			    JOIN projectmanagement.OntologyValidationExperts using (OntologyValidationExpertID)
			    where 
			    OntologyPropertyID=? and ExpertDescription<>'' ";
  if(isset($_REQUEST["ExpertID"]))
    $query .= " and OntologyValidationExpertID=? ";
  $mysql->Prepare($query);
  if(isset($_REQUEST["ExpertID"]))
    $res = $mysql->ExecuteStatement(array($OntologyPropertyID, $_REQUEST["ExpertID"]));
  else 
    $res = $mysql->ExecuteStatement(array($OntologyPropertyID));
  while($rec = $res->fetch())
  {
    $ret .= $rec["ExpertFullName"]." : ".$rec["ExpertDescription"]."<br>";
  }
  return $ret;
}

function GetRelationComments($OntologyPropertyID, $DomainOntologyClassID, $RangeOntologyClassID)
{
  $ret = "";
  $mysql = pdodb::getInstance();
  $query = "select ExpertFullName, ExpertDescription from projectmanagement.OntologyClassRelationValidation 
			    JOIN projectmanagement.OntologyValidationExperts using (OntologyValidationExpertID)
			    where 
			    OntologyPropertyID=? and DomainOntologyClassID=? and RangeOntologyClassID=? and ExpertDescription<>'' ";

  if(isset($_REQUEST["ExpertID"]))
    $query .= " and OntologyValidationExpertID=? ";
  
  $mysql->Prepare($query);
  if(isset($_REQUEST["ExpertID"]))
    $res = $mysql->ExecuteStatement(array($OntologyPropertyID, $DomainOntologyClassID, $RangeOntologyClassID, $_REQUEST["ExpertID"]));
  else 
    $res = $mysql->ExecuteStatement(array($OntologyPropertyID, $DomainOntologyClassID, $RangeOntologyClassID));    
  
  while($rec = $res->fetch())
  {
    $ret .= $rec["ExpertFullName"]." : ".$rec["ExpertDescription"]."<br>";
  }
  return $ret;
}

function GetHirarchyComments($OntologyClassID, $ParentOntologyClassID)
{
  $ret = "";
  $mysql = pdodb::getInstance();
  $query = "select ExpertFullName, ExpertDescription from projectmanagement.OntologyClassHirarchyValidation 
			    JOIN projectmanagement.OntologyValidationExperts using (OntologyValidationExpertID)
			    where 
			    OntologyClassID=? and ParentOntologyClassID=? and ExpertDescription<>'' ";
  if(isset($_REQUEST["ExpertID"]))
    $query .= " and OntologyValidationExpertID=? ";

  $mysql->Prepare($query);
  if(isset($_REQUEST["ExpertID"]))
    $res = $mysql->ExecuteStatement(array($OntologyClassID, $ParentOntologyClassID, $_REQUEST["ExpertID"]));
  else
    $res = $mysql->ExecuteStatement(array($OntologyClassID, $ParentOntologyClassID));
  while($rec = $res->fetch())
  {
    $ret .= $rec["ExpertFullName"]." : ".$rec["ExpertDescription"]."<br>";
  }
  return $ret;
}

function ShowTermRefers($TermID)
{
    $mysql = pdodb::getInstance();
    $obj = new be_terms();
    $obj->LoadDataFromDatabase($TermID);
    $mysql->Prepare("select TermReferences.TermReferenceID, TermReferenceMappingID, title, TermReferenceContent.content, TermReferenceMapping.PageNum, ParagraphNo, SentenceNo from projectmanagement.TermReferenceMapping 
    JOIN projectmanagement.TermReferences using (TermReferenceID)
    LEFT JOIN projectmanagement.TermReferenceContent on (TermReferenceContent.TermReferenceID=TermReferences.TermReferenceID and TermReferenceContent.PageNum=TermReferenceMapping.PageNum)
    where TermID=? Order by TermReferenceMappingID DESC");
    $res = $mysql->ExecuteStatement(array($TermID));
    echo "<form method=post>";
    echo "<table width=98% cellspacing=0 cellpadding=5 border=1 align=center>";
    echo "<tr class=HeaderOfTable>";
    echo "<td width=20%>منبع</td><td width=1%>صفحه</td><td width=1%>پاراگراف</td><td>محتوا</td></tr>";
    echo "</tr>";
    while($rec = $res->fetch())
    {
	echo "<tr>";
	echo "<td>";
	echo $rec["title"];
	echo "</td>";
	echo "<td>";
	echo $rec["PageNum"];
	echo "</td>";
	echo "<td>";
	echo $rec["ParagraphNo"];
	echo "</td>";
	echo "<td>";
	echo str_replace($obj->TermTitle, "<font color=red>".$obj->TermTitle."</font>", str_replace("\n", "<br>", $rec["content"]));
	echo "</td>";
	echo "</tr>";
    }
    echo "</table>";
}

function ShowOntoElementRefers($EntityType, $EntityID, $OntologyPropertyPermittedValueID = 0)
{
  echo "<table width=98% cellspacing=0 cellpadding=5 border=1 align=center>";
  echo "<tr><td>فهرست صفحاتی در منابع اطلاعاتی که حاوی واژگان مرتبط با این مفهوم هستند</td></tr>";
  echo "</table>";
  $mysql = pdodb::getInstance();
  $query = "select TermID from projectmanagement.TermOntologyElementMapping
	    where EntityType=? and OntologyEntityID=?";
  if($EntityType=="DATA_RANGE")
  $query .= " and OntologyPropertyPermittedValueID=? ";
  $mysql->Prepare($query);
  if($EntityType=="DATA_RANGE")
    $res = $mysql->ExecuteStatement(array($EntityType, $EntityID, $OntologyPropertyPermittedValueID));
  else 
    $res = $mysql->ExecuteStatement(array($EntityType, $EntityID));
  while($rec = $res->fetch())
  {
    ShowTermRefers($rec["TermID"]);
  }
}

function ShowAllProperties($OntologyClassID, $ClassTitle, $OntologyID)
{
    $mysql = pdodb::getInstance();
    $plist = manage_OntologyClasses::GetClassRelatedProperties($ClassTitle, $OntologyID);  
    if(count($plist)>0)
    {
      $HasProp = false;
      for($m=0; $m<count($plist); $m++)
	if($plist[$m]["PropertyType"]=="DATATYPE")
	{
	  $HasProp = true;
	  echo "خصوصیات زیر را به واسطه مفهوم بالاتر دارد: ";
	  echo "<br>";
	  break;
	}
      echo "<table>\r\n";
      echo "<tr class=HeaderOfTable>";
      //echo "<td></td><td></td>";
      echo "</tr>";
      for($m=0; $m<count($plist); $m++)
      {
	if($plist[$m]["PropertyType"]=="DATATYPE")
	{
	  $PropertyComment = $VStatus = "";
	  echo "<tr>\r\n";
	  echo "<td>";
	  echo "<a href='ShowExpertsResult.php?EntityType=DATA_PROPERTY&EntityID=".$plist[$m]["PropertyID"]."' target=_blank>";
	  echo "<b>".$plist[$m]["PropertyLabel"]."</b> "; // ." (".$plist[$m]["PropertyTitle"].")</td>";
	  echo "</a>";
	  echo "<td>";
	  ShowPropertyValidation($plist[$m]["PropertyID"], $OntologyClassID);
	  
	  $pv = manage_OntologyPropertyPermittedValues::GetList($plist[$m]["PropertyID"]);
	  if(count($pv)>0)
	  {
	    echo " مقادیر مجاز: ";
	    for($k=0; $k<count($pv); $k++)
	    {
	      if($k>0)
		echo " ، ";
	      echo "<a href='ShowExpertsResult.php?EntityType=DATA_RANGE&EntityID=".$plist[$m]["PropertyID"]."&OntologyPropertyPermittedValueID=".$pv[$k]->OntologyPropertyPermittedValueID."' target=_blank>";
	      echo $pv[$k]->PermittedValue;
	      echo "</a>";
	    }
	  }
	  echo "<br>\r\n";

	  $PropComments = GetPropertyComments($plist[$m]["PropertyID"]);
	  if($PropComments!="")
	  {
	      echo "نظرات خبرگان در این خصوص: <br>";
	      echo $PropComments;
	      echo "<br>";
	  }
	  
	  echo "</td>";
	  
	  echo "</tr>";    
	}
      }
      echo "</table>\r\n";
      
      
      echo "<br>";
    }

    $plist = manage_OntologyClasses::GetClassRelatedProperties($ClassTitle, $OntologyID);  
    if(count($plist)>0)
    {
      $HasProp = false;
      for($m=0; $m<count($plist); $m++)
	if($plist[$m]["PropertyType"]=="OBJECT")
	{
	  $HasProp = true;
	  echo " روابط زیر را به واسطه مفهوم بالاتر دارد: <br>";	
	  break;
	}
      echo "<table>\r\n";
      echo "<tr class=HeaderOfTable>";
      echo "</tr>";
      
      $query = "select DomainClassID, RangeClassID, OntologyProperties.OntologyPropertyID, PropertyType, PropertyTitle, domain, `range`
      , (select group_concat(label) from projectmanagement.OntologyPropertyLabels where OntologyPropertyID=OntologyObjectPropertyRestriction.OntologyPropertyID) as PropertyLabel
      , (select group_concat(label) from projectmanagement.OntologyClassLabels where OntologyClassID=OntologyObjectPropertyRestriction.DomainClassID) as DomainClassLabel 
      , (select group_concat(label) from projectmanagement.OntologyClassLabels where OntologyClassID=OntologyObjectPropertyRestriction.RangeClassID) as RangeClassLabel 
      from projectmanagement.OntologyObjectPropertyRestriction 
      JOIN projectmanagement.OntologyProperties using (OntologyPropertyID) 
      where (DomainClassID='".$OntologyClassID."' or RangeClassID='".$OntologyClassID."') and RelationStatus='VALID'";
      $res = $mysql->Execute($query);

      while($rec = $res->fetch())
      {
	  echo "<tr>\r\n";
	  echo "<td>\r\n";
	  echo $rec["DomainClassLabel"];
	  echo "<a href='ShowExpertsResult.php?EntityType=OBJECT_PROPERTY&EntityID=".$rec["OntologyPropertyID"]."' target=_blank>";
	  echo " <b>".$rec["PropertyLabel"]."</b> "; 
	  echo "</a>";
	  echo $rec["RangeClassLabel"];
	  echo "</td>\r\n";
	  
	  echo "<td>\r\n";
	  ShowClassRelationValidation($rec["OntologyPropertyID"], $rec["DomainClassID"], $rec["RangeClassID"]);
	  echo "<br>";
	  $PropComments = GetRelationComments($rec["OntologyPropertyID"], $rec["DomainClassID"], $rec["RangeClassID"]);
	  if($PropComments!="")
	  {
	      echo "نظرات خبرگان در این خصوص: <br>";
	      echo $PropComments;
	      echo "<br>";
	  }
	  
	  echo "</td>\r\n";
	  echo "</tr>\r\n";	    
      }
      
      echo "</table>";
    } // if property exist

    $res2 = $mysql->Execute("select OntologyClassHirarchy.*, label, ClassTitle  
	    from projectmanagement.OntologyClassHirarchy 
	    LEFT JOIN projectmanagement.OntologyClassLabels on (OntologyClassHirarchy.OntologyClassID=OntologyClassLabels.OntologyClassID)
	    LEFT JOIN projectmanagement.OntologyClasses on (OntologyClassHirarchy.OntologyClassID=OntologyClasses.OntologyClassID)
	    where OntologyClassHirarchy.OntologyClassParentID=".$OntologyClassID);
    while($rec2 = $res2->fetch())
    {
      $HirarchyComment = $HStatus = "";
      echo " نوعی <b>".$rec2["label"]."</b> است.";
      ShowClassHirarchyValidation($rec2["OntologyClassID"], $rec2["OntologyClassParentID"]);
      echo "<br>";
      $PropComments = GetHirarchyComments($rec2["OntologyClassParentID"], $rec2["OntologyClassID"]);
      if($PropComments!="")
      {
	  echo "نظرات خبرگان در این خصوص: <br>";
	  echo $PropComments;
	  echo "<br>";
      }
      ShowAllProperties($rec2["OntologyClassID"], $rec2["ClassTitle"], $OntologyID);    
    }
    
}

HTMLBegin();
$mysql = pdodb::getInstance();

if(isset($_REQUEST["EntityType"]))
{
  if(isset($_REQUEST["OntologyPropertyPermittedValueID"]))
    ShowOntoElementRefers($_REQUEST["EntityType"], $_REQUEST["EntityID"], $_REQUEST["OntologyPropertyPermittedValueID"]);
  else
    ShowOntoElementRefers($_REQUEST["EntityType"], $_REQUEST["EntityID"]);
  die();
}

$OntologyID = $_REQUEST["OntologyID"];
$OntologyTitle = $_REQUEST["OntologyTitle"];

if(isset($_REQUEST["Remove"]))
{
  echo "<p align=center><font color=green>نظرات حذف شد</font></p>";
  if(isset($_REQUEST["ExpertID"]))
  {
    $mysql->Prepare("delete from projectmanagement.OntologyClassesValidation where OntologyID=? and OntologyValidationExpertID=?");
    $mysql->ExecuteStatement(array($OntologyID, $_REQUEST["ExpertID"]));

    $mysql->Prepare("delete from projectmanagement.OntologyClassRelationValidation where OntologyID=? and OntologyValidationExpertID=?");
    $mysql->ExecuteStatement(array($OntologyID, $_REQUEST["ExpertID"]));
    
    $mysql->Prepare("delete from projectmanagement.OntologyPropertyValidation where OntologyID=? and OntologyValidationExpertID=?");
    $mysql->ExecuteStatement(array($OntologyID, $_REQUEST["ExpertID"]));
    
    $mysql->Prepare("delete from projectmanagement.OntologyClassHirarchyValidation where OntologyID=? and OntologyValidationExpertID=?");
    $mysql->ExecuteStatement(array($OntologyID, $_REQUEST["ExpertID"]));
    
    $mysql->Prepare("update projectmanagement.OntologyValidationExperts set ValidationStatus='NOT_START' where OntologyValidationExpertID=?");
    $mysql->ExecuteStatement(array($_REQUEST["ExpertID"]));
  }
  else 
  {
    $mysql->Prepare("delete from projectmanagement.OntologyClassesValidation where OntologyID=?");
    $mysql->ExecuteStatement(array($OntologyID));

    $mysql->Prepare("delete from projectmanagement.OntologyClassRelationValidation where OntologyID=?");
    $mysql->ExecuteStatement(array($OntologyID));
    
    $mysql->Prepare("delete from projectmanagement.OntologyPropertyValidation where OntologyID=?");
    $mysql->ExecuteStatement(array($OntologyID));
    
    $mysql->Prepare("delete from projectmanagement.OntologyClassHirarchyValidation where OntologyID=?");
    $mysql->ExecuteStatement(array($OntologyID));
    
    $mysql->Prepare("update projectmanagement.OntologyValidationExperts set ValidationStatus='NOT_START' where OntologyID=?");
    $mysql->ExecuteStatement(array($OntologyID));
  }
  
}

echo "<table width=80% align=center border=1 cellspacing=0 cellpadding=5>";
echo "<tr>";
echo "<td>";
if(isset($_REQUEST["ExpertID"]))
{
  $ExpertObj = new be_OntologyValidationExperts();
  $ExpertObj->LoadDataFromDatabase($_REQUEST["ExpertID"]);
  echo "نظر ".$ExpertObj->ExpertFullName." بر روی هستان نگار: ".$OntologyTitle;
}
else
  echo "تجمیع نظرات خبرگان بر روی هستان نگار: ".$OntologyTitle;
echo "<br>";
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td align=center>";
if(isset($_REQUEST["SelectedClass"]))
{
  echo "<input type=button value='بازگشت' onclick='document.location=\"ShowExpertsResult.php?OntologyID=".$OntologyID."&OntologyTitle=".$OntologyTitle;
  if(isset($_REQUEST["ExpertID"]))
    echo "&ExpertID=".$_REQUEST["ExpertID"];
  echo "\";'>";
}
else
{
  echo "<input type=button value='بستن' onclick='window.close();'>";
}
  if(isset($_REQUEST["ExpertID"]))
    echo "<input type=button value='حذف نظرات این خبره' onclick='javascript: RemoveThisExpert();'>";
  else
    echo "<input type=button value='حذف نظرات همه خبرگان' onclick='javascript: RemoveAllExperts();'>";

echo "<br>";
echo "</td>";
echo "</tr>";

echo "</table><br>";

if(isset($_REQUEST["SelectedClass"]))
{
  $OntologyClassID = $_REQUEST["SelectedClass"];
  $OntologyID =  $_REQUEST["OntologyID"];
  $obj = new be_OntologyClasses();
  $obj->LoadDataFromDatabase($OntologyClassID);
  
  echo "<table width=80% align=center border=1 cellspacing=0 cellpadding=5>";
  echo "<tr>";
  echo "<td>";
  echo "  مفهومی با عنوان <b>".$obj->label."</b> در حوزه موضوع وجود دارد."; // ."(".$rec["ClassTitle"].") ";
  ShowClassvalidation($OntologyClassID);  
  echo "<br>";
  $ClassComments = GetClassComments($OntologyClassID);
  if($ClassComments!="")
  {
      echo "نظرات خبرگان در این خصوص: <br>";
      echo $ClassComments;
      echo "<br>";
  }

  $plist = manage_OntologyClasses::GetClassRelatedProperties($obj->ClassTitle, $OntologyID);  
  if(count($plist)>0)
  {
    $HasProp = false;
    for($m=0; $m<count($plist); $m++)
      if($plist[$m]["PropertyType"]=="DATATYPE")
      {
	$HasProp = true;
	echo "<b>".$obj->label."</b> خصوصیات زیر را دارد: <br>";	
	break;
      }
    echo "<table>\r\n";
    echo "<tr class=HeaderOfTable>";
    //echo "<td></td><td></td>";
    echo "</tr>";
    for($m=0; $m<count($plist); $m++)
    {
      if($plist[$m]["PropertyType"]=="DATATYPE")
      {
	$PropertyComment = $VStatus = "";
	echo "<tr>\r\n";
	echo "<td>";
	echo "<a href='ShowExpertsResult.php?EntityType=DATA_PROPERTY&EntityID=".$plist[$m]["PropertyID"]."' target=_blank>";
	echo "<b>".$plist[$m]["PropertyLabel"]."</b> "; // ." (".$plist[$m]["PropertyTitle"].")</td>";
	echo "</a>";
	echo "<td>";
	ShowPropertyValidation($plist[$m]["PropertyID"], $OntologyClassID);
	
	$pv = manage_OntologyPropertyPermittedValues::GetList($plist[$m]["PropertyID"]);
	if(count($pv)>0)
	{
	  echo " مقادیر مجاز: ";
	  for($k=0; $k<count($pv); $k++)
	  {
	    if($k>0)
	      echo " ، ";
	    echo "<a href='ShowExpertsResult.php?EntityType=DATA_RANGE&EntityID=".$plist[$m]["PropertyID"]."&OntologyPropertyPermittedValueID=".$pv[$k]->OntologyPropertyPermittedValueID."' target=_blank>";
	    echo $pv[$k]->PermittedValue;
	    echo "</a>";
	  }
	}
	echo "<br>\r\n";

	$PropComments = GetPropertyComments($plist[$m]["PropertyID"]);
	if($PropComments!="")
	{
	    echo "نظرات خبرگان در این خصوص: <br>";
	    echo $PropComments;
	    echo "<br>";
	}
	
	echo "</td>";
	
	echo "</tr>";    
      }
    }
    echo "</table>\r\n";
  } // if property exist
  echo "<br>";
  
  $res2 = $mysql->Execute("select OntologyClassHirarchy.*, label, ClassTitle  
	  from projectmanagement.OntologyClassHirarchy 
	  LEFT JOIN projectmanagement.OntologyClassLabels on (OntologyClassHirarchy.OntologyClassID=OntologyClassLabels.OntologyClassID)
	  LEFT JOIN projectmanagement.OntologyClasses on (OntologyClassHirarchy.OntologyClassID=OntologyClasses.OntologyClassID)
	  where OntologyClassHirarchy.OntologyClassParentID=".$OntologyClassID);
  while($rec2 = $res2->fetch())
  {
    $HirarchyComment = $HStatus = "";
    echo "<b>".$obj->label."</b> نوعی <b>".$rec2["label"]."</b> است.";
    ShowClassHirarchyValidation($rec2["OntologyClassID"], $rec2["OntologyClassParentID"]);
    echo "<br>";
    $PropComments = GetHirarchyComments($rec2["OntologyClassParentID"], $rec2["OntologyClassID"]);
    if($PropComments!="")
    {
	echo "نظرات خبرگان در این خصوص: <br>";
	echo $PropComments;
	echo "<br>";
    }
    ShowAllProperties($rec2["OntologyClassID"], $rec2["ClassTitle"], $OntologyID);    
  }
  
  echo "<br>";
    /******************* Class Relations ***********************/
  $plist = manage_OntologyClasses::GetClassRelatedProperties($obj->ClassTitle, $OntologyID);  
  if(count($plist)>0)
  {
    $HasProp = false;
    for($m=0; $m<count($plist); $m++)
      if($plist[$m]["PropertyType"]=="OBJECT")
      {
	$HasProp = true;
	echo "<b>".$obj->label."</b> روابط زیر را با سایر مفاهیم دارد: <br>";	
	break;
      }
    echo "<table>\r\n";
    echo "<tr class=HeaderOfTable>";
    echo "</tr>";
    
    $query = "select DomainClassID, RangeClassID, OntologyProperties.OntologyPropertyID, PropertyType, PropertyTitle, domain, `range`
    , (select group_concat(label) from projectmanagement.OntologyPropertyLabels where OntologyPropertyID=OntologyObjectPropertyRestriction.OntologyPropertyID) as PropertyLabel
    , (select group_concat(label) from projectmanagement.OntologyClassLabels where OntologyClassID=OntologyObjectPropertyRestriction.DomainClassID) as DomainClassLabel 
    , (select group_concat(label) from projectmanagement.OntologyClassLabels where OntologyClassID=OntologyObjectPropertyRestriction.RangeClassID) as RangeClassLabel 
    from projectmanagement.OntologyObjectPropertyRestriction 
    JOIN projectmanagement.OntologyProperties using (OntologyPropertyID) 
    where (DomainClassID='".$OntologyClassID."' or RangeClassID='".$OntologyClassID."') and RelationStatus='VALID'";
    $res = $mysql->Execute($query);

    while($rec = $res->fetch())
    {
	echo "<tr>\r\n";
	echo "<td>\r\n";
	echo $rec["DomainClassLabel"];
	echo "<a href='ShowExpertsResult.php?EntityType=OBJECT_PROPERTY&EntityID=".$rec["OntologyPropertyID"]."' target=_blank>";
	echo " <b>".$rec["PropertyLabel"]."</b> "; 
	echo "</a>";
	echo $rec["RangeClassLabel"];
	echo "</td>\r\n";
	
	echo "<td>\r\n";
	ShowClassRelationValidation($rec["OntologyPropertyID"], $rec["DomainClassID"], $rec["RangeClassID"]);
	echo "<br>";
	$PropComments = GetRelationComments($rec["OntologyPropertyID"], $rec["DomainClassID"], $rec["RangeClassID"]);
	if($PropComments!="")
	{
	    echo "نظرات خبرگان در این خصوص: <br>";
	    echo $PropComments;
	    echo "<br>";
	}
	
	echo "</td>\r\n";
	echo "</tr>\r\n";	    
    }
    
    echo "</table>";
  } // if property exist
    /*****************************************/
  $ClassExtraComments = GetClassExtraComments($OntologyClassID);
  if($ClassExtraComments!="")
  {
      echo "<br><br>";
      echo "نظرات خبرگان در مورد کمبود خصوصیتها یا روابط این مفهوم: <br>";
      echo $ClassExtraComments;
      echo "<br>";
  }
  
  echo "</table><br>";
  ShowOntoElementRefers("Class", $_REQUEST["SelectedClass"]);  
}
else
{
    echo "<table dir=rtl border=1 cellpadding=5 align=center width=80%><tr><td>";
    $mysql = pdodb::getInstance();
    $query = "select OntologyClasses.OntologyClassID, ClassTitle, 
      (select group_concat(label, ' ') from projectmanagement.OntologyClassLabels where OntologyClassLabels.OntologyClassID=OntologyClasses.OntologyClassID group by OntologyClassID) as ClassLabel
      from projectmanagement.OntologyClasses 
      where OntologyID=? and 
      OntologyClassID not in (select OntologyClassParentID from projectmanagement.OntologyClassHirarchy) order by ClassLabel";
    $mysql->Prepare($query);
    $res = $mysql->ExecuteStatement(array($OntologyID));
    while($rec = $res->fetch())
    {
      echo "<a href='ShowExpertsResult.php?SelectedClass=".$rec["OntologyClassID"]."&OntologyID=".$OntologyID."&OntologyTitle=".$OntologyTitle;
      if(isset($_REQUEST["ExpertID"]))
	echo "&ExpertID=".$_REQUEST["ExpertID"];
      echo "'>";
      echo "<font color=black title='".$rec["ClassTitle"]."'>";
      echo $rec["ClassLabel"];
      echo "</font>";
      echo "</a>";
      
      ShowClassvalidation($rec["OntologyClassID"]);  
      echo "<br>";

      ShowChilds(1, $rec["OntologyClassID"]," ");
    }
    echo "</td></tr></table>";
}
?>

<script>
  function Save()
  {
    document.getElementById('SaveType').value='Save';
    document.f1.submit();
  }
  
  function Confirm()
  {
    document.getElementById('SaveType').value='Confirm';
    document.f1.submit();
  }
  function RemoveThisExpert()
  {
    if(confirm('آیا از حذف کلیه نظرات این خبره اطمینان دارید؟'))
      document.location='ShowExpertsResult.php?OntologyID=<? echo $OntologyID ?>&OntologyTitle=<? echo $OntologyTitle ?>&ExpertID=<? if(isset($_REQUEST["ExpertID"])) echo $_REQUEST["ExpertID"]; ?>&Remove=1';
  }
  function RemoveAllExperts()
  {
    if(confirm('آیا از حذف کلیه نظرات همه خبرگان اطمینان دارید؟'))
      document.location='ShowExpertsResult.php?OntologyID=<? echo $OntologyID ?>&OntologyTitle=<? echo $OntologyTitle ?>&Remove=1';
  }
</script>

</html>
