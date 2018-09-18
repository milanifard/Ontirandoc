<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : خصوصیات هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-3-1
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/OntologyProperties.class.php");
include("classes/OntologyPropertyLabels.class.php");
include ("classes/ontologies.class.php");
HTMLBegin();

function NumberOfValidRelation($ObjectPropertyID)
{
	$mysql = pdodb::getInstance();
	$mysql->Prepare("select count(*) as tcount from projectmanagement.OntologyObjectPropertyRestriction where RelationStatus='VALID' and OntologyPropertyID=?");
	$res = $mysql->ExecuteStatement(array($ObjectPropertyID));
	$rec = $res->fetch();
	return $rec["tcount"];
}

function ShowClassListLabels($ClassListString, $OntologyID, $DomainOrRange, $NotifyNotUsed)
{
  $mysql = pdodb::getInstance();
  $ClassList = explode(", ",$ClassListString);
  for($i=0; $i<count($ClassList); $i++)
  {
    $mysql->Prepare("select * from projectmanagement.OntologyClasses JOIN projectmanagement.OntologyClassLabels using (OntologyClassID) where ClassTitle=? and OntologyID=?");
    $res = $mysql->ExecuteStatement(array($ClassList[$i], $OntologyID));
    if($rec = $res->fetch())
    {
    	$sw = IsInRestriction($DomainOrRange, $rec["OntologyClassID"], $_REQUEST["UpdateID"]);
    	if(!$sw && $NotifyNotUsed) echo "<font color=red>";
      echo $rec["label"]." (<a href='ManageOntologyClasses.php?UpdateID=".$rec["OntologyClassID"]."&OntologyID=".$OntologyID."&OnlyEditForm=1' target=_blank>".$ClassList[$i]."</a>)<br>";
      	if(!$sw && $NotifyNotUsed) echo "</font>";
    }
    else
    {
    	echo "<font color=red>نامشخص</font> (".$ClassList[$i].")<br>";
    }
  }
}

function IsInRestriction($DomainOrRange, $ClassID, $PropertyID)
{
  $mysql = pdodb::getInstance();
  if($DomainOrRange=="DOMAIN")
 	$query = "select count(*) as tcount from projectmanagement.OntologyObjectPropertyRestriction where OntologyPropertyID='".$PropertyID."' and DomainClassID='".$ClassID."' and RelationStatus='VALID'";
  else
  	$query = "select count(*) as tcount from projectmanagement.OntologyObjectPropertyRestriction where OntologyPropertyID='".$PropertyID."' and RangeClassID='".$ClassID."' and RelationStatus='VALID'";
  //echo $query."<br>";
  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array());
  $rec = $res->fetch();
  //echo $rec["tcount"]."<br>";
  if($rec["tcount"]>0)
  	return true;
  return false;
  
}

