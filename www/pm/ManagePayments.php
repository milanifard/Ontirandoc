<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : پرداختی ها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-1-2
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/payments.class.php");
include ("classes/persons.class.php");
HTMLBegin();
$PersonName = "";
$pobj = new be_persons();
$pobj->LoadDataFromDatabase($_REQUEST["PersonID"]);
$PersonName = $pobj->pfname." ".$pobj->plname;

if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["PersonID"]))
		$Item_PersonID=$_REQUEST["PersonID"];
	if(isset($_REQUEST["Item_amount"]))
		$Item_amount=$_REQUEST["Item_amount"];
	if(isset($_REQUEST["PaymentDate_DAY"]))
	{
		$Item_PaymentDate = SharedClass::ConvertToMiladi($_REQUEST["PaymentDate_YEAR"], $_REQUEST["PaymentDate_MONTH"], $_REQUEST["PaymentDate_DAY"]);
	}
	if(isset($_REQUEST["Item_PayType"]))
		$Item_PayType=$_REQUEST["Item_PayType"];
	if(isset($_REQUEST["Item_PaymentDescription"]))
		$Item_PaymentDescription=$_REQUEST["Item_PaymentDescription"];
		
	$Item_PaymentFile = "";
	$Item_PaymentFileName = "";
	if (trim($_FILES['Item_PaymentFile']['name']) != '')
	{
		if ($_FILES['Item_PaymentFile']['error'] != 0)
		{
			echo ' خطا در ارسال فایل' . $_FILES['Item_PaymentFile']['error'];
		}
		else
		{
			$_size = $_FILES['Item_PaymentFile']['size'];
			$_name = $_FILES['Item_PaymentFile']['tmp_name'];
			$Item_PaymentFile = addslashes((fread(fopen($_name, 'r' ),$_size)));
			$Item_PaymentFileName = trim($_FILES['Item_PaymentFile']['name']);
		}
	}
		
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_payments::Add($Item_PersonID
				, $Item_amount
				, $Item_PaymentDate
				, $Item_PayType
				, $Item_PaymentDescription
				, $Item_PaymentFile
				, $Item_PaymentFileName
				);
	}	
	else 
	{	
		manage_payments::Update($_REQUEST["UpdateID"] 
				, $Item_amount
				, $Item_PaymentDate
				, $Item_PayType
				, $Item_PaymentDescription
				, $Item_PaymentFile
				, $Item_PaymentFileName
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
$PaymentDescription = "";
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_payments();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_amount.value='".htmlentities($obj->amount, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	if($obj->PaymentDate_Shamsi!="date-error") 
	{
		$LoadDataJavascriptCode .= "document.f1.PaymentDate_YEAR.value='".substr($obj->PaymentDate_Shamsi, 2, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.PaymentDate_MONTH.value='".substr($obj->PaymentDate_Shamsi, 5, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.PaymentDate_DAY.value='".substr($obj->PaymentDate_Shamsi, 8, 2)."'; \r\n "; 
	}
	$LoadDataJavascriptCode .= "document.f1.Item_PayType.value='".htmlentities($obj->PayType, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$PaymentDescription = htmlentities($obj->PaymentDescription, ENT_QUOTES, 'UTF-8'); 
}	
?>
<form method="post" id="f1" name="f1" enctype="multipart/form-data" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
//echo manage_persons::ShowSummary($_REQUEST["PersonID"]);
//echo manage_persons::ShowTabs($_REQUEST["PersonID"], "Managepayments");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش پرداخت به <b><? echo $PersonName ?></b></td>
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
 مبلغ
	</td>
	<td nowrap>
	<input type="text" name="Item_amount" id="Item_amount" maxlength="10" size="10"> ریال
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 تاریخ
	</td>
	<td nowrap>
	<input maxlength="2" id="PaymentDate_DAY"  name="PaymentDate_DAY" type="text" size="2">/
	<input maxlength="2" id="PaymentDate_MONTH" name="PaymentDate_MONTH" type="text" size="2" >/
	<input maxlength="2" id="PaymentDate_YEAR" name="PaymentDate_YEAR" type="text" size="2" >
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 نوع پرداخت
	</td>
	<td nowrap>
	<select name="Item_PayType" id="Item_PayType" >
		<option value=0>-
		<option value='TRANSFER'>واریز به حساب
		<option value='CHECK'>چک
		<option value='CASH''>نقد
	</select>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 توضیحات
	</td>
	<td nowrap>
	<textarea name="Item_PaymentDescription" id="Item_PaymentDescription" cols="80" rows="5"><? echo $PaymentDescription ?></textarea>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 
	</td>
	<td nowrap>
	<input type="file" name="Item_PaymentFile" id="Item_PaymentFile">
	<? if(isset($_REQUEST["UpdateID"]) && $obj->PaymentFileName!="") { ?>
	<a href='DownloadFile.php?FileType=payments&RecID=<? echo $_REQUEST["UpdateID"]; ?>'>دریافت فایل [<?php echo $obj->PaymentFileName; ?>]</a>
	<? } ?>
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='ManagePayments.php?PersonID=<?php echo $_REQUEST["PersonID"]; ?>'" value="جدید">
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
$res = manage_payments::GetList($_REQUEST["PersonID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->PaymentID])) 
	{
		manage_payments::Remove($res[$k]->PaymentID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_payments::GetList($_REQUEST["PersonID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_PersonID" name="Item_PersonID" value="<? echo htmlentities($_REQUEST["PersonID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="8">
	پرداختی ها به <b><? echo $PersonName ?></b>
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>مبلغ</td>
	<td>تاریخ</td>
	<td>نوع پرداخت</td>
	<td>توضیحات</td>
	<td>فایل</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->PaymentID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManagePayments.php?UpdateID=".$res[$k]->PaymentID."&PersonID=".$_REQUEST["PersonID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td>".htmlentities($res[$k]->amount, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".$res[$k]->PaymentDate_Shamsi."</td>";
		echo "	<td>".$res[$k]->PayType_Desc."</td>";
	echo "	<td>".str_replace("\r", "<br>", htmlentities($res[$k]->PaymentDescription, ENT_QUOTES, 'UTF-8'))."</td>";
	echo "	<td>";
	if($res[$k]->PaymentFileName!="")
	  echo "<a href='DownloadFile.php?FileType=payments&RecID=".$res[$k]->PaymentID."'>فایل </a>";
	else {
	    echo "&nbsp;";
	}
	
	echo "	</td>";
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
<form target="_blank" method="post" action="Newpayments.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="PersonID" name="PersonID" value="<? echo htmlentities($_REQUEST["PersonID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
</script>
</html>
