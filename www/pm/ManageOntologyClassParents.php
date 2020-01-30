<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : سلسله مراتب کلاسهای هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-3-1
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/OntologyClassHirarchy.class.php");
include ("classes/OntologyClasses.class.php");
HTMLBegin();
$mysql = pdodb::getInstance();
$obj = new be_OntologyClasses();
$obj->LoadDataFromDatabase($_REQUEST["OntologyClassID"]);
$OntologyID = $obj->OntologyID;
if(isset($_REQUEST["Save"]))
{
    if(isset($_REQUEST["OntologyClassID"]))
        $Item_OntologyClassID=$_REQUEST["OntologyClassID"];
    if(isset($_REQUEST["Item_OntologyClassParentID"]))
        $Item_OntologyClassParentID=$_REQUEST["Item_OntologyClassParentID"];
    manage_OntologyClassHirarchy::Add($Item_OntologyClassParentID
        , $Item_OntologyClassID
    );
    echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"]))
{
    $obj = new be_OntologyClassHirarchy();
    $obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
    $LoadDataJavascriptCode .= "document.f1.Item_OntologyClassParentID.value='".htmlentities($obj->OntologyClassParentID, ENT_QUOTES, 'UTF-8')."'; \r\n ";
}
?>
<form method="post" id="f1" name="f1" >
    <?
    if(isset($_REQUEST["UpdateID"]))
    {
        echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
    }
    echo manage_OntologyClasses::ShowSummary($_REQUEST["OntologyClassID"]);
    echo manage_OntologyClasses::ShowTabs($_REQUEST["OntologyClassID"], "ManageOntologyClassHirarchy");
    ?>
    <div class="container">
        <br>  <table class="table">
            <thead class="thead-dark">
            <style>
                .border{
                    display:inline-block;
                    width:100px;
                    height:100px;
                    margin=0px auto;
                }
            </style>


            <td align="center"> <?php echo C_ADD_PARENT_CLASS ?> </td>
            </tr>
            <tr>
            </thead>
            <td>
                <table  style="width: 100%">
                    <style>
                        .border{
                            display:inline-block;
                            width:100px;
                            height:100px;
                            margin=0px auto;
                        }
                    </style>




                    <input type="hidden" name="OntologyClassID" id="OntologyClassID" value='<? if(isset($_REQUEST["OntologyClassID"])) echo htmlentities($_REQUEST["OntologyClassID"], ENT_QUOTES, 'UTF-8'); ?>'>
                    <tr>
                        <td width="1%" nowrap>

                            <?php echo C_PARENT_CLASS ?>
                        </td>
                        <td nowrap>
                            <select name="Item_OntologyClassParentID" id="Item_OntologyClassParentID">
                                <option value=0>-
                                    <?
                                    $list = manage_OntologyClasses::GetList($obj->OntologyID);
                                    for($i=0; $i<count($list); $i++)
                                    {
                                        echo "<option value='".$list[$i]->OntologyClassID."'>";
                                        echo $list[$i]->label." (".$list[$i]->ClassTitle.")";
                                    }
                                    ?>
                            </select>
                            <a onclick='javascript: window.open("ShowOntologyClassTree.php?ReturnID=1&InputName=Item_OntologyClassParentID&OntologyID=<? echo $OntologyID;  ?>")' href="#"> <?php echo "Select" ?></a>
                        </td>
                    </tr>
                </table >
            </td>
            </tr>
            <tr >
                <td align="center">
                    <input type="button"  class="btn btn-info" onclick="javascript: ValidateForm();" value="<?php echo "ADD" ?>">
                    <input type="button"   class="btn btn-danger" onclick="javascript: window.close();" value="<?php echo "CLOSE" ?>">
                </td>
            </tr>
        </table>
        <input type="hidden" name="Save" id="Save" value="1">
</form><script>
    function ValidateForm()
    {
        document.f1.submit();
    }
</script>
<?php
$res = manage_OntologyClassHirarchy::GetParentListArray($_REQUEST["OntologyClassID"]);
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
    if(isset($_REQUEST["ch_".$res[$k]["OntologyClassHirarchyID"]]))
    {
        manage_OntologyClassHirarchy::Remove($res[$k]["OntologyClassHirarchyID"]);
        $SomeItemsRemoved = true;
    }
}
if($SomeItemsRemoved)
    $res = manage_OntologyClassHirarchy::GetParentListArray($_REQUEST["OntologyClassID"]);
?>
<form id="ListForm" name="ListForm" method="post">
    <input type="hidden" id="Item_OntologyClassID" name="Item_OntologyClassID" value="<? echo htmlentities($_REQUEST["OntologyClassID"], ENT_QUOTES, 'UTF-8'); ?>">
    <br><table class="table table-dark table-striped">
        <tr class="text-light">
            <td class="text-white" align="center" colspan="5">
                <?php echo C_PARENTS_CLASS ?>
            </td>
        </tr>


        <tr class="table table-bordered">



            <td style="width: 30%" class="text-body">ردیف</td>
            <td style="width: 0%" class="text-body">کلاس</td>
        </tr>
        <?
        for($k=0; $k<count($res); $k++)
        {
            if($k%2==0)
                echo "<tr class=\"OddRow\">";
            else
                echo "<tr class=\"EvenRow\">";
            echo "<td>";
            echo "<input type=\"checkbox\" name=\"ch_".$res[$k]["OntologyClassHirarchyID"]."\">";
            echo "</td>";
            echo "<td>".($k+1)."</td>";
            echo "	<td>".$res[$k]["label"]." (".$res[$k]["ClassTitle"].")</td>";
            echo "</tr>";
        }
        ?>
        <tr class="FooterOfTable">
            <td colspan="5" align="center">
                <input type="button" class="btn btn-danger" onclick="javascript: ConfirmDelete();" value="<?php echo C_REMOVE ?>">
            </td>
        </tr>
    </table>
</form>
<form target="_blank" method="post" action="NewOntologyClassHirarchy.php" id="NewRecordForm" name="NewRecordForm">
    <input type="hidden" id="OntologyClassID" name="OntologyClassID" value="<? echo htmlentities($_REQUEST["OntologyClassID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
    function ConfirmDelete()
    {
        if(confirm(<?php echo C_ARE_YOU_SURE ?>)) document.ListForm.submit();
    }

    
</script>
</div>
</html>
