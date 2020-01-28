<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : پروژه ادغام هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-10-15
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/OntologyMergeProject.class.php");
require_once("classes/OntologyProperties.class.php");
require_once("classes/OntologyClasses.class.php");
require_once("classes/MergeOntology.class.php");

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

function CalculateCoveragePercentage($OntologyID1, $OntologyID2)
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

	$query = "select count(*) as tcount from projectmanagement.OntologyProperties where OntologyID=? and
		 OntologyPropertyID in (
		 select OntologyPropertyID from projectmanagement.OntologyPropertyMapping where
		OntologyPropertyMapping.OntologyID=? and MappedOntologyID=? and MappedOntologyEntityID<>'0')";
	$mysql->Prepare($query, true);
	$res = $mysql->ExecuteStatement(array($OntologyID1, $OntologyID1, $OntologyID2), PDO::FETCH_ASSOC, true);
	$rec = $res->fetch();
	$MappedPropCount = $rec["tcount"];
	$query = "select count(*) as tcount from projectmanagement.OntologyProperties where OntologyID=? ";
	$mysql->Prepare($query, true);
	$res = $mysql->ExecuteStatement(array($OntologyID1), PDO::FETCH_ASSOC, true);
	$rec = $res->fetch();
	$TotalPropCount = $rec["tcount"];
	return (($MappedClassCount+$MappedPropCount)*100)/($TotalClassCount+$TotalPropCount);
}


HTMLBegin();

/*
حذف تمام یک ادغام
delete from projectmanagement.OntologyMergeHirarchy where OntologyMergeProjectID=1
delete from projectmanagement.OntologyMergeHirarchy where OntologyMergeProjectID=1
delete from projectmanagement.OntologyClasses where OntologyID=53
delete from projectmanagement.OntologyClassHirarchy where OntologyClassID in (select OntologyClassID from projectmanagement.OntologyClasses where OntologyID=53)
delete from projectmanagement.OntologyClassLabels where OntologyClassID in (select OntologyClassID from projectmanagement.OntologyClasses where OntologyID=53)
delete from projectmanagement.OntologyPropertyLabels where OntologyPropertyID in (select OntologyPropertyID from projectmanagement.OntologyProperties where OntologyID=53)
delete from projectmanagement.OntologyClasses where OntologyID=53
*/


$mysql = pdodb::getInstance();

if(isset($_REQUEST["Reset"]))
{
	$OntologyMergeProjectID = $_REQUEST["OntologyMergeProjectID"];
	$obj = new be_OntologyMergeProject();
	$obj->LoadDataFromDatabase($OntologyMergeProjectID);
	MergeOntology::ClearOntology($obj->TargetOntologyID);
	MergeOntology::ResetOntologyMergeEntitiesStatus($OntologyMergeProjectID);
	echo "<p align=center><font color=green>عناصر هستان نگار مقصد حذف و وضعیت عناصر مشارکت کننده در ادغام ریست شدند.</font></p>";
}

if(isset($_REQUEST["Mapping"]))
{
	$OntologyList = array();
	$query = "select * from projectmanagement.OntologyMergeProjectMembers
			JOIN projectmanagement.ontologies using (OntologyID)
			where OntologyMergeProjectID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array($_REQUEST["OntologyMergeProjectID"]));
	$i = 0;
	while($rec = $res->fetch())
	{
		$OntologyList[$i]["OntologyID"] = $rec["OntologyID"];
		$OntologyList[$i]["OntologyTitle"] = $rec["OntologyTitle"];
		$i++;
	}
	echo "<table dir=ltr width=90% align=center border=1 cellpadding=5 cellspacing=0>";
	echo "<tr bgcolor=#cccccc>";
	echo "<td>&nbsp;</td>";
	for($i=0; $i<count($OntologyList); $i++)
	{
		echo "<td>".$OntologyList[$i]["OntologyTitle"]."</td>";
	}
	echo "</tr>";
	for($i=0; $i<count($OntologyList); $i++)
	{
		echo "<tr>";
		echo "<td bgcolor=#cccccc>".$OntologyList[$i]["OntologyTitle"]."</td>";
		for($j=0; $j<count($OntologyList); $j++)
		{
			
			if($i==$j)
			{
				echo "<td bgcolor=#aaaaaa>&nbsp;";
			}
			else
			{
				echo "<td dir=ltr>";
				$OntologyID1 = $OntologyList[$i]["OntologyID"];
				$OntologyID2 = $OntologyList[$j]["OntologyID"];
				echo "All: ";
				echo round(CalculateCoveragePercentage($OntologyID1, $OntologyID2), 2);
				
				echo "(CC: ";
				echo round(CalculateClassCoveragePercentage($OntologyID1, $OntologyID2), 2);
				echo " - ";
				echo "PC: ";
				echo round(CalculatePropertyCoveragePercentage($OntologyID1, $OntologyID2), 2);
				echo ")<br>";
				echo "<a href='CompareOntologies.php?OntologyID1=".$OntologyID1."&OntologyID2=".$OntologyID2."&ActionType=MAPPING' target=_blank>Edit</a>";
				echo " / ";
				echo "<a href='CompareOntologies.php?OntologyID1=".$OntologyID1."&OntologyID2=".$OntologyID2."&ActionType=RESULT' target=_blank>View</a>";
				
			}
			echo "</td>";
		}
		echo "</tr>";
	}
	echo "<tr><td colspan=".(count($OntologyList)+1).">";
	echo "CC (Class Coverage): تعداد کلاسهایی در هستان نگار ردیف که در هستان نگار ستون متناظری دارند به تعداد کل کلاسهای هستان نگار ردیف ";
	echo "<br>";
	echo "PC (Property Coverage): تعداد خصوصیات/روابط در هستان نگار ردیف که در هستان نگار ستون متناظری دارند به تعداد کل خصوصیات/روابط هستان نگار ردیف ";
	echo "<br>";
	echo "به بیان دیگر مقدار پوششی که هستان نگار ستون روی هستان نگار ردیف دارد چه مقدار است.";
	echo "</td></tr>";
	
	echo "</table>";
	echo "<br><table align=center><tr><td>";
	echo "<a href='CompareOntologies.php?ActionType=APPLY' target=_blank>اعمال روابط متعدی و تقارنی برای نگاشتها</a>";
	echo "</td></tr></table>";
	die();
}

