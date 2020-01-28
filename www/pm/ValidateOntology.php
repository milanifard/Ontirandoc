<?php 
/*
ارزیابی عناصر هستان نگار
*/
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', "on");

include_once 'sys_config.class.php';
//include_once 'definitions.inc';
//require_once config::$root_path.config::$framework_path.'User.class.php';
//require_once config::$root_path.config::$ui_components_path.'HTMLUtil.class.php';
//require_once config::$root_path.config::$framework_path.'FrameworkUtil.class.php';
//require_once config::$root_path.config::$framework_path.'System.class.php';

//require_once('session.inc.php');
//include_once(config::$language.'_utf8.inc.php');

session_start();

set_include_path(get_include_path() . PATH_SEPARATOR . getenv("DOCUMENT_ROOT") . "/generalClasses");
set_include_path(get_include_path() . PATH_SEPARATOR . getenv("DOCUMENT_ROOT") . "/generalUI/ext4");

//require_once 'PDODataAccess.class.php';
//require_once 'DataAudit.class.php';
//require_once('classconfig.inc.php');

//--------------------------------------------------
//require_once inc_PDODataAccess;
//require_once inc_component;

include_once("classes/OntologyClasses.class.php");
include_once("classes/OntologyClassLabels.class.php");
include_once("classes/OntologyClassHirarchy.class.php");
include_once("classes/ontologies.class.php");
include_once("classes/OntologyPropertyPermittedValues.class.php");
include_once("classes/terms.class.php");

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
    /*
    echo $indent."<a href='#' onclick=\"javascript: SelectItem('";
    echo $rec["OntologyClassID"];
    echo "')\">";
    echo "<img src='images/chain.gif' border=0></a>".$rec["ClassLabel"]." (".$rec["ClassTitle"].")<br>";
    */
    echo "<a href='ValidateOntology.php?SelectedClass=".$rec["OntologyClassID"]."'>";
    if($vrec = GetClassValidationRecord($rec["OntologyClassID"]))
    {
      if($vrec["ExpertOpinion"]=="ACCEPT")
	echo "<font color=green  title='".$rec["ClassTitle"]."'>";
      else if($vrec["ExpertOpinion"]=="REJECT")
	echo "<font color=red  title='".$rec["ClassTitle"]."'>";
      else if($vrec["ExpertOpinion"]=="UNKNOWN")
	echo "<font color=#cc9900  title='".$rec["ClassTitle"]."'>";
      else {
	echo "<font color=black  title='".$rec["ClassTitle"]."'>";
      }
      
    }
    else {
	echo "<font color=black  title='".$rec["ClassTitle"]."'>";
    }
    
    echo $rec["ClassLabel"];
    echo "</font>";
    echo "</a>";
    echo "<br>";
    if($i<$totalcount)
      $indent = $PreIndent."<img src='images/i.gif'>";
    else 
      $indent = $PreIndent."<img src='images/e.gif'>";
    ShowChilds($LevelNo, $rec["OntologyClassID"], $indent);
  }
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

function ShowOntoElementRefers($EntityType, $EntityID)
{
  echo "<table width=98% cellspacing=0 cellpadding=5 border=1 align=center>";
  echo "<tr><td>فهرست صفحاتی در منابع اطلاعاتی که حاوی واژگان مرتبط با این مفهوم هستند</td></tr>";
  echo "</table>";
  $mysql = pdodb::getInstance();
  $query = "select TermID from projectmanagement.TermOntologyElementMapping
	    where EntityType=? and OntologyEntityID=?";
  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($EntityType, $EntityID));
  while($rec = $res->fetch())
  {
    ShowTermRefers($rec["TermID"]);
  }
}

function GetClassValidationRecord($OntologyClassID)
{
  $mysql = pdodb::getInstance();
  //echo "<br>";
  $query = "select * from projectmanagement.OntologyClassesValidation where 
			    OntologyClassID=".$OntologyClassID." and 
			    OntologyValidationExpertID=".$_SESSION["ExpertID"];
  $res = $mysql->Execute($query);
  return $res->fetch();
}


