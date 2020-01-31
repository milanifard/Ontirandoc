<?php
include("header.inc.php");
include_once("classes/FormsStruct.class.php");
include_once("classes/FormManagers.class.php");
include_once("classes/FormFields.class.php");
require_once("classes/SecurityManager.class.php");

$_REQUEST = SecurityManager::validateInput($_REQUEST);

$ParentObj = new be_FormsStruct();
$ParentObj->LoadDataFromDatabase($_REQUEST["Item_FormStructID"]);
if($ParentObj->CreatorUser!=$_SESSION["UserID"] && !$ParentObj->HasThisPersonAccessToManageStruct($_SESSION["PersonID"]))
{
	echo "You don't have permission";
	die();
}
$fields = manage_FormFields::GetList($_REQUEST["Item_FormStructID"]);
$mysql = pdodb::getInstance();
    header('Content-Type: text/plain;charset=utf-8');
  	header('Content-disposition: attachment; filename=report.xls');
  	$query = "select ".$ParentObj->RelatedTable.".*, TemporaryUsersAccessForms.filled from formsgenerator.".$ParentObj->RelatedTable." 
  							JOIN formsgenerator.QuestionnairesCreators on (QuestionnairesCreators.RelatedRecordID=".$ParentObj->RelatedTable.".".$ParentObj->KeyFieldName.")
							JOIN formsgenerator.TemporaryUsers on (TemporaryUsers.WebUserID=QuestionnairesCreators.UserID)
							JOIN formsgenerator.TemporaryUsersAccessForms on (TemporaryUsersAccessForms.WebUserID=TemporaryUsers.WebUserID)
							where QuestionnairesCreators.FormsStructID='".$ParentObj->FormsStructID."' 							
  							";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement(array());
	echo "<table>";
	echo "<tr>";
	foreach($fields as $filed)
	{
		echo "<td>";
		$field->FieldTitle = strlen($field->FieldTitle)>40 ? substr($field->FieldTitle, 0, 40)."..." : $field->FieldTitle;
		echo $field->FieldTitle;			
		echo "</td>";
	}
	echo "<td>تایید نهایی</td>";
	echo "</tr>";
	while($rec = $res->fetch())
	{
		echo "<tr>";
		for($i=0; $i<count($fields); $i++)
		{
			echo "<td>";
			echo $rec[$fields[$i]->RelatedFieldName];			
			echo "</td>";
		}
		echo "<td>".$rec["filled"]."</td>";
		echo "</tr>";		
	}
	echo "</table>";
?>
