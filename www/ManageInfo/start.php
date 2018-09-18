<?php
  include('header.inc.php');
  $mysql=dbclass::getInstance();
  $cal =FrameworkUtil::getCalendarInfo();
  $_SESSION['ActiveEduYear']=$cal['CurYear'];
  $_SESSION['ActiveEduSemester']=$cal['CurSemester'];
  $mysql->audit("ورود کاربر");
  $query=" select PersonID from framework.AccountSpecs where WebUserID =  '".$_SESSION['UserID']."' ";
 
  $res = $mysql->Execute($query);
  $rec = $res->FetchRow();	
  $_SESSION['PersonID'] = $rec["PersonID"];		

  ServerTransfer('../../gateway/MainFrame.php');	
?>
