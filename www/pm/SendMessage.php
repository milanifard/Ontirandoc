<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : پیامهای شخصی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-21
*/

//
//
//
//
//
//THIS FILE IS TAKEN BY MOHAMAD_ALI_SAIDI PLEASE BE CAREFUL  !
//
//
//
//
//
//
/*
 *the senderId that we get from the session it returns the value of omid and when i want to save the value of the sender id in the database , the senderid column was defined as int and the value that it was saved to is char , so i changed it after 5hrs searching for this error !!!!
 */



error_reporting(E_ERROR | E_PARSE );
//error_reporting(E_ALL);
ini_set("display_errors", 1);
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/PrivateMessageFollows.class.php");
HTMLBegin();
$headers = 'From: falinoos@falinoos.com';

$mysql = pdodb::getInstance();

if(isset($_REQUEST["AutoSave"]))
{
    if($_REQUEST["TempData"]!="")
    {
        $res = $mysql->Execute("select TemporarySavedDataID from projectmanagement.TemporarySavedData where PersonID='".$_SESSION["PersonID"]."' and FieldName='MessageBody'");
        if($rec = $res->fetch())
        {
            $mysql->Prepare("update projectmanagement.TemporarySavedData set FieldValue=? where PersonID='".$_SESSION["PersonID"]."' and FieldName='MessageBody'");
            $mysql->ExecuteStatement(array($_REQUEST["TempData"]));
        }
        else {
            $mysql->Execute("delete from projectmanagement.TemporarySavedData where PersonID='".$_SESSION["PersonID"]."' and FieldName='MessageBody'");
            $mysql->Prepare("insert into projectmanagement.TemporarySavedData (PersonID, FieldName, FieldValue) values ('".$_SESSION["PersonID"]."','MessageBody',?)");
            $mysql->ExecuteStatement(array($_REQUEST["TempData"]));
        }
    }
    die();
}

if(isset($_REQUEST["Save"]))
{
    $Item_MessageTitle=$_REQUEST["Item_MessageTitle"];
    $Item_MessageBody=$_REQUEST["Item_MessageBody"];
    $Item_FileName=$_REQUEST["Item_FileName"];
    $Item_ToPersonID=$_REQUEST["Item_ToPersonID"];
    $Item_FileContent = "";
    $Item_FileName = "";
    if (trim($_FILES['Item_FileContent']['name']) != '')
    {
        if ($_FILES['Item_FileContent']['error'] != 0)
        {
            echo C_SENDING_FILE_ERROR . $_FILES['Item_FileContent']['error'];
        }
        else
        {
            $_size = $_FILES['Item_FileContent']['size'];
            $_name = $_FILES['Item_FileContent']['tmp_name'];
            $Item_FileContent = addslashes((fread(fopen($_name, 'r' ),$_size)));
            $Item_FileName = trim($_FILES['Item_FileContent']['name']);
        }
    }

    $MessageID = manage_PrivateMessages::Add($Item_MessageTitle
        , $Item_MessageBody
        , $Item_FileContent
        , $Item_FileName
    );
    $Receivers = explode(",", $Item_ToPersonID);
    for($j=0; $j<count($Receivers); $j++)
    {
        $FollowID = manage_PrivateMessageFollows::Add($MessageID
            , $Item_comment
            , $_SESSION["PersonID"]
            , $Receivers[$j]
            , 0
            , ""
            , ""
            , "NOT_READ"
            , 0
        );

        $MessageSubject = "نامه ای جدید در بخش مکاتبات سامانه ی مدیریت پروژه برای شما ارسال شده است";
        $Message‌Body = "موضوع نامه: ";
        $Message‌Body .= $Item_MessageTitle."\r\n";
        $Message‌Body .= "متن نامه: \r\n".$Item_MessageBody."\r\n";
        $Message‌Body .= "دسترسی به نامه با لینک زیر: \r\n";
        $Message‌Body .= "<a target=_blank href='http://pm.falinoos.com/pm/ShowMessage.php?MessageFollowID=".$FollowID."'>مشاهده</a>";

        $mysql->Prepare("select * from projectmanagement.persons where PersonID=?");
        $res = $mysql->ExecuteStatement(array($Receivers[$j]));
        if($rec = $res->fetch())
        {
            if($rec["CardNumber"]!="")
                mail($rec["CardNumber"], $MessageSubject, $Message‌Body, $headers);
        }

    }

    echo SharedClass::CreateMessageBox(C_MESSAGE_SENT);
    $mysql->Execute("delete from projectmanagement.TemporarySavedData where PersonID='".$_SESSION["PersonID"]."' and FieldName='MessageBody'");
}
$OldMessageBody = "";
$res = $mysql->Execute("select FieldValue from projectmanagement.TemporarySavedData where PersonID='".$_SESSION["PersonID"]."' and FieldName='MessageBody'");
if($rec = $res->fetch())
{
    $OldMessageBody = $rec["FieldValue"];
}

