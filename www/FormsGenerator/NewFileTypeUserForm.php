<?php
include("header.inc.php");
include("classes/FileTypeForms.class.php");
include("classes/FileTypeUserPermissions.class.php");
include("classes/FileTypeUserPermittedForms.class.php");
$ParentObj = new be_FileTypeUserPermissions();
$ParentObj->LoadDataFromDatabase($_REQUEST["FileTypeUserPermissionID"]);

HTMLBegin();
if(isset($_REQUEST["Save"]))
{
	if(isset($_REQUEST["Item_AddFormPermission"]))
		$Item_AddFormPermission='YES';
	else 
		$Item_AddFormPermission='NO';
	if(isset($_REQUEST["Item_RemoveFormPermission"]))
		$Item_RemoveFormPermission='YES';
	else 
		$Item_RemoveFormPermission='NO';
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_FileTypeUserPermittedForms::Add($_REQUEST["FileTypeUserPermissionID"]
				, $_REQUEST["Item_FormsStructID"]
				, $Item_AddFormPermission
				, $Item_RemoveFormPermission
				, 'YES'
				);
	}	
	else 
	{	
		manage_FileTypeUserPermittedForms::Update($_REQUEST["UpdateID"] 
				, $_REQUEST["Item_FormsStructID"]
				, $Item_AddFormPermission
				, $Item_RemoveFormPermission
				, 'YES'
				);
	}	
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
	echo "<script>window.opener.document.location='ManageFileTypeUserForms.php?id=".$_REQUEST["FileTypeUserPermissionID"]."'</script>";
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_FileTypeUserPermittedForms();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_FormsStructID.value='".$obj->FormsStructID."'; \r\n "; 
	if($obj->AddFormPermission=="YES")
		$LoadDataJavascriptCode .= "document.f1.Item_AddFormPermission.checked='true'; \r\n "; 
	if($obj->RemoveFormPermission=="YES")
		$LoadDataJavascriptCode .= "document.f1.Item_RemoveFormPermission.checked='true'; \r\n "; 
	if($obj->ViewFormPermission=="YES")
		$LoadDataJavascriptCode .= "document.f1.Item_ViewFormPermission.checked='true'; \r\n "; 
}	
$FormsList = "<option value='0'>-";
$list = manage_FileTypeForms::GetList($ParentObj->FileTypeID);
for($i=0; $i<count($list); $i++)
	$FormsList .= "<option value='".$list[$i]->FormsStructID."'>".$list[$i]->FormTitle;
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
<tr class=HeaderOfTable><td align=center>ایجاد/ویرایش فرمهای مجاز و نحوه دسترسی به آنها در انواع پرونده</td></tr>
<tr><td>
<table width=100% border=0>
	<input type=hidden name=Item_FileTypeUserPermissionID id=Item_FileTypeUserPermissionID value='<? echo $_REQUEST["FileTypeUserPermissionID"]; ?>'>
<tr id=tr_FormsStructID name=tr_FormsStructID style='display:'>
<td width=1% nowrap>
	فرم
</td>
<td nowrap>
	<select name=Item_FormsStructID id=Item_FormsStructID>
		<?php echo $FormsList; ?>
	</select>
	<br>
	تنها فرمهای تعریف شده برای این نوع پرونده در لیست فوق نمایش داده می شوند
</td>
</tr>
<tr id=tr_AddFormPermission name=tr_AddFormPermission style='display:'>
<td width=1% nowrap>
	مجوزها: اضافه کردن فرم
	<input type=checkbox name=Item_AddFormPermission id=Item_AddFormPermission>
	 حذف فرم
	<input type=checkbox name=Item_RemoveFormPermission id=Item_RemoveFormPermission>
</td>
</tr>
</table></td></tr><tr class=FooterOfTable><td align=center><input type=button onclick='javascript: ValidateForm();' value='ذخیره'></td></tr>
</table>
<input type=hidden name=Save id=Save value=1>
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		if(document.f1.Item_FormsStructID.value==0)
		{
			alert('باید فرمی انتخاب شده باشد');
			return;
		}
		document.f1.submit();
	}
</script>
