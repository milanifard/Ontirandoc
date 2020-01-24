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
<div class="container">
    <div class="row">
        <div class="col">
            <form method="post" id="f1" name="f1" class="" enctype="multipart/form-data">
                <?
                if (isset($_REQUEST["UpdateID"])) {
                    echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='" . $_REQUEST["UpdateID"] . "'>";
                }
                ?>
                <div class="card mt-4">
                    <h5 class="card-title my-2 text-info" style="text-align: center;">ایجاد/ویرایش افراد</h5>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="Item_pfname" style="display: block; text-align: right;">نام</label>
                            <input type="text" name="Item_pfname" id="Item_pfname" maxlength="45" size="40"
                                   class="form-control"
                                   placeholder="omid" style="text-align: right;">
                        </div>
                        <div class="form-group">
                            <label for="Item_plname" style="display: block; text-align: right;">نام خانوادگی</label>
                            <input type="text" class="form-control" name="Item_plname" id="Item_plname" maxlength="45"
                                   size="40"
                                   placeholder="milanifard" style="text-align: right;">
                        </div>
                        <div class="form-group">
                            <label for="Item_CardNumber" style="display: block; text-align: right;">ایمیل</label>
                            <input type="email" class="form-control" name="Item_CardNumber" id="Item_CardNumber"
                                   maxlength="45"
                                   size="40" dir=ltr
                                   placeholder="omid@milanifard.com" style="text-align: right;">
                        </div>
                        <div class="form-group">
                            <label for="mobile" style="display: block; text-align: right;">موبایل</label>
                            <!-- TODO: add '-' into the input format of mobile number -->
                            <input type="text" class="form-control" name="mobile" id="mobile" maxlength="45" size="40"
                                   dir=ltr placeholder="0910000000020" style="text-align: right;">
                        </div>
                        <div class="form-group" style="display: block; text-align: right;">
                            <label for="Item_AccountInfo">حساب کاربری</label>
                            <input type="text" class="form-control" type="text" name="Item_AccountInfo"
                                   id="Item_AccountInfo" maxlength="500"
                                   size="40" dir=rtl style="text-align: right;">
                        </div>
                        <div class="custom-file">
                            <label class="custom-file-label" for="Item_FileContent">تصویر</label>
                            <input type="file" name="Item_FileContent" id="Item_FileContent" class="custom-file-input">
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="#" class="btn btn-success col-3">ذخیره</a>
                        <a href="#" class="btn btn-primary col-3" style="float: right;">جدید</a>
                    </div>
                </div>

                <!--                <br>-->
                <!--                <table width="90%" border="1" cellspacing="0" align="center">-->
                <!--                    <tr class="HeaderOfTable">-->
                <!--                        <td align="center"><h3></h3></td>-->
                <!--                    </tr>-->
                <!--                    <tr>-->
                <!--                        <td>-->
                <!--                            <table width="100%" border="0">-->
                <!--                                <tr>-->
                <!--                                    <td width="1%" nowrap>-->
                <!--                                        نام-->
                <!--                                    </td>-->
                <!--                                    <td nowrap>-->
                <!--                                        <input type="text" name="Item_pfname" id="Item_pfname" maxlength="45" size="40">-->
                <!--                                    </td>-->
                <!--                                </tr>-->
                <!--                                <tr>-->
                <!--                                    <td width="1%" nowrap>-->
                <!--                                        نام خانوادگی-->
                <!--                                    </td>-->
                <!--                                    <td nowrap>-->
                <!--                                        <input type="text" name="Item_plname" id="Item_plname" maxlength="45" size="40">-->
                <!--                                    </td>-->
                <!--                                </tr>-->
                <!--                                <tr>-->
                <!--                                    <td width="1%" nowrap>-->
                <!--                                        ایمیل-->
                <!--                                    </td>-->
                <!--                                    <td nowrap>-->
                <!--                                        <input type="text" name="Item_CardNumber" id="Item_CardNumber" maxlength="45"-->
                <!--                                               size="40" dir=ltr>-->
                <!--                                    </td>-->
                <!--                                </tr>-->
                <!--                                <tr>-->
                <!--                                    <td width="1%" nowrap>-->
                <!--                                        موبایل-->
                <!--                                    </td>-->
                <!--                                    <td nowrap>-->
                <!--                                        <input type="text" name="mobile" id="mobile" maxlength="45" size="40" dir=ltr>-->
                <!--                                    </td>-->
                <!--                                </tr>-->
                <!--                                <tr>-->
                <!--                                    <td width="1%" nowrap>-->
                <!--                                        حساب بانکی-->
                <!--                                    </td>-->
                <!--                                    <td nowrap>-->
                <!--                                        <input type="text" name="Item_AccountInfo" id="Item_AccountInfo" maxlength="500"-->
                <!--                                               size="40" dir=rtl>-->
                <!--                                    </td>-->
                <!--                                </tr>-->
                <!--                                <tr>-->
                <!--                                    <td width="1%" nowrap>-->
                <!--                                        تصویر-->
                <!--                                    </td>-->
                <!--                                    <td nowrap>-->
                <!--                                        <input type="file" name="Item_FileContent" id="Item_FileContent">-->
                <!--                                    </td>-->
                <!--                                </tr>-->
                <!--                            </table>-->
                <!--                        </td>-->
                <!--                    </tr>-->
                <!--                    <tr class="FooterOfTable">-->
                <!--                        <td align="center">-->
                <!--                            <input type="button" onclick="javascript: ValidateForm();" value="ذخیره">-->
                <!--                            <input type="button" onclick="javascript: document.location='Managepersons.php';"-->
                <!--                                   value="جدید">-->
                <!--                        </td>-->
                <!--                    </tr>-->
                <!--                </table>-->
                <input name="Save" id="Save" value="1" hidden>
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
    <div class="row mt-4 justify-content-center">
        <div class="col">
            <div class="card mb-4">
                <form id="ListForm" name="ListForm" method="post">
                    <? if (isset($_REQUEST["PageNumber"]))
                        echo "<input type=\"hidden\" name=\"PageNumber\" value=" . $_REQUEST["PageNumber"] . ">"; ?>
                    <!--        <br>-->

                    <div class="card-body">
                        <table width="90%" align="center" class="table table-bordered table-sm text-center">
                            <tr bgcolor="#FAFAFA">
                                <td colspan="10" class="text-center text-info">
                                    لیست افراد
                                </td>
                            </tr>
                            <thead style="background-color: #FAFAFA">
                            <tr>
                                <td class="text-info" scope="col">&nbsp;</td>
                                <td class="text-info" scope="col">ردیف</td>
                                <td class="text-info" scope="col">ویرایش</td>
                                <td class="text-info" scope="col">تصویر</td>
                                <td class="text-info" scope="col"><a href="javascript: Sort('pfname', 'ASC');">نام</a></td>
                                <td class="text-info"><a href="javascript: Sort('plname', 'ASC');">نام خانوادگی</a></td>
                                <td class="text-info" scope="col">ایمیل</td>
                                <td class="text-info" scope="col">موبایل</td>
                                <td class="text-info" scope="col">پرداختها</td>
                            </tr>
                            </thead>
                            <tbody>
                            <!--                            TODO: change check box to custom layout-->
                            <?
                            for ($k = 0; $k < count($res); $k++) {
                                if ($k % 2 == 0)
                                    echo "<tr scope\"row\">";
                                else
                                    echo "<tr scope\"row\">";
                                echo "<td>";
                                echo "<input type=\"checkbox\" name=\"ch_" . $res[$k]->PersonID . "\">";
                                echo "</td>";
                                echo "<td>" . ($k + $FromRec + 1) . "</td>";
                                echo "	<td><a href=\"Managepersons.php?UpdateID=" . $res[$k]->PersonID . "\"><img src='images/edit.gif' title='ویرایش'></a></td>";
                                echo "	<td><img src='ShowPersonPhoto.php?PersonID=" . $res[$k]->PersonID . "' width=40></td>";
                                echo "	<td>" . (isset($res[$k]->pfname) ? htmlentities($res[$k]->pfname, ENT_QUOTES, 'UTF-8') : '') . "</td>";
                                echo "	<td>" . (isset($res[$k]->plname) ? htmlentities($res[$k]->plname, ENT_QUOTES, 'UTF-8') : '') . "</td>";
                                echo "	<td>" . (isset($res[$k]->CardNumber) ? htmlentities($res[$k]->CardNumber, ENT_QUOTES, 'UTF-8') : '') . "</td>";
                                echo "	<td>" . (isset($res[$k]->mobile) ? htmlentities($res[$k]->mobile, ENT_QUOTES, 'UTF-8') : '') . "</td>";
                                echo "	<td><a href='ManagePayments.php?PersonID=" . $res[$k]->PersonID . "'>پرداختها</a></td>";
                                echo "</tr>";
                            }
                            ?>
                            </tbody>
                            <!--                            <tr bgcolor="#FAFAFA">-->
                            <!--                                <td colspan="10" align="right">-->
                            <!--                                   -->
                            <!--                                </td>-->
                            <!--                            </tr>-->
                        </table>
                        <div class="dropdown-divider"></div>
                        <div class="row justify-content-center text-center">
                            <div class="col">
                                <buttton class="btn btn-danger col-3 btn-lg" onclick="javascript: ConfirmDelete();">حذف</buttton>
                            </div>
                        </div>
                    </div>
                    <?php
                    if ($PageNumber != 0) {
                        echo `<div class="card-footer">
                        <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                            <div class="btn-group" role="group">`;
                        $TotalCount = manage_persons::GetCount();
                        for ($k = 0; $k < $TotalCount / $NumberOfRec; $k++) {
                            if ($PageNumber != $k) {
                                echo "<button type='button' class='btn btn-secondary' href='javascript: ShowPage(" . ($k) . ")'>";
                                echo($k + 1);
                                echo "</button>";
                            }
                        }

                        echo `</div>
                        </div>
                    </div>`;
                    }
                ?>
                </form>
                <form target="_blank" method="post" action="Newpersons.php" id="NewRecordForm" name="NewRecordForm">
                </form>
                <form method="post" name="f2" id="f2">
                    <input type="hidden" name="PageNumber" id="PageNumber" value="0">
                    <input type="hidden" name="OrderByFieldName" id="OrderByFieldName"
                           value="<? echo $OrderByFieldName; ?>">
                    <input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
                </form>
            </div>
        </div>
    </div>
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
