<?php
/*
  صفحه  ایجاد/ویرایش مربوط به : جلسات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-2-29
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/SessionMembers.class.php");
include_once("classes/UniversitySessions.class.php");
include_once("classes/UniversitySessionsSecurity.class.php");
HTMLBegin();

/*$mysql = pdodb::getInstance();
$query = "delete from sessionmanagement.UniversitySessions where UniversitySessionID='1338'";

$mysql->Execute($query);*/

 	/*	 $LID = manage_UniversitySessions::GetLID($SessionTypeID);echo $LID;*/
if(isset($_REQUEST["UpdateID"]))
{ 
	$pc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["UpdateID"]);
}
else
	die(); // این صفحه فقط در مود ویرایش است

if(isset($_REQUEST["Save"])) 
{
	$Item_SessionNumber = $Item_SessionTypeID = $Item_SessionTitle = $Item_SessionDate = $Item_SessionStartTime = $Item_SessionDurationTime = $Item_SessionStatus = "";
	if(isset($_REQUEST["Item_SessionTypeID"]))
		$Item_SessionTypeID=$_REQUEST["Item_SessionTypeID"];
	if(isset($_REQUEST["Item_SessionNumber"]))
		$Item_SessionNumber=$_REQUEST["Item_SessionNumber"];
	if(isset($_REQUEST["Item_SessionTitle"]))
		$Item_SessionTitle=$_REQUEST["Item_SessionTitle"];
	if(isset($_REQUEST["SessionDate_DAY"]))
	{
		$Item_SessionDate = SharedClass::ConvertToMiladi($_REQUEST["SessionDate_YEAR"], $_REQUEST["SessionDate_MONTH"], $_REQUEST["SessionDate_DAY"]);
	}
	if(isset($_REQUEST["Item_SessionLocation"]))
		$Item_SessionLocation=$_REQUEST["Item_SessionLocation"];
	if(isset($_REQUEST["SessionStartTime_HOUR"]))
	{
		$Item_SessionStartTime=$_REQUEST["SessionStartTime_HOUR"]*60+$_REQUEST["SessionStartTime_MIN"];
	}
	if(isset($_REQUEST["SessionDurationTime_HOUR"]))
	{
		$Item_SessionDurationTime=$_REQUEST["SessionDurationTime_HOUR"]*60+$_REQUEST["SessionDurationTime_MIN"];
	}
	if(isset($_REQUEST["Item_SessionStatus"]))
		$Item_SessionStatus=$_REQUEST["Item_SessionStatus"];
	$Item_SessionDescisionsFile = "";
	$Item_SessionDescisionsFileName = "";
	if(isset($_FILES['Item_SessionDescisionsFile']))
	{
		if (trim($_FILES['Item_SessionDescisionsFile']['name']) != '')
		{
			if ($_FILES['Item_SessionDescisionsFile']['error'] != 0)
			{
				echo ' خطا در ارسال فایل' . $_FILES['Item_SessionDescisionsFile']['error'];
			}
			else
			{
				$_size = $_FILES['Item_SessionDescisionsFile']['size'];
				$_name = $_FILES['Item_SessionDescisionsFile']['tmp_name'];
				$Item_SessionDescisionsFile = addslashes((fread(fopen($_name, 'r' ),$_size)));
				$Item_SessionDescisionsFileName = trim($_FILES['Item_SessionDescisionsFile']['name']);
			}
		}
	}
	if(isset($_REQUEST["Item_SessionDescisionsFileName"]))
	$Item_SessionDescisionsFileName=$_REQUEST["Item_SessionDescisionsFileName"];
	manage_UniversitySessions::Update($_REQUEST["UpdateID"] 
			, $Item_SessionNumber
			, $Item_SessionTitle
			, $Item_SessionDate
			, $Item_SessionLocation
			, $Item_SessionStartTime
			, $Item_SessionDurationTime
			, $Item_SessionStatus
			, $Item_SessionDescisionsFile
			, $Item_SessionDescisionsFileName
			, $pc
			);
	//echo "<script>window.opener.document.location.reload(); window.close();</script>";
	//die();
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';

$obj = new be_UniversitySessions();
$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
$FieldPermission = $pc->GetPermission("SessionNumber");
if($FieldPermission=="WRITE")
	$LoadDataJavascriptCode .= "document.f1.Item_SessionNumber.value='".htmlentities($obj->SessionNumber, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
else if($FieldPermission=="READ")
	$LoadDataJavascriptCode .= "document.getElementById('Item_SessionNumber').innerHTML='".htmlentities($obj->SessionNumber, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
$FieldPermission = $pc->GetPermission("SessionTitle");
if($FieldPermission=="WRITE")
	$LoadDataJavascriptCode .= "document.f1.Item_SessionTitle.value='".htmlentities($obj->SessionTitle, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
else if($FieldPermission=="READ")
	$LoadDataJavascriptCode .= "document.getElementById('Item_SessionTitle').innerHTML='".htmlentities($obj->SessionTitle, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
$FieldPermission = $pc->GetPermission("SessionDate");
//echo $obj->SessionDate_Shamsi."<br>";
if($obj->SessionDate_Shamsi!="date-error") 
{
	if($FieldPermission=="WRITE")
	{
		$LoadDataJavascriptCode .= "document.f1.SessionDate_YEAR.value='".substr($obj->SessionDate_Shamsi, 2, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.SessionDate_MONTH.value='".substr($obj->SessionDate_Shamsi, 5, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.SessionDate_DAY.value='".substr($obj->SessionDate_Shamsi, 8, 2)."'; \r\n "; 
	}
	else if($FieldPermission=="READ")
	{
		$LoadDataJavascriptCode .= "document.getElementById('SessionDate_YEAR').innerHTML='".substr($obj->SessionDate_Shamsi, 2, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.getElementById('SessionDate_MONTH').innerHTML='".substr($obj->SessionDate_Shamsi, 5, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.getElementById('SessionDate_DAY').innerHTML='".substr($obj->SessionDate_Shamsi, 8, 2)."'; \r\n "; 
	}
}
$FieldPermission = $pc->GetPermission("SessionLocation");
if($FieldPermission=="WRITE")
	$LoadDataJavascriptCode .= "document.f1.Item_SessionLocation.value='".htmlentities($obj->SessionLocation, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
else if($FieldPermission=="READ")
	$LoadDataJavascriptCode .= "document.getElementById('Item_SessionLocation').innerHTML='".htmlentities($obj->SessionLocation, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
$FieldPermission = $pc->GetPermission("SessionStartTime");
if($FieldPermission=="WRITE")
{
	$LoadDataJavascriptCode .= "document.f1.SessionStartTime_HOUR.value='".floor($obj->SessionStartTime/60)."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.SessionStartTime_MIN.value='".($obj->SessionStartTime%60)."'; \r\n "; 
}
else if($FieldPermission=="READ")
{
	$LoadDataJavascriptCode .= "document.getElementById('SessionStartTime_HOUR').innerHTML='".floor($obj->SessionStartTime/60)."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.getElementById('SessionStartTime_MIN').innerHTML='".($obj->SessionStartTime%60)."'; \r\n "; 
}
$FieldPermission = $pc->GetPermission("SessionDurationTime");
if($FieldPermission=="WRITE")
{
	$LoadDataJavascriptCode .= "document.f1.SessionDurationTime_HOUR.value='".floor($obj->SessionDurationTime/60)."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.SessionDurationTime_MIN.value='".($obj->SessionDurationTime%60)."'; \r\n "; 
}
else if($FieldPermission=="READ")
{
	$LoadDataJavascriptCode .= "document.getElementById('SessionDurationTime_HOUR').innerHTML='".floor($obj->SessionDurationTime/60)."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.getElementById('SessionDurationTime_MIN').innerHTML='".($obj->SessionDurationTime%60)."'; \r\n "; 
}
$FieldPermission = $pc->GetPermission("SessionStatus");
if($FieldPermission=="WRITE")
	$LoadDataJavascriptCode .= "document.f1.Item_SessionStatus.value='".htmlentities($obj->SessionStatus, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
else if($FieldPermission=="READ")
	$LoadDataJavascriptCode .= "document.getElementById('Item_SessionStatus').innerHTML='".htmlentities($obj->SessionStatus_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
$FieldPermission = $pc->GetPermission("SessionDescisionsFile");
?>
<form method="post" id="f1" name="f1" enctype="multipart/form-data" >
<?
	echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	echo manage_UniversitySessions::ShowSummary($_REQUEST["UpdateID"]);
	echo manage_UniversitySessions::ShowTabs($_REQUEST["UpdateID"], "NewUniversitySessions");
?>

<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">

<td align="center">ویرایش مشخصات جلسه</td>
</tr>
<tr>
<td>
<table width="100%" border="0">


<? if($pc->GetPermission("SessionNumber")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 شماره جلسه
	</td>
	<td nowrap>
	<? if($pc->GetPermission("SessionNumber")=="WRITE") { ?>
	<input type="text" name="Item_SessionNumber" id="Item_SessionNumber" maxlength="20" size="40" readonly>


	<? } else if($pc->GetPermission("SessionNumber")=="READ") { ?>
	<span id="Item_SessionNumber" name="Item_SessionNumber"></span> 
	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("SessionTitle")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 عنوان جلسه
	</td>
	<td nowrap>
	<? if($pc->GetPermission("SessionTitle")=="WRITE") { ?>
	<input type="text" name="Item_SessionTitle" id="Item_SessionTitle" maxlength="500" size="40">
	<? } else if($pc->GetPermission("SessionTitle")=="READ") { ?>
	<span id="Item_SessionTitle" name="Item_SessionTitle"></span> 
	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("SessionDate")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 تاریخ تشکیل
	</td>
	<td nowrap>
	<? if($pc->GetPermission("SessionDate")=="WRITE") { ?>
	<input maxlength="2" id="SessionDate_DAY"  name="SessionDate_DAY" type="text" size="2">/
	<input maxlength="2" id="SessionDate_MONTH"  name="SessionDate_MONTH" type="text" size="2" >/
	<input maxlength="2" id="SessionDate_YEAR" name="SessionDate_YEAR" type="text" size="2" >
	<? } else if($pc->GetPermission("SessionDate")=="READ") { ?>
	<span id="SessionDate_DAY" name="SessionDate_DAY"></span>/
 	<span id="SessionDate_MONTH" name="SessionDate_MONTH"></span>/
 	<span id="SessionDate_YEAR" name="SessionDate_YEAR"></span>
 	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("SessionLocation")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 محل تشکیل
	</td>
	<td nowrap>
	<? if($pc->GetPermission("SessionLocation")=="WRITE") { ?>
	<input type="text" name="Item_SessionLocation" id="Item_SessionLocation" maxlength="200" size="40">
	<? } else if($pc->GetPermission("SessionLocation")=="READ") { ?>
	<span id="Item_SessionLocation" name="Item_SessionLocation"></span> 
	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("SessionStartTime")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 زمان شروع
	</td>
	<td nowrap>
	<? if($pc->GetPermission("SessionStartTime")=="WRITE") { ?>
	<input maxlength="2" id="SessionStartTime_MIN"  name="SessionStartTime_MIN" type="text" size="2">:
	<input maxlength="2" id="SessionStartTime_HOUR"  name="SessionStartTime_HOUR" type="text" size="2" >
	<? } else if($pc->GetPermission("SessionStartTime")=="READ") { ?>
	<span id="SessionStartTimeMIN" name="SessionStartTime_MIN"></span>:
 	<span id="SessionStartTime_HOUR" name="SessionStartTime_HOUR"></span>
 	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("SessionDurationTime")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 مدت جلسه
	</td>
	<td nowrap>
	<? if($pc->GetPermission("SessionDurationTime")=="WRITE") { ?>
	<input maxlength="2" id="SessionDurationTime_MIN"  name="SessionDurationTime_MIN" type="text" size="2">:
	<input maxlength="2" id="SessionDurationTime_HOUR"  name="SessionDurationTime_HOUR" type="text" size="2" >
	<? } else if($pc->GetPermission("SessionDurationTime")=="READ") { ?>
	<span id="SessionDurationTimeMIN" name="SessionDurationTime_MIN"></span>:
 	<span id="SessionDurationTime_HOUR" name="SessionDurationTime_HOUR"></span>
 	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("SessionStatus")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 وضعیت جلسه
	</td>
	<td nowrap>
	<? if($pc->GetPermission("SessionStatus")=="WRITE") { ?>
	<select name="Item_SessionStatus" id="Item_SessionStatus" >
		<option value='0'>درخواست تشکیل</option>
		<option value='1'>در حال برگزاری</option>
		<option value='2'>برگزار شده</option>
	</select>
	<? } else if($pc->GetPermission("SessionStatus")=="READ") { ?>
	<span id="Item_SessionStatus" name="Item_SessionStatus"></span> 	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("SessionDescisionsFile")!="NONE") { ?>
<!--<tr>
	<td width="1%" nowrap>
 صورتجلسه
	</td>
	<td nowrap>
	< ? if($pc->GetPermission("SessionDescisionsFile")=="WRITE") { ?>
	<input type="file" name="Item_SessionDescisionsFile" id="Item_SessionDescisionsFile">

	< ?php if($obj->SessionDescisionsFileName!="") { ?>
	<a href='DownloadFile.php?FileType=SessionDescision&RecID=<? echo $_REQUEST["UpdateID"]; ?>'>دریافت فایل [<?php echo $obj->SessionDescisionsFileName ?>]</a>
	< ?php } ?>
	< ? } else if($pc->GetPermission("SessionDescisionsFile")=="READ") { ?>
	< ?php if($obj->SessionDescisionsFileName!="") { ?>
	< ?php if($obj->DescisionsFileStatus=="YES" || manage_SessionMembers::HasSignRight($obj->UniversitySessionID, $_SESSION["PersonID"])=="YES") { ?>
	<a href='DownloadFile.php?FileType=SessionDescision&RecID=<? echo $_REQUEST["UpdateID"]; ?>'>دریافت فایل [<?php echo $obj->SessionDescisionsFileName ?>]</a>
	< ?php } else { ?>
	[< ?php echo $obj->SessionDescisionsFileName ?>] (هنوز تایید نشده است)
	< ?php } ?>
	< ?php } ?>
	< ? } ?>
	</td>
</tr>-->
<? } ?>
<tr>
	<td>ایجاد کننده </td><td><?php echo $obj->Creator_FullName ?></td>
</tr>

</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<? if($pc->HasWriteAccessOnOneItemAtLeast) { ?>
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
