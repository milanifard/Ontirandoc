<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : انواع کارها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectTaskTypes.class.php");
include ("classes/projects.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["Item_title"]))
		$Item_title=$_REQUEST["Item_title"];
	if(isset($_REQUEST["ProjectID"]))
		$Item_ProjectID=$_REQUEST["ProjectID"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_ProjectTaskTypes::Add($Item_title
				, $Item_ProjectID
				);
		echo "<script>window.opener.document.location.reload(); window.close();</script>";
	}	
	else 
	{	
		manage_ProjectTaskTypes::Update($_REQUEST["UpdateID"] 
				, $Item_title
				);
		echo "<script>window.opener.document.location.reload(); window.close();</script>";
		die();
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_ProjectTaskTypes();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_title.value='".htmlentities($obj->title, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش انواع کارها</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr>
	<td width="1%" nowrap>
 عنوان
	</td>
	<td nowrap>
	<input type="text" name="Item_title" id="Item_title" maxlength="100" size="40">
	</td>
</tr>
<? 
if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="ProjectID" id="ProjectID" value='<? if(isset($_REQUEST["ProjectID"])) echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: window.close();" value="بستن">
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
</html>