if(isset($_REQUEST["Execute"]))
{
	$OntologyMergeProjectID = $_REQUEST["OntologyMergeProjectID"];
	$OntologyMergeEntityID = $_REQUEST["OntologyMergeEntityID"];
	$obj = new be_OntologyMergeProject();
	$obj->LoadDataFromDatabase($OntologyMergeProjectID);
	$TargetOntologyID = $obj->TargetOntologyID;
	if(isset($_REQUEST["MyDecision"]))
	{
		$EntityID = $_REQUEST["EntityID"];
		if($_REQUEST["MyDecision"]=="ADD")
		{
			if($_REQUEST["EntityType"]=="CLASS")
			{
				$NewClassID = MergeOntology::CopyClassTo($EntityID, $TargetOntologyID);
				if($NewClassID>0)
				{
				  MergeOntology::ChangeMergeRecordStatus($OntologyMergeEntityID, "ADD");	
				  MergeOntology::UpdateMergeRecordTarget($OntologyMergeEntityID, $NewClassID, "CLASS");
				  MergeOntology::UpdateAllMappedEntityStatusForClass($OntologyMergeProjectID, $EntityID, $NewClassID, "CLASS");
				}
				else
				{
					echo "<p align=center><font color=red>کلاسی با همین نام وجود دارد</font></p>";				
				}
			
			}
			else if($_REQUEST["EntityType"]=="PROP")
			{
				
				$NewPropertyID = MergeOntology::CopyPropertyTo($EntityID, $TargetOntologyID, $OntologyMergeProjectID);
				if($NewPropertyID>0)
				{
				MergeOntology::ChangeMergeRecordStatus($OntologyMergeEntityID, "ADD");	
				MergeOntology::UpdateMergeRecordTarget($OntologyMergeEntityID, $NewPropertyID, "PROPERTY");
				MergeOntology::UpdateAllMappedEntityStatusForProperty($OntologyMergeProjectID, $EntityID, $NewPropertyID, "PROPERTY", $TargetOntologyID);
				}
				else
				{
					echo "<p align=center><font color=red>خصوصیتی با همین نام وجود دارد</font></p>";
				}
				
			}
		}
		else if($_REQUEST["MyDecision"]=="IGNORE")
		{
			MergeOntology::ChangeMergeRecordStatus($OntologyMergeEntityID, "IGNORE", $_REQUEST["IgnoreReason"], $_REQUEST["IgnoreDescription"]);	
		}
	}

	// تا زمانیکه کلاسهایی پیدا کند که از طریق رابطه تعدی با هم معادلند آنها را ذخیره می کند
	//while(MergeOntology::FindAllIndirectEqualClasses());
	// متقارن ها را هم اضافه می کند
	//MergeOntology::ApplySymmetryRuleOnMappings();
	// دو بخش بالا در قسمت نگاشت هستان نگارها به صورت یک دکمه اضافه شدند
	
	MergeOntology::AddSourceClassAndProperties($OntologyMergeProjectID);
	// تا زمانیکه کلاسها منتقل نشده اند به سراغ خصوصیات نمی رود
	if(MergeOntology::FindClassAndDecide($OntologyMergeProjectID)==false)
	{
	    // پس از مشخص شدن تکلیف تمام کلاسها، باید ساختار سلسله مراتبی کلاسها هم منتقل شود
	      MergeOntology::UpdateClassHirarchies($OntologyMergeProjectID);
	      MergeOntology::FindPropertyAndDecide($OntologyMergeProjectID);
	      echo "*";
	}
	die();
}

