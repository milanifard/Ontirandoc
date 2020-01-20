<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : الگوهای جلسه
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 89-2-26
*/
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/SessionTypes.class.php");
HTMLBegin();
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
if(isset($_REQUEST["SearchAction"]))
{
    $OrderByFieldName = "SessionTypeID";
    $OrderType = "";
    if(isset($_REQUEST["OrderByFieldName"]))
    {
        $OrderByFieldName = $_REQUEST["OrderByFieldName"];
        $OrderType = $_REQUEST["OrderType"];
    }
    $SessionTypeTitle=htmlentities($_REQUEST["Item_SessionTypeTitle"], ENT_QUOTES, 'UTF-8');
    $SessionTypeLocation=htmlentities($_REQUEST["Item_SessionTypeLocation"], ENT_QUOTES, 'UTF-8');
}
else
{
    $OrderByFieldName = "SessionTypeID";
    $OrderType = "";
    $SessionTypeTitle='';
    $SessionTypeLocation='';
}
$res = manage_SessionTypes::Search($SessionTypeTitle, $SessionTypeLocation, "", $OrderByFieldName, $OrderType);
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
    if(isset($_REQUEST["ch_".$res[$k]->SessionTypeID]))
    {
        manage_SessionTypes::Remove($res[$k]->SessionTypeID);
        $SomeItemsRemoved = true;
    }
}
if($SomeItemsRemoved)
    $res = manage_SessionTypes::Search($SessionTypeTitle, $SessionTypeLocation, "", $OrderByFieldName, $OrderType);
?>
<div class="container-fluid" style="padding: 0">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="table-responsive col-md-8">

            <form id="SearchForm" name="SearchForm" >
                <input type="hidden" name="PageNumber" id="PageNumber" value="0">
                <input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<? echo $OrderByFieldName; ?>">
                <input type="hidden" name="OrderType" id="OrderType" value="<? echo $OrderType; ?>">
                <input type="hidden" name="SearchAction" id="SearchAction" value="1">
                <br>
                <table class="table table-bordered">
                    <thead class="bg-info text-center h4">
                    <td><img src='images/search.gif'><b><a href="#" onclick='javascript: if(document.getElementById("SearchTr").style.display=="none") document.getElementById("SearchTr").style.display=""; else document.getElementById("SearchTr").style.display="none";'><? echo C_SEARCH ?></a></td>
                    </thead>
                    <tr id='SearchTr' style='display: none'>
                        <td>
                            <table class="table table-bordered">
                                <tr class="h4">
                                    <td width="1%" nowrap>
                                        <font color=red>*</font> <? echo C_TITLE ?>
                                    </td>
                                    <td nowrap>
                                        <input type="text" name="Item_SessionTypeTitle" id="Item_SessionTypeTitle" maxlength="500" size="40">
                                    </td>
                                </tr>

                                <tr>
                                    <td width="1%" nowrap>
                                        <font color=red>*</font>  <? echo C_SESSION_LOCATION ?>
                                    </td>
                                    <td nowrap>
                                        <input type="text" name="Item_SessionTypeLocation" id="Item_SessionTypeLocation" maxlength="200" size="40">
                                    </td>
                                </tr>

                                <thead class="bg-info">
                                <td colspan="2" align="center"><input type="submit" value="جستجو"></td>
                                </thead>
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
                    document.SearchForm.Item_SessionTypeTitle.value='<? echo htmlentities($_REQUEST["Item_SessionTypeTitle"], ENT_QUOTES, 'UTF-8'); ?>';
                    document.SearchForm.Item_SessionTypeLocation.value='<? echo htmlentities($_REQUEST["Item_SessionTypeLocation"], ENT_QUOTES, 'UTF-8'); ?>';
                </script>
                <?
            }
            ?>
            <form id="ListForm" name="ListForm" method="post">
                <? if(isset($_REQUEST["PageNumber"]))
                    echo "<input type=\"hidden\" name=\"PageNumber\" value=".$_REQUEST["PageNumber"].">"; ?>
                <br>
                <table class="table table-bordered table-striped">
                    <thead class="bg-secondary">
                    <td colspan="7">
                        الگوهای جلسه
                    </td>
                    </thead>
                    <thead class="bg-info">
                    <td width="1%"> </td>
                    <td width="1%"><? echo C_ROW ?></td>
                    <td width="2%"><? echo C_EDIT ?></td>
                    <td><a href="javascript: Sort('SessionTypeTitle', 'ASC');"><? echo C_TITLE ?></a></td>
                    <td><a href="javascript: Sort('SessionTypeLocation', 'ASC');"><? echo C_SESSION_LOCATION ?> </a></td>
                    <td width=1% nowrap><? echo C_SESSION_MEMBERS?></td>
                    <td width=1% nowrap> <? echo C_SESSION_PERMITTED_PERSON ?></td>
                    </thead>
                    <?
                    for($k=0; $k<count($res); $k++)
                    {
                        echo "<tr>";
                        echo "<td>";
                        echo "<input type=\"checkbox\" name=\"ch_".$res[$k]->SessionTypeID."\">";
                        echo "</td>";
                        echo "<td>".($k+$FromRec+1)."</td>";
                        echo "	<td>";
                        echo "<a target=\"_blank\" href=\"NewSessionTypes.php?UpdateID=".$res[$k]->SessionTypeID."\">";
                        echo "<img src='images/edit.gif' title='ویرایش'>";
                        echo "</a></td>";
                        echo "	<td>".htmlentities($res[$k]->SessionTypeTitle, ENT_QUOTES, 'UTF-8')."</td>";
                        echo "	<td>".htmlentities($res[$k]->SessionTypeLocation, ENT_QUOTES, 'UTF-8')."</td>";
                        echo "<td width=1% nowrap align=center><a  target=\"_blank\" href='ManageSessionTypeMembers.php?SessionTypeID=".$res[$k]->SessionTypeID ."'><img src='images/members.gif' title='اعضا'></a></td>";
                        echo "<td width=1% nowrap align=center><a  target=\"_blank\" href='ManagePersonPermittedSessionTypes.php?SessionTypeID=".$res[$k]->SessionTypeID ."'><img src='images/people.gif' title='کاربران مجاز'></a></td>";
                        echo "</tr>";
                    }
                    ?>
                    <thead class="bg-info">
                    <td colspan="7" align="center">
                        <button class="btn btn-danger" type="button" onclick="javascript: ConfirmDelete();"> <?php echo C_DELETE ?> </button>
                        <button class="btn btn-success" type="button" onclick="javascript: NewRecordForm.submit();"><?php echo C_SAVE ?></button>
                    </td>
                    </thead>
                    <tr class="bg-secondary"><td colspan="7" align="right">
                            <?
                            for($k=0; $k<manage_SessionTypes::GetCount()/$NumberOfRec; $k++)
                            {
                                if($PageNumber!=$k)
                                    echo "<a href='javascript: ShowPage(".($k).")'>";
                                echo ($k+1);
                                if($PageNumber!=$k)
                                    echo "</a>";
                                echo " ";
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </form>
            <form target="_blank" method="post" action="NewSessionTypes.php" id="NewRecordForm" name="NewRecordForm">
            </form>

        </div>
    </div>
    <div class="col-mid-2"></div>
</div>

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
</script>
</html>
