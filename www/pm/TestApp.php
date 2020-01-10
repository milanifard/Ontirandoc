<?php 
include("header.inc.php");
include("classes/SystemFacilityGroups.class.php");
include("classes/messages.class.php");
HTMLBegin();
class Test
{
    private static $_tests = array();

    public static function add($callback, $title = "Unnamed Test", $set = "General")
    {
        self::$_tests[] = array("set" => $set, "title" => $title, "callback" => $callback);
    }

    public static function run($before = null, $after = null)
    {
        if ($before) {
            $before($this->_tests);
        }
        $passed = array();
        $failed = array();
        $exceptions = array();
        foreach (self::$_tests as $test) {
            try {
                $result = call_user_func($test["callback"]);
                if ($result)
                    $passed[] = array("set" => $test["set"], "title" => $test["title"]);
                else
                    $failed[] = array("set" => $test["set"], "title" => $test["title"]);
            } catch (Exception $e) {
                $exceptions[] = array("set" => $test["set"], "title" => $test["title"], "type" => get_class($e));
            }
        }
        if ($after) {
            $after($this->_tests);
        }
        return array("passed" => $passed, "failed" => $failed, "exceptions" => $exceptions);
    }
}

Test::add(
  function()
  {
      $obj = new be_SystemFacilityGroups();
      try {
          $obj->LoadDataFromDatabase(1);
          return true;
      }
      catch(Exception $e)
      {
          return false;
      }
  }
  ,"b_SystemFacilityGroups->LoadDataFromDatabase()", "Administration"
);




Test::add(
    function()
    {
        try {
            if(manage_SystemFacilityGroups::GetCount()==0) {
                return true;
            }
            return false;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_SystemFacilityGroups::GetCount()", "Administration"
);

//Mohamad_Ali_Al_Saidi php test for class messages.class.php *** plz be careful !
Test::add(
    function()
    {
        $obj = new be_messages();
        try {
            $obj->LoadDataFromDatabase(1);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"be_messages.class->LoadDataFromDatabase()", "Message"
);

Test::add(
    function()
    {
        $obj = new manage_messages();
        try {
            $obj->GetCount();
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_messages.class->GetCount()", "Message"
);

Test::add(
    function()
    {
        $obj = new manage_messages();
        try {
            $obj->GetLastID();
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_messages.class->GetLastID()", "Message"
);

Test::add(
    function()
    {
        $obj = new manage_messages();
        try {
            $obj->Add("test","","","","","2000/01/01","2000/01/01");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_messages.class->GetLastID()", "Message"
);

Test::add(
    function()
    {
        $obj = new manage_messages();
        try {
            $obj->Update("1","test","","","","","2000/01/01","2000/01/01");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_messages.class->Update()", "Message"
);

Test::add(
    function()
    {
        $obj = new manage_messages();
        try {
            $obj->Remove("1");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_messages.class->Remove()", "Message"
);

Test::add(
    function()
    {
        $obj = new manage_messages();
        try {
            $obj->GetList("1","","MessageID","asc");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_messages.class->GetList()", "Message"
);
Test::add(
    function()
    {
        $obj = new manage_messages();
        try {
            $obj->GetList("1","","MessageID","asc");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_messages.class->GetList()", "Message"
);

Test::add(
    function()
    {
        $obj = new manage_messages();
        try {
            $obj->GetActiveMessages();
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_messages.class->GetActiveMessages()", "Message"
);

Test::add(
    function()
    {
        $obj = new manage_messages();
        try {
            $obj->Search("","","","1","1");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_messages.class->Search()", "Message"
);

Test::add(
    function()
    {
        $obj = new manage_messages();
        try {
            $obj->SearchResultCount("","","");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_messages.class->SearchResultCount()", "Message"
);

Test::add(
    function()
    {
        $obj = new manage_messages();
        try {
            $obj->ComparePassedDataWithDB("","","","","","","","");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_messages.class->ComparePassedDataWithDB()", "Message"
);



//Mohamad_Ali_Al_Saidi php test for class messages.class.php *** plz be careful ! ***

$res = Test::run();
echo "<br>";
echo "<div class='container'>";
echo "<div class='row'><div class='col-12'>";

echo "<table class='table table-bordered'>";
foreach ($res["passed"] as $presult) {
    echo "<tr><td>".$presult["set"]."</td><td>".$presult["title"]."</td><td class='table-success'>Passed</td></tr>";
}
foreach ($res["failed"] as $presult) {
    echo "<tr><td>".$presult["set"]."</td><td>".$presult["title"]."</td><td class='table-danger'>Failed</td></tr>";
}
foreach ($res["exceptions"] as $presult) {
    echo "<tr><td>".$presult["set"]."</td><td>".$presult["title"]."</td><td class='table-info'>Exception</td></tr>";
}

echo "</div></div>";
echo "</div>";
?>
</html>
