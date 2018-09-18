<?php

error_reporting(0);
if (isset($_GET["Debug"]) && $_GET["Debug"] == '401371457')
{
	//ini_set('display_errors', 1);
	//ini_set('display_startup_errors', 1);
	//error_reporting(E_ALL);
	//die(phpInfo());
}

include 'sys_config.class.php';
include 'definitions.inc';
require config::$root_path.config::$framework_path.'User.class.php';
require config::$root_path.config::$ui_components_path.'HTMLUtil.class.php';
require config::$root_path.config::$framework_path.'FrameworkUtil.class.php';
require_once config::$root_path.config::$framework_path.'System.class.php';
require_once('session.inc.php');
include_once(config::$language.'_utf8.inc.php');
require_once 'FDate.class.php';
require_once("../sharedClasses/SharedClass.class.php");
require_once('../ProjectManagement/classes/ProjectTasks.class.php');
require_once('../ProjectManagement/classes/ProjectTaskAssignedUsers.class.php');
require_once('../ProjectManagement/classes/ProjectTasksSecurity.class.php');
require_once('../ProjectManagement/classes/ProjectTaskComments.class.php');

session_start();

$ItemsPerPage = 6;
$AnyChangeSeen = false;

if(!isset($_SESSION['User'])){
  FrameworkUtil::SessionExpired(); 
  return;
}
HTMLBegin();
echo "<script>document.title = 'گزارش خطا به مرکز کامپیوتر';</script>";

