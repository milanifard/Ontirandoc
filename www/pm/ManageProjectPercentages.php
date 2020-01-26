<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : اعضای پروژه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/

//Adel Aboutalebi Pirnaeimi
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectMembers.class.php");
include("classes/projects.class.php");
include("classes/ProjectTasks.class.php");
include("classes/projectsSecurity.class.php");
HTMLBegin();
if (isset($_REQUEST["PersonID"])) {
	$SelectedPersonID = $_REQUEST["PersonID"];
} else
	die();
if (isset($_REQUEST["Save"]))
	echo SharedClass::CreateMessageBox(C_ARE_YOU_SURE);
?>
<br>
<?php echo manage_ProjectTasks::CreateKartableHeader("ShowAllPersonStatus"); ?>
<form method=post id=f1 name=f1>
	<input type=hidden name=PersonID id=PersonID value='<?php echo $SelectedPersonID ?>'>
	<input type=hidden name=Save id=Save value='1'>
	<br>


	<div class="container col-8" >


		<div class="card">
			<div class="card-header">
				<?php echo C_PROJECTS_ASSIGNED_TO; ?> <strong><?php echo SharedClass::GetPersonFullName($SelectedPersonID); ?></strong>
			</div>
			<div class="card-body">
				<table class="table table-bordered table-sm table-striped">
					<thead class="table-info">

						<tr>
							<th width="1%"><?php echo C_PROJECT_NAME; ?></th>
							<th width="1%"><?php echo C_PERCENTAGE_OF_TIME_ALLOCATED; ?></th>
						</tr>
					</thead>

					<tbody>

						<?php
						$mysql = pdodb::getInstance();
						$query = "select *  
					from projectmanagement.ProjectMembers 
					JOIN projectmanagement.projects using (ProjectID)
					where
					ProjectMembers.PersonID=?";
						$mysql->Prepare($query);
						$res = $mysql->ExecuteStatement(array($SelectedPersonID));
						$i = 0;
						while ($rec = $res->fetch()) {
							$i++;
							echo "<tr>";
							echo "<td nowrap>" . $rec["title"] . "</a></td>";
							if (manage_ProjectMembers::GetMemberShipType($_SESSION["PersonID"], $rec["ProjectID"]) == "MANAGER" || manage_UserProjectScopes::IsUserAccessToUnitProjects($_SESSION["PersonID"], $rec["ouid"])) {
								$Percent = $rec["ParticipationPercent"];
								if (isset($_REQUEST["Pr_" . $rec["ProjectID"]])) {
									if ($_REQUEST["Pr_" . $rec["ProjectID"]] != $rec["ParticipationPercent"]) {
										manage_ProjectMembers::Update($rec["ProjectmemberID"], $rec["PersonID"], $rec["AccessType"], $_REQUEST["Pr_" . $rec["ProjectID"]]);
										$Percent = $_REQUEST["Pr_" . $rec["ProjectID"]];
									}
								}
								echo "<td><input type=text name='Pr_" . $rec["ProjectID"] . "' value='" . $Percent . "' size=3 maxlength=3>%</td>";
							} else
								echo "<td>" . $rec["ParticipationPercent"] . "%</td>";
							echo "</tr>";
						}
						?>
						<tr>
							<td colspan=3 bgcolor=#bee5eb align=center>
								<input type="submit" class="btn btn-success" value='<?php echo C_SAVE; ?>'>
								&nbsp;
								<input type="button" class="btn btn-danger" onclick='javascript: document.location="ShowAllPersonStatus.php"' value='<?php echo C_RETURN; ?>'>
							</td>
						</tr>

					</tbody>


				</table>
			</div>
		</div>
	</div>
</form>

</html>