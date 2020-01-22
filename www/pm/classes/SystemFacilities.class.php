<?php
/*
 تعریف کلاسها و متدهای مربوط به : 
	برنامه نویس: امید میلانی فرد
	تاریخ ایجاد: 92-8-4
*/

/*
کلاس پایه: 
*/
class be_SystemFacilities
{
	public $FacilityID;		//
	public $FacilityName;		//
	public $GroupID;		//
	public $GroupID_Desc;		/* شرح مربوط به گروه */
	public $OrderNo;		//
	public $PageAddress;		//
    public $EFacilityName; // added by naghme Mohammadifar
	function be_SystemFacilities() {}

	function LoadDataFromDatabase($RecID)
	{
		$query = "select SystemFacilities.* 
			, l2.GroupName  as l2_GroupName from projectmanagement.SystemFacilities 
			LEFT JOIN projectmanagement.SystemFacilityGroups  l2 on (l2.GroupID=SystemFacilities.GroupID)  where  SystemFacilities.FacilityID=? ";
		$mysql = pdodb::getInstance();
		$mysql->Prepare ($query);
		$res = $mysql->ExecuteStatement (array ($RecID));
		if($rec=$res->fetch())
		{
			$this->FacilityID=$rec["FacilityID"];
			$this->FacilityName=$rec["FacilityName"];
			$this->GroupID=$rec["GroupID"];
			$this->GroupID_Desc=$rec["l2_GroupName"]; // محاسبه از روی جدول وابسته
			$this->OrderNo=$rec["OrderNo"];
			$this->PageAddress=$rec["PageAddress"];
			$this->EFacilityName=$rec["EFacilityName"]; // added by naghme mohammadifar
		}
	}
}
/*
کلاس مدیریت 
*/
class manage_SystemFacilities
{
	static function GetCount($WhereCondition="")
	{
		$mysql = pdodb::getInstance();
		$query = "select count(FacilityID) as TotalCount from projectmanagement.SystemFacilities";
		if($WhereCondition!="")
		{
			$query .= " where ".$WhereCondition;
		}
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array());
        if($rec=$res->fetch())
		{
			return $rec["TotalCount"];
		}
		return 0;
	}
	static function GetLastID()
	{
		$mysql = pdodb::getInstance();
		$query = "select max(FacilityID) as MaxID from projectmanagement.SystemFacilities";
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array());
        if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}
	/**
	* @param $FacilityName: عنوان
	* @param $GroupID: گروه
	* @param $OrderNo: ترتیب
	* @param $PageAddress: آدرس صفحه
	* @return کد داده اضافه شده	*/
	static function Add($FacilityName, $GroupID, $OrderNo, $PageAddress)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into projectmanagement.SystemFacilities (";
		$query .= " FacilityName";
		$query .= ", GroupID";
		$query .= ", OrderNo";
		$query .= ", PageAddress";
		$query .= ") values (";
		$query .= "? , ? , ? , ? ";
		$query .= ")";
		$ValueListArray = array();
		array_push($ValueListArray, $FacilityName); 
		array_push($ValueListArray, $GroupID); 
		array_push($ValueListArray, $OrderNo); 
		array_push($ValueListArray, $PageAddress); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_SystemFacilities::GetLastID();
		$mysql->audit("ثبت داده جدید در  با کد ".$LastID);
		return $LastID;
	}
	/**
	* @param $UpdateRecordID: کد آیتم مورد نظر جهت بروزرسانی
	* @param $FacilityName: عنوان
	* @param $GroupID: گروه
	* @param $OrderNo: ترتیب
	* @param $PageAddress: آدرس صفحه
	* @return 	*/
	static function Update($UpdateRecordID, $FacilityName, $GroupID, $OrderNo, $PageAddress)
	{
		$k=0;
		$LogDesc = manage_SystemFacilities::ComparePassedDataWithDB($UpdateRecordID, $FacilityName, $GroupID, $OrderNo, $PageAddress);
		$mysql = pdodb::getInstance();
		$query = "update projectmanagement.SystemFacilities set ";
			$query .= " FacilityName=? ";
			$query .= ", GroupID=? ";
			$query .= ", OrderNo=? ";
			$query .= ", PageAddress=? ";
		$query .= " where FacilityID=?";
		$ValueListArray = array();
		array_push($ValueListArray, $FacilityName); 
		array_push($ValueListArray, $GroupID); 
		array_push($ValueListArray, $OrderNo); 
		array_push($ValueListArray, $PageAddress); 
		array_push($ValueListArray, $UpdateRecordID); 
		$mysql->Prepare($query);
		$mysql->ExecuteStatement($ValueListArray);
		$mysql->audit("بروز رسانی داده با شماره شناسایی ".$UpdateRecordID." در  - موارد تغییر داده شده: ".$LogDesc);
	}
	/**
	* @param $RemoveRecordID: کد رکوردی که باید حذف شود
	* @return -	*/
	static function Remove($RemoveRecordID)
	{
		$mysql = pdodb::getInstance();
		$query = "delete from projectmanagement.SystemFacilities where FacilityID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from projectmanagement.UserFacilities where FacilityID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$query = "delete from projectmanagement.FacilityPages where FacilityID=?";
		$mysql->Prepare($query);
		$mysql->ExecuteStatement(array($RemoveRecordID));
		$mysql->audit("حذف داده با شماره شناسایی ".$RemoveRecordID." از ");
	}
	static function GetList()
	{
		$mysql = pdodb::getInstance();
		$k=0;
		$ret = array();
		$query = "select SystemFacilities.FacilityID
				,SystemFacilities.FacilityName
				,SystemFacilities.GroupID
				,SystemFacilities.OrderNo
				,SystemFacilities.PageAddress
			, l2.GroupName  as l2_GroupName  from projectmanagement.SystemFacilities 
			LEFT JOIN projectmanagement.SystemFacilityGroups  l2 on (l2.GroupID=SystemFacilities.GroupID) 
			order by l2.OrderNo, SystemFacilities.OrderNo ";
		$res = $mysql->Execute($query);
		$i=0;
		while($rec=$res->fetch())
		{
			$ret[$k] = new be_SystemFacilities();
			$ret[$k]->FacilityID=$rec["FacilityID"];
			$ret[$k]->FacilityName=$rec["FacilityName"];
			$ret[$k]->GroupID=$rec["GroupID"];
			$ret[$k]->GroupID_Desc=$rec["l2_GroupName"]; // محاسبه از روی جدول وابسته
			$ret[$k]->OrderNo=$rec["OrderNo"];
			$ret[$k]->PageAddress=$rec["PageAddress"];
			$k++;
		}
		return $ret;
	}
	// داده های پاس شده را با محتویات ذخیره شده فعلی در دیتابیس مقایسه کرده و موارد تفاوت را در یک رشته بر می گرداند
	/**
	* @param $CurRecID: کد آیتم مورد نظر در بانک اطلاعاتی
	* @param $FacilityName: عنوان
	* @param $GroupID: گروه
	* @param $OrderNo: ترتیب
	* @param $PageAddress: آدرس صفحه
	* @return 	*/
	static function ComparePassedDataWithDB($CurRecID, $FacilityName, $GroupID, $OrderNo, $PageAddress)
	{
		$ret = "";
		$obj = new be_SystemFacilities();
		$obj->LoadDataFromDatabase($CurRecID);
		if($FacilityName!=$obj->FacilityName)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "عنوان";
		}
		if($GroupID!=$obj->GroupID)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "گروه";
		}
		if($OrderNo!=$obj->OrderNo)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "ترتیب";
		}
		if($PageAddress!=$obj->PageAddress)
		{
			if($ret!="")
				$ret .= " - ";
			$ret .= "آدرس صفحه";
		}
		return $ret;
	}

	// edited by naghme mohammadifar
	function ShowSummary($RecID)
	{
        $ret = "<br>";
        $ret .= "<table class=\"table table-bordered table-dark\">";
        $obj = new be_SystemFacilities();
        $obj->LoadDataFromDatabase($RecID);
        $ret .= "<thead>";
        $ret .= "<tr >";
        $ret .= "<th class='text-lg-center'> <strong>";
        if(UI_LANGUAGE == 'FA') {
            $ret .= htmlentities($obj->FacilityName, ENT_QUOTES, 'UTF-8');
        }
        else {
            $ret .= htmlentities($obj->EFacilityName, ENT_QUOTES, 'UTF-8');
        }
        $ret .= "</strong> </th>";
        $ret .= "</tr>";
        $ret .= "</thead>";
        $ret .= "</table>";
        return $ret;
	}

	//-------------------------------
	function ShowTabs($RecID, $CurrentPageName)
	{
		$ret = "<table align=\"center\" width=\"90%\" border=\"1\" cellspacing=\"0\">";
 		$ret .= "<tr>";
		$ret .= "<td width=\"33%\" ";
		if($CurrentPageName=="NewSystemFacilities")
			$ret .= "bgcolor=\"#cccccc\" ";
		$ret .= "><a href='NewSystemFacilities.php?UpdateID=".$RecID."'>مشخصات اصلی</a></td>";
		$ret .= "<td width=\"33%\" ";
		if($CurrentPageName=="ManageUserFacilities")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageUserFacilities.php?FacilityID=".$RecID."'></a></td>";
		$ret .= "<td width=\"33%\" ";
		if($CurrentPageName=="ManageFacilityPages")
 			$ret .= " bgcolor=\"#cccccc\" ";
		$ret .= "><a href='ManageFacilityPages.php?FacilityID=".$RecID."'></a></td>";
		$ret .= "</table>";
		return $ret;
	}
}
?>