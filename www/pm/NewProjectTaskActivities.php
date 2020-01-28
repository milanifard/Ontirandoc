<?php
/*
 صفحه  ایجاد/ویرایش مربوط به : اقدامات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-17
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/ProjectTaskActivities.class.php");
include("classes/ProjectTasks.class.php");
include("classes/ProjectTaskActivityTypes.class.php");
include("classes/ProjectTasksSecurity.class.php");
/*
if($_SESSION["User"]->PersonID == 401371457)
{
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}
*/
$ChangeTables = $ChangePages = "";
$task = new be_ProjectTasks();
HTMLBegin();
// نحوه دسترسی کاربر به آیتم پدر را بارگذاری می کند
if(isset($_REQUEST["UpdateID"])) 
{
	$obj = new be_ProjectTaskActivities();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $obj->ProjectTaskID);
	$task->LoadDataFromDatabase($obj->ProjectTaskID);
}
else
{
	$ppc = security_ProjectTasks::LoadUserPermissions($_SESSION["PersonID"], $_REQUEST["ProjectTaskID"]);
	$task->LoadDataFromDatabase($_REQUEST["ProjectTaskID"]);
}
$HasAddAccess = $HasUpdateAccess = $HasViewAccess = false;
if($ppc->GetPermission("Add_ProjectTaskActivities")=="YES")
	$HasAddAccess = true;
if(isset($_REQUEST["UpdateID"])) 
{ 
	if($ppc->GetPermission("Update_ProjectTaskActivities")=="PUBLIC")
		$HasUpdateAccess = true;
	else if($ppc->GetPermission("Update_ProjectTaskActivities")=="PRIVATE" && $_SESSION["PersonID"]==$obj->CreatorID)
		$HasUpdateAccess = true;
	if($ppc->GetPermission("View_ProjectTaskActivities")=="PUBLIC")
		$HasViewAccess = true;
	else if($ppc->GetPermission("View_ProjectTaskActivities")=="PRIVATE" && $_SESSION["PersonID"]==$obj->CreatorID)
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
	if(isset($_REQUEST["ProjectTaskID"]))
		$Item_ProjectTaskID=$_REQUEST["ProjectTaskID"];
	if(isset($_REQUEST["Item_CreatorID"]))
		$Item_CreatorID=$_REQUEST["Item_CreatorID"];
	if(isset($_REQUEST["ActivityDate_DAY"]))
	{
		$Item_ActivityDate = SharedClass::ConvertToMiladi($_REQUEST["ActivityDate_YEAR"], $_REQUEST["ActivityDate_MONTH"], $_REQUEST["ActivityDate_DAY"]);
	}
	if(isset($_REQUEST["Item_ProjectTaskActivityTypeID"]))
		$Item_ProjectTaskActivityTypeID=$_REQUEST["Item_ProjectTaskActivityTypeID"];
	if(isset($_REQUEST["ActivityLength_HOUR"]))
	{
		$Item_ActivityLength=$_REQUEST["ActivityLength_HOUR"]*60+$_REQUEST["ActivityLength_MIN"];
	}
	if(isset($_REQUEST["Item_ProgressPercent"]))
		$Item_ProgressPercent=$_REQUEST["Item_ProgressPercent"];
	if(isset($_REQUEST["Item_ActivityDescription"]))
		$Item_ActivityDescription=$_REQUEST["Item_ActivityDescription"];
	$Item_FileContent = "";
	$extension = "";

	if ((isset($_FILES ['Item_FileContent']) && trim($_FILES ['Item_FileContent']['tmp_name']) != ''))
	{
		if ($_FILES['Item_FileContent']['error'] != 0)
		{
			echo ' خطا در ارسال فایل' . $_FILES['Item_FileContent']['error'];
		}
		else
		{
                        $st = preg_split( "/\./", $_FILES ['Item_FileContent']['name']);
			$extension = $st[count($st) - 1];
		}
	}

	if(isset($_REQUEST["Item_ChangedTables"]))
		$Item_ChangedTables=$_REQUEST["Item_ChangedTables"];
	if(isset($_REQUEST["Item_ChangedPages"]))
		$Item_ChangedPages=$_REQUEST["Item_ChangedPages"];
	if(!isset($_REQUEST["UpdateID"])) 
	{	
		if($HasAddAccess)
		$TaskActID=manage_ProjectTaskActivities::Add($Item_ProjectTaskID
				, $Item_ActivityDate
				, $Item_ProjectTaskActivityTypeID
				, $Item_ActivityLength
				, $Item_ProgressPercent
				, $Item_ActivityDescription
				, $Item_FileContent
				, $extension
				, $Item_ChangedTables
				, $Item_ChangedPages
				);
	}	
	else 
	{	
		if($HasUpdateAccess)
		manage_ProjectTaskActivities::Update($_REQUEST["UpdateID"] 
				, $Item_ActivityDate
				, $Item_ProjectTaskActivityTypeID
				, $Item_ActivityLength
				, $Item_ProgressPercent
				, $Item_ActivityDescription
				, $Item_FileContent
				, $extension
				, $Item_ChangedTables
				, $Item_ChangedPages
				);
                   $TaskActID = $_REQUEST["UpdateID"];
	}
	
	if ((isset($_FILES ['Item_FileContent']) && trim($_FILES ['Item_FileContent']['tmp_name']) != ''))
	{
		$fp = fopen("/mystorage/PlanAndProjectDocuments/TaskActivities/$TaskActID.$extension", "w");
		$Size = fwrite ($fp, fread(fopen($_FILES['Item_FileContent']['tmp_name'], 'r'), $_FILES['Item_FileContent']['size']));
		fclose ($fp);
		
		if (!$Size)
		{
			echo SharedClass::CreateMessageBox("فایل به درستی ذخیره نشد.");
			die;
		}
	}

	echo SharedClass::CreateMessageBox("اطلاعات ذخیره شد.");
	
	echo "<script>window.opener.document.location.reload(); window.close();</script>";
	die();
}
$LoadDataJavascriptCode = '';
$ret = manage_ProjectTasks::GetTaskProgressPercentAndUsedTime($task->ProjectTaskID);
$RemainPercent = 100-$ret["TotalProgress"];
if($RemainPercent<0)
	$RemainPercent = 0;
