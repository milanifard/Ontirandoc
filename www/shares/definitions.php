<?php
define("UI_LANGUAGE", "FA");

// use define2 in development, to prevent multiple constant definition error during developmnet
// remove this and multiple defined constant after develoment, on release version
function define2($name,$value) {
    if (!defined($name))
        return define($name,$value);
    return false;
}


if(UI_LANGUAGE=="EN") {
    define2("C_EDIT_REF_CONTENT", "New/Update content");
    define2("C_FA_PAGE", "page num");
    define2("C_FA_CONTENT", "content");
    define2("C_REFINE", "fix");
    define2("C_REF_CONTENT", "refrence content");
    define2("C_VOCAB", "terms");

    define2("C_BACK", "back");
    define2("C_MY_TITLE_PROJECT_COMMENTS", "comment");
    define2("C_COMMENTS", "comments");
    define2("C_CREATED_AT", "create time");
    define2("C_REQUISITIE_WORK", "requisite task");
    define2("C_MY_TITLE_PROJECT_REQUISITES", "requisite tasks");
    define2("C_REQUIREMENTS_JOB", "requirements");
    define2("C_MY_TITLE_PRE_REQUIREMENTS", "requirements");



    define2("C_WORK_CODE", "Executor");
    define2("C_READY_FOR_CONTROL", "Ready for control");
    define2("C_CONSIDER_WORK_CREATION_TIME_RANGE", "Creation time range");
    define2("C_CREATION_TIME_RANGE", "Creation time range");
    define2("C_CONSIDER_ACTION_CREATION_TIME_RANGE", "Activity range");
    define2("C_ACTION_TIME_RANGE", "Activity range");
    define2("C_SEARCH_RESULTS", "Search result");
    define2("C_LAST_ACTION_TIME", "Last action time");
    define2("C_EXECUTORS", "executors");
    define2("C_VIEWERS", "viewers");
    define2("C_TIME_TO_DO", "ِDone time");

    define2("C_SAVE", "Save");
    define2("C_NEW", "New");
    define2("C_REMOVE", "Remove");
    define2("C_NAME", "Name");
    define2("C_CREATE", "Create");
    define2("C_ORDER", "Order");
    define2("C_ROW", "Row");
    define2("C_EDIT", "Edit");
    define2("C_PERSIAN", "Persian");
    define2("C_ENGLISH", "English");
    define2("C_EXIT", "Exit");

    define2("C_DATA_SAVE_SUCCESS", "Data saved successfully");
//    define2("C_ARE_YOU_SURE", "Are you sure?");
    define2("C_ACTIVE_USER", "Active User: ");
    define2("C_MAIN_MENU", "Main Menu");
    define2("C_FIRST_PAGE", "Home");
    define2("C_CHANGE_PASSWORD", "Change Password");
    define2("C_MY_ACTIONS", "My Actions");
    // NewProjectTaskTypes.php by Mostafa Sader
    define2("C_CREATE_EDIT_TYPES_OF_WORKS","Create/Edit types of works");
    define2("C_DESIRED_TITLE","Desired title");
    // NewProjectTaskActivities.php by Mostafa Sader
    define2("C_ACTION_DATE","Action Date");
    define2("C_TYPE_OF_ACTION","Type of action");
    define2("C_TIME_CONSUMING","Time consuming");
    define2("C_PROGRESS","Progress");
    define2("C_ATTACHED_FILE","attached file");
    define2("C_CHANGED_TABLES","Changed tables");
    define2("C_CHANGED_PAGES","Changed pages");
    //SendMessage.php needed definitions MOHAMAD_ALI_SAIDI
    define2("C_TITLE","Title");
    define2("C_TEXT","Text");
    define2("C_FILE","File");
    define2("C_TO_USER","To");
    define2("C_SEND","Send");
    define2("C_SELECT","Select");
    define2("C_SEND_MESSAGE","Send Message");
    define2("C_MESSAGE_SENT","Message Sent");
    define2("C_TITLE_EMPTY","Please Fill The Tile Field");
    define2("C_RECEIVER_EMPTY","Please Select Receiver");
    define2("C_AUTO_SAVE","Auto Saving...");
    define2("C_SENDING_FILE_ERROR","Error In Sending File");
    //----------------------------------

    //MailBox.php needed definitions MOHAMAD_ALI_SAIDI
    define2("C_MESSAGES_RECEIVED","Inbox");
    define2("C_SENDER_NAME","Sender Name");
    define2("C_TIME_SENT","Time Sent");
    define2("C_REPLY_DES","Reply Description");
    define2("C_DELETE","Delete");
    //----------------------------------


    //SentBox.php needed definitions MOHAMAD_ALI_SAIDI
    define2("C_MESSAGES_SENT","OutBox");
    define2("C_RECEIVER_NAME","Receiver Name");
    //----------------------------------


    //SearchMessage.php needed definitions Alireza Imani
    define2("C_SEARCH_MESSAGE","Search Message");
    define2("C_PART_OF_TEXT","Part of text");
    define2("C_CHOOSE","Choose");
    define2("C_FROM_DATE","From date");
    define2("C_TO_DATE","To date");
    define2("C_SEARCH","Search");

    //MetaData2Onto.php needed definitions Alireza Imani
    define2("C_CHOOSE_CONDITIONS_FOR_REVERSE_ENGINEERING","Choose conditions for reverse engineering");
    define2("C_INTENDED_SCOPES","Intended Scopes");
    define2("C_EDUCATIONAL","Educational");
    define2("C_RESEARCH","Research");
    define2("C_STUDENT_SERVICES","Student Services");
    define2("C_WELFARE","Welfare");
    define2("C_FINANCIAL","Financial");
    define2("C_SUPPORT","Support");
    define2("C_ADMINISTRATIVE","Administrative");
    define2("C_RELATED_TO_SYSTEM_OPERATIONS","Related to system operations");
    define2("C_TARGET_ONTOLOGY","Target Ontology");
    define2("C_REMOVE_PREVIOUS_MERGE_SUGGESTIONS","Remove previous merge suggestions");
    define2("C_REMOVE_EXISTING_ELEMENTS_OF_ONTOLOGY","Remove existing elements of Ontology");
    define2("C_PERFORM_REVERSE_ENGINEERING","Perform Reverse Engineering");
    define2("C_REVIEW_PROPERTIES_MERGING_SUGGESTIONS","Review properties merging suggestions");
    define2("C_REVIEW_INTEGRATION_SUGGESTIONS","Review integration suggestions");
    define2("C_HIERARCHICAL_RELATIONSHIPS_BETWEEN_CLASSES","Hierarchical relationships between classes");
    define2("C_CONVERSION_DONE","Conversion done");

    //ShowTermsManipulationHistory.php needed definitions Alireza Imani
    define2("C_COMPLETED_TASK","Completed Task");
    define2("C_DESCRIPTION","Description");
    define2("C_SUBJECT","Subject");
    //define2("C_TIME","Time");
    define2("C_EXTRACT_NEW_WORD","Extract new word");
    define2("C_REMOVE_WORD","Remove word");
    define2("C_MERGE_TWO_WORDS","Merge two words");
    define2("C_CHANGE_WORD","Change word");
    define2("C_REPLACE_WORD","Replace word");
    define2("C_TO","to");
    define2("C_BY","by");

    //NewQuestionnare.php ALI NOORI
    define2("C_CREATING_EDITTING_QUESTIONNARE","Creating/EdittingQuestionnare");
    define2("C_FORM_NAME","Form Name");
    define2("C_FORM_EXPLANATION_UP","Form Explanation Above");
    define2("C_FORM_EXPLANATION_DOWN","Form Explanation Below");
    define2("C_TYPE_SHOW_ENTER_DATA_LAYOUT","Type of entry data layout");
    define2("C_ONE_COLUMN","One Column");
    define2("C_TWO_COLUMN","Two Column");
    define2("C_WIDTH_QUESTION_COLUMN","Width of question column");
    define2("C_MARGIN_SECOND_ROWS","margin for second rows");
    define2("C_MARGIN_SECOND_ROWS_YES","YES");
    define2("C_MARGIN_SECOND_ROWS_NO","NO");
    define2("C_RETURN","Return");
    define2("C_TABLE_INFORMATION","Table Of Information");
    define2("C_BANK_INFORMATION","Bank Of Information");
    define2("C_FORMATION_USER","Formation User");
    define2("C_CREATE_TIME","Create Time");
    define2("C_MANAGE_OPTIONS","Manage Options");
    define2("C_MANAGE_DETAILS_TABLES","Manage details tables");
    //-----------------------------
    //MyTimeReport.php ALI NOORI
    define2("C_YEAR","Year");
    define2("C_MONTH","Month");
    define2("C_SHOW_REPORT_ACTIONS","Show Report Actions");
    define2("C_USAGE_TIME_REPORT","Usage Time Report");
    define2("C_DATE","Date");
    define2("C_ACTIVITY","Activity");
//    define2("C_TIME","Time");
    define2("C_TOTAL","Total");
    define2("C_RIAL","Rial");
    //-------------------------
    //CompareAllOntos.php ALI NOORI
    define2("C_COMPARE_COVER_HASTAN_NEGAR","Compare Cover Of HastanNegar");
    define2("C_WITH_OTHER_HASTAN_NEGAR","With Other HastanNegar");
    define2("C_NAME_HASTAN_NEGAR","Name Of HastanNegar");
    define2("C_PERCENTAGE_MAPPING_CLASS","Class Mapping Percentage");
    define2("C_PROPERTIES_MAPPING_PERCENTAGE","Properties mapping percentage");
    //--------------------------

    //ShowTermReferHistory.php Hossein Lotfi
    define2("C_SOURCE_NAME","Source Name");
    define2("C_PAGE","Page");
    define2("C_PARAGRAPH","Paragraph");
    define2("C_SUBMIT_NEW_REFERENCE", "Submit New Reference");
    define2("C_REMOVE_REFERENCE", "Remove Reference");
    define2("C_CHANGE_REFERENCE", "Change Reference");
    define2("C_REPLACE_REFERENCE_WITH", "Replace Reference With");
    define2("C_WITH_REFERENCE_TO", "With Reference To");
    define2("C_S", "P");
    define2("C_P", "p");

    //SelectStaff.php Hossein Lotfi
    define2("C_LAST_NAME","Last Name");
    define2("C_REMOVE_PREVIOUS_CHOICE","Remove Previous Choice");
    define2("C_FULL_NAME","Full Name");

    //ManageTermReferences.php Hossein Lotfi
    define2("C_CONTENT","Content");
    define2("C_TERM", "Term");
    define2("C_FREQUENCY","Frequency");
    define2("C_INFORMATION_SAVED","Information Saved");
    define2("C_CREATE_EDIT_TERMS_REFERENCES","Create/Edit Terms References");
    define2("C_FILE2","File");
    define2("C_GET_FILE", "Get File");
    define2("C_TERMS_REFERENCES","Terms References");
    define2("C_TERMS","Terms");
    define2("C_STATISTICAL_ANALYSIS","Statistical Analysis");

    //SelectMultiStaff.php By Ehsan Amini
    define2("C_USER_NAME", "User Name");

    //NewRequest.php By Ehsan Amini
    define2("C_TASK_REQUEST", "Task Request");
    define2("C_IF_REQUEST_IS_ABOUT_CHANGING_ACCESS_TO_DATABASE_DATA_CLICK_HERE", "[If request is about changing access to database data click here]");
    define2("C_UNKNOWN_SYSTEM_CODE", "Unknown system code");
    define2("C_NO_RESPONSE_HAS_BEEN_DETERMINED_FOR_THIS_PROJECT", "No response has been determined for this project");

    //CreateKartableHeader function in ProjectTasks.class.php By Ehsan Amini
    define2("C_CURRENT_TASKS", "Current Tasks");
    define2("C_PROJECTS_MEMBERS", "Projects Members");
    define2("C_TASKS_IN_NEED_OF_CONTROL", "Tasks in need of control");
    define2("C_DONE_TASKS", "Done Tasks");
    define2("C_CREATED_TASKS", "Created Tasks");

    //ShowAllPersonStatus.php By Ehsan Amini
    define2("C_PROJECTS_COUNT", "Projects Count");
    define2("C_TIME_PERCENTAGE_ALLOCATED", "Percentage time allocated");
    define2("C_LAST_NAME_AND_FIRST_NAME", "Last Name and First Name");
    define2("C_THIS_LIST_SHOWS_MEMBERS_OF_THE_PROJECTS_THAT_YOU_ARE_MANAGING_OR_SUBORDINATE_TO_THE_ORGANIZATIONAL_UNIT_UNDER_YOUR_MANAGEMENT", "This list shows members of the projects that you are managing or subordinate to the organizational unit under your management");
    define2("C_FOR_ADJUSTING_PERCENTAGES_YOU_CAN_CLICK_ON_PERCENTAGE_NUMBER_IN_EACH_ROW","For adjusting percentages you can click on percentage number in each row");

    //SessionTypes.class.php By Arman Ghoreshi
    define2("C_SESSION_LOCATION","Location");
    define2("C_SESSION_INFO","Session Info");
    define2("C_SESSION_PERMITTED_PERSON","Permitted Users");
    define2("C_SESSION_MEMBERS","Members");
    //NewSessionTypes.php By Arman Ghoreshi
    define2("C_SESSION_CREATE_EDIT","Create/Edit Session Patterns");
    define2("C_START_TIME","Start Time");
    define2("C_END_TIME","End Time");
    //managePersonPermittedSessionType.php By Arman Ghoreshi
    define2("C_SESSION_PERMITTED_CREATE_EDIT","Create/Edit Permitted Person");
    define2("C_SESSION_PERMITTED_LIST","Permitted Users for Sessions");
    define2("C_PERMISSIONS","Permissions");
    //ManageSessionTypeMembers.php By Arman Ghoreshi
    define2("C_ROLE","Role");
    define2("C_SESSIOM_MEMBERS","Session Members");
    // define2("C_ARE_YOU_SURE","Are You Sure?");
    define2("C_SESSION_MEMBERS_CREATE_EDIT","Create/Edit Session Members");
    define2("C_PERSONEL","Employees");
    define2("C_OTHER","Others");
    define2("C_MEMBERSHIP_TYPE","Membership Type");
    define2("C_MEMBER_PERSONAL_ID","Members Personal ID");
    define2("C_SESSION_APPROVAL","The meeting is subject to the approval of this user");
    define2("C_SIGN_MINUTES","PERMISSION TO SIGN MINUTES");
    define2("C_ELECTRONIC_SIGN","An electronic signature is required to confirm the minutes");
    define2("C_YES","Yes");
    define2("C_NO","No");

    //ManageFacilityPages.php By Naghme Mohammadifar
    define2("C_SAVED_INFO","New information saved!");
    define2("C_CREATE_EDIT_A_PAGE_RELATED_TO_FEATURE", "Create/edit a page related to the part");
    define2("C_TITLE_PAGE","Page");
    define2("C_CLOSE", "Close");
    define2("C_RELATED_PAGES_TO_THIS_FEATURE","Related pages to the part");
    define2("C_CONFIRM_TO_DELETE","Are you sure to delete?");
    define2("C_PAGE_PLACE_HOLDER","Your page name");
    //NewResearchProjectComments.php Alireza Forghani Toosi
    define2("C_SEASON", "Season");
    define2("C_CREATE_EDIT_RESEARCH_PROJECT_COMMENT", "Create/Edit research project comment");
    define2("C_COMMENT_CHANGE_HISTORY", "Change history of this comment");

    //ResearchProject.class.php Alireza Forghani Toosi
    define2("C_SEASONS", "Seasons");
    define2("C_REFERENCE_TYPES", "Reference types");
    define2("C_REFERENCES", "References");
    define2("C_NOTES", "Notes");
    define2("C_OUTPUTS", "Outputs");
    define2("C_PRIVILEGES", "Privileges");
    define2("C_MAIN_PROPERTIES", "Main properties");

    //NewProjectComments.php Alireza Forghani Toosi
    define2("C_DATA_SAVED", "Information saved");

    //DesktopManager.php Alireza Forghani Toosi
    define2("C_MESSAGES", "Messages");
    define2("C_RECEIVED_LETTERS", "Received letters");
    define2("C_ATTACHMENTS", "Attachments");

    //Managemessages.php By kouroshAtaei

    define2("CREATE_EDIT","create / edit ");
    define2("C_MESSAGE" , "Message");
    define2("AT_FILE" ,"attached file");
    define2("REC_FILE" , "receive file");
    define2("PIC" ,"picture");
    define2("START_TIME" ,"begin");
    define2("END_TIME", "end" );
    define2("SAVE_M" , "save");
    define2("NEW_M" ,"new");
    define2("SEARCH_M" ,"search");
    define2("MESSAGES_M" , "messages");
    define2("CREATOR_M" , "creator");
    define2("CREATE_TIM_M" , "creat time");
    define2("ROW_M" , "row");
    define2("EDIT_M" , "edit");
    define2("DELETE_M" ,"delete");
    define2("ARE_YOU_SURE" , "Are you sure ?");
    define2("ERROR_SEND" ,"Error submitting file");
    define2("INFO_SAVED" , "Information saved");

    //newResearchProjectRefrences.php By kouroshAtaei

    define2("CREAT_AND_EDIT_RES_RESEARCH" , "Create / edit a research work source");
    define2("SEARCH_ENG" , "Search Engine");
    define2("TAGS_WORDS" ,"Search Keywords");
    define2("LANG_N" ,"Language");
    define2("EN_LAN_N", "English");
    define2("FA_LAN_N", "Persian");
    define2("TITLE_N" ,"title");
    define2("WRITERS_N", "writers");
    define2("YEARS_N", "year");
    define2("SUM_N", "Abstract");
    define2("STATE_OF_STUDY", "Study status");
    define2("ALREADY_STUDY", "studied");
    define2("ALREADY_NOT_STUDY", "not studied");
    define2("STUDING", "Studying");
    define2("IMPORTNT", "Importance");
    define2("CAT_N", "Category");
    define2("ALL_COM", "Overview");
    define2("FILE_N" , "file");
    define2("NOTES_N", "notes");
    define2("CLOSE_N" , "close");
    //manageFieldsDataMapping.php By kouroshAtaei
    define2("SELECTION_M" , "Select the appropriate table and field to determine the value equation table");
    define2("TABLE_M" , "table") ;
    define2("DEF_TABLE", "Define the equation table");
    define2("VAL_FIELD_M","Equalized values for the corresponding field");
    define2("REAL_VAL" ,"Original value");
    define2("M_VAL_EQ" , "Equivalent value");
    define2("DATABASE" , "database");
    define2("FIELD_M" ,"field");

    // ------------------------ ManageSessionTypes.php By diba aminshahidi -------------------------
    define2("C_PATTERN","Session Pattern");
    define2("C_MEETING_TYPE","Session Type");
    define2("C_SESSIONS","Sessions");
    define2("C_SESSION_NUMBER","Session Number");
    define2("C_SESSION_TITLE","Session Title");
    define2("C_DURATION","Duration");
    define2("C_SESSION_STATUS","Session Status");
    define2("C_CREATE","Create");
    define2("C_INSTRUCTION_KEYWORD","Keywords in Instructions");
    define2("C_ENACTMENT_KEYWORD","Keywords in Enactments");
    define2("C_APPROVE","Approve");
    define2("C_REJECTED","Rejected because");

    //ManageProjectMilestones.php by Sajjad Iranmanesh
    define2("C_CREATE_EDIT_IMPORTANT_DATE", "Create/Edit important date");
    define2("C_DATE", "Date");
    define2("C_DESCRIPTION", "Description");
    define2("C_SAVE", "Save");
    define2("C_NEW", "New");
    define2("C_ROW", "Row");
    define2("C_EDIT", "Edit");
    define2("C_IMPORTANT_DATES", "Important dates");

    //ManageOntologyPropertyLabels.php By Javad Mahdavian
    define2("C_CREATE_EDIT_LABELS" , "Create/Edit the property labels");
    define2("C_LABEL" , "Label");
    define2("C_LABELS" , "Labels");

    //ManageProjectTaskActivityTypes.php by Sajjad Iranmanesh
    define2("C_CREATE_EDIT_ACTIONS", "Create/Edit actions");
    define2("C_TITLE", "Title");
    define2("C_SAVE", "Save");
    define2("C_NEW", "New");
    define2("C_ACTIONS_TYPES", "Action types");
    define2("C_ACTIONS_COUNT", "Action count");
    define2("C_DELETE", "Delete");
    define2("C_DONT_HAVE_PERMISSION", "You don't have permission to view this record");

    //ManageProjectTaskAssignedUsers.php by Sajjad Iranmanesh
    define2("C_CREATE_EDIT_USERS_ASSIGNED_TO_ACTIVITY", "Create/Edit users assgined to activity");
    define2("C_ASSIGNEE_DESCRIPTION", "Assignee description");
    define2("C_PARTICIPATION_PERCENTAGE", "Participation percentage");
    define2("C_ECECUTOR", "Executor");
    define2("C_VIEWER", "Viewer");
    define2("C_SEND_LETTER_FROM_ADVERTISER", "Send letter from advvertisor to selected person");
    define2("C_DONT_HAVE_VALUE", "A person don't have any value");
    define2("C_USERS_ASSIGNED_TO_ACTIVITY", "Users assigned to activity");

    //Manageontologies.php by Naghme Mohammadifar
    define2("C_ONTOLOGY_FEATURES" , "Ontology features");
    define2("C_ONTOLOGY_CLASSES" , "Ontology classes");
    define2("C_DATA_FEATURES","Data features");
    define2("C_THING_FEATURES","Things features");
    define2("C_CREATE_EDIT_ONTOLOGY" , "Create/Edit ontology");
    define2("C_CLASSES","Classes");
    define2("C_TREE_STRUCTURE","Tree structure");
    define2("C_GET_OWL_CODE_FROM_STRUCTURE","Get owl code from structure");
    define2("C_GET_ER_CODE","Get ER code ");
    define2("C_CLASS_STATISTICAL_ANALYSIS","Classes statistical analysis");
    define2("C_INTERNET_PATH","Internet path");
    define2("C_GETTING_FILE","Receive file");
    define2("C_TRANSMIT_FILE_TO_DB","Transmit file to database");
    define2("C_ONTOLOGY","Ontology");
    define2("C_FEATURES","Features");
    define2("C_EXPERT_JUDGES","Expert judges");
    define2("C_PRINT","Print");
    define2("C_PRINT_WITH_MERGE_SOURCES","Print- with merge sources");
    define2("C_PRINT_WITH_VOCAB_EXTRACTION_SOURCES","Print- with vocab extraction sources");
    define2("C_PRINT_WITH_DATABASE_SOURCES","Print- with database sources");
    define2("C_DICTIONARY","Dictionary");
    define2("C_FREQUENCY_ANALYSIS","Frequency analysis");
    define2("C_DISTANCE_ANALYSIS","Distance analysis levenshtein");
    define2("C_STATISTICAL_EVALUATION","Statistical evaluation");
    define2("C_REVERSE_ENGINEERING","Reverse engineering RDB");
    define2("C_ANALYSIS_WITH_WORDNET","Analysis with Wordnet");
    define2("C_CONTENT_COMPARISON","Content comparison");
    define2("C_MERGED_PROJECTS","Merged projects");
    define2("C_ALERT_TO_CLOSE","By doing this all the things will be deleted, are you sure?");
    define2("C_ONTOLOGIES_LIST","Ontologies lists");
    define2("C_CLASSES_IN_TERMS_OF_REFERRAL_RATES","Classes in terms of referral rates - more that 2 refers");
    define2("C_PROPERTIES_IN_ORDER_OF_REFERENCE","Properties in order of reference - more than two references");
    define2("C_ENTITIES_THAT_WERE_ONCE_REFERRED_TO_AS_A_CLASS_AND_ONCE_AS_A_PROPERTY","Entities that were once referred to as a class and once as a property");
    define2("C_CLASSES_REFERRED_TO_ONLY_ONCE","Classes referred to only once (not referred to as attributes)");
    define2("C_PROPERTIES_THAT_ARE_ONLY_MENTIONED_ONCE","Properties that are only mentioned once (not referred to as class)");
    define2("C_ONTOLOGY_TITLE","Ontology title");
    define2("C_ONTOLOGY_TYPE","Ontology type");
    define2("C_PERCENTAGE_OF_SIMILARITY__BETWEEN_CLASS_TITLES"," percentage of similarity between class titles that do not have the same Persian tag and are related to different masters");
    define2("C_CHECK_TITLES_WITH_OVER","Check titles with over ");
    define2("C_CHECK_TAGS_WITH_OVER","Check tags with over ");
    define2("C_PERCENTAGE_OF_SIMILARITY_BETWEEN_PERSIAN_LABELS_FOR_CLASSES_THAT_ARE_NOT_IDENTICAL"," percentage of similarity between Persian labels for classes that are not identical in title and for different typographers");
    define2("PERCENTAGE_OF_SIMILARITY_BETWEEN_DIFFERENT_TITLES_OF_ATTRIBUTES_THAT_DO_NOT_MATCH_THE_SAME_PERSIAN_TAG_AND_ARE_RELATED_TO_DIFFERENT_TYPOGRAPHERS"," percentage of similarity between different titles of attributes that do not match the same Persian tag and are related to different typographers");
    define2("C_PERCENTAGE_OF_SIMILARITY_AMONG_FARSI_LABELS_FOR_PROPERTIES_THAT_DO_NOT_HAVE_THE_SAME_TITLE_AND_ARE_DIFFERENT"," percentage of Similarity among Farsi Labels for Properties That Do Not Have the Same Title and Are Different");
    define2("C_CLASS_TITLE","Class title");
    define2("C_PROPERTY_TITLE","Property title");
    define2("C_CLASSES_WITH_SAME_NAME_ACCORDING_TO_WORDNET","Classes with same name acording to wordnet");
    define2("C_PROPERTIES_WITH_SAME_NAME_ACCORDING_TO_WORDNET","Properties with same name acording to wordnet");
    define2("PERCENTAGE_OF_EACH_ONTOLOGY_COVERAGE_OTHER_ONTOLOGY_CLASSES","Percentage of each ontology coverage other ontology classes");
    define2("C_AVERAGE","Average");
    define2("C_CLASS_COUNT","Class count");
    define2("C_PROPERTY_COUNT","Property count");
    define2("C_AVERAGE_OF_COVERAGE_OTHER_ONTOLOGY_CLASSES","Average of coverage other ontologies (classes)");
    define2("C_REPOSITORY","Repository");
    define2("C_PERCENTAGE_OF_EACH_ONTOLOGY_COVERAGE_OTHER_ONTOLOGY_PROPERTIES","Percentage of each ontology coverage other ontology properties");
    define2("C_AVERAGE_OF_COVERAGE_OTHER_ONTOLOGY_PROPERTIES","Average of coverage other ontologies (properties)");
    define2("C_PERCENTAGE_OF_EACH_ONTOLOGY_COVERAGE_DOCUMENT_TERMS","Percentage of each ontology coverage document terms ");
    define2("C_TOTAL_ELEMENTS","Total elements");
    define2("C_COVERAGE_PERCENTAGE","Coverage Percentage");


    //TasksForControl.php -- by navidbeta
    define2("C_RELATED_PROJECT" , "Related Project");
    define2("C_PRIORITY" , "Priority");
    define2("C_T_TITLE" , "Title");
    define2("C_CREATOR" , "Creator");
    define2("C_CREATED_TIME" , "Created Time");
    define2("C_T_AREUSURE" , "Are You Sure?");

    //ManageUserFacilities.php -- by navidbeta
    define2("C_ADD_USER_FACILITY" , "Add User Facility");
    define2("C_T_USER" , "User");
    define2("C_PRIVILEGED_USERS" , "Privileged Users");
    define2("C_POSSIBILITY" , "Possibility");

    //ManageUserPermissions.php -- by navidbeta
    define2("C_DATA_STORED" , "Information Stored Successfully");
    define2("C_USER_ACCESSES" , "User Accesses");
    define2("C_MENUS" , "Menus");
    define2("C_T_RETURN" , "Return");

    //additionals for classes -- by navidbeta
    define2("C_CONTRACTS" , "CONTRACTS");
    define2("C_APPROVAED_CREDIT" , "Approved credit");
    define2("C_PROJECT_PROGRESS" , "Project progress");
    define2("C_OTHER_RESOURCES" , "Other Resources");
    define2("C_GROUP" , "Group");
    define2("C_T_PAGEADDRESS" , "Page Address");
    define2("C_ORDERNUM" , "Order Number");

    //ManagePermittedDatabases by Javad Mahdavian
    define2("C_DATABASE_DOC" , "Documentation of Databases");
    define2("C_DATABASES", "Databases");
    define2("C_SERVER", "Server");
    define2("C_TABLES" , "Tables");
    define2("C_DEADLINE" , "Deadline");

    //EN fo MangePersons.php
    define2("C_CREATING_EDITTING_PERSONS", "Add/Edit users");
    define2("C_MP_EMAIL", "Email");
    define2("C_MP_MOBILE", "Mobile");
    define2("C_MP_USERNAME", "Username");
    define2("C_MP_PEOPLE_LIST", "User lists");
    define2("C_MP_IMAGE", "Image");
    define2("C_MP_PAYMENTS", "Payments");

    //ManageQuestionnaires.php Hoormazd Ranjbar
    define2("C_MQ_FILTER", "Filter Title");
    define2("C_MQ_CODE", "Code");
    define2("C_MQ_MAIN_FORM", "Main Form");
    define2("C_MQ_MANAGERS", "Managers");
    define2("C_MQ_SETTINGS", "Settings");
    define2("C_MQ_CREATOR", "Creator");
    define2("C_MQ_CREATE_DATE", "Create Date");
    define2("C_MQ_FILL", "Filled");
    define2("C_MQ_LAST_ACCEPT", "Last Accept");
    define2("C_MQ_MAKE", "Make");

    //MyActions.php Hoormazd Ranjbar
    //EN
    define2("C_MYACTIONS", "My Actions");
    define2("C_MA_ACTION", "Action");
    define2("C_MA_DONE_DATE", "Date");
    define2("C_MA_TOTAL_FIND", "Total Found items");


    //Manageterms by Naghme Mohammadifar
    define2("C_STRUCTURAL_SIMILARITY","Structural similarity");
    define2("C_PEERS_IN_THE_SEMANTIC_NETWORK","Peers in the semantic network");
    define2("C_MORE_SPECIFIC_MEANING_IN_THE_SEMANTIC_GRID","More specific meaning in the semantic grid");
    define2("C_MORE_GENERAL_MEANING_IN_THE_SEMANTIC_GRID","More general meaning in the semantic grid");
    define2("C_PERIODICITY","periodicity");
    define2("C_WORD","Word ");
    define2("C_RECORDER","Recorder ");
    define2("C_REFERENCES_IN_WORDS","References");
    define2("C_WORD_CODE","Code");
    define2("C_SIMILARITY_TYPE","Similarity type");
    define2("C_RESOURCES_AND_THE_NUMBER_OF_REPETITIONS","Resources and the number of repetitions");
    define2("C_REPLACEMENT","Replacement");
    define2("C_REPLACE_THE_SELECTED_WORD_WITH_THE_WORD_IN_THIS_ROW","Replace the selected word with the word in this row");
    define2("C_CREATE_EDIT_TERMS","Create / edit terms");
    define2("C_NOTE","Note");
    define2("C_MERGE_SUGGESTIONS","Merge suggestions");
    define2("C_CREATION_TIME","Creation time");
    define2("C_ONTOLOGY_ELEMENT","Ontology element");
    define2("C_RECORD","Record");
    define2("C_SUMMARY_OF_INFORMATION","Summary of information");
    define2("C_MAPPING_OF_IDIOMS_AND_ELEMENTS_OF_HISTOGRAM","Mapping of idioms and elements of ontology");




    ///// Mahdi Ghayour /////
    define2("C_TASK", "Task");
    define2("C_TASK_TYPE", "Task type");
    define2("C_DOCUMENT", "Document");
    define2("C_ACTION", "Action");
    define2("C_PRIORITY_NORMAL", "Normal");
    define2("C_PRIORITY_LOW", "Low");
    define2("C_PRIORITY_HIGH", "High");
    define2("C_PRIORITY_CRITICAL", "Critical");

    define2("C_STATUS", "Status");
    define2('C_STATUS_NOT_START', 'not started');
    define2('C_STATUS_PROGRESSING', 'progressing');
    define2('C_STATUS_DONE', 'done');
    define2('C_STATUS_SUSPENDED', 'suspended');
    define2('C_STATUS_REPLYED', 'replyed');

    define2("C_FORMPART_NEWEDIT", "Create/Edit form parts");
    define2("C_FORMPART_NAME", "Name");
    define2("C_FORMPART_ORDER", "Order");
    define2("C_FORMPART_TOPTEXT", "Top text");
    define2("C_FORMPART_BOTTOMTEXT", "Bottom text");
    define2("C_FORMPART_TITLE", "Form parts");

    define2("C_APPROVAL_STATUS", "approval status");
    define2("C_PRESENT_TYPE", "present type");
    define2("C_PRESENT_TIME", "present time");
    define2("C_ABSENT", "absent");
    define2("C_ABSENT2", "absent");
    define2("C_PRESENT", "present");

    //// End of Mahdi Ghayour //////

    //MyRequests.php by Sara Bolouri
    define2("C_RELATED_PROJECT","Related Project");
    define2("C_YOUR_REQUESTS_LIST" , "Your request list");
    define2("C_STATUS" , "Status");
    define2("C_OTHER_SPECIFICATIONS" , "Other specification");
    define2("C_CREATE_TIME1" , "Create time");
    define2("C_PREREQUISITES" , "Prerequisites");
    define2("C_NOTES" , "Notes");
    define2("C_USERS_ASSIGNED_TO_WORK" , "User assigned to work");
    define2("C_DOCUMENTS" , "Documents");

    //ManagePayments.php by Sara Bolouri
    define2("C_CREATING_EDITING_PAYMENT_TO" , "Create/Edit payment to");
    define2("C_AMOUNT" , "Amount");
    define2("C_PAYMENT_TYPE" , "Payment type");
    define2("C_CHEQUE" , "Cheque");
    define2("C_CASH" , "Cash");
    define2("C_DEPOSIT" , "Deposit");
    define2("C_DESCRIPTION1" , "Description");
    define2("C_CHOOSE_FILE" , "Choose file");
    define2("C_PAYMENTS_TO" , "Payments to");
    define2("C_FILE1" , "File");

    //ShowProjectActivities.php by Sara Bolouri
    define2("C_MAIN_SPECIFICATIONS" , "Main Specification");
    define2("C_MEMBERS" , "Members");
    define2("C_DOCUMENTS" , "Documents");
    define2("C_DOCUMENT_TYPES" , "Document types");
    define2("C_ACTION_TYPES" , "Action types");
    define2("C_TASK_TYPES" , "Task types");
    define2("C_GROUP_OF_TASKS" , "Group of tasks");
    define2("C_HISTORY" , "History");
    define2("C_ACTIVITIES" , "Activities");
    define2("C_APPLIER" , "Applier");
    define2("C_RELATED_ROLE" , "Related role");
    define2("C_CODE" , "Code");
    define2("C_ACTION_TYPE1" , "Action type");
    define2("C_RELATED_SECTION" , "Related section");
    define2("C_OPERATION_DESCRIPTION" , "Operation description");
    define2("C_MEMBER" , "Member");
    define2("C_IMPORTANT_DATE", "Important date");
    define2("C_DOCUMENT", "Document");
    define2("C_DOCUMENT_TYPE" , "Document type");
    define2("C_ACTION_TYPE2" , "Action type");
    define2("C_TASK_TYPE" , "task type");
    define2("C_ADD" , "Add");
    define2("C_UPDATE" , "Update");
    define2("C_VIEW" , "View");
    define2("C_CHOOSE_APPLIER" , "Choose applier");
    define2("C_CONTRACTS" , "Contracts");
    define2("C_PROJECT_PROGRESS" , "Project progress");
    define2("C_CREDITS_APPROVED" , "Credits approved");
    define2("C_CONTRACTS_OTHER_RESOURCES" , "Credits approved - Other resources");


//    ControlForms.php by Yegane Shabgard
    define2("C_FORM_TYPE" , "From Type:");
    define2("C_SEARCH_SELECTED_FORM" , "Search Appropriate From");
    define2("C_CREATOR_NAME" , "Creator Name");
    define2("C_CREATOR" , "Creator: ");
    define2("C_SENDER" , "Sender: ");
    define2("C_FORM_NAME_2" , "Form Name: ");
    define2("C_LAST_SENDER" , "Last Sender");
    define2("C_SEND_TIME" , "Sending Time");
    define2("C_CURRENT_STEP" , "Current Step");
    define2("C_CURRENT_STEP_NAME" , "Current Step Name: ");
    define2("C_NEW_STEP" , "New Step: ");


//    PrintPageHelper.php by Yegane Shabgard
    define2("C_HELP_TO_CREATE_PAINT_PAGES" , "Help For Creating Costume Print Pages");
    define2("C_HELP_PARAMETER_SEND_AS_ID" , "Parameter Which Will Be Sent To Your Page Is RecID.");
    define2("C_HELP_CREATE_YOUR_OWN" , "When You Want Printing Has Specific Format Create Its Own Page And Put It On Server And Add Page Address To Costume Print Page");
    define2("C_HELP_DEFAULT_ICON" , "In Default Mode By Clicking On Each Record A Table Will Be Shown");
    define2("C_FORM_MAKER_SEND" , "Form Generator System Add An Icon As Print Icon By Default");
    define2("C_PAGE_TO_PRINT_FROM" , "Printing Specific Page Is A PHP Page Which Sends Record code To It");

    //   NewFile.php by Yegane Shabgard
    define2("C_TEXTS" , "Texts");
    define2("C_FILE_DEFINITION" , "File Information");
    define2("C_DEFINITION" , "Information");
    define2("C_IMAGES" , "Images");
    define2("C_FILES" , "Files");
    define2("C_FORMS" , "Forms");
    define2("C_LETTERS" , "Letters");
    define2("C_SESSIONS" , "Sessions");
    define2("C_DEBTS" , "Debts");
    define2("C_FILE_TYPE" , "File Type");
    define2("C_FILE_NUMBER" , "File Number");
    define2("C_PROFESSOR" , "Professor");
    define2("C_STUDENT" , "Student");
    define2("C_EMPLOYER" , "Employer");
    define2("C_OTHERS" , "Others");
    define2("C_STRUCTURE_UNIT" , "Structure Unit");
    define2("C_STRUCTURE_SUBUNIT" , "Structure Sub Unit");
    define2("C_INSTRUCTION_GROUP" , "Instruction Group");
    define2("C_USER_TYPE" , "User Type");


    // Projects Kartable Adel Aboutalebi
    define2("C_ROW", "Row");
    define2("C_PROJECT_GROUP", "Project group");
    define2("C_EDIT", "Edit");
    define2("C_TITLE","Title");
    define2("C_PRIORITY" , "Priority");
    define2("C_STATUS", "Status");
    define2("C_REPORT", "Report");

    // ManageProjectTaskActivities Adel Aboutalebi
    define2('C_ACTIONS', 'Actions');
    define2('C_ACTION_TYPE', 'Action type');
    define2('C_USAGE_TIME', 'Usage time');
    define2('C_Progress', 'Progress');
    define2('C_DESCRIPTION', 'Description');
    define2("C_ATTACHMENTS", "Attachments");
    define2("C_CREATOR", "Creator");
    define2("C_ACTION_DATE", "Action date");
    define2("C_CREATE", "Create");
    define2("C_DELETE", "Delete");
    define2("C_NOT_EXIST", "Not exist");
    define2("C_ARE_YOU_SURE","Are you sure?");

    // ManageProjectPrecentage Adel Aboutalebi
    define2('C_PROJECTS_ASSIGNED_TO', 'Project assigned to');
    define2('C_PROJECT_NAME', 'Project name');
    define2('C_PERCENTAGE_OF_TIME_ALLOCATED', 'Precentage of time allocated');
    define2("C_RETURN","Return");
    define2("C_SAVE", "Save");
    define2("C_DATA_STORED","Data stored");

    // ManageOntologyClassLabels by Mohammad Kahani
    define2("C_T_CREATE_EDIT_CLASS_LABELS","Create/Edit Class Labels");
    define2("C_T_CLASS_LABELS","Class Labels");

    //ManageOntologyClassChilds by Mohammad Kahani
    define2("C_T_ADD_CHILD_CLASS","Add Child Classes");
    define2("C_T_CHILD_CLASSES","Child Classes");
    define2("C_T_CLASS","Class");

    //ManageOntologyClassHirarchy by Mohammad Kahani
    define2("C_T_CREATE_EDIT_SUBCLASS","Create/Edit SubClasses ");
    define2("C_T_CHILD_CLASS","Child Class");
    define2("C_T_SUBCLASS","SubClasses");

    //ShowSummary() & ShowTabs in OntologyClasses.class by Mohammad Kahani
    define2("C_T_HIERARCHY_ONTOLOGY_CLASSES","Hierarchy Ontology Classes");


    //_________________ AMIN ALIZADEH _____________________________
    define2("C_PRINTSESSIONPAGE_MTH", "Meeting info");
    define2("C_PRINTSESSIONPAGE_TMFC", "Meeting");
    define2("C_PRINTSESSIONPAGE_TMTC", "title");
    define2("C_PRINTSESSIONPAGE_TMFOC", "date");
    define2("C_PRINTSESSIONPAGE_TMFIFC", "number");
    define2("C_PRINTSESSIONPAGE_TMSIXC", "start hour");
    define2("C_PRINTSESSIONPAGE_TMSEVENC", "time");
    define2("C_PRINTSESSIONPAGE_STFIRST", "order");
    define2("C_PRINTSESSIONPAGE_STSEC", "instructions");
    define2("C_PRINTSESSIONPAGE_STTIR", "results");
    define2("C_PRINTSESSIONPAGE_STFORTH", "follow up agent");
    define2("C_PRINTSESSIONPAGE_STFIF", "due time");

    define2("C_PRINTSESSIONPAGE_TTH", "presents");
    define2("C_PRINTSESSIONPAGE_TTFIRST", "order");
    define2("C_PRINTSESSIONPAGE_TTSEC", "first name & last name");
    define2("C_PRINTSESSIONPAGE_TTTIR", "presence");
    define2("C_PRINTSESSIONPAGE_TTFOR", "delay");
    define2("C_PRINTSESSIONPAGE_TTFIF", "signiture");
    define2("C_PRINTSESSIONPAGE_TTSIX", "submition date (sign)");
    define2("C_PRINTSESSIONPAGE_FTH", "absents");

    define2("C_ONT_MERG_CLASSES_PAGE_OFIR", "relation");
    define2("C_ONT_MERG_CLASSES_PAGE_OSEC", "sub class");
    define2("C_ONT_MERG_CLASSES_PAGE_OTIR", "");
    define2("C_ONT_MERG_CLASSES_PAGE_OFOR", "in");
    define2("C_ONT_MERG_CLASSES_PAGE_OFIF", "join");
    define2("C_ONT_MERG_CLASSES_PAGE_CH", "Join Recommends");
    define2("C_ONT_MERG_CLASSES_PAGE_CBFIR", "order");
    define2("C_ONT_MERG_CLASSES_PAGE_CBSEC", "class");
    define2("C_ONT_MERG_CLASSES_PAGE_CBTIR", "primary key");
    define2("C_ONT_MERG_CLASSES_PAGE_CBFOR", "relational key");
    define2("C_ONT_MERG_CLASSES_PAGE_CBFIF", "merge");
    define2("C_LOOKUP_PAGE_HELP_PAGE_HEADER", "Items search page instructions");

    define2("C_LOOKUP_PAGE_HELP_PAGE_CONTENT", "
        Sometimes the huge amount of data in a single field cause slow performance of selecting options for user and also consumes a large area
        of the page.
        </br>
        for such cases Look Up could be a good choice.
        </br>
        In this method a link is placed in front of this fields name which by clicking it a new window will be opened
        and user can search the desired item and select it as the value for corresponding field.
        <br>
        this page should be programed by the developer and the address should be placed in the 'search page address'.
        <br>
        values which system pass to the new page includes:
        <li>FormName: name of the form which contains the field.
        <li>InputName: the hidden element in the form which selected item key should be placed in its value
        <li>SpanName:  a span which contains the information about the selected item
        <br>
        for instance consider values below:
        <br>FormName=f1&InputName=PersonID&SpanName=MySpan
        <br>
        Developer should call a javascript code like below when the he selected the desired item:
    ");
    //Amir karami
    define2("C_Class", "Class");
    define2("C_Property", "Property");
    define2("C_SinCnnP","Search In Classes Name and Properties");
    define2("C_SinLabels","Search In Labels");
    define2("C_Relation","Relation");
    define2("C_CatSuggest","Categorise Suggestion");
    define2("C_Sentence1","Merge Suggestions or Heirachy Relation Connections(Classes which have common childes) ");
    define2("C_Sentence2","Parrent Relations-Invalid Child(Duplicate)");
    define2("C_MergeComp","Merge Complete");
    define2("C_Merge_Classes","Merge Classes");
    define2("C_Pclasses", "Parent Classes");
    define2("C_SubClasses", "Sub Classes");
    define2("C_Done", "Done");
    define2("C_CoE_Ontology","Create / Edit Ontology Classes");
    define2("C_Class_title","Class Title");
    define2("C_Label","Label");
    define2("C_Upper_Class","Upper Class");
    define2("C_Show_graph","Show Graph");
    define2("C_Cclasses", "Child Classes");
    define2("C_Merge_result","Merge Result");
    define2("C_Ontology_Classes","Ontology Classes");
    define2("C_Related_Prop","Related Properties");
    define2("C_Merge","Merge");
    define2("C_Area","Domain");
    define2("C_Prop_Labels","Properties Label");
    define2("C_Prop_Range","Properties Range");
    define2("C_Prop_Name","Properties Name");
    //_________________ END _____________________________

    //SignOut.php Mostafa Ghofrani
    define2("C_SESSION_EXPIRED", "Your session has been expired");
    define2("C_WELCOME", "Welcome");
    define2("C_RELOGING", "For loging in");
    define2("C_CLICK_THIS", "click here!");

    //TaskMessages.php Mostafa Ghofrani
    define2("C_LATEST_STATUS_OTHERS", "Latest tasks doneon your jobs by others ");
    define2("C_JOB_DONE", "Job done");
    define2("C_RELATED_USER", "Assigned user");
    define2("C_RELATED_TITLE", "Assigned job title");
    define2("C_LATEST_STATUS_YOU", "Latest tasks done on your jobs by yourself");
    define2("C_MORE_DET", "Show more");
    define2("C_LESS_DET", "Show less");
    define2("C_JOB_TITLE", "Job title");

    //HomePage.php and compare_ontologies By mohammadAfsharian
    define2("C_RECIVED_LETTER","Recived Letter");
    define2("C_COMPAIRE_ONTOLOGIES","Compair Ontologies");
    define2("C_CLASS","Class");
    define2("C_PROPERTICE","Propertice");
    //define2("C_TO","to");
    define2("C_CHOICE","Choice");
    define2("C_ALLOWED_ATTRBIUTE_DATA" , "Allowed Attribute Data");
    define2("C_ABUSES_AND_SYMMETRIES","Abuses And Symmetries");
    define2("C_CLEAR_PRE_EXITING_MAPPING","Clear Pre-Existing Mapping");
    define2("C_COMPARE_THE_ELEMENT","Compare The Element");
    define2("C_WITH","With");
    define2("C_SHOWING_COMPARISON_RESULTS","Showing Comparison Result");
    define2("C_SEMI_AUTOMATED_MAPPING","Semi Automated Mapping");
    //define2("C_MESSAGES","Message");

    //Manage_projects By mohammadAfsharian
    //define2("C_TITLE","Title");
    //define2("C_SEARCH","Search");
    //define2("C_PROJECT_GROUP","Project Group");
    define2("C_RELATED_SYSTEM","Related System");
    define2("C_CONDITION","Condition");
    //define2("C_REMOVE","Remove");
    define2("C_CREAT","Creat");
    define2("C_RESPONSIV"," Responsiv");
    //define2("C_EDIT","Edit");
    //define2("C_ROW","Row");
    define2("C_PARTS","Parts");
    define2("C_PROJECT","Project");
    //define2("C_PRIORITY","Priority");
    //define2("C_ARE_YOU_SURE","Are You Sure?");
    define2("C_CHOSE_YOUR_CONDITIONS", "Chose Your Conditions");
    define2("C_NOT_STARTED","Not Started");
    define2("C_ONGOING","ongoing");
    define2("C_SUPPORTED","supported");
    define2("C_FINISHED","finished");
    define2("C_SUSPENDED","Suspended");
    define2("C_PROJECT_MEMBER","Project Member:");
    define2("C_CHOISE","[choise]");
    define2("C_BY_MY_OWN","By My Own");
    define2("C_CREATE_EDIT_POSBBLE","edit/create poss");
    define2("C_PAGE_ADDRE","page address");
    define2("C_LIST_SYSTEM_POSSIBILITIES"," list of sysytem possibilited");


    /**
     * define Constant English Word
     * OntologyMergePropeperties by ArefNazari
     **/

    define2("C_DIR","ltr");
    define2("C_ROW","Row");
    define2("C_PRIORITY","Priority");
    define2("C_DOMAIN","Domain");
    define2("C_RANGE","Range");
    define2("C_ALLOWED_VALUES","Allowed values");
    define2("C_MERGE_SUGGESTIONS","Merge suggestions");
    define2("C_ACTIONS","Actions");

    define2("C_PRIORITY2","Property 2");
    define2("C_DOMAIN_PRIORITY2","Domain Property 2");
    define2("C_RANGE_PRIORITY2","Range Property 2");
    define2("C_ALLOWED_VALUES2","Allowed values 2");

    define2("C_MERGE","Merge");
    define2("C_NOT_MERGE","Not Merge");
    define2("C_NAME_OF_USER","name of user");
    define2("C_NAME_AND_FAMILY","name and last name");
    define2("C_PASSWORD","password");
    define2("C_LIST_OF_USERS","list of users");
    define2("C_REGISTERED_INFO","registered information");
    define2("C_USER","user");
    define2("C_PAGES","pages");

    /**
     * define Constant English Word
     * OntologyMergePropeperties by Reza Latifi
     **/
    define2("C_USER_NO_PERMISSION", "You don't have permission");
    define2("C_FINAL_ACEEPT", "final agreement");
    define2("C_IDENTIFICATION_CODE", "ID");
    define2("C_STEP_CODE", "step code");
    define2("C_DEPARTMENT_CODE","department code");
    define2("C_QUESTIONNAIRE_ALREADY_FILLED", "You have filled this questionnairy brfore");
    define2("C_NO_PERMISSION_TO_FILL_QUES", "You don't have permission to fill this questionnairy");
    define2("C_INFORMATION_SAVED", "information saved");
    define2('C_LOGOUT', 'logout');
    define2("C_EDUCATIONAL", "educational");
    define2("C_RESEARCH", "research");
    define2("C_OFFICIAL", "official");
    define2("C_FINANCIAL", "financial");
    define2('C_PERSONAL', 'personal');
    define2('C_OTHER', 'other');
    define2('C_OUTPUT_TYPE', 'output type');
    define2('C_CALCULATION_TYPE', 'calculation type');
    define2('C_CALCULATION_QUERY', 'calculation request');
    define2('C_CODE_FILE_NAME', 'source code file name');
    define2('C_ENTITY_TYPE', 'entity type');
    define2('C_REGULAR_EXPRESSION', 'regular expression');
    define2('C_MIN_VALID_VALUE', 'minimum valid value');
    define2('C_MAX_VALID_VALUE', 'maximum valid value');
    define2('C_DOMAIN_TABLE', 'valid values domain table');
    define2('C_OWNER', 'owner');
    define2('C_CATEGORY', 'category');
    define2("C_UPDATER", "updater:");
    define2("C_DETAILS", "details");
    define2("C_UPDATE_DATE", "update date");
    define2("C_ORDER", "order");
    define2("C_ENTITY", "entity");
    define2("C_USED_KEY", "used key");
    define2('C_STATEMENT_TO_GO_TO_THE_NEXT_STATE','statement to go to the next state');
    define2('C_FIELD_NAME','field name');
    define2('C_STATEMENT', 'term');
    define2('C_EQUAL', 'equal');
    define2('C_CONTAIN', 'contain');
    define2('C_GREATER_THAN', 'greater than');
    define2('C_LESS_THAN', 'less than');
    define2('C_GREATER_THAN_OR_EQUAL', 'greater than or equal');
    define2('C_LESS_THAN_OR_EQUAL', 'less than or eqaul');
    define2('C_NOT_EQUAL', 'not equal');
    /**
     * END
     **/

    // Saeed rastegar moghaddam - MyPayments.php
    define2("C_MY_REPORTS","My payment reports");
    define2("C_MY_DATE","Date");
    define2("C_MY_PRICE","Price");
    define2("C_MY_INFO","Info");
    define2("C_MY_FILE","File");


    // Saeed rastegar moghaddam - MyProjectMembers.php
    define2("C_NO_PERMISSION","you dont have the permission to view this record");
    define2("C_DATA_STORED","data has been stored");
    define2("C_MY_TITLE_PROJECTMEMBERS","Edit / Create Project Members");
    define2("C_USER_CODE","Uer Code");
    define2("C_PERMISSION_TYPE","Permission Type");
    define2("C_USER_PARTNERSHIP_PERSENTAGE","User persentage of partnership in project");
    define2("C_STORE","store");
    define2("C_CLOSE","close");

}
else
{
    define2("C_EDIT_REF_CONTENT", "ثبت محتوا");
    define2("C_FA_PAGE", "شماره صفحه");
    define2("C_FA_CONTENT", "محتوای صفحه");
    define2("C_REFINE", "اصلاح");
    define2("C_REF_CONTENT", "محتوای منبع");
    define2("C_VOCAB", "واژگان");


    define2("C_BACK", "بازگشت");
    define2("C_MY_TITLE_PROJECT_COMMENTS", "یادداشت");
    define2("C_COMMENTS", "یادداشتها");
    define2("C_CREATED_AT", "زمان ایجاد");
    define2("C_REQUISITIE_WORK", "کار پیش نیاز");
    define2("C_MY_TITLE_PROJECT_REQUISITES", "کارهای پیش نیاز");
    define2("C_REQUIREMENTS_JOB", "پیش نیازها");
    define2("C_MY_TITLE_PRE_REQUIREMENTS", "پیش نیازها");

    define2("C_WORK_CODE", "مجریان");
    define2("C_READY_FOR_CONTROL", "آماده برای کنترل");
    define2("C_CONSIDER_WORK_CREATION_TIME_RANGE", "بازه  ایجاد");
    define2("C_CREATION_TIME_RANGE", "بازه ایجاد");
    define2("C_CONSIDER_ACTION_CREATION_TIME_RANGE", "بازه اقدام");
    define2("C_ACTION_TIME_RANGE", "بازه اقدام");
    define2("C_SEARCH_RESULTS", "نتیجه جستجو");
    define2("C_LAST_ACTION_TIME", "زمان آخرین اقدام");
    define2("C_EXECUTORS", "مجریان");
    define2("C_VIEWERS", "ناظران");
    define2("C_TIME_TO_DO", "زمان انجام");

    define2("C_LIST_OF_USERS","لیست کاربران");
    define2("C_REGISTERED_INFO","اطلاعات ذخیره شد");
    define2("C_USER","کاربر");
    define2("C_PAGES","صفحات");

    define2("C_NAME_OF_USER","نام کاربری");
    define2("C_NAME_AND_FAMILY","نام و نام خانوادگی");
    define2("C_PASSWORD","کلمه عبور");
    define2("C_PAGE_ADDRE","آدرس صفحه");
    define2("C_LIST_SYSTEM_POSSIBILITIES","لیست امکانات صفحه");
    define2("C_CREATE_EDIT_POSBBLE","ایجاد/ویرایش امکانات");
    define2("C_SAVE", "ذخیره");
    define2("C_NEW", "جدید");
    define2("C_REMOVE", "حذف");
    define2("C_NAME", "نام");
    define2("C_CREATE", "ایجاد");
    define2("C_ORDER", "ترتیب");
    define2("C_ROW", "ردیف");
    define2("C_EDIT", "ویرایش");
    define2("C_PERSIAN", "فارسی");
    define2("C_ENGLISH", "انگلیسی");

    define2("C_DATA_SAVE_SUCCESS", "اطلاعات با موفقیت ذخیره شد");
//    define2("C_ARE_YOU_SURE", "مطمئن هستید؟");
    define2("C_ACTIVE_USER", "کاربر فعال: ");
    define2("C_MAIN_MENU", "منوی اصلی");
    define2("C_FIRST_PAGE", "صفحه اول");
    define2("C_CHANGE_PASSWORD", "تغییر رمز عبور");
    define2("C_MY_ACTIONS", "اقدامات من");
    define2("C_EXIT", "خروج");

    // NewProjectTaskTypes.php by Mostafa Sader
    define2("C_CREATE_EDIT_TYPES_OF_WORKS","ایجاد/ویرایش انواع کارها");
    define2("C_DESIRED_TITLE","عنوان مورد نظر");
    // NewProjectTaskActivities.php by Mostafa Sader
    define2("C_ACTION_DATE"," تاریخ اقدام");
    define2("C_TYPE_OF_ACTION"," نوع اقدام");
    define2("C_TIME_CONSUMING","زمان مصرفی");
    define2("C_PROGRESS","درصد پیشرفت");
    define2("C_ATTACHED_FILE","فایل ضمیمه");
    define2("C_CHANGED_TABLES","جداول تغییر داده شده");
    define2("C_CHANGED_PAGES","صفحات تغییر داده شده");

    //SendMessage.php needed definitions MOHAMAD_ALI_SAIDI
    define2("C_TITLE","عنوان");
    define2("C_TEXT","متن");
    define2("C_FILE","محتوای فایل");
    define2("C_TO_USER","به کاربر");
    define2("C_SEND","ارسال");
    define2("C_SELECT","انتخاب");
    define2("C_SEND_MESSAGE","ارسال پیام");
    define2("C_MESSAGE_SENT","پیام ارسال شد");
    define2("C_TITLE_EMPTY","عنوان را وارد کنید");
    define2("C_RECEIVER_EMPTY","گیرنده را مشخص کنید");
    define2("C_AUTO_SAVE","ذخیره سازی خودکار..");
    define2("C_SENDING_FILE_ERROR"," خطا در ارسال فایل");
    //----------------------------------
    //MailBox.php needed definitions MOHAMAD_ALI_SAIDI

    define2("C_MESSAGES_RECEIVED","نامه های رسیده");
    define2("C_SENDER_NAME","فرستنده");
    define2("C_TIME_SENT","زمان ارسال");
    define2("C_REPLY_DES","شرح ارجاع");
    define2("C_DELETE","حذف");

    //----------------------------------

    //SentBox.php needed definitions MOHAMAD_ALI_SAIDI
    define2("C_MESSAGES_SENT","نامه های ارسالی");
    define2("C_RECEIVER_NAME","دریافت کننده");
    //----------------------------------


    //SearchMessage.php needed definitions Alireza Imani
    define2("C_SEARCH_MESSAGE","جستجوی نامه");
    define2("C_PART_OF_TEXT","بخشی از متن");
    define2("C_CHOOSE","انتخاب");
    define2("C_FROM_DATE","از تاریخ");
    define2("C_TO_DATE","تا تاریخ");
    define2("C_SEARCH","جستجو");

    //MetaData2Onto.php needed definitions Alireza Imani
    define2("C_CHOOSE_CONDITIONS_FOR_REVERSE_ENGINEERING","انتخاب شرایط برای مهندسی معکوس");
    define2("C_INTENDED_SCOPES","حوزه‌های مورد نظر");
    define2("C_EDUCATIONAL","آموزشی");
    define2("C_RESEARCH","پژوهشی");
    define2("C_STUDENT_SERVICES","خدمات دانشجویی");
    define2("C_WELFARE","رفاهی");
    define2("C_FINANCIAL","مالی");
    define2("C_SUPPORT","پشتیبانی");
    define2("C_ADMINISTRATIVE","اداری");
    define2("C_RELATED_TO_SYSTEM_OPERATIONS","مرتبط با عملیات سیستمی");
    define2("C_TARGET_ONTOLOGY","هستان نگار مقصد");
    define2("C_REMOVE_PREVIOUS_MERGE_SUGGESTIONS","حذف پیشنهادهای ادغام قبلی");
    define2("C_REMOVE_EXISTING_ELEMENTS_OF_ONTOLOGY","حذف عناصر موجود در هستان نگار");
    define2("C_PERFORM_REVERSE_ENGINEERING","انجام مهندسی معکوس");
    define2("C_REVIEW_PROPERTIES_MERGING_SUGGESTIONS","بررسی پیشنهاد ادغام خصوصیت ها");
    define2("C_REVIEW_INTEGRATION_SUGGESTIONS","بررسی پیشنهادهای تجمیع");
    define2("C_HIERARCHICAL_RELATIONSHIPS_BETWEEN_CLASSES","روابط سلسله مراتبی بین کلاس ها");
    define2("C_CONVERSION_DONE","تبدیل انجام شد");

    //ShowTermsManipulationHistory.php needed definitions Alireza Imani
    define2("C_COMPLETED_TASK","عمل انجام شده");
    define2("C_DESCRIPTION","شرح");
    define2("C_SUBJECT","عمل کننده");
    define2("C_TIME","زمان");
    define2("C_EXTRACT_NEW_WORD","استخراج واژه‌ی جدید");
    define2("C_REMOVE_WORD","حذف واژه");
    define2("C_MERGE_TWO_WORDS","ادغام دو واژه");
    define2("C_CHANGE_WORD","تغییر واژه");
    define2("C_REPLACE_WORD","جایگزینی واژه");
    define2("C_TO","به");
    define2("C_BY","با");

    //NewQuestionnare.php ALI NOORI
    define2("C_CREATING_EDITTING_QUESTIONNARE","ایجاد/ویرایش پرسشنامه");
    define2("C_FORM_NAME","عنوان فرم");
    define2("C_FORM_EXPLANATION_UP","توضیحات بالای فرم");
    define2("C_FORM_EXPLANATION_DOWN","توضیحات پایین فرم");
    define2("C_TYPE_SHOW_ENTER_DATA_LAYOUT","نوع نمایش صفحه ورود داده");
    define2("C_ONE_COLUMN","یک ستونی");
    define2("C_TWO_COLUMN","دو ستونی");
    define2("C_WIDTH_QUESTION_COLUMN","عرض ستون سوالات");
    define2("C_MARGIN_SECOND_ROWS","حاشیه برای ردیفهای فرم");
    define2("C_MARGIN_SECOND_ROWS_YES","قرار داده شود");
    define2("C_MARGIN_SECOND_ROWS_NO","قرار داده نشود");
    define2("C_RETURN","بازگشت");
    define2("C_TABLE_INFORMATION","جدول اطلاعاتی مربوطه");
    define2("C_BANK_INFORMATION","بانک اطلاعاتی مربوطه");
    define2("C_FORMATION USER","کاربرسازنده");
    define2("C_CREATE_TIME","تاریخ ایجاد");
    define2("C_MANAGE_OPTIONS","مدریت گزینه ها");
    define2("C_MANAGE_DETAILS_TABLES","مدیریت جداول جزییات");
    //-----------------------------
    //MyTimeReport.php ALI NOORI
    define2("C_YEAR","سال :");
    define2("C_MONTH","ماه :");
    define2("C_SHOW_REPORT_ACTIONS","نمایش گزارش اقدامات کاری");
    define2("C_USAGE_TIME_REPORT","گزارش زمان مصرفی");
    define2("C_DATE","تاریخ");
    define2("C_ACTIVITY","فعالیت");
//    define2("C_TIME","زمان");
    define2("C_TOTAL","مجموع");
    define2("C_RIAL","ریال");
    //-------------------------
    //CompareAllOntos.php ALI NOORI
    define2("C_COMPARE_COVER_HASTAN_NEGAR","مقایسه همپوشانی هستان نگار");
    define2("C_WITH_OTHER_HASTAN_NEGAR"," با سایر هستان نگاره");
    define2("C_NAME_HASTAN_NEGAR","نام هستان نگار");
    define2("C_PERCENTAGE_MAPPING_CLASS","درصد نگاشت کلاسه");
    define2("C_PROPERTIES_MAPPING_PERCENTAGE","درصد نگاشت خصوصیات");
    //--------------------------

    //ShowTermReferHistory.php Hossein Lotfi
    define2("C_SOURCE_NAME","نام منبع");
    define2("C_PAGE","صفحه");
    define2("C_PARAGRAPH","پاراگراف");
    define2("C_SUBMIT_NEW_REFERENCE", "ثبت ارجاع جدید");
    define2("C_REMOVE_REFERENCE", "حذف ارجاع");
    define2("C_CHANGE_REFERENCE", "تغییر ارجاع");
    define2("C_REPLACE_REFERENCE_WITH", "جایگزینی ارجاع به");
    define2("C_WITH_REFERENCE_TO", "با ارجاع به");
    define2("C_S", "ص");
    define2("C_P", "پ");

    //SelectStaff.php Hossein Lotfi
    define2("C_LAST_NAME","نام خانوادگی");
    define2("C_REMOVE_PREVIOUS_CHOICE","حذف انتخاب قبلی");
    define2("C_FULL_NAME","نام و نام خانوادگی");

    //ManageTermReferences.php Hossein Lotfi
    define2("C_CONTENT","محتوا");
    define2("C_TERM", "اصطلاح");
    define2("C_FREQUENCY","فراوانی");
    define2("C_INFORMATION_SAVED","اطلاعات ذخیره شد");
    define2("C_CREATE_EDIT_TERMS_REFERENCES","ایجاد/ویرایش منابع اصطلاحات");
    define2("C_FILE2","فایل");
    define2("C_GET_FILE", "دریافت فایل");
    define2("C_TERMS_REFERENCES","منابع اصطلاحات");
    define2("C_TERMS","اصطلاحات");
    define2("C_STATISTICAL_ANALYSIS","تحلیل آماری");

    //SelectMultiStaff.php By Ehsan Amini
    define2("C_USER_NAME", "نام کاربر");

    //NewRequest.php By Ehsan Amini
    define2("C_TASK_REQUEST", "درخواست انجام کار");
    define2("C_IF_REQUEST_IS_ABOUT_CHANGING_ACCESS_TO_DATABASE_DATA_CLICK_HERE", "[در صورتیکه درخواست به منظور ایجاد تغییرات دستی بر روی داده های بانک اطلاعاتی است اینجا را کلیک کنید]");
    define2("C_UNKNOWN_SYSTEM_CODE", "کد سیستم نامشخص است");
    define2("C_NO_RESPONSE_HAS_BEEN_DETERMINED_FOR_THIS_PROJECT", "برای این پروژه پاسخگویی تعیین نشده است");

    //CreateKartableHeader function in ProjectTasks.class.php By Ehsan Amini
    define2("C_CURRENT_TASKS", "کارهای جاری");
    define2("C_PROJECTS_MEMBERS", "اعضای پروژه ها");
    define2("C_TASKS_IN_NEED_OF_CONTROL", "کارهای نیازمند کنترل");
    define2("C_DONE_TASKS", "کارهای انجام شده");
    define2("C_CREATED_TASKS", "کارهای ایجاد شده");

    //ShowAllPersonStatus.php By Ehsan Amini
    define2("C_PROJECTS_COUNT", "تعداد پروژه ها");
    define2("C_TIME_PERCENTAGE_ALLOCATED", "درصد تخصیصی زمان");
    define2("C_LAST_NAME_AND_FIRST_NAME", "نام خانوادگی و نام");
    define2("C_THIS_LIST_SHOWS_MEMBERS_OF_THE_PROJECTS_THAT_YOU_ARE_MANAGING_OR_SUBORDINATE_TO_THE_ORGANIZATIONAL_UNIT_UNDER_YOUR_MANAGEMENT", "در این لیست اعضای پروژه هایی که شما مدیر آنها هستید و یا در زیرمجموعه واحد سازمانی تحت مدیریت شماست نمایش داده میشوند");
    define2("C_FOR_ADJUSTING_PERCENTAGES_YOU_CAN_CLICK_ON_PERCENTAGE_NUMBER_IN_EACH_ROW","برای تنظیم درصدها میتوانید روی عدددرصد در هر ردیف کلیک نمایید");

    //SessionTypes.class.php By Arman Ghoreshi
    define2("C_SESSION_LOCATION","محل تشکیل");
    define2("C_SESSION_INFO","مشخصات اصلی");
    define2("C_SESSION_PERMITTED_PERSON","کاربران مجاز");
    define2("C_SESSION_MEMBERS","اعضا");
    //NewSessionTypes.php By Arman Ghoreshi
    define2("C_SESSION_CREATE_EDIT","ایجاد/ویرایش الگوهای جلسه");
    define2("C_START_TIME","زمان شروع");
    define2("C_END_TIME","زمان پایان");
    //managePersonPermittedSessionType.php By Arman Ghoreshi
    define2("C_SESSION_PERMITTED_CREATE_EDIT","ایجاد/ویرایش کاربران مجاز الگوهای جلسه");
    define2("C_SESSION_PERMITTED_LIST","کاربران مجاز الگوهای جلسات");
    define2("C_PERMISSIONS","دسترسی ها");
    //ManageSessionTypeMembers.php By Arman Ghoreshi
    define2("C_ROLE","نقش");
    define2("C_SESSIOM_MEMBERS","اعضای الگوهای جلسه");
    define2("C_ARE_YOU_SURE","آیا مطمین هستید؟");
    define2("C_SESSION_MEMBERS_CREATE_EDIT","ایجاد/ویرایش اعضای الگوهای جلسه");
    define2("C_PERSONEL","پرسنل");
    define2("C_OTHER","سایر");
    define2("C_MEMBERSHIP_TYPE","نوع عضو");
    define2("C_MEMBER_PERSONAL_ID","کد شخصی عضو");
    define2("C_SESSION_APPROVAL","برگزاری جلسه منوط به تایید این کاربر است");
    define2("C_SIGN_MINUTES","اجازه امضای صورتجلسه");
    define2("C_ELECTRONIC_SIGN","برای قطعی شدن صورتجلسه نیاز به امضای الکترونیکی فرد می باشد");
    define2("C_YES","بلی");
    define2("C_NO","خیر");


    //ManageFacilityPages.php By Naghme Mohammadifar
    define2("C_SAVED_INFO","اطلاعات ذخیره شد!");
    define2("C_CREATE_EDIT_A_PAGE_RELATED_TO_FEATURE","ایجاد/ویرایش صفحه مرتبط با امکان");
    define2("C_TITLE_PAGE","صفحه");
    define2("C_CLOSE", "بستن");
    define2("C_CONFIRM_TO_DELETE","آیا مطمئن هستید؟");
    define2("C_RELATED_PAGES_TO_THIS_FEATURE","صفحات مرتبط با این امکان");
    define2("C_PAGE_PLACE_HOLDER","نام صفحه مورد نظر");

    //NewResearchProjectComments.php Alireza Forghani Toosi
    define2("C_SEASON", "فصل");
    define2("C_CREATE_EDIT_RESEARCH_PROJECT_COMMENT", "ایجاد/ویرایش یادداشت کار پژوهشی");
    define2("C_COMMENT_CHANGE_HISTORY", "سابقه ی تغییرات روی این یادداشت");

    //ResearchProject.class.php Alireza Forghani Toosi
    define2("C_SEASONS", "فصول");
    define2("C_REFERENCE_TYPES", "انواع منابع");
    define2("C_REFERENCES", "منابع");
    define2("C_NOTES", "یادداشتها");
    define2("C_OUTPUTS", "خروجی ها");
    define2("C_PRIVILEGES", "دسترسی ها");
    define2("C_MAIN_PROPERTIES", "مشخصات اصلی");

    //Managemessages.php By kouroshAtaei

    define2("CREATE_EDIT","ایجاد/ویرایش پیام");
    define2("C_MESSAGE" , "متن پیام");
    define2("AT_FILE" ,"فایل پیوست");
    define2("REC_FILE" , "دریاف فایل");
    define2("PIC" ,"تصویر");
    define2("START_TIME" ,"زمان شروع");
    define2("END_TIME", "زمان پایان" );
    define2("SAVE_M" , "ذخیره");
    define2("NEW_M" ,"جدید");
    define2("SEARCH_M" ,"جستجو");
    define2("MESSAGES_M" , "پیام ها");
    define2("CREATOR_M" , "ایجاد کننده ");
    define2("CREATE_TIM_M" , "زمان ایجاد");
    define2("ROW_M" , "ردیف");
    define2("EDIT_M" , "ویرایش");
    define2("DELETE_M" ,"حذف");
    define2("ARE_YOU_SURE" , "آیا مطمئن هستید ؟");
    define2("ERROR_SEND" ,"خطا در ارسال فایل");
    define2("INFO_SAVED" , "اطلاعات ذخیره شد");
    //newResearchProjectRefrences.php By kouroshAtaei
    define2("CREAT_AND_EDIT_RES_RESEARCH" , "ایجاد/ویرایش منبع کار پژوهشی");
    define2("SEARCH_ENG" , "موتور جستجو");
    define2("TAGS_WORDS" ,"کلمات کلیدی جستجو");
    define2("LANG_N" ,"زبان");
    define2("EN_LAN_N", "انگلیسی");
    define2("FA_LAN_N", "فارسی");
    define2("TITLE_N" ,"عنوان");
    define2("WRITERS_N", "نویسندگان");
    define2("YEARS_N", "سال");
    define2("SUM_N", "چکیده");
    define2("STATE_OF_STUDY", "وضیعت مطالعه");
    define2("ALREADY_STUDY", "مطالعه شده");
    define2("ALREADY_NOT_STUDY", "مطالعه نشده");
    define2("STUDING", "در حال مطالعه");
    define2("IMPORTNT", "اهمیت");
    define2("CAT_N", "دسته");
    define2("ALL_COM", "نظر کلی");
    define2("FILE_N" , "فایل");
    define2("NOTES_N", "یادداشتها");
    define2("CLOSE_N" , "بستن");

    //manageFieldsDataMapping.php By kouroshAtaei
    define2("SELECTION_M" , "انتخاب جدول و فیلد مربوطه برای تعیین جدول معادلسازی مقادیر");
    define2("TABLE_M" , "جدول") ;
    define2("DEF_TABLE", "تعریف جدول معادلسازی");
    define2("VAL_FIELD_M","مقادیر معادلسازی شده برای فیلد مربوطه");
    define2("REAL_VAL" ,"مقدار اصلی");
    define2("M_VAL_EQ" , "مقدار معادل");
    define2("DATABASE" , "بانک اطلاعاتی");
    define2("FIELD_M" ,"فیلد");

    //ManagerDesktop.php Alireza Forghani Toosi
    define2("C_MESSAGES", "پیام ها");
    define2("C_RECEIVED_LETTERS", "نامه های رسیده");
    define2("C_ATTACHMENTS", "ضمیمه");

    //ManageProjectMilestones.php by Sajjad Iranmanesh
    define2("C_CREATE_EDIT_IMPORTANT_DATE", "ایجاد/ویرایش تاریخهای مهم");
    define2("C_DATE", "تاریخ");
    define2("C_DESCRIPTION", "شرح");
    define2("C_SAVE", "ذخیره");
    define2("C_NEW", "جدید");
    define2("C_ROW", "ردیف");
    define2("C_EDIT", "ویرایش");
    define2("C_IMPORTANT_DATES", "تاریخ های مهم");

    //ManageOntologyPropertyLabels.php By Javad Mahdavian
    define2("C_CREATE_EDIT_LABELS" , "ایجاد/ویرایش برچسبهای خصوصیات");
    define2("C_LABEL" , "برچسب");
    define2("C_LABELS" , "برچسبها");

    //ManageProjectTaskActivityTypes.php by Sajjad Iranmanesh
    define2("C_CREATE_EDIT_ACTIONS", "ایجاد/ویرایش انواع اقدامات");
    define2("C_TITLE", "عنوان");
    define2("C_SAVE", "ذخیره");
    define2("C_NEW", "جدید");
    define2("C_ACTIONS_TYPES", "انواع اقدامات");
    define2("C_ACTIONS_COUNT", "تعداد اقدامات");
    define2("C_DELETE", "حذف");
    define2("C_DONT_HAVE_PERMISSION", "مجوز مشاهده این رکورد را ندارید");

    //ManageProjectTaskAssignedUsers.php by Sajjad Iranmanesh
    define2("C_CREATE_EDIT_USERS_ASSIGNED_TO_ACTIVITY", "ایجاد/ویرایش کاربران منتسب به کار");
    define2("C_ASSIGNEE_DESCRIPTION", "شرح انتساب");
    define2("C_PARTICIPATION_PERCENTAGE", "درصد مشارکت");
    define2("C_ECECUTOR", "مجری");
    define2("C_VIEWER", "ناظر");
    define2("C_SEND_LETTER_FROM_ADVERTISER", " ارسال نامه آگاهی دهنده برای فرد انتخاب شده");
    define2("C_DONT_HAVE_VALUE", "مقداری در شخص مربوطه وارد نشده است");
    define2("C_USERS_ASSIGNED_TO_ACTIVITY", "کاربران منتسب به کار");


    //Manageontologies.php by Naghme Mohammadifar
    define2("C_ONTOLOGY_CLASSES" , "کلاس‌های هستان‌نگار");
    define2("C_ONTOLOGY_FEATURES" , "خصوصیات هستان‌نگار");
    define2("C_CREATE_EDIT_ONTOLOGY" , "ایجاد/ویرایش هستان نگار");
    define2("C_CLASSES","کلاس‌ها");
    define2("C_THING_FEATURES","خصوصیت شی");
    define2("C_DATA_FEATURES","خصوصیت داده");
    define2("C_TREE_STRUCTURE","ساختار درختی");
    define2("C_GET_OWL_CODE_FROM_STRUCTURE","دریافت کد OWL از روی ساختار");
    define2("C_GET_ER_CODE","دریافت کد ER");
    define2("C_CLASS_STATISTICAL_ANALYSIS","تحلیل آماری کلاس‌ها");
    define2("C_INTERNET_PATH","مسیر اینترنتی");
    define2("C_GETTING_FILE","دریافت فایل");
    define2("C_TRANSMIT_FILE_TO_DB","انتقال عناصر از فایل به پایگاه داده");
    define2("C_ONTOLOGY","هستان نگار");
    define2("C_FEATURES","خصوصیات");
    define2("C_EXPERT_JUDGES","خبرگان ارزیاب");
    define2("C_PRINT","چاپ");
    define2("C_PRINT_WITH_MERGE_SOURCES","چاپ - با منابع ادغام");
    define2("C_PRINT_WITH_VOCAB_EXTRACTION_SOURCES","چاپ - با منابع استخراج واژگان");
    define2("C_PRINT_WITH_DATABASE_SOURCES","چاپ - با منابع پایگاه داده");
    define2("C_DICTIONARY","دیکشنری");
    define2("C_FREQUENCY_ANALYSIS","تحیلی فراوانی");
    define2("C_DISTANCE_ANALYSIS","تحلیل فاصله levenshtein");
    define2("C_STATISTICAL_EVALUATION","ارزیابی آماری");
    define2("C_REVERSE_ENGINEERING","مهندسی معکوس RDB");
    define2("C_ANALYSIS_WITH_WORDNET","تحلیل با wordnet");
    define2("C_CONTENT_COMPARISON","مقایسه محتوایی");
    define2("C_MERGED_PROJECTS","پروژه های ادغام");
    define2("C_ALERT_TO_CLOSE","با اینکار عناصر قبلی حذف خواهند شد. اطمینان دارید؟");
    define2("C_CLASSES_REFERRED_TO_ONLY_ONCE","کلاسهایی که تنها یکبار مورد اشاره قرار گرفته اند (به عنوان خصوصیت نیز ارجاع نشده اند)");
    define2("C_PROPERTIES_THAT_ARE_ONLY_MENTIONED_ONCE","خصوصیاتی که تنها یکبار مورد اشاره قرار گرفته اند (به عنوان کلاس هم ارجاع نشده اند)");
    define2("C_ONTOLOGIES_LIST","فهرست هستان‌نگار‌ها");
    define2("C_CLASSES_IN_TERMS_OF_REFERRAL_RATES","کلاسها از نظر میزان ارجاع - بیش از دو ارجاع");
    define2("C_PROPERTIES_IN_ORDER_OF_REFERENCE","خصوصیات به ترتیب میزان ارجاع - بیش از دو ارجاع");
    define2("C_ENTITIES_THAT_WERE_ONCE_REFERRED_TO_AS_A_CLASS_AND_ONCE_AS_A_PROPERTY","موجودیتهایی که یکبار به عنوان کلاس و یکبار به عنوان خصوصیت مورد ارجاع بوده اند");
    define2("C_ONTOLOGY_TITLE","عنوان هستان نگار");
    define2("C_ONTOLOGY_TYPE","نوع هستان نگار");
    define2("C_CHECK_TITLES_WITH_OVER","بررسی عناوین با بیش از ");
    define2("C_PERCENTAGE_OF_SIMILARITY__BETWEEN_CLASS_TITLES","درصد مشابهت در بین عناوین کلاسها که برچسب فارسی یکسان ندارند و مربوط به هستان نگارهای متفاوت هستند");
    define2("C_CHECK_TAGS_WITH_OVER","بررسی برچسبها با بیش از ");
    define2("PERCENTAGE_OF_SIMILARITY_BETWEEN_DIFFERENT_TITLES_OF_ATTRIBUTES_THAT_DO_NOT_MATCH_THE_SAME_PERSIAN_TAG_AND_ARE_RELATED_TO_DIFFERENT_TYPOGRAPHERS","درصد مشابهت در بین عناوین خصوصیات که برچسب فارسی یکسان ندارند و مربوط به هستان نگارهای متفاوت هستند");
    define2("C_PERCENTAGE_OF_SIMILARITY_BETWEEN_PERSIAN_LABELS_FOR_CLASSES_THAT_ARE_NOT_IDENTICAL"," درصد مشابهت در بین برچسب فارسی کلاسهایی که عنوان یکسان ندارند و مربوط به هستان نگارهای متفاوت هستند");
    define2("C_PERCENTAGE_OF_SIMILARITY_AMONG_FARSI_LABELS_FOR_PROPERTIES_THAT_DO_NOT_HAVE_THE_SAME_TITLE_AND_ARE_DIFFERENT"," درصد مشابهت در بین برچسب فارسی خصوصیت هایی که عنوان یکسان ندارند و مربوط به هستان نگارهای متفاوت هستند");
    define2("C_CLASS_TITLE","عنوان کلاس");
    define2("C_PROPERTY_TITLE","عنوان خصوصیت");
    define2("C_CLASSES_WITH_SAME_NAME_ACCORDING_TO_WORDNET","کلاس‌‌ها با نام مشابه براساس Wordnet");
    define2("C_PROPERTIES_WITH_SAME_NAME_ACCORDING_TO_WORDNET","خصوصیات با نام مشابه براساس Wordnet");
    define2("PERCENTAGE_OF_EACH_ONTOLOGY_COVERAGE_OTHER_ONTOLOGY_CLASSES","درصدی از هر هستان‌نگار که سایر کلاس‌های هستان‌نگار را پوشش می دهد");
    define2("C_AVERAGE","میانگین");
    define2("C_CLASS_COUNT","تعداد کلاس");
    define2("C_PROPERTY_COUNT","تعداد خصوصیت");
    define2("C_AVERAGE_OF_COVERAGE_OTHER_ONTOLOGY_CLASSES","میانگین پوشش سایر هستان‌نگارها (کلاس‌ها)");
    define2("C_REPOSITORY","مخزن");
    define2("C_PERCENTAGE_OF_EACH_ONTOLOGY_COVERAGE_OTHER_ONTOLOGY_PROPERTIES","درصدی از هر هستان‌نگار که دیگر خصوصیات هستان‌نگار را پوشش می دهد");
    define2("C_AVERAGE_OF_COVERAGE_OTHER_ONTOLOGY_PROPERTIES","میانگین پوشش سایر هستان‌نگارها (خصوصیات)");
    define2("C_PERCENTAGE_OF_EACH_ONTOLOGY_COVERAGE_DOCUMENT_TERMS","درصد پوشش واژگان متن هر هستان‌نگار ");
    define2("C_TOTAL_ELEMENTS","عناصر کل");
    define2("C_COVERAGE_PERCENTAGE","درصد پوشش");
    // ------------------------------  ManageSessionType.php By Diba Aminshahidi ----------------------------
    define2("C_PATTERN","الگو های جلسه");
    define2("C_MEETING_TYPE","نوع جلسه");
    define2("C_SESSIONS","جلسات");
    define2("C_SESSION_NUMBER","شماره جلسه");
    define2("C_SESSION_TITLE","عنوان جلسه");
    define2("C_DURATION","مدت جلسه");
    define2("C_SESSION_STATUS","وضعیت جلسه");
    define2("C_CREATE","ایجاد");
    define2("C_INSTRUCTION_KEYWORD","کلمه کلیدی در دستور کار");
    define2("C_ENACTMENT_KEYWORD","کلمه کلیدی در مصوبه ها");
    define2("C_APPROVE", "تایید");
    define2("C_REJECTED","رد به دلیل");

    //TasksForControl.php -- by navidbeta
    define2("C_RELATED_PROJECT" , "پروژه مربوطه‌");
    define2("C_PRIORITY" , "اولویت");
    define2("C_T_TITLE" , "عنوان");
    define2("C_CREATOR" , "ایجاد کننده");
    define2("C_CREATED_TIME" , "زمان ایجاد");
    define2("C_T_AREUSURE" , "آیا مطمئن هستید؟");

    //ManageUserFacilities.php -- by navidbeta
    define2("C_ADD_USER_FACILITY" , "اضافه کردن دسترسی کاربر");
    define2("C_T_USER" , "کاربر");
    define2("C_PRIVILEGED_USERS" , "کاربران دارای دسترسی");
    define2("C_POSSIBILITY" , "امکان");

    //ManageUserPermissions.php -- by navidbeta
    define2("C_DATA_STORED" , "اطلاعات ذخیره شد");
    define2("C_USER_ACCESSES" , "دسترسی های کاربر");
    define2("C_MENUS" , "منوها");
    define2("C_T_RETURN" , "بازگشت");

    //additionals for classes -- by navidbeta
    define2("C_CONTRACTS" , "قرارداد ها‌");
    define2("C_APPROVAED_CREDIT" , "اعتبار مصوب‌");
    define2("C_PROJECT_PROGRESS" , "پیشرفت پروژه");
    define2("C_OTHER_RESOURCES" , "سایر منابع");
    define2("C_GROUP" , "گروه");
    define2("C_T_PAGEADDRESS" , "آدرس صفحه");
    define2("C_ORDERNUM" , "شماره ترتیب");

    //ManagePermittedDatabases by Javad Mahdavian
    define2("C_DATABASE_DOC" , "مستند سازی پایگاه های داده");
    define2('C_DATABASES', 'پایگاه های داده');
    define2("C_SERVER", "سرور");
    define2("C_TABLES" , "جداول");
    define2("C_DEADLINE" , "مهلت انجام");

    //ManagePersons.php Hoormazd Ranjbar
    define2("C_CREATING_EDITTING_PERSONS", "ایجاد/ویرایش افراد");
    define2("C_MP_EMAIL", "ایمیل");
    define2("C_MP_MOBILE", "موبایل");
    define2("C_MP_USERNAME", "حساب کاربری");
    define2("C_MP_PEOPLE_LIST", "لیست افراد");
    define2("C_MP_IMAGE", "تصویر");
    define2("C_MP_PAYMENTS", "پرداختها");

    //ManageQuestionnaires.php Hoormazd Ranjbar
    define2("C_MQ_FILTER", "عنوان فیلتر");
    define2("C_MQ_CODE", "کد");
    define2("C_MQ_MAIN_FORM", "فرم اصلی");
    define2("C_MQ_MANAGERS", "مدیران");
    define2("C_MQ_SETTINGS", "تنظیمات");
    define2("C_MQ_CREATOR", "ایجاد کننده");
    define2("C_MQ_CREATE_DATE", "تاریخ ایجاد");
    define2("C_MQ_FILL", "پر شده");
    define2("C_MQ_LAST_ACCEPT", "تایید نهایی");
    define2("C_MQ_MAKE", "ایجاد");

    //MyActions.php Hoormazd Ranjbar
    define2("C_MYACTIONS", "اقدامات انجام شده");
    define2("C_MA_ACTION", "عمل انجام شده");
    define2("C_MA_DONE_DATE", "تاریخ انجام شده");
    define2("C_MA_TOTAL_FIND", "تعداد کل موارد یافت شده");



    //Manageterms.php by Naghme mohammadifar
    define2("C_STRUCTURAL_SIMILARITY","شباهت ساختاری");
    define2("C_PEERS_IN_THE_SEMANTIC_NETWORK","همرده در شبکه معنایی");
    define2("C_MORE_SPECIFIC_MEANING_IN_THE_SEMANTIC_GRID","معنای خاص تر در شبکه معنایی");
    define2("C_MORE_GENERAL_MEANING_IN_THE_SEMANTIC_GRID","معنای عام تر در شبکه معنایی");
    define2("C_PERIODICITY","تناوب");
    define2("C_WORD","واژه : ");
    define2("C_RECORDER","ثبت کننده : ");
    define2("C_REFERENCES_IN_WORDS","ارجاعات: ");
    define2("C_WORD_CODE","کد");
    define2("C_SIMILARITY_TYPE","نوع شباهت");
    define2("C_RESOURCES_AND_THE_NUMBER_OF_REPETITIONS","منابع و تعداد تکرار");
    define2("C_REPLACEMENT","جایگزینی");
    define2("C_REPLACE_THE_SELECTED_WORD_WITH_THE_WORD_IN_THIS_ROW","واژه انتخابی با واژه این ردیف جایگزین شود");
    define2("C_CREATE_EDIT_TERMS","ایجاد/ویرایش اصطلاحات");
    define2("C_NOTE","یادداشت");
    define2("C_MERGE_SUGGESTIONS","پیشنهادات ادغام");
    define2("C_CREATION_TIME","زمان ایجاد");
    define2("C_ONTOLOGY_ELEMENT","عنصر هستان نگار");
    define2("C_RECORD","ثبت");
    define2("C_SUMMARY_OF_INFORMATION","خلاصه اطلاعات");
    define2("C_MAPPING_OF_IDIOMS_AND_ELEMENTS_OF_HISTOGRAM","نگاشت اصطلاحات و عناصر هستان نگار");


    ///// Mahdi Ghayour /////
    define2("C_TASK", "کار");
    define2("C_TASK_TYPE", "نوع کار");
    define2("C_DOCUMENT", "سند");
    define2("C_ACTION", "اقدام");
    define2("C_PRIORITY_NORMAL", "عادی");
    define2("C_PRIORITY_LOW", "پایین");
    define2("C_PRIORITY_HIGH", "بالا");
    define2("C_PRIORITY_CRITICAL", "بحرانی");

    define2("C_STATUS", "وضعیت");
    define2('C_STATUS_NOT_START', 'اقدام نشده');
    define2('C_STATUS_PROGRESSING', 'در دست قدام');
    define2('C_STATUS_DONE', 'اقدام شده');
    define2('C_STATUS_SUSPENDED', 'معلق');
    define2('C_STATUS_REPLYED', 'پاسخ داده شده');

    define2("C_FORMPART_NEWEDIT", "ایجاد/ویرایش بخش های فرم");
    define2("C_FORMPART_NAME", "نام بخش");
    define2("C_FORMPART_ORDER", "ترتیب نمایش");
    define2("C_FORMPART_TOPTEXT", "متن بالای بخش");
    define2("C_FORMPART_BOTTOMTEXT", "متن پایین بخش");
    define2("C_FORMPART_TITLE", "بخشهای فرم");

    define2("C_APPROVAL_STATUS", "وضعیت تایید درخواست");
    define2("C_PRESENT_TYPE", "نوع حضور");
    define2("C_PRESENT_TIME", "مدت حضور");
    define2("C_ABSENT", "غایب");
    define2("C_ABSENT2", "غیبت");
    define2("C_PRESENT", "حاضر");
    //// End of Mahdi Ghayour //////

    //MyRequests.php by Sara Bolouri
    define2("C_RELATED_PROJECT","پروژه مربوطه");
    define2("C_YOUR_REQUESTS_LIST" , "لیست درخواست های شما از سوی شما");
    define2("C_STATUS" , "وضعیت");
    define2("C_OTHER_SPECIFICATIONS" , "سایر مشخصات");
    define2("C_CREATE_TIME1" , "زمان ایجاد");
    define2("C_PREREQUISITES" , "پیشنیازها");
    define2("C_NOTES" , "یادداشت ها");
    define2("C_USERS_ASSIGNED_TO_WORK" , "کاربران منتصب به کار");
    define2("C_DOCUMENTS" , "مستندات");

    //ManagePayments.php by Sara Bolouri
    define2("C_CREATING_EDITING_PAYMENT_TO" , "ایجاد/ویرایش پرداخت به");
    define2("C_AMOUNT" , "مبلغ");
    define2("C_PAYMENT_TYPE" , "نوع پرداخت");
    define2("C_CHEQUE" , "چک");
    define2("C_CASH" , "نقد");
    define2("C_DEPOSIT" , "واریز به حساب");
    define2("C_DESCRIPTION1" , "توضیحات");
    define2("C_CHOOSE_FILE" , "انتخاب فایل");
    define2("C_PAYMENTS_TO" , "پرداختی ها به");
    define2("C_FILE1" , "فایل");

    //ShowProjectActivities.php by Sara Bolouri
    define2("C_MAIN_SPECIFICATIONS" , "مشخصات اصلی");
    define2("C_MEMBERS" , "اعضا");
    define2("C_DOCUMENTS" , "مستندات");
    define2("C_DOCUMENT_TYPES" , "انواع سند");
    define2("C_ACTION_TYPES" , "انواع اقدام");
    define2("C_TASK_TYPES" , "انواع کار");
    define2("C_GROUP_OF_TASKS" , "گروه کارها");
    define2("C_HISTORY" , "تاریخچه");
    define2("C_ACTIVITIES" , "فعالیتها");
    define2("C_APPLIER" , "اعمال کننده");
    define2("C_RELATED_ROLE" , "کار مربوطه");
    define2("C_CODE" , "کد");
    define2("C_ACTION_TYPE1" , "نوع عمل");
    define2("C_RELATED_SECTION" , "بخش مربوطه");
    define2("C_OPERATION_DESCRIPTION" , "شرح عملیات");
    define2("C_MEMBER" , "عضو");
    define2("C_IMPORTANT_DATE", "تاریخ مهم");
    define2("C_DOCUMENT", "سند");
    define2("C_DOCUMENT_TYPE" , "نوع سند");
    define2("C_ACTION_TYPE2" , "نوع اقدام");
    define2("C_TASK_TYPE" , "نوع کار");
    define2("C_ADD" , "اضافه");
    define2("C_UPDATE" , "بروزرسانی");
    define2("C_VIEW" , "مشاهده");
    define2("C_CHOOSE_APPLIER" , "انتخاب عمل کننده");
    define2("C_CONTRACTS" , "قراردادها");
    define2("C_PROJECT_PROGRESS" , "پیشرفت پروژه");
    define2("C_CREDITS_APPROVED" , "اعتبارات مصوب");
    define2("C_CONTRACTS_OTHER_RESOURCES" , "اعتبارات مصوب - منابع دیگر");

    // Projects Kartable Adel Aboutalebi
    define2("C_ROW", "ردیف");
    define2("C_PROJECT_GROUP", "گروه پروژه");
    define2("C_EDIT", "ویرایش");
    define2("C_TITLE","عنوان");
    define2("C_PRIORITY" , "اولویت");
    define2("C_STATUS", "وضعیت");
    define2("C_REPORT", "گزارش");

    // ManageProjectTaskActivities Adel Aboutalebi
    define2('C_ACTIONS', 'اقدامات');
    define2('C_ACTION_TYPE', 'نوع اقدام');
    define2('C_USAGE_TIME', 'زمان مصرفی');
    define2('C_Progress', 'درصد پیشرفت');
    define2('C_DESCRIPTION', 'شرح');
    define2("C_ATTACHMENTS", "ضمیمه");
    define2("C_CREATOR", "ایجاد کننده");
    define2("C_ACTION_DATE", "تاریخ اقدام");
    define2("C_CREATE", "ایجاد");
    define2("C_DELETE", "حذف");
    define2("C_NOT_EXIST", "ندارد");
    define2("C_ARE_YOU_SURE","آیا مطمین هستید؟");

    // ManageProjectPrecentage Adel Aboutalebi
    define2('C_PROJECTS_ASSIGNED_TO', 'پروژه های انتسابی به');
    define2('C_PROJECT_NAME', 'نام پروژه');
    define2('C_PERCENTAGE_OF_TIME_ALLOCATED', 'درصد تخصیصی زمان');
    define2("C_RETURN","بازگشت");
    define2("C_SAVE", "ذخیره");
    define2("C_DATA_STORED","اطلاعات ذخیره شد");


    //controlFrom.php by Yegane Shabgard
    define2("C_FORM_TYPE" , "نوع فرم:");
    define2("C_SEARCH_SELECTED_FORM" , "جستجو فرم های مورد نظر");
    define2("C_CREATOR_NAME" , "نام ایجاد کننده:");
    define2("C_CREATOR" , "ایجاد کننده:");
    define2("C_SENDER" , "ارسال کننده:");
    define2("C_FORM_NAME_2" , "نام فرم");
    define2("C_LAST_SENDER" , "آخرین ارسال کننده");
    define2("C_SEND_TIME" , "زمان ارسال");
    define2("C_CURRENT_STEP" , "مرحله فعلی");
    define2("C_CURRENT_STEP_NAME" , "نام مرحله فعلی:");
    define2("C_NEW_STEP" , "مرحله جدید:");

//    PrintPageHelper.php by Yegane Shabgard
    define2("C_HELP_TO_CREATE_PAINT_PAGES" , "راهنمای تولید صفحات اختصاصی چاپ");
    define2("C_HELP_PARAMETER_SEND_AS_ID" , "پارامتری که به صفحه شما ارسال خواهد شد RecID نام دارد.");
    define2("C_HELP_CREATE_YOUR_OWN" , "	در زمانیکه می خواهید شکل چاپی به فرمت خاصی باشد باید صفحه نمایش آن را خودتان تهیه کرده و بر روی سرور قرار دهید و سپس آدرس صفحه را در 
	گزینه صفحه چاپ اختصاصی وارد کنید.");
    define2("C_HELP_DEFAULT_ICON" , "در حالت پیش فرض با کلیک روی آیکون چاپ هر رکورد اطلاعات به صورت زیر هم در یک جدول نمایش داده می شود.");
    define2("C_FORM_MAKER_SEND" , "سیستم فرم ساز به طور اتومات در کنار هر رکورد از داده های مربوط به هر فرم یک آیکون برای چاپ اطلاعات آن رکورد قرار می دهد.");
    define2("C_PAGE_TO_PRINT_FROM" , "صفحه چاپ اختصاصی یک فرم یک صفحه PHP می باشد که کد رکورد مربوطه به آن پاس می شود.");
//   NewFile.php by Yegane Shabgard
    define2("C_TEXTS" , "متون");
    define2("C_FILE_DEFINITION" , "مشخصات پرونده");
    define2("C_DEFINITION" , "مشخصات");
    define2("C_IMAGES" , "تصاویر");
    define2("C_FILES" , "فایل ها");
    define2("C_FORMS" , "فرم ها");
    define2("C_LETTERS" , "نامه ها");
    define2("C_SESSIONS" , "جلسات");
    define2("C_DEBTS" , "امانت ها");
    define2("C_FILE_TYPE" , "نوع پرونده");
    define2("C_FILE_NUMBER" , "شماره پرونده");
    define2("C_PROFESSOR" , "استاد");
    define2("C_STUDENT" , "دانشجو");
    define2("C_EMPLOYER" , "کارمند");
    define2("C_OTHERS" , "سایر");
    define2("C_STRUCTURE_UNIT" , "واحد سازمانی");
    define2("C_STRUCTURE_SUBUNIT" , "زیر واحد سازمانی");
    define2("C_INSTRUCTION_GROUP" , "گروه آموزشی");
    define2("C_USER_TYPE" , "نوع شخص");


    // ManageOntologyClassLabels by Mohammad Kahani
    define2("C_T_CREATE_EDIT_CLASS_LABELS","ایجاد/ویرایش برچسب کلاسها");
    define2("C_T_CLASS_LABELS","برچسب کلاسها");

    //ManageOntologyClassChilds by Mohammad Kahani
    define2("C_T_ADD_CHILD_CLASS","اضافه کردن کلاسهای فرزند");
    define2("C_T_CHILD_CLASSES","کلاسهای فرزند");
    define2("C_T_CLASS","کلاس");

    //ManageOntologyClassHirarchy by Mohammad Kahani
    define2("C_T_CREATE_EDIT_SUBCLASS","ایجاد/ویرایش زیرکلاسها");
    define2("C_T_CHILD_CLASS","کلاس فرزند");
    define2("C_T_SUBCLASS","زیرکلاسها");


    //ShowSummary() & ShowTabs in my .class by Mohammad Kahani
    define2("C_T_HIERARCHY_ONTOLOGY_CLASSES","سلسله مراتب کلاسهای هستان نگار");


    //_________________ AMIN ALIZADEH _____________________________
    define2("C_PRINTSESSIONPAGE_MTH", "اطلاعات جلسه");
    define2("C_PRINTSESSIONPAGE_TMFC", "جلسه");
    define2("C_PRINTSESSIONPAGE_TMTC", "عنوان");
    define2("C_PRINTSESSIONPAGE_TMFOC", "تاریخ");
    define2("C_PRINTSESSIONPAGE_TMFIFC", "شماره");
    define2("C_PRINTSESSIONPAGE_TMSIXC", "ساعت تشکلیل");
    define2("C_PRINTSESSIONPAGE_TMSEVENC", "مدت جلسه");
    define2("C_PRINTSESSIONPAGE_STFIRST", "ردیف");
    define2("C_PRINTSESSIONPAGE_STSEC", "دستور کار");
    define2("C_PRINTSESSIONPAGE_STTIR", "مصوبه");
    define2("C_PRINTSESSIONPAGE_STFORTH", "مسوول پیگیری");
    define2("C_PRINTSESSIONPAGE_STFIF", "مهلت اقدام");

    define2("C_PRINTSESSIONPAGE_TTH", "حاضرین جلسه");
    define2("C_PRINTSESSIONPAGE_TTFIRST", "ردیف");
    define2("C_PRINTSESSIONPAGE_TTSEC", "نام و نام خانوادگی");
    define2("C_PRINTSESSIONPAGE_TTTIR", "حضور");
    define2("C_PRINTSESSIONPAGE_TTFOR", "تاخیر");
    define2("C_PRINTSESSIONPAGE_TTFIF", "امضا");
    define2("C_PRINTSESSIONPAGE_TTSIX", "تاریخ تایید(امضا)");
    define2("C_PRINTSESSIONPAGE_FTH", "غایبین جلسه");

    define2("C_ONT_MERG_CLASSES_PAGE_OFIR", "رابطه");
    define2("C_ONT_MERG_CLASSES_PAGE_OSEC", "زیر کلاس");
    define2("C_ONT_MERG_CLASSES_PAGE_OTIR", "است");
    define2("C_ONT_MERG_CLASSES_PAGE_OFOR", "در");
    define2("C_ONT_MERG_CLASSES_PAGE_OFIF", "ادغام شود");
    define2("C_ONT_MERG_CLASSES_PAGE_CH", "پیشنهادات ادغام");
    define2("C_ONT_MERG_CLASSES_PAGE_CBFIR", "ردیف");
    define2("C_ONT_MERG_CLASSES_PAGE_CBSEC", "کلاس");
    define2("C_ONT_MERG_CLASSES_PAGE_CBTIR", "کلیدهای اصلی");
    define2("C_ONT_MERG_CLASSES_PAGE_CBFOR", "کلید ارتباطی");
    define2("C_ONT_MERG_CLASSES_PAGE_CBFIF", "انجام ادغام");

    define2("C_LOOKUP_PAGE_HELP_PAGE_HEADER", "راهنمای تولید صفحه جستجوی مقادیر");
    define2("C_LOOKUP_PAGE_HELP_PAGE_CONTENT", "
        بعضی از مواقع داده هایی که در یک فیلد لیستی قابل انتخاب هستند بسیاز زیاد بوده و نمایش آنها به صورت لیست
            کشویی علاوه بر اینکه حجم زیادی
            از صفحه را به خود اختصاص می دهد باعث کندی عمل کاربر در انتخاب گزینه مورد نظر خود می شود.
            <br>
            برای اینگونه موارد می توان نحوه نمایش لیست را Look Up تعیین کرد.
            <br>
            در این حالت در فرم ورود داده در جلوی نام این فیلد یک لینک برای انتخاب داده مورد نظر نمایش داده می شود که
            کاربر بتواند با کلیک روی آن
            در صفحه ای که در یک پنجره جداگانه باز می شود به جستجوی آیتم مورد نظر خود پرداخته و آن را انتخاب کند تا در
            مقدار فیلد قرار گیرد.
            <br>
            این صفحه باید توسط برنامه نویس نوشته شده و آدرس آن در مشخصه 'آدرس صفحه جستجوی داده' وارد شود.
            <br>
            مواردی که سیستم در زمان فراخوانی این صفحه به آن پاس می دهد عبارتند از:
            <br>
            <li>FormName: نام فرمی که فیلد در آن قرار دارد
            <li>InputName: نام عنصری که داخل فرم به صورت مخفی قرار دارد و باید مقدار کلید آیتم انتخاب شده در آن قرار
                گیرد
            <li>SpanName: نام span ای را به همراه دارد که توضیحات مربوط به آیتم انتخاب شده در آن نوشته می شود
                <br>
                به عنوان مثال مقادیر زیر را در نظر بگیرید:
                <br>FormName=f1&InputName=PersonID&SpanName=MySpan
                <br>
                حال باید برنامه نویس در کد خود زمانیکه کاربر آیتم مورد نظر را انتخاب کرد یک تابع جاوا اسکریپ مشابه این
                را فراخوانی کند:
    
    ");
    //AMIR Karami
    define2("C_Class", "کلاس");
    define2("C_Property", "ویژگی");
    define2("C_SinCnnP","جستجو در نام کلاسها و خصوصیات");
    define2("C_SinLabels","جستجو در برچسبها");
    define2("C_Relation","رابطه");
    define2("C_CatSuggest","پیشنهادهای دسته بندی");
    define2("C_Sentence1","پیشنهادهای ادغام یا برقراری رابطه سلسله مراتبی (کلاسهایی که فرزند مشترک دارند)");
    define2("C_Sentence2","روابط پدر - فرزندی نامعتبر (تکراری): کلاس پدر و فرزند هر دو زیر کلاس مستقیم یک کلاس هستند)");
    define2("C_MergeComp","ادغام انجام شد");
    define2("C_Merge_Classes","ادغام کلاس ها");
    define2("C_Pclasses", "کلاس های پدر");
    define2("C_SubClasses", "زیر کلاس ها");
    define2("C_Cclasses", "کلاس های فرزند");
    define2("C_Done", "انجام");
    define2("C_CoE_Ontology","ایجاد/ویرایش کلاسهای هستان نگار");
    define2("C_Class_title","عنوان کلاس");
    define2("C_Label","برچسب");
    define2("C_Upper_Class","کلاس بالاتر");
    define2("C_Show_graph","نمایش گراف");
    define2("C_Merge_result","حاصل ادغام");
    define2("C_Ontology_Classes","کلاسهای هستان نگار");
    define2("C_Related_Prop","خصوصیات مرتبط");
    define2("C_Merge","ادغام");
    define2("C_Area","حوزه");
    define2("C_Prop_Labels","برچسب خصوصیت");
    define2("C_Prop_Range","برد خصوصیت");
    define2("C_Prop_Name","نام خصوصیت");
    //_________________ END _____________________________

    //SignOut.php Mostafa Ghofrani
    define2("C_SESSION_EXPIRED", "نشست شما منقضی شده است");
    define2("C_WELCOME", "خوش آمدید");
    define2("C_RELOGING", "برای ورود مجدد");
    define2("C_CLICK_THIS", "اینجا را کلیک کنید");

    //TaskMessages.php Mostafa Ghofrani
    define2("C_LATEST_STATUS_OTHERS", "آخرین عملیات انجام شده روی کارهای مرتبط با شما توسط دیگر کاربران");
    define2("C_JOB_DONE", "عملیات انجام شده");
    define2("C_RELATED_USER", "کاربر مربوطه");
    define2("C_RELATED_TITLE", "عنوان کار مربوطه");
    define2("C_LATEST_STATUS_YOU", "آخرین عملیات انجام شده روی کارهای مرتبط با شما توسط خودتان");
    define2("C_MORE_DET", "مشاهده‌ی جزئیات بیشتر");
    define2("C_LESS_DET", "مشاهده‌ی خلاصه‌تر");
    define2("C_JOB_TITLE", "عنوان کار مربوطه");

    //HomePage.php and compare_ontologies By mohammadAfsharian
    define2("C_RECIVED_LETTER"," نامه های رسیده ");
    define2("C_COMPAIRE_ONTOLOGIES","مقایسه دو هستان نگار");
    define2("C_CLASS","کلاس");
    define2("C_PROPERTICE","خصوصیت");
    define2("C_TO","به");
    define2("C_CHOICE","انتخاب");
    define2("C_ALLOWED_ATTRBIUTE_DATA" , "داده مجاز خصوصیت");
    define2("C_ABUSES_AND_SYMMETRIES","اعمال تعدی و تقارنی");
    define2("C_CLEAR_PRE_EXITING_MAPPING","نگاشت از پیش موجود پاک شود");
    define2("C_COMPARE_THE_ELEMENT","مقایسه عناصر");
    define2("C_WITH","با");
    define2("C_SHOWING_COMPARISON_RESULTS","نمایش نتایج مقایسه");
    define2("C_SEMI_AUTOMATED_MAPPING","انجام نگاشت نیمه خودکار");
    define2("C_MESSAGES","پیامها");

    //Manage_projects By mohammadAfsharian
    define2("C_TITLE","عنوان");
    define2("C_SEARCH","جستجو");
    define2("C_PROJECT_GROUP","گروه پروژه");
    define2("C_RELATED_SYSTEM","سیستم مربوطه");
    define2("C_CONDITION","وضعیت");
    define2("C_REMOVE","حذف");
    define2("C_CREAT","ایجاد");
    define2("C_RESPONSIV","پاسخگو");
    define2("C_EDIT","ویرایش");
    define2("C_ROW","ردیف");
    define2("C_PARTS","اعضا");
    define2("C_PROJECT","پروژه");
    define2("C_PRIORITY","اولویت");
    define2("C_ARE_YOU_SURE","آیا مطمِن هستید?");
    define2("C_CHOSE_YOUR_CONDITIONS", "وضعیت خود را انتخاب کنید");
    define2("C_NOT_STARTED","شروع نشده");
    define2("C_ONGOING","شروع نشده");
    define2("C_SUPPORTED","درحال پشتیبانی");
    define2("C_FINISHED","خاتمه یافته");
    define2("C_SUSPENDED","معلق");
    define2("C_PROJECT_MEMBER","اعضای پروژه:");
    define2("C_CHOISE","[انتخاب]");
    define2("C_BY_MY_OWN","خودم");

    /**
     * define Constant Persian Word
     * OntologyMergePropeperties by ArefNazari
     **/
    define2("C_DIR","rtl");
    define2("C_ROW","ردیف");
    define2("C_PRIORITY","خصوصیت");
    define2("C_DOMAIN","دامنه");
    define2("C_RANGE","برد");
    define2("C_ALLOWED_VALUES","مقادیر مجاز");
    define2("C_MERGE_SUGGESTIONS","پیشنهادات ادغام");
    define2("C_ACTIONS","اعمال");

    define2("C_PRIORITY2","خصوصیت 2");
    define2("C_DOMAIN_PRIORITY2","دامنه خصوصیت 2");
    define2("C_RANGE_PRIORITY2","برد خصوصیت 2");
    define2("C_ALLOWED_VALUES2","مقادیر مجاز 2");

    define2("C_MERGE","ادغام");
    define2("C_NOT_MERGE","عدم ادغام");


    /**
     * define Constant English Word
     * OntologyMergePropeperties by Reza Latifi
     **/
    define2("C_USER_NO_PERMISSION", "عدم دسترسی");
    define2("C_FINAL_ACEEPT", "تایید نهایی");
    define2("C_IDENTIFICATION_CODE", "کد شناسایی");
    define2("C_STEP_CODE", "کد مرحله");
    define2("C_DEPARTMENT_CODE","کد واحد سازمانی");
    define2("C_QUESTIONNAIRE_ALREADY_FILLED", "قبلا پرسشنامه را پر کرده اید");
    define2("C_NO_PERMISSION_TO_FILL_QUES", "مجوز پر کردن این پرسشنامه را ندارید");
    define2("C_INFORMATION_SAVED", "اطلاعات ذخیره شد");
    define2('C_LOGOUT', 'خروج از سیستم');
    define2("C_EDUCATIONAL", "تحصیلی");
    define2("C_RESEARCH", "تحقیقاتی");
    define2("C_OFFICIAL", "اداری");
    define2("C_FINANCIAL", "مالی");
    define2('C_PERSONAL', 'شخصی');
    define2('C_OTHER', 'سایر');
    define2('C_OUTPUT_TYPE', 'نوع خروجی');
    define2('C_CALCULATION_TYPE', 'نوع محاسبات');
    define2('C_CALCULATION_QUERY', 'درخواست محاسبه');
    define2('C_CODE_FILE_NAME', 'نام فایل حاوی کد');
    define2('C_ENTITY_TYPE', 'نوع داده');
    define2('C_REGULAR_EXPRESSION', 'عبارت منظم');
    define2('C_MIN_VALID_VALUE', 'حداقل مقدار مجاز');
    define2('C_MAX_VALID_VALUE', 'حداکثر مقدار مجاز');
    define2('C_DOMAIN_TABLE', 'جدول مقادیر مجاز');
    define2('C_OWNER', 'مالک');
    define2('C_CATEGORY', 'دسته بندی');
    define2("C_UPDATER", "بروزرسانی کننده");
    define2("C_DETAILS", "شرح");
    define2("C_UPDATE_DATE", "تاریخ بروزرسانی");
    define2("C_ORDER", "ترتیب");
    define2("C_ENTITY", "تعداد");
    define2("C_USED_KEY", "کلید مورد استفاده");
    define2('C_STATEMENT_TO_GO_TO_THE_NEXT_STATE','تعریف شرط برای رفتن به مرحله بعد');
    define2('C_FIELD_NAME','نام فیلد');
    define2('C_STATEMENT', 'شرط');
    define2('C_EQUAL', 'مساوی');
    define2('C_CONTAIN', 'شامل');
    define2('C_GREATER_THAN', 'بزرگتر');
    define2('C_LESS_THAN', 'کوچکتر');
    define2('C_GREATER_THAN_OR_EQUAL', 'بزرگتر یا برابر');
    define2('C_LESS_THAN_OR_EQUAL', 'کوچکتر یا برابر');
    define2('C_NOT_EQUAL', 'مخالف');

    /**
     * END
     **/

    // Saeed rastegar moghaddam - MyPayments.php
    define2("C_MY_REPORTS","گزارش دریافتی های من");
    define2("C_MY_DATE","تاریخ");
    define2("C_MY_PRICE","مبلغ");
    define2("C_MY_INFO","شرح");
    define2("C_MY_FILE","فایل");


    // Saeed rastegar moghaddam - MyProjectMembers.php
    define2("C_NO_PERMISSION","مجوز مشاهده این رکورد را ندارید");
    define2("C_DATA_STORED","داده ذخیره شد");
    define2("C_MY_TITLE_PROJECTMEMBERS","ایچاد / ویرایش اعضای پروژه");
    define2("C_USER_CODE","کد شخص");
    define2("C_PERMISSION_TYPE","نوع دسترسی");
    define2("C_USER_PARTNERSHIP_PERSENTAGE","درصد مشارکت در پروژه");
    define2("C_STORE","ذخیره");
    define2("C_CLOSE","بستن");
}

?>
