<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : پیامها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-5
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/messages.class.php");
include("classes/ProjectTasks.class.php");
include("classes/PrivateMessageFollows.class.php");
HTMLBegin();
$res = manage_messages::GetActiveMessages(); 
$LettersCount = manage_PrivateMessageFollows::GetNewMessagesCount();
if(count($res)>0) 
{
?>
  <table width="90%" align="center" border="1" cellspacing="0">
  <tr bgcolor="#cccccc">
	  <td colspan="8">
	  پیامها
	  </td>
  </tr>
<?
  for($k=0; $k<count($res); $k++)
  {
	  if($k%2==0)
		  echo "<tr class=\"OddRow\">";
	  else
		  echo "<tr class=\"EvenRow\">";
	  echo "	<td><a target=_blank href='ShowMessagePhoto.php?MessageID=".$res[$k]->MessageID."'><img src='ShowMessagePhoto.php?MessageID=".$res[$k]->MessageID."' width=50></a></td>";		
	  echo "	<td>".str_replace("\r", "<br>", htmlentities($res[$k]->MessageBody, ENT_QUOTES, 'UTF-8'));
	  if($res[$k]->RelatedFileName!="")
	    echo "	<br><a href='DownloadFile.php?FileType=messages&RecID=".$res[$k]->MessageID."'>ضمیمه</a>";
	  echo "</td>";
	  
	  
	  echo "	<td nowrap>".$res[$k]->CreatorID_FullName."</td>";
	  echo "	<td nowrap>".$res[$k]->CreateDate_Shamsi."</td>";
	  echo "</tr>";
  }
  echo "</table>";
}
?>
<br>
<table width="90%" align="center" border="1" cellspacing="0">
<tr>
  <td>نامه های رسیده: <a href='MailBox.php'><b><? echo $LettersCount ?></b></a></td>
</tr>
</table>
<br>

<?
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
