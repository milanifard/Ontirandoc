<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : اعضای پروژه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectMembers.class.php");
include ("classes/projects.class.php");
include ("classes/ProjectTasks.class.php");
include("classes/projectsSecurity.class.php");
HTMLBegin();

if(isset($_REQUEST["OrderBy"]))
	$OrderBy = $_REQUEST["OrderBy"];
else
	$OrderBy = "Name"; 
?>
<br>
<?php echo manage_ProjectTasks::CreateKartableHeader("ShowAllPersonStatus"); ?>
<br>
	<table border=1 cellspacing=0 cellpadding=4 align=center>
	<tr class=HeaderOfTable>
		<td nowrap><a href='ShowAllPersonStatus.php?OrderBy=Name'>نام خانوادگی و نام</td>
		<td nowrap><a href='ShowAllPersonStatus.php?OrderBy=Percent'>درصد تخصیصی زمان</td>
		<td nowrap><a href='ShowAllPersonStatus.php?OrderBy=Projects'>تعداد پروژه ها</td>
	</tr>
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
			if($i%2==0)
				echo "<tr class=OddRow>";
			else
				echo "<tr class=EvenRow>";
			echo "<td nowrap>".$rec["plname"]." ".$rec["pfname"]."</a></td>";
			echo "<td><a href='ManageProjectPercentages.php?PersonID=".$rec["PersonID"]."'>".$rec["TotalPercent"]."</td>";
			echo "<td>".$rec["TotalProjects"]."</td>";
			echo "</tr>";
		}
	?>
	<tr>
		<td colspan=3 bgcolor=#cccccc>
		در این لیست اعضای پروژه هایی که شما مدیر آنها هستید و یا در زیرمجموعه واحد سازمانی تحت مدیریت شماست نمایش داده میشوند
		<br>
		برای تنظیم درصدها میتوانید روی عدددرصد در هر ردیف کلیک نمایید
		</td>
	</tr>
	</table>
	
</html>
