<?php
/*
 صفحه عملیاتی کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-18
*/


//Adel Aboutalebi Pirnaeimi
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/projects.class.php");
include("classes/ProjectTasks.class.php");
HTMLBegin();
$res = manage_projects::GetUserProjects($_SESSION["PersonID"]);
?>
<br>

<div class="container">
	<table class="table table-bordered table-sm table-striped">
		<thead class="table-info">
			<tr>
				<th width="1%"><?php echo C_ROW; ?></th>
				<th width="1%"><?php echo C_EDIT; ?></th>
				<th><?php echo C_TITLE; ?></th>
				<th width="7%"><?php echo C_PROJECT_GROUP; ?></th>
				<th width="1%"><?php echo C_PRIORITY; ?></th>
				<th width="1%"><?php echo C_STATUS; ?></th>
				<th width="1%"><?php echo C_REPORT; ?></th>
			</tr>
		</thead>
		<tbody>
			<?
			for ($k = 0; $k < count($res); $k++) {
				echo "<tr>";
				echo "<td class='text-center align-middle'>" . ($k + 1) . "</td>";
				echo "	<td class='text-center align-middle'>";
				echo "	<a target=\"_blank\" href='Newprojects.php?UpdateID=" . $res[$k]->ProjectID . "'>";
				echo "	<i class='fas fa-edit'></i>";
				echo "	</a>  ";
				echo "	</td>";

				echo "	<td class='align-middle'>" . htmlentities($res[$k]->title, ENT_QUOTES, 'UTF-8') . "</td>";
				echo "	<td class='align-middle' nowrap>&nbsp;" . $res[$k]->ProjectGroupID_Desc . "</td>";
				echo "	<td class='align-middle' nowrap>" . $res[$k]->ProjectPriority_Desc . "</td>";
				echo "	<td class='align-middle' nowrap>" . $res[$k]->ProjectStatus_Desc . "</td>";

				echo "	<td class='text-center align-middle' nowrap>";
				echo "	<a target=\"_blank\" href='ShowProjectOverview.php?ProjectID=" . $res[$k]->ProjectID . "'>";
				echo "	<i class='fas fa-file align-middle'></i>";
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

		</tbody>
	</table>
</div>

</table>
</form>

</html>