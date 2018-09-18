<?php
	include('header.inc');
	include("../sharedClasses/SharedClass.class.php");
	include("classes/SessionDecisions.class.php");
	include ("classes/UniversitySessions.class.php");
	include("classes/UniversitySessionsSecurity.class.php");
	include("classes/SessionMembers.class.php");
	
	require_once(config::$root_path."educ/MPDF52/mpdf.php"); 	
	$mysql = pdodb::getInstance();
	ini_set('display_errors','off');
	HTMLUtil::$dont_print = true;
        $out = HTMLUtil::HTMLStart('','rtl');
	 
if ( isset($_POST["image"]) && !empty($_POST["image"]) ) { 

    $dataURL = $_POST["image"];  

    $parts = explode(',', $dataURL);  
    $data = $parts[1];  


    $dataa = base64_decode($data);  
    $fp = addslashes(fread(fopen($data,'r')));

    fwrite($fp, $data);  
    fclose($fp); 

}

if(isset($_REQUEST["MemberPersonID"]))
{
	echo ($dataa);
	echo ($_REQUEST["MemberPersonID"]);
	echo ($_REQUEST["UniversitySessionID"]);
 	manage_UniversitySessions::SignTheDescesionFile($_REQUEST["MemberPersonID"],$_REQUEST["UniversitySessionID"], $dataa);   
}


// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["UniversitySessionID"]);
$uni_session = new be_UniversitySessions();
$uni_session->LoadDataFromDatabase($_REQUEST["UniversitySessionID"]);
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
$HasRemoveAccess = true;
if($ppc->GetPermission("Add_SessionDecisions")=="YES")
	$HasAddAccess = true;
$res = manage_SessionDecisions::GetList($_REQUEST["UniversitySessionID"]);	

$out.='<table width="90%" align="center" border="1" cellspacing="0">';
$out.='<tr bgcolor="#cccccc">';
	$out.='<td colspan="5">';
          	$out .='جلسه: '.$uni_session->SessionTypeID_Desc.'<br>';  
          	$out .='عنوان: '.$uni_session->SessionTitle.'<br>';  
          	$out .='تاریخ: '.$uni_session->SessionDate_Shamsi.'<br>';  
          	$out .='شماره: '.$uni_session->SessionNumber.'<br>';  
          	$out .='ساعت تشکیل: '.floor($uni_session->SessionStartTime/60).":".($uni_session->SessionStartTime%60).'مدت جلسه:'.floor($uni_session->SessionDurationTime/60).":".($uni_session->SessionDurationTime%60);  
	$out .='</td>';
$out .='</tr>';
$out .='<tr>';
$out .='<td width=1%>ردیف</td>';
$out .='<td>دستور کار</td>';
$out .='<td>مصوبه</td>';
$out .='<td width=10% nowrap>مسوول پیگیری</td>';
$out .='<td width=1% nowrap>مهلت اقدام</td>';
$out .='</tr>';



for($k=0; $k<count($res); $k++)
{
	if($k%2==0)
		$out .= "<tr class=\"OddRow\">";
	else
		$out .= "<tr class=\"EvenRow\">";
	$out .="<td>".htmlentities($res[$k]->OrderNo, ENT_QUOTES, 'UTF-8')."</td>";
	$out .=	"<td>".str_replace("\n", "<br>", htmlentities($res[$k]->SessionPreCommandDescription, ENT_QUOTES, 'UTF-8'))."</td>";
	$out .=	"<td>".str_replace("\n", "<br>", htmlentities($res[$k]->description, ENT_QUOTES, 'UTF-8'))."</td>";
	$out .=	"<td>&nbsp;".$res[$k]->ResponsiblePersonID_FullName."</td>";
	$out .=	"<td nowrap>";
	if($res[$k]->DeadlineDate_Shamsi!="date-error")
		$out .= $res[$k]->DeadlineDate_Shamsi;
	else
		$out .= "-";
	$out .= "</td>";
	$out .= "</tr>";
}

$out .='</table>';



$out .='<br>';
$out .='<form id="ListForm" name="ListForm" method="post"><table width="90%" align="center" border="1" cellspacing="0" cellpadding=10>
<tr bgcolor=#cccccc>
	<td colspan=6>حاضرین جلسه</td>
</tr>
<tr bgcolor=#cccccc>
	<td width=1%>ردیف</td><td width=20%>نام و نام خانوادگی<td width=1% nowrap>حضور</td><td width=1%>تاخیر</td><td>امضا</td><td>تاریخ تایید (امضا) </td>
</tr>';

	$k = 0;
	$list = manage_SessionMembers::GetList($_REQUEST["UniversitySessionID"], 0, 1000);
	for($i=0; $i<count($list); $i++)
	{
             $SignImg ='<img src="DisplayCanvas.php?RecId=' .$list[$i]->SessionMemberID . '" width="200"   />';

		if($list[$i]->PresenceType=="PRESENT")
		{
			$k++;
			$out .= "<tr>";
			$out .= "<td width=1%>".$k."</td>";
			$out .= "<td>".$list[$i]->FirstName." ".$list[$i]->LastName."</td>";
			/*echo "<td>
          		<a target=\"_blank\" href=\"Signature.php?PID=".$list[$i]->SessionMemberID."\">".$list[$i]->FirstName." ".$list[$i]->LastName."</a>
    			</td>";*/
/*$out .= "<td>
          <a target=\"_blank\" href=\"Signature.php?MemberPersonID=".$list[$i]->MemberPersonID."&UniversitySessionID=".$_REQUEST["UniversitySessionID"]."\">".$list[$i]->FirstName." ".$list[$i]->LastName."</a>
    </td>";*/

			$out .= "<td nowrap>".floor($list[$i]->PresenceTime/60).":".($list[$i]->PresenceTime%60)."</td>";
			$out .= "<td nowrap>".floor($list[$i]->TardinessTime/60).":".($list[$i]->TardinessTime%60)."</td>";
			if($list[$i]->canvasimg!='')			
			$out .= "<td>" . $SignImg . "</td>";
			else
			$out .= "<td>&nbsp;</td>";

if($list[$i]->SignTime_Shamsi!="date-error")
		       $out .="<td nowrap>".$list[$i]->SignTime_Shamsi."</td>";
	                else
		      $out .="<td>-</td>";

			$out .= "</tr>"; 
		}
	}

$out .='</table>';
$out .='</form>';
$out .='<br>';
$out .='<table width="90%" align="center" border="1" cellspacing="0" cellpadding=10>
<tr bgcolor=#cccccc>
	<td>
	<b>
	غایبین جلسه: 
	</b>';

	$k = 0;
	$list = manage_SessionMembers::GetList($_REQUEST["UniversitySessionID"], 0, 1000);
	for($i=0; $i<count($list); $i++)
	{
		if($list[$i]->PresenceType=="ABSENT")
		{
			$k++;
			$out .= $k."- ";
			$out .= $list[$i]->FirstName." ".$list[$i]->LastName." ";
		}
	}

	$out .='</td>';
$out .='</tr>';
$out .='</table>';
$out .='</html>';

	$mpdf=new mPDF('fa','A4');
	$mpdf->SetDirectionality('rtl');
	$mpdf->SetDisplayMode('fullpage');
	//$mpdf->SetFont(' Arial, Helvetica, sans-serif normal 12px/24px ');
	$mpdf->WriteHTML($out);
	$mpdf->Output();
	exit;