if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["Item_TargetOntologyID"]))
		$Item_TargetOntologyID=$_REQUEST["Item_TargetOntologyID"];
	if(isset($_REQUEST["Item_MergeStatus"]))
		$Item_MergeStatus=$_REQUEST["Item_MergeStatus"];
	if(isset($_REQUEST["Item_MergeProjectTitle"]))
		$Item_MergeProjectTitle=$_REQUEST["Item_MergeProjectTitle"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_OntologyMergeProject::Add($Item_TargetOntologyID
				, $Item_MergeProjectTitle
				);
	}	
	else 
	{	
		manage_OntologyMergeProject::Update($_REQUEST["UpdateID"] 
				, $Item_TargetOntologyID
				, $Item_MergeProjectTitle
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_OntologyMergeProject();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_TargetOntologyID.value='".htmlentities($obj->TargetOntologyID, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_MergeProjectTitle.value='".htmlentities($obj->MergeProjectTitle, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
		echo manage_OntologyMergeProject::ShowSummary($_REQUEST["UpdateID"]);
		echo manage_OntologyMergeProject::ShowTabs($_REQUEST["UpdateID"], "NewOntologyMergeProject");
	}
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش پروژه ادغام هستان نگار</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr>
	<td width="1%" nowrap>
 هستان نگار مقصد
	</td>
	<td nowrap>
	<select name="Item_TargetOntologyID" id="Item_TargetOntologyID">
	<option value=0>-
	<? echo SharedClass::CreateARelatedTableSelectOptions("projectmanagement.ontologies", "OntologyID", "OntologyTitle", "OntologyTitle"); ?>	</select>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 وضعیت ادغام
	</td>
	<td nowrap>
	<span name="Item_MergeStatus" id="Item_MergeStatus"></span>	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 عنوان پروژه
	</td>
	<td nowrap>
	<input type="text" name="Item_MergeProjectTitle" id="Item_MergeProjectTitle" maxlength="45" size="40">
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='ManageOntologyMergeProject.php';" value="جدید">
</td>
</tr>
</table>
<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
<?php 
$res = manage_OntologyMergeProject::GetList(); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->OntologyMergeProjectID])) 
	{
		manage_OntologyMergeProject::Remove($res[$k]->OntologyMergeProjectID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_OntologyMergeProject::GetList(); 
?>
<form id="ListForm" name="ListForm" method="post"> 
<br><table width="98%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="10">
	پروژه ادغام هستان نگار
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>هستان نگار مقصد</td>
	<td>وضعیت ادغام</td>
	<td>عنوان پروژه</td>
	<td width=1% nowrap>هستان نگارها</td>
	<td width=1% nowrap>نگاشت پیش از ادغام</td>
	<td width=1% nowrap>اجرای فرآیند</td>
	<td width=1% nowrap>reset</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->OntologyMergeProjectID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManageOntologyMergeProject.php?UpdateID=".$res[$k]->OntologyMergeProjectID."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
		echo "	<td>".$res[$k]->TargetOntologyID_Desc."</td>";
	echo "	<td>".htmlentities($res[$k]->MergeStatus, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".htmlentities($res[$k]->MergeProjectTitle, ENT_QUOTES, 'UTF-8')."</td>";
	echo "<td width=1% nowrap><a  target=\"_blank\" href='ManageOntologyMergeProjectMembers.php?OntologyMergeProjectID=".$res[$k]->OntologyMergeProjectID ."'>هستان نگارها</a></td>";
	echo "<td width=1% nowrap>";	
	echo "<a  target=\"_blank\" href='ManageOntologyMergeProject.php?Mapping=1&OntologyMergeProjectID=".$res[$k]->OntologyMergeProjectID ."'>نگاشت پیش از ادغام</a></td>";
	echo "<td width=1% nowrap>";	
	echo "<a  target=\"_blank\" href='ManageOntologyMergeProject.php?Execute=1&OntologyMergeProjectID=".$res[$k]->OntologyMergeProjectID ."'>اجرای فرآیند ادغام</a></td>";
	echo "<td><a  href='ManageOntologyMergeProject.php?Reset=1&OntologyMergeProjectID=".$res[$k]->OntologyMergeProjectID ."'>Reset</a></td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="10" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewOntologyMergeProject.php" id="NewRecordForm" name="NewRecordForm">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
</script>
</html>
