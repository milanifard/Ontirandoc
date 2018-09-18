<?php
include 'header.inc.php';
HTMLBegin();
$mysql = pdodb::getInstance();
$mysql->Prepare("select *,concat((SentTime), ' ', substr(SentTime, 12,10)) as gSentTime
,if(p1.plname!='',concat(p1.pfname,' ',p1.plname),concat(p2.PFName,' ',p2.PLName)) as pname,p1.person_type
 from projectmanagement.SentBugs

LEFT JOIN hrmstotal.persons p1 on (SentBugs.PersonID=p1.PersonID)

LEFT JOIN educ.persons p2 on (SentBugs.PersonID=p2.PersonID)
                                
				where SentBugsID=?");
$res = $mysql->ExecuteStatement(array($_REQUEST["BugID"]));
$rec = $res->fetch();
$SenderUserID = $rec["PersonID"];
$res = $mysql->ExecuteStatement(array($_REQUEST["BugID"]));
$rec = $res->fetch();
$SenderUserID = $rec["UserID"];
$FullName = $rec["pname"];
$person_type = $rec["person_type"];
$SenderPersonID = $rec["PersonID"];
$BugSysCode = $rec["SysCode"];
$Platform = $rec["PlatForm"];
$Browser = $rec["BrowserInfo"];
$IPAddress = $rec["IPAddress"];
$SentTime = $rec["gSentTime"];
$PageAddress = $rec["PageAddress"];
$CallBackNumber = $rec["CallBackNumber"];

?>
<form method=post>
	<input type=hidden name=Save id=Save>
	<input type=hidden name=PageName id=PageName value='<?php echo $_REQUEST["PageName"] ?>'>
	<input type=hidden name=BrowserName id=BrowserName value='<?php echo $_REQUEST["BrowserName"] ?>'>
	<input type=hidden name=BrowserVer id=BrowserVer value='<?php echo $_REQUEST["BrowserVer"] ?>'>
	<input type=hidden name=Platform id=Platform value='<?php echo $_REQUEST["Platform"] ?>'>
	<table width=80% align=center border=1 cellspacing=0>
	<tr>
		<td align=center class=HeaderOfTable>
		مشخصات نحوه اتصال و فرد ارسال کننده خطا
		</td>
	</tr>
	<tr>
		<td>
		<table width=100% border=0>
			<tr>
				<td colspan=2 bgcolor=#cccccc>مشخصات فردی</td>
			</tr>
			<tr>
				<td width=10% nowrap>نام و نام خانوادگی:</td><td><?php echo $FullName ?></td>
			</tr>
			<tr>
				<td width=10% nowrap>کد شخصی:</td><td><?php echo $SenderPersonID ?></td>
			</tr>
			<tr>
				<td width=10% nowrap>کد نوع شخص:</td><td><?php echo $person_type ?></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<br>
		<table width=100% border=1 cellspacing=0 cellpadding=3>
		<tr>
			<td bgcolor=#cccccc colspan=9>
			مشخصات پرسنلی
			</td>
		</tr>
		<tr class=HeaderOfTable>
			<td>staff_id</td>
			<td>نوع شخص</td>
			<td>کد آخرین حکم</td>
			<td>کد نسخه آخرین حکم</td>
			<td>واحد سازمانی</td>
			<td>دانشکده</td>
			<td>گروه آموزشی</td>
			<td>ProCode</td>
			<td>نوع استخدامی قبل بازنشستگی</td>
		</tr>
		<?php 
			$res = $mysql->Execute("select * from hrmstotal.staff
									LEFT JOIN hrmstotal.org_new_units on (staff.UnitCode=org_new_units.ouid)
									LEFT JOIN projectmanagement.faculties using (FacCode)
									LEFT JOIN projectmanagement.EducationalGroups using (EduGrpCode) 
									where PersonID='".$SenderPersonID."'");
			while($rec = $res->fetch())
			{
				echo "<tr>";
				echo "<td>".$rec["staff_id"]."</td>";
				echo "<td>".$rec["person_type"]."</td>";
				echo "<td>".$rec["last_writ_id"]."</td>";
				echo "<td>".$rec["last_writ_ver"]."</td>";
				echo "<td>&nbsp;".$rec["ptitle"]."</td>";
				echo "<td>&nbsp;".$rec["PFacName"]."</td>";
				echo "<td>&nbsp;".$rec["PEduName"]."</td>";
				echo "<td>&nbsp;".$rec["ProCode"]."</td>";
				echo "<td>".$rec["last_person_type"]."</td>";
				echo "</tr>";
			}
		?>
		</table>
		<br>
		</td>
	</tr>
	<?php 
		$query = "select * from hrmstotal.persons JOIN hrmstotal.staff using (PersonID, person_type)
									where PersonID='".$SenderPersonID."' 
									";
		$res = $mysql->Execute($query);
		$rec = $res->fetch();
		
		$query = "select * from hrmstotal.writs
									LEFT JOIN hrmstotal.org_new_units using (ouid)
									LEFT JOIN hrmstotal.position using (post_id)
									where writ_id='".$rec["last_writ_id"]."' 
									and writ_ver='".$rec["last_writ_ver"]."'
									and writs.staff_id='".$rec["staff_id"]."'
									";
		$res = $mysql->Execute($query);
		if($rec = $res->fetch())
		{
	?>
	<tr>
		<td bgcolor=#cccccc>مشخصات موجود در آخرین حکم منتسب شده</td>
	</tr>
	<tr>
		<td>
		<table width=100% border=0>
			<tr>
				<td width=10% nowrap>واحد سازمانی:</td><td><?php echo $rec["ptitle"] ?></td>
			</tr>
			<tr>
				<td width=10% nowrap>پست سازمانی:</td><td><?php echo $rec["title"] ?></td>
			</tr>
			<tr>
				<td width=10% nowrap>emp_mode:</td><td><?php echo $rec["emp_mode"] ?></td>
			</tr>
			<tr>
				<td width=10% nowrap>emp_state:</td><td><?php echo $rec["emp_state"] ?></td>
			</tr>
		</table>
		</td>
	</tr>
	<?php } else { ?>
	<tr>
		<td>
		<table width=100% border=0>
		<tr>
			<td colspan=2 bgcolor=#cccccc>آخرین حکم در سیستم موجود نمی باشد</td>
		</tr>
		</table>
		</td>
	</tr>
	<?php } 
		$query = "select * from projectmanagement.UsersSystems
								LEFT JOIN projectmanagement.systems using (SysCode) 
								LEFT JOIN projectmanagement.domains on (domains.DomainValue=UserRole and domains.DomainName='USER_ROLE')
								where UserID='".$SenderUserID."' and UsersSystems.SysCode='1'"; 
		$res = $mysql->Execute($query);
		$rec = $res->fetch();
	?>
	<tr>
		<td bgcolor=#cccccc>
		مشخصات کاربری 
		</td>
	</tr>
	<tr>
		<td>
		<table width=100% border=0>
			<tr>
				<td width=10% nowrap>نام کاربری:</td><td><?php echo $SenderUserID ?></td>
			</tr>
			<tr>
				<td width=10% nowrap>کد نقش در آموزش:</td><td>&nbsp;<?php echo $rec["description"] ?></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td bgcolor=#cccccc>
		مشخصات اتصال 
		</td>
	</tr>
	<tr>
		<td>
		<table width=100% border=0>
			<tr>
				<td width=10% nowrap>Platform:</td><td><?php echo $Platform ?></td>
			</tr>
			<tr>
				<td width=10% nowrap>Browser:</td><td><?php echo $Browser ?></td>
			</tr>
			<tr>
				<td width=10% nowrap>IPAddress:</td><td><?php echo long2ip($IPAddress) ?></td>
			</tr>
			<tr>
				<td width=10% nowrap>زمان:</td><td><?php echo $SentTime ?></td>
			</tr>
			<tr>
				<td>صفحه: </td><td><?php echo $PageAddress ?></td>
			</tr>
			<tr>
				<td>شماره تماس: </td><td><?php echo $CallBackNumber ?></td>
			</tr>
		</table>
		</td>
	</tr>
	
	<tr>
		<td align=center class=FooterOfTable>
		<input type=button value='بستن' onclick='javascript: window.close();'>
		</td>
	</tr>
	</table>
</form>

<br>
<br>

<form>
    <table width=70% border=1 align=center>
        <tr class=HeaderOfTable>
<td>کد کار</td>
            <td>عنوان</td>  
            <td>توضیحات</td>
        </tr>
		<?php 
			$query2="SELECT 
                                p.ProjectID,
                                s.SentBugsID,
                                p.ProjectTaskID,
                                p.CreatorID,
                                s.PersonID,
                                p.title,
                                p.description
                            FROM
                                projectmanagement.ProjectTasks p
                                    right join
                                projectmanagement.SentBugs s ON (p.CreatorID = s.PersonID)
                            where
                                p.CreatorID = '".$SenderPersonID."'
                            group by p.ProjectTaskID";
                            $result = $mysql->Execute($query2);

			while($recc = $result->fetch())
			{
				echo "<tr>";
				echo "<td><a href=\"NewProjectTasks.php?UpdateID=" . $recc["ProjectTaskID"] . "\" target=_blank>" . $recc["ProjectTaskID"] . "</a></td>";
				echo "<td>".$recc["title"]."</td>";
				echo "<td>".$recc["description"]."</td>";				
				echo "</tr>";
			}
		?>
    </table>
    
</form>

</body>
</html>
