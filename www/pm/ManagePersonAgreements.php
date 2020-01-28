<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : قرارداد پرسنل
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 93-12-26
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/PersonAgreements.class.php");
include_once("classes/persons.class.php");
HTMLBegin();
$PersonName = "";
$pobj = new be_persons();
$pobj->LoadDataFromDatabase($_REQUEST["PersonID"]);
$PersonName = $pobj->pfname." ".$pobj->plname;
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["PersonID"]))
		$Item_PersonID=$_REQUEST["PersonID"];
	if(isset($_REQUEST["FromDate_DAY"]))
	{
		$Item_FromDate = SharedClass::ConvertToMiladi($_REQUEST["FromDate_YEAR"], $_REQUEST["FromDate_MONTH"], $_REQUEST["FromDate_DAY"]);
	}
	if(isset($_REQUEST["ToDate_DAY"]))
	{
		$Item_ToDate = SharedClass::ConvertToMiladi($_REQUEST["ToDate_YEAR"], $_REQUEST["ToDate_MONTH"], $_REQUEST["ToDate_DAY"]);
	}
	if(isset($_REQUEST["Item_AgreementDescription"]))
		$Item_AgreementDescription=$_REQUEST["Item_AgreementDescription"];
	if(isset($_REQUEST["Item_HourlyPrice"]))
		$Item_HourlyPrice=$_REQUEST["Item_HourlyPrice"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_PersonAgreements::Add($Item_PersonID
				, $Item_FromDate
				, $Item_ToDate
				, $Item_AgreementDescription
				, $Item_HourlyPrice
				);
	}	
	else 
	{	
		manage_PersonAgreements::Update($_REQUEST["UpdateID"] 
				, $Item_FromDate
				, $Item_ToDate
				, $Item_AgreementDescription
				, $Item_HourlyPrice
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_PersonAgreements();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	if($obj->FromDate_Shamsi!="date-error") 
	{
		$LoadDataJavascriptCode .= "document.f1.FromDate_YEAR.value='".substr($obj->FromDate_Shamsi, 2, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.FromDate_MONTH.value='".substr($obj->FromDate_Shamsi, 5, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.FromDate_DAY.value='".substr($obj->FromDate_Shamsi, 8, 2)."'; \r\n "; 
	}
	if($obj->ToDate_Shamsi!="date-error") 
	{
		$LoadDataJavascriptCode .= "document.f1.ToDate_YEAR.value='".substr($obj->ToDate_Shamsi, 2, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.ToDate_MONTH.value='".substr($obj->ToDate_Shamsi, 5, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.ToDate_DAY.value='".substr($obj->ToDate_Shamsi, 8, 2)."'; \r\n "; 
	}
	$LoadDataJavascriptCode .= "document.f1.Item_AgreementDescription.value='".htmlentities($obj->AgreementDescription, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_HourlyPrice.value='".htmlentities($obj->HourlyPrice, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
//echo manage_persons::ShowSummary($_REQUEST["PersonID"]);
//echo manage_persons::ShowTabs($_REQUEST["PersonID"], "ManagePersonAgreements");

?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش قرارداد پرسنل</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<? 
if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="PersonID" id="PersonID" value='<? if(isset($_REQUEST["PersonID"])) echo htmlentities($_REQUEST["PersonID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
<tr>
	<td width="1%" nowrap>
 از تاریخ
	</td>
	<td nowrap>
	<input maxlength="2" id="FromDate_DAY"  name="FromDate_DAY" type="text" size="2">/
	<input maxlength="2" id="FromDate_MONTH" name="FromDate_MONTH" type="text" size="2" >/
	<input maxlength="2" id="FromDate_YEAR" name="FromDate_YEAR" type="text" size="2" >
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 تا تاریخ
	</td>
	<td nowrap>
	<input maxlength="2" id="ToDate_DAY"  name="ToDate_DAY" type="text" size="2">/
	<input maxlength="2" id="ToDate_MONTH" name="ToDate_MONTH" type="text" size="2" >/
	<input maxlength="2" id="ToDate_YEAR" name="ToDate_YEAR" type="text" size="2" >
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 شرح قرارداد
	</td>
	<td nowrap>
	<textarea name="Item_AgreementDescription" id="Item_AgreementDescription" cols="80" rows="5"></textarea>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 مبلغ ساعتی
	</td>
	<td nowrap>
	<input type="text" name="Item_HourlyPrice" id="Item_HourlyPrice" maxlength="10" size="10"> ریال
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='ManagePersonAgreements.php?PersonID=<?php echo $_REQUEST["PersonID"]; ?>'" value="جدید">
 <input type="button" onclick="javascript: document.location='Managepersons.php';" value="بازگشت">
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
$res = manage_PersonAgreements::GetList($_REQUEST["PersonID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->PersonAgreementID])) 
	{
		manage_PersonAgreements::Remove($res[$k]->PersonAgreementID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_PersonAgreements::GetList($_REQUEST["PersonID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_PersonID" name="Item_PersonID" value="<? echo htmlentities($_REQUEST["PersonID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="6">
	قراردادهای <b><? echo $PersonName; ?> </b>
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>از تاریخ</td>
	<td>تا تاریخ</td>
	<td>مبلغ ساعتی</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->PersonAgreementID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManagePersonAgreements.php?UpdateID=".$res[$k]->PersonAgreementID."&PersonID=".$_REQUEST["PersonID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td>".$res[$k]->FromDate_Shamsi."</td>";
	echo "	<td>".$res[$k]->ToDate_Shamsi."</td>";
	echo "	<td>".htmlentities($res[$k]->HourlyPrice, ENT_QUOTES, 'UTF-8')."</td>";
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
<form target="_blank" method="post" action="NewPersonAgreements.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="PersonID" name="PersonID" value="<? echo htmlentities($_REQUEST["PersonID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
</script>
</html>
