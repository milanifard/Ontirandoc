<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : مصوبات جلسه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-2
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/SessionDecisions.class.php");
include_once("classes/UniversitySessions.class.php");
include_once("classes/UniversitySessionsSecurity.class.php");
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["UniversitySessionID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
$HasRemoveAccess = true;
if($ppc->GetPermission("Add_SessionDecisions")=="YES")
	$HasAddAccess = true;
 
$RemoveType = $ppc->GetPermission("Remove_SessionDecisions");
$UpdateType = $ppc->GetPermission("Update_SessionDecisions");

if(isset($_REQUEST["DescisionsListStatus"]) && $UpdateType=="PUBLIC")
{
	if($_REQUEST["DescisionsListStatus"]=="RAW")
	{
		manage_UniversitySessions::UnConfirmDescisionsListStatus($_REQUEST["UniversitySessionID"]);
		manage_UniversitySessions::UnSignAllMembers($_REQUEST["UniversitySessionID"]);
	}
	else
		manage_UniversitySessions::ConfirmDescisionsListStatus($_REQUEST["UniversitySessionID"]);
}

$ParentObj = new be_UniversitySessions();
$ParentObj->LoadDataFromDatabase($_REQUEST["UniversitySessionID"]);

$res = manage_SessionDecisions::GetList($_REQUEST["UniversitySessionID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->SessionDecisionID])) 
	{
		if($RemoveType=="PUBLIC" || ($RemoveType=="PRIVATE" && $res[$k]->CreatorPersonID==$_SESSION["PersonID"]))
			{
			manage_SessionDecisions::Remove($res[$k]->SessionDecisionID); 
			$SomeItemsRemoved = true;
		}
	}
}
if($SomeItemsRemoved)
	$res = manage_SessionDecisions::GetList($_REQUEST["UniversitySessionID"]); 
echo manage_UniversitySessions::ShowSummary($_REQUEST["UniversitySessionID"]);
echo manage_UniversitySessions::ShowTabs($_REQUEST["UniversitySessionID"], "ManageSessionDecisions");
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_UniversitySessionID" name="Item_UniversitySessionID" value="<? echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="10">
	مصوبات جلسه
	</td>
</tr>
<?php if($UpdateType=="PUBLIC") { ?>
<tr bgcolor=#cccccc>
	<td colspan="10">
		وضعیت مصوبات جلسه: 
		<select name=DescisionsListStatus id=DescisionsListStatus onchange='javascript: ChangeStatus(this.value);'>
			<option value='RAW'>در دست تکمیل
			<option value='CONFIRMED' <?php if($ParentObj->DescisionsListStatus=="CONFIRMED") echo "selected"; ?> >تکمیل شده
		</select>
		با قرار گرفتن این گزینه روی حالت
		<strong> 
		تکمیل شده
		</strong> 
		افراد مجاز می توانند مصوبات را
		<strong>
		 امضا
		 </strong>
		  کنند
	</td>
</tr>
<?php } ?>
<tr class="HeaderOfTable">
<?if($ParentObj->DescisionsListStatus=="RAW"){?>
	<td width="1%"> </td>
<?}if($ParentObj->DescisionsListStatus=="RAW"){?>
	<td width="2%">ویرایش</td>
<?}?>
	<td width=1%>ردیف</td>
        <td>شرح</td>
        <td>دستور کار </td>
	<td width=10% nowrap>مسوول پیگیری</td>
	<td width=1% nowrap>تکرار در دستور کار بعدی</td>
	<td width=1%>ضمیمه</td>
	<td width=1% nowrap>مهلت اقدام</td>
	<!--<td width=1% nowrap>اجرا شده</td>-->
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
if($ParentObj->DescisionsListStatus=="RAW"){
	echo "<td>";
	if($RemoveType=="PUBLIC" || ($RemoveType=="PRIVATE" && $res[$k]->CreatorPersonID==$_SESSION["PersonID"]))
		echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->SessionDecisionID."\">";
	else
		echo " ";
	echo "</td>";
}
if($ParentObj->DescisionsListStatus=="RAW"){
	echo "	<td>";
	echo "<a target=\"_blank\" href=\"NewSessionDecisions.php?UpdateID=".$res[$k]->SessionDecisionID."\">";
	if($UpdateType=="PUBLIC" || ($UpdateType=="PRIVATE" && $res[$k]->CreatorPersonID==$_SESSION["PersonID"]))
			echo "<img src='images/edit.gif' title='ویرایش'>";
	else
		echo "<img src='images/read.gif' title='مشاهده'>";
	echo "</a></td>";
}

	echo "	<td>".htmlentities($res[$k]->OrderNo, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".str_replace("\n", "<br>", htmlentities($res[$k]->description, ENT_QUOTES, 'UTF-8'))."</td>";
	echo "	<td>".str_replace("\n", "<br>", htmlentities($res[$k]->SessionPreCommandDescription, ENT_QUOTES, 'UTF-8'))."</td>";

	echo "	<td>&nbsp;".$res[$k]->ResponsiblePersonID_FullName."</td>";
		echo "	<td>".$res[$k]->RepeatInNextSession_Desc."</td>";
	if($res[$k]->RelatedFileName!="")
		echo "	<td><a href='DownloadFile.php?FileType=Decesion&RecID=".$res[$k]->SessionDecisionID."'><img src='images/Download.gif' title='دریافت فایل ضمیمه'></a></td>";
	else
		echo "	<td>&nbsp;</td>";
	echo "	<td nowrap>";
	if($res[$k]->DeadlineDate_Shamsi!="date-error")
		echo $res[$k]->DeadlineDate_Shamsi;
	else
		echo "-";
	echo "</td>";
	/*echo "<td align=center>";
	if($res[$k]->SessionControl=="DONE")
		echo "X";
	else
		echo "&nbsp;";
	echo "</td>";*/
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="9" align="center">
<? if($RemoveType!="NONE" && $ParentObj->DescisionsListStatus=="RAW") { ?>
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
<? } ?>
<? if($HasAddAccess && $ParentObj->DescisionsListStatus=="RAW") { ?>
	 <input type="button" onclick='javascript: NewRecordForm.submit();' value='ایجاد'>
<? } ?>
<!--<input type="button" onclick='javascript: window.open("PrintSessionDecisions.php?UniversitySessionID=<?php echo $_REQUEST["UniversitySessionID"] ?>");' value='تایید امضا'>-->
<input type="button" onclick='javascript: window.open("PrintSession.php?UniversitySessionID=<?php echo $_REQUEST["UniversitySessionID"] ?>");' value='چاپ'>
<?
/*if($_SESSION["UserID"]=='gholami-a'){*/
//		echo "<a href='PrintSessionpdf.php?UniversitySessionID=".$_REQUEST["UniversitySessionID"]."'><img src='images/filetype_pdf.png'
// title='دریافت محتویات جلسه به صورت pdf' align='center'></a>";
//}
?>

</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewSessionDecisions.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="UniversitySessionID" name="UniversitySessionID" value="<? echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
function ChangeStatus(status)
{
	if(status=="RAW")
	{
		if(confirm('اخطار: در صورتیکه وضعیت را مجددا به حالت در حال تکمیل برگردانید چنانچه اعضا صورتجلسه را امضا کرده باشند این امضاها حذف می شود.'))
			document.location="ManageSessionDecisions.php?UniversitySessionID=<?php echo $_REQUEST["UniversitySessionID"] ?>&DescisionsListStatus="+status;
		else
			document.getElementById('DescisionsListStatus').value='CONFIRMED';
	}
	else
		document.location="ManageSessionDecisions.php?UniversitySessionID=<?php echo $_REQUEST["UniversitySessionID"] ?>&DescisionsListStatus="+status;
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
