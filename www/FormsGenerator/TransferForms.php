<?php
include("header.inc.php");
HTMLBegin();
//echo "از PHPMyAdmin استفاده شود";
//die();
$mysql = dbclass::getInstance();
$db = dbclass::getInstance("192.168.14.46","omid","milaniomid1356","formsgenerator");
$res = $mysql->Execute("select * from FormsStruct where FormsStructID='48'");
if($rec = $res->FetchRow())
{
	/*
	$query = "insert into FormsStruct 
	(FormsStructID, RelatedDB, RelatedTable, FormTitle, TopDescription, ButtomDescription, JavascriptCode, SortByField, SortType, KeyFieldName, PrintType, PrintPageAddress, CreatorUser, CreateDate, FormType, ParentID)
	values 
	(".$rec["FormsStructID"].",'".$rec["RelatedDB"]."', '".$rec["RelatedTable"]."', '".$rec["FormTitle"]."', '".$rec["TopDescription"]."', '".$rec["ButtomDescription"]."','".$rec["JavascriptCode"]."','".$rec["SortByField"]."','".$rec["SortType"]."','".$rec["KeyFieldName"]."','".$rec["PrintType"]."','".$rec["PrintPageAddress"]."','".$rec["CreatorUser"]."','".$rec["CreateDate"]."','".$rec["FormType"]."','".$rec["ParentID"]."')";
	*/
	//$db->Execute($query);
	/*	
	$res = $mysql->Execute("select * from FormFields where FormsStructID='48'");
	while($rec = $res->FetchRow())
	{
				$query = "insert into FormFields (
				FormFieldID
				, FormsStructID
				, RelatedFieldName
				, FieldTitle
				, FieldType
				, MaxLength
				, InputWidth
				, InputRows
				, MinNumber
				, MaxNumber
				, MaxFileSize
				, CreatingListType
				, AddAllItemsToList
				, ListRelatedTable
				, ListRelatedValueField
				, ListRelatedDescriptionField
				, ListRelatedDomainName
				, ListQuery
				, FieldInputType
				, DefaultValue
				, ValidFileExtensions
				, ShowInList
				, ColumnOrder
				, ColumnWidth
				, ListShowType
				, LookUpPageAddress
				, OrderInInputForm
				, ImageWidth
				, ImageHeight
				, FieldHint
				, RelatedFileNameField
				) values ('".$rec["FormFieldID"]."'
				, '".$rec["FormsStructID"]."'
				, '".$rec["RelatedFieldName"]."'
				, '".$rec["FieldTitle"]."'
				, '".$rec["FieldType"]."'
				, '".$rec["MaxLength"]."'
				, '".$rec["InputWidth"]."'
				, '".$rec["InputRows"]."'
				, '".$rec["MinNumber"]."'
				, '".$rec["MaxNumber"]."'
				, '".$rec["MaxFileSize"]."'
				, '".$rec["CreatingListType"]."'
				, '".$rec["AddAllItemsToList"]."'
				, '".$rec["ListRelatedTable"]."'
				, '".$rec["ListRelatedValueField"]."'
				, '".$rec["ListRelatedDescriptionField"]."'
				, '".$rec["ListRelatedDomainName"]."'
				, '".$rec["ListQuery"]."'
				, '".$rec["FieldInputType"]."'
				, '".$rec["DefaultValue"]."'
				, '".$rec["ValidFileExtensions"]."'
				, '".$rec["ShowInList"]."'
				, '".$rec["ColumnOrder"]."'
				, '".$rec["ColumnWidth"]."'
				, '".$rec["ListShowType"]."'
				, '".$rec["LookUpPageAddress"]."'
				, '".$rec["OrderInInputForm"]."'
				, '".$rec["ImageWidth"]."'
				, '".$rec["ImageHeight"]."'
				, '".$rec["FieldHint"]."'
				, '".$rec["RelatedFileNameField"]."'
				)";
		$db->Execute($query);
	}

		$res = $mysql->Execute("select * from FieldsItemList where FormFieldID in (select FormFieldID from FormFields where FormsStructID='48')");
		while($rec = $res->FetchRow())
		{
				$query = "insert into FieldsItemList (FieldItemListID, FormFieldID
				, ItemValue
				, ItemDescription
				) values (
				'".$rec["FieldItemListID"]."'
				, '".$rec["FormFieldID"]."'
				, '".$rec["ItemValue"]."'
				, '".$rec["ItemDescription"]."'
				)";			
				$db->Execute($query);
		}
		*/
}
?>
</html>
	
