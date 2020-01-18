<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : کاربران مجاز الگوهای جلسات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-2-28
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/PersonPermittedSessionTypes.class.php");
include ("classes/SessionTypes.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"]))
{
    if(isset($_REQUEST["Item_PersonID"]))
        $Item_PersonID=$_REQUEST["Item_PersonID"];
    if(isset($_REQUEST["SessionTypeID"]))
        $Item_SessionTypeID=$_REQUEST["SessionTypeID"];
    if(!isset($_REQUEST["UpdateID"]))
    {
        manage_PersonPermittedSessionTypes::Add($Item_PersonID
            , $Item_SessionTypeID
        );
    }
    else
    {
        manage_PersonPermittedSessionTypes::Update($_REQUEST["UpdateID"]
            , $Item_PersonID
        );
    }
    echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"]))
{
    $obj = new be_PersonPermittedSessionTypes();
    $obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
    $LoadDataJavascriptCode .= "document.getElementById('Span_PersonID_FullName').innerHTML='".$obj->PersonID_FullName."'; \r\n ";
    $LoadDataJavascriptCode .= "document.getElementById('Item_PersonID').value='".$obj->PersonID."'; \r\n ";
}
?>
<form method="post" id="f1" name="f1" >
    <?
    if(isset($_REQUEST["UpdateID"]))
    {
        echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
    }
    echo manage_SessionTypes::ShowSummary($_REQUEST["SessionTypeID"]);
    echo manage_SessionTypes::ShowTabs($_REQUEST["SessionTypeID"], "ManagePersonPermittedSessionTypes");
    ?>
    <br>
    <div class="container-fluid" style="padding: 0">
        <div class="row">
        <div class="col-md-2"></div>
        <div class="table-responsive col-md-8">
            <table class="table table-bordered">
                <thead class="text-center">
                <tr class="bg-info">
                    <td> <b> <?php echo C_SESSION_PERMITTED_CREATE_EDIT?> </b> </td>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <table class="table">
                                <tr class="container-fluid">
                                        <td class="col-md-6">
                                            <span>فرد مجاز:</span>
                                            <input type=hidden name="Item_PersonID" id="Item_PersonID">
                                            <span id="Span_PersonID_FullName" name="Span_PersonID_FullName"></span>
                                            <button type="button" class="btn btn-info" onclick='javascript: window.open("SelectStaff.php?InputName=Item_PersonID&SpanName=Span_PersonID_FullName");'><?php echo C_SEARCH ?></button>
                                        </td>
                                </tr>
                                <?
                                if(!isset($_REQUEST["UpdateID"]))
                                {
                                    ?>
                                    <input type="hidden" name="SessionTypeID" id="SessionTypeID" value='<? if(isset($_REQUEST["SessionTypeID"])) echo htmlentities($_REQUEST["SessionTypeID"], ENT_QUOTES, 'UTF-8'); ?>'>
                                <? } ?>
                            </table>
                        </td>
                    </tr>
                <thead class="bg-info">
                    <tr >
                        <td align="center">
                            <button class="btn btn-success" type="button" onclick="javascript: ValidateForm();"><?php echo C_SAVE ?></button>
                            <button class="btn btn-danger" type="button" onclick="javascript: document.location='ManagePersonPermittedSessionTypes.php?SessionTypeID=<?php echo $_REQUEST["SessionTypeID"]; ?>'"><?php echo C_NEW ?></button>
                        </td>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="col-md-2"></div>
        </div>
    </div>
    <input type="hidden" name="Save" id="Save" value="1">
</form><script>
    <? echo $LoadDataJavascriptCode; ?>
    function ValidateForm()
    {
        document.f1.submit();
    }
</script>
<?php
$res = manage_PersonPermittedSessionTypes::GetList($_REQUEST["SessionTypeID"]);
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
    if(isset($_REQUEST["ch_".$res[$k]->PersonPermittedSessionTypeID]))
    {
        manage_PersonPermittedSessionTypes::Remove($res[$k]->PersonPermittedSessionTypeID);
        $SomeItemsRemoved = true;
    }
}
if($SomeItemsRemoved)
    $res = manage_PersonPermittedSessionTypes::GetList($_REQUEST["SessionTypeID"]);
?>
<form id="ListForm" name="ListForm" method="post">
    <input type="hidden" id="Item_SessionTypeID" name="Item_SessionTypeID" value="<? echo htmlentities($_REQUEST["SessionTypeID"], ENT_QUOTES, 'UTF-8'); ?>">
    <br>
    <div class="row">
        <div class="col-md-2"></div>
        <div class="table-responsive col-md-8">
            <table class="table table-bordered table-hover">
                <thead class="text-center bg-info">
                <tr>
                    <td colspan="5">
                        <b>
                            کاربران مجاز الگوهای جلسات
                        </b>
                    </td>
                </tr>
                <tr class="bg-secondary">
                    <td width="1%"> </td>
                    <td width="1%">ردیف</td>
                    <td width="2%">ویرایش</td>
                    <td>فرد مجاز</td>
                    <td width=1%>دسترسیها</td>
                </tr>
                </thead>
                <?
                for($k=0; $k<count($res); $k++)
                {
                    if($k%2==0)
                        echo "<tr class=\"OddRow\">";
                    else
                        echo "<tr class=\"EvenRow\">";
                    echo "<td>";
                    echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->PersonPermittedSessionTypeID."\">";
                    echo "</td>";
                    echo "<td>".($k+1)."</td>";
                    echo "	<td><a href=\"ManagePersonPermittedSessionTypes.php?UpdateID=".$res[$k]->PersonPermittedSessionTypeID."&SessionTypeID=".$_REQUEST["SessionTypeID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
                    echo "	<td>".$res[$k]->PersonID_FullName."</td>";
                    echo "	<td>";
                    echo "<a target=_blank href='SessionTypesSetSecurity.php?RecID=".$_REQUEST["SessionTypeID"]."&SelectedPersonID=".$res[$k]->PersonID."'><img src='images/security.gif' title='تعریف دسترسی'></a>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
                <thead>
                    <tr class="bg-info">
                        <td colspan="5" align="center">
                            <button class="btn btn-danger" type="button" onclick="javascript: ConfirmDelete();" ><?php echo C_DELETE ?></button>
                        </td>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</form>
<form target="_blank" method="post" action="NewPersonPermittedSessionTypes.php" id="NewRecordForm" name="NewRecordForm">
    <input type="hidden" id="SessionTypeID" name="SessionTypeID" value="<? echo htmlentities($_REQUEST["SessionTypeID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
    function ConfirmDelete()
    {
        if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
    }
    setInterval(function(){

        var xmlhttp;
        if (window.XMLHttpRequest)
        {
            // code for IE7 , Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        }
        else
        {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.open("POST","header.inc.php",true);
        xmlhttp.send();

    }, 60000);

</script>
</html>
