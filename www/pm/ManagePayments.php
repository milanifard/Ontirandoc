<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : پرداختی ها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-1-2
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/payments.class.php");
include ("classes/persons.class.php");
HTMLBegin();
$PersonName = "";
$pobj = new be_persons();
$pobj->LoadDataFromDatabase($_REQUEST["PersonID"]);
$PersonName = $pobj->pfname." ".$pobj->plname;

if(isset($_REQUEST["Save"]))
{
    if(isset($_REQUEST["PersonID"]))
        $Item_PersonID=$_REQUEST["PersonID"];
    if(isset($_REQUEST["Item_amount"]))
        $Item_amount=$_REQUEST["Item_amount"];
    if(isset($_REQUEST["PaymentDate_DAY"]))
    {
        $Item_PaymentDate = SharedClass::ConvertToMiladi($_REQUEST["PaymentDate_YEAR"], $_REQUEST["PaymentDate_MONTH"], $_REQUEST["PaymentDate_DAY"]);
    }
    if(isset($_REQUEST["Item_PayType"]))
        $Item_PayType=$_REQUEST["Item_PayType"];
    if(isset($_REQUEST["Item_PaymentDescription"]))
        $Item_PaymentDescription=$_REQUEST["Item_PaymentDescription"];

    $Item_PaymentFile = "";
    $Item_PaymentFileName = "";
    if (trim($_FILES['Item_PaymentFile']['name']) != '')
    {
        if ($_FILES['Item_PaymentFile']['error'] != 0)
        {
            echo ' خطا در ارسال فایل' . $_FILES['Item_PaymentFile']['error'];
        }
        else
        {
            $_size = $_FILES['Item_PaymentFile']['size'];
            $_name = $_FILES['Item_PaymentFile']['tmp_name'];
            $Item_PaymentFile = addslashes((fread(fopen($_name, 'r' ),$_size)));
            $Item_PaymentFileName = trim($_FILES['Item_PaymentFile']['name']);
        }
    }

    if(!isset($_REQUEST["UpdateID"]))
    {
        manage_payments::Add($Item_PersonID
            , $Item_amount
            , $Item_PaymentDate
            , $Item_PayType
            , $Item_PaymentDescription
            , $Item_PaymentFile
            , $Item_PaymentFileName
        );
    }
    else
    {
        manage_payments::Update($_REQUEST["UpdateID"]
            , $Item_amount
            , $Item_PaymentDate
            , $Item_PayType
            , $Item_PaymentDescription
            , $Item_PaymentFile
            , $Item_PaymentFileName
        );
    }
    echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
$PaymentDescription = "";
if(isset($_REQUEST["UpdateID"]))
{
    $obj = new be_payments();
    $obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
    $LoadDataJavascriptCode .= "document.f1.Item_amount.value='".htmlentities($obj->amount, ENT_QUOTES, 'UTF-8')."'; \r\n ";
    if($obj->PaymentDate_Shamsi!="date-error")
    {
        $LoadDataJavascriptCode .= "document.f1.PaymentDate_YEAR.value='".substr($obj->PaymentDate_Shamsi, 2, 2)."'; \r\n ";
        $LoadDataJavascriptCode .= "document.f1.PaymentDate_MONTH.value='".substr($obj->PaymentDate_Shamsi, 5, 2)."'; \r\n ";
        $LoadDataJavascriptCode .= "document.f1.PaymentDate_DAY.value='".substr($obj->PaymentDate_Shamsi, 8, 2)."'; \r\n ";
    }
    $LoadDataJavascriptCode .= "document.f1.Item_PayType.value='".htmlentities($obj->PayType, ENT_QUOTES, 'UTF-8')."'; \r\n ";
    $PaymentDescription = htmlentities($obj->PaymentDescription, ENT_QUOTES, 'UTF-8');
}
?>
<br>
<div class="container border"">
    <?
    if(isset($_REQUEST["UpdateID"]))
    {
        echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
    }
    ?>
    <br>
    <div>
        <div class="bg-dark text-white text-center font-weight-bolder">
            ایجاد/ویرایش پرداخت به <b><? echo $PersonName ?></b>
            <br>
            <br>
        </div>
    </div>
    <br>
    <div class="form-group row">
        <label class="col-sm-2 col-form-label text-right">مبلغ</label>
        <div class="col-sm-8">
            <input type="text" class="form-control" value="ریال">
        </div>
    </div>

    <fieldset class="form-group">
        <div class="row">
            <legend class="col-form-label col-sm-2 pt-0 text-right">نوع پرداخت</legend>
            <div class="col-sm-9">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios1" value="option1" >
                    <label class="form-check-label" for="gridRadios1">
                        واریز به حساب
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios2" value="option2">
                    <label class="form-check-label" for="gridRadios2">
                        چک
                    </label>
                </div>
                <div class="form-check disabled">
                    <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios3" value="option3">
                    <label class="form-check-label" for="gridRadios3">
                        نقد
                    </label>
                </div>
            </div>
        </div>
    </fieldset>

    <div class="form-group">
        <div class="row">
            <label class="col-form-label text-right col-sm-2">توضیحات</label>
            <textarea  class="form-control col-sm-8"  rows="3"></textarea>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <label class="col-form-label text-right col-sm-2">انتخاب فایل</label>
            <input type="file" class="form-control-file col-sm-9" >
        </div>
    </div>

    <div class="container row-cols-lg-6" >
        <input type="button" class= "btn btn-dark"  onclick="javascript: ValidateForm();" value="ذخیره">
        <input type="button" class="btn btn-dark" onclick="javascript: document.location='ManagePayments.php?PersonID=--><?php echo $_REQUEST["PersonID"]; ?>'" value="جدید">
    </div>
    <br>
    <input type="hidden" name="Save" id="Save" value="1">
</div>

<script>
    <? echo $LoadDataJavascriptCode; ?>
    function ValidateForm()
    {
        document.f1.submit();
    }
</script>
<?php
$res = manage_payments::GetList($_REQUEST["PersonID"]);
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
    if(isset($_REQUEST["ch_".$res[$k]->PaymentID]))
    {
        manage_payments::Remove($res[$k]->PaymentID);
        $SomeItemsRemoved = true;
    }
}
if($SomeItemsRemoved)
    $res = manage_payments::GetList($_REQUEST["PersonID"]);