?>

<form method="post" id="f1" name="f1" enctype="multipart/form-data" >

    <br>

    <div class="container">

        <div class="row border border-dark">

            <div class="col-12">

                <div class="row">

                    <p class="col-12 bg-info text-dark text-center  ">


                        <?php
                        echo C_SEND_MESSAGE;
                        ?>

                    </p>

                </div>



                <div class="row">

                    <div class="col-2">
                        <br>
                        <span class="text-danger text-center ">
                            * </span>
                        <span class="text-red text-center ">

                            <?php
                                echo C_TITLE;
                            ?> </span>

                    </div>

                    <div class="col-9">
                        <br>
                        <div class="form-group">
                            <input type="text" class="form-control" name="Item_MessageTitle" id="Item_MessageTitle" maxlength="1000" size="40">
                        </div>

                    </div>

                </div>

                <hr>

                <div class="row ">

                    <div class="col-2">

                        <span class="text-center">
                                <?php
                                echo C_TEXT;
                                ?> </span>


                    </div>

                    <div class="col-9">

                        <div class="form-group">
                            <textarea name="Item_MessageBody" class="form-control" id="Item_MessageBody" cols="80" rows="5"><?php echo $OldMessageBody; ?></textarea>
                            <span id=AutoSaveSpan name=AutoSaveSpan></span>
                        </div>

                    </div>

                </div>

                <hr>


                <div class="row ">

                    <div class="col-2">

                        <span class="text-center text-nowrap">
                                <?php
                                echo C_FILE;
                                ?> </span>


                    </div>

                    <div class="col-9">

                        <div class="form-group">
                            <input type="file" class="btn" name="Item_FileContent" id="Item_FileContent">
                        </div>

                    </div>

                </div>

                <hr>

                <div class="row ">

                    <div class="col-2">

                       <span class="text-danger text-center  text-nowrap ">
                            *</span>
                        <span class=" text-center text-nowrap " id="tr_ToPersonID" name="tr_ToPersonID" style='display:' >
                              <?php
                              echo C_TO_USER;
                              ?> </span>


                    </div>

                    <div class="col-9">

                        <div class="form-group">
                            <input type=hidden  name="Item_ToPersonID" id="Item_ToPersonID" value="0">
                            <span id="Span_ToPersonID_FullName" name="Span_ToPersonID_FullName"></span> 	<a href='#'  onclick='javascript: window.open("SelectMultiStaff.php?InputName=Item_ToPersonID&SpanName=Span_ToPersonID_FullName");'>[<?php
                                echo C_SELECT;
                                ?>]</a>
                        </div>

                    </div>



                </div>


                <div class="row">

                    <div class="col-12 bg-info text-dark text-center ">

                        <div class="btn">
                            <input type="button" class="btn btn-success" onclick="javascript: ValidateForm();" value=<?php
                            echo C_SEND;
                            ?> >

                        </div>

                    </div>

                </div>





            </div>

        </div>

    </div>

    <input type="hidden" name="Save" id="Save" value="1">

</form>

<script>
    <? echo $LoadDataJavascriptCode; ?>
    function ValidateForm()
    {
        if(document.getElementById('Item_MessageTitle').value=="")
        {
            alert( '<?php
                echo C_TITLE_EMPTY;
                ?>');
            return;
        }

        if(document.getElementById('Item_ToPersonID').value=="0")
        {
            alert('<?php
                echo C_RECEIVER_EMPTY;
                ?>');
            return;
        }
        document.f1.submit();
    }

    function AutoSave()
    {
        document.getElementById('AutoSaveSpan').innerHTML='<?php
            echo C_AUTO_SAVE;
            ?>';
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function()
        {
            if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                document.getElementById('AutoSaveSpan').innerHTML = xmlhttp.responseText;
            }
        }

        xmlhttp.open("GET","SendMessage.php?AutoSave=1&TempData="+document.getElementById('Item_MessageBody').value,true);
        xmlhttp.send();
        setTimeout("AutoSave()",60000);
    }

    setTimeout("AutoSave()",60000);

</script>
