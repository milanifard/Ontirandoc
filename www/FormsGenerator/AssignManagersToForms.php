<?php
include("header.inc.php");
include_once("classes/FormsStruct.class.php");
include_once("classes/FormManagers.class.php");
HTMLBegin();

$PageItemsCount = 200;
 $k=0;
$PageNumber = 0;
//$ListCondition = " 1=1 order by CreateDate DESC ";
$FormTitle = "";
if(isset($_REQUEST["FormTitle"]))
	$FormTitle = $_REQUEST["FormTitle"];
$ListCondition = " FormsStruct.FormTitle like '%".$FormTitle."%' order by CreateDate DESC ";
if(isset($_REQUEST["PageNumber"]))
{
	$PageNumber = $_REQUEST["PageNumber"];
	$ListCondition .= " limit ".($_REQUEST["PageNumber"]*$PageItemsCount).",".$PageItemsCount; 
}
else
{
	$ListCondition .= " limit 0,".$PageItemsCount; 
}
$res = manage_FormsStruct::GetList($ListCondition); 
echo "<br>";
?>
<form method=post>
<table align=center border=1 cellspacing=0>
<tr>
	<td>
	عنوان: <input type=text name='FormTitle' value='<?php echo $FormTitle ?>'> <input type=submit value='فیلتر'>
	</td>
</tr>
</table>
</form>
<br>
<?php 
echo "<form id=f1 name=f1 method=post>"; 
if(isset($_REQUEST["PageNumber"]))
	echo "<input type=hidden name=PageNumber value=".$_REQUEST["PageNumber"].">"; 
echo "<table width=98% align=center border=1 cellspacing=0>";
echo "<tr class=HeaderOfTable>";
echo "	<td width=1%>کد</td>";
echo "	<td width=5% nowrap>تنظیم</td>";
echo "	<td width=30%>عنوان فرم</td>";
echo "	<td width=30%>فرم اصلی</td>";
echo "	<td width=30%>مدیران</td>";
echo "	<td width=5% nowrap>ایجاد کننده</td>";
echo "	<td width=5% nowrap>تاریخ ایجاد</td>";

echo "</tr>";
for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		echo "<tr class=OddRow>";
	else
		echo "<tr class=EvenRow>";
	echo "	<td>";
	echo "	".$res[$k]->FormsStructID."</a></td>";
	echo "	<td nowrap>";
	echo "	<a href='SuperManageFormManagers.php?Item_FormStructID=".$res[$k]->FormsStructID."'><img title='تعریف مدیران این فرم' src='images/roles.gif' border=0 width=35></a>";
	echo "	</td>";
	echo "	<td>".$res[$k]->FormTitle."</td>";
	echo "	<td>&nbsp;".$res[$k]->ParentTitle."</td>";
	echo "	<td nowrap>";
	$managers = manage_FormManagers::GetList(" FormsStructID=".$res[$k]->FormsStructID);
	if(count($managers)==0)
		echo "&nbsp;";
	for($i=0; $i<count($managers); $i++)
	{
		echo $managers[$i]->PersonName." (".$managers[$i]->AccessType.")<br>";
		
	}
	echo "	</td>";		
	echo "	<td nowrap>".$res[$k]->CreatorUser."</td>";
	echo "	<td nowrap>".$res[$k]->CreateDate."</td>";
	echo "</tr>";
}
echo "<tr bgcolor=#cccccc><td colspan=17 align=right>";
for($k=0; $k<count($res)/$PageItemsCount; $k++)
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