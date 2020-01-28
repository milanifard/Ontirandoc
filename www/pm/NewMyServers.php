<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : 
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 93-12-2
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/VirtualServers.class.php");
include_once("classes/PhysicalServers.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"])) 
{
	$Item_VMName=$_REQUEST["Item_VMName"];
	$Item_VMDescription=$_REQUEST["Item_VMDescription"];
	$Item_EndUserAccess=$_REQUEST["Item_EndUserAccess"];
	$Item_EmployeeAccess=$_REQUEST["Item_EmployeeAccess"];
	$Item_ProvidedServices=$_REQUEST["Item_ProvidedServices"];
	$Item_InstallnConfiguration=$_REQUEST["Item_InstallnConfiguration"];
	$Item_BackupPlan=$_REQUEST["Item_BackupPlan"];
	$Item_PerformanceBenchmark=$_REQUEST["Item_PerformanceBenchmark"];
	$Item_PhysicalServerID=$_REQUEST["PhysicalServerID"];
	$Item_administrator=$_REQUEST["Item_administrator"];
	manage_VirtualServers::Update($_REQUEST["UpdateID"] 
			, $Item_VMName
			, $Item_VMDescription
			, $Item_EndUserAccess
			, $Item_EmployeeAccess
			, $Item_ProvidedServices
			, $Item_InstallnConfiguration
			, $Item_BackupPlan
			, $Item_PerformanceBenchmark
			, $Item_administrator
			);
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
$VMDescription = "IP: \r\nCPU: \r\nMemory: GB\r\nDisk: T / F: \r\n";
$InstallnConfiguration = "";
$ProvidedServices = "";
$EndUserAccess = $EmployeeAccess = $ProvidedServices = $BackupPlan = $PerformanceBenchmark = "";
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_VirtualServers();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_VMName.value='".htmlentities($obj->VMName, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$VMDescription= htmlentities($obj->VMDescription, ENT_QUOTES, 'UTF-8'); 
	$EndUserAccess = htmlentities($obj->EndUserAccess, ENT_QUOTES, 'UTF-8'); 
	$EmployeeAccess = htmlentities($obj->EmployeeAccess, ENT_QUOTES, 'UTF-8'); 
	$ProvidedServices = htmlentities($obj->ProvidedServices, ENT_QUOTES, 'UTF-8'); 
	$InstallnConfiguration = htmlentities($obj->InstallnConfiguration, ENT_QUOTES, 'UTF-8'); 
	$BackupPlan = htmlentities($obj->BackupPlan, ENT_QUOTES, 'UTF-8'); 
	$PerformanceBenchmark = htmlentities($obj->PerformanceBenchmark, ENT_QUOTES, 'UTF-8'); 
	$LoadDataJavascriptCode .= "document.getElementById('Span_administrator_FullName').innerHTML='".$obj->administrator_FullName."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.getElementById('Item_administrator').value='".$obj->administrator."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
echo manage_PhysicalServers::ShowSummary($_REQUEST["PhysicalServerID"]);
//echo manage_PhysicalServers::ShowTabs($_REQUEST["PhysicalServerID"], "ManageVirtualServers");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">مستندسازی سرور</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr>
	<td width="1%" nowrap>
 نام
	</td>
	<td nowrap>
	<input type="text" name="Item_VMName" id="Item_VMName" maxlength="145" size="40" dir=ltr>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 شرح مختصر
	</td>
	<td nowrap>
	<textarea name="Item_VMDescription" id="Item_VMDescription" cols="80" rows="5" dir=ltr><? echo $VMDescription; ?></textarea>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 شرح دسترسی کاربران نهایی
	</td>
	<td nowrap>
	<textarea name="Item_EndUserAccess" id="Item_EndUserAccess" cols="80" rows="5" dir=ltr><? echo $EndUserAccess; ?></textarea>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 شرح دسترسی کارکنان
	</td>
	<td nowrap>
	<textarea name="Item_EmployeeAccess" id="Item_EmployeeAccess" cols="80" rows="5" dir=ltr><? echo $EmployeeAccess; ?></textarea>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 شرح سرویسها
	</td>
	<td nowrap>
	<textarea name="Item_ProvidedServices" id="Item_ProvidedServices" cols="80" rows="5" dir=ltr><? echo $ProvidedServices; ?></textarea>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 نصب و پیکره بندی
	</td>
	<td nowrap>
	<textarea name="Item_InstallnConfiguration" id="Item_InstallnConfiguration" cols="80" rows="15" dir=ltr><? echo $InstallnConfiguration; ?></textarea>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 برنامه پشتیبان گیری و بازیابی
	</td>
	<td nowrap>
	<textarea name="Item_BackupPlan" id="Item_BackupPlan" cols="80" rows="5"  dir=ltr><? echo $BackupPlan; ?></textarea>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 بررسی کارایی
	</td>
	<td nowrap>
	<textarea name="Item_PerformanceBenchmark" id="Item_PerformanceBenchmark" cols="80" rows="5"  dir=ltr><? echo $PerformanceBenchmark; ?></textarea>
	</td>
</tr>
<? 
if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="PhysicalServerID" id="PhysicalServerID" value='<? if(isset($_REQUEST["PhysicalServerID"])) echo htmlentities($_REQUEST["PhysicalServerID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
<tr>
	<td width="1%" nowrap>
 admin
	</td>
	<td nowrap>
	<input type=hidden name="Item_administrator" id="Item_administrator">
	<span id="Span_administrator_FullName" name="Span_administrator_FullName"></span> 	
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='ManageMyServers.php'" value="بازگشت">
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
</html>
