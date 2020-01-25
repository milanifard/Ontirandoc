<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به :
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 92-8-4
*/
/*
 * changed by Naghme Mohammadifar
 */

//-------------------- Include Part ---------------------------------
include("header.inc.php");
include("classes/FacilityPages.class.php");
include ("classes/SystemFacilities.class.php");
//-------------------------------------------------------------------


HTMLBegin();
if(isset($_REQUEST["Save"]))
{
    if(isset($_REQUEST["FacilityID"]))
        $Item_FacilityID=$_REQUEST["FacilityID"];
    if(isset($_REQUEST["Item_PageName"]))
        $Item_PageName=$_REQUEST["Item_PageName"];
    if(!isset($_REQUEST["UpdateID"]))
    {
        manage_FacilityPages::Add($Item_FacilityID, $Item_PageName);
    }
    else
    {
        manage_FacilityPages::Update($_REQUEST["UpdateID"], $Item_PageName);
    }
    echo SharedClass::CreateMessageBox(C_SAVED_INFO);
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"]))
{
    $obj = new be_FacilityPages();
    $obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
    $LoadDataJavascriptCode .= "document.f1.Item_PageName.value='".htmlentities($obj->PageName, ENT_QUOTES, 'UTF-8')."'; \r\n ";
}
?>

<div class="container">
    <form method="post" id="f1" name="f1" >

        <?php
        if(isset($_REQUEST["UpdateID"]))
        {
            echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
        }
        echo manage_SystemFacilities::ShowSummary($_REQUEST["FacilityID"]);
        ?>
        <br>
        <table class="table table-bordered " >
            <thead >
            <tr>
                <th class="text-center table-info" >
                    <?php
                    echo C_CREATE_EDIT_A_PAGE_RELATED_TO_FEATURE
                    ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <div class="form-group row">
                        <label for="Item_PageName" class="col-sm-1 col-form-label">
                            <?php
                            echo C_TITLE_PAGE
                            ?>
                        </label>
                        <div class="col-sm-11">
                            <?php if(!isset($_REQUEST["UpdateID"])) { ?>
                                <input type="hidden" name="FacilityID" id="FacilityID" value='<? if(isset($_REQUEST["FacilityID"])) echo htmlentities($_REQUEST["FacilityID"], ENT_QUOTES, 'UTF-8'); ?>'>
                            <?php } ?>
                            <input  type="text" class="form-control" name="Item_PageName" id="Item_PageName" required placeholder= <?php echo "'".C_PAGE_PLACE_HOLDER."'" ?> />
                        </div>
                    </div>
                </td>

            </tr>

            <tr >
                <td class="text-center">
                    <input type="submit" class="btn   btn-outline-success" value=<?php echo C_SAVE?> >
                    <input type="button" class="btn   btn-info" onclick="javascript: document.location='ManageFacilityPages.php?FacilityID=<?php echo $_REQUEST["FacilityID"]; ?>'" value=<?php echo C_NEW?>>
                    <input type="button" class="btn  btn-danger" onclick="javascript: window.close();" value=<?php echo "'".C_CLOSE."'"?>>
                </td>
            </tr>
            </tbody>
        </table>

        <input type="hidden" name="Save" id="Save" value="1">
    </form>




    <?php
    $res = manage_FacilityPages::GetList($_REQUEST["FacilityID"]);
    $SomeItemsRemoved = false;
    for($k=0; $k<count($res); $k++)
    {
        if(isset($_REQUEST["ch_".$res[$k]->FacilityPageID]))
        {
            manage_FacilityPages::Remove($res[$k]->FacilityPageID);
            $SomeItemsRemoved = true;
        }
    }
    if($SomeItemsRemoved)
        $res = manage_FacilityPages::GetList($_REQUEST["FacilityID"]);
    ?>

    <form id="ListForm" name="ListForm" method="post">
        <input type="hidden" id="Item_FacilityID" name="Item_FacilityID" value="<? echo htmlentities($_REQUEST["FacilityID"], ENT_QUOTES, 'UTF-8'); ?>">
        <br>
        <table class="table table-bordered table-striped"  >
            <thead >
            <tr  >
                <th class="text-center table-info" colspan="4"><?php echo C_RELATED_PAGES_TO_THIS_FEATURE?> </th>
            </tr>
            <tr >
                <th > </th>
                <th ><?php echo C_ROW?></th>
                <th "><?php echo C_EDIT?></td>
                <th ><?php echo C_PAGE?></th>

            </tr>
            </thead>
            <tbody>

            <?
            for($k=0; $k<count($res); $k++)
            {
                echo "<tr>";
                echo "<td width=\"10px\">";
                echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->FacilityPageID."\">";
                echo "</td >";
                echo "<td width=\"20px\" >".($k+1)."</td>";
                echo "	<td width=\"20px\" ><a class=\"btn btn-sm btn-outline-primary\"href=\"ManageFacilityPages.php?UpdateID=".$res[$k]->FacilityPageID."&FacilityID=".$_REQUEST["FacilityID"]."\">
	                    <i class=\"fa fa-edit\"></i></a></td>";
                echo "	<td >".htmlentities($res[$k]->PageName, ENT_QUOTES, 'UTF-8')."</td>";
                echo "</tr>";
            }
            ?>

            <tr >
                <td class="text-center" colspan="4">
                    <input type="button" class=" btn btn-danger btn-light" onclick="javascript:ConfirmDelete();" value=<?php echo C_DELETE?>>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>

<form target="_blank" method="post" action="NewFacilityPages.php" id="NewRecordForm" name="NewRecordForm">
    <div class="form-group">
        <input type="hidden" id="FacilityID" name="FacilityID" class="form-control" value="<? echo htmlentities($_REQUEST["FacilityID"], ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</form>
</div>


<script>
    function ConfirmDelete()
    {
        if(confirm( <?php
                        echo '"';
                        echo C_CONFIRM_TO_DELETE;
                        echo '"'; ?> )) document.ListForm.submit();
    }
</script>
</html>
