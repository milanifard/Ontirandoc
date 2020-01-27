<?php
/*
 صفحه  نمایش لیست و مدیریت داده ها مربوط به : پیامها
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 94-2-5
*/
//
//
//
//
//
////THIS FILE IS TAKEN BY Mohammad_Afsharian_Shandiz PLEASE BE CAREFUL  !
/// //
/// //
/// //
/// //
/// //
/// //
/// //
/// //
/// //
/// //
include("header.inc.php");
include("../sharedClasses/SharedClass.class.php");
include("classes/messages.class.php");
include("classes/ProjectTasks.class.php");
include("classes/PrivateMessageFollows.class.php");
HTMLBegin();
$res = manage_messages::GetActiveMessages();
$LettersCount = manage_PrivateMessageFollows::GetNewMessagesCount();
if(count($res)>0)
{
?>






<table width="90%" align="center" border="1" cellspacing="0">
    <tr bgcolor="#cccccc">
        <td colspan="8">
            <? echo C_MESSAGES ?>
        </td>
    </tr>
    <?php
    for($k=0; $k<count($res); $k++)
    {
        if($k%2==0)
            echo "<tr class=\"OddRow\">";
        else
            echo "<tr class=\"EvenRow\">";
        echo "	<td><a target=_blank href='ShowMessagePhoto.php?MessageID=".$res[$k]->MessageID."'><img src='ShowMessagePhoto.php?MessageID=".$res[$k]->MessageID."' width=50></a></td>";
        echo "	<td>".str_replace("\r", "<br>", htmlentities($res[$k]->MessageBody, ENT_QUOTES, 'UTF-8'));
        if($res[$k]->RelatedFileName!="")
            echo "	<br><a href='DownloadFile.php?FileType=messages&RecID=".$res[$k]->MessageID."'>ضمیمه</a>";
        echo "</td>";


        echo "	<td nowrap>".$res[$k]->CreatorID_FullName."</td>";
        echo "	<td nowrap>".$res[$k]->CreateDate_Shamsi."</td>";
        echo "</tr>";
    }
    echo "</table>";
    }
    ?>

    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped table-warning">
                        <tr>
                            <td><?php
                                echo C_RECIVED_LETTER;
                                ?>: <a class="form-control text-dark" href='MailBox.php'><b><? echo $LettersCount ?></b></a></td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>








        <br>