<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-18
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectTasks.class.php");				// Need to change bilingual data
include("classes/ProjectTasksSecurity.class.php");		// No need to change
include("classes/ProjectTaskAssignedUsers.class.php");	// Need to change bilingual data

$mysql = pdodb::getInstance();
$query = " select  person_type,PersonID 
			   from projectmanagement.persons where personid = ". $_SESSION['PersonID'];

$mysql->Prepare($query);
$pres = $mysql->ExecuteStatement(array(),1,true);

$prec=$pres->fetch();
HTMLBegin();

$OrderByFieldName = "TaskPeriority";
if (!isset($_SESSION["OrdType"]))
	$_SESSION["OrdType"] = "ASC";

$NumberOfRec = 30;
$k=0;
$PageNumber = 0;
if(isset($_REQUEST["PageNumber"]))
{
	$FromRec = $_REQUEST["PageNumber"]*$NumberOfRec;
	$PageNumber = (int) $_REQUEST["PageNumber"];
}
else
	$FromRec = 0;

if(isset($_REQUEST["SearchAction"])) 
{
	if(isset($_REQUEST["OrderByFieldName"]) && $_REQUEST["OrderByFieldName"] != '')
	{
		$OrderByFieldName = $_REQUEST["OrderByFieldName"];
		if (isset($_REQUEST["OrderType"]) && in_array($_REQUEST["OrderType"], ["DESC", "ASC"]))
			$_SESSION["OrdType"] = $_REQUEST["OrderType"];
	}
	$ProjectID=htmlentities($_REQUEST["Item_ProjectID"], ENT_QUOTES, 'UTF-8');
	$ProjectTaskTypeID=htmlentities($_REQUEST["Item_ProjectTaskTypeID"], ENT_QUOTES, 'UTF-8');
        $ProjectTaskID=htmlentities($_REQUEST["Item_ProjectTaskID"], ENT_QUOTES, 'UTF-8');
	$title=htmlentities($_REQUEST["Item_title"], ENT_QUOTES, 'UTF-8');
	$description=htmlentities($_REQUEST["Item_description"], ENT_QUOTES, 'UTF-8');
	$CreatorID=htmlentities($_REQUEST["Item_CreatorID"], ENT_QUOTES, 'UTF-8');
	$TaskPeriority=htmlentities($_REQUEST["Item_TaskPeriority"], ENT_QUOTES, 'UTF-8');
	$TaskStatus=htmlentities($_REQUEST["Item_TaskStatus"], ENT_QUOTES, 'UTF-8');
	$ExecutorID = htmlentities($_REQUEST["Item_ExecutorID"], ENT_QUOTES, 'UTF-8');
	$TaskComment = htmlentities($_REQUEST["Item_TaskComment"], ENT_QUOTES, 'UTF-8');
	$DocumentDescription = htmlentities($_REQUEST["Item_DocumentDescription"], ENT_QUOTES, 'UTF-8');
	$ActivityDescription = htmlentities($_REQUEST["Item_ActivityDescription"], ENT_QUOTES, 'UTF-8');
	$CreatorName = SharedClass::GetPersonFullName($CreatorID);
	$ExecutorName = SharedClass::GetPersonFullName($ExecutorID);
}
else
{
	$ProjectID='';
	$ProjectTaskTypeID='';
        $ProjectTaskID='';
	$title='';
	$description='';
	$CreatorID='';
	$TaskPeriority='';
	$TaskStatus='';
	$ExecutorID = 0;
	$TaskComment = "";
	$DocumentDescription = "";
	$ActivityDescription = "";
	$ExecutorName = "";
	$CreatorName = "";
}

if (isset($_POST['OrderByFieldName']) && $_POST['OrderByFieldName'] != '' && $_SESSION['OrdType'] == 'ASC')
	$NextOrderType = 'DESC';
else
	 $NextOrderType = 'ASC';

function PutHeader($Title, $OrderBy)
{
	global $NextOrderType;
	global $OrderByFieldName;
	$Output = $Title;

	if ($OrderBy != '')
	{
		$Output = "<a href=\"javascript: Sort('$OrderBy');\">$Title";
		if ($OrderBy == $OrderByFieldName)
		{
			if ($NextOrderType == 'DESC')
				$Output .= "<i class='fas fa-sort-amount-down' title='ترتیب فعلی بر اساس این فیلد نزولی است'></i> ";
			else
				$Output .= "<i class='fas fa-sort-amount-down-alt' title='ترتیب فعلی بر اساس این فیلد صعودی است'></i> ";
		}
		
		$Output .= "</a>";
	}
	
	return $Output;
}

