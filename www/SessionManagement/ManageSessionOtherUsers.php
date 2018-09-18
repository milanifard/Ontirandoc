<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : سایر کاربران
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-2-30
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/SessionOtherUsers.class.php");
include ("classes/UniversitySessions.class.php");
include("classes/UniversitySessionsSecurity.class.php");
HTMLBegin();
/*if($_SESSION["UserID"] == "gholami-a")
{
	$mysql = pdodb::getInstance();
	$query="insert into sessionmanagement.SessionOtherUsers (UniversitySessionID,PersonID) values ('930','705178')";
	$res=$mysql->Execute($query);
	echo $query;
	die();
}*/



// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["UniversitySessionID"]);
$HasUpdateAccess = $HasViewAccess = "";
$HasUpdateAccess = $ppc->GetPermission("Update_SessionOtherUsers");
$HasViewAccess = $ppc->GetPermission("View_SessionOtherUsers");
$HasAddAccess = $ppc->GetPermission("Add_SessionOtherUsers");

if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["UniversitySessionID"]))
		$Item_UniversitySessionID=$_REQUEST["UniversitySessionID"];
	if(isset($_REQUEST["Item_PersonID"]))
		$Item_PersonID=$_REQUEST["Item_PersonID"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_SessionOtherUsers::Add($Item_UniversitySessionID
				, $Item_PersonID
				);
	}	
	else 
	{	
		manage_SessionOtherUsers::Update($_REQUEST["UpdateID"] 
				, $Item_PersonID
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_SessionOtherUsers();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
		$LoadDataJavascriptCode .= "document.getElementById('Span_PersonID_FullName').innerHTML='".$obj->PersonID_FullName."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.getElementById('Item_PersonID').value='".$obj->PersonID."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
echo manage_UniversitySessions::ShowSummary($_REQUEST["UniversitySessionID"]);
echo manage_UniversitySessions::ShowTabs($_REQUEST["UniversitySessionID"], "ManageSessionOtherUsers");
if($HasViewAccess!="PUBLIC")
	die();
?>
<br>

<?php if($HasAddAccess=="PUBLIC" || $HasUpdateAccess=="PUBLIC") { ?>
<table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش سایر کاربران</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<? 
if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="UniversitySessionID" id="UniversitySessionID" value='<? if(isset($_REQUEST["UniversitySessionID"])) echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
<tr>
	<td width="1%" nowrap>
 نام و نام خانوادگی: 
	</td>
	<td nowrap>
	<input type=hidden name="Item_PersonID" id="Item_PersonID">
	<span id="Span_PersonID_FullName" name="Span_PersonID_FullName"></span> 	<a href='#' onclick='javascript: window.open("SelectStaff.php?InputName=Item_PersonID&SpanName=Span_PersonID_FullName");'>[انتخاب]</a>	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='ManageSessionOtherUsers.php?UniversitySessionID=<?php echo $_REQUEST["UniversitySessionID"]; ?>'" value="جدید">
</td>
</tr>
</table>
<?php } ?>
<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
<?php 
$OrderByFieldName = "SessionOtherUserID";
$OrderType = "";
if(isset($_REQUEST["OrderByFieldName"]))
{
	$OrderByFieldName = $_REQUEST["OrderByFieldName"];
	$OrderType = $_REQUEST["OrderType"];
}
$res = manage_SessionOtherUsers::GetList($_REQUEST["UniversitySessionID"], $OrderByFieldName, $OrderType); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->SessionOtherUserID])) 
	{
		manage_SessionOtherUsers::Remove($res[$k]->SessionOtherUserID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_SessionOtherUsers::GetList($_REQUEST["UniversitySessionID"], $OrderByFieldName, $OrderType); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_UniversitySessionID" name="Item_UniversitySessionID" value="<? echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="5">
	سایر کاربران
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>نام و نام خانوادگی</td>
	<td width=1%>دسترسی</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->SessionOtherUserID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManageSessionOtherUsers.php?UpdateID=".$res[$k]->SessionOtherUserID."&UniversitySessionID=".$_REQUEST["UniversitySessionID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td>".$res[$k]->PersonID_FullName."</td>";
	echo "	<td><a target=_blank href='UniversitySessionsSetSecurity.php?RecID=".$_REQUEST["UniversitySessionID"]."&SelectedPersonID=".$res[$k]->PersonID."'><img src='images/security.gif' title='تعریف دسترسی'></a></td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="5" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewSessionOtherUsers.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="UniversitySessionID" name="UniversitySessionID" value="<? echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<form method="post" name="f2" id="f2">
<input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
<input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
function Sort(OrderByFieldName, OrderType)
{
	f2.OrderByFieldName.value=OrderByFieldName; 
	f2.OrderType.value=OrderType; 
	f2.submit();
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
