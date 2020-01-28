<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : کلاسهای هستان نگار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-29
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/OntologyClasses.class.php");
include_once("classes/OntologyClassLabels.class.php");
include_once("classes/OntologyClassHirarchy.class.php");
include_once("classes/OntologyProperties.class.php");
include_once("classes/ontologies.class.php");
HTMLBegin();
echo manage_ontologies::ShowSummary($_REQUEST["OntologyID"]);
$res = manage_OntologyClasses::GetList($_REQUEST["OntologyID"]); 
?>
<br><table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="6">
	کلاسهای هستان نگار
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%">ردیف</td>
	<td>عنوان کلاس</td>
	<td nowrap>برچسبها</td>
	<td nowrap>زیر کلاسها</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
    $LabelsList = "";
    $list = manage_OntologyClassLabels::GetList($res[$k]->OntologyClassID);
    for($m=0; $m<count($list); $m++)
    {
      if($m>0)
	$LabelsList .= ", ";
      $LabelsList .= $list[$m]->label;
    }
    
    $SubClassesList = "";
    $list = manage_OntologyClassHirarchy::GetList($res[$k]->OntologyClassID);
    for($m=0; $m<count($list); $m++)
    {
      if($m>0)
	$SubClassesList .= ", ";
      $SubClassesList .= $list[$m]->OntologyClassParentID_Desc;
    }
    
    if($k%2==0)
	    echo "<tr class=\"OddRow\">";
    else
	    echo "<tr class=\"EvenRow\">";
    echo "<td>".($k+1)."</td>";
    echo "	<td dir=ltr>".htmlentities($res[$k]->ClassTitle, ENT_QUOTES, 'UTF-8')."</td>";
    echo "	<td>".$LabelsList." ";
    echo " </td>";
    echo "<td dir=ltr>".$SubClassesList." ";
    echo "</td>";
    echo "</tr>";
}
echo "</table>";
$res = manage_OntologyProperties::GetList($_REQUEST["OntologyID"]); 
?>
<table width="90%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">
	<td colspan="10">
	خصوصیات هستان نگار
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%"> </td>
	<td width="1%">ردیف</td>
	<td width="2%">ویرایش</td>
	<td>عنوان</td>
	<td width=1%>نوع</td>
	<td width=1%>Functional</td>
	<td>حوزه</td>
	<td>بازه</td>
	<td>معکوس</td>
	<td nowrap>برچسبها </td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
    $LabelsList = "";
    $list = manage_OntologyPropertyLabels::GetList($res[$k]->OntologyPropertyID);
    for($m=0; $m<count($list); $m++)
    {
      if($m>0)
	$LabelsList .= ", ";
      $LabelsList .= $list[$m]->label;
    }

	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>".($k+1)."</td>";
	echo "	<td>".htmlentities($res[$k]->PropertyTitle, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".$res[$k]->PropertyType_Desc."</td>";
	echo "	<td>".$res[$k]->IsFunctional_Desc."</td>";
	echo "	<td>".htmlentities($res[$k]->domain, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".htmlentities($res[$k]->range, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>".htmlentities($res[$k]->inverseOf, ENT_QUOTES, 'UTF-8')."</td>";
	echo "<td nowrap>".$LabelsList." ";
	echo "</td>";
	echo "</tr>";
}
?>
</table>
</html>