if(isset($_REQUEST["SearchAction"]))
{
	$OtherConditions = "";
	
	if (isset($_POST["ConsiderDateRange"]) && $ToDate != '' && $FromDate != '')
		$OtherConditions .= " and date(CreateDate) between '$FromDate' and '$ToDate' ";
	
	if (isset($_POST["ConsiderActivityDateRange"]) && $ActivityToDate != '' && $ActivityFromDate != '')
		$OtherConditions .= " and exists(SELECT ProjectTaskID FROM projectmanagement.ProjectTaskActivities where ProjectTaskID = ProjectTasks.ProjectTaskID and date(ActivityDate) between '$ActivityFromDate' and '$ActivityToDate') ";
	
	$res = manage_ProjectTasks::Search($ProjectID, $ProjectTaskTypeID, $ProjectTaskID, $title, $description, $CreatorID, $TaskPeriority, $TaskStatus, $ExecutorID, $TaskComment, $DocumentDescription, $ActivityDescription, $OtherConditions, $FromRec, $NumberOfRec, $OrderByFieldName, $_SESSION["OrdType"]);
	$SomeItemsRemoved = false;
	for($k=0; $k<count($res); $k++)
	{
		if(isset($_REQUEST["ch_".$res[$k]->ProjectTaskID]) && $res[$k]->CanRemoveByCaller) 
		{
			manage_ProjectTasks::Remove($res[$k]->ProjectTaskID); 
			$SomeItemsRemoved = true;
		}
	}
	if($SomeItemsRemoved)
		$res = manage_ProjectTasks::Search($ProjectID, $ProjectTaskTypeID, $ProjectTaskID, $title, $description, $CreatorID, $TaskPeriority, $TaskStatus, $ExecutorID, $TaskComment, $DocumentDescription, $ActivityDescription, $OtherConditions,$FromRec, $NumberOfRec, $OrderByFieldName, $_SESSION["OrdType"]);
}

?>
<META http-equiv=Content-Type content="text/html; charset=UTF-8" >
<link rel="stylesheet" type="text/css" href="/sharedClasses/resources/css/ext-all.css" />
<!--style>
td{
	height : 26px;
	padding-right : 4px;
	vertical-align: middle;
}
div.pagination 
{
	padding: 3px;
	margin: 3px;
	text-align:center;		
}
div.pagination a
{
	padding: 2px 5px 2px 5px; 
	margin: 2px;
	border: 1px solid #000000;
	text-decoration: none; /* no underline */
	color: #000000;
}
div.pagination a:hover, div.meneame a:active 
{
	border: 1px solid #000;
	background-image:none;
	background-color:#0061de;
	color: #fff;
}
div.pagination span.current 
{
	margin-right:3px;
	padding:2px 6px;		
	font-weight: bold;
	color: #ff0084;
} 
div.pagination span.disabled
{
	margin-right:3px;
	padding:2px 6px;
	color: #adaaad;
}
</style!-->