function GetAllPossibleLinks($PropertyID, $OntologyID)
{
  $ret = "";
  $mysql = pdodb::getInstance();
  $mysql->Prepare("select *,
  (select group_concat(label) from projectmanagement.OntologyPropertyLabels where OntologyPropertyLabels.OntologyPropertyID=OntologyProperties.OntologyPropertyID) as label 
  from projectmanagement.OntologyProperties where OntologyPropertyID=?");
  $res = $mysql->ExecuteStatement(array($PropertyID));
  if($rec = $res->fetch())
  {
    $domain = $rec["domain"];
    $range = $rec["range"];
    $DomainClassesList = $RangeClassesList = "0";
    $DomainArray = explode(", ", $domain);
    $RangeArray = explode(", ", $range);
    $PropertyLabel = $rec["label"];
    for($i=0; $i<count($DomainArray); $i++)
    {
      for($j=0; $j<count($RangeArray); $j++)
      {
	    $query = "select OntologyClassID,
	   (select group_concat(label) from projectmanagement.OntologyClassLabels where OntologyClassLabels.OntologyClassID=OntologyClasses.OntologyClassID) as label 
	   from projectmanagement.OntologyClasses where ClassTitle='".$DomainArray[$i]."' and OntologyID=?";
	   $mysql->Prepare($query);
	   $res = $mysql->ExecuteStatement(array($OntologyID));
	   $rec = $res->fetch();
	   $DomainClassID = $rec["OntologyClassID"];
	   if($DomainClassID!="")
	    $DomainClassesList .= ", ".$DomainClassID;
	   else
	    continue;
	   $DomainClassLabel = $rec["label"];
	   
	   $query = "select OntologyClassID,
	   (select group_concat(label) from projectmanagement.OntologyClassLabels where OntologyClassLabels.OntologyClassID=OntologyClasses.OntologyClassID) as label 
	   from projectmanagement.OntologyClasses where ClassTitle='".$RangeArray[$j]."'  and OntologyID=?";
	   $mysql->Prepare($query);
	   $res = $mysql->ExecuteStatement(array($OntologyID));
	   $rec = $res->fetch();
	   $RangeClassID = $rec["OntologyClassID"];
	   if($RangeClassID!="")
	    $RangeClassesList .= ", ".$RangeClassID;
	   else 
	    continue;
	   $RangeClassLabel = $rec["label"];
	   $mysql->Prepare("select * from projectmanagement.OntologyObjectPropertyRestriction where OntologyPropertyID=? and DomainClassID='".$DomainClassID."' and RangeClassID='".$RangeClassID."'");
	   $res = $mysql->ExecuteStatement(array($PropertyID));
	   if($rec = $res->fetch())
	   {
	      $RelationStatus = $rec["RelationStatus"];
	      $OntologyObjectPropertyRestrictionID = $rec["OntologyObjectPropertyRestrictionID"];

	      //$DomainClassCardinality = $rec["DomainClassCardinality"];
	      //$RangeClassCardinality = $rec["RangeClassCardinality"];
	      
	      if(isset($_REQUEST["Save"])) 
	      {
		$CheckBoxID = "r_".$OntologyObjectPropertyRestrictionID;
		$SBoxID = "c_".$OntologyObjectPropertyRestrictionID;
		//echo "*";
		if(isset($_REQUEST[$CheckBoxID]))
		{
			//echo "+";
		  //$DomainClassCardinality = $_REQUEST["d_".$SBoxID];
		  //$RangeClassCardinality = $_REQUEST["r_".$SBoxID];
		
		  $mysql->Prepare("update projectmanagement.OntologyObjectPropertyRestriction 
				    set RelationStatus='VALID'			    
				    where OntologyObjectPropertyRestrictionID=".$OntologyObjectPropertyRestrictionID);
		  $mysql->ExecuteStatement(array());
		  $RelationStatus = "VALID";
		}
		else 
		{
			//echo "-<br>";
		  $query = "update projectmanagement.OntologyObjectPropertyRestriction set RelationStatus='INVALID' where OntologyObjectPropertyRestrictionID=".$OntologyObjectPropertyRestrictionID;
		  //echo $query . "<br>";
		  $mysql->Execute($query);
		  $RelationStatus = "INVALID";
		}
	      }
	   }
	   else 
	   {
	      $mysql->Prepare("insert into projectmanagement.OntologyObjectPropertyRestriction (OntologyPropertyID, DomainClassID, RangeClassID, RelationStatus) values (?, '".$DomainClassID."', '".$RangeClassID."', 'INVALID')");
	      $mysql->ExecuteStatement(array($PropertyID));
	      $mysql->Prepare("select * from projectmanagement.OntologyObjectPropertyRestriction where OntologyPropertyID=? and DomainClassID='".$DomainClassID."' and RangeClassID='".$RangeClassID."'");
	      $res = $mysql->ExecuteStatement(array($PropertyID));
	      $rec = $res->fetch();
	      $RelationStatus = $rec["RelationStatus"];
	      //$DomainClassCardinality = $rec["DomainClassCardinality"];
	      //$RangeClassCardinality = $rec["RangeClassCardinality"];
	      $OntologyObjectPropertyRestrictionID = $rec["OntologyObjectPropertyRestrictionID"];
	   }
	   $CheckBoxID = "r_".$OntologyObjectPropertyRestrictionID;
	   $SBoxID = "c_".$OntologyObjectPropertyRestrictionID;
	   $ret .= "<input type=checkbox name='".$CheckBoxID."' id='".$CheckBoxID."' ";
	   if($RelationStatus=="VALID")
	    $ret .= " checked ";
	   $ret .= ">";
	/*
	   $ret .= "<select name=d_".$SBoxID." id=d_".$SBoxID.">";
	   $ret .= "<option value='1'>1";
	   $ret .= "<option value='N' ";
	   if($DomainClassCardinality=="N")
	    $ret .= " selected ";
	   $ret .= " >چند";
	   $ret .= "</select>";
	 */ 
	   $ret .= $DomainClassLabel;

	   $ret .= " <b>[".$PropertyLabel."]</b> ";
/*
	   $ret .= "<select name=r_".$SBoxID." id=r_".$SBoxID.">";
	   $ret .= "<option value='1'>1";
	   $ret .= "<option value='N' ";
	   if($RangeClassCardinality=="N")
	    $ret .= " selected ";
	   $ret .= " >چند";
	   $ret .= "</select>";
	   */
	   
	   $ret .= $RangeClassLabel."<br>";
      }
    }
    $query = "delete from projectmanagement.OntologyObjectPropertyRestriction where OntologyPropertyID=? and (DomainClassID not in (".$DomainClassesList.") or RangeClassID not in (".$RangeClassesList."))";
    $mysql->Prepare($query);
    $mysql->ExecuteStatement(array($PropertyID));
    
  }
  return $ret;
}

$mysql = pdodb::getInstance();

if(isset($_REQUEST["RemovePropertyID"])) 
{
	manage_OntologyProperties::Remove($_REQUEST["RemovePropertyID"]); 
	echo "<p align=center>حذف شد</p>";
	die();
}
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["OntologyID"]))
		$Item_OntologyID=$_REQUEST["OntologyID"];
	if(isset($_REQUEST["Item_PropertyTitle"]))
		$Item_PropertyTitle=$_REQUEST["Item_PropertyTitle"];
	if(isset($_REQUEST["Item_PropertyType"]))
		$Item_PropertyType=$_REQUEST["Item_PropertyType"];
	if(isset($_REQUEST["Item_IsFunctional"]))
		$Item_IsFunctional=$_REQUEST["Item_IsFunctional"];
	if(isset($_REQUEST["Item_domain"]))
		$Item_domain=$_REQUEST["Item_domain"];
	if(isset($_REQUEST["Item_range"]))
		$Item_range=$_REQUEST["Item_range"];
	if(isset($_REQUEST["Item_inverseOf"]))
		$Item_inverseOf=$_REQUEST["Item_inverseOf"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		$PropID = manage_OntologyProperties::Add($Item_OntologyID
				, $Item_PropertyTitle
				, $Item_PropertyType
				, $Item_IsFunctional
				, $Item_domain
				, $Item_range
				, $Item_inverseOf
				);
		$Item_label = $_REQUEST["Item_label"];
		manage_OntologyPropertyLabels::Add($PropID, $Item_label);
		
		GetAllPossibleLinks($PropID, $_REQUEST["OntologyID"]);
		
		if(isset($_REQUEST["FromTermOnto"]))
		{
		  echo "<script>\r\n";
		  if($Item_PropertyType=="DATATYPE")
		    echo "var obj = window.opener.document.getElementById('RelatedDataPropID');\r\n";
		  else 
		    echo "var obj = window.opener.document.getElementById('RelatedObjPropID');\r\n";		      
		  
		  echo "var option = document.createElement(\"option\");\r\n";
		  echo "option.text = '".$Item_label." (".$Item_PropertyTitle.")';\r\n";
		  echo "option.value = '".$PropID."';\r\n";
		  echo "obj.add(option);\r\n";
		  echo "obj.value='".$PropID."';\r\n";
		  echo "window.close();\r\n";
		  echo "</script>";
		}
		
	}	
	else 
	{	
		manage_OntologyProperties::Update($_REQUEST["UpdateID"] 
				, $Item_PropertyTitle
				, $Item_PropertyType
				, $Item_IsFunctional
				, $Item_domain
				, $Item_range
				, $Item_inverseOf
				);
		$Item_label = $_REQUEST["Item_label"];
		manage_OntologyPropertyLabels::UpdateOrInsertFirstLabel($_REQUEST["UpdateID"], $Item_label);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
$range = $domain = '';
$PropType = '';
if(isset($_REQUEST["UpdateID"])) 
{	
    $obj = new be_OntologyProperties();
    $obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
    $LoadDataJavascriptCode .= "document.f1.Item_PropertyTitle.value='".htmlentities($obj->PropertyTitle, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
    $LoadDataJavascriptCode .= "document.f1.Item_PropertyType.value='".htmlentities($obj->PropertyType, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
    $PropType = $obj->PropertyType;
    $LoadDataJavascriptCode .= "document.f1.Item_IsFunctional.value='".htmlentities($obj->IsFunctional, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
    $domain = htmlentities($obj->domain, ENT_QUOTES, 'UTF-8'); 
    $range = htmlentities($obj->range, ENT_QUOTES, 'UTF-8'); 
    $LoadDataJavascriptCode .= "document.f1.Item_inverseOf.value='".htmlentities($obj->inverseOf, ENT_QUOTES, 'UTF-8')."'; \r\n ";
    $LoadDataJavascriptCode .= "document.f1.Item_label.value='".manage_OntologyPropertyLabels::GetFirstLabel($_REQUEST["UpdateID"])."'; \r\n "; 
    
}	
else if(isset($_REQUEST["DataProp"]))
{
  $LoadDataJavascriptCode .= "document.f1.Item_PropertyType.value='DATATYPE'; \r\n "; 
}
?>
<form method="post" id="f1" name="f1" >
<input type="hidden" name="Save" id="Save" value="1">
<?
  if(isset($_REQUEST["DoNotShowList"]))
  {
    echo "<input type=\"hidden\" name=\"DoNotShowList\" id=\"DoNotShowList\" value='1'>";
  }
  
  if(isset($_REQUEST["UpdateID"])) 
  {
    echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
    echo manage_OntologyProperties::ShowSummary($_REQUEST["UpdateID"]);
    echo manage_OntologyProperties::ShowTabs($_REQUEST["UpdateID"], "NewOntologyProperties");
  }
  echo manage_ontologies::ShowSummary($_REQUEST["OntologyID"]);
  echo manage_ontologies::ShowTabs($_REQUEST["OntologyID"], "ManageOntologyProperties");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش خصوصیات هستان نگار</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<? 
	if(isset($_REQUEST["FromTermOnto"])) 
	{
	  echo "<input type=\"hidden\" name=\"FromTermOnto\" id=\"FromTermOnto\" value='1'>";
	}

if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="OntologyID" id="OntologyID" value='<? if(isset($_REQUEST["OntologyID"])) echo htmlentities($_REQUEST["OntologyID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
<tr>
	<td width="1%" nowrap>
 عنوان
	</td>
	<td nowrap>
	<input dir=ltr type="text" name="Item_PropertyTitle" id="Item_PropertyTitle" maxlength="245" size="40">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 برچسب
	</td>
	<td nowrap>
	<input type="text" dir=rtl name="Item_label" id="Item_label" maxlength="245" size="40">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 نوع
	</td>
	<td nowrap>
	<select dir="ltr" name="Item_PropertyType" id="Item_PropertyType" >
		<option value=0>-
		<option value='DATATYPE'>DATATYPE</option>
		<option value='OBJECT' selected>OBJECT</option>
		<option value='ANNOTATION'>ANNOTATION</option>
	</select>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
	<a href='#' onclick='javascript: document.getElementById("DomainTR").style.display="";'>حوزه</a>
	</td>
	<td nowrap>
	<? 
	  if($PropType=="OBJECT")
	  	ShowClassListLabels($domain, $_REQUEST["OntologyID"],"DOMAIN", true);
	  if($PropType=="DATATYPE")
	  	ShowClassListLabels($domain, $_REQUEST["OntologyID"],"DOMAIN", false);
	?>
	</td>
</tr>
<tr style='display: ' id=DomainTR name=DomainTR>
	<td width="1%" nowrap>
 
	</td>
	<td nowrap>
	<textarea name="Item_domain" id="Item_domain" rows="8" cols="100" dir=ltr><? echo $domain; ?></textarea>
	<a target=_blank href='ShowOntologyClassTree.php?InputName=Item_domain&OntologyID=<? echo $_REQUEST["OntologyID"] ?>'>انتخاب</a>
	</td>
</tr>

<tr>
	<td width="1%" nowrap>
	<a href='#' onclick='javascript: document.getElementById("RangeTR").style.display="";'>
 بازه
  </a>
	</td>
	<td nowrap>
	<? 
	  if($PropType=="OBJECT")
	  {
	  	ShowClassListLabels($range, $_REQUEST["OntologyID"],"RANGE", true);
	  }
	  else
	  	echo $range;
	?>
	</td>
</tr>

<tr style='display: ' id=RangeTR name=RangeTR>
	<td width="1%" nowrap>
 
	</td>
	<td nowrap>
	<textarea name="Item_range" id="Item_range" rows="8" cols="100" dir=ltr><? echo $range; ?></textarea>
	<a target=_blank href='ShowOntologyClassTree.php?InputName=Item_range&OntologyID=<? echo $_REQUEST["OntologyID"] ?>'>انتخاب</a>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 معکوس
	</td>
	<td nowrap>
	<input type="text" dir=ltr name="Item_inverseOf" id="Item_inverseOf" maxlength="245" size="40">
	<a target=_blank href='ShowOntologyClassTree.php?InputName=Item_inverseOf&OntologyID=<? echo $_REQUEST["OntologyID"] ?>'>انتخاب</a>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 Functional
	</td>
	<td nowrap>
	<select name="Item_IsFunctional" id="Item_IsFunctional" >
		<option value='NO'>خیر</option>
		<option value='YES'>بلی</option>
	</select>
	</td>
</tr>
<? if(isset($_REQUEST["UpdateID"])) { 
$RelationList = GetAllPossibleLinks($_REQUEST["UpdateID"], $_REQUEST["OntologyID"]); 
echo "<tr>";
echo "<td colspan=2>";
echo "<b>تعیین ارتباطات معتبر بین مفاهیم حوزه و برد این خصوصیت: <br></b>";
echo $RelationList;
echo "</td>";
echo "</tr>";
?>
<tr>
<td>مقادیر</td><td>
<a href='ManageOntologyPropertyPermittedValues.php?OntologyID=<? echo $_REQUEST["OntologyID"] ?>&OntologyPropertyID=<? echo $_REQUEST["UpdateID"] ?>'>ویرایش</a>
<br>
<?
$query = "select PermittedValue from projectmanagement.OntologyPropertyPermittedValues where OntologyPropertyID=?";
$mysql->Prepare($query);
$res = $mysql->ExecuteStatement(array($_REQUEST["UpdateID"]));
while($rec = $res->fetch())
	echo $rec["PermittedValue"]."<br>";

/*
$query = "select distinct DataValue from projectmanagement.TermOntologyElementMapping where EntityType='DATA_RANGE' and OntologyEntityID=?";
$mysql->Prepare($query);
$res = $mysql->ExecuteStatement(array($_REQUEST["UpdateID"]));
while($rec = $res->fetch())
	echo $rec["DataValue"]."<br>";
*/	
?>
</td>
</tr>

<tr>
  <td nowrap>حاصل ادغام: </td>
  <td>
  <table dir=ltr>
<?
  $mysql->Prepare("select distinct OntologyProperties.OntologyID, EntityID, OntologyTitle, PropertyTitle from projectmanagement.OntologyMergeEntities 
			    LEFT JOIN projectmanagement.OntologyProperties on (OntologyProperties.OntologyPropertyID=OntologyMergeEntities.EntityID)
			    LEFT JOIN projectmanagement.ontologies using (OntologyID)
				  where TargetEntityType='PROPERTY' and TargetEntityID=?");
  $res = $mysql->ExecuteStatement(array($_REQUEST["UpdateID"]));
  while($rec = $res->fetch())
  {
    echo "<tr><td>".$rec["OntologyTitle"]."</td><td>";
    echo "<td><a target=_blank href='ManageOntologyProperties.php?UpdateID=".$rec["EntityID"]."&OntologyID=".$rec["OntologyID"]."'>".$rec["PropertyTitle"]."</a>";
    echo "</td></tr>";
  }
?>
  </tr>
  </table>
  </td>
</tr>

<? } ?>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='ManageOntologyProperties.php?OntologyID=<?php echo $_REQUEST["OntologyID"]; ?>'" value="جدید">
 <input type="button" onclick="javascript: window.close();" value="بستن">
 <? if(isset($_REQUEST["UpdateID"])) { ?>
  <input type="button" onclick="javascript: if(confirm('برای حذف مطمئن هستید؟')) document.location='ManageOntologyProperties.php?RemovePropertyID=<?php echo $_REQUEST["UpdateID"]; ?>';" value="حذف">
  <? } ?>
</td>
</tr>
</table>

</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
<?php 
if(isset($_REQUEST["DoNotShowList"]))
  die();
$res = manage_OntologyProperties::GetList($_REQUEST["OntologyID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->OntologyPropertyID])) 
	{
		manage_OntologyProperties::Remove($res[$k]->OntologyPropertyID); 
		$SomeItemsRemoved = true;
	}
	if(isset($_REQUEST["label_".$res[$k]->OntologyPropertyID]) && $_REQUEST["label_".$res[$k]->OntologyPropertyID]!="") 
	{
		manage_OntologyPropertyLabels::Add($res[$k]->OntologyPropertyID, $_REQUEST["label_".$res[$k]->OntologyPropertyID]);
		$SomeItemsRemoved = true;
	}
	if(isset($_REQUEST["Domain_".$res[$k]->OntologyPropertyID])) 
	{
	    $query = "update projectmanagement.OntologyProperties set domain='".$_REQUEST["Domain_".$res[$k]->OntologyPropertyID]."' where OntologyPropertyID=".$res[$k]->OntologyPropertyID;
	    $mysql->Execute($query);
	    $query = "update projectmanagement.OntologyProperties set `range`='".$_REQUEST["Range_".$res[$k]->OntologyPropertyID]."' where OntologyPropertyID=".$res[$k]->OntologyPropertyID;
	    $mysql->Execute($query);
	    $SomeItemsRemoved = true;
	}
	
}
if($SomeItemsRemoved)
	$res = manage_OntologyProperties::GetList($_REQUEST["OntologyID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_OntologyID" name="Item_OntologyID" value="<? echo htmlentities($_REQUEST["OntologyID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="10">
	خصوصیات هستان نگار
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>عنوان</td>
	<td>حوزه و برد</td>
	<td nowrap>برچسبها </td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
    $LabelsList = "";
    $list = manage_OntologyPropertyLabels::GetList($res[$k]->OntologyPropertyID);
    for($m=0; $m<count($list); $m++)
    {
      if($m>0)
	$LabelsList .= ", ";
      $LabelsList .= $list[$m]->label;
    }
	if($res[$k]->domain=="" && $res[$k]->range=="")
		echo "<tr bgcolor=\"#ff4d4d\">";
	else if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->OntologyPropertyID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a target=_blank href=\"ManageOntologyProperties.php?UpdateID=".$res[$k]->OntologyPropertyID."&OntologyID=".$_REQUEST["OntologyID"]."&DoNotShowList=1\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td dir=ltr>";
	if($res[$k]->IsFunctional_Desc=="بلی")
	  echo "<img src='images/cal.jpeg' title='خصوصیت functional است'>";
	if($res[$k]->PropertyType_Desc=="DATATYPE") echo "<img src='images/chain.gif' title='خصوصیت شیء'>";
	else echo "<img src='images/task.jpg' title='خصوصیت داده'>";

	echo htmlentities($res[$k]->PropertyTitle, ENT_QUOTES, 'UTF-8');
	echo "<br>";
	echo $LabelsList;
	echo "</td>";
	//echo "	<td>".$res[$k]->PropertyType_Desc."</td>";
	//echo "	<td>".$res[$k]->IsFunctional_Desc."</td>";
	echo "	<td dir=ltr>
	حوزه: <br>
	<textarea id='Domain_".$res[$k]->OntologyPropertyID."' name='Domain_".$res[$k]->OntologyPropertyID."'>".htmlentities($res[$k]->domain, ENT_QUOTES, 'UTF-8')."</textarea>";
	$l = explode(", ", $res[$k]->domain);
	for($i=0; $i<count($l); $i++)
	{
	  $ro = $mysql->Execute("select * from projectmanagement.OntologyClasses where ClassTitle='".$l[$i]."' and OntologyID=".$_REQUEST["OntologyID"]);
	  if(!($ro->fetch()))
	    echo "<br><font color=red>".$l[$i]."</font>";
	}
	echo "	<br>برد:<br>
	<textarea id='Range_".$res[$k]->OntologyPropertyID."' name='Range_".$res[$k]->OntologyPropertyID."'>".htmlentities($res[$k]->range, ENT_QUOTES, 'UTF-8')."</textarea>";
	$l = explode(", ", $res[$k]->range);
	for($i=0; $i<count($l); $i++)
	{
	  $ro = $mysql->Execute("select * from projectmanagement.OntologyClasses where ClassTitle='".$l[$i]."' and OntologyID=".$_REQUEST["OntologyID"]);
	  if(!($ro->fetch()))
	    echo "<br><font color=red>".$l[$i]."</font>";
	}
	if($res[$k]->PropertyType_Desc=="OBJECT" && NumberOfValidRelation($res[$k]->OntologyPropertyID)==0)
		echo "<br><b><font color=red>روابط مجاز حوزه و برد تعریف نشده</font></b>";
	echo "</td>";
	//echo "	<td dir=ltr>".htmlentities($res[$k]->inverseOf, ENT_QUOTES, 'UTF-8')."</td>";
	echo "<td nowrap>";
	$SuggestedLabel = "";
	if($LabelsList=="")
	{
	  $SuggestedLabel = manage_OntologyProperties::GetSuggestedLabel($res[$k]->PropertyTitle);
	}
	
	echo "<input type=text name='label_".$res[$k]->OntologyPropertyID."' id='label_".$res[$k]->OntologyPropertyID."' value='".$SuggestedLabel."'>";
	echo "<br><a  target=\"_blank\" href='ManageOntologyPropertyLabels.php?OntologyPropertyID=".$res[$k]->OntologyPropertyID ."'>[ویرایش]</a></td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="10" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
&nbsp;
	<input type=submit value='ذخیره'>
&nbsp;
	<input type="button" onclick="javascript: window.open('ShowSimilarClassRelations.php?OntologyID=<? echo $_REQUEST["OntologyID"]; ?>');" value="روابط مشابه">
&nbsp;
	<input type="button" onclick="javascript: window.open('ShowSimilarClassProperties.php?OntologyID=<? echo $_REQUEST["OntologyID"]; ?>');" value="خصوصیات تکراری">
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewOntologyProperties.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="OntologyID" name="OntologyID" value="<? echo htmlentities($_REQUEST["OntologyID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
</script>
</html>
