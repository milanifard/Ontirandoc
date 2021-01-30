<?php
/*
 صفحه عملیاتی کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-18
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectTasks.class.php");
include("classes/projects.class.php");
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
    if(!isset($_REQUEST["Item_ProjectID"]))
    {
        $_REQUEST["Item_ProjectID"]  = '';
    }
    $ProjectID=htmlentities($_REQUEST["Item_ProjectID"], ENT_QUOTES, 'UTF-8');
}
else
{
    $OrderByFieldName = "DoneDate";
    $OrderType = "DESC";
    $ProjectID='';
}

$res = manage_ProjectTasks::GetTasksOfPerson($ProjectID, $_SESSION["PersonID"], "EXECUTOR", "DONE", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType);
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
    if(isset($_REQUEST["ch_".$res[$k]->ProjectTaskID])  && $res[$k]->CanRemoveByCaller)
    {
        manage_ProjectTasks::Remove($res[$k]->ProjectTaskID);
        $SomeItemsRemoved = true;
    }
}
if($SomeItemsRemoved)
    $res = manage_ProjectTasks::GetTasksOfPerson($ProjectID, $_SESSION["PersonID"], "EXECUTOR", "DONE", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType);
?>
<form id="SearchForm" name="SearchForm" method=post>
    <input type="hidden" name="PageNumber" id="PageNumber" value="0">
    <input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
    <input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
    <input type="hidden" name="SearchAction" id="SearchAction" value="1">
    <br>
    <?php echo manage_ProjectTasks::CreateKartableHeader("LastDoneTasks"); ?>
    <br>
    <div class="container border">
        <br>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <label class="input-group-text border-dark bg-dark text-white" for="inputGroupSelect01"><?php echo C_RELATED_PROJECT ?></label>
            </div>
            <select class='custom-select' onchange='javascript: document.SearchForm.submit();'>
                <option value=0>--
                    <?
                    echo SharedClass::CreateARelatedTableSelectOptions("projectmanagement.projects", "ProjectID", "title", "title");
                    ?>
            </select>
        </div>
        <div class="container text-center">
            <input type="button" class='btn btn-outline-success ' onclick='javascript: NewRecordForm.submit();' value="<?php echo C_CREATE ?>">
            <input type="button" class='btn btn-outline-danger  ' onclick="javascript: ConfirmDelete();" value="<?php echo C_REMOVE ?>">
        </div>
        <br>
    </div>
    <br>
    <?
    if(isset($_REQUEST["SearchAction"]))
    {
        ?>
        <script>
            document.SearchForm.Item_ProjectID.value='<? echo htmlentities($_REQUEST["Item_ProjectID"], ENT_QUOTES, 'UTF-8'); ?>';
        </script>
        <?
    }
    ?>
    <div class="container border">
        <br >
        <table class="table">

            <thead class=" font-weight-bolder text-center">
            <tr>
                <td class="table-info" ><?php echo C_ROW ?></td>
                <td class="table-info" ><?php echo C_EDIT ?></td>
                <td class="table-info" ><a href="javascript: Sort('ProjectID', 'ASC');"><?php echo C_RELATED_PROJECT ?></a></td>
                <td class="table-info" ><a href="javascript: Sort('TaskPeriority', 'ASC');"><?php echo C_PRIORITY ?></a></td>
                <td class="table-info"><a href="javascript: Sort('title', 'ASC');"><?php echo C_TITLE ?></a></td>
                <td class="table-info"><a href="javascript: Sort('CreatorID', 'ASC');"><?php echo CREATOR_M ?></a></td>
                <td class="table-info"><a href="javascript: Sort('LastActivityDate', 'ASC');"><?php echo C_DATE_OF_LAST_ACTION ?></a></td>
                <td class="table-info"><a href="javascript: Sort('DoneDate', 'ASC');"><?php echo C_TIME_TO_DO ?></a></td>
                <td class="table-info">
                    <?php echo C_OTHER_SPECIFICATIONS ?>
                </td>
            </tr>
            </thead>
            <tbody>
            <?
            for($k=0; $k<count($res); $k++)
            {
                if($res[$k]->TaskStatus=="PROGRESSING")
                    echo "<tr>";
                else if($k%2==0)
                    echo "<tr class=\"OddRow\">";
                else
                    echo "<tr class=\"EvenRow\">";
                echo "<td>";
                if($res[$k]->CanRemoveByCaller)
                    echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->ProjectTaskID."\">";
                else
                    echo "&nbsp;";
                echo "</td>";
                echo "<td>".($k+$FromRec+1)."</td>";
                echo "	<td>";
                echo "<a target=\"_blank\" href=\"NewProjectTasks.php?UpdateID=".$res[$k]->ProjectTaskID."\">";
                echo "<img src='images/edit.gif' title=".C_TITLE.">";
                echo "</a></td>";
                echo "	<td nowrap>&nbsp;".$res[$k]->ProjectID_Desc."</td>";
                echo "	<td nowrap>&nbsp;".$res[$k]->TaskPeriority."</td>";
                echo "	<td>".htmlentities($res[$k]->title, ENT_QUOTES, 'UTF-8')."</td>";
                echo "	<td nowrap>".$res[$k]->CreatorID_FullName."</td>";
                echo "	<td nowrap>".$res[$k]->LastActivityTime."</td>";
                echo "	<td nowrap>".$res[$k]->DoneDate_Shamsi."</td>";
                echo "<td nowrap>";
                echo "<a target=\"_blank\" href='ManageProjectTaskAssignedUsers.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
                echo "<img src='images/members.gif' border='0' title=".C_USERS_ASSIGNED_TO_WORK.">";
                echo "</a>  ";
                echo "<a target=\"_blank\" href='ManageProjectTaskActivities.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
                echo "<img src='images/activity.gif' border='0' title=".C_ACTIONS.">";
                echo "</a>  ";
                echo "<a target=\"_blank\" href='ManageProjectTaskComments.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
                echo "<img src='images/comment.gif' border='0' title=".C_NOTES.">";
                echo "</a>  ";
                echo "<a target=\"_blank\" href='ManageProjectTaskDocuments.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
                echo "<img src='images/document.gif' border='0' title=".C_DOCUMENTS.">";
                echo "</a>  ";
                echo "<a target=\"_blank\" href='ManageProjectTaskRequisites.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
                echo "<img src='images/chain.gif' border='0' title=".C_PREREQUISITES.">";
                echo "</a>  ";
                echo "<a target=\"_blank\" href='ManageProjectTaskHistory.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
                echo "<img src='images/history.gif' border='0' title=".C_HISTORY.">";
                echo "</a>  ";
                echo "</td>";
                echo "</tr>";
            }
            ?>
            <tr>
                <td colspan="10" align="center">
                    <input type="button" class='btn btn-outline-success ' onclick='javascript: NewRecordForm.submit();' value="<?php echo C_CREATE ?>">
                    <input type="button" class='btn btn-outline-danger  ' onclick="javascript: ConfirmDelete();" value="<?php echo C_REMOVE ?>">
                </td>
            </tr>
            <tr >
                <td colspan="10" align="right">
                    <?
                    for($k=0; $k<manage_ProjectTasks::GetTasksOfPersonCount($ProjectID, $_SESSION["PersonID"], "EXECUTOR", "DONE")/$NumberOfRec; $k++)
                    {
                        if($PageNumber!=$k)
                            echo "<a href='javascript: ShowPage(".($k).")'>";
                        echo ($k+1);
                        if($PageNumber!=$k)
                            echo "</a>";
                        echo " ";
                    }
                    ?>
                </td>
            </tr>
            </tbody>
        </table>
        <br>
    </div>
</form>
<form target="_blank" method="post" action="NewProjectTasks.php" id="NewRecordForm" name="NewRecordForm">
</form>
<script>
    function ConfirmDelete()
    {
        if(confirm(<?php echo C_ARE_YOU_SURE ?>)) document.SearchForm.submit();
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
</html>
