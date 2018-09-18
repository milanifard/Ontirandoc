<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : پروژه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-15
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/projects.class.php");
include("classes/projectsSecurity.class.php");
include("classes/ProjectGroups.class.php");
include("classes/ProjectMembers.class.php");
include("classes/ProjectResponsibles.class.php");
$mysql = pdodb::getInstance();

HTMLBegin();
//echo "*".$_SESSION["PersonID"]."*";
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
$ProjectGroupOptions = "";
$res = manage_ProjectGroups::Search(0, "", "h1_ptitle, ProjectGroupName", "");
for($k=0; $k<count($res); $k++)
{
	$ProjectGroupOptions .= "<option value='".$res[$k]->ProjectGroupID."'>".$res[$k]->RelatedUnitID_Desc." - ".$res[$k]->ProjectGroupName;
}

if(isset($_REQUEST["SearchAction"])) 
{
	$OrderByFieldName = "ProjectID";
	$OrderType = "";
	if(isset($_REQUEST["OrderByFieldName"]))
	{
		$OrderByFieldName = $_REQUEST["OrderByFieldName"];
		$OrderType = $_REQUEST["OrderType"];
	}
	$title=htmlentities($_REQUEST["Item_title"], ENT_QUOTES, 'UTF-8');
	$SysCode=htmlentities($_REQUEST["Item_SysCode"], ENT_QUOTES, 'UTF-8');
	$ProjectStatus=htmlentities($_REQUEST["Item_ProjectStatus"], ENT_QUOTES, 'UTF-8');
	$ouid = '0';
	$ProjectGroupID = htmlentities($_REQUEST["Item_ProjectGroupID"], ENT_QUOTES, 'UTF-8');
	$MemberPersonID = htmlentities($_REQUEST["Item_PersonID"], ENT_QUOTES, 'UTF-8');
} 
else
{ 
	$OrderByFieldName = "ProjectID";
	$OrderType = "";
	$title='';
	$SysCode='';
	$ProjectStatus='';
	$ouid = '0';
	$ProjectGroupID = '0';
	$MemberPersonID = $_SESSION["PersonID"];
}
$res = manage_projects::Search($MemberPersonID, $ouid, $ProjectGroupID, $title, $SysCode, $ProjectStatus, "", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->ProjectID])) 
	{
		manage_projects::Remove($res[$k]->ProjectID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_projects::Search($MemberPersonID, $ouid, $ProjectGroupID, $title, $SysCode, $ProjectStatus, "", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType); 
?>
<form id="f1" name="f1" method=post> 
<input type="hidden" name="PageNumber" id="PageNumber" value="0">
<input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
<input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
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
 عنوان
	</td>
	<td nowrap>
	<input type="text" name="Item_title" id="Item_title" maxlength="500" size="40">
	</td>
</tr>

<tr>
	<td width="1%" nowrap>
 گروه پروژه
	</td>
	<td nowrap>
	<select name="Item_ProjectGroupID" id="Item_ProjectGroupID">
	<option value=0>-
	<?php echo $ProjectGroupOptions ?>
	</select>
	</td>
</tr>

<tr>
	<td width="1%" nowrap>
 سیستم مربوطه
	</td>
	<td nowrap>
	<select name="Item_SysCode" id="Item_SysCode">
	<option value=0>-
	<? echo SharedClass::CreateARelatedTableSelectOptions("projectmanagement.systems", "SysCode", "description", "description"); ?>	</select>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 وضعیت
	</td>
	<td nowrap>
	<select name="Item_ProjectStatus" id="Item_ProjectStatus" >
		<option value=0>-
		<option value='NOT_STARTED'>شروع نشده</option>
		<option value='DEVELOPING'>در دست اقدام</option>
		<option value='MAINTENANCE'>در حال پشتیبانی</option>
		<option value='FINISHED'>خاتمه یافته</option>
		<option value='SUSPENDED'>معلق</option>
	</select>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 عضو پروژه: 
	</td>
	<td>
	<input type=hidden name="Item_PersonID" id="Item_PersonID" value="<?php echo $_SESSION["PersonID"] ?>" >
	<span id="Span_PersonID_FullName" name="Span_PersonID_FullName">خودم</span>
	<a href='#' onclick='javascript: window.open("SelectStaff.php?InputName=Item_PersonID&SpanName=Span_PersonID_FullName&ProjectID=0");'>[انتخاب]</a>
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
		document.f1.Item_title.value='<? echo htmlentities($_REQUEST["Item_title"], ENT_QUOTES, 'UTF-8'); ?>';
		document.f1.Item_SysCode.value='<? echo htmlentities($_REQUEST["Item_SysCode"], ENT_QUOTES, 'UTF-8'); ?>';
		document.f1.Item_ProjectGroupID.value='<? echo htmlentities($_REQUEST["Item_ProjectGroupID"], ENT_QUOTES, 'UTF-8'); ?>';				
		document.f1.Item_ProjectStatus.value='<? echo htmlentities($_REQUEST["Item_ProjectStatus"], ENT_QUOTES, 'UTF-8'); ?>';
		document.f1.Item_PersonID.value='<? echo htmlentities($_REQUEST["Item_PersonID"], ENT_QUOTES, 'UTF-8'); ?>';
		<?php 
			$mysql = pdodb::getInstance();
			$mysql->Prepare("select * from hrmstotal.persons where PersonID=?");
			$res2 = $mysql->ExecuteStatement(array($_REQUEST["Item_PersonID"]));
			if($rec = $res2->fetch())
			{
				?>
		document.getElementById('Span_PersonID_FullName').innerHTML='<? echo $rec["pfname"]." ".$rec["plname"]; ?>';
				<?php 
			}
			else
			{
				?>
				document.getElementById('Span_PersonID_FullName').innerHTML='-';
				<?php 
			}	
		?>
</script>
<?
}
?> 
<form id="ListForm" name="ListForm" method="post"> 
<? if(isset($_REQUEST["PageNumber"]))
	echo "<input type=\"hidden\" name=\"PageNumber\" value=".$_REQUEST["PageNumber"].">"; ?>
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="9">
	پروژه
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td><a href="javascript: Sort('title', 'ASC');">عنوان</a></td>
	<td width=1% nowrap><a href="javascript: Sort('ProjectGroupID', 'ASC');">گروه پروژه</a></td>
	<td width=1% nowrap><a href="javascript: Sort('ProjectPriority', 'ASC');">اولویت</a></td>
	<td width=1% nowrap><a href="javascript: Sort('ProjectStatus', 'ASC');">وضعیت</a></td>
	<td width=1% nowrap>
	اعضا
	</td>
	<td width=1% nowrap>گزارش</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->ProjectID."\">";
	echo "</td>";
	echo "<td>".($k+$FromRec+1)."</td>";
	echo "	<td>";
	echo "<a target=\"_blank\" href=\"Newprojects.php?UpdateID=".$res[$k]->ProjectID."\">";
		echo "<img src='images/edit.gif' title='ویرایش'>";
	echo "</a></td>";
	echo "	<td>".htmlentities($res[$k]->title, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td nowrap>&nbsp;".$res[$k]->ProjectGroupID_Desc."</td>";
	echo "	<td nowrap>".$res[$k]->ProjectPriority_Desc."</td>";
	echo "	<td nowrap>".$res[$k]->ProjectStatus_Desc."</td>";
	/*
	echo "<td nowrap>";
	echo "<a target=\"_blank\" href='ManageProjectMembers.php?ProjectID=".$res[$k]->ProjectID ."'>";
	echo "<img src='images/members.gif' border='0' title='اعضای پروژه'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageProjectResponsibles.php?ProjectID=".$res[$k]->ProjectID ."'>";
	echo "<img src='images/operator.gif' border='0' title='پاسخگویان درخواستها'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageProjectMilestones.php?ProjectID=".$res[$k]->ProjectID ."'>";
	echo "<img src='images/calendar.gif' border='0' title='تاریخهای مهم'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageProjectDocuments.php?ProjectID=".$res[$k]->ProjectID ."'>";
	echo "<img src='images/document.gif' border='0' title='مستندات'>";
	echo "</a>  ";
	
	echo "<a target=\"_blank\" href='ManageProjectDocumentTypes.php?ProjectID=".$res[$k]->ProjectID ."'>";
	echo "<img src='images/category.gif' border='0' title='انواع سند پروژه ها'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageProjectTaskActivityTypes.php?ProjectID=".$res[$k]->ProjectID ."'>";
	echo "<img src='images/category2.gif' border='0' title='انواع اقدامات'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageProjectTaskTypes.php?ProjectID=".$res[$k]->ProjectID ."'>";
	echo "<img src='images/jobs.gif' border='0' title='انواع کارها'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ManageProjectHistory.php?ProjectID=".$res[$k]->ProjectID ."'>";
	echo "<img src='images/history.gif' border='0' title='تاریخچه'>";
	echo "</a>  ";
	echo "<a target=\"_blank\" href='ShowProjectActivities.php?ProjectID=".$res[$k]->ProjectID ."'>";
	echo "<img src='images/history.gif' border='0' title='فعالیتها'>";
	echo "</a>  ";
	echo "</td>";
	*/
	echo "<td nowrap>";
	$members = manage_ProjectMembers::GetList($res[$k]->ProjectID);
	for($i=0; $i<count($members); $i++)
		echo $members[$i]->AccessType_Desc.": ".$members[$i]->PersonID_FullName."<br>";
	$members = manage_ProjectResponsibles::GetList($res[$k]->ProjectID);
	for($i=0; $i<count($members); $i++)
		echo "<font color=green>پاسخگو</font>: ".$members[$i]->PersonID_FullName."<br>";
	echo "&nbsp;</td>";
	echo "	<td nowrap>";
	echo "	<a target=\"_blank\" href='ShowProjectOverview.php?ProjectID=".$res[$k]->ProjectID ."'>";
	echo "	<img src='images/report1.jpg' border='0' title='گزارش'>";
	echo "	</a>  ";
	echo "	</td>";
	
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="9" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
	 <input type="button" onclick='javascript: NewRecordForm.submit();' value='ایجاد'>
</td>
</tr>
<tr bgcolor="#cccccc"><td colspan="9" align="right">
<?
for($k=0; $k<manage_projects::GetProjectsInSearchResult($MemberPersonID, $ouid, $ProjectGroupID, $title, $SysCode, $ProjectStatus)/$NumberOfRec; $k++)
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
<form target="_blank" method="post" action="Newprojects.php" id="NewRecordForm" name="NewRecordForm">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
function ShowPage(PageNumber)
{
	f1.PageNumber.value=PageNumber; 
	f1.submit();
}
function Sort(OrderByFieldName, OrderType)
{
	f1.OrderByFieldName.value=OrderByFieldName; 
	f1.OrderType.value=OrderType; 
	f1.submit();
}
</script>
</html>
