<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : پیامهای شخصی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-21
*/
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', "on");

include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/PrivateMessageFollows.class.php");
HTMLBegin();
$headers = 'From: falinoos@falinoos.com';
if(isset($_REQUEST["MessageFollowID"]))
{
  $MessageFollowID = $_REQUEST["MessageFollowID"];
  $CurMail = new be_PrivateMessageFollows();
  $CurMail->Load($MessageFollowID);
  //echo "++".$CurMail->FromPersonID."**";
  $IsPermitted = manage_PrivateMessages::IsPermitted($CurMail->PrivateMessageID);
  if(!$IsPermitted)
  {
    echo "Not permitted";
    die();
  }
}
else {
    echo "error!"; die();
}
$CurMail->SetToRead();
if(isset($_REQUEST["Item_ToPersonID"]))
{
  $Item_ToPersonID = $_REQUEST["Item_ToPersonID"];
  $Item_MessageBody = $_REQUEST["Item_MessageBody"];
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
  $FollowID = manage_PrivateMessageFollows::Add($CurMail->PrivateMessageID
		  , $Item_MessageBody
		  , $_SESSION["PersonID"]
		  , $Item_ToPersonID
		  , $CurMail->PrivateMessageFollowID
		  , $Item_FileContent
		  , $Item_FileName
		  , "NOT_READ"
		  , 0
		  );

  $MessageSubject = "نامه ای در بخش مکاتبات سامانه ی مدیریت پروژه به شما ارجاع شده است";
  $Message‌Body = "موضوع نامه: ";
  $Message‌Body .= $CurMail->MessageTitle."\r\n";
  $Message‌Body .= "متن نامه: \r\n".$CurMail->MessageBody."\r\n";
  $Message‌Body .= "دسترسی به نامه با لینک زیر: \r\n";
  $Message‌Body .= "<a target=_blank href='http://pm.falinoos.com/pm/ShowMessage.php?MessageFollowID=".$FollowID."'>مشاهده</a>";
  
  $mysql->Prepare("select * from projectmanagement.persons where PersonID=?");
  $res = $mysql->ExecuteStatement(array($Item_ToPersonID));
  if($rec = $res->fetch())
  {
    if($rec["CardNumber"]!="")
      mail($rec["CardNumber"], $MessageSubject, $Message‌Body, $headers);
  }
		  
 echo "<script>document.location='MailBox.php';</script>";
 die();
}
?>
<form method=post id=f1 name=f1  enctype="multipart/form-data" >
<table width=98% border=1 cellspacing=0>
<tr>
<td>

<table width=100% border=0>
<tr>
  <td width=10%>فرستنده: </td><td><? echo $CurMail->FromPersonID_FullName; ?></td>
</tr>
<tr>
  <td>عنوان: </td><td><? echo $CurMail->MessageTitle; ?></td>
</tr>
<tr>
  <td>متن: </td><td><? echo str_replace("\n", "<br>", $CurMail->MessageBody); ?></td>
</tr>
<tr>
  <td>ضمیمه: </td>
  <td>
    &nbsp;
    <?
      if($CurMail->FileName!="")
	echo "<a href='DownloadFile.php?FileType=PrivateMessages&RecID=".$CurMail->PrivateMessageID."'>".$CurMail->FileName."</a>";
    ?>
  </td>
</tr>
<tr>
  <td colspan=2>
  <?
    /*
    $i=0;
    $UpperLevelID = $CurMail->UpperLevelID;
    if($UpperLevelID>0)
    {
      echo "<br>";
      echo "<b>سابقه ارجاعات: </b><br>";
      echo "(".$CurMail->ReferTime_Shamsi.") از: ".$CurMail->FromPersonID_FullName." به: ".$CurMail->ToPersonID_FullName." شرح ارجاع: ".$CurMail->comment;
      if($CurMail->ReferFileName!="")
	echo " <a href='DownloadFile.php?FileType=PrivateMessageFollows&RecID=".$CurMail->PrivateMessageFollowID."'>".ضمیمه."</a>";
      echo "<br>";
      while($UpperLevelID>0 && $i<20)
      {
	
	$UpperMail = new be_PrivateMessageFollows();
	$UpperMail->Load($UpperLevelID);
	if($UpperMail->UpperLevelID>0)
	{
	  echo "(".$UpperMail->ReferTime_Shamsi.") از: ".$UpperMail->FromPersonID_FullName." به: ".$UpperMail->ToPersonID_FullName." شرح ارجاع: ".$UpperMail->comment;
	  if($UpperMail->ReferFileName!="")
	    echo " <a href='DownloadFile.php?FileType=PrivateMessageFollows&RecID=".$UpperMail->PrivateMessageFollowID."'>".ضمیمه."</a>";
	  echo "<br>";
	}
	$UpperLevelID = $UpperMail->UpperLevelID;
	$i++;
      }
      echo "<br>";
     }
     */
  ?>
  </td>
</tr>
<tr id="tr_ToPersonID" name="tr_ToPersonID" style='display:'>
      <td width="1%" nowrap>
      <font color=red>*</font> 
		  ارجاع به
      </td>
	<td nowrap>
	<input type=hidden name="Item_ToPersonID" id="Item_ToPersonID" value="<? echo $CurMail->FromPersonID ?>">
	<span id="Span_ToPersonID_FullName" name="Span_ToPersonID_FullName"><? echo $CurMail->FromPersonID_FullName ?></span> 	<a href='#' onclick='javascript: window.open("SelectStaff.php?InputName=Item_ToPersonID&SpanName=Span_ToPersonID_FullName");'>[انتخاب]</a>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 متن ارجاع
	</td>
	<td nowrap>
	<textarea name="Item_MessageBody" id="Item_MessageBody" cols="80" rows="5"></textarea>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
ضمیمه ارجاع
	</td>
	<td nowrap>
	<input type="file" name="Item_FileContent" id="Item_FileContent">
	</td>
</tr>
</table>

</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ارجاع">
&nbsp;
<input type=button value='آرشیو' onclick='javascript: DeleteMail();'>
&nbsp;
<? if(isset($_REQUEST["BackPage"]) && $_REQUEST["BackPage"]=="SearchMessage") { ?>
<input type=button value='بستن' onclick='window.close();'>
<? } else { ?>
<input type=button value='بازگشت' onclick='document.location="<? if(isset($_REQUEST["BackPage"])) echo $_REQUEST["BackPage"]; else echo "MailBox.php"; ?>"'>
<? } ?>
&nbsp;

</td>
</tr>

</table>
</form>
<br>
<table border=0 cellspacing=1 cellpadding=5>
<tr class=HeaderOfTable>
  <td align=center><b>سابقه ی نامه</b></td>
</tr>
<tr>
<td>
<?
  echo manage_PrivateMessages::CreateTree($CurMail->PrivateMessageID);
?>
</td>
</tr>
</table>
<form method=post id=f2 id=f2 action='MailBox.php'>
  <input type=hidden name="ch_<? echo $CurMail->PrivateMessageFollowID ?>" value="1">
</form>
<script>
	function DeleteMail()
	{
	  if(confirm('از آرشیو نامه اطمینان دارید؟'))
	    document.getElementById('f2').submit();
	}
	
	function ValidateForm()
	{
	  if(document.getElementById('Item_ToPersonID').value=="0")
	  {
	    alert('گیرنده را تعیین کنید');
	    return;
	  }
	  document.f1.submit();
	}
</script>