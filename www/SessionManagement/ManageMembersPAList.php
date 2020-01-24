<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : اعضا
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-2-30

تغییر: 31-2-89
*/

// This file taken by MGhayour
// local url: http://localhost:90/MyProject/Ontirandoc/www/SessionManagement/ManageMembersPAList.php

include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/SessionMembers.class.php");
include ("classes/UniversitySessions.class.php");
include("classes/UniversitySessionsSecurity.class.php");
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["UniversitySessionID"]);
$HasUpdateAccess = $HasViewAccess = "";
$HasUpdateAccess = $ppc->GetPermission("Update_MembersPAList");
$HasViewAccess = $ppc->GetPermission("View_MembersPAList");
$NumberOfRec = 100;
 $k=0;
$PageNumber = 0;
if(isset($_REQUEST["PageNumber"]))
{
	$FromRec = $_REQUEST["PageNumber"]*$NumberOfRec;
	$PageNumber = $_REQUEST["PageNumber"];
}
else
{
	$FromRec = 0; 
}
$res = manage_SessionMembers::GetList($_REQUEST["UniversitySessionID"], $FromRec, $NumberOfRec);
if(isset($_REQUEST["Save"]) && $HasUpdateAccess)
{
	$UniversitySessionID = $_REQUEST["UniversitySessionID"];
	manage_SessionHistory::Add($UniversitySessionID, $UniversitySessionID, "PAList", "", "EDIT");		
	for($k=0; $k<count($res); $k++)
	{
		manage_SessionMembers::UpdatePAStatus($res[$k]->SessionMemberID, $_REQUEST["PresenceType_".$res[$k]->SessionMemberID], $_REQUEST["PresentHour_".$res[$k]->SessionMemberID]*60+$_REQUEST["PresentMin_".$res[$k]->SessionMemberID], $_REQUEST["TardinessHour_".$res[$k]->SessionMemberID]*60+$_REQUEST["TardinessMin_".$res[$k]->SessionMemberID]);
	}
	$res = manage_SessionMembers::GetList($_REQUEST["UniversitySessionID"], $FromRec, $NumberOfRec);
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
echo manage_UniversitySessions::ShowSummary($_REQUEST["UniversitySessionID"]);
echo manage_UniversitySessions::ShowTabs($_REQUEST["UniversitySessionID"], "ManageMembersPAList");
if($HasViewAccess=="NONE")
	die();
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="UniversitySessionID" name="UniversitySessionID" value="<? echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>">
	<input type="hidden" id="Save" name="Save" value="1">
<? if(isset($_REQUEST["PageNumber"]))
	echo "<input type=\"hidden\" name=\"PageNumber\" value=".$_REQUEST["PageNumber"].">"; ?>
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="14">
	اعضا
	</td>
</tr>
<tr class="HeaderOfTable">
	<td rowspan=2 width="1%">ردیف</td>
	<td rowspan=2 >نام خانوادگی</td>	
	<td rowspan=2 >نام</td>
	<td rowspan=2 >نقش </td>
	<td rowspan=2 width=1% nowrap>وضعیت تایید درخواست</td>
	<td align=center colspan=3>امضا</td>
	<td rowspan=2 width=1% nowrap>نوع حضور</td>
	<td rowspan=2 width=1% nowrap>مدت حضور</td>
	<td rowspan=2 width=1% nowrap>غیبت</td>
</tr>
<tr class=HeaderOfTable>
	<td width=1%>وضعیت </td>
	<td>امضا</td>
	<td width=1%>زمان </td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
 $SignImg ='<img src="DisplayCanvas.php?RecId=' . $res[$k]->SessionMemberID . '" width="100"  />';
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>".($k+$FromRec+1)."</td>";
	echo "	<td>".htmlentities($res[$k]->LastName, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".htmlentities($res[$k]->FirstName, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".$res[$k]->MemberRole_Desc."</td>";
	echo "	<td>".$res[$k]->ConfirmStatus_Desc."</td>";
	echo "	<td>".$res[$k]->SignStatus_Desc."</td>";
	//echo "	<td>&nbsp;".htmlentities($res[$k]->SignDescription, ENT_QUOTES, 'UTF-8')."</td>";
	if($res[$k]->canvasimg!='')			
			echo "<td>" . $SignImg . "</td>";
			else
			echo "<td>&nbsp;</td>";
	
	if($res[$k]->SignTime_Shamsi!="date-error")
		echo "	<td nowrap>".$res[$k]->SignTime_Shamsi."</td>";
	else
		echo "	<td>-</td>";
	
	if($HasUpdateAccess=="PUBLIC") 
	{ 
		echo "	<td><select name='PresenceType_".$res[$k]->SessionMemberID."' id='PresenceType_".$res[$k]->SessionMemberID."'>";
		echo "<option value='PRESENT'>حاضر";
		echo "<option value='ABSENT' ";
		if($res[$k]->PresenceType=="ABSENT")
			echo " selected ";	
		echo ">غایب";
		echo "</select></td>";
		echo "	<td nowrap><input type=text size=2 id='PresentMin_".$res[$k]->SessionMemberID."' name='PresentMin_".$res[$k]->SessionMemberID."' value='".floor($res[$k]->PresenceTime%60)."'>:";
		echo "	<input type=text size=2 id='PresentHour_".$res[$k]->SessionMemberID."' name='PresentHour_".$res[$k]->SessionMemberID."' value='".floor($res[$k]->PresenceTime/60)."'></td>";
		echo "	<td nowrap><input type=text size=2 id='TardinessMin_".$res[$k]->SessionMemberID."' name='TardinessMin_".$res[$k]->SessionMemberID."' value='".floor($res[$k]->TardinessTime%60)."'>:";
		echo "<input type=text size=2 id='TardinessHour_".$res[$k]->SessionMemberID."' name='TardinessHour_".$res[$k]->SessionMemberID."' value='".floor($res[$k]->TardinessTime/60)."'></td>";
	}
	else
	{
		if($res[$k]->PresenceType=="ABSENT")
			echo "<td>غایب</td>";
		else
			echo "<td>حاضر</td>";
		echo "	<td nowrap>".floor($res[$k]->PresenceTime%60).":".floor($res[$k]->PresenceTime/60)."</td>";
		echo "	<td nowrap>".floor($res[$k]->TardinessTime%60).":".floor($res[$k]->TardinessTime/60)."</td>";
	}
	echo "</tr>";
}
?>
<? if($HasUpdateAccess=="PUBLIC") { ?>
<tr class="FooterOfTable">
<td colspan="14" align="center">
	 <input type="submit" value='ذخیره'>
</td>
</tr>
<? } ?>
</table>
</form>
<script>
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
