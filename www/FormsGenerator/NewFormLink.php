<?php
include("header.inc.php");
include_once("classes/files.class.php");
include_once("classes/FileTypeUserPermissions.class.php");
include_once("classes/FileTypeUserPermittedEduGroups.class.php");
include_once("classes/FileTypeUserPermittedUnits.class.php");
include_once("classes/FileTypeUserPermittedSubUnits.class.php");
include_once("classes/SecurityManager.class.php");
include_once("classes/FileTypes.class.php");
include_once("classes/FileTypeForms.class.php");
include_once("classes/FormUtils.class.php");
include_once("classes/FileContents.class.php");
include_once("classes/FormsStruct.class.php");
HTMLBegin();
$mysql = dbclass::getInstance();
$CurFile = new be_files();
$CurFile->LoadDataFromDatabase($_REQUEST["FileID"]);
$FileTypeID = $CurFile->FileTypeID;

if(isset($_REQUEST["FormRecordID"]))
{
	$obj = new be_FormsStruct();
	$obj->LoadDataFromDatabase($_REQUEST["FormsStructID"]);
	$query = "select * from ".$obj->RelatedDB.".".$obj->RelatedTable." where ".$obj->KeyFieldName."=".$_REQUEST["FormRecordID"];
	$res = $mysql->Execute($query);
	if($rec = $res->FetchRow())
	{
		$query = "insert into FileContents (FileID, ContentType, FormsStructID, FormRecordID) values ('".$_REQUEST["FileID"]."', 'FORM', '".$_REQUEST["FormsStructID"]."', '".$_REQUEST["FormRecordID"]."')";
		$mysql->Execute($query);
		$query = "select max(FileContentID) from FileContents where FormRecordID='".$_REQUEST["FormRecordID"]."'";
		$res = $mysql->Execute($query);
		if($rec = $res->FetchRow())
		{
			$MaxID = $rec[0];
			$mysql->Execute("insert into FileContentHistory (FileContentID, ActionType, ActionTime, PersonID) values ('".$MaxID."', 'ADD', now(), '".$_SESSION["PersonID"]."') ");
		}
?>
	<script>
		window.opener.document.location='ManageFileContent.php?UpdateID=<?php echo $_REQUEST["FileID"] ?>&ContentType=<?php echo $_REQUEST["ContentType"] ?>';
		window.close();	
	</script>
<?php 
		die();	
	}
	echo "<p align=center><font color=red>فرم مورد نظر وجود ندارد</font></p><br>";
}
?>
<form method=post>
<table width=80% align=center border=1 cellspacing=0 cellpadding=3>
<tr class=HeaderOfTable>
	<td align=center colspan=2>
	ایجاد اتصال به یک فرم در گردش	
	</td>
</tr>
<tr>
	<td colspan=2>
	<table width=100% border=0>
	<tr>
		<td width=10%>
		نوع فرم
		</td>
		<td>
			<select name=FormsStructID id=FormsStructID>
			<?php 
				$list = manage_FileTypeForms::GetList($FileTypeID);
				for($i=0; $i<count($list); $i++)
				{
					if(SecurityManager::HasUserAddAccessThisFormFromThisFileType($_SESSION["PersonID"], $list[$i]->FormsStructID, $FileTypeID))
					{
						echo "<option value='".$list[$i]->FormsStructID."'>".$list[$i]->FormTitle;
					} 
				}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td width=10%>
		کد فرم
		</td>
		<td>
			<input type=text name=FormRecordID id=FormRecordID size=5 maxlength=6> 
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr class=HeaderOfTable>
	<td align=center colspan=2>
	<input type=hidden name=ContentType value='<?php echo $_REQUEST["ContentType"] ?>'>
	<input type=hidden name=FileID value='<?php echo $_REQUEST["FileID"] ?>'>
	<input type=submit value='ایجاد'>
	</td>
</tr>
</table> 
</form>
</html>