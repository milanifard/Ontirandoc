<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : پیامهای شخصی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-21
*/
error_reporting(E_ERROR | E_PARSE );
//error_reporting(E_ALL);
ini_set("display_errors", 1);
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/PrivateMessageFollows.class.php");
HTMLBegin();
$headers = 'From: falinoos@falinoos.com';

$mysql = pdodb::getInstance();

if(isset($_REQUEST["AutoSave"])) 
{
  if($_REQUEST["TempData"]!="")
  {
    $res = $mysql->Execute("select TemporarySavedDataID from projectmanagement.TemporarySavedData where PersonID='".$_SESSION["PersonID"]."' and FieldName='MessageBody'");
    if($rec = $res->fetch())
    {
      $mysql->Prepare("update projectmanagement.TemporarySavedData set FieldValue=? where PersonID='".$_SESSION["PersonID"]."' and FieldName='MessageBody'");
      $mysql->ExecuteStatement(array($_REQUEST["TempData"]));
    }
    else {
      $mysql->Execute("delete from projectmanagement.TemporarySavedData where PersonID='".$_SESSION["PersonID"]."' and FieldName='MessageBody'");
      $mysql->Prepare("insert into projectmanagement.TemporarySavedData (PersonID, FieldName, FieldValue) values ('".$_SESSION["PersonID"]."','MessageBody',?)");
      $mysql->ExecuteStatement(array($_REQUEST["TempData"]));
    }
  }
  die();
}

if(isset($_REQUEST["Save"])) 
{
	$Item_MessageTitle=$_REQUEST["Item_MessageTitle"];
	$Item_MessageBody=$_REQUEST["Item_MessageBody"];
	$Item_FileName=$_REQUEST["Item_FileName"];
	$Item_ToPersonID=$_REQUEST["Item_ToPersonID"];
	$Item_FileContent = "";
	$Item_FileName = "";
	if (trim($_FILES['Item_FileContent']['name']) != '')
	{
		if ($_FILES['Item_FileContent']['error'] != 0)
		{
			echo ' خطا در ارسال فایل' . $_FILES['Item_FileContent']['error'];
		}
		else
		{
			$_size = $_FILES['Item_FileContent']['size'];
			$_name = $_FILES['Item_FileContent']['tmp_name'];
			$Item_FileContent = addslashes((fread(fopen($_name, 'r' ),$_size)));
			$Item_FileName = trim($_FILES['Item_FileContent']['name']);
		}
	}
	
	$MessageID = manage_PrivateMessages::Add($Item_MessageTitle
			, $Item_MessageBody
			, $Item_FileContent
			, $Item_FileName
			);
	$Receivers = explode(",", $Item_ToPersonID);
	for($j=0; $j<count($Receivers); $j++)
	{
	  $FollowID = manage_PrivateMessageFollows::Add($MessageID
			, $Item_comment
			, $_SESSION["PersonID"]
			, $Receivers[$j]
			, 0
			, ""
			, ""
			, "NOT_READ"
			, 0
			);
	  
	  $MessageSubject = "نامه ای جدید در بخش مکاتبات سامانه ی مدیریت پروژه برای شما ارسال شده است";
	  $Message‌Body = "موضوع نامه: ";
	  $Message‌Body .= $Item_MessageTitle."\r\n";
	  $Message‌Body .= "متن نامه: \r\n".$Item_MessageBody."\r\n";
	  $Message‌Body .= "دسترسی به نامه با لینک زیر: \r\n";
	  $Message‌Body .= "<a target=_blank href='http://pm.falinoos.com/pm/ShowMessage.php?MessageFollowID=".$FollowID."'>مشاهده</a>";
	  
	  $mysql->Prepare("select * from projectmanagement.persons where PersonID=?");
	  $res = $mysql->ExecuteStatement(array($Receivers[$j]));
	  if($rec = $res->fetch())
	  {
	    if($rec["CardNumber"]!="")
	      mail($rec["CardNumber"], $MessageSubject, $Message‌Body, $headers);
	  }
			
	}
	
	echo SharedClass::CreateMessageBox("پیام ارسال شد");
	$mysql->Execute("delete from projectmanagement.TemporarySavedData where PersonID='".$_SESSION["PersonID"]."' and FieldName='MessageBody'");
}
$OldMessageBody = "";
$res = $mysql->Execute("select FieldValue from projectmanagement.TemporarySavedData where PersonID='".$_SESSION["PersonID"]."' and FieldName='MessageBody'");
if($rec = $res->fetch())
{
  $OldMessageBody = $rec["FieldValue"];
}

?>
<form method="post" id="f1" name="f1" enctype="multipart/form-data" >
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ارسال پیام</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr>
	<td width="1%" nowrap>
	<font color=red>*</font> 
 عنوان
	</td>
	<td nowrap>
	<input type="text" name="Item_MessageTitle" id="Item_MessageTitle" maxlength="1000" size="40">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 متن
	</td>
	<td nowrap>
	<textarea name="Item_MessageBody" id="Item_MessageBody" cols="80" rows="5"><? echo $OldMessageBody; ?></textarea>
	<span id=AutoSaveSpan name=AutoSaveSpan></span>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 محتوای فایل
	</td>
	<td nowrap>
	<input type="file" name="Item_FileContent" id="Item_FileContent">
	</td>
</tr>
<tr id="tr_ToPersonID" name="tr_ToPersonID" style='display:'>
      <td width="1%" nowrap>
      <font color=red>*</font> 
		  به کاربر
      </td>
	<td nowrap>
	<input type=hidden name="Item_ToPersonID" id="Item_ToPersonID" value="0">
	<span id="Span_ToPersonID_FullName" name="Span_ToPersonID_FullName"></span> 	<a href='#' onclick='javascript: window.open("SelectMultiStaff.php?InputName=Item_ToPersonID&SpanName=Span_ToPersonID_FullName");'>[انتخاب]</a>
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ارسال">
</td>
</tr>
</table>
<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
	  if(document.getElementById('Item_MessageTitle').value=="")
	  {
	    alert('عنوان را وارد کنید');
	    return;
	  }

	  if(document.getElementById('Item_ToPersonID').value=="0")
	  {
	    alert('گیرنده را مشخص کنید');
	    return;
	  }
	  document.f1.submit();
	}
	
	function AutoSave()
	{
	  document.getElementById('AutoSaveSpan').innerHTML='ذخیره سازی خودکار..';
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
	    if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
	      document.getElementById('AutoSaveSpan').innerHTML = xmlhttp.responseText;
	    }
	  }
	    
	  xmlhttp.open("GET","SendMessage.php?AutoSave=1&TempData="+document.getElementById('Item_MessageBody').value,true);
	  xmlhttp.send();
	  setTimeout("AutoSave()",60000);	  
	}

	setTimeout("AutoSave()",60000);
	
</script>
</html>
