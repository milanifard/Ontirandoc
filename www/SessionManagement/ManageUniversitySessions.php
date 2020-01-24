<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : جلسات
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-3-6
*/
include("../shares/header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/UniversitySessions.class.php");
include("classes/UniversitySessionsSecurity.class.php");
/*if(config::$critical_status!=10){
echo "<br><br>
		<div style='color:red;font-family:tahoma;font-size:14px;font-weight:bold' align=center>" . 
						                                 ".به علت بار زیاد سرور این قسمت غیرفعال شده است" . 
		"</div>";
die();
}*/
//ini_set('display_errors','off');
HTMLBegin();
//if($_SESSION["PersonID"]=="201309")
//	$_SESSION["PersonID"] = "201391";
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
//print_r($_REQUEST["SearchAction"]);
if(isset($_REQUEST["SearchAction"]))
{
    $OrderByFieldName = "SessionDate";
    $OrderType = "DESC";
    if(isset($_REQUEST["OrderByFieldName"]))
    {
        $OrderByFieldName = $_REQUEST["OrderByFieldName"];
        $OrderType = $_REQUEST["OrderType"];
    }
    $SessionTypeID=htmlentities($_REQUEST["Item_SessionTypeID"], ENT_QUOTES, 'UTF-8');
    $SessionNumber=htmlentities($_REQUEST["Item_SessionNumber"], ENT_QUOTES, 'UTF-8');
    $SessionTitle=htmlentities($_REQUEST["Item_SessionTitle"], ENT_QUOTES, 'UTF-8');
    $SessionFromDate = SharedClass::ConvertToMiladi($_REQUEST["SessionFromDate_YEAR"], $_REQUEST["SessionFromDate_MONTH"], $_REQUEST["SessionFromDate_DAY"]);
    $SessionToDate = SharedClass::ConvertToMiladi($_REQUEST["SessionToDate_YEAR"], $_REQUEST["SessionToDate_MONTH"], $_REQUEST["SessionToDate_DAY"]);
    $SessionLocation=htmlentities($_REQUEST["Item_SessionLocation"], ENT_QUOTES, 'UTF-8');
    $PreCommandKeyWord=htmlentities($_REQUEST["Item_PreCommandKeyWord"], ENT_QUOTES, 'UTF-8');
    $DecisionKeyWord=htmlentities($_REQUEST["Item_DecisionKeyWord"], ENT_QUOTES, 'UTF-8');
    $_REQUEST["view"]='';
}
else
{
    $OrderByFieldName = "SessionDate";
    $OrderType = "DESC";
    $SessionTypeID='';
    $SessionNumber='';
    $SessionTitle='';
    $SessionDate='';
    $SessionLocation='';
    $SessionFromDate = '0000-00-00';
    $SessionToDate = '0000-00-00';
    $PreCommandKeyWord = "";
    $DecisionKeyWord = "";
}
$SessionTypesList = manage_UniversitySessions::GetOptions($_SESSION["PersonID"]);
$view = "";
if(isset($_REQUEST["view"]))
    $view = $_REQUEST["view"];
$res = manage_UniversitySessions::Search($_SESSION["PersonID"], $SessionTypeID,$view, $SessionNumber, $SessionTitle, $SessionFromDate, $SessionToDate, $SessionLocation, $PreCommandKeyWord, $DecisionKeyWord, "", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType);
/*if($_SESSION["UserID"]=='gholami-a'){
print_r($res[1]->UniversitySessionID);}*/
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
    if(isset($_REQUEST["ch_".$res[$k]->UniversitySessionID]))
    {
        manage_UniversitySessions::Remove($res[$k]->UniversitySessionID);
        $SomeItemsRemoved = true;
    }
}
if($SomeItemsRemoved)
    $res = manage_UniversitySessions::Search($_SESSION["PersonID"], $SessionTypeID,$_REQUEST["view"], $SessionNumber, $SessionTitle, $SessionFromDate, $SessionToDate, $SessionLocation, $PreCommandKeyWord, $DecisionKeyWord, "", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType);

if(isset($_REQUEST["SearchAction"]))
{
    ?>
    <script>
        document.SearchForm.Item_SessionTypeID.value='<? echo htmlentities($_REQUEST["Item_SessionTypeID"], ENT_QUOTES, 'UTF-8'); ?>';
        document.SearchForm.Item_SessionNumber.value='<? echo htmlentities($_REQUEST["Item_SessionNumber"], ENT_QUOTES, 'UTF-8'); ?>';
        document.SearchForm.Item_SessionTitle.value='<? echo htmlentities($_REQUEST["Item_SessionTitle"], ENT_QUOTES, 'UTF-8'); ?>';
        document.SearchForm.SessionFromDate_DAY.value='<? echo htmlentities($_REQUEST["SessionFromDate_DAY"], ENT_QUOTES, 'UTF-8'); ?>';
        document.SearchForm.SessionFromDate_MONTH.value='<? echo htmlentities($_REQUEST["SessionFromDate_MONTH"], ENT_QUOTES, 'UTF-8'); ?>';
        document.SearchForm.SessionFromDate_YEAR.value='<? echo htmlentities($_REQUEST["SessionFromDate_YEAR"], ENT_QUOTES, 'UTF-8'); ?>';
        document.SearchForm.SessionToDate_DAY.value='<? echo htmlentities($_REQUEST["SessionToDate_DAY"], ENT_QUOTES, 'UTF-8'); ?>';
        document.SearchForm.SessionToDate_MONTH.value='<? echo htmlentities($_REQUEST["SessionToDate_MONTH"], ENT_QUOTES, 'UTF-8'); ?>';
        document.SearchForm.SessionToDate_YEAR.value='<? echo htmlentities($_REQUEST["SessionToDate_YEAR"], ENT_QUOTES, 'UTF-8'); ?>';
        document.SearchForm.Item_SessionLocation.value='<? echo htmlentities($_REQUEST["Item_SessionLocation"], ENT_QUOTES, 'UTF-8'); ?>';
    </script>
    <?
}
?>
<br>
<?if($view=="" && (!isset($_REQUEST["SearchAction"]) || $_REQUEST["SearchAction"]!=1)) { ?>
<div class="container-fluid row">
    <div class="col-md-2"> </div>
    <div class="col-md-8">
        <table class="table table-bordered table-responsive"  >
            <thead class="bg-secondary text-center">
                <td class="h6" colspan="2">
                    <?php
                     echo C_SESSIONS;
                    ?>
                </td>
            </thead>

            <thead class="bg-info">
                <!--<td width="1%">ردیف</td>-->
                <td class="font-weight-bold"><a href="javascript: Sort('SessionTypeID', 'ASC');"><?php echo C_MEETING_TYPE ?></a></td>
                <td class="font-weight-bold" width="2%"><?php echo C_VIEWER ?></td>
            </thead>
            <?

            $mysql = pdodb::getInstance();
            $query = "select distinct SessionTypeID, SessionTypes.SessionTypeTitle  
            from sessionmanagement.PersonPermissionsOnFields 
            JOIN sessionmanagement.SessionTypes on (SessionTypeID=RecID) 
            where 
            PersonPermissionsOnFields.TableName='SessionTypes' 
    
            and PersonPermissionsOnFields.FieldName='CreateNewSession' 
            and PersonPermissionsOnFields.PersonID=?  order by SessionTypeID";
            $mysql->Prepare($query);
            $result = $mysql->ExecuteStatement(array($_SESSION["PersonID"]));
            while($rec=$result->fetch()){

                echo "<tr>";
                echo "<td>".htmlentities($rec["SessionTypeTitle"], ENT_QUOTES, 'UTF-8')."</td>";
                echo "<td class='text-center align-items-center'>";
                echo "<a href=\"ManageUniversitySessions.php?view=".$rec["SessionTypeID"]."\">";
                echo "<i class='fa fa-info-circle fa-2x'></i> ";
                echo "</a></td>";
                echo "</tr>";
            }
            ?>
            <tfoot class="bg-info">
                <tr><td> </td><td></td> </tr>
            </tfoot>
        </table>
    </div>
    <div class="col-md-2"> </div>
</div>
<?}

if(isset($_REQUEST["view"]) ){

    ?>
    <br>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <form class="form-group" id="SearchForm" name="SearchForm" method=post >
                    <input type="hidden" name="PageNumber" id="PageNumber" value="0">
                    <input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
                    <input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
                    <input type="hidden" name="SearchAction" id="SearchAction" value="1">
                    <br>
                    <table class="table table-bordered">
                        <thead class="bg-info text-center h4">
                        <td><i class="fa fa-search fa-1x"></i> <b><a href="#" onclick='javascript: if(document.getElementById("SearchTr").style.display=="none") document.getElementById("SearchTr").style.display=""; else document.getElementById("SearchTr").style.display="none";'><? echo C_SEARCH ?></a></td>
                        </thead>
                        <tr id='SearchTr' style='display: none'>
                            <td>
                                <table width="100%" align="center" border="0" cellspacing="0">
                                    <?
                                    if(isset($_REQUEST["UpdateID"]))
                                    {
                                        ?>
                                    <? } else { ?>
                                    <tr id="tr_SessionTypeID" name="tr_SessionTypeID" style='display:'>
                                        <td width="1%" nowrap>
                                            <?php echo C_MEETING_TYPE ?>
                                        </td>
                                        <td nowrap>
                                            <select name="Item_SessionTypeID" id="Item_SessionTypeID">
                                                <option value=0>-
                                                    <? echo $SessionTypesList ?>
                                            </select>
                                        </td>
                                        <? } ?>

                                    <tr>
                                        <td width="1%" nowrap>
                                            <?php echo C_SESSION_NUMBER ?>
                                        </td>
                                        <td nowrap>
                                            <input type="text" name="Item_SessionNumber" id="Item_SessionNumber" maxlength="20" size="40">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td width="1%" nowrap>
                                            <?php echo C_SESSION_TITLE?>
                                        </td>
                                        <td nowrap>
                                            <input type="text" name="Item_SessionTitle" id="Item_SessionTitle" maxlength="500" size="40">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td width="1%" nowrap>
                                            <?php echo C_DATE ?>
                                        </td>
                                        <td nowrap>
                                            <?php echo C_FROM_DATE ?>
                                            <input maxlength="2" id="SessionFromDate_DAY"  name="SessionFromDate_DAY" type="text" size="2">/
                                            <input maxlength="2" id="SessionFromDate_MONTH" name="SessionFromDate_MONTH" type="text" size="2" >/
                                            <input maxlength="2" id="SessionFromDate_YEAR" name="SessionFromDate_YEAR" type="text" size="2" >
                                            <?php echo C_TO_DATE ?>
                                            <input maxlength="2" id="SessionToDate_DAY"  name="SessionToDate_DAY" type="text" size="2">/
                                            <input maxlength="2" id="SessionToDate_MONTH" name="SessionToDate_MONTH" type="text" size="2" >/
                                            <input maxlength="2" id="SessionToDate_YEAR" name="SessionToDate_YEAR" type="text" size="2" >
                                        </td>
                                    </tr>

                                    <tr>
                                        <td width="1%" nowrap>
                                            <?php echo C_SESSION_LOCATION ?>
                                        </td>
                                        <td nowrap>
                                            <input type="text" name="Item_SessionLocation" id="Item_SessionLocation" maxlength="200" size="40">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="1%" nowrap>
                                            <?php echo C_INSTRUCTION_KEYWORD ?>
                                        </td>
                                        <td nowrap>
                                            <input type="text" name="Item_PreCommandKeyWord" id="Item_PreCommandKeyWord" maxlength="200" size="40">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="1%" nowrap>
                                            <?php echo C_ENACTMENT_KEYWORD ?>
                                        </td>
                                        <td nowrap>
                                            <input type="text" name="Item_DecisionKeyWord" id="Item_DecisionKeyWord" maxlength="200" size="40">
                                        </td>
                                    </tr>
                                    <tr class="bg-info">
                                        <td colspan="2" align="center"><button class="btn btn-white" type="submit"> <?php echo C_SEARCH ?> </button></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="col-md-1"></div>
        </div>
        <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-10">
                <form id="ListForm" name="ListForm" method="post">
                    <? if(isset($_REQUEST["PageNumber"]))
                        echo "<input type=\"hidden\" name=\"PageNumber\" value=".$_REQUEST["PageNumber"].">"; ?>
                    <br>
                    <table class="table-bordered table table-striped table-responsive">
                        <thead class="bg-secondary text-center ">
                            <td class="h4" colspan="13">
                                <? echo C_SESSIONS ?>
                            </td>
                        </thead>
                        <thead class="bg-info font-weight-bold">
                            <td width="1%"></td>
                            <td width="1%"><? echo C_ROW ?></td>
                            <td width="2%"><? echo C_EDIT ?></td>
                            <td><a href="javascript: Sort('SessionTypeID', 'ASC');"> <? echo C_MEETING_TYPE ?></a></td>
                            <td><a href="javascript: Sort('SessionNumber', 'ASC');"><? echo C_SESSION_NUMBER ?></a></td>
                            <td><a href="javascript: Sort('SessionTitle', 'ASC');"><? echo C_SESSION_TITLE ?></a></td>
                            <td><a href="javascript: Sort('SessionDate', 'ASC');"><? echo C_DATE ?></a></td>
                            <td><a href="javascript: Sort('SessionLocation', 'ASC');"><? echo C_SESSION_LOCATION ?></a></td>
                            <td><a href="javascript: Sort('SessionStartTime', 'ASC');"><? echo C_START_TIME ?></a></td>
                            <td><a href="javascript: Sort('SessionDurationTime', 'ASC');"><? echo C_DURATION ?></a></td>
                            <td><a href="javascript: Sort('SessionStatus', 'ASC');"><? echo C_SESSION_STATUS ?></a></td>
                            <td nowrap>
                                <? echo C_OTHER ?>
                            </td>
                        </thead>
                        <?
                        for($k=0; $k<count($res); $k++)
                        {

                            echo "<tr class='' >";
                            echo "<td>";
                            if($res[$k]->CurrentUserHasRemoveAccess=="YES")
                                echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->UniversitySessionID."\">";
                            else
                                echo "&nbsp;";
                            echo "</td>";
                            echo "<td>".($k+$FromRec+1)."</td>";
                            echo "	<td>";
                            echo "<a target=\"_blank\" href=\"UpdateUniversitySessions.php?UpdateID=".$res[$k]->UniversitySessionID."\">";
                            echo "<i class='fa fa-edit fa-2x' ></i>";
                            echo "</a></td>";
                            echo "	<td>".$res[$k]->SessionTypeID_Desc."</td>";
                            echo "	<td>".htmlentities($res[$k]->SessionNumber, ENT_QUOTES, 'UTF-8')."</td>";
                            echo "	<td>".htmlentities($res[$k]->SessionTitle, ENT_QUOTES, 'UTF-8')."</td>";
                            echo "	<td>".$res[$k]->SessionDate_Shamsi."</td>";
                            echo "	<td>".htmlentities($res[$k]->SessionLocation, ENT_QUOTES, 'UTF-8')."</td>";
                            echo "	<td>".floor($res[$k]->SessionStartTime/60).":".($res[$k]->SessionStartTime%60)."</td>";
                            echo "	<td>".floor($res[$k]->SessionDurationTime/60).":".($res[$k]->SessionDurationTime%60)."</td>";

                            echo "	<td>".$res[$k]->SessionStatus_Desc."</td>";
                            echo "<td nowrap>";
                            echo "<a target=\"_blank\" href='ManageSessionPreCommands.php?UniversitySessionID=".$res[$k]->UniversitySessionID ."'>";
                            echo "<i class='fa fa-book fa-3x' title='دستور کار'></i>";
                            echo "</a>  ";
                            echo "<a target=\"_blank\" href='ManageSessionDecisions.php?UniversitySessionID=".$res[$k]->UniversitySessionID ."'>";
                            echo "<i class='fa fa-clipboard-check fa-3x' title='مصوبات'></i>";
                            echo "</a>  ";
                            echo "<a target=\"_blank\" href='ManageMembersPAList.php?UniversitySessionID=".$res[$k]->UniversitySessionID ."'>";
                            echo "<i class='fa fa-check-circle fa-3x' title='حض.ر غیاب'></i>";
                            echo "</a>  ";
                            echo "<a target=\"_blank\" href='ManageSessionDocuments.php?UniversitySessionID=".$res[$k]->UniversitySessionID ."'>";
                            echo "<i class='fa fa-folder-open fa-3x' title='مستندات'></i>";
                            echo "</a>  ";
                            echo "<a target=\"_blank\" href='ManageSessionMembers.php?UniversitySessionID=".$res[$k]->UniversitySessionID ."'>";
                            echo "<i class='fa fa-user fa-3x' title='اعضا'></i>";
                            echo "</a>  ";
                            echo "<a target=\"_blank\" href='ManageSessionOtherUsers.php?UniversitySessionID=".$res[$k]->UniversitySessionID ."'>";
                            echo "<i class='fa fa-users fa-3x' title='سایر کاربران'></i>";
                            echo "</a>  ";
                            echo "<a target=\"_blank\" href='ManageSessionHistory.php?UniversitySessionID=".$res[$k]->UniversitySessionID ."'>";
                            echo "<i class='fa fa-history fa-3x' title='سابقه'></i>";
                            echo "</a>  ";
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                        <tr class="bg-info">
                            <td colspan="13" align="center">
                                <button class="btn btn-danger" type="button" onclick="javascript: ConfirmDelete();"> <?php echo C_DELETE ?> </button>

                                <button class="btn btn-success" type="button" onclick='javascript: NewRecordForm.submit();'> <?php echo C_CREATE ?> </button>
                                &nbsp;
                                <button class="btn btn-warning" type="button" onclick='javascript: document.location="ManageUniversitySessions.php"'> <?php echo C_RETURN ?> </button>
                            </td>
                        </tr>
                        <thead class="bg-secondary"><td colspan="13" align="right">
                                <?

                                $SessionsCount = manage_UniversitySessions::GetSearchResultCount($_SESSION["PersonID"], $SessionTypeID,$_REQUEST["view"],$SessionNumber, $SessionTitle, $SessionFromDate, $SessionToDate, $SessionLocation, $PreCommandKeyWord, $DecisionKeyWord, "");
                                for($k=0; $k<$SessionsCount/$NumberOfRec; $k++)
                                {
                                    if($PageNumber!=$k)
                                        echo "<a href='javascript: ShowPage(".($k).")'>";
                                    echo ($k+1);
                                    if($PageNumber!=$k)
                                        echo "</a>";
                                    echo " ";
                                }
                                ?>
                            </td></thead>
                    </table>
                </form>
                <form target="_blank" method="post" action="NewUniversitySessions.php" id="NewRecordForm" name="NewRecordForm">
                </form>
            </div>
            <div class="col-md-1"></div>
        </div>
    </div>
<?}?>
<script>
    function ConfirmDelete()
    {
        if(confirm('آیا مطمین هستید؟')) document.ListForm.submit();
    }
    function ShowPage(PageNumber)
    {
        SearchForm.PageNumber.value=PageNumber;
        SearchForm.submit();
    }
    function Sort(OrderByFieldName, OrderType)
    {
        SearchForm.OrderByFieldName.value=OrderByFieldName;
        SearchForm.OrderType.value=OrderType;
        SearchForm.submit();
    }
    /*function hideTable() {

     document.getElementById('div2').style.display = 'block';
     return false;


     }*/
    setInterval(function(){

        var xmlhttp;
        if (window.XMLHttpRequest)
        {
            // code for IE7 , Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        }
        else
        {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.open("POST","header.inc.php",true);
        xmlhttp.send();

    }, 60000);

</script>

</html>