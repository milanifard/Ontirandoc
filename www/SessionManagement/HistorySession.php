<?php

include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/SessionPreCommands.class.php");
include_once("classes/UniversitySessions.class.php");
include_once("classes/UniversitySessionsSecurity.class.php");
include_once("classes/SessionActReg.class.php");
	function Newshdate($st)
	{
		$st = shdate($st);
		$yy = substr($st,6,2); 
		$mm = substr($st,3,2); 
		$dd = substr($st,0,2);
		return "13".$yy."/".$mm."/".$dd;
	}

HTMLBegin();
//$session= $_REQUEST["UniversitySessionID"];
$OrderBy = "";
if(isset($_REQUEST["OrderBy"]))
	$OrderBy = $_REQUEST["OrderBy"];

$res1 = manage_SessionPreCommands::Getli($_REQUEST["OrderNo"],$_REQUEST["UniversitySessionID"],$_REQUEST["st"],$OrderBy);
/*print_r($res); 
echo 'test4';*/
?>
<form id="f1" name="f1" method="post"> 
	<input type="hidden" id="Item_UniversitySessionID" name="Item_UniversitySessionID" value="<? echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>">
<br><table width="80%" align="center" border="1" cellspacing="0">
<tr bgcolor="#cccccc">

	<td colspan=3>
                                                                                                                                                                                                                                     تاریخچه 
	</td>
</tr>
<tr class="HeaderOfTable">

	<td width="15%">
                                                                                                                                                                                                                              تاریخ جلسه
	</td>
	<td width="35%">
                                                                                                                                                                                                                                    اقدامات
	</td>
	<td >
                                                                                                                                                                                                                                 مصوبات
	</td>

</tr>
<?
for($k=0; $k<count($res1); $k++)
{
	if($k%2==0)
		echo "<tr class=\"OddRow\">";
	else
		echo "<tr class=\"EvenRow\">";
	echo "	<td>".Newshdate($res1[$k]->SessionDate)."</td>";

	$description=htmlentities(str_replace('\r\n', '</br>', $res1[$k]->ActReg), ENT_QUOTES, 'UTF-8');
if($description!=''){
	echo "	<td>".$description."</td>";}
else
	echo "<td>در این تاریخ هیچ اقدامی ثبت نشده است</td>";	

echo "	<td>".$res1[$k]->description."</td>";
	
	echo "</tr>";
}
?>

<tr class="FooterOfTable"><td colspan=3 align=center> <input type="button" onclick="javascript: window.close();" value="بستن"></td>
</tr>
</table>
</form>
<script>
 setInterval(function(){
        
        var xmlhttp;
            if (window.XMLHttpRequest)
            {
                // code for IE7 , Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            }
            else
            {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            
            xmlhttp.open("POST","header.inc.php",true);            
            xmlhttp.send();
        
    }, 60000);

</script>
