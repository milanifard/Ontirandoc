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
<div class="row">
	<div class="col-1"></div>
	<div class="col-10">
		<table class="table table-bordered table-sm table-striped" align=center cellspacing=0 cellpadding=3>
			<tr class="table-info">
				<td colspan=4 align=center> <? echo C_MY_REPORTS ?> </td>
			</tr>
			<tr bgcolor=#cccccc>
				<td width=20%> <? echo C_MY_DATE ?> </td>
				<td width=20%> <? echo C_MY_PRICE ?> </td>
				<td width=60%> <? echo C_MY_INFO ?> </td>
				<td width=1%> <? echo C_MY_FILE ?> </td>
			</tr>

			<?
			$query = " select PaymentID, amount, PaymentFileName, PaymentDescription, projectmanagement.g2j(PaymentDate) as gDate from projectmanagement.payments
		where 
		PersonID='" . $_SESSION["PersonID"] . "'
		order by PaymentDate DESC";
			$res = $mysql->Execute($query);
			while ($rec = $res->fetch()) {
				echo "<tr><td nowrap>" . $rec["gDate"] . "</td>";
				echo "<td>" . number_format($rec["amount"]) . "</td>";
				echo "<td>" . $rec["PaymentDescription"] . "</td>";
				echo "<td>";
				if ($rec["PaymentFileName"] != "")
					echo "<a href='DownloadFile.php?FileType=payments&RecID=" . $rec["PaymentID"] . "'>فایل </a>";
				else {
					echo "&nbsp;";
				}
				echo "</td>";
				echo "</tr>";
			}
			?>
		</table>
	</div>
	<div class="col-1"></div>
</div>
</html>