<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : 
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 92-8-4
*/
include("header.inc.php");
include_once("classes/AccountSpecs.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"]))
{
    if(isset($_REQUEST["Item_UserID"]))
        $Item_UserID=$_REQUEST["Item_UserID"];
    if(isset($_REQUEST["Item_UserPassword"]))
        $Item_UserPassword=$_REQUEST["Item_UserPassword"];
    if(isset($_REQUEST["Item_PersonID"]))
        $Item_PersonID=$_REQUEST["Item_PersonID"];
    if(!isset($_REQUEST["UpdateID"]))
    {
        manage_AccountSpecs::Add($Item_UserID
            , $Item_UserPassword
            , $Item_PersonID
        );
    }
    else
    {
        manage_AccountSpecs::Update($_REQUEST["UpdateID"]
            , $Item_UserID
            , $Item_UserPassword
            , $Item_PersonID
        );
    }
    echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"]))
{
    $obj = new be_AccountSpecs();
    $obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
    $LoadDataJavascriptCode .= "document.f1.Item_UserID.value='".htmlentities($obj->UserID, ENT_QUOTES, 'UTF-8')."'; \r\n ";
    //$LoadDataJavascriptCode .= "document.f1.Item_UserPassword.value='".htmlentities($obj->UserPassword, ENT_QUOTES, 'UTF-8')."'; \r\n ";
    $LoadDataJavascriptCode .= "document.f1.Item_PersonID.value='".htmlentities($obj->PersonID, ENT_QUOTES, 'UTF-8')."'; \r\n ";
}
?>
<div class="container-md p-3 my-3 bg-light text-black-50">
    <form method="post" id="f1" name="f1" >
        <?php
        if(isset($_REQUEST["UpdateID"]))
        {
            echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
        }
        ?>

        <br><table class="table table-hover bg-dark">
            <tr class="HeaderOfTable">
                <td class="text-white bg-dark font-weight-bold" align="center">ا <?php echo C_CREATE_EDIT_POSBBLE?></td>
            </tr>
            <tr>
                <td>
                    <table class="table table-info">
                        <tr>
                            <td class="text-info" width="1%" nowrap>
                                <?php echo C_NAME_OF_USER?>
                            </td>
                            <td nowrap>
                                <input type="text" class="form-control" name="Item_UserID" id="Item_UserID" maxlength="100" size="40">
                            </td>
                        </tr>
                        <tr>
                            <td class="text-info" width="1%" nowrap>
                                <?php echo C_PASSWORD?>
                            </td>
                            <td nowrap>
                                <input type="password" class="form-control" name="Item_UserPassword" id="Item_UserPassword" maxlength="100" size="40" value=''>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-info" width="1%" nowrap>
                                <?php echo C_NAME_AND_FAMILY?>
                            </td>
                            <td nowrap>
                                <select class="custom-select-lg" name="Item_PersonID" id="Item_PersonID">
                                    <option value=0>-
                                        <?php
                                        //echo SharedClass::CreateAdvanceRelatedTableSelectOptions("projectmanagement.persons", "PersonID", "FullName", "concat(plname, ' ', pfname) as FullName, PersonID", "plname, pfname");
                                        $mysql = pdodb::getInstance();
                                        $pres = $mysql->Execute("select concat(plname, ' ', pfname) as FullName, PersonID from projectmanagement.persons order by plname, pfname");
                                        while($prec = $pres->fetch())
                                        {
                                            echo "<option value='".$prec["PersonID"]."'>".$prec["FullName"];
                                        }
                                        ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="FooterOfTable">
                <td align="center">
                    <input type="button" class="btn btn-primary" onclick="javascript: ValidateForm();" value="<?php echo C_SAVE ?>">
                    <input type="button" class="btn btn-primary" onclick="javascript: document.location='ManageAccountSpecs.php';" value="<?php echo C_NEW ?>">
                </td>
            </tr>
        </table>
        <input type="hidden" name="Save" id="Save" value="1">
    </form><script>
        <?php echo $LoadDataJavascriptCode; ?>
        function ValidateForm()
        {
            document.f1.submit();
        }
    </script>
    <?php
    $res = manage_AccountSpecs::GetList();
    $SomeItemsRemoved = false;
    for($k=0; $k<count($res); $k++)
    {
        if(isset($_REQUEST["ch_".$res[$k]->AccountSpecID]))
        {
            manage_AccountSpecs::Remove($res[$k]->AccountSpecID);
            $SomeItemsRemoved = true;
        }
    }
    if($SomeItemsRemoved)
        $res = manage_AccountSpecs::GetList();
    ?>

    <form  id="ListForm" name="ListForm" method="post">
        <div class="form-group">
            <br><table class="table table-dark>
        <tr  bgcolor="#cccccc">
            <td class="table-primary text-primary display-5 font-weight-bold" align="center" colspan="6">
                <?php echo C_LIST_OF_USERS?>
            </td>
            </tr>
            <tr class="table-bordered">
                <td class="font-weight-bolder" width="1%">&nbsp;</td>
                <td class="font-weight-bolder" width="1%"><?php echo C_ROW?></td>
                <td class="font-weight-bolder" width="2%"><?php echo C_EDIT?></td>
                <td class="font-weight-bolder"><?php echo C_NAME_OF_USER?></td>
                <td class="font-weight-bolder"><?php echo C_NAME_AND_FAMILY?></td>
                <td class="font-weight-bolder" width=1% nowrap><?php echo C_PRIVILEGES?></td>
            </tr>
            <?php


            for($k=0; $k<count($res); $k++)
            {
                if($k%2==0)
                    echo "<tr class=\"table-bordered\">";
                else
                    echo "<tr class=\"table-bordered\">";
                echo "<td><input type=\"checkbox\" name='ch_".$res[$k]->AccountSpecID."'></td>";
                echo "<td>".($k+1)."</td>";
                echo "	<td><a href='ManageAccountSpecs.php?UpdateID=".$res[$k]->AccountSpecID."'><i style=\"font-size:24px\" class=\"fa\">&#xf044;</i></a></td>";
                echo "	<td>".$res[$k]->UserID."</td>";
                echo "	<td>".$res[$k]->PersonID_Desc."</td>";
                echo "	<td><a href='ManageUserPermissions.php?Item_UserID=".$res[$k]->UserID."'><i class=\"fa fa-file-pdf-o\" style=\"font-size:24px;color:lightseagreen\"></i></a></td>";
                echo "</tr>";
            }
            ?>
            <tr class="FooterOfTable">
                <td   class="table-danger"  colspan="6" align="center">
                    <input type="button" class="btn btn-danger" onclick="javascript: ConfirmDelete();" value="<?php echo C_REMOVE?>">
                </td>
            </tr>
            </table>
        </div>
    </form>
    <form target="_blank" method="post" action="NewAccountSpecs.php" id="NewRecordForm" name="NewRecordForm">
    </form>
    <script>
        function ConfirmDelete()
        {
            if(confirm(<?php echo C_BEING_SURE?>)) document.ListForm.submit();
        }
    </script>
</div>
</html>
