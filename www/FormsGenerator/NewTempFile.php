<?php
include("header.inc.php");
HTMLBegin();
$FileTypeName = "";
$SelectedUnit = 0;
$FileTypeID = -1;

$FileID = $_REQUEST["UpdateID"];
$mysql = dbclass::getInstance();
$query = "select f.*, ft.*, p.plname as pplname, p.pfname as ppfname, s.pfname as spfname, s.plname as splname,
								u.ptitle as UnitName, su.ptitle as SubUnitName, eg.PEduName   
			 from formsgenerator.FilesTemporarayAccessList 
			JOIN formsgenerator.files as f using (FileID) 
			LEFT JOIN formsgenerator.FileTypes as ft using (FileTypeID) 
			LEFT JOIN hrms_total.persons as p using (PersonID)
			LEFT JOIN formsgenerator.StudentSpecs as s using (StNo)
			LEFT JOIN hrms_total.org_units as u using (ouid)
			LEFT JOIN hrms_total.org_sub_units su using (sub_ouid)
			LEFT JOIN formsgenerator.EducationalGroups as eg on (f.EduGrpCode=eg.EduGrpCode) where FileID='".$FileID."'";
//	echo $query;
$res = $mysql->Execute($query);
if($rec = $res->FetchRow())
{
	$FileTypeID = $rec["FileTypeID"];
	$FileTypeName = $rec["FileTypeName"];
	$FileNo = $rec["FileNo"];
	if($rec["PersonType"]=="OTHER")
	{
		$PersonTypeName = "سایر";
		$PersonName = $rec["PFName"]." ".$rec["PLName"];
	}
	else if($rec["PersonType"]=="PROF" || $rec["PersonType"]=="STAFF")
	{
		$PersonTypeName = "کارکنان";
		$PersonName = $rec["ppfname"]." ".$rec["pplname"];
	}
	else if($rec["PersonType"]=="STUDENT")
	{
		$PersonTypeName = "دانشجو";
		$PersonName = $rec["spfname"]." ".$rec["splname"];
	} 
	$UnitName = $rec["UnitName"];
	$SubUnitName = $rec["SubUnitName"];
	$EduGroupName = $rec["PEduName"];
	$RelatedToPerson = $rec["RelatedToPerson"];
	$FileTitle = $rec["FileTitle"];
}
else
{
	die();
}
?>
<form method=post id=f1 name=f1>
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=hidden name='UpdateID' id='UpdateID' value='".$_REQUEST["UpdateID"]."'>";
	}
?>
<br><table width=90% border=1 cellspacing=0 align=center>
<tr class=HeaderOfTable><td align=center>ایجاد/ویرایش پرونده ها</td></tr>
<!-- در مود ویرایش امکان مدیریت سایر بخشهای پرونده را در صورت داشتن دسترسی می دهد -->
<?php if(isset($_REQUEST["UpdateID"])) { ?>
<tr bgcolor=#cccccc>
	<td align=center>
	<table width=100% border=1 cellspacing=0>
		<tr>
			<td width=14% align=center bgcolor=#efefef>
				<a href='NewTempFile.php?UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img width=50 border=0 title='مشخصات پرونده' src='images/file_Properties.gif'><br>مشخصات</a>			
			</td>
			<td width=14% align=center >
				<a href='ManageTempFileContent.php?ContentType=TEXT&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img width=50 border=0 title='متون' src='images/file_text.gif'><br>متون</a>			
			</td>
			<td width=14% align=center >
				<a href='ManageTempFileContent.php?ContentType=PHOTO&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='تصاویر' src='images/file_photo.gif'><br>تصاویر</a>			
			</td>
			<td width=14% align=center >
				<a href='ManageTempFileContent.php?ContentType=FILE&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='فایلها' src='images/file_file.gif'><br>فایلها</a>			
			</td>
			<td width=14% align=center >
				<a href='ManageTempFileContent.php?ContentType=FORM&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='فرمها' src='images/file_form.gif'><br>فرمها</a>			
			</td>
			<td width=14% align=center >
				<a href='ManageTempFileContent.php?ContentType=LETTER&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='نامه ها' src='images/file_letter.gif'><br>نامه ها</a>			
			</td>
			<td width=14% align=center >
				<a href='ManageTempFileContent.php?ContentType=SESSION&UpdateID=<?php echo $_REQUEST["UpdateID"]; ?>'><img  width=50 border=0 title='جلسات' src='images/file_session.gif'><br>جلسات</a>			
			</td>
		</tr>
	</table>
	</td>
</tr>
<?php } ?>
<tr><td>
<table width=100% border=0>

<tr id=tr_FileTypeID name=tr_FileTypeID style='display:'>
<td width=1% nowrap>
	نوع پرونده
</td>
<td nowrap>
	<span id=FileTypeName name=FileTypeName><?php echo $FileTypeName ?></span>
</td>
</tr>
<tr id=tr_FileNo name=tr_FileNo style='display:'>
<td width=1% nowrap>
	شماره پرونده
</td>
<td nowrap>
	<?php echo $FileNo; ?>
</td>
</tr>
<?php if($RelatedToPerson=="YES") { ?>
<tr id=tr_PersonType name=tr_PersonType style='display:'>
<td width=1% nowrap>
	نوع شخص
</td>
<td nowrap>
	<?php echo $PersonTypeName; ?>
</td>
</tr>

<tr id=tr_PersonID name=tr_PersonID style='display:'>
<td>نام و نام خانوادگی</td>
<td nowrap>
	<span name=PersonSpan id=PersonSpan><?php echo $PersonName ?></span>&nbsp;
</td>
</tr>
<?php } ?>
<tr id=tr_ouid name=tr_ouid style='display:'>
<td width=1% nowrap>
	واحد سازمانی
</td>
<td nowrap>
	<span name=UnitSpan id=UnitSpan><?php echo $UnitName ?></span>&nbsp;
</td>
</tr>
<tr id=tr_sub_ouid name=tr_sub_ouid style='display:'>
<td width=1% nowrap>
	زیر واحد سازمانی
</td>
<td nowrap>
	<span name=SubUnitSpan id=SubUnitSpan><?php echo $SubUnitName ?></span>&nbsp;
</td>
</tr>
<tr id=tr_EduGrpCode name=tr_EduGrpCode style='display:'>
<td width=1% nowrap>
	گروه آموزشی
</td>
<td nowrap>
	<span name=EduGrpSpan id=EduGrpSpan><?php echo $EduGroupName ?></span>&nbsp;
</td>
</tr>
<tr id=tr_FileTitle name=tr_FileTitle style='display:'>
<td width=1% nowrap>
	عنوان
</td>
<td nowrap>
	<?php echo $FileTitle; ?>
</td>
</tr>
</table></td></tr>
</table>
<input type=hidden name=Save id=Save value=0>
</form>
