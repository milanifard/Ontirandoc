<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : اعضای الگوهای جلسه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-2-26
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/SessionTypeMembers.class.php");
include ("classes/SessionTypes.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["Item_MemberRow"]))
		$Item_MemberRow=$_REQUEST["Item_MemberRow"];
	if(isset($_REQUEST["SessionTypeID"]))
		$Item_SessionTypeID=$_REQUEST["SessionTypeID"];
	if(isset($_REQUEST["Item_MemberPersonType"]))
		$Item_MemberPersonType=$_REQUEST["Item_MemberPersonType"];
	if(isset($_REQUEST["Item_MemberPersonID"]))
		$Item_MemberPersonID=$_REQUEST["Item_MemberPersonID"];
	if(isset($_REQUEST["Item_FirstName"]))
		$Item_FirstName=$_REQUEST["Item_FirstName"];
	if(isset($_REQUEST["Item_LastName"]))
		$Item_LastName=$_REQUEST["Item_LastName"];
	if(isset($_REQUEST["Item_MemberRoleID"]))
		$Item_MemberRoleID=$_REQUEST["Item_MemberRoleID"];
	if(isset($_REQUEST["Item_NeedToConfirm"]))
		$Item_NeedToConfirm=$_REQUEST["Item_NeedToConfirm"];
	if(isset($_REQUEST["Item_AccessSign"]))
		$Item_AccessSign=$_REQUEST["Item_AccessSign"];
	if(isset($_REQUEST["Item_NeedToSignSessionDecisions"]))
		$Item_NeedToSignSessionDecisions=$_REQUEST["Item_NeedToSignSessionDecisions"];
	//if(isset($_REQUEST["Item_NeedToConfirmPresence"]))
	//	$Item_NeedToConfirmPresence=$_REQUEST["Item_NeedToConfirmPresence"];
	$Item_NeedToConfirmPresence = "NO";
	//if(isset($_REQUEST["Item_AccessFinalAccept"]))
	//	$Item_AccessFinalAccept=$_REQUEST["Item_AccessFinalAccept"];
	$Item_AccessFinalAccept="WRITE";
	//if(isset($_REQUEST["Item_AccessRejectSign"]))
	//	$Item_AccessRejectSign=$_REQUEST["Item_AccessRejectSign"];
	$Item_AccessRejectSign="NO";
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_SessionTypeMembers::Add($Item_MemberRow
				, $Item_SessionTypeID
				, $Item_MemberPersonType
				, $Item_MemberPersonID
				, $Item_FirstName
				, $Item_LastName
				, $Item_MemberRoleID
				, $Item_NeedToConfirm
				, $Item_AccessSign
				, $Item_NeedToSignSessionDecisions
				, $Item_NeedToConfirmPresence
				, $Item_AccessFinalAccept
				, $Item_AccessRejectSign
				);
	}	
	else 
	{	
		manage_SessionTypeMembers::Update($_REQUEST["UpdateID"] 
				, $Item_MemberRow
				, $Item_MemberPersonType
				, $Item_MemberPersonID
				, $Item_FirstName
				, $Item_LastName
				, $Item_MemberRoleID
				, $Item_NeedToConfirm
				, $Item_AccessSign
				, $Item_NeedToSignSessionDecisions
				, $Item_NeedToConfirmPresence
				, $Item_AccessFinalAccept
				, $Item_AccessRejectSign
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_SessionTypeMembers();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_MemberRow.value='".htmlentities($obj->MemberRow, ENT_QUOTES, 'UTF-8')."'; \r\n ";
	$LoadDataJavascriptCode .= "document.f1.Item_MemberPersonType.value='".htmlentities($obj->MemberPersonType, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_MemberPersonID.value='".htmlentities($obj->MemberPersonID, ENT_QUOTES, 'UTF-8')."'; \r\n ";
	$LoadDataJavascriptCode .= "document.getElementById('Span_MemberPersonID_FullName').innerHTML='".$obj->MemberPersonID_FullName."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_FirstName.value='".htmlentities($obj->FirstName, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_LastName.value='".htmlentities($obj->LastName, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_MemberRoleID.value='".htmlentities($obj->MemberRoleID, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_NeedToConfirm.value='".htmlentities($obj->NeedToConfirm, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_AccessSign.value='".htmlentities($obj->AccessSign, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_NeedToSignSessionDecisions.value='".htmlentities($obj->NeedToSignSessionDecisions, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	//$LoadDataJavascriptCode .= "document.f1.Item_NeedToConfirmPresence.value='".htmlentities($obj->NeedToConfirmPresence, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	//$LoadDataJavascriptCode .= "document.f1.Item_AccessFinalAccept.value='".htmlentities($obj->AccessFinalAccept, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	//$LoadDataJavascriptCode .= "document.f1.Item_AccessRejectSign.value='".htmlentities($obj->AccessRejectSign, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
$MaxMemberRow = manage_SessionTypeMembers::GetLastMemberRow($_REQUEST["SessionTypeID"]);
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
echo manage_SessionTypes::ShowSummary($_REQUEST["SessionTypeID"]);
echo manage_SessionTypes::ShowTabs($_REQUEST["SessionTypeID"], "ManageSessionTypeMembers");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش اعضای الگوهای جلسه</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr>
	<td width="1%" nowrap>
	 ردیف
	</td>
	<td nowrap>
	<input type="text" name="Item_MemberRow" id="Item_MemberRow" maxlength="2" size="2" value='<?php echo $MaxMemberRow ?>'>
	</td>
</tr>
<? 
if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="SessionTypeID" id="SessionTypeID" value='<? if(isset($_REQUEST["SessionTypeID"])) echo htmlentities($_REQUEST["SessionTypeID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
<tr>
	<td width="1%" nowrap>
 نوع عضو
	</td>
	<td nowrap>
	<select name="Item_MemberPersonType" id="Item_MemberPersonType"  onchange="javascript: Select_MemberPersonType(this.value);" >
		<option value='PERSONEL'>پرسنل</option>
		<option value='OTHER'>سایر</option>
	</select>
	</td>
</tr>
<tr id="tr_MemberPersonID" name="tr_MemberPersonID" style='display:'>
	<td width="1%" nowrap>
 کد شخصی عضو
	</td>
	<td nowrap>
	<input type=hidden name="Item_MemberPersonID" id="Item_MemberPersonID">
	<span id="Span_MemberPersonID_FullName" name="Span_MemberPersonID_FullName"></span> 	<a href='#' onclick='javascript: window.open("SelectStaff.php?InputName=Item_MemberPersonID&SpanName=Span_MemberPersonID_FullName&LInput=Item_LastName&FInput=Item_FirstName");'>[انتخاب]</a>	</td>
</tr>
<tr id="tr_FirstName" name="tr_FirstName" style='display: none'>
	<td width="1%" nowrap>
 نام
	</td>
	<td nowrap>
	<input type="text" name="Item_FirstName" id="Item_FirstName" maxlength="100" size="40">
	</td>
</tr>
<tr id="tr_LastName" name="tr_LastName" style='display: none'>
	<td width="1%" nowrap>
 نام خانوادگی
	</td>
	<td nowrap>
	<input type="text" name="Item_LastName" id="Item_LastName" maxlength="100" size="40">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 نقش
	</td>
	<td nowrap>
	<select name="Item_MemberRoleID" id="Item_MemberRoleID">
	<? echo SharedClass::CreateARelatedTableSelectOptions("sessionmanagement.MemberRoles", "MemberRoleID", "title", "MemberRoleID"); ?>	</select>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 برگزاری جلسه منوط به تایید این کاربر است
	</td>
	<td nowrap>
	<select name="Item_NeedToConfirm" id="Item_NeedToConfirm" >
		<option value='NO'>خیر </option>
		<option value='YES'>بلی</option>
	</select>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 اجازه امضای صورتجلسه
	</td>
	<td nowrap>
	<select name="Item_AccessSign" id="Item_AccessSign" >
		<option value='NO'>خیر</option>
		<option value='YES'>بلی</option>
	</select>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 برای قطعی شدن صورتجلسه نیاز به امضای الکترونیکی فرد می باشد
	</td>
	<td nowrap>
	<select name="Item_NeedToSignSessionDecisions" id="Item_NeedToSignSessionDecisions" >
		<option value='NO'>خیر</option>
		<option value='YES'>بلی</option>
	</select>
	</td>
</tr>
<!-- 
<tr>
	<td width="1%" nowrap>
 مدعو باید درخواست حضور را تایید نماید
	</td>
	<td nowrap>
	<select name="Item_NeedToConfirmPresence" id="Item_NeedToConfirmPresence" >
		<option value='NO'>خیر</option>
		<option value='YES'>بلی</option>
	</select>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 نحوه دسترسی به تایید نهایی صورتجلسه
	</td>
	<td nowrap>
	<select name="Item_AccessFinalAccept" id="Item_AccessFinalAccept" >
		<option value='WRITE'>ویرایش</option>
		<option value='READ'>خواندن</option>
		<option value='NONE'>عدم دسترسی</option>
	</select>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 نحوه دسترسی به امکان لغو امضای اعضای جلسه
	</td>
	<td nowrap>
	<select name="Item_AccessRejectSign" id="Item_AccessRejectSign" >
		<option value=0>-
		<option value='WRITE'>ویرایش</option>
		<option value='READ'>خواندن</option>
		<option value='NONE'>عدم دسترسی</option>
	</select>
	</td>
</tr>
 -->
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='ManageSessionTypeMembers.php?SessionTypeID=<?php echo $_REQUEST["SessionTypeID"]; ?>'" value="جدید">
</td>
</tr>
</table>
<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function Select_MemberPersonType(SelectedValue) 
	{
		if(SelectedValue=='PERSONEL')
			document.getElementById('tr_FirstName').style.display='none';
		if(SelectedValue=='PERSONEL')
			document.getElementById('tr_MemberPersonID').style.display='';
		if(SelectedValue=='PERSONEL')
			document.getElementById('tr_LastName').style.display='none';
		if(SelectedValue=='OTHER')
			document.getElementById('tr_FirstName').style.display='';
		if(SelectedValue=='OTHER')
			document.getElementById('tr_MemberPersonID').style.display='none';
		if(SelectedValue=='OTHER')
			document.getElementById('tr_LastName').style.display='';
	}
	Select_MemberPersonType(document.getElementById('Item_MemberPersonType').value);
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
<?php 
$res = manage_SessionTypeMembers::GetList($_REQUEST["SessionTypeID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->SessionTypeMemberID])) 
	{
		manage_SessionTypeMembers::Remove($res[$k]->SessionTypeMemberID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_SessionTypeMembers::GetList($_REQUEST["SessionTypeID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_SessionTypeID" name="Item_SessionTypeID" value="<? echo htmlentities($_REQUEST["SessionTypeID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="8">
	اعضای الگوهای جلسه
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td width="2%">شماره</td>
	<td>نام</td>
	<td>نام خانوادگی</td>
	<td>نقش</td>
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
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->SessionTypeMemberID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManageSessionTypeMembers.php?UpdateID=".$res[$k]->SessionTypeMemberID."&SessionTypeID=".$_REQUEST["SessionTypeID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td>".$res[$k]->MemberRow."</td>";
	echo "	<td>".htmlentities($res[$k]->FirstName, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".htmlentities($res[$k]->LastName, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".$res[$k]->MemberRoleID_Desc."</td>";
	echo "	<td>";
	if($res[$k]->MemberPersonID>0)
		echo "<a target=_blank href='SessionTypesSetSecurity.php?RecID=".$_REQUEST["SessionTypeID"]."&SelectedPersonID=".$res[$k]->MemberPersonID."'><img src='images/security.gif' title='تعریف دسترسی'></a>";
	else
		echo "&nbsp;";
	echo "</td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="8" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewSessionTypeMembers.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="SessionTypeID" name="SessionTypeID" value="<? echo htmlentities($_REQUEST["SessionTypeID"], ENT_QUOTES, 'UTF-8'); ?>">
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
