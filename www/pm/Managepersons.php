<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : 
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 92-8-4
*/
include("header.inc.php");
include("classes/persons.class.php");
HTMLBegin();
if (isset($_REQUEST["Save"])) {
    $Item_FileContent = "";
    $Item_FileName = "";
    if (trim($_FILES['Item_FileContent']['name']) != '') {
        if ($_FILES['Item_FileContent']['error'] != 0) {
            echo ' خطا در ارسال فایل' . $_FILES['Item_FileContent']['error'];
        } else {
            $_size = $_FILES['Item_FileContent']['size'];
            $_name = $_FILES['Item_FileContent']['tmp_name'];
            $Item_FileContent = addslashes((fread(fopen($_name, 'r'), $_size)));
            $Item_FileName = trim($_FILES['Item_FileContent']['name']);
        }
    }

    $Item_pfname = $_REQUEST["Item_pfname"];
    $Item_plname = $_REQUEST["Item_plname"];
    $Item_CardNumber = $_REQUEST["Item_CardNumber"];
    //$Item_EnterExitTypeID=$_REQUEST["Item_EnterExitTypeID"];
    $Item_AccountInfo = $_REQUEST["Item_AccountInfo"];
    $mobile = $_REQUEST["mobile"];

    if (!isset($_REQUEST["UpdateID"])) {
        manage_persons::Add($Item_pfname
            , $Item_plname
            , $Item_CardNumber
            , $Item_FileContent
            , $Item_FileName
            , $Item_AccountInfo
            , $mobile
        );
    } else {
        manage_persons::Update($_REQUEST["UpdateID"]
            , $Item_pfname
            , $Item_plname
            , $Item_CardNumber
            , $Item_FileContent
            , $Item_FileName
            , $Item_AccountInfo
            , $mobile
        );
    }

    echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if (isset($_REQUEST["UpdateID"])) {
    $obj = new be_persons();
    $obj->LoadDataFromDatabase($_REQUEST["UpdateID"]);
    $LoadDataJavascriptCode .= "document.f1.Item_pfname.value='" . htmlentities($obj->pfname, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
    $LoadDataJavascriptCode .= "document.f1.Item_plname.value='" . htmlentities($obj->plname, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
    $LoadDataJavascriptCode .= "document.f1.Item_CardNumber.value='" . htmlentities($obj->CardNumber, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
    $LoadDataJavascriptCode .= "document.f1.Item_AccountInfo.value='" . htmlentities($obj->AccountInfo, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
    $LoadDataJavascriptCode .= "document.f1.mobile.value='" . htmlentities($obj->mobile, ENT_QUOTES, 'UTF-8') . "'; \r\n ";
}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <form method="post" id="f1" name="f1" class="" enctype="multipart/form-data">
                <?
                if (isset($_REQUEST["UpdateID"])) {
                    echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='" . $_REQUEST["UpdateID"] . "'>";
                }
                ?>
                <br>
                <table width="90%" border="1" cellspacing="0" align="center">
                    <tr class="HeaderOfTable">
                        <td align="center"><h3>ایجاد/ویرایش افراد</h3></td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" border="0">
                                <tr>
                                    <td width="1%" nowrap>
                                        نام
                                    </td>
                                    <td nowrap>
                                        <input type="text" name="Item_pfname" id="Item_pfname" maxlength="45" size="40">
                                    </td>
                                </tr>
                                <tr>
                                    <td width="1%" nowrap>
                                        نام خانوادگی
                                    </td>
                                    <td nowrap>
                                        <input type="text" name="Item_plname" id="Item_plname" maxlength="45" size="40">
                                    </td>
                                </tr>
                                <tr>
                                    <td width="1%" nowrap>
                                        ایمیل
                                    </td>
                                    <td nowrap>
                                        <input type="text" name="Item_CardNumber" id="Item_CardNumber" maxlength="45"
                                               size="40" dir=ltr>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="1%" nowrap>
                                        موبایل
                                    </td>
                                    <td nowrap>
                                        <input type="text" name="mobile" id="mobile" maxlength="45" size="40" dir=ltr>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="1%" nowrap>
                                        حساب بانکی
                                    </td>
                                    <td nowrap>
                                        <input type="text" name="Item_AccountInfo" id="Item_AccountInfo" maxlength="500"
                                               size="40" dir=rtl>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="1%" nowrap>
                                        تصویر
                                    </td>
                                    <td nowrap>
                                        <input type="file" name="Item_FileContent" id="Item_FileContent">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr class="FooterOfTable">
                        <td align="center">
                            <input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
                            <input type="button" onclick="javascript: document.location='Managepersons.php';"
                                   value="جدید">
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="Save" id="Save" value="1">
            </form>
        </div>
    </div>
    <script>
        <? echo $LoadDataJavascriptCode; ?>
        function ValidateForm() {
            document.f1.submit();
        }
    </script>
    <?php
    $NumberOfRec = 30;
    $k = 0;
    $PageNumber = 0;
    if (isset($_REQUEST["PageNumber"])) {
        if (!is_numeric($PageNumber))
            $PageNumber = 0;
        else
            $PageNumber = $_REQUEST["PageNumber"];
        $FromRec = $PageNumber * $NumberOfRec;
    } else {
        $FromRec = 0;
    }
    $OrderByFieldName = "PersonID";
    $OrderType = "";
    if (isset($_REQUEST["OrderByFieldName"])) {
        $OrderByFieldName = $_REQUEST["OrderByFieldName"];
        $OrderType = $_REQUEST["OrderType"];
    }
    $res = manage_persons::GetList($FromRec, $NumberOfRec, $OrderByFieldName, $OrderType);
    $SomeItemsRemoved = false;
    for ($k = 0; $k < count($res); $k++) {
        if (isset($_REQUEST["ch_" . $res[$k]->PersonID])) {
            manage_persons::Remove($res[$k]->PersonID);
            $SomeItemsRemoved = true;
        }
    }
    if ($SomeItemsRemoved)
        $res = manage_persons::GetList($FromRec, $NumberOfRec, $OrderByFieldName, $OrderType);
    ?>
    <form id="ListForm" name="ListForm" method="post">
        <? if (isset($_REQUEST["PageNumber"]))
            echo "<input type=\"hidden\" name=\"PageNumber\" value=" . $_REQUEST["PageNumber"] . ">"; ?>
        <br>
        <table width="90%" align="center" border="1" cellspacing="0">
            <tr bgcolor="#cccccc">
                <td colspan="10">
                    لیست افراد
                </td>
            </tr>
            <tr class="HeaderOfTable">
                <td width="1%">&nbsp;</td>
                <td width="1%">ردیف</td>
                <td width="2%">ویرایش</td>
                <td width=1%>تصویر</td>
                <td><a href="javascript: Sort('pfname', 'ASC');">نام</a></td>
                <td><a href="javascript: Sort('plname', 'ASC');">نام خانوادگی</a></td>
                <td width="10%">ایمیل</td>
                <td width="10%">موبایل</td>
                <td width="10%">پرداختها</td>
            </tr>
            <?
            for ($k = 0; $k < count($res); $k++) {
                if ($k % 2 == 0)
                    echo "<tr class=\"OddRow\">";
                else
                    echo "<tr class=\"EvenRow\">";
                echo "<td>";
                echo "<input type=\"checkbox\" name=\"ch_" . $res[$k]->PersonID . "\">";
                echo "</td>";
                echo "<td>" . ($k + $FromRec + 1) . "</td>";
                echo "	<td><a href=\"Managepersons.php?UpdateID=" . $res[$k]->PersonID . "\"><img src='images/edit.gif' title='ویرایش'></a></td>";
                echo "	<td><img src='ShowPersonPhoto.php?PersonID=" . $res[$k]->PersonID . "' width=40></td>";
                echo "	<td>" . htmlentities($res[$k]->pfname, ENT_QUOTES, 'UTF-8') . "</td>";
                echo "	<td>" . htmlentities($res[$k]->plname, ENT_QUOTES, 'UTF-8') . "</td>";
                echo "	<td>" . htmlentities($res[$k]->CardNumber, ENT_QUOTES, 'UTF-8') . "</td>";
                echo "	<td>" . htmlentities($res[$k]->mobile, ENT_QUOTES, 'UTF-8') . "</td>";
                echo "	<td><a href='ManagePayments.php?PersonID=" . $res[$k]->PersonID . "'>پرداختها</a></td>";
                echo "</tr>";
            }
            ?>
            <tr class="FooterOfTable">
                <td colspan="10" align="center">
                    <input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
                </td>
            </tr>
            <tr bgcolor="#cccccc">
                <td colspan="10" align="right">
                    <?
                    $TotalCount = manage_persons::GetCount();
                    for ($k = 0; $k < $TotalCount / $NumberOfRec; $k++) {
                        if ($PageNumber != $k)
                            echo "<a href='javascript: ShowPage(" . ($k) . ")'>";
                        echo($k + 1);
                        if ($PageNumber != $k)
                            echo "</a>";
                        echo " ";
                    }
                    ?>
                </td>
            </tr>
        </table>
    </form>
    <form target="_blank" method="post" action="Newpersons.php" id="NewRecordForm" name="NewRecordForm">
    </form>
    <form method="post" name="f2" id="f2">
        <input type="hidden" name="PageNumber" id="PageNumber" value="0">
        <input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
        <input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
    </form>
</div>
<script>
    function ConfirmDelete() {
        if (confirm('آیا مطمین هستید؟')) document.ListForm.submit();
    }

    function ShowPage(PageNumber) {
        f2.PageNumber.value = PageNumber;
        f2.submit();
    }

    function Sort(OrderByFieldName, OrderType) {
        f2.OrderByFieldName.value = OrderByFieldName;
        f2.OrderType.value = OrderType;
        f2.submit();
    }
</script>
</html>
