<?php
require_once '../shares/MySql.config.php';
require_once '../adodb/adodb.inc.php';
require_once '../adodb/adodb-pager.inc.php';
require_once '../adodb/adodb-exceptions.inc.php';

class dbclass {
	
	private $driver ;
	private $host ;
	public  $default_db ;
	private $user ;
	private $pass ;
	private $dsn;
	private $adodb;
	static  $instance = false;
	
	/* : constructor */
	/* public: some trivial reporting */
	private function __construct($_host = "", $_user = "", $_pass = "", $_default_db = "",$_driver="") {
		$this->connect($_driver,$_host,$_user,$_pass,$_default_db);
		//register_shutdown_function(array($this, "close"));
	}
/**
 * Enter description here...
 *
 * @param unknown_type $_host
 * @param unknown_type $_user
 * @param unknown_type $_pass
 * @param unknown_type $_default_db
 * @param unknown_type $_driver
 * @return dbclass
 */
	public function getInstance($_host = "", $_user = "", $_pass = "", $_default_db = "",$_driver="") {
        if ("" == $_driver)     $_driver = 'mysql';
        if ("" == $_host)       $_host = MYSQL_NAME;
        if ("" == $_user)       $_user = MYSQL_USERNAME;
        if ("" == $_pass)       $_pass = MYSQL_PASSWORD;
        if ("" == $_default_db) $_default_db = MYSQL_DATABASE;
		if (!@dbclass::$instance[$_host.$_user.$_default_db]) 
		{
    		dbclass::$instance[$_host.$_user.$_default_db] = new dbclass($_host,$_user,$_pass,$_default_db,$_driver);
  		}
  		return dbclass::$instance[$_host.$_user.$_default_db];
	}

     public function ExecuteBinary($sqlstr) {
       try {
         $startTime = microtime(true);
         $res = $this->adodb->Execute($sqlstr);
         $endTime = microtime(true);
         $this->AffectedRows = $this->adodb->Affected_Rows();
         return $res;
       }
       catch (exception $e) {
         $this->RollbackTrans();
         print (' ﺦﻃﺍ ﺩﺭ ﺎﺟﺭﺍی پﺮﺳ ﻭ ﺝﻭ ﺍﺯ ﺏﺎﻧک ﺎﻃﻼﻋﺎﺗی');
         exit ();
       }
     }
	
