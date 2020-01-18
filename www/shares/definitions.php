<?php
define("UI_LANGUAGE", "EN"); // EN or FA

if(UI_LANGUAGE=="EN") {
    define("C_SAVE", "Save");
    define("C_NEW", "New");
    define("C_REMOVE", "Remove");
    define("C_NAME", "Name");
    define("C_CREATE", "Create");
    define("C_ORDER", "Order");
    define("C_ROW", "Row");
    define("C_EDIT", "Edit");
    define("C_PERSIAN", "Persian");
    define("C_ENGLISH", "English");
    define("C_EXIT", "Exit");

    define("C_DATA_SAVE_SUCCESS", "Data saved successfully");
    define("C_ARE_YOU_SURE", "Are you sure?");
    define("C_ACTIVE_USER", "Active User: ");
    define("C_MAIN_MENU", "Main Menu");
    define("C_FIRST_PAGE", "Home");
    define("C_CHANGE_PASSWORD", "Change Password");
    define("C_MY_ACTIONS", "My Actions");

    //SendMessage.php needed definitions MOHAMAD_ALI_SAIDI
    define("C_TITLE","Title");
    define("C_TEXT","Text");
    define("C_FILE","File");
    define("C_TO_USER","To");
    define("C_SEND","Send");
    define("C_SELECT","Select");
    define("C_SEND_MESSAGE","Send Message");
    define("C_MESSAGE_SENT","Message Sent");
    define("C_TITLE_EMPTY","Please Fill The Tile Field");
    define("C_RECEIVER_EMPTY","Please Select Receiver");
    define("C_AUTO_SAVE","Auto Saving...");
    define("C_SENDING_FILE_ERROR","Error In Sending File");
    //----------------------------------

    //MailBox.php needed definitions MOHAMAD_ALI_SAIDI
    define("C_MESSAGES_RECEIVED","Inbox");
    define("C_SENDER_NAME","Sender Name");
    define("C_TIME_SENT","Time Sent");
    define("C_REPLY_DES","Reply Description");
    define("C_DELETE","Delete");
    //----------------------------------


    //SentBox.php needed definitions MOHAMAD_ALI_SAIDI
    define("C_MESSAGES_SENT","OutBox");
    define("C_RECEIVER_NAME","Receiver Name");
    //----------------------------------


    //SearchMessage.php needed definitions Alireza Imani
    define("C_SEARCH_MESSAGE","Search Message");
    define("C_PART_OF_TEXT","Part of text");
    define("C_CHOOSE","Choose");
    define("C_FROM_DATE","From date");
    define("C_TO_DATE","To date");
    define("C_SEARCH","Search");

    //MetaData2Onto.php needed definitions Alireza Imani
    define("C_CHOOSE_CONDITIONS_FOR_REVERSE_ENGINEERING","Choose conditions for reverse engineering");
    define("C_INTENDED_SCOPES","Intended Scopes");
    define("C_EDUCATIONAL","Educational");
    define("C_RESEARCH","Research");
    define("C_STUDENT_SERVICES","Student Services");
    define("C_WELFARE","Welfare");
    define("C_FINANCIAL","Financial");
    define("C_SUPPORT","Support");
    define("C_ADMINISTRATIVE","Administrative");
    define("C_RELATED_TO_SYSTEM_OPERATIONS","Related to system operations");
    define("C_TARGET_ONTOLOGY","Target Ontology");
    define("C_REMOVE_PREVIOUS_MERGE_SUGGESTIONS","Remove previous merge suggestions");
    define("C_REMOVE_EXISTING_ELEMENTS_OF_ONTOLOGY","Remove existing elements of Ontology");
    define("C_PERFORM_REVERSE_ENGINEERING","Perform Reverse Engineering");
    define("C_REVIEW_PROPERTIES_MERGING_SUGGESTIONS","Review properties merging suggestions");
    define("C_REVIEW_INTEGRATION_SUGGESTIONS","Review integration suggestions");
    define("C_HIERARCHICAL_RELATIONSHIPS_BETWEEN_CLASSES","Hierarchical relationships between classes");
    define("C_CONVERSION_DONE","Conversion done");

    //ShowTermsManipulationHistory.php needed definitions Alireza Imani
    define("C_COMPLETED_TASK","Completed Task");
    define("C_DESCRIPTION","Description");
    define("C_SUBJECT","Subject");
    define("C_TIME","Time");
    define("C_EXTRACT_NEW_WORD","Extract new word");
    define("C_REMOVE_WORD","Remove word");
    define("C_MERGE_TWO_WORDS","Merge two words");
    define("C_CHANGE_WORD","Change word");
    define("C_REPLACE_WORD","Replace word");
    define("C_TO","to");
    define("C_BY","by");

    //NewQuestionnare.php ALI NOORI
    define("C_CREATING_EDITTING_QUESTIONNARE","Creating/EdittingQuestionnare");
    define("C_FORM_NAME","Form Name");
    define("C_FORM_EXPLANATION_UP","Form Explanation Above");
    define("C_FORM_EXPLANATION_DOWN","Form Explanation Below");
    define("C_TYPE_SHOW_ENTER_DATA_LAYOUT","Type of entry data layout");
    define("C_ONE_COLUMN","One Column");
    define("C_TWO_COLUMN","Two Column");
    define("C_WIDTH_QUESTION_COLUMN","Width of question column");
    define("C_MARGIN_SECOND_ROWS","margin for second rows");
    define("C_MARGIN_SECOND_ROWS_YES","YES");
    define("C_MARGIN_SECOND_ROWS_NO","NO");
    define("C_RETURN","Return");
    define("C_TABLE_INFORMATION","Table Of Information");
    define("C_BANK_INFORMATION","Bank Of Information");
    define("C_FORMATION_USER","Formation User");
    define("C_CREATE_TIME","Create Time");
    define("C_MANAGE_OPTIONS","Manage Options");
    define("C_MANAGE_DETAILS_TABLES","Manage details tables");
    //-----------------------------
    //MyTimeReport.php ALI NOORI
    define("C_YEAR","Year");
    define("C_MONTH","Month");
    define("C_SHOW_REPORT_ACTIONS","Show Report Actions");
    define("C_USAGE_TIME_REPORT","Usage Time Report");
    define("C_DATE","Date");
    define("C_ACTIVITY","Activity");
    define("C_TIME","Time");
    define("C_TOTAL","Total");
    define("C_RIAL","Rial");
    //-------------------------
    //CompareAllOntos.php ALI NOORI
    define("C_COMPARE_COVER_HASTAN_NEGAR","Compare Cover Of HastanNegar");
    define("C_WITH_OTHER_HASTAN_NEGAR","With Other HastanNegar");
    define("C_NAME_HASTAN_NEGAR","Name Of HastanNegar");
    define("C_PERCENTAGE_MAPPING_CLASS","Class Mapping Percentage");
    define("C_PROPERTIES_MAPPING_PERCENTAGE","Properties mapping percentage");
    //--------------------------

    //ShowTermReferHistory.php Hossein Lotfi
    define("C_SOURCE_NAME","Source Name");
    define("C_PAGE","Page");
    define("C_PARAGRAPH","Paragraph");
    define("C_SUBMIT_NEW_REFERENCE", "Submit New Reference");
    define("C_REMOVE_REFERENCE", "Remove Reference");
    define("C_CHANGE_REFERENCE", "Change Reference");
    define("C_REPLACE_REFERENCE_WITH", "Replace Reference With");
    define("C_WITH_REFERENCE_TO", "With Reference To");
    define("C_S", "P");
    define("C_P", "p");

    //SelectStaff.php Hossein Lotfi
    define("C_LAST_NAME","Last Name");
    define("C_REMOVE_PREVIOUS_CHOICE","Remove Previous Choice");
    define("C_FULL_NAME","Full Name");

    //ManageTermReferences.php Hossein Lotfi
    define("C_CONTENT","Content");
    define("C_TERM", "Term");
    define("C_FREQUENCY","Frequency");
    define("C_INFORMATION_SAVED","Information Saved");
    define("C_CREATE_EDIT_TERMS_REFERENCES","Create/Edit Terms References");
    define("C_FILE2","File");
    define("C_GET_FILE", "Get File");
    define("C_TERMS_REFERENCES","Terms References");
    define("C_TERMS","Terms");
    define("C_STATISTICAL_ANALYSIS","Statistical Analysis");

    //SelectMultiStaff.php By Ehsan Amini
    define("C_USER_NAME", "User Name");

    //NewRequest.php By Ehsan Amini
    define("C_TASK_REQUEST", "Task Request");
    define("C_IF_REQUEST_IS_ABOUT_CHANGING_ACCESS_TO_DATABASE_DATA_CLICK_HERE", "[If request is about changing access to database data click here]");
    define("C_UNKNOWN_SYSTEM_CODE", "Unknown system code");
    define("C_NO_RESPONSE_HAS_BEEN_DETERMINED_FOR_THIS_PROJECT", "No response has been determined for this project");

    //CreateKartableHeader function in ProjectTasks.class.php By Ehsan Amini
    define("C_CURRENT_TASKS", "Current Tasks");
    define("C_PROJECTS_MEMBERS", "Projects Members");
    define("C_TASKS_IN_NEED_OF_CONTROL", "Tasks in need of control");
    define("C_DONE_TASKS", "Done Tasks");
    define("C_CREATED_TASKS", "Created Tasks");

    //ShowAllPersonStatus.php By Ehsan Amini
    define("C_PROJECTS_COUNT", "Projects Count");
    define("C_TIME_PERCENTAGE_ALLOCATED", "Percentage time allocated");
    define("C_LAST_NAME_AND_FIRST_NAME", "Last Name and First Name");
    define("C_THIS_LIST_SHOWS_MEMBERS_OF_THE_PROJECTS_THAT_YOU_ARE_MANAGING_OR_SUBORDINATE_TO_THE_ORGANIZATIONAL_UNIT_UNDER_YOUR_MANAGEMENT", "This list shows members of the projects that you are managing or subordinate to the organizational unit under your management");
    define("C_FOR_ADJUSTING_PERCENTAGES_YOU_CAN_CLICK_ON_PERCENTAGE_NUMBER_IN_EACH_ROW","For adjusting percentages you can click on percentage number in each row");

    //SessionTypes.class.php By Arman Ghoreshi
    define("C_SESSION_LOCATION","Location");
    define("C_SESSION_INFO","Session Info");
    define("C_SESSION_PERMITTED_PERSON","Permitted Users");
    define("C_SESSION_MEMBERS","Members");
    //NewSessionTypes.php By Arman Ghoreshi
    define("C_SESSION_CREATE_EDIT","Create/Edit Session Patterns");
    define("C_START_TIME","Start Time");
    define("C_END_TIME","End Time");
    //managePersonPermittedSessionType.php By Arman Ghoreshi
    define("C_SESSION_PERMITTED_CREATE_EDIT","Create/Edit Permitted Person");
    define("C_SESSION_PERMITTED_LIST","Permitted Users for Sessions");
    define("C_PERMISSIONS","Permissions");
    //ManageSessionTypeMembers.php By Arman Ghoreshi
    define("C_ROLE","Role");
    define("C_SESSIOM_MEMBERS","Session Members");

    //ManageFacilityPages.php By Naghme Mohammadifar
    define("C_SAVED_INFO","New information saved!");
    define("C_CREATE_EDIT_A_PAGE_RELATED_TO_FEATURE", "Create/edit a page related to the part");
    define("C_TITLE_PAGE","Page");
    define("C_CLOSE", "Close");
    define("C_RELATED_PAGES_TO_THIS_FEATURE","Related pages to the part");
    define("C_CONFIRM_TO_DELETE","Are you sure to delete?");

    //NewResearchProjectComments.php Alireza Forghani Toosi
    define("C_SEASON", "Season");
    define("C_CREATE_EDIT_RESEARCH_PROJECT_COMMENT", "Create/Edit research project comment");

    //Managemessages.php By kouroshAtaei

    define("CREATE_EDIT","create / edit ");
    define("C_MESSAGE" , "Message");
    define("AT_FILE" ,"attached file");
    define("REC_FILE" , "receive file");
    define("PIC" ,"picture");
    define("START_TIME" ,"begin");
    define("END_TIME", "end" );
    define("SAVE_M" , "save");
    define("NEW_M" ,"new");
    define("SEARCH_M" ,"search");
    define("MESSAGES_M" , "messages");
    define("CREATOR_M" , "creator");
    define("CREATE_TIM_M" , "creat time");
    define("ROW_M" , "row");
    define("EDIT_M" , "edit");
    define("DELETE_M" ,"delete");
    define("ARE_YOU_SURE" , "Are you sure ?");
    define("ERROR_SEND" ,"Error submitting file");
    define("INFO_SAVED" , "Information saved");

    //newResearchProjectRefrences.php By kouroshAtaei

    define("CREAT_AND_EDIT_RES_RESEARCH" , "Create / edit a research work source");
    define("SEARCH_ENG" , "Search Engine");
    define("TAGS_WORDS" ,"Search Keywords");
    define("LANG_N" ,"Language");
    define("EN_LAN_N", "English");
    define("FA_LAN_N", "Persian");
    define("TITLE_N" ,"title");
    define("WRITERS_N", "writers");
    define("YEARS_N", "year");
    define("SUM_N", "Abstract");
    define("STATE_OF_STUDY", "Study status");
    define("ALREADY_STUDY", "studied");
    define("ALREADY_NOT_STUDY", "not studied");
    define("STUDING", "Studying");
    define("IMPORTNT", "Importance");
    define("CAT_N", "Category");
    define("ALL_COM", "Overview");
    define("FILE_N" , "file");
    define("NOTES_N", "notes");
    define("CLOSE_N" , "close");
    //manageFieldsDataMapping.php By kouroshAtaei
    define("SELECTION_M" , "Select the appropriate table and field to determine the value equation table");
    define("TABLE_M" , "table") ;
    define("DEF_TABLE", "Define the equation table");
    define("VAL_FIELD_M","Equalized values for the corresponding field");
    define("REAL_VAL" ,"Original value");
    define("M_VAL_EQ" , "Equivalent value");
    define("DATABASE" , "database");
    define("FIELD_M" ,"field");

}
else
{
    define("C_SAVE", "ذخیره");
    define("C_NEW", "جدید");
    define("C_REMOVE", "حذف");
    define("C_NAME", "نام");
    define("C_CREATE", "ایجاد");
    define("C_ORDER", "ترتیب");
    define("C_ROW", "ردیف");
    define("C_EDIT", "ویرایش");
    define("C_PERSIAN", "فارسی");
    define("C_ENGLISH", "انگلیسی");

    define("C_DATA_SAVE_SUCCESS", "اطلاعات با موفقیت ذخیره شد");
    define("C_ARE_YOU_SURE", "مطمئن هستید؟");
    define("C_ACTIVE_USER", "کاربر فعال: ");
    define("C_MAIN_MENU", "منوی اصلی");
    define("C_FIRST_PAGE", "صفحه اول");
    define("C_CHANGE_PASSWORD", "تغییر رمز عبور");
    define("C_MY_ACTIONS", "اقدامات من");
    define("C_EXIT", "خروج");

    //SendMessage.php needed definitions MOHAMAD_ALI_SAIDI
    define("C_TITLE","عنوان");
    define("C_TEXT","متن");
    define("C_FILE","محتوای فایل");
    define("C_TO_USER","به کاربر");
    define("C_SEND","ارسال");
    define("C_SELECT","انتخاب");
    define("C_SEND_MESSAGE","ارسال پیام");
    define("C_MESSAGE_SENT","پیام ارسال شد");
    define("C_TITLE_EMPTY","عنوان را وارد کنید");
    define("C_RECEIVER_EMPTY","گیرنده را مشخص کنید");
    define("C_AUTO_SAVE","ذخیره سازی خودکار..");
    define("C_SENDING_FILE_ERROR"," خطا در ارسال فایل");
    //----------------------------------
    //MailBox.php needed definitions MOHAMAD_ALI_SAIDI

    define("C_MESSAGES_RECEIVED","نامه های رسیده");
    define("C_SENDER_NAME","فرستنده");
    define("C_TIME_SENT","زمان ارسال");
    define("C_REPLY_DES","شرح ارجاع");
    define("C_DELETE","حذف");

    //----------------------------------

    //SentBox.php needed definitions MOHAMAD_ALI_SAIDI
    define("C_MESSAGES_SENT","نامه های ارسالی");
    define("C_RECEIVER_NAME","دریافت کننده");
    //----------------------------------


    //SearchMessage.php needed definitions Alireza Imani
    define("C_SEARCH_MESSAGE","جستجوی نامه");
    define("C_PART_OF_TEXT","بخشی از متن");
    define("C_CHOOSE","انتخاب");
    define("C_FROM_DATE","از تاریخ");
    define("C_TO_DATE","تا تاریخ");
    define("C_SEARCH","جستجو");

    //MetaData2Onto.php needed definitions Alireza Imani
    define("C_CHOOSE_CONDITIONS_FOR_REVERSE_ENGINEERING","انتخاب شرایط برای مهندسی معکوس");
    define("C_INTENDED_SCOPES","حوزه‌های مورد نظر");
    define("C_EDUCATIONAL","آموزشی");
    define("C_RESEARCH","پژوهشی");
    define("C_STUDENT_SERVICES","خدمات دانشجویی");
    define("C_WELFARE","رفاهی");
    define("C_FINANCIAL","مالی");
    define("C_SUPPORT","پشتیبانی");
    define("C_ADMINISTRATIVE","اداری");
    define("C_RELATED_TO_SYSTEM_OPERATIONS","مرتبط با عملیات سیستمی");
    define("C_TARGET_ONTOLOGY","هستان نگار مقصد");
    define("C_REMOVE_PREVIOUS_MERGE_SUGGESTIONS","حذف پیشنهادهای ادغام قبلی");
    define("C_REMOVE_EXISTING_ELEMENTS_OF_ONTOLOGY","حذف عناصر موجود در هستان نگار");
    define("C_PERFORM_REVERSE_ENGINEERING","انجام مهندسی معکوس");
    define("C_REVIEW_PROPERTIES_MERGING_SUGGESTIONS","بررسی پیشنهاد ادغام خصوصیت ها");
    define("C_REVIEW_INTEGRATION_SUGGESTIONS","بررسی پیشنهادهای تجمیع");
    define("C_HIERARCHICAL_RELATIONSHIPS_BETWEEN_CLASSES","روابط سلسله مراتبی بین کلاس ها");
    define("C_CONVERSION_DONE","تبدیل انجام شد");

    //ShowTermsManipulationHistory.php needed definitions Alireza Imani
    define("C_COMPLETED_TASK","عمل انجام شده");
    define("C_DESCRIPTION","شرح");
    define("C_SUBJECT","عمل کننده");
    define("C_TIME","زمان");
    define("C_EXTRACT_NEW_WORD","استخراج واژه‌ی جدید");
    define("C_REMOVE_WORD","حذف واژه");
    define("C_MERGE_TWO_WORDS","ادغام دو واژه");
    define("C_CHANGE_WORD","تغییر واژه");
    define("C_REPLACE_WORD","جایگزینی واژه");
    define("C_TO","به");
    define("C_BY","با");

    //NewQuestionnare.php ALI NOORI
    define("C_CREATING_EDITTING_QUESTIONNARE","ایجاد/ویرایش پرسشنامه");
    define("C_FORM_NAME","عنوان فرم");
    define("C_FORM_EXPLANATION_UP","توضیحات بالای فرم");
    define("C_FORM_EXPLANATION_DOWN","توضیحات پایین فرم");
    define("C_TYPE_SHOW_ENTER_DATA_LAYOUT","نوع نمایش صفحه ورود داده");
    define("C_ONE_COLUMN","یک ستونی");
    define("C_TWO_COLUMN","دو ستونی");
    define("C_WIDTH_QUESTION_COLUMN","عرض ستون سوالات");
    define("C_MARGIN_SECOND_ROWS","حاشیه برای ردیفهای فرم");
    define("C_MARGIN_SECOND_ROWS_YES","قرار داده شود");
    define("C_MARGIN_SECOND_ROWS_NO","قرار داده نشود");
    define("C_RETURN","بازگشت");
    define("C_TABLE_INFORMATION","جدول اطلاعاتی مربوطه");
    define("C_BANK_INFORMATION","بانک اطلاعاتی مربوطه");
    define("C_FORMATION USER","کاربرسازنده");
    define("C_CREATE_TIME","تاریخ ایجاد");
    define("C_MANAGE_OPTIONS","مدریت گزینه ها");
    define("C_MANAGE_DETAILS_TABLES","مدیریت جداول جزییات");
    //-----------------------------
    //MyTimeReport.php ALI NOORI
    define("C_YEAR","سال :");
    define("C_MONTH","ماه :");
    define("C_SHOW_REPORT_ACTIONS","نمایش گزارش اقدامات کاری");
    define("C_USAGE_TIME_REPORT","گزارش زمان مصرفی");
    define("C_DATE","تاریخ");
    define("C_ACTIVITY","فعالیت");
    define("C_TIME","زمان");
    define("C_TOTAL","مجموع");
    define("C_RIAL","ریال");
    //-------------------------
    //CompareAllOntos.php ALI NOORI
    define("C_COMPARE_COVER_HASTAN_NEGAR","مقایسه همپوشانی هستان نگار");
    define("C_WITH_OTHER_HASTAN_NEGAR"," با سایر هستان نگاره");
    define("C_NAME_HASTAN_NEGAR","نام هستان نگار");
    define("C_PERCENTAGE_MAPPING_CLASS","درصد نگاشت کلاسه");
    define("C_PROPERTIES_MAPPING_PERCENTAGE","درصد نگاشت خصوصیات");
    //--------------------------
    
    //ShowTermReferHistory.php Hossein Lotfi
    define("C_SOURCE_NAME","نام منبع");
    define("C_PAGE","صفحه");
    define("C_PARAGRAPH","پاراگراف");
    define("C_SUBMIT_NEW_REFERENCE", "ثبت ارجاع جدید");
    define("C_REMOVE_REFERENCE", "حذف ارجاع");
    define("C_CHANGE_REFERENCE", "تغییر ارجاع");
    define("C_REPLACE_REFERENCE_WITH", "جایگزینی ارجاع به");
    define("C_WITH_REFERENCE_TO", "با ارجاع به");
    define("C_S", "ص");
    define("C_P", "پ");

    //SelectStaff.php Hossein Lotfi
    define("C_LAST_NAME","نام خانوادگی");
    define("C_REMOVE_PREVIOUS_CHOICE","حذف انتخاب قبلی");
    define("C_FULL_NAME","نام و نام خانوادگی");

    //ManageTermReferences.php Hossein Lotfi
    define("C_CONTENT","محتوا");
    define("C_TERM", "اصطلاح");
    define("C_FREQUENCY","فراوانی");
    define("C_INFORMATION_SAVED","اطلاعات ذخیره شد");
    define("C_CREATE_EDIT_TERMS_REFERENCES","ایجاد/ویرایش منابع اصطلاحات");
    define("C_FILE2","فایل");
    define("C_GET_FILE", "دریافت فایل");
    define("C_TERMS_REFERENCES","منابع اصطلاحات");
    define("C_TERMS","اصطلاحات");
    define("C_STATISTICAL_ANALYSIS","تحلیل آماری");

    //SelectMultiStaff.php By Ehsan Amini
    define("C_USER_NAME", "نام کاربر");

    //NewRequest.php By Ehsan Amini
    define("C_TASK_REQUEST", "درخواست انجام کار");
    define("C_IF_REQUEST_IS_ABOUT_CHANGING_ACCESS_TO_DATABASE_DATA_CLICK_HERE", "[در صورتیکه درخواست به منظور ایجاد تغییرات دستی بر روی داده های بانک اطلاعاتی است اینجا را کلیک کنید]");
    define("C_UNKNOWN_SYSTEM_CODE", "کد سیستم نامشخص است");
    define("C_NO_RESPONSE_HAS_BEEN_DETERMINED_FOR_THIS_PROJECT", "برای این پروژه پاسخگویی تعیین نشده است");

    //CreateKartableHeader function in ProjectTasks.class.php By Ehsan Amini
    define("C_CURRENT_TASKS", "کارهای جاری");
    define("C_PROJECTS_MEMBERS", "اعضای پروژه ها");
    define("C_TASKS_IN_NEED_OF_CONTROL", "کارهای نیازمند کنترل");
    define("C_DONE_TASKS", "کارهای انجام شده");
    define("C_CREATED_TASKS", "کارهای ایجاد شده");

    //ShowAllPersonStatus.php By Ehsan Amini
    define("C_PROJECTS_COUNT", "تعداد پروژه ها");
    define("C_TIME_PERCENTAGE_ALLOCATED", "درصد تخصیصی زمان");
    define("C_LAST_NAME_AND_FIRST_NAME", "نام خانوادگی و نام");
    define("C_THIS_LIST_SHOWS_MEMBERS_OF_THE_PROJECTS_THAT_YOU_ARE_MANAGING_OR_SUBORDINATE_TO_THE_ORGANIZATIONAL_UNIT_UNDER_YOUR_MANAGEMENT", "در این لیست اعضای پروژه هایی که شما مدیر آنها هستید و یا در زیرمجموعه واحد سازمانی تحت مدیریت شماست نمایش داده میشوند");
    define("C_FOR_ADJUSTING_PERCENTAGES_YOU_CAN_CLICK_ON_PERCENTAGE_NUMBER_IN_EACH_ROW","برای تنظیم درصدها میتوانید روی عدددرصد در هر ردیف کلیک نمایید");

    //SessionTypes.class.php By Arman Ghoreshi
    define("C_SESSION_LOCATION","محل تشکیل");
    define("C_SESSION_INFO","مشخصات اصلی");
    define("C_SESSION_PERMITTED_PERSON","کاربران مجاز");
    define("C_SESSION_MEMBERS","اعضا");
    //NewSessionTypes.php By Arman Ghoreshi
    define("C_SESSION_CREATE_EDIT","ایجاد/ویرایش الگوهای جلسه");
    define("C_START_TIME","زمان شروع");
    define("C_END_TIME","زمان پایان");
    //managePersonPermittedSessionType.php By Arman Ghoreshi
    define("C_SESSION_PERMITTED_CREATE_EDIT","ایجاد/ویرایش کاربران مجاز الگوهای جلسه");
    define("C_SESSION_PERMITTED_LIST","کاربران مجاز الگوهای جلسات");
    define("C_PERMISSIONS","دسترسی ها");
    //ManageSessionTypeMembers.php By Arman Ghoreshi
    define("C_ROLE","نقش");
    define("C_SESSIOM_MEMBERS","اعضای الگوهای جلسه");



    //ManageFacilityPages.php By Naghme Mohammadifar
    define("C_SAVED_INFO","اطلاعات ذخیره شد!");
    define("C_CREATE_EDIT_A_PAGE_RELATED_TO_FEATURE","ایجاد/ویرایش صفحه مرتبط با امکان");
    define("C_TITLE_PAGE","صفحه");
    define("C_CLOSE", "بستن");
    define("C_CONFIRM_TO_DELETE","آیا مطمئن هستید؟");
    define("C_RELATED_PAGES_TO_THIS_FEATURE","صفحات مرتبط با این امکان");

    //NewResearchProjectComments.php Alireza Forghani Toosi
    define("C_SEASON", "فصل");
    define("C_CREATE_EDIT_RESEARCH_PROJECT_COMMENT", "ایجاد/ویرایش یادداشت کار پژوهشی");

    //Managemessages.php By kouroshAtaei

    define("CREATE_EDIT","ایجاد/ویرایش پیام");
    define("C_MESSAGE" , "متن پیام");
    define("AT_FILE" ,"فایل پیوست");
    define("REC_FILE" , "دریاف فایل");
    define("PIC" ,"تصویر");
    define("START_TIME" ,"زمان شروع");
    define("END_TIME", "زمان پایان" );
    define("SAVE_M" , "ذخیره");
    define("NEW_M" ,"جدید");
    define("SEARCH_M" ,"جستجو");
    define("MESSAGES_M" , "پیام ها");
    define("CREATOR_M" , "ایجاد کننده ");
    define("CREATE_TIM_M" , "زمان ایجاد");
    define("ROW_M" , "ردیف");
    define("EDIT_M" , "ویرایش");
    define("DELETE_M" ,"حذف");
    define("ARE_YOU_SURE" , "آیا مطمئن هستید ؟");
    define("ERROR_SEND" ,"خطا در ارسال فایل");
    define("INFO_SAVED" , "اطلاعات ذخیره شد");
    //newResearchProjectRefrences.php By kouroshAtaei
    define("CREAT_AND_EDIT_RES_RESEARCH" , "ایجاد/ویرایش منبع کار پژوهشی");
    define("SEARCH_ENG" , "موتور جستجو");
    define("TAGS_WORDS" ,"کلمات کلیدی جستجو");
    define("LANG_N" ,"زبان");
    define("EN_LAN_N", "انگلیسی");
    define("FA_LAN_N", "فارسی");
    define("TITLE_N" ,"عنوان");
    define("WRITERS_N", "نویسندگان");
    define("YEARS_N", "سال");
    define("SUM_N", "چکیده");
    define("STATE_OF_STUDY", "وضیعت مطالعه");
    define("ALREADY_STUDY", "مطالعه شده");
    define("ALREADY_NOT_STUDY", "مطالعه نشده");
    define("STUDING", "در حال مطالعه");
    define("IMPORTNT", "اهمیت");
    define("CAT_N", "دسته");
    define("ALL_COM", "نظر کلی");
    define("FILE_N" , "فایل");
    define("NOTES_N", "یادداشتها");
    define("CLOSE_N" , "بستن");

    //manageFieldsDataMapping.php By kouroshAtaei
    define("SELECTION_M" , "انتخاب جدول و فیلد مربوطه برای تعیین جدول معادلسازی مقادیر");
    define("TABLE_M" , "جدول") ;
    define("DEF_TABLE", "تعریف جدول معادلسازی");
    define("VAL_FIELD_M","مقادیر معادلسازی شده برای فیلد مربوطه");
    define("REAL_VAL" ,"مقدار اصلی");
    define("M_VAL_EQ" , "مقدار معادل");
    define("DATABASE" , "بانک اطلاعاتی");
    define("FIELD_M" ,"فیلد");



}

?>