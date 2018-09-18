<?php
include("header.inc.php");
include("PAS_shared_utils.php");

HTMLBegin();
$mysql = dbclass::getInstance();
$FromRec = 0;
if(isset($_REQUEST["FromRec"]))
	$FromRec = $_REQUEST["FromRec"];
$ItemsCount = 15;
$query = "select count(ATS) from projectmanagement.SysAudit where UserID='".$_SESSION["UserID"]."' ";
$res = $mysql->Execute($query);
$TotalCount = 0;
if($rec = $res->FetchRow())
	$TotalCount = $rec[0];
$query = "select *, concat(g2j(ATS), ' ', substr(ATS, 12,5)) as gATS from projectmanagement.SysAudit where UserID='".$_SESSION["UserID"]."' order by ATS DESC limit $FromRec, $ItemsCount";
$list = "";
$res = $mysql->Execute($query);
$i = 0;
while($rec = $res->FetchRow())
{
	$i++;
	if($i%2==0)
		$list .= "<tr class=OddRow>";
	else
		$list .= "<tr class=EvenRow>";
	$list .= "<td>".($FromRec+$i)."</td>";
	$list .= "<td>".$rec["ActionDesc"]."</td>";
	$list .= "<td nowrap>".$rec["gATS"]."</td>";
	$list .= "</tr>";
}
?>
<br>
<form method="post" id=f1 name=f1>
<input type=hidden name=FromRec id=FromRec value='<?php echo $FromRec ?>'>
<table border="1" cellspacing="0" align=center width=80%>
	<tr>
		<td>
			<table border="1" cellspacing="0" align=center width=100%>
			<tr class=HeaderOfTable>
				<td width=1%>ردیف</td>
				<td>عمل انجام شده</td>
				<td>زمان انجام</td>
			</tr>
			<?php echo $list ?>
			</table>
		</td>
	</tr>
	<tr>
		<td>
		تعداد کل موارد یافت شده: <?php echo $TotalCount; ?>
		<br>
		صفحه: 
		<?php
			for($PageNumber=1; $PageNumber<=($TotalCount/$ItemsCount)+1; $PageNumber++)
			{
				if(($PageNumber-1)*$ItemsCount==$FromRec)
					echo "<b>";
				else
					echo "<a href='#' onclick='javascript: GoPage(".($PageNumber-1)*$ItemsCount.");'>";
				echo $PageNumber;
				if(($PageNumber-1)*$ItemsCount==$FromRec)
					echo "</b>";
				else
					echo "</a>";
				echo "&nbsp; "; 
				if($PageNumber%30==0)
					echo "<br>";
			}
		?>
		</td>
	</tr>
</table>
</form>
<script>
	function GoPage(FromRec)
	{
		document.f1.FromRec.value=FromRec;
		f1.submit();
	}
</script>

</body>
</html>