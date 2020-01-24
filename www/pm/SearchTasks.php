<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-18
*/

// This file taken by MGhayour
// local url: http://localhost:90/MyProject/Ontirandoc/www/pm/SearchTasks.php


include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectTasks.class.php");
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
	$OrderByFieldName = "ProjectTaskID";
	$OrderType = "";
	if(isset($_REQUEST["OrderByFieldName"]))
	{
		$OrderByFieldName = $_REQUEST["OrderByFieldName"];
		$OrderType = $_REQUEST["OrderType"];
	}
	$ProjectID=htmlentities($_REQUEST["Item_ProjectID"], ENT_QUOTES, 'UTF-8');
	$ProjectTaskTypeID=htmlentities($_REQUEST["Item_ProjectTaskTypeID"], ENT_QUOTES, 'UTF-8');
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
	$OrderByFieldName = "ProjectTaskID";
	$OrderType = "";
	$ProjectID = $_REQUEST["DefaultProjectID"];
	$ProjectTaskTypeID='';
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
if(isset($_REQUEST["SearchAction"])) 
	$res = manage_ProjectTasks::Search($ProjectID, $ProjectTaskTypeID, $title, $description, $CreatorID, $TaskPeriority, $TaskStatus, $ExecutorID, $TaskComment, $DocumentDescription, $ActivityDescription, "", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType); 
else
	$res = array();
?>




<!-- search form section -->
<form id="SearchForm" name="SearchForm" method=post>
<?php
	$FormName = "f1";
	if(isset($_REQUEST["FormName"]))
	{
		$FormName = $_REQUEST["FormName"];
		echo "<input type=hidden name=FormName id=FormName value='".$_REQUEST["FormName"]."'>"; 
	}	
?>
<input type=hidden name='InputName' value='<?= $_REQUEST["InputName"] ?>'>
<input type=hidden name='SpanName' value='<?= $_REQUEST["SpanName"] ?>'>
<input type=hidden name='DefaultProjectID' value='<?= $_REQUEST["DefaultProjectID"] ?>'>
<input type="hidden" name="PageNumber" id="PageNumber" value="0">
<input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
<input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
<input type="hidden" name="SearchAction" id="SearchAction" value="1"> 
<br>
<div id='SearchTr' class="container">

	<div class="row justify-content-center">
		<div class="card">
			<div class="card-header">
				<i class="fa fa-search"></i>
				<?php echo C_SEARCH ?>
			</div>
			<div class="card-body">
				<table class="table">
					<tr>
						<td nowrap>
							C_RELATED_PROJECT
						</td>
						<td nowrap>
						<select name="Item_ProjectID" id="Item_ProjectID">
						<option value=0>-
						<? echo SharedClass::CreateARelatedTableSelectOptions("projectmanagement.projects", "ProjectID", "title", "title"); ?>	</select>
						</td>
					</tr>

					<tr>
						<td nowrap>
							C_TASK_TYPE
						</td>
						<td nowrap>
						<select name="Item_ProjectTaskTypeID" id="Item_ProjectTaskTypeID">
						<option value=0>-
						<? echo SharedClass::CreateARelatedTableSelectOptions("projectmanagement.ProjectTaskTypes", "ProjectTaskTypeID", "title", "title"); ?>	</select>
						</td>
					</tr>

					<tr>
						<td nowrap>
							C_TITLE
						</td>
						<td nowrap>
						<input type="text" name="Item_title" id="Item_title" maxlength="1000" size="40">
						</td>
					</tr>

					<tr>
						<td nowrap>
							C_DESCRIPTION
						</td>
						<td nowrap>
						<input type=text name="Item_description" id="Item_description" mexlength="1000" size="40">
						</td>
					</tr>

					<tr>
						<td nowrap>
							C_NOTE
						</td>
						<td nowrap>
						<input type="text" name="Item_TaskComment" id="Item_TaskComment" maxlength="1000" size="40">
						</td>
					</tr>
					<tr>
						<td nowrap>
							C_DOCUMENT
						</td>
						<td nowrap>
						<input type="text" name="Item_DocumentDescription" id="Item_DocumentDescription" maxlength="1000" size="40">
						</td>
					</tr>
					<tr>
						<td nowrap>
							C_ACTION
						</td>
						<td nowrap>
						<input type="text" name="Item_ActivityDescription" id="Item_ActivityDescription" maxlength="1000" size="40">
						</td>
					</tr>


					<tr>
						<td nowrap>
							C_PRIORITY
						</td>
						<td nowrap>
						<select name="Item_TaskPeriority" id="Item_TaskPeriority" >
							<option value=0>-
							<option value='3'>C_PRIORITY_NORMAL</option>
							<option value='4'>C_PRIORITY_LOW</option>
							<option value='2'>C_PRIORITY_HIGH</option>
							<option value='1'>C_PRIORITY_CRITICAL</option>
						</select>
						</td>
					</tr>
					<tr>
						<td nowrap>
							C_STATUS
						</td>
						<td nowrap>
						<select name="Item_TaskStatus" id="Item_TaskStatus" >
							<option value=0>-
							<option value='NOT_START'>اقدام نشده</option>
							<option value='PROGRESSING'>در دست قدام</option>
							<option value='DONE'>اقدام شده</option>
							<option value='SUSPENDED'>معلق</option>
							<option value='REPLYED'>پاسخ داده شده</option>
						</select>
						</td>
					</tr>
					<tr>
						<td>
						ایجاد کننده: 
						</td>
						<td>
						<input type=hidden name="Item_CreatorID" id="Item_CreatorID" value=0>
						<span id="Span_PersonID_FullName" name="Span_PersonID_FullName"></span> 	
						<a href='#' onclick='javascript: window.open("SelectStaff.php?FormName=SearchForm&InputName=Item_CreatorID&SpanName=Span_PersonID_FullName");'>[انتخاب]</a>
						</td>
					</tr>
					<tr>
						<td>
						مجری/ناظر: 
						</td>
						<td>
						<input type=hidden name="Item_ExecutorID" id="Item_ExecutorID">
						<span id="Span_Executor_FullName" name="Span_Executor_FullName"></span> 	
						<a href='#' onclick='javascript: window.open("SelectStaff.php?FormName=SearchForm&InputName=Item_ExecutorID&SpanName=Span_Executor_FullName");'>[انتخاب]</a>
						</td>
					</tr>
				</table>
				<input class="btn btn-primary" type="submit" value="جستجو">
				<input class="btn btn-light" type=button value='حذف انتخاب قبلی' onclick='javascript: ClearLastSelected();'>
			</div>
		</div>
	</div>
