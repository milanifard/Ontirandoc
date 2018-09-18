<?php
include("header.inc.php");
include("classes/FormsFlowSteps.class.php");
include("classes/FormFields.class.php");
include("classes/FormsDetailTables.class.php");
include("classes/FormManagers.class.php");
include("classes/FormsStruct.class.php");
HTMLBegin();
$ParentObj = new be_FormsFlowSteps();
$ParentObj->LoadDataFromDatabase($_REQUEST["FormFlowStepID"]);

$ParentObj2 = new be_FormsStruct();
$ParentObj2->LoadDataFromDatabase($ParentObj->FormsStructID);

if($ParentObj2->CreatorUser!=$_SESSION["UserID"] && !$ParentObj2->HasThisPersonAccessToManageStruct($_SESSION["PersonID"]))
{
	echo "You don't have permission";
	die();
}

if(isset($_REQUEST["Save"]))
{
	$res = manage_FormFields::GetList($ParentObj->FormsStructID, "OrderInInputForm");
	echo "<p align=center><font color=green>اطلاعات ذخیره شد</font></p>";
	for($i=0; $i<count($res); $i++)
	{
		$FieldID = $res[$i]->FormFieldID;
		$AccessType = $_REQUEST["s_".$FieldID];
		manage_FormFields::SetFieldAccessType($FieldID, $_REQUEST["FormFlowStepID"], $AccessType);
	}
	$res = manage_FormsDetailTables::GetList($ParentObj->FormsStructID);
	for($i=0; $i<count($res); $i++)
	{
		$EditAccessType = $_REQUEST["EditAccessType_".$res[$i]->FormsDetailTableID];
		$AddAccessType = $_REQUEST["AddAccessType_".$res[$i]->FormsDetailTableID];
		$RemoveAccessType = $_REQUEST["RemoveAccessType_".$res[$i]->FormsDetailTableID];
		manage_FormsDetailTables::SetFieldAccessType($res[$i]->FormsDetailTableID, $_REQUEST["FormFlowStepID"], $EditAccessType, $AddAccessType, $RemoveAccessType);
		$res2 = manage_FormFields::GetList($res[$i]->DetailFormStructID, "OrderInInputForm");
		for($j=0; $j<count($res2); $j++)
		{
			$FieldID = $res2[$j]->FormFieldID;
			$AccessType = $_REQUEST["s_".$FieldID];
			$AccessType = manage_FormFields::SetFieldAccessType($FieldID, $_REQUEST["FormFlowStepID"], $AccessType);
		}
	}
}
$res = manage_FormFields::GetList($ParentObj->FormsStructID, "OrderInInputForm");
$LoadDataJavascriptCode = '';
?>
<form method=post id=f1 name=f1>
<br>
	<table border=1 cellspacing=0 width=60% align=center>
		<tr class=HeaderOfTable>
		<td align=center colspan=2>تعیین دسترسی به فیلدها در مرحله <b><?php echo $ParentObj->StepTitle ?></b>
		  &nbsp;&nbsp;<a href='CopyAccess.php?FormsStructID=<?php echo $ParentObj->FormsStructID ?>&FormFlowStepID=<?php echo $_REQUEST["FormFlowStepID"] ?>'>کپی دسترسی</a>
		</td>
		</tr>
		<input type=hidden name=FormFlowStepID id=FormFlowStepID value='<? echo $_REQUEST["FormFlowStepID"]; ?>'>
		<tr bgcolor=#ccccc>
			<td>نام فیلد</td>
			<td width=10% nowrap>نوع دسترسی</td>
		</tr>
		<?php 
		for($i=0; $i<count($res); $i++)
		{
			$AccessType = manage_FormFields::GetFieldAccessType($res[$i]->FormFieldID, $_REQUEST["FormFlowStepID"]);
			echo "<tr>";
			echo "<td>".$res[$i]->FieldTitle."</td>";
			echo "<td>";
			echo "<select name='s_".$res[$i]->FormFieldID."'>";
			echo "<option value=''> تعریف نشده ";
			echo "<option value='READ_ONLY' ";
			if($AccessType=="READ_ONLY")
				echo " selected ";
			echo ">فقط خواندنی";
			echo "<option value='EDITABLE' ";
			if($AccessType=="EDITABLE")
				echo " selected ";
			echo ">قابل ویرایش";
			echo "<option value='HIDE' ";
			if($AccessType=="HIDE")
				echo " selected ";
			echo ">عدم دسترسی";
			echo "</select>";
			echo "</td>";
			echo "</tr>";
		}
		?>
	</table>
	<br>
	<?php $res = manage_FormsDetailTables::GetList($ParentObj->FormsStructID); 
	for($i=0; $i<count($res); $i++)
	{
		$AccessRec = manage_FormsDetailTables::GetFieldAccessType($res[$i]->FormsDetailTableID, $_REQUEST["FormFlowStepID"]);
		$res2 = manage_FormFields::GetList($res[$i]->DetailFormStructID, "OrderInInputForm");
		?>
		<table border=1 cellspacing=0 width=60% align=center>
		<tr class=HeaderOfTable>
		<td align=center colspan=2>تعیین دسترسی به <b><?php echo $res[$i]->FormTitle ?></b>
		</td>
		</tr>
		<tr>
			<td colspan=2>
			<select name='EditAccessType_<?php echo $res[$i]->FormsDetailTableID ?>' style="width: 400px">
			<option value='ALL' <?php if($AccessRec["EditAccessType"]=="ALL") echo "selected"; ?> >کاربر داده های این جدول را می تواند ویرایش کند
			<option value='ONLY_USER' <?php if($AccessRec["EditAccessType"]=="ONLY_USER") echo "selected"; ?> >  کاربر تنها داده هایی که خودش ایجاد کرده می تواند ویرایش کند 			
			<option value='READ_ONLY' <?php if($AccessRec["EditAccessType"]=="READ_ONLY") echo "selected"; ?> >امکان ویرایش داده های این جدول وجود ندارد


			</option>
			</td>
		</tr>
		<tr>
			<td colspan=2>
			<select name='AddAccessType_<?php echo $res[$i]->FormsDetailTableID ?>' style="width: 400px">
			<option value='ACCESS'  >کاربر می تواند در این جدول داده جدید ثبت کند
			<option value='NOT_ACCESS' <?php if($AccessRec["AddAccessType"]=="NOT_ACCESS") echo "selected"; ?> > امکان اضافه کردن داده در این جدول وجود ندارد			
			</option>
			</td>
		</tr>
		<tr>
			<td colspan=2>
			<select name='RemoveAccessType_<?php echo $res[$i]->FormsDetailTableID ?>' style="width: 400px">
			<option value='ALL'>کاربر می تواند داده های جدول را حذف کند
			<option value='ONLY_USER' <?php if($AccessRec["RemoveAccessType"]=="ONLY_USER") echo "selected"; ?> >  کاربر تنها داده هایی که خودش ایجاد کرده می تواند حذف کند
			<option value='READ_ONLY' <?php if($AccessRec["RemoveAccessType"]=="READ_ONLY") echo "selected"; ?> >داده های این جدول قابل حذف نیست


			</option>
			</td>
		</tr>
		<tr bgcolor=#ccccc>
			<td>نام فیلد</td>
			<td width=10% nowrap>نوع دسترسی</td>
		</tr>
		<?php 
			for($j=0; $j<count($res2); $j++)
			{
				$AccessType = manage_FormFields::GetFieldAccessType($res2[$j]->FormFieldID, $_REQUEST["FormFlowStepID"]);
				echo "<tr>";
				echo "<td>".$res2[$j]->FieldTitle."</td>";
				echo "<td>";
				echo "<select name='s_".$res2[$j]->FormFieldID."'>";
				echo "<option value=''> تعریف نشده ";
				echo "<option value='EDITABLE' ";
				if($AccessType=="EDITABLE")
					echo " selected ";
				echo ">قابل ویرایش";
				echo "<option value='READ_ONLY' ";
				if($AccessType=="READ_ONLY")
					echo " selected ";
				echo ">فقط خواندنی";
				echo "<option value='HIDE' ";
				if($AccessType=="HIDE")
					echo " selected ";
				echo ">عدم دسترسی";
				echo "</select>";
				echo "</td>";
				echo "</tr>";
			}
		
		?>
		</table>	
		<br>
		<?php 
	}
	?>

	<table border=1 cellspacing=0 width=60% align=center>
		<tr class=HeaderOfTable>
		<td colspan=2 align=center>
		<input type=hidden name=Save id=Save value=1>
		<input type=submit value='&nbsp;ذخیره&nbsp;'>
		&nbsp;
		<input type=button onclick='javascript: document.location="ManageFormFlow.php?Item_FormStructID=<?php echo $ParentObj->FormsStructID ?>";' value='بازگشت'>
		</td>
		</tr>
	</table>
</form>
</html>
