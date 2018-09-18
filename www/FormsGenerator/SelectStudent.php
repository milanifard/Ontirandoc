<?
	include("header.inc.php");
	HTMLBegin();
?>
<script>
	<?= PersiateKeyboard() ?>
</script>
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
<tr>
	<td>شماره دانشجویی: </td><td><input type=text name=StNo size=8 maxlength=10></td>
</tr>
<tr class='FooterOfTable'>
	<td colspan=2 align=center><input type=submit value='جستجو'></td>
</tr>
</table>
<br>
<table width=80% align=center border=1 cellspacing=0 cellpadding=3>
<tr class='HeaderOfTable'>
	<td>نام و نام خانوادگی</td>
	<td>شماره دانشجویی</td>
	<td>دانشکده</td>
	<td>گروه</td>
</tr>
<?
	$mysql = dbclass::getInstance();
	if(isset($_REQUEST["FName"]))
	{
		$query = "select *	from StudentSpecs 
								JOIN hrms_total.org_units on (ouid=FacCode)
								JOIN EducationalGroups using (EduGrpCode)
								where PLName like '%".$_REQUEST["LName"]."%' and PFName like '%".$_REQUEST["FName"]."%' and StNo like '%".$_REQUEST["StNo"]."%' limit 0,200";
		$res = $mysql->Execute($query);
		$i = 0;
		while($arr_res=$res->FetchRow())
		{
			$i++;
			if($i>200)
				break;
			if($i%2==0)
				echo "<tr class=OddRow>";
			else
				echo "<tr class=EvenRow>";
			echo "<td>";
			$ouid = $arr_res["FacCode"];
			$sub_ouid = 0;
			$UnitName = $arr_res["ptitle"];
			$SubUnitName = "";
			$EduGrpName = $arr_res["PEduName"];
			
			echo "<a href='javascript: SelectPerson(\"".$arr_res["StNo"]."\", \"".$arr_res["PFName"]." ".$arr_res["PLName"]."\", ";
			echo "\"".$ouid."\", \"".$sub_ouid."\", \"".$arr_res["EduGrpCode"]."\", ";
			echo "\"".$UnitName."\", \"".$SubUnitName."\", \"".$EduGrpName."\" ";
			echo "); '>";
			echo $arr_res["PFName"]." ".$arr_res["PLName"];
			echo "</td>";

			echo "<td>".$arr_res["StNo"]."</td>";
			echo "<td>".$UnitName."</td>";
			echo "<td>".$EduGrpName."</td>";
			echo "</tr>";
		}
	}
?>
</table>
<script>
	function SelectPerson(PersonID, PersonName, ouid, sub_ouid, EduGrpCode, UnitName, SubUnitName, EduGrpName)
	{
		window.opener.document.f1.<?php echo $_REQUEST["InputName"] ?>.value=PersonID;
		window.opener.document.getElementById('<?php echo $_REQUEST["SpanName"] ?>').innerHTML=PersonName;
		<?php if(isset($_REQUEST["UnitSpanName"])) { ?>
		window.opener.document.f1.<?php echo $_REQUEST["UnitInputName"] ?>.value=ouid;
		window.opener.document.f1.<?php echo $_REQUEST["SubUnitInputName"] ?>.value=sub_ouid;
		window.opener.document.f1.<?php echo $_REQUEST["EduGrpInputName"] ?>.value=EduGrpCode;
		window.opener.document.getElementById('<?php echo $_REQUEST["UnitSpanName"] ?>').innerHTML=UnitName;
		window.opener.document.getElementById('<?php echo $_REQUEST["SubUnitSpanName"] ?>').innerHTML=SubUnitName;
		window.opener.document.getElementById('<?php echo $_REQUEST["EduGrpSpanName"] ?>').innerHTML=EduGrpName;
		<?php } ?>
		window.close();
		
	}
</script>
</body></html>
