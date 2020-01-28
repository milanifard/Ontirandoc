<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : 
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-19
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/TermEquivalentEnglishTerms.class.php");
include_once("classes/terms.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["TermID"]))
		$Item_TermID=$_REQUEST["TermID"];
	if(isset($_REQUEST["Item_EnglishTerm"]))
		$Item_EnglishTerm=$_REQUEST["Item_EnglishTerm"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_TermEquivalentEnglishTerms::Add($Item_TermID
				, $Item_EnglishTerm
				);
	}	
	else 
	{	
		manage_TermEquivalentEnglishTerms::Update($_REQUEST["UpdateID"] 
				, $Item_EnglishTerm
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_TermEquivalentEnglishTerms();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_EnglishTerm.value='".htmlentities($obj->EnglishTerm, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
echo manage_terms::ShowSummary($_REQUEST["TermID"]);
echo manage_terms::ShowTabs($_REQUEST["TermID"], "ManageTermEquivalentEnglishTerms");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش </td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<? 
if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="TermID" id="TermID" value='<? if(isset($_REQUEST["TermID"])) echo htmlentities($_REQUEST["TermID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
<tr>
	<td width="1%" nowrap>
 معادل انگلیسی
	</td>
	<td nowrap>
	<input type="text" name="Item_EnglishTerm" id="Item_EnglishTerm" maxlength="250" size="40">
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='ManageTermEquivalentEnglishTerms.php?TermID=<?php echo $_REQUEST["TermID"]; ?>'" value="جدید">
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
$res = manage_TermEquivalentEnglishTerms::GetList($_REQUEST["TermID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->TermEquivalentEnglishTermID])) 
	{
		manage_TermEquivalentEnglishTerms::Remove($res[$k]->TermEquivalentEnglishTermID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_TermEquivalentEnglishTerms::GetList($_REQUEST["TermID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_TermID" name="Item_TermID" value="<? echo htmlentities($_REQUEST["TermID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="4">
	
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>معادل انگلیسی</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->TermEquivalentEnglishTermID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManageTermEquivalentEnglishTerms.php?UpdateID=".$res[$k]->TermEquivalentEnglishTermID."&TermID=".$_REQUEST["TermID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td>".htmlentities($res[$k]->EnglishTerm, ENT_QUOTES, 'UTF-8')."</td>";
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
<form target="_blank" method="post" action="NewTermEquivalentEnglishTerms.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="TermID" name="TermID" value="<? echo htmlentities($_REQUEST["TermID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
</script>
</html>
