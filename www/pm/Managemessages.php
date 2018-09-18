<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : پیامها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-5
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/messages.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["Item_MessageBody"]))
		$Item_MessageBody=$_REQUEST["Item_MessageBody"];
	if(isset($_REQUEST["Item_RelatedFileName"]))
		$Item_RelatedFileName=$_REQUEST["Item_RelatedFileName"];
	$Item_FileContent = "";
	$Item_RelatedFileName = "";
	if (trim($_FILES['Item_FileContent']['name']) != '')
	{
		if ($_FILES['Item_FileContent']['error'] != 0)
		{
			echo ' خطا در ارسال فایل' . $_FILES['Item_FileContent']['error'];
		}
		else
		{
			$_size = $_FILES['Item_FileContent']['size'];
			$_name = $_FILES['Item_FileContent']['tmp_name'];
			$Item_FileContent = addslashes((fread(fopen($_name, 'r' ),$_size)));
			$Item_RelatedFileName = trim($_FILES['Item_FileContent']['name']);
		}
	}
	if(isset($_REQUEST["Item_ImageFileName"]))
		$Item_ImageFileName=$_REQUEST["Item_ImageFileName"];
	$Item_ImageFileContent = "";
	$Item_ImageFileName = "";
	if (trim($_FILES['Item_ImageFileContent']['name']) != '')
	{
		if ($_FILES['Item_ImageFileContent']['error'] != 0)
		{
			echo ' خطا در ارسال فایل' . $_FILES['Item_ImageFileContent']['error'];
		}
		else
		{
			$_size = $_FILES['Item_ImageFileContent']['size'];
			$_name = $_FILES['Item_ImageFileContent']['tmp_name'];
			$Item_ImageFileContent = addslashes((fread(fopen($_name, 'r' ),$_size)));
			$Item_ImageFileName = trim($_FILES['Item_ImageFileContent']['name']);
		}
	}
	if(isset($_REQUEST["Item_CreatorID"]))
		$Item_CreatorID=$_REQUEST["Item_CreatorID"];
	if(isset($_REQUEST["StartDate_DAY"]))
	{
		$Item_StartDate = SharedClass::ConvertToMiladi($_REQUEST["StartDate_YEAR"], $_REQUEST["StartDate_MONTH"], $_REQUEST["StartDate_DAY"]);
	}
	if(isset($_REQUEST["EndDate_DAY"]))
	{
		$Item_EndDate = SharedClass::ConvertToMiladi($_REQUEST["EndDate_YEAR"], $_REQUEST["EndDate_MONTH"], $_REQUEST["EndDate_DAY"]);
	}
	if(isset($_REQUEST["Item_CreateDate"]))
		$Item_CreateDate=$_REQUEST["Item_CreateDate"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_messages::Add($Item_MessageBody
				, $Item_FileContent
				, $Item_RelatedFileName
				, $Item_ImageFileContent
				, $Item_ImageFileName
				, $Item_StartDate
				, $Item_EndDate
				);
	}	
	else 
	{	
		manage_messages::Update($_REQUEST["UpdateID"] 
				, $Item_MessageBody
				, $Item_FileContent
				, $Item_RelatedFileName
				, $Item_ImageFileContent
				, $Item_ImageFileName
				, $Item_StartDate
				, $Item_EndDate
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
$TMessageBody = "";
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_messages();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$TMessageBody = htmlentities($obj->MessageBody, ENT_QUOTES, 'UTF-8');
	if($obj->StartDate_Shamsi!="date-error") 
	{
		$LoadDataJavascriptCode .= "document.f1.StartDate_YEAR.value='".substr($obj->StartDate_Shamsi, 2, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.StartDate_MONTH.value='".substr($obj->StartDate_Shamsi, 5, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.StartDate_DAY.value='".substr($obj->StartDate_Shamsi, 8, 2)."'; \r\n "; 
	}
	if($obj->EndDate_Shamsi!="date-error") 
	{
		$LoadDataJavascriptCode .= "document.f1.EndDate_YEAR.value='".substr($obj->EndDate_Shamsi, 2, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.EndDate_MONTH.value='".substr($obj->EndDate_Shamsi, 5, 2)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.EndDate_DAY.value='".substr($obj->EndDate_Shamsi, 8, 2)."'; \r\n "; 
	}
}	
?>
<form method="post" id="f1" name="f1" enctype="multipart/form-data" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش پیامها</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr>
	<td width="1%" nowrap>
 متن پیام
	</td>
	<td nowrap>
	<textarea name="Item_MessageBody" id="Item_MessageBody" cols="80" rows="5"><? echo $TMessageBody ?></textarea>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 فایل ضمیمه
	</td>
	<td nowrap>
	<input type="file" name="Item_FileContent" id="Item_FileContent">
	<? if(isset($_REQUEST["UpdateID"]) && $obj->RelatedFileName!="") { ?>
	<a href='DownloadFile.php?FileType=messages&RecID=<? echo $_REQUEST["UpdateID"]; ?>'>دریافت فایل [<?php echo $obj->RelatedFileName; ?>]</a>
	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 تصویر
	</td>
	<td nowrap>
	<input type="file" name="Item_ImageFileContent" id="Item_ImageFileContent">
	<? if(isset($_REQUEST["UpdateID"]) && $obj->ImageFileName!="") { ?>
	<img width=50 src='ShowMessagePhoto.php?MessageID=<? echo $_REQUEST["UpdateID"]; ?>'>
	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 زمان شروع نمایش
	</td>
	<td nowrap>
	<input maxlength="2" id="StartDate_DAY"  name="StartDate_DAY" type="text" size="2">/
	<input maxlength="2" id="StartDate_MONTH" name="StartDate_MONTH" type="text" size="2" >/
	<input maxlength="2" id="StartDate_YEAR" name="StartDate_YEAR" type="text" size="2" >
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 زمان پایان نمایش
	</td>
	<td nowrap>
	<input maxlength="2" id="EndDate_DAY"  name="EndDate_DAY" type="text" size="2">/
	<input maxlength="2" id="EndDate_MONTH" name="EndDate_MONTH" type="text" size="2" >/
	<input maxlength="2" id="EndDate_YEAR" name="EndDate_YEAR" type="text" size="2" >
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='Managemessages.php';" value="جدید">
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
$NumberOfRec = 100;
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
if(isset($_REQUEST["SearchAction"])) 
{
	$OrderByFieldName = "MessageID";
	$OrderType = "";
	if(isset($_REQUEST["OrderByFieldName"]))
	{
		$OrderByFieldName = $_REQUEST["OrderByFieldName"];
		$OrderType = $_REQUEST["OrderType"];
	}
	$MessageBody=htmlentities($_REQUEST["Item_MessageBody"], ENT_QUOTES, 'UTF-8');
	$CreateDate=htmlentities($_REQUEST["Item_CreateDate"], ENT_QUOTES, 'UTF-8');
} 
else
{ 
	$OrderByFieldName = "MessageID";
	$OrderType = "DESC";
	$MessageBody='';
	$CreateDate='';
}
$res = manage_messages::Search($MessageBody, $CreateDate, "", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->MessageID])) 
	{
		manage_messages::Remove($res[$k]->MessageID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_messages::Search($MessageBody, $CreateDate, "", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType); 
?>
<form id="SearchForm" name="SearchForm" method=post> 
<input type="hidden" name="PageNumber" id="PageNumber" value="0">
<input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
<input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
<input type="hidden" name="SearchAction" id="SearchAction" value="1"> 
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr class="HeaderOfTable">
<td><img src='images/search.gif'><b><a href="#" onclick='javascript: if(document.getElementById("SearchTr").style.display=="none") document.getElementById("SearchTr").style.display=""; else document.getElementById("SearchTr").style.display="none";'>جستجو</a></td>
</tr>
<tr id='SearchTr' style='display: none'>
<td>
<table width="100%" align="center" border="0" cellspacing="0">
<tr>
	<td width="1%" nowrap>
 متن پیام
	</td>
	<td nowrap>
	<textarea name="Item_MessageBody" id="Item_MessageBody" cols="80" rows="5"></textarea>
	</td>
</tr>


<tr class="HeaderOfTable">
<td colspan="2" align="center"><input type="submit" value="جستجو"></td>
</tr>
</table>
</td>
</tr>
</table>
</form>
<? 
if(isset($_REQUEST["SearchAction"])) 
{
?>
<script>
		document.SearchForm.Item_MessageBody.value='<? echo htmlentities($_REQUEST["Item_MessageBody"], ENT_QUOTES, 'UTF-8'); ?>';
		document.SearchForm.Item_CreateDate.value='<? echo htmlentities($_REQUEST["Item_CreateDate"], ENT_QUOTES, 'UTF-8'); ?>';
</script>
<?
}
?> 
<form id="ListForm" name="ListForm" method="post"> 
<? if(isset($_REQUEST["PageNumber"]))
	echo "<input type=\"hidden\" name=\"PageNumber\" value=".$_REQUEST["PageNumber"].">"; ?>
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="10">
	پیامها
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td><a href="javascript: Sort('MessageBody', 'ASC');">متن پیام</a></td>
	<td>فایل ضمیمه</td>
	<td>تصویر</td>
	<td><a href="javascript: Sort('CreatorID', 'ASC');">ایجاد کننده</a></td>
	<td><a href="javascript: Sort('CreateDate', 'ASC');">زمان ایجاد</a></td>
	<td><a href="javascript: Sort('StartDate', 'ASC');">شروع نمایش</a></td>
	<td><a href="javascript: Sort('EndDate', 'ASC');">پایان نمایش</a></td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->MessageID."\">";
	echo "</td>";
	echo "<td>".($k+$FromRec+1)."</td>";
	echo "	<td><a href=\"Managemessages.php?UpdateID=".$res[$k]->MessageID."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td>".str_replace("\r", "<br>", htmlentities($res[$k]->MessageBody, ENT_QUOTES, 'UTF-8'))."</td>";
	echo "	<td><a href='DownloadFile.php?FileType=messages&RecID=".$res[$k]->MessageID."'><img src='images/Download.gif'></a></td>";
	echo "	<td><a target=_blank href='ShowMessagePhoto.php?MessageID=".$res[$k]->MessageID."'><img src='ShowMessagePhoto.php?MessageID=".$res[$k]->MessageID."' width=50></a></td>";
	echo "	<td>".$res[$k]->CreatorID_FullName."</td>";
	echo "	<td>".$res[$k]->CreateDate_Shamsi."</td>";
	echo "	<td>".$res[$k]->StartDate_Shamsi."</td>";
	echo "	<td>".$res[$k]->EndDate_Shamsi."</td>";
	echo "</tr>";
}
?>
<tr class="FooterOfTable">
<td colspan="10" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
</td>
</tr>
<tr bgcolor="#cccccc"><td colspan="10" align="right">
<?
$TotalCount = manage_messages::SearchResultCount($MessageBody, $CreateDate, ""); 
for($k=0; $k<$TotalCount/$NumberOfRec; $k++)
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
<form target="_blank" method="post" action="Newmessages.php" id="NewRecordForm" name="NewRecordForm">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
function ShowPage(PageNumber)
{
	SearchForm.PageNumber.value=PageNumber; 
	SearchForm.submit();
}
function Sort(OrderByFieldName, OrderType)
{
	SearchForm.OrderByFieldName.value=OrderByFieldName; 
	SearchForm.OrderType.value=OrderType; 
	SearchForm.submit();
}
</script>
</html>
