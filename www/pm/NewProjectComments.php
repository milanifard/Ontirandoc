<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : یادداشتهای کار پژوهشی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 93-3-11
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("ResearchProjectComments.class.php");
include ("ResearchProject.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"]))
{
    if(isset($_REQUEST["Item_CommentBody"]))
        $Item_CommentBody=$_REQUEST["Item_CommentBody"];
    if(isset($_REQUEST["Item_ResearchProjectSessionID"]))
        $Item_ResearchProjectSessionID=$_REQUEST["Item_ResearchProjectSessionID"];
    if(isset($_REQUEST["Item_CreateDate"]))
        $Item_CreateDate=$_REQUEST["Item_CreateDate"];
    if(isset($_REQUEST["ResearchProjectID"]))
        $Item_ResearchProjectID=$_REQUEST["ResearchProjectID"];
    if(!isset($_REQUEST["UpdateID"]))
    {
        manage_ResearchProjectComments::Add($Item_CommentBody
            , $Item_ResearchProjectSessionID
            , $Item_ResearchProjectID
        );
    }
    else
    {
        manage_ResearchProjectComments::Update($_REQUEST["UpdateID"]
            , $Item_CommentBody
            , $Item_ResearchProjectSessionID
        );
    }
    echo SharedClass::CreateMessageBox(C_DATA_SAVED);
}
$CommentBody = $LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"]))
{
	$obj = new be_ResearchProjectComments();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
    $CommentBody = htmlentities($obj->CommentBody, ENT_QUOTES, 'UTF-8');
    $LoadDataJavascriptCode .= "document.f1.Item_ResearchProjectSessionID.value='".htmlentities($obj->ResearchProjectSessionID, ENT_QUOTES, 'UTF-8')."'; \r\n ";
}
?>
<form method="post" id="f1" name="f1" >
    <?
    if(isset($_REQUEST["UpdateID"]))
    {
        echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
    }
    echo manage_ResearchProject::ShowSummary($_REQUEST["ResearchProjectID"]);
    echo manage_ResearchProject::ShowTabs($_REQUEST["ResearchProjectID"], "ManageResearchProjectComments");
    ?>
    <br><table class="col-lg-11 table-sm table-borderless" style="border-radius: 5px; float: none; margin: auto;">
        <tr class="HeaderOfTable table-info">
            <td align="center"><? echo C_CREATE_EDIT_RESEARCH_PROJECT_COMMENT ?></td>
        </tr>
        <tr>
            <td>
                <table class="table-sm col-lg-11 table-borderless">
                    <tr>
                        <td width="1%" nowrap>
                            <? echo C_TEXT ?>
                        </td>
                        <td nowrap>
                            <textarea class="form-control" rows="5"><? echo $CommentBody; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td width="1%" nowrap>
                            <? echo C_SEASON ?>
                        </td>
                        <td nowrap>
                            <select name="Item_ResearchProjectSessionID" id="Item_ResearchProjectSessionID" class="form-control">
                                <option value=0>
                                    <? echo SharedClass::CreateARelatedTableSelectOptions("projectmanagement.ResearchProjectSessions", "ResearchProjectSessionID", "SessionTitle", "SessionTitle"); ?>	</select>
                        </td>
                    </tr>
                    <?
                    if(!isset($_REQUEST["UpdateID"]))
                    {
                        ?>
                        <input type="hidden" name="ResearchProjectID" id="ResearchProjectID" value='<? if(isset($_REQUEST["ResearchProjectID"])) echo htmlentities($_REQUEST["ResearchProjectID"], ENT_QUOTES, 'UTF-8'); ?>'>
                    <? } ?>
                </table>
            </td>
        </tr>
        <tr class="FooterOfTable table-info"/>
        <td align="center">
            <input type="button" class="btn btn-success" onclick="javascript: ValidateForm();" value=<? echo C_SAVE ?>>
            <input type="button" class="btn btn-warning" onclick="javascript: document.location='ManageResearchProjectComments.php?ResearchProjectID=<?php echo $_REQUEST["ResearchProjectID"]; ?>'" value=<? echo C_NEW ?>>
        </td>
        </tr>
    </table>
    <input type="hidden" name="Save" id="Save" value="1">
</form><script>
    <? echo $LoadDataJavascriptCode; ?>
    function ValidateForm()
    {
        document.f1.submit();
    }
</script>
</html>
