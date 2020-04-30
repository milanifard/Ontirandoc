<?php
/* Ferdowsi University Framework   v1.0
 * Abstract Data Layer  based on   PDO  library
 * Author  : A.Baniasadi 
 * Date    : 1388-12
 * 
 */
 ini_set('memory_limit', '256M');

class pdodb {
  const PDO_CONNECTION_ERROR =  ' خطا در اتصال به بانک اطلاعاتی ';
  const PDO_STATEMENT_ERROR  =  '  خطا در پرس و جو از بانک اطلاعاتی ';
  const ARABIC_YA = 'ي';
  const PERSIAN_YA = 'ی';
  const ARABIC_KAF = 'ك';
  const PERSIAN_KAF = 'ک';
  const DEBUG_MODE = true;
  private $driver ;
  private $host ;
  public  $default_db ;
  private $user ;
  private $pass ;
  private $dsn;
  private $pdo;  
  private $statement;
  private $AffectedRows;
  static  $pdoinstance = false;
  static  $logQuery = false;
  
  public function getDefaultDB(){
       return $this->default_db;
  }

/* : constructor */
/* public: some trivial reporting */
  private function __construct($_host = "", $_user = "", $_pass = "", $_default_db = "",$_driver="",$debug=self::DEBUG_MODE) {
    $this->dsn = "mysql:dbname=$_default_db;host=$_host";
    $this->default_db = $_default_db;
    $this->connect($_driver,$_host,$_user,$_pass,$_default_db,$debug);    
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
  public static function getInstance($_host = "", $_user = "", $_pass = "", $_default_db = "",$_driver="") {
    if ("" == $_driver)      $_driver = sys_config::$db_server['driver'];
    if ("" == $_host)        $_host = sys_config::$db_server['host'];
    if ("" == $_user)        $_user = sys_config::$db_server['user'];
    if ("" == $_pass)        $_pass = sys_config::$db_server['pass'];
    if ("" == $_default_db)  $_default_db = sys_config::$db_server['database'];       
    
    if (!@pdodb::$pdoinstance[$_host.$_user.$_default_db]) {
      pdodb::$pdoinstance[$_host.$_user.$_default_db] = new pdodb($_host,$_user,$_pass,$_default_db,$_driver);
    }
    return pdodb::$pdoinstance[$_host.$_user.$_default_db];
  }
  private function connect($_driver = "", $_host = "", $_user = "", $_pass = "", $_default_db = "",$debug=self::DEBUG_MODE) {
    try{
      if ("" == $_driver)     
        $_driver = $this->driver;
      else                    
        $this->driver = $_driver;
      if ("" == $_host)       $_host = $this->host;
      if ("" == $_user)       $_user = $this->user;
      if ("" == $_pass)       $_pass = $this->pass;
      if ("" == $_default_db) $_default_db = $this->default_db;
                   
      $this->pdo = new pdo($this->dsn , $_user , $_pass, array(PDO::MYSQL_ATTR_INIT_COMMAND =>  "SET NAMES utf8") );
      //$this->pdo->exec("set names 'utf8' ");
      return $this->pdo;
    }
    catch (PDOException $e) {
      print (self::PDO_CONNECTION_ERROR);      
      print ($e->getMessage());
      if ($debug) $this->backtrace();
      exit();      
    }
  }
  //-----------------------------------------------------------new methods
  function Prepare($sqlstr,$debug=true) {//return statement   	
    $this->statement = $this->pdo->prepare($sqlstr);
   	$error_info = $this->pdo->errorInfo();
    if ($error_info[0] !='00000' && $debug ){    	
      print_r($error_info ) . "<br>" . $sqlstr . "<br>" . self::PDO_STATEMENT_ERROR;
      $this->backtrace();
      exit();
    }       
    return $this->statement;
  }
  static function LogQueryToDB($mainQuery,$time,$DBName,$QueryStatus='SUCCESS',$parameter_array=array()){
    if(((isset($_SESSION['User']) && self::$logQuery)) || ($QueryStatus=='FAILED') ){
      $mysql = pdodb::getInstance(config::$db_servers['master']['host'],config::$db_servers['master']['dataanalysis_user']
		                      ,config::$db_servers['master']['dataanalysis_pass'],config::$db_servers['master']['dataanalysis_db']);
      $query = "insert into SystemDBLog (page,query,SerializedParam,UserID,IPAddress,SysCode,ExecuteTime,QueryStatus,DBName)
            values (:page,:query,:SerializedParam,:UserID,:IPAddress,:SysCode,:ExecuteTime,:QueryStatus,:DBName)";
      $st = $mysql->prepare($query);
      $st->execute(array(':UserID'=>$_SESSION['UserID'],':IPAddress'=>$_SESSION['LIPAddress'],
           ':SysCode'=>$_SESSION['SystemCode'],":query"=>$mainQuery,":SerializedParam"=>serialize($parameter_array),
            ':page'=>$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'],':ExecuteTime'=>$time,':QueryStatus'=>$QueryStatus,':DBName'=>$DBName));
    }
    return true;
  }
  function ExecuteStatement($parameter_array,$fetch_mode=PDO::FETCH_ASSOC,$debug=true) {//return statement
    if (!is_array($parameter_array)){
      print(' پارامتر باید آرایه باشد  ');
      exit();
    }
    $trans = array(self::ARABIC_YA => self::PERSIAN_YA , self::ARABIC_KAF => self::PERSIAN_KAF);
    foreach ($parameter_array  as &$value){
      $value = strtr($value , $trans);
    }
    $this->statement->setFetchMode($fetch_mode);
    $startTime = microtime(true);
      //$this->pdo->query("SET profiling = 1");
    $this->statement->execute($parameter_array);
    $endTime = microtime(true);
    $error_info = $this->statement->errorInfo();
      $statement = $this->pdo->query(" SHOW PROFILES ");
      $rec = $statement->fetchall();
    if(pdodb::$logQuery) {


//      $rec = $rec[count($rec)-1];
//      ($rec ? $rec["Duration"] : "");
      self::LogQueryToDB((isset($rec['Query']) ? $rec['Query'] : $this->statement->queryString),($endTime-$startTime),$this->default_db,
        ($error_info[0] !='00000' ? 'FAILED' : 'SUCCESS'),$parameter_array);
    }
    else if ($error_info[0] !='00000') {
        self::LogQueryToDB((isset($rec['Query']) ? $rec['Query'].implode(",",$error_info) : $this->statement->queryString.implode(",",$error_info)),($endTime-$startTime),$this->default_db,
        ($error_info[0] !='00000' ? 'FAILED' : 'SUCCESS'),$parameter_array);
    }
    //if ($error_info[0] !='00000' && $debug){
	if ($error_info[0] !='00000' ){	
      print_r($error_info ) . "<br>" . print_r($parameter_array) . "<br>" . self::PDO_STATEMENT_ERROR;
      $this->backtrace();
      exit();
    }    
    $this->AffectedRows=$this->statement->rowCount();   
    return $this->statement;
 }

  function EnhancedExecute($parameter_array,$fetch_mode=PDO::FETCH_ASSOC,$debug=self::DEBUG_MODE,$execbinary=false) {//return statement
    if (!is_array($parameter_array)){
      print(' پارامتر باید آرایه باشد ');
     //exit();
  }
    if($execbinary==false){
      $trans = array(self::ARABIC_YA => self::PERSIAN_YA , self::ARABIC_KAF => self::PERSIAN_KAF);
      foreach ($parameter_array as &$value){
        $value = strtr($value , $trans);
      }
    }
   $this->statement->setFetchMode($fetch_mode);
   $this->statement->execute($parameter_array);
   $error_info = $this->statement->errorInfo();
//if ($error_info[0] !=\'00000\' && $debug){
//return $this->statement->errorInfo();//print_r($error_info ) . \"<br>\" . print_r($parameter_array) . \"<br>\" . self::PDO_STATEMENT_ERROR;
//$this->backtrace();
//exit();
//}
   $this->AffectedRows=$this->statement->rowCount();
   return $this->statement;
  }
  //-----------------------------------------------------------
  function Execute($sqlstr,$fetch_mode=PDO::FETCH_ASSOC,$debug=true) {      // for backward  compatibility
    $trans = array(self::ARABIC_YA => self::PERSIAN_YA , self::ARABIC_KAF => self::PERSIAN_KAF);
    $startTime = microtime(true);//
    $this->statement = $this->pdo->query(strtr($sqlstr,$trans));
    $endTime = microtime(true);//
    @$this->statement->setFetchMode($fetch_mode);// some query may return 0 record
    $error_info = $this->pdo->errorInfo();//
    if(self::$logQuery){//
      self::LogQueryToDB($sqlstr,($endTime-$startTime),$this->default_db,
                  ($error_info[0] !='00000' ? 'FAILED' : 'SUCCESS'));
    }
    else if ($error_info[0] !='00000') {
	self::LogQueryToDB($sqlstr.implode(",",$error_info),($endTime-$startTime),$this->default_db,
                  ($error_info[0] !='00000' ? 'FAILED' : 'SUCCESS'));
    }
    //if($error_info[0] !='00000' && $debug ){
	if($error_info[0] !='00000'){
      print_r($error_info ) . self::PDO_STATEMENT_ERROR;
      $this->backtrace();
      exit();
    }
    $this->AffectedRows=$this->statement->rowCount(); //      
    return $this->statement;     
  }

   function Execute_ThrowException($sqlstr,$fetch_mode=PDO::FETCH_ASSOC) {     
    $trans = array(self::ARABIC_YA => self::PERSIAN_YA , self::ARABIC_KAF => self::PERSIAN_KAF);
    $this->statement = $this->pdo->query(strtr($sqlstr,$trans));
    //echo $sqlstr."<br>";
   	//$this->statement->setFetchMode($fetch_mode);
    $error_info = $this->pdo->errorInfo();
    if ($error_info[0] !='00000'){
       //print_r($error_info);
       throw new Exception($error_info[2]); /////
    }   
    return $this->statement;     
  }

  function ExecuteScalar($sqlstr,$debug=self::DEBUG_MODE) {    
    $trans = array(self::ARABIC_YA => self::PERSIAN_YA , self::ARABIC_KAF => self::PERSIAN_KAF);
    $startTime = microtime(true);
    $this->AffectedRows = $this->pdo->exec(strtr($sqlstr,$trans));
    $endTime = microtime(true);
    $error_info=$this->pdo->errorInfo();
    if(self::$logQuery) {
       self::LogQueryToDB($sqlstr,($endTime-$startTime),$this->default_db,($error_info[0] !='00000' ? 'FAILED' : 'SUCCESS'));
    }
    if ($error_info[0] !='00000' && $debug ){
      print_r($error_info) . self::PDO_STATEMENT_ERROR;
      $this->backtrace();
      exit();
    }       
    return $this->AffectedRows;     
  }
  //Added by bagheri for inserting or updating binary data (other execute functions use strtr for replace some arabic characters to persian ones and this method corrupts some binary files like photos).
  //Date: 2011-Apr-28
  function ExecuteBinary($sqlstr,$debug=true) {
    $startTime = microtime(true);
    $this->AffectedRows = $this->pdo->exec($sqlstr);
    $endTime = microtime(true);
    $error_info=$this->pdo->errorInfo();
    if(self::$logQuery) {
       self::LogQueryToDB($sqlstr,($endTime-$startTime),$this->default_db,($error_info[0] !='00000' ? 'FAILED' : 'SUCCESS'));
    }
    if ($error_info[0] !='00000' && $debug ){
      print_r($error_info) . self::PDO_STATEMENT_ERROR;
      $this->backtrace();
      exit();
    }
    return $this->AffectedRows;
  }
  function BeginTrans() {
    $this->pdo->exec("SET AUTOCOMMIT=0");
    $this->pdo->exec("START TRANSACTION");
  }
  function CommitTrans() {
    $this->pdo->exec("COMMIT");
    $this->pdo->exec("SET AUTOCOMMIT=1");
  }
  function RollbackTrans() {
    $this->pdo->exec("ROLLBACK");
    $this->pdo->exec("SET AUTOCOMMIT=1");
  }
  function Insert_ID() {
    return $this->pdo->lastInsertId();
  }
  function Affected_Rows(){
    return $this->AffectedRows;
  }
  public function qstr($_str){// for backward  compatibility
    $_str=str_ireplace(array("SELECT","UPDATE","INSERT","DROP","DELETE","UNION","FROM","WHERE"),"",$_str);
    return $this->pdo->quote($_str);
  }
  function audit($task) {
    $this->ExecuteScalar("insert into SysAudit (UserID, ActionDesc, IPAddress, SysCode) " .
	 	             "values('{$_SESSION['UserID']}', '$task', '{$_SESSION['LIPAddress']}', '{$_SESSION['SystemCode']}') ");      
  }
  function Close() {
    dbclass::$instance[$this->host.$this->user.$this->default_db]=null;
    unset(dbclass::$instance[$this->host.$this->user.$this->default_db]);       
  }
  function LogIt($TableName, $UserID, $ActionType, $target, $IPA) {// for backward  compatibility
    $this->ExecuteScalar("insert into $TableName (UserID, target, ActionType, IPAddress) 
           	              values('$UserID', $target, $ActionType, $IPA)");    
    return $this->Insert_ID();
  }
  function session_LogIt($ActionType,$target,$tableName=null) {// for backward  compatibility  	
    if($tableName!==null)
      $this->ExecuteScalar("insert into ".$_SESSION['LogFile']." (UserID, target,action, IPAddress,TableName) 
                                values('{$_SESSION['UserID']}','$target','$ActionType','{$_SESSION['LIPAddress']}','$tableName')");   
    else 
      $this->ExecuteScalar("insert into ".$_SESSION['LogFile']." (UserID, target, ActionType, IPAddress) 
			values('{$_SESSION['UserID']}', $target, $ActionType, {$_SESSION['LIPAddress']})");		
    return $this->Insert_ID();
  }
  function session_UpdateLog( $LogID, $ActionType) {// for backward  compatibility		
    $this->ExecuteScalar( "update ".$_SESSION['LogFile']." set UserID='{$_SESSION['UserID']}', 
                                ActionType=$ActionType, 
                                IPAddress={$_SESSION['LIPAddress']}, 
                                ATS=now() where LogID='$LogID'");		
  }
  function __destruct() {
  }
  public function GetRecord($TableName, $WhereClause='', $SelectWhat='*'){// for backward  compatibility		
    $FQuery = "select $SelectWhat from $TableName".($WhereClause != '' ? " where $WhereClause" : '');		             
    $stmt = $this->Execute($FQuery);
    return $stmt->fetch();    
  }
  public function GetActionDescription($TableName, $SelectWhat, $WhereClause) {// for backward  compatibility
    $FQuery = "select $SelectWhat from $TableName".($WhereClause != '' ? " where $WhereClause" : '');                 
    $stmt = $this->Execute($FQuery);
    $Record = $stmt->fetch();
    return $Record ? $Record[$SelectWhat] : NULL;
  }
  public function GetDomainDescription($DomainName, $DomainVal) {// for backward  compatibility
    $table = DOMAINS;
    $ret_str = NULL;		
    $FQuery = "select description from $table where DomainName='$DomainName' and DomainValue='$DomainVal'";
    $stmt = $this->Execute($FQuery);
    if ($stmt->rowCount() ){
	  $arr = $stmt->fetch();
	  $ret_str = $arr['description'] ;
    }
    return $ret_str;
  }
  public function insertLetter($LetterNo, $RefLetterNo, $SenderCode, $LDay, $LMonth, $LYear, $description) {// for backward  compatibility
    $t = LETTERS;          
    if ($this->letterExists($LetterNo,$LYear))  return -1; // Already exists
    $FQuery = "insert into $t (LetterNo, RefLetterNo, SenderCode, LYear, LMonth, LDay, description, ".
            "LetterStatus) values('$LetterNo', '$RefLetterNo', $SenderCode, $LYear, $LMonth, ".
            "$LDay, '$description', 'REGISTERED')";
    $this->ExecuteScalar($FQuery);
    $LetterID = $this->Insert_ID();
    $ErrMsg='';
    if(($LogID = $this->session_LogIt(LETTER_REGISTERED, $LetterID )) == -1)   return -2; // Log Error!
    $FQuery = "update $t set LogID=$LogID where LetterID=$LetterID";
    $this->ExecuteScalar($FQuery);            
    return $LetterID;
  } // end of member function insertLetter
  function letterExists($LetterNo, $year) {// for backward  compatibility
    $t = LETTERS;		
    $FQuery = "select LetterID from $t where LetterNo='$LetterNo' and LYear = '$year' and LetterStatus <> 'DELETED'";
    $stmt=$this->Execute($FQuery);
    return $stmt->rowCount();
  } 
    
     /**
     * @param mode ADODB_FETCH_NUM,ADODB_FETCH_ASSOC,ADODB_FETCH_BOTH
     */
  public function SetFetchMode($mode=PDO::FETCH_ASSOC){  	
  	/* default  fetch mode is PDO::FETCH_BOTH
  	 * other fetch mode 
  	 *    PDO::FETCH_NUM
  	 *    PDO::FETCH_OBJ
  	 *    PDO::FETCH_BOUND
  	 *    PDO::FETCH_ASSOC
  	 *    PDO::FETCH_CLASS
  	 *    PDO::FETCH_INTO
  	 *    PDO::FETCH_LAZY 
  	 *    and more .... 	
  	*/   
    return $this->statement->setFetchMode($mode);
  }
  private function backtrace(){
  	return print_r(debug_backtrace());
  } 
}// end class
?>