$mysql = pdodb::getInstance();

 //...................کنترل دسترسی به صفحه ......
      $res = $mysql->Execute(" SELECT   st.StuStatus
					FROM educ.StudentSpecs ss inner join  educ.StudyStatus st
								on ss.StatusID = st.StatusID

			       WHERE ss.StNo = '".$_SESSION["UserID"]."'");
      $rec = $res->fetch(); 


      if( isset($rec['StuStatus']) && $rec['StuStatus'] == 0) {

          echo " <center><font color='red'> دسترسی به این صفحه برای شما امکان پذیر نمی باشد.</font></center>";
          die();
      }

//.............................

if(isset($_REQUEST["Save"]) && $_REQUEST["Save"]=="1")
{
	$query = "insert into projectmanagement.SentBugs (CallBackNumber, UserID, SentTime, UserGroupID, PersonID, SysCode, PageAddress, BrowserInfo, PlatForm, IPAddress, description)  
				values (?, '".$_SESSION["UserID"]."', now(), '".$_SESSION["UserGroup"]."', '".$_SESSION["User"]->PersonID."', '".$_SESSION["SystemCode"]."', ?, ?, ?, '{$_SESSION['LIPAddress']}', ?)";
	$mysql->Prepare($query);
	$mysql->ExecuteStatement(array($_REQUEST["CallBackNumber"], $_REQUEST["PageName"], $_REQUEST["BrowserName"]." ".$_REQUEST["BrowserVer"], $_REQUEST["Platform"], $_REQUEST["description"]));
	$res = $mysql->Execute("select max(SentBugsID) as MaxID from projectmanagement.SentBugs where UserID='".$_SESSION["UserID"]."'");
	if($rec = $res->fetch())
	{
		$BugID = $rec["MaxID"];
		$pc = security_ProjectTasks::LoadUserPermissions($_SESSION["User"]->PersonID, 0);
		$TaskHeader = "گزارش خطا: ";
		$TaskHeader .= mb_strimwidth($_REQUEST["description"], 0, 90, "...");
		
		$ProjectTaskID = manage_ProjectTasks::Add(330, 0, 0, $TaskHeader, $_REQUEST["description"], "ONCE", 1, "", "", "", "", "", "NO", "", 1, "NOT_START", 0, 0, 0,'00:00','00:00', "NO", $pc);
		$mysql->Execute("update projectmanagement.SentBugs set ProjectTaskID='".$ProjectTaskID."' where SentBugsID='".$BugID."'");
		
		/*$mysql->Prepare("select * from pas.PersonCardReaders where PersonID = ? and CardReaderID = 15;");
		$Rslt = $mysql->ExecuteStatement([$_SESSION["User"]->PersonID]);
		
		if ($Rslt->rowCount() > 0)
			manage_ProjectTaskAssignedUsers::Add($ProjectTaskID, 348, "ارجاع گزارش خطا به مسئول آن در سازمان مرکزی", 100, "EXECUTOR");
		else
		{*/	$res = $mysql->Execute("select * from projectmanagement.BugAssignedPersons");
			while($rec = $res->fetch())
				manage_ProjectTaskAssignedUsers::Add($ProjectTaskID, $rec["PersonID"], "", 100, "EXECUTOR");
			
			if (isset($_GET["ProjectID"]) && $_GET["ProjectID"] == 669)
				manage_ProjectTaskAssignedUsers::Add($ProjectTaskID, 401369409, "", 100, "EXECUTOR");
		//}
	}
	echo "<script>window.close();</script>";
	die();
}
?>
<form method=post id=f1 name=f1>
	<input type=hidden name=PageNumber id=PageNumber value=1>
	<input type=hidden name=Save id=Save value=1>
	<input type=hidden name=PageName id=PageName value='<?php echo $_REQUEST["PageName"] ?>'>
	<input type=hidden name=BrowserName id=BrowserName value='<?php echo $_REQUEST["BrowserName"] ?>'>
	<input type=hidden name=BrowserVer id=BrowserVer value='<?php echo $_REQUEST["BrowserVer"] ?>'>
	<input type=hidden name=Platform id=Platform value='<?php echo $_REQUEST["Platform"] ?>'>
	<table width=80% align=center border=1 cellspacing=0>
	<tr>
		<td align=center class=HeaderOfTable>
		گزارش خطا
		</td>
	</tr>
	<tr>
		<td style="padding: 7px; line-height: 1.7;">
		- در صورتی که هنگام کار با پورتال خود به مشکلی برخورد کرده‌اید و فکر می‌کنید مربوط به برنامه  می‌باشد، برای گزارش کردن این خطا به مرکز کامپیوتر می‌توانید این فرم را تکمیل و ارسال نمایید.
		<br>
		- همراه با توضیحات شما اطلاعات مربوط به کاربر و مشخصات اتصال شما از جمله نوع مرورگر و سیستم‌عامل مورد استفاده نیز ارسال خواهد شد. تا با بررسی آنها خطای مربوطه رفع شود.
		<br>
		- لطفاً توضیحات مربوط به خطا را تا حد امکان کامل وارد نمایید تا سریع‌تر بتوان آن را رفع نمود.
		<br>
		- از طریق همین صفحه می‌توانید لیست گزارشات خطای ارسالی خود و وضعیت آنها را مشاهده نمایید.
		<br>
		<span style="color: red;">- برای گزارش مشكلات شبكه و اينترنت، لطفاً غير از تشريح مشكل و خطا، اطلاعاتی مانند شماره دانشجويی و نام كاربری مورد استفاده، مكان بروز خطا (در صورت استقرار در خوابگاه: محل و شماره اتاق خوابگاه) و زمان رخداد را حتماً ذكر فرمائيد.</span>
		</td>
	</tr>
	<tr>
		<td>
		<table width=100% border=0>
			<tr>
				<td>توضیحات:
				</td>
				<td>
					<textarea rows="5" cols="80" id=description name=description></textarea>
				</td>
			</tr>

			<tr>
				<td>شماره تماس:
				</td>
				<td>
					<input type=text id=CallBackNumber name=CallBackNumber value=''>
					در صورت نیاز با شما تماس گرفته خواهد شد
				</td>
			</tr>
			 
		</table>
		</td>
	</tr>
	<tr>
		<td align=center class=FooterOfTable>
		<input type=button onclick='javascript: if(document.getElementById("description").value!="") document.getElementById("f1").submit(); else alert("توضیحات را وارد نمایید");' value='ارسال'>
		&nbsp;
		<input type=button value='بستن' onclick='javascript: window.close();'>
		</td>
	</tr>
	</table>
	<br>
	<?php 
		$list = "";
		$From = 0;
		if(isset($_REQUEST["PageNumber"]))
		{
			$From = ($_REQUEST["PageNumber"]-1)*$ItemsPerPage;			
		}
		if(!is_numeric($From))
			$From = 0;
		$res = $mysql->Execute("select count(*) as TotalCount from projectmanagement.SentBugs JOIN projectmanagement.ProjectTasks using (ProjectTaskID) where UserID='".$_SESSION["UserID"]."'");
		$rec = $res->fetch(); 
		$TotalCount = $rec["TotalCount"];
		$res = $mysql->Execute("select *, concat(g2j(SentTime), ' ', substr(SentTime, 12,10)) as gSentTime from projectmanagement.SentBugs
											JOIN projectmanagement.ProjectTasks using (ProjectTaskID) 
											where UserID='".$_SESSION["UserID"]."' order by SentTime DESC limit ".$From.", ".$ItemsPerPage);
		$i = 0;
		
		// For Checking Status Changes ...
		$A = pdodb::getInstance();
		
		$query = "SELECT LastVisit FROM projectmanagement.UserPageLastVisits where UserID = :UserID and PageID = 2;";
		$A->Prepare($query);
		$Rslt = $A->ExecuteStatement(["UserID" => $_SESSION["UserID"]]);
		$LastVisit = $Rslt->fetch()["LastVisit"];
		// ...
		
		while($rec = $res->fetch())
		{
			$list .= "<tr>";
			$list .= "<td>".$rec["ProjectTaskID"]."</td>";
			$list .= "<td nowrap>".$rec["gSentTime"]."</td>";
			$list .= "<td>";
			$list .= "<pre>" . $rec["description"] . "</pre>";
			$CommentList = manage_ProjectTaskComments::GetList($rec["ProjectTaskID"], 0, 100);
			for($k=0; $k<count($CommentList); $k++)
			{
				$list .= "<b>".$CommentList[$k]->CreatorID_FullName.": (" . $CommentList[$k]->CreateTime_Shamsi . ")</b>";
				$list .= "<pre>" . $CommentList[$k]->CommentBody . "</pre>";
			}
			
			$list .= "</td>";
			$list .= "<td nowrap>";
			$AssignedList = manage_ProjectTaskAssignedUsers::GetList($rec["ProjectTaskID"], "PersonID", "");
			for($k=0; $k<count($AssignedList); $k++)
			{
				if($k>0)
					$list .= "<br>";
				$list .= $AssignedList[$k]->PersonID_FullName;
			}
			$list .= "</td>";
			$list .= "<td nowrap>";
			
			$Changed = false;
			$query = "select count(*) as Status from (
					(SELECT BeforeState FROM projectmanagement.ProjectTaskStatusChanges where DateTime > '$LastVisit' and TaskID = :TaskID order by DateTime asc limit 1)
					union
					(SELECT NewState FROM projectmanagement.ProjectTaskStatusChanges where DateTime > '$LastVisit' and TaskID = :TaskID order by DateTime desc limit 1)
					) as t";
			$A->Prepare($query);
			$Rslt = $A->ExecuteStatement(["TaskID" => $rec["ProjectTaskID"]]);
			if ($Rslt->fetch()["Status"] == 2)
			{
				$Changed = true;
				$AnyChangeSeen = true;
			}

			if ($Changed) $list .= "<span style='color: rgb(8, 177, 255);'>";
			if($rec["TaskStatus"]=="NOT_START")
				$list .= "اقدام نشده";
			else if($rec["TaskStatus"]=="PROGRESSING")
				$list .= "در دست اقدام";
			else if($rec["TaskStatus"]=="DONE")
				$list .= "اقدام شده";
			else if($rec["TaskStatus"]=="SUSPENDED")
				$list .= "معلق";
			else if($rec["TaskStatus"]=="REPLYED")
				$list .= "پاسخ داده شده";
			if ($Changed) $list .= "</span>";
			$list .= "</tr>";
			$i++;
		}
		if($list!="")
		{
			echo "<table width=90% align=center border=1 cellspacing=0 cellpadding=3>";
			echo "<tr class=HeaderOfTable>";
			echo "<td width=1% nowrap>کد گزارش</td><td width=10%>زمان ارسال</td><td>شرح</td><td width=10%>مجری</td><td width=10%>وضعیت</td>";
			echo "</tr>";
			echo $list;
			echo "<tr>";
			echo "<td colspan=5> صفحه: ";
			
			$CurrPageNumber = (isset($_POST['PageNumber']))? $_POST['PageNumber'] : 1;
			for($i=0; $i<($TotalCount/$ItemsPerPage); $i++)
				if ($i + 1 != $CurrPageNumber)
					echo "<a href='javascript: GoPage(".($i+1).")'>".($i+1)."</a> ";
				else
					echo ($i + 1) . " ";
			echo "</td>";
			echo "</tr>";
			echo "</table>";	
		}
		
	?>
</form>
<script>
	function GoPage(PageNum)
	{
		document.getElementById('Save').value=0;
		document.getElementById('PageNumber').value=PageNum;
		document.getElementById('f1').submit();
	}
	
	if (<?php echo ($AnyChangeSeen)? "true" : "false"; ?>)
	{
		try { window.opener.document.location.reload(); }
		catch(err){}
	}
</script>

<style type="text/css">

pre {
	white-space: -moz-pre-line; /* Mozilla, supported since 1999 */
	white-space: -pre-line; /* Opera */
	white-space: -o-pre-line; /* Opera */
	white-space: pre-line; /* CSS3 - Text module (Candidate Recommendation) http://www.w3.org/TR/css3-text/#white-space */
	word-wrap: break-word; /* IE 5.5+ */
	font-family: inherit;
	margin-top: 3px;
	margin-bottom: 15px;
}

</style>

</body>
</html>

<?php

// Set page's last visit time for the user ...
$A = pdodb::getInstance();
$query = "insert into UserPageLastVisits (UserID, PageID, LastVisit) values (?, 2, now()) on duplicate key update LastVisit = now();";
$A->Prepare($query);
$Rslt = $A->ExecuteStatement([$_SESSION["UserID"]]);

?>