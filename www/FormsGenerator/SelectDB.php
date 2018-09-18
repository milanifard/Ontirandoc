<?php
include("header.inc.php");
HTMLBegin();
$mysql = dbclass::getInstance();
?>
<form method=post id=f1 name=f1>
<br>
<table width=90% border=1 cellspacing=0 align=center>
<tr class=HeaderOfTable><td align=center>جستجوی جداول</td></tr>
 <tr>
 <td>
 <table width=100% border=0>
	<tr >
	<td width=1% nowrap>
		بانک اطلاعاتی
	</td>
	<td  nowrap>
		<select name=DBName id=DBName dir=ltr>
		<?php
			$query = "select SCHEMA_NAME from SCHEMATA order by SCHEMA_NAME";
			$res = $mysql->Execute($query);
			while($rec = $res->FetchRow())
			{
				echo "<option value='".$rec["SCHEMA_NAME"]."' ";
				if(isset($_REQUEST["DBName"]) && $rec["SCHEMA_NAME"]==$_REQUEST["DBName"])
					echo " selected ";
				echo ">".$rec["SCHEMA_NAME"];
			}
		?>
		</select>
		
	</td>
	</tr>
	<tr >
	<td width=1% nowrap>
		جدول اطلاعاتی
	</td>
	<td nowrap>
		<input dir=ltr type=text name=TableName id=TableName value='<?php if(isset($_REQUEST["TableName"])) echo $_REQUEST["TableName"]; ?>'>
	</td>
	</tr>
	<tr >
	<td width=1% nowrap>
		شرح
	</td>
	<td nowrap>
		<input type=text name=comment id=comment value='<?php if(isset($_REQUEST["comment"])) echo $_REQUEST["comment"]; ?>'>
	</td>
	</tr>	
	<tr class=FooterOfTable><td colspan=2 align=center><input type=submit value='جستجو'></td></tr>
	</table>
	</td>
	</tr>
</table>
</form>
<br>
<table width=80% align=center border=1 cellpadding=3 cellspacing=0>
<tr class=HeaderOfTable>
<td width=10% nowrap>بانک اطلاعاتی</td>
<td width=20%>جدول</td>
<td>شرح</td>
</tr>
<?php 
if(isset($_REQUEST["DBName"])) 
{	
	$query = "SELECT * from TABLES_SCHEMA where 
				TABLE_SCHEMA like '%".$_REQUEST["DBName"]."%' and 
				TABLE_NAME like '%".$_REQUEST["TableName"]."%' and 
				TABLE_COMMENT like '%".$_REQUEST["comment"]."%' limit 0,201";
	$res = $mysql->Execute($query);
	$i=0;
	while($rec = $res->FetchRow())
	{
		$i++;
		if($i>200)
			break;
		if($i%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "<td><a href='javascript: SelectDB(\"".$rec["TABLE_SCHEMA"]."\",\"".$rec["TABLE_NAME"]."\", \"".$rec["TABLE_COMMENT"]."\");'>".$rec["TABLE_SCHEMA"]."</a></td>";
		echo "<td>".$rec["TABLE_NAME"]."</td>";
		echo "<td>&nbsp;".$rec["TABLE_COMMENT"]."</td>";
		echo "</tr>";
	}
	if($i>200)
		echo "<tr class=FooterOfTable><td colspan=3>تنها ۲۰۰ مورد نمایش داده می شود</td></tr>";
}	
?>
<script>
	function SelectDB(DBName, TableName, TableComment)
	{
		window.opener.document.f1.Item_RelatedDB.value=DBName;
		window.opener.document.f1.Item_RelatedTable.value=TableName;
		window.opener.document.f1.Item_FormTitle.value=TableComment;
		window.close();
	}
</script>
</body>
</html>
