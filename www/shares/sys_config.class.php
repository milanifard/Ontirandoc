<?php
require_once 'config.class.php';
class sys_config{
	 public static $db_server = array (
	          "driver"   => "",
	          "host"     => "",
	          "database" => "",
	          "user"     => "",
	          "pass"     => ""
	 );
	 public static $page_authorize = true;
}

sys_config::$db_server = array (
       "driver" => config::$db_servers['master']["driver"],
       "host" => config::$db_servers['master']["host"],
       "database" => config::$db_servers['master']["lab_db"],
       "user" => config::$db_servers['master']["lab_user"],
       "pass" => config::$db_servers['master']["lab_pass"]
   );

class FormsGeneratorDB
{
  const DB_NAME = 'formsgenerator';
}

class EducUser
{
  public $PersonID;
  function EducUser()
  {
  }
}

?>
