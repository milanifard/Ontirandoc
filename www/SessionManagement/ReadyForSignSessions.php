<?php 
/*
 صفحه  نمایش لیست صورتجلسه های آماده امضا
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-6
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/UniversitySession.class.php");

HTMLBegin();


/*$mysql = pdodb::getInstance();
$query="UPDATE `sessionmanagement`.`SessionMembers` SET `SignTime`='0000-00-00 00:00:00', `PresenceType`='PRESENT' WHERE `SessionMemberID`='17324'";
$mysql->Prepare($query);
$res = $mysql->ExecuteStatement(array());*/

$PersonID=$_SESSION['PersonID'];
/*if ($PersonID==401366873)
	$_SESSION['PersonID']=401371014;*/

if(isset($_REQUEST["UniversitySessionID"]))
	manage_UniversitySessions::SignTheDescesionFile($_SESSION["PersonID"], $_REQUEST["UniversitySessionID"], $_REQUEST["description"]);
$res = manage_UniversitySessions::GetReadyForSignSessions($_SESSION["PersonID"]); 
/*print_r($_SESSION['PersonID']);*/
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
	<td width=8%>صورتجلسه</td>
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
	//echo "	<td><a href='DownloadFile.php?FileType=SessionDescision&RecID=".$res[$k]->UniversitySessionID."'><img src='images/Download.gif'></a></td>";
	echo "	<td><a href='PrintSessionNew.php?UniversitySessionID=".$res[$k]->UniversitySessionID."'>مصوبات جلسه</a></td>";
	echo "	<td width=1% nowrap>";
	echo "	<form id='f_".$res[$k]->UniversitySessionID."' name='f_".$res[$k]->UniversitySessionID."' method=post>";
	echo "	<input type=hidden name=UniversitySessionID id=UniversitySessionID value='".$res[$k]->UniversitySessionID."'>";
	echo "	شرح امضا: <input type=text name=description id=description>";
	echo "	<input type=button value='امضا' onclick='document.f_".$res[$k]->UniversitySessionID.".submit();'> ";
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
