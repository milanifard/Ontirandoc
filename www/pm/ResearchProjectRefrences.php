<?php 
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : مراجع کار پژوهشی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 93-3-5
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ResearchProjectRefrences.class.php");
include ("classes/ResearchProject.class.php");
HTMLBegin();
$res = manage_ResearchProjectRefrences::Search($_REQUEST["ResearchProjectID"] , $RefrenceTitle, $URL, $ReadType, $RefrenceTypeID, ""); 
?>
<style>
  td 
  { font-family: tahoma;
  font-size: 11px;
  }
</style>
<table width="98%" align="center" border="1" cellspacing="0" margin="0">
<tr bgcolor="#cccccc">
	<td colspan="11">
	مراجع کار پژوهشی
	</td>
</tr>
<tr class="HeaderOfTable">
	<td width="1%">ردیف</td>
	<td>سال</td>
	<td>عنوان</td>
	<td>نظر کلی</td>
</tr>
<?
for($k=0; $k<count($res); $k++)
{
    if($res[$k]->BriefComment!="")
    {
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "<td>".($k+1)."</td>";
	echo "</a></td>";
	echo "	<td>&nbsp;".htmlentities($res[$k]->PublishYear, ENT_QUOTES, 'UTF-8')."</td>";
	if($res[$k]->language=="FA")
	  echo "	<td dir=rtl>";
	else 
	  echo "	<td dir=ltr>";

	  
	echo htmlentities($res[$k]->RefrenceTitle, ENT_QUOTES, 'UTF-8')."</td>";
	echo "	<td>&nbsp;".str_replace("\r", "<br>", htmlentities($res[$k]->BriefComment, ENT_QUOTES, 'UTF-8'))."</td>";
	echo "</tr>";
      }
}
?>
</table>
</html>
