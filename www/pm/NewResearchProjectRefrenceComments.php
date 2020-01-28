<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : یادداشتهای مراجع کار پژوهشی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 93-3-11
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/ResearchProjectRefrenceComments.class.php");
include_once("classes/ResearchProjectRefrences.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["Item_CommentBody"]))
		$Item_CommentBody=$_REQUEST["Item_CommentBody"];
	if(isset($_REQUEST["Item_ResearchProjectSessionID"]))
		$Item_ResearchProjectSessionID=$_REQUEST["Item_ResearchProjectSessionID"];
	if(isset($_REQUEST["Item_CreateDate"]))
		$Item_CreateDate=$_REQUEST["Item_CreateDate"];
	if(isset($_REQUEST["ResearchProjectRefrenceID"]))
		$Item_ResearchProjectRefrenceID=$_REQUEST["ResearchProjectRefrenceID"];

	$Item_FileContent = "";
	$Item_FileName = "";
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
			$Item_FileName = trim($_FILES['Item_FileContent']['name']);
		}
	}
		
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_ResearchProjectRefrenceComments::Add($Item_CommentBody
				, $Item_ResearchProjectSessionID
				, $Item_ResearchProjectRefrenceID
				, $Item_FileContent
				, $Item_FileName
				);
	}	
	else 
	{	
		manage_ResearchProjectRefrenceComments::Update($_REQUEST["UpdateID"] 
				, $Item_CommentBody
				, $Item_ResearchProjectSessionID
				, $Item_FileContent
				, $Item_FileName
				);
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';

$CommentBody = "";
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_ResearchProjectRefrenceComments();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$CommentBody = htmlentities($obj->CommentBody, ENT_QUOTES, 'UTF-8'); 
	$LoadDataJavascriptCode .= "document.f1.Item_ResearchProjectSessionID.value='".htmlentities($obj->ResearchProjectSessionID, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	

?>
<form method="post" id="f1" name="f1" enctype="multipart/form-data" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
	echo manage_ResearchProjectRefrences::ShowSummary($_REQUEST["ResearchProjectRefrenceID"]);
	
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش یادداشت مراجع کار پژوهشی</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr>
	<td width="1%" nowrap>
 متن
	</td>
	<td nowrap>
	<textarea name="Item_CommentBody" id="Item_CommentBody" cols="100" rows="15"><? echo $CommentBody; ?></textarea>
	</td>
	#BS,#BE,#US,#UE,#RS,#RE
</tr>
<tr>
	<td width="1%" nowrap>
 فصل
	</td>
	<td nowrap>
	<select name="Item_ResearchProjectSessionID" id="Item_ResearchProjectSessionID">
	<option value=0>-
	<?  echo SharedClass::CreateARelatedTableSelectOptions("projectmanagement.ResearchProjectSessions", "ResearchProjectSessionID", "SessionTitle", "SessionTitle"); ?>	</select>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 تصویر
	</td>
	<td nowrap>
	<input type="file" name="Item_FileContent" id="Item_FileContent">
	<? if(isset($_REQUEST["UpdateID"]) && $obj->RelatedPhotoName!="") { ?>
	<img src='ShowRPhoto.php?TableName=ResearchProjectRefrenceComments&ConditionField=ResearchProjectRefrenceCommentID&FieldName=RelatedPhoto&RecID=<? echo $_REQUEST["UpdateID"]; ?>&DownloadFileName=<? echo $obj->FileName ?>'>
	<? } ?>
	</td>
</tr>

<? 

if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="ResearchProjectRefrenceID" id="ResearchProjectRefrenceID" value='<? if(isset($_REQUEST["ResearchProjectRefrenceID"])) echo htmlentities($_REQUEST["ResearchProjectRefrenceID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 <input type="button" onclick="javascript: document.location='NewResearchProjectRefrenceComments.php?ResearchProjectRefrenceID=<?php echo $_REQUEST["ResearchProjectRefrenceID"]; ?>'" value="جدید">
 <input type="button" onclick="javascript: window.close();" value="بستن">
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
<br>
<? if(isset($_REQUEST["UpdateID"])) 
{	
?>

<table width=90% align=center border=1 cellspacing=0>
  <tr class=HeaderOfTable>
    <td colspan=2>سابقه ی تغییرات روی این یادداشت</td>
  </tr>
  <tr>
    <td width=5%>تاریخ</td><td>متن</td>
  </tr>
  <?
    $res = manage_ResearchProjectRefrenceComments::GetHistory($_REQUEST["UpdateID"]);
    while($rec = $res->fetch())
    {
      echo "<tr>";
      echo "<td>".str_replace("\n", "<br?", $rec["sChangeDate"])."</td>";
      echo "<td>".$rec["LastCommentBody"]."</td>";
      echo "</tr>";
    }
  ?>
</table>
<? } ?>


</html>
