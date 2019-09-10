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
if(isset($_REQUEST["PersonID"])) 
{
	$SelectedPersonID = $_REQUEST["PersonID"];
}
else
	die();
if(isset($_REQUEST["Save"]))
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
?>
<br>
<?php echo manage_ProjectTasks::CreateKartableHeader("ShowAllPersonStatus"); ?>
<form method=post id=f1 name=f1>
<input type=hidden name=PersonID id=PersonID value='<?php echo $SelectedPersonID ?>'> 
<input type=hidden name=Save id=Save value='1'>
<br>
	<table border=1 cellspacing=0 cellpadding=4 align=center>
	<tr>
		<td align=center colspan=2 bgcolor=#cccccc>
		پروژه های انتسابی به <?php echo SharedClass::GetPersonFullName($SelectedPersonID); ?>
		</td>
	</tr>
	<tr class=HeaderOfTable>
		<td nowrap>نام پروژه</td>
		<td nowrap>درصد تخصیصی زمان</td>
	</tr>
	<?php 
		$mysql = pdodb::getInstance();
		$query = "select *  
					from projectmanagement.ProjectMembers 
					JOIN projectmanagement.projects using (ProjectID)
					where
					ProjectMembers.PersonID=? 
				";
		$mysql->Prepare($query);
		$res = $mysql->ExecuteStatement(array($SelectedPersonID));
		$i = 0;
		while($rec= $res->fetch())
		{
			$i++;
			if($i%2==0)
				echo "<tr class=OddRow>";
			else
				echo "<tr class=EvenRow>";
			echo "<td nowrap>".$rec["title"]."</a></td>";
			if(manage_ProjectMembers::GetMemberShipType($_SESSION["PersonID"], $rec["ProjectID"])=="MANAGER" || manage_UserProjectScopes::IsUserAccessToUnitProjects($_SESSION["PersonID"], $rec["ouid"]))
			{
				$Percent = $rec["ParticipationPercent"];
				if(isset($_REQUEST["Pr_".$rec["ProjectID"]]))
				{
					if($_REQUEST["Pr_".$rec["ProjectID"]]!=$rec["ParticipationPercent"])
					{
						manage_ProjectMembers::Update($rec["ProjectmemberID"], $rec["PersonID"], $rec["AccessType"], $_REQUEST["Pr_".$rec["ProjectID"]]);
						$Percent = $_REQUEST["Pr_".$rec["ProjectID"]];
					}
				}
				echo "<td><input type=text name='Pr_".$rec["ProjectID"]."' value='".$Percent."' size=3 maxlength=3>%</td>";
			}
			else
				echo "<td>".$rec["ParticipationPercent"]."%</td>";
			echo "</tr>";
		}
	?>
	<tr>
		<td colspan=3 bgcolor=#cccccc align=center>
			<input type=submit value='ذخیره'>
			&nbsp;
			<input type=button value='بازگشت' onclick='javascript: document.location="ShowAllPersonStatus.php"'>
		</td>
	</tr>
	</table>
</form>
</html>
