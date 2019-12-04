<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : گردش پیامهای شخصی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-22
*/
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', "on");


//
//
//
//
//
//THIS FILE IS TAKEN BY MOHAMAD_ALI_SAIDI PLEASE BE CAREFUL  !
//
//
//
//
//
//











include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/PrivateMessageFollows.class.php");

HTMLBegin();
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
$OrderByFieldName = "PrivateMessageFollowID";
$OrderType = "DESC";
if(isset($_REQUEST["OrderByFieldName"]))
{
	$OrderByFieldName = $_REQUEST["OrderByFieldName"];
	$OrderType = $_REQUEST["OrderType"];
}
$res = manage_PrivateMessageFollows::GetList("InBox", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType); 
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->PrivateMessageFollowID])) 
	{
		manage_PrivateMessageFollows::Archive($res[$k]->PrivateMessageFollowID); 
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_PrivateMessageFollows::GetList("InBox", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType); 

?>




<form id="ListForm" name="ListForm" method="post">

<?php if(isset($_REQUEST["PageNumber"]))
	echo "<input type=\"hidden\" name=\"PageNumber\" value=".$_REQUEST["PageNumber"].">"; ?>

<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="6">
	 نامه های رسیده
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
<?php
  $OrderType1 = $OrderType2 = "ASC";
  if(isset($_REQUEST["OrderByFieldName"]))
  {
    if($_REQUEST["OrderByFieldName"]=="FromPersonID" && $_REQUEST["OrderType"]=="ASC")
      $OrderType1 = "DESC";
    if($_REQUEST["OrderByFieldName"]=="ReferTime" && $_REQUEST["OrderType"]=="ASC")
      $OrderType2 = "DESC";
  }
?>

	<td width=1%><a href="javascript: Sort('FromPersonID', '<? echo $OrderType1 ?>');">فرستنده</a></td>
	<td width=1% nowrap><a href="javascript: Sort('ReferTime', '<? echo $OrderType2 ?>');">زمان ارسال</a></td>
	<td>عنوان</td>
	<td>شرح ارجاع</td>
</tr>
<?php
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	if($res[$k]->ReferStatus=="NOT_READ")
	  $NewMail = "<b>";
	else 
	  $NewMail = "";
	echo "<td>";
	echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->PrivateMessageFollowID."\">";
	echo "</td>";
	echo "<td>".$NewMail;
	echo "<a href=\"ShowMessage.php?MessageFollowID=".$res[$k]->PrivateMessageFollowID."\">";
	echo ($k+$FromRec+1)."</td>";
	echo "	<td nowrap>".$NewMail.htmlentities($res[$k]->FromPersonID_FullName, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td nowrap>".$NewMail.htmlentities($res[$k]->ReferTime_Shamsi, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".$NewMail.htmlentities($res[$k]->MessageTitle, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>&nbsp;".$NewMail.htmlentities($res[$k]->comment, ENT_QUOTES, 'UTF-8')."</td>";
	echo "</tr>";
}
?>

<tr class="FooterOfTable">
<td colspan="6" align="center">
	<input type="button" onclick="javascript: ConfirmDelete();" value="حذف">
</td>
</tr>
<tr bgcolor="#cccccc"><td colspan="6" align="right">

<?php
for($k=0; $k<manage_PrivateMessageFollows::GetCount("InBox")/$NumberOfRec; $k++)
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
<form target="_blank" method="post" action="NewPrivateMessageFollows.php" id="NewRecordForm" name="NewRecordForm">
</form>
<form method="post" name="f2" id="f2">
<input type="hidden" name="PageNumber" id="PageNumber" value="0">
<input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
<input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
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
function Sort(OrderByFieldName, OrderType)
{
	f2.OrderByFieldName.value=OrderByFieldName; 
	f2.OrderType.value=OrderType; 
	f2.submit();
}
</script>