?>
<!--<form id="ListForm" name="ListForm" method="post">-->
<div class="container border">
    <input type="hidden" id="Item_PersonID" name="Item_PersonID" value="<? echo htmlentities($_REQUEST["PersonID"], ENT_QUOTES, 'UTF-8'); ?>">
    <br>
    <table class="table">
        <thead class="thead-light">
            <tr>
                <th colspan="8" class="text-center"> پرداختی ها به <b><? echo $PersonName ?></b></th>
            </tr>
        </thead>
        <thead>
            <tr >
                <td>ردیف</td>
                <td>ویرایش</td>
                <td>مبلغ</td>
                <td>تاریخ</td>
                <td>نوع پرداخت</td>
                <td>توضیحات</td>
                <td>فایل</td>
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
            echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->PaymentID."\">";
            echo "</td>";
            echo "<td>".($k+1)."</td>";
            echo "	<td><a href=\"ManagePayments.php?UpdateID=".$res[$k]->PaymentID."&PersonID=".$_REQUEST["PersonID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
            echo "	<td>".htmlentities($res[$k]->amount, ENT_QUOTES, 'UTF-8')."</td>";
            echo "	<td>".$res[$k]->PaymentDate_Shamsi."</td>";
            echo "	<td>".$res[$k]->PayType_Desc."</td>";
            echo "	<td>".str_replace("\r", "<br>", htmlentities($res[$k]->PaymentDescription, ENT_QUOTES, 'UTF-8'))."</td>";
            echo "	<td>";
            if($res[$k]->PaymentFileName!="")
                echo "<a href='DownloadFile.php?FileType=payments&RecID=".$res[$k]->PaymentID."'>فایل </a>";
            else {
                echo "&nbsp;";
            }

            echo "	</td>";
            echo "</tr>";
        }
        ?>
        <tfoot>
            <tr>
                <td colspan="8" align="center">
                    <input type="button" class="btn bg-light text-dark" onclick="javascript: ConfirmDelete();" value="حذف">
                </td>
            </tr>
        </tfoot>
    </table>
</div>
<!--</form>-->
<form target="_blank" method="post" action="Newpayments.php" id="NewRecordForm" name="NewRecordForm">
    <input type="hidden" id="PersonID" name="PersonID" value="<? echo htmlentities($_REQUEST["PersonID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
    function ConfirmDelete()
    {
        if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
    }
</script>
</html>