</div>
</form>

<!-- result section -->
<? 
if(isset($_REQUEST["SearchAction"])) 
{ 
?>
<script>
		document.SearchForm.Item_ProjectID.value='<? echo htmlentities($_REQUEST["Item_ProjectID"], ENT_QUOTES, 'UTF-8'); ?>';
		document.SearchForm.Item_ProjectTaskTypeID.value='<? echo htmlentities($_REQUEST["Item_ProjectTaskTypeID"], ENT_QUOTES, 'UTF-8'); ?>';
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
<?
}
else
{
?>
<script>
		document.SearchForm.Item_ProjectID.value='<? echo htmlentities($_REQUEST["DefaultProjectID"], ENT_QUOTES, 'UTF-8'); ?>';
</script>
<?php 
}
?>

<div class="container">
<form id="ListForm" name="ListForm" method="post"> 
	<? if(isset($_REQUEST["PageNumber"]))
		echo "<input type=\"hidden\" name=\"PageNumber\" value=".$_REQUEST["PageNumber"].">"; ?>
	<br>

	<div class="row justify-content-center">
		<div class="card">
			<div class="card-header">
				<i class="fa fa-file-alt"></i>
				کار
			</div>
			<div class="card-body">
				<table class="table table-striped">
					<thead>
						<th>
							<td>ردیف</td>
							<td>کد</td>
							<td><a href="javascript: Sort('ProjectID', 'ASC');">پروژه مربوطه</a></td>
							<td><a href="javascript: Sort('ProjectTaskTypeID', 'ASC');">نوع کار</a></td>
							<td><a href="javascript: Sort('title', 'ASC');">عنوان</a></td>
							<td><a href="javascript: Sort('title', 'ASC');">وضعیت</a></td>
							<td><a href="javascript: Sort('CreatorID', 'ASC');">ایجاد کننده</a></td>
							<td nowrap width=1%>
							انتخاب
							</td>
						</th>
					</thead>
					<tbody>
					<?php
						for($k=0; $k<count($res); $k++)
						{
							echo "<tr>";
							echo "<td>".($k+$FromRec+1)."</td>";
							echo "<td>";
							echo $res[$k]->ProjectTaskID;
							echo "</td>";
							echo "	<td>".$res[$k]->ProjectID_Desc."</td>";
							echo "	<td>".$res[$k]->ProjectTaskTypeID_Desc."</td>";
							echo "	<td>".htmlentities($res[$k]->title, ENT_QUOTES, 'UTF-8')."</td>";
							echo "	<td>".$res[$k]->TaskStatus_Desc."</td>";
							echo "	<td>".$res[$k]->CreatorID_FullName."</td>";
							echo "<td nowrap>";
							$FilteredTaskTitle = str_replace("\"", " ", str_replace("'", " ", $res[$k]->title));
							echo "<a href=\"javascript: SelectTask(".$res[$k]->ProjectTaskID.", '".$res[$k]->ProjectTaskID.") ".$FilteredTaskTitle."')\">انتخاب</a>";
							echo "</td>";
							echo "</tr>";
						}
					?>
					</tbody>
				</table>
			</div>
			<div class="card-footer">
				<!-- page numbers -->
				<?
				if(isset($_REQUEST["SearchAction"]))
					for($k=0; $k<manage_ProjectTasks::GetTasksCountInSearchResult($ProjectID, $ProjectTaskTypeID, $title, $description, $CreatorID, $TaskPeriority, $TaskStatus, $ExecutorID, $TaskComment, $DocumentDescription, $ActivityDescription, "", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType)/$NumberOfRec; $k++)
					{
						if($PageNumber!=$k)
							echo "<a href='javascript: ShowPage(".($k).")'>";
						echo ($k+1);
						if($PageNumber!=$k)
							echo "</a>";
						echo " ";
					}
				?>
			</div>
		</div>
	</div>


</form>
</div>

<form target="_blank" method="post" action="NewProjectTasks.php" id="NewRecordForm" name="NewRecordForm">
</form>
<script>
function SelectTask(ProjectTaskID, TaskTitle)
{
	window.opener.document.<?php echo $FormName ?>.<?php echo $_REQUEST["InputName"] ?>.value=ProjectTaskID;
	window.opener.document.getElementById('<?php echo $_REQUEST["SpanName"] ?>').innerHTML=TaskTitle;
	window.close();
}

function ClearLastSelected()
{
	window.opener.document.<?php echo $FormName ?>.<?php echo $_REQUEST["InputName"] ?>.value='0';
	window.opener.document.getElementById('<?php echo $_REQUEST["SpanName"] ?>').innerHTML='';
	window.close();
}

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
<br>
<br>
</body>
</html>
