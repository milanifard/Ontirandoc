<?
	include("header.inc.php");
	HTMLBegin();
?>
<br>
<form method=post>
<input type=hidden name='InputName' value='<?= $_REQUEST["InputName"] ?>'>
<input type=hidden name='SpanName' value='<?= $_REQUEST["SpanName"] ?>'>
<?php 
 if(isset($_REQUEST["UnitSpanName"]))
 {
 	?>
<input type=hidden name='UnitInputName' value='<?= $_REQUEST["UnitInputName"] ?>'>
<input type=hidden name='UnitSpanName' value='<?= $_REQUEST["UnitSpanName"] ?>'>
<input type=hidden name='SubUnitInputName' value='<?= $_REQUEST["SubUnitInputName"] ?>'>
<input type=hidden name='SubUnitSpanName' value='<?= $_REQUEST["SubUnitSpanName"] ?>'>
<input type=hidden name='EduGrpInputName' value='<?= $_REQUEST["EduGrpInputName"] ?>'>
<input type=hidden name='EduGrpSpanName' value='<?= $_REQUEST["EduGrpSpanName"] ?>'>
 	
 	<?php 	
 }
?>
<table width=80% align=center border=1 cellspacing=0 cellpadding=3>
<tr>
	<td>نام: </td><td><input type=text name=FName onkeypress="return submitenter(this, event);"></td>
</tr>
<tr>
	<td>نام خانوادگی: </td><td><input type=text name=LName onkeypress="return submitenter(this, event);"></td>
</tr>
<tr class='FooterOfTable'>
	<td colspan=2 align=center><input type=submit value='جستجو'></td>
</tr>
</table>
<br>
<table width=80% align=center border=1 cellspacing=0 cellpadding=3>
<tr class='HeaderOfTable'>
	<td>نام و نام خانوادگی</td>
</tr>
<?
	$mysql = pdodb::getInstance();
	if(isset($_REQUEST["FName"]))
	{
		$query = "select * from projectmanagement.persons 
								where plname like '%".$_REQUEST["LName"]."%' and pfname like '%".$_REQUEST["FName"]."%' limit 0,200";
		$mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array());
		$i = 0;
		while($arr_res=$res->fetch())
		{
			$i++;
			if($i>200)
				break;
			if($i%2==0)
				echo "<tr class=OddRow>";
			else
				echo "<tr class=EvenRow>";
			echo "<td>";
			echo "<a href='javascript: SelectPerson(\"".$arr_res["PersonID"]."\", \"".$arr_res["pfname"]." ".$arr_res["plname"]."\"); '>";
			echo $arr_res["pfname"]." ".$arr_res["plname"];
			echo "</td>";

			echo "</tr>";
		}
	}
?>
</table>
<script>
	function SelectPerson(PersonID, PersonName)
	{
		//window.opener.document.f1.<?php echo $_REQUEST["InputName"] ?>.value=PersonID;
        window.opener.document.getElementById('<?php echo $_REQUEST["InputName"] ?>').value=PersonID;
		window.opener.document.getElementById('<?php echo $_REQUEST["SpanName"] ?>').innerHTML=PersonName;
		window.close();
		
	}
</script>
</body></html>
