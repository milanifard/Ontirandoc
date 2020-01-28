<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : اعضای پروژه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/ProjectMembers.class.php");
include_once("classes/projects.class.php");
include_once("classes/ProjectTasks.class.php");
include_once("classes/projectsSecurity.class.php");
HTMLBegin();

if(isset($_REQUEST["OrderBy"]))
	$OrderBy = $_REQUEST["OrderBy"];
else
	$OrderBy = "Name"; 
?>
<br>
<?php echo manage_ProjectTasks::CreateKartableHeader("ShowAllPersonStatus"); ?>
<br>
<div class="row">
<div class="col-2"></div>
<div class="col-8">
	<table class="table table-bordered table-sm table-striped">
	<thead class="table-info">
		<tr>
			<td class="text-nowrap"><a href='ShowAllPersonStatus.php?OrderBy=Name'><? echo C_LAST_NAME_AND_FIRST_NAME; ?></td>
			<td class="text-nowrap"><a href='ShowAllPersonStatus.php?OrderBy=Percent'><? echo C_TIME_PERCENTAGE_ALLOCATED; ?></td>
			<td class="text-nowrap"><a href='ShowAllPersonStatus.php?OrderBy=Projects'><? echo C_PROJECTS_COUNT; ?></td>
		</tr>
	</thead>
	<?php 
		$mysql = pdodb::getInstance();
		$query = "select persons.PersonID, pfname, plname, sum(ParticipationPercent) as TotalPercent, count(*) as TotalProjects 
					from projectmanagement.ProjectMembers 
					JOIN projectmanagement.projects using (ProjectID)
					JOIN projectmanagement.persons on (ProjectMembers.PersonID=persons.PersonID)  
					where projects.ouid in 
					(select PermittedUnitID from projectmanagement.UserProjectScopes where UserID=? )
					or ProjectID in (select ProjectID from projectmanagement.ProjectMembers where PersonID=? and AccessType='MANAGER') 
					group by persons.PersonID
				";
		if($OrderBy=="Percent")
			$query .= " order by TotalPercent";
		else if($OrderBy=="Name")
			$query .= " order by plname, pfname";
		else
			$query .= " order by TotalProjects";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($_SESSION["UserID"], $_SESSION["PersonID"]));
		$i = 0;
		while($rec= $res->fetch())
		{
			$i++;
			echo "<tr>";
			echo "<td class=\"text-nowrap\">".$rec["plname"]." ".$rec["pfname"]."</a></td>";
			echo "<td><a href='ManageProjectPercentages.php?PersonID=".$rec["PersonID"]."'>".$rec["TotalPercent"]."</td>";
			echo "<td>".$rec["TotalProjects"]."</td>";
			echo "</tr>";
		}
	?>
	<tr>
		<td colspan=3 class="bg-dark text-light">
		<? echo C_THIS_LIST_SHOWS_MEMBERS_OF_THE_PROJECTS_THAT_YOU_ARE_MANAGING_OR_SUBORDINATE_TO_THE_ORGANIZATIONAL_UNIT_UNDER_YOUR_MANAGEMENT; ?>
		<br>
		<? echo C_FOR_ADJUSTING_PERCENTAGES_YOU_CAN_CLICK_ON_PERCENTAGE_NUMBER_IN_EACH_ROW; ?>
		</td>
	</tr>
	</table>
</div>
<div class="col-2"></div>
</div>
	
</html>
