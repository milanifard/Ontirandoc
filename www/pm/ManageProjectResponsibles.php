
<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : پاسخگویان به درخواستهای خارجی در پروژه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-31
*/
/*
 *
 * changed by Arezoo Abdi
 *
 */
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectResponsibles.class.php");
include ("classes/projects.class.php");
HTMLBegin();

if(isset($_REQUEST["Save"]))
{
    if(isset($_REQUEST["ProjectID"]))
        $Item_ProjectID=$_REQUEST["ProjectID"];
    if(isset($_REQUEST["Item_PersonID"]))
        $Item_PersonID=$_REQUEST["Item_PersonID"];
    if(!isset($_REQUEST["UpdateID"]))
    {
        manage_ProjectResponsibles::Add($Item_ProjectID
            , $Item_PersonID
        );
    }
    else
    {
        manage_ProjectResponsibles::Update($_REQUEST["UpdateID"]
            , $Item_PersonID
        );
    }
    echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"]))
{
    $obj = new be_ProjectResponsibles();
    $obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
    $LoadDataJavascriptCode .= "document.getElementById('Span_PersonID_FullName').innerHTML='".$obj->PersonID_FullName."'; \r\n ";
    $LoadDataJavascriptCode .= "document.getElementById('Item_PersonID').value='".$obj->PersonID."'; \r\n ";
}
?>
<form method="post" id="f1" name="f1" >
    <?php
    if(isset($_REQUEST["UpdateID"]))
    {
        echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
    }
    echo manage_projects::ShowSummary($_REQUEST["ProjectID"]);
    echo manage_projects::ShowTabs($_REQUEST["ProjectID"], "ManageProjectResponsibles");
    ?>
    <div class="container">
        <br><table class="table table-bordered" >
            <thead >
            <tr class="bg-dark text-white">
                <td align="center">ایجاد/ویرایش پاسخگویان به درخواستهای خارجی در پروژه</td>
            </tr>
            </thead>>


            <?php
            if(!isset($_REQUEST["UpdateID"]))
            {
                ?>
                <input type="hidden" name="ProjectID" id="ProjectID" value='<?php if(isset($_REQUEST["ProjectID"])) echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>'>
            <?php } ?>
            <tbody>
            <tr class="bg-light">
                <td>نام و نام خانوادگی:
                    <input type=hidden name="Item_PersonID" id="Item_PersonID">
                    <span id="Span_PersonID_FullName" name="Span_PersonID_FullName"></span> 	<a href='#' onclick='javascript: window.open("SelectStaff.php?InputName=Item_PersonID&SpanName=Span_PersonID_FullName");'>[انتخاب]</a>
                </td>
            </tr>



            <tr class="FooterOfTable bg-light">
                <td align="center">
                    <input type="button" class="btn btn-success" onclick="javascript: ValidateForm();" value="ذخیره">
                    <input type="button" class="btn btn-info" onclick="javascript: document.location='ManageProjectResponsibles.php?ProjectID=<?php echo $_REQUEST["ProjectID"]; ?>'" value="جدید">
                </td>
            </tr>
            </tbody>>
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
$res = manage_ProjectResponsibles::GetList($_REQUEST["ProjectID"]);
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
    if(isset($_REQUEST["ch_".$res[$k]->ProjectResponsibleID]))
    {
        manage_ProjectResponsibles::Remove($res[$k]->ProjectResponsibleID);
        $SomeItemsRemoved = true;
    }
}
if($SomeItemsRemoved)
    $res = manage_ProjectResponsibles::GetList($_REQUEST["ProjectID"]);
?>
<form id="ListForm" name="ListForm" method="post">
    <input type="hidden" id="Item_ProjectID" name="Item_ProjectID" value="<?php echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
    <br><table class="table table-bordered">
        <thead>
        <tr class="bg-dark text-white">
            <td colspan="3" align="center">پاسخگویان به درخواستهای خارجی در پروژه</td>
        </tr>
        </thead>>
        <tbody>
        <tr class="bg-light">
            <td width="1%">ردیف</td>
            <td width="2%">ویرایش</td>
            <td></td>
        </tr>
        <?php
        for($k=0; $k<count($res); $k++)
        {
            if($k%2==0)
                echo "<tr class=\"OddRow\">";
            else
                echo "<tr class=\"EvenRow\">";
            echo "<td>";
            echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->ProjectResponsibleID."\">";
            echo "</td>";
            echo "<td>".($k+1)."</td>";
            echo "	<td><a href=\"ManageProjectResponsibles.php?UpdateID=".$res[$k]->ProjectResponsibleID."&ProjectID=".$_REQUEST["ProjectID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
            echo "	<td>".$res[$k]->PersonID_FullName."</td>";
            echo "</tr>";
        }
        ?>
        <tr class="FooterOfTable bg-light">
            <td colspan="4" align="center">
                <input type="button" class="btn btn-danger" onclick="javascript: ConfirmDelete();" value="حذف">
            </td>
        </tr>
        </tbody>>
    </table>
</form>
<form target="_blank" method="post" action="NewProjectResponsibles.php" id="NewRecordForm" name="NewRecordForm">
    <input type="hidden" id="ProjectID" name="ProjectID" value="<?php echo htmlentities($_REQUEST["ProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
    function ConfirmDelete()
    {
        if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
    }

</script>
</div>
</html>
