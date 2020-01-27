<?php
/*
 نمایش و مدیریت لیست درخواستهای ارسال شده توسط کاربر
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-31
*/
/*
 * Changed by Sara Bolouri Bazaz
 */
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectTasks.class.php");
include_once("classes/ProjectTasksSecurity.class.php");
HTMLBegin();
$NumberOfRec = 20;
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
    $OrderByFieldName = "CreateDate";
    $OrderType = "DESC";
    if(isset($_REQUEST["OrderByFieldName"]))
    {
        $OrderByFieldName = $_REQUEST["OrderByFieldName"];
        $OrderType = $_REQUEST["OrderType"];
    }
    $ProjectID=htmlentities($_REQUEST["Item_ProjectID"], ENT_QUOTES, 'UTF-8');
}
else
{
    $OrderByFieldName = "CreateDate";
    $OrderType = "DESC";
    $ProjectID='';
}

$res = manage_ProjectTasks::GetUserRequestedTasks($ProjectID, $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType);
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
    $res = manage_ProjectTasks::GetUserRequestedTasks($ProjectID, $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType);
?>
<form id="f1" name="f1" method=post>
    <input type="hidden" name="PageNumber" id="PageNumber" value="0">
    <input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
    <input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
    <input type="hidden" name="SearchAction" id="SearchAction" value="1">
    <br>

    <div class="container">
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
    </div>

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
    <div class="container border-dark">
        <table class="table">
            <thead>
            <tr class="bg-dark border-0 text-white font-weight-bold">
                <td colspan="9" class="text-center"><?php echo C_YOUR_REQUESTS_LIST ?></td>
            </tr>
            </thead>
            <tbody>
            <tr class="bg-light text-black-50 font-weight-bolder text-center">
                <td><?php echo C_ROW ?></td>
                <td><?php echo C_EDIT ?></td>
                <td><a href="javascript: Sort('ProjectID', 'ASC');"><?php echo C_RELATED_PROJECT ?></a></td>
                <td><a href="javascript: Sort('title', 'ASC');"><?php echo C_TITLE ?></a></td>
                <td><a href="javascript: Sort('TaskStatus', 'ASC');"><?php echo C_STATUS ?></a></td>
                <td><a href="javascript: Sort('CreatorDate', 'ASC');"><?php echo C_CREATE_TIME1 ?></a></td>
                <td><?php echo C_OTHER_SPECIFICATIONS ?></td>
            </tr>
            <?
            for($k=0; $k<count($res); $k++)
            {
                if($k%2==0)
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
                echo "<img src='images/edit.gif' title='ویرایش'>";
                echo "</a></td>";
                echo "	<td nowrap>&nbsp;".$res[$k]->ProjectID_Desc."</td>";
                echo "	<td>".htmlentities($res[$k]->title, ENT_QUOTES, 'UTF-8')."</td>";
                echo "	<td nowrap>&nbsp;".$res[$k]->TaskStatus_Desc."</td>";
                echo "	<td nowrap>".$res[$k]->CreateDate_Shamsi."</td>";
                echo "<td nowrap>";
                echo "<a target=\"_blank\" href='ManageProjectTaskAssignedUsers.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
                echo "<img src='images/members.gif' border='0' title=". C_USERS_ASSIGNED_TO_WORK ."";
                echo "</a>  ";
                echo "<a target=\"_blank\" href='ManageProjectTaskActivities.php?ProjectTaskID=".$res[$k]->ProjectTaskID ."'>";
                echo "<img src='images/activity.gif' border='0' title= ".C_ACTIONS .">";
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
                <td colspan="9" align="center">
                    <input type="button" class='btn border-dark text-dark' onclick="javascript: ConfirmDelete();" value="<?php echo C_REMOVE ?>">
                </td>
            </tr>
            <tr>
                <td colspan="9" align="right">
                    <?
                    for($k=0; $k<manage_ProjectTasks::GetUserRequestedTasksCount($ProjectID)/$NumberOfRec; $k++)
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
    </div>
</form>


<script>
    function ConfirmDelete()
    {
        if(confirm('<?php echo C_ARE_YOU_SURE ?>')) document.SearchForm.submit();
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
