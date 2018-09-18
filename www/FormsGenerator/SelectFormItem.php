<?php
include("header.inc.php");
include("classes/FormsStruct.class.php");
HTMLBegin();
$mysql = dbclass::getInstance();
?>
<table width=90% align=center border=1 cellpadding=3 cellspacing=0>
<tr class=HeaderOfTable>
<td width=20%>نام فیلد</td>
<td>شرح</td>
<td width=20%>نوع</td>
</tr>
<?php 
	$obj = new be_FormsStruct();
	$obj->LoadDataFromDatabase($_REQUEST["FormStructID"]);
	$query = "SELECT * from COLUMNS
				where 
				TABLE_SCHEMA='".$obj->RelatedDB."' and 
				TABLE_NAME='".$obj->RelatedTable."' ";
	$res = $mysql->Execute($query);
	$i=0;
	while($rec = $res->FetchRow())
	{
		$i++;
		if($i%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "<td>";
		if($rec["COLUMN_KEY"]!="PRI")
			echo "<a href='javascript: SelectField(\"".$rec["COLUMN_NAME"]."\");'>";
		echo $rec["COLUMN_NAME"]."</a></td>";
		echo "<td>&nbsp;".$rec["COLUMN_COMMENT"]."</td>";
		echo "<td dir=ltr>&nbsp;".$rec["COLUMN_TYPE"]."</td>";
		echo "</tr>";
	}
?>
</table>
<br>

<script>
	function SelectField(FieldName)
	{
		window.opener.document.f1.Item_RelatedField.value=FieldName;
		window.close();
	}
</script>
</body>
</html>
