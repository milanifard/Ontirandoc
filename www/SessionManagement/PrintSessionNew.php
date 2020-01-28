<?php 
/*
 صفحه چاپ مصوبات جلسه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-20
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/SessionDecisions.class.php");
include_once("classes/UniversitySessions.class.php");
include_once("classes/UniversitySessionsSecurity.class.php");
include_once("classes/SessionMembers.class.php");
HTMLBegin();

// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["UniversitySessionID"]);
$uni_session = new be_UniversitySessions();
$uni_session->LoadDataFromDatabase($_REQUEST["UniversitySessionID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
$HasRemoveAccess = true;
if($ppc->GetPermission("Add_SessionDecisions")=="YES")
	$HasAddAccess = true;
$res = manage_SessionDecisions::GetList($_REQUEST["UniversitySessionID"]); 
?>
<br>
<table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="9">
	جلسه: <?php echo $uni_session->SessionTypeID_Desc ?><br>
	عنوان: <?php echo $uni_session->SessionTitle ?><br>
	تاریخ: <?php echo $uni_session->SessionDate_Shamsi ?><br>
	شماره: <?php echo $uni_session->SessionNumber ?><br>
	ساعت تشکیل: <?php echo floor($uni_session->SessionStartTime/60).":".($uni_session->SessionStartTime%60) ?> مدت جلسه: <?php echo floor($uni_session->SessionDurationTime/60).":".($uni_session->SessionDurationTime%60) ?><br>
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width=1%>ردیف</td>
	<td>دستور کار</td>
	<td>مصوبه</td>
	<td width=10% nowrap>مسوول پیگیری</td>
	<td width=1% nowrap>مهلت اقدام</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "	<td>".htmlentities($res[$k]->OrderNo, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".str_replace("\n", "<br>", htmlentities($res[$k]->SessionPreCommandDescription, ENT_QUOTES, 'UTF-8'))."</td>";
	echo "	<td>".str_replace("\n", "<br>", htmlentities($res[$k]->description, ENT_QUOTES, 'UTF-8'))."</td>";
	echo "	<td>&nbsp;".$res[$k]->ResponsiblePersonID_FullName."</td>";
	echo "	<td nowrap>";
	if($res[$k]->DeadlineDate_Shamsi!="date-error")
		echo $res[$k]->DeadlineDate_Shamsi;
	else
		echo "-";
	echo "</td>";
	echo "</tr>";
}
?>
</table>
<br>
<form id="ListForm" name="ListForm" method="post"><table width="90%" align="center" border="1" cellspacing="0" cellpadding=10>
<tr bgcolor=#cccccc>
	<td colspan=5>حاضرین جلسه</td>
</tr>
<tr bgcolor=#cccccc>
	<td width=1%>ردیف</td><td width=20%>نام و نام خانوادگی<td width=1% nowrap>حضور</td><td width=1%>تاخیر</td><td>امضا</td>
</tr>
<?php
	$k = 0;
	$list = manage_SessionMembers::GetList($_REQUEST["UniversitySessionID"], 0, 1000);
	for($i=0; $i<count($list); $i++)
	{$k++;
			echo "<tr>";
			echo "<td width=1%>".$k."</td>";

                        echo "<td>".$list[$i]->FirstName." ".$list[$i]->LastName."</td>";
			echo "<td nowrap>".floor($list[$i]->PresenceTime/60).":".($list[$i]->PresenceTime%60)."</td>";
			echo "<td nowrap>".floor($list[$i]->TardinessTime/60).":".($list[$i]->TardinessTime%60)."</td>";

			if($list[$i]->SignTime_Shamsi!="date-error")
		        echo "	<td nowrap>".$list[$i]->SignTime_Shamsi."</td>";
	                else
		        echo "	<td>-</td>";

			echo "</tr>"; 
		
	}
?>
</table>
</form>
<br>
<table width="90%" align="center" border="1" cellspacing="0" cellpadding=10>
<tr bgcolor=#cccccc>
	<td>
	<b>
	غایبین جلسه: 
	</b>
<?php
	$k = 0;
	$list = manage_SessionMembers::GetList($_REQUEST["UniversitySessionID"], 0, 1000);
	for($i=0; $i<count($list); $i++)
	{
		if($list[$i]->PresenceType=="ABSENT")
		{
			$k++;
			echo $k."- ";
			echo $list[$i]->FirstName." ".$list[$i]->LastName." ";
		}
	}
?>
	</td>
</tr>
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