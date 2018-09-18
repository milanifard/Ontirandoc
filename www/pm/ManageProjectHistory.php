<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : تاریخچه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-24
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectHistory.class.php");
include ("classes/projects.class.php");
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
	$OrderByFieldName = "ProjectHistoryID";
	$OrderType = "";
	if(isset($_REQUEST["OrderByFieldName"]))
	{
		$OrderByFieldName = $_REQUEST["OrderByFieldName"];
		$OrderType = $_REQUEST["OrderType"];
	}
	$Item_PersonID=htmlentities($_REQUEST["Item_PersonID"], ENT_QUOTES, 'UTF-8');
	$ChangedPart=htmlentities($_REQUEST["Item_ChangedPart"], ENT_QUOTES, 'UTF-8');
	$ActionType=htmlentities($_REQUEST["Item_ActionType"], ENT_QUOTES, 'UTF-8');
} 
else
{ 
	$OrderByFieldName = "ProjectHistoryID";
	$OrderType = "DESC";
	$Item_PersonID='';
	$ChangedPart='';
	$ActionType='';
}
$res = manage_ProjectHistory::Search($_REQUEST["ProjectID"] , $Item_PersonID, $ChangedPart, $ActionType, "", $OrderByFieldName, $OrderType); 
echo manage_projects::ShowSummary($_REQUEST["ProjectID"]);
echo manage_projects::ShowTabs($_REQUEST["ProjectID"], "ManageProjectHistory");
?>
<form id="SearchForm" name="SearchForm" method=post> 
<input type="hidden" name="PageNumber" id="PageNumber" value="0">
<input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
<input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
	<input type="hidden" id="ProjectID" name="ProjectID" value="<? echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
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
		<option value='MAIN_PROJECT'>مشخصات اصلی</option>
		<option value='MEMBER'>عضو</option>
		<option value='MILESTONE'>تاریخ مهم</option>
		<option value='DOCUMENT'>سند</option>
		<option value='DOCUMENT_TYPE'>نوع سند</option>
		<option value='ACTIVITY_TYPE'>نوع اقدام</option>
		<option value='TASK_TYPE'>نوع کار</option>
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
	<input type="hidden" id="Item_ProjectID" name="Item_ProjectID" value="<? echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
<? if(isset($_REQUEST["PageNumber"]))
	echo "<input type=\"hidden\" name=\"PageNumber\" value=".$_REQUEST["PageNumber"].">"; ?>
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="8">
	تاریخچه
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
	echo "	<td nowrap>".$res[$k]->ActionTime_Shamsi."</td>";
	echo "	<td nowrap>".$res[$k]->PersonID_FullName."</td>";
	echo "	<td nowrap>".$res[$k]->ActionType_Desc."</td>";
	echo "	<td nowrap>".$res[$k]->ChangedPart_Desc." [".$res[$k]->RelatedItemID."]</td>";
	echo "	<td>&nbsp;".htmlentities($res[$k]->ActionDesc, ENT_QUOTES, 'UTF-8')."</td>";
	echo "</tr>";
}
?>
<tr bgcolor="#cccccc"><td colspan="8" align="right">
<?
for($k=0; $k<manage_ProjectHistory::GetCount($_REQUEST["ProjectID"])/$NumberOfRec; $k++)
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
<form target="_blank" method="post" action="NewProjectHistory.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="ProjectID" name="ProjectID" value="<? echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ShowPage(PageNumber)
{
	SearchForm.PageNumber.value=PageNumber; 
	SearchForm.submit();
}
function Sort(OrderByFieldName, OrderType)
{
	SearchForm.OrderByFieldName.value=OrderByFieldName; 
	SearchForm.OrderType.value=OrderType; 
	SearchForm.submit();
}
</script>
</html>
