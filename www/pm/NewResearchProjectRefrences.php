<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

<html>
<body class="bg-transparent">
<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : مراجع کار پژوهشی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 93-3-5
*/
/*
 تاریخ ویرایش :25/10/98
برنامه نویس :کورش احمدزاده عطایی
 */
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/ResearchProjectRefrences.class.php");
include_once("classes/ResearchProject.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"]))
{
    if(isset($_REQUEST["ResearchProjectID"]))
        $Item_ResearchProjectID=$_REQUEST["ResearchProjectID"];
    if(isset($_REQUEST["Item_RefrenceTitle"]))
        $Item_RefrenceTitle=$_REQUEST["Item_RefrenceTitle"];
    if(isset($_REQUEST["Item_URL"]))
        $Item_URL=$_REQUEST["Item_URL"];
    if(isset($_REQUEST["Item_abstract"]))
        $Item_abstract=$_REQUEST["Item_abstract"];
    if(isset($_REQUEST["Item_ReadType"]))
        $Item_ReadType=$_REQUEST["Item_ReadType"];
    if(isset($_REQUEST["Item_RefrenceTypeID"]))
        $Item_RefrenceTypeID=$_REQUEST["Item_RefrenceTypeID"];
    if(isset($_REQUEST["Item_BriefComment"]))
        $Item_BriefComment=$_REQUEST["Item_BriefComment"];
    if(isset($_REQUEST["Item_FileName"]))
        $Item_FileName=$_REQUEST["Item_FileName"];
    $Item_PublishYear=$_REQUEST["Item_PublishYear"];
    $Item_authors=$_REQUEST["Item_authors"];
    $Item_APA=$_REQUEST["Item_APA"];
    $Item_SearchEngine=$_REQUEST["Item_SearchEngine"];
    $Item_SearchKeywords=$_REQUEST["Item_SearchKeywords"];
    $Item_priprity=$_REQUEST["Item_priprity"];
    $Item_language=$_REQUEST["Item_language"];
    $Item_FileContent = "";
    $Item_FileName = "";
    if (trim($_FILES['Item_FileContent']['name']) != '')
    {
        if ($_FILES['Item_FileContent']['error'] != 0)
        {
            echo ERROR_SEND . $_FILES['Item_FileContent']['error'];
        }
        else
        {
            $_size = $_FILES['Item_FileContent']['size'];
            $_name = $_FILES['Item_FileContent']['tmp_name'];
            $Item_FileContent = addslashes((fread(fopen($_name, 'r' ),$_size)));
            $Item_FileName = trim($_FILES['Item_FileContent']['name']);
        }
    }
    if(!isset($_REQUEST["UpdateID"]))
    {
        manage_ResearchProjectRefrences::Add($Item_ResearchProjectID
            , $Item_RefrenceTitle
            , $Item_URL
            , $Item_abstract
            , $Item_ReadType
            , $Item_RefrenceTypeID
            , $Item_BriefComment
            , $Item_FileContent
            , $Item_FileName
            , $Item_PublishYear
            , $Item_authors
            , $Item_APA
            , $Item_SearchEngine
            , $Item_SearchKeywords
            , $Item_priprity
            , $Item_language
        );
        echo "<script>window.opener.document.location.reload(); window.close();</script>";
    }
    else
    {
        manage_ResearchProjectRefrences::Update($_REQUEST["UpdateID"]
            , $Item_RefrenceTitle
            , $Item_URL
            , $Item_abstract
            , $Item_ReadType
            , $Item_RefrenceTypeID
            , $Item_BriefComment
            , $Item_FileContent
            , $Item_FileName
            , $Item_PublishYear
            , $Item_authors
            , $Item_APA
            , $Item_SearchEngine
            , $Item_SearchKeywords
            , $Item_priprity
            , $Item_language
        );
        echo "<script>window.opener.document.location.reload(); window.close();</script>";
        die();
    }
    echo SharedClass::CreateMessageBox(INFO_SAVED);
}
$LoadDataJavascriptCode = '';
$abstract = $BComment = $APA = "";
$language = "EN";
if(isset($_REQUEST["UpdateID"]))
{
    $obj = new be_ResearchProjectRefrences();
    $obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
    $LoadDataJavascriptCode .= "document.f1.Item_RefrenceTitle.value='".htmlentities($obj->RefrenceTitle, ENT_QUOTES, 'UTF-8')."'; \r\n ";
    $LoadDataJavascriptCode .= "document.f1.Item_URL.value='".htmlentities($obj->URL, ENT_QUOTES, 'UTF-8')."'; \r\n ";
    //$LoadDataJavascriptCode .= "document.f1.Item_abstract.value='".htmlentities($obj->abstract, ENT_QUOTES, 'UTF-8')."'; \r\n ";
    $LoadDataJavascriptCode .= "document.f1.Item_ReadType.value='".htmlentities($obj->ReadType, ENT_QUOTES, 'UTF-8')."'; \r\n ";
    $LoadDataJavascriptCode .= "document.f1.Item_RefrenceTypeID.value='".htmlentities($obj->RefrenceTypeID, ENT_QUOTES, 'UTF-8')."'; \r\n ";
    $LoadDataJavascriptCode .= "document.f1.Item_authors.value='".htmlentities($obj->authors, ENT_QUOTES, 'UTF-8')."'; \r\n ";
    $LoadDataJavascriptCode .= "document.f1.Item_PublishYear.value='".htmlentities($obj->PublishYear, ENT_QUOTES, 'UTF-8')."'; \r\n ";
    //$LoadDataJavascriptCode .= "document.f1.Item_BriefComment.value='".htmlentities($obj->BriefComment, ENT_QUOTES, 'UTF-8')."'; \r\n ";
    $abstract = htmlentities($obj->abstract, ENT_QUOTES, 'UTF-8');
    $BComment = htmlentities($obj->BriefComment, ENT_QUOTES, 'UTF-8');
    $APA = htmlentities($obj->APA, ENT_QUOTES, 'UTF-8');
    $language = htmlentities($obj->language, ENT_QUOTES, 'UTF-8');
    $LoadDataJavascriptCode .= "document.f1.Item_SearchEngine.value='".htmlentities($obj->SearchEngine, ENT_QUOTES, 'UTF-8')."'; \r\n ";
    $LoadDataJavascriptCode .= "document.f1.Item_SearchKeywords.value='".str_replace("&quot;", "\"", htmlentities($obj->SearchKeywords, ENT_QUOTES, 'UTF-8'))."'; \r\n ";
    $LoadDataJavascriptCode .= "document.f1.Item_priprity.value='".htmlentities($obj->priprity, ENT_QUOTES, 'UTF-8')."'; \r\n ";
    $LoadDataJavascriptCode .= "document.f1.Item_language.value='".htmlentities($obj->language, ENT_QUOTES, 'UTF-8')."'; \r\n ";
    $PID = $obj->ResearchProjectID;
}
else
    $PID = $_REQUEST["ResearchProjectID"];
