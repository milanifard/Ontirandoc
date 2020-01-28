<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : تاریخچه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-24
*/
/*
 * Changed By Sara Bolouri Bazaz
 */
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/ProjectTaskHistory.class.php");
include_once("classes/projects.class.php");
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
    $OrderByFieldName = "ProjectHistoryID";
    $OrderType = "";
    if(isset($_REQUEST["OrderByFieldName"]))
    {
        $OrderByFieldName = $_REQUEST["OrderByFieldName"];
        $OrderType = $_REQUEST["OrderType"];
    }

    $Item_PersonID=htmlentities($_REQUEST["Item_PersonID"], ENT_QUOTES, 'UTF-8');
    $ChangedPart=htmlentities($_REQUEST["Item_ChangedPart"], ENT_QUOTES, 'UTF-8');
    $ActionType=htmlentities($_REQUEST["Item_ActionType"], ENT_QUOTES, 'UTF-8');

}
else
{
    $OrderByFieldName = "ProjectHistoryID";
    $OrderType = "";
    $Item_PersonID='';
    $ChangedPart='';
    $ActionType='';
}

if(!isset($_REQUEST["ProjectID"]))
{
    $_REQUEST["ProjectID"]  = '';
}
$res = manage_ProjectTaskHistory::SearchInAllTasksOfProject($_REQUEST["ProjectID"] , $Item_PersonID, $ChangedPart, $ActionType, "", $OrderByFieldName, $OrderType);
echo manage_projects::ShowSummary($_REQUEST["ProjectID"]);
echo manage_projects::ShowTabs($_REQUEST["ProjectID"], "ShowProjectActivities");
?>
<form method=post name="f1" id="f1">
    <input type="hidden" name="PageNumber" id="PageNumber" value="0">
    <input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
    <input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
    <input type="hidden" id="ProjectID" name="ProjectID" value="<? echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="SearchAction" id="SearchAction" value="1">
    <br>
    <div class="container">

        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#m1">
            <?php echo C_SEARCH ?>
        </button>

        <!-- The Modal -->
        <div class="modal fade" id="m1">
            <div class="modal-dialog">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title"> <?php echo C_SEARCH ?> </h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text border-dark bg-info text-white" for="inputGroupSelect01"><?php echo C_RELATED_SECTION ?></label>
                            </div>
                            <select class='custom-select'>
                                <option value=0>--
                                <option value='MAIN_PROJECT'><?php echo C_MAIN_SPECIFICATIONS ?></option>
                                <option value='MEMBER'><?php echo C_MEMBER ?></option>
                                <option value='MILESTONE'> <?php echo C_IMPORTANT_DATE ?></option>
                                <option value='DOCUMENT'><?php echo C_DOCUMENT ?> </option>
                                <option value='DOCUMENT_TYPE'><?php echo C_DOCUMENT_TYPE ?></option>
                                <option value='ACTIVITY_TYPE'><?php echo C_ACTION_TYPE2 ?></option>
                                <option value='TASK_TYPE'><?php echo C_TASK_TYPE ?></option>
                            </select>
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text border-dark bg-info text-white" for="inputGroupSelect01"><?php echo C_ACTION_TYPE1 ?></label>
                            </div>
                            <select class='custom-select'>
                                <option value=0>--
                                <option value='ADD'><?php echo C_ADD ?></option>
                                <option value='REMOVE'><?php echo C_REMOVE ?></option>
                                <option value='UPDATE'><?php echo C_UPDATE ?></option>
                                <option value='VIEW'><?php echo C_VIEW ?></option>
                            </select>
                        </div>
                        <div>
                            <a href="#" class="btn btn-info" role="button" onclick='javascript: window.open("SelectStaff.php?FormName=SearchForm&InputName=Item_PersonID&SpanName=Span_PersonID_FullName");'><?php echo C_CHOOSE_APPLIER ?></a>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-info"><?php echo C_SEARCH ?></button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo C_CLOSE ?></button>
                    </div>

                </div>
            </div>
        </div>

    </div>


</form>

