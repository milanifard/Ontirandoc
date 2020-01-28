<?php
include("header.inc.php");
include_once("classes/FormsStruct.class.php");
include_once("classes/FormManagers.class.php");
HTMLBegin();
$PageItemsCount = 60;
 $k=0;
$PageNumber = 0;
//$ListCondition = " 1=1 order by CreateDate DESC ";
$FormTitle = "";
if(isset($_REQUEST["FormTitle"]))
	$FormTitle = $_REQUEST["FormTitle"];
$ListCondition = " (FormsStruct.CreatorUser='".$_SESSION["UserID"]."' or FormsStruct.FormsStructID in (select FormsStructID from FormManagers where PersonID='".$_SESSION["PersonID"]."')) and FormsStruct.FormTitle like '%".$FormTitle."%' order by FormsStruct.CreateDate DESC ";
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
if(isset($_REQUEST["FormTitle"]))
 	echo "<input type=hidden name=FormTitle value=".$_REQUEST["FormTitle"].">";
echo "<table width=98% align=center border=1 cellspacing=0>";
echo "<tr class=HeaderOfTable>";
echo "<td width=1%>&nbsp;</td>";
echo "	<td width=1%>کد</td>";
echo "	<td width=30%>عنوان فرم</td>";
echo "	<td width=30%>فرم اصلی</td>";
echo "	<td width=30%>مدیران</td>";
echo "	<td width=10% nowrap>تنظیمات</td>";
echo "	<td width=5% nowrap>ایجاد کننده</td>";
echo "	<td width=5% nowrap>تاریخ ایجاد</td>";

echo "</tr>";
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->FormsStructID])) 
	{
		manage_FormsStruct::Remove($res[$k]->FormsStructID); 
	}
	else
	{
		// هر شخص فقط فرمهای ایجاد شده توسط خودش را ببیند
		// فعلا جهت رفع مشکلات و پاسخگویی کاربر omid هم دسترسی به فرمهای بقیه خواهد داشت
		//if($_SESSION["UserID"]==$res[$k]->CreatorUser || $_SESSION["UserID"]=="omid")
		//if($_SESSION["UserID"]==$res[$k]->CreatorUser || $_SESSION["UserID"]=="omid")
		{
			if($k%2==0)
				echo "<tr class=OddRow>";
			else
				echo "<tr class=EvenRow>";
			echo "<td><input type=checkbox name=ch_".$res[$k]->FormsStructID."></td>";
			echo "	<td>";
			echo "	<a href='NewFormStruct.php?UpdateID=".$res[$k]->FormsStructID."'>";
			echo "	".$res[$k]->FormsStructID."</a></td>";
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
			echo "	<td nowrap>";
			echo "	<a href='ManageFormFields.php?FormsStructID=".$res[$k]->FormsStructID."'><img title='مدیریت فیلدها' src='images/Fields.gif' border=0 width=35></a>";
			echo "	<a href='ManageFormDetailTables.php?Item_FormStructID=".$res[$k]->FormsStructID."'><img title='مدیریت جداول جزییات' src='images/Tables.gif' border=0 width=35></a>";
			echo "	<a href='ManageFormFlow.php?Item_FormStructID=".$res[$k]->FormsStructID."'><img title='مدیریت جریان کاری' src='images/chart.gif' border=0 width=35></a>";
			echo "	<a target=_blank href='PrintFormFlowChart.php?FormsStructID=".$res[$k]->FormsStructID."'><img title='چارت جریان کاری' src='images/chart.jpg' border=0 ></a>";
			echo "	<a href='ManageFormManagers.php?Item_FormStructID=".$res[$k]->FormsStructID."'><img title='تعریف مدیران این فرم' src='images/roles.gif' border=0 width=35></a>";
			echo "	</td>";
			echo "	<td nowrap>".$res[$k]->CreatorUser."</td>";
			echo "	<td nowrap>".$res[$k]->CreateDate."</td>";
			echo "</tr>";
		}
	}
}
echo "<tr class=FooterOfTable><td colspan=17 align=center><input type=submit value='حذف'>&nbsp;<input type=button value='ایجاد' onclick='javascript: document.location=\"NewFormStruct.php\";'></tr>";
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