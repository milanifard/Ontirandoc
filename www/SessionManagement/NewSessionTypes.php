<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : الگوهای جلسه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-2-26
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/SessionTypes.class.php");
HTMLBegin();
if(isset($_REQUEST["Save"])) 
{
	if(isset($_REQUEST["Item_SessionTypeTitle"]))
		$Item_SessionTypeTitle=$_REQUEST["Item_SessionTypeTitle"];
	if(isset($_REQUEST["Item_SessionTypeLocation"]))
		$Item_SessionTypeLocation=$_REQUEST["Item_SessionTypeLocation"];
	if(isset($_REQUEST["SessionTypeStartTime_HOUR"]))
	{
		$Item_SessionTypeStartTime=$_REQUEST["SessionTypeStartTime_HOUR"]*60+$_REQUEST["SessionTypeStartTime_MIN"];
	}
	if(isset($_REQUEST["SessionTypeDurationTime_HOUR"]))
	{
		$Item_SessionTypeDurationTime=$_REQUEST["SessionTypeDurationTime_HOUR"]*60+$_REQUEST["SessionTypeDurationTime_MIN"];
	}
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		manage_SessionTypes::Add($Item_SessionTypeTitle
				, $Item_SessionTypeLocation
				, $Item_SessionTypeStartTime
				, $Item_SessionTypeDurationTime
				);
		echo "<script>window.opener.document.location.reload(); window.close();</script>";
	}	
	else 
	{	
		manage_SessionTypes::Update($_REQUEST["UpdateID"] 
				, $Item_SessionTypeTitle
				, $Item_SessionTypeLocation
				, $Item_SessionTypeStartTime
				, $Item_SessionTypeDurationTime
				);
		echo "<script>window.opener.document.location.reload(); window.close();</script>";
		die();
	}	
	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد");
}
$LoadDataJavascriptCode = '';
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_SessionTypes();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$LoadDataJavascriptCode .= "document.f1.Item_SessionTypeTitle.value='".htmlentities($obj->SessionTypeTitle, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.Item_SessionTypeLocation.value='".htmlentities($obj->SessionTypeLocation, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.SessionTypeStartTime_HOUR.value='".floor($obj->SessionTypeStartTime/60)."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.SessionTypeStartTime_MIN.value='".($obj->SessionTypeStartTime%60)."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.SessionTypeDurationTime_HOUR.value='".floor($obj->SessionTypeDurationTime/60)."'; \r\n "; 
	$LoadDataJavascriptCode .= "document.f1.SessionTypeDurationTime_MIN.value='".($obj->SessionTypeDurationTime%60)."'; \r\n "; 
}
?>
<form method="post" id="f1" name="f1" >
<?
	if(isset($_REQUEST["UpdateID"])) 
	{
		echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
		echo manage_SessionTypes::ShowSummary($_REQUEST["UpdateID"]);
		echo manage_SessionTypes::ShowTabs($_REQUEST["UpdateID"], "NewSessionTypes");
	}
?>
<br>
    <div class="table-responsive container-fluid">
        <div class="row">
            <div class="col-sm-2"></div>
            <table class="table table-bordered col-sm-8">
                <thead class="bg-info">
                    <tr>
                        <th class="text-center">ایجاد/ویرایش الگوهای جلسه</th>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <table class="table table-bordered tab">
                            <tr>
                                <td width="1%" nowrap>
                                    <font color=red>*</font> عنوان
                                </td>
                                <td style="">
                                    <input class="form-control" type="text" name="Item_SessionTypeTitle" id="Item_SessionTypeTitle" maxlength="500">
                                </td>
                            </tr>
                            <tr>
                                <td width="1%" nowrap>
                                    <font color=red>*</font> محل تشکیل
                                </td>
                                <td style="">
                                    <input class="form-control" type="text" name="Item_SessionTypeLocation" id="Item_SessionTypeLocation" maxlength="200">
                                </td>
                            </tr>
                            <tr>
                                <td width="1%" nowrap>
                                    زمان شروع
                                </td>
                                <td nowrap>
                                    <input maxlength="2" id="SessionTypeStartTime_MIN"  name="SessionTypeStartTime_MIN" type="text" size="2">:
                                    <input maxlength="2" id="SessionTypeStartTime_HOUR"  name="SessionTypeStartTime_HOUR" type="text" size="2" >
                                </td>
                            </tr>
                            <tr>
                                <td width="1%" nowrap>
                                    مدت زمان
                                </td>
                                <td nowrap>
                                    <input maxlength="2" id="SessionTypeDurationTime_MIN"  name="SessionTypeDurationTime_MIN" type="text" size="2">:
                                    <input maxlength="2" id="SessionTypeDurationTime_HOUR"  name="SessionTypeDurationTime_HOUR" type="text" size="2" >
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </tbody>
                <thead class="text-center bg-info">
                <tr>
                    <td>
                        <button type="button" class="btn btn-success" onclick="javascript: ValidateForm();"> ذخیره</button>
                        <button type="button" class="btn btn-danger" onclick="javascript: window.close();"> بستن</button>
                    </td>
                </tr>
                </thead>
            </table>
            <div class="col-sm-2"></div>
        </div>
    </div>
<input type="hidden" name="Save" id="Save" value="1">
</form><script>
	<? echo $LoadDataJavascriptCode; ?>
	function ValidateForm()
	{
		if(document.getElementById('Item_SessionTypeTitle'))
		{
			if(document.getElementById('Item_SessionTypeTitle').value=='')
			{
				alert('مقداری در عنوان وارد نشده است');
				return;
			}
		}
		if(document.getElementById('Item_SessionTypeLocation'))
		{
			if(document.getElementById('Item_SessionTypeLocation').value=='')
			{
				alert('مقداری در محل تشکیل وارد نشده است');
				return;
			}
		}
		document.f1.submit();
	}
</script>
</html>
