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
//if($_SESSION["UserID"]!="omid")
//{
//  echo "It's restricted";
//  die();
//}
$mysql = pdodb::getInstance();
if(isset($_REQUEST["STermTitle"]))
{
  $query = "select distinct TermReferenceContentID, TermReferenceContent.PageNum, content from projectmanagement.terms 
	    JOIN projectmanagement.TermReferenceMapping using (TermID)
	    JOIN projectmanagement.TermReferenceContent on (TermReferenceContent.TermReferenceID=1 and TermReferenceMapping.PageNum=TermReferenceContent.PageNum)
	    where TermReferenceMapping.TermReferenceID=1 and TermTitle=?";

  echo "<table align=center border=1 cellspacing=0 cellpadding=5>";
  echo "<tr bgcolor=#cccccc><tD width=1%>".C_PAGE."</td><td>".C_CONTENT."</td></td>";
  $mysql->Prepare($query);
  $res = $mysql->ExecuteStatement(array($_REQUEST["STermTitle"]));
  while($rec = $res->fetch())
  {
    echo "<tr>";
    echo "<td>".$rec["PageNum"]."</td>";
    echo "<td>".str_replace("\n", "<br>", str_replace($_REQUEST["STermTitle"], "<b>".$_REQUEST["STermTitle"]."</b>", $rec["content"]))."</td>";
    echo "</tr>";
  }
  die();
}
if(isset($_REQUEST["ActionType"]) && $_REQUEST["ActionType"]=="STAT")
{
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
  echo "<tr bgcolor=#cccccc><td>".C_ROW."</td><td><a href='ManageTermReferences.php?ActionType=STAT&SortBy=TermTitle'>".C_TERM."</a></td>";
  echo "<td><a href='ManageTermReferences.php?ActionType=STAT&SortBy=count(*) DESC'>".C_FREQUENCY."</td></tr>";
  $i = 0;
  while($rec = $res->fetch())
  {
    $i++;
    echo "<tr><td>".$i."</td><td>".$rec["TermTitle"]."</td><td><a target=_blank href='ManageTermReferences.php?STermTitle=".$rec["TermTitle"]."'>".$rec["tcount"]."</a></td></tr>";
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
			echo C_SENDING_FILE_ERROR. $_FILES['Item_FileContent']['error'];
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
	echo SharedClass::CreateMessageBox('<? echo C_INFORMATION_SAVED; ?>');
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
<td align="center"><? echo C_CREATE_EDIT_TERMS_REFERENCES; ?></td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr>
	<td width="1%" nowrap>
 <? echo C_TITLE; ?>
	</td>
	<td nowrap>
	<input type="text" name="Item_title" id="Item_title" maxlength="100" size="40">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 <? echo C_FILE2; ?>
	</td>
	<td nowrap>
	<input type="file" name="Item_FileContent" id="Item_FileContent">
	<? if(isset($_REQUEST["UpdateID"]) && $obj->RelatedFileName!="") { ?>
	<a href='DownloadFile.php?FileType=TermReferences&FieldName=FileContent&RecID=<? echo $_REQUEST["UpdateID"]; ?>'><? echo C_GET_FILE; ?> [<?php echo $obj->RelatedFileName; ?>]</a>
	<? } ?>
	</td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="<? echo C_SAVE; ?>">
 <input type="button" onclick="javascript: document.location='ManageTermReferences.php';" value="<? echo C_NEW; ?>">
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
	<? echo C_TERMS_REFERENCES; ?>
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">	<? echo C_ROW; ?></td>
	<td width="2%">	<? echo C_EDIT; ?></td>
	<td><? echo C_TITLE; ?></td>
	<td><? echo C_FILE2; ?></td>
	<td width=1%><? echo C_CONTENT; ?></td>
	<td width=1% nowrap><? echo C_TERMS; ?></td>
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
	echo "	<td><a href=\"ManageTermReferences.php?UpdateID=".$res[$k]->TermReferenceID."\"><img src='images/edit.gif' title=".C_EDIT."></a></td>";
	echo "	<td>".htmlentities($res[$k]->title, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td><a href='DownloadFile.php?FileType=TermReferences&FieldName=FileContent&RecID=".$res[$k]->TermReferenceID."'><img src='images/Download.gif'></a></td>";
	
	$res2 = $mysql->Execute("select count(*) as tcount from projectmanagement.TermReferenceContent where TermReferenceID=".$res[$k]->TermReferenceID);
	$rec2 = $res2->fetch();
	$ContentCount = $rec2["tcount"];

	$res2 = $mysql->Execute("select count(*) as tcount from projectmanagement.TermReferenceMapping where TermReferenceID=".$res[$k]->TermReferenceID);
	$rec2 = $res2->fetch();
	$MappingCount = $rec2["tcount"];
	
	echo "<td width=1% nowrap><a  href='ManageTermReferenceContent.php?TermReferenceID=".$res[$k]->TermReferenceID ."'>$ContentCount</a></td>";
	echo "<td width=1% nowrap><a  href='ManageTermReferenceMapping.php?TermReferenceID=".$res[$k]->TermReferenceID ."'>$MappingCount</a></td>";
	echo "</tr>";
}
?>
<input type=hidden name='ActionType' id='ActionType' value='REMOVE'>
<tr class="FooterOfTable">
<td colspan="7" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="<? echo C_REMOVE; ?>">
	<input type="button" onclick="javascript: ConfirmAnalyze();" value="<? echo C_STATISTICAL_ANALYSIS; ?>">
</td>
</tr>
</table>
</form>
<form target="_blank" method="post" action="NewTermReferences.php" id="NewRecordForm" name="NewRecordForm">
</form>
<script>
function ConfirmDelete()
{
	if(confirm('<? echo C_ARE_YOU_SURE; ?>')) document.ListForm.submit();
}
function ConfirmAnalyze()
{
    document.getElementById('ActionType').value = 'STAT';
    document.ListForm.submit();
}
</script>
</html>
