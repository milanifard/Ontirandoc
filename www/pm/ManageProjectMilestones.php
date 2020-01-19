<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : تاریخهای مهم
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectMilestones.class.php");
include("classes/projects.class.php");
include("classes/projectsSecurity.class.php");
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
if (isset($_REQUEST["UpdateID"])) {
    $obj = new be_ProjectMilestones();
    $obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
    $ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $obj->ProjectID);
} else
    $ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["ProjectID"]);
//echo $ppc->Show();
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
if ($ppc->GetPermission("Add_ProjectMilestones") == "YES")
    $HasAddAccess = true;
if (isset($_REQUEST["UpdateID"])) {
    if ($ppc->GetPermission("Update_ProjectMilestones") == "PUBLIC")
        $HasUpdateAccess = true;
    else if ($ppc->GetPermission("Update_ProjectMilestones") == "PRIVATE" && $_SESSION["PersonID"] == $obj->CreatorID)
        $HasUpdateAccess = true;
    if ($ppc->GetPermission("View_ProjectMilestones") == "PUBLIC")
        $HasViewAccess = true;
    else if ($ppc->GetPermission("View_ProjectMilestones") == "PRIVATE" && $_SESSION["PersonID"] == $obj->CreatorID)
        $HasViewAccess = true;
} else {
    $HasViewAccess = true;
}
if (!$HasViewAccess) {
    echo "مجوز مشاهده این رکورد را ندارید";
    die();
}
if (isset($_REQUEST["Save"])) {
    if (isset($_REQUEST["ProjectID"]))
        $Item_ProjectID = $_REQUEST["ProjectID"];
    if (isset($_REQUEST["MilestoneDate_DAY"])) {
        $Item_MilestoneDate = SharedClass::ConvertToMiladi($_REQUEST["MilestoneDate_YEAR"], $_REQUEST["MilestoneDate_MONTH"], $_REQUEST["MilestoneDate_DAY"]);
    }
    if (isset($_REQUEST["Item_description"]))
        $Item_description = $_REQUEST["Item_description"];
    if (isset($_REQUEST["Item_CreatorID"]))
        $Item_CreatorID = $_REQUEST["Item_CreatorID"];
    if (!isset($_REQUEST["UpdateID"])) {
        if ($HasAddAccess)
            manage_ProjectMilestones::Add($Item_ProjectID
                , $Item_MilestoneDate
                , $Item_description
            );
    } else {
        if ($HasUpdateAccess)
            manage_ProjectMilestones::Update($_REQUEST["UpdateID"]
                , $Item_MilestoneDate
                , $Item_description
            );
    }
    echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if (isset($_REQUEST["UpdateID"])) {
    $obj = new be_ProjectMilestones();
    $obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
    if ($obj->MilestoneDate_Shamsi != "date-error") {
        if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) {
            $LoadDataJavascriptCode .= "document.f1.MilestoneDate_YEAR.value='" . substr($obj->MilestoneDate_Shamsi, 2, 2) . "'; \r\n ";
            $LoadDataJavascriptCode .= "document.f1.MilestoneDate_MONTH.value='" . substr($obj->MilestoneDate_Shamsi, 5, 2) . "'; \r\n ";
            $LoadDataJavascriptCode .= "document.f1.MilestoneDate_DAY.value='" . substr($obj->MilestoneDate_Shamsi, 8, 2) . "'; \r\n ";
        } else {
            $LoadDataJavascriptCode .= "document.getElementById('MilestoneDate_YEAR').innerHTML='" . substr($obj->MilestoneDate_Shamsi, 2, 2) . "'; \r\n ";
            $LoadDataJavascriptCode .= "document.getElementById('MilestoneDate_MONTH').innerHTML='" . substr($obj->MilestoneDate_Shamsi, 5, 2) . "'; \r\n ";
            $LoadDataJavascriptCode .= "document.getElementById('MilestoneDate_DAY').innerHTML='" . substr($obj->MilestoneDate_Shamsi, 8, 2) . "'; \r\n ";
        }
    }
    if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
        $LoadDataJavascriptCode .= "document.f1.Item_description.value='" . htmlentities($obj->description, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
    else
        $LoadDataJavascriptCode .= "document.getElementById('Item_description').innerHTML='" . htmlentities($obj->description, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
}
?>
<form method="post" id="f1" name="f1">
    <?
    if (isset($_REQUEST["UpdateID"])) {
        echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='" . $_REQUEST["UpdateID"] . "'>";
    }
    echo manage_projects::ShowSummary($_REQUEST["ProjectID"]);
    echo manage_projects::ShowTabs($_REQUEST["ProjectID"], "ManageProjectMilestones");
    ?>
    <br>
    <div class="row">
        <div class="col-1"></div>
        <div class="col-10">
            <table class="table table-sm table-borderless">
                <thead>
                <tr class="table-info">
                    <td class="text-center"><? echo C_CREATE_EDIT_IMPORTANT_DATE; ?></td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <table>
                            <?
                            if (!isset($_REQUEST["UpdateID"])) {
                                ?>
                                <input type="hidden" name="ProjectID" id="ProjectID"
                                       value='<? if (isset($_REQUEST["ProjectID"])) echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>'>
                            <? } ?>
                            <tr>
                                <td width="1%" nowrap>
                                    <? echo C_DATE; ?>
                                </td>
                                <td nowrap>
                                    <? if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
                                        <input maxlength="2" id="MilestoneDate_DAY" name="MilestoneDate_DAY" type="text"
                                               size="2">/
                                        <input maxlength="2" id="MilestoneDate_MONTH" name="MilestoneDate_MONTH"
                                               type="text"
                                               size="2">/
                                        <input maxlength="2" id="MilestoneDate_YEAR" name="MilestoneDate_YEAR"
                                               type="text"
                                               size="2">
                                    <? } else { ?>
                                        <span id="MilestoneDate_DAY" name="MilestoneDate_DAY"></span>/
                                        <span id="MilestoneDate_MONTH" name="MilestoneDate_MONTH"></span>/
                                        <span id="MilestoneDate_YEAR" name="MilestoneDate_YEAR"></span>
                                    <? } ?>
                                </td>
                            </tr>
                            <tr>
                                <td width="1%" nowrap>
                                    <? echo C_DESCRIPTION; ?>
                                </td>
                                <td nowrap>
                                    <? if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
                                        <input type="text" name="Item_description" id="Item_description" maxlength="500"
                                               size="40">
                                    <? } else { ?>
                                        <span id="Item_description" name="Item_description"></span>
                                    <? } ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </tbody>
                <tr class="FooterOfTable">
                    <td align="center">
                        <? if (($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || (!isset($_REQUEST["UpdateID"]) && $HasAddAccess)) {
                            ?>
                            <input type="button" onclick="javascript: ValidateForm();" value="<? echo C_SAVE; ?>">
                        <? } ?>
                        <input type="button"
                               onclick="javascript: document.location='ManageProjectMilestones.php?ProjectID=<?php echo $_REQUEST["ProjectID"]; ?>'"
                               value="<? echo C_NEW; ?>">
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
        document.f1.submit();
    }
</script>
<?php
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
$ppc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["ProjectID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
$HasRemoveAccess = true;
if ($ppc->GetPermission("Add_ProjectMilestones") == "YES")
    $HasAddAccess = true;
$RemoveType = $ppc->GetPermission("Remove_ProjectMilestones");
$UpdateType = $ppc->GetPermission("Update_ProjectMilestones");
$res = manage_ProjectMilestones::GetList($_REQUEST["ProjectID"]);
$SomeItemsRemoved = false;
for ($k = 0; $k < count($res); $k++) {
    if (isset($_REQUEST["ch_" . $res[$k]->ProjectMilestoneID])) {
        if ($RemoveType == "PUBLIC" || ($RemoveType == "PRIVATE" && $res[$k]->CreatorID == $_SESSION["PersonID"])) {
            manage_ProjectMilestones::Remove($res[$k]->ProjectMilestoneID);
            $SomeItemsRemoved = true;
        }
    }
}
if ($SomeItemsRemoved)
    $res = manage_ProjectMilestones::GetList($_REQUEST["ProjectID"]);
?>
<form id="ListForm" name="ListForm" method="post">
    <input type="hidden" id="Item_ProjectID" name="Item_ProjectID"
           value="<? echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
    <br>
    <div class="row">
        <div class="col-1"></div>
        <div class="col-10">
            <table class="table table-bordered table-sm table-striped">
                <tr class="table-info" bgcolor="#cccccc">
                    <td class="text-center" colspan="5">
                        <? echo C_IMPORTANT_DATES; ?>
                    </td>
                </tr>
                <tr class="HeaderOfTable">
                    <td width="1%"></td>
                    <td width="1%"><? echo C_ROW; ?></td>
                    <td width="2%"><? echo C_EDIT; ?></td>
                    <td width=10%><? echo C_DATE; ?></td>
                    <td><? echo C_DESCRIPTION; ?></td>
                </tr>
                <?
                for ($k = 0; $k < count($res); $k++) {
                    if ($k % 2 == 0)
                        echo "<tr class=\"OddRow\">";
                    else
                        echo "<tr class=\"EvenRow\">";
                    echo "<td>";
                    if ($RemoveType == "PUBLIC" || ($RemoveType == "PRIVATE" && $res[$k]->CreatorID == $_SESSION["PersonID"]))
                        echo "<input type=\"checkbox\" name=\"ch_" . $res[$k]->ProjectMilestoneID . "\">";
                    else
                        echo " ";
                    echo "</td>";
                    echo "<td>" . ($k + 1) . "</td>";
                    echo "	<td><a href=\"ManageProjectMilestones.php?UpdateID=" . $res[$k]->ProjectMilestoneID . "&ProjectID=" . $_REQUEST["ProjectID"] . "\"><img src='images/edit.gif' title='ویرایش'></a></td>";
                    echo "	<td nowrap>" . $res[$k]->MilestoneDate_Shamsi . "</td>";
                    echo "	<td>" . htmlentities($res[$k]->description, ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "</tr>";
                }
                ?>
                <tr class="FooterOfTable">
                    <td colspan="5" align="center">
                        <? if ($RemoveType != "NONE") { ?>
                            <input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
                        <? } ?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-1"></div>
    </div>
</form>
<form target="_blank" method="post" action="NewProjectMilestones.php" id="NewRecordForm" name="NewRecordForm">
    <input type="hidden" id="ProjectID" name="ProjectID"
           value="<? echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
    function ConfirmDelete() {
        if (confirm('آیا مطمین هستید؟')) document.ListForm.submit();
    }
</script>
</html>