if($language=="FA")
    $direction = "rtl";
else
    $direction = "ltr";
?>
<div class="container">
<form method="post" id="f1" name="f1" enctype="multipart/form-data" >
    <div class="form-group">
    <?php
    if(isset($_REQUEST["UpdateID"]))
    {
        echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
        echo manage_ResearchProjectRefrences::ShowSummary($_REQUEST["UpdateID"]);
        //echo manage_ResearchProjectRefrences::ShowTabs($_REQUEST["UpdateID"], "NewResearchProjectRefrences");
    }
    ?>
    <br><table  class="table-bordered w-auto" cellspacing="0" align="center">
        <tr class="table-info">
            <td class="d-flex justify-content-center" ><?php echo CREAT_AND_EDIT_RES_RESEARCH ?></td>
        </tr>
        <tr>
            <td>
                <table class="table-borderless">
                    <?php
                    if(!isset($_REQUEST["UpdateID"]))
                    {
                        ?>
                        <input  class="form-control" type="hidden" name="ResearchProjectID" id="ResearchProjectID" value='<?php if(isset($_REQUEST["ResearchProjectID"])) echo htmlentities($_REQUEST["ResearchProjectID"], ENT_QUOTES, 'UTF-8'); ?>'>
                    <?php } ?>
                    <tr>
                        <td class="w-25" nowrap>
                            <?php echo SEARCH_ENG ?>
                        </td>
                        <td nowrap>
                            <select  class="form-control col-sm-3" name="Item_SearchEngine" id="Item_SearchEngine" >
                                <option value=0>-
                                <option value='GOOGLE'>GOOGLE SCHOLAR</option>
                                <option value='SCOPUS'>SCOPUS</option>
                                <option value='WEB_OF_KNOWLEDGE'>WEB OF KNOWLEDGE</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="w-25" nowrap>
                            <?php echo TAGS_WORDS ?>
                        </td>
                        <td nowrap>
                            <input class="form-control col-md-8" dir=ltr type="text" name="Item_SearchKeywords" id="Item_SearchKeywords" maxlength="1000" size="100">
                        </td>
                    </tr>

                    <tr>
                        <td class="w-25" nowrap>
                            <?php echo LANG_N ?>
                        </td>
                        <td nowrap>
                            <select class="form-control col-sm-3" name="Item_language" id="Item_language" >
                                <option value=0>-
                                <option value='EN'><?php echo EN_LAN_N ?></option>
                                <option value='FA'><?php echo FA_LAN_N ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="w-25" nowrap>
                            <?php echo TITLE_N ?>
                        </td>
                        <td >
                            <input class="form-control col-md-8" dir="<?php echo $direction ?>" type="text" name="Item_RefrenceTitle" id="Item_RefrenceTitle" maxlength="500" size="100">
                        </td>
                    </tr>
                    <tr>
                        <td  nowrap>
                            <?php echo WRITERS_N ?>
                        </td>
                        <td nowrap>
                            <input class="form-control col-md-8" dir="<?php echo $direction ?>" type="text" name="Item_authors" id="Item_authors" maxlength="500" size="100">
                        </td>
                    </tr>
                    <tr>
                        <td width="1%" nowrap>
                            <?php echo YEARS_N ?>
                        </td>
                        <td nowrap>
                            <input class="form-control col-sm-1" type="text" name="Item_PublishYear" id="Item_PublishYear" maxlength="4" size="4">
                        </td>
                    </tr>
                    <tr>
                        <td  nowrap>
                            APA
                        </td>
                        <td nowrap>
                            <textarea class="form-control col-md-10" dir=ltr name="Item_APA" id="Item_APA" rows=3 cols=90><?php echo $APA; ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            URL
                        </td>
                        <td nowrap>
                            <input class="form-control col-md-8" dir=ltr type="text" name="Item_URL" id="Item_URL" maxlength="500" size="40">
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <?php echo SUM_N ?>
                        </td>
                        <td >
                            <textarea  class="form-control col-md-10" name="Item_abstract" id="Item_abstract" cols="80" rows="5"><?php echo $abstract; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <?php echo STATE_OF_STUDY ?>
                        </td>
                        <td >
                            <select class="form-control col-sm-3" name="Item_ReadType" id="Item_ReadType" >
                                <option value=0>-
                                <option value='YES'> <?php echo ALREADY_STUDY ?></option>
                                <option value='NO'> <?php echo ALREADY_NOT_STUDY ?></option>
                                <option value='READING'><?php echo STUDING ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <?php echo IMPORTNT ?>
                        </td>
                        <td nowrap>
                            <input class="form-control col-sm-1" type="text" name="Item_priprity" id="Item_priprity" maxlength="2" size="2" value="1">
                        </td>
                    </tr>

                    <tr>
                        <td >
                            <?php echo CAT_N ?>
                        </td>
                        <td nowrap>
                            <select class="form-control col-sm-3" name="Item_RefrenceTypeID" id="Item_RefrenceTypeID">
                                <option value=0>-
                                    <?php
                                    $mysql=pdodb::getInstance();
                                    $mysql->Prepare("select RefrenceTypeID, RefrenceTypeTitle from projectmanagement.RefrenceTypes where ResearchProjectID=? order by RefrenceTypeTitle");
                                    $res = $mysql->ExecuteStatement(array($PID));
                                    while($rec = $res->fetch())
                                    {
                                        echo "<option value='".$rec["RefrenceTypeID"]."'>".$rec["RefrenceTypeTitle"];
                                    }
                                    ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <?php echo ALL_COM?>
                        </td>
                        <td nowrap>
                            <textarea  class="form-control col-md-8" name="Item_BriefComment" id="Item_BriefComment" cols="80" rows="5"><?php echo $BComment; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <?php echo FILE_N?>
                        </td>
                        <td nowrap>
                            <input type="file" name="Item_FileContent" id="Item_FileContent">
                            <?php if(isset($_REQUEST["UpdateID"]) && $obj->FileName!="") { ?>
                                <a href='DownloadRFile.php?TableName=ResearchProjectRefrences&ConditionField=ResearchProjectRefrenceID&FieldName=FileContent&RecID=<?php echo $_REQUEST["UpdateID"]; ?>&DownloadFileName=<?php echo $obj->FileName ?>'>دریافت فایل [<?php echo $obj->FileName; ?>]</a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php if(isset($_REQUEST["UpdateID"])) { ?>
                        <tr>
                            <td >
                                <a href='ManageResearchProjectRefrenceComments.php?ResearchProjectRefrenceID=<?php echo $_REQUEST["UpdateID"]; ?>'>
                                    <b>
                                        <?php echo NOTES_N?>
                                    </b>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center">
                <input type="button" class="btn btn-outline-success" onclick="javascript: ValidateForm();" value="<?php echo SAVE_M?>">
                <input type="button" class="btn btn-outline-danger" onclick="javascript: window.close();" value="<?php echo CLOSE_N ?>">
            </td>
        </tr>
    </table>
    <input type="hidden" name="Save" id="Save" value="1">
    </div>
</form>
</div>
<script>
    <?php echo $LoadDataJavascriptCode; ?>
    function ValidateForm()
    {
        document.f1.submit();
    }
</script>
</body>
</html>
