<?php
include("header.inc.php");
include("classes/FormsStruct.class.php");
include("classes/FormManagers.class.php");
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
                    <input style="text-align: center;" type="text" class="form-control" placeholder="عنوان فیلتر" input
                           type=text name='FormTitle'
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
                    echo "<table width=98% align=center border=1 cellspacing=0 class='table table-bordered table-sm'>";
                    echo "<thead><tr style='background-color: #c3efef; font-size: 1.2em;'>";
                    echo "<td width=1%>&nbsp;</td>";
                    echo "	<td width=1%>کد</td>";
                    echo "	<td width=30%>عنوان فرم</td>";
                    echo "	<td width=30%>فرم اصلی</td>";
                    echo "	<td width=30%>مدیران</td>";
                    echo "	<td width=10% nowrap>تنظیمات</td>";
                    echo "	<td width=5% nowrap>ایجاد کننده</td>";
                    echo "	<td width=5% nowrap>تاریخ ایجاد</td>";
                    echo "	<td width=5% nowrap>پر شده</td>";
                    echo "	<td width=5% nowrap>تایید نهایی</td>";
                    echo "</tr></thead>";
                    for ($k = 0; $k < count($res); $k++) {
                        if (isset($_REQUEST["ch_" . $res[$k]->FormsStructID])) {
                            manage_FormsStruct::Remove($res[$k]->FormsStructID);
                        } else {
                            // هر شخص فقط فرمهای ایجاد شده توسط خودش را ببیند
                            // فعلا جهت رفع مشکلات و پاسخگویی کاربر omid هم دسترسی به فرمهای بقیه خواهد داشت
                            //if($_SESSION["UserID"]==$res[$k]->CreatorUser || $_SESSION["UserID"]=="omid")
                            //if($_SESSION["UserID"]==$res[$k]->CreatorUser || $_SESSION["UserID"]=="omid")
                            {
                                if ($k % 2 == 0)
                                    echo "<tr class=OddRow>";
                                else
                                    echo "<tr class=EvenRow>";
                                echo "<td style='vertical-align: middle;'><input type=checkbox name=ch_" . $res[$k]->FormsStructID . "></td>";
                                echo "	<td style='vertical-align: middle;'>";
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
                                echo "	<a href='ManageQuestionnaireFields.php?FormsStructID=" . $res[$k]->FormsStructID . "'><img title='مدیریت فیلدها' src='images/Fields.gif' border=0 width=35></a>";
                                echo "	<a href='ManageFormsSections.php?FormsStructID=" . $res[$k]->FormsStructID . "'><img title='مدیریت بخشها' src='images/chart.jpg' border=0 width=35></a>";
                                echo "	<a href='ManageQuestionnaireDetailTables.php?Item_FormStructID=" . $res[$k]->FormsStructID . "'><img title='مدیریت جداول جزییات' src='images/Tables.gif' border=0 width=35></a>";
                                echo "	<a href='ManageQuestionnaireManagers.php?Item_FormStructID=" . $res[$k]->FormsStructID . "'><img title='تعریف مدیران این فرم' src='images/roles.gif' border=0 width=35></a>";
                                echo "	<a href='ManageQuestionnaireUsers.php?Item_FormStructID=" . $res[$k]->FormsStructID . "'><img title='تعریف کاربران برای ثبت داده' src='images/users.gif' border=0 width=35></a>";
                                echo "	<a href='DownloadQuestionnaires.php?Item_FormStructID=" . $res[$k]->FormsStructID . "'><img title='دریافت پرسشنامه های ثبت شده' src='images/SelectTable.gif' border=0 width=35></a>";

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

                                echo "</tr>";
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
                        echo `</div>
                    </div>`;
                    }
                    //                    echo "</td></tr>";
                    echo "</table>";
                    ?>
                </div>
                <div class="card-footer">
                    <input class="btn btn-danger" type=submit value='حذف'>&nbsp;
                    <input class="btn btn-success" type=button value='ایجاد'
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