<?php
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', "on");
/*
 صفحه جستجوی پیام
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-21
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/PrivateMessageFollows.class.php");
HTMLBegin();

$SearchResult = "";
$Item_FromPersonID = $Item_ToPersonID = "0";
$Item_MessageTitle = $Item_MessageBody = $FromPerson = $ToPerson = "";
$StartTime_YEAR = $StartTime_MONTH = $StartTime_DAY = "";
$EndTime_YEAR = $EndTime_MONTH = $EndTime_DAY = "";
if (isset($_REQUEST["Search"])) {
    $Item_MessageTitle = $_REQUEST["Item_MessageTitle"];
    $Item_MessageBody = $_REQUEST["Item_MessageBody"];
    $Item_FromPersonID = $_REQUEST["Item_FromPersonID"];
    $Item_ToPersonID = $_REQUEST["Item_ToPersonID"];

    $mysql->Prepare("select * from projectmanagement.persons where PersonID=?");
    $res = $mysql->ExecuteStatement(array($Item_FromPersonID));
    $rec = $res->fetch();
    $FromPerson = $rec["pfname"] . " " . $rec["plname"];

    $mysql->Prepare("select * from projectmanagement.persons where PersonID=?");
    $res = $mysql->ExecuteStatement(array($Item_ToPersonID));
    $rec = $res->fetch();
    $ToPerson = $rec["pfname"] . " " . $rec["plname"];

    $Item_StartTime = $Item_EndTime = "";

    if (!empty($_REQUEST["from_date_input"])) {
        if (UI_LANGUAGE == "FA") {
            $Item_StartTime = SharedClass::jalali_to_gregorian(
                substr($_REQUEST["from_date_input"], 0, 4),
                substr($_REQUEST["from_date_input"], 5, 2)
                , substr($_REQUEST["from_date_input"], 8, 2), '-');
        } else
            $Item_StartTime = substr($_REQUEST["from_date_input"], 0, 4) . '-'
                . substr($_REQUEST["from_date_input"], 5, 2) . '-'
                . substr($_REQUEST["from_date_input"], 8, 2);
    }
    if (!empty($_REQUEST["to_date_input"])) {
        if (UI_LANGUAGE == "FA") {
            $Item_EndTime = SharedClass::jalali_to_gregorian(
                substr($_REQUEST["to_date_input"], 0, 4),
                substr($_REQUEST["to_date_input"], 5, 2)
                , substr($_REQUEST["to_date_input"], 8, 2), '-');
        } else
            $Item_EndTime = substr($_REQUEST["to_date_input"], 0, 4) . '-'
                . substr($_REQUEST["to_date_input"], 5, 2) . '-'
                . substr($_REQUEST["to_date_input"], 8, 2);
    }

    $mysql = pdodb::getInstance();
    $query = "select PrivateMessages.PrivateMessageID, PrivateMessageFollowID, MessageTitle, MessageBody, comment, ReferStatus,
  concat(p1.pfname, ' ', p1.plname) as FromPerson,
  concat(p2.pfname, ' ', p2.plname) as ToPerson,
  concat(projectmanagement.g2j(ReferTime), substr(ReferTime, 11, 10)) as gReferTime
  from projectmanagement.PrivateMessages
  JOIN projectmanagement.PrivateMessageFollows using (PrivateMessageID)
  left JOIN projectmanagement.persons p1 on (p1.PersonID=PrivateMessageFollows.FromPersonID)
  left JOIN projectmanagement.persons p2 on (p2.PersonID=PrivateMessageFollows.ToPersonID)
  where (FromPersonID='" . $_SESSION["PersonID"] . "' or ToPersonID='" . $_SESSION["PersonID"] . "') 
  and MessageTitle like ? and MessageBody like ? 
  ";
    if ($_REQUEST["Item_FromPersonID"] != "0")
        $query .= " and FromPersonID=? ";
    if ($_REQUEST["Item_ToPersonID"] != "0")
        $query .= " and ToPersonID=? ";
    if (!empty($_REQUEST["from_date_input"]))
        $query .= " and ReferTime>='" . $Item_StartTime . " 00:00:00' ";
    if (!empty($_REQUEST["to_date_input"]))
        $query .= " and ReferTime<='" . $Item_EndTime . " 23:59:59' ";

    $query .= " order by ReferTime DESC ";
    //echo $query;
    $mysql->Prepare($query);

    $ValueListArray = array();
    array_push($ValueListArray, "%" . $_REQUEST["Item_MessageTitle"] . "%");
    array_push($ValueListArray, "%" . $_REQUEST["Item_MessageBody"] . "%");
    if ($_REQUEST["Item_FromPersonID"] != "0")
        array_push($ValueListArray, $Item_FromPersonID);
    if ($_REQUEST["Item_ToPersonID"] != "0")
        array_push($ValueListArray, $Item_ToPersonID);
    $res = $mysql->ExecuteStatement($ValueListArray);
    $i = 0;
    while ($rec = $res->fetch()) {
        $i++;
        $SearchResult .= "<tr>";
        $SearchResult .= "<td class='text-center'>";
        $SearchResult .= "<a target=_blank href=\"ShowMessage.php?BackPage=SearchMessage&MessageFollowID=" . $rec["PrivateMessageFollowID"] . "\">";
        $SearchResult .= $i;
        $SearchResult .= "</a>";
        $SearchResult .= "</td>";
        $SearchResult .= "<td class='text-center'>";
        if ($rec["ReferStatus"] == "ARCHIVE")
            $SearchResult .= " <i class=\"input-group-text
                                    fa fa-trash\"
                                       id=\"to_date\"></i>";
        $SearchResult .= $rec["MessageTitle"] . "</td>";
        $SearchResult .= "<td class='text-center' nowrap>" . $rec["FromPerson"] . "</td>";
        $SearchResult .= "<td class='text-center' nowrap>" . $rec["ToPerson"] . "</td>";
        $SearchResult .= "<td class='text-center' nowrap>" . $rec["gReferTime"] . "</td>";
        $SearchResult .= "<td class='text-center'>" . $rec["comment"] . "</td>";

        $SearchResult .= "</tr>";
    }
    if ($i > 0) {
        $Header = "<table class=\"table table-striped\">";
        $Header .= "<thead>
                <td class='text-center'>ردیف</td>
                <td class='text-center'>عنوان</td>
                <td class='text-center'>فرستنده</td>
                <td class='text-center' >گیرنده</td>
                <td  class='text-center'>زمان ارسال</td>
                <td class='text-center'>شرح ارجاع</td>
                </thead>";
        $SearchResult = $Header . $SearchResult;
    }
}

?>

<!--    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">-->
<script src="../node_modules/jquery/dist/jquery.slim.min.js" type="text/javascript">
</script>
<script src="../node_modules/popper.js/dist/umd/popper.min.js" type="text/javascript">
</script>
<script src="../node_modules/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
<link rel="stylesheet"
      href="../node_modules/md.bootstrappersiandatetimepicker/dist/jquery.md.bootstrap.datetimepicker.style.css"/>
<link rel="stylesheet" href="css/fontawesome.min.css">

<form method="post" id="f1" name="f1" enctype="multipart/form-data">
    <div class="container">
        <div class="row border border-light shadow-sm" style="margin-top: 3% !important;">
            <div class="col-12">
                <div class="row">
                    <p class="col-12 bg-info text-dark text-center  ">
                        <?php
                        echo C_SEARCH_MESSAGE;
                        ?>
                    </p>

                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <span>
                                <?php
                                echo C_TITLE;
                                ?> </span>
                            <!--                            <span class="text-danger text-center ">-->
                            <!--                            * </span>-->
                            <input type="text" class="form-control"
                                   name="Item_MessageTitle" id="Item_MessageTitle"
                                   value='<? echo $Item_MessageTitle ?>' maxlength="1000" style="margin-top: 3%;">
                        </div>
                    </div>


                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <span>
                                <?php
                                echo C_PART_OF_TEXT;
                                ?> </span>
                            <input type="text" class="form-control" name="Item_MessageBody" id="Item_MessageBody"
                                   value='<? echo $Item_MessageBody ?>' maxlength="1000" style="margin-top: 2%;">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3" id="tr_ToPersonID" name="tr_ToPersonID">
                        <div class="form-group">
                            <span>
                                <?php
                                echo C_SENDER_NAME;
                                ?> </span>
                            <br>
                            <input type=hidden name="Item_FromPersonID" id="Item_FromPersonID"
                                   value="<? echo $Item_FromPersonID ?>">
                            <span id="Span_FromPersonID_FullName"
                                  name="Span_FromPersonID_FullName"><? echo $FromPerson ?></span>
                            <a href='#'
                               onclick='javascript: window.open("SelectStaff.php?InputName=Item_FromPersonID&SpanName=Span_FromPersonID_FullName");'>[<?php echo C_CHOOSE; ?>
                                ]</a>
                        </div>
                    </div>
                    <div class="col-3" id="tr_ToPersonID" name="tr_ToPersonID">
                        <div class="form-group">
                            <span>
                                <?php
                                echo C_RECEIVER_NAME;
                                ?> </span>
                            <br>
                            <input type=hidden name="Item_ToPersonID" id="Item_ToPersonID"
                                   value="<? echo $Item_ToPersonID ?>">
                            <span id="Span_ToPersonID_FullName"
                                  name="Span_ToPersonID_FullName">
                                <? echo $ToPerson ?></span>
                            <a href='#'
                               onclick='javascript: window.open("SelectStaff.php?InputName=Item_ToPersonID&SpanName=Span_ToPersonID_FullName");'>[<?php echo C_CHOOSE; ?>
                                ]</a>

                        </div>
                    </div>
                </div>
                <div class="row" style="padding-bottom: 3%;">
                    <div class="col-3" id="tr_ToPersonID" name="tr_ToPersonID">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <i class="input-group-text
                                    fa fa-calendar-day"
                                       id="from_date" style="cursor: pointer; font-size: 1.3em !important;"></i>
                                </div>
                                <input type="text" id="from_date_input" name="from_date_input"
                                       class="form-control " placeholder='<?php echo C_FROM_DATE; ?>'>
                            </div>
                        </div>
                    </div>
                    <div class="col-3" id="tr_ToPersonID" name="tr_ToPersonID">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <i class="input-group-text
                                    fa fa-calendar-day"
                                       id="to_date" style="cursor: pointer; font-size: 1.3em !important;"></i>
                                </div>
                                <input type="text" id="to_date_input" name="to_date_input"
                                       class="form-control " placeholder='<?php echo C_TO_DATE; ?>'
                                       aria-label="to_date"
                                       aria-describedby="to_date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 bg-info text-dark text-center ">
                        <div class="btn">
                            <input type="button" class="btn btn-dark" onclick="ValidateForm();"
                                   value=<?php
                            echo C_SEARCH;
                            ?>>
                        </div>
                        <input type="hidden" name="Search" id="Search" value="1">
                    </div>
                </div>
            </div>
        </div>
</form>

<script src="../node_modules/md.bootstrappersiandatetimepicker/dist/jquery.md.bootstrap.datetimepicker.js"
        type="text/javascript"></script>
<script type="text/javascript">
    let gregorian = ('<?php echo UI_LANGUAGE ?>' == "EN");

    $('#from_date').MdPersianDateTimePicker({
        targetTextSelector: '#from_date_input',
        fromDate: true,
        isGregorian: gregorian,
        groupId: 'rangeSelector1',
        dateFormat: 'yyyy/MM/dd',
        textFormat: 'yyyy/MM/dd',
    });
    $('#to_date').MdPersianDateTimePicker({
        targetTextSelector: '#to_date_input',
        toDate: true,
        isGregorian: gregorian,
        groupId: 'rangeSelector1',
        dateFormat: 'yyyy/MM/dd',
        textFormat: 'yyyy/MM/dd',
    });

</script>

<script>


    var
        persianNumbers = [/۰/g, /۱/g, /۲/g, /۳/g, /۴/g, /۵/g, /۶/g, /۷/g, /۸/g, /۹/g],
        arabicNumbers = [/٠/g, /١/g, /٢/g, /٣/g, /٤/g, /٥/g, /٦/g, /٧/g, /٨/g, /٩/g],
        fixNumbers = function (str) {
            if (typeof str === 'string') {
                for (var i = 0; i < 10; i++) {
                    str = str.replace(persianNumbers[i], i).replace(arabicNumbers[i], i);
                }
            }
            return str;
        };

    function ValidateForm() {
        if ('<?php echo UI_LANGUAGE ?>' === "FA") {
            let from_date = document.getElementById('from_date_input');
            let to_date = document.getElementById('to_date_input')
            from_date.value = fixNumbers(from_date.value);
            to_date.value = fixNumbers(to_date.value);
        }
        document.f1.submit();
    }
</script>
<?
echo $SearchResult;
?>
</html>