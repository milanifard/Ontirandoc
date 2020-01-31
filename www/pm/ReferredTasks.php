<?php
//------------------------------
// Programmer:	Masoud Shariati
// Creation Date:	95-10
//------------------------------


//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
error_reporting(0);

include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/ProjectTasks.class.php");
include_once("classes/projects.class.php");
include_once("classes/ProjectTasksSecurity.class.php");
include_once("classes/ProjectTaskAssignedUsers.class.php");

$NumberOfRec = 30;
$k = 0;
$PageNumber = 0;

if (!isset($_SESSION["OrdType"]))
    $_SESSION["OrdType"] = "DESC";

if (isset($_REQUEST["PageNumber"])) {
    $PageNumber = (int)$_REQUEST["PageNumber"];
    $FromRec = $PageNumber * $NumberOfRec;
} else
    $FromRec = 0;

if (isset($_REQUEST["SearchAction"])) {
    $OrderByFieldName = "ProjectTaskID";
    if (isset($_REQUEST["OrderByFieldName"])) {
        $OrderByFieldName = $_REQUEST["OrderByFieldName"];
        if (isset($_REQUEST["OrderType"]) && in_array($_REQUEST["OrderType"], ["DESC", "ASC"]))
            $_SESSION["OrdType"] = $_REQUEST["OrderType"];
    }
    $ProjectID = htmlentities($_REQUEST["Item_ProjectID"], ENT_QUOTES, 'UTF-8');
} else {
    $OrderByFieldName = "ReferTime";
    $_SESSION["OrdType"] = "DESC";
    $ProjectID = '';
}
//$SSS = date("Y-m-d H:i:s");
$res = manage_ProjectTasks::GetReferredTasks($ProjectID, $FromRec, $NumberOfRec, $OrderByFieldName, $_SESSION["OrdType"]);
//if($_SESSION["UserID"]=="mshariati") echo $SSS . "<br />" . date("Y-m-d H:i:s") . "<br />";

HTMLBegin();

?>
    <form id="SearchForm" name="SearchForm" method=post>
        <input type="hidden" name="PageNumber" id="PageNumber" value="0">
        <input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
        <input type="hidden" name="OrderType" id="OrderType"
               value="<? echo ($_SESSION["OrdType"] == 'ASC') ? 'DESC' : 'ASC'; ?>">
        <input type="hidden" name="SearchAction" id="SearchAction" value="1">
        <br>
        <?php echo manage_ProjectTasks::CreateKartableHeader("ReferredTasks"); ?>
        <div class="row">
            <div class="col-1"></div>
            <div class="col-10">
                <table class="table table-sm table-borderless">
                    <tr id="SearchTr">
                        <td>
                            <table width="100%" align="center" border="0" cellspacing="0">
                                <tr>
                                    <td width="1%" nowrap>
                                       <? echo C_RELATED_PROJECT ?>
                                    </td>
                                    <td nowrap>
                                        <select name="Item_ProjectID" id="Item_ProjectID"
                                                onchange='javascript: document.SearchForm.submit();'>
                                            <option value=0>-
                                                <? echo manage_projects::GetUserProjectsOptions($_SESSION["PersonID"]); ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-1"></div>
        </div>
        <?
        if (isset($_REQUEST["SearchAction"])) {
            ?>
            <script>
                document.SearchForm.Item_ProjectID.value = '<? echo htmlentities($_REQUEST["Item_ProjectID"], ENT_QUOTES, 'UTF-8'); ?>';
            </script>
            <?
        }
        ?>
        <br>
        <div class="row">
            <div class="col-1"></div>
            <div class="col-10">
                <table class="table table-sm table-borderless">
<!--                    <tr class="FooterOfTable">-->
                        <!--
                        <td colspan="10" align="center">
                            <input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
                             <input type="button" onclick='javascript: NewRecordForm.submit();' value='ایجاد'>
                        </td>
                        </tr>
                        -->
                    <thead class="table-info">
                    <tr>
                        <!--<td width="1%">&nbsp;</td>-->
                        <td width="1%"><? echo C_ROW ?></td>
                        <td width="2%"><? echo C_EDIT ?></td>
                        <td width=1%>
                            <a href="javascript: Sort('ProjectID', <?php echo ($_SESSION["OrdType"] == "ASC") ? "'DESC'" : "'ASC'"; ?>);"><? echo C_RELATED_PROJECT?></a></td>
                        <td width=1% nowrap>
                            <a href="javascript: Sort('TaskPeriority', <?php echo ($_SESSION["OrdType"] == "ASC") ? "'DESC'" : "'ASC'"; ?>);"><? echo C_PRIORITY?></a>
                        </td>
                        <td>
                            <a href="javascript: Sort('title', <?php echo ($_SESSION["OrdType"] == "ASC") ? "'DESC'" : "'ASC'"; ?>);"><? echo C_TITLE ?></a>
                        </td>
                        <td nowrap width=1%>
                            <a href="javascript: Sort('CreateDate', <?php echo ($_SESSION["OrdType"] == "ASC") ? "'DESC'" : "'ASC'"; ?>);"><? echo C_CREATED_TIME ?></a></td>
                        <td nowrap width=1%>
                            <a href="javascript: Sort('persons5_FullName', <?php echo ($_SESSION["OrdType"] == "ASC") ? "'DESC'" : "'ASC'"; ?>);"><? echo C_CREATOR ?></a></td>
                        <td nowrap width=1%>
                            <a href="javascript: Sort('TaskStatus', <?php echo ($_SESSION["OrdType"] == "ASC") ? "'DESC'" : "'ASC'"; ?>);"><? echo C_STATUS ?></a>
                        </td>
                        <td nowrap width=1%>
                            <a href="javascript: Sort('ReferTime', <?php echo ($_SESSION["OrdType"] == "ASC") ? "'DESC'" : "'ASC'"; ?>);"><? echo C_LAST_REFERENCED_TIME?></a></td>
                        <td nowrap width=1%>
                            <? echo C_EXECUTOR?>
                        </td>
                    </tr>
                    </thead>
                    <?
                    for ($k = 0; $k < count($res); $k++) {
                        $Visited = manage_projectTasks::IsReferredTaskVisited($res[$k]->ProjectTaskID, $res[$k]->ToPersonWUID, $res[$k]->ReferTimeC);

                        if ($res[$k]->TaskStatus == "PROGRESSING")
                            echo "<tr bgcolor=#8BC7A1>";
                        //else if($res[$k]->TaskStatus=="DONE")
                        //echo "<tr bgcolor=#DADADD>";
                        else if ($k % 2 == 0)
                            echo "<tr class=\"OddRow\">";
                        else
                            echo "<tr class=\"EvenRow\">";
                        /*echo "<td>";
                        if($res[$k]->CanRemoveByCaller)
                            echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->ProjectTaskID."\">";
                        else
                            echo "&nbsp;";
                        echo "</td>";*/
                        echo "<td>" . ($k + $FromRec + 1) . "</td>";
                        echo "	<td>";
                        echo "<a href=\"javascript:void(0);\" onclick=\"javascript:window.open('NewProjectTasks.php?UpdateID=" . $res[$k]->ProjectTaskID . "');\">";
                        echo "<img src='images/edit.gif' title='ویرایش'>";
                        echo "</a></td>";
                        echo "	<td nowrap>&nbsp;" . $res[$k]->ProjectID_Desc . "</td>";
                        echo "	<td nowrap>&nbsp;" . $res[$k]->TaskPeriority . "</td>";

                        $Begin = "";
                        $End = "";
                        if ($Visited == 'NO' && $res[$k]->ToPersonWUID == $_SESSION["User"]->UserID) {
                            $Begin = "<b>";
                            $End = "</b>";
                        } else if ($Visited == 'YES' && $res[$k]->ToPersonWUID != $_SESSION["User"]->UserID) {
                            $Begin = "<span title='" . $res[$k]->ToPersonName . " این ارجاع را دید.' style='font-size: 1.7em; vertical-align: middle;'>👁</span> ";
                        }

                        echo "	<td>$Begin" . htmlentities($res[$k]->title, ENT_QUOTES, 'UTF-8') . "$End</td>";
                        echo "	<td nowrap>" . $res[$k]->CreateDate_Shamsi . "</td>";
                        echo "	<td nowrap>" . $res[$k]->CreatorID_FullName . "</td>";
                        $St = $res[$k]->TaskStatus_Desc;
                        echo "	<td nowrap>" . (($St == 'اقدام شده') ? "<b>" . $St . "</b>" : $St) . "</td>";
                        echo "	<td nowrap>" . $res[$k]->ReferTime . "</td>";
                        $executors = manage_ProjectTaskAssignedUsers::GetList($res[$k]->ProjectTaskID, "PersonID", "");
                        echo "	<td nowrap>";
                        for ($m = 0; $m < count($executors); $m++) {
                            if ($m > 0)
                                echo "<br>";
                            echo $executors[$m]->PersonID_FullName;
                        }
                        echo "&nbsp;</td>";

                        echo "</tr>";
                    }
                    ?>
                    <!--
                    <tr class="FooterOfTable">
                    <td colspan="10" align="center">
                        <input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
                         <input type="button" onclick='javascript: NewRecordForm.submit();' value='ایجاد'>
                    </td>
                    </tr>
                    -->
                    <tr bgcolor="#cccccc">
                        <td colspan="10" align="right">
                            <?php

                            $Count = manage_ProjectTasks::GetReferredTasksCount((int)$ProjectID) / $NumberOfRec;
                            //if($_SESSION["UserID"]=="mshariati") echo manage_ProjectTasks::GetReferredTasksCount((int) $ProjectID);
                            for ($k = 0; $k < $Count; $k++) {
                                if ($PageNumber != $k)
                                    echo "<a href='javascript: ShowPage(" . ($k) . ")'>";
                                echo($k + 1);
                                if ($PageNumber != $k)
                                    echo "</a>";
                                echo " ";
                            }

                            ?>

                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-1"></div>
        </div>
    </form>
    <form target="_blank" method="post" action="NewProjectTasks.php" id="NewRecordForm" name="NewRecordForm">
    </form>
    <script>
        function ConfirmDelete() {
            if (confirm(<? echo C_ARE_YOU_SURE ?>)) document.SearchForm.submit();
        }

        function ShowPage(PageNumber) {
            SearchForm.PageNumber.value = PageNumber;
            SearchForm.OrderType.value = OrderType;
            SearchForm.submit();
        }

        function Sort(OrderByFieldName, OrderType) {
            SearchForm.OrderByFieldName.value = OrderByFieldName;
            SearchForm.OrderType.value = OrderType;
            SearchForm.submit();
        }
    </script>
    </html>

<?php

// Set page's last visit time for the user ...
$A = pdodb::getInstance();
$query = "insert into UserPageLastVisits (UserID, PageID, LastVisit) values (?, 3, now()) on duplicate key update LastVisit = now();";
$A->Prepare($query);
$Rslt = $A->ExecuteStatement([$_SESSION["UserID"]]);

?>