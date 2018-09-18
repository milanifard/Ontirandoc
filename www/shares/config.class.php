<?php
define('ROOT_PATH',str_repeat("../",substr_count($_SERVER['SCRIPT_NAME'],'/')-1));
require_once 'dbclass.inc.php';
require_once 'pdodb.class.php';

class config{
  public static $db_servers = array(
    'master' => array( 
    'host'   => 'localhost',
    'driver' => 'mysql',
                
    "lab_user" => 'user1',
    "lab_pass" => 'user1',
    "lab_db"   => 'projectmanagement',

    "dataanalysis_user" => 'user1',
    "dataanalysis_pass" => 'user1',
    "dataanalysis_db"   => 'projectmanagement',

    "formsgenerator_user" => 'user1',
    "formsgenerator_pass" => 'user1',
    "formsgenerator_db"   => 'formsgenerator') 
  );
  public static $display_error = true;
  public static $root_path = ROOT_PATH;
  public static $start_page = 'pm/login.php';

}
?>
