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
    echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"]))
{
    $obj = new be_FacilityPages();
    $obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
    $LoadDataJavascriptCode .= "document.f1.Item_PageName.value='".htmlentities($obj->PageName, ENT_QUOTES, 'UTF-8')."'; \r\n ";
}
?>


<form method="post" id="f1" name="f1" >
    <?
    if(isset($_REQUEST["UpdateID"]))
    {
        echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
    }
    echo manage_SystemFacilities::ShowSummary($_REQUEST["FacilityID"]);
    ?>

    <div class="container">
        <br>
        <table class="table" width="90%" border="1" cellspacing="0" align="center">
            <tr class="HeaderOfTable">
                <td align="center">ایجاد/ویرایش صفحه مرتبط با امکان</td>
            </tr>
            <tr>
                <td>
                    <table width="100%" border="0">
                        <?
                        if(!isset($_REQUEST["UpdateID"]))
                        {
                            ?>
                            <input type="hidden" name="FacilityID" id="FacilityID" value='<? if(isset($_REQUEST["FacilityID"])) echo htmlentities($_REQUEST["FacilityID"], ENT_QUOTES, 'UTF-8'); ?>'>
                            <?
                        }
                        ?>

                        <tr>
                            <td width="1%" nowrap>
                                صفحه
                            </td>
                            <td nowrap>
                                <input type="text" name="Item_PageName" id="Item_PageName" maxlength="145" size="40">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="FooterOfTable">
                <td align="center">
                    <input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
                    <input type="button" onclick="javascript: document.location='ManageFacilityPages.php?FacilityID=<?php echo $_REQUEST["FacilityID"]; ?>'" value="جدید">
                    <input type="button" onclick="javascript: window.close();" value="بستن">
                </td>
            </tr>
        </table>
        <input type="hidden" name="Save" id="Save" value="1">
</form>
</div>


<script>
    <? echo $LoadDataJavascriptCode; ?>
    function ValidateForm()
    {
        document.f1.submit();
    }
</script>



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
    <table width="90%" align="center" border="1" cellspacing="0">
        <tr bgcolor="#cccccc">
            <td colspan="4">
                صفحات مرتبط با این امکان
            </td>
        </tr>
        <tr class="HeaderOfTable">
            <td width="1%">&nbsp;</td>
            <td width="1%">ردیف</td>
            <td width="2%">ویرایش</td>
            <td>صفحه</td>
        </tr>




        <?
        for($k=0; $k<count($res); $k++)
        {
            if($k%2==0)
                echo "<tr class=\"OddRow\">";
            else
                echo "<tr class=\"EvenRow\">";
            echo "<td>";
            echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->FacilityPageID."\">";
            echo "</td>";
            echo "<td>".($k+1)."</td>";
            echo "	<td><a href=\"ManageFacilityPages.php?UpdateID=".$res[$k]->FacilityPageID."&FacilityID=".$_REQUEST["FacilityID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
            echo "	<td>".htmlentities($res[$k]->PageName, ENT_QUOTES, 'UTF-8')."</td>";
            echo "</tr>";
        }
        ?>



        <tr class="FooterOfTable">
            <td colspan="4" align="center">
                <input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
            </td>
        </tr>
    </table>
</form>
<form target="_blank" method="post" action="NewFacilityPages.php" id="NewRecordForm" name="NewRecordForm">
    <input type="hidden" id="FacilityID" name="FacilityID" value="<? echo htmlentities($_REQUEST["FacilityID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>



<script>
    function ConfirmDelete()
    {
        if(confirm('آیا مطمئن هستید؟')) document.ListForm.submit();
    }
</script>
</html>
