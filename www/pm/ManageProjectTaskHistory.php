<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : تاریخچه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-23
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/ProjectTaskHistory.class.php");
include_once("classes/ProjectTasks.class.php");
HTMLBegin();
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
	$Item_PersonID=htmlentities($_REQUEST["Item_PersonID"], ENT_QUOTES, 'UTF-8');
	$ChangedPart=htmlentities($_REQUEST["Item_ChangedPart"], ENT_QUOTES, 'UTF-8');
	$ActionType=htmlentities($_REQUEST["Item_ActionType"], ENT_QUOTES, 'UTF-8');
} 
else
{ 
	$Item_PersonID='';
	$ChangedPart='';
	$ActionType='';
}
$res = manage_ProjectTaskHistory::Search($_REQUEST["ProjectTaskID"] , $Item_PersonID, $ChangedPart, $ActionType, ""); 
echo manage_ProjectTasks::ShowSummary($_REQUEST["ProjectTaskID"]);
echo manage_ProjectTasks::ShowTabs($_REQUEST["ProjectTaskID"], "ManageProjectTaskHistory");
?>
<form id="SearchForm" name="SearchForm" method=post> 
<input type="hidden" name="PageNumber" id="PageNumber" value="0">
	<input type="hidden" id="ProjectTaskID" name="ProjectTaskID" value="<? echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>">
<input type="hidden" name="SearchAction" id="SearchAction" value="1"> 
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr class="HeaderOfTable">
<td><img src='images/search.gif'><b><a href="#" onclick='javascript: if(document.getElementById("SearchTr").style.display=="none") document.getElementById("SearchTr").style.display=""; else document.getElementById("SearchTr").style.display="none";'>جستجو</a></td>
</tr>
<tr id='SearchTr' style='display: none'>
<td>
<table width="100%" align="center" border="0" cellspacing="0">

<tr>
	<td width="1%" nowrap>
 بخش مربوطه
	</td>
	<td nowrap>
	<select name="Item_ChangedPart" id="Item_ChangedPart" >
		<option value=0>-
		<option value='MAIN_TASK'>مشخصات کار</option>
		<option value='COMMENT'>یادداشت</option>
		<option value='DOCUMENT'>سند</option>
		<option value='ACTIVITY'>اقدام</option>
		<option value='REQUISITE'>پیشنیاز</option>
		<option value='USER'>کاربر</option>
		<option value='VIEWER'>ناظر</option>
	</select>
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
		<option value='REMOVE'>حذف</option>
		<option value='UPDATE'>بروزرسانی</option>
		<option value='VIEW'>مشاهده</option>
	</select>
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
		document.SearchForm.Item_PersonID.value='<? echo htmlentities($_REQUEST["Item_PersonID"], ENT_QUOTES, 'UTF-8'); ?>';
		document.SearchForm.Item_ChangedPart.value='<? echo htmlentities($_REQUEST["Item_ChangedPart"], ENT_QUOTES, 'UTF-8'); ?>';
		document.SearchForm.Item_ActionType.value='<? echo htmlentities($_REQUEST["Item_ActionType"], ENT_QUOTES, 'UTF-8'); ?>';
</script>
<?
}
?> 
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_ProjectTaskID" name="Item_ProjectTaskID" value="<? echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>">
<? if(isset($_REQUEST["PageNumber"]))
	echo "<input type=\"hidden\" name=\"PageNumber\" value=".$_REQUEST["PageNumber"].">"; ?>
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="8">
	سابقه
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%">ردیف</td>
	<td width=10% nowrap>زمان انجام</td>
	<td width=10% nowrap>اعمال کننده</td>
	<td width=10% nowrap>نوع عمل</td>	
	<td width=10% nowrap>بخش مربوطه</td>
	<td>شرح کار</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>".($k+$FromRec+1)."</td>";
	echo "	<td nowrap>&nbsp;".$res[$k]->ActionTime_Shamsi."</td>";
	echo "	<td nowrap>".$res[$k]->PersonID_FullName."</td>";
	echo "	<td nowrap>".$res[$k]->ActionType_Desc."</td>";	
	echo "	<td nowrap>".$res[$k]->ChangedPart_Desc;
	echo "	[";
	if($res[$k]->ChangedPart=="MAIN_TASK")
		echo "<a href='NewProjectTasks.php?UpdateID=".$res[$k]->RelatedItemID."'>";
	else if($res[$k]->ChangedPart=="COMMENT")
		echo "<a href='ManageProjectTaskComments.php?UpdateID=".$res[$k]->RelatedItemID."&ProjectTaskID=".$res[$k]->ProjectTaskID."'>";
	else if($res[$k]->ChangedPart=="DOCUMENT")
		echo "<a href='ManageProjectTaskDocuments.php?UpdateID=".$res[$k]->RelatedItemID."&ProjectTaskID=".$res[$k]->ProjectTaskID."'>";
	else if($res[$k]->ChangedPart=="ACTIVITY")
		echo "<a href='NewProjectTaskActivities.php?UpdateID=".$res[$k]->RelatedItemID."'>";
	else if($res[$k]->ChangedPart=="REQUISITE")
		echo "<a href='ManageProjectTaskRequisites.php?UpdateID=".$res[$k]->RelatedItemID."&ProjectTaskID=".$res[$k]->ProjectTaskID."'>";
	else if($res[$k]->ChangedPart=="USER")
		echo "<a href='ManageProjectTaskAssignedUsers.php?UpdateID=".$res[$k]->RelatedItemID."&ProjectTaskID=".$res[$k]->ProjectTaskID."'>";
	else if($res[$k]->ChangedPart=="VIEWER")
		echo "<a href='ManageProjectTaskAssignedUsers.php?UpdateID=".$res[$k]->RelatedItemID."&ProjectTaskID=".$res[$k]->ProjectTaskID."'>";
	echo $res[$k]->RelatedItemID."</a>";
	echo "]";
	echo "	</td>";
	echo "	<td>&nbsp;".str_replace("\r", "<br>", htmlentities($res[$k]->ActionDesc, ENT_QUOTES, 'UTF-8'))."</td>";
	echo "</tr>";
}
?>
<tr bgcolor="#cccccc"><td colspan="8" align="right">
<?
for($k=0; $k<manage_ProjectTaskHistory::GetCount($_REQUEST["ProjectTaskID"])/$NumberOfRec; $k++)
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
<form target="_blank" method="post" action="NewProjectTaskHistory.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="ProjectTaskID" name="ProjectTaskID" value="<? echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
</html>
