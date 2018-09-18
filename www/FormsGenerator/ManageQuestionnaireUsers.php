<?php
include("header.inc.php");
include("classes/FormsStruct.class.php");
include("classes/FormManagers.class.php");

$ParentObj = new be_FormsStruct();
$ParentObj->LoadDataFromDatabase($_REQUEST["Item_FormStructID"]);
if($ParentObj->CreatorUser!=$_SESSION["UserID"] && !$ParentObj->HasThisPersonAccessToManageStruct($_SESSION["PersonID"]))
{
	echo "You don't have permission";
	die();
}
$mysql = pdodb::getInstance();
if(isset($_REQUEST["DownloadUser"]))
{
    header('Content-Type: text/plain;charset=utf-8');
  	header('Content-disposition: attachment; filename=report.xls');
	$res = $mysql->Execute("select TemporaryUsers.*, TemporaryUsersAccessForms.*, QuestionnairesCreators.RelatedRecordID from formsgenerator.TemporaryUsers 
								JOIN formsgenerator.TemporaryUsersAccessForms using (WebUserID) 
								LEFT JOIN formsgenerator.QuestionnairesCreators on (QuestionnairesCreators.UserID=TemporaryUsers.WebUserID)
								where TemporaryUsersAccessForms.FormsStructID='".$_REQUEST["Item_FormStructID"]."'
								");
	echo "<table>";
	while($rec = $res->fetch())
	{
		echo "<tr>";
		echo "<td>".$rec["WebUserID"]."</td>";
		echo "<td>".$rec["WebPassword"]."</td>";
		if($rec["RelatedRecordID"]!="")
		{
			if($rec["filled"]=="YES")
				echo "<td>CONFIRM</td>";
			else
				echo "<td>DRAFT</td>";
		}
		else
			echo "<td>EMPTY</td>";
		echo "<td>".$rec["FilledDate"]."</td>";
		echo "</tr>";		
	}
	echo "</table>";
	die();
}
HTMLBegin();
if(isset($_REQUEST["UsersCount"]))
{
	for($i=0; $i<$_REQUEST["UsersCount"]; $i++)
	{
		$WebUserID = "u".$_REQUEST["Item_FormStructID"]."_".$i.rand(100000, 900000);
		$password = "p".rand(1000000, 2000000);
		$mysql->Execute("insert into formsgenerator.TemporaryUsers (WebUserID, WebPassword, UserStatus) values ('".$WebUserID."', '".$password."', 'ENABLE')");
		$mysql->Execute("insert into formsgenerator.TemporaryUsersAccessForms (WebUserID, FormsStructID, filled) values ('".$WebUserID."', '".$_REQUEST["Item_FormStructID"]."', 'NO')");
	}
	//$mysql->Execute("")
}
$CurrentTempUsersCount = 0;
$res = $mysql->Execute("select count(*) as TotalCount from formsgenerator.TemporaryUsers JOIN formsgenerator.TemporaryUsersAccessForms using (WebUserID) where FormsStructID='".$_REQUEST["Item_FormStructID"]."'");
$rec = $res->fetch();
$CurrentTempUsersCount = $rec["TotalCount"];
if($CurrentTempUsersCount=="")
	$CurrentTempUsersCount = 0;
?>
<form method=post id=f1 name=f1>
<br><table width=80% border=1 cellspacing=0 align=center>
<tr class=HeaderOfTable><td align=center>کاربران پرسشنامه  <b><?php echo $ParentObj->FormTitle ?></b></td></tr>
<tr>
	<td>
	ایجاد <input type=text name=UsersCount id=UsersCount size=3 value='10'> نام کاربری و کلمه عبور برای ثبت اطلاعات در پرسشنامه <input type=submit value='اعمال'>
	</td>
</tr>
<tr>
	<td>تعداد کاربران تولید شده برای این پرسشنامه در حال حاضر:
	<b><?php echo $CurrentTempUsersCount ?></b>
	&nbsp; &nbsp;
	<a target=_blank href='ManageQuestionnaireUsers.php?Item_FormStructID=<?php echo $_REQUEST["Item_FormStructID"] ?>&DownloadUser=1'>دریافت فایل کاربران</a>
	</td>
</tr>
<tr><td>
<table width=100% border=0>
	<input type=hidden name=Item_FormStructID id=Item_FormStructID value='<? echo $_REQUEST["Item_FormStructID"]; ?>'>
</table></td></tr><tr class=FooterOfTable><td align=center>
<input type=button onclick='javascript: document.location="ManageQuestionnaires.php";' value='بازگشت'>
</td></tr>
<tr>
  <td>
  کاربران برای تکمیل پرسشنامه باید وارد مسیر 
  http://_YOURHOSTNAME_/ProjectManagement/FormsGenerator/login.php
  شوند
  </td>
</tr>
</table>

<input type=hidden name=Save id=Save value=1>
</form>

</html>