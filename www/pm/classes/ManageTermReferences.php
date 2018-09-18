<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : مراجع اصطلاحات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-6
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/TermReferences.class.php");
HTMLBegin();
if($_SESSION["UserID"]!="omid")
{
  echo "It's restricted";
  die();
}
if(isset($_REQUEST["ActionType"]) && $_REQUEST["ActionType"]=="STAT")
{
  $mysql = pdodb::getInstance();
  $query = "select TermTitle, count(*) as tcount from 
	    projectmanagement.terms 
	    JOIN projectmanagement.TermReferenceMapping using (TermID)
	    where TermReferenceID=1 
	    group by TermTitle";
  if(isset($_REQUEST["SortBy"]))
    $query .= " order by ".$_REQUEST["SortBy"];
  else {
    $query .= " order by TermTitle";
  }
  //echo $query."<br>";
  $res = $mysql->Execute($query);
  echo "<table align=center border=1 cellspacing=0 cellpadding=5>";
  echo "<tr bgcolor=#cccccc><tD>ردیف</td><td><a href='ManageTermReferences.php?ActionType=STAT&SortBy=TermTitle'>اصطلاح</a></td>";
  echo "<td><a href='ManageTermReferences.php?ActionType=STAT&SortBy=count(*) DESC'>فراوانی</td></tr>";
  $i = 0;
  while($rec = $res->fetch())
  {
    $i++;
    echo "<tr><td>".$i."</td><td>".$rec["TermTitle"]."</td><td>".$rec["tcount"]."</td></tr>";
  }
  echo "</table>";
  die();
}

if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["Item_title"]))
		$Item_title=$_REQUEST["Item_title"];
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
	if(isset($_REQUEST["Item_RelatedFileName"]))
		$Item_RelatedFileName=$_REQUEST["Item_RelatedFileName"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_TermReferences::Add($Item_title
				, $Item_FileContent
				, $Item_RelatedFileName
				);
	}	
	else 
	{	
		manage_TermReferences::Update($_REQUEST["UpdateID"] 
				, $Item_title
				, $Item_FileContent
				, $Item_RelatedFileName
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_TermReferences();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_title.value='".htmlentities($obj->title, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
?>
<form method="post" id="f1" name="f1" enctype="multipart/form-data" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
		//echo manage_TermReferences::ShowSummary($_REQUEST["UpdateID"]);
		//echo manage_TermReferences::ShowTabs($_REQUEST["UpdateID"], "NewTermReferences");
	}
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش منابع اصطلاحات</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr>
	<td width="1%" nowrap>
 عنوان
	</td>
	<td nowrap>
	<input type="text" name="Item_title" id="Item_title" maxlength="100" size="40">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 فایل
	</td>
	<td nowrap>
	<input type="file" name="Item_FileContent" id="Item_FileContent">
	<? if(isset($_REQUEST["UpdateID"]) && $obj->RelatedFileName!="") { ?>
	<a href='DownloadFile.php?FileType=TermReferences&FieldName=FileContent&RecID=<? echo $_REQUEST["UpdateID"]; ?>'>دریافت فایل [<?php echo $obj->RelatedFileName; ?>]</a>
	<? } ?>
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='ManageTermReferences.php';" value="جدید">
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
$res = manage_TermReferences::GetList(); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->TermReferenceID])) 
	{
		manage_TermReferences::Remove($res[$k]->TermReferenceID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_TermReferences::GetList(); 
?>
<form id="ListForm" name="ListForm" method="post"> 
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="7">
	منابع اصطلاحات
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>عنوان</td>
	<td>فایل</td>
	<td width=1%>محتوا</td>
	<td width=1% nowrap>اصطلاحات</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->TermReferenceID."\">";
	echo "</td>";
	echo "<td>".($k+1)."</td>";
	echo "	<td><a href=\"ManageTermReferences.php?UpdateID=".$res[$k]->TermReferenceID."\"><img src='images/edit.gif' title='ویرایش'></a></td>";
	echo "	<td>".htmlentities($res[$k]->title, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td><a href='DownloadFile.php?FileType=TermReferences&FieldName=FileContent&RecID=".$res[$k]->TermReferenceID."'><img src='images/Download.gif'></a></td>";
	echo "<td width=1% nowrap><a  href='ManageTermReferenceContent.php?TermReferenceID=".$res[$k]->TermReferenceID ."'>محتوا</a></td>";
	echo "<td width=1% nowrap><a  href='ManageTermReferenceMapping.php?TermReferenceID=".$res[$k]->TermReferenceID ."'>اصطلاحات</a></td>";
	echo "</tr>";
}
?>
<input type=hidden name='ActionType' id='ActionType' value='REMOVE'>
<tr class="FooterOfTable">
<td colspan="7" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
	<input type="button" onclick="javascript: ConfirmAnalyze();" value="تحلیل آماری">
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewTermReferences.php" id="NewRecordForm" name="NewRecordForm">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
}
function ConfirmAnalyze()
{
    document.getElementById('ActionType').value = 'STAT';
    document.ListForm.submit();
}
</script>
</html>
