<?php
include("header.inc.php");
HTMLBegin();
$mysql = dbclass::getInstance();
?>
<br>
<table width=80% align=center border=1 cellpadding=3 cellspacing=0>
<tr bgcolor=#cccccc>
<td colspan=2 align=center>
<b>
<?php echo $_REQUEST["DomainName"]; ?>
</b>
</td>
</tr>
<tr class=HeaderOfTable>
<td width=5%>مقدار</td>
<td>شرح</td>
</tr>
<?php 
	$res = $mysql->Execute("select * from domains where DomainName='".$_REQUEST["DomainName"]."' order by description");
	while($rec = $res->FetchRow())
	{
		echo "<tr>";
		echo "<td>".$rec["DomainValue"]."</td>";
		echo "<td>".$rec["description"]."</td>";
		echo "</tr>";
	}
?>
</table>
</body>
</html>
