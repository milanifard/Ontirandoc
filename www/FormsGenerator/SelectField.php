<?php
include("header.inc.php");
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
	function IsUsedBefore($FieldName)
	{
		$mysql = dbclass::getInstance();
		$query = "select * from FormFields where FormsStructID='".$_REQUEST["FormStructID"]."' and RelatedFieldName='".$FieldName."'";
		$res = $mysql->Execute($query);
		if($res->FetchRow())
			return true;
		return false;	
	}
	
	$query = "SELECT * from COLUMNS
				where 
				TABLE_SCHEMA='".$_REQUEST["DBName"]."' and 
				TABLE_NAME='".$_REQUEST["TableName"]."' ";
	$res = $mysql->Execute($query);
	$i=0;
	while($rec = $res->FetchRow())
	{
		$i++;
		if(!IsUsedBefore($rec["COLUMN_NAME"]))
		{
			if($i%2==0)
				echo "<tr class=OddRow>";
			else
				echo "<tr class=EvenRow>";
			echo "<td>";
			if($rec["COLUMN_KEY"]!="PRI")
				echo "<a href='javascript: SelectField(\"".$rec["COLUMN_NAME"]."\",\"".$rec["COLUMN_COMMENT"]."\");'>";
			echo $rec["COLUMN_NAME"]."</a></td>";
			echo "<td>&nbsp;".$rec["COLUMN_COMMENT"]."</td>";
			echo "<td dir=ltr>&nbsp;".$rec["COLUMN_TYPE"]."</td>";
			echo "</tr>";
		}
		else
		{
			echo "<tr bgcolor=#cccccc>";
			echo "<td>";
			echo $rec["COLUMN_NAME"]."</a></td>";
			echo "<td>&nbsp;".$rec["COLUMN_COMMENT"]."</td>";
			echo "<td dir=ltr>&nbsp;".$rec["COLUMN_TYPE"]."</td>";
			echo "</tr>";
		}
	}
?>
</table>
<br>
<p>
&nbsp;&nbsp;
<img style="vertical-align:middle;" src='images/info.gif' width=35>
<span style="vertical-align:middle; font-size:12px;"> فیلدهایی که قبلا به این فرم اضافه کرده اید با ردیفهای خاکستری رنگ نمایش داده می شوند.</span></td>
</p>
<script>
	function SelectField(FieldName, comment)
	{
		window.opener.document.f1.Item_RelatedFieldName.value=FieldName;
		window.opener.document.f1.Item_FieldTitle.value=comment;
		window.close();
	}
</script>
</body>
</html>