if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
	$LoadDataJavascriptCode .= "document.f1.Item_ProgressPercent.value='".$RemainPercent."'; \r\n "; 
else
	$LoadDataJavascriptCode .= "document.getElementById('Item_ProgressPercent').innerHTML='".$RemainPercent."'; \r\n "; 
$ActivityDescription = "";
if(isset($_REQUEST["UpdateID"])) 
{	
	$obj = new be_ProjectTaskActivities();
	$obj->LoadDataFromDatabase($_REQUEST["UpdateID"]); 
	if($obj->ActivityDate_Shamsi!="date-error") 
	{
		if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		{
			$LoadDataJavascriptCode .= "document.f1.ActivityDate_YEAR.value='".substr($obj->ActivityDate_Shamsi, 2, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.f1.ActivityDate_MONTH.value='".substr($obj->ActivityDate_Shamsi, 5, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.f1.ActivityDate_DAY.value='".substr($obj->ActivityDate_Shamsi, 8, 2)."'; \r\n "; 
		}
		else 
		{
			$LoadDataJavascriptCode .= "document.getElementById('ActivityDate_YEAR').innerHTML='".substr($obj->ActivityDate_Shamsi, 2, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.getElementById('ActivityDate_MONTH').innerHTML='".substr($obj->ActivityDate_Shamsi, 5, 2)."'; \r\n "; 
			$LoadDataJavascriptCode .= "document.getElementById('ActivityDate_DAY').innerHTML='".substr($obj->ActivityDate_Shamsi, 8, 2)."'; \r\n "; 
		}
	}
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_ProjectTaskActivityTypeID.value='".htmlentities($obj->ProjectTaskActivityTypeID, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_ProjectTaskActivityTypeID').innerHTML='".htmlentities($obj->ProjectTaskActivityTypeID_Desc, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
	{
		$LoadDataJavascriptCode .= "document.f1.ActivityLength_HOUR.value='".floor($obj->ActivityLength/60)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.ActivityLength_MIN.value='".($obj->ActivityLength%60)."'; \r\n "; 
	}
	else 
	{
		$LoadDataJavascriptCode .= "document.getElementById('ActivityLength_HOUR').innerHTML='".floor($obj->ActivityLength/60)."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.getElementById('ActivityLength_MIN').innerHTML='".($obj->ActivityLength%60)."'; \r\n "; 
	}
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_ProgressPercent.value='".htmlentities($obj->ProgressPercent, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_ProgressPercent').innerHTML='".htmlentities($obj->ProgressPercent, ENT_QUOTES, 'UTF-8')."'; \r\n ";
	/* 
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_ActivityDescription.value='".htmlentities($obj->ActivityDescription, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_ActivityDescription').innerHTML='".htmlentities($obj->ActivityDescription, ENT_QUOTES, 'UTF-8')."'; \r\n ";
	*/
	$ActivityDescription = htmlentities($obj->ActivityDescription, ENT_QUOTES, 'UTF-8'); 
	
	/*
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_ChangedTables.value='".htmlentities($obj->ChangedTables, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_ChangedTables').innerHTML='".htmlentities($obj->ChangedTables, ENT_QUOTES, 'UTF-8')."'; \r\n ";
	 if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
		$LoadDataJavascriptCode .= "document.f1.Item_ChangedPages.value='".htmlentities($obj->ChangedPages, ENT_QUOTES, 'UTF-8')."'; \r\n "; 
	else
		$LoadDataJavascriptCode .= "document.getElementById('Item_ChangedPages').innerHTML='".htmlentities($obj->ChangedPages, ENT_QUOTES, 'UTF-8')."'; \r\n ";
	*/
	$ChangeTables =  htmlentities($obj->ChangedTables, ENT_QUOTES, 'UTF-8');
	$ChangePages =  htmlentities($obj->ChangedPages, ENT_QUOTES, 'UTF-8');
}	
else
{
	$now = date("Ymd"); 
	$yy = substr($now,0,4); 
	$mm = substr($now,4,2); 
	$dd = substr($now,6,2);
	$CurrentDay = $yy."/".$mm."/".$dd;
	list($dd,$mm,$yy) = ConvertX2SDate($dd,$mm,$yy);
	$yy = substr($yy, 2, 2);
	
	if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"])))
	{
		$LoadDataJavascriptCode .= "document.f1.ActivityDate_YEAR.value='".$yy."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.ActivityDate_MONTH.value='".$mm."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.f1.ActivityDate_DAY.value='".$dd."'; \r\n "; 
	}
	else 
	{
		$LoadDataJavascriptCode .= "document.getElementById('ActivityDate_YEAR').innerHTML='".$yy."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.getElementById('ActivityDate_MONTH').innerHTML='".$mm."'; \r\n "; 
		$LoadDataJavascriptCode .= "document.getElementById('ActivityDate_DAY').innerHTML='".$dd."'; \r\n "; 
	}
}
?>
<div class="container">
    <form method="post" id="f1" name="f1" enctype="multipart/form-data" >
        <?php
            if(isset($_REQUEST["UpdateID"]))
            {
                echo "<input type=\"hidden\" name=\"UpdateID\" id=\"UpdateID\" value='".$_REQUEST["UpdateID"]."'>";
            }
        ?>
        <br>
        <table class="table table-bordered table-striped">
            <thead class="HeaderOfTable">
            <th class="text-center table-primary">ایجاد/ویرایش اقدامات</th>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?php
                        if(!isset($_REQUEST["UpdateID"]))
                        {
                        ?>
                        <input type="hidden" name="ProjectTaskID" id="ProjectTaskID" value='<?php if(isset($_REQUEST["ProjectTaskID"])) echo htmlentities($_REQUEST["ProjectTaskID"], ENT_QUOTES, 'UTF-8'); ?>'>
                        <?php } ?>


                        <div class="form-group row">
                            <label for="Activity_groups" class="col-sm-2 col-form-label"> تاریخ اقدام</label>
                            <div >
                                <?php if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
                                    <div id="Activity_groups" >
                                        <input type="text" class="form-control col-sm-4 " maxlength="2" id="ActivityDate_DAY"  name="ActivityDate_DAY"  size="2">
                                        <input type="text" class="form-control col-sm-4" maxlength="2" id="ActivityDate_MONTH"  name="ActivityDate_MONTH" size="2" >
                                        <input type="text" class="form-control col-sm-4" maxlength="2" id="ActivityDate_YEAR" name="ActivityDate_YEAR"  size="2" >
                                    </div>
                                <?php } else { ?>
                                <div id="Activity_groups " class="row" >
                                    <span class="form-control col-sm-4" id="ActivityDate_DAY" name="ActivityDate_DAY"></span>
                                    <span class="form-control col-sm-4" id="ActivityDate_MONTH" name="ActivityDate_MONTH"></span>
                                    <span class="form-control col-sm-4" id="ActivityDate_YEAR" name="ActivityDate_YEAR"></span>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                     </td>
                <tr>
                    <td>
                        <div class="form-row">
                            <div class="form-group col-sm-2">
                                <label for="Item_ProjectTaskActivityTypeID" > نوع اقدام</label>
                            </div>

                            <div class="form-group col-md-10">
                                <?php if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
                                    <div>
                                        <select class="form-control " name="Item_ProjectTaskActivityTypeID" id="Item_ProjectTaskActivityTypeID">
                                            <option value=0>
                                                <?php echo manage_ProjectTaskActivityTypes::CreateSelectOptions($task->ProjectID); ?>
                                        </select>
                                        </div>
                                <?php }
                                else { ?>
                                    <span class="form-control " id="Item_ProjectTaskActivityTypeID" name="Item_ProjectTaskActivityTypeID"></span>
                                    <? } ?>
                            </div>
                        </div>
                    </td>
                <tr>
                    <td>
                        <div class="form-row ">
                            <div class="form-group col-sm-2">
                            <label for="Activity_Length" >زمان مصرفی</label>
                            </div>
                                <?php if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
                                     <div id="Activity_Length" class="form-group col-md-4" >
                                        <input class="form-control " maxlength="2" id="ActivityLength_MIN"  name="ActivityLength_MIN" type="text" size="2">:
                                        <input class="form-control" maxlength="3" id="ActivityLength_HOUR"  name="ActivityLength_HOUR" type="text" size="3" >
                                    </div>
                                <?php } else { ?>
                                    <div id="Activity_Length"  class="form-group row">
                                        <span class="form-control col-5" id="ActivityLength_MIN" name="ActivityLength_MIN"></span> :
                                        <span class="form-control col-5" id="ActivityLength_HOUR" name="ActivityLength_HOUR"></span>
                                    </div>
                                <?php } ?>
                        </div>
                    </td>
                <tr>
                    <td>
                        <div class="form-group row">
                            <label for="Item_ProgressPercent" class="col-sm-2 col-form-label">درصد پیشرفت</label>
                            <div class="col-sm-10">
                                <?php if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
                                    <input class="form-control" type="text" name="Item_ProgressPercent" id="Item_ProgressPercent" maxlength="3" size="3">
                                <?php } else { ?>
                                    <span class="form-control"  id="Item_ProgressPercent" name="Item_ProgressPercent"></span>
                                <?php } ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="form-group row">
                                <label for="Item_ActivityDescription" class="col-sm-2 col-form-label">شرح</label>
                                <div class="col-sm-10">
                                    <?php if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
                                    <textarea class="form-control" name="Item_ActivityDescription" id="Item_ActivityDescription" cols="80" rows="5"><?php echo $ActivityDescription ?></textarea>
                                    <?php } else { ?>
                                    <span class="form-control" id="Item_ActivityDescription" name="Item_ActivityDescription"><?php echo str_replace("\r", "<br>", $ActivityDescription); ?></span>
                                    <?php } ?>
                                </div>
                        </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group row">
                                <label for="Item_FileContent" class="col-sm-2 col-form-label">فایل ضمیمه</label>
                                <div class="col-sm-10">
                                    <?php if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
                                     <input class="form-control" type="file" name="Item_FileContent" id="Item_FileContent">
                                    <?php if(isset($_REQUEST["UpdateID"]) && $obj->FileName!="") { ?>
                                    <a  href='ReciptFile.php?AID=<? echo $obj->ProjectTaskActivityID; ?>&FileName_AID=<? echo $obj->FileName;?>'>دریافت فایل [<?php
                                    echo $obj->FileName ?>]</a>
                                    <?php } ?>
                                    <?php } else { ?>
                                    <?php if(isset($_REQUEST["UpdateID"]) && $obj->FileName!="") { ?>
                                    <a  href='ReciptFile.php?AID=<? echo $obj->ProjectTaskActivityID; ?>&FileName_AID=<? echo $obj->FileName;?>'>دریافت فایل [<?php
                                            echo $obj->FileName ?>]</a>
                                    <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group row">
                                    <label for="Item_ChangedTables" class="col-sm-2 col-form-label">جداول تغییر داده شده</label>
                                    <div class="col-sm-10">
                                    <?php if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
                                        <textarea  class="form-control name="Item_ChangedTables" id="Item_ChangedTables" cols="80" rows="5"><?php echo $ChangeTables ?></textarea>
                                    <?php } else { ?>
                                        <span class="form-control id="Item_ChangedTables" name="Item_ChangedTables"><?php echo str_replace("\r", "<br>", $ChangeTables); ?></span>
                                    <?php } ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group row">
                                    <label for="Item_ChangedPages" class="col-sm-2 col-form-label">صفحات تغییر داده شده</label>
                                    <div class="col-sm-10">
                                    <?php if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || ($HasAddAccess && !isset($_REQUEST["UpdateID"]))) { ?>
                                        <textarea class="form-control name="Item_ChangedPages" id="Item_ChangedPages" cols="80" rows="5"><?php echo $ChangePages ?></textarea>
                                    <?php } else { ?>
                                    <span class="form-control id="Item_ChangedPages" name="Item_ChangedPages"><?php echo str_replace("\r", "<br>", $ChangePages); ?></span>
                                    <?php } ?>
                                    </div>
                                    </div>

                            </td>
                        </tr>
                         <tr >
                            <td class="text-center table-primary">
                            <?php if(($HasUpdateAccess && isset($_REQUEST["UpdateID"])) || (!isset($_REQUEST["UpdateID"]) && $HasAddAccess))
                                {
                            ?>
                            <input class="btn btn-success" type="submit" value="ذخیره">
                            <?php } ?>
                             <input class="btn btn-danger" type="button" onclick="javascript: window.close();" value="بستن">
                            </td>
                         </tr>
    </table>
<input type="hidden" name="Save" id="Save" value="1">
</form>
</div>
    <script>
	<? echo $LoadDataJavascriptCode; ?>
</script>