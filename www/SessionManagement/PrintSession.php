<?php
/*
 صفحه چاپ مصوبات جلسه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-20
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/SessionDecisions.class.php");
include("classes/UniversitySessions.class.php");
include("classes/UniversitySessionsSecurity.class.php");
include("classes/SessionMembers.class.php");
HTMLBegin();

if (isset($_POST["image"]) && !empty($_POST["image"])) {
    echo '154564@@@@@@@@@';
//print_r($_REQUEST["MemberPersonID"]); 
    // Init dataURL variable
    $dataURL = $_POST["image"];
    // Extract base64 data (Get rid from the MIME & Data Type)
    $parts = explode(',', $dataURL);
    $data = $parts[1];

    // Decode Base64 data
    $dataa = base64_decode($data);
//print_r($dataa);
    // Save data as an image
    $fp = addslashes(fread(fopen($data, 'r')));
//print_r($fp);
    fwrite($fp, $data);
    fclose($fp);

}

if (isset($_REQUEST["MemberPersonID"])) {//echo "</br>";
    //echo '&&&&&&&&&';
//echo "</br>";
    echo($dataa);
//echo "</br>";
    echo($_REQUEST["MemberPersonID"]);
//echo "</br>";
    echo($_REQUEST["UniversitySessionID"]);
//echo "</br>";
    manage_UniversitySessions::SignTheDescesionFile($_REQUEST["MemberPersonID"], $_REQUEST["UniversitySessionID"], $dataa);

}


// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["UniversitySessionID"]);
$uni_session = new be_UniversitySessions();
$uni_session->LoadDataFromDatabase($_REQUEST["UniversitySessionID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
$HasRemoveAccess = true;
if ($ppc->GetPermission("Add_SessionDecisions") == "YES")
    $HasAddAccess = true;
$res = manage_SessionDecisions::GetList($_REQUEST["UniversitySessionID"]);
?>

<div class="container container-fluid  p-0">
    <div class="card">
        <div class="card-header">
            <?php echo C_PRINTSESSIONPAGE_MTH ?>
        </div>

        <div class="card-body">


    <div class="row m-0">
        <div class="col">
            <div class="row"> <?php echo C_PRINTSESSIONPAGE_TMFC ?>: <?php echo $uni_session->SessionTypeID_Desc ?>
            </div>
            <div class="row"> <?php echo C_PRINTSESSIONPAGE_TMTC ?>: <?php echo $uni_session->SessionTitle ?>
            </div>
            <div class="row"> <?php echo C_PRINTSESSIONPAGE_TMFOC ?>: <?php echo $uni_session->SessionDate_Shamsi ?>
            </div>
            <div class="row"> <?php echo C_PRINTSESSIONPAGE_TMFIFC ?>: <?php echo $uni_session->SessionNumber ?>
            </div>
            <div class="row"> <?php echo C_PRINTSESSIONPAGE_TMSIXC ?>: <?php echo floor($uni_session->SessionStartTime / 60) . ":" . ($uni_session->SessionStartTime % 60) ?>
            </div>
            <div class="row"><?php echo C_PRINTSESSIONPAGE_TMSEVENC ?>: <?php echo floor($uni_session->SessionDurationTime / 60) . ":" . ($uni_session->SessionDurationTime % 60) ?>
            </div>
        </div>
    </div>
    <div class="row">
        <table class="table table-bordered table-striped">
            <tr>
                <th scope="col"><?php echo C_PRINTSESSIONPAGE_STFIRST ?></th>
                <th scope="col"><?php echo C_PRINTSESSIONPAGE_STSEC ?></th>
                <th scope="col"><?php echo C_PRINTSESSIONPAGE_STTIR ?></th>
                <th scope="col"><?php echo C_PRINTSESSIONPAGE_STFORTH ?></th>
                <th scope="col"><?php echo C_PRINTSESSIONPAGE_STFIF ?></th>
            </tr>
            <?
            for ($k = 0; $k < count($res); $k++) {
                echo "<tr>";
                echo "	<td>" . htmlentities($res[$k]->OrderNo, ENT_QUOTES, 'UTF-8') . "</td>";
                echo "	<td>" . str_replace("\n", "<br>", htmlentities($res[$k]->SessionPreCommandDescription, ENT_QUOTES, 'UTF-8')) . "</td>";
                echo "	<td>" . str_replace("\n", "<br>", htmlentities($res[$k]->description, ENT_QUOTES, 'UTF-8')) . "</td>";
                echo "	<td>&nbsp;" . $res[$k]->ResponsiblePersonID_FullName . "</td>";
                echo "	<td nowrap>";
                if ($res[$k]->DeadlineDate_Shamsi != "date-error")
                    echo $res[$k]->DeadlineDate_Shamsi;
                else
                    echo "-";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>

    </div>
        </div>
    </div>
</div>

<div class="container container-fluid p-0 mt-3">
    <div class="card">
        <div class="card-header">
        <?php echo C_PRINTSESSIONPAGE_TTH ?>
        </div>
        <div class="card-body">
            <form id="ListForm" name="ListForm" method="post">
                <table class="table table-striped">
                    <tr>
                        <th scope="col"><?php echo C_PRINTSESSIONPAGE_TTFIRST ?></th>
                        <th scope="col"><?php echo C_PRINTSESSIONPAGE_TTSEC ?></th>
                        <th scope="col"><?php echo C_PRINTSESSIONPAGE_TTTIR ?></th>
                        <th scope="col"><?php echo C_PRINTSESSIONPAGE_TTFOR ?></th>
                        <th scope="col"><?php echo C_PRINTSESSIONPAGE_TTFIF ?></th>
                        <th scope="col"><?php echo C_PRINTSESSIONPAGE_TTSIX ?></th>
                    </tr>
                    <?php
                    $k = 0;
                    $list = manage_SessionMembers::GetList($_REQUEST["UniversitySessionID"], 0, 1000);
                    for ($i = 0; $i < count($list); $i++) {
                        $SignImg = '<img src="DisplayCanvas.php?RecId=' . $list[$i]->SessionMemberID . '" width="200"   />';

                        if ($list[$i]->PresenceType == "PRESENT") {
                            $k++;
                            echo "<tr>";
                            echo "<td>" . $k . "</td>";

                            echo "<td>
                                        <a target=\"_blank\" href=\"Signature.php?MemberPersonID=" . $list[$i]->MemberPersonID . "&UniversitySessionID=" . $_REQUEST["UniversitySessionID"] . "\">" . $list[$i]->FirstName . " " . $list[$i]->LastName . "</a>
                                    </td>";

                            echo "<td >" . floor($list[$i]->PresenceTime / 60) . ":" . ($list[$i]->PresenceTime % 60) . "</td>";
                            echo "<td >" . floor($list[$i]->TardinessTime / 60) . ":" . ($list[$i]->TardinessTime % 60) . "</td>";
                            if ($list[$i]->canvasimg != '')
                                echo "<td>" . $SignImg . "</td>";
                            else
                                echo "<td>&nbsp;</td>";
                            if ($list[$i]->SignTime_Shamsi != "date-error")
                                echo "	<td nowrap>" . $list[$i]->SignTime_Shamsi . "</td>";
                            else
                                echo "	<td>-</td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </table>

            </form>
        </div>
    </div>
</div>
<div class="container container-fluid p-0 mt-3">

    <div class="card">
        <div class="card-header">
            <?php echo C_PRINTSESSIONPAGE_FTH ?>
        </div>
        <div class="card-body">
                <?php
                $k = 0;
                $list = manage_SessionMembers::GetList($_REQUEST["UniversitySessionID"], 0, 1000);
                for ($i = 0; $i < count($list); $i++) {
                    if ($list[$i]->PresenceType == "ABSENT") {
                        $k++;
                        echo $k . "- ";
                        echo $list[$i]->FirstName . " " . $list[$i]->LastName . " ";
                    }
                }
                ?>
        </div>
    </div>
</div>
<script>
    setInterval(function () {

        var xmlhttp;
        if (window.XMLHttpRequest) {
            // code for IE7 , Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.open("POST", "header.inc.php", true);
        xmlhttp.send();

    }, 60000);
</script>
</html>
