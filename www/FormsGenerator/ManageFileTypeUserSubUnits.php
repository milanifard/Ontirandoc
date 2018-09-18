<?php
include("header.inc.php");
include("classes/FileTypeUserPermissions.class.php");
include("classes/FileTypeUserPermittedSubUnits.class.php");
include("classes/FormUtils.class.php");
HTMLBegin();

$ParentObj = new be_FileTypeUserPermissions();
$ParentObj->LoadDataFromDatabase($_REQUEST["id"]);
if(isset($_REQUEST["Save"]))
{
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_FileTypeUserPermittedSubUnits::Add($_REQUEST["id"]
				, $_REQUEST["Item_UnitID"]
				, $_REQUEST["Item_SubUnitID"]
				);
	}	
	else 
	{	
		manage_FileTypeUserPermittedSubUnits::Update($_REQUEST["UpdateID"] 
				, $_REQUEST["Item_UnitID"]
				, $_REQUEST["Item_SubUnitID"]
				);
	}	
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_FileTypeUserPermittedSubUnits();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_UnitID.value='".$obj->UnitID."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_SubUnitID.value='".$obj->SubUnitID."'; \r\n ";
}	
?>
<script>
<? echo PersiateKeyboard() ?>
</script>
<form method=post id=f1 name=f1>
<?
	$SelectedUnitID = 43;
	if(isset($_REQUEST["Item_UnitID"]))
	{
		$SelectedUnitID = $_REQUEST["Item_UnitID"];
	}
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=hidden name='UpdateID' id='UpdateID' value='".$_REQUEST["UpdateID"]."'>";
	}
?>
<br><table width=80% border=1 cellspacing=0 align=center>
<tr class=HeaderOfTable><td align=center>تعریف زیر واحدهای مجاز برای <b><b><?php echo $ParentObj->PersonName ?></b></td></tr>
<tr><td>
<table width=100% border=0>
	<input type=hidden name=id id=id value='<? echo $_REQUEST["id"]; ?>'>
<tr id=tr_UnitID name=tr_UnitID style='display:'>
<td width=1% nowrap>
	واحد سازمانی: 
</td>
<td nowrap>
	<select name=Item_UnitID id=Item_UnitID onchange='javascript: RefreshPageByUnitID(this.value);'>
		<option value=0>-
		<?php echo FormUtils::CreateUnitsOptions($SelectedUnitID); ?>
	</select>
</td>
</tr>
<tr id=tr_SubUnitID name=tr_SubUnitID style='display:'>
<td width=1% nowrap>
	زیر واحد سازمانی: 
</td>
<td nowrap>
	<select name=Item_SubUnitID id=Item_SubUnitID>
		<?php echo FormUtils::CreateSubUnitsOptions($SelectedUnitID, ""); ?>
	</select>
</td>
</tr>
</table></td></tr><tr class=FooterOfTable><td align=center><input type=button onclick='javascript: ValidateForm();' value='ذخیره'>
&nbsp;
<input type=button onclick='javascript: window.close();' value='بازگشت'>
</td></tr>
</table>
<input type=hidden name=Save id=Save value=1>
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
	function RefreshPageByUnitID(UnitID)
	{
		document.location='ManageFileTypeUserSubUnits.php?id=<?php echo $_REQUEST["id"] ?>&Item_UnitID='+UnitID;
	}
</script>
<?php
 $k=0;
$ListCondition = " FileTypeUserPermissionID='".$_REQUEST["id"]."' ";

$res = manage_FileTypeUserPermittedSubUnits::GetList($ListCondition); 
echo "<form id=f2 name=f2 method=post>"; 
?>
<input type=hidden name=id id=id value='<? echo $_REQUEST["id"]; ?>'>
<?php 
echo "<br><table width=80% align=center border=1 cellspacing=0>";
echo "<tr class=HeaderOfTable>";
echo "<td width=1%> </td>";
echo "	<td>واحد سازمانی</td>";
echo "	<td>زیر واحد سازمانی</td>";
echo "</tr>";
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->FileTypeUserPermittedSubUnitID])) 
	{
		manage_FileTypeUserPermittedSubUnits::Remove($res[$k]->FileTypeUserPermittedSubUnitID); 
	}
	else
	{
		if($k%2==0)
			echo "<tr class=OddRow>";
		else
			echo "<tr class=EvenRow>";
		echo "<td><input type=checkbox name=ch_".$res[$k]->FileTypeUserPermittedSubUnitID."></td>";
		echo "	<td>".$res[$k]->UnitName."</td>";
		echo "	<td>".$res[$k]->SubUnitName."</td>";
		echo "</tr>";
	}
}
echo "<tr class=FooterOfTable><td colspan=4 align=center><input type=submit value='حذف'></tr>";
echo "</table>";
echo "</form>";
?>
</html>