<?php 
include("header.inc.php");
include_once("classes/SystemFacilityGroups.class.php");
include_once("classes/messages.class.php");
include_once("classes/terms.class.php");
include_once("classes/ProjectHistory.class.php");
include_once("classes/AccountSpecs.class.php");
include_once("classes/RefrenceTypes.class.php");
include_once("classes/FacilityPages.class.php");
include_once("../SessionManagement/classes/SessionActReg.class.php"); // By Arman Ghoreshi
include_once("classes/TermEquivalentEnglishTerms.class.php");
include_once("classes/payments.class.php");
include_once("classes/UserFacilities.class.php"); //by mostafaghr
include_once("classes/UserFacilities.class.php"); //by navidbeta
include("classes/OntologyValidationExperts.php"); //by Mohammad Kahani
include_once("classes/OntologyPropertyLabels.class.php");//by kourosh ahmadzadeh ataei
include_once("classes/ProjectTaskActivityTypes.class.php"); // By AMINAG
include_once("classes/projectsSecurity.class.php"); // By Javad Mahdavian
include_once("../SessionManagement/classes/UniversitySessionsSecurity.class.php");//By Amir Karami
include_once("../SessionManagement/classes/ResearchProject.class.php");//by Mohammad Afsharian Shandiz
include_once("classes/persons.class.php"); //by Sara Bolouri Bazaz
include_once("classes/ProjectDocuments.class.php"); // by Samin Hazeri
include_once("classes/OntologyMergeProjectMembers.class.php"); // by Mahdi Ghayour
include_once("classes/OntologyClassLabels.class.php");
include_once("classes/ProjectTaskRequisites.class.php");

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

//Mohamad_Ali_Al_Saidi php test for class messages.class.php *** plz be careful !ma
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
                    if (omid, omid3000) dosent work you

                    add this record to your DB, or restore DB backup
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
/*
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
*/
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

// Payments.class.php - Sajjad Iranmanesh - begin