	private function connect($_driver = "", $_host = "", $_user = "", $_pass = "", $_default_db = "") {
    try{
        if ("" == $_driver)     $_driver = $this->driver;
        if ("" == $_host)       $_host = $this->host;
        if ("" == $_user)       $_user = $this->user;
        if ("" == $_pass)       $_pass = $this->pass;
        if ("" == $_default_db) $_default_db = $this->default_db;
		  $this->adodb = NewADOConnection($_driver);
		  $this->adodb->Connect($_host,$_user,$_pass,$_default_db,true);
		  $this->adodb->Execute("set names 'utf8' ");
		  return $this->adodb;
    }catch (exception $e) {
			print (' خطا در اتصال به بانک اطلاعاتی');
			exit ();
	  }
	}
	function Execute($sqlstr) {
		try {
		  $arabic_ya = "ي";
      $persian_ya = "ی";
      $arabic_kaf = "ك";
      $persian_kaf = "ک";
      $trans = array($arabic_ya => $persian_ya , $arabic_kaf => $persian_kaf);
      return $this->adodb->Execute(strtr($sqlstr,$trans));
		} catch (exception $e) {
			$this->RollbackTrans();
			// please comment 2 lines below in operational environment for security issues
			var_dump($e);
			adodb_backtrace($e->gettrace());
			print (' خطا در اجرای پرس و جو از بانک اطلاعاتی');
			exit ();
		}
	}
	function BeginTrans() {
		$this->adodb->Execute("SET AUTOCOMMIT=0");
		$this->adodb->Execute("START TRANSACTION");
	}
	function CommitTrans() {
		$this->adodb->Execute("COMMIT");
		$this->adodb->Execute("SET AUTOCOMMIT=1");

	}
	function RollbackTrans() {
		$this->adodb->Execute("ROLLBACK");
		$this->adodb->Execute("SET AUTOCOMMIT=1");

	}
	function Insert_ID() {
		return $this->adodb->Insert_ID();
	}
	function Affected_Rows()
	{
		return $this->adodb->Affected_Rows();
	}
	public function qstr($_str){
	  $_str=str_ireplace(array("SELECT","UPDATE","INSERT","DROP","DELETE"),"",$_str);
		return $this->adodb->qstr($_str);
	}
	function audit($task) {
		$Ipaddr = $_SESSION['LIPAddress'];
		if($Ipaddr=="")
			$Ipaddr = 0;
		$this->adodb->Execute("insert into SysAudit (UserID, ActionDesc, IPAddress) " .
		"                  values('{$_SESSION['UserID']}', '$task', '".$Ipaddr."') ");
		/*		$this->adodb->Execute("insert into SysAudit (UserID, ActionDesc) " .
		"                  values('{$_SESSION['UserID']}', '$task') ");
		*/
	}
	function Close() {
		dbclass::$instance[$this->host.$this->user.$this->default_db]=null;
		$this->adodb->Close();
	}
    function LogIt($TableName, $UserID, $ActionType, $target, $IPA) {
		$this->adodb->Execute("insert into $TableName (UserID, target, ActionType, IPAddress) 
			values('$UserID', $target, $ActionType, $IPA)");
		return $this->Insert_ID();
	}
    function session_LogIt($ActionType,$target,$tableName=null) {  	
		if($tableName!==null)
      $this->adodb->Execute("insert into ".$_SESSION['LogFile']." (UserID, target,action, IPAddress,TableName) 
        values('{$_SESSION['UserID']}','$target','$ActionType','{$_SESSION['LIPAddress']}','$tableName')");   
    else 
    $this->adodb->Execute("insert into ".$_SESSION['LogFile']." (UserID, target, ActionType, IPAddress) 
			values('{$_SESSION['UserID']}', $target, $ActionType, {$_SESSION['LIPAddress']})");		
		return $this->Insert_ID();
  }
  function session_UpdateLog( $LogID, $ActionType) {		
		$this->adodb->Execute( "update ".$_SESSION['LogFile']." set UserID='{$_SESSION['UserID']}', 
		                                                            ActionType=$ActionType, 
			                                                          IPAddress={$_SESSION['LIPAddress']}, 
			                                                          ATS=now() where LogID='$LogID'");		
	}
  function __destruct() {
	}
  public function  GetRecord_1($TableName, $WhereClause='', $SelectWhat='*') {
  	/*
  	 * this function has  a bug  "command  out of sysnc  "
  	 */
  	$query = " call  get_field_value( ? , ? , ? )";
  	$stmt = $this->adodb->Prepare($query);  	
  	$rs = $this->adodb->Execute($stmt,array($TableName,$SelectWhat,$WhereClause)); 
  	if ( $rs->RecordCount())
  	   $rs->FetchRow();
  	else
  	  return NULL;  	
  }
  public function GetRecord($TableName, $WhereClause='', $SelectWhat='*'){		
		$FQuery = "select $SelectWhat from $TableName".($WhereClause != '' ? " where $WhereClause" : '');		             
		$rs = $this->Execute($FQuery);
		return $rs->RecordCount() ?  $rs->FetchRow() :  NULL ;
		    
  }
  public function GetActionDescription($TableName, $DescFieldName, $WhereClause) {
		$Record = $this->GetRecord($TableName, $WhereClause, $DescFieldName);
		return $Record ? $Record[$DescFieldName] : NULL;
	}
  public function GetDomainDescription($DomainName, $DomainVal) {
		$table = DOMAINS;
		$ret_str = NULL;		
		$FQuery = "select description from $table where DomainName='$DomainName' and DomainValue='$DomainVal'";
		$rs = $this->adodb->Execute($FQuery);
		if ($rs->RecordCount() )
		{
				$arr = $rs->FetchRow();
				$ret_str = $arr['description'] ;
		}
		return $ret_str;
	}
  public function insertLetter($LetterNo, $RefLetterNo, $SenderCode, $LDay, $LMonth, $LYear, $description) {
    $t = LETTERS;    
    try{      
      if ($this->letterExists($LetterNo,$LYear))
        return -1; // Already exists
      $FQuery = "insert into $t (LetterNo, RefLetterNo, SenderCode, LYear, LMonth, LDay, description, ".
            "LetterStatus) values('$LetterNo', '$RefLetterNo', $SenderCode, $LYear, $LMonth, ".
            "$LDay, '$description', 'REGISTERED')";
      $this->adodb->Execute($FQuery);
      $LetterID = $this->adodb->Insert_ID();
      $ErrMsg='';
      if(($LogID = $this->session_LogIt(LETTER_REGISTERED, $LetterID )) == -1)
        return -2; // Log Error!
      $FQuery = "update $t set LogID=$LogID where LetterID=$LetterID";
      $this->adodb->Execute($FQuery);            
      return $LetterID;
    }catch(exception $e){      
      return -3;
    }
  } // end of member function insertLetter
  function letterExists($LetterNo, $year) {
		$t = LETTERS;		
		$FQuery = "select LetterID from $t where LetterNo='$LetterNo' and LYear = $year and LetterStatus <> 'DELETED'";
		$rs=$this->adodb->Execute($FQuery);
		return $rs->RecordCount();
	} 
    
  /**
     * @param mode ADODB_FETCH_NUM,ADODB_FETCH_ASSOC,ADODB_FETCH_BOTH
  */
  public function SetFetchMode($mode){
      $this->adodb->SetFetchMode($mode);
  }

  public function domain_list($domain_name , $input_name , $default_value, $all_flag=false , $all_string = 'همه موارد  ' ){
  	$_query = " select  description ,  domainvalue from  domains  where DomainName='$domain_name' ";
  	if ($all_flag){
  		$_query .= "  union  select  '$all_string' , 'all'  ";
  	}  	
  	$rs = $this->adodb->Execute($_query);
  	return $rs -> GetMenu2($input_name,$default_value);
  }
}
?>
