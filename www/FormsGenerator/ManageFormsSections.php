<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : بخش بندیهای فرمها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 90-5-9
*/

// This file taken by MGhayour
// local url: http://localhost:90/MyProject/Ontirandoc/www/FormsGenerator/ManageFormsSections.php

include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/FormsSections.class.php");
include ("classes/FormsStruct.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["FormsStructID"]))
		$Item_FormsStructID=$_REQUEST["FormsStructID"];
	if(isset($_REQUEST["Item_SectionName"]))
		$Item_SectionName=$_REQUEST["Item_SectionName"];
	if(isset($_REQUEST["Item_ShowOrder"]))
	{
		$Item_ShowOrder=$_REQUEST["Item_ShowOrder"];
		$Item_HeaderDesc=$_REQUEST["Item_HeaderDesc"];
		$Item_FooterDesc=$_REQUEST["Item_FooterDesc"];
	}
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_FormsSections::Add($Item_FormsStructID
				, $Item_SectionName
				, $Item_ShowOrder
				, $Item_HeaderDesc
				, $Item_FooterDesc
				);
	}	
	else 
	{	
		manage_FormsSections::Update($_REQUEST["UpdateID"] 
				, $Item_SectionName
				, $Item_ShowOrder
				, $Item_HeaderDesc
				, $Item_FooterDesc
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_FormsSections();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_SectionName.value='".htmlentities($obj->SectionName, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ShowOrder.value='".htmlentities($obj->ShowOrder, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	//$LoadDataJavascriptCode .= "document.f1.Item_HeaderDesc.value='".htmlentities($obj->HeaderDesc, ENT_QUOTES, 'UTF-8')."'; \r\n ";
	//$LoadDataJavascriptCode .= "document.f1.Item_FooterDesc.value='".htmlentities($obj->FooterDesc, ENT_QUOTES, 'UTF-8')."'; \r\n ";
}	
?>
<form method="post" id="f1" name="f1" >
	<?
		if(isset($_REQUEST["UpdateID"])) 
		{
			echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
		}
	//echo manage_FormsStruct::ShowSummary($_REQUEST["FormsStructID"]);
	//echo manage_FormsStruct::ShowTabs($_REQUEST["FormsStructID"], "ManageFormsSections");
	?>
	<br>
	<table width="90%" border="1" cellspacing="0" align="center">
		<tr class="HeaderOfTable">
		<td align="center">ایجاد/ویرایش بخش های فرم</td>
		</tr>
		<tr>
		<td>
		<table width="100%" border="0">
			<? 
			if(!isset($_REQUEST["UpdateID"]))
			{
			?> 
			<input type="hidden" name="FormsStructID" id="FormsStructID" value='<? if(isset($_REQUEST["FormsStructID"])) echo htmlentities($_REQUEST["FormsStructID"], ENT_QUOTES, 'UTF-8'); ?>'>
			<? } ?>
			<tr>
				<td width="1%" nowrap>
			نام بخش
				</td>
				<td nowrap>
				<input type="text" name="Item_SectionName" id="Item_SectionName" maxlength="250" size="40">
				</td>
			</tr>
			<tr>
				<td width="1%" nowrap>
			ترتیب نمایش
				</td>
				<td nowrap>
				<input type="text" name="Item_ShowOrder" id="Item_ShowOrder" maxlength="2" size="2">
				</td>
			</tr>
			<tr>
				<td width="1%" nowrap>
				متن بالای بخش
				</td>
				<td nowrap>
				<textarea name="Item_HeaderDesc" id="Item_HeaderDesc" cols="100" rows="5"><?php  if(isset($_REQUEST["UpdateID"])) echo $obj->HeaderDesc; ?></textarea>
				</td>
			</tr>
			<tr>
				<td width="1%" nowrap>
				متن پایین بخش
				</td>
				<td nowrap>
				<textarea name="Item_FooterDesc" id="Item_FooterDesc" cols="100" rows="5"><?php  if(isset($_REQUEST["UpdateID"])) echo $obj->FooterDesc; ?></textarea>
				</td>
			</tr>

		</table>
		</td>
		</tr>
		<tr class="FooterOfTable">
		<td align="center">
		<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
		<input type="button" onclick="javascript: document.location='ManageFormsSections.php?FormsStructID=<?php echo $_REQUEST["FormsStructID"]; ?>'" value="جدید">
		</td>
		</tr>
	</table>
	<input type="hidden" name="Save" id="Save" value="1">
</form>
<script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
<?php 
$res = manage_FormsSections::GetList($_REQUEST["FormsStructID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->FormsSectionID])) 
	{
		manage_FormsSections::Remove($res[$k]->FormsSectionID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_FormsSections::GetList($_REQUEST["FormsStructID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_FormsStructID" name="Item_FormsStructID" value="<? echo htmlentities($_REQUEST["FormsStructID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="5">
	بخشهای فرم
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>نام بخش</td>
	<td>ترتیب نمایش</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->FormsSectionID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManageFormsSections.php?UpdateID=".$res[$k]->FormsSectionID."&FormsStructID=".$_REQUEST["FormsStructID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td>".htmlentities($res[$k]->SectionName, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".htmlentities($res[$k]->ShowOrder, ENT_QUOTES, 'UTF-8')."</td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="5" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
	&nbsp;
	<input type="button" onclick="javascript: document.location='ManageQuestionnaires.php';" value="بازگشت">
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewFormsSections.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="FormsStructID" name="FormsStructID" value="<? echo htmlentities($_REQUEST["FormsStructID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
</script>
</html>
