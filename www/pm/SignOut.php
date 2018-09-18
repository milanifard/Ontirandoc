<?php
	session_start();
	session_destroy();
	$Referer = "";
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css" > INPUT, SELECT { font-family: Tahoma }'.
</style>
<link rel="stylesheet"  href="../template/css/login.css" type="text/css">
</head>
<body  dir=rtl link="#0000FF" alink="#0000FF" vlink="#0000FF">
<br>
<form method=post>
<table with=80% align=center border=1 celspacing=0 cellpadding=15>
<tr>
	<td align=center>
		<font size=5>
		<? if(isset($_REQUEST["SessionExpired"])) { $Referer="?Referer=".$_SERVER["HTTP_REFERER"]; ?>
		<font color=red>
		نشست شما منقضی شده است
		</font>
		<? } else { ?>
		خوش آمدید
		<? } ?>
		<br>
		<br>
		برای ورود مجدد
		<a href='login.php<?php echo $Referer; ?>'>
		اینجا</a> را کلیک کنید
	</td>
</tr>
</table>
</form>
</body>
