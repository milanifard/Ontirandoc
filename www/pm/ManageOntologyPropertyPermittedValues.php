<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : مقادیر مجاز خصوصیت
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 95-6-1
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/OntologyPropertyPermittedValues.class.php");
include_once("classes/OntologyProperties.class.php");
HTMLBegin();
$LoadScript = "";
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["OntologyPropertyID"]))
		$Item_OntologyPropertyID=$_REQUEST["OntologyPropertyID"];
	if(isset($_REQUEST["Item_PermittedValue"]))
		$Item_PermittedValue=$_REQUEST["Item_PermittedValue"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_OntologyPropertyPermittedValues::Add($Item_OntologyPropertyID
				, $Item_PermittedValue
				);
	}	
	else 
	{	
		manage_OntologyPropertyPermittedValues::Update($_REQUEST["UpdateID"] 
				, $Item_PermittedValue
				);
	}	
	if(isset($_REQUEST["FromTermOnto"]))
	{
	  $LoadScript = "LoadPermittedValueList(".$_REQUEST["OntologyPropertyID"].");\r\n";
	}
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_OntologyPropertyPermittedValues();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_PermittedValue.value='".htmlentities($obj->PermittedValue, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
echo manage_OntologyProperties::ShowSummary($_REQUEST["OntologyPropertyID"]);
echo manage_OntologyProperties::ShowTabs($_REQUEST["OntologyPropertyID"], "ManageOntologyPropertyPermittedValues");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش مقادیر مجاز خصوصیت</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<? 
if(isset($_REQUEST["FromTermOnto"])) { ?> 
<input type="hidden" name="FromTermOnto" id="FromTermOnto" value='1'>
<? } ?>
<input type="hidden" name="OntologyPropertyID" id="OntologyPropertyID" value='<? if(isset($_REQUEST["OntologyPropertyID"])) echo htmlentities($_REQUEST["OntologyPropertyID"], ENT_QUOTES, 'UTF-8'); ?>'>
<input type="hidden" name="OntologyID" id="OntologyID" value='<? if(isset($_REQUEST["OntologyID"])) echo htmlentities($_REQUEST["OntologyID"], ENT_QUOTES, 'UTF-8'); ?>'>

<tr>
	<td width="1%" nowrap>
 مقدار مجاز
	</td>
	<td nowrap>
	<input type="text" name="Item_PermittedValue" id="Item_PermittedValue" maxlength="300" size="40">
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='ManageOntologyPropertyPermittedValues.php?OntologyPropertyID=<?php echo $_REQUEST["OntologyPropertyID"]; ?><? if(isset($_REQUEST["FromTermOnto"])) echo "&FromTermOnto=1"; ?>'" value="جدید">
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
$res = manage_OntologyPropertyPermittedValues::GetList($_REQUEST["OntologyPropertyID"]); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->OntologyPropertyPermittedValueID])) 
	{
		manage_OntologyPropertyPermittedValues::Remove($res[$k]->OntologyPropertyPermittedValueID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_OntologyPropertyPermittedValues::GetList($_REQUEST["OntologyPropertyID"]); 
?>
<form id="ListForm" name="ListForm" method="post"> 
<? 
if(isset($_REQUEST["FromTermOnto"])) { ?> 
<input type="hidden" name="FromTermOnto" id="FromTermOnto" value='1'>
<? } ?>
<input type="hidden" id="Item_OntologyPropertyID" name="Item_OntologyPropertyID" value="<? echo htmlentities($_REQUEST["OntologyPropertyID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="4">
	مقادیر مجاز خصوصیت
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td>مقدار</td>
	<td width="2%">ویرایش</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->OntologyPropertyPermittedValueID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "<td>".$res[$k]->PermittedValue."</td>";
	echo "	<td><a href=\"ManageOntologyPropertyPermittedValues.php?UpdateID=".$res[$k]->OntologyPropertyPermittedValueID."&OntologyPropertyID=".$_REQUEST["OntologyPropertyID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="4" align="center">
	<input type="button" onclick="javascript: if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();" value="حذف">
	&nbsp;
	<? if(isset($_REQUEST["FromTermOnto"])) { ?>
	<input type="button" onclick="javascript: window.close();" value="بستن">
	<? } else { ?>
	<input type="button" onclick="javascript: document.location='ManageOntologyProperties.php?OntologyID=<? echo $_REQUEST["OntologyID"]; ?>&UpdateID=<? echo $_REQUEST["OntologyPropertyID"]; ?>';" value="بازگشت">
	<? } ?>
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewOntologyPropertyPermittedValues.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="OntologyPropertyID" name="OntologyPropertyID" value="<? echo htmlentities($_REQUEST["OntologyPropertyID"], ENT_QUOTES, 'UTF-8'); ?>">
	<input type="hidden" id="OntologyID" name="OntologyID" value="<? echo htmlentities($_REQUEST["OntologyID"], ENT_QUOTES, 'UTF-8'); ?>">
	
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
<? if(isset($_REQUEST["FromTermOnto"])) { ?> 	
function LoadPermittedValueList(OntologyPropertyID)
{
  //document.getElementById('PermittedValueSpan').innerHTML = '<img src="images/ajax-loader.gif">';
  var params = "Ajax=1&LoadPermittedData="+OntologyPropertyID+"<? echo "&TermID=".$_REQUEST["TermID"]; ?>";
  //alert('TermOntologyPage.php'+'&'+params);
  var http = new XMLHttpRequest();
  http.open("POST", "TermOntologyPage.php", true);
  //Send the proper header information along with the request
  http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  http.setRequestHeader("Content-length", params.length);
  http.setRequestHeader("Connection", "close");
  
  http.onreadystatechange = function()
  {//Call a function when the state changes.
    if(http.readyState == 4 && http.status == 200)
    {
      //document.getElementById('PermittedValueSpan').innerHTML = http.responseText;
      window.opener.document.getElementById('OntologyPropertyPermittedValueID').innerHTML = http.responseText;
      window.opener.document.getElementById('OntologyPropertyPermittedValueID').value='<? echo $OntologyPropertyPermittedValueID;  ?>';
    }
  }
  http.send(params);
}
<? } ?>
<? echo $LoadScript; ?>
</script>
</html>
