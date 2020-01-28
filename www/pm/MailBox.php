<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : گردش پیامهای شخصی
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-22
*/
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', "on");


//
//
//
//
//
//THIS FILE IS TAKEN BY MOHAMAD_ALI_SAIDI PLEASE BE CAREFUL  !
//
//
//
//
//
//

/*
*the page is redesigned to bootstrap and the <?php> is added in the missing fields
 *  */









include("header.inc.php");
include_once("../sharedClasses/SharedClass.class.php");
include_once("classes/PrivateMessageFollows.class.php");

HTMLBegin();

$NumberOfRec = 30;
 $k=0;
$PageNumber = 0;
if(isset($_REQUEST["PageNumber"]))
{
	if(!is_numeric($PageNumber))
		$PageNumber = 0;
	else
		$PageNumber = $_REQUEST["PageNumber"];
	$FromRec = $PageNumber*$NumberOfRec;
}
else
{
	$FromRec = 0;
}
$OrderByFieldName = "PrivateMessageFollowID";
$OrderType = "DESC";
if(isset($_REQUEST["OrderByFieldName"]))
{
	$OrderByFieldName = $_REQUEST["OrderByFieldName"];
	$OrderType = $_REQUEST["OrderType"];
}
$res = manage_PrivateMessageFollows::GetList("InBox", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType);
$SomeItemsRemoved = false;
for($k=0; $k<count($res); $k++)
{
	if(isset($_REQUEST["ch_".$res[$k]->PrivateMessageFollowID]))
	{
		manage_PrivateMessageFollows::Archive($res[$k]->PrivateMessageFollowID);
		$SomeItemsRemoved = true;
	}
}
if($SomeItemsRemoved)
	$res = manage_PrivateMessageFollows::GetList("InBox", $FromRec, $NumberOfRec, $OrderByFieldName, $OrderType);

?>




<form id="ListForm" name="ListForm" method="post">

<?php
    if(isset($_REQUEST["PageNumber"]))
	echo "<input type=\"hidden\" name=\"PageNumber\" value=".$_REQUEST["PageNumber"].">";
?>

<br>

    <div class="container ">

        <div class="row border border-dark">

            <div class="col-12">

                <div class="row">

                    <p class="col-12 bg-info text-dark text-center  ">


                        <?php
                        echo C_MESSAGES_RECEIVED;
                        ?>

                    </p>

                </div>
                <div class="row">

                    <div class="col-12">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover table-striped">

                                <thead class="text-center thead-dark">
                                <tr>
                                    <th scope="col">  </th>
                                    <th scope="col"><?php
                                        echo C_ROW;
                                        ?> </th>

                                    <?php
                                    $OrderType1 = $OrderType2 = "ASC";
                                    if(isset($_REQUEST["OrderByFieldName"]))
                                    {
                                        if($_REQUEST["OrderByFieldName"]=="FromPersonID" && $_REQUEST["OrderType"]=="ASC")
                                            $OrderType1 = "DESC";
                                        if($_REQUEST["OrderByFieldName"]=="ReferTime" && $_REQUEST["OrderType"]=="ASC")
                                            $OrderType2 = "DESC";
                                    }
                                    ?>


                                    <th scope="col" class="text-nowrap" ><a href="javascript: Sort('FromPersonID', '<?php echo $OrderType1 ?>');"><?php echo C_SENDER_NAME; ?> </a></th>
                                    <th scope="col" class="text-nowrap" ><a href="javascript: Sort('ReferTime', '<?php echo $OrderType2 ?>');"><?php echo C_TIME_SENT; ?> </a></th>
                                    <th scope="col" class="text-nowrap" ><?php echo C_TITLE; ?></th>
                                    <th scope="col" class="text-nowrap" ><?php echo C_REPLY_DES; ?></th>
                                </tr>
                                </thead>

                                <tbody>
                                <?php
                                for($k=0; $k<count($res); $k++)
                                {


                                    echo "<tr class=\"text-center\">";
                                    if($res[$k]->ReferStatus=="NOT_READ")
                                        $NewMail = "<b>";
                                    else
                                        $NewMail = "";
                                    echo "<td class=\"align-middle\">";
                                    echo "<input type=\"checkbox\" class \"form-check-input \" name=\"ch_".$res[$k]->PrivateMessageFollowID."\">";
                                    echo "</td>";
                                    echo "<td class=\"align-middle\">".$NewMail;
                                    echo "<a class=\"btn btn-outline-primary\" role=\"button\" href=\"ShowMessage.php?MessageFollowID=".$res[$k]->PrivateMessageFollowID."\">";
                                    echo ($k+$FromRec+1)."</td>";
                                    echo "	<td  class=\"align-middle text-nowrap \">".$NewMail.htmlentities($res[$k]->FromPersonID_FullName, ENT_QUOTES, 'UTF-8')."</td>";
                                    echo "	<td  class=\"align-middle text-nowrap \">".$NewMail.htmlentities($res[$k]->ReferTime_Shamsi, ENT_QUOTES, 'UTF-8')."</td>";
                                    echo "	<td class=\"align-middle\">".$NewMail.htmlentities($res[$k]->MessageTitle, ENT_QUOTES, 'UTF-8')."</td>";
                                    echo "	<td class=\"align-middle\">".$NewMail.htmlentities($res[$k]->comment, ENT_QUOTES, 'UTF-8')."</td>";
                                    echo "</tr>";
                                }
                                ?>








                                </tbody>



                            </table>


                        </div>



                    </div>

                </div>






                <div class="row">

                    <div class="col-12 bg-info text-dark text-center ">

                        <div class="btn">

                            <input type="button" class="btn btn-success" onclick="javascript: ConfirmDelete();" value=<?php echo C_DELETE; ?>>

                        </div>

                    </div>

                </div>



                <div class="row">

                    <div class="col-12 bg-secondary  text-right ">

                        <?php
                        for($k=0; $k<manage_PrivateMessageFollows::GetCount("InBox")/$NumberOfRec; $k++)
                        {
                            if($PageNumber!=$k)
                                echo "<a href='javascript: ShowPage(".($k).")'>";
                            echo ($k+1);
                            if($PageNumber!=$k)
                                echo "</a>";
                            echo " ";
                        }
                        ?>

                    </div>


                </div>




            </div>



        </div>


    </div>

</form>


<form target="_blank" method="post" action="NewPrivateMessageFollows.php" id="NewRecordForm" name="NewRecordForm">
</form>
<form method="post" name="f2" id="f2">
    <input type="hidden" name="PageNumber" id="PageNumber" value="0">
    <input type="hidden" name="OrderByFieldName" id="OrderByFieldName" value="<?php echo $OrderByFieldName; ?>">
    <input type="hidden" name="OrderType" id="OrderType" value="<?php echo $OrderType; ?>">
</form>

<script>
    function ConfirmDelete()
    {
        if(confirm('<?php echo C_ARE_YOU_SURE; ?>'))
            document.ListForm.submit();
    }
    function ShowPage(PageNumber)
    {
        f2.PageNumber.value=PageNumber;
        f2.submit();
    }
    function Sort(OrderByFieldName, OrderType)
    {
        f2.OrderByFieldName.value=OrderByFieldName;
        f2.OrderType.value=OrderType;
        f2.submit();
    }
</script>
</html>