Test::add(
    function()
    {
        try
        {
            if(manage_payments::Add(1, 1000, "2000/01/01", "CASH", "Server CX11", "Some blob", "Invoice11.pdf"))
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
    ,"manage_payments->Add()", "payments"
);

Test::add(
    function()
    {
        try{
            $obj = new be_payments();
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
        catch (Exception $e)
        {
            return false;
        }
    }, "be_payments.class->LoadDataFromDatabase()", "payments"
);

Test::add(
    function ()
    {
        try
        {
            if(manage_payments::GetLastID()==-1)
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
    ,"manage_payments->GetLastID()", "payments"
);

Test::add(
    function()
    {
        try
        {
            if(manage_payments::Update(1, 2000, "2020/01/01", "CASH", "Server CX11", "Some blob 1", "Invoice111.pdf"))
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
    ,"manage_payments->Update()", "payments"
);

Test::add(          #4
    function()
    {
        try
        {
            if(manage_payments::GetList(1))
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
    ,"manage_payments->GetList()", "Payments"
);

Test::add(          #4
    function()
    {
        try
        {
            if(manage_payments::ComparePassedDataWithDB(1, 2000, "2020/01/01","CASH", "Server CX11"))
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
    ,"manage_payments->ComparePassedDataWithDB()", "payments"
);

Test::add(
    function()
    {
        try
        {
            if(manage_payments::Remove(1))
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
    ,"manage_payments->Remove()", "payments"
);
// Payments.class.php - Sajjad Iranmanesh - end




// FormManagers.class.php - Alireza Imani - begin
/*
Test::add(
    function()
    {
        $obj = new be_FormManager();
        try {
            $obj->LoadDataFromDatabase(1);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"be_FormManager.class->LoadDataFromDatabase()", "FormManagers"
);
Test::add(
    function ()
    {
        try
        {
            if(manage_FormManagers::GetLastID()!=-1)
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
    ,"manage_FormManagers->GetLastID()", "FormManagers"
);
Test::add(
    function ()
    {
        try
        {
            if(manage_FormManagers::Add("0","0","FULL")==1)
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
    ,"manage_FormManagers->Add()", "FormManagers"
);
Test::add(
    function ()
    {
        try
        {
            if(manage_FormManagers::Update("0","0","DATA")==1)
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
    ,"manage_FormManagers->Update()", "FormManagers"
);
Test::add(
    function()
    {
        try
        {
            if(manage_FormManagers::Remove(1))
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
    ,"manage_FormManagers->Remove()", "FormManagers"
);
Test::add(
    function()
    {
        try
        {
            if(manage_FormManagers::GetList(1))
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
    ,"manage_FormManagers->GetList()", "FormManagers"
);
Test::add(
    function()
    {
        try
        {
            if(manage_FormManagers::GetRows(1))
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
    ,"manage_FormManagers->GetRows()", "FormManagers"
);
*/

// FormManagers.class.php - Alireza Imani - end


// UserFacilities.class.php - navidbeta - start

Test::add(
    function()
    {
        $obj = new be_UserFacilities();
        try {
            $obj->LoadDataFromDatabase(1);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"be_UserFacilities->LoadDataFromDatabase()", "UserFacilities"
);

Test::add(
    function()
    {
        try{
            manage_UserFacilities::HasAccess("1" , "1");
            return true;
            
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_UserFacilities->HasAccess()","UserFacilities"
);

Test::add(
    function()
    {
        try{
            manage_UserFacilities::GetCount("1");
            return true;
            
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_UserFacilities->GetCount()","UserFacilities"
);

Test::add(
    function()
    {
        try {
            manage_UserFacilities::GetLastID();
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_UserFacilities->GetLastID()", "UserFacilities"
);

Test::add(
    function()
    {
        try{
            manage_UserFacilities::RemoveAllUserFacilities("1");
            return true;
            
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_UserFacilities->RemoveAllUserFacilities()","UserFacilities"
);

Test::add(
    function()
    {
        try{
            manage_UserFacilities::Add("1" , "1");
            return true;
            
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_UserFacilities->Add()","UserFacilities"
);

Test::add(
    function()
    {
        try{
     \       manage_UserFacilities::Update("1" , "1");
            return true;
            
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_UserFacilities->Update()","UserFacilities"
);

Test::add(
    function()
    {
        try{
            manage_UserFacilities::Remove("1");
            return true;
            
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_UserFacilities->Remove()","UserFacilities"
);

Test::add(
    function ()
    {

        try
        {
            if(is_array(manage_UserFacilities::GetList("1"))){
                return true;
            }
            return false;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_UserFacilities->GetList()", "UserFacilities"
);

Test::add(
    function()
    {
        try {
            manage_UserFacilities::Search("","","","");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_UserFacilities->Search()", "UserFacilities"
);

Test::add(
    function()
    {
        try {
            manage_UserFacilities::SearchResultCount("","","","");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_UserFacilities->SearchResultCount()", "UserFacilities"
);

Test::add(
    function()
    {
        try {
            manage_UserFacilities::ComparePassedDataWithDB("","");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_UserFacilities->ComparePassedDataWithDB()", "UserFacilities"
);

// UserFacilities.class.php - navidbeta - end

//OntologyClassLabels.class.php by Alireza Forghani Toosi
Test::add(
    function(){
        try {
            be_OntologyClassLabels::LoadDataFromDatabase(1);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
);

Test::add(
    function(){
        try {
            manage_OntologyClassLabels::GetCount(1);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
);

Test::add(
    function(){
        try {
            manage_OntologyClassLabels::GetLastID();
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
);

Test::add(
    function(){
        try {
            manage_OntologyClassLabels::Add(1, "label");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
);

Test::add(
    function(){
        try {
            manage_OntologyClassLabels::GetFirstLabel(1);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
);

Test::add(
    function(){
        try {
            manage_OntologyClassLabels::UpdateOrInsertFirstLabel(1, "label");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
);

Test::add(
    function(){
        try {
            manage_OntologyClassLabels::Update(1, "label");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
);

Test::add(
    function(){
        try {
            manage_OntologyClassLabels::Remove(1);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
);

Test::add(
    function(){
        try {
            manage_OntologyClassLabels::GetList(1);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
);

Test::add(
    function(){
        try {
            manage_OntologyClassLabels::ComparePassedDataWithDB(1, "label");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
);

##################################################################
#                                                                #
#  OntologyValidationExperts.class.php - Mohammad Kahani - Start #
#                                                                #
##################################################################
/*
Test::add(
    function()
    {
        $obj = new be_OntologyValidationExperts();
        try {
            if ($obj->LoadDataFromDatabase(1)){
            return true;
            }
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"be_OntologyValidationExperts.class->LoadDataFromDatabase()", "OntologyValidationExperts"
);
Test::add(
    function()
    {
        try {
            if(manage_OntologyValidationExperts::GetCount(1)==0){
                return true;
            }
            else{
                return false;
            }
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_OntologyValidationExperts->getCount()", "OntologyValidationExperts"
);

Test::add(
    function(){
        try{
            if(manage_OntologyValidationExperts::GetLastID()!=-1){
                return true;
            }
            else{
                return false;
            }
        }
        catch(Exception $e){
            return false;
        }
    }
    ,"manage_OntologyValidationExperts->GetLastID()","OntologyValidationExperts"
);

Test::add(
    function(){
        $obj = new manage_OntologyValidationExperts();
        try{
            $obj -> Add("","","","","");
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }
    ,"manage_OntologyValidationExperts->Add()","OntologyValidationExperts"
);


Test::add(
    function(){
        $obj = new manage_OntologyValidationExperts();
        try{
            $obj -> Update("","","","","");
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }
    ,"manage_OntologyValidationExperts->Update()","OntologyValidationExperts"
);


Test::add(
    function(){
        $obj = new manage_OntologyValidationExperts();
        try{
            $obj -> Remove(1);
            return true;
        }
        catch(Exception $e){
            return false;
        }
    }
    ,"manage_OntologyValidationExperts->Remove()","OntologyValidationExperts"
);


Test::add(
    function()
    {
        $obj = new manage_OntologyValidationExperts();
        try {
            $obj->ComparePassedDataWithDB("","","","","");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"manage_OntologyValidationExperts.class->ComparePassedDataWithDB()", "OntologyValidationExperts"
);
*/

##################################################################
#                                                                #
#  OntologyValidationExperts.class.php - Mohammad Kahani - End   #
#                                                                #
##################################################################


//OntologyClassLabels.class.php -kourosh ahamadzadeh ataei -begin

Test::add(
    function()
    {
        $obj = new be_OntologyClassLabels;
        try {
            $obj->LoadDataFromDatabase(1);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"be_OntologyClassLabels.class->LoadDataFromDatabase()", "OntologyClassLabels"
);
Test::add(
        function ()
        {
            try {

                manage_OntologyClassLabels::GetCount(1);
                return true;
            }
            catch (Exception $e)
            {
                return false;
            }
        }
    ,"manage_OntologyClassLabels.class->GetCount()", "OntologyClassLabels"

);
Test::add(
    function ()
    {
        try {

            manage_OntologyClassLabels::GetLastID();
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_OntologyClassLabels.class->GetLastID()", "OntologyClassLabels"

);
Test::add(
    function ()
    {
        try {

            manage_OntologyClassLabels::Add(1,"");
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_OntologyClassLabels.class->Add()", "OntologyClassLabels"

);
Test::add(
    function ()
    {
        try {

            manage_OntologyClassLabels::GetFirstLabel(1);
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_OntologyClassLabels.class->GetFirstLabel()", "OntologyClassLabels"

);
Test::add(
    function ()
    {
        try {

            manage_OntologyClassLabels::UpdateOrInsertFirstLabel(1,"");
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_OntologyClassLabels.class->UpdateOrInsertFirstLabel()", "OntologyClassLabels"

);
Test::add(
    function ()
    {
        try {

            manage_OntologyClassLabels::Update(1,"");
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_OntologyClassLabels.class->Update()", "OntologyClassLabels"

);
Test::add(
    function ()
    {
        try {

            manage_OntologyClassLabels::Remove(1);
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_OntologyClassLabels.class->Remove()", "message"

);

Test::add(
    function ()
    {
        $obj=new manage_OntologyClassLabels;
        try {

            if(is_array(manage_OntologyClassLabels::GetList(1)))
                return true;
            else
                return false;

        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_OntologyClassLabels.class->Update()", "OntologyClassLabels"

);
Test::add(
    function ()
    {
        try {

            manage_OntologyClassLabels::ComparePassedDataWithDB(1,"");
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_OntologyClassLabels.class->ComparePassedDataWithDB()", "OntologyClassLabels"

);

//OntologyClassLabels.class.php -kourosh ahamadzadeh ataei -end



//_________________ProjectTaskActivityTypes.class.php AMIN ALIZADEH _________________
Test::add(function(){
    $obj = new be_ProjectTaskActivityTypes();
    try{
    $res = $obj->LoadDataFromDatabase(1);
    if($res){
        return true;
    }
    return false;
    }
    catch (Exception $e){
        return false;
    }
},"be_ProjectTaskActivityTypes->LoadDataFromDatabase()","ProjectTaskActivityTypes");

Test::add(function(){
    $obj = new manage_ProjectTaskActivityTypes();
    try{
        $res = $obj->GetCount(1);
        return true;
    }
    catch (Exception $e){
        return false;
    }
},"manage_ProjectTaskActivityTypes->GetCount()","ProjectTaskActivityTypes");

Test::add(function(){
    $obj = new manage_ProjectTaskActivityTypes();
    try{
        $res = $obj->GetLastID();
        echo $res;
        if($res)
            return true;
        return false;
    }
    catch (Exception $e){
        return false;
    }
},"manage_ProjectTaskActivityTypes->GetLastID()","ProjectTaskActivityTypes");

Test::add(function(){
    $obj = new manage_ProjectTaskActivityTypes();
    try{
        $res = $obj->Add("Bala bala bala",  $obj->GetLastID() + 1);
        if($res)
            return true;
        return true;
    }
    catch (Exception $e){
        return false;
    }
},"manage_ProjectTaskActivityTypes->Add(()","ProjectTaskActivityTypes");

Test::add(function(){
    try{
        $obj = new manage_ProjectTaskActivityTypes();
        $res = $obj->Update($obj->GetLastID(), "Bala bala bala");
        return true;
    }
    catch (Exception $e){
        return false;
    }
},"manage_ProjectTaskActivityTypes->Update(()","ProjectTaskActivityTypes");

Test::add(function(){
    try{
        $obj = new manage_ProjectTaskActivityTypes();
        $res = $obj->Remove($obj->GetLastID());
        if($res)
            return true;
        return false;
    }
    catch (Exception $e){
        return false;
    }
},"manage_ProjectTaskActivityTypes->Remove(()","ProjectTaskActivityTypes");


Test::add(function(){
    try{
        $obj = new manage_ProjectTaskActivityTypes();
//        $res = $obj->GetList($obj->GetLastID());
//        if($res)
//            return true;
        /* The code contains fatal error so it is a failure without running */
        /* I comment it so it won't cause stopping other tests*/
        return false;
    }
    catch (Exception $e){
        return false;
    }
},"manage_ProjectTaskActivityTypes->GetList(()","ProjectTaskActivityTypes");

Test::add(function(){
    try{
        $obj = new manage_ProjectTaskActivityTypes();
        $res = $obj->CreateSelectOptions($obj->GetLastID());
        if($res)
            return true;
        return false;
    }
    catch (Exception $e){
        return false;
    }
},"manage_ProjectTaskActivityTypes->CreateSelectOptions(()","ProjectTaskActivityTypes");

//_______________________________END__________________________________________________



// ProjectTaskRequisites.class.php test by Mostafa Ghofrani

Test::add(
    function()
    {
        $obj = new be_ProjectTaskRequisites();
        try {
            $obj->LoadDataFromDatabase(1);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"be_ProjectTaskRequisites->LoadDataFromDatabase()", "UserFacilities"
);

Test::add(
    function()
    {
        try{
            manage_ProjectTaskRequisites::GetCount("1");
            return true;
            
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_ProjectTaskRequisites->GetCount()","UserFacilities"
);

Test::add(
    function()
    {
        try{
            manage_ProjectTaskRequisites::GetLastID();
            return true;
            
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_ProjectTaskRequisites->GetLastID()","UserFacilities"
);

Test::add(
    function()
    {
        try{
            manage_ProjectTaskRequisites::Add("1", "1");
            return true;
            
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_ProjectTaskRequisites->Add()","UserFacilities"
);

Test::add(
    function()
    {
        try{
            manage_ProjectTaskRequisites::Update("1", "1");
            return true;
            
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_ProjectTaskRequisites->Update()","UserFacilities"
);

Test::add(
    function()
    {
        try{
            manage_ProjectTaskRequisites::Remove("1");
            return true;
            
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_ProjectTaskRequisites->Remove()","UserFacilities"
);

Test::add(
    function()
    {
        try{
            manage_ProjectTaskRequisites::GetList("1", "1");
            return true;
            
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"manage_ProjectTaskRequisites->GetList()","UserFacilities"
);

// ProjectTaskRequisites.class.php end test by Mostafa Ghofrani



// ProjectsSecurity.class.php Unit Test =============== Javad Mahdavian =================
//===================================================================================================================

Test::add(
    function()
    {
        $obj = new security_projects();
        try {
            if ($obj->SaveFieldPermission("1","","1","Add") == 1){
            return true;
            }
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"security_projects->SaveFieldPermission()", "security_projects"
);

Test::add(
    function()
    {
        $obj = new security_projects();
        try {
            if ($obj->SaveDetailTablePermission("1","","1","1","1","1","1") == 1){
            return true;
            }
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"security_projects->SaveDetailTablePermission()", "security_projects"
);

Test::add(
    function()
    {
        $obj = new security_projects();
        try {
            if ($obj->ReadFieldPermission("1","","1")){
                return true;
            }
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"security_projects->ReadFieldPermission()", "security_projects"
);

Test::add(
    function()
    {
        $obj = new security_projects();
        try {
            if ($obj->ReadDetailTablePermission("1","","1","Add")){
            return true;
            }
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"security_projects->ReadDetailTablePermission()", "security_projects"
);

Test::add(
    function()
    {
        $obj = new security_projects();
        try {
            if ($obj->ResetRecordFieldsPermission("1","1") == 1){
            return true;
            }
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"security_projects->ResetRecordFieldsPermission()", "security_projects"
);

Test::add(
    function()
    {
        $obj = new security_projects();
        try {
            if ($obj->ResetRecordDetailTablesPermission("1","1") == 1){
            return true;
            }
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"security_projects->ResetRecordDetailTablesPermission()", "security_projects"
);

Test::add(
    function()
    {
        try {
            if (security_projects::LoadUserPermissions("1","1")){
            return true;
            }
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"security_projects->LoadUserPermissions()", "security_projects"
);

Test::add(
    function()
    {
        try {
            if (security_projects::SetPermissionToFullControl()){
                return true;
            }
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"security_projects->SetPermissionToFullControl()", "security_projects"
);
// End of ProjectsSecurity.class.php By Javad Mahdavian ===========================
//=================================================================================
// UniversitySessionsSecurity.class.php Unit Test =============== AMIR Karami =================
Test::add(
    function()
    {
        $obj = new security_UniversitySessions();
        try {
            $obj->SaveFieldPermission("1","File_Name","1","1");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"security_UniversitySessions->SaveFieldPermission()", "UniversitySessionsSecurity"
);

Test::add(
    function()
    {
        $obj = new security_UniversitySessions();
        try{
            $obj->SaveDetailTablePermission("1", "Table", "1", "1", "1", "1", "1");
            return true;

        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"security_UniversitySessions->SaveDetailTablePermission()","UniversitySessionsSecurity"
);
Test::add(
    function()
    {
        $obj = new security_UniversitySessions();
        try{
            $obj->ReadFieldPermission("1", "Table", "1");
            return true;

        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"security_UniversitySessions->ReadFieldPermission()","UniversitySessionsSecurity"
);
Test::add(
    function()
    {
        $obj = new security_UniversitySessions();
        try{
            $obj->ReadDetailTablePermission("1", "Table", "1", "1");
            return true;

        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"security_UniversitySessions->ReadDetailTablePermission()","UniversitySessionsSecurity"
);
Test::add(
    function()
    {
        $obj = new security_UniversitySessions();
        try{
            $obj->ResetRecordFieldsPermission("1", "1");
            return true;

        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"security_UniversitySessions->ResetRecordFieldsPermission()","UniversitySessionsSecurity"
);
Test::add(
    function()
    {
        $obj = new security_UniversitySessions();
        try{
            $obj->ResetRecordDetailTablesPermission("1", "1");
            return true;

        }
        catch (Exception $e)
        {
            return false;
        }
    }
    ,"security_UniversitySessions->ResetRecordDetailTablesPermission()","UniversitySessionsSecurity"
);
Test::add(
    function()
    {
        try {
            security_UniversitySessions::LoadUserPermissions("1","1");
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    ,"security_UniversitySessions->ResetRecordDetailTablesPermission()", "UniversitySessionsSecurity"
);

//=================================================================================
// ResearchProject.class.php Unit Test =============== Mohammad Afsharian Shandiz =================
//=================================================================================================
Test::add(
    function ()
    {
        $obj = new be_ResearchProject();
        try{
            $obj->LoadDataFromDatabase(1);
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    , "be_ResearchProject->LoadDataFromDatabase()", "Message"
);

Test::add(
    function ()
    {
        try{
            if(manage_ResearchProject::GetCount(1)==0) {
                return false;
            }
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    , "ResearchProject->GetCount()", "Message"
);

Test::add(
    function ()
    {
        try{
            if(manage_ResearchProject::GetLastID()==-1) {
                return false;
            }
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    , "ResearchProject->GetLastID()", "Message"
);

Test::add(
    function ()
    {
        try{
            if(manage_ResearchProject::Add("1","Term"))
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
    , "ResearchProject->Add()", "Message"
);

Test::add(
    function ()
    {
        try{
            manage_ResearchProject::Update("1","TermUpdate");
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    , "ResearchProject->Update()", "Message"
);

Test::add(
    function ()
    {
        try{
            if(is_array(manage_ResearchProject::GetList("1"))){
                return true;
            }
            return false;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    , "ResearchProject->GetList()", "Message"
);

Test::add(
    function ()
    {
        try{
            if(manage_ResearchProject::ComparePassedDataWithDB("1","TermUpdate")!=''){
                return true;
            }
            return false;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    , "ResearchProject->ComparePassedDataWithDB()", "Message"
);

Test::add(
    function ()
    {
        try{
            manage_ResearchProject::Remove("1");
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    , "ResearchProject->Remove()", "Message"
);
Test::add(
    function ()
    {
        try{
            manage_ResearchProject::ShowSummary("1");
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    , "ResearchProject->ShowSummary()", "Message"
);
Test::add(
    function ()
    {
        try{
            manage_ResearchProject::IsCurrentUserValid("1");
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    , "ResearchProject->IsCurrentUserValid()", "Message"
);
Test::add(
    function ()
    {
        try{
            manage_ResearchProject::ShowTabs("1" , "NewResearchProject");
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
    , "ResearchProject->ShowTabs()", "Message"
);

// --------------------------------------------- END ----------------------moahmmad afsharian shandiz-------------------------------

//persons.class.php UNIT TEST Start ------------> by Sara Bolouri Bazaz
Test::add(

    function ()
    {
        $obj = new be_persons();
        try {
            $obj->LoadDataFromDatabase(1);
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    , "be_persons->LoadDataFromDatabase()" , "Message"
);

Test::add(

    function ()
    {
        try {
            if(manage_persons::GetCount("whereCondition") == 0){
                return false;
            }
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    , "manage_persons->GetCount()" , "Message"
);

Test::add(

    function ()
    {
        try {
            if(manage_persons::GetLastID() == -1){
                return false;
            }
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    , "manage_persons->GetLastID()" , "Message"
);

Test::add(

    function ()
    {
        try {
            if(manage_persons::Add("pfname", "plname", "CardNumber" ) > -1){
                return true;
            }
            return false;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    , "manage_persons->Add()" , "Message"
);

Test::add(

    function ()
    {
        try {
            manage_persons::Update("1", "pfname", "plname", "CardNumber" );
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    , "manage_persons->Update()" , "Message"
);

Test::add(

    function ()
    {
        try {
            manage_persons::Remove("1" );
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    , "manage_persons->Remove()" , "Message"
);

Test::add(

    function ()
    {
        try {
            if(manage_persons::GetList("1", "1", "FieldName", "OrderType" ) != ''){
                return true;
            }
            return false;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    , "manage_persons->GetList()" , "Message"
);

Test::add(

    function ()
    {
        try {
            if(manage_persons::Search("pfname", "plname", "FieldName", "OrderType" ) != ''){
                return true;
            }
            return false;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    , "manage_persons->Search()" , "Message"
);

Test::add(

    function ()
    {
        try {
            if(manage_persons::SearchResultCount("pfname", "plname") == 0){
                return false;
            }
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    , "manage_persons->SearchResultCount()" , "Message"
);

Test::add(

    function ()
    {
        try {
            if(manage_persons::ComparePassedDataWithDB("1", "pfname", "plname", "CardName" ) != ''){
                return true;
            }
            return false;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    , "manage_persons->ComparePassedDataWithDB()" , "Message"
);

//persons.class.php UNIT TEST Finish ------------> by Sara Bolouri Bazaz



// ----------> projectDocuments.class.ph ------UNIT TEST START--------> Samin Hazeri


Test::add(

    function ()
    {
        $obj = new be_ProjectDocuments();
        try {
            $obj->LoadDataFromDatabase(1 );
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    , "be_ProjectDocuments->LoadDataFromDatabase()" , "Message"
);



Test::add(

    function ()
    {
        try {
            if(manage_ProjectDocuments::GetCount("ProjectId") == 0){
                return false;
            }
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    , "manage_ProjectDocuments->GetCount()" , "Message"
);

Test::add(

    function ()
    {
        try {
            if(manage_ProjectDocuments::GetLastID() == -1){
                return false;
            }
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    , "manage_ProjectDocuments->GetLastID()" , "Message"
);

Test::add(

    function ()
    {
        try {
            if(manage_ProjectDocuments::Add("ProjectID", "ProjectDocument", "FileContent", "FileName", "description" ) > -1){
                return true;
            }
            return false;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    , "manage_ProjectDocuments->Add()" , "Message"
);

Test::add(

    function ()
    {
        try {
            manage_ProjectDocuments::Update("UpdateRecord", "ProjectDoc", "FileName", "Description" );
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    , "manage_ProjectDocuments->Update()" , "Message"
);

Test::add(

    function ()
    {
        try {
            manage_ProjectDocuments::Remove("removeRecord" );
            return true;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    , "manage_ProjectDocuments->Remove()" , "Message"
);

Test::add(

    function ()
    {
        try {
            if(manage_ProjectDocuments::GetList("pId") != ''){
                return true;
            }
            return false;
        }
        catch(Exception $e)
        {
            return false;
        }
    }
    , "manage_ProjectDocuments->GetList()" , "Message"
);


// ----------> projectDocuments.class.ph ------UNIT TEST FINISH--------> Samin Hazeri

///////////////// OntologyMergeProjectMembers.class.php By Mahdi Ghayour /////////

// TODO: unit test
Test::add(
    function () {
        try {
            manage_OntologyMergeProjectMembers::GetCount(1);
            return true;
        } catch (Exception $e) {
            return false;
        }
    },
    "manage_OntologyMergeProjectMembers.class->GetCount()",
    "manage_OntologyMergeProjectMembers"
);

///////////////// end of OntologyMergeProjectMembers.class.php //////////


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
