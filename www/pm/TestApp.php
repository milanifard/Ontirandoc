<?php 
include("header.inc.php");
include("classes/SystemFacilityGroups.class.php");
include("classes/messages.class.php");
include("classes/terms.class.php");
include("classes/ProjectHistory.class.php");
include("classes/AccountSpecs.class.php");
include("classes/RefrenceTypes.class.php");
include("classes/FacilityPages.class.php");
include("../SessionManagement/classes/SessionActReg.class.php"); // By Arman Ghoreshi
include("classes/TermEquivalentEnglishTerms.class.php");
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


//terms.class.php Unit Tests By Ehsan Amini

Test::add(
    function()
    {
        $obj = new be_terms();
        try {
            $obj->LoadDataFromDatabase(1);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"be_terms->LoadDataFromDatabase()", "Terms"
);

Test::add(
    function()
    {
        $obj = new manage_terms();
        try {
            $obj->GetCount();
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_terms->GetCount()", "Terms"
);

Test::add(
    function()
    {
        $obj = new manage_terms();
        try {
            $obj->GetLastID();
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_terms->GetLastID()", "Terms"
);

Test::add(
    function()
    {
        $obj = new manage_terms();
        try {
            $obj->Add("","");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_terms->Add()", "Terms"
);

Test::add(
    function()
    {
        $obj = new manage_terms();
        try {
            $obj->Update("","","");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_terms->Update()", "Terms"
);


Test::add(
    function()
    {
        $obj = new manage_terms();
        try {
            $obj->Remove("");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_terms->Remove()", "Terms"
);

Test::add(
    function()
    {
        $obj = new manage_terms();
        try {
            $obj->GetList("","","TermTitle","asc");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_terms->GetList()", "Terms"
);

Test::add(
    function()
    {
        $obj = new manage_terms();
        try {
            $obj->Search("","","","1","1");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_terms->Search()", "Terms"
);

Test::add(
    function()
    {
        $obj = new manage_terms();
        try {
            $obj->SearchResultCount("","","");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_terms->SearchResultCount()", "Terms"
);

Test::add(
    function()
    {
        $obj = new manage_terms();
        try {
            $obj->ComparePassedDataWithDB("","","");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_terms->ComparePassedDataWithDB()", "Terms"
);

Test::add(
    function()
    {
        $obj = new manage_terms();
        try {
            $obj->ShowSummary("");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_terms->ShowSummary()", "Terms"
);

Test::add(
    function()
    {
        $obj = new manage_terms();
        try {
            $obj->ShowTabs("","");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_terms->ShowTabs()", "Terms"
);

//End of terms.class.php Unit Tests By Ehsan Amini

//ProjectHistory.class.php - Hossein lotfi - Start


Test::add(
    function()
    {
        $obj = new be_ProjectHistory();
        try {
            $obj->LoadDataFromDatabase(1);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"be_ProjectHistory->LoadDataFromDatabase()", "ProjectHistory"
);

Test::add(
    function()
    {
        $obj = new manage_ProjectHistory();
        try {
            $obj->GetCount(1);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_ProjectHistory->GetCount()", "ProjectHistory"
);

Test::add(
    function()
    {
        $obj = new manage_ProjectHistory();
        try {
            $obj->GetLastID();
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_ProjectHistory->GetLastID()", "ProjectHistory"
);

Test::add(
    function()
    {
        $obj = new manage_ProjectHistory();
        try {
            $obj->Add("", "", "", "", "");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_ProjectHistory->Add()", "ProjectHistory"
);

Test::add(
    function()
    {
        $obj = new manage_ProjectHistory();
        try {
            $obj->GetList("", "", "", "ProjectID", "");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_ProjectHistory->GetList()", "ProjectHistory"
);

Test::add(
    function()
    {
        $obj = new manage_ProjectHistory();
        try {
            $obj->Search("", "", "", "", "");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_ProjectHistory->Search()", "ProjectHistory"
);


//ProjectHistory.class.php - Hossein Lotfi - Finish

//AccountSpecs.class.php -Ali Noori - Start
Test::add(                  #1
        function ()
        {
            $obj=new be_AccountSpecs();
            try{
                $obj->LoadDataFromDatabase(1);
                return true;
            }
            catch (Exception $e)
            {
                return false;
            }
        }
    ,"be_AccountSpecs.class->LoadDataFromDatabase()", "AccountSpecs"
);
Test::add(                  #2
        function ()
        {
            try
            {
                if(manage_AccountSpecs::GetCount()==0) {
                    return true;
                }
                return false;
            }
            catch (Exception $e)
            {
                return false;
            }
        }
    ,"manage_AccountSpecs->GetCount()", "AccountSpecs"
);
Test::add(                  #3
        function ()
        {
            try
            {
                if(manage_AccountSpecs::GetLastID()==-1) {
                    return true;
                }
                return false;
            }
            catch(Exception $e)
            {
                return false;
            }
        }
    ,"manage_AccountSpecs->GetLastID()", "AccountSpecs"
);
Test::add(                  #4
        function()
        {
            try
            {
                if(manage_AccountSpecs::Add("ali","123","alavi")>-1){//return lastID we assume that lastID is always greater than zero
                    return true;
                }
                return false;
            }
            catch(Exception $e)
            {
                return false;
            }
        }
    ,"manage_AccountSpecs->Add()", "AccountSpecs"
);
Test::add(                  #5
        function ()
        {
            try
            {
                $tid = manage_AccountSpecs::Add("aliTest5", "123555", "alavi");
                manage_AccountSpecs::Update($tid,"ali","123","alavi");//it does not return anything
                return true;
            }
            catch (Exception $e)
            {
                return false;
            }
        }
    ,"manage_AccountSpecs->Update()", "AccountSpecs"
);
Test::add(                  #6
        function ()
        {
            try
            {

                $tid = manage_AccountSpecs::Add("aliTest6","123555","alavi");
                manage_AccountSpecs::Remove($tid);
                /*
                 MGhayour:
                    WTF!!
                    you just removed the main testing account of omid, omid3000 :/

                    i fixed it, we add a user and remove him
                    for whom that (omid, omid3000) dosent work for him

                    plz add this record to your DB, or restore DB backup
                    Table: projectmanagement/accountspecs
                    INSERT INTO `AccountSpecs` VALUES (1,'omid','30f9dd65de612bfe458a871b90eabb3026c9fb29',1,'omid')
                */
                return true;
            }
            catch (Exception $e)
            {
                return false;
            }
        }
    ,"manage_AccountSpecs->Remove()", "AccountSpecs"
);
Test::add(                  #7
    function ()
    {

        try
        {
            if(is_array(manage_AccountSpecs::GetList())){//if return type is array
                return true;
            }
            return false;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_AccountSpecs->GatList()", "AccountSpecs"
);
Test::add(                  #8
    function ()
    {
        try
        {
            if(manage_AccountSpecs::GetComboBoxOptions()!=''){
                return true;
            }
            return false;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_AccountSpecs->GetComboBoxOptions()", "AccountSpecs"
);
Test::add(                      #9
    function ()
    {
        try
        {
            if(manage_AccountSpecs::ComparePassedDataWithDB("1","ali","123","alavi")!=''){
                return true;
            }
            return false;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_AccountSpecs->ComparePassedDataWithDB()", "AccountSpecs"
);

//AccountSpecs.class.php -Ali Noori - Finish

//RefrenceTypes.class.php -Hoormazd Ranjbar - Start
Test::add(              #1
        function ()
        {
            $obj = new be_RefrenceTypes();
            try
            {
                $obj->LoadDataFromDatabase(1);
                return true;
            }
            catch (Exception $e)
            {
                return false;
            }
        }
    , "be_RefrenceTypes.class->LoadDataFromDatabase", "RefrenceTypes"
);
Test::add(              #2
        function ()
        {
            try
            {
                manage_RefrenceTypes::Add("1", "RefExpTitle");
                if(manage_RefrenceTypes::GetCount("1")>=0) {
                    return true;
                }
                return false;
            }
            catch (Exception $e)
            {
                return false;
            }
        }
        ,"manage_RefrenceTypes->GetCount()", "RefrenceTypes"
);
Test::add(          #3
        function ()
        {
            try
            {
                manage_RefrenceTypes::Add("1", "RefExpTitle");
                if(manage_RefrenceTypes::GetLastID()==-1)
                {
                    return false;
                }
                return true;
            }
            catch (Exception $e)
            {
                return false;
            }
        }
        ,"manage_RefrenceTypes->GetLastID()", "RefrenceTypes"
);
Test::add(          #4
        function()
        {
            try
            {
                if(manage_RefrenceTypes::Add("1","RefExpTitle")>-1)
                {
                    return true;
                }
                return false;
            }
            catch (Exception $e)
            {
                return false;
            }
        }
    ,"manage_RefrenceTypes->Add()", "RefrenceTypes"
);
Test::add(          #5
    function ()
    {
        try
        {
            manage_RefrenceTypes::Update("1","RefExpTitle");
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_RefrenceTypes->Update()", "RefrenceTypes"
);
Test::add(                  #6
    function ()
    {
        try
        {
            manage_RefrenceTypes::Remove("1");
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_RefrenceTypes->Remove()", "RefrenceTypes"
);
Test::add(                  #7
    function ()
    {

        try
        {
            if(is_array(manage_RefrenceTypes::GetList("1"))){
                return true;
            }
            return false;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_RefrenceTypes->GatList()", "RefrenceTypes"
);
Test::add(                      #8
    function ()
    {
        try
        {
            if(manage_RefrenceTypes::ComparePassedDataWithDB("1","RefExpTitle")!=''){
                return true;
            }
            return false;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_RefrenceTypes->ComparePassedDataWithDB()", "RefrenceTypes"
);
//RefrenceTypes.class.php -Hoormazd Ranjbar - Finish


//-------------------------------------------------------------------------------------------
//--------------------FacilityPages.class.php  by Naghme Mohammadifar------------------------
//-------------------------------------Start-------------------------------------------------

Test::add(
    function()
    {
        $obj = new be_FacilityPages();
        try {
            $obj->LoadDataFromDatabase(1);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"be_FacilityPages.class->LoadDataFromDatabase()", "Message"
);
Test::add(
    function ()
    {
        try
        {
            if(manage_FacilityPages::GetCount(1)==0) {
                return false;
            }
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_FacilityPages.class->GetCount()", "Message"
);
Test::add(
    function ()
    {
        try
        {
            if(manage_FacilityPages::GetLastID()==-1) {
                return false;
            }
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_FacilityPages->GetLastID()", "Message"
);

Test::add(
        function()
        {
            try
            {
                if(manage_FacilityPages::Add("1","pageName")>-1)
                {
                    return true;
                }
                return false;
            }
            catch (Exception $e)
            {
                return false;
            }
}
    ,"manage_FacilityPages->Add()", "Message"
);
Test::add(
    function ()
    {
        try
        {
            manage_FacilityPages::Update("1","pageName");
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_FacilityPages->Update()", "Message"
);
Test::add(
    function ()
    {
        try
        {
            manage_FacilityPages::Remove("1");
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_FacilityPages->Remove()", "Message"
);
Test::add(
    function ()
    {

        try
        {
            if(is_array(manage_FacilityPages::GetList("1"))){
                return true;
            }
            return false;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_FacilityPages->GetList()", "Message"
);
Test::add(
    function ()
    {
        try
        {
            if(manage_FacilityPages::ComparePassedDataWithDB("1","PageName")!=''){
                return true;
            }
            return false;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_FacilityPages->ComparePassedDataWithDB()", "Message"
);
//---------------------------END OF FacilityPages.class.php -----------------------------------------------------

// ========================================+ Arman Ghoreshi +==============================================
// ========================== SessionManagement/classes/SessionActReg.class.php ===========================
Test::add(
        function()
        {
            try{
                if (manage_SessionActReg::GetLastID() != -1)
                    return true;
                return false;
            }
            catch (Exception $e)
            {
                return false;
            }
        }
        ,"SessionManagement/classes/SessionActReg->GetLastID()"
        ,"Message"
);
Test::add(
    function()
    {
        try{
            if (manage_SessionActReg::Added())
                return true;
            return false;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"SessionManagement/classes/SessionActReg->Added()"
    ,"Message"
);
// ==============================================END=======================================================

// ---------------------- TermEquivalentEnglishTerms.class.php by Diba Aminshahidi ------------------------

Test::add(
        function ()
        {
            $obj = new be_TermEquivalentEnglishTerms();
            try{
                $obj->LoadDataFromDatabase(1);
                return true;
            }
            catch (Exception $e)
            {
                return false;
            }
        }
        , "TermEquivalentEnglishTerms->LoadDataFromDatabase()"
        , "Message"
);

Test::add(
    function ()
    {
        try{
            if(manage_TermEquivalentEnglishTerms::GetCount(1)==0) {
                return false;
            }
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    , "TermEquivalentEnglishTerms->GetCount()"
    , "Message"
);

Test::add(
    function ()
    {
        try{
            if(manage_TermEquivalentEnglishTerms::GetLastID()==-1) {
                return false;
            }
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    , "TermEquivalentEnglishTerms->GetLastID()"
    , "Message"
);

Test::add(
    function ()
    {
        try{
            if(manage_TermEquivalentEnglishTerms::Add("1","Term"))
            {
                return true;
            }
            return false;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    , "TermEquivalentEnglishTerms->Add()"
    , "Message"
);

Test::add(
    function ()
    {
        try{
            manage_TermEquivalentEnglishTerms::Update("1","TermUpdate");
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    , "TermEquivalentEnglishTerms->Update()"
    , "Message"
);

Test::add(
    function ()
    {
        try{
            if(is_array(manage_TermEquivalentEnglishTerms::GetList("1"))){
                return true;
            }
            return false;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    , "TermEquivalentEnglishTerms->GetList()"
    , "Message"
);

Test::add(
    function ()
    {
        try{
            if(manage_TermEquivalentEnglishTerms::ComparePassedDataWithDB("1","TermUpdate")!=''){
                return true;
            }
            return false;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    , "TermEquivalentEnglishTerms->ComparePassedDataWithDB()"
    , "Message"
);

Test::add(
    function ()
    {
        try{
            manage_TermEquivalentEnglishTerms::Remove("1");
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    , "TermEquivalentEnglishTerms->Remove()"
    , "Message"
);
// --------------------------------------------- END ------------------------------------------------------

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
