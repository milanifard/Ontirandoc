<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : انواع مراجع
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 93-3-5
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/RefrenceTypes.class.php");
include ("classes/ResearchProject.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["ResearchProjectID"]))
		$Item_ResearchProjectID=$_REQUEST["ResearchProjectID"];
	if(isset($_REQUEST["Item_RefrenceTypeTitle"]))
		$Item_RefrenceTypeTitle=$_REQUEST["Item_RefrenceTypeTitle"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_RefrenceTypes::Add($Item_ResearchProjectID
				, $Item_RefrenceTypeTitle
				);
	}	
	else 
	{	
		manage_RefrenceTypes::Update($_REQUEST["UpdateID"] 
				, $Item_RefrenceTypeTitle
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_RefrenceTypes();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_RefrenceTypeTitle.value='".htmlentities($obj->RefrenceTypeTitle, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
echo manage_ResearchProject::ShowSummary($_REQUEST["ResearchProjectID"]);
echo manage_ResearchProject::ShowTabs($_REQUEST["ResearchProjectID"], "ManageRefrenceTypes");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش انواع مراجع</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<? 
if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="ResearchProjectID" id="ResearchProjectID" value='<? if(isset($_REQUEST["ResearchProjectID"])) echo htmlentities($_REQUEST["ResearchProjectID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
<tr>
	<td width="1%" nowrap>
 عنوان
	</td>
	<td nowrap>
	<input type="text" name="Item_RefrenceTypeTitle" id="Item_RefrenceTypeTitle" maxlength="245" size="40">
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='ManageRefrenceTypes.php?ResearchProjectID=<?php echo $_REQUEST["ResearchProjectID"]; ?>'" value="جدید">
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
$res = manage_RefrenceTypes::GetList($_REQUEST["ResearchProjectID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->RefrenceTypeID])) 
	{
		manage_RefrenceTypes::Remove($res[$k]->RefrenceTypeID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_RefrenceTypes::GetList($_REQUEST["ResearchProjectID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_ResearchProjectID" name="Item_ResearchProjectID" value="<? echo htmlentities($_REQUEST["ResearchProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="4">
	انواع مراجع
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>عنوان</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->RefrenceTypeID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManageRefrenceTypes.php?UpdateID=".$res[$k]->RefrenceTypeID."&ResearchProjectID=".$_REQUEST["ResearchProjectID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td>".htmlentities($res[$k]->RefrenceTypeTitle, ENT_QUOTES, 'UTF-8')."</td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="4" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewRefrenceTypes.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="ResearchProjectID" name="ResearchProjectID" value="<? echo htmlentities($_REQUEST["ResearchProjectID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
</script>
</html>
