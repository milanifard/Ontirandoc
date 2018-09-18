<?php
include("header.inc.php");
HTMLBegin();

$mysql = dbclass::getInstance();

/*if($_SESSION["UserID"]!="kahani")
    die("<center>در حال آماده سازی</center>");*/

$CountInList = 50;
$startPage = 0;

$strPageTable = "";

$pnumber = 1;

if (isset($_REQUEST["PageNumber"])) {
    $startPage = ($_REQUEST["PageNumber"] - 1) * $CountInList;
    $pnumber = $_REQUEST["PageNumber"];
}

if ($startPage < 0) {
    $startPage = 0;
    $pnumber = 1;
}
//*******

$strlist = "";
$rowcount = 0;

$dttemp = $mysql->Execute("SELECT ifnull(count(*),0) as cnt FROM projectmanagement.Util_GetServices ug left join projectmanagement.Util_Location ul using(LocationID) 
    left join projectmanagement.Util_SubServices us using(SubServiceID) where ug.PersonID='" . $_SESSION["PersonID"] . "' and ug.PersonType in ('PROF','STAFF') ");

if ($drtemp = $dttemp->FetchRow())
    $rowcount = $drtemp["cnt"];


$dt = $mysql->Execute("SELECT us.title,ul.LocationName,ug.Number,g2j(date(ug.RegisterDate)) as DEnter,time(ug.RegisterDate) as TimeEnter   FROM  projectmanagement.Util_GetServices ug left join  projectmanagement.Util_Location ul using(LocationID) 
    left join  projectmanagement.Util_SubServices us using(SubServiceID) where ug.PersonID='" . $_SESSION["PersonID"] . "' and ug.PersonType in ('PROF','STAFF')  order by ug.RegisterDate desc  LIMIT $startPage,$CountInList ");

$i = $startPage;
while ($dr = $dt->FetchRow()) {
    $i++;
    if ($i % 2 == 0)
        $strlist .= "<tr align='center' class=OddRow >";
    else
        $strlist .= "<tr align='center' class=EvenRow >";

    $strlist .="<td width=1%> $i</td>";
    $strlist .="<td> " . $dr["DEnter"] ." ". $dr["TimeEnter"] . "</td>";
    $strlist .="<td> " . $dr["title"] . "</td>";
    $strlist .="<td> " . $dr["LocationName"] . "</td>";
    $strlist .="<td> " . $dr["Number"] . "</td>";

    $strlist .="</tr>";
}

$adad = $rowcount / $CountInList;
//echo $adad." <br> ".$rowcount."<br>".$CountInList ;
if ($rowcount % $CountInList > 0)
    $adad++;

//echo $adad;

$strPageTable = "<table align=center border=0 width=90%  cellspan=0 collspan=0";
$strPageTable .="<tr align=center>";
$strPageTable .="<td align=center>";
for ($k = 1; $k <= $adad; $k++) {
    if ($k == $pnumber)
        $strPageTable .="<a href='#' onclick='PageChange(\"" . $k . "\");' style='color:red'>$k</a>&nbsp;";
    else
        $strPageTable .="<a href='#' onclick='PageChange(\"" . $k . "\");' style='color:blue'>$k</a>&nbsp;";
}
$strPageTable .="</td>";
$strPageTable .="</tr>";
$strPageTable .="</table>";
?>	
<form name="f1" id="f1">
    <input type="hidden" name="Action" id="Action" >
    <input type="hidden" name="ActionID" id="ActionID">
    <input type="hidden" name="PageNumber" id="PageNumber" value='<? if (isset($_REQUEST["PageNumber"]))
    echo $_REQUEST["PageNumber"]; ?>' >

    <center><font color="blue">لیست سرویس های دریافتی</font></center>
    <? echo $strPageTable; ?>
    <table align=center border=0 width=70% bgcolor="#BCD2EE" cellspan=0 collspan=0>
        <tr class="HeaderOfTable" align="center" >
            <td width="1%">ردیف</td>
            <td> زمان دریافت</td>
            <td> نوع سرویس</td>
            <td> محل دریافت</td>
            <td width="1%"> تعداد</td>
        </tr>
        <? echo $strlist ?>
    </table>

    <br>
    <br>
    <script type="text/javascript">
        //setTimeout("f1.submit();",30000);

        function PageChange(id)
        {
            f1.PageNumber.value=id;
            f1.submit();
        }

    </script>
</form>
<?php
HTMLEnd();
?>

