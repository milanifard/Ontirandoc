<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : مصوبات جلسه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-3
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/SessionDecisions.class.php");
include_once("classes/UniversitySessions.class.php");
include_once("classes/UniversitySessionsSecurity.class.php");
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
if(isset($_REQUEST["UpdateID"])) 
{
	$obj = new be_SessionDecisions();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $obj->UniversitySessionID);
	$UniversitySessionID = $obj->UniversitySessionID;
}
else
{
	$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["UniversitySessionID"]);
	$UniversitySessionID = $_REQUEST["UniversitySessionID"];
}
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
if($ppc->GetPermission("Add_SessionDecisions")=="YES")
	$HasAddAccess = true;
if(isset($_REQUEST["UpdateID"])) 
{ 
	if($ppc->GetPermission("Update_SessionDecisions")=="PUBLIC")
		$HasUpdateAccess = true;
	else if($ppc->GetPermission("Update_SessionDecisions")=="PRIVATE" && $_SESSION["PersonID"]==$obj->CreatorPersonID)
		$HasUpdateAccess = true;
	if($ppc->GetPermission("View_SessionDecisions")=="PUBLIC")
		$HasViewAccess = true;
	else if($ppc->GetPermission("View_SessionDecisions")=="PRIVATE" && $_SESSION["PersonID"]==$obj->CreatorPersonID)
		$HasViewAccess = true;
} 
else 
{ 
	$HasViewAccess = true;
} 
if(!$HasViewAccess)
{ 
	echo "مجوز مشاهده این رکورد را ندارید";
	die();
} 
$MainSession = new be_UniversitySessions();
$MainSession->LoadDataFromDatabase($UniversitySessionID);
if(isset($_REQUEST["Save"])) 
{
	if($MainSession->DescisionsListStatus=="CONFIRMED")
	{
		echo SharedClass::CreateMessageBox("امکان ذخیره سازی وجود ندارد زیرا وضعیت لیست مصوبات تکمیل شده تنظیم شده است");
	}
	else
	{
		$Item_SessionControl = "NOT_START";
		if(isset($_REQUEST["UniversitySessionID"]))
			$Item_UniversitySessionID=$_REQUEST["UniversitySessionID"];
		if(isset($_REQUEST["Item_OrderNo"]))
			$Item_OrderNo=$_REQUEST["Item_OrderNo"];
		if(isset($_REQUEST["Item_description"]))
			$Item_description=$_REQUEST["Item_description"];
		if(isset($_REQUEST["Item_ResponsiblePersonID"]))
			$Item_ResponsiblePersonID=$_REQUEST["Item_ResponsiblePersonID"];
		if(isset($_REQUEST["Item_RepeatInNextSession"]))
			$Item_RepeatInNextSession=$_REQUEST["Item_RepeatInNextSession"];
		if(isset($_REQUEST["Item_SessionControl"]))
			$Item_SessionControl=$_REQUEST["Item_SessionControl"];
		 
		$Item_RelatedFile = "";
		$Item_RelatedFileName = "";
		if (trim($_FILES['Item_RelatedFile']['name']) != '')
		{
			if ($_FILES['Item_RelatedFile']['error'] != 0)
			{
				echo ' خطا در ارسال فایل' . $_FILES['Item_RelatedFile']['error'];
			}
			else
			{
				$_size = $_FILES['Item_RelatedFile']['size'];
				$_name = $_FILES['Item_RelatedFile']['tmp_name'];
				$Item_RelatedFile = addslashes((fread(fopen($_name, 'r' ),$_size)));
				$Item_RelatedFileName = trim($_FILES['Item_RelatedFile']['name']);
			}
		}
		if(isset($_REQUEST["Item_RelatedFileName"]))
			$Item_RelatedFileName=$_REQUEST["Item_RelatedFileName"];
		if(isset($_REQUEST["Item_HasDeadline"]))
			$Item_HasDeadline=$_REQUEST["Item_HasDeadline"];
		if(isset($_REQUEST["DeadlineDate_DAY"]))
		{
			$Item_DeadlineDate = SharedClass::ConvertToMiladi($_REQUEST["DeadlineDate_YEAR"], $_REQUEST["DeadlineDate_MONTH"], $_REQUEST["DeadlineDate_DAY"]);
		}
		if(isset($_REQUEST["Item_CreatorPersonID"]))
			$Item_CreatorPersonID=$_REQUEST["Item_CreatorPersonID"];

		if(isset($_REQUEST["Item_SessionPreCommandID"]))
			$Item_SessionPreCommandID=$_REQUEST["Item_SessionPreCommandID"];
			
		if(!isset($_REQUEST["UpdateID"])) 
		{	
			if($HasAddAccess)
			manage_SessionDecisions::Add($Item_UniversitySessionID
					, $Item_OrderNo
					, $Item_description
					, $Item_ResponsiblePersonID
					, $Item_RepeatInNextSession
					, $Item_RelatedFile
					, $Item_RelatedFileName
					, $Item_HasDeadline
					, $Item_DeadlineDate
					, $Item_SessionPreCommandID
					, $Item_SessionControl
					);
			echo "<script>window.opener.document.location='ManageSessionDecisions.php?UniversitySessionID=".$_REQUEST["UniversitySessionID"]."'; window.close();</script>";
		}	
		else 
		{	
			if($HasUpdateAccess)
			manage_SessionDecisions::Update($_REQUEST["UpdateID"] 
					, $Item_OrderNo
					, $Item_description
					, $Item_ResponsiblePersonID
					, $Item_RepeatInNextSession
					, $Item_RelatedFile
					, $Item_RelatedFileName
					, $Item_HasDeadline
					, $Item_DeadlineDate
					, $Item_SessionPreCommandID
					, $Item_SessionControl
					);
			
			$obj = new be_SessionDecisions();
			$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
			echo "<script>window.opener.document.location='ManageSessionDecisions.php?UniversitySessionID=".$obj->UniversitySessionID."'; window.close();</script>";
			die();
		}	
		echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
	}
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_SessionDecisions();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
	$LoadDataJavascriptCode .= "document.f1.Item_SessionPreCommandID.value='".$obj->SessionPreCommandID."'; \r\n ";
	$LoadDataJavascriptCode .= "document.getElementById('Span_SessionPreCommandRow').innerHTML='".$obj->SessionPreCommandRow."'; \r\n ";
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_OrderNo.value='".htmlentities($obj->OrderNo, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_OrderNo').innerHTML='".htmlentities($obj->OrderNo, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	//if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
	//	$LoadDataJavascriptCode .= "document.f1.Item_description.value='".htmlentities($obj->description, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	//else
	//	$LoadDataJavascriptCode .= "document.getElementById('Item_description').innerHTML='".htmlentities($obj->description, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.getElementById('Span_ResponsiblePersonID_FullName').innerHTML='".$obj->ResponsiblePersonID_FullName."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.getElementById('Item_ResponsiblePersonID').value='".$obj->ResponsiblePersonID."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_RepeatInNextSession.value='".htmlentities($obj->RepeatInNextSession, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_RepeatInNextSession').innerHTML='".htmlentities($obj->RepeatInNextSession_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
	{
		$LoadDataJavascriptCode .= "document.f1.Item_HasDeadline.value='".htmlentities($obj->HasDeadline, ENT_QUOTES, 'UTF-8')."'; \r\n ";
		$LoadDataJavascriptCode .= "document.f1.Item_SessionControl.value='".$obj->SessionControl."'; \r\n ";
	} 
	else
	{
		$LoadDataJavascriptCode .= "document.getElementById('Item_HasDeadline').innerHTML='".htmlentities($obj->HasDeadline_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n ";
		$LoadDataJavascriptCode .= "document.getElementById('Item_SessionControl').innerHTML='".htmlentities($obj->SessionControl_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n ";
	} 
	if($obj->DeadlineDate_Shamsi!="date-error") 
	{
		if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		{
			$LoadDataJavascriptCode .= "document.f1.DeadlineDate_YEAR.value='".substr($obj->DeadlineDate_Shamsi, 2, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.f1.DeadlineDate_MONTH.value='".substr($obj->DeadlineDate_Shamsi, 5, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.f1.DeadlineDate_DAY.value='".substr($obj->DeadlineDate_Shamsi, 8, 2)."'; \r\n "; 
		}
		else 
		{
			$LoadDataJavascriptCode .= "document.getElementById('DeadlineDate_YEAR').innerHTML='".substr($obj->DeadlineDate_Shamsi, 2, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.getElementById('DeadlineDate_MONTH').innerHTML='".substr($obj->DeadlineDate_Shamsi, 5, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.getElementById('DeadlineDate_DAY').innerHTML='".substr($obj->DeadlineDate_Shamsi, 8, 2)."'; \r\n "; 
		}
	}
}	
else
{
	$LoadDataJavascriptCode .= "document.f1.Item_OrderNo.value='".(manage_SessionDecisions::GetMaxOrderNo($_REQUEST["UniversitySessionID"])+1)."'; \r\n ";	
}

?>
<form method="post" id="f1" name="f1" enctype="multipart/form-data" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش مصوبات جلسه</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<? 
if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="UniversitySessionID" id="UniversitySessionID" value='<? if(isset($_REQUEST["UniversitySessionID"])) echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
<tr>
	<td width="1%" nowrap>
 ردیف
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type="text" name="Item_OrderNo" id="Item_OrderNo" maxlength="20" size="40">
	<? } else { ?>
	<span id="Item_OrderNo" name="Item_OrderNo"></span> 
	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 شماره ردیف دستور کار مربوطه
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type=hidden name="Item_SessionPreCommandID" id="Item_SessionPreCommandID">
	<b>
	<span id="Span_SessionPreCommandRow" name="Span_SessionPreCommandRow"></span> 	
	</b>
	<a href='#' onclick='javascript: window.open("SelectFromSessionPreCommands.php?UniversitySessionID=<?php echo $UniversitySessionID ?>");'>[انتخاب]</a>
	<? } else { ?>
	<b>
	<span id="Span_SessionPreCommandRow" name="Span_SessionPreCommandRow"></span> 	<? } ?>
	</b>
	</td>
</tr>

<tr>
	<td width="1%" nowrap>
 شرح
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<textarea name="Item_description" id="Item_description" cols="80" rows="5"><?php if(isset($_REQUEST["UpdateID"])) echo htmlentities($obj->description, ENT_QUOTES, 'UTF-8'); ?></textarea>
	<? } else { ?>
	<span id="Item_description" name="Item_description"><?php if(isset($_REQUEST["UpdateID"])) echo str_replace('\n', '<br>', htmlentities($obj->description, ENT_QUOTES, 'UTF-8')); ?></span> 
	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 مسوول پیگیری
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type=hidden name="Item_ResponsiblePersonID" id="Item_ResponsiblePersonID">
	<span id="Span_ResponsiblePersonID_FullName" name="Span_ResponsiblePersonID_FullName"></span> 	<a href='#' onclick='javascript: window.open("SelectStaff.php?InputName=Item_ResponsiblePersonID&SpanName=Span_ResponsiblePersonID_FullName");'>[انتخاب]</a>
	<? } else { ?>
	<span id="Span_ResponsiblePersonID_FullName" name="Span_ResponsiblePersonID_FullName"></span> 	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 تکرار در دستور کار جلسه بعد
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<select name="Item_RepeatInNextSession" id="Item_RepeatInNextSession" >
		<option value='NO'>خیر</option>
		<option value='YES'>بلی</option>
	</select>
	<? } else { ?>
	<span id="Item_RepeatInNextSession" name="Item_RepeatInNextSession"></span> 	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 فایل ضمیمه
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type="file" name="Item_RelatedFile" id="Item_RelatedFile">
	<? if(isset($_REQUEST["UpdateID"]) && $obj->RelatedFileName!="") { ?>
	<a href='DownloadFile.php?FileType=Decesion&RecID=<? echo $_REQUEST["UpdateID"]; ?>'>دریافت فایل [<?php echo $obj->RelatedFileName; ?>]</a>
	<? } ?>
	<? } else { ?>
	<? if(isset($_REQUEST["UpdateID"]) && $obj->RelatedFileName!="") { ?>
	<a href='DownloadFile.php?FileType=Decesion&RecID=<? echo $_REQUEST["UpdateID"]; ?>'>دریافت فایل [<?php echo $obj->RelatedFileName; ?>]</a>
	<? } ?>
	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 مهلت اقدام دارد
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<select name="Item_HasDeadline" id="Item_HasDeadline" >
		<option value='NO'>خیر</option>
		<option value='YES'>بلی</option>
	</select>
	<? } else { ?>
	<span id="Item_HasDeadline" name="Item_HasDeadline"></span> 	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 مهلت اقدام
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input maxlength="2" id="DeadlineDate_DAY"  name="DeadlineDate_DAY" type="text" size="2">/
	<input maxlength="2" id="DeadlineDate_MONTH"  name="DeadlineDate_MONTH" type="text" size="2" >/
	<input maxlength="2" id="DeadlineDate_YEAR" name="DeadlineDate_YEAR" type="text" size="2" >
	<? } else { ?>
	<span id="DeadlineDate_DAY" name="DeadlineDate_DAY"></span>/
 	<span id="DeadlineDate_MONTH" name="DeadlineDate_MONTH"></span>/
 	<span id="DeadlineDate_YEAR" name="DeadlineDate_YEAR"></span>
 	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 وضعیت اجرا
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<select name="Item_SessionControl" id="Item_SessionControl" >
		<option value='NOT_START'>اجرا نشده</option>
		<option value='DONE'>اجرا شده</option>
	</select>
	<? } else { ?>
	<span id="Item_SessionControl" name="Item_SessionControl"></span>
 	<? } ?>
	</td>
</tr>

</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<? if(($HasUpdateAccess && isset($REQUEST["UpdateID"])) || (!isset($REQUEST["UpdateID"]) && $HasAddAccess))
	{
?>
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
<? } ?>
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
 setInterval(function(){
        
        var xmlhttp;
            if (window.XMLHttpRequest)
            {
                // code for IE7 , Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            }
            else
            {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            
            xmlhttp.open("POST","header.inc.php",true);            
            xmlhttp.send();
        
    }, 60000);

</script>
</html>
