<?php
/*
 گزارش کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-07-04
*/
include("header.inc.php");
//include("../organization/classes/ChartServices.class.php");
//include("../staff/PAS/PAS_shared_utils.php");

function ShowTimeInHourAndMinuteOrEmpty($TotalMinutes)
{
    $h = (int)($TotalMinutes / 60);
    $m = $TotalMinutes % 60;
    if ($h < 10)
        $h = "0" . $h;
    if ($m < 10)
        $m = "0" . $m;
    if ($h . ":" . $m != "00:00")
        return $h . ":" . $m;
    else
        return "-";
}


HTMLBegin();
$mysql = pdodb::getInstance();
$now = date("Ymd");
$yy = substr($now, 0, 4);
$mm = substr($now, 4, 2);
$dd = substr($now, 6, 2);
$CurrentDay = $yy . "/" . $mm . "/" . $dd;
list($dd, $mm, $yy) = ConvertX2SDate($dd, $mm, $yy);
$yy = substr($yy, 2, 2);
$CurYear = 1300 + $yy;
if (isset($_REQUEST["SelectedYear"])) {
    $SelectedYear = $_REQUEST["SelectedYear"];
    $SelectedMonth = $_REQUEST["SelectedMonth"];
} else {
    $SelectedYear = $CurYear;
    $SelectedMonth = $mm;
}
?>
<!--<form id="SearchForm" name="SearchForm" method=post>-->
<!--    <br>-->
<!--    <table width="90%" align="center" border="1" cellspacing="0">-->
<!--        <tr id='SearchTr'>-->
<!--            <td>-->
<!--                <table width="100%" align="center" border="0" cellspacing="0">-->
<!--                    <tr>-->
<!--                        <td>-->
<!--                            سال:-->
<!--                            <input size=4 maxlength=4 type=text name=SelectedYear id=SelectedYear value='--><?php //echo $SelectedYear ?><!--'> ماه:-->
<!--                            <input size=2 maxlength=2 type=text name=SelectedMonth id=SelectedMonth value='--><?php //echo $SelectedMonth ?><!--'>-->
<!--                        </td>-->
<!--                    </tr>-->
<!--                    <tr class="HeaderOfTable">-->
<!--                        <td align="center"><input type="submit" value="نمایش گزارش اقدامات کاری"></td>-->
<!--                    </tr>-->
<!--                </table>-->
<!--            </td>-->
<!--        </tr>-->
<!--    </table>-->
<!--</form>-->
<div class="container">
    <form id="SearchForm" name="SearchForm" method=post>
        <br>
        <div class="form-group">
            <div class="row">
                <div class="col-10 text-center" style="border: 2px solid black">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="row">
                                                <div class="col-3 text-center  mt-3"><p><?php echo C_YEAR ?></p></div>
                                                <div class="col-9  mt-3">
                                                    <select class="form-control" name="SelectedYear" id="SelectedYear">
                                                        <?php
                                                        for ($x = 1398; $x > 1357; $x--) {
                                                            if ($x == $SelectedYear) {
                                                                ?>
                                                                <option selected="selected"><?php echo $x ?></option>
                                                                <?php
                                                            } else { ?>
                                                                <option><?php echo $x ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="row">
                                                <div class="col-3 text-center  mt-3"><p><?php echo C_MONTH ?></p></div>
                                                <div class="col-9  mt-3">
                                                    <select class="form-control" name="SelectedMonth"
                                                            id="SelectedMonth">
                                                        <?php
                                                        for ($x = 1; $x < 13; $x++) {
                                                            if ($x == $SelectedMonth) {
                                                                ?>
                                                                <option selected="selected"><?php echo $x ?></option>
                                                                <?php
                                                            } else { ?>
                                                                <option><?php echo $x ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row HeaderOfTable mt-2">
                                        <div class="col-12 text-center">
                                            <input type="submit" class="btn btn-success m-2"
                                                   value=<?php echo C_SHOW_REPORT_ACTIONS ?>>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>
</div>
<?
if (isset($_REQUEST["SelectedYear"])) {
    $res = $mysql->Execute("select projectmanagement.j2g('" . $_REQUEST["SelectedYear"] . "', '" . $_REQUEST["SelectedMonth"] . "', '01') as gDate");
    $rec = $res->fetch();
    //echo $rec["gDate"];
    $res = $mysql->Execute("select HourlyPrice from projectmanagement.PersonAgreements where FromDate<='" . $rec["gDate"] . "' and ToDate>='" . $rec["gDate"] . "' and PersonID='" . $_SESSION["PersonID"] . "'");
    $HourlyPrice = 0;
    if ($rec = $res->fetch()) {
        $HourlyPrice = $rec["HourlyPrice"];
    }
    //echo $HorlyPrice;
    ?>
    <br>
    <!--    <table width=80% align=center border=1 cellspacing=0 cellpadding=3>-->
    <!--        <tr class=HeaderOfTable>-->
    <!--            <td colspan=19 align=center>-->
    <!--                گزارش زمان مصرفی-->
    <!--            </td>-->
    <!--        </tr>-->
    <!--        <tr bgcolor=#cccccc>-->
    <!--            <td width=5%>-->
    <!--                تاریخ-->
    <!--            </td>-->
    <!--            <td width=80%>-->
    <!--                فعالیت-->
    <!--            </td>-->
    <!--            <td width=20%>-->
    <!--                زمان-->
    <!--            </td>-->
    <!---->
    <!--        </tr>-->
    <div class="container">
        <div class="row">
            <div class="col-10 text-center" style="border: 2px solid black">
                <div class="row HeaderOfTable">
                    <div class="col-12 text-center">
                        <p class="text-center">
                            <?php
                            echo C_USAGE_TIME_REPORT;
                            ?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-1 border border-dark text-center">
                        <p>
                            <?php
                            echo C_DATE;
                            ?>
                        </p>
                    </div>
                    <div class="col-8 border border-dark text-center">
                        <p>
                            <?php
                            echo C_ACTIVITY;
                            ?>
                        </p>
                    </div>
                    <div class="col-3 border border-dark text-center">
                        <p>
                            <?php
                            echo C_TIME;
                            ?>
                        </p>
                    </div>
                </div>

                <?php
                if (strlen($SelectedMonth) < 2)
                    $SelectedMonth = "0" . $SelectedMonth;
                $EndDay = 31;
                if ($SelectedMonth > 6) {
                    if ($SelectedMonth == 12)
                        $EndDay = 29;
                    else
                        $EndDay = 30;
                }

                $query = "	select group_concat('<b>پروژه: </b>', IF(projects.title is null, '-', projects.title), ' - <b>کار: </b>', IF(ProjectTasks.title is null, '-', ProjectTasks.title), ' - <b>اقدام: </b> ', ActivityDescription, '(',floor(ActivityLength/60), ':', MOD(ActivityLength,60), ')<br>') as title, 
			g2j(ActivityDate) as gDate, sum(ActivityLength) as tl
			from projectmanagement.ProjectTaskActivities
			JOIN projectmanagement.ProjectTasks using (ProjectTaskID)
			LEFT JOIN projectmanagement.projects using (ProjectID)
			JOIN projectmanagement.persons on (persons.PersonID=ProjectTaskActivities.CreatorID)
			where ProjectTasks.DeleteFlag='NO' 
			and ActivityDate between projectmanagement.j2g('" . $_REQUEST["SelectedYear"] . "','" . $_REQUEST["SelectedMonth"] . "','01') 
			and projectmanagement.j2g('" . $_REQUEST["SelectedYear"] . "','" . $_REQUEST["SelectedMonth"] . "','" . $EndDay . "') 
			and ProjectTaskActivities.CreatorID='" . $_SESSION["PersonID"] . "'
			group by g2j(ActivityDate)
			order by g2j(ActivityDate)
			";

                $res = $mysql->Execute($query);
                $total = 0;
                while ($rec = $res->fetch()) {
                    $total += $rec["tl"];
                    echo "</tr>";
                    echo "<td nowrap>" . $rec["gDate"] . "</td>";
                    echo "<td>" . $rec["title"] . "</td>";
                    //echo "<td>".$rec["title"]."</td>";

                    //echo "<td>&nbsp;".$rec["ActivityDescription"]."</td>";
                    echo "<td>" . ShowTimeInHourAndMinuteOrEmpty($rec["tl"]) . "</td>";
                    echo "</tr>";
                }
                $payment = round(($total * $HourlyPrice) / 60, 0);
                //            echo "<tr bgcolor=#cccccc>
                //                        <td colspan=2><b>م
                //                    مجموع</td>
                //                        <td>" . ShowTimeInHourAndMinuteOrEmpty($total) . " <br>(" . number_format($payment) . " ریال)
                //                        </td>
                //                    </tr>";
                //

                echo "<div class='row'>
                        <div class='col-9'>
                            <div class='row border border-dark'>
                                <div class='col-2'>
                                    <p>" .
                    C_TOTAL
                    .
                    "</p>
                                </div>
                                <div class='col-10'>"
                    .
                    ShowTimeInHourAndMinuteOrEmpty($total)
                    .
                    "</div>
                            </div>
                        </div>
                        <div class='col-3'>
                            <div class='row border border-dark'>
                                <div class='col-2'>
                                    <p>"
                    .
                    C_RIAL
                    .
                    "</p>
                                </div>
                                <div class='col-10'>"
                    .
                    number_format($payment)
                    .
                    "</div>
                            </div>
                        </div>
                  </div>
              </div>";

                ?>
            </div>
        </div>
    </div>

    <!--    </table>-->
<?php } ?>
</html>
