<?php
include("../shares/header.inc.php");

include_once("classes/FormsStruct.class.php");
include_once("classes/FormFields.class.php");
include_once("classes/FormsFlowSteps.class.php");
include_once("classes/FieldsItemList.class.php");
require_once('classes/FormsFlowStepRelations.class.php');
include_once("classes/FormsDetailTables.class.php");
include_once("classes/SecurityManager.class.php");
//require_once('../organization/classes/ChartServices.class.php');
HTMLBegin();
$mysql = pdodb::getInstance();
$_REQUEST = SecurityManager::validateInput($_REQUEST);
if(!isset($_REQUEST["SelectedFormStructID"]))
{
	echo "SelectedFormStructID=?";
	die();
}
?>
<SCRIPT LANGUAGE="JavaScript1.1" SRC="FormCheck.js"></SCRIPT>

<br>
<p align=center><span id=MessageSpan name=MessageSpan></span></p>
<br>
<?php
	$CurForm = new be_FormsStruct();
	$CurForm->LoadDataFromDatabase($_REQUEST["SelectedFormStructID"]);

	$query = "select * from formsgenerator.TemporaryUsersAccessForms where WebUserID='".$_SESSION["UserID"]."' and FormsStructID=?";
	$mysql->Prepare($query);
	$res = $mysql->ExecuteStatement([$_REQUEST["SelectedFormStructID"]]);
	if($rec = $res->fetch())
	{
		if($rec["filled"]=="YES") // در حالتیکه پرسشنامه پر و تایید شده باشد
		{
			echo C_QUESTIONNAIRE_ALREADY_FILLED;
			die();
		}
		else
		{
			$res = $mysql->Execute("select * from formsgenerator.QuestionnairesCreators where UserID='".$_SESSION["UserID"]."' and FormsStructID='".$CurForm->FormsStructID."'");
			if($rec = $res->fetch()) 			// اگر از قبل پرسشنامه تکمیل نشده داشته باشد کاربر را به صفحه ویرایش پرسشنامه می فرستد
			{
				$RecID = $rec["RelatedRecordID"]; 
			?>
			<form id="f2" name="f2" method="post" action='ViewQuestionnaire.php'>
				<input type="hidden" nme='FormFlowStepID' id='FormFlowStepID' value=0>
				<input type="hidden" name='RelatedRecordID' id='RelatedRecordID' value=0>
				<input type="hidden" name='SelectedFormStructID' id='SelectedFormStructID' value=0>
			</form>
			<script>
				function ViewForm(FormsStructID, FormFlowStepID, RelatedRecordID){
					document.f2.FormFlowStepID.value=FormFlowStepID;
					document.f2.RelatedRecordID.value=RelatedRecordID;
					document.f2.SelectedFormStructID.value=FormsStructID;
					f2.submit();
				}
				ViewForm(<?php echo $CurForm->FormsStructID ?>, -1, <?php echo $RecID ?>);
			</script>
			<?php
			} 
		}
	}
	else
	{
		echo C_NO_PERMISSION_TO_FILL_QUES;
		die();
	}
	
	if(isset($_REQUEST["ActionType"])){
		switch($_REQUEST["ActionType"]){
			case "CONFIRM":{
				// وقتی کاربر تایید نهایی کرده باشد فرم ارسال شده و دفعه بعدی که کاربر وارد شود نمی تواند آن را تغییر دهد
				$mysql->Prepare("update formsgenerator.TemporaryUsersAccessForms set filled='YES' where WebUserID='".$_SESSION["UserID"]."' and FormsStructID=?");
				$mysql->ExecuteStatement([$_REQUEST["SelectedFormStructID"]]);
				
				$RecID = $CurForm->AddData(-1, $_SESSION["PersonID"]);

				$mysql->Prepare("insert into formsgenerator.QuestionnairesCreators (UserID, FormsStructID, RelatedRecordID) values('".$_SESSION["UserID"]."', ?, '".$RecID."')");
				$mysql->ExecuteStatement([$_REQUEST["SelectedFormStructID"]]);
				
				echo "<script>document.getElementById('MessageSpan').innerHTML='<font color='green'>".C_INFORMATION_SAVED."</font>';</script>";
				//echo "<script>setTimeout(\"document.getElementById('MessageSpan').innerHTML='';\", 1500);</script>";
				echo "<p align='center'><a href='login.php'>خروج از سیستم</a></p>";
				die();
			break;
			}
			case "SAVE_GO_EDIT":
			case "SEND":{
				$RecID = $CurForm->AddData(-1, $_SESSION["PersonID"]);
				$CurForm->SendData($RecID, 0, -1, $_SESSION["PersonID"]);
				$mysql->Prepare("insert into formsgenerator.QuestionnairesCreators (UserID, FormsStructID, RelatedRecordID) values('".$_SESSION["UserID"]."', ?, '".$RecID."')");
				$mysql->ExecuteStatement([$_REQUEST["SelectedFormStructID"]]);
				
				?>
				<form id="f2" name="f2" method="post" action='ViewQuestionnaire.php'>
					<input type="hidden" name='FormFlowStepID' id='FormFlowStepID' value=0>
					<input type="hidden" name='RelatedRecordID' id='RelatedRecordID' value=0>
					<input type="hidden" name='SelectedFormStructID' id='SelectedFormStructID' value=0>
				</form>
				<script>
					function ViewForm(FormsStructID, FormFlowStepID, RelatedRecordID)
					{
						document.f2.FormFlowStepID.value=FormFlowStepID;
						document.f2.RelatedRecordID.value=RelatedRecordID;
						document.f2.SelectedFormStructID.value=FormsStructID;
						f2.submit();
					}
					ViewForm(<?php echo $CurForm->FormsStructID ?>, -1, <?php echo $RecID ?>);
				</script>
				<?php
			break;
			}
		}
	}
echo $CurForm->CreateUserInterface(-1, $_SESSION["PersonID"], 0);
	echo "<p align=center><input type=button value='".C_LOGOUT."' onclick='javascript: document.location=\"login.php?logout=1\"'></p>";
?>
</html>
