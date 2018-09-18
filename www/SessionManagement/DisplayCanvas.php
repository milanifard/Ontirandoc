<?php
/**
 * Author : Atoosa.Gholami 
 * Date : 1392-11
 */
include("header.inc.php");

$RecId = (int) (isset($_REQUEST['RecId']) ? $_REQUEST['RecId'] : '0');
$TableName = 'sessionmanagement.SessionMembers';
$mysql = pdodb::getInstance();
if ($RecId) {
	$FQuery = "select UniversitySessionID ,canvasimg from $TableName where SessionMemberID = ?";
	$mysql->Prepare($FQuery);
	$stmt = $mysql->ExecuteStatement(array($RecId));
	$rec = $stmt->fetch();
}
if ($RecId== 0 || !$stmt->rowCount() || is_null(@$rec['canvasimg'])) {
	if (!file_exists('../images/no-image.gif') || !($fp = fopen('../images/no-image.gif', 'rb'))) {
		//echo 'Error opening file no-image.gif';
		echo "";
		die();
	}
	$contents = fread($fp, 1054);
	fclose($fp);
	header('Content-Type: image/png');
	header('Content-disposition: attachment; filename=no-image');
	echo $contents;
	return;
}
echo
header('Content-Type: image/png');
header('Content-disposition: attachment; filename=' . $rec['RecId']);
echo $rec['canvasimg'];
?>
