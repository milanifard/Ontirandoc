<?php
function HTMLBegin($bgcolor = '#C8DEF0', $AddDocType=TRUE)
{
    if($AddDocType==TRUE)
        echo "<!DOCTYPE html>\n<html>\n<head>\n" ;
    else
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">";
    echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n".
        "<link rel=\"stylesheet\"  href=\"css/login.css\" type=\"text/css\">\n";
    if (UI_LANGUAGE == "EN") {
        echo "<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css\" integrity=\"sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm\" crossorigin=\"anonymous\">\n".
            "<script src=\"https://code.jquery.com/jquery-3.2.1.slim.min.js\" integrity=\"sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN\" crossorigin=\"anonymous\"></script>\n".
            "<script src=\"https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js\" integrity=\"sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q\" crossorigin=\"anonymous\"></script>\n".
            "<script src=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js\" integrity=\"sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl\" crossorigin=\"anonymous\"></script>\n";
    }
    else if (UI_LANGUAGE == "FA")
    {
        echo "<link rel=\"stylesheet\"  href=\"https://cdn.rtlcss.com/bootstrap/v4.2.1/css/bootstrap.min.css\" integrity=\"sha384-vus3nQHTD+5mpDiZ4rkEPlnkcyTP+49BhJ4wJeJunw06ZAp+wzzeBPUXr42fi8If\"  crossorigin=\"anonymous\">\n" .
            "<script src=\"https://code.jquery.com/jquery-3.2.1.slim.min.js\" integrity=\"sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN\" crossorigin=\"anonymous\"></script>\n".
            "<script src=\"https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js\" integrity=\"sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q\" crossorigin=\"anonymous\"></script>\n".
            "<script  src=\"https://cdn.rtlcss.com/bootstrap/v4.2.1/js/bootstrap.min.js\" integrity=\"sha384-a9xOd0rz8w0J8zqj1qJic7GPFfyMfoiuDjC9rqXlVOcGO/dmRqzMn34gZYDTel8k\" crossorigin=\"anonymous\"></script>\n";
    }
    echo "<link rel=\"stylesheet\" href=\"https://use.fontawesome.com/releases/v5.7.0/css/all.css\" integrity=\"sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ\" crossorigin=\"anonymous\">";
    echo "\n</head>\n";
    echo "<body ";
    if(UI_LANGUAGE=="FA")
        echo " dir = 'rtl' ";
    echo ">";
}

function HTMLEnd()
{
    echo "</body></html>";
}

class SharedClass
{
    static function FixNumber($var)
    {
        if (preg_match('/^\d+$/', $var))
            return $var;
        return 0;
    }

