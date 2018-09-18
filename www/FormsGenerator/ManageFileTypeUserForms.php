<?php
include("header.inc.php");
include("classes/FileTypeUserPermissions.class.php");
include("classes/FileTypeUserPermittedForms.class.php");
HTMLBegin();
$ParentObj = new be_FileTypeUserPermissions();
$ParentObj->LoadDataFromDatabase($_REQUEST["id"]);
$ListCondition = " FileTypeUserPermittedForms.FileTypeUserPermissionID='".$_REQUEST["id"]."' ";
$res = manage_FileTypeUserPermittedForms::GetList($ListCondition); 
echo "<input type=hidden name=id id=id value='".$_REQUEST["id"]."'>";
echo "<form id=f1 name=f1 method=post>"; 
echo "<br><table align=center border=1 cellspacing=0 width=50%>";
echo "<tr class=HeaderOfTable><td align=center colspan=7>تعریف فرمهای مجاز برای <b>".$ParentObj->PersonName."</b></td></tr>";
echo "<tr class=HeaderOfTable>";
echo "<td width=1%> </td>";
echo "	<td width=2%>کد</td>";
echo "	<td>فرم</td>";
echo "	<td nowrap>مجوز اضافه</td>";
echo "	<td nowrap>مجوز حذف </td>";
echo "	<td width=30>&nbsp;</td>";
echo "</tr>";
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->FileTypeUserPermittedFormID])) 
	{
		manage_FileTypeUserPermittedForms::Remove($res[$k]->FileTypeUserPermittedFormID); 
	}
	else
	{
		if($k%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "<td><input type=checkbox name=ch_".$res[$k]->FileTypeUserPermittedFormID."></td>";
		echo "	<td><a target=_blank href='NewFileTypeUserForm.php?FileTypeUserPermissionID=".$_REQUEST["id"]."&UpdateID=".$res[$k]->FileTypeUserPermittedFormID."'>".$res[$k]->FileTypeUserPermittedFormID."</a></td>";
		echo "	<td nowrap>".$res[$k]->FormTitle."</td>";
		echo "	<td>".$res[$k]->AddFormPermission."</td>";
		echo "	<td>".$res[$k]->RemoveFormPermission."</td>";
		echo "	<td width=30><a target=_blank href='ManageFileTypeUserFormDetails.php?id=".$res[$k]->FileTypeUserPermittedFormID."'><img width=30 src='images/FieldsAccess.jpg' title='مجوزهای دسترسی به آیتمهای فرم' border=0></td>";
		echo "</tr>";
	}
}
echo "<tr class=FooterOfTable><td colspan=7 align=center><input type=submit value='حذف'>";
echo "&nbsp;<input type=button value='اضافه' onclick='javascript: window.open(\"NewFileTypeUserForm.php?FileTypeUserPermissionID=".$_REQUEST["id"]."\");'>";
echo "</tr>";
echo "</table>";
echo "</form>";
?>
</html>