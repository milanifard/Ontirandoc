<?
	include("header.inc");
	include("General.inc");
	HTMLBegin();
?>
<script src="../stuoffice/Scripts/General.js"></script>


<form method=post>
<table align=center border=1 width=80% cellspacing=0 cellpadding=5>
<tr>
	<td>
	<table width=100%>
		<tr>
			<td colspan=2 align=center bgcolor=#cccccc>
				بروزرساني فيلد در مستندات كليه جداول
			</td>
		</tr>
		<tr>
			<td>DataBase: </td>
			<td>
			<select name=DB>
			<option value=''>-
			<option value='educ'>educ
			<option value='mis'>mis
			<option value='hrms'>hrms
			<option value='goods'>goods
			<option value='nazar'>nazar
			<option value='taghziye'>taghziye
			<option value='dbhesab'>dbhesab
			</select>
			</td>
		</tr>
		<tr>
			<td>نام فيلد: </td>
			<td><input type=text name=sFieldName dir=ltr></td>
		</tr>
		<tr>
			<td>شرح: </td>
			<td><textarea name=sdescription onkeypress="return submitenter(this, event);" rows=5 cols=80></textarea>
			</td>
		</tr>
		<tr>
			<td>نام جدول اصلي: </td>
			<td><input type=text name=sTableName dir=ltr> در صورتيكه فيلدهايي با اين نام كليد خارجي به جدولي هستند اين قسمت پر شود</td>
		</tr>
		<tr>
			<td colspan=2 align=center bgcolor=#cccccc>
				<input type=submit value='&nbsp;ثبت&nbsp;'>
			</td>
		</tr>
	</table>
	</td>
</tr>
</table>
</form>
<? 
if(isset($_POST["sFieldName"])) 
{
	TGeneral:: ExecuteQuery("update mis.MIS_TableFields set description='".$_POST["sdescription"]."'  where FieldName='".$_POST["sFieldName"]."' and DBName='".$_POST["DB"]."'");
	if($_POST["sTableName"]!="")
		TGeneral:: ExecuteQuery("update mis.MIS_TableFields set RelatedTable='".$_POST["sTableName"]."', RelatedField='".$_POST["sFieldName"]."' where FieldName='".$_POST["sFieldName"]."' and TableName<>'".$_POST["sTableName"]."' and DBName='".$_POST["DB"]."'");
	echo "<p align=center>بروزرساني فيلد: ".$_POST["sFieldName"]." انجام شد";
} 
?>
</body>
</html>
