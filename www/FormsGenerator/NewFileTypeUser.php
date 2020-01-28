<?php
include("header.inc.php");
include_once("classes/FileTypeUserPermissions.class.php");
HTMLBegin();

if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_FileTypeUserPermissions();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
	$FileTypeID = $obj->FileTypeID;
}
else
	$FileTypeID = $_REQUEST["FileTypeID"];

if(isset($_REQUEST["Save"]))
{
	if(isset($_REQUEST["Item_DefineAccessPermission"]))
		$Item_DefineAccessPermission='YES';
	else 
		$Item_DefineAccessPermission='NO';
	if(isset($_REQUEST["Item_AddPermission"]))
		$Item_AddPermission='YES';
	else 
		$Item_AddPermission='NO';
	if(isset($_REQUEST["Item_RemovePermission"]))
		$Item_RemovePermission='YES';
	else 
		$Item_RemovePermission='NO';
	if(isset($_REQUEST["Item_UpdatePermission"]))
		$Item_UpdatePermission='YES';
	else 
		$Item_UpdatePermission='NO';
	if(isset($_REQUEST["Item_ContentUpdatePermission"]))
		$Item_ContentUpdatePermission='YES';
	else 
		$Item_ContentUpdatePermission='NO';
	if(isset($_REQUEST["Item_ViewPermission"]))
		$Item_ViewPermission='YES';
	else 
		$Item_ViewPermission='NO';
	if(isset($_REQUEST["Item_TemporarySendPermission"]))
		$Item_TemporarySendPermission='YES';
	else 
		$Item_TemporarySendPermission='NO';
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_FileTypeUserPermissions::Add($_REQUEST["Item_FileTypeID"]
				, $_REQUEST["Item_PersonID"]
				, $_REQUEST["Item_AccessRange"]
				, "NO"
				, $Item_AddPermission
				, $Item_RemovePermission
				, $Item_UpdatePermission
				, $Item_ContentUpdatePermission
				, $Item_ViewPermission
				, $Item_TemporarySendPermission
				);
		echo "<script>window.opener.document.location='ManageFileTypeUsers.php?FileTypeID=".$FileTypeID."'; window.close();</script>";
		die();
	}	
	else 
	{	
		manage_FileTypeUserPermissions::Update($_REQUEST["UpdateID"] 
				, $_REQUEST["Item_PersonID"]
				, $_REQUEST["Item_AccessRange"]
				, "NO"
				, $Item_AddPermission
				, $Item_RemovePermission
				, $Item_UpdatePermission
				, $Item_ContentUpdatePermission
				, $Item_ViewPermission
				, $Item_TemporarySendPermission
				);
		echo "<script>window.opener.document.location='ManageFileTypeUsers.php?FileTypeID=".$FileTypeID."'; window.close();</script>";
		die();
	}	
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
}
$LoadDataJavascriptCode = '';
$PersonName = "";
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_FileTypeUserPermissions();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
	$PersonName = $obj->PersonName; 
	$LoadDataJavascriptCode .= "document.f1.Item_PersonID.value='".$obj->PersonID."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_AccessRange.value='".$obj->AccessRange."'; \r\n ";
	/* 
	if($obj->DefineAccessPermission=="YES")
		$LoadDataJavascriptCode .= "document.f1.Item_DefineAccessPermission.checked='true'; \r\n ";
	*/ 
	if($obj->AddPermission=="YES")
		$LoadDataJavascriptCode .= "document.f1.Item_AddPermission.checked='true'; \r\n "; 
	if($obj->RemovePermission=="YES")
		$LoadDataJavascriptCode .= "document.f1.Item_RemovePermission.checked='true'; \r\n "; 
	if($obj->UpdatePermission=="YES")
		$LoadDataJavascriptCode .= "document.f1.Item_UpdatePermission.checked='true'; \r\n "; 
	if($obj->ContentUpdatePermission=="YES")
		$LoadDataJavascriptCode .= "document.f1.Item_ContentUpdatePermission.checked='true'; \r\n "; 
	if($obj->ViewPermission=="YES")
		$LoadDataJavascriptCode .= "document.f1.Item_ViewPermission.checked='true'; \r\n "; 
	if($obj->TemporarySendPermission=="YES")
		$LoadDataJavascriptCode .= "document.f1.Item_TemporarySendPermission.checked='true'; \r\n "; 
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
<tr class=HeaderOfTable><td align=center>ایجاد/ویرایش مجوزهای کاربران روی انواع پرونده</td></tr>
<tr><td>
<table width=100% border=0>
	<input type=hidden name=Item_FileTypeID id=Item_FileTypeID value='<? echo $_REQUEST["FileTypeID"]; ?>'>
<tr id=tr_PersonID name=tr_PersonID style='display:'>
<td width=1% nowrap>
	کاربر
</td>
<td nowrap>
	<input type=hidden name=Item_PersonID id=Item_PersonID>
	<span id=MySpan name=MySpan><?php echo $PersonName ?></span>
	<a target=_blank href='SelectStaff.php?InputName=Item_PersonID&SpanName=MySpan'>[انتخاب]</a>
</td>
</tr>
<tr id=tr_AccessRange name=tr_AccessRange style='display:'>
<td width=1% nowrap>
	محدوده دسترسی
</td>
<td nowrap>
	<select name=Item_AccessRange id=Item_AccessRange>
	<option value='ONLY_USER'>تنها پرونده هایی که خود کاربر ایجاد کرده است
	<option value='UNIT'>برخی از واحدهای سازمانی
	<option value='SUB_UNIT'>برخی از زیر واحدهای سازمانی
	<option value='EDU_GROUP'>برخی از گروه های آموزشی
	<option value='ALL'>بدون محدودیت
	</select>
</td>
</tr>
<tr id=tr_DefineAccessPermission name=tr_DefineAccessPermission style='display:'>
<td colspan=2 nowrap>
	مجوزها: 
	<!-- 
	 تعریف دسترسی
	<input type=checkbox name=Item_DefineAccessPermission id=Item_DefineAccessPermission>
	 -->
	اضافه
	<input type=checkbox name=Item_AddPermission id=Item_AddPermission>
	حذف
	<input type=checkbox name=Item_RemovePermission id=Item_RemovePermission>
	بروزرسانی مشخصات
	<input type=checkbox name=Item_UpdatePermission id=Item_UpdatePermission>
	بروزرسانی محتوا
	<input type=checkbox name=Item_ContentUpdatePermission id=Item_ContentUpdatePermission>
	مشاهده
	<input type=checkbox name=Item_ViewPermission id=Item_ViewPermission>
	ارسال موقت
	<input type=checkbox name=Item_TemporarySendPermission id=Item_TemporarySendPermission>
</td>
</tr>
</table></td></tr><tr class=FooterOfTable><td align=center><input type=button onclick='javascript: ValidateForm();' value='ذخیره'>
&nbsp;
<input type=button onclick='javascript: document.location="ManageFileTypeUsers.php?FileTypeID=<?php echo $FileTypeID; ?>";' value='بازگشت'>
</td></tr>
</table>
<input type=hidden name=Save id=Save value=1>
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
