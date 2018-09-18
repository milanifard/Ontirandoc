<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : دستور کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-1
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/SessionPreCommands.class.php");
include ("classes/UniversitySessions.class.php");
include("classes/UniversitySessionsSecurity.class.php");
ini_set('display_errors','off');

HTMLBegin();

/*if($_SESSION["UserID"] == "gholami-a")
{
	$mysql = pdodb::getInstance();
	$query="update sessionmanagement.SessionPreCommands set description='93/09/24بهبود سامانه مدیریت جلسات'  where UniversitySessionID=930 and ResponsiblePersonID=705178 and SessionPreCommandID=8096";
	$res=$mysql->Execute($query);
	echo $query;
	die();
}*/




//echo $_REQUEST["SessionDate_DAY"];
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["UniversitySessionID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
$HasRemoveAccess = true;
if($ppc->GetPermission("Add_SessionPreCommands")=="YES")
	$HasAddAccess = true;
$RemoveType = $ppc->GetPermission("Remove_SessionPreCommands");
$UpdateType = $ppc->GetPermission("Update_SessionPreCommands");
//echo $UpdateType;
$OrderBy = "SessionPreCommands.priority";
if(isset($_REQUEST["OrderBy"]))
	$OrderBy = $_REQUEST["OrderBy"];

$res = manage_SessionPreCommands::GetList($_REQUEST["UniversitySessionID"], $OrderBy); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->SessionPreCommandID])) 
	{
		if($RemoveType=="PUBLIC" || ($RemoveType=="PRIVATE" && $res[$k]->CreatorPersonID==$_SESSION["PersonID"]))
		{
			manage_SessionPreCommands::Remove($res[$k]->SessionPreCommandID, $OrderBy); 
			$SomeItemsRemoved = true;
		}
	}
}
if($SomeItemsRemoved)
	$res = manage_SessionPreCommands::GetList($_REQUEST["UniversitySessionID"]); 
echo manage_UniversitySessions::ShowSummary($_REQUEST["UniversitySessionID"]);
echo manage_UniversitySessions::ShowTabs($_REQUEST["UniversitySessionID"], "ManageSessionPreCommands");
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_UniversitySessionID" name="Item_UniversitySessionID" value="<? echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="9">
	دستور کار
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<!--<td width=1% nowrap><a href='ManageSessionPreCommands.php?UniversitySessionID=<?php echo $_REQUEST["UniversitySessionID"] ?>&OrderBy=OrderNo'>ردیف</a></td>-->
         <td width=1% nowrap>ردیف</td>
	<td width="2%">ویرایش</td>
	<td>شرح</td>
	<td width=10% nowrap><a href='ManageSessionPreCommands.php?UniversitySessionID=<?php echo $_REQUEST["UniversitySessionID"] ?>&OrderBy=persons4.plname,OrderNo'>مسوول پیگیری</a></td>
	<!--<td width=1% nowrap>تکرار در دستورکار بعد</td>-->

	<td><a href='ManageSessionPreCommands.php?UniversitySessionID=<?php echo $_REQUEST["UniversitySessionID"] ?>&OrderBy=priority,OrderNo'>اولویت
</a></td>
	<td><a href='ManageSessionPreCommands.php?UniversitySessionID=<?php echo $_REQUEST["UniversitySessionID"] ?>&OrderBy=DeadLine,OrderNo'>
                                                                                                                                                                                                                                   تاریخ مهلت
	</a></td>
	<td>تاریخچه</td>




	<td width=1%>ضمیمه</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	if($RemoveType=="PUBLIC" || ($RemoveType=="PRIVATE" && $res[$k]->CreatorPersonID==$_SESSION["PersonID"]))
		echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->SessionPreCommandID."\">";
	else
		echo " ";
	echo "</td>";
	echo "	<td>".htmlentities($res[$k]->OrderNo, ENT_QUOTES, 'UTF-8')."</td>";	
	echo "	<td>";
	echo "<a target=\"_blank\" href=\"NewSessionPreCommands.php?UpdateID=".$res[$k]->SessionPreCommandID."\">";
	if($UpdateType=="PUBLIC" || ($UpdateType=="PRIVATE" && $res[$k]->CreatorPersonID==$_SESSION["PersonID"]))
			echo "<img src='images/edit.gif' title='ویرایش'>";
	else
		echo "<img src='images/read.gif' title='مشاهده'>";
	echo "</a></td>";
	
		
	$description=htmlentities(str_replace('\r\n', '<br>', $res[$k]->description), ENT_QUOTES, 'UTF-8');
	$ActReg=htmlentities(str_replace('\r\n', '<br>', $res[$k]->ActReg), ENT_QUOTES, 'UTF-8');
		echo "<td>";		
		if ($description!='')
		echo "شرح:".$description."&nbsp;&nbsp;<br>";
		if($ActReg!='')
		echo "اقدامات:".$ActReg;
		echo "</td>";




	//echo "	<td>".htmlentities(str_replace('\r\n', '<br>', $res[$k]->description), ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td nowrap>".$res[$k]->ResponsiblePersonID_FullName."</td>";
/*if($_SESSION["UserID"]=='gholami-a'){*/
if($res[$k]->priority_Desc!='')
	echo "	<td nowrap>".$res[$k]->priority_Desc."</td>";
else 
	echo "	<td nowrap>-</td>";
if ($res[$k]->DeadLine_Shamsi!=01 && $res[$k]->DeadLine_Shamsi!=date-error){
	echo "	<td nowrap>".$res[$k]->DeadLine_Shamsi."</td>";}
else{
	echo "	<td nowrap>-</td>";}

/*}*/

	/*echo "	<td>".$res[$k]->RepeatInNextSession_Desc."</td>";*/

/*if($_SESSION["UserID"]=='gholami-a'){*/

echo "<td><a target=\"_blank\" href=\"HistorySession.php?OrderNo=".$res[$k]->OrderNo."&UniversitySessionID=".$res[$k]->UniversitySessionID."&st=".$res[$k]->SessionTypeID."\">";
echo "<img src='images/draft.gif' title='مشاهده'>";
	echo "</a></td>";

/*}*/


	if($res[$k]->RelatedFileName!="")
		echo "	<td><a href='DownloadFile.php?FileType=PreCommand&RecID=".$res[$k]->SessionPreCommandID."'><img src='images/Download.gif' title='دریافت فایل ضمیمه'></a></td>";
	else
		echo "	<td>&nbsp;</td>";
	
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="9" align="center">
<? if($RemoveType!="NONE") { ?>
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
<? } ?>
<? if($HasAddAccess) { ?>
	 <input type="button" onclick='javascript: NewRecordForm.submit();' value='ایجاد'>
<? } ?>
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewSessionPreCommands.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="UniversitySessionID" name="UniversitySessionID" value="<? echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
		 	

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
