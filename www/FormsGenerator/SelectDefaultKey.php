<?php
include("header.inc.php");
HTMLBegin();
$mysql = dbclass::getInstance();
?>
<table width=80% align=center border=1 cellpadding=3 cellspacing=0>
<tr class=HeaderOfTable>
<td width=20%>نام کلید</td>
<td>شرح</td>
</tr>
<tr>
	<td><a href='javascript: Select("#CURRENT_DATE#");'>#CURRENT_DATE#</td>
	<td>تاریخ روز جاری (برای فیلدهایی که از نوع تاریخ هستند)</td>
</tr>
<tr>
	<td><a href='javascript: Select("#CURRENT_USERID#");'>#CURRENT_USERID#</td>
	<td>نام کاربر جاری</td>
</tr>
<tr>
	<td><a href='javascript: Select("#CURRENT_PERSON_ID#");'>#CURRENT_PERSON_ID#</td>
	<td>کد شخصی کاربر جاری</td>
</tr>
<tr>
	<td><a href='javascript: Select("#IP_ADDRESS#");'>#IP_ADDRESS#</td>
	<td>آدرس IP کامپیوتر مشتری</td>
</tr>
<tr>
	<td><a href='javascript: Select("#CUR_EDU_YEAR#");'>#CUR_EDU_YEAR#</td>
	<td>سال تحصیلی جاری</td>
</tr>
<tr>
	<td><a href='javascript: Select("#CUR_SEMESTER#");'>#CUR_SEMESTER#</td>
	<td>نیمسال تحصیلی جاری</td>
</tr>
<tr>
	<td><a href='javascript: Select("#CUR_YEAR#");'>#CUR_YEAR#</td>
	<td>سال جاری</td>
</tr>
<tr>
	<td><a href='javascript: Select("#CUR_MONTH#");'>#CUR_MONTH#</td>
	<td>ماه جاری</td>
</tr>
<tr>
	<td><a href='javascript: Select("#CUR_DAY#");'>#CUR_DAY#</td>
	<td>روز جاری</td>
</tr>
<tr>
	<td><a href='javascript: Select("#PRE_YEAR#");'>#PRE_YEAR#</td>
	<td>سال پیش</td>
</tr>
<tr>
	<td><a href='javascript: Select("#PRE_MONTH#");'>#PRE_MONTH#</td>
	<td>ماه پیش</td>
</tr>

</table>
<script>
	function Select(DefaultKey)
	{
		window.opener.document.f1.Item_DefaultValue.value=DefaultKey;
		window.close();
	}
</script>
</body>
</html>
