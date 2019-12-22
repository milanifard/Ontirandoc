<?php
define("UI_LANGUAGE", "FA"); // EN or FA

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

}

?>