function GetClassRelationValidationRecord($PropertyID, $DomainOntologyClassID, $RangeOntologyClassID)
{
  $mysql = pdodb::getInstance();
  //echo "<br>";
  $query = "select * from projectmanagement.OntologyClassRelationValidation where 
			    OntologyPropertyID=".$PropertyID." and 
			    DomainOntologyClassID=".$DomainOntologyClassID." and 
			    RangeOntologyClassID=".$RangeOntologyClassID." and 
			    OntologyValidationExpertID=".$_SESSION["ExpertID"];
  $res = $mysql->Execute($query);
  return $res->fetch();
}

function GetPropertyValidationRecord($PropertyID, $OntologyClassID)
{
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select * from projectmanagement.OntologyPropertyValidation where 
			    OntologyPropertyID=".$PropertyID." and 
			    RelatedOntologyClassID=".$OntologyClassID." and 
			    OntologyValidationExpertID=".$_SESSION["ExpertID"]);
  return $res->fetch();
}

function GetClassHirarchyValidationRecord($ParentOntologyClassID, $OntologyClassID)
{
  $mysql = pdodb::getInstance();
  $res = $mysql->Execute("select * from projectmanagement.OntologyClassHirarchyValidation where 
			    ParentOntologyClassID=".$ParentOntologyClassID." and 
			    OntologyClassID=".$OntologyClassID." and 
			    OntologyValidationExpertID=".$_SESSION["ExpertID"]);
  return $res->fetch();
}

?>
<style type="text/css" >
body {
	min-width: 400px; 
	color: #000;
	/*background:#3b5f8a url(../rcssimgs/bluelines.png);*/
	font-family: Tahoma;
	font-size: 13 pt;
	}

th, td {
	font-family: Tahoma;
	color: #000;
	margin: 0;
	vertical-align:top;
	font-size: 12px;
	}
a:link {
	color:#011dc9;
	background: transparent;
	text-decoration:none;
	}

a:visited, a:active {
	color:#011a9b;
	background: transparent;
	text-decoration:none;
	}

a:hover {
	color:#001373;
	background: transparent;
	text-decoration:underline;
	}
	
INPUT, SELECT { font-family: Tahoma }
</style>

<body  dir=rtl link="#0000FF" alink="#0000FF" vlink="#0000FF">
<?
$mysql = pdodb::getInstance();
if(isset($_REQUEST["logout"]) || isset($_REQUEST["Finish"]))
{
  echo "<table width=80% align=center border=1 cellspacing=0 cellpadding=5>";
  echo "<tr>";
  echo "<td>";
  echo "خبره محترم <font color=#990099>".$_SESSION["ExpertFullName"]."</font> از همکاری شما سپاسگزاریم";
  echo "<br>";
  echo "برای ورود مجدد  ";
  echo "<a href='ValidateOntology.php'>اینجا</a>";
  echo " را کلیک کنید.";
  echo "</td>";
  echo "</tr>";
  echo "</table><br>";
  if(isset($_REQUEST["Finish"]))
  {
    $mysql->Execute("update projectmanagement.OntologyValidationExperts set ValidationStatus='DONE' where OntologyValidationExpertID='".$_SESSION["ExpertID"]."'");
  }
  else {
      $mysql->Execute("update projectmanagement.OntologyValidationExperts set ValidationStatus='IN_PROGRESS' where OntologyValidationExpertID='".$_SESSION["ExpertID"]."'");
  }
  
  unset($_SESSION["ExpertID"]);  
  die();
}
if(isset($_REQUEST["ExpertEnterCode"]))
{
  $mysql->Prepare("select * from projectmanagement.OntologyValidationExperts where ExpertEnterCode=?");
  $res = $mysql->ExecuteStatement(array($_REQUEST["ExpertEnterCode"]));
  if($rec = $res->fetch())
  {
    $_SESSION["OntologyID"] = $rec["OntologyID"];
    $_SESSION["ExpertID"] = $rec["OntologyValidationExpertID"];
    $_SESSION["ExpertFullName"] = $rec["ExpertFullName"];
    $_SESSION["ValidationStatus"] = $rec["ValidationStatus"];
  }
  else {
    unset($_SESSION["ExpertID"]);
      echo "<font color=red>کد ورود نادرست است</font>";
  }
  
}
if(!isset($_SESSION["ExpertID"]))
{
  echo "<form>";
  echo "<table width=80% align=center border=1 cellspacing=0 cellpadding=5>";
  echo "<tr>";
  echo "<td>کد ورود: <input type=text name='ExpertEnterCode' id='ExpertEnterCode' value=''> <input type=submit value='ورود'>";
  echo "</tr>";
  echo "</table>";
  echo "</form>";
  die();
}
echo "<table width=80% align=center border=1 cellspacing=0 cellpadding=5>";
echo "<tr>";
echo "<td>";
echo "خبره محترم <font color=#990099>".$_SESSION["ExpertFullName"]."</font> به سامانه ارزیابی هستان نگار خوش آمدید";
echo "<br>";
echo "ساختار سلسله مراتبی زیر، مدلی از مفاهیم موجود در حوزه مورد مطالعه می باشد.<br>";
echo "لطفا با کلیک کردن بر روی عنوان هر مفهوم و وارد شدن به صفحه مشخصات آن، نظر خود را در مورد آن مفهوم، خصوصیات و روابط آن با سایر مفاهیم ثبت نمایید.<br>";
echo "مفاهیمی که از نظر شما مورد قبول باشد به رنگ سبز، رد شده ها به رنگ قرمز و مواردی که در خصوص آنها نظر مشخصی ندارید به رنگ زرد نشان داده می شوند.  <br>";
echo "زمانیکه کار ارزیابی به اتمام رسید، ";
echo "<a href='ValidateOntology.php?Finish=1'>اینجا</a>";
echo " را کلیک کنید.";
echo "<br>";
echo "چنانچه هنوز ارزیابی را تمام نکرده و تمایل دارید بعدا کار را تکمیل کنید، برای خروج  ";
echo "<a href='ValidateOntology.php?logout=1'>اینجا</a>";
echo " را کلیک کنید.";
echo "</td>";
echo "</tr>";
echo "</table><br>";

if(isset($_REQUEST["SelectedClass"]))
{
  $OntologyClassID = $_REQUEST["SelectedClass"];
  echo "<form method=\"POST\" id=f1 name=f1 enctype=\"multipart/form-data\">";
  $ValidationStatus = $_SESSION["ValidationStatus"];
  echo "<table width=80% align=center border=1 cellspacing=0 cellpadding=5>";
  
  if(isset($_REQUEST["SaveType"]))
  {
    echo "<p align=center><font color=green>نظرات شما ثبت گردید</font></p>";
    $mysql->Prepare("select * from projectmanagement.OntologyClasses where OntologyClassID=?");
    $res = $mysql->ExecuteStatement(array($OntologyClassID));
    $rec = $res->fetch();
    
    $mysql->Prepare("delete from projectmanagement.OntologyClassesValidation where OntologyClassID=? and OntologyValidationExpertID=".$_SESSION["ExpertID"]);
    $mysql->ExecuteStatement(array($OntologyClassID));
    
    $mysql->Prepare("delete from projectmanagement.OntologyPropertyValidation where RelatedOntologyClassID=? and OntologyValidationExpertID=".$_SESSION["ExpertID"]);
    $mysql->ExecuteStatement(array($OntologyClassID));
    
    $mysql->Prepare("delete from projectmanagement.OntologyClassHirarchyValidation where OntologyClassID=? and  OntologyValidationExpertID=".$_SESSION["ExpertID"]);
    $mysql->ExecuteStatement(array($OntologyClassID));
    
    $ExpertOpinion = $_REQUEST["ExpertOpinion"];
    $ExpertDescription = $_REQUEST["ExpertComment"];
    $ExtraComment = $_REQUEST["ExtraComment"];
    $query = "insert into projectmanagement.OntologyClassesValidation (OntologyID, OntologyClassID, ExpertOpinion, ExpertComment, OntologyValidationExpertID, ExtraComment) ";
    $query .= " values ('".$_SESSION["OntologyID"]."', '".$OntologyClassID."', ?, ?, '".$_SESSION["ExpertID"]."', ?)";
    $mysql->Prepare($query);
    $mysql->ExecuteStatement(array($ExpertOpinion, $ExpertDescription, $ExtraComment));
    $plist = manage_OntologyClasses::GetClassRelatedProperties($rec["ClassTitle"], $_SESSION["OntologyID"]);  
    for($m=0; $m<count($plist); $m++)
    {
      if($plist[$m]["PropertyType"]=="DATATYPE")
      {
	$query = "insert into projectmanagement.OntologyPropertyValidation (OntologyID, OntologyPropertyID, RelatedOntologyClassID, ExpertOpinion, ExpertDescription, OntologyValidationExpertID) ";
	$query .= " values ('".$_SESSION["OntologyID"]."', '".$plist[$m]["PropertyID"]."', '".$OntologyClassID."', ?, ?, '".$_SESSION["ExpertID"]."')";
	$ExpertOpinion = $_REQUEST["p_".$plist[$m]["PropertyID"]];
	$ExpertDescription = $_REQUEST["p_c_".$plist[$m]["PropertyID"]];
	$mysql->Prepare($query);
	$mysql->ExecuteStatement(array($ExpertOpinion, $ExpertDescription));
      }
    }
    
    $res2 = $mysql->Execute("select OntologyClassHirarchy.*, label 
	from projectmanagement.OntologyClassHirarchy 
	LEFT JOIN projectmanagement.OntologyClassLabels on (OntologyClassHirarchy.OntologyClassID=OntologyClassLabels.OntologyClassID)
	where OntologyClassHirarchy.OntologyClassParentID=".$OntologyClassID);
    while($rec2 = $res2->fetch())
    {
	$query = "insert into projectmanagement.OntologyClassHirarchyValidation (OntologyID, ParentOntologyClassID, OntologyClassID, ExpertOpinion, ExpertDescription, OntologyValidationExpertID) ";
	$query .= " values ('".$_SESSION["OntologyID"]."', '".$rec2["OntologyClassID"]."', '".$rec2["OntologyClassParentID"]."', ?, ?, '".$_SESSION["ExpertID"]."')";
	$ExpertOpinion = $_REQUEST["h_".$rec2["OntologyClassHirarchyID"]];
	$ExpertDescription = $_REQUEST["h_c_".$rec2["OntologyClassHirarchyID"]];
	$mysql->Prepare($query);
	$mysql->ExecuteStatement(array($ExpertOpinion, $ExpertDescription));
    }
    
  }
  
  $mysql->Prepare("select OntologyClasses.OntologyClassID, 
			    OntologyClasses.ClassTitle,
			    OntologyClassLabels.label, 
			    OntologyClassesValidation.ExpertOpinion,
			    OntologyClassesValidation.ExpertComment,
			    OntologyClassesValidation.ExtraComment
			    from projectmanagement.OntologyClasses 
			    LEFT JOIN projectmanagement.OntologyClassLabels on (OntologyClasses.OntologyClassID=OntologyClassLabels.OntologyClassID)
			    LEFT JOIN projectmanagement.OntologyClassesValidation on (OntologyClassesValidation.OntologyClassID=OntologyClasses.OntologyClassID and OntologyValidationExpertID=".$_SESSION["ExpertID"].")
			    where OntologyClasses.OntologyClassID=?");
  $res = $mysql->ExecuteStatement(array($OntologyClassID));
  $rec = $res->fetch();
  $ExtraComment = $rec["ExtraComment"];
  echo "<tr>";
  echo "<td>";
  echo "  مفهومی با عنوان <b>".$rec["label"]."</b> در حوزه موضوع وجود دارد."; // ."(".$rec["ClassTitle"].") ";
  echo "<select name='ExpertOpinion' id='ExpertOpinion'>\r\n";
  echo "<option value='NONE'>-";
  echo "<option value='UNKNOWN' ";
  if($rec["ExpertOpinion"]=="UNKNOWN")
    echo "selected ";
  echo ">برای من نامشخص است";
  echo "<option value='ACCEPT' ";
  if($rec["ExpertOpinion"]=="ACCEPT")
    echo "selected ";
  echo " >مورد تایید است";
  echo "<option value='REJECT' ";
  if($rec["ExpertOpinion"]=="REJECT")
    echo "selected ";
  echo ">مورد قبول نیست";
  echo "</select>\r\n";
  echo "<img onclick='javascript: document.getElementById(\"ExpertComment\").style.display=\"\";' src='images/document.jpg' title='توضیح'> \r\n";
  echo "<input style='display: ";
  if($rec["ExpertComment"]=="")
    echo " none ";
  echo "' type=text name='ExpertComment' id='ExpertComment' value='".$rec["ExpertComment"]."'><br>\r\n";
  $plist = manage_OntologyClasses::GetClassRelatedProperties($rec["ClassTitle"], $_SESSION["OntologyID"]);  
  if(count($plist)>0)
  {
    $HasProp = false;
    for($m=0; $m<count($plist); $m++)
      if($plist[$m]["PropertyType"]=="DATATYPE")
      {
	$HasProp = true;
	echo "<b>".$rec["label"]."</b> خصوصیات زیر را دارد: <br>";	
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
	if($prec = GetPropertyValidationRecord($plist[$m]["PropertyID"], $rec["OntologyClassID"]))
	{
	  $PropertyComment = $prec["ExpertDescription"];
	  $VStatus = $prec["ExpertOpinion"];
	}
	echo "<tr>\r\n";
	echo "<td><b>".$plist[$m]["PropertyLabel"]."</b> "; // ." (".$plist[$m]["PropertyTitle"].")</td>";
	echo "<td>";
	echo "<select name='p_".$plist[$m]["PropertyID"]."' id='p_".$plist[$m]["PropertyID"]."'>";
	echo "<option value='NONE'>-";
	echo "<option value='UNKNOWN' ";
	if($VStatus=="UNKNOWN")
	  echo " selected ";
	echo ">نمی دانم";
	echo "<option value='ACCEPT' ";
	if($VStatus=="ACCEPT")
	  echo " selected ";
	echo ">مورد تایید است";
	echo "<option value='REJECT' ";
	if($VStatus=="REJECT")
	  echo " selected ";
	echo ">مورد قبول نیست";
	echo "</select>\r\n";
	echo "<img onclick='javascript: document.getElementById(\"p_c_".$plist[$m]["PropertyID"]."\").style.display=\"\";' src='images/document.jpg' title='توضیح'> \r\n";
	echo "<input style='display: ";
	if($PropertyComment=="")
	  echo " none ";
	echo "' type=text name='p_c_".$plist[$m]["PropertyID"]."' id='p_c_".$plist[$m]["PropertyID"]."' value='".$PropertyComment."'> ";
	$pv = manage_OntologyPropertyPermittedValues::GetList($plist[$m]["PropertyID"]);
	if(count($pv)>0)
	{
	  echo "مقادیر مجاز: ";
	  for($k=0; $k<count($pv); $k++)
	  {
	    if($k>0)
	      echo " ، ";
	    echo $pv[$k]->PermittedValue;
	  }
	}
	echo "<br>\r\n";
	echo "</td>";
	
	echo "</tr>";    
      }
    }
    echo "</table>\r\n";
  } // if property exist
  $res2 = $mysql->Execute("select OntologyClassHirarchy.*, label 
	  from projectmanagement.OntologyClassHirarchy 
	  LEFT JOIN projectmanagement.OntologyClassLabels on (OntologyClassHirarchy.OntologyClassID=OntologyClassLabels.OntologyClassID)
	  where OntologyClassHirarchy.OntologyClassParentID=".$rec["OntologyClassID"]);
  while($rec2 = $res2->fetch())
  {
    $HirarchyComment = $HStatus = "";
    if($prec = GetClassHirarchyValidationRecord($rec2["OntologyClassID"], $rec2["OntologyClassParentID"]))
    {
	$HirarchyComment = $prec["ExpertDescription"];
	$HStatus = $prec["ExpertOpinion"];
    }
    echo "<br><b>".$rec["label"]."</b> نوعی <b>".$rec2["label"]."</b> است.";
    echo "<select name='h_".$rec2["OntologyClassHirarchyID"]."' id='h_".$rec2["OntologyClassHirarchyID"]."'>";
    echo "<option value='NONE'>-</option>";
    echo "<option value='UNKNOWN' ";
    if($HStatus=="UNKNOWN")
      echo " selected ";
    echo ">نمی دانم</option>";
    echo "<option value='ACCEPT' ";
    if($HStatus=="ACCEPT")
      echo " selected ";
    echo ">مورد تایید است</option>";
    echo "<option value='REJECT' ";
    if($HStatus=="REJECT")
      echo " selected ";
    echo ">مورد قبول نیست</option>";
    echo "</select>";
    echo "<img onclick='javascript: document.getElementById(\"h_c_".$rec2["OntologyClassHirarchyID"]."\").style.display=\"\";' src='images/document.jpg' title='توضیح'> ";
    echo "<input style='display: ";
    if($HirarchyComment=="")
      echo "none";
    echo "' type=text name='h_c_".$rec2["OntologyClassHirarchyID"]."' id='h_c_".$rec2["OntologyClassHirarchyID"]."' value='".$HirarchyComment."'><br>";
  }
  
  
    /******************* Class Relations ***********************/
  $plist = manage_OntologyClasses::GetClassRelatedProperties($rec["ClassTitle"], $_SESSION["OntologyID"]);  
  if(count($plist)>0)
  {
    $HasProp = false;
    for($m=0; $m<count($plist); $m++)
      if($plist[$m]["PropertyType"]=="OBJECT")
      {
	$HasProp = true;
	echo "<b>".$rec["label"]."</b> روابط زیر را با سایر مفاهیم دارد: <br>";	
	break;
      }
    echo "<table>\r\n";
    echo "<tr class=HeaderOfTable>";
    echo "</tr>";
    
    $query = "select DomainClassID, RangeClassID, OntologyProperties.OntologyPropertyID, PropertyType, PropertyTitle, domain, `range`
    , (select group_concat(label) from OntologyPropertyLabels where OntologyPropertyID=OntologyObjectPropertyRestriction.OntologyPropertyID) as PropertyLabel
    , (select group_concat(label) from OntologyClassLabels where OntologyClassID=OntologyObjectPropertyRestriction.DomainClassID) as DomainClassLabel 
    , (select group_concat(label) from OntologyClassLabels where OntologyClassID=OntologyObjectPropertyRestriction.RangeClassID) as RangeClassLabel 
    from projectmanagement.OntologyObjectPropertyRestriction 
    JOIN projectmanagement.OntologyProperties using (OntologyPropertyID) 
    where (DomainClassID='".$OntologyClassID."' or RangeClassID='".$OntologyClassID."') and RelationStatus='VALID'";
    $res = $mysql->Execute($query);

    while($rec = $res->fetch())
    {
      $RelationComment = $VStatus = "";
      $cl = manage_OntologyClasses::GetClassIDAndLabel($_SESSION["OntologyID"], $RangeClasses[$k]);
      $SelectBoxID = "r_".$rec["OntologyPropertyID"]."_".$rec["DomainClassID"]."_".$rec["RangeClassID"];
      $CommentID = "r_c_".$rec["OntologyPropertyID"]."_".$rec["DomainClassID"]."_".$rec["RangeClassID"];
      
      if(isset($_REQUEST["SaveType"]))
      {
	  $mysql->Prepare("delete from projectmanagement.OntologyClassRelationValidation where 
				      OntologyPropertyID=? and DomainOntologyClassID=? and RangeOntologyClassID=? and OntologyValidationExpertID=".$_SESSION["ExpertID"]);
	  $mysql->ExecuteStatement(array($rec["OntologyPropertyID"], $rec["DomainClassID"], $rec["RangeClassID"]));
	  $query = "insert into projectmanagement.OntologyClassRelationValidation
			    (OntologyID, OntologyPropertyID, DomainOntologyClassID, RangeOntologyClassID, ExpertOpinion, ExpertDescription, OntologyValidationExpertID)
			    values (?, ?, ?, ?, ?, ?, ?)";
	  $mysql->Prepare($query);
	  $mysql->ExecuteStatement(array($_SESSION["OntologyID"], $rec["OntologyPropertyID"], $rec["DomainClassID"], $rec["RangeClassID"], $_REQUEST[$SelectBoxID], $_REQUEST[$CommentID], $_SESSION["ExpertID"]));
      }
      if($prec = GetClassRelationValidationRecord($rec["OntologyPropertyID"], $rec["DomainClassID"], $rec["RangeClassID"]))
      {
	$RelationComment = $prec["ExpertDescription"];
	$VStatus = $prec["ExpertOpinion"];
      }
      
      echo "<tr>\r\n";
      echo "<td>\r\n";
      echo $rec["DomainClassLabel"];
      echo " <b>".$rec["PropertyLabel"]."</b> "; 
      echo $rec["RangeClassLabel"];
      echo "</td>\r\n";
      
      echo "<td>\r\n";
      echo "<select name='".$SelectBoxID."' id='".$SelectBoxID."'>\r\n";
      echo "<option value='NONE'>-</option>";
      echo "<option value='UNKNOWN' ";
      if($VStatus=="UNKNOWN")
	echo " selected ";
      echo ">نمی دانم</option>";
      echo "<option value='ACCEPT' ";
      if($VStatus=="ACCEPT")
	echo " selected ";
      echo ">مورد تایید است</option>";
      echo "<option value='REJECT' ";
      if($VStatus=="REJECT")
	echo " selected ";
      echo ">مورد قبول نیست</option>";
      echo "</select>\r\n";
      echo "<img onclick='javascript: document.getElementById(\"".$CommentID."\").style.display=\"\";' src='images/document.jpg' title='توضیح'>\r\n ";
      echo "<input style='display: ";
      if($RelationComment=="")
	echo " none ";
      echo "' type=text name='".$CommentID."' id='".$CommentID."' value='".$RelationComment."'><br>\r\n";
      
      echo "</td>\r\n";
      echo "</tr>\r\n";	    
    }
    
    echo "</table>";
  } // if property exist
    /*****************************************/
  //if($_SESSION["ExpertID"]==2)
  {
      echo "<tr><td colspan=3>";
      echo "چنانچه اعتقاد دارید خصوصیات دیگری باید به این مفهوم اضافه شود یا این مفهوم روابط دیگری نیز با سایر مفاهیم دارد ";
      echo " در این قسمت ثبت نمایید: ";
      echo "<br>";
      echo "<textarea id='ExtraComment' name='ExtraComment' cols=80 rows=5>".$ExtraComment."</textarea>";
      echo "</td></tr>";  
  }
  echo "<input type=hidden name='SelectedClass' id='SelectedClass' value='".$_REQUEST["SelectedClass"]."'>";
  echo "<tr class=FooterOfTable><td colspan=3>";
  echo "<input type=button value='ذخیره' onclick='javascript: Save();'>";
  echo "&nbsp;";
  echo "<input type=button value='بازگشت' onclick='document.location=\"ValidateOntology.php\"'>";
  echo "</td></tr>";
  echo "<input type=hidden name='SaveType' id='SaveType' value='Save'>";
  echo "</form>";
  echo "</table><br>";
  ShowOntoElementRefers("Class", $_REQUEST["SelectedClass"]);      
}
else
{
  //ShowAllClasses();
  
    echo "<table dir=rtl border=1 cellpadding=5 align=center width=80%><tr><td>";
    $mysql = pdodb::getInstance();
    $query = "select OntologyClasses.OntologyClassID, ClassTitle, 
      (select group_concat(label, ' ') from projectmanagement.OntologyClassLabels where OntologyClassLabels.OntologyClassID=OntologyClasses.OntologyClassID group by OntologyClassID) as ClassLabel
      from projectmanagement.OntologyClasses 
      where OntologyID=? and 
      OntologyClassID not in (select OntologyClassParentID from projectmanagement.OntologyClassHirarchy) order by ClassLabel";
    $mysql->Prepare($query);
    $res = $mysql->ExecuteStatement(array($_SESSION["OntologyID"]));
    while($rec = $res->fetch())
    {
      echo "<a href='ValidateOntology.php?SelectedClass=".$rec["OntologyClassID"]."' title='تعیین وضعیت'>";
      if($vrec = GetClassValidationRecord($rec["OntologyClassID"]))
      {
	if($vrec["ExpertOpinion"]=="ACCEPT")
	  echo "<font color=green title='".$rec["ClassTitle"]."'>";
	else if($vrec["ExpertOpinion"]=="REJECT")
	  echo "<font color=red title='".$rec["ClassTitle"]."'>";
	else if($vrec["ExpertOpinion"]=="UNKNOWN")
	  echo "<font color=#cc9900 title='".$rec["ClassTitle"]."'>";
	else {
	  echo "<font color=black title='".$rec["ClassTitle"]."'>";
	}
	
      }
      else {
	  echo "<font color=black title='".$rec["ClassTitle"]."'>";
      }
      
      echo $rec["ClassLabel"];
      echo "</font>";
      echo "</a>";
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
</script>

</html>
