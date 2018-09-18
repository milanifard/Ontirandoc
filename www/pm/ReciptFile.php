<?php

//-----------------------------
//	Programmer	: A.gholami
//	Date		: 94.03
//-----------------------------

include("header.inc.php");
ini_set('display_errors','off');
/*
if($_SESSION["User"]->PersonID == 401371457)
{
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}
*/
$ProjectDocumentID = isset($_REQUEST['PrDID'])? $_REQUEST['PrDID'] : null;
$FileName_PrDID = isset($_REQUEST['FName_PrDID'])? $_REQUEST['FName_PrDID'] : null;

$ProjectTaskDocumentID = isset($_REQUEST['PtdID'])? $_REQUEST['PtdID'] : null;
$FileName_ptID = isset($_REQUEST['FName_ptdID'])? $_REQUEST['FName_ptdID'] : null;

$ProjectTaskActivityID = isset($_REQUEST['AID'])? $_REQUEST['AID'] : null;
$FileName_AID = isset($_REQUEST['FileName_AID'])? $_REQUEST['FileName_AID'] : null;


if(!empty($ProjectDocumentID))
{
	$res3 = PdoDataAccess::runquery("select ProjectDocumentID,(FileName) as fileprd from projectmanagement.ProjectDocuments "
	                . " where ProjectDocumentID = ? and FileName = ? ", array($ProjectDocumentID,$FileName_PrDID));
	
	$extension = $res3[0]['fileprd'];
	$ProjectDocumentID = $res3[0]['ProjectDocumentID'];
	
	
	/* print_r(PdoDataAccess::GetLatestQueryString());die();*/ 
	
	if (empty($extension)) {
	    echo " .فایل ضمیمه وجود ندارد";
	} else {
	    $fnprd = "/mystorage/PlanAndProjectDocuments/projects/Documents/" . $ProjectDocumentID . "." . $extension;
	   // print_r($fn);die(); 
	
	
	    if (file_exists($fnprd)) {
	        header('Content-disposition: filename="' . $ProjectDocumentID . "." . $extension . '"');
	        header('Pragma: no-cache');
	        header('Expires: 0');
	        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	        header('Pragma: public');
	        header("Content-type: $extension");
	        header("Content-Transfer-Encoding: binary");
	        echo file_get_contents($fnprd);
	    } else {
	    echo "<p dir=rtl>محتوای فايل موجود نمی‌باشد.</p>";
	    }
	}
}
/*-------------New by gholami 94/09/07------------------*/
elseif(!empty($ProjectTaskDocumentID)){

/*print_r($ProjectTaskDocumentID);
echo "<br>";
print_r($FileName_ptID);
die();*/
$res = PdoDataAccess::runquery("select ProjectTaskDocumentID,FileName from projectmanagement.ProjectTaskDocuments  "
        . " where ProjectTaskDocumentID = ? and FileName = ? ", array($ProjectTaskDocumentID,$FileName_ptID));

$extension = $res[0]['FileName'];
$w = explode('.', $extension);

if(count($w) >1 ) 
$temp=$w[count($w)-1];
//$temp=$w[1];
else 
$temp=$w[0];
//print_r($temp);die();
$ProjectTaskDocumentID=$res[0]['ProjectTaskDocumentID'];

//print_r($extension);//die();

 //print_r(PdoDataAccess::GetLatestQueryString());die();
$Filename1 =$ProjectTaskDocumentID . "." . $temp;
//print_r($Filename1);
if(empty($Filename1)){ echo " .فایل ضمیمه وجود ندارد";}else{
$name = "/mystorage/PlanAndProjectDocuments/TaskDocuments/".$ProjectTaskDocumentID . "." . $temp;
//print_r($name);
//die();
/*(/mystorage/PlanAndProjectDocuments/TaskDocuments/.)*/

if (file_exists($name)) {

/*if($_SESSION["UserID"]=="gholami-a")
{
echo "f1:". $temp .",ext:".$extension.",n1:".$name;
die("test");
}*/
    header('Content-disposition: filename="' .  $Filename1 . '"');  
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header("Content-type: $temp");
    header("Content-Transfer-Encoding: binary");
if($temp!='php'){
 echo file_get_contents($name);}
else{
    echo htmlspecialchars(file_get_contents($name));
}
} else {    
    echo "<p dir=rtl>محتوای فايل موجود نمی‌باشد.</p>";
}
}
}

/*-------------New by gholami 94/09/18------------------*/
/*-------------Modified by shariati 96/03/09------------*/
elseif(!empty($ProjectTaskActivityID))
{
	/*
	print_r($ProjectTaskActivityID);
	echo "<br>";
	print_r($FileName_AID);
	die();*/
	
	$res4 = PdoDataAccess::runquery("select ProjectTaskActivityID,FileName from projectmanagement.ProjectTaskActivities where ProjectTaskActivityID = ? and FileName = ?;", array($ProjectTaskActivityID, $FileName_AID));
	        
	if (count($res4) < 1)
		die("<p dir=rtl>.چنین پیوستی وجود ندارد</p>");
	
	$extension = $res4[0]['FileName'];
	$Parts = explode('.', $extension);
	
	if(count($Parts) > 0)
		$extension = $Parts[count($Parts) - 1];
	
	$ProjectTaskActivityID = $res4[0]['ProjectTaskActivityID'];
	
	//print_r($ProjectTaskActivityID);//die();
	//print_r(PdoDataAccess::GetLatestQueryString());die();
	
	$Filename = "$ProjectTaskActivityID.$extension";
	
	$aname = "/mystorage/PlanAndProjectDocuments/TaskActivities/$Filename";
	
	//print_r($aname);die();
	
	if (file_exists($aname))
	{
	    header("Content-disposition: filename=$Filename");  
	    header('Pragma: no-cache');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	    header('Pragma: public');
	    header("Content-type: $extension");
	    header("Content-Transfer-Encoding: binary");
	    echo file_get_contents($aname);
	}
	else
	{    
	    echo "<p dir=rtl>محتوای فايل موجود نمی‌باشد.</p>";
	}
}

?>