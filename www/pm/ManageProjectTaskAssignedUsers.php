<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : کاربران منتسب به کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-17
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/ProjectTaskAssignedUsers.class.php");
include_once("classes/ProjectTasks.class.php");
include_once("classes/ProjectTasksSecurity.class.php");
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
if (isset($_REQUEST["UpdateID"])) {
    $obj = new be_ProjectTaskAssignedUsers();
    $obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
    $ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $obj->ProjectTaskID);
    $ProjectTaskID = $obj->ProjectTaskID;
} else {
    $ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["ProjectTaskID"]);
    $ProjectTaskID = $_REQUEST["ProjectTaskID"];
}
$ProjectTaskObj = new be_ProjectTasks();
$ProjectTaskObj->LoadDataFromDatabase($ProjectTaskID);
$ProjectID = $ProjectTaskObj->ProjectID;

$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
if ($ppc->GetPermission("Add_ProjectTaskAssignedUsers") == "YES")
    $HasAddAccess = true;
if (isset($_REQUEST["UpdateID"])) {
    if ($ppc->GetPermission("Update_ProjectTaskAssignedUsers") == "PUBLIC")
        $HasUpdateAccess = true;
    else if ($ppc->GetPermission("Update_ProjectTaskAssignedUsers") == "PRIVATE" && $_SESSION["User"]->PersonID == $obj->CreatorID)
        $HasUpdateAccess = true;
    if ($ppc->GetPermission("View_ProjectTaskAssignedUsers") == "PUBLIC")
        $HasViewAccess = true;
    else if ($ppc->GetPermission("View_ProjectTaskAssignedUsers") == "PRIVATE" && $_SESSION["User"]->PersonID == $obj->CreatorID)
        $HasViewAccess = true;
} else {
    $HasViewAccess = true;
}
if (!$HasViewAccess) {
    echo C_DONT_HAVE_PERMISSION;
    die();
}
if (isset($_REQUEST["Save"])) {
    if (isset($_REQUEST["ProjectTaskID"]))
        $Item_ProjectTaskID = $_REQUEST["ProjectTaskID"];
    if (isset($_REQUEST["Item_PersonID"]))
        $Item_PersonID = $_REQUEST["Item_PersonID"];
    if (isset($_REQUEST["Item_AssignDescription"]))
        $Item_AssignDescription = $_REQUEST["Item_AssignDescription"];
    if (isset($_REQUEST["Item_ParticipationPercent"]))
        $Item_ParticipationPercent = $_REQUEST["Item_ParticipationPercent"];
    if (isset($_REQUEST["Item_CreatorID"]))
        $Item_CreatorID = $_REQUEST["Item_CreatorID"];
    if (isset($_REQUEST["Item_AssignType"]))
        $Item_AssignType = $_REQUEST["Item_AssignType"];
    if (!isset($_REQUEST["UpdateID"])) {
        if ($HasAddAccess)
            manage_ProjectTaskAssignedUsers::Add($Item_ProjectTaskID
                , $Item_PersonID
                , $Item_AssignDescription
                , $Item_ParticipationPercent
                , $Item_AssignType

            );
    } else {
        if ($HasUpdateAccess)
            manage_ProjectTaskAssignedUsers::Update($_REQUEST["UpdateID"]
                , $Item_PersonID
                , $Item_AssignDescription
                , $Item_ParticipationPercent
                , $Item_AssignType
            );
    }
    if (isset($_REQUEST["SendLetter"])) {
        $mysql = pdodb::getInstance();
        $mysql->Prepare("select * from projectmanagement.AccountSpecs where PersonID=?");
        $res = $mysql->ExecuteStatement(array($Item_PersonID));
        $rec = $res->fetch();
        $ReceiverUserID = $rec["WebUserID"];

        $LetterMessage .= "با سلام <br>";
        $LetterMessage .= "کاری با شماره " . $ProjectTaskObj->ProjectTaskID . " با عنوان \"" . $ProjectTaskObj->title . "\" به شما انتساب یافته است.<br>";
        $LetterMessage .= "<p align=center><font color=green>(اين نامه به صورت اتوماتيك و توسط اتوماسيون اداري از طرف كاربر سيستم ارسال گرديده لذا خواهشمند است از پاسخ دادن و يا ارجاع آن به شخص ديگر خودداري فرمائيد.)</font>";
        SendLetterModule("انتساب کار با عنوان: " . $ProjectTaskObj->title, $LetterMessage, $ReceiverUserID, "");
    }
    echo SharedClass::CreateMessageBox(C_DATA_SAVE_SUCCESS);
}
$LoadDataJavascriptCode = '';
if (isset($_REQUEST["UpdateID"])) {
    $obj = new be_ProjectTaskAssignedUsers();
    $obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
    $LoadDataJavascriptCode .= "document.getElementById('Span_PersonID_FullName').innerHTML='" . $obj->PersonID_FullName . "'; \r\n ";
    if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
        $LoadDataJavascriptCode .= "document.getElementById('Item_PersonID').value='" . $obj->PersonID . "'; \r\n ";
    if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
        $LoadDataJavascriptCode .= "document.f1.Item_AssignDescription.value='" . htmlentities($obj->AssignDescription, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
    else
        $LoadDataJavascriptCode .= "document.getElementById('Item_AssignDescription').innerHTML='" . htmlentities($obj->AssignDescription, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
    if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
        $LoadDataJavascriptCode .= "document.f1.Item_ParticipationPercent.value='" . htmlentities($obj->ParticipationPercent, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
    else
        $LoadDataJavascriptCode .= "document.getElementById('Item_ParticipationPercent').innerHTML='" . htmlentities($obj->ParticipationPercent, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
    if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
        $LoadDataJavascriptCode .= "document.f1.Item_AssignType.value='" . htmlentities($obj->AssignType, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
    else
        $LoadDataJavascriptCode .= "document.getElementById('Item_AssignType').innerHTML='" . htmlentities($obj->AssignType_Desc, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
}
?>
<form method="post" id="f1" name="f1">
    <?
    if (isset($_REQUEST["UpdateID"])) {
        echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='" . $_REQUEST["UpdateID"] . "'>";
    }
    echo manage_ProjectTasks::ShowSummary($_REQUEST["ProjectTaskID"]);
    echo manage_ProjectTasks::ShowTabs($_REQUEST["ProjectTaskID"], "ManageProjectTaskAssignedUsers");
    ?>
    <br>
    <div class="row">
        <div class="col-1"></div>
        <div class="col-10">
            <table class="table table-sm table-borderless">
                <thead>
                <tr class="table-info">
                    <td align="center"><? echo C_CREATE_EDIT_USERS_ASSIGNED_TO_ACTIVITY ?></td>
                </tr>
                </thead>
                <tr>
                    <td>
                        <table >
                            <?
                            if (!isset($_REQUEST["UpdateID"])) {
                                ?>
                                <input type="hidden" name="ProjectTaskID" id="ProjectTaskID"
                                       value='<? if (isset($_REQUEST["ProjectTaskID"])) echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>'>
                            <? } ?>
                            <tr>
                                <td width="1%" nowrap>
                                    <font color=red>*</font> <? C_LAST_NAME_AND_FIRST_NAME ?>
                                </td>
                                <td nowrap>
                                    <? if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
                                        <input type=hidden name="Item_PersonID" id="Item_PersonID">
                                        <span id="Span_PersonID_FullName" name="Span_PersonID_FullName"></span>
                                        <a href='#'
                                           onclick='javascript: window.open("SelectStaff.php?InputName=Item_PersonID&SpanName=Span_PersonID_FullName<?php if ($ProjectID != "0") echo "&ProjectID=" . $ProjectID ?>");'>[انتخاب]</a>
                                    <? } else { ?>
                                        <span id="Span_PersonID_FullName" name="Span_PersonID_FullName"></span>    <? } ?>
                                </td>
                            </tr>
                            <tr>
                                <td width="1%" nowrap>
                                    <? echo C_ASSIGNEE_DESCRIPTION ?>
                                </td>
                                <td nowrap>
                                    <? if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
                                        <input type="text" name="Item_AssignDescription" id="Item_AssignDescription"
                                               maxlength="500" size="40">
                                    <? } else { ?>
                                        <span id="Item_AssignDescription" name="Item_AssignDescription"></span>
                                    <? } ?>
                                </td>
                            </tr>
                            <tr>
                                <td width="1%" nowrap>
                                    <? echo C_PARTICIPATION_PERCENTAGE ?>
                                </td>
                                <td nowrap>
                                    <? if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
                                        <input type="text" name="Item_ParticipationPercent" id="Item_ParticipationPercent"
                                               maxlength="3" size="3" value="100">%
                                    <? } else { ?>
                                        <span id="Item_ParticipationPercent" name="Item_ParticipationPercent"></span>
                                    <? } ?>
                                </td>
                            </tr>
                            <tr>
                                <td width="1%" nowrap>
                                    <? echo C_ROLE ?>
                                </td>
                                <td nowrap>
                                    <? if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
                                        <select name="Item_AssignType" id="Item_AssignType">
                                            <option value='EXECUTOR'><? echo C_ECECUTOR ?></option>
                                            <option value='VIEWER'><? echo C_VIEWER ?></option>
                                        </select>
                                    <? } else { ?>
                                        <span id="Item_AssignType" name="Item_AssignType"></span>    <? } ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan=2>
                                    <input type=checkbox name=SendLetter><? echo C_SEND_LETTER_FROM_ADVERTISER ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="FooterOfTable">
                    <td align="center">
                        <? if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || (!isset($_REQUEST["UpdateID"]) && $HasAddAccess)) {
                            ?>
                            <input type="button" onclick="javascript: ValidateForm();" value="<? echo C_SAVE?>">
                        <? } ?>
                        <? if ($HasAddAccess || $HasUpdateAccess) { ?>
                            <input type="button" class="btn btn-info"
                                   onclick="javascript: document.location='ManageProjectTaskAssignedUsers.php?ProjectTaskID=<?php echo $_REQUEST["ProjectTaskID"]; ?>'"
                                   value="<? echo C_SEND ?>">
                        <?php } ?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-1"></div>
    </div>
    <input type="hidden" name="Save" id="Save" value="1">
</form>
<script>
    <? echo $LoadDataJavascriptCode; ?>
    function ValidateForm() {
        if (document.getElementById('Item_PersonID')) {
            if (document.getElementById('Item_PersonID').value == '') {
                alert(<? C_DONT_HAVE_VALUE ?>);
                return;
            }
        }
        document.f1.submit();
    }
</script>
<?php
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["ProjectTaskID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
$HasRemoveAccess = true;
if ($ppc->GetPermission("Add_ProjectTaskAssignedUsers") == "YES")
    $HasAddAccess = true;
$RemoveType = $ppc->GetPermission("Remove_ProjectTaskAssignedUsers");
$UpdateType = $ppc->GetPermission("Update_ProjectTaskAssignedUsers");
$OrderByFieldName = "ProjectTaskAssignedUserID";
$OrderType = "";
if (isset($_REQUEST["OrderByFieldName"])) {
    $OrderByFieldName = $_REQUEST["OrderByFieldName"];
    $OrderType = $_REQUEST["OrderType"];
}
$res = manage_ProjectTaskAssignedUsers::GetList($_REQUEST["ProjectTaskID"], $OrderByFieldName, $OrderType);
$SomeItemsRemoved = false;
for ($k = 0; $k < count($res); $k++) {
    if (isset($_REQUEST["ch_" . $res[$k]->ProjectTaskAssignedUserID])) {
        if ($RemoveType == "PUBLIC" || ($RemoveType == "PRIVATE" && $res[$k]->CreatorID == $_SESSION["PersonID"])) {
            manage_ProjectTaskAssignedUsers::Remove($res[$k]->ProjectTaskAssignedUserID);
            $SomeItemsRemoved = true;
        }
    }
}
if ($SomeItemsRemoved)
    $res = manage_ProjectTaskAssignedUsers::GetList($_REQUEST["ProjectTaskID"], $OrderByFieldName, $OrderType);
?>
<form id="ListForm" name="ListForm" method="post">
    <input type="hidden" id="Item_ProjectTaskID" name="Item_ProjectTaskID"
           value="<? echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>">
    <br>
    <div class="row">
        <div class="col-1"></div>
        <div class="col-10">
            <table class="table table-bordered table-sm table-striped">
                <thead>
                <tr class="table-info">
                    <td colspan="7">
                        <? echo C_USERS_ASSIGNED_TO_ACTIVITY ?>
                    </td>
                </tr>
                </thead>
                <tbody>
                <tr class="HeaderOfTable">
                    <td width="1%"></td>
                    <td width="1%"><? echo C_ROW ?></td>
                    <td width="2%"><? echo C_EDIT ?></td>
                    <td width=20% nowrap><a href="javascript: Sort('persons2.plname, persons2.pfname', 'ASC');"><? echo C_LAST_NAME_AND_FIRST_NAME ?></a></td>
                    <td width=5% nowrap><a href="javascript: Sort('ParticipationPercent', 'ASC');"><? echo C_PARTICIPATION_PERCENTAGE ?></a></td>
                    <td width=1% nowrap><a href="javascript: Sort('AssignType', 'ASC');"><? echo C_ROLE ?></a></td>
                    <td><a href="javascript: Sort('AssignDescription', 'ASC');"><? echo C_ASSIGNEE_DESCRIPTION ?></a></td>
                </tr>
                <?
                for ($k = 0; $k < count($res); $k++) {
                    if ($k % 2 == 0)
                        echo "<tr class=\"OddRow\">";
                    else
                        echo "<tr class=\"EvenRow\">";
                    echo "<td>";
                    if ($RemoveType == "PUBLIC" || ($RemoveType == "PRIVATE" && $res[$k]->CreatorID == $_SESSION["PersonID"]))
                        echo "<input type=\"checkbox\" name=\"ch_" . $res[$k]->ProjectTaskAssignedUserID . "\">";
                    else
                        echo " ";
                    echo "</td>";
                    echo "<td>" . ($k + 1) . "</td>";
                    echo "	<td><a href=\"ManageProjectTaskAssignedUsers.php?UpdateID=" . $res[$k]->ProjectTaskAssignedUserID . "&ProjectTaskID=" . $_REQUEST["ProjectTaskID"] . "\"><img src='images/edit.gif' title='ویرایش'></a></td>";
                    echo "	<td nowrap>";
                    echo "<img width=80 src='ShowPersonPhoto.php?PersonID=" . $res[$k]->PersonID . "'> ";
                    echo $res[$k]->PersonID_FullName . "</td>";
                    echo "	<td>" . htmlentities($res[$k]->ParticipationPercent, ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "	<td>" . $res[$k]->AssignType_Desc . "</td>";
                    echo "	<td>&nbsp;" . htmlentities($res[$k]->AssignDescription, ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "</tr>";
                }
                ?>
                <tr class="FooterOfTable">
                    <td colspan="7" align="center">
                        <? if ($RemoveType != "NONE") { ?>
                            <input type="button" class="btn btn-info" onclick="javascript: ConfirmDelete();" value="<? echo C_SEND ?>>">
                        <? } ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-1"></div>
    </div>
</form>
<form target="_blank" method="post" action="NewProjectTaskAssignedUsers.php" id="NewRecordForm" name="NewRecordForm">
    <input type="hidden" id="ProjectTaskID" name="ProjectTaskID"
           value="<? echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<form method="post" name="f2" id="f2">
    <input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
    <input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
</form>
<script>
    function ConfirmDelete() {
        if (confirm(<? C_ARE_YOU_SURE ?>)) document.ListForm.submit();
    }

    function Sort(OrderByFieldName, OrderType) {
        f2.OrderByFieldName.value = OrderByFieldName;
        f2.OrderType.value = OrderType;
        f2.submit();
    }
</script>
</html>
