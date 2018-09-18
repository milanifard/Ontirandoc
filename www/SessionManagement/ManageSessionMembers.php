<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : اعضا
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-2-30

تغییر: 31-2-89
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/SessionMembers.class.php");
include ("classes/UniversitySessions.class.php");
include("classes/UniversitySessionsSecurity.class.php");
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["UniversitySessionID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
$HasRemoveAccess = true;
if($ppc->GetPermission("Add_SessionMembers")=="YES")
	$HasAddAccess = true;
$RemoveType = $ppc->GetPermission("Remove_SessionMembers");
$UpdateType = $ppc->GetPermission("Update_SessionMembers");
$NumberOfRec = 30;
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
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->SessionMemberID])) 
	{
		if($RemoveType=="PUBLIC")
			{
			manage_SessionMembers::Remove($res[$k]->SessionMemberID); 
			$SomeItemsRemoved = true;
		}
	}
}
if($SomeItemsRemoved)
	$res = manage_SessionMembers::GetList($_REQUEST["UniversitySessionID"], $FromRec, $NumberOfRec); 
echo manage_UniversitySessions::ShowSummary($_REQUEST["UniversitySessionID"]);
echo manage_UniversitySessions::ShowTabs($_REQUEST["UniversitySessionID"], "ManageSessionMembers");
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_UniversitySessionID" name="Item_UniversitySessionID" value="<? echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>">
<? if(isset($_REQUEST["PageNumber"]))
	echo "<input type=\"hidden\" name=\"PageNumber\" value=".$_REQUEST["PageNumber"].">"; ?>
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="15">
	اعضا
	</td>
</tr>
<tr class="HeaderOfTable">
	<td rowspan=2 width="1%"> </td>
	<td rowspan=2 width="1%">ردیف</td>
	<td rowspan=2 width="2%">ویرایش</td>
	<td rowspan=2 width="2%">شماره</td>
	<td rowspan=2 >نام خانوادگی</td>	
	<td rowspan=2 >نام</td>
	<td rowspan=2 >نقش </td>
	<td rowspan=2 width=1% nowrap>وضعیت تایید درخواست</td>
	<td align=center colspan=3>امضا</td>
	<td rowspan=2 width=1% nowrap>نوع حضور</td>
	<td rowspan=2 width=1% nowrap>مدت حضور</td>
	<td rowspan=2 width=1% nowrap>غیبت</td>
	<td rowspan=2 width=1% nowrap>دسترسی</td>
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
	echo "<td>";
	if($RemoveType=="PUBLIC")
		echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->SessionMemberID."\">";
	else
		echo " ";
	echo "</td>";
	echo "<td>".($k+$FromRec+1)."</td>";
	echo "	<td>";
	echo "<a target=\"_blank\" href=\"NewSessionMembers.php?UpdateID=".$res[$k]->SessionMemberID."\">";
	if($UpdateType=="PUBLIC")
			echo "<img src='images/edit.gif' title='ویرایش'>";
	else
		echo "<img src='images/read.gif' title='مشاهده'>";
	echo "</a></td>";
	echo "	<td>".$res[$k]->MemberRow."</td>";
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
	echo "	<td>".$res[$k]->PresenceType_Desc."</td>";
	echo "	<td>".floor($res[$k]->PresenceTime/60).":".($res[$k]->PresenceTime%60)."</td>";
	echo "	<td>".floor($res[$k]->TardinessTime/60).":".($res[$k]->TardinessTime%60)."</td>";
	echo "<td>";
	if($res[$k]->MemberPersonID>0 && $UpdateType=="PUBLIC")
		echo "<a target=_blank href='UniversitySessionsSetSecurity.php?RecID=".$_REQUEST["UniversitySessionID"]."&SelectedPersonID=".$res[$k]->MemberPersonID."'><img src='images/security.gif' title='تعریف دسترسی'></a>";
	else
		echo "&nbsp;";
	echo "</td>";
	
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="15" align="center">
<? if($RemoveType!="NONE") { ?>
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
<? } ?>
<? if($HasAddAccess) { ?>
	 <input type="button" onclick='javascript: NewRecordForm.submit();' value='ایجاد'>
<? } ?>
</td>
</tr>
<tr bgcolor="#cccccc"><td colspan="15" align="right">
<?
for($k=0; $k<manage_SessionMembers::GetCount($_REQUEST["UniversitySessionID"])/$NumberOfRec; $k++)
{
	if($PageNumber!=$k)
		echo "<a href='javascript: ShowPage(".($k).")'>";
	echo ($k+1);
	if($PageNumber!=$k)
		echo "</a>";
	echo " ";
}
?>
</td></tr>
</table>
</form>
<form target="_blank" method="post" action="NewSessionMembers.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="UniversitySessionID" name="UniversitySessionID" value="<? echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<form method="post" name="f2" id="f2">
<input type="hidden" name="PageNumber" id="PageNumber" value="0">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
function ShowPage(PageNumber)
{
	f2.PageNumber.value=PageNumber; 
	f2.submit();
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
