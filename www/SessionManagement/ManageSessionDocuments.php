<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : مستندات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-3
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/SessionDocuments.class.php");
include_once("classes/UniversitySessions.class.php");
include_once("classes/UniversitySessionsSecurity.class.php");
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["UniversitySessionID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
$HasRemoveAccess = true;
if($ppc->GetPermission("Add_SessionDocuments")=="YES")
	$HasAddAccess = true;
$RemoveType = $ppc->GetPermission("Remove_SessionDocuments");
$UpdateType = $ppc->GetPermission("Update_SessionDocuments");
$OrderByFieldName = "SessionDocumentID";
$OrderType = "";
if(isset($_REQUEST["OrderByFieldName"]))
{
	$OrderByFieldName = $_REQUEST["OrderByFieldName"];
	$OrderType = $_REQUEST["OrderType"];
}
$res = manage_SessionDocuments::GetList($_REQUEST["UniversitySessionID"], $OrderByFieldName, $OrderType); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->SessionDocumentID])) 
	{
		if($RemoveType=="PUBLIC" || ($RemoveType=="PRIVATE" && $res[$k]->CreatorPersonID==$_SESSION["PersonID"]))
			{
			manage_SessionDocuments::Remove($res[$k]->SessionDocumentID); 
			$SomeItemsRemoved = true;
		}
	}
}
if($SomeItemsRemoved)
	$res = manage_SessionDocuments::GetList($_REQUEST["UniversitySessionID"], $OrderByFieldName, $OrderType); 
echo manage_UniversitySessions::ShowSummary($_REQUEST["UniversitySessionID"]);
echo manage_UniversitySessions::ShowTabs($_REQUEST["UniversitySessionID"], "ManageSessionDocuments");
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_UniversitySessionID" name="Item_UniversitySessionID" value="<? echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="8">
	مستندات
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td><a href="javascript: Sort('DocumentDescription', 'ASC');">شرح</a></td>	
	<td width=1%><a href="javascript: Sort('DocumentFile', 'ASC');">فایل</a></td>
	<td width=1%><a href="javascript: Sort('InputOrOutput', 'ASC');">نوع</a></td>
	<td width=10% nowrap><a href="javascript: Sort('CreatorPersonID', 'ASC');">ایجاد کننده</a></td>
	<td width=1% nowrap><a href="javascript: Sort('CreateTime', 'ASC');">تاریخ ایجاد</a></td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	if($RemoveType=="PUBLIC" || ($RemoveType=="PRIVATE" && $res[$k]->CreatorPersonID==$_SESSION["PersonID"]))
		echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->SessionDocumentID."\">";
	else
		echo " ";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td>";
	echo "<a target=\"_blank\" href=\"NewSessionDocuments.php?UpdateID=".$res[$k]->SessionDocumentID."\">";
	if($UpdateType=="PUBLIC" || ($UpdateType=="PRIVATE" && $res[$k]->CreatorPersonID==$_SESSION["PersonID"]))
			echo "<img src='images/edit.gif' title='ویرایش'>";
	else
		echo "<img src='images/read.gif' title='مشاهده'>";
	echo "</a></td>";
	echo "	<td>".htmlentities($res[$k]->DocumentDescription, ENT_QUOTES, 'UTF-8')."</td>";	
	echo "	<td><a href='DownloadFile.php?FileType=Document&RecID=".$res[$k]->SessionDocumentID."'><img src='images/Download.gif'></a></td>";
		echo "	<td>".$res[$k]->InputOrOutput_Desc."</td>";
	echo "	<td nowrap>".$res[$k]->CreatorPersonID_FullName."</td>";
	echo "	<td nowrap>".$res[$k]->CreateTime_Shamsi."</td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="8" align="center">
<? if($RemoveType!="NONE") { ?>
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
<? } ?>
<? if($HasAddAccess) { ?>
	 <input type="button" onclick='javascript: NewRecordForm.submit();' value='ایجاد'>
<? } ?>
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewSessionDocuments.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="UniversitySessionID" name="UniversitySessionID" value="<? echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<form method="post" name="f2" id="f2">
<input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
<input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
function Sort(OrderByFieldName, OrderType)
{
	f2.OrderByFieldName.value=OrderByFieldName; 
	f2.OrderType.value=OrderType; 
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
