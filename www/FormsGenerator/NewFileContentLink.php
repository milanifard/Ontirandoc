<?php
include("header.inc.php");
include_once("classes/FormUtils.class.php");
include_once("classes/FileTypes.class.php");
include_once("classes/SecurityManager.class.php");
include_once("classes/files.class.php");
HTMLBegin();
$mysql = dbclass::getInstance();
if(isset($_REQUEST["FileContentID"]))
{
	// در صورتیکه محتوایی برای ثبت اتصال انتخاب شده بود مجددا کنترل می کند که دسترسی به آن محتوا مجاز باشد
	$list = SecurityManager::GetUserPermittedFileTypesForAccess($_SESSION["PersonID"]);
	$FileTypeOptions = "";
	for($i=0; $i<count($list); $i++)
	{
		if($i>0)
			$FileTypeOptions .= " or ";
		$FileTypeOptions .= "(f.FileTypeID=".$list[$i]["FileTypeID"];
		// اگر دسترسی کاربر به این نوع دارای محدودیتی بود آن محدودیت هم به شرط اضافه می شود
		if($list[$i]["AccessRange"]=="UNIT")
			$FileTypeOptions .= " and f.ouid in (".$list[$i]["PermittedRangeList"].") ";
		else if($list[$i]["AccessRange"]=="SUB_UNIT")
			$FileTypeOptions .= " and f.sub_ouid in (".$list[$i]["PermittedRangeList"].")  ";
		else if($list[$i]["AccessRange"]=="EDU_GROUP")
			$FileTypeOptions .= " and f.EduGrpCode in (".$list[$i]["PermittedRangeList"].")  ";
		else if($list[$i]["AccessRange"]=="ONLY_USER")
			$FileTypeOptions .= " and f.CreatorID='".$_SESSION["PersonID"]."' ";
		$FileTypeOptions .= ")";
	}
	if($FileTypeOptions=="")
	{
		echo "هیچ نوع پرونده ای در دسترس شما نمی باشد";
		die();
	}
	$ListCondition = " (".$FileTypeOptions.") ";
	
	$query = "select f.*, ft.*, p.plname as pplname, p.pfname as ppfname, s.pfname as spfname, s.plname as splname,
									u.ptitle as UnitName, su.ptitle as SubUnitName, eg.PEduName, 
									fc.FileID, fc.FileName, fc.description, fc.ContentNumber, fc.ContentDate, fc.FormsStructID, fc.FormRecordID, fc.FileContentID  
									from files as f
									INNER JOIN FileContents as fc using (FileID)
									LEFT JOIN FileTypes as ft using (FileTypeID) 
									LEFT JOIN hrms_total.persons as p using (PersonID)
									LEFT JOIN StudentSpecs as s using (StNo)
									LEFT JOIN hrms_total.org_units as u using (ouid)
									LEFT JOIN hrms_total.org_sub_units su using (sub_ouid)
									LEFT JOIN EducationalGroups as eg on (f.EduGrpCode=eg.EduGrpCode) where  
									FileStatus='ENABLE' 
									and ContentType='".$_REQUEST["ContentType"]."' 
									and ContentStatus='ENABLE'
									and RelatedContentID=0 and FileContentID='".$_REQUEST["FileContentID"]."' and ";
	$query .= $ListCondition;
	//echo $query;
	$res = $mysql->Execute($query);
	if($rec = $res->FetchRow())
	{
		$query = "insert into FileContents (FileID, ContentType, RelatedContentID) values ('".$_REQUEST["FileID"]."', '".$_REQUEST["ContentType"]."', '".$_REQUEST["FileContentID"]."')";
		$mysql->Execute($query);
		$query = "select max(FileContentID) from FileContents where RelatedContentID='".$_REQUEST["FileContentID"]."'";
		$res = $mysql->Execute($query);
		if($rec = $res->FetchRow())
		{
			$MaxID = $rec[0];
			$mysql->Execute("insert into FileContentHistory (FileContentID, ActionType, ActionTime, PersonID) values ('".$MaxID."', 'ADD', now(), '".$_SESSION["PersonID"]."') ");
		}
?>
	<script>
		window.opener.document.location='ManageFileContent.php?UpdateID=<?php echo $_REQUEST["FileID"] ?>&ContentType=<?php echo $_REQUEST["ContentType"] ?>';
		window.close();	
	</script>
<?php 
		
	}
	die();
}
if(isset($_REQUEST["Search"]))
{
	$list = SecurityManager::GetUserPermittedFileTypesForAccess($_SESSION["PersonID"]);
	$FileTypeOptions = "";
	for($i=0; $i<count($list); $i++)
	{
		if($i>0)
			$FileTypeOptions .= " or ";
		$FileTypeOptions .= "(f.FileTypeID=".$list[$i]["FileTypeID"];
		// اگر دسترسی کاربر به این نوع دارای محدودیتی بود آن محدودیت هم به شرط اضافه می شود
		if($list[$i]["AccessRange"]=="UNIT")
			$FileTypeOptions .= " and f.ouid in (".$list[$i]["PermittedRangeList"].") ";
		else if($list[$i]["AccessRange"]=="SUB_UNIT")
			$FileTypeOptions .= " and f.sub_ouid in (".$list[$i]["PermittedRangeList"].")  ";
		else if($list[$i]["AccessRange"]=="EDU_GROUP")
			$FileTypeOptions .= " and f.EduGrpCode in (".$list[$i]["PermittedRangeList"].")  ";
		else if($list[$i]["AccessRange"]=="ONLY_USER")
			$FileTypeOptions .= " and f.CreatorID='".$_SESSION["PersonID"]."' ";
		$FileTypeOptions .= ")";
	}
	if($FileTypeOptions=="")
	{
		echo "هیچ نوع پرونده ای در دسترس شما نمی باشد";
		die();
	}
	$ListCondition = " (".$FileTypeOptions.") ";
	
	
	$query = "select f.*, ft.*, p.plname as pplname, p.pfname as ppfname, s.pfname as spfname, s.plname as splname,
									u.ptitle as UnitName, su.ptitle as SubUnitName, eg.PEduName, 
									fc.FileID, fc.FileName, fc.description, fc.ContentNumber, fc.ContentDate, fc.FormsStructID, fc.FormRecordID, fc.FileContentID  
									from files as f
									INNER JOIN FileContents as fc using (FileID)
									LEFT JOIN FileTypes as ft using (FileTypeID) 
									LEFT JOIN hrms_total.persons as p using (PersonID)
									LEFT JOIN StudentSpecs as s using (StNo)
									LEFT JOIN hrms_total.org_units as u using (ouid)
									LEFT JOIN hrms_total.org_sub_units su using (sub_ouid)
									LEFT JOIN EducationalGroups as eg on (f.EduGrpCode=eg.EduGrpCode) where  
									FileStatus='ENABLE' 
									and ContentType='".$_REQUEST["ContentType"]."' 
									and ContentStatus='ENABLE'
									and RelatedContentID=0 and ";
	if(isset($_REQUEST["description"]) && $_REQUEST["description"]!="")
		$query .= " fc.description like '%".$_REQUEST["description"]."%' and ";
	if(isset($_REQUEST["ContentNumber"]) && $_REQUEST["ContentNumber"]!="")
		$query .= " fc.ContentNumber='".$_REQUEST["ContentNumber"]."' and ";
	if(isset($_REQUEST["ContentDate"]) && $_REQUEST["ContentDate"]!="")
		$query .= " fc.ContentDate='".xdate($_REQUEST["ContentDate"])."' and ";
		
	$query .= $ListCondition;
	//echo $query;
	$res = $mysql->Execute($query);
	echo "<br><table width=95% align=center border=1 cellspacing=0 cellpadding=3>";
	echo "<tr class=HeaderOfTable>";
	echo "<td width=1%>ردیف</td>";
	echo "<td >نوع پرونده</td>";
	echo "<td >شماره</td>";
	echo "<td >فرد مربوطه</td>";
	echo "<td >مکان</td>";
	echo "<td >شرح</td>";
	if($_REQUEST["ContentType"]=="PHOTO" || $_REQUEST["ContentType"]=="FILE")
		echo "<td>&nbsp;</td>";
	if($_REQUEST["ContentType"]=="LETTER" || $_REQUEST["ContentType"]=="SESSION")
		echo "<td>شماره</td><td>تاریخ</td><td>فایل</td>";
	echo "<td>اضافه کردن</a></td>";
	echo "</tr>";
	$i = 0;
	while($rec = $res->FetchRow())
	{
		if($i>100)
		{
			echo "<tr class=FooterOfTable><td colspan=10 align=center>تنها ۱۰۰ مورد اول نشان داده شده اند - لطفا شرایط جستجوی خود را محدودتر کنید</td></tr>";
		}
		$i++;
		if($i%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "<td>".$i."</td>";
		echo "<td>".$rec["FileTypeName"]."</td>";
		echo "<td>&nbsp;".$rec["FileNo"]."</td>";
		echo "<td>".$rec["pplname"]." ".$rec["ppfname"]." ".$rec["splname"]." ".$rec["spfname"]."</td>";
		echo "<td>".$rec["UnitName"]." - ".$rec["SubUnitName"]." - ".$rec["PEduName"]."</td>";
		echo "<td>".$rec["description"]."</td>";
		if($_REQUEST["ContentType"]=="PHOTO")
		{
			echo "<td><a target=_blank href='ShowPhotoFileContent.php?FileContentID=".$rec["FileContentID"]."'>مشاهده تصویر</a></td>";
		}
		else if($_REQUEST["ContentType"]=="FILE")
		{
			echo "<td><a href='DownloadFileContent.php?FileContentID=".$rec["FileContentID"]."'>دریافت فایل</a></td>";
		}
		else if($_REQUEST["ContentType"]=="LETTER" || $_REQUEST["ContentType"]=="SESSION")
		{
			echo "<td>".$rec["ContentNumber"]."</td>";
			echo "<td>".shdate($rec["ContentDate"])."</td>";
			echo "<td><a href='DownloadFileContent.php?FileContentID=".$rec["FileContentID"]."'>دریافت فایل</a></td>";
		}
		echo "<td><a href='NewFileContentLink.php?FileID=".$_REQUEST["FileID"]."&ContentType=".$_REQUEST["ContentType"]."&FileContentID=".$rec["FileContentID"]."'>ثبت اتصال</a></td>";
		echo "</tr>";
	}
	echo "<tr class=FooterOfTable><td colspan=10 align=center><input type=button value='بازگشت' onclick='javascript: history.back()'></td></tr>";
	echo "</table>";
	die();
}
?>
<form method=post>
<table width=80% align=center border=1 cellspacing=0 cellpadding=3>
<tr class=HeaderOfTable>
	<td align=center colspan=2>
	جستجوی  
	<?php 
		if($_REQUEST["ContentType"]=="TEXT") 
			echo "متن";
		else if($_REQUEST["ContentType"]=="PHOTO") 
			echo "تصویر";
		else if($_REQUEST["ContentType"]=="FILE") 
			echo "فایل";
		else if($_REQUEST["ContentType"]=="LETTER") 
			echo "نامه";
		else if($_REQUEST["ContentType"]=="SESSION") 
			echo "جلسه";
		else if($_REQUEST["ContentType"]=="FORM") 
			echo "فرم";
	?>
	مورد نظر در پرونده های قابل دسترس
	</td>
</tr>
<tr>
	<td colspan=2>
	<table width=100% border=0>
	<tr>
		<td width=10%>
		شرح
		</td>
		<td>
			<input type=text name=description id=description>
		</td>
	</tr>
	<?php if($_REQUEST["ContentType"]=="LETTER" || $_REQUEST["ContentType"]=="SESSION") { ?>
	<tr>
		<td width=10%>
		شماره
		</td>
		<td>
			<input type=text name=ContentNumber id=ContentNumber>
		</td>
	</tr>
	<tr>
		<td width=10%>
		تاریخ
		</td>
		<td>
			<input type=text name=ContentDate id=ContentDate>  &nbsp; (روز/ماه/سال دو رقم - مثال: 87/03/12)
		</td>
	</tr>
	
	<?php } ?>
	</table>
	</td>
</tr>
<tr class=HeaderOfTable>
	<td align=center colspan=2>
	<input type=hidden name=Search id=Search value=1>
	<input type=hidden name=ContentType value='<?php echo $_REQUEST["ContentType"] ?>'>
	<input type=hidden name=FileID value='<?php echo $_REQUEST["FileID"] ?>'>
	<input type=submit value='جستجو'>
	</td>
</tr>
</table> 
</form>
</html>