<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : پروژه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-15
*/


////THIS FILE IS TAKEN BY Mohammad_Afsharian_Shandiz PLEASE BE CAREFUL  !
///
///
///
///
///
///
///
///
///
///
include("../shares/header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/projects.class.php");
include("classes/projectsSecurity.class.php");
include("classes/ProjectGroups.class.php");
include("classes/ProjectMembers.class.php");
include("classes/ProjectResponsibles.class.php");

$mysql = pdodb::getInstance();

HTMLBegin();
//echo "*".$_SESSION["PersonID"]."*";
$NumberOfRec = 30;
$k=0;
$PageNumber = 0;
if(isset($_REQUEST["PageNumber"]))
{
    $FromRec = $_REQUEST["PageNumber"]*$NumberOfRec;
    $PageNumber = $_REQUEST["PageNumber"];
}
else
{
    $FromRec = 0;
}
$ProjectGroupOptions = "";
$res = manage_ProjectGroups::Search(0, "", "h1_ptitle, ProjectGroupName", "");
for($k=0; $k<count($res); $k++)
{
    $ProjectGroupOptions .= "<option value='".$res[$k]->ProjectGroupID."'>".$res[$k]->RelatedUnitID_Desc." - ".$res[$k]->ProjectGroupName;
}

if(isset($_REQUEST["SearchAction"]))
{
    $OrderByFieldName = "ProjectID";
    $OrderType = "";
    if(isset($_REQUEST["OrderByFieldName"]))
    {
        $OrderByFieldName = $_REQUEST["OrderByFieldName"];
        $OrderType = $_REQUEST["OrderType"];
    }
    $title=htmlentities($_REQUEST["Item_title"], ENT_QUOTES, 'UTF-8');
    $SysCode=htmlentities($_REQUEST["Item_SysCode"], ENT_QUOTES, 'UTF-8');
    $ProjectStatus=htmlentities($_REQUEST["Item_ProjectStatus"], ENT_QUOTES, 'UTF-8');
    $ouid = '0';
    $ProjectGroupID = htmlentities($_REQUEST["Item_ProjectGroupID"], ENT_QUOTES, 'UTF-8');
    $MemberPersonID = htmlentities($_REQUEST["Item_PersonID"], ENT_QUOTES, 'UTF-8');
}
else
{
    $OrderByFieldName = "ProjectID";
    $OrderType = "";
    $title='';
    $SysCode='';
    $ProjectStatus='';
    $ouid = '0';
    $ProjectGroupID = '0';
    $MemberPersonID = $_SESSION["PersonID"];
}
$res = manage_projects::Search($MemberPersonID, $ouid, $ProjectGroupID, $title, $SysCode, $ProjectStatus, "", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType);
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
    if(isset($_REQUEST["ch_".$res[$k]->ProjectID]))
    {
        manage_projects::Remove($res[$k]->ProjectID);
        $SomeItemsRemoved = true;
    }
}
if($SomeItemsRemoved)
    $res = manage_projects::Search($MemberPersonID, $ouid, $ProjectGroupID, $title, $SysCode, $ProjectStatus, "", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType);
?>

<form class="form-group" id="f1" name="f1" method=post>
    <input class="form-control " type="hidden" name="PageNumber" id="PageNumber" value="0">
    <input class="form-control" type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
    <input class="form-control" type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
    <input class="form-control" type="hidden" name="SearchAction" id="SearchAction" value="1">
    <br><table class="table table-striped mw-100 align-content-center border-1 bg-secondary"  cellspacing="0">
        <tr class="HeaderOfTable">
            <td><i class="fa fa-search"></i><b><a class="form-control table-hover " href="#" onclick='javascript: if(document.getElementById("SearchTr").style.display=="none") document.getElementById("SearchTr").style.display=""; else document.getElementById("SearchTr").style.display="none";'><? echo C_SEARCH; ?></a></td>
        </tr>
        <tr class="form-control " id='SearchTr' style='display: none'>
            <td>
                <table class="w-100 table-borderless table-secondary fa-table-tennis table-hover align-content-center"   cellspacing="0">
                    <tr>
                        <td  width="1%" nowrap>
                            <? echo C_TITLE; ?>
                        </td>
                        <td nowrap>
                            <input  class="form-control" type="text" name="Item_title" id="Item_title" maxlength="500" size="40">
                        </td>
                    </tr>

                    <tr>
                        <td width="1%" nowrap>
                            <? echo C_PROJECT_GROUP; ?>
                        </td>
                        <td nowrap>
                            <select  name="Item_ProjectGroupID" id="Item_ProjectGroupID">
                                <option value=0>-
                                    <?php echo $ProjectGroupOptions ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td width="1%" nowrap>
                            <? echo C_RELATED_SYSTEM; ?>
                        </td>
                        <td nowrap>
                            <select name="Item_SysCode" id="Item_SysCode">
                                <option value=0>-
                                    <? echo SharedClass::CreateARelatedTableSelectOptions("projectmanagement.systems", "SysCode", "description", "description"); ?>	</select>
                        </td>
                    </tr>
                    <tr>
                        <td width="1%" nowrap>
                            <? echo C_CONDITION; ?>
                        </td>
                        <td nowrap>
                            <select class="mdb-select md-form md-outline colorful-select dropdown-primary "  name="Item_ProjectStatus" id="Item_ProjectStatus" >
                                <option value="disabled selected" ><?php echo C_CHOSE_YOUR_CONDITIONS ?></option>
                                <option value='NOT_STARTED'><?php echo C_NOT_STARTED ?></option>
                                <option value='DEVELOPING'><?php echo C_ONGOING ?></option>
                                <option value='MAINTENANCE'><?php echo C_SUPPORTED ?></option>
                                <option value='FINISHED'><?php echo C_FINISHED ?></option>
                                <option value='SUSPENDED'><?php echo C_SUSPENDED ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td width="1%" nowrap>
                            <?php echo C_PROJECT_MEMBER ?>
                        </td>
                        <td>
                            <input class="form-control" type=hidden name="Item_PersonID" id="Item_PersonID" value="<?php echo $_SESSION["PersonID"] ?>" >
                            <span id="Span_PersonID_FullName" name="Span_PersonID_FullName"><?php echo C_BY_MY_OWN ?></span>
                            <a href='#' onclick='javascript: window.open("SelectStaff.php?InputName=Item_PersonID&SpanName=Span_PersonID_FullName&ProjectID=0");'> <?php echo C_CHOISE ?> </a>
                        </td>
                    </tr>
                    <tr class="HeaderOfTable">
                        <td colspan="2" align="center"><input class="form-control" type="submit" value="<? echo C_SEARCH; ?>"></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>
<?
if(isset($_REQUEST["SearchAction"]))
{
    ?>
    <script>
        document.f1.Item_title.value='<? echo htmlentities($_REQUEST["Item_title"], ENT_QUOTES, 'UTF-8'); ?>';
        document.f1.Item_SysCode.value='<? echo htmlentities($_REQUEST["Item_SysCode"], ENT_QUOTES, 'UTF-8'); ?>';
        document.f1.Item_ProjectGroupID.value='<? echo htmlentities($_REQUEST["Item_ProjectGroupID"], ENT_QUOTES, 'UTF-8'); ?>';
        document.f1.Item_ProjectStatus.value='<? echo htmlentities($_REQUEST["Item_ProjectStatus"], ENT_QUOTES, 'UTF-8'); ?>';
        document.f1.Item_PersonID.value='<? echo htmlentities($_REQUEST["Item_PersonID"], ENT_QUOTES, 'UTF-8'); ?>';
        <?php
        $mysql = pdodb::getInstance();
        $mysql->Prepare("select * from hrmstotal.persons where PersonID=?");
        $res2 = $mysql->ExecuteStatement(array($_REQUEST["Item_PersonID"]));
        if($rec = $res2->fetch())
        {
        ?>
        document.getElementById('Span_PersonID_FullName').innerHTML='<? echo $rec["pfname"]." ".$rec["plname"]; ?>';
        <?php
        }
        else
        {
        ?>
        document.getElementById('Span_PersonID_FullName').innerHTML='-';
        <?php
        }
        ?>
    </script>
    <?
}
?>
<form id="ListForm" name="ListForm" method="post">
    <? if(isset($_REQUEST["PageNumber"]))
        echo "<input type=\"hidden\" name=\"PageNumber\" value=".$_REQUEST["PageNumber"].">"; ?>
    <br>
    <table class="col-12 w-100 table-bordered table-secondary fa-table-tennis table-hover align-content-center"   cellspacing="0">

        <tr class='col-12 bg-dark text-light text-center' >
            <td colspan="9">
                <? echo C_PROJECT; ?>
            </td>
        </tr>
        <tr class="  HeaderOfTable">

            <td class='col-1 border-dark bg-info text-white text-center' width="1%"><? echo C_ROW; ?></td>
            <td class='col-1 border-dark bg-success text-dark text-center' width="2%"><? echo C_EDIT; ?></td>
            <td class='col-1 border-dark bg-info text-white text-center'><a class="text-white" href="javascript: Sort('title', 'ASC');"><? echo C_TITLE; ?></a></td>
            <td class='col-1 border-dark bg-success text-dark text-center' width=1% nowrap><a class="text-dark" href="javascript: Sort('ProjectGroupID', 'ASC');"> <? echo C_PROJECT_GROUP; ?></a></td>
            <td class='col-1 border-dark bg-info text-white white-center' width=1% nowrap><a class="text-white" href="javascript: Sort('ProjectPriority', 'ASC');"><? echo C_PRIORITY; ?></a></td>
            <td class='col-1 border-dark bg-success  text-dark text-center' width=1% nowrap><a class="text-dark" href="javascript: Sort('ProjectStatus', 'ASC');"> <? echo C_CONDITION; ?></a></td>
            <td class='col-1 border-dark bg-info text-white text-center' width=1% nowrap>
                <? echo C_PARTS; ?>

            </td>
        </tr>

        <?
        for($k=0; $k<count($res); $k++)
        {
            if($k%2==0)
                echo "<tr class=\"OddRow\">";
            else
                echo "<tr class=\"EvenRow\">";
            echo "<td>";
            echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->ProjectID."\">";
            echo "</td>";
            echo "<td>".($k+$FromRec+1)."</td>";
            echo "	<td>";
            echo "<a target=\"_blank\" href=\"Newprojects.php?UpdateID=".$res[$k]->ProjectID."\">";
            echo "<img src='images/edit.gif' title='ویرایش'>";
            echo "</a></td>";
            echo "	<td>".htmlentities($res[$k]->title, ENT_QUOTES, 'UTF-8')."</td>";
            echo "	<td nowrap>&nbsp;".$res[$k]->ProjectGroupID_Desc."</td>";
            echo "	<td nowrap>".$res[$k]->ProjectPriority_Desc."</td>";
            echo "	<td nowrap>".$res[$k]->ProjectStatus_Desc."</td>";
            /*
            echo "<td nowrap>";
            echo "<a target=\"_blank\" href='ManageProjectMembers.php?ProjectID=".$res[$k]->ProjectID ."'>";
            echo "<img src='images/members.gif' border='0' title='اعضای پروژه'>";
            echo "</a>  ";
            echo "<a target=\"_blank\" href='ManageProjectResponsibles.php?ProjectID=".$res[$k]->ProjectID ."'>";
            echo "<img src='images/operator.gif' border='0' title='پاسخگویان درخواستها'>";
            echo "</a>  ";
            echo "<a target=\"_blank\" href='ManageProjectMilestones.php?ProjectID=".$res[$k]->ProjectID ."'>";
            echo "<img src='images/calendar.gif' border='0' title='تاریخهای مهم'>";
            echo "</a>  ";
            echo "<a target=\"_blank\" href='ManageProjectDocuments.php?ProjectID=".$res[$k]->ProjectID ."'>";
            echo "<img src='images/document.gif' border='0' title='مستندات'>";
            echo "</a>  ";

            echo "<a target=\"_blank\" href='ManageProjectDocumentTypes.php?ProjectID=".$res[$k]->ProjectID ."'>";
            echo "<img src='images/category.gif' border='0' title='انواع سند پروژه ها'>";
            echo "</a>  ";
            echo "<a target=\"_blank\" href='ManageProjectTaskActivityTypes.php?ProjectID=".$res[$k]->ProjectID ."'>";
            echo "<img src='images/category2.gif' border='0' title='انواع اقدامات'>";
            echo "</a>  ";
            echo "<a target=\"_blank\" href='ManageProjectTaskTypes.php?ProjectID=".$res[$k]->ProjectID ."'>";
            echo "<img src='images/jobs.gif' border='0' title='انواع کارها'>";
            echo "</a>  ";
            echo "<a target=\"_blank\" href='ManageProjectHistory.php?ProjectID=".$res[$k]->ProjectID ."'>";
            echo "<img src='images/history.gif' border='0' title='تاریخچه'>";
            echo "</a>  ";
            echo "<a target=\"_blank\" href='ShowProjectActivities.php?ProjectID=".$res[$k]->ProjectID ."'>";
            echo "<img src='images/history.gif' border='0' title='فعالیتها'>";
            echo "</a>  ";
            echo "</td>";
            */
            echo "<td nowrap>";
            $members = manage_ProjectMembers::GetList($res[$k]->ProjectID);
            for($i=0; $i<count($members); $i++)
                echo $members[$i]->AccessType_Desc.": ".$members[$i]->PersonID_FullName."<br>";
            $members = manage_ProjectResponsibles::GetList($res[$k]->ProjectID);
            for($i=0; $i<count($members); $i++)
                echo "<font color=green>پاسخگو</font>: ".$members[$i]->PersonID_FullName."<br>";
            echo "&nbsp;</td>";
            /*
            echo "	<td nowrap>";
            echo "	<a target=\"_blank\" href='ShowProjectOverview.php?ProjectID=".$res[$k]->ProjectID ."'>";
            echo "	<img src='images/report1.jpg' border='0' title='گزارش'>";
            echo "	</a>  ";
            echo "	</td>";
            */
            echo "</tr>";
        }
        ?>
        <tr class="FooterOfTable">
            <td colspan="9" align="center">

                <button type="button" class="btn btn-danger" onclick="javascript : ConfirmDelete();" ><? echo C_REMOVE; ?></button>
                <button type="button" class="btn btn-primary" onclick='javascript: NewRecordForm.submit();' ><? echo C_CREAT; ?></button>


            </td>
        </tr>
        <tr bgcolor="#cccccc"><td colspan="9" align="right">
                <?
                for($k=0; $k<manage_projects::GetProjectsInSearchResult($MemberPersonID, $ouid, $ProjectGroupID, $title, $SysCode, $ProjectStatus)/$NumberOfRec; $k++)
                {
                    if($PageNumber!=$k)
                        echo "<a href='javascript: ShowPage(".($k).")'>";
                    echo ($k+1);
                    if($PageNumber!=$k)
                        echo "</a>";
                    echo " ";
                }
                ?>
            </td></tr>
    </table>
</form>
<form class="form-group" target="_blank" method="post" action="Newprojects.php" id="NewRecordForm" name="NewRecordForm">
</form>
<script>
    function ConfirmDelete()
    {
        if(confirm('<? echo C_ARE_YOU_SURE; ?>')) document.ListForm.submit();
    }
    function ShowPage(PageNumber)
    {
        f1.PageNumber.value=PageNumber;
        f1.submit();
    }
    function Sort(OrderByFieldName, OrderType)
    {
        f1.OrderByFieldName.value=OrderByFieldName;
        f1.OrderType.value=OrderType;
        f1.submit();
    }
</script>
</html>