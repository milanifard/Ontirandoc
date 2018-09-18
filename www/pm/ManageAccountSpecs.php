<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : 
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 92-8-4
*/
include("header.inc.php");
include("classes/AccountSpecs.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["Item_UserID"]))
		$Item_UserID=$_REQUEST["Item_UserID"];
	if(isset($_REQUEST["Item_UserPassword"]))
		$Item_UserPassword=$_REQUEST["Item_UserPassword"];
	if(isset($_REQUEST["Item_PersonID"]))
		$Item_PersonID=$_REQUEST["Item_PersonID"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_AccountSpecs::Add($Item_UserID
				, $Item_UserPassword
				, $Item_PersonID
				);
	}	
	else 
	{	
		manage_AccountSpecs::Update($_REQUEST["UpdateID"] 
				, $Item_UserID
				, $Item_UserPassword
				, $Item_PersonID
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_AccountSpecs();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_UserID.value='".htmlentities($obj->UserID, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	//$LoadDataJavascriptCode .= "document.f1.Item_UserPassword.value='".htmlentities($obj->UserPassword, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_PersonID.value='".htmlentities($obj->PersonID, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش کاربر</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr>
	<td width="1%" nowrap>
 نام کاربری
	</td>
	<td nowrap>
	<input type="text" name="Item_UserID" id="Item_UserID" maxlength="100" size="40">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 کلمه عبور
	</td>
	<td nowrap>
	<input type="password" name="Item_UserPassword" id="Item_UserPassword" maxlength="100" size="40" value=''>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 نام و نام خانوادگی
	</td>
	<td nowrap>
	<select name="Item_PersonID" id="Item_PersonID">
	<option value=0>-
	<?
	  //echo SharedClass::CreateAdvanceRelatedTableSelectOptions("projectmanagement.persons", "PersonID", "FullName", "concat(plname, ' ', pfname) as FullName, PersonID", "plname, pfname"); 
	  $mysql = pdodb::getInstance();
	  $pres = $mysql->Execute("select concat(plname, ' ', pfname) as FullName, PersonID from projectmanagement.persons order by plname, pfname");
	  while($prec = $pres->fetch())
	  {
	    echo "<option value='".$prec["PersonID"]."'>".$prec["FullName"];
	  }
	?>
  	</select>	
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='ManageAccountSpecs.php';" value="جدید">
</td>
</tr>
</table>
<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
<?php 
$res = manage_AccountSpecs::GetList(); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->AccountSpecID])) 
	{
		manage_AccountSpecs::Remove($res[$k]->AccountSpecID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_AccountSpecs::GetList(); 
?>
<form id="ListForm" name="ListForm" method="post"> 
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="6">
	لیست کاربران
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%">&nbsp;</td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>نام کاربری</td>
	<td>نام و نام خانوادگی</td>
	<td width=1% nowrap>مجوزها</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->AccountSpecID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManageAccountSpecs.php?UpdateID=".$res[$k]->AccountSpecID."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td>".$res[$k]->UserID."</td>";
	echo "	<td>".$res[$k]->PersonID_Desc."</td>";
	echo "	<td><a href=\"ManageUserPermissions.php?Item_UserID=".$res[$k]->UserID."\"><img src='images/permission.gif' title='مجوزها'></a></td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="6" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewAccountSpecs.php" id="NewRecordForm" name="NewRecordForm">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
</script>
</html>
