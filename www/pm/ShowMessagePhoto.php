<?php
	include_once("header.inc.php");
	$mysql = pdodb::getInstance();
	if (!isset($_GET['MessageID'])){
		return;
	}
		
		
	$SelectedPersonID = $_GET['MessageID'];
	$FQuery = "select ImageFileContent from projectmanagement.messages where MessageID=?";
	$mysql->Prepare($FQuery);

	$res = $mysql->ExecuteStatement(array($SelectedPersonID));
	if($rec=$res->fetch()) {
		header('Content-disposition: filename="photo"');
		header('Content-type: image/jpeg');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header("Content-Transfer-Encoding: binary");

		echo $rec["ImageFileContent"];
	}
?>