<?php
include("header.inc.php");
include("classes/FormsStruct.class.php");
include("classes/FormManagers.class.php");
include("classes/FormFields.class.php");

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
	$res = $mysql->Execute($query);
	echo "<table>";
	echo "<tr>";
	for($i=0; $i<count($fields); $i++)
	{
		echo "<td>";
		if(strlen($fields[$i]->FieldTitle)>40)
			$fields[$i]->FieldTitle = substr($fields[$i]->FieldTitle, 0, 40)."...";
		echo $fields[$i]->FieldTitle;			
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
