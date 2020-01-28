<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : اعضا
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-3
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/SessionMembers.class.php");
include_once("classes/UniversitySessions.class.php");
include_once("classes/UniversitySessionsSecurity.class.php");
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
if(isset($_REQUEST["UpdateID"])) 
{
	$obj = new be_SessionMembers();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $obj->UniversitySessionID);
}
else
	$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["UniversitySessionID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
if($ppc->GetPermission("Add_SessionMembers")=="YES")
	$HasAddAccess = true;
if(isset($_REQUEST["UpdateID"])) 
{ 
	if($ppc->GetPermission("Update_SessionMembers")=="PUBLIC")
		$HasUpdateAccess = true;
	if($ppc->GetPermission("View_SessionMembers")=="PUBLIC")
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
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["Item_MemberRow"]))
		$Item_MemberRow=$_REQUEST["Item_MemberRow"];
	
	if(isset($_REQUEST["UniversitySessionID"]))
		$Item_UniversitySessionID=$_REQUEST["UniversitySessionID"];
	if(isset($_REQUEST["Item_MemberPersonType"]))
		$Item_MemberPersonType=$_REQUEST["Item_MemberPersonType"];
	if(isset($_REQUEST["Item_MemberPersonID"]))
		$Item_MemberPersonID=$_REQUEST["Item_MemberPersonID"];
	if(isset($_REQUEST["Item_FirstName"]))
		$Item_FirstName=$_REQUEST["Item_FirstName"];
	if(isset($_REQUEST["Item_LastName"]))
		$Item_LastName=$_REQUEST["Item_LastName"];
	if(isset($_REQUEST["Item_MemberRole"]))
		$Item_MemberRole=$_REQUEST["Item_MemberRole"];
	if(isset($_REQUEST["Item_NeedToConfirm"]))
		$Item_NeedToConfirm=$_REQUEST["Item_NeedToConfirm"];
	if(isset($_REQUEST["Item_AccessSign"]))
		$Item_AccessSign=$_REQUEST["Item_AccessSign"];
	if(isset($_REQUEST["Item_ConfirmStatus"]))
		$Item_ConfirmStatus=$_REQUEST["Item_ConfirmStatus"];
	if(isset($_REQUEST["Item_SignStatus"]))
		$Item_SignStatus=$_REQUEST["Item_SignStatus"];
	if(isset($_REQUEST["Item_SignDescription"]))
		$Item_SignDescription=$_REQUEST["Item_SignDescription"];
	if(isset($_REQUEST["SignTime_DAY"]))
	{
		$Item_SignTime = SharedClass::ConvertToMiladi($_REQUEST["SignTime_YEAR"], $_REQUEST["SignTime_MONTH"], $_REQUEST["SignTime_DAY"]);
	}
	if(isset($_REQUEST["Item_PresenceType"]))
		$Item_PresenceType=$_REQUEST["Item_PresenceType"];
	if(isset($_REQUEST["PresenceTime_HOUR"]))
	{
		$Item_PresenceTime=$_REQUEST["PresenceTime_HOUR"]*60+$_REQUEST["PresenceTime_MIN"];
	}
	if(isset($_REQUEST["TardinessTime_HOUR"]))
	{
		$Item_TardinessTime=$_REQUEST["TardinessTime_HOUR"]*60+$_REQUEST["TardinessTime_MIN"];
	}
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		if($HasAddAccess)
		manage_SessionMembers::Add($Item_MemberRow, $Item_UniversitySessionID
				, $Item_MemberPersonType
				, $Item_MemberPersonID
				, $Item_FirstName
				, $Item_LastName
				, $Item_MemberRole
				, $Item_NeedToConfirm
				, $Item_AccessSign
				, $Item_ConfirmStatus
				, $Item_SignStatus
				, $Item_SignDescription
				, $Item_SignTime
				, $Item_PresenceType
				, $Item_PresenceTime
				, $Item_TardinessTime
				);
		echo "<script>window.opener.document.location.reload(); window.close();</script>";
	}	
	else 
	{	
		if($HasUpdateAccess)
		manage_SessionMembers::Update($_REQUEST["UpdateID"]
				, $Item_MemberRow 
				, $Item_MemberPersonType
				, $Item_MemberPersonID
				, $Item_FirstName
				, $Item_LastName
				, $Item_MemberRole
				, $Item_NeedToConfirm
				, $Item_AccessSign
				, $Item_ConfirmStatus
				, $Item_SignStatus
				, $Item_SignDescription
				, $Item_SignTime
				, $Item_PresenceType
				, $Item_PresenceTime
				, $Item_TardinessTime
				);
		echo "<script>window.opener.document.location.reload(); window.close();</script>";
		die();
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_SessionMembers();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_MemberRow.value='".$obj->MemberRow."'; \r\n ";
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_MemberPersonType.value='".htmlentities($obj->MemberPersonType, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_MemberPersonType').innerHTML='".htmlentities($obj->MemberPersonType_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.getElementById('Span_MemberPersonID_FullName').innerHTML='".$obj->MemberPersonID_FullName."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.getElementById('Item_MemberPersonID').value='".$obj->MemberPersonID."'; \r\n ";

	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_FirstName.value='".htmlentities($obj->FirstName, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_FirstName').innerHTML='".htmlentities($obj->FirstName, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_LastName.value='".htmlentities($obj->LastName, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_LastName').innerHTML='".htmlentities($obj->LastName, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_MemberRole.value='".htmlentities($obj->MemberRole, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_MemberRole').innerHTML='".htmlentities($obj->MemberRole_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_NeedToConfirm.value='".htmlentities($obj->NeedToConfirm, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_NeedToConfirm').innerHTML='".htmlentities($obj->NeedToConfirm_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_AccessSign.value='".htmlentities($obj->AccessSign, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_AccessSign').innerHTML='".htmlentities($obj->AccessSign_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_ConfirmStatus.value='".htmlentities($obj->ConfirmStatus, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_ConfirmStatus').innerHTML='".htmlentities($obj->ConfirmStatus_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_SignStatus.value='".htmlentities($obj->SignStatus, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_SignStatus').innerHTML='".htmlentities($obj->SignStatus_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_SignDescription.value='".htmlentities($obj->SignDescription, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_SignDescription').innerHTML='".htmlentities($obj->SignDescription, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	if($obj->SignTime_Shamsi!="date-error") 
	{
		if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		{
			$LoadDataJavascriptCode .= "document.f1.SignTime_YEAR.value='".substr($obj->SignTime_Shamsi, 2, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.f1.SignTime_MONTH.value='".substr($obj->SignTime_Shamsi, 5, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.f1.SignTime_DAY.value='".substr($obj->SignTime_Shamsi, 8, 2)."'; \r\n "; 
		}
		else 
		{
			$LoadDataJavascriptCode .= "document.getElementById('SignTime_YEAR').innerHTML='".substr($obj->SignTime_Shamsi, 2, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.getElementById('SignTime_MONTH').innerHTML='".substr($obj->SignTime_Shamsi, 5, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.getElementById('SignTime_DAY').innerHTML='".substr($obj->SignTime_Shamsi, 8, 2)."'; \r\n "; 
		}
	}
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_PresenceType.value='".htmlentities($obj->PresenceType, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_PresenceType').innerHTML='".htmlentities($obj->PresenceType_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
	{
		$LoadDataJavascriptCode .= "document.f1.PresenceTime_HOUR.value='".floor($obj->PresenceTime/60)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.PresenceTime_MIN.value='".($obj->PresenceTime%60)."'; \r\n "; 
	}
	else 
	{
		$LoadDataJavascriptCode .= "document.getElementById('PresenceTime_HOUR').innerHTML='".floor($obj->PresenceTime/60)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.getElementById('PresenceTime_MIN').innerHTML='".($obj->PresenceTime%60)."'; \r\n "; 
	}
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
	{
		$LoadDataJavascriptCode .= "document.f1.TardinessTime_HOUR.value='".floor($obj->TardinessTime/60)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.TardinessTime_MIN.value='".($obj->TardinessTime%60)."'; \r\n "; 
	}
	else 
	{
		$LoadDataJavascriptCode .= "document.getElementById('TardinessTime_HOUR').innerHTML='".floor($obj->TardinessTime/60)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.getElementById('TardinessTime_MIN').innerHTML='".($obj->TardinessTime%60)."'; \r\n "; 
	}
}
$MaxMemberRow = manage_SessionMembers::GetLastMemberRow($_REQUEST["UniversitySessionID"]);	
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
<td align="center">ایجاد/ویرایش اعضا</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr>
	<td width="1%" nowrap>
	 ردیف
	</td>
	<td nowrap>
	<input type="text" name="Item_MemberRow" id="Item_MemberRow" maxlength="2" size="2" value='<?php echo $MaxMemberRow ?>'>
	</td>
</tr>

<? 
if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="UniversitySessionID" id="UniversitySessionID" value='<? if(isset($_REQUEST["UniversitySessionID"])) echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
<tr>
	<td width="1%" nowrap>
 نوع عضو
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<select name="Item_MemberPersonType" id="Item_MemberPersonType"  onchange="javascript: Select_MemberPersonType(this.value);" >
		<option value='PERSONEL'>پرسنل</option>
		<option value='OTHER'>سایر</option>
	</select>
	<? } else { ?>
	<span id="Item_MemberPersonType" name="Item_MemberPersonType"></span> 	<? } ?>
	</td>
</tr>
<tr id="tr_MemberPersonID" name="tr_MemberPersonID" style='display:'>
	<td width="1%" nowrap>
 عضو
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type=hidden name="Item_MemberPersonID" id="Item_MemberPersonID">
	<span id="Span_MemberPersonID_FullName" name="Span_MemberPersonID_FullName"></span> 	
	<a href='#' onclick='javascript: window.open("SelectStaff.php?InputName=Item_MemberPersonID&SpanName=Span_MemberPersonID_FullName&LInput=Item_LastName&FInput=Item_FirstName");'>[انتخاب]</a>
	<? } else { ?>
	<span id="Span_MemberPersonID_FullName" name="Span_MemberPersonID_FullName"></span> 	<? } ?>
	</td>
</tr>
<tr id="tr_FirstName" name="tr_FirstName" style='display: none'>
	<td width="1%" nowrap>
 نام
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type="text" name="Item_FirstName" id="Item_FirstName" maxlength="100" size="40">
	<? } else { ?>
	<span id="Item_FirstName" name="Item_FirstName"></span> 
	<? } ?>
	</td>
</tr>
<tr id="tr_LastName" name="tr_LastName" style='display: none'>
	<td width="1%" nowrap>
 نام خانوادگی
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type="text" name="Item_LastName" id="Item_LastName" maxlength="100" size="40">
	<? } else { ?>
	<span id="Item_LastName" name="Item_LastName"></span> 
	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 نقش 
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<select name="Item_MemberRole" id="Item_MemberRole">
	<? echo SharedClass::CreateARelatedTableSelectOptions("sessionmanagement.MemberRoles", "MemberRoleID", "title", "title"); ?>	</select>
	<? } else { ?>
	<span id="Item_MemberRole" name="Item_MemberRole"></span> 	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 برگزاری منوط به تایید کاربر است
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<select name="Item_NeedToConfirm" id="Item_NeedToConfirm" >
		<option value='YES'>بلی</option>
		<option value='NO'>خیر</option>
	</select>
	<? } else { ?>
	<span id="Item_NeedToConfirm" name="Item_NeedToConfirm"></span> 	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 اجازه امضای صورتجلسه
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<select name="Item_AccessSign" id="Item_AccessSign" >
		<option value='NO'>خیر</option>
		<option value='YES'>بلی</option>
	</select>
	<? } else { ?>
	<span id="Item_AccessSign" name="Item_AccessSign"></span> 	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 وضعیت تایید درخواست
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<select name="Item_ConfirmStatus" id="Item_ConfirmStatus" >
		<option value='RAW'>در انتظار تایید</option>
		<option value='ACCEPT'>پذیرفته</option>
		<option value='REJECT'>رد شده</option>
	</select>
	<? } else { ?>
	<span id="Item_ConfirmStatus" name="Item_ConfirmStatus"></span> 	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 وضعیت امضا
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<select name="Item_SignStatus" id="Item_SignStatus" >
		<option value='NO'>خیر</option>
		<option value='YES'>بلی</option>
	</select>
	<? } else { ?>
	<span id="Item_SignStatus" name="Item_SignStatus"></span> 	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 شرح امضا
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type="text" name="Item_SignDescription" id="Item_SignDescription" maxlength="200" size="40">
	<? } else { ?>
	<span id="Item_SignDescription" name="Item_SignDescription"></span> 
	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 زمان امضا
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input maxlength="2" id="SignTime_DAY"  name="SignTime_DAY" type="text" size="2">/
	<input maxlength="2" id="SignTime_MONTH"  name="SignTime_MONTH" type="text" size="2" >/
	<input maxlength="2" id="SignTime_YEAR" name="SignTime_YEAR" type="text" size="2" >
	<? } else { ?>
	<span id="SignTime_DAY" name="SignTime_DAY"></span>/
 	<span id="SignTime_MONTH" name="SignTime_MONTH"></span>/
 	<span id="SignTime_YEAR" name="SignTime_YEAR"></span>
 	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 نوع حضور
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<select name="Item_PresenceType" id="Item_PresenceType" >
		<option value='PRESENT'>حاضر</option>
		<option value='ABSENT'>غایب</option>
	</select>
	<? } else { ?>
	<span id="Item_PresenceType" name="Item_PresenceType"></span> 	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 مدت حضور
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input maxlength="2" id="PresenceTime_MIN"  name="PresenceTime_MIN" type="text" size="2">:
	<input maxlength="2" id="PresenceTime_HOUR"  name="PresenceTime_HOUR" type="text" size="2" >
	<? } else { ?>
	<span id="PresenceTime_MIN" name="PresenceTime_MIN"></span>:
 	<span id="PresenceTime_HOUR" name="PresenceTime_HOUR"></span>
 	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 غیبت
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input maxlength="2" id="TardinessTime_MIN"  name="TardinessTime_MIN" type="text" size="2">:
	<input maxlength="2" id="TardinessTime_HOUR"  name="TardinessTime_HOUR" type="text" size="2" >
	<? } else { ?>
	<span id="TardinessTime_MIN" name="TardinessTime_MIN"></span>:
 	<span id="TardinessTime_HOUR" name="TardinessTime_HOUR"></span>
 	<? } ?>
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || (!isset($_REQUEST["UpdateID"]) && $HasAddAccess))
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
	function Select_MemberPersonType(SelectedValue) 
	{
		if(SelectedValue=='PERSONEL')
			document.getElementById('tr_LastName').style.display='none';
		if(SelectedValue=='PERSONEL')
			document.getElementById('tr_FirstName').style.display='none';
		if(SelectedValue=='PERSONEL')
			document.getElementById('tr_MemberPersonID').style.display='';
		if(SelectedValue=='OTHER')
			document.getElementById('tr_LastName').style.display='';
		if(SelectedValue=='OTHER')
			document.getElementById('tr_FirstName').style.display='';
		if(SelectedValue=='OTHER')
			document.getElementById('tr_MemberPersonID').style.display='none';
	}
	Select_MemberPersonType(document.getElementById('Item_MemberPersonType').value);
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
