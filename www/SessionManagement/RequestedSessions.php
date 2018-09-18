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
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="13">
	جلسات
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%">ردیف</td>
	<td>نوع جلسه</td>
	<td>شماره جلسه</td>
	<td>عنوان جلسه</td>
	<td>تاریخ تشکیل</td>
	<td>محل تشکیل</td>
	<td>زمان شروع</td>
	<td>مدت جلسه</td>
	<td>&nbsp;</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
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
	echo "	<input type=button value='تایید' onclick='javascript: document.location=\"RequestedSessions.php?ConfirmID=".$res[$k]->UniversitySessionID."\"'> &nbsp;";
	echo "	<input type=button value='رد به دلیل: ' onclick=\"javascript: if(document.f_".$res[$k]->UniversitySessionID.".description.value=='') alert('دلیل رد درخواست را وارد نمایید'); else document.f_".$res[$k]->UniversitySessionID.".submit();\"> ";
	echo "	<input type=text name=description id=description>";
	echo "	</form>"; 
	echo "	</td>";
	echo "</tr>";
}
?>
</td></tr>
</table>
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
