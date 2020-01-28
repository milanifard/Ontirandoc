<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : پروژه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-15
*/
include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/projects.class.php");
include_once("classes/projectsSecurity.class.php");
include_once("classes/ProjectGroups.class.php");

HTMLBegin();
if(isset($_REQUEST["UpdateID"])) 
	$pc = security_projects::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["UpdateID"]);
else
	$pc = security_projects::LoadUserPermissions($_SESSION["PersonID"], 0);

if(isset($_REQUEST["Save"])) 
{
	
	$Achievable = "NO";	
	if(isset($_REQUEST["Achievable"])) 
		$Achievable = "YES";
	
	if(isset($_REQUEST["Item_title"]))
		$Item_title=$_REQUEST["Item_title"];
	if(isset($_REQUEST["Item_description"]))
		$Item_description=$_REQUEST["Item_description"];
	if(isset($_REQUEST["StartTime_DAY"]))
	{
		$Item_StartTime = SharedClass::ConvertToMiladi($_REQUEST["StartTime_YEAR"], $_REQUEST["StartTime_MONTH"], $_REQUEST["StartTime_DAY"]);
	}
	if(isset($_REQUEST["EndTime_DAY"]))
	{
		$Item_EndTime = SharedClass::ConvertToMiladi($_REQUEST["EndTime_YEAR"], $_REQUEST["EndTime_MONTH"], $_REQUEST["EndTime_DAY"]);
	}
	if(isset($_REQUEST["Item_SysCode"]))
		$Item_SysCode=$_REQUEST["Item_SysCode"];
	if(isset($_REQUEST["Item_ProjectPriority"]))
		$Item_ProjectPriority=$_REQUEST["Item_ProjectPriority"];
	if(isset($_REQUEST["Item_ProjectStatus"]))
		$Item_ProjectStatus=$_REQUEST["Item_ProjectStatus"];
	if(isset($_REQUEST["Item_ouid"]))
		$Item_ouid=$_REQUEST["Item_ouid"];
	if(isset($_REQUEST["Item_ProjectGroupID"]))
		$Item_ProjectGroupID=$_REQUEST["Item_ProjectGroupID"];
		
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_projects::Add($Item_ouid
				, $Item_ProjectGroupID
				, $Item_title
				, $Item_description
				, $Item_StartTime
				, $Item_EndTime
				, $Item_SysCode
				, $Item_ProjectPriority
				, $Item_ProjectStatus
				, $Achievable
				, $pc
				);
		echo "<script>window.opener.document.location.reload(); window.close();</script>";
	}	
	else 
	{	
		manage_projects::Update($_REQUEST["UpdateID"]
		 		, $Item_ouid
				, $Item_ProjectGroupID
				, $Item_title
				, $Item_description
				, $Item_StartTime
				, $Item_EndTime
				, $Item_SysCode
				, $Item_ProjectPriority
				, $Item_ProjectStatus
				, $Achievable
				, $pc
				);
		echo "<script>window.opener.document.location.reload(); window.close();</script>";
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
$PDesc = "";
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_projects();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$FieldPermission = $pc->GetPermission("title");
	if($FieldPermission=="WRITE")
		$LoadDataJavascriptCode .= "document.f1.Item_title.value='".htmlentities($obj->title, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else if($FieldPermission=="READ")
		$LoadDataJavascriptCode .= "document.getElementById('Item_title').innerHTML='".htmlentities($obj->title, ENT_QUOTES, 'UTF-8')."'; \r\n "; 

	$FieldPermission = $pc->GetPermission("ouid");
	if($FieldPermission=="WRITE")
		$LoadDataJavascriptCode .= "document.f1.Item_ouid.value='".htmlentities($obj->ouid, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else if($FieldPermission=="READ")
		$LoadDataJavascriptCode .= "document.getElementById('Item_ouid').innerHTML='".htmlentities($obj->ouid_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 

	$FieldPermission = $pc->GetPermission("ProjectGroupID");
	if($FieldPermission=="WRITE")
	{
		$LoadDataJavascriptCode .= "updateProjectGroupsList(".$obj->ouid.");\r\n";
		$LoadDataJavascriptCode .= "document.f1.Item_ProjectGroupID.value='".htmlentities($obj->ProjectGroupID, ENT_QUOTES, 'UTF-8')."'; \r\n ";		
	} 
	else if($FieldPermission=="READ")
		$LoadDataJavascriptCode .= "document.getElementById('Item_ProjectGroupID').innerHTML='".htmlentities($obj->ProjectGroupID_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
		
	$FieldPermission = $pc->GetPermission("title");
	if($FieldPermission=="WRITE")
		$LoadDataJavascriptCode .= "document.f1.Item_title.value='".htmlentities($obj->title, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else if($FieldPermission=="READ")
		$LoadDataJavascriptCode .= "document.getElementById('Item_title').innerHTML='".htmlentities($obj->title, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
		
	$FieldPermission = $pc->GetPermission("description");
	$PDesc = htmlentities($obj->description, ENT_QUOTES, 'UTF-8'); 
	$FieldPermission = $pc->GetPermission("StartTime");
	if($obj->StartTime_Shamsi!="date-error") 
	{
		if($FieldPermission=="WRITE")
		{
			$LoadDataJavascriptCode .= "document.f1.StartTime_YEAR.value='".substr($obj->StartTime_Shamsi, 2, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.f1.StartTime_MONTH.value='".substr($obj->StartTime_Shamsi, 5, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.f1.StartTime_DAY.value='".substr($obj->StartTime_Shamsi, 8, 2)."'; \r\n "; 
		}
		else if($FieldPermission=="READ")
		{
			$LoadDataJavascriptCode .= "document.getElementById('StartTime_YEAR').innerHTML='".substr($obj->StartTime_Shamsi, 2, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.getElementById('StartTime_MONTH').innerHTML='".substr($obj->StartTime_Shamsi, 5, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.getElementById('StartTime_DAY').innerHTML='".substr($obj->StartTime_Shamsi, 8, 2)."'; \r\n "; 
		}
	}
	$FieldPermission = $pc->GetPermission("EndTime");
	if($obj->EndTime_Shamsi!="date-error") 
	{
		if($FieldPermission=="WRITE")
		{
			$LoadDataJavascriptCode .= "document.f1.EndTime_YEAR.value='".substr($obj->EndTime_Shamsi, 2, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.f1.EndTime_MONTH.value='".substr($obj->EndTime_Shamsi, 5, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.f1.EndTime_DAY.value='".substr($obj->EndTime_Shamsi, 8, 2)."'; \r\n "; 
		}
		else if($FieldPermission=="READ")
		{
			$LoadDataJavascriptCode .= "document.getElementById('EndTime_YEAR').innerHTML='".substr($obj->EndTime_Shamsi, 2, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.getElementById('EndTime_MONTH').innerHTML='".substr($obj->EndTime_Shamsi, 5, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.getElementById('EndTime_DAY').innerHTML='".substr($obj->EndTime_Shamsi, 8, 2)."'; \r\n "; 
		}
	}
	$FieldPermission = $pc->GetPermission("SysCode");
	if($FieldPermission=="WRITE")
		$LoadDataJavascriptCode .= "document.f1.Item_SysCode.value='".htmlentities($obj->SysCode, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else if($FieldPermission=="READ")
		$LoadDataJavascriptCode .= "document.getElementById('Item_SysCode').innerHTML='".htmlentities($obj->SysCode_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$FieldPermission = $pc->GetPermission("ProjectPriority");
	if($FieldPermission=="WRITE")
		$LoadDataJavascriptCode .= "document.f1.Item_ProjectPriority.value='".htmlentities($obj->ProjectPriority, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else if($FieldPermission=="READ")
		$LoadDataJavascriptCode .= "document.getElementById('Item_ProjectPriority').innerHTML='".htmlentities($obj->ProjectPriority_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$FieldPermission = $pc->GetPermission("ProjectStatus");
	if($FieldPermission=="WRITE")
		$LoadDataJavascriptCode .= "document.f1.Item_ProjectStatus.value='".htmlentities($obj->ProjectStatus, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else if($FieldPermission=="READ")
		$LoadDataJavascriptCode .= "document.getElementById('Item_ProjectStatus').innerHTML='".htmlentities($obj->ProjectStatus_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 

	if($FieldPermission=="WRITE")
		if($obj->Achievable=="YES")
		$LoadDataJavascriptCode .= "document.f1.Achievable.checked='true'; \r\n ";
			
	else if($FieldPermission=="READ")
		$LoadDataJavascriptCode .= "document.getElementById('Achievable').innerHTML.checked='true'; \r\n ";
		

}	
//else
//	$LoadDataJavascriptCode .= "updateProjectGroupsList(".manage_UserProjectScopes::GetTheFirstPermittedUnitID($_SESSION["UserID"]).");\r\n";
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
		echo manage_projects::ShowSummary($_REQUEST["UpdateID"]);
		echo manage_projects::ShowTabs($_REQUEST["UpdateID"], "Newprojects");
	}
?>
<br><table width="90%" border="1" cellspacing="0" align="center">
<tr class="HeaderOfTable">
<td align="center">ایجاد/ویرایش پروژه</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<? if($pc->GetPermission("ouid")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
	 واحد سازمانی
	</td>
	<td nowrap>
	<? if($pc->GetPermission("ouid")=="WRITE") { ?>
	<select name="Item_ouid" id="Item_ouid" onchange='javascript: updateProjectGroupsList(this.value)'>
		<option value='1'>مدیریت
	</select>
	<? } else if($pc->GetPermission("title")=="READ") { ?>
	<span id="Item_title" name="Item_title"></span> 
	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("ProjectGroupID")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 گروه پروژه
	</td>
	<td nowrap>
	<? if($pc->GetPermission("ProjectGroupID")=="WRITE") { ?>
	<span id=TaskTypesSpan name=TaskTypesSpan>
		<select name="Item_ProjectGroupID" id="Item_ProjectGroupID">
		<option value=0>-
		<? echo manage_ProjectGroups::CreateSelectOptions(0); ?>	
		</select>
	</span>
	<? } else if($pc->GetPermission("ProjectGroupID")=="READ") { ?>
	<span id="Item_ProjectGroupID" name="Item_ProjectGroupID"></span> 	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("title")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 عنوان
	</td>
	<td nowrap>
	<? if($pc->GetPermission("title")=="WRITE") { ?>
	<input type="text" name="Item_title" id="Item_title" maxlength="500" size="40">
	<? } else if($pc->GetPermission("title")=="READ") { ?>
	<span id="Item_title" name="Item_title"></span> 
	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("description")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 شرح
	</td>
	<td nowrap>
	<? if($pc->GetPermission("description")=="WRITE") { ?>
	<textarea name="Item_description" id="Item_description" cols="80" rows="5"><? echo $PDesc ?></textarea>
	<? } else if($pc->GetPermission("description")=="READ") { ?>
	<span id="Item_description" name="Item_description"><? echo $PDesc ?></span> 
	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("StartTime")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 شروع
	</td>
	<td nowrap>
	<? if($pc->GetPermission("StartTime")=="WRITE") { ?>
	<input maxlength="2" id="StartTime_DAY"  name="StartTime_DAY" type="text" size="2">/
	<input maxlength="2" id="StartTime_MONTH"  name="StartTime_MONTH" type="text" size="2" >/
	<input maxlength="2" id="StartTime_YEAR" name="StartTime_YEAR" type="text" size="2" >
	<? } else if($pc->GetPermission("StartTime")=="READ") { ?>
	<span id="StartTime_DAY" name="StartTime_DAY"></span>/
 	<span id="StartTime_MONTH" name="StartTime_MONTH"></span>/
 	<span id="StartTime_YEAR" name="StartTime_YEAR"></span>
 	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("EndTime")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 پایان
	</td>
	<td nowrap>
	<? if($pc->GetPermission("EndTime")=="WRITE") { ?>
	<input maxlength="2" id="EndTime_DAY"  name="EndTime_DAY" type="text" size="2">/
	<input maxlength="2" id="EndTime_MONTH"  name="EndTime_MONTH" type="text" size="2" >/
	<input maxlength="2" id="EndTime_YEAR" name="EndTime_YEAR" type="text" size="2" >
	<? } else if($pc->GetPermission("EndTime")=="READ") { ?>
	<span id="EndTime_DAY" name="EndTime_DAY"></span>/
 	<span id="EndTime_MONTH" name="EndTime_MONTH"></span>/
 	<span id="EndTime_YEAR" name="EndTime_YEAR"></span>
 	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("SysCode")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 سیستم مربوطه
	</td>
	<td nowrap>
	<? if($pc->GetPermission("SysCode")=="WRITE") { ?>
	<select name="Item_SysCode" id="Item_SysCode">
	<option value=0>-
	<? echo SharedClass::CreateARelatedTableSelectOptions("projectmanagement.systems", "SysCode", "description", "description"); ?>	</select>
	<? } else if($pc->GetPermission("SysCode")=="READ") { ?>
	<span id="Item_SysCode" name="Item_SysCode"></span> 	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("ProjectPriority")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 اولویت
	</td>
	<td nowrap>
	<? if($pc->GetPermission("ProjectPriority")=="WRITE") { ?>
	<select name="Item_ProjectPriority" id="Item_ProjectPriority" >
		<option value='2'>عادی</option>
		<option value='3'>پایین</option>
		<option value='1'>بالا</option>
		<option value='0'>بحرانی</option>
	</select>
	<? } else if($pc->GetPermission("ProjectPriority")=="READ") { ?>
	<span id="Item_ProjectPriority" name="Item_ProjectPriority"></span> 	<? } ?>
	</td>
</tr>
<? } ?>
<? if($pc->GetPermission("ProjectStatus")!="NONE") { ?>
<tr>
	<td width="1%" nowrap>
 وضعیت
	</td>
	<td nowrap>
	<? if($pc->GetPermission("ProjectStatus")=="WRITE") { ?>
	<select name="Item_ProjectStatus" id="Item_ProjectStatus" >
		<option value='NOT_STARTED'>شروع نشده</option>
		<option value='DEVELOPING'>در دست اقدام</option>
		<option value='MAINTENANCE'>در حال پشتیبانی</option>
		<option value='FINISHED'>خاتمه یافته</option>
		<option value='SUSPENDED'>معلق</option>
	</select>
	<? } else if($pc->GetPermission("ProjectStatus")=="READ") { ?>
	<span id="Item_ProjectStatus" name="Item_ProjectStatus"></span> 	<? } ?>
	</td>
</tr>
<? } ?>

<? if($pc->GetPermission("Achievable")!="NONE") { ?>
<tr>

	<td nowrap colspan=2>
	<? if($pc->GetPermission("Achievable")=="WRITE") { ?>
	
	<input type=checkbox name='Achievable' id='Achievable'  > 
	 <b>قابل دستیابی برای کلیه اعضای دانشگاه اعم از اعضای هیات علمی,کارمندان ودانشجویان</b>
	<? } 
	else if($pc->GetPermission("ProjectPriority")=="READ") { ?>
	<span id="Achievable" name="Achievable"></span> 	<? } ?>
	</td>
</tr>
<? } ?>

</table>
</td>
</tr>
<tr class="FooterOfTable">
<td align="center">
<? if($pc->HasWriteAccessOnOneItemAtLeast) { ?>
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
	function getHTTPObject() 
	{
	  var xmlhttp;
	  if (!xmlhttp && typeof XMLHttpRequest != 'undefined') 
	  {
	    try 
	    {
	      xmlhttp = new XMLHttpRequest();
	    } 
	    catch (e) 
	    {
	      xmlhttp = false;
	    }
	  }
	   return xmlhttp;
	}
	var http; // We create the HTTP Object
	var SelectedID;
	
	function handleHttpResponse()
	{
	  if(this.readyState==4)
	    {
	    	document.getElementById('TaskTypesSpan').innerHTML = this.responseText;
			<?php 

					if(isset($_REQUEST["UpdateID"])) 
				  	{
						$FieldPermission = $pc->GetPermission("ProjectGroupID");
						if($FieldPermission=="WRITE")
						{
						  	echo "		if(document.getElementById('Item_ouid').value==".$obj->ouid.")\r\n";
						  	echo "			document.f1.Item_ProjectGroupID.value='".htmlentities($obj->ProjectGroupID, ENT_QUOTES, 'UTF-8')."'; \r\n ";
						}			  
					}
			?>
	    }
	}
	
	function updateProjectGroupsList(ouid) 
	{
	  http = getHTTPObject();
	  http.open("GET", "GetProjectGroupsList.php?ouid="+ouid, true);
	  http.onreadystatechange = handleHttpResponse;
	  http.send(null);
	}
</script>
</html>