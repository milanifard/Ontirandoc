<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : خبرگان بررسی کننده هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 95-5-24
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/OntologyValidationExperts.class.php");
include ("classes/ontologies.class.php");
HTMLBegin();
$onto = new be_ontologies();
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["Item_ExpertFullName"]))
		$Item_ExpertFullName=$_REQUEST["Item_ExpertFullName"];
	if(isset($_REQUEST["Item_ExpertDesciption"]))
		$Item_ExpertDesciption=$_REQUEST["Item_ExpertDesciption"];
	if(isset($_REQUEST["Item_ExpertEnterCode"]))
		$Item_ExpertEnterCode=$_REQUEST["Item_ExpertEnterCode"];
	if(isset($_REQUEST["Item_ValidationStatus"]))
		$Item_ValidationStatus=$_REQUEST["Item_ValidationStatus"];
	if(isset($_REQUEST["OntologyID"]))
		$Item_OntologyID=$_REQUEST["OntologyID"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_OntologyValidationExperts::Add($Item_ExpertFullName
				, $Item_ExpertDesciption
				, $Item_ExpertEnterCode
				, $Item_ValidationStatus
				, $Item_OntologyID
				);
	}	
	else 
	{	
		manage_OntologyValidationExperts::Update($_REQUEST["UpdateID"] 
				, $Item_ExpertFullName
				, $Item_ExpertDesciption
				, $Item_ExpertEnterCode
				, $Item_ValidationStatus
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_OntologyValidationExperts();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$onto->LoadDataFromDatabase($obj->OntologyID);
	$LoadDataJavascriptCode .= "document.f1.Item_ExpertFullName.value='".htmlentities($obj->ExpertFullName, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ExpertDesciption.value='".htmlentities($obj->ExpertDesciption, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ExpertEnterCode.value='".htmlentities($obj->ExpertEnterCode, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_ValidationStatus.value='".htmlentities($obj->ValidationStatus, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
else {
    $onto->LoadDataFromDatabase($_REQUEST["OntologyID"]);
}

?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
echo manage_ontologies::ShowSummary($_REQUEST["OntologyID"]);
echo manage_ontologies::ShowTabs($_REQUEST["OntologyID"], "ManageOntologyValidationExperts");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش خبرگان بررسی کننده هستان نگار</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr>
	<td width="1%" nowrap>
 نام و نام خانوادگی
	</td>
	<td nowrap>
	<input type="text" name="Item_ExpertFullName" id="Item_ExpertFullName" maxlength="145" size="40">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 شرح پست/شغل/تخصص
	</td>
	<td nowrap>
	<input type="text" name="Item_ExpertDesciption" id="Item_ExpertDesciption" maxlength="500" size="40">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 کد ورود خبره به سایت
	</td>
	<td nowrap>
	<input type="text" name="Item_ExpertEnterCode" id="Item_ExpertEnterCode" maxlength="45" size="40">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 وضعیت ارزیابی
	</td>
	<td nowrap>
	<select name="Item_ValidationStatus" id="Item_ValidationStatus" >
		<option value=0>-
		<option value='NOT_START'>ارزیابی نشده</option>
		<option value='IN_PROGRESS'>در حال ارزیابی</option>
		<option value='DONE'>ارزیابی شده</option>
	</select>
	</td>
</tr>
<? 
if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="OntologyID" id="OntologyID" value='<? if(isset($_REQUEST["OntologyID"])) echo htmlentities($_REQUEST["OntologyID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
</table>
</td>
</tr>
<tr>
  <td>
  ارزیابان می توانند  از طریق این 
  <a href='ValidateOntology.php'>لینک</a>
  و ورود کد خود اقدام به ارزیابی عناصر هستان نگار کنند.
  <br>
  <br>
  برای مشاهده سرجمع نظرات خبرگان 
  <a href='ShowExpertsResult.php?OntologyID=<? echo $_REQUEST["OntologyID"] ?>&OntologyTitle=<? echo $onto->OntologyTitle ?>'>اینجا</a>
   را کلیک کنید.
  </td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='ManageOntologyValidationExperts.php?OntologyID=<?php echo $_REQUEST["OntologyID"]; ?>'" value="جدید">
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
$res = manage_OntologyValidationExperts::GetList($_REQUEST["OntologyID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->OntologyValidationExpertID])) 
	{
		manage_OntologyValidationExperts::Remove($res[$k]->OntologyValidationExpertID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_OntologyValidationExperts::GetList($_REQUEST["OntologyID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_OntologyID" name="Item_OntologyID" value="<? echo htmlentities($_REQUEST["OntologyID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="8">
	خبرگان بررسی کننده هستان نگار
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>نام و نام خانوادگی</td>
	<td>شرح پست/شغل/تخصص</td>
	<td>کد ورود خبره به سایت</td>
	<td>وضعیت ارزیابی</td>
	<td>مشاهده نظرات</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->OntologyValidationExpertID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManageOntologyValidationExperts.php?UpdateID=".$res[$k]->OntologyValidationExpertID."&OntologyID=".$_REQUEST["OntologyID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td>".htmlentities($res[$k]->ExpertFullName, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".htmlentities($res[$k]->ExpertDesciption, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".htmlentities($res[$k]->ExpertEnterCode, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".$res[$k]->ValidationStatus_Desc."</td>";
	echo "	<td><a target=_blank href='ShowExpertsResult.php?ExpertID=".$res[$k]->OntologyValidationExpertID."&OntologyID=".$onto->OntologyID."&OntologyTitle=".$onto->OntologyTitle."'>مشاهده نظرات</a></td>";
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
<form target="_blank" method="post" action="NewOntologyValidationExperts.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="OntologyID" name="OntologyID" value="<? echo htmlentities($_REQUEST["OntologyID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
</script>
</html>
