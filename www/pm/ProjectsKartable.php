<?php 
/*
 صفحه عملیاتی کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-18
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/projects.class.php");
include("classes/ProjectTasks.class.php");
HTMLBegin();
$res = manage_projects::GetUserProjects($_SESSION["PersonID"]); 
?>
<br>

<table width="98%" align="center" border="1" cellspacing="0">
<tr class="HeaderOfTable">
	<td width="1%">ردیف</td>
	<td width=1% nowrap>ویرایش</td>
	<td>عنوان</td>
	<td width=1% nowrap>گروه پروژه</td>
	<td width=1% nowrap>اولویت</td>
	<td width=1% nowrap>وضعیت</td>
	<td width=1% nowrap>گزارش</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>".($k+1)."</td>";
	echo "	<td nowrap>";
	echo "	<a target=\"_blank\" href='Newprojects.php?UpdateID=".$res[$k]->ProjectID ."'>";
	echo "	<img src='images/edit.gif' border='0' title='ویرایش'>";
	echo "	</a>  ";
	echo "	</td>";
	
	echo "	<td>".htmlentities($res[$k]->title, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td nowrap>&nbsp;".$res[$k]->ProjectGroupID_Desc."</td>";
	echo "	<td nowrap>".$res[$k]->ProjectPriority_Desc."</td>";
	echo "	<td nowrap>".$res[$k]->ProjectStatus_Desc."</td>";
	
	echo "	<td nowrap>";
	echo "	<a target=\"_blank\" href='ShowProjectOverview.php?ProjectID=".$res[$k]->ProjectID ."'>";
	echo "	<img src='images/report1.jpg' border='0' title='گزارش'>";
	echo "	</a>  ";
	echo "	</td>";
	
	/*
	echo "<td nowrap>";
	echo "<a target=\"_blank\" href='ManageProjectMembers.php?ProjectID=".$res[$k]->ProjectID ."'>";
	echo "<img src='images/members.gif' border='0' title='اعضای پروژه'>";
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
	echo "</tr>";
}
?>
</table>
</form>
</html>
