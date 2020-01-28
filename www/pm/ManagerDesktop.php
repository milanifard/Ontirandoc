<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : پیامها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-5
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/messages.class.php");
include_once("classes/ProjectTasks.class.php");
include_once("classes/PrivateMessageFollows.class.php");
HTMLBegin();
$res = manage_messages::GetActiveMessages(); 
$LettersCount = manage_PrivateMessageFollows::GetNewMessagesCount();
if(count($res)>0)
{
?>
  <table align="center" cellspacing="0" class="table-sm col-lg-11 table-stripped">
  <tr class="table-active">
	  <td colspan="8">
	  <? echo C_MESSAGES ?>
	  </td>
  </tr>
<?php
  for($k=0; $k<count($res); $k++)
  {
	  echo "<tr>";
	  echo "	<td><a target=_blank href='ShowMessagePhoto.php?MessageID=".$res[$k]->MessageID."'><img src='ShowMessagePhoto.php?MessageID=".$res[$k]->MessageID."' width=50></a></td>";
	  echo "	<td>".str_replace("\r", "<br>", htmlentities($res[$k]->MessageBody, ENT_QUOTES, 'UTF-8'));
	  if($res[$k]->RelatedFileName!="")
	    echo "	<br><a href='DownloadFile.php?FileType=messages&RecID=".$res[$k]->MessageID."'>".C_ATTACHMENTS."</a>";
	  echo "</td>";
	  
	  
	  echo "	<td nowrap>".$res[$k]->CreatorID_FullName."</td>";
	  echo "	<td nowrap>".$res[$k]->CreateDate_Shamsi."</td>";
	  echo "</tr>";
  }

}
?>
  </table>
<br>
<table align="center" cellspacing="0" class="table-responsive-lg table-bordered col-lg-11 table-success">
<tr>
  <td><? echo C_RECEIVED_LETTERS.": " ?><a href='MailBox.php'><b><? echo $LettersCount ?></b></a></td>
</tr>
</table>
<br>

<?php
/*
$messages = manage_ProjectTasks::GetTotalLastSystemMessage();
if($messages!="")
{
	echo "<table width=98% align=center border=1 cellspacing=0>";
	echo "<tr><td colspan=5><b>آخرین عملیات انجام شده روی کارها<b></td></tr>";
	echo "<tr class=HeaderOfTable><td width=1% nowrap>زمان</td><td width=10% nowrap>عمل انجام شده</td><td width=10% nowrap>کاربر مربوطه</td><td width=10%>پروژه</td><td>عنوان کار مربوطه</td></tr>";
	echo $messages;
	echo "</table>";
}
*/
?>
</html>
