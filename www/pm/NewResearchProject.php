<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : پروژهی پژوهشی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 93-3-5
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/ResearchProject.class.php");
HTMLBegin();
if (isset($_REQUEST["Save"])) {
    if (isset($_REQUEST["Item_title"]))
        $Item_title = $_REQUEST["Item_title"];
    if (isset($_REQUEST["Item_ProjectType"]))
        $Item_ProjectType = $_REQUEST["Item_ProjectType"];
    if (isset($_REQUEST["Item_OwnerID"]))
        $Item_OwnerID = $_REQUEST["Item_OwnerID"];
    if (!isset($_REQUEST["UpdateID"])) {
        manage_ResearchProject::Add($Item_title
            , $Item_ProjectType
        );
        echo "<script>window.opener.document.location.reload(); window.close();</script>";
    } else {
        manage_ResearchProject::Update($_REQUEST["UpdateID"]
            , $Item_title
            , $Item_ProjectType
        );
        echo "<script>window.opener.document.location.reload(); window.close();</script>";
        die();
    }
    echo SharedClass::CreateMessageBox(C_SAVED_INFO);
}
$LoadDataJavascriptCode = '';
if (isset($_REQUEST["UpdateID"])) {
    $obj = new be_ResearchProject();
    $obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
    $LoadDataJavascriptCode .= "document.f1.Item_title.value='" . htmlentities($obj->title, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
    $LoadDataJavascriptCode .= "document.f1.Item_ProjectType.value='" . htmlentities($obj->ProjectType, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
}
?>
<form method="post" id="f1" name="f1">
    <?
    if (isset($_REQUEST["UpdateID"])) {
        echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='" . $_REQUEST["UpdateID"] . "'>";
        echo manage_ResearchProject::ShowSummary($_REQUEST["UpdateID"]);
        echo manage_ResearchProject::ShowTabs($_REQUEST["UpdateID"], "NewResearchProject");
    }
    ?>
    <br>
    <div class="row">
        <div class="col-1"></div>
        <div class="col-10">
            <table class="table table-bordered table-sm table-striped">
                <thead class="table-info">
                <tr class="HeaderOfTable">
                    <td align="center"><? echo C_CREATE_OR_EDIT_RESEARCH_WORK ?></td>
                </tr>
                </thead>
                <tr class="table-info">
                    <td align="center">
                        <table class="table">
                            <thead class="table-info">
                            <tr>
                                <td width="1%" nowrap>
                                    <? echo C_TITLE ?>
                                </td>
                                <td nowrap>
                                    <input type="text" name="Item_title" id="Item_title" maxlength="345" size="40">
                                </td>
                            </tr>
                            </thead>
                            <tr>
                                <td width="1%" nowrap>
                                    <? echo C_TYPE ?>
                                </td>
                                <td nowrap>
                                    <select name="Item_ProjectType" id="Item_ProjectType">
                                        <option value=0>-
                                        <option value='PAPER'><? echo C_ARTICLE ?></option>
                                        <option value='THESIS'><? echo C_THESIS ?></option>
                                        <option value='BOOK'><? echo C_BOOK ?></option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="table-info">
                    <td align="center">
                        <input type="button" class="btn btn-success" onclick="javascript: ValidateForm();"
                               value="<? echo  C_STORE ?>">
                        <input type="button" class="btn btn-danger" onclick="javascript: window.close();" value="<? echo C_CLOSE ?>">
                    </td>
                </tr>
            </table>

        </div>
        <div class="col-1"></div>

        <input type="hidden" name="Save" id="Save" value="1">
    </div>
</form>
<?
if (isset($_REQUEST["UpdateID"])) {
    if (isset($_REQUEST["KeyWord"]))
        $KeyWord = $_REQUEST["KeyWord"];
    else
        $KeyWord = "";
    ?>
    <br>
    <form id=sf name=sf method=post>
        <div class="row">
            <div class="col-1"></div>
            <div class="col-10">
                <input type="hidden" name="UpdateID" id="UpdateID" value='<? echo $_REQUEST["UpdateID"] ?>'>
                <table class="table table-bordered table-sm table-striped">
                    <tr class="table-info">
                        <td><? echo C_KEY_WORD ?>: <input type=text name=KeyWord id=KeyWord value='<? echo $KeyWord ?>'><input
                                    type=submit class="btn btn-info"
                                    value='<? echo C_SEARCH ?>'>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-1"></div>
        </div>
    </form>
    <?
    $k = 0;
    if (isset($_REQUEST["KeyWord"])) {
        echo "<br>  <div class=\"row\"> <div class=\"col-1\"></div>
        <div class=\"col-10\"><table class=\"table table-bordered table-sm table-striped\">";
        echo "<thead></thead><tr class=\"table-info\"><td colspan=2>یادداشتهای منابع</td></tr></thead>";
        $query = "select ResearchProjectRefrenceCommentID, ResearchProjectRefrenceID, CommentBody 
		from projectmanagement.ResearchProjectRefrenceComments 
		JOIN projectmanagement.ResearchProjectRefrences using (ResearchProjectRefrenceID)
		where  ResearchProjectID='" . $_REQUEST["UpdateID"] . "' and CommentBody like ? ";
        $mysql = pdodb::getInstance();
        //echo $query;
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array("%" . $KeyWord . "%"));
        $i = 0;
        while ($rec = $res->fetch()) {
            $k++;
            echo "<tr>";
            echo "<td><a target=_blank href='ManageResearchProjectRefrenceComments.php?UpdateID=" . $rec["ResearchProjectRefrenceCommentID"];
            echo "&ResearchProjectRefrenceID=" . $rec["ResearchProjectRefrenceID"];
            echo "'>" . $k . "</a></td>";
            if (strlen(preg_replace('/[^\00-\255]+/u', '', $rec["CommentBody"])) / strlen($rec["CommentBody"]) > 0.8)
                echo "<td dir=ltr>";
            else {
                echo "<td>";
            }
            echo str_replace($KeyWord, "<font color=blue><b>" . $KeyWord . "</b></font>", str_replace("\n", "<br>", $rec["CommentBody"])) . "</td>";
            echo "</tr>";
        }
        echo "</table></div><div class=\"col-1\"></div></div>";

        echo "<br>
                <div class=\"row\">
                <div class=\"col-1\"></div>
                <div class=\"col-10\"><table class=\"table table-bordered table-sm table-striped\">";
        echo "<thead class=table-info><tr><td colspan=2>یادداشتهای متفرقه</td></tr></thead>";
        $query = "select ResearchProjectCommentID, CommentBody 
		from projectmanagement.ResearchProjectComments 
		where  ResearchProjectID='" . $_REQUEST["UpdateID"] . "' and CommentBody like ? ";
        $mysql = pdodb::getInstance();
        //echo $query;
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array("%" . $KeyWord . "%"));
        $i = 0;
        while ($rec = $res->fetch()) {
            $k++;
            echo "<tr>";
            echo "<td><a target=_blank href='ManageResearchProjectComments.php?UpdateID=" . $rec["ResearchProjectCommentID"];
            echo "&ResearchProjectRefrenceID=" . $rec["ResearchProjectRefrenceID"];
            echo "'>" . $k . "</a></td>";
            if (strlen(preg_replace('/[^\00-\255]+/u', '', $rec["CommentBody"])) / strlen($rec["CommentBody"]) > 0.8)
                echo "<td dir=ltr>";
            else {
                echo "<td>";
            }
            echo str_replace($KeyWord, "<font color=blue><b>" . $KeyWord . "</b></font>", str_replace("\n", "<br>", $rec["CommentBody"])) . "</td>";
            echo "</tr>";
        }
        echo "</table></div>
                    <div class=\"col-1\"></div></div>";

        echo "<br>  <div class=\"row\"><div class=\"col-1\"></div>
            <div class=\"col-10\">
            <table class=\"table table-bordered table-sm table-striped\">";
        echo "<thead class='table-info'><tr><td colspan=4>" . C_SEARCH_RESULT_IN_ABSTRACT . "</td></tr></thead>";
        $query = "select ResearchProjectRefrenceID, RefrenceTitle, BriefComment, abstract
		from projectmanagement.ResearchProjectRefrences 
		where  ResearchProjectID='" . $_REQUEST["UpdateID"] . "' and (BriefComment like ? or abstract like ?)";
        $mysql = pdodb::getInstance();
        //echo $query."<br>";
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array("%" . $KeyWord . "%", "%" . $KeyWord . "%"));
        $i = 0;
        while ($rec = $res->fetch()) {
            $k++;
            echo "<tr>";
            echo "<td><a target=_blank href='NewResearchProjectRefrences.php?UpdateID=" . $rec["ResearchProjectRefrenceID"];
            echo "'>" . $k . "</a></td>";
            echo "<td>&nbsp;" . str_replace($KeyWord, "<font color=blue><b>" . $KeyWord . "</b></font>", str_replace("\n", "<br>", $rec["abstract"])) . "</td>";
            echo "<td>&nbsp;" . str_replace($KeyWord, "<font color=blue><b>" . $KeyWord . "</b></font>", str_replace("\n", "<br>", $rec["BriefComment"])) . "</td>";
            echo "</tr>";
        }
        echo "</table></div>
                <div class=\"col-1\"></div></div>";

    }
} ?>
<script>
    <? echo $LoadDataJavascriptCode; ?>
    function ValidateForm() {
        document.f1.submit();
    }
</script>
</html>
