<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : تنظیمات عمومی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 92-8-11
*/
include("header.inc.php");
HTMLBegin();
$mysql = pdodb::getInstance();
if(isset($_REQUEST["Save"])) 
{
  $PrintResultDescriptionCol = $PrintResultHowToCol = $PrintResultMaxMinCol = $PrintResultShowAllExams = "NO";
  
  if(isset($_REQUEST["PrintResultDescriptionCol"]))
    $PrintResultDescriptionCol = "YES";
  if(isset($_REQUEST["PrintResultHowToCol"]))
    $PrintResultHowToCol = "YES";
  if(isset($_REQUEST["PrintResultMaxMinCol"]))
    $PrintResultMaxMinCol = "YES";


  $mysql->Prepare("update projectmanagement.GlobalSettings
			  set LabTitle=?, LabDoctor=?, TaxPercent=?, FooterOfResponse=?, 
			  PrintResultCols=?, PrintResultDescriptionCol=?, PrintResultHowToCol=?, PrintResultMaxMinCol=?, 
			  PrintResultShowAllExams=?
			  ");
  $mysql->ExecuteStatement(array($_REQUEST["LabTitle"], $_REQUEST["LabDoctor"], $_REQUEST["TaxPercent"], $_REQUEST["FooterOfResponse"], 
				  $_REQUEST["PrintResultCols"], $PrintResultDescriptionCol, $PrintResultHowToCol, $PrintResultMaxMinCol,
				  $_REQUEST["PrintResultShowAllExams"]));
  echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$res = $mysql->Execute("select * from projectmanagement.GlobalSettings");
$rec = $res->fetch();
$LabTitle = $rec["LabTitle"];
$LabDoctor = $rec["LabDoctor"];
$TaxPercent = $rec["TaxPercent"];
$FooterOfResponse = $rec["FooterOfResponse"];
$PrintResultCols = $rec["PrintResultCols"];
$PrintResultDescriptionCol = $rec["PrintResultDescriptionCol"];
$PrintResultHowToCol = $rec["PrintResultHowToCol"];
$PrintResultMaxMinCol = $rec["PrintResultMaxMinCol"];
$PrintResultShowAllExams = $rec["PrintResultShowAllExams"];


?>
<form method="post" id="f1" name="f1" >
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">تنظیمات</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr>
	<td width="1%" nowrap>
نام آزمایشگاه
	</td>
	<td nowrap>
	<input type="text" name="LabTitle" id="LabTitle" value="<? echo $LabTitle ?>" maxlength="245" size="40">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 دکتر آزمایشگاه
	</td>
	<td nowrap>
	<input type="text" name="LabDoctor" id="LabDoctor" value="<? echo $LabDoctor ?>" maxlength="245" size="40">
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 مالیات بر ارزش افزوده
	</td>
	<td nowrap>
	<input type="text" name="TaxPercent" id="TaxPercent" value="<? echo $TaxPercent ?>" maxlength="2" size="2">%
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
پانویس نتیجه آزمایش
	</td>
	<td nowrap>
	<textarea rows=5 cols=80 id=FooterOfResponse name=FooterOfResponse><? echo $FooterOfResponse ?></textarea>
	</td>
</tr>
<tr bgcolor=#cccccc>
  <td colspan=2 align=center>
  تنظیمات صفحه چاپ نتیجه آزمایشات
  </td>
  <td>
  </td>
</tr>
<tr>
  <td nowrap>نوع نمایش</td>
  <td>
    <select name=PrintResultShowAllExams id=PrintResultShowAllExams>
    <option value='YES'>تمامی آزمایش ها در لیست نمایش داده شود
    <option value='NO' <? if($PrintResultShowAllExams=="NO") echo "selected"; ?> >فقط آزمایشهای انتخابی در لیست نمایش داده شود
    </select>
  </td>
</tr>

<tr>
  <td nowrap>تعداد آزمون در ردیف</td>
  <td><input name=PrintResultCols id=PrintResultCols type=text size=3 maxlength=1 value='<? echo $PrintResultCols ?>'></td>
</tr>
<tr>
  <td colspan=2>
  <input type=checkbox name=PrintResultMaxMinCol id=PrintResultMaxMinCol <? if($PrintResultMaxMinCol=="YES") echo "checked"; ?> >
  نمایش ستون حداقل و حداکثر مجاز
  </td>
</tr>
<tr>
  <td colspan=2>
  <input type=checkbox name=PrintResultHowToCol id=PrintResultHowToCol <? if($PrintResultHowToCol=="YES") echo "checked"; ?> >
  نمایش ستون روش انجام کار
  </td>
</tr>
<tr>
  <td colspan=2>
  <input type=checkbox name=PrintResultDescriptionCol id=PrintResultDescriptionCol <? if($PrintResultDescriptionCol=="YES") echo "checked"; ?> >
  نمایش ستون توضیحات
  </td>
</tr>
</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
 </td>
</tr>
</table>
<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
</script>
</html>
