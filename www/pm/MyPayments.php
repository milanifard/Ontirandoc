<?php 
/*
 گزارش کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-07-04
*/
include("header.inc.php");
HTMLBegin();
$mysql = pdodb::getInstance();
?>
	<br>
	<table width=80% align=center border=1 cellspacing=0 cellpadding=3>
	<tr class=HeaderOfTable>
		<td colspan=4 align=center>	 گزارش دریافتی های من 		</td>
	</tr>
	<tr bgcolor=#cccccc>
	  <td width=20%>	تاریخ	</td>
	  <td width=20%>	مبلغ	</td>
	  <td width=60%>	شرح	</td>
	  <td width=1%>	فایل	</td>
	</tr>
<?
    $query = " select PaymentID, amount, PaymentFileName, PaymentDescription, projectmanagement.g2j(PaymentDate) as gDate from projectmanagement.payments
		where 
		PersonID='".$_SESSION["PersonID"]."'
		order by PaymentDate DESC";
    $res = $mysql->Execute($query);
    while($rec = $res->fetch())
    {
      echo "<tr><td nowrap>".$rec["gDate"]."</td>";
      echo "<td>".number_format($rec["amount"])."</td>";
      echo "<td>".$rec["PaymentDescription"]."</td>";
      echo "<td>";
      if($rec["PaymentFileName"]!="")
	echo "<a href='DownloadFile.php?FileType=payments&RecID=".$rec["PaymentID"]."'>فایل </a>";
      else {
	  echo "&nbsp;";
      }
      echo "</td>";
      echo "</tr>";
    }
?>
</table> 
</html>
