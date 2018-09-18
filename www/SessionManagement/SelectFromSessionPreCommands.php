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
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["UniversitySessionID"]);
$ViewType = $ppc->GetPermission("View_SessionPreCommands");
$res = manage_SessionPreCommands::GetList($_REQUEST["UniversitySessionID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_UniversitySessionID" name="Item_UniversitySessionID" value="<? echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="7">
	انتخاب دستور کار مورد نظر - برای انتخاب روی شماره ردیف کلیک کنید
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width=1% nowrap>ردیف</td>
	<td>شرح</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($ViewType=="PUBLIC" || ($ViewType=="PRIVATE" && $res[$k]->CreatorPersonID==$_SESSION["PersonID"]))
	{
		if($k%2==0)
			echo "<tr class=\"OddRow\">";
		else
			echo "<tr class=\"EvenRow\">";
		echo "	<td><a href='javascript: SelectRow(".$res[$k]->SessionPreCommandID.", ".$res[$k]->OrderNo.")'>".$res[$k]->OrderNo."</a></td>";	
		echo "	<td>".htmlentities(str_replace('\r\n', '<br>', $res[$k]->description), ENT_QUOTES, 'UTF-8')."</td>";
		echo "</tr>";
	}
}
?>
<tr>
	<td colspan=2><b><a href='javascript: SelectRow(0, "")'>حذف گزینه انتخاب شده</a></b></td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewSessionPreCommands.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="UniversitySessionID" name="UniversitySessionID" value="<? echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>

<script>
function SelectRow(SessionPreCommandID, RowNumber)
{
	window.opener.document.f1.Item_SessionPreCommandID.value=SessionPreCommandID;
	window.opener.document.getElementById('Span_SessionPreCommandRow').innerHTML=RowNumber;
	window.close();
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
