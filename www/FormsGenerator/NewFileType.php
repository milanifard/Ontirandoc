<?php
include("header.inc.php");
include("classes/FileTypes.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"]))
{
	if(isset($_REQUEST["Item_RelatedPersonCanBeProffessor"]))
		$Item_RelatedPersonCanBeProffessor = "YES";
	else
		$Item_RelatedPersonCanBeProffessor = "NO";
	if(isset($_REQUEST["Item_RelatedPersonCanBeStaff"]))
		$Item_RelatedPersonCanBeStaff = "YES";
	else
		$Item_RelatedPersonCanBeStaff = "NO";
	if(isset($_REQUEST["Item_RelatedPersonCanBeStudent"]))
		$Item_RelatedPersonCanBeStudent = "YES";
	else
		$Item_RelatedPersonCanBeStudent = "NO";
	if(isset($_REQUEST["Item_RelatedPersonCanBeOther"]))
		$Item_RelatedPersonCanBeOther = "YES";
	else
		$Item_RelatedPersonCanBeOther = "NO";
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_FileTypes::Add($_REQUEST["Item_FileTypeName"]
				, $_REQUEST["Item_UserCanChangeLocation"]
				, $_REQUEST["Item_SetLocationType"]
				, $Item_RelatedPersonCanBeProffessor
				, $Item_RelatedPersonCanBeStaff
				, $Item_RelatedPersonCanBeStudent
				, $Item_RelatedPersonCanBeOther
				, $_REQUEST["Item_RelatedToPerson"]
				);
		echo "<script>window.opener.document.location='ManageFileTypes.php'; window.close();</script>";
		die();
				
	}	
	else 
	{	
		manage_FileTypes::Update($_REQUEST["UpdateID"] 
				, $_REQUEST["Item_FileTypeName"]
				, $_REQUEST["Item_UserCanChangeLocation"]
				, $_REQUEST["Item_SetLocationType"]
				, $Item_RelatedPersonCanBeProffessor
				, $Item_RelatedPersonCanBeStaff
				, $Item_RelatedPersonCanBeStudent
				, $Item_RelatedPersonCanBeOther
				, $_REQUEST["Item_RelatedToPerson"]
				);
		echo "<script>window.opener.document.location='ManageFileTypes.php'; window.close();</script>";
		die();
				
	}	
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_FileTypes();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_FileTypeName.value='".$obj->FileTypeName."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_UserCanChangeLocation.value='".$obj->UserCanChangeLocation."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_SetLocationType.value='".$obj->SetLocationType."'; \r\n ";
	if($obj->RelatedPersonCanBeProffessor=="YES") 
		$LoadDataJavascriptCode .= "document.f1.Item_RelatedPersonCanBeProffessor.checked='true'; \r\n ";
	if($obj->RelatedPersonCanBeStaff=="YES") 
		$LoadDataJavascriptCode .= "document.f1.Item_RelatedPersonCanBeStaff.checked='true'; \r\n ";
	if($obj->RelatedPersonCanBeStudent=="YES")
		$LoadDataJavascriptCode .= "document.f1.Item_RelatedPersonCanBeStudent.checked='true'; \r\n ";
	if($obj->RelatedPersonCanBeOther=="YES")
		$LoadDataJavascriptCode .= "document.f1.Item_RelatedPersonCanBeOther.checked='true'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_RelatedToPerson.value='".$obj->RelatedToPerson."'; \r\n "; 
}	
?>
<script>
<? echo PersiateKeyboard() ?>
</script>
<form method=post id=f1 name=f1>
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=hidden name='UpdateID' id='UpdateID' value='".$_REQUEST["UpdateID"]."'>";
	}
?>
<br><table width=90% border=1 cellspacing=0 align=center>
<tr class=HeaderOfTable><td align=center>ایجاد/ویرایش انواع پرونده</td></tr>
<tr><td>
<table width=100% border=0>
<tr id=tr_FileTypeName name=tr_FileTypeName style='display:'>
<td width=1% nowrap>
	نام
</td>
<td nowrap>
	<input type=text name=Item_FileTypeName id=Item_FileTypeName size=60>
</td>
</tr>
<tr id=tr_UserCanChangeLocation name=tr_UserCanChangeLocation style='display:'>
<td width=1% nowrap>
	آیا کاربر مجاز به تغییر محل پرونده می باشد
</td>
<td nowrap>
	<select name=Item_UserCanChangeLocation id=Item_UserCanChangeLocation>
	<option value='NO'>خیر
	<option value='YES'>بلی
	</select>
</td>
</tr>
<tr id=tr_SetLocationType name=tr_SetLocationType style='display:'>
<td width=1% nowrap>
	محل پرونده 
	</td>
<td nowrap>
	<select name=Item_SetLocationType id=Item_SetLocationType>
	<option value='NONE'>به صورت اتومات تعیین نمی شود
	<option value='RELATED_PERSON'>بر اساس محل کار فرد منتسب به پرونده تنظیم شود
	<option value='CREATOR'>بر اساس محل کار ایجاد کننده پرونده تنظیم شود
	</select>
</td>
</tr>
<tr id=tr_RelatedToPerson name=tr_RelatedToPerson style='display:'>
<td width=1% nowrap>
	این نوع پرونده مربوط به اشخاص است؟
</td>
<td nowrap>
	<select name=Item_RelatedToPerson id=Item_RelatedToPerson onchange='javascript: ChangeItem();'>
	<option value='NO'>خیر
	<option value='YES'>بلی
	</select>
</td>
</tr>
<tr id=tr_R name=tr_R style='display:none'>
<td colspan=2>
	انواع افرادی که می توانند منتسب به این نوع پرونده شوند: 
	<input type=checkbox name=Item_RelatedPersonCanBeProffessor id=Item_RelatedPersonCanBeProffessor>استاد
	<input type=checkbox name=Item_RelatedPersonCanBeStaff id=Item_RelatedPersonCanBeStaff>کارمند
	<input type=checkbox name=Item_RelatedPersonCanBeStudent id=Item_RelatedPersonCanBeStudent>دانشجو
	<input type=checkbox name=Item_RelatedPersonCanBeOther id=Item_RelatedPersonCanBeOther>متفرقه
</td>
</tr>
<tr>
<td colspan=2>
<br>
<li>
منظور از محل پرونده واحد و زیر واحد سازمانی و گروه آموزشی مربوطه می باشد
</td>
</tr>
</table></td></tr><tr class=FooterOfTable><td align=center><input type=button onclick='javascript: ValidateForm();' value='ذخیره'></td></tr>
</table>
<input type=hidden name=Save id=Save value=1>
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
	function ChangeItem()
	{
		if(document.f1.Item_RelatedToPerson.value=="YES") document.getElementById("tr_R").style.display=""; else document.getElementById("tr_R").style.display="none";
	}
	ChangeItem();
</script>
