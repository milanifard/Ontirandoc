<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : انواع کارها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-16
*/
/*
 * changed by mostafa sader
 */
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/ProjectTaskTypes.class.php");
include_once("classes/projects.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"]))
{
    if(isset($_REQUEST["Item_title"]))
        $Item_title=$_REQUEST["Item_title"];
    if(isset($_REQUEST["ProjectID"]))
        $Item_ProjectID=$_REQUEST["ProjectID"];
    if(!isset($_REQUEST["UpdateID"]))
    {
        manage_ProjectTaskTypes::Add($Item_title
            , $Item_ProjectID
        );
        echo "<script>window.opener.document.location.reload(); window.close();</script>";
    }
    else
    {
        manage_ProjectTaskTypes::Update($_REQUEST["UpdateID"]
            , $Item_title
        );
        echo "<script>window.opener.document.location.reload(); window.close();</script>";
        die();
    }
    echo SharedClass::CreateMessageBox(C_INFORMATION_SAVED);
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"]))
{
    $obj = new be_ProjectTaskTypes();
    $obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
    $LoadDataJavascriptCode .= "document.f1.Item_title.value='".htmlentities($obj->title, ENT_QUOTES, 'UTF-8')."'; \r\n ";
}
?>
<div class="container" >
    <form method="post" id="f1" name="f1" >
        <?php
        if(isset($_REQUEST["UpdateID"]))
        {
            echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
        }
        ?>
        <br>
        <table class="table table-bordered " >
            <thead>
            <tr>
                <th class="text-center table-primary"><?php echo C_CREATE_EDIT_TYPES_OF_WORKS ?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <div class="form-group row">
                        <label for="Item_title" class="col-sm-1 col-form-label"><?php echo C_TITLE ?></label>
                        <div class="col-sm-11">
                            <input class="form-control" type="text" name="Item_title" id="Item_title" required placeholder=<?php echo '"'.C_DESIRED_TITLE.'"'?>>
                            <?php
                            if(!isset($_REQUEST["UpdateID"]))
                            {
                                ?>
                                <input type="hidden" name="ProjectID" id="ProjectID" value='<?php if(isset($_REQUEST["ProjectID"])) echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>'>
                            <?php } ?>
                        </div>
                    </div>
                </td>
            <tr class="text-center table-primary">
                <td align="center">
                    <input type="submit" class="btn   btn-outline-success"  value=<?php echo C_SAVE?>>
                    <input type="button" class="btn   btn-outline-danger" onclick="javascript: window.close();" value=<?php echo C_CLOSE?>>
                </td>
            </tr>
            </tbody>
        </table>
        <input type="hidden" name="Save" id="Save" value="1">
    </form>


    <script>
        <?php echo $LoadDataJavascriptCode; ?>
    </script>
</div>