<?
if(isset($_REQUEST["SearchAction"]))
{
    ?>
    <script>
        document.SearchForm.Item_PersonID.value='<? echo htmlentities($_REQUEST["Item_PersonID"], ENT_QUOTES, 'UTF-8'); ?>';
        document.SearchForm.Item_ChangedPart.value='<? echo htmlentities($_REQUEST["Item_ChangedPart"], ENT_QUOTES, 'UTF-8'); ?>';
        document.SearchForm.Item_ActionType.value='<? echo htmlentities($_REQUEST["Item_ActionType"], ENT_QUOTES, 'UTF-8'); ?>';
    </script>
    <?
}
?>
<br>
<form method="post">
    <input type="hidden" id="Item_ProjectID" name="Item_ProjectID" value="<? echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
    <? if(isset($_REQUEST["PageNumber"]))
        echo "<input type=\"hidden\" name=\"PageNumber\" value=".$_REQUEST["PageNumber"].">";
    ?>

    <div class="container">
        <table class="table table-sm">
            <thead class=" table-bordered font-weight-bold text-center">
            <td colspan="8" class="table-primary text-dark">
                <?php echo C_HISTORY ?>
            </td>
            <tr class="table-info">
                <td rowspan="2" class="p-4"><?php echo C_ROW ?></td>
                <td rowspan="2" class="p-4"><?php echo C_APPLIER ?></td>
                <td colspan="2" ><?php echo C_RELATED_ROLE ?></td>
                <td rowspan="2" class="p-4"><?php echo C_ACTION_TYPE1 ?></td>
                <td rowspan="2" class="p-4"><?php echo C_RELATED_SECTION ?></td>
                <td rowspan="2" class="p-4"><?php echo C_OPERATION_DESCRIPTION ?></td>
            </tr>
            <tr class="table-info">
                <td ><?php echo C_CODE ?></td>
                <td ><?php echo C_TITLE ?></td>
            </tr>
            </thead>
            <!--    body is ...   -->
            </tbody>
            <tfoot>
            <tr>
                <td colspan="8" align="right">
                    <?
                    for($k=0; $k<manage_ProjectTaskHistory::GetCountOfAllInProject($_REQUEST["ProjectID"])/$NumberOfRec; $k++)
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
            </tfoot>
            <?
            for($k=0; $k<count($res); $k++)
            {
                if($k%2==0)
                    echo "<tr class=\"OddRow\">";
                else
                    echo "<tr class=\"EvenRow\">";
                echo "<td>".($k+$FromRec+1)."</td>";
                echo "	<td nowrap>".$res[$k]->PersonID_FullName."</td>";
                echo "	<td><a href='NewProjectTasks.php?UpdateID=".$res[$k]->ProjectTaskID."'>".$res[$k]->ProjectTaskID."</a></td>";
                echo "	<td>".htmlentities($res[$k]->ProjectTaskTitle, ENT_QUOTES, 'UTF-8')."</td>";
                echo "	<td>".$res[$k]->ActionType_Desc."</td>";
                echo "	<td>".$res[$k]->ChangedPart_Desc;
                if($res[$k]->ChangedPart!="MAIN_TASK")
                {
                    echo " [کد: ";
                    if($res[$k]->ChangedPart=="COMMENT")
                        echo "<a href='ManageProjectTaskComments.php?ProjectTaskID=".$res[$k]->ProjectTaskID."&UpdateID=".$res[$k]->RelatedItemID."'>";
                    if($res[$k]->ChangedPart=="USER")
                        echo "<a href='ManageProjectTaskAssignedUsers.php?ProjectTaskID=".$res[$k]->ProjectTaskID."&UpdateID=".$res[$k]->RelatedItemID."'>";
                    if($res[$k]->ChangedPart=="DOCUMENT")
                        echo "<a href='ManageProjectTaskDocuments.php?ProjectTaskID=".$res[$k]->ProjectTaskID."&UpdateID=".$res[$k]->RelatedItemID."'>";
                    if($res[$k]->ChangedPart=="REQUISITE")
                        echo "<a href='ManageProjectTaskRequisites.php?ProjectTaskID=".$res[$k]->ProjectTaskID."&UpdateID=".$res[$k]->RelatedItemID."'>";
                    if($res[$k]->ChangedPart=="ACTIVITY")
                        echo "<a href='NewProjectTaskActivities.php?UpdateID=".$res[$k]->RelatedItemID."'>";
                    echo $res[$k]->RelatedItemID."]";
                }
                echo "</td>";
                echo "	<td>".htmlentities($res[$k]->ActionDesc, ENT_QUOTES, 'UTF-8')."</td>";
                echo "</tr>";
            }
            ?>

        </table>
    </div>


</form>
<form target="_blank" method="post" action="NewProjectHistory.php" id="NewRecordForm" name="NewRecordForm">
    <input type="hidden" id="ProjectID" name="ProjectID" value="<? echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
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
