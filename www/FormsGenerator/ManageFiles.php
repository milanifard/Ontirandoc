<?php
include("header.inc.php");
include("classes/FormUtils.class.php");
include("classes/FileTypes.class.php");
include("classes/SecurityManager.class.php");
HTMLBegin();
$list = SecurityManager::GetUserPermittedFileTypesForAccess($_SESSION["PersonID"]);
$FileTypeOptions = "<option value=0>-";
for($i=0; $i<count($list); $i++)
{
	$FileTypeOptions .= "<option value='".$list[$i]["FileTypeID"]."'>".$list[$i]["FileTypeName"];
}
if(isset($_REQUEST["NewFile"]))
{ 
	echo "<br><table width=50% align=center border=1 cellspacing=0 cellpadding=7>";
	echo "<tr class=HeaderOfTable>";
	echo "<td align=center> انتخاب نوع پرونده برای ایجاد پرونده جدید </td>";
	echo "</tr>";
	for($i=0; $i<count($list); $i++)
	{
			echo "<tr><td>&nbsp;<a href='NewFile.php?FileTypeID=".$list[$i]["FileTypeID"]."'><b>".$list[$i]["FileTypeName"]."</td></tr>";
	}
	echo "</table>";
	die();
}
$ouid = 0;
if(isset($_REQUEST["ouid"]))
	$ouid = $_REQUEST["ouid"];
?>
<br>
<form method=post id=f1 name=f1 action=FileSearchResult.php>
<table width=50% align=center border=1 cellspacing=0 cellpadding=6>
<tr class=HeaderOfTable>
	<td align=center>مدیریت پرونده های الکترونیکی</td>
</tr>
<tr>
	<td>
	<table width=100%>
		<tr>
			<td>
			نوع پرونده
			</td>
			<td>
			<select name=FileType id=FileType>
				<?php echo $FileTypeOptions ?>
			</select>
			</td>
		</tr>
		<tr>
			<td>
			کد سیستمی
			</td>
			<td>
				<input type=text name=FileID id=FileID>
			</td>
		</tr>
		<tr>
			<td>
			واحد سازمانی
			</td>
			<td>
				<select id=ouid name=ouid onchange='javascript: document.location="ManageFiles.php?ouid="+this.value'>
					<option value=0>-
					<?php echo FormUtils::CreateUnitsOptions($ouid); ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
			زیر واحد سازمانی
			</td>
			<td>
				<select id=sub_ouid name=sub_ouid>
					<option value=0>-
					<?php echo FormUtils::CreateSubUnitsOptions($ouid, ""); ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
			گروه آموزشی
			</td>
			<td>
				<select id=EduGrpCode name=EduGrpCode>
					<option value=0>-
					<?php echo FormUtils::CreateEduGrpsOptions(""); ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
			شماره پرونده
			</td>
			<td>
				<input type=text name=FileNo id=FileNo>
			</td>
		</tr>
		<tr>
			<td>
			نام خانوادگی شخص مربوط به پرونده
			</td>
			<td>
				<input type=text name=PLName id=PLName>
			</td>
		</tr>
		<tr>
			<td>
			نام شخص مربوط به پرونده
			</td>
			<td>
				<input type=text name=PFName id=PFName>
			</td>
		</tr>
		
	</table>
	</td>
</tr>
<tr class=FooterOfTable>
	<td align=center>
	<input type=submit value='جستجو'>
	&nbsp;
<?php /*
$list = SecurityManager::GetUserPermittedFileTypesForAdding($_SESSION["PersonID"]);
if(count($list)>1) { ?>
	<input type=button value='ثبت پرونده جدید' onclick='javascript: document.location="ManageFiles.php?NewFile=1"'>
<?php } else if(count($list)>0) { ?>
	<input type=button value='ثبت پرونده جدید' onclick='javascript: document.location="NewFile.php?FileTypeID=<?php echo $list[0]->FileTypeID ?>"'>
<?php } */ ?>
	</td>	
</tr>
</table>
</form>
</html>