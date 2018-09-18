<?php
require_once "header.inc.php";
require_once "../ManageInfo/classes/DimensionsDefinition.class.php";

$task = isset($_REQUEST['task'])?$_REQUEST['task']:'';
$DimID = isset($_REQUEST['DimID'])?$_REQUEST['DimID']:'';
switch($task){
    case 'getDimNameDesc':
        getDimNameDesc($DimID);
}
function getDimNameDesc($DimID){
    if($DimID == '')
        die('');
    $dim = new be_DimensionsDefinition();
    $dim->LoadDataFromDatabase($DimID);
    die($dim->DimNameDesc);
}
?>
