<?php
include("header.inc.php");
include("PAS_shared_utils.php");

HTMLBegin();
$mysql = pdodb::getInstance();
$FromRec = 0;
if (isset($_REQUEST["FromRec"]))
    $FromRec = $_REQUEST["FromRec"];
$ItemsCount = 15;
$query = "select count(ATS) from projectmanagement.SysAudit where UserID='" . $_SESSION["UserID"] . "' ";
$res = $mysql->Execute($query);
$TotalCount = 0;
if ($rec = $res->fetch())
    $TotalCount = $rec[0];
$query = "select *, concat(g2j(ATS), ' ', substr(ATS, 12,5)) as gATS from projectmanagement.SysAudit where UserID='" . $_SESSION["UserID"] . "' order by ATS DESC limit $FromRec, $ItemsCount";
$list = "";
$res = $mysql->Execute($query);
$i = 0;
while ($rec = $res->fetch()) {
    $i++;
    $list .= "<tr>";
    $list .= "<td>" . ($FromRec + $i) . "</td>";
    $list .= "<td>" . $rec["ActionDesc"] . "</td>";
    $list .= "<td nowrap>" . $rec["gATS"] . "</td>";
    $list .= "</tr>";
}
?>
<br>
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header text-info text-center" style="font-size: 1.3em; font-weight: bold;"><? echo C_MYACTIONS ?> <i class="fas fa-list-alt"></i></div>
                <div class="card-body text-center">
                    <form method="post" id=f1 name=f1>
                        <input type=hidden name=FromRec id=FromRec value='<?php echo $FromRec ?>'>
                        <table class="table table-bordered table-hover table-striped" cellspacing="0" align=center width=100%>
                            <thead class="table-info">
                            <tr>
                                <td scope="col" width=1%><? echo C_ROW ?></td>
                                <td scope="col"><? echo C_MA_ACTION ?></td>
                                <td scope="col"><? echo C_MA_DONE_DATE ?></td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php echo $list ?>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="card-footer">
                    <? echo C_MA_TOTAL_FIND ?> : <?php echo $TotalCount; ?>
                    <br>
                    <? echo C_PAGE ?> :
                    <?php
                    for ($PageNumber = 1; $PageNumber <= ($TotalCount / $ItemsCount) + 1; $PageNumber++) {
                        if (($PageNumber - 1) * $ItemsCount == $FromRec)
                            echo "<b>";
                        else
                            echo "<a href='#' onclick='javascript: GoPage(" . ($PageNumber - 1) * $ItemsCount . ");'>";
                        echo $PageNumber;
                        if (($PageNumber - 1) * $ItemsCount == $FromRec)
                            echo "</b>";
                        else
                            echo "</a>";
                        echo "&nbsp; ";
                        if ($PageNumber % 30 == 0)
                            echo "<br>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function GoPage(FromRec) {
        document.f1.FromRec.value = FromRec;
        f1.submit();
    }
</script>

</body>
</html>