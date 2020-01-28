<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : ارتباط اصطلاحات و مراجع
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-7
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/TermReferenceMapping.class.php");
include_once("classes/TermReferences.class.php");
include_once("classes/terms.class.php");
include_once("classes/TermReferenceContent.class.php");
include_once("classes/OntologyClasses.class.php");
include_once("classes/OntologyProperties.class.php");
include_once("classes/OntologyClassHirarchy.class.php");

function GetRelatedOntologyElement($TermID)
{
  $ElementName = "";
  $mysql = pdodb::getInstance();
  $mysql->Prepare("select EntityType, OntologyClassLabels.label as ClassLabel, OntologyPropertyLabels.label as PropertyLabel,
		    ClassTitle, PropertyTitle, PermittedValue
		    from projectmanagement.TermOntologyElementMapping 
		    LEFT JOIN projectmanagement.OntologyClasses on (OntologyClasses.OntologyClassID=OntologyEntityID)
		    LEFT JOIN projectmanagement.OntologyClassLabels on (OntologyClasses.OntologyClassID=OntologyClassLabels.OntologyClassID)
		    LEFT JOIN projectmanagement.OntologyProperties on (OntologyProperties.OntologyPropertyID=OntologyEntityID)
		    LEFT JOIN projectmanagement.OntologyPropertyLabels on (OntologyProperties.OntologyPropertyID=OntologyPropertyLabels.OntologyPropertyID)
		    LEFT JOIN projectmanagement.OntologyPropertyPermittedValues using (OntologyPropertyPermittedValueID)
		    where TermID=?");
  $res = $mysql->ExecuteStatement(array($TermID));
  if($rec = $res->fetch())
  {
    if($rec["EntityType"]=="CLASS")
      $ElementName = $rec["ClassLabel"]." (".$rec["ClassTitle"].")";
    else if($rec["EntityType"]=="DATA_RANGE")
      $ElementName = $rec["PropertyLabel"]." (".$rec["PropertyTitle"].") - ".$rec["PermittedValue"];
    else
      $ElementName = $rec["PropertyLabel"]." (".$rec["PropertyTitle"].")";
  }
  return $ElementName;
}

function ShowPropertyList($plist, $OntologyID)
{
  $mysql = pdodb::getInstance();
    for($m=0; $m<count($plist); $m++)
    {
	echo "<tr>";
	echo "<td >";
	if($plist[$m]["PropertyType"]=="OBJECT")
	  echo "رابطه: ";	                
	if($plist[$m]["PropertyType"]=="OBJECT")
	{
	    $DomainList = explode(", ",$plist[$m]["domain"]);
	    for($j=0; $j<count($DomainList); $j++)
	    {
	      $ClassIDLabel = manage_OntologyClasses::GetClassIDAndLabel($OntologyID, $DomainList[$j]);
	      if($j>0)
		echo " - ";
	      echo $ClassIDLabel["label"];
	    }
	    echo " ";
	}
	echo "<b><a href=# onclick=\"window.open('ManageOntologyProperties.php?UpdateID=".$plist[$m]["PropertyID"]."&OntologyID=".$OntologyID."');\" >".$plist[$m]["PropertyLabel"]."</a></b>";

	if($plist[$m]["PropertyType"]=="OBJECT")
	{
	  echo " ";
	    $RangeList = explode(", ",$plist[$m]["range"]);
	    for($j=0; $j<count($RangeList); $j++)
	    {
	      $ClassIDLabel = manage_OntologyClasses::GetClassIDAndLabel($OntologyID, $RangeList[$j]);
	      if($j>0)
		echo " - ";
	      echo $ClassIDLabel["label"];
	    }
	    echo " ";
	}
	if($plist[$m]["PropertyType"]=="DATATYPE")
	{
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
	}
	echo "</td></tr>";    
    }
}

function GetSimilarTerm($TermTitle, $SimilarList, $thereshold)
{
  $mysql = pdodb::getInstance();
  $mysql->Prepare("select TermID, TermTitle, CreatorUserID from projectmanagement.terms ");
  $res = $mysql->ExecuteStatement(array());
  $i=0;
  while($rec = $res->fetch())
  {
    $distance = levenshtein($TermTitle, $rec["TermTitle"]);
    $p = (1-($distance/max(strlen($TermTitle), strlen($rec["TermTitle"]))) )*100;
    if($p>$thereshold)
    {
      $SimilarList[$i]["TermID"] = $rec["TermID"];
      $SimilarList[$i]["TermTitle"] = $rec["TermTitle"];
      $SimilarList[$i]["CreatorUserID"]=$rec["CreatorUserID"];
      $SimilarList[$i]["reason"] = "شباهت ساختاری";
      $i++;
    }
  }
  return $SimilarList;
}

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

if(isset($_REQUEST["CheckNewTerm"]))
{
  $SimilarList = array();
  $SimilarList = GetSimilarTerm($_REQUEST["CheckNewTerm"], $SimilarList, 70);
  if(count($SimilarList)==0)
  {
    echo "<script>AddNewTerm(\"".$_REQUEST["CheckNewTerm"]."\");</script>";
    //die();
  }
  echo "واژگان مشابهی که می توانند به جای این واژه استفاده شوند: <br>";
  for($i=0; $i<count($SimilarList); $i++)
  {
    echo "<a href='#' onclick='javascript: document.getElementById(\"NewTerm\").value=\"\"; document.getElementById(\"Item_TermID\").value=\"".$SimilarList[$i]["TermID"]."\"; document.getElementById(\"SimilarSpan\").style.display=\"none\";'>";
    echo $SimilarList[$i]["TermTitle"];
    echo "</a>";
    echo "<br>";
  }
  echo "<b>هیچکدام از موارد بالا مورد نظر نیست. واژه وارد شده اضافه شود: <a href='#' onclick='AddNewTerm(\"".$SimilarList[$i]["TermTitle"]."\"); document.getElementById(\"SimilarSpan\").style.display=\"none\";'>";
  echo "[اضافه]";
  echo "</a></b>";
  die();
}

if(isset($_REQUEST["AddNewTerm"]))
{
  $mysql->Prepare("select TermID from projectmanagement.terms where TermTitle=?");
  $res = $mysql->ExecuteStatement(array($_REQUEST["AddNewTerm"]));
  if($rec = $res->fetch())
    $TermID = $rec["TermID"];
  else
    $TermID = manage_terms::Add($_REQUEST["AddNewTerm"], "");
  echo $TermID;
  die();
}
if(isset($_REQUEST["LoadTerms"]))
{
  $list = manage_terms::GetList(0, 1000, "TermTitle", "ASC");
  echo "<select name=\"Item_TermID\" id=\"Item_TermID\"><option value=0>-";
  for($i=0; $i<count($list); $i++)
  {
    echo "<option value='".$list[$i]->TermID."'>".$list[$i]->TermTitle;
  }
  echo "</select>";
  die();
}

if(isset($_REQUEST["Ajax"])) 
{
    if($_REQUEST["TermID"]=="0")
      die();
    $TermID = $_REQUEST["TermID"];
    $mysql->Prepare("select OntologyEntityID, EntityType, OntologyClassLabels.label as ClassLabel, OntologyPropertyLabels.label as PropertyLabel,
		      ClassTitle, PropertyTitle, PermittedValue, OntologyPropertyPermittedValueID
		      from projectmanagement.TermOntologyElementMapping 
		      LEFT JOIN projectmanagement.OntologyClasses on (OntologyClasses.OntologyClassID=OntologyEntityID)
		      LEFT JOIN projectmanagement.OntologyClassLabels on (OntologyClasses.OntologyClassID=OntologyClassLabels.OntologyClassID)
		      LEFT JOIN projectmanagement.OntologyProperties on (OntologyProperties.OntologyPropertyID=OntologyEntityID)
		      LEFT JOIN projectmanagement.OntologyPropertyLabels on (OntologyProperties.OntologyPropertyID=OntologyPropertyLabels.OntologyPropertyID)
		      LEFT JOIN projectmanagement.OntologyPropertyPermittedValues using (OntologyPropertyPermittedValueID)
		      where TermID=?");
    $res = $mysql->ExecuteStatement(array($TermID));

    if($rec = $res->fetch())
    {
      echo "<b>این واژه در هستان نگار به شکل زیر مدل شده است:</b><br>";
      if($rec["EntityType"]=="CLASS") 
      {
	  $obj = new be_OntologyClasses();      
	  $obj->LoadDataFromDatabase($rec["OntologyEntityID"]); 
	  echo "<a href=# onclick=\"window.open('ManageOntologyClasses.php?UpdateID=".$rec["OntologyEntityID"]."&OntologyID=".$obj->OntologyID."');\" >ویرایش کلاس</a><br>";
	  $plist = manage_OntologyClasses::GetClassRelatedProperties($obj->ClassTitle, $obj->OntologyID);
	  if(count($plist)>0)
	    echo "خصوصیات: <br><table>";
	  ShowPropertyList($plist, $obj->OntologyID);
	  if(count($plist)>0)
	    echo "</table>";

	  $ParentClasses = manage_OntologyClassHirarchy::GetParentListArray($rec["OntologyEntityID"]);
	  for($j=0; $j<count($ParentClasses); $j++)
	  {
	    $plist = manage_OntologyClasses::GetClassRelatedProperties($ParentClasses[$j]["ClassTitle"], $obj->OntologyID);
	    echo "<font color=green>";
	    echo "خصوصیات به واسطه کلاس پدر (".$ParentClasses[$j]["label"]."): ";
	    echo "</font>";
	    echo "<br>";
	    if(count($plist)>0)
	    {
	      echo "<table>";
	    }
	    ShowPropertyList($plist, $obj->OntologyID);
	    if(count($plist)>0)
	      echo "</table>";
	    echo "<br>";
	 //   echo "<br>".$ParentClasses[$j]["OntologyClassID"]."<br>";
	    $ParentLevel2Classes = manage_OntologyClassHirarchy::GetParentListArray($ParentClasses[$j]["OntologyClassID"]);	    
	   //echo "<p dir=ltr>";
	  //  print_r($ParentLevel2Classes);
	  //  echo "</p>";
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
	      if(count($plist)>0)
		echo "</table>";
	      echo "<br>";
	    }
	    	    
	  }
      }
      if($rec["EntityType"]=="DATA_PROPERTY" || $rec["EntityType"]=="OBJECT_PROPERTY"  || $rec["EntityType"]=="DATA_RANGE") 
      {
	$obj = new be_OntologyProperties();
	$obj->LoadDataFromDatabase($rec["OntologyEntityID"]); 
	echo "<a target=_blank href='ManageOntologyProperties.php?UpdateID=".$rec["OntologyEntityID"]."&OntologyID=".$TargetOntologyID."'>خصوصیت ".$obj->label."</a><br>";
      }
      if($rec["EntityType"]=="DATA_PROPERTY" || $rec["EntityType"]=="DATA_RANGE") 
      {
	
	  echo "کلاسهای دارای این خصوصیت: ";
	  $DomainList = explode(", ",$obj->domain);
	  for($j=0; $j<count($DomainList); $j++)
	  {
	    $ClassIDLabel = manage_OntologyClasses::GetClassIDAndLabel($obj->OntologyID, $DomainList[$j]);
	    if($j>0)
	      echo " - ";
	    echo "<a target=_blank href='ManageOntologyClasses.php?UpdateID=".$ClassIDLabel["OntologyClassID"]."&OntologyID=".$obj->OntologyID."'>";
	    echo $ClassIDLabel["label"];
	    echo "</a>";
	  }
	  echo " ";

	  echo "<br>";
	  //echo "برد: ".$obj->range;
	  //echo "<br>";
      }
      else if($rec["EntityType"]=="OBJECT_PROPERTY")
      {
	  $DomainList = explode(", ",$obj->domain);
	  for($j=0; $j<count($DomainList); $j++)
	  {
	    $ClassIDLabel = manage_OntologyClasses::GetClassIDAndLabel($obj->OntologyID, $DomainList[$j]);
	    if($j>0)
	      echo " - ";
	    echo "<a target=_blank href='ManageOntologyClasses.php?UpdateID=".$ClassIDLabel["OntologyClassID"]."&OntologyID=".$obj->OntologyID."'>";
	    echo $ClassIDLabel["label"];
	    echo "</a>";
	  }
	  echo " <b>".$obj->label."</b> ";
	  $RangeList = explode(", ",$obj->range);
	  //echo $obj->range;
	  for($j=0; $j<count($RangeList); $j++)
	  {
	    //echo "*".$RangeList[$j]."* ";
	    $ClassIDLabel = manage_OntologyClasses::GetClassIDAndLabel($obj->OntologyID, $RangeList[$j]);
	    if($j>0)
	      echo " - ";
	    echo "<a target=_blank href='ManageOntologyClasses.php?UpdateID=".$ClassIDLabel["OntologyClassID"]."&OntologyID=".$obj->OntologyID."'>";
	    echo $ClassIDLabel["label"];
	    echo "</a>";
	  }
	  echo " ";
      }
      if($rec["EntityType"]=="DATA_PROPERTY"  || $rec["EntityType"]=="DATA_RANGE")
      {
	echo "مقادیر: ";
	$query = "select distinct OntologyPropertyPermittedValueID, PermittedValue from projectmanagement.OntologyPropertyPermittedValues where OntologyPropertyID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($rec["OntologyEntityID"]));
	$m=0;
	while($rec2 = $res->fetch())
	{
	  if($rec["EntityType"]=="DATA_RANGE" && $rec2["OntologyPropertyPermittedValueID"]==$rec["OntologyPropertyPermittedValueID"])
	    echo "<b><font color=green>";
	  
	  if($m>0)
	    echo " - ";
	  echo $rec2["PermittedValue"];
	  $m++;
	  if($rec["EntityType"]=="DATA_RANGE" && $rec2["OntologyPropertyPermittedValueID"]==$rec["OntologyPropertyPermittedValueID"])
	    echo "</b></font color=green>";
	}
	echo "<br>";
      }
    }
    die();
}

HTMLBegin();

if(isset($_REQUEST["RemoveID"]))
{
  manage_TermReferenceMapping::Remove($_REQUEST["RemoveID"]); 
  echo "<p align=center><font color=green>حذف شد</font></p>";
  //die();
}


if(isset($_REQUEST["Save"])) 
{
	$Item_CreatorUserID = $Item_CreateDate = "";
	$Item_TermReferenceID=$_REQUEST["TermReferenceID"];
	$Item_TermID=$_REQUEST["Item_TermID"];
	$Item_PageNum=$_REQUEST["Item_PageNum"];
	if(isset($_REQUEST["Item_CreatorUserID"]))
	  $Item_CreatorUserID=$_REQUEST["Item_CreatorUserID"];
	if(isset($_REQUEST["Item_CreateDate"]))
	  $Item_CreateDate=$_REQUEST["Item_CreateDate"];
	$Item_MappingComment=$_REQUEST["Item_MappingComment"];
	$Item_ParagraphNo=$_REQUEST["Item_ParagraphNo"];
	$Item_SentenceNo=$_REQUEST["Item_SentenceNo"];
		
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_TermReferenceMapping::Add($Item_TermReferenceID
				, $Item_TermID
				, $Item_PageNum
				, $Item_MappingComment
				, $Item_ParagraphNo
				, $Item_SentenceNo
				);
	}	
	else 
	{	
		manage_TermReferenceMapping::Update($_REQUEST["UpdateID"] 
				, $Item_TermID
				, $Item_PageNum
				, $Item_MappingComment
				, $Item_ParagraphNo
				, $Item_SentenceNo
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}

if(!isset($_REQUEST["PageNum"])) 
{ 
// چنانچه شماره صفحه خاصی برای استخراج واژگان ارسال نشده باشد آخرین صفحه ای که ورود اطلاعات شده را انتخاب می کند 
  $mysql->Prepare("select PageNum, ParagraphNo, SentenceNo from projectmanagement.TermReferenceMapping where TermReferenceID=? Order by TermReferenceMappingID DESC");
  $res = $mysql->ExecuteStatement(array($_REQUEST["TermReferenceID"]));
  $LastParagraph = $LastSentenceNo = $LastPage = 1;
  if($rec = $res->fetch())
  {
    $LastPage = $rec["PageNum"];
    $LastParagraph = $rec["ParagraphNo"];
    $LastSentenceNo = $rec["SentenceNo"];
  }
}
else
{
  $mysql->Prepare("select PageNum, ParagraphNo, SentenceNo from projectmanagement.TermReferenceMapping where TermReferenceID=? 
		    and PageNum=?
		    Order by TermReferenceMappingID DESC");
  $res = $mysql->ExecuteStatement(array($_REQUEST["TermReferenceID"], $_REQUEST["PageNum"]));
  $LastParagraph = $LastSentenceNo = 1;
  $LastPage = $_REQUEST["PageNum"];
  if($rec = $res->fetch())
  {
    $LastParagraph = $rec["ParagraphNo"];
    $LastSentenceNo = $rec["SentenceNo"];
  }
}
$LoadDataJavascriptCode = '';
$Item_MappingComment = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_TermReferenceMapping();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_TermID.value='".htmlentities($obj->TermID, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_PageNum.value='".htmlentities($obj->PageNum, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ParagraphNo.value='".htmlentities($obj->ParagraphNo, ENT_QUOTES, 'UTF-8')."'; \r\n ";
	$LoadDataJavascriptCode .= "document.f1.Item_SentenceNo.value='".htmlentities($obj->SentenceNo, ENT_QUOTES, 'UTF-8')."'; \r\n ";
	
	$Item_MappingComment=htmlentities($obj->MappingComment, ENT_QUOTES, 'UTF-8'); 
	$LastPage = $obj->PageNum;
}	
$PageContent = manage_TermReferenceContent::GetContent($_REQUEST["TermReferenceID"], $LastPage);
//echo $_REQUEST["TermReferenceID"].", ".$LastPage;
$PageContent = str_replace("\n", "<br>", $PageContent);

?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
echo manage_TermReferences::ShowSummary($_REQUEST["TermReferenceID"]);
echo manage_TermReferences::ShowTabs($_REQUEST["TermReferenceID"], "ManageTermReferenceMapping");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش ارتباط اصطلاحات و منابع</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<? 
if(!isset($_REQUEST["UpdateID"])) { ?> 
<input type="hidden" name="TermReferenceID" id="TermReferenceID" value='<? echo $_REQUEST["TermReferenceID"]; ?>'>
<? } ?>
<? if(isset($_REQUEST["PageNum"])) { ?>
  <input type="hidden" name="PageNum" id="PageNum" value="<? echo $_REQUEST["PageNum"] ?>">
<? } else if(isset($_REQUEST["SItem_TermID"])) { ?>
  <input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? if(isset($_REQUEST["OrderByFieldName"])) echo $_REQUEST["OrderByFieldName"]; ?>">
  <input type="hidden" name="OrderType" id="OrderType" value="<? if(isset($_REQUEST["OrderType"])) echo $_REQUEST["OrderType"]; ?>">
  <input type="hidden" name="SearchAction" id="SearchAction" value="1">
  <input type="hidden" name="SItem_TermID" id="SItem_TermID" value="<? if(isset($_REQUEST["SItem_TermID"])) echo $_REQUEST["SItem_TermID"]; ?>">
  <input type="hidden" name="SItem_PageNum" id="SItem_PageNum" value="<? if(isset($_REQUEST["SItem_PageNum"])) echo $_REQUEST["SItem_PageNum"]; ?>">
<? } ?>
<tr>
	<td nowrap>
	اصطلاح:
	<span id="TermListSpan" name="TermListSpan">
	<select name="Item_TermID" id="Item_TermID" onchange='javascript: ShowDetails(this.value);'>
	<option value=0>-
	<? echo SharedClass::CreateARelatedTableSelectOptions("projectmanagement.terms", "TermID", "TermTitle", "TermTitle"); ?>	
	</select>
	</span>
	<a href="#" onclick="javascript: window.open('Manageterms.php?Merge=1&TermID='+document.getElementById('Item_TermID').value);" >
	[پیشنهادات ادغام] 
	</a> 
	</td>
	<td>
	 ایجاد واژه جدید: 
	 <input type=text name=NewTerm id=NewTerm> <a href='javascript: CheckNewTerm();'>اضافه</a>
	 <br>
	 <span id=SimilarSpan name=SimilarSpan style='display: none'></span>
	</td>
</tr>

<tr><td colspan=2 bgcolor=#cccccc><span id=DetailsSpan name=DetailsSpan></span></td></tr>

<tr>
	<td colspan=2> شماره صفحه:
	<? if(isset($_REQUEST["PageNum"])) { echo $_REQUEST["PageNum"]; ?>
	<input type="hidden" name="Item_PageNum" id="Item_PageNum" maxlength="4" size="3" value='<? echo $_REQUEST["PageNum"] ?>'>
	&nbsp;
	<? } else { ?>
	<input type="text" name="Item_PageNum" id="Item_PageNum" maxlength="4" size="3" value='<? echo $LastPage ?>'>
	<? } ?>
	پاراگراف:
	<input type="text" name="Item_ParagraphNo" id="Item_ParagraphNo" maxlength="2" size="3" value='<? echo $LastParagraph ?>'>
جمله:
	<input type="text" name="Item_SentenceNo" id="Item_SentenceNo" maxlength="2" size="2" value='<? echo $LastSentenceNo ?>'>
	</td>
</tr>
<tr>
	<td colspan=2 nowrap>
 یادداشت: 
	<textarea name="Item_MappingComment" id="Item_MappingComment" cols="80" rows="2"><? echo $Item_MappingComment ?></textarea>
	</td>
</tr>
<tr class="FooterOfTable">
<td align="center" colspan=2>
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
&nbsp;
 <input type="button" onclick="javascript: document.location='ManageTermReferenceMapping.php?TermReferenceID=<?php echo $_REQUEST["TermReferenceID"]; if(isset($_REQUEST["PageNum"])) echo "&PageNum=".$_REQUEST["PageNum"]; ?>'" value="جدید">
&nbsp;
<? if(isset($_REQUEST["UpdateID"])) { ?>
  <input type="button" onclick="javascript: if(confirm('آیا برای حذف اطمینان دارید؟')) document.location='ManageTermReferenceMapping.php?RemoveID=<?php echo $_REQUEST["UpdateID"]; if(isset($_REQUEST["PageNum"])) echo "&PageNum=".$_REQUEST["PageNum"]; ?>'" value="حذف">
<? } ?>
</td>
</tr>
<tr>
  <td colspan=2>
  <?
    if(isset($_REQUEST["PageNum"])) 
    {
    ?>
    <table width=100% border=0 cellpadding=10>
      <tr>
      <td width=1% nowrap>
      <a href='ManageTermReferenceMapping.php?TermReferenceID=<? echo $_REQUEST["TermReferenceID"]; ?>&PageNum=<? if($_REQUEST["PageNum"]>0) echo $_REQUEST["PageNum"]-1; else echo "1"; ?>'>
      <b>
[صفحه قبل]
      </b>
      </a>
      </td>
      <td width=98%>&nbsp;</td>
      <td  width=1% nowrap>
      <a href='ManageTermReferenceMapping.php?TermReferenceID=<? echo $_REQUEST["TermReferenceID"]; ?>&PageNum=<? echo $_REQUEST["PageNum"]+1; ?>'>
      <b>
[صفحه بعد]
      </b>
      </a>
      </td>
     </tr>
     </table>
    <?
      // زمانیکه قرار است کل یک صفحه بررسی شود تمام واژگانی که از آن مستخرج شده رنگی شوند
      $res = manage_TermReferenceMapping::Search($_REQUEST["TermReferenceID"] , "", $_REQUEST["PageNum"], "", 0, 1000, "TermReferenceMappingID", "DESC"); 
      for($k=0; $k<count($res); $k++)
      {
	$PageContent = str_replace($res[$k]->TermID_Desc, "<font color=green><b>".$res[$k]->TermID_Desc."</b></font>", $PageContent);
      }
    }
    if(isset($_REQUEST["UpdateID"])) 
    {
      echo str_replace($obj->TermID_Desc, "<font color=green><b>".$obj->TermID_Desc."</b></font>", $PageContent);
    }
    echo $PageContent;         
  ?>
  </td>
</tr>
</table>
</td>
</tr>
</table>
<input type="hidden" name="Save" id="Save" value="1">
</form>
<script>
	<? echo $LoadDataJavascriptCode; ?>
	
	function FirstAddAndSleep()
	{
	  AddNewTerm();
	  setTimeout(ValidateForm, 2000); 
	}
	
	function ValidateForm()
	{
	  if(document.getElementById('Item_TermID').value==0)
	  {
	    FirstAddAndSleep();
	  }
	  else
	    document.f1.submit();
	}
</script>
<?php 
$NumberOfRec = 100;
 $k=0;
$PageNumber = 0;
if(isset($_REQUEST["PageNumber"]))
{
    if(!is_numeric($PageNumber))
	    $PageNumber = 0;
    else
	    $PageNumber = $_REQUEST["PageNumber"];
    $FromRec = $PageNumber*$NumberOfRec;
}
else
{
    $FromRec = 0; 
}
if(isset($_REQUEST["SearchAction"])) 
{
    $OrderByFieldName = "TermReferenceMappingID";
    $OrderType = "DESC";
    if(isset($_REQUEST["OrderByFieldName"]))
    {
	    $OrderByFieldName = $_REQUEST["OrderByFieldName"];
	    $OrderType = $_REQUEST["OrderType"];
    }
    $TermID=htmlentities($_REQUEST["SItem_TermID"], ENT_QUOTES, 'UTF-8');
    $PageNum=htmlentities($_REQUEST["SItem_PageNum"], ENT_QUOTES, 'UTF-8');
} 
else if(isset($_REQUEST["PageNum"]))
{
    $OrderByFieldName = "TermReferenceMappingID";
    $OrderType = "DESC";
    if(isset($_REQUEST["OrderByFieldName"]))
    {
	    $OrderByFieldName = $_REQUEST["OrderByFieldName"];
	    $OrderType = $_REQUEST["OrderType"];
    }
    $TermID='';
    $PageNum=$_REQUEST["PageNum"];
}
else
{ 
    $OrderByFieldName = "TermReferenceMappingID";
    $OrderType = "DESC";
    $TermID='';
    $PageNum='';
}
//echo $PageNum."<br>";
$res = manage_TermReferenceMapping::Search($_REQUEST["TermReferenceID"] , $TermID, $PageNum, "", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->TermReferenceMappingID])) 
	{
		manage_TermReferenceMapping::Remove($res[$k]->TermReferenceMappingID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_TermReferenceMapping::Search($_REQUEST["TermReferenceID"] , $TermID, $PageNum, "", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType); 
?>

<? if(!isset($_REQUEST["PageNum"])) { ?>
      <form id="SearchForm" name="SearchForm" method=post> 
      <input type="hidden" name="PageNumber" id="PageNumber" value="0">
      <input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
      <input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
      <input type="hidden" id="TermReferenceID" name="TermReferenceID" value="<? echo htmlentities($_REQUEST["TermReferenceID"], ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="SearchAction" id="SearchAction" value="1"> 
      <br><table width="90%" align="center" border="1" cellspacing="0">
      <tr class="HeaderOfTable">
      <td><img src='images/search.gif'><b><a href="#" onclick='javascript: if(document.getElementById("SearchTr").style.display=="none") document.getElementById("SearchTr").style.display=""; else document.getElementById("SearchTr").style.display="none";'>جستجو</a></td>
      </tr>
      <tr id='SearchTr' style='display: none'>
      <td>
      <table width="100%" align="center" border="0" cellspacing="0">
      <tr>
	      <td width="1%" nowrap>
      کد اصطلاح
	      </td>
	      <td nowrap>
	      <select name="SItem_TermID" id="SItem_TermID">
	      <option value=0>-
	      <? echo SharedClass::CreateARelatedTableSelectOptions("projectmanagement.terms", "TermID", "TermTitle", "TermTitle"); ?>	</select>
	      </td>
      </tr>

      <tr>
	      <td width="1%" nowrap>
      شماره صفحه
	      </td>
	      <td nowrap>
	      <input type="text" name="SItem_PageNum" id="SItem_PageNum" maxlength="4" size="3">
	      </td>
      </tr>

      <tr class="HeaderOfTable">
      <td colspan="2" align="center"><input type="submit" value="جستجو"></td>
      </tr>
      </table>
      </td>
      </tr>
      </table>
      </form>
      <? if(isset($_REQUEST["SearchAction"])) { ?>
      <script>
	    document.SearchForm.Item_TermID.value='<? echo htmlentities($_REQUEST["Item_TermID"], ENT_QUOTES, 'UTF-8'); ?>';
	    document.SearchForm.Item_PageNum.value='<? echo htmlentities($_REQUEST["Item_PageNum"], ENT_QUOTES, 'UTF-8'); ?>';
      </script>
      <? } ?> 

<? } ?>
<form id="ListForm" name="ListForm" method="post"> 
<input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
<input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">

<? if(!isset($_REQUEST["PageNum"])) { ?>
  <input type="hidden" name="SearchAction" id="SearchAction" value="1">
  <input type="hidden" name="SItem_TermID" id="SItem_TermID" value="<? if(isset($_REQUEST["SItem_TermID"])) echo $_REQUEST["SItem_TermID"]; ?>">
  <input type="hidden" name="SItem_PageNum" id="SItem_PageNum" value="<? if(isset($_REQUEST["SItem_PageNum"])) echo $_REQUEST["SItem_PageNum"]; ?>">
<? } else { ?>
  <input type="hidden" name="PageNum" id="PageNum" value="<? if(isset($_REQUEST["PageNum"])) echo $_REQUEST["PageNum"]; ?>">
<? } ?>

<input type="hidden" id="TermReferencesID" name="TermReferencesID" value="<? echo htmlentities($_REQUEST["TermReferenceID"], ENT_QUOTES, 'UTF-8'); ?>">
<? if(isset($_REQUEST["PageNumber"]))
	echo "<input type=\"hidden\" name=\"PageNumber\" value=".$_REQUEST["PageNumber"].">"; ?>
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="11">
	ارتباط اصطلاحات و منابع
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td><a href="javascript: Sort('TermID', 'ASC');">کد اصطلاح</a></td>
	<td width=1%><a href="javascript: Sort('PageNum', 'ASC');">صفحه</a></td>
	<td width=1%><a href="javascript: Sort('ParagraphNo', 'ASC');">پاراگراف</a></td>
	<td width=1%>جمله</td>
	<td><a href="javascript: Sort('CreatorUserID', 'ASC');">ایجاد کننده</a></td>
	<td><a href="javascript: Sort('CreateDate', 'ASC');">زمان ایجاد</a></td>
	<td><a href="javascript: Sort('MappingComment', 'ASC');">یادداشت</a></td>
	<td>عنصر هستان نگار</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->TermReferenceMappingID."\">";
	echo "</td>";
	echo "<td>".($k+$FromRec+1)."</td>";
	echo "<td>";
	echo "<a href=\"ManageTermReferenceMapping.php?UpdateID=".$res[$k]->TermReferenceMappingID."&TermReferenceID=".$_REQUEST["TermReferenceID"];
	if(isset($_REQUEST["PageNumber"]))
	  echo "&PageNumber=".$_REQUEST["PageNumber"];
	if(isset($_REQUEST["SearchAction"]))
	{
	  echo "&SearchAction=1&SItem_TermID=".$_REQUEST["SItem_TermID"]."&SItem_PageNum=".$_REQUEST["SItem_PageNum"];
	}
	if(isset($_REQUEST["OrderByFieldName"]))
	{
	  echo "&OrderByFieldName=".$_REQUEST["OrderByFieldName"]."&OrderType=".$_REQUEST["OrderType"];
	}
	if(isset($_REQUEST["PageNum"]))
	{
	  echo "&PageNum=".$_REQUEST["PageNum"];
	}
	echo "\">";
	echo "<img src='images/edit.gif' title='ویرایش'>";
	echo "</a></td>";
	echo "	<td>".$res[$k]->TermID_Desc."</td>";
	echo "	<td>".htmlentities($res[$k]->PageNum, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".htmlentities($res[$k]->ParagraphNo, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".htmlentities($res[$k]->SentenceNo, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".htmlentities($res[$k]->CreatorUserID, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".$res[$k]->CreateDate_Shamsi."</td>";
	echo "	<td>".str_replace("\r", "<br>", htmlentities($res[$k]->MappingComment, ENT_QUOTES, 'UTF-8'))."</td>";
	echo "	<td><a href='TermOntologyPage.php?TermID=".$res[$k]->TermID."' target=_blank>";
	$ElementName = GetRelatedOntologyElement($res[$k]->TermID);
	if($ElementName=="")
	  echo "[ثبت]";
	else
	  echo $ElementName;
	echo "</a></td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="11" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
</td>
</tr>
<tr bgcolor="#cccccc"><td colspan="11" align="right">
<?
$TotalCount = manage_TermReferenceMapping::SearchResultCount($_REQUEST["TermReferenceID"] , $TermID, $PageNum, ""); 
for($k=0; $k<$TotalCount/$NumberOfRec; $k++)
{
	if($PageNumber!=$k)
		echo "<a href='javascript: ShowPage(".($k).")'>";
	echo ($k+1);
	if($PageNumber!=$k)
		echo "</a>";
	echo " ";
}
?>
</td></tr>
</table>
</form>
<form target="_blank" method="post" action="NewTermReferenceMapping.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="TermReferenceID" name="TermReferenceID" value="<? echo htmlentities($_REQUEST["TermReferenceID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
function ShowPage(PageNumber)
{
  <? if(isset($_REQUEST["PageNum"])) { ?>
    document.location='ManageTermReferenceMapping.php?PageNumber='+PageNumber+'&TermReferenceID=<? echo $_REQUEST["TermReferenceID"]; ?>&PageNum=<? echo $_REQUEST["PageNum"]; ?>&OrderByFieldName=<? if(isset($_REQUEST["OrderByFieldName"])) echo $_REQUEST["OrderByFieldName"]; else echo "TermReferenceMappingID"; ?>&OrderType=<? if(isset($_REQUEST["OrderType"])) echo $_REQUEST["OrderType"]; else echo "DESC"; ?>';
  <? } else { ?>
	SearchForm.PageNumber.value=PageNumber; 
	SearchForm.submit();
  <? } ?>
}
function Sort(OrderByFieldName, OrderType)
{
  <? if(isset($_REQUEST["PageNum"])) { ?>
    document.location='ManageTermReferenceMapping.php?TermReferenceID=<? echo $_REQUEST["TermReferenceID"]; ?>&PageNum=<? echo $_REQUEST["PageNum"]; ?>&OrderByFieldName='+OrderByFieldName+'&OrderType='+OrderType;
  <? } else { ?>
	SearchForm.OrderByFieldName.value=OrderByFieldName; 
	SearchForm.OrderType.value=OrderType; 
	SearchForm.submit();
  <? } ?>
}

function LoadTermList(SelectedTermID)
{
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
      { 
	if (xmlhttp.readyState==4 && xmlhttp.status==200)
	{ 	 
	  document.getElementById("TermListSpan").innerHTML=xmlhttp.responseText;
	  //alert(xmlhttp.responseText);
	  document.getElementById("Item_TermID").value=SelectedTermID;
	}
      }
    xmlhttp.open("GET","ManageTermReferenceMapping.php?LoadTerms=1",true);
    xmlhttp.send();
}

function AddNewTerm()
{
    var NewTerm = document.getElementById("NewTerm").value;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
      { 
	if (xmlhttp.readyState==4 && xmlhttp.status==200)
	{ 	 
	  LoadTermList(xmlhttp.responseText);
	  //alter(xmlhttp.responseText);
	}
      }
    xmlhttp.open("GET","ManageTermReferenceMapping.php?AddNewTerm="+NewTerm,true);
    xmlhttp.send();
}

function CheckNewTerm()
{
    var NewTerm = document.getElementById("NewTerm").value;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
      { 
	if (xmlhttp.readyState==4 && xmlhttp.status==200)
	{ 
	  document.getElementById('SimilarSpan').innerHTML = xmlhttp.responseText;
	  document.getElementById('SimilarSpan').style.display = '';
	  //alter(xmlhttp.responseText);
	}
      }
    xmlhttp.open("GET","ManageTermReferenceMapping.php?CheckNewTerm="+NewTerm,true);
    xmlhttp.send();
}

function ShowDetails(TermID)
{
  document.getElementById('DetailsSpan').innerHTML = '<img src="images/ajax-loader.gif">';
  var params = "Ajax=1&TermID="+TermID;
  
  var http = new XMLHttpRequest();
  http.open("POST", "ManageTermReferenceMapping.php", true);
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
ShowDetails(document.getElementById('Item_TermID').value);
</script>
</html>
