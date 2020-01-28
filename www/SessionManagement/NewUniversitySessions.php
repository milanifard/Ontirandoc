<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : جلسات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-2-22
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/UniversitySessions.class.php");
include_once("classes/SessionTypeMembers.class.php");
HTMLBegin();
if(isset($_REQUEST["Item_SessionTypeID"]))
{
	$SessionTypeID = $_REQUEST["Item_SessionTypeID"];
	if(manage_SessionTypeMembers::IsPersonPermittedToCreateSession($_SESSION["PersonID"], $SessionTypeID))
	{
		$UniversitySessionID = manage_UniversitySessions::CreateNewUniversitySession($SessionTypeID);
		if($UniversitySessionID>0)
		{
			echo "<script>document.location='UpdateUniversitySessions.php?UpdateID=".$UniversitySessionID."';</script>";
			die();
		}
		else
			echo SharedClass::CreateMessageBox("امکان ایجاد وجود ندارد", "red");
	}
	else
		echo SharedClass::CreateMessageBox("مجوز ایجاد از این نوع جلسه به شما داده نشده است", "red");
}
$SessionTypesList = manage_SessionTypeMembers::GetSelectOptions($_SESSION["PersonID"]);
?>
<form method="post" id="f1" name="f1" enctype="multipart/form-data" >
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد جلسه</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr id="tr_SessionTypeID" name="tr_SessionTypeID" style='display:'>
<td width="1%" nowrap>
	نوع جلسه
</td>
	<td nowrap>
	<select name="Item_SessionTypeID" id="Item_SessionTypeID">
	<? echo $SessionTypesList ?>	
	</select>
	</td>
</tr>
<tr class="FooterOfTable">
<td align="center" colspan=2>
<input type="submit" value="ایجاد جلسه جدید">
</td>
</tr>
</table>
<input type="hidden" name="Save" id="Save" value="1">
</form>
</html>
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