<?php
include("header.inc.php");
include_once("classes/FormUtils.class.php");
include_once("classes/SecurityManager.class.php");
include_once("classes/FormsFlowSteps.class.php");
$mysql = dbclass::getInstance();
$FileContentID = $_REQUEST["FileContentID"];

// برای اطمینان از امنیت بایستی یک بار دیگر در صفحه چک شود آیا دسترسی کاربر به این رکورد داده مجاز است یا خیر
// کد بعدا نوشته شود 

$res = $mysql->Execute("select FileContent, FileName from FileContents where FileContentID='".$FileContentID."'");
if($rec=$res->FetchRow())
{
		header('Content-disposition: filename="' . $rec["FileName"].'"');
		header('Content-type: image/jpeg');
		header('Pragma: no-cache');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header("Content-Transfer-Encoding: binary");
		echo $rec["FileContent"];
}
?>