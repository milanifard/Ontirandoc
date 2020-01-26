<?php
/*
 صفحه  نمایش لیست درخواستهای شرکت در جلسه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-6
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/UniversitySessions.class.php");

HTMLBegin();
if(isset($_REQUEST["RejectID"]))
    manage_UniversitySessions::RejectRequest($_SESSION["PersonID"], $_REQUEST["RejectID"], $_REQUEST["description"]);
if(isset($_REQUEST["ConfirmID"]))
    manage_UniversitySessions::AcceptRequest($_SESSION["PersonID"], $_REQUEST["ConfirmID"]);
$res = manage_UniversitySessions::GetRequestedSessions($_SESSION["PersonID"]);
?>
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="table-responsive col-md-8">
            <table class="table table-striped table-bordered">
                <thead class="bg-secondary text-center font-weight-bold">
                <td colspan="13">
                    <?php echo C_SESSIONS ?>
                </td>
                </thead>
                <thead class="bg-info font-weight-bold">
                <td width="1%"><? echo C_ROW ?></td>
                <td><? echo C_MEETING_TYPE ?></td>
                <td><? echo C_SESSION_NUMBER ?></td>
                <td><? echo C_SESSION_TITLE ?></td>
                <td><? echo C_DATE ?></td>
                <td><? echo C_SESSION_LOCATION ?></td>
                <td><? echo C_START_TIME ?></td>
                <td><? echo C_DURATION ?></td>
                <td>&nbsp;</td>
                </thead>

                <?
                for($k=0; $k<count($res); $k++)
                {

                    echo "<tr>";
                    echo "<td>".($k+1)."</td>";
                    echo "	<td>".$res[$k]->SessionTypeID_Desc."</td>";
                    echo "	<td>".htmlentities($res[$k]->SessionNumber, ENT_QUOTES, 'UTF-8')."</td>";
                    echo "	<td>&nbsp;".htmlentities($res[$k]->SessionTitle, ENT_QUOTES, 'UTF-8')."</td>";
                    echo "	<td>".$res[$k]->SessionDate_Shamsi."</td>";
                    echo "	<td>".htmlentities($res[$k]->SessionLocation, ENT_QUOTES, 'UTF-8')."</td>";
                    echo "	<td>".floor($res[$k]->SessionStartTime/60).":".($res[$k]->SessionStartTime%60)."</td>";
                    echo "	<td>".floor($res[$k]->SessionDurationTime/60).":".($res[$k]->SessionDurationTime%60)."</td>";
                    echo "	<td width=1% nowrap>";
                    echo "	<form id='f_".$res[$k]->UniversitySessionID."' name='f_".$res[$k]->UniversitySessionID."' method=post>";
                    echo "	<input type=hidden name=RejectID value='".$res[$k]->UniversitySessionID."'>";
                    echo "	<button class='btn btn-success' type=button  onclick='javascript: document.location=\"RequestedSessions.php?ConfirmID=".$res[$k]->UniversitySessionID."\"'>";
                    echo C_APPROVE;
                    echo "</button> &nbsp;";
                    echo " <button class='btn btn-danger' type=button  onclick=\"javascript: if(document.f_".$res[$k]->UniversitySessionID.".description.value=='') alert('دلیل رد درخواست را وارد نمایید'); else document.f_".$res[$k]->UniversitySessionID.".submit();\">";
                    echo C_REJECTED;
                    echo "</button> ";
                    echo "	<input type=text name=description id=description>";
                    echo "	</form>";
                    echo "	</td>";
                    echo "</tr>";
                }
                ?>
                </td></tr>
            </table>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>
<script>
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
