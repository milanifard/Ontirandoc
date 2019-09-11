<?php
include("header.inc.php");
include("classes/FormUtils.class.php");
include("classes/SecurityManager.class.php");
include("classes/FormsFlowSteps.class.php");
$mysql = pdodb::getInstance();
$FormsStructID = $_REQUEST["FormsStructID"];
$RecID = $_REQUEST["RecID"];
$FieldName = $_REQUEST["FieldName"];
$DownloadFileName = $_REQUEST["DownloadFileName"];

// 	کد مرحله مربوط به رکورد داده را بدست می آورد
$CurStepID = FormUtils::GetCurrentStepID($RecID, $FormsStructID);

// برای اطمینان از امنیت بایستی یک بار دیگر در صفحه چک شود آیا دسترسی کاربر به این رکورد داده مجاز است یا خیر
// در صورتیکه مرحله منهای یک باشد یعنی مرحله کاری ندارد و مجاز است 
if($CurStepID>0 && !SecurityManager::HasUserAccessToThisRecord($_SESSION["PersonID"], $CurStepID, $RecID))
{
	echo ":)";
	die();
}

$mysql->Prepare("select * from formsgenerator.FormsStruct where FormsStructID='".$FormsStructID."'");
$res = $mysql->ExecuteStatement(array());
if($rec=$res->fetch())
{
	$mysql->Prepare("select ".$FieldName." from ".$rec["RelatedDB"].".".$rec["RelatedTable"]." where ".$rec["KeyFieldName"]."='".$RecID."'");
    $res = $mysql->ExecuteStatement(array());
	if($arr=$res->fetch())
	{
		header('Content-disposition: filename="' . $DownloadFileName.'"');
		header('Content-type: image/jpeg');
		header('Pragma: no-cache');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header("Content-Transfer-Encoding: binary");
		echo $arr[0];
	}
}
?>