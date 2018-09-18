<?php
include("header.inc.php");
include("classes/FileTypeUserPermissions.class.php");
HTMLBegin();
function ShowTick($FieldValue)
{
	if($FieldValue=="YES")
		return "X";
	else
		return "&nbsp;";
}
$PageItemsCount = 50;
 $k=0;
$PageNumber = 0;
$ListCondition = " FileTypeID='".$_REQUEST["FileTypeID"]."' ";
if(isset($_REQUEST["PageNumber"]))
{
	$PageNumber = $_REQUEST["PageNumber"];
	$ListCondition .= " limit ".($_REQUEST["PageNumber"]*$PageItemsCount).",".$PageItemsCount; 
}
else
{
	$ListCondition .= " limit 0,".$PageItemsCount; 
}
$res = manage_FileTypeUserPermissions::GetList($ListCondition); 
echo "<form id=f1 name=f1 method=post>"; 
if(isset($_REQUEST["PageNumber"]))
	echo "<input type=hidden name=PageNumber value=".$_REQUEST["PageNumber"].">"; 
echo "<br><table width=98% align=center border=1 cellspacing=0>";
echo "<tr class=HeaderOfTable>";
echo "<td width=1% rowspan=2> </td>";
echo "	<td width=2% rowspan=2>کد</td>";
echo "	<td rowspan=2>نام کاربر</td>";
echo "	<td rowspan=2>محدوده دسترسی</td>";
echo "	<td colspan=7 align=center>مجوزها</td>";
echo "	<td colspan=4 rowspan=2>&nbsp;</td>";
echo "</tr><tr class=HeaderOfTable>";
echo "	<td width=1% nowrap>تعریف دسترسی</td>";
echo "	<td width=1% nowrap>اضافه  </td>";
echo "	<td width=1% nowrap>حذف </td>";
echo "	<td width=1% nowrap>بروزرسانی مشخصات</td>";
echo "	<td width=1% nowrap>بروزرسانی محتوا </td>";
echo "	<td width=1% nowrap>مشاهده </td>";
echo "	<td width=1% nowrap> ارسال موقت</td>";
echo "</tr>";
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->FileTypeUserPermissionID])) 
	{
		manage_FileTypeUserPermissions::Remove($res[$k]->FileTypeUserPermissionID); 
	}
	else
	{
		
		if($k%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "<td><input type=checkbox name=ch_".$res[$k]->FileTypeUserPermissionID."></td>";
		echo "	<td><a target=_blank href='NewFileTypeUser.php?UpdateID=".$res[$k]->FileTypeUserPermissionID."'>*".$res[$k]->FileTypeUserPermissionID."</a></td>";
		echo "	<td nowrap>".$res[$k]->PersonName."</td>";
		echo "	<td nowrap>";
		if($res[$k]->AccessRange=="ONLY_USER")
			echo "فقط پرونده هایی که خود کاربر ایجاد کرده";
		else if($res[$k]->AccessRange=="UNIT")
			echo "برخی واحدهای سازمانی";
		else if($res[$k]->AccessRange=="SUB_UNIT")
			echo "برخی زیر واحدهای سازمانی";
		else if($res[$k]->AccessRange=="EDU_GROUP")
			echo "برخی گروه های آموزشی";
		else
			echo "بدون محدودیت";
		echo "</td>";
		echo "	<td align=center>".ShowTick($res[$k]->DefineAccessPermission)."</td>";
		echo "	<td align=center>".ShowTick($res[$k]->AddPermission)."</td>";
		echo "	<td align=center>".ShowTick($res[$k]->RemovePermission)."</td>";
		echo "	<td align=center>".ShowTick($res[$k]->UpdatePermission)."</td>";
		echo "	<td align=center>".ShowTick($res[$k]->ContentUpdatePermission)."</td>";
		echo "	<td align=center>".ShowTick($res[$k]->ViewPermission)."</td>";
		echo "	<td align=center>".ShowTick($res[$k]->TemporarySendPermission)."</td>";
		if($res[$k]->AccessRange=="UNIT")
			echo "<td width=30><a target=_blank href='ManageFileTypeUserUnits.php?id=".$res[$k]->FileTypeUserPermissionID."'><img width=30 src='images/building.jpg' title='واحدهای سازمانی مجاز برای کاربر' border=0></td>";
		else
			echo "<td width=30>&nbsp;</td>";
		if($res[$k]->AccessRange=="SUB_UNIT")
			echo "<td width=30><a target=_blank href='ManageFileTypeUserSubUnits.php?id=".$res[$k]->FileTypeUserPermissionID."'><img width=30 src='images/subunit.jpg' title='زیر واحدهای سازمانی مجاز برای کاربر' border=0></td>";
		else
			echo "<td width=30>&nbsp;</td>";
		if($res[$k]->AccessRange=="EDU_GROUP")
			echo "<td width=30><a target=_blank href='ManageFileTypeUserEduGroups.php?id=".$res[$k]->FileTypeUserPermissionID."'><img width=30 src='images/group.jpg' title='گروه های آموزشی مجاز برای کاربر' border=0></td>";
		else
			echo "<td width=30>&nbsp;</td>";
		echo "<td width=30><a target=_blank href='ManageFileTypeUserForms.php?id=".$res[$k]->FileTypeUserPermissionID."'><img width=30 src='images/FieldsAccess.jpg' title='نوع دسترسی به فرمهای پرونده' border=0></td>";
		
		echo "</tr>";
	}
}
echo "<tr class=FooterOfTable><td colspan=16 align=center><input type=submit value='حذف'>&nbsp;";
echo "<input type=button value='جدید' onclick='javascript: window.open(\"NewFileTypeUser.php?FileTypeID=".$_REQUEST["FileTypeID"]."\");'>";
echo "&nbsp;<input type=button value='بازگشت' onclick='document.location=\"ManageFileTypes.php\"'>";
echo "</td></tr>";
echo "<tr bgcolor=#cccccc><td colspan=16 align=right>";
for($k=0; $k<manage_FileTypeUserPermissions::GetCount()/$PageItemsCount; $k++)
{
	if($PageNumber!=$k)
		echo "<a href='javascript: ShowPage(".($k).")'>";
	echo ($k+1);
	if($PageNumber!=$k)
		echo "</a>";
	echo " ";
}
echo "</td></tr>";
echo "</table>";
echo "</form>";
?>
<form method=post name=f2 id=f2>
<input type=hidden name=PageNumber id=PageNumber value=0>
</form>
<script>
function ShowPage(PageNumber)
{
	f2.PageNumber.value=PageNumber; 
	f2.submit();
}
</script>
</html>