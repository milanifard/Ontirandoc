<?php
include("header.inc.php");
include_once("classes/FileTypes.class.php");

HTMLBegin();
function ShowTick($FieldValue)
{
	if($FieldValue=="YES")
		return "X";
	else
		return "&nbsp;";
}
$PageItemsCount = 30;
 $k=0;
$PageNumber = 0;
$ListCondition = " 1=1 ";
if(isset($_REQUEST["PageNumber"]))
{
	$PageNumber = $_REQUEST["PageNumber"];
	$ListCondition .= " limit ".($_REQUEST["PageNumber"]*$PageItemsCount).",".$PageItemsCount; 
}
else
{
	$ListCondition .= " limit 0,".$PageItemsCount; 
}
$res = manage_FileTypes::GetList($ListCondition); 
echo "<form id=f1 name=f1 method=post>"; 
if(isset($_REQUEST["PageNumber"]))
	echo "<input type=hidden name=PageNumber value=".$_REQUEST["PageNumber"].">"; 
echo "<br><table width=98% align=center border=1 cellspacing=0>";
echo "<tr class=HeaderOfTable>";
echo "<td rowspan=2 width=1%> </td>";
echo "	<td rowspan=2 width=2%>کد</td>";
echo "	<td rowspan=2>نام</td>";
echo "	<td rowspan=2>مجاز به تغییر محل پرونده</td>";
echo "	<td rowspan=2>محل پرونده</td>";
echo "	<td rowspan=2>مربوط به اشخاص</td>";
echo "	<td align=center colspan=4>انواع مجاز اشخاص برای انتساب</td>";
echo "<td rowspan=2>کاربران</td>";
echo "<td rowspan=2>فرمها</td>";
echo "</tr><tr class=HeaderOfTable>";
echo "	<td>استاد</td>";
echo "	<td>کارمند</td>";
echo "	<td>دانشجو</td>";
echo "	<td>متفرقه</td>";

echo "</tr>";
for($k=0; $k<count($res); $k++)
{
	$sw = true;
	if(isset($_REQUEST["ch_".$res[$k]->FileTypeID])) 
	{
		if(manage_FileTypes::GetRelatedFileCount($res[$k]->FileTypeID)==0)
		{
			$sw = false;
			manage_FileTypes::Remove($res[$k]->FileTypeID);
		}
		else
			echo "<p align=center><font color=red>از این نوع پرونده [".$res[$k]->FileTypeName."] در سیستم پرونده هایی وجود دارد بنابراین قابل حذف نیست</font></p><br>"; 
	}
	if($sw)
	{
		if($k%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "<td><input type=checkbox name=ch_".$res[$k]->FileTypeID."></td>";
		echo "	<td><a target=_blank href='NewFileType.php?UpdateID=".$res[$k]->FileTypeID."'>".$res[$k]->FileTypeID."</a></td>";
		echo "	<td>".$res[$k]->FileTypeName."</td>";
		echo "	<td>".ShowTick($res[$k]->UserCanChangeLocation)."</td>";
		echo "	<td>";
		if($res[$k]->SetLocationType=="RELATED_PERSON")
			echo "با توجه محل کار فرد منتسب به پرونده";
		else if($res[$k]->SetLocationType=="CREATOR")
			echo "با توجه محل کار ایجاد کننده پرونده";
		else if($res[$k]->SetLocationType=="NONE")
			echo "اتومات تنظیم نمی شود";
		echo "</td>";
		echo "	<td align=center>".ShowTick($res[$k]->RelatedToPerson)."</td>";		
		echo "	<td align=center>".ShowTick($res[$k]->RelatedPersonCanBeProffessor)."</td>";
		echo "	<td align=center>".ShowTick($res[$k]->RelatedPersonCanBeStaff)."</td>";
		echo "	<td align=center>".ShowTick($res[$k]->RelatedPersonCanBeStudent)."</td>";
		echo "	<td align=center>".ShowTick($res[$k]->RelatedPersonCanBeOther)."</td>";
		echo "<td><a href='ManageFileTypeUsers.php?FileTypeID=".$res[$k]->FileTypeID."'><img src='images/users.gif' border=0 width=30></a></td>";
		echo "<td><a href='ManageFileTypeForms.php?FileTypeID=".$res[$k]->FileTypeID."'><img src='images/list2.gif' border=0 width=30></a></td>";
		echo "</tr>";
	}
}
echo "<tr class=FooterOfTable><td colspan=12 align=center><input type=submit value='حذف'>";
echo "&nbsp;";
echo "<input type=button value='جدید' onclick='javascript: window.open(\"NewFileType.php\");'>";
echo "</td></tr>";
echo "<tr bgcolor=#cccccc><td colspan=12 align=right>";
for($k=0; $k<manage_FileTypes::GetCount()/$PageItemsCount; $k++)
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