    static function IsDateFormat($st)
    {
        //echo "<br>".$st;
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$st)) {
            //echo ":OK<br>";
            return true;
        } else {
            //echo ":FALSE<br>";
            return false;
        }	}

    /*
     * @param $ShamsiYear: سال شمسی
     * @param $ShamsiMonth: ماه شمسی
     * @param $ShamsiDay: روز شمسی
     * @return تاریخ میلادی
     */
    static function xdate2($date)
    {
        if($date==NULL)
            return '1000-01-01';
        else{
            $yy=substr($date,0,4);
            $mm=substr($date,5,2);
            $dd=substr($date,8,2);
            $xdate2 = ConvertS2XDate($dd,$mm,$yy);
            return $xdate2;}
    }
    static function ConvertToMiladi2($ShamsiYear, $ShamsiMonth, $ShamsiDay)
    {
        //!ereg("^[0-9]{2}", $ShamsiMonth) || !ereg("^[0-9]{2}", $ShamsiDay))
        if(!is_numeric($ShamsiYear) || !is_numeric($ShamsiMonth) || !is_numeric($ShamsiDay))
            return "1000-01-01";
        if($ShamsiMonth>12 || $ShamsiDay>31 || $ShamsiYear==0)
            return "1000-01-01";
        if(strlen($ShamsiDay)==1)
            $ShamsiDay = "0".$ShamsiDay;
        if(strlen($ShamsiMonth)==1)
            $ShamsiMonth = "0".$ShamsiMonth;
        $ShamsiDate = SharedClass::xdate2($ShamsiYear."/".$ShamsiMonth."/".$ShamsiDay);
        return substr($ShamsiDate,0,4)."-".substr($ShamsiDate,4,2)."-".substr($ShamsiDate,6,2);
    }

    /*
     * @param $ShamsiYear: سال شمسی
     * @param $ShamsiMonth: ماه شمسی
     * @param $ShamsiDay: روز شمسی
     * @return تاریخ میلادی
     */
    static function ConvertToMiladi($ShamsiYear, $ShamsiMonth, $ShamsiDay)
    {
        //!ereg("^[0-9]{2}", $ShamsiMonth) || !ereg("^[0-9]{2}", $ShamsiDay))
        if(!is_numeric($ShamsiYear) || !is_numeric($ShamsiMonth) || !is_numeric($ShamsiDay))
            return "1000-01-01";
        if($ShamsiMonth>12 || $ShamsiDay>31 || $ShamsiYear==0)
            return "1000-01-01";
        if(strlen($ShamsiDay)==1)
            $ShamsiDay = "0".$ShamsiDay;
        if(strlen($ShamsiMonth)==1)
            $ShamsiMonth = "0".$ShamsiMonth;
        $ShamsiDate = xdate(strtotime($ShamsiYear."/".$ShamsiMonth."/".$ShamsiDay));
//		echo $ShamsiYear."/".$ShamsiMonth."/".$ShamsiDay." -> ".substr($ShamsiDate,0,4)."-".substr($ShamsiDate,4,2)."-".substr($ShamsiDate,6,2)."<br>";
        return substr($ShamsiDate,0,4)."-".substr($ShamsiDate,4,2)."-".substr($ShamsiDate,6,2);
    }

    // بر اساس یک کلید از جدول دومین آپشنهای یک لیست را بر می گرداند
    static function CreateADomainNameSelectOptions($DomainName, $OrderByColumn = "description")
    {
        $ret = "";
        $mysql = dbclass::getInstance();
        $res = $mysql->Execute("select * from baseinfo.domains where DomainName='".$DomainName."' order by ".$OrderByColumn);
        while($rec = $res->FetchRow())
        {
            $ret .= "<option value='".$rec["DomainValue"]."'>";
            $ret .= $rec["description"];
            $ret .= "</option>";
        }
        return $ret;
    }

    // آپشنهای یک لیست را بر اساس یک جدول و فیلد مقدار و متن مربوطه می سازد
    static function CreateARelatedTableSelectOptions($RelatedTable, $RelatedValueField, $RelatedDescriptionField, $OrderBy = "")
    {
        if($OrderBy=="")
            $OrderBy = $RelatedValueField;
        $ret = "";
        $mysql = pdodb::getInstance();
        $mysql->Prepare("select * from ".$RelatedTable." order by ".$OrderBy);
        $res = $mysql->ExecuteStatement(array());
        while($rec = $res->fetch())
        {
            $ret .= "<option value='".$rec[$RelatedValueField]."'>";
            $ret .= $rec[$RelatedDescriptionField];
            $ret .= "</option>";
        }
        return $ret;
    }



    static function CreateAdvanceRelatedTableSelectOptions($RelatedTable, $RelatedValueField, $RelatedDescriptionField, $SelectOptions, $OrderBy = "")
    {
        if($OrderBy=="")
            $OrderBy = $RelatedValueField;
        $ret = "";
        $mysql = dbclass::getInstance();
        $res = $mysql->Execute("select ".$SelectOptions." from ".$RelatedTable." order by ".$OrderBy);
        while($rec = $res->FetchRow())
        {
            $ret .= "<option value='".$rec[$RelatedValueField]."'>";
            $ret .= $rec[$RelatedDescriptionField];
            $ret .= "</option>";
        }
        return $ret;
    }

    static function CreateMessageBox($MessageBody, $MessageColor='green')
    {
        $ret = "";
        $ret .= "<table align=center><tr id=\"MessageBox\" style=\"display: \"><td><font color='".$MessageColor."'>".$MessageBody."</font></td></tr></table>\r\n";
        $ret .= "<script>setTimeout('document.getElementById(\"MessageBox\").style.display=\"none\";', 3000);</script>";
        return $ret;
    }

    static function GetPersonFullName($PersonID)
    {
        $mysql = pdodb::getInstance();
        $mysql->Prepare("select concat(pfname, ' ', plname) as FullName from projectmanagement.persons where PersonID=?");
        $res = $mysql->ExecuteStatement(array($PersonID));
        if($rec = $res->fetch())
        {
            return $rec["FullName"];
        }
        return "-";
    }

    /**
     * @param string|integer $jy سال شمسی
     * @param string|integer $jm ماه شمسی
     * @param string|integer $jd روز شمسی
     * @param string $mod کاراکتر جداکننده ی ماه و روز و سال در خروجی
     * @return array|string
     * @author Alireza Imani
     *
     */
    static function jalali_to_gregorian($jy, $jm, $jd, $mod = '')
    {
        if ($jy > 979) {
            $gy = 1600;
            $jy -= 979;
        } else {
            $gy = 621;
        }

        $days = (365 * $jy) + (((int)($jy / 33)) * 8) + ((int)((($jy % 33) + 3) / 4)) + 78 + $jd + (($jm < 7) ? ($jm - 1) * 31 : (($jm - 7) * 30) + 186);
        $gy += 400 * ((int)($days / 146097));
        $days %= 146097;
        if ($days > 36524) {
            $gy += 100 * ((int)(--$days / 36524));
            $days %= 36524;
            if ($days >= 365) $days++;
        }
        $gy += 4 * ((int)(($days) / 1461));
        $days %= 1461;
        $gy += (int)(($days - 1) / 365);
        if ($days > 365) $days = ($days - 1) % 365;
        $gd = $days + 1;
        foreach (array(0, 31, ((($gy % 4 == 0) and ($gy % 100 != 0)) or ($gy % 400 == 0)) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31) as $gm => $v) {
            if ($gd <= $v) break;
            $gd -= $v;
        }

        return ($mod === '') ? array($gy, $gm, $gd) : $gy . $mod . $gm . $mod . $gd;
    }
}

class PermissionsContainer
{
    private $PermissionTable = array();
    private $ItemsCount = 0;
    public $HasWriteAccessOnOneItemAtLeast = false; // کاربر حداقل به یکی از آیتمها دسترسی کامل دارد

    public function Add($ObjectName, $PermissionType)
    {
        $this->PermissionTable[$this->ItemsCount]["ObjectName"] = $ObjectName;
        $this->PermissionTable[$this->ItemsCount]["PermissionType"] = $PermissionType;
        if($PermissionType=="WRITE")
            $this->OneItemAccessAtLeast = true;
        $this->ItemsCount++;
    }

    public function Reset()
    {
        $this->ItemsCount = 0;
    }

    public function GetPermission($ObjectName)
    {
        for($i=0; $i<$this->ItemsCount; $i++)
        {
            if($this->PermissionTable[$i]["ObjectName"] ==  $ObjectName)
                return $this->PermissionTable[$i]["PermissionType"];
        }
        return "NONE";
    }

    public function Show()
    {
        for($i=0; $i<$this->ItemsCount; $i++)
        {
            echo $this->PermissionTable[$i]["ObjectName"]." -> ".$this->PermissionTable[$i]["PermissionType"]."<br>";
        }
    }
}
?>