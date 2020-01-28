<?php
include("header.inc.php");
include_once("classes/FormsStruct.class.php");
include_once("classes/FormManagers.class.php");
HTMLBegin();
$mysql = pdodb::getInstance(config::$db_servers["master"]["host"], config::$db_servers["master"]["formsgenerator_user"], config::$db_servers["master"]["formsgenerator_pass"], FormsGeneratorDB::DB_NAME);
$PageItemsCount = 60;
$k = 0;
$PageNumber = 0;
//$ListCondition = " 1=1 order by CreateDate DESC ";
$FormTitle = "";
if (isset($_REQUEST["FormTitle"]))
    $FormTitle = $_REQUEST["FormTitle"];
$ListCondition = " (FormsStruct.CreatorUser='" . $_SESSION["UserID"] . "' or FormsStruct.FormsStructID in (select FormsStructID from FormManagers where PersonID='" . $_SESSION["PersonID"] . "')) and FormsStruct.FormTitle like '%" . $FormTitle . "%' and IsQuestionnaire='YES' order by FormsStruct.CreateDate DESC ";
if (isset($_REQUEST["PageNumber"])) {
    $PageNumber = $_REQUEST["PageNumber"];
    $ListCondition .= " limit " . ($_REQUEST["PageNumber"] * $PageItemsCount) . "," . $PageItemsCount;
} else {
    $ListCondition .= " limit 0," . $PageItemsCount;
}
$res = manage_FormsStruct::GetList($ListCondition);
echo "<br>";
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col col-6">
            <form method=post>
                <!--                <table align=center border=1 cellspacing=0>-->
                <!--                    <tr>-->
                <!--                        <td>-->
                <!--                            عنوان: <input type=text name='FormTitle' value='-->
                <!--    --><?php //echo $FormTitle ?><!--'> <input type=submit value='فیلتر'>-->
                <!--                        </td>-->
                <!--                    </tr>-->
                <!--                </table>-->
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="<? echo C_MQ_FILTER ?>" input
                           type=text name='FormTitle' <? echo(UI_LANGUAGE == "FA" ? "dir: rtl" : "") ?>
                           value='<?php echo $FormTitle ?>'>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-outline-info">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
        </div>
        </form>
    </div>
    <hr style="margin-top: 0;">
    <div class="row">
        <div class="col">
            <form class="card">
                <div class="card-body text-center">
                    <?php
                    echo "<form id=f1 name=f1 method=post>";
                    if (isset($_REQUEST["PageNumber"]))
                        echo "<input type=hidden name=PageNumber value=" . $_REQUEST["PageNumber"] . ">";
                    if (isset($_REQUEST["FormTitle"]))
                        echo "<input type=hidden name=FormTitle value=" . $_REQUEST["FormTitle"] . ">";
                    echo "<table class='table table-bordered table-sm table-striped'>";
                    echo "<thead><tr style='background-color: #c3efef; font-size: 1.2em;'>";
                    echo "<td scope='col' width=1%>&nbsp;</td>";
                    echo "	<td scope='col' width=1%>" . (C_MQ_CODE) . "</td>";
                    echo "	<td scope='col' width=30%>" . (C_FORM_NAME) . "</td>";
                    echo "	<td scope='col' width=30%>" . (C_MQ_MAIN_FORM) . "</td>";
                    echo "	<td scope='col' width=30%>" . (C_MQ_MANAGERS) . "</td>";
                    echo "	<td scope='col' width=10% nowrap>" . (C_MQ_SETTINGS) . "</td>";
                    echo "	<td scope='col' width=5% nowrap>" . (C_MQ_CREATOR) . "</td>";
                    echo "	<td scope='col' width=5% nowrap>" . (C_MQ_CREATE_DATE) . "</td>";
                    echo "	<td scope='col' width=5% nowrap>" . (C_MQ_FILL) . "</td>";
                    echo "	<td scope='col' width=5% nowrap>" . (C_MQ_LAST_ACCEPT) . "</td>";
                    echo "</tr></thead>";
                    echo "<tbody>";
                    for ($k = 0; $k < count($res); $k++) {
                        if (isset($_REQUEST["ch_" . $res[$k]->FormsStructID])) {
                            manage_FormsStruct::Remove($res[$k]->FormsStructID);
                        } else {
                            // هر شخص فقط فرمهای ایجاد شده توسط خودش را ببیند
                            // فعلا جهت رفع مشکلات و پاسخگویی کاربر omid هم دسترسی به فرمهای بقیه خواهد داشت
                            //if($_SESSION["UserID"]==$res[$k]->CreatorUser || $_SESSION["UserID"]=="omid")
                            //if($_SESSION["UserID"]==$res[$k]->CreatorUser || $_SESSION["UserID"]=="omid")
                            {
                                echo "<tr>";
                                echo "<td style='vertical-align: middle;'><input type=checkbox name=ch_" . $res[$k]->FormsStructID . "></td>";
                                echo "	<th scope='row' style='vertical-align: middle;'>";
                                echo "	<a href='NewQuestionnaire.php?UpdateID=" . $res[$k]->FormsStructID . "'>";
                                echo "	" . $res[$k]->FormsStructID . "</a></td>";
                                echo "	<td style='vertical-align: middle;'>" . $res[$k]->FormTitle . "</td>";
                                echo "	<td style='vertical-align: middle;'>&nbsp;" . $res[$k]->ParentTitle . "</td>";
                                echo "	<td style='vertical-align: middle;' nowrap>";
                                $managers = manage_FormManagers::GetList(" FormsStructID=" . $res[$k]->FormsStructID);
                                if (count($managers) == 0)
                                    echo "&nbsp;";
                                for ($i = 0; $i < count($managers); $i++) {
                                    echo $managers[$i]->PersonName . " (" . $managers[$i]->AccessType . ")<br>";

                                }
                                echo "	</td>";
                                echo "	<td style='vertical-align: middle;' nowrap>";
                                echo "	<a href='ManageQuestionnaireFields.php?FormsStructID=" . $res[$k]->FormsStructID . "' style=\"font-size: 20px; color: #2aa2a2;\"><i title='مدیریت فیلدها' class=\"fas fa-list\"></i></a>";
                                echo "	<a href='ManageFormsSections.php?FormsStructID=" . $res[$k]->FormsStructID . "' style=\"font-size: 20px; color: #2aa2a2;\"><i title='مدیریت بخشها' class=\"fas fa-tasks\"></i></a>";
                                echo "	<a href='ManageQuestionnaireDetailTables.php?Item_FormStructID=" . $res[$k]->FormsStructID . "' style=\"font-size: 20px; color: #2aa2a2;\"><i title='مدیریت جداول جزییات' class=\"fas fa-table\"></i></a>";
                                echo "	<a href='ManageQuestionnaireManagers.php?Item_FormStructID=" . $res[$k]->FormsStructID . "' style=\"font-size: 20px; color: #2aa2a2;\"><i title='تعریف مدیران این فرم' class=\"fas fa-users\"></i></a>";
                                echo "	<a href='ManageQuestionnaireUsers.php?Item_FormStructID=" . $res[$k]->FormsStructID . "' style=\"font-size: 20px; color: #2aa2a2;\"><i title='تعریف کاربران برای ثبت داده' class=\"fas fa-user-plus\"></i></a>";
                                echo "	<a href='DownloadQuestionnaires.php?Item_FormStructID=" . $res[$k]->FormsStructID . "' style=\"font-size: 20px; color: #2aa2a2;\"><i title='دریافت پرسشنامه های ثبت شده' class=\"fas fa-file-export\"></i></a>";

                                echo "	</td>";
                                echo "	<td style='vertical-align: middle;' nowrap>" . $res[$k]->CreatorUser . "</td>";
                                echo "	<td style='vertical-align: middle;' nowrap>" . $res[$k]->CreateDate . "</td>";
                                $query = "select count(*) as TotalCount from " . $res[$k]->RelatedDB . "." . $res[$k]->RelatedTable . " 
											JOIN formsgenerator.QuestionnairesCreators on (QuestionnairesCreators.RelatedRecordID=" . $res[$k]->RelatedTable . "." . $res[$k]->KeyFieldName . ") 
											JOIN formsgenerator.TemporaryUsers on (TemporaryUsers.WebUserID=QuestionnairesCreators.UserID)
											JOIN formsgenerator.TemporaryUsersAccessForms on (TemporaryUsersAccessForms.WebUserID=TemporaryUsers.WebUserID)
											where QuestionnairesCreators.FormsStructID='" . $res[$k]->FormsStructID . "' 
											";
                                $res2 = $mysql->Execute($query);
                                $rec2 = $res2->fetch();
                                echo "<td style='vertical-align: middle;'>" . $rec2["TotalCount"] . "</td>";
                                $query = "select count(*) as TotalCount from " . $res[$k]->RelatedDB . "." . $res[$k]->RelatedTable . " 
											JOIN formsgenerator.QuestionnairesCreators on (QuestionnairesCreators.RelatedRecordID=" . $res[$k]->RelatedTable . "." . $res[$k]->KeyFieldName . ") 
											JOIN formsgenerator.TemporaryUsers on (TemporaryUsers.WebUserID=QuestionnairesCreators.UserID)
											JOIN formsgenerator.TemporaryUsersAccessForms on (TemporaryUsersAccessForms.WebUserID=TemporaryUsers.WebUserID)
											where QuestionnairesCreators.FormsStructID='" . $res[$k]->FormsStructID . "' 
											and filled='YES'";
                                $res2 = $mysql->Execute($query);
                                $rec2 = $res2->fetch();
                                echo "<td style='vertical-align: middle;'>" . $rec2["TotalCount"] . "</td>";

                                echo "</tr></tbody>";
                            }
                        }
                    }
                    //                    echo "<tr bgcolor=#cccccc><td colspan=17 align=right>";
                    if ($PageNumber != 0) {
                        echo `<div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                        <div class="btn-group" role="group">`;
                        $TotalCount = manage_persons::GetCount();
                        for ($k = 0; $k < $TotalCount / $NumberOfRec; $k++) {
                            if ($PageNumber != $k) {
                                echo "<button type='button' class='btn btn-secondary' href='javascript: ShowPage(" . ($k) . ")'>";
                                echo($k + 1);
                                echo "</button>";
                            }
                        }
                        echo `</div></div>`;
                    }
                    //                    echo "</td></tr>";
                    echo "</table>";
                    ?>
                </div>
                <div class="card-footer">
                    <input class="btn btn-danger" type=submit value='<? echo C_DELETE ?>'>&nbsp;
                    <input class="btn btn-success" type=button value='<? echo C_MQ_MAKE ?>'
                           onclick='javascript: document.location="NewQuestionnaire.php";'>
                </div>
            </form>
        </div>
    </div>
</div>

<form method=post name=f2 id=f2>
    <input type=hidden name=PageNumber id=PageNumber value=0>
</form>
</div>

<script>
    function ShowPage(PageNumber) {
        f2.PageNumber.value = PageNumber;
        f2.submit();
    }
</script>
</html>