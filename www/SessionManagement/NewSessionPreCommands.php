<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : دستور کار
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-3
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/SessionPreCommands.class.php");
include_once("classes/UniversitySessions.class.php");
include_once("classes/UniversitySessionsSecurity.class.php");
include_once("classes/SessionActReg.class.php");

HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
if(isset($_REQUEST["UpdateID"])) 
{
	$obj = new be_SessionPreCommands();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $obj->UniversitySessionID);
	$UniversitySessionID = $obj->UniversitySessionID;
}
else
{
	$ppc = security_UniversitySessions::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["UniversitySessionID"]);
	$UniversitySessionID = $_REQUEST["UniversitySessionID"];
}
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
if($ppc->GetPermission("Add_SessionPreCommands")=="YES")
	$HasAddAccess = true;
if(isset($_REQUEST["UpdateID"])) 
{ 
	if($ppc->GetPermission("Update_SessionPreCommands")=="PUBLIC")
		$HasUpdateAccess = true;
	else if($ppc->GetPermission("Update_SessionPreCommands")=="PRIVATE" && $_SESSION["PersonID"]==$obj->CreatorPersonID)
		$HasUpdateAccess = true;
	if($ppc->GetPermission("View_SessionPreCommands")=="PUBLIC")
		$HasViewAccess = true;
	else if($ppc->GetPermission("View_SessionPreCommands")=="PRIVATE" && $_SESSION["PersonID"]==$obj->CreatorPersonID)
		$HasViewAccess = true;
        if($ppc->GetPermission("Update_ActRegister")=="PUBLIC")
		$HasUpdateAccess = true;
	else if($ppc->GetPermission("Update_ActRegister")=="PRIVATE" && $_SESSION["PersonID"]==$obj->CreatorPersonID)
		$HasUpdateAccess = true;
	if($ppc->GetPermission("View_ActRegister")=="PUBLIC")
		$HasViewAccess = true;
	else if($ppc->GetPermission("View_ActRegister")=="PRIVATE" && $_SESSION["PersonID"]==$obj->CreatorPersonID)
		$HasViewAccess = true;



} 
else 
{ 
	$HasViewAccess = true;
} 
if(!$HasViewAccess)
{ 
	echo "مجوز مشاهده این رکورد را ندارید";
	die();
} 
if(isset($_REQUEST["Save"])) 
{
	//$Item_DeadLine="";
	if(isset($_REQUEST["UniversitySessionID"]))
		$Item_UniversitySessionID=$_REQUEST["UniversitySessionID"];
	if(isset($_REQUEST["Item_OrderNo"]))
		$Item_OrderNo=$_REQUEST["Item_OrderNo"];
	if(isset($_REQUEST["Item_description"]))
		$Item_description=$_REQUEST["Item_description"];
 
       if(isset($_REQUEST["Item_ActReg"]))
		$Item_ActReg=$_REQUEST["Item_ActReg"];


	if(isset($_REQUEST["Item_ResponsiblePersonID"]))
		$Item_ResponsiblePersonID=$_REQUEST["Item_ResponsiblePersonID"];
	if(isset($_REQUEST["Item_RepeatInNextSession"]))
		$Item_RepeatInNextSession=$_REQUEST["Item_RepeatInNextSession"];
	$Item_RelatedFile = "";
	$Item_RelatedFileName = "";
	if (trim($_FILES['Item_RelatedFile']['name']) != '')
	{
		if ($_FILES['Item_RelatedFile']['error'] != 0)
		{
			echo ' خطا در ارسال فایل' . $_FILES['Item_RelatedFile']['error'];
		}
		else
		{
			$_size = $_FILES['Item_RelatedFile']['size'];
			$_name = $_FILES['Item_RelatedFile']['tmp_name'];
			$Item_RelatedFile = addslashes((fread(fopen($_name, 'r' ),$_size)));
			$Item_RelatedFileName = trim($_FILES['Item_RelatedFile']['name']);
		}
	}
	if(isset($_REQUEST["Item_RelatedFileName"]))
		$Item_RelatedFileName=$_REQUEST["Item_RelatedFileName"];
	if(isset($_REQUEST["Item_CreatorPersonID"]))
		$Item_CreatorPersonID=$_REQUEST["Item_CreatorPersonID"];

	if(isset($_REQUEST["Item_priority"]))
		$Item_priority=$_REQUEST["Item_priority"];

	if(isset($_REQUEST["DeadLine_DAY"]))
	{
		$Item_DeadLine = SharedClass::ConvertToMiladi($_REQUEST["DeadLine_YEAR"], $_REQUEST["DeadLine_MONTH"], $_REQUEST["DeadLine_DAY"]);
	}


	if(!isset($_REQUEST["UpdateID"])) 
	{	
		if($HasAddAccess)
		$SessionPreCommandID=manage_SessionPreCommands::Add($Item_UniversitySessionID
				, $Item_OrderNo
				, $Item_description
				, $Item_ResponsiblePersonID
				, $Item_RepeatInNextSession
				, $Item_RelatedFile
				, $Item_RelatedFileName
				, $Item_DeadLine
				, $Item_priority
				);
               // print_r($SessionPreCommandID);//die();
if($_REQUEST["Item_ActReg"]!=''){

               		        manage_SessionActReg::Added($SessionPreCommandID
				, $Item_ActReg
				);

}

		echo "<script>window.opener.document.location='ManageSessionPreCommands.php?UniversitySessionID=".$UniversitySessionID."'; window.close();</script>";
	}	
	else 
	{	
		if($HasUpdateAccess)
		manage_SessionPreCommands::Update($_REQUEST["UpdateID"] 
				, $Item_OrderNo
				, $Item_description
				, $Item_ResponsiblePersonID
				, $Item_RepeatInNextSession
				, $Item_RelatedFile
				, $Item_RelatedFileName
				, $Item_DeadLine
				, $Item_priority
				);
		/*print_r($_REQUEST["Item_ActReg"]);
		echo "</br>";
                print_r(htmlentities($obj->ActReg, ENT_QUOTES, 'UTF-8'));die();*/
		if($_REQUEST["Item_ActReg"]!= htmlentities($obj->ActReg, ENT_QUOTES, 'UTF-8')){ 
                manage_SessionActReg::Added($_REQUEST["UpdateID"]
		, $Item_ActReg
		);
}
		echo "<script>window.opener.document.location='ManageSessionPreCommands.php?UniversitySessionID=".$UniversitySessionID."'; window.close();</script>";
		die();
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_SessionPreCommands();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_OrderNo.value='".htmlentities($obj->OrderNo, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_OrderNo').innerHTML='".htmlentities($obj->OrderNo, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.getElementById('Span_ResponsiblePersonID_FullName').innerHTML='".$obj->ResponsiblePersonID_FullName."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.getElementById('Item_ResponsiblePersonID').value='".$obj->ResponsiblePersonID."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_RepeatInNextSession.value='".htmlentities($obj->RepeatInNextSession, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_RepeatInNextSession').innerHTML='".htmlentities($obj->RepeatInNextSession_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
}	
else
{
	$LoadDataJavascriptCode .= "document.f1.Item_OrderNo.value='".(manage_SessionPreCommands::GetMaxOrderNo($_REQUEST["UniversitySessionID"])+1)."'; \r\n ";	
}
/*
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
	  $LoadDataJavascriptCode .= "document.f1.Item_priority.value='".htmlentities(1, ENT_QUOTES, 'UTF-8')."'; \r\n ";
	else 
	  $LoadDataJavascriptCode .= "document.getElementById('Item_priority').innerHTML='".htmlentities($obj->priority_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
*/


/*if($obj->DeadLine==null) 
{*/
//if(isset($_REQUEST["UpdateID"])){echo 'yes';}else {echo 'no';}
	if(isset($_REQUEST["UpdateID"]) && $obj->DeadLine_Shamsi!="date-error") 
	{

	  if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
	  {
		  $LoadDataJavascriptCode .= "document.f1.DeadLine_YEAR.value='".substr($obj->DeadLine_Shamsi, 2, 2)."'; \r\n "; 
		  $LoadDataJavascriptCode .= "document.f1.DeadLine_MONTH.value='".substr($obj->DeadLine_Shamsi, 5, 2)."'; \r\n "; 
		  $LoadDataJavascriptCode .= "document.f1.DeadLine_DAY.value='".substr($obj->DeadLine_Shamsi, 8, 2)."'; \r\n "; 
	  }
	  else 
	  {
		  $LoadDataJavascriptCode .= "document.getElementById('DeadLine_YEAR').innerHTML='".substr($obj->DeadLine_Shamsi, 2, 2)."'; \r\n "; 
		  $LoadDataJavascriptCode .= "document.getElementById('DeadLine_MONTH').innerHTML='".substr($obj->DeadLine_Shamsi, 5, 2)."'; \r\n "; 
		  $LoadDataJavascriptCode .= "document.getElementById('DeadLine_DAY').innerHTML='".substr($obj->DeadLine_Shamsi, 8, 2)."'; \r\n "; 
	  }
	}
	if(isset($_REQUEST["UpdateID"]))
	  $DeadLine = $obj->DeadLine_Shamsi;
?>
<form method="post" id="f1" name="f1" enctype="multipart/form-data" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
	}
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش دستور کار</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<? 
if(!isset($_REQUEST["UpdateID"]))
{
?> 
<input type="hidden" name="UniversitySessionID" id="UniversitySessionID" value='<? if(isset($_REQUEST["UniversitySessionID"])) echo htmlentities($_REQUEST["UniversitySessionID"], ENT_QUOTES, 'UTF-8'); ?>'>
<? } ?>
<tr>
	<td width="1%" nowrap>
 ردیف
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type="text" name="Item_OrderNo" id="Item_OrderNo" maxlength="20" size="40" readonly>
	<? } else { ?>
	<span id="Item_OrderNo" name="Item_OrderNo"></span> 
	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 شرح
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<textarea name="Item_description" id="Item_description" cols="80" rows="5"><?php if(isset($_REQUEST["UpdateID"])) echo htmlentities($obj->description, ENT_QUOTES, 'UTF-8'); ?></textarea>
	<? } else { ?>
	<span id="Item_description" name="Item_description"><?php if(isset($_REQUEST["UpdateID"])) echo htmlentities($obj->description, ENT_QUOTES, 'UTF-8'); ?></span> 
	<? } ?>
	</td>
</tr>

<tr>
	<td width="1%" nowrap>
 ثبت اقدامات
	</td>
	<td >
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<textarea name="Item_ActReg" id="Item_ActReg" cols="80" rows="5"><?php if(isset($_REQUEST["UpdateID"])) echo htmlentities($obj->ActReg, ENT_QUOTES, 'UTF-8'); ?></textarea>
	<? } else { ?>
	<span id="Item_ActReg" name="Item_ActReg"><?php if(isset($_REQUEST["UpdateID"])) echo htmlentities($obj->ActReg, ENT_QUOTES, 'UTF-8'); ?></span> 
	<? } ?>
	</td>
</tr>


<tr>
	<td width="1%" nowrap>
  مسوول پیگیری
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type=hidden name="Item_ResponsiblePersonID" id="Item_ResponsiblePersonID">
	<span id="Span_ResponsiblePersonID_FullName" name="Span_ResponsiblePersonID_FullName"></span> 	<a href='#' onclick='javascript: window.open("SelectStaff.php?InputName=Item_ResponsiblePersonID&SpanName=Span_ResponsiblePersonID_FullName");'>[انتخاب]</a>
	<? } else { ?>
	<span id="Span_ResponsiblePersonID_FullName" name="Span_ResponsiblePersonID_FullName"></span> 	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 در دستور کاری بعدی تکرار شود؟
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<select name="Item_RepeatInNextSession" id="Item_RepeatInNextSession" >
		<option value='NO'>خیر</option>
		<option value='YES'>بلی</option>
	</select>
	<? } else { ?>
	<span id="Item_RepeatInNextSession" name="Item_RepeatInNextSession"></span> 	<? } ?>
	</td>
</tr>
<tr>
	<td width="1%" nowrap>
 فایل ضمیمه
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
	<input type="file" name="Item_RelatedFile" id="Item_RelatedFile">
	<? if(isset($_REQUEST["UpdateID"]) && $obj->RelatedFileName!="") { ?>
	<a href='DownloadFile.php?FileType=PreCommands&RecID=<? echo $_REQUEST["UpdateID"]; ?>'>دریافت فایل [<?php echo $obj->RelatedFileName; ?>]</a>
	<? } ?>
	<? } else { ?>
	<? if(isset($_REQUEST["UpdateID"]) && $obj->RelatedFileName!="") { ?>
	<a href='DownloadFile.php?FileType=PreCommand&RecID=<? echo $_REQUEST["UpdateID"]; ?>'>دریافت فایل [<?php echo $obj->RelatedFileName; ?>]</a>
	<? } ?>
	<? } ?>
	</td>
</tr>

<tr>
	<td width="1%" nowrap>
تاریخ مهلت
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>

		<input maxlength="2" id="DeadLine_DAY"  name="DeadLine_DAY" type="text" size="2">/
		<input maxlength="2" id="DeadLine_MONTH"  name="DeadLine_MONTH" type="text" size="2" >/
		<input maxlength="2" id="DeadLine_YEAR" name="DeadLine_YEAR" type="text" size="2" >

	<? } else { ?>
		<!--<span id="DeadLine_DAY" name="DeadLine_DAY"></span>/
	 	<span id="DeadLine_MONTH" name="DeadLine_MONTH"></span>/
	 	<span id="DeadLine_YEAR" name="DeadLine_YEAR"></span>-->
 	<? /*echo 'test';*/ if ($obj->DeadLine_Shamsi!=01 && $obj->DeadLine_Shamsi!="date-error") {echo $DeadLine;}
else{echo '1000-01-01';}
} ?>
	</td>
</tr>

<tr>
	<td width="1%" nowrap>
                                                                                                                                                                                                                                      اولویت
	</td>
	<td nowrap>
	<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>

		<select name="Item_priority" id="Item_priority" >
			<option value='1'>بالا</option>
			<option value='2'>متوسط</option>
			<option value='3'>پایین</option>
		</select>


	<? } else { ?>

		<span id="Item_priority" name="Item_priority"></span>

	<? } ?>
	</td>
</tr>




</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<? if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || (!isset($_REQUEST["UpdateID"]) && $HasAddAccess))
	{
?>
<input type="button" onclick="javascript: ValidateForm();" value="ذخیره">
<? } ?>
 <input type="button" onclick="javascript: window.close();" value="بستن">
</td>
</tr>
</table>
<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		document.f1.submit();
	}
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
</html>