<body dir='<?php echo (UI_LANGUAGE=="FA")?"rtl":"ltr" ?>'>
	<div class="row">
		<div class="col-1"></div>
		<div class="col-10">
			<form id="SearchForm" name="SearchForm" method="post"> 
				<input type="hidden" name="PageNumber" id="PageNumber" value="0">
				<input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<?= $OrderByFieldName ?>">
				<input type="hidden" name="OrderType" id="OrderType" value="<?= $NextOrderType ?>">
				<input type="hidden" name="SearchAction" id="SearchAction" value="1"> 
				<br>
				<table class="table table-sm table-borderless">
					<tr><td>
						<!-- // Sendig Some Data To User Side ... -->
						<select id="ProjectTaskTypeIDs" hidden>
							<?php echo manage_ProjectTasks::CreateARelatedTableSelectOptions("projectmanagement.ProjectTaskTypes", "ProjectID", "ProjectTaskTypeID", "ProjectID"); ?>
						</select>
						<!-- // ... -->
						<table>
							<tr>
								<td width="1%" nowrap><? echo C_RELATED_PROJECT ?></td>
								<td nowrap>
									<select class="form-control" name="Item_ProjectID" id="Item_ProjectID" onchange="SetProjectTypes(this.value);">
										<option value=0>-</option>
										<? echo manage_ProjectTasks::CreateARelatedTableSelectOptions("projectmanagement.projects", "ProjectID", "title", "title", "and DeleteFlag = 'NO'"); ?>	</select>
								</td>
							</tr>
								<input type="hidden" name="Item_ProjectTaskTypeID" id="Item_ProjectTaskTypeID" value="0">
							<tr>
								<td width="1%" nowrap>کد کار</td>
								<td nowrap>
								<input type="text" name="Item_ProjectTaskID" id="Item_ProjectTaskID" required>
								</td>
							</tr>
							<tr>
								<td width="1%" nowrap><? echo C_TITLE ?></td>
								<td nowrap>
								<input type="text" name="Item_title" id="Item_title" maxlength="1000" size="40" required>
								</td>
							</tr>
							<tr>
								<td width="1%" nowrap><? echo C_DESCRIPTION ?></td>
								<td nowrap>
								<input type="text" name="Item_description" id="Item_description" mexlength="1000" size="40" required>
								</td>
							</tr>
							<tr>
								<td width="1%" nowrap><? echo C_NOTE ?></td>
								<td nowrap>
								<input type="text" name="Item_TaskComment" id="Item_TaskComment" maxlength="1000" size="40" required>
								</td>
							</tr>
							<tr>
								<td width="1%" nowrap><? echo C_DOCUMENT ?></td>
								<td nowrap>
								<input type="text" name="Item_DocumentDescription" id="Item_DocumentDescription" maxlength="1000" size="40" required>
								</td>
							</tr>
							<tr>
								<td width="1%" nowrap><? echo C_ACTION ?></td>
								<td nowrap>
								<input type="text" name="Item_ActivityDescription" id="Item_ActivityDescription" maxlength="1000" size="40" required>
								</td>
							</tr>
							<tr>
								<td width="1%" nowrap><? echo C_PRIORITY ?></td>
								<td nowrap>
									<select class="form-control" name="Item_TaskPeriority" id="Item_TaskPeriority" >
										<option value='0'>-</option>
										<?php
											for ($i = 1; $i <= 30; $i++)
												echo "<option value='$i'>" . str_pad($i, 2, '0', STR_PAD_LEFT) . "</option>";
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td width="1%" nowrap><? echo C_STATUS ?></td>
								<td nowrap>
									<select class="form-control" name="Item_TaskStatus" id="Item_TaskStatus" >
										<option value=0>-</option>
										<option value='NOT_START'><? echo C_STATUS_NOT_START ?></option>
										<option value='PROGRESSING'><? echo C_STATUS_PROGRESSING ?></option>
										<option value='DONE'><? echo C_STATUS_DONE ?></option>
										<option value='SUSPENDED'><? echo C_STATUS_SUSPENDED ?></option>
										<option value='REPLYED'><? echo C_STATUS_REPLYED ?></option>
										<option value='READY_FOR_TEST'>آماده برای کنترل</option>
									</select>
								</td>
							</tr>
							<tr>
								<td><? echo CREATOR_M ?>: </td>
								<td>
									<input type="hidden" name="Item_CreatorID" id="Item_CreatorID" value=0>
									<span id="Span_PersonID_FullName" name="Span_PersonID_FullName"></span> 	
									<a href='#' onclick='javascript: window.open("SelectStaff.php?FormName=SearchForm&InputName=Item_CreatorID&SpanName=Span_PersonID_FullName");'>[<? echo C_SELECT ?>]</a>
								</td>
							</tr>
							<tr>
								<td><? echo C_ECECUTOR."/".C_VIEWER ?>: </td>
								<td>
									<input type="hidden" name="Item_ExecutorID" id="Item_ExecutorID">
									<span id="Span_Executor_FullName" name="Span_Executor_FullName"></span> 	
									<a href='#' onclick='javascript: window.open("SelectStaff.php?FormName=SearchForm&InputName=Item_ExecutorID&SpanName=Span_Executor_FullName");'>[<? echo C_SELECT ?>]</a>
								</td>
							</tr>
							<tr>
								<td title="در نظر گرفتن بازه‌ی زمانی ایجاد کار">
									<input type="checkbox" name="ConsiderDateRange" <?= (isset($_POST["ConsiderDateRange"]) || !isset($_REQUEST["SearchAction"]))?  "checked" : ""; ?>>بازه ایجاد:
								</td>
								<td>
									<span>از</span>
									<input type="number" name="FromDay" min="1" max="31" style="width: 45px;" value="<?= explode("/", $ShamsiFromDate)[2] ?>" required> /
									<input type="number" name="FromMonth" min="1" max="12" style="width: 45px;" value="<?= explode("/", $ShamsiFromDate)[1] ?>" required> /
									<input type="number" name="FromYear" min="1370" max="1450" style="width: 60px;" value="<?= explode("/", $ShamsiFromDate)[0] ?>" required>
									<span>تا</span>
									<input type="number" name="ToDay" min="1" max="31" style="width: 45px;" value="<?= explode("/", $ShamsiToDate)[2] ?>" required> /
									<input type="number" name="ToMonth" min="1" max="12" style="width: 45px;" value="<?= explode("/", $ShamsiToDate)[1] ?>" required> /
									<input type="number" name="ToYear" min="1370" max="1450" style="width: 60px;" value="<?= explode("/", $ShamsiToDate)[0] ?>" required>
								</td>
							</tr>
							<tr>
								<td title="در نظر گرفتن بازه‌ی زمانی ایجاد اقدام">
									<input type="checkbox" name="ConsiderActivityDateRange" <?= isset($_POST["ConsiderActivityDateRange"])?  "checked" : ""; ?>>بازه اقدام:
								</td>
								<td>
									<span>از</span>
									<input type="number" name="ActivityFromDay" min="1" max="31" style="width: 45px;" value="<?= explode("/", $ActivityShamsiFromDate)[2] ?>" required> /
									<input type="number" name="ActivityFromMonth" min="1" max="12" style="width: 45px;" value="<?= explode("/", $ActivityShamsiFromDate)[1] ?>" required> /
									<input type="number" name="ActivityFromYear" min="1370" max="1450" style="width: 60px;" value="<?= explode("/", $ActivityShamsiFromDate)[0] ?>" required>
									<span>تا</span>
									<input type="number" name="ActivityToDay" min="1" max="31" style="width: 45px;" value="<?= explode("/", $ActivityShamsiToDate)[2] ?>" required> /
									<input type="number" name="ActivityToMonth" min="1" max="12" style="width: 45px;" value="<?= explode("/", $ActivityShamsiToDate)[1] ?>" required> /
									<input type="number" name="ActivityToYear" min="1370" max="1450" style="width: 60px;" value="<?= explode("/", $ActivityShamsiToDate)[0] ?>" required>
								</td>
							</tr>
							<tfoot>
								<tr class="table-info">
									<td colspan="2" align="center">
										<input type="submit" class="btn btn-info" value="<? echo C_SEARCH ?>" onclick="SearchForm.OrderType.value = (document.getElementById('OrderType').value == 'DESC')? 'ASC' : 'DESC';">
									</td>
								</tr>
							</tfoot>
						</table>
					</td></tr>
				</table>
				<? 
				if(isset($_REQUEST["SearchAction"])) 
				{ 
				?>
				<script>
					document.SearchForm.Item_ProjectID.value='<? echo htmlentities($_REQUEST["Item_ProjectID"], ENT_QUOTES, 'UTF-8'); ?>';
					document.SearchForm.Item_ProjectTaskTypeID.value='<? echo htmlentities($_REQUEST["Item_ProjectTaskTypeID"], ENT_QUOTES, 'UTF-8'); ?>';
					document.SearchForm.Item_ProjectTaskID.value='<? echo htmlentities($_REQUEST["Item_ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>';
					document.SearchForm.Item_title.value='<? echo htmlentities($_REQUEST["Item_title"], ENT_QUOTES, 'UTF-8'); ?>';
					document.SearchForm.Item_description.value='<? echo htmlentities($_REQUEST["Item_description"], ENT_QUOTES, 'UTF-8'); ?>';
					document.SearchForm.Item_CreatorID.value='<? echo htmlentities($_REQUEST["Item_CreatorID"], ENT_QUOTES, 'UTF-8'); ?>';
					document.SearchForm.Item_TaskPeriority.value='<? echo htmlentities($_REQUEST["Item_TaskPeriority"], ENT_QUOTES, 'UTF-8'); ?>';
					document.SearchForm.Item_TaskStatus.value='<? echo htmlentities($_REQUEST["Item_TaskStatus"], ENT_QUOTES, 'UTF-8'); ?>';
					document.SearchForm.Item_ExecutorID.value='<? echo htmlentities($_REQUEST["Item_ExecutorID"], ENT_QUOTES, 'UTF-8'); ?>';
					document.SearchForm.Item_TaskComment.value='<? echo htmlentities($_REQUEST["Item_TaskComment"], ENT_QUOTES, 'UTF-8'); ?>';
					document.SearchForm.Item_DocumentDescription.value='<? echo htmlentities($_REQUEST["Item_DocumentDescription"], ENT_QUOTES, 'UTF-8'); ?>';
					document.SearchForm.Item_ActivityDescription.value='<? echo htmlentities($_REQUEST["Item_ActivityDescription"], ENT_QUOTES, 'UTF-8'); ?>';
					document.getElementById('Span_PersonID_FullName').innerHTML='<?php echo $CreatorName ?>';
					document.getElementById('Span_Executor_FullName').innerHTML='<?php echo $ExecutorName ?>';
				</script>
				<br>
				<table class="table table-bordered table-sm table-striped">
					<tr><td colspan="13">
						نتایج جستجو
					</td></tr>
					<thead class="table-info">
						<tr>
							<td width="1%" nowrap><?= PutHeader(' ', '') ?></td>
							<td width="1%" nowrap><?= PutHeader(C_ROW, '') ?></td>
							<td width="2%" nowrap><?= PutHeader(C_EDIT, '') ?></td>
							<td width=8% nowrap><?= PutHeader('پروژه مربوطه', 'trim(p1_title)') ?></td>
							<td width=1% nowrap><?= PutHeader('اولویت', 'TaskPeriority') ?></td>
							<td nowrap><?= PutHeader(C_TITLE, 'trim(ProjectTasks.title)') ?></td>
							<td width=4% nowrap><?= PutHeader('وضعیت', 'TaskStatus') ?></td>
							<td width=6% nowrap><?= PutHeader('ایجاد کننده', 'persons5_FullName') ?></td>
							<td width=5% nowrap><?= PutHeader('زمان ایجاد', 'ProjectTasks.CreateDate') ?></td>
							<td width=5% nowrap><?= PutHeader('زمان آخرین اقدام', 'LastActivityDate') ?></td>
							<td width=1% nowrap>مجریان</a></td>
							<td width=1% nowrap><?= PutHeader('زمان انجام', 'DoneDate') ?></td>
							<td width=1% nowrap><?= PutHeader('زمان مصرفی', 'ActivityLength') ?></td>
						</tr>
					</thead>
					<?

					for($k=0; $k<count($res); $k++)
					{
						echo "<tr><td>";
						if($res[$k]->CanRemoveByCaller)
							echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->ProjectTaskID."\">";
						else
							echo " ";
						echo "</td>";
						echo "<td>".($k+$FromRec+1)."</td>";
						echo "	<td>";
						echo "<a target=\"_blank\" href=\"NewProjectTasks.php?UpdateID=".$res[$k]->ProjectTaskID."\">";
						echo "<i class='fas fa-edit' title='".C_EDIT."'></i>";
						echo "</a></td>";
						echo "	<td >".$res[$k]->ProjectID_Desc."</td>";
						echo "	<td nowrap>&nbsp;".$res[$k]->TaskPeriority."</td>";
						//echo "	<td nowrap>".$res[$k]->ProjectTaskTypeID_Desc."</td>";
						echo "	<td>".htmlentities($res[$k]->title, ENT_QUOTES, 'UTF-8')."</td>";
						echo "	<td>".htmlentities($res[$k]->TaskStatus_Desc, ENT_QUOTES, 'UTF-8')."</td>";
						echo "	<td>".$res[$k]->CreatorID_FullName."</td>";
						echo "	<td nowrap>".$res[$k]->CreateDate_Shamsi."</td>";
						echo "	<td nowrap>".$res[$k]->LastActivityDate."</td>";
						$executors = manage_ProjectTaskAssignedUsers::GetList($res[$k]->ProjectTaskID, "PersonID", "");
						echo "	<td nowrap>";
						for($m=0; $m<count($executors); $m++)
						{
							if($m>0)
								echo "<br>";
							echo $executors[$m]->PersonID_FullName;
						}
						echo "</td>";
						/*
						echo "<td nowrap>";
						echo "<a target=\"_blank\" href='ManageProjectTaskAssignedUsers.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
						echo "<img src='images/members.gif' border='0' title='کاربران منتسب به کار'>";
						echo "</a>  ";
						echo "<a target=\"_blank\" href='ManageProjectTaskActivities.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
						echo "<img src='images/activity.gif' border='0' title='اقدامات'>";
						echo "</a>  ";
						echo "<a target=\"_blank\" href='ManageProjectTaskComments.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
						echo "<img src='images/comment.gif' border='0' title='یادداشتها'>";
						echo "</a>  ";
						echo "<a target=\"_blank\" href='ManageProjectTaskDocuments.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
						echo "<img src='images/document.gif' border='0' title='اسناد کارها'>";
						echo "</a>  ";
						echo "<a target=\"_blank\" href='ManageProjectTaskRequisites.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
						echo "<img src='images/chain.gif' border='0' title='پیشنیازها'>";
						echo "</a>  ";
						echo "<a target=\"_blank\" href='ManageProjectTaskHistory.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
						echo "<img src='images/history.gif' border='0' title='تاریخچه'>";
						echo "</a>  ";
						echo "</td>";
						*/
						if($res[$k]->DoneDate!="0000-00-00 00:00:00")
							echo "	<td nowrap>".$res[$k]->DoneDate_Shamsi."</td>";
						else
							echo "	<td nowrap>-</td>";
						echo "	<td nowrap>".(floor($res[$k]->ActivityLength/60)).":".(($res[$k]->ActivityLength%60))."</td>";
						echo "</tr>";
					}
					?>
					<tfoot>
						<tr class="table-info">
							<td colspan="13" align="center">
								<input type="button" class="btn btn-danger" onclick="javascript: ConfirmDelete();" value="<? echo C_REMOVE; ?>">
								<input type="button" class="btn btn-success" onclick='javascript: NewRecordForm.submit();' value='ایجاد'>
							</td>
						</tr>
					</tfoot>
					<tr><td colspan="13" align="right">
					<?
					for($k=0; $k<manage_ProjectTasks::SchResultCount($ProjectID, $ProjectTaskTypeID,$ProjectTaskID, $title, $description, $CreatorID, $TaskPeriority, $TaskStatus, $ExecutorID, $TaskComment, $DocumentDescription, $ActivityDescription, $OtherConditions)/$NumberOfRec; $k++)
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
		</div>
	</div>
</body>


<?
}
?>

<form target="_blank" method="post" action="NewProjectTasks.php" id="NewRecordForm" name="NewRecordForm">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('<? echo C_ARE_YOU_SURE ?>')) document.SearchForm.submit();
}
function ShowPage(PageNumber)
{
	SearchForm.OrderByFieldName.value = "<?= $OrderByFieldName ?>";
	SearchForm.OrderType.value = (document.getElementById("OrderType").value == "DESC")? "ASC" : "DESC";
	SearchForm.PageNumber.value=PageNumber; 
	SearchForm.submit();
}
function Sort(OrderByFieldName)
{
	SearchForm.OrderByFieldName.value = OrderByFieldName;
	SearchForm.OrderType.value = document.getElementById("OrderType").value;
	SearchForm.PageNumber.value=<?= $PageNumber ?>; 
	SearchForm.submit();
}

function SetProjectTypes(Project)
{
	Index = 0;
	ProjectTypes=[];
	for (var i = 1; i < document.getElementById("ProjectTaskTypeIDs").children.length; i++)
	{
		Option = document.getElementById("ProjectTaskTypeIDs").children[i];
		if (Option.value == Project) ProjectTypes[Index++] = Option.innerHTML;
	}
	
	for (var i = 1; i < document.getElementById("Item_ProjectTaskTypeID").children.length; i++)
	{
		Option = document.getElementById("Item_ProjectTaskTypeID").children[i];
		if (ProjectTypes.indexOf(Option.value) == -1)
			Option.hidden = true;
		else
			Option.hidden = false;
	}
	
	Sel = document.getElementById("Item_ProjectTaskTypeID").selectedIndex;
	if (ProjectTypes.indexOf(document.getElementById("Item_ProjectTaskTypeID").options[Sel].value) == -1)
		document.getElementById("Item_ProjectTaskTypeID").selectedIndex = 0;
}

Project = document.getElementById("Item_ProjectID").selectedIndex;
if (Project != -1) SetProjectTypes(document.getElementById("Item_ProjectID").options[Project].value);

</script>
</html>
