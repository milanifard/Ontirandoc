<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : سابقه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-3
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/SessionHistory.class.php");
include ("classes/UniversitySessions.class.php");
include("classes/UniversitySessionsSecurity.class.php");
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["UniversitySessionID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
$HasRemoveAccess = true;
if($ppc->GetPermission("Add_SessionHistory")=="YES")
	$HasAddAccess = true;
$RemoveType = $ppc->GetPermission("Remove_SessionHistory");
$UpdateType = $ppc->GetPermission("Update_SessionHistory");
$HasViewAccess = $ppc->GetPermission("View_SessionHistory");
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
if(isset($_REQUEST["SearchAction"])) 
{
	$ItemType=htmlentities($_REQUEST["Item_ItemType"], ENT_QUOTES, 'UTF-8');
	$description=htmlentities($_REQUEST["Item_description"], ENT_QUOTES, 'UTF-8');
	$SerachPersonID=htmlentities($_REQUEST["Item_PersonID"], ENT_QUOTES, 'UTF-8');
	$ActionType=htmlentities($_REQUEST["Item_ActionType"], ENT_QUOTES, 'UTF-8');
} 
else
{ 
	$ItemType='';
	$description='';
	$SearchPersonID='';
	$ActionType='';
}
if($HasViewAccess=="PRIVATE")
	$SearchPersonID = $_SESSION["PersonID"];
$res = manage_SessionHistory::Search($_REQUEST["UniversitySessionID"] , $ItemType, $description, $SearchPersonID, $ActionType, ""); 
echo manage_UniversitySessions::ShowSummary($_REQUEST["UniversitySessionID"]);
echo manage_UniversitySessions::ShowTabs($_REQUEST["UniversitySessionID"], "ManageSessionHistory");
if($HasViewAccess!="PUBLIC" && $HasViewAccess!="PRIVATE")
	die();
?>
<form id="SearchForm" name="SearchForm" method=post> 
<input type="hidden" name="PageNumber" id="PageNumber" value="0">
	<input type="hidden" id="UniversitySessionID" name="UniversitySessionID" value="<? echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="SearchAction" id="SearchAction" value="1"> 
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr class="HeaderOfTable">
<td><img src='images/search.gif'>
<b><a href="#" onclick='javascript: if(document.getElementById("SearchTr").style.display=="none") document.getElementById("SearchTr").style.display=""; else document.getElementById("SearchTr").style.display="none";'>جستجو</a></td>
</tr>
<tr id='SearchTr' style='display: none'>
<td>
<table width="100%" align="center" border="0" cellspacing="0">
<tr>
	<td width="1%" nowrap>
 نوع آیتم
	</td>
	<td nowrap>
	<select name="Item_ItemType" id="Item_ItemType" >
		<option value=0>-
		<option value='MAIN'>مشخصه جلسه</option>
		<option value='PRECOMMAND'>دستور کار</option>
		<option value='DECISION'>مصوبه</option>
		<option value='DOCUMENT'>سند</option>
		<option value='MEMBER'>عضو</option>
		<option value='USER'>کاربر</option>
		<option value='OTHER'>سایر</option>
	</select>
	</td>
</tr>

<tr>
	<td width="1%" nowrap>
 شرح
	</td>
	<td nowrap>
	<input type="text" name="Item_description" id="Item_description" maxlength="1000" size="40">
	</td>
</tr>

<tr id="tr_PersonID" name="tr_PersonID" style='display:'>
<td width="1%" nowrap>
	 عمل کننده
</td>
	<td nowrap>
	<input type=hidden name="Item_PersonID" id="Item_PersonID">
	<span id="Span_PersonID_FullName" name="Span_PersonID_FullName"></span>  	
	<a href='#' onclick='javascript: window.open("SelectStaff.php?FormName=SearchForm&InputName=Item_PersonID&SpanName=Span_PersonID_FullName");'>[انتخاب]</a>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 نوع عمل
	</td>
	<td nowrap>
	<select name="Item_ActionType" id="Item_ActionType" >
		<option value=0>-
		<option value='ADD'>اضافه</option>
		<option value='EDIT'>ویرایش</option>
		<option value='REMOVE'>حذف</option>
		<option value='VIEW'>مشاهده</option>
	</select>
	</td>
</tr>

<tr class="HeaderOfTable">
<td colspan="2" align="center"><input type="submit" value="جستجو"></td>
</tr>
</table>
</td>
</tr>
</table>
</form>
<? 
if(isset($_REQUEST["SearchAction"])) 
{
?>
<script>
		document.SearchForm.Item_ItemType.value='<? echo htmlentities($_REQUEST["Item_ItemType"], ENT_QUOTES, 'UTF-8'); ?>';
		document.SearchForm.Item_description.value='<? echo htmlentities($_REQUEST["Item_description"], ENT_QUOTES, 'UTF-8'); ?>';
		document.SearchForm.Item_PersonID.value='<? echo htmlentities($_REQUEST["Item_PersonID"], ENT_QUOTES, 'UTF-8'); ?>';
		document.SearchForm.Item_ActionType.value='<? echo htmlentities($_REQUEST["Item_ActionType"], ENT_QUOTES, 'UTF-8'); ?>';
</script>
<?
}
?> 
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_UniversitySessionID" name="Item_UniversitySessionID" value="<? echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>">
<? if(isset($_REQUEST["PageNumber"]))
	echo "<input type=\"hidden\" name=\"PageNumber\" value=".$_REQUEST["PageNumber"].">"; ?>
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="10">
	سابقه
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%">ردیف</td>
	<td>کد آیتم</td>
	<td>نوع عمل</td>	
	<td>نوع آیتم</td>
	<td>عمل کننده</td>
	<td>زمان انجام</td>
	<td>IPAddress</td>
	<td>شرح</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>".($k+$FromRec+1)."</td>";
	echo "<td>";
	if($res[$k]->ItemType=="MAIN")
		echo "<a href='UpdateUniversitySessions.php?UpdateID=".$res[$k]->ItemID."'>";
	else if($res[$k]->ItemType=="PRECOMMAND")
		echo "<a target=_blank href='NewSessionPreCommands.php?UpdateID=".$res[$k]->ItemID."'>";
	else if($res[$k]->ItemType=="DECISION")
		echo "<a target=_blank href='NewSessionDecisions.php?UpdateID=".$res[$k]->ItemID."'>";
	else if($res[$k]->ItemType=="DOCUMENT")
		echo "<a target=_blank href='NewSessionDocuments.php?UpdateID=".$res[$k]->ItemID."'>";
	else if($res[$k]->ItemType=="MEMBER")
		echo "<a target=_blank href='NewSessionMembers.php?UpdateID=".$res[$k]->ItemID."'>";
	else if($res[$k]->ItemType=="PAList")
		echo "<a href='ManageMembersPAList.php?UniversitySessionID=".$res[$k]->ItemID."'>";
		
	echo htmlentities($res[$k]->ItemID, ENT_QUOTES, 'UTF-8');
	echo "</a>";
	echo "</td>";
	echo "<td>".$res[$k]->ActionType_Desc."</td>";
	echo "<td>".$res[$k]->ItemType_Desc."</td>";
	echo "<td>".$res[$k]->PersonID_FullName."</td>";
	echo "<td>".$res[$k]->ActionTime_Shamsi."</td>";
	echo "<td>".htmlentities($res[$k]->IPAddress, ENT_QUOTES, 'UTF-8')."</td>";	
	echo "<td>".htmlentities($res[$k]->description, ENT_QUOTES, 'UTF-8')."</td>";
	echo "</tr>";
}
?>
<tr bgcolor="#cccccc"><td colspan="10" align="right">
<?
for($k=0; $k<manage_SessionHistory::GetCount($_REQUEST["UniversitySessionID"])/$NumberOfRec; $k++)
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
<form target="_blank" method="post" action="NewSessionHistory.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="UniversitySessionID" name="UniversitySessionID" value="<? echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>

<script>

function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
function ShowPage(PageNumber)
{
	SearchForm.PageNumber.value=PageNumber; 
	SearchForm.submit();
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
