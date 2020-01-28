<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : محتوای پاراگرافهای مراجع
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-5-4
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/TermReferenceContent.class.php");
include_once("classes/TermReferences.class.php");
HTMLBegin();
$mysql = pdodb::getInstance();

$mysql->Prepare("select max(PageNum)+1 as LastPage from TermReferenceContent where TermReferenceID=?");
$res = $mysql->ExecuteStatement(array($_REQUEST["TermReferenceID"]));
$LastPage = 1;
if($rec = $res->fetch())
{
  $LastPage = $rec["LastPage"];
}
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["TermReferenceID"]))
		$Item_TermReferenceID=$_REQUEST["TermReferenceID"];
	if(isset($_REQUEST["Item_PageNum"]))
		$Item_PageNum=$_REQUEST["Item_PageNum"];
	if(isset($_REQUEST["Item_content"]))
		$Item_content=$_REQUEST["Item_content"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_TermReferenceContent::Add($Item_TermReferenceID
				, $Item_PageNum
				, $Item_content
				);
	}	
	else 
	{	
		manage_TermReferenceContent::Update($_REQUEST["UpdateID"] 
				, $Item_PageNum
				, $Item_content
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = $content = '';
$LoadDataJavascriptCode .= "document.f1.Item_PageNum.value='".$LastPage."'; \r\n "; 
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_TermReferenceContent();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_PageNum.value='".htmlentities($obj->PageNum, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$content = htmlentities($obj->content, ENT_QUOTES, 'UTF-8');
}	
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
echo manage_TermReferences::ShowSummary($_REQUEST["TermReferenceID"]);
echo manage_TermReferences::ShowTabs($_REQUEST["TermReferenceID"], "ManageTermReferenceContent");
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش محتوای پاراگرافهای مراجع</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<? 
if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="TermReferenceID" id="TermReferenceID" value='<? if(isset($_REQUEST["TermReferenceID"])) echo htmlentities($_REQUEST["TermReferenceID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
<tr>
	<td width="1%" nowrap>
 صفحه
	</td>
	<td nowrap>
	<input type="text" name="Item_PageNum" id="Item_PageNum" maxlength="3" size="3">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 محتوا
	</td>
	<td nowrap>
	<textarea name="Item_content" id="Item_content" cols="80" rows="7"><? echo $content ?></textarea><a href='#' onclick="javascript: FixString();">پالایش</a>
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='ManageTermReferenceContent.php?TermReferenceID=<?php echo $_REQUEST["TermReferenceID"]; ?>'" value="جدید">
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
$NumberOfRec = 30;
 $k=0;
$PageNumber = 0;
if(isset($_REQUEST["PageNumber"]))
{
	if(!is_numeric($PageNumber))
		$PageNumber = 0;
	else
		$PageNumber = $_REQUEST["PageNumber"];
	$FromRec = $PageNumber*$NumberOfRec;
}
else
{
	$FromRec = 0; 
}
$res = manage_TermReferenceContent::GetList($_REQUEST["TermReferenceID"], $FromRec, $NumberOfRec); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->TermReferenceContentID])) 
	{
		manage_TermReferenceContent::Remove($res[$k]->TermReferenceContentID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_TermReferenceContent::GetList($_REQUEST["TermReferenceID"], $FromRec, $NumberOfRec); 
?>
<form id="ListForm" name="ListForm" method="post"> 
	<input type="hidden" id="Item_TermReferenceID" name="Item_TermReferenceID" value="<? echo htmlentities($_REQUEST["TermReferenceID"], ENT_QUOTES, 'UTF-8'); ?>">
<? if(isset($_REQUEST["PageNumber"]))
	echo "<input type=\"hidden\" name=\"PageNumber\" value=".$_REQUEST["PageNumber"].">"; ?>
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="6">
	محتوای پاراگرافهای مراجع
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>صفحه</td>
	<td>واژگان مستخرج</td>
	<td>محتوا</td>
	
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->TermReferenceContentID."\">";
	echo "</td>";
	echo "<td>".($k+$FromRec+1)."</td>";
	echo "	<td><a href=\"ManageTermReferenceContent.php?UpdateID=".$res[$k]->TermReferenceContentID."&TermReferenceID=".$_REQUEST["TermReferenceID"]."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td>".htmlentities($res[$k]->PageNum, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td><a href=\"ManageTermReferenceMapping.php?TermReferenceID=".$_REQUEST["TermReferenceID"]."&PageNum=".$res[$k]->PageNum."\">واژگان مستخرج</a></td>";
	echo "	<td>".str_replace("\r", "<br>", htmlentities($res[$k]->content, ENT_QUOTES, 'UTF-8'))."</td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="6" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
</td>
</tr>
<tr bgcolor="#cccccc"><td colspan="6" align="right">
<?
for($k=0; $k<manage_TermReferenceContent::GetCount($_REQUEST["TermReferenceID"])/$NumberOfRec; $k++)
{
	if($PageNumber!=$k)
		echo "<a href='javascript: ShowPage(".($k).")'>";
	echo ($k+1);
	if($PageNumber!=$k)
		echo "</a>";
	echo " ";
}
?>
</td></tr>
</table>
</form>
<form target="_blank" method="post" action="NewTermReferenceContent.php" id="NewRecordForm" name="NewRecordForm">
	<input type="hidden" id="TermReferenceID" name="TermReferenceID" value="<? echo htmlentities($_REQUEST["TermReferenceID"], ENT_QUOTES, 'UTF-8'); ?>">
</form>
<form method="post" name="f2" id="f2">
<input type="hidden" name="PageNumber" id="PageNumber" value="0">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
function ShowPage(PageNumber)
{
	f2.PageNumber.value=PageNumber; 
	f2.submit();
}

function FixString()
{
  var str = document.getElementById('Item_content').value;
  //document.getElementById('Item_content').value = str.replace('ـ', '');

   document.getElementById('Item_content').value = str.replace(new RegExp('ـ', 'g'), '');
  
}

</script>
</html>
