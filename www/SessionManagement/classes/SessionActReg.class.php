<?php

require_once("SessionHistory.class.php");

class be_SessionActReg
{
	public $RowID;		
	public $SessionPreCommandID;		
	public $ActReg;		

	function be_SessionActReg() {}
}

class manage_SessionActReg
{
    
    	static function GetLastID()
	{
		$mysql = pdodb::getInstance();
		$query = "select max(RowID) as MaxID from sessionmanagement.ActRegister";
        $mysql->Prepare($query);
        $res = $mysql->ExecuteStatement(array());
        if($rec=$res->fetch())
		{
			return $rec["MaxID"];
		}
		return -1;
	}

	static function Added($SessionPreCommandID,$ActReg)
	{
		$k=0;
		$mysql = pdodb::getInstance();
		$query = "insert into sessionmanagement.ActRegister (";		
		$query .= "SessionPreCommandID";
		$query .= ", ActReg";		
		$query .= ") values (";
		$query .= " ? , ?";
		$query .= ")";
   		//print_r($query);//die();
		$ValueListArray = array();
		array_push($ValueListArray,$SessionPreCommandID); 
		array_push($ValueListArray,$ActReg);
		$mysql->Prepare($query);                
		$mysql->ExecuteStatement($ValueListArray);
		$LastID = manage_SessionActReg::GetLastID();
		$mysql->audit("ثبت داده جدید در دستور کار با کد ".$LastID);
		manage_SessionHistory::Add($SessionPreCommandID, $LastID, "PRECOMMAND", "", "ADD");
		return $LastID;
	}
}

?>