
/*
تاریخ ویرایش :25/10/98
برنامه نویس :کورش احمدزاده عطایی
*/


<?
include "header.inc.php";
include "FormsGeneratorDB.class.php";
HTMLBegin();
?>
<p align=center><span id=MessageSpan name=MessageSpan></span></p>
<?php
$mysql = pdodb::getInstance();
$LevelNo = "1";
$DBName = $TableName = $FieldName = "";
if(isset($_REQUEST["DBName"]))
{
    $DBName = $_REQUEST["DBName"];
    $TableName = $_REQUEST["TableName"];
    $FieldName = $_REQUEST["FieldName"];
}
if(isset($_REQUEST["FinalSave"]))
{
    $query = "insert into mis.FieldsDataMapping (DBName, TableName, FieldName, ActualValue, ShowValue) values (?,?,?,?,?)";
    $mysql->Prepare($query);
    $mysql->ExecuteStatement(array($_REQUEST["DBName"], $_REQUEST["TableName"], $_REQUEST["FieldName"], $_REQUEST["ActualValue"], $_REQUEST["ShowValue"]));
    echo "<script>document.getElementById('MessageSpan').innerHTML='<font color=green>اطلاعات ذخیره شد</font>';</script>";
    echo "<script>setTimeout(\"document.getElementById('MessageSpan').innerHTML='';\", 1500);</script>";
}
$query = "select * from mis.FieldsDataMapping where DBName=? and TableName=? and FieldName=?";
$mysql->Prepare($query);
$res = $mysql->ExecuteStatement(array($DBName, $TableName, $FieldName));
$list = "";
$i = 0;
while($rec = $res->fetch())
{
    if(isset($_REQUEST["ch_".$rec["FieldsDataMappingID"]]))
    {
        $mysql->Execute("delete from mis.FieldsDataMapping where FieldsDataMappingID='".$rec["FieldsDataMappingID"]."'");
    }
    else
    {
        if($i%2==0)
            $list .= "<tr class=OddRow>";
        else
            $list .= "<tr class=EvenRow>";
        $list .= "<td>";
        $list .= "<input type=checkbox name=ch_".$rec["FieldsDataMappingID"].">";
        $list .= "</td>";
        $list .= "<td nowrap>";
        $list .= $rec["ActualValue"];
        $list .= "</td>";
        $list .= "<td dir=rtl>";
        $list .= $rec["ShowValue"];
        $list .= "</td>";
        $list .= "</tr>";
    }
}
?>
<form method=post id=f1 name=f1>
    <input type=hidden name=WizardReportID id=WizardReportID value='<?php echo $_REQUEST["WizardReportID"]; ?>'>
    <table width=95% align=center border=1 cellspacing=0 cellpadding=3>
        <tr>
            <td>
                <table width=100% border=0>
                    <tr class=HeaderOfTable>
                        <TD COLSPAN=2 ALIGN=CENTER>
                            <?php echo SELECTION_M ?>
                        </TD>
                    </tr>
                    <tr>
                        <td width=1% nowrap><?php echo DATABASE ?></td>
                        <td>
                            <?php if(isset($_REQUEST["Save"])) { ?>
                                <input type=hidden name=Save id=Save value=1>
                                <input type=hidden name=DBName id=DBName value='<?php echo $DBName; ?>'><?php echo $DBName; ?>
                            <?php } else { ?>
                                <select name=DBName id=DBName dir=ltr onchange='javascript: document.f1.submit();'>
                                    <option value=''>-
                                        <?php
                                        $query = "select SCHEMA_NAME from formsgenerator.SCHEMATA order by SCHEMA_NAME";
                                        $res = $mysql->Execute($query);
                                        while($rec = $res->fetch())
                                        {
                                            echo "<option value='".$rec["SCHEMA_NAME"]."' ";
                                            if($DBName==$rec["SCHEMA_NAME"])
                                                echo " Selected ";
                                            echo ">".$rec["SCHEMA_NAME"];
                                        }
                                        ?>
                                </select>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td width=1% nowrap><?php echo TABLE_M ?></td>
                        <td>
                            <?php if(isset($_REQUEST["Save"])) { ?>
                                <input type=hidden name=TableName id=TableName value='<?php echo $TableName; ?>'><?php echo $TableName; ?>
                            <?php } else { ?>
                                <select name=TableName id=TableName dir=ltr onchange='javascript: document.f1.submit();'>
                                    <option value=''>-
                                        <?php
                                        $query = "SELECT * from mis.TABLES_SCHEMA where TABLE_SCHEMA='".$DBName."' order by TABLE_NAME";
                                        $res = $mysql->Execute($query);
                                        while($rec = $res->fetch())
                                        {
                                            echo "<option value='".$rec["TABLE_NAME"]."' ";
                                            if($TableName==$rec["TABLE_NAME"])
                                                echo " selected ";
                                            echo ">";
                                            echo $rec["TABLE_NAME"];
                                            echo " (".$rec["TABLE_COMMENT"].")";
                                        }
                                        ?>
                                </select>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td width=1% nowrap><?php echo FIELD_M ?></td>
                        <td>
                            <?php if(isset($_REQUEST["Save"])) { ?>
                                <input type=hidden name=FieldName id=FieldName value='<?php echo $FieldName; ?>'><?php echo $FieldName; ?>
                            <?php } else { ?>
                                <select name=FieldName id=FieldName dir=ltr>
                                    <option value=''>-
                                        <?php
                                        $query = "SELECT * from mis.COLUMNS where TABLE_SCHEMA='".$DBName."' and TABLE_NAME='".$TableName."' order by COLUMN_NAME";
                                        $res = $mysql->Execute($query);
                                        while($rec = $res->fetch())
                                        {
                                            echo "<option value='".$rec["COLUMN_NAME"]."' ";
                                            if($FieldName==$rec["COLUMN_NAME"])
                                                echo " selected ";
                                            echo ">";
                                            echo $rec["COLUMN_NAME"];
                                            echo " (".$rec["COLUMN_COMMENT"].")";
                                        }
                                        ?>
                                </select>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php if(!isset($_REQUEST["Save"]) && $TableName!="") { ?>
                        <tr>
                            <td colspan=2 align=center>
                                <input type=submit name=Save id=Save value=<?php echo DEF_TABLE ?> >
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if(isset($_REQUEST["Save"])) { ?>
                        <tr>
                            <td colspan=2 align=center>
                                <?php if(!isset($_REQUEST["FromEditTable"])) { ?>
                                    <input type=button onclick='javascript: document.location="ManageFieldsDataMapping.php"' value='بازگشت'>
                                <?php } else { ?>
                                    <input type=button onclick='javascript: window.close();' value=<?php echo CLOSE_N?>>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>

                </table>
            </td>
        </tr>
    </table>
</form>

<?php  if(isset($_REQUEST["Save"]) && $FieldName!="") { ?>
    <br>
    <form method=post id=f3 name=f3>
        <input type=hidden name=FinalSave id=FinalSave value=1>
        <input type=hidden name=Save id=Save value=1>
        <input type=hidden name=DBName id=DBName value='<?php echo $DBName; ?>'>
        <input type=hidden name=TableName id=TableName value='<?php echo $TableName; ?>'>
        <input type=hidden name=FieldName id=FieldName value='<?php echo $FieldName; ?>'>
        <table width=95% align=center>
            <tr>
                <td>
                    <?php echo REAL_VAL ?>
                    <input type=text name=ActualValue id=ActualValue size=20>
                    <?php echo M_VAL_EQ ?>
                    <input type=text name=ShowValue id=ShowValue size=60>

                    <input type=submit value=<?php echo SAVE_M ?>>
                </td>
            </tr>
        </table>
    </form>


    <form method=post id=f2 name=f2>
        <input type=hidden name=Save id=Save value=1>
        <input type=hidden name=DBName id=DBName value='<?php echo $DBName; ?>'>
        <input type=hidden name=TableName id=TableName value='<?php echo $TableName; ?>'>
        <input type=hidden name=FieldName id=FieldName value='<?php echo $FieldName; ?>'>

        <input type=hidden name=WizardReportID id=WizardReportID value='<?php echo $_REQUEST["WizardReportID"]; ?>'>
        <table width=95% align=center border=1 cellspacing=0 cellpadding=3>
            <tr class=HeaderOfTable>
                <td align=center colspan=3><?php echo VAL_FIELD_M ?></td>
            </tr>
            <tr bgcolor=#aaaaaa>
                <td width=1%>&nbsp;</td>
                <td width=30%><?php echo REAL_VAL?></td>
                <td><?php echo M_VAL_EQ?></td>
            </tr>
            <?php echo $list; ?>
            <tr class=FooterOfTable>
                <td colspan=3 align=center>
                    <input type=submit value=<?php echo DELETE_M?>>
                </td>
            </tr>
        </table>
    </form>
<?php } ?>
</html>
/*
تاریخ ویرایش :25/10/98
برنامه نویس :کورش احمدزاده عطایی
*/


<?
include "header.inc.php";
include "FormsGeneratorDB.class.php";
HTMLBegin();
?>
<p align=center><span id=MessageSpan name=MessageSpan></span></p>
<?php
$mysql = pdodb::getInstance();
$LevelNo = "1";
$DBName = $TableName = $FieldName = "";
if(isset($_REQUEST["DBName"]))
{
    $DBName = $_REQUEST["DBName"];
    $TableName = $_REQUEST["TableName"];
    $FieldName = $_REQUEST["FieldName"];
}
if(isset($_REQUEST["FinalSave"]))
{
    $query = "insert into mis.FieldsDataMapping (DBName, TableName, FieldName, ActualValue, ShowValue) values (?,?,?,?,?)";
    $mysql->Prepare($query);
    $mysql->ExecuteStatement(array($_REQUEST["DBName"], $_REQUEST["TableName"], $_REQUEST["FieldName"], $_REQUEST["ActualValue"], $_REQUEST["ShowValue"]));
    echo "<script>document.getElementById('MessageSpan').innerHTML='<font color=green>اطلاعات ذخیره شد</font>';</script>";
    echo "<script>setTimeout(\"document.getElementById('MessageSpan').innerHTML='';\", 1500);</script>";
}
$query = "select * from mis.FieldsDataMapping where DBName=? and TableName=? and FieldName=?";
$mysql->Prepare($query);
$res = $mysql->ExecuteStatement(array($DBName, $TableName, $FieldName));
$list = "";
$i = 0;
while($rec = $res->fetch())
{
    if(isset($_REQUEST["ch_".$rec["FieldsDataMappingID"]]))
    {
        $mysql->Execute("delete from mis.FieldsDataMapping where FieldsDataMappingID='".$rec["FieldsDataMappingID"]."'");
    }
    else
    {
        if($i%2==0)
            $list .= "<tr class=OddRow>";
        else
            $list .= "<tr class=EvenRow>";
        $list .= "<td>";
        $list .= "<input type=checkbox name=ch_".$rec["FieldsDataMappingID"].">";
        $list .= "</td>";
        $list .= "<td nowrap>";
        $list .= $rec["ActualValue"];
        $list .= "</td>";
        $list .= "<td dir=rtl>";
        $list .= $rec["ShowValue"];
        $list .= "</td>";
        $list .= "</tr>";
    }
}
?>
<form method=post id=f1 name=f1>
    <input type=hidden name=WizardReportID id=WizardReportID value='<?php echo $_REQUEST["WizardReportID"]; ?>'>
    <table width=95% align=center border=1 cellspacing=0 cellpadding=3>
        <tr>
            <td>
                <table width=100% border=0>
                    <tr class=HeaderOfTable>
                        <TD COLSPAN=2 ALIGN=CENTER>
                            <?php echo SELECTION_M ?>
                        </TD>
                    </tr>
                    <tr>
                        <td width=1% nowrap><?php echo DATABASE ?></td>
                        <td>
                            <?php if(isset($_REQUEST["Save"])) { ?>
                                <input type=hidden name=Save id=Save value=1>
                                <input type=hidden name=DBName id=DBName value='<?php echo $DBName; ?>'><?php echo $DBName; ?>
                            <?php } else { ?>
                                <select name=DBName id=DBName dir=ltr onchange='javascript: document.f1.submit();'>
                                    <option value=''>-
                                        <?php
                                        $query = "select SCHEMA_NAME from formsgenerator.SCHEMATA order by SCHEMA_NAME";
                                        $res = $mysql->Execute($query);
                                        while($rec = $res->fetch())
                                        {
                                            echo "<option value='".$rec["SCHEMA_NAME"]."' ";
                                            if($DBName==$rec["SCHEMA_NAME"])
                                                echo " Selected ";
                                            echo ">".$rec["SCHEMA_NAME"];
                                        }
                                        ?>
                                </select>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td width=1% nowrap><?php echo TABLE_M ?></td>
                        <td>
                            <?php if(isset($_REQUEST["Save"])) { ?>
                                <input type=hidden name=TableName id=TableName value='<?php echo $TableName; ?>'><?php echo $TableName; ?>
                            <?php } else { ?>
                                <select name=TableName id=TableName dir=ltr onchange='javascript: document.f1.submit();'>
                                    <option value=''>-
                                        <?php
                                        $query = "SELECT * from mis.TABLES_SCHEMA where TABLE_SCHEMA='".$DBName."' order by TABLE_NAME";
                                        $res = $mysql->Execute($query);
                                        while($rec = $res->fetch())
                                        {
                                            echo "<option value='".$rec["TABLE_NAME"]."' ";
                                            if($TableName==$rec["TABLE_NAME"])
                                                echo " selected ";
                                            echo ">";
                                            echo $rec["TABLE_NAME"];
                                            echo " (".$rec["TABLE_COMMENT"].")";
                                        }
                                        ?>
                                </select>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td width=1% nowrap><?php echo FIELD_M ?></td>
                        <td>
                            <?php if(isset($_REQUEST["Save"])) { ?>
                                <input type=hidden name=FieldName id=FieldName value='<?php echo $FieldName; ?>'><?php echo $FieldName; ?>
                            <?php } else { ?>
                                <select name=FieldName id=FieldName dir=ltr>
                                    <option value=''>-
                                        <?php
                                        $query = "SELECT * from mis.COLUMNS where TABLE_SCHEMA='".$DBName."' and TABLE_NAME='".$TableName."' order by COLUMN_NAME";
                                        $res = $mysql->Execute($query);
                                        while($rec = $res->fetch())
                                        {
                                            echo "<option value='".$rec["COLUMN_NAME"]."' ";
                                            if($FieldName==$rec["COLUMN_NAME"])
                                                echo " selected ";
                                            echo ">";
                                            echo $rec["COLUMN_NAME"];
                                            echo " (".$rec["COLUMN_COMMENT"].")";
                                        }
                                        ?>
                                </select>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php if(!isset($_REQUEST["Save"]) && $TableName!="") { ?>
                        <tr>
                            <td colspan=2 align=center>
                                <input type=submit name=Save id=Save value=<?php echo DEF_TABLE ?> >
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if(isset($_REQUEST["Save"])) { ?>
                        <tr>
                            <td colspan=2 align=center>
                                <?php if(!isset($_REQUEST["FromEditTable"])) { ?>
                                    <input type=button onclick='javascript: document.location="ManageFieldsDataMapping.php"' value='بازگشت'>
                                <?php } else { ?>
                                    <input type=button onclick='javascript: window.close();' value=<?php echo CLOSE_N?>>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>

                </table>
            </td>
        </tr>
    </table>
</form>

<?php  if(isset($_REQUEST["Save"]) && $FieldName!="") { ?>
    <br>
    <form method=post id=f3 name=f3>
        <input type=hidden name=FinalSave id=FinalSave value=1>
        <input type=hidden name=Save id=Save value=1>
        <input type=hidden name=DBName id=DBName value='<?php echo $DBName; ?>'>
        <input type=hidden name=TableName id=TableName value='<?php echo $TableName; ?>'>
        <input type=hidden name=FieldName id=FieldName value='<?php echo $FieldName; ?>'>
        <table width=95% align=center>
            <tr>
                <td>
                    <?php echo REAL_VAL ?>
                    <input type=text name=ActualValue id=ActualValue size=20>
                    <?php echo M_VAL_EQ ?>
                    <input type=text name=ShowValue id=ShowValue size=60>

                    <input type=submit value=<?php echo SAVE_M ?>>
                </td>
            </tr>
        </table>
    </form>


    <form method=post id=f2 name=f2>
        <input type=hidden name=Save id=Save value=1>
        <input type=hidden name=DBName id=DBName value='<?php echo $DBName; ?>'>
        <input type=hidden name=TableName id=TableName value='<?php echo $TableName; ?>'>
        <input type=hidden name=FieldName id=FieldName value='<?php echo $FieldName; ?>'>

        <input type=hidden name=WizardReportID id=WizardReportID value='<?php echo $_REQUEST["WizardReportID"]; ?>'>
        <table width=95% align=center border=1 cellspacing=0 cellpadding=3>
            <tr class=HeaderOfTable>
                <td align=center colspan=3><?php echo VAL_FIELD_M ?></td>
            </tr>
            <tr bgcolor=#aaaaaa>
                <td width=1%>&nbsp;</td>
                <td width=30%><?php echo REAL_VAL?></td>
                <td><?php echo M_VAL_EQ?></td>
            </tr>
            <?php echo $list; ?>
            <tr class=FooterOfTable>
                <td colspan=3 align=center>
                    <input type=submit value=<?php echo DELETE_M?>>
                </td>
            </tr>
        </table>
    </form>
<?php } ?>
</html>