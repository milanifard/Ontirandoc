create database projectmanagement char set utf8 collate utf8_persian_ci;

create database sessionmanagement char set utf8 collate utf8_persian_ci;

create database baseinfo char set utf8 collate utf8_persian_ci;

create database wordnet char set utf8 collate utf8_persian_ci;

create database ferdowsnet char set utf8 collate utf8_persian_ci;

create database formsgenerator char set utf8 collate utf8_persian_ci;

use formsgenerator;

-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: 172.20.8.186    Database: formsgenerator
-- ------------------------------------------------------
-- Server version	5.5.46-0+deb8u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DELIMITER ;;
CREATE FUNCTION `g2j`(_edate  date) RETURNS varchar(10) CHARSET utf8
    DETERMINISTIC
BEGIN
declare gy,gm,gd    int ;
declare  g_day_no   int ;
declare  i  int;

declare j_day_no ,  j_np ,  jy , jm , jd  int ;
set gy  = year(_edate)-1600;
set gm = month(_edate)-1;
set gd  = day(_edate)-1;

if  (year(_edate) < 1900  or year(_edate) > 2100  )  or (month(_edate) <1  or month(_edate)  > 12 )   or  (day(_edate) < 1 or day(_edate) > 31 )  then
return 'date-error';
end if;

set g_day_no = 365 * gy + floor((gy+3) /  4) - floor((gy+99) / 100) + floor((gy+399)/ 400);

set i=0;
while i < gm do
  set g_day_no=g_day_no+(select  emon from EMonArray  where _id=i+1);
  set i = i + 1;
end while;
if  gm >1  and ((gy % 4 =0 and gy % 100 !=0)  or  (gy%400=0))   then
  set g_day_no = g_day_no + 1 ;
end if;
set  g_day_no = g_day_no + gd;
set  j_day_no =  g_day_no-79;
set  j_np = floor(j_day_no /  12053);
set  j_day_no = j_day_no % 12053;
set  jy = 979+33 *  j_np + 4  *  floor(j_day_no /  1461);
set j_day_no = j_day_no % 1461;

if   j_day_no >= 366  then
  set jy = jy + floor((j_day_no-1) /  365);
  set j_day_no = (j_day_no-1) % 365;
end if;

set  i=0;
while  i < 11  and j_day_no >=  ( select fmon from FMonArray  where _id= i + 1)  do
  set j_day_no = j_day_no - ( select fmon from FMonArray  where _id = i + 1);
  set  i = i + 1;
end while;

set jm = i+1;
set jd = j_day_no+1;

return  concat_ws('/',jy,if(jm < 10 , concat('0',jm) , jm)    ,if(jd < 10 , concat('0',jd) , jd ));
END ;;
DELIMITER ;

--
-- Table structure for table `DetailFormRecords`
--

DROP TABLE IF EXISTS `DetailFormRecords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DetailFormRecords` (
  `DetailFormRecordID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `MasterRecordID` int(10) unsigned NOT NULL COMMENT 'کد رکورد داده اصلی (رکورد فرم اصلی)',
  `MasterFormsStructID` int(10) unsigned NOT NULL COMMENT 'کد ساختار فرم اصلی',
  `DetailRecordID` int(10) unsigned NOT NULL COMMENT 'کد رکورد داده جزییات',
  `DetailFormsStructID` int(10) unsigned NOT NULL COMMENT 'کد ساختار فرم جزییات',
  `CreatorID` int(10) unsigned NOT NULL COMMENT 'کد کاربر ایجاد کننده',
  `CreateTime` datetime NOT NULL COMMENT 'زمان ایجاد',
  `LastUpdatedPersonID` int(10) unsigned NOT NULL COMMENT 'کد آخرین کاربر بروز کننده',
  `LastUpdatedTime` datetime NOT NULL COMMENT 'زمان آخرین بروزرسانی',
  PRIMARY KEY (`DetailFormRecordID`),
  KEY `new_index2` (`DetailRecordID`),
  KEY `new_index` (`MasterRecordID`,`MasterFormsStructID`,`DetailFormsStructID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='رکوردهای جزییات';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `DetailTableRecordCreators`
--

DROP TABLE IF EXISTS `DetailTableRecordCreators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DetailTableRecordCreators` (
  `DetailTableRecordsCreators` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FormStructID` int(10) unsigned NOT NULL COMMENT 'کد ساختار فرم مربوط به جدول جزییات',
  `RecID` int(10) unsigned NOT NULL COMMENT 'کد آیتم مربوطه در جدول جزییات',
  `CereatorID` int(10) unsigned NOT NULL COMMENT 'کد شخص ایجاد کننده رکورد',
  PRIMARY KEY (`DetailTableRecordsCreators`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='ایجاد کننده یک رکورد در جدول جزییات را نگهداری می کند';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `DetailTablesAccessType`
--

DROP TABLE IF EXISTS `DetailTablesAccessType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DetailTablesAccessType` (
  `DetailTablesAccessType` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FormsDetailTableID` int(11) NOT NULL COMMENT 'کد جدول جزییات',
  `FormFlowStepID` int(11) NOT NULL DEFAULT '0' COMMENT 'کد مرحله',
  `EditAccessType` enum('READ_ONLY','ONLY_USER','ALL') COLLATE utf8_persian_ci NOT NULL DEFAULT 'ALL' COMMENT 'نحوه ویرایش',
  `AddAccessType` enum('NOT_ACCESS','ACCESS') COLLATE utf8_persian_ci NOT NULL DEFAULT 'ACCESS' COMMENT 'نحوه اضافه کردن داده',
  `RemoveAccessType` enum('READ_ONLY','ONLY_USER','ALL') COLLATE utf8_persian_ci NOT NULL DEFAULT 'ALL' COMMENT 'نحوه حذف داده ها',
  PRIMARY KEY (`DetailTablesAccessType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='نحوه دسترسی به جداول جزییات در مراحل مختلف گردش قرم';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `EMonArray`
--

DROP TABLE IF EXISTS `EMonArray`;
/*!50001 DROP VIEW IF EXISTS `EMonArray`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `EMonArray` (
  `_id` tinyint NOT NULL,
  `emon` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `EducationalGroups`
--

DROP TABLE IF EXISTS `EducationalGroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EducationalGroups` (
  `EduGrpCode` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'کد گروه',
  `FacCode` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'کد دانشکده',
  `EEduName` char(55) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'عنوان لاتین گروه',
  `PEduName` char(55) COLLATE utf8_persian_ci NOT NULL COMMENT 'عنوان فارسی گروه',
  `UnicefCode` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'کد Unicef',
  PRIMARY KEY (`EduGrpCode`,`FacCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='گروه های آموزشی';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `FMonArray`
--

DROP TABLE IF EXISTS `FMonArray`;
/*!50001 DROP VIEW IF EXISTS `FMonArray`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `FMonArray` (
  `_id` tinyint NOT NULL,
  `fmon` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `FieldTypes`
--

DROP TABLE IF EXISTS `FieldTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FieldTypes` (
  `FieldTypeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `TypeName` varchar(50) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام',
  PRIMARY KEY (`FieldTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='انواع فیلد';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FieldsAccessType`
--

DROP TABLE IF EXISTS `FieldsAccessType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FieldsAccessType` (
  `FieldAccessTypeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FormFieldID` int(10) unsigned NOT NULL COMMENT 'کد فیلد',
  `FormFlowStepID` int(10) unsigned NOT NULL COMMENT 'کد مرحله',
  `AccessType` enum('READ_ONLY','EDITABLE','HIDE') COLLATE utf8_persian_ci NOT NULL DEFAULT 'READ_ONLY' COMMENT 'نوع دسترسی',
  PRIMARY KEY (`FieldAccessTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='تعریف دسترسی به فیلدها در مراحل';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FieldsItemList`
--

DROP TABLE IF EXISTS `FieldsItemList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FieldsItemList` (
  `FieldItemListID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FormFieldID` int(10) unsigned DEFAULT NULL COMMENT 'کد فیلد',
  `ItemValue` int(11) NOT NULL DEFAULT '0' COMMENT 'مقدار آیتم',
  `ItemDescription` varchar(255) COLLATE utf8_persian_ci NOT NULL COMMENT 'شرح آیتم',
  PRIMARY KEY (`FieldItemListID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='آیتمهای لیست برای فیلدهای از نوع لیستی';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FileContentHistory`
--

DROP TABLE IF EXISTS `FileContentHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FileContentHistory` (
  `FileContentHistoryID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FileContentID` int(11) NOT NULL COMMENT 'کلید خارجی به جدول محتویات پرونده ها',
  `PersonID` int(11) NOT NULL COMMENT 'کد شخصی کاربر عمل کننده',
  `ActionType` enum('ADD','UPDATE','REMOVE') COLLATE utf8_persian_ci NOT NULL DEFAULT 'ADD' COMMENT 'نوع عمل',
  `ActionTime` datetime NOT NULL COMMENT 'زمان عمل',
  PRIMARY KEY (`FileContentHistoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='تاریخچه محتویات پرونده ها';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FileContents`
--

DROP TABLE IF EXISTS `FileContents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FileContents` (
  `FileContentID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FileID` int(11) NOT NULL COMMENT 'پرونده الکترونیکی مربوطه',
  `ContentType` enum('TEXT','PHOTO','FILE','FORM','LETTER','SESSION') COLLATE utf8_persian_ci NOT NULL COMMENT 'نوع محتوا',
  `FileName` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام فایل',
  `description` varchar(1000) COLLATE utf8_persian_ci NOT NULL COMMENT 'شرح/خلاصه نامه/خلاصه جلسه',
  `FileContent` mediumblob NOT NULL COMMENT 'محتوای فایل ضمیمه',
  `LetterType` enum('SENT','RECEIVED') COLLATE utf8_persian_ci NOT NULL COMMENT 'نوع نامه',
  `ContentNumber` varchar(50) COLLATE utf8_persian_ci NOT NULL COMMENT 'شماره نامه/جلسه',
  `ContentDate` datetime NOT NULL COMMENT 'تاریخ نامه/جلسه',
  `FormsStructID` int(10) unsigned NOT NULL COMMENT 'ساختار فرم مربوطه',
  `FormRecordID` int(10) unsigned NOT NULL COMMENT 'فرم مربوطه',
  `RelatedContentID` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'کد محتوای مربوطه (زمانیکه محتوا لینکی به محتوای اصلی است)',
  `ContentStatus` enum('ENABLE','DISABLE') COLLATE utf8_persian_ci NOT NULL DEFAULT 'ENABLE' COMMENT 'وضعیت محتوا (برای حذف منطقی)',
  `OrderNo` int(11) NOT NULL COMMENT 'شماره ترتیب',
  PRIMARY KEY (`FileContentID`),
  KEY `new_index` (`FileID`,`ContentType`,`ContentStatus`) USING BTREE,
  KEY `new_index2` (`RelatedContentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='محتویات پرونده';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FileFormsTemporarayAccessList`
--

DROP TABLE IF EXISTS `FileFormsTemporarayAccessList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FileFormsTemporarayAccessList` (
  `FileFormsTemporarayAccessListID` int(11) NOT NULL AUTO_INCREMENT,
  `FilesTemporarayAccessListID` int(10) unsigned NOT NULL COMMENT 'کلید به جدول فرمهای امانت داده شده',
  `FormFieldID` int(10) unsigned NOT NULL COMMENT 'کلید به جدول فیلدهای فرم',
  `AccessType` enum('READ_ONLY','EDITABLE','HIDE') COLLATE utf8_persian_ci NOT NULL DEFAULT 'HIDE' COMMENT 'نوع دسترسی',
  PRIMARY KEY (`FileFormsTemporarayAccessListID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='مجوز دسترسی به فیلدهای فرم در یک ارسال موقت (امانت)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FileFormsTemporaryAccessForAddRemove`
--

DROP TABLE IF EXISTS `FileFormsTemporaryAccessForAddRemove`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FileFormsTemporaryAccessForAddRemove` (
  `FileFormsTemporaryAccessForAddRemoveID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FilesTemporarayAccessListID` int(11) NOT NULL COMMENT 'کلید خارجی به جدول پرونده های به امانت داده شده',
  `FormsStructID` int(11) NOT NULL COMMENT 'کد ساختار فرم مربوطه',
  `AddPermission` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'دسترسی برای ایجاد فرم جدید از این نوع',
  `RemovePermission` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'دسترسی برای حذف فرم از این نوع از پرونده',
  PRIMARY KEY (`FileFormsTemporaryAccessForAddRemoveID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='تعریف دسترسی موقت به فرمهای یک پرونده برای اضافه یا حذف فرم';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FileHistory`
--

DROP TABLE IF EXISTS `FileHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FileHistory` (
  `FileHistoryID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FileID` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'کلید خارجی به جدول پرونده های الکترونیکی',
  `ActionTime` datetime NOT NULL COMMENT 'زمان عمل',
  `PersonID` int(11) NOT NULL COMMENT 'کد شخصی فرد عمل کننده',
  `description` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT 'شرح - برای مشخص کردن فیلدهای تغییر داده شده',
  PRIMARY KEY (`FileHistoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='تاریخچه پرونده';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FileTypeForms`
--

DROP TABLE IF EXISTS `FileTypeForms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FileTypeForms` (
  `FileTypeFormID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FileTypeID` int(10) unsigned NOT NULL COMMENT 'کد نوع پرونده',
  `FormsStructID` int(11) NOT NULL COMMENT 'کلید خارجی به جدول ساختار فرمها',
  `mandatory` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'اجباری/اختیاری بودن فرم',
  PRIMARY KEY (`FileTypeFormID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='فرمهای مجاز برای اضافه شدن در این نوع پرونده';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FileTypeUserPermissions`
--

DROP TABLE IF EXISTS `FileTypeUserPermissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FileTypeUserPermissions` (
  `FileTypeUserPermissionID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FileTypeID` int(10) unsigned NOT NULL COMMENT 'نوع پرونده',
  `PersonID` int(10) unsigned NOT NULL COMMENT 'کد شخصی کاربر مجاز',
  `AccessRange` enum('UNIT','SUB_UNIT','EDU_GROUP','ONLY_USER','ALL') COLLATE utf8_persian_ci NOT NULL DEFAULT 'ALL' COMMENT 'محدوده دسترسی کاربر بر اساس مکان (واحدهای سازمانی خاص/زیر واحدهای سازمانی خاص/گروه های آموزشی خاص)',
  `DefineAccessPermission` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'مجوز تعریف نحوه دسترسی',
  `AddPermission` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'مجوز اضافه کرن پرونده',
  `RemovePermission` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'مجوز حذف پرونده',
  `UpdatePermission` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'مجوز بروزرسانی مشخصات اصلی',
  `ContentUpdatePermission` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'مجوز بروزرسانی محتوای غیر فرمی پرونده',
  `ViewPermission` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'مجوز مشاهده پرونده',
  `TemporarySendPermission` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'مجوز ارسال موقت (امانت) یک پرونده',
  PRIMARY KEY (`FileTypeUserPermissionID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='مجوزهای کاربران روی انواع پرونده';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FileTypeUserPermittedEduGroups`
--

DROP TABLE IF EXISTS `FileTypeUserPermittedEduGroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FileTypeUserPermittedEduGroups` (
  `FileTypeUserPermittedEduGroupID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FileTypeUserPermissionID` int(10) unsigned NOT NULL COMMENT 'کلید به جدول مجوز دسترسی کاربران به انوع پرونده',
  `EduGrpCode` int(11) NOT NULL COMMENT 'گروه آموزشی',
  PRIMARY KEY (`FileTypeUserPermittedEduGroupID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='گروه های آموزشی مجاز برای دسترسی کاربر روی انواع پرونده';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FileTypeUserPermittedFormDetails`
--

DROP TABLE IF EXISTS `FileTypeUserPermittedFormDetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FileTypeUserPermittedFormDetails` (
  `FileTypeUserPermittedFormDetail` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FileTypeUserPermittedFormID` int(11) NOT NULL COMMENT 'کلید خارجی به جدول تعریف دسترسی کاربر به فرمهای یک نوع پرونده',
  `FormFieldID` int(11) NOT NULL COMMENT 'کلید خارجی به جدول فیلدهای یک فرم',
  `AccessType` enum('READ_ONLY','EDITABLE','HIDE') COLLATE utf8_persian_ci NOT NULL COMMENT 'نحوه دسترسی کاربر به این فیلد',
  PRIMARY KEY (`FileTypeUserPermittedFormDetail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='جزییات نحوه دسترسی کاربر به فیلدهای یک فرم در یک نوع پرونده ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FileTypeUserPermittedForms`
--

DROP TABLE IF EXISTS `FileTypeUserPermittedForms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FileTypeUserPermittedForms` (
  `FileTypeUserPermittedFormID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FileTypeUserPermissionID` int(10) unsigned NOT NULL COMMENT 'کد رکورد دسترسی کاربر',
  `FormsStructID` int(10) unsigned NOT NULL COMMENT 'کد ساختار فرم مربوطه',
  `AddFormPermission` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'مجوز اضافه کردن فرم',
  `RemoveFormPermission` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'مجوز حذف فرم',
  `ViewFormPermission` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'مجوز مشاهده/ویرایش فرم',
  PRIMARY KEY (`FileTypeUserPermittedFormID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='فرمهای مجاز و نحوه دسترسی به آنها در انواع پرونده';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FileTypeUserPermittedSubUnits`
--

DROP TABLE IF EXISTS `FileTypeUserPermittedSubUnits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FileTypeUserPermittedSubUnits` (
  `FileTypeUserPermittedSubUnitID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FileTypeUserPermissionID` int(10) unsigned NOT NULL COMMENT 'کلید به جدول مجوز دسترسی کاربران به انوع پرونده',
  `SubUnitID` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'زیر واحد سازمانی',
  `UnitID` int(10) unsigned NOT NULL COMMENT 'واحد سازمانی',
  PRIMARY KEY (`FileTypeUserPermittedSubUnitID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='زیر واحدهای سازمانی مجاز برای دسترسی کاربر روی انواع پرونده';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FileTypeUserPermittedUnits`
--

DROP TABLE IF EXISTS `FileTypeUserPermittedUnits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FileTypeUserPermittedUnits` (
  `FileTypeUserPermittedUnitID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FileTypeUserPermissionID` int(10) unsigned NOT NULL COMMENT 'کلید به جدول مجوز دسترسی کاربران به انوع پرونده',
  `ouid` int(11) NOT NULL COMMENT 'واحد سازمانی',
  PRIMARY KEY (`FileTypeUserPermittedUnitID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='واحدهای سازمانی مجاز برای دسترسی کاربر روی انواع پرونده';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FileTypes`
--

DROP TABLE IF EXISTS `FileTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FileTypes` (
  `FileTypeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FileTypeName` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام',
  `UserCanChangeLocation` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'YES' COMMENT 'آیا کاربر مجاز به تعیین دستی محل پرونده (واحد سازمانی - زیر واحد سازمانی - گروه آموزشی) می باشد',
  `SetLocationType` enum('CREATOR','RELATED_PERSON','NONE') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NONE' COMMENT 'محل پرونده (واحد - زیر واحد - گروه آموزشی) بر چه اساس پر شود',
  `RelatedPersonCanBeProffessor` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'فرد مربوط به پرونده می تواند استاد باشد',
  `RelatedPersonCanBeStaff` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'فرد مربوط به پرونده می تواند کارمند باشد',
  `RelatedPersonCanBeStudent` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'فرد مربوط به پرونده می تواند دانشجو باشد',
  `RelatedPersonCanBeOther` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'فرد مربوط به پرونده می تواند فرد متفرقه باشد',
  `RelatedToPerson` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'پرونده مربوط به یک شخص می باشد؟',
  PRIMARY KEY (`FileTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='انواع پرونده';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FilesTemporarayAccessList`
--

DROP TABLE IF EXISTS `FilesTemporarayAccessList`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FilesTemporarayAccessList` (
  `FilesTemporarayAccessListID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SenderID` int(10) unsigned NOT NULL COMMENT 'کاربر ارسال کننده (امانت دهنده)',
  `ReceiverID` int(10) unsigned NOT NULL COMMENT 'کاربر دریافت کننده (امانت گیرنده)',
  `SendDate` datetime NOT NULL COMMENT 'تاریخ ارسال',
  `FileID` int(11) NOT NULL COMMENT 'کد پرونده مربوطه',
  `ContentUpdatePermission` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'مجوز بروزرسانی محتویات غیر فرمی پرونده',
  PRIMARY KEY (`FilesTemporarayAccessListID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='دسترسی موقت به پرونده ها (امانت پرونده ها)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FilesTemporaryAccessListHistory`
--

DROP TABLE IF EXISTS `FilesTemporaryAccessListHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FilesTemporaryAccessListHistory` (
  `FilesTemporaryAccessListHistoryID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SenderID` int(11) NOT NULL COMMENT 'فرستنده',
  `ReceiverID` int(11) NOT NULL COMMENT 'گیرنده',
  `ActionTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'زمان انجام',
  `FileID` int(11) NOT NULL COMMENT 'کد پرونده',
  `ActionType` enum('SEND','REMOVE','UPDATE') COLLATE utf8_persian_ci NOT NULL DEFAULT 'SEND' COMMENT 'نوع عملیات (ارسال پرونده یا حذف ارسال)',
  PRIMARY KEY (`FilesTemporaryAccessListHistoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='تاریخچه امانت پرونده ها';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FormFields`
--

DROP TABLE IF EXISTS `FormFields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FormFields` (
  `FormFieldID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FormsStructID` int(10) unsigned NOT NULL COMMENT 'کد فرم مربوطه',
  `RelatedFieldName` varchar(50) COLLATE utf8_persian_ci NOT NULL COMMENT 'فیلد متناظر در جدول اطلاعاتی',
  `FieldTitle` varchar(255) COLLATE utf8_persian_ci NOT NULL COMMENT 'عنوان فیلد',
  `FieldType` int(10) unsigned NOT NULL COMMENT 'نوع فیلد کلید (کلید خارجی به جدول انواع فیلد)',
  `MaxLength` int(10) unsigned NOT NULL COMMENT 'حداکثر طول داده مجاز',
  `InputWidth` int(10) unsigned NOT NULL COMMENT 'طول جعبه ورود داده',
  `InputRows` int(10) unsigned NOT NULL COMMENT 'ارتفاع جعبه ورود داده (مخصوص فیلدهای چند خطی)',
  `MinNumber` float NOT NULL COMMENT 'شروع بازه مجاز داده های عددی',
  `MaxNumber` float NOT NULL COMMENT 'انتهای بازه مجاز داده های عددی',
  `MaxFileSize` int(10) unsigned NOT NULL COMMENT 'حداکثر حجم مجاز برای فایل',
  `CreatingListType` enum('STATIC_LIST','RELATED_TABLE','QUERY','DOMAINS') COLLATE utf8_persian_ci NOT NULL DEFAULT 'STATIC_LIST' COMMENT 'نحوه ساخت لیست برای فیلدهای لیستی',
  `AddAllItemsToList` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'YES' COMMENT 'آیا آیتمی به نام همه مقادیر در لیست آیتمها قرار داده شود؟ (با کد صفر)',
  `ListRelatedTable` varchar(50) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام جدول برای تولید لیست آیتمها',
  `ListRelatedValueField` varchar(50) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام فیلد معادل مقدار در تولید لیست',
  `ListRelatedDescriptionField` varchar(50) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام فیلد معدل شرح در تولید لیست',
  `ListRelatedDomainName` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'DomainName مربوطه در زمانیکه لیست از روی جدول domains ساخته می شود',
  `ListQuery` varchar(1000) COLLATE utf8_persian_ci NOT NULL COMMENT 'query ساخت لیست',
  `FieldInputType` enum('OPTIONAL','MANDATORY') COLLATE utf8_persian_ci NOT NULL DEFAULT 'OPTIONAL' COMMENT 'نوع ورود داده به فیلد (اجباری/اختیاری)',
  `DefaultValue` varchar(50) COLLATE utf8_persian_ci NOT NULL COMMENT 'مقدار پیش فرض',
  `ValidFileExtensions` varchar(255) COLLATE utf8_persian_ci NOT NULL COMMENT 'پسوندهای مجاز فابل',
  `ShowInList` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL COMMENT 'در لیست به عنوان یک ستون نمایش داده شود؟',
  `ColumnOrder` int(10) unsigned NOT NULL COMMENT 'شماره ترتیب ستون در لیست',
  `ColumnWidth` int(10) unsigned NOT NULL COMMENT 'عرض ستون در لیست',
  `ListShowType` enum('COMBOBOX','LOOKUP','RADIO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'COMBOBOX' COMMENT '???? ????? ???? (??????? ?????)',
  `LookUpPageAddress` varchar(255) COLLATE utf8_persian_ci NOT NULL COMMENT 'آدرس صفحه مربوط به جستجوی داده (در فیلدهای لیستی)',
  `OrderInInputForm` int(10) unsigned NOT NULL COMMENT 'شماره ترتیب در صفحه ورود داده',
  `ImageWidth` int(10) unsigned NOT NULL COMMENT 'عرض تصویر',
  `ImageHeight` int(10) unsigned NOT NULL COMMENT 'ارتفاع تصویر',
  `FieldHint` varchar(500) COLLATE utf8_persian_ci NOT NULL COMMENT 'متنی که جلوی فیلد نمایش داده می شود',
  `RelatedFileNameField` varchar(50) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام فیلد برای نگهداری اسم فایل - مخصوص فیلدهای نوع فایل و تصویر',
  `HTMLEditor` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'آیا HTML Editor روی فیلد فعال باشد (مخصوص فیلدهای متنی چند خطی)',
  `FormsSectionID` int(11) NOT NULL DEFAULT '0' COMMENT 'کد بخش',
  `ShowSlider` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'به شکل اسلایدر نشان داده شود',
  `SliderLength` int(11) NOT NULL DEFAULT '200' COMMENT 'طول اسلایدر',
  `SliderStartLabel` varchar(255) COLLATE utf8_persian_ci NOT NULL COMMENT 'برچسب ابتدای اسلایدر',
  `SliderEndLabel` varchar(255) COLLATE utf8_persian_ci NOT NULL COMMENT 'برچسب انتهای اسلایدر',
  `DisplayColor` enum('BLACK','RED','GREEN') COLLATE utf8_persian_ci NOT NULL DEFAULT 'BLACK' COMMENT 'رنگ نمایش',
  `DisplayType` enum('SIMPLE','BOLD','ITALIC') COLLATE utf8_persian_ci NOT NULL DEFAULT 'SIMPLE' COMMENT 'نوع نمایش',
  `IsDataSummary` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'قرار گرفتن داده این فیلد در خلاصه داده فرم',
  `RelatedURL` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'آدرس صفحه مرتبط برای اجرا در زمان پر شدن این فیلد',
  PRIMARY KEY (`FormFieldID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='فیلدهای فرم';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FormLabels`
--

DROP TABLE IF EXISTS `FormLabels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FormLabels` (
  `FormsLabelID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `LabelDescription` text COLLATE utf8_persian_ci NOT NULL COMMENT 'شرح',
  `LocationType` enum('AFTER','BEFORE') COLLATE utf8_persian_ci NOT NULL COMMENT 'محل قرار گرفتن',
  `RelatedFieldID` int(11) NOT NULL COMMENT 'کد فیلدی که برچسب قبل یا بعد از آن قرار می گیرد',
  `ShowType` enum('SIMPLE','BOLD','ITALIC') COLLATE utf8_persian_ci NOT NULL DEFAULT 'SIMPLE' COMMENT 'نحوه نمایش - توپر - زیر خط دار - ساده',
  `ShowHorizontalLine` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'خط افقی زیر برچسب کشیده شود؟',
  PRIMARY KEY (`FormsLabelID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='برچسبهای یک فرم';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FormManagers`
--

DROP TABLE IF EXISTS `FormManagers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FormManagers` (
  `FormManagerID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FormsStructID` int(10) unsigned NOT NULL COMMENT 'کد ساختار فرم',
  `PersonID` int(10) unsigned NOT NULL COMMENT 'کد شخصی مدیر',
  `AccessType` enum('FULL','DATA','STRUCT') COLLATE utf8_persian_ci NOT NULL DEFAULT 'FULL' COMMENT 'نوع دسترسی',
  PRIMARY KEY (`FormManagerID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='مدیران ساختارهای فرم';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FormsDataUpdateHistory`
--

DROP TABLE IF EXISTS `FormsDataUpdateHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FormsDataUpdateHistory` (
  `FormsDataUpdateHistoryID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FormsStructID` int(10) unsigned NOT NULL COMMENT 'کد فرم',
  `RecID` int(11) NOT NULL COMMENT 'کد رکورد مورد نظر در جدول اطلاعاتی (داده فرم)',
  `PersonID` int(11) NOT NULL DEFAULT '0' COMMENT 'کد کاربر بروزرسانی کننده',
  `UpdateTime` datetime NOT NULL COMMENT 'زمان بروزرسانی',
  `description` varchar(1000) COLLATE utf8_persian_ci DEFAULT NULL,
  `PersonType` enum('PERSONEL','STUDENT') COLLATE utf8_persian_ci NOT NULL DEFAULT 'PERSONEL' COMMENT 'نوع شخص',
  PRIMARY KEY (`FormsDataUpdateHistoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='تاریخچه تغییرات روی داده های فرمها';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FormsDetailTables`
--

DROP TABLE IF EXISTS `FormsDetailTables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FormsDetailTables` (
  `FormsDetailTableID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FormStructID` int(11) NOT NULL COMMENT 'کد فرم اصلی',
  `DetailFormStructID` int(11) NOT NULL COMMENT 'کد فرم/جدول جزییات',
  `RelatedField` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام فیلد ارتباطی جدول جزییات با کلید جدول اصلی',
  `OrderNo` smallint(6) NOT NULL COMMENT 'شماره ترتیب جدول جزییات',
  PRIMARY KEY (`FormsDetailTableID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='جداول جزییات مربوط به فرمها';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FormsFlowHistory`
--

DROP TABLE IF EXISTS `FormsFlowHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FormsFlowHistory` (
  `FormsFlowHistoryID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FormsStructID` int(11) NOT NULL COMMENT 'کد فرم ',
  `RecID` int(10) unsigned NOT NULL COMMENT 'کد رکورد داده',
  `FromPersonID` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'کد کاربر ارسال کننده',
  `FromStepID` int(10) unsigned NOT NULL COMMENT 'کد مرحله ای که از آن مرحله ارسال صورت گرفته است',
  `ToStepID` int(10) unsigned NOT NULL COMMENT 'کد مرحله ای که رکرود به آن ارسال شده است',
  `SendDate` datetime NOT NULL COMMENT 'زمان ارسال',
  `SenderType` enum('PERSONEL','STUDENT') COLLATE utf8_persian_ci NOT NULL DEFAULT 'PERSONEL' COMMENT 'نوع ارسال کننده',
  PRIMARY KEY (`FormsFlowHistoryID`),
  KEY `new_index` (`FormsStructID`,`RecID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='تاریخچه گردش فرم';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FormsFlowStepRelations`
--

DROP TABLE IF EXISTS `FormsFlowStepRelations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FormsFlowStepRelations` (
  `FormFlowStepRelationID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FormFlowStepID` int(10) unsigned NOT NULL COMMENT 'کد مرحله',
  `NextStepID` int(10) unsigned NOT NULL COMMENT 'کد مرحله بعدی',
  PRIMARY KEY (`FormFlowStepRelationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='ارتباط مراحل در گردش فرم';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FormsFlowSteps`
--

DROP TABLE IF EXISTS `FormsFlowSteps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FormsFlowSteps` (
  `FormsFlowStepID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FormsStructID` int(11) NOT NULL COMMENT 'کد فرم مربوطه',
  `StepTitle` varchar(255) COLLATE utf8_persian_ci NOT NULL COMMENT 'عنوان',
  `StepType` enum('START','ARCHIVE','OTHER') COLLATE utf8_persian_ci NOT NULL DEFAULT 'OTHER' COMMENT 'نوع مرحله (شروع / بایگانی/ غیره)',
  `StudentPortalAccess` enum('ALLOW','DENY') COLLATE utf8_persian_ci NOT NULL COMMENT 'دسترسی از طریق پورتال دانشجویی',
  `StaffPortalAccess` enum('ALLOW','DENY') COLLATE utf8_persian_ci NOT NULL COMMENT 'دسترسی از طریق پورتال کارمندی',
  `ProfPortalAccess` enum('ALLOW','DENY') COLLATE utf8_persian_ci NOT NULL COMMENT 'دسترسی از طریق پورتال اساتید',
  `OtherPortalAccess` enum('ALLOW','DENY') COLLATE utf8_persian_ci NOT NULL COMMENT 'دسترسی از طریق پورتال سایر',
  `FilterType` enum('NO_FILTER','UNITS','SUB_UNITS','EDU_GROUPS') COLLATE utf8_persian_ci NOT NULL COMMENT 'نوع فیلتر اعمال شده برای دسترسی کاربران',
  `FilterOnUserRoles` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL COMMENT 'فیلتر بر اساس نقش کاربران',
  `FilterOnSpecifiedUsers` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL COMMENT 'فیلتر بر اساس کاربران خاص',
  `UserAccessRange` enum('ALL','HIM','UNIT','SUB_UNIT','EDU_GROUP','BELOW_IN_CHART_ALL_LEVEL','BELOW_IN_CHART_LEVEL1','UNDER_MANAGEMENT','BELOW_IN_CHART_LEVEL2','BELOW_IN_CHART_LEVEL3','BELOW_IN_CHART_LEVEL4') COLLATE utf8_persian_ci NOT NULL DEFAULT 'ALL' COMMENT 'فضای داده های در دسترس کاربر (خودش - واحدش - زیر واحدش - گروه آموزشی خودش)',
  `RelatedOrganzationChartID` int(11) NOT NULL COMMENT 'کد چارت سازمانی مربوطه',
  `AccessRangeRelatedPersonType` enum('CREATOR','SENDER') COLLATE utf8_persian_ci NOT NULL COMMENT 'مشخص می کند محدودیت بازه دسترسی بر اساس ایجاد کننده فرم است یا فرستنده فرم',
  `ShowBarcodeInPrintPage` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'کد فرم به صورت بارکد در صفحه چاپ نمایش داده شود',
  `UserCanBackward` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'YES' COMMENT 'کاربر می تواند فرم را به مرحله قبل برگشت بزند',
  `PrintPageTitle` varchar(500) COLLATE utf8_persian_ci NOT NULL COMMENT 'عنوان بالای صفحه',
  `PrintPageHeader` varchar(1000) COLLATE utf8_persian_ci NOT NULL COMMENT 'متن بالای صفحه',
  `PrintPageFooter` varchar(1000) COLLATE utf8_persian_ci NOT NULL COMMENT 'متن پایین صفحه',
  `PrintPageSigniture` enum('WITH_NAME','WITHOUT_NAME','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'نوع امضای پایین صفحه چاپ',
  `ShowHistoryInPrintPage` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'سابقه ارسالها در پایین صفحه چاپ نمایش داده شود',
  `NumberOfPermittedSend` int(11) NOT NULL DEFAULT '0' COMMENT 'تعداد قابل ارسال از این فرم',
  `LimitationOfNumberPeriod` enum('FOREVER','YEAR','MONTH','DAY') COLLATE utf8_persian_ci NOT NULL DEFAULT 'FOREVER' COMMENT 'بازه محدودیت تعداد ارسال',
  `SendDatePermittedStartDate` datetime NOT NULL COMMENT 'تاریخ مجاز شروع ارسال',
  `SendDatePermittedEndDate` datetime NOT NULL COMMENT 'پایان تاریخ مجاز ارسال',
  PRIMARY KEY (`FormsFlowStepID`),
  KEY `FilterType` (`FilterType`),
  KEY `StepType` (`StepType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='مراحل گردش فرمها';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FormsPermittedSystems`
--

DROP TABLE IF EXISTS `FormsPermittedSystems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FormsPermittedSystems` (
  `FormsPermittedSystemID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FormsStructID` int(10) unsigned DEFAULT NULL COMMENT 'کد فرم مربوطه',
  `SysCode` int(10) unsigned NOT NULL COMMENT 'کد سیستم',
  PRIMARY KEY (`FormsPermittedSystemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='سیستمهای مجاز برای دسترسی به فرم';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FormsRecords`
--

DROP TABLE IF EXISTS `FormsRecords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FormsRecords` (
  `FormsRecordsID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `RelatedRecordID` int(10) unsigned NOT NULL COMMENT 'کد رکورد مربوطه در جدول اطلاعاتی',
  `FormsStructID` int(10) unsigned NOT NULL COMMENT 'کد ساختار فرم',
  `FormFlowStepID` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'کد مرحله',
  `SendDate` datetime NOT NULL COMMENT 'زمان ارسال داده به این مرحله',
  `SenderID` int(11) NOT NULL COMMENT 'کاربر ارسال کننده به این مرحله',
  `CreatorID` int(11) NOT NULL COMMENT 'کاربر ایجاد کننده داده',
  `SenderType` enum('PERSONEL','STUDENT') COLLATE utf8_persian_ci NOT NULL DEFAULT 'PERSONEL' COMMENT 'نوع فرستنده',
  `CreatorType` enum('PERSONEL','STUDENT') COLLATE utf8_persian_ci NOT NULL DEFAULT 'PERSONEL' COMMENT 'نوع ایجاد کننده',
  PRIMARY KEY (`FormsRecordsID`),
  KEY `new_index` (`FormFlowStepID`,`RelatedRecordID`) USING BTREE,
  KEY `new_index2` (`CreatorID`),
  KEY `new_index3` (`SenderID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='رکوردهای مربوط به فرمها';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FormsSections`
--

DROP TABLE IF EXISTS `FormsSections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FormsSections` (
  `FormsSectionID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FormsStructID` int(10) unsigned NOT NULL COMMENT 'کد ساختار فرم',
  `SectionName` varchar(250) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام بخش',
  `ShowOrder` int(11) NOT NULL COMMENT 'ترتیب نمایش',
  `HeaderDesc` varchar(1000) COLLATE utf8_persian_ci NOT NULL COMMENT 'سرتیتر',
  `FooterDesc` varchar(1000) COLLATE utf8_persian_ci NOT NULL COMMENT 'پانویس',
  PRIMARY KEY (`FormsSectionID`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='بخش بندیهای فرمها';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FormsStruct`
--

DROP TABLE IF EXISTS `FormsStruct`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FormsStruct` (
  `FormsStructID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `RelatedDB` varchar(50) COLLATE utf8_persian_ci NOT NULL COMMENT 'بانک اطلاعاتی مربوطه',
  `RelatedTable` varchar(50) COLLATE utf8_persian_ci NOT NULL COMMENT 'جدول اطلاعاتی مربوطه',
  `FormTitle` varchar(255) COLLATE utf8_persian_ci NOT NULL COMMENT 'عنوان فرم',
  `TopDescription` varchar(2000) COLLATE utf8_persian_ci NOT NULL COMMENT 'توضیحات بالای فرم',
  `ButtomDescription` varchar(1000) COLLATE utf8_persian_ci NOT NULL COMMENT 'توضیحات پایین فرم',
  `JavascriptCode` text COLLATE utf8_persian_ci NOT NULL COMMENT 'کد جاوا اسکریپت ',
  `SortByField` varchar(50) COLLATE utf8_persian_ci NOT NULL COMMENT 'فیلد پیش فرض مرتب سازی لیست',
  `SortType` enum('DESC','ASC') COLLATE utf8_persian_ci NOT NULL DEFAULT 'ASC' COMMENT 'ترتیب مرتب سازی پیش فرض',
  `KeyFieldName` varchar(50) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام فیلد کلید',
  `PrintType` enum('DEFAULT','SPECIAL') COLLATE utf8_persian_ci NOT NULL DEFAULT 'DEFAULT' COMMENT 'نوع صفحه چاپی (پیش فرض/اختصاصی)',
  `PrintPageAddress` varchar(255) COLLATE utf8_persian_ci NOT NULL COMMENT 'آدرس صفحه چاپ اختصاصی',
  `CreatorUser` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'کاربر سازنده',
  `CreateDate` datetime NOT NULL COMMENT 'تاریخ ایجاد',
  `FormType` enum('SYSTEM','USER') COLLATE utf8_persian_ci NOT NULL DEFAULT 'USER' COMMENT 'نوع فرم (سیستمی/کاربری)',
  `ParentID` int(11) NOT NULL DEFAULT '0' COMMENT 'کد فرم پدر (برای جداول جزییات)',
  `ShowType` enum('1COLS','2COLS') COLLATE utf8_persian_ci NOT NULL DEFAULT '2COLS' COMMENT 'نحوه نمایش فرم ورود داده - یک ستونی یا دو ستونی',
  `ValidationExtraJavaScript` text COLLATE utf8_persian_ci NOT NULL COMMENT 'کد اعتبارسنجی جاوااسکریپت علاوه بر مقادیر پیش فرض',
  `IsQuestionnaire` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'آیا این فرم پرسشنامه است',
  `ShowBorder` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT '????? ????? ?? ?? ??? ???? ????',
  `QuestionColumnWidth` varchar(4) COLLATE utf8_persian_ci NOT NULL COMMENT 'عرض ستون سوالات',
  PRIMARY KEY (`FormsStructID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='ساختار فرمها';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `QuestionnairesCreators`
--

DROP TABLE IF EXISTS `QuestionnairesCreators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `QuestionnairesCreators` (
  `QuestionnairesCreatorID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UserID` varchar(50) COLLATE utf8_persian_ci NOT NULL COMMENT 'کد کاربری',
  `FormsStructID` int(11) NOT NULL COMMENT 'کد پرسشنامه',
  `RelatedRecordID` int(11) NOT NULL COMMENT 'کد رکورد مربوطه',
  `FillDate` datetime NOT NULL COMMENT 'زمان ثبت',
  PRIMARY KEY (`QuestionnairesCreatorID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='ایجاد کننده هر پرسشنامه';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ReceivedForms`
--

DROP TABLE IF EXISTS `ReceivedForms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ReceivedForms` (
  `ReceivedFormsID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PersonID` int(10) unsigned NOT NULL COMMENT 'کد شخص دریافت کننده',
  `RecID` int(10) unsigned NOT NULL COMMENT 'کد رکورد مربوط به داده های فرم',
  `FormFlowStepID` int(10) unsigned NOT NULL COMMENT 'کد مرحله مربوط به فرم',
  `SendDate` datetime NOT NULL COMMENT 'تاریخ ارسال',
  `SenderID` int(11) NOT NULL COMMENT 'ارسال کننده',
  `CreatorID` int(11) NOT NULL COMMENT 'ایجاد کننده',
  `SenderType` enum('PERSONEL','STUDENT') COLLATE utf8_persian_ci NOT NULL DEFAULT 'PERSONEL' COMMENT 'نوع ارسال کننده',
  `CreatorType` enum('PERSONEL','STUDENT') COLLATE utf8_persian_ci NOT NULL DEFAULT 'PERSONEL' COMMENT 'نوع ایجاد کننده',
  PRIMARY KEY (`ReceivedFormsID`),
  KEY `new_index` (`PersonID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='فرمهای دریافتی افراد';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SysAudit`
--

DROP TABLE IF EXISTS `SysAudit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SysAudit` (
  `RecID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `UserID` varchar(15) COLLATE utf8_persian_ci DEFAULT NULL,
  `ActionType` tinyint(3) unsigned DEFAULT NULL,
  `ActionDesc` varchar(200) COLLATE utf8_persian_ci DEFAULT NULL,
  `IPAddress` bigint(20) DEFAULT NULL,
  `SysCode` tinyint(3) unsigned DEFAULT NULL,
  `IsSecure` tinyint(3) unsigned DEFAULT NULL,
  `ATS` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TemporaryUsers`
--

DROP TABLE IF EXISTS `TemporaryUsers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TemporaryUsers` (
  `TemporaryUserID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `WebUserID` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام کاربری',
  `WebPassword` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT 'کلمه عبور',
  `UserStatus` enum('ENABLE','DISABLE') COLLATE utf8_persian_ci NOT NULL COMMENT 'وضعیت کاربر',
  PRIMARY KEY (`TemporaryUserID`),
  KEY `WebUserID_index` (`WebUserID`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='کاربران موقت که برای پر کردن پرسشنامه استفاده می شود';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TemporaryUsersAccessForms`
--

DROP TABLE IF EXISTS `TemporaryUsersAccessForms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TemporaryUsersAccessForms` (
  `TemporaryUsersAccessFormID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `WebUserID` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT 'کد کاربری',
  `FormsStructID` int(11) NOT NULL COMMENT 'کد فرم مربوطه',
  `filled` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL COMMENT 'مشخص می کند کاربر فرم را پر کرده یا خیر',
  `FilledDate` datetime NOT NULL COMMENT 'زمان پر شدن فرم',
  PRIMARY KEY (`TemporaryUsersAccessFormID`),
  KEY `WebUserID_index` (`WebUserID`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='فرمهایی که کاربران موقت امکان پر کردن آنها را دارند';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `domains`
--

DROP TABLE IF EXISTS `domains`;
/*!50001 DROP VIEW IF EXISTS `domains`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `domains` (
  `DomainName` tinyint NOT NULL,
  `description` tinyint NOT NULL,
  `eDescription` tinyint NOT NULL,
  `DomainValue` tinyint NOT NULL,
  `unc_code` tinyint NOT NULL,
  `ActiveDomain` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `EMonArray`
--

/*!50001 DROP TABLE IF EXISTS `EMonArray`*/;
/*!50001 DROP VIEW IF EXISTS `EMonArray`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`172.21.10.11` SQL SECURITY DEFINER */
/*!50001 VIEW `EMonArray` AS select `projectmanagement`.`EMonArray`.`_id` AS `_id`,`projectmanagement`.`EMonArray`.`emon` AS `emon` from `projectmanagement`.`EMonArray` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `FMonArray`
--

/*!50001 DROP TABLE IF EXISTS `FMonArray`*/;
/*!50001 DROP VIEW IF EXISTS `FMonArray`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`172.21.10.11` SQL SECURITY DEFINER */
/*!50001 VIEW `FMonArray` AS select `projectmanagement`.`FMonArray`.`_id` AS `_id`,`projectmanagement`.`FMonArray`.`fmon` AS `fmon` from `projectmanagement`.`FMonArray` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `domains`
--

/*!50001 DROP TABLE IF EXISTS `domains`*/;
/*!50001 DROP VIEW IF EXISTS `domains`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`172.21.10.11` SQL SECURITY DEFINER */
/*!50001 VIEW `domains` AS select `projectmanagement`.`domains`.`DomainName` AS `DomainName`,`projectmanagement`.`domains`.`description` AS `description`,`projectmanagement`.`domains`.`eDescription` AS `eDescription`,`projectmanagement`.`domains`.`DomainValue` AS `DomainValue`,`projectmanagement`.`domains`.`unc_code` AS `unc_code`,`projectmanagement`.`domains`.`ActiveDomain` AS `ActiveDomain` from `projectmanagement`.`domains` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-09-02 16:35:06

use wordnet;

-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: 172.20.8.186    Database: wordnet
-- ------------------------------------------------------
-- Server version	5.5.46-0+deb8u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Temporary table structure for view `adjectiveswithpositions`
--

DROP TABLE IF EXISTS `adjectiveswithpositions`;
/*!50001 DROP VIEW IF EXISTS `adjectiveswithpositions`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `adjectiveswithpositions` (
  `synsetid` tinyint NOT NULL,
  `wordid` tinyint NOT NULL,
  `casedwordid` tinyint NOT NULL,
  `senseid` tinyint NOT NULL,
  `sensenum` tinyint NOT NULL,
  `lexid` tinyint NOT NULL,
  `tagcount` tinyint NOT NULL,
  `sensekey` tinyint NOT NULL,
  `position` tinyint NOT NULL,
  `lemma` tinyint NOT NULL,
  `pos` tinyint NOT NULL,
  `lexdomainid` tinyint NOT NULL,
  `definition` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `adjpositions`
--

DROP TABLE IF EXISTS `adjpositions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adjpositions` (
  `synsetid` int(10) unsigned NOT NULL DEFAULT '0',
  `wordid` int(10) unsigned NOT NULL DEFAULT '0',
  `position` enum('a','p','ip') NOT NULL,
  PRIMARY KEY (`synsetid`,`wordid`),
  KEY `k_adjpositions_synsetid` (`synsetid`),
  KEY `k_adjpositions_wordid` (`wordid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `adjpositiontypes`
--

DROP TABLE IF EXISTS `adjpositiontypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adjpositiontypes` (
  `position` enum('a','p','ip') NOT NULL,
  `positionname` varchar(24) NOT NULL,
  PRIMARY KEY (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `casedwords`
--

DROP TABLE IF EXISTS `casedwords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `casedwords` (
  `casedwordid` int(10) unsigned NOT NULL DEFAULT '0',
  `wordid` int(10) unsigned NOT NULL DEFAULT '0',
  `cased` varchar(80) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`casedwordid`),
  UNIQUE KEY `unq_casedwords_cased` (`cased`),
  KEY `k_casedwords_wordid` (`wordid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `dict`
--

DROP TABLE IF EXISTS `dict`;
/*!50001 DROP VIEW IF EXISTS `dict`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `dict` (
  `synsetid` tinyint NOT NULL,
  `wordid` tinyint NOT NULL,
  `casedwordid` tinyint NOT NULL,
  `lemma` tinyint NOT NULL,
  `senseid` tinyint NOT NULL,
  `sensenum` tinyint NOT NULL,
  `lexid` tinyint NOT NULL,
  `tagcount` tinyint NOT NULL,
  `sensekey` tinyint NOT NULL,
  `cased` tinyint NOT NULL,
  `pos` tinyint NOT NULL,
  `lexdomainid` tinyint NOT NULL,
  `definition` tinyint NOT NULL,
  `sampleset` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lexdomains`
--

DROP TABLE IF EXISTS `lexdomains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lexdomains` (
  `lexdomainid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `lexdomainname` varchar(32) DEFAULT NULL,
  `lexdomain` varchar(32) DEFAULT NULL,
  `pos` enum('n','v','a','r','s') DEFAULT NULL,
  PRIMARY KEY (`lexdomainid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lexlinks`
--

DROP TABLE IF EXISTS `lexlinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lexlinks` (
  `synset1id` int(10) unsigned NOT NULL DEFAULT '0',
  `word1id` int(10) unsigned NOT NULL DEFAULT '0',
  `synset2id` int(10) unsigned NOT NULL DEFAULT '0',
  `word2id` int(10) unsigned NOT NULL DEFAULT '0',
  `linkid` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`word1id`,`synset1id`,`word2id`,`synset2id`,`linkid`),
  KEY `k_lexlinks_linkid` (`linkid`),
  KEY `k_lexlinks_synset1id` (`synset1id`),
  KEY `k_lexlinks_synset1id_word1id` (`synset1id`,`word1id`),
  KEY `k_lexlinks_synset2id` (`synset2id`),
  KEY `k_lexlinks_synset2id_word2id` (`synset2id`,`word2id`),
  KEY `k_lexlinks_word1id` (`word1id`),
  KEY `k_lexlinks_word2id` (`word2id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `linktypes`
--

DROP TABLE IF EXISTS `linktypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `linktypes` (
  `linkid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `link` varchar(50) DEFAULT NULL,
  `recurses` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`linkid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `morphmaps`
--

DROP TABLE IF EXISTS `morphmaps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `morphmaps` (
  `wordid` int(10) unsigned NOT NULL DEFAULT '0',
  `pos` enum('n','v','a','r','s') NOT NULL DEFAULT 'n',
  `morphid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`morphid`,`pos`,`wordid`),
  KEY `k_morphmaps_morphid` (`morphid`),
  KEY `k_morphmaps_wordid` (`wordid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `morphology`
--

DROP TABLE IF EXISTS `morphology`;
/*!50001 DROP VIEW IF EXISTS `morphology`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `morphology` (
  `morphid` tinyint NOT NULL,
  `wordid` tinyint NOT NULL,
  `lemma` tinyint NOT NULL,
  `pos` tinyint NOT NULL,
  `morph` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `morphs`
--

DROP TABLE IF EXISTS `morphs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `morphs` (
  `morphid` int(10) unsigned NOT NULL DEFAULT '0',
  `morph` varchar(70) NOT NULL,
  PRIMARY KEY (`morphid`),
  UNIQUE KEY `unq_morphs_morph` (`morph`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `postypes`
--

DROP TABLE IF EXISTS `postypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `postypes` (
  `pos` enum('n','v','a','r','s') NOT NULL,
  `posname` varchar(20) NOT NULL,
  PRIMARY KEY (`pos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `samples`
--

DROP TABLE IF EXISTS `samples`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `samples` (
  `synsetid` int(10) unsigned NOT NULL DEFAULT '0',
  `sampleid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sample` mediumtext NOT NULL,
  PRIMARY KEY (`synsetid`,`sampleid`),
  KEY `k_samples_synsetid` (`synsetid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `samplesets`
--

DROP TABLE IF EXISTS `samplesets`;
/*!50001 DROP VIEW IF EXISTS `samplesets`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `samplesets` (
  `synsetid` tinyint NOT NULL,
  `sampleset` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `semlinks`
--

DROP TABLE IF EXISTS `semlinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `semlinks` (
  `synset1id` int(10) unsigned NOT NULL DEFAULT '0',
  `synset2id` int(10) unsigned NOT NULL DEFAULT '0',
  `linkid` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`synset1id`,`synset2id`,`linkid`),
  KEY `k_semlinks_linkid` (`linkid`),
  KEY `k_semlinks_synset1id` (`synset1id`),
  KEY `k_semlinks_synset2id` (`synset2id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `senses`
--

DROP TABLE IF EXISTS `senses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `senses` (
  `wordid` int(10) unsigned NOT NULL DEFAULT '0',
  `casedwordid` int(10) unsigned DEFAULT NULL,
  `synsetid` int(10) unsigned NOT NULL DEFAULT '0',
  `senseid` int(10) unsigned DEFAULT NULL,
  `sensenum` smallint(5) unsigned NOT NULL DEFAULT '0',
  `lexid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `tagcount` int(10) unsigned DEFAULT NULL,
  `sensekey` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`wordid`,`synsetid`),
  UNIQUE KEY `unq_senses_senseid` (`senseid`),
  UNIQUE KEY `unq_senses_sensekey` (`sensekey`),
  KEY `k_senses_lexid` (`lexid`),
  KEY `k_senses_synsetid` (`synsetid`),
  KEY `k_senses_wordid` (`wordid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `sensesXlexlinksXsenses`
--

DROP TABLE IF EXISTS `sensesXlexlinksXsenses`;
/*!50001 DROP VIEW IF EXISTS `sensesXlexlinksXsenses`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `sensesXlexlinksXsenses` (
  `linkid` tinyint NOT NULL,
  `ssynsetid` tinyint NOT NULL,
  `swordid` tinyint NOT NULL,
  `ssenseid` tinyint NOT NULL,
  `scasedwordid` tinyint NOT NULL,
  `ssensenum` tinyint NOT NULL,
  `slexid` tinyint NOT NULL,
  `stagcount` tinyint NOT NULL,
  `ssensekey` tinyint NOT NULL,
  `spos` tinyint NOT NULL,
  `slexdomainid` tinyint NOT NULL,
  `sdefinition` tinyint NOT NULL,
  `dsynsetid` tinyint NOT NULL,
  `dwordid` tinyint NOT NULL,
  `dsenseid` tinyint NOT NULL,
  `dcasedwordid` tinyint NOT NULL,
  `dsensenum` tinyint NOT NULL,
  `dlexid` tinyint NOT NULL,
  `dtagcount` tinyint NOT NULL,
  `dsensekey` tinyint NOT NULL,
  `dpos` tinyint NOT NULL,
  `dlexdomainid` tinyint NOT NULL,
  `ddefinition` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `sensesXsemlinksXsenses`
--

DROP TABLE IF EXISTS `sensesXsemlinksXsenses`;
/*!50001 DROP VIEW IF EXISTS `sensesXsemlinksXsenses`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `sensesXsemlinksXsenses` (
  `linkid` tinyint NOT NULL,
  `ssynsetid` tinyint NOT NULL,
  `swordid` tinyint NOT NULL,
  `ssenseid` tinyint NOT NULL,
  `scasedwordid` tinyint NOT NULL,
  `ssensenum` tinyint NOT NULL,
  `slexid` tinyint NOT NULL,
  `stagcount` tinyint NOT NULL,
  `ssensekey` tinyint NOT NULL,
  `spos` tinyint NOT NULL,
  `slexdomainid` tinyint NOT NULL,
  `sdefinition` tinyint NOT NULL,
  `dsynsetid` tinyint NOT NULL,
  `dwordid` tinyint NOT NULL,
  `dsenseid` tinyint NOT NULL,
  `dcasedwordid` tinyint NOT NULL,
  `dsensenum` tinyint NOT NULL,
  `dlexid` tinyint NOT NULL,
  `dtagcount` tinyint NOT NULL,
  `dsensekey` tinyint NOT NULL,
  `dpos` tinyint NOT NULL,
  `dlexdomainid` tinyint NOT NULL,
  `ddefinition` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `sensesXsynsets`
--

DROP TABLE IF EXISTS `sensesXsynsets`;
/*!50001 DROP VIEW IF EXISTS `sensesXsynsets`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `sensesXsynsets` (
  `synsetid` tinyint NOT NULL,
  `wordid` tinyint NOT NULL,
  `casedwordid` tinyint NOT NULL,
  `senseid` tinyint NOT NULL,
  `sensenum` tinyint NOT NULL,
  `lexid` tinyint NOT NULL,
  `tagcount` tinyint NOT NULL,
  `sensekey` tinyint NOT NULL,
  `pos` tinyint NOT NULL,
  `lexdomainid` tinyint NOT NULL,
  `definition` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `synsets`
--

DROP TABLE IF EXISTS `synsets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `synsets` (
  `synsetid` int(10) unsigned NOT NULL DEFAULT '0',
  `pos` enum('n','v','a','r','s') NOT NULL,
  `lexdomainid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `definition` mediumtext,
  PRIMARY KEY (`synsetid`),
  KEY `k_synsets_lexdomainid` (`lexdomainid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `synsetsXsemlinksXsynsets`
--

DROP TABLE IF EXISTS `synsetsXsemlinksXsynsets`;
/*!50001 DROP VIEW IF EXISTS `synsetsXsemlinksXsynsets`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `synsetsXsemlinksXsynsets` (
  `linkid` tinyint NOT NULL,
  `ssynsetid` tinyint NOT NULL,
  `sdefinition` tinyint NOT NULL,
  `dsynsetid` tinyint NOT NULL,
  `ddefinition` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `verbswithframes`
--

DROP TABLE IF EXISTS `verbswithframes`;
/*!50001 DROP VIEW IF EXISTS `verbswithframes`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `verbswithframes` (
  `synsetid` tinyint NOT NULL,
  `wordid` tinyint NOT NULL,
  `frameid` tinyint NOT NULL,
  `casedwordid` tinyint NOT NULL,
  `senseid` tinyint NOT NULL,
  `sensenum` tinyint NOT NULL,
  `lexid` tinyint NOT NULL,
  `tagcount` tinyint NOT NULL,
  `sensekey` tinyint NOT NULL,
  `frame` tinyint NOT NULL,
  `lemma` tinyint NOT NULL,
  `pos` tinyint NOT NULL,
  `lexdomainid` tinyint NOT NULL,
  `definition` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `vframemaps`
--

DROP TABLE IF EXISTS `vframemaps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vframemaps` (
  `synsetid` int(10) unsigned NOT NULL DEFAULT '0',
  `wordid` int(10) unsigned NOT NULL DEFAULT '0',
  `frameid` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`synsetid`,`wordid`,`frameid`),
  KEY `k_vframemaps_frameid` (`frameid`),
  KEY `k_vframemaps_synsetid` (`synsetid`),
  KEY `k_vframemaps_wordid` (`wordid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vframes`
--

DROP TABLE IF EXISTS `vframes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vframes` (
  `frameid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `frame` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`frameid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vframesentencemaps`
--

DROP TABLE IF EXISTS `vframesentencemaps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vframesentencemaps` (
  `synsetid` int(10) unsigned NOT NULL DEFAULT '0',
  `wordid` int(10) unsigned NOT NULL DEFAULT '0',
  `sentenceid` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`synsetid`,`wordid`,`sentenceid`),
  KEY `k_vframesentencemaps_sentenceid` (`sentenceid`),
  KEY `k_vframesentencemaps_synsetid` (`synsetid`),
  KEY `k_vframesentencemaps_wordid` (`wordid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vframesentences`
--

DROP TABLE IF EXISTS `vframesentences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vframesentences` (
  `sentenceid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sentence` mediumtext,
  PRIMARY KEY (`sentenceid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `words`
--

DROP TABLE IF EXISTS `words`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `words` (
  `wordid` int(10) unsigned NOT NULL DEFAULT '0',
  `lemma` varchar(80) NOT NULL,
  PRIMARY KEY (`wordid`),
  UNIQUE KEY `unq_words_lemma` (`lemma`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `wordsXsenses`
--

DROP TABLE IF EXISTS `wordsXsenses`;
/*!50001 DROP VIEW IF EXISTS `wordsXsenses`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `wordsXsenses` (
  `wordid` tinyint NOT NULL,
  `lemma` tinyint NOT NULL,
  `casedwordid` tinyint NOT NULL,
  `synsetid` tinyint NOT NULL,
  `senseid` tinyint NOT NULL,
  `sensenum` tinyint NOT NULL,
  `lexid` tinyint NOT NULL,
  `tagcount` tinyint NOT NULL,
  `sensekey` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `wordsXsensesXsynsets`
--

DROP TABLE IF EXISTS `wordsXsensesXsynsets`;
/*!50001 DROP VIEW IF EXISTS `wordsXsensesXsynsets`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `wordsXsensesXsynsets` (
  `synsetid` tinyint NOT NULL,
  `wordid` tinyint NOT NULL,
  `lemma` tinyint NOT NULL,
  `casedwordid` tinyint NOT NULL,
  `senseid` tinyint NOT NULL,
  `sensenum` tinyint NOT NULL,
  `lexid` tinyint NOT NULL,
  `tagcount` tinyint NOT NULL,
  `sensekey` tinyint NOT NULL,
  `pos` tinyint NOT NULL,
  `lexdomainid` tinyint NOT NULL,
  `definition` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `adjectiveswithpositions`
--

/*!50001 DROP TABLE IF EXISTS `adjectiveswithpositions`*/;
/*!50001 DROP VIEW IF EXISTS `adjectiveswithpositions`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `adjectiveswithpositions` AS select `senses`.`synsetid` AS `synsetid`,`senses`.`wordid` AS `wordid`,`senses`.`casedwordid` AS `casedwordid`,`senses`.`senseid` AS `senseid`,`senses`.`sensenum` AS `sensenum`,`senses`.`lexid` AS `lexid`,`senses`.`tagcount` AS `tagcount`,`senses`.`sensekey` AS `sensekey`,`adjpositions`.`position` AS `position`,`words`.`lemma` AS `lemma`,`synsets`.`pos` AS `pos`,`synsets`.`lexdomainid` AS `lexdomainid`,`synsets`.`definition` AS `definition` from (((`senses` join `adjpositions` on(((`senses`.`wordid` = `adjpositions`.`wordid`) and (`senses`.`synsetid` = `adjpositions`.`synsetid`)))) left join `words` on((`senses`.`wordid` = `words`.`wordid`))) left join `synsets` on((`senses`.`synsetid` = `synsets`.`synsetid`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `dict`
--

/*!50001 DROP TABLE IF EXISTS `dict`*/;
/*!50001 DROP VIEW IF EXISTS `dict`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `dict` AS select `s`.`synsetid` AS `synsetid`,`words`.`wordid` AS `wordid`,`s`.`casedwordid` AS `casedwordid`,`words`.`lemma` AS `lemma`,`s`.`senseid` AS `senseid`,`s`.`sensenum` AS `sensenum`,`s`.`lexid` AS `lexid`,`s`.`tagcount` AS `tagcount`,`s`.`sensekey` AS `sensekey`,`casedwords`.`cased` AS `cased`,`synsets`.`pos` AS `pos`,`synsets`.`lexdomainid` AS `lexdomainid`,`synsets`.`definition` AS `definition`,`samplesets`.`sampleset` AS `sampleset` from ((((`words` left join `senses` `s` on((`words`.`wordid` = `s`.`wordid`))) left join `casedwords` on(((`words`.`wordid` = `casedwords`.`wordid`) and (`s`.`casedwordid` = `casedwords`.`casedwordid`)))) left join `synsets` on((`s`.`synsetid` = `synsets`.`synsetid`))) left join `samplesets` on((`s`.`synsetid` = `samplesets`.`synsetid`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `morphology`
--

/*!50001 DROP TABLE IF EXISTS `morphology`*/;
/*!50001 DROP VIEW IF EXISTS `morphology`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `morphology` AS select `morphmaps`.`morphid` AS `morphid`,`words`.`wordid` AS `wordid`,`words`.`lemma` AS `lemma`,`morphmaps`.`pos` AS `pos`,`morphs`.`morph` AS `morph` from ((`words` join `morphmaps` on((`words`.`wordid` = `morphmaps`.`wordid`))) join `morphs` on((`morphmaps`.`morphid` = `morphs`.`morphid`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `samplesets`
--

/*!50001 DROP TABLE IF EXISTS `samplesets`*/;
/*!50001 DROP VIEW IF EXISTS `samplesets`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `samplesets` AS select `samples`.`synsetid` AS `synsetid`,group_concat(distinct `samples`.`sample` order by `samples`.`sampleid` ASC separator '|') AS `sampleset` from `samples` group by `samples`.`synsetid` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `sensesXlexlinksXsenses`
--

/*!50001 DROP TABLE IF EXISTS `sensesXlexlinksXsenses`*/;
/*!50001 DROP VIEW IF EXISTS `sensesXlexlinksXsenses`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `sensesXlexlinksXsenses` AS select `l`.`linkid` AS `linkid`,`s`.`synsetid` AS `ssynsetid`,`s`.`wordid` AS `swordid`,`s`.`senseid` AS `ssenseid`,`s`.`casedwordid` AS `scasedwordid`,`s`.`sensenum` AS `ssensenum`,`s`.`lexid` AS `slexid`,`s`.`tagcount` AS `stagcount`,`s`.`sensekey` AS `ssensekey`,`s`.`pos` AS `spos`,`s`.`lexdomainid` AS `slexdomainid`,`s`.`definition` AS `sdefinition`,`d`.`synsetid` AS `dsynsetid`,`d`.`wordid` AS `dwordid`,`d`.`senseid` AS `dsenseid`,`d`.`casedwordid` AS `dcasedwordid`,`d`.`sensenum` AS `dsensenum`,`d`.`lexid` AS `dlexid`,`d`.`tagcount` AS `dtagcount`,`d`.`sensekey` AS `dsensekey`,`d`.`pos` AS `dpos`,`d`.`lexdomainid` AS `dlexdomainid`,`d`.`definition` AS `ddefinition` from ((`sensesXsynsets` `s` join `lexlinks` `l` on(((`s`.`synsetid` = `l`.`synset1id`) and (`s`.`wordid` = `l`.`word1id`)))) join `sensesXsynsets` `d` on(((`l`.`synset2id` = `d`.`synsetid`) and (`l`.`word2id` = `d`.`wordid`)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `sensesXsemlinksXsenses`
--

/*!50001 DROP TABLE IF EXISTS `sensesXsemlinksXsenses`*/;
/*!50001 DROP VIEW IF EXISTS `sensesXsemlinksXsenses`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `sensesXsemlinksXsenses` AS select `l`.`linkid` AS `linkid`,`s`.`synsetid` AS `ssynsetid`,`s`.`wordid` AS `swordid`,`s`.`senseid` AS `ssenseid`,`s`.`casedwordid` AS `scasedwordid`,`s`.`sensenum` AS `ssensenum`,`s`.`lexid` AS `slexid`,`s`.`tagcount` AS `stagcount`,`s`.`sensekey` AS `ssensekey`,`s`.`pos` AS `spos`,`s`.`lexdomainid` AS `slexdomainid`,`s`.`definition` AS `sdefinition`,`d`.`synsetid` AS `dsynsetid`,`d`.`wordid` AS `dwordid`,`d`.`senseid` AS `dsenseid`,`d`.`casedwordid` AS `dcasedwordid`,`d`.`sensenum` AS `dsensenum`,`d`.`lexid` AS `dlexid`,`d`.`tagcount` AS `dtagcount`,`d`.`sensekey` AS `dsensekey`,`d`.`pos` AS `dpos`,`d`.`lexdomainid` AS `dlexdomainid`,`d`.`definition` AS `ddefinition` from ((`sensesXsynsets` `s` join `semlinks` `l` on((`s`.`synsetid` = `l`.`synset1id`))) join `sensesXsynsets` `d` on((`l`.`synset2id` = `d`.`synsetid`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `sensesXsynsets`
--

/*!50001 DROP TABLE IF EXISTS `sensesXsynsets`*/;
/*!50001 DROP VIEW IF EXISTS `sensesXsynsets`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `sensesXsynsets` AS select `senses`.`synsetid` AS `synsetid`,`senses`.`wordid` AS `wordid`,`senses`.`casedwordid` AS `casedwordid`,`senses`.`senseid` AS `senseid`,`senses`.`sensenum` AS `sensenum`,`senses`.`lexid` AS `lexid`,`senses`.`tagcount` AS `tagcount`,`senses`.`sensekey` AS `sensekey`,`synsets`.`pos` AS `pos`,`synsets`.`lexdomainid` AS `lexdomainid`,`synsets`.`definition` AS `definition` from (`senses` join `synsets` on((`senses`.`synsetid` = `synsets`.`synsetid`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `synsetsXsemlinksXsynsets`
--

/*!50001 DROP TABLE IF EXISTS `synsetsXsemlinksXsynsets`*/;
/*!50001 DROP VIEW IF EXISTS `synsetsXsemlinksXsynsets`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `synsetsXsemlinksXsynsets` AS select `l`.`linkid` AS `linkid`,`s`.`synsetid` AS `ssynsetid`,`s`.`definition` AS `sdefinition`,`d`.`synsetid` AS `dsynsetid`,`d`.`definition` AS `ddefinition` from ((`synsets` `s` join `semlinks` `l` on((`s`.`synsetid` = `l`.`synset1id`))) join `synsets` `d` on((`l`.`synset2id` = `d`.`synsetid`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `verbswithframes`
--

/*!50001 DROP TABLE IF EXISTS `verbswithframes`*/;
/*!50001 DROP VIEW IF EXISTS `verbswithframes`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `verbswithframes` AS select `senses`.`synsetid` AS `synsetid`,`senses`.`wordid` AS `wordid`,`vframemaps`.`frameid` AS `frameid`,`senses`.`casedwordid` AS `casedwordid`,`senses`.`senseid` AS `senseid`,`senses`.`sensenum` AS `sensenum`,`senses`.`lexid` AS `lexid`,`senses`.`tagcount` AS `tagcount`,`senses`.`sensekey` AS `sensekey`,`vframes`.`frame` AS `frame`,`words`.`lemma` AS `lemma`,`synsets`.`pos` AS `pos`,`synsets`.`lexdomainid` AS `lexdomainid`,`synsets`.`definition` AS `definition` from ((((`senses` join `vframemaps` on(((`senses`.`wordid` = `vframemaps`.`wordid`) and (`senses`.`synsetid` = `vframemaps`.`synsetid`)))) join `vframes` on((`vframemaps`.`frameid` = `vframes`.`frameid`))) left join `words` on((`senses`.`wordid` = `words`.`wordid`))) left join `synsets` on((`senses`.`synsetid` = `synsets`.`synsetid`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `wordsXsenses`
--

/*!50001 DROP TABLE IF EXISTS `wordsXsenses`*/;
/*!50001 DROP VIEW IF EXISTS `wordsXsenses`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `wordsXsenses` AS select `words`.`wordid` AS `wordid`,`words`.`lemma` AS `lemma`,`senses`.`casedwordid` AS `casedwordid`,`senses`.`synsetid` AS `synsetid`,`senses`.`senseid` AS `senseid`,`senses`.`sensenum` AS `sensenum`,`senses`.`lexid` AS `lexid`,`senses`.`tagcount` AS `tagcount`,`senses`.`sensekey` AS `sensekey` from (`words` join `senses` on((`words`.`wordid` = `senses`.`wordid`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `wordsXsensesXsynsets`
--

/*!50001 DROP TABLE IF EXISTS `wordsXsensesXsynsets`*/;
/*!50001 DROP VIEW IF EXISTS `wordsXsensesXsynsets`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `wordsXsensesXsynsets` AS select `senses`.`synsetid` AS `synsetid`,`words`.`wordid` AS `wordid`,`words`.`lemma` AS `lemma`,`senses`.`casedwordid` AS `casedwordid`,`senses`.`senseid` AS `senseid`,`senses`.`sensenum` AS `sensenum`,`senses`.`lexid` AS `lexid`,`senses`.`tagcount` AS `tagcount`,`senses`.`sensekey` AS `sensekey`,`synsets`.`pos` AS `pos`,`synsets`.`lexdomainid` AS `lexdomainid`,`synsets`.`definition` AS `definition` from ((`words` join `senses` on((`words`.`wordid` = `senses`.`wordid`))) join `synsets` on((`senses`.`synsetid` = `synsets`.`synsetid`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-09-02 16:34:36

use ferdowsnet;

-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: 172.20.8.186    Database: ferdowsnet
-- ------------------------------------------------------
-- Server version	5.5.46-0+deb8u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `englishwords`
--

DROP TABLE IF EXISTS `englishwords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `englishwords` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(2000) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `word` (`word`(255))
) ENGINE=InnoDB AUTO_INCREMENT=145193 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `persianwords`
--

DROP TABLE IF EXISTS `persianwords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `persianwords` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(2000) COLLATE utf8_persian_ci NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `word` (`word`(255))
) ENGINE=InnoDB AUTO_INCREMENT=256016 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `relationsynset`
--

DROP TABLE IF EXISTS `relationsynset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relationsynset` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `synsetID1` int(11) NOT NULL,
  `synsetID2` int(11) NOT NULL,
  `relationType` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `all_col` (`synsetID1`,`synsetID2`,`relationType`) USING HASH,
  KEY `FK_Synset_2` (`synsetID2`),
  CONSTRAINT `FK_Synset_1` FOREIGN KEY (`synsetID1`) REFERENCES `synset` (`ID`),
  CONSTRAINT `FK_Synset_2` FOREIGN KEY (`synsetID2`) REFERENCES `synset` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=166845 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sense`
--

DROP TABLE IF EXISTS `sense`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sense` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `enWordID` int(11) DEFAULT NULL,
  `synSetID` int(11) NOT NULL DEFAULT '0',
  `confidence` tinyint(4) NOT NULL DEFAULT '0',
  `peWordID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `enWordID` (`enWordID`) USING BTREE,
  KEY `sysetIF` (`synSetID`) USING BTREE,
  KEY `peWordID` (`peWordID`),
  KEY `all_col` (`peWordID`,`enWordID`,`synSetID`) USING HASH,
  CONSTRAINT `FK_enWord` FOREIGN KEY (`enWordID`) REFERENCES `englishwords` (`ID`),
  CONSTRAINT `FK_peWord` FOREIGN KEY (`peWordID`) REFERENCES `persianwords` (`ID`),
  CONSTRAINT `FK_synset` FOREIGN KEY (`synSetID`) REFERENCES `synset` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=176412 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `synset`
--

DROP TABLE IF EXISTS `synset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `synset` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `POS` varchar(100) NOT NULL,
  `Gloss` varchar(2000) NOT NULL DEFAULT '',
  `Example` varchar(2000) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=95117 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `v_get_en_pe_synset`
--

DROP TABLE IF EXISTS `v_get_en_pe_synset`;
/*!50001 DROP VIEW IF EXISTS `v_get_en_pe_synset`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `v_get_en_pe_synset` (
  `synsetid` tinyint NOT NULL,
  `peWords` tinyint NOT NULL,
  `peIDs` tinyint NOT NULL,
  `enWords` tinyint NOT NULL,
  `enIDs` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_get_synset`
--

DROP TABLE IF EXISTS `v_get_synset`;
/*!50001 DROP VIEW IF EXISTS `v_get_synset`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `v_get_synset` (
  `synsetid` tinyint NOT NULL,
  `pos` tinyint NOT NULL,
  `peWords` tinyint NOT NULL,
  `peIDs` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `v_get_en_pe_synset`
--

/*!50001 DROP TABLE IF EXISTS `v_get_en_pe_synset`*/;
/*!50001 DROP VIEW IF EXISTS `v_get_en_pe_synset`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_get_en_pe_synset` AS select `s`.`synSetID` AS `synsetid`,group_concat(`p`.`word` separator ',') AS `peWords`,group_concat(`p`.`ID` separator ',') AS `peIDs`,group_concat(`e`.`word` separator ',') AS `enWords`,group_concat(`e`.`ID` separator ',') AS `enIDs` from ((`sense` `s` join `persianwords` `p` on((`p`.`ID` = `s`.`peWordID`))) join `englishwords` `e` on((`e`.`ID` = `s`.`enWordID`))) group by `s`.`synSetID` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_get_synset`
--

/*!50001 DROP TABLE IF EXISTS `v_get_synset`*/;
/*!50001 DROP VIEW IF EXISTS `v_get_synset`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_get_synset` AS select `s`.`synSetID` AS `synsetid`,`syn`.`POS` AS `pos`,group_concat(`p`.`word` separator ',') AS `peWords`,group_concat(`p`.`ID` separator ',') AS `peIDs` from ((`sense` `s` join `persianwords` `p` on((`p`.`ID` = `s`.`peWordID`))) join `synset` `syn` on((`syn`.`ID` = `s`.`synSetID`))) group by `s`.`synSetID` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-09-02 16:34:21

use baseinfo;

-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: 172.20.8.186    Database: baseinfo
-- ------------------------------------------------------
-- Server version	5.5.46-0+deb8u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `domains`
--

DROP TABLE IF EXISTS `domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domains` (
  `DomainName` varchar(30) COLLATE utf8_persian_ci NOT NULL DEFAULT '',
  `description` varchar(150) COLLATE utf8_persian_ci DEFAULT NULL,
  `eDescription` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL,
  `DomainValue` int(11) unsigned NOT NULL DEFAULT '0',
  `unc_code` int(10) unsigned NOT NULL COMMENT 'کد موسسه',
  `ActiveDomain` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL COMMENT 'آیا دامنه فعال است ؟',
  PRIMARY KEY (`DomainName`,`DomainValue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-09-02 16:34:02

use projectmanagement;

-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: 172.20.8.186    Database: projectmanagement
-- ------------------------------------------------------
-- Server version	5.5.46-0+deb8u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `AccountSpecs`
--

DROP TABLE IF EXISTS `AccountSpecs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AccountSpecs` (
  `AccountSpecID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL,
  `UserPassword` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL,
  `PersonID` int(11) DEFAULT NULL,
  `WebUserID` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL,
  PRIMARY KEY (`AccountSpecID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `EMonArray`
--

DROP TABLE IF EXISTS `EMonArray`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EMonArray` (
  `_id` int(11) NOT NULL,
  `emon` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FMonArray`
--

DROP TABLE IF EXISTS `FMonArray`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FMonArray` (
  `_id` int(11) NOT NULL,
  `fmon` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FacilityPages`
--

DROP TABLE IF EXISTS `FacilityPages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FacilityPages` (
  `FacilityPageID` int(11) NOT NULL AUTO_INCREMENT,
  `FacilityID` int(11) DEFAULT NULL,
  `PageName` varchar(145) COLLATE utf8_persian_ci DEFAULT NULL,
  PRIMARY KEY (`FacilityPageID`)
) ENGINE=InnoDB AUTO_INCREMENT=233 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyClassAnalysis`
--

DROP TABLE IF EXISTS `OntologyClassAnalysis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyClassAnalysis` (
  `OntologyAnalysisID` int(11) NOT NULL AUTO_INCREMENT,
  `OntologyClassID` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL,
  `NumberOfChilds` int(11) DEFAULT NULL COMMENT 'تعداد اعضای زیر مجموعه در روابط سلسله مراتبی',
  `NumberOfRelations` int(11) DEFAULT NULL COMMENT 'تعداد روابط غیر سلسله مراتبی',
  `NumberOfProperties` int(11) DEFAULT NULL COMMENT 'تعداد خصوصیات',
  `OntologyID` int(11) DEFAULT NULL,
  `NumberOfIndirectProperties` int(11) DEFAULT NULL COMMENT 'تعداد خصوصیات با واسطه',
  `NumberOfIndirectRelations` int(11) DEFAULT NULL,
  PRIMARY KEY (`OntologyAnalysisID`)
) ENGINE=InnoDB AUTO_INCREMENT=6426 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='تحلیل آماری کلاسهای هستان نگار';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyClassHirarchy`
--

DROP TABLE IF EXISTS `OntologyClassHirarchy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyClassHirarchy` (
  `OntologyClassHirarchyID` int(11) NOT NULL AUTO_INCREMENT,
  `OntologyClassID` int(11) DEFAULT NULL COMMENT 'کلاس فرزند',
  `OntologyClassParentID` int(11) DEFAULT NULL COMMENT 'کلاس پدر',
  PRIMARY KEY (`OntologyClassHirarchyID`),
  KEY `idx1` (`OntologyClassID`),
  KEY `idx2` (`OntologyClassParentID`)
) ENGINE=InnoDB AUTO_INCREMENT=344698 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='سلسله مراتب کلاسهای هستان نگار';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyClassHirarchyValidation`
--

DROP TABLE IF EXISTS `OntologyClassHirarchyValidation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyClassHirarchyValidation` (
  `OntologyClassHirarchyValidationID` int(11) NOT NULL AUTO_INCREMENT,
  `OntologyID` int(11) DEFAULT NULL COMMENT 'هستان نگار',
  `ParentOntologyClassID` int(11) DEFAULT NULL COMMENT 'کلاس پدر',
  `OntologyClassID` int(11) DEFAULT NULL COMMENT 'کلاس فرزند',
  `ExpertOpinion` enum('NONE','ACCEPT','REJECT','UNKNOWN') COLLATE utf8_persian_ci DEFAULT 'NONE' COMMENT 'نظر خبره',
  `ExpertDescription` varchar(500) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'یادداشت خبره',
  `OntologyValidationExpertID` int(11) DEFAULT NULL COMMENT 'خبره',
  PRIMARY KEY (`OntologyClassHirarchyValidationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='ارزیابی رابطه بین کلاسهای هستان نگار';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyClassLabels`
--

DROP TABLE IF EXISTS `OntologyClassLabels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyClassLabels` (
  `OntologyClassLabelID` int(11) NOT NULL AUTO_INCREMENT,
  `OntologyClassID` int(11) DEFAULT NULL,
  `label` varchar(1000) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'برچسب',
  PRIMARY KEY (`OntologyClassLabelID`),
  KEY `idx1` (`OntologyClassID`),
  KEY `idx2` (`label`(255))
) ENGINE=InnoDB AUTO_INCREMENT=37529 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='برچسب کلاسها';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyClassMapping`
--

DROP TABLE IF EXISTS `OntologyClassMapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyClassMapping` (
  `OntologyClassMappingID` int(11) NOT NULL AUTO_INCREMENT,
  `OntologyID` int(11) DEFAULT NULL,
  `OntologyClassID` int(11) DEFAULT NULL,
  `MappedOntologyID` int(11) DEFAULT NULL,
  `MappedOntologyEntityID` int(11) DEFAULT NULL,
  `MappedOntologyEntityType` enum('CLASS','PROP','DATA_PROP') COLLATE utf8_persian_ci DEFAULT NULL,
  PRIMARY KEY (`OntologyClassMappingID`)
) ENGINE=InnoDB AUTO_INCREMENT=32105 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyClassRelationValidation`
--

DROP TABLE IF EXISTS `OntologyClassRelationValidation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyClassRelationValidation` (
  `OntologyClassRelationValidationID` int(11) NOT NULL AUTO_INCREMENT,
  `OntologyID` int(11) DEFAULT NULL COMMENT 'هستان نگار',
  `OntologyPropertyID` int(11) DEFAULT NULL COMMENT 'خصوصیت',
  `DomainOntologyClassID` int(11) DEFAULT NULL COMMENT 'کلاس طرف اول رابطه',
  `RangeOntologyClassID` int(11) DEFAULT NULL COMMENT 'کلاس طرف دوم رابطه',
  `ExpertOpinion` enum('NONE','ACCEPT','REJECT','UNKNOWN') COLLATE utf8_persian_ci DEFAULT 'NONE' COMMENT 'نظر خبره',
  `ExpertDescription` varchar(500) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'یادداشت خبره',
  `OntologyValidationExpertID` int(11) DEFAULT NULL COMMENT 'خبره',
  PRIMARY KEY (`OntologyClassRelationValidationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='ارزیابی رابطه بین کلاسهای هستان نگار';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyClasses`
--

DROP TABLE IF EXISTS `OntologyClasses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyClasses` (
  `OntologyClassID` int(11) NOT NULL AUTO_INCREMENT,
  `OntologyID` int(11) DEFAULT NULL,
  `ClassTitle` varchar(245) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'عنوان کلاس',
  PRIMARY KEY (`OntologyClassID`),
  KEY `idx1` (`OntologyID`)
) ENGINE=InnoDB AUTO_INCREMENT=71957 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='کلاسهای هستان نگار';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyClassesValidation`
--

DROP TABLE IF EXISTS `OntologyClassesValidation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyClassesValidation` (
  `OntologyClassesValidationID` int(11) NOT NULL AUTO_INCREMENT,
  `OntologyID` int(11) DEFAULT NULL COMMENT 'هستان نگار',
  `OntologyClassID` int(11) DEFAULT NULL COMMENT 'کلاس',
  `ExpertOpinion` enum('NONE','ACCEPT','REJECT','UNKNOWN') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نظر خبره',
  `ExpertComment` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'یادداشت خبره',
  `OntologyValidationExpertID` int(11) DEFAULT NULL,
  `ExtraComment` varchar(3000) COLLATE utf8_persian_ci DEFAULT NULL,
  PRIMARY KEY (`OntologyClassesValidationID`),
  KEY `idx1` (`OntologyClassID`,`OntologyID`),
  KEY `idx2` (`OntologyValidationExpertID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='ارزیابی کلاس هستان نگار';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyMergeEntities`
--

DROP TABLE IF EXISTS `OntologyMergeEntities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyMergeEntities` (
  `OntologyMergeEntityID` int(11) NOT NULL AUTO_INCREMENT,
  `EntityType` enum('CLASS','PROPERTY','PROPERTY_VALUE') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نوع موجودیت',
  `EntityID` int(11) DEFAULT NULL COMMENT 'کد موجودیت',
  `OntologyMergeProjectID` int(11) DEFAULT NULL COMMENT 'پروژه ادغام',
  `ActionType` enum('IGNORE','ADD','NOT_DECIDE','MAP') COLLATE utf8_persian_ci DEFAULT NULL,
  `TargetEntityID` int(11) DEFAULT NULL,
  `TargetEntityType` enum('CLASS','PROPERTY','PROPERTY_VALUE') COLLATE utf8_persian_ci DEFAULT NULL,
  `EntityTitle` varchar(145) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'عنوان موجودیت',
  `EntityLabel` varchar(145) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'برچسب موجودیت',
  `IgnoreReason` enum('UNRELATED_DOMAIN','MODELING_ISSUE','OTHER') COLLATE utf8_persian_ci DEFAULT NULL,
  `IgnoreDescription` varchar(500) COLLATE utf8_persian_ci DEFAULT NULL,
  PRIMARY KEY (`OntologyMergeEntityID`),
  KEY `idx1` (`EntityID`),
  KEY `idx2` (`OntologyMergeProjectID`),
  KEY `idx3` (`ActionType`),
  KEY `idx4` (`TargetEntityID`),
  KEY `idx5` (`EntityTitle`),
  KEY `idx6` (`EntityLabel`)
) ENGINE=InnoDB AUTO_INCREMENT=149239 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='اجزای ادغام شده';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyMergeHirarchy`
--

DROP TABLE IF EXISTS `OntologyMergeHirarchy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyMergeHirarchy` (
  `OntologyMergeHirarchyID` int(11) NOT NULL AUTO_INCREMENT,
  `OntologyMergeProjectID` int(11) DEFAULT NULL COMMENT 'پروژه ادغام',
  `ParentClassID` int(11) DEFAULT NULL COMMENT 'کلاس پدر',
  `ChildClassID` int(11) DEFAULT NULL COMMENT 'کلاس فرزند',
  `ActionType` enum('NOT_DECIDE','REJECT','ADD','MAP') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نوع عمل',
  `TargetParentClassID` int(11) DEFAULT NULL COMMENT 'کد پدر در مقصد',
  `TargetChildClassID` int(11) DEFAULT NULL COMMENT 'کد فرزند در مقصد',
  `RelationName` varchar(45) COLLATE utf8_persian_ci DEFAULT '',
  `RelationLabel` varchar(45) COLLATE utf8_persian_ci DEFAULT '',
  PRIMARY KEY (`OntologyMergeHirarchyID`),
  KEY `idx1` (`OntologyMergeProjectID`),
  KEY `idx2` (`ParentClassID`),
  KEY `idx3` (`ChildClassID`),
  KEY `idx4` (`ActionType`),
  KEY `idx5` (`TargetParentClassID`),
  KEY `idx6` (`TargetChildClassID`)
) ENGINE=InnoDB AUTO_INCREMENT=44472 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='وضعیت سلسله مراتب در ادغام';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyMergeProject`
--

DROP TABLE IF EXISTS `OntologyMergeProject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyMergeProject` (
  `OntologyMergeProjectID` int(11) NOT NULL AUTO_INCREMENT,
  `TargetOntologyID` int(11) DEFAULT NULL,
  `MergeStatus` enum('UNDERPROCESS','FINISH') COLLATE utf8_persian_ci DEFAULT NULL,
  `MergeProjectTitle` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL,
  PRIMARY KEY (`OntologyMergeProjectID`),
  KEY `idx` (`TargetOntologyID`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='پروژه ادغام هستان نگار';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyMergeProjectMembers`
--

DROP TABLE IF EXISTS `OntologyMergeProjectMembers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyMergeProjectMembers` (
  `OntologyMergeProjectMemberID` int(11) NOT NULL AUTO_INCREMENT,
  `OntologyMergeProjectID` int(11) DEFAULT NULL COMMENT 'پروژه مربوطه',
  `OntologyID` int(11) DEFAULT NULL COMMENT 'هستان نگار عضو',
  PRIMARY KEY (`OntologyMergeProjectMemberID`),
  KEY `idx` (`OntologyMergeProjectID`),
  KEY `idx2` (`OntologyID`)
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='هستان نگارها جزو پروژه ادغام';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyMergeReviewedPotentials`
--

DROP TABLE IF EXISTS `OntologyMergeReviewedPotentials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyMergeReviewedPotentials` (
  `OntologyMergeReviewedPotentialID` int(11) NOT NULL AUTO_INCREMENT,
  `EntityID1` int(11) DEFAULT NULL COMMENT 'آیتم ۱',
  `EntityID2` int(11) DEFAULT NULL COMMENT 'آیتم ۲',
  `EntityType1` enum('CLASS','DATAPROP','OBJPROP') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نوع آیتم ۱',
  `EntityType2` enum('CLASS','DATAPROP','OBJPROP') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نوع آیتم ۲',
  `ActionType` enum('NOT_DECIDE','NOT_MERGE','MERGE') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نوع عمل',
  `ResultEntityID` int(11) DEFAULT NULL COMMENT 'آیتم نتیجه',
  `ResultEntityType` enum('CLASS','DATAPROP','OBJPROP') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نوع آیتم نتیجه',
  `SimilartyType` enum('SAME_TITLE','SAME_LABEL','SAME_PARENT','SAME_CHILD','SAME_PROP','SAME_DOMAIN_RANGE','OTHER') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'دلیل تشابه',
  `TargetOntologyID` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'هستان نگار مقصد',
  `ExtraInfo` varchar(200) COLLATE utf8_persian_ci DEFAULT NULL,
  `ExtraInfo2` varchar(145) COLLATE utf8_persian_ci DEFAULT NULL,
  PRIMARY KEY (`OntologyMergeReviewedPotentialID`),
  KEY `idx1` (`EntityID1`),
  KEY `idx2` (`EntityID2`),
  KEY `idx3` (`EntityType1`),
  KEY `idx4` (`EntityType2`),
  KEY `idx5` (`ResultEntityID`),
  KEY `idx6` (`ResultEntityType`),
  KEY `idx7` (`ActionType`)
) ENGINE=InnoDB AUTO_INCREMENT=453372 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyObjectPropertyRestriction`
--

DROP TABLE IF EXISTS `OntologyObjectPropertyRestriction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyObjectPropertyRestriction` (
  `OntologyObjectPropertyRestrictionID` int(11) NOT NULL AUTO_INCREMENT,
  `OntologyPropertyID` int(11) DEFAULT NULL,
  `DomainClassID` varchar(245) COLLATE utf8_persian_ci DEFAULT NULL,
  `RangeClassID` varchar(245) COLLATE utf8_persian_ci DEFAULT NULL,
  `RelationStatus` enum('VALID','INVALID') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'وضعیت رابطه',
  `DomainClassCardinality` enum('1','N') COLLATE utf8_persian_ci DEFAULT '1',
  `RangeClassCardinality` enum('1','N') COLLATE utf8_persian_ci DEFAULT '1',
  PRIMARY KEY (`OntologyObjectPropertyRestrictionID`),
  KEY `idx1` (`OntologyPropertyID`),
  KEY `idx2` (`DomainClassID`),
  KEY `idx3` (`RangeClassID`),
  KEY `idx4` (`RelationStatus`)
) ENGINE=InnoDB AUTO_INCREMENT=51786 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='محدودیت روی ارتباط کلاسها از طریق خصوصیت شیء';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyProperties`
--

DROP TABLE IF EXISTS `OntologyProperties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyProperties` (
  `OntologyPropertyID` int(11) NOT NULL AUTO_INCREMENT,
  `OntologyID` int(11) DEFAULT NULL,
  `PropertyTitle` varchar(500) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'عنوان',
  `PropertyType` enum('DATATYPE','OBJECT','ANNOTATION') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نوع',
  `IsFunctional` enum('YES','NO') COLLATE utf8_persian_ci DEFAULT 'NO',
  `domain` varchar(4000) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'حوزه',
  `range` varchar(4000) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'بازه',
  `inverseOf` varchar(500) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'معکوس',
  PRIMARY KEY (`OntologyPropertyID`),
  KEY `idx1` (`OntologyID`),
  KEY `idx2` (`domain`(255)),
  KEY `idx3` (`range`(255))
) ENGINE=InnoDB AUTO_INCREMENT=245649 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyPropertyLabels`
--

DROP TABLE IF EXISTS `OntologyPropertyLabels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyPropertyLabels` (
  `OntologyPropertyLabelID` int(11) NOT NULL AUTO_INCREMENT,
  `OntologyPropertyID` int(11) DEFAULT NULL,
  `label` varchar(1000) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'برچسب',
  PRIMARY KEY (`OntologyPropertyLabelID`),
  KEY `idx1` (`OntologyPropertyID`),
  KEY `ifx2` (`label`(255))
) ENGINE=InnoDB AUTO_INCREMENT=210094 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='برچسبهای خصوصیات';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyPropertyMapping`
--

DROP TABLE IF EXISTS `OntologyPropertyMapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyPropertyMapping` (
  `OntologyPropertyMappingID` int(11) NOT NULL AUTO_INCREMENT,
  `OntologyID` int(11) DEFAULT NULL,
  `OntologyPropertyID` int(11) DEFAULT NULL,
  `MappedOntologyID` int(11) DEFAULT NULL,
  `MappedOntologyEntityID` int(11) DEFAULT NULL,
  `MappedOntologyEntityType` enum('CLASS','PROP','DATA_PROP') COLLATE utf8_persian_ci DEFAULT NULL,
  PRIMARY KEY (`OntologyPropertyMappingID`)
) ENGINE=InnoDB AUTO_INCREMENT=38985 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyPropertyPermittedValues`
--

DROP TABLE IF EXISTS `OntologyPropertyPermittedValues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyPropertyPermittedValues` (
  `OntologyPropertyPermittedValueID` int(11) NOT NULL AUTO_INCREMENT,
  `OntologyPropertyID` int(11) DEFAULT NULL COMMENT 'خصوصیت',
  `PermittedValue` varchar(300) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'مقدار مجاز',
  PRIMARY KEY (`OntologyPropertyPermittedValueID`),
  KEY `idx1` (`OntologyPropertyID`)
) ENGINE=InnoDB AUTO_INCREMENT=128155 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='مقادیر مجاز خصوصیت';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyPropertyValidation`
--

DROP TABLE IF EXISTS `OntologyPropertyValidation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyPropertyValidation` (
  `OntologyPropertyValidationID` int(11) NOT NULL AUTO_INCREMENT,
  `OntologyID` int(11) DEFAULT NULL COMMENT 'هستان نگار',
  `OntologyPropertyID` int(11) DEFAULT NULL COMMENT 'خصوصیت',
  `RelatedOntologyClassID` int(11) DEFAULT NULL COMMENT 'کلاس مرتبط',
  `ExpertOpinion` enum('NONE','ACCEPT','REJECT','UNKNOWN') COLLATE utf8_persian_ci DEFAULT 'NONE' COMMENT 'نظر خبره',
  `ExpertDescription` varchar(500) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'یادداشت خبره',
  `OntologyValidationExpertID` int(11) DEFAULT NULL COMMENT 'خبره',
  PRIMARY KEY (`OntologyPropertyValidationID`),
  KEY `idx1` (`OntologyID`),
  KEY `idx2` (`OntologyPropertyID`),
  KEY `idx3` (`RelatedOntologyClassID`),
  KEY `idx4` (`OntologyValidationExpertID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='ارزیابی خصوصیت هستان نگار';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologySubGraph`
--

DROP TABLE IF EXISTS `OntologySubGraph`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologySubGraph` (
  `OntologySubGraphID` int(11) NOT NULL AUTO_INCREMENT,
  `OntologyID` int(11) DEFAULT NULL,
  `SubGraphClassHeader` int(11) DEFAULT NULL COMMENT 'کد کلاس نماینده',
  `SubGraphClassMember` int(11) DEFAULT NULL COMMENT 'کد کلاس عضو زیر گراف',
  PRIMARY KEY (`OntologySubGraphID`)
) ENGINE=InnoDB AUTO_INCREMENT=8181 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='زیر گرافهای مربوط به هستان نگار';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OntologyValidationExperts`
--

DROP TABLE IF EXISTS `OntologyValidationExperts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OntologyValidationExperts` (
  `OntologyValidationExpertID` int(11) NOT NULL AUTO_INCREMENT,
  `ExpertFullName` varchar(145) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نام و نام خانوادگی',
  `ExpertDesciption` varchar(500) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'شرح پست/شغل/تخصص',
  `ExpertEnterCode` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'کد ورود خبره به سایت',
  `ValidationStatus` enum('NOT_START','IN_PROGRESS','DONE') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'وضعیت ارزیابی',
  `OntologyID` int(11) DEFAULT NULL COMMENT 'هستان نگار',
  PRIMARY KEY (`OntologyValidationExpertID`),
  KEY `idx1` (`OntologyID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='خبرگان بررسی کننده هستان نگار';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PersonAgreements`
--

DROP TABLE IF EXISTS `PersonAgreements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonAgreements` (
  `PersonAgreementID` int(11) NOT NULL AUTO_INCREMENT,
  `PersonID` int(11) DEFAULT NULL,
  `FromDate` datetime DEFAULT NULL COMMENT 'از تاریخ',
  `ToDate` datetime DEFAULT NULL COMMENT 'تا تاریخ',
  `AgreementDescription` varchar(2000) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'شرح قرارداد',
  `HourlyPrice` int(11) DEFAULT NULL COMMENT 'مبلغ ساعتی',
  PRIMARY KEY (`PersonAgreementID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='قرارداد پرسنل';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PersonPermissionsOnFields`
--

DROP TABLE IF EXISTS `PersonPermissionsOnFields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonPermissionsOnFields` (
  `PersonPermissionsOnFieldID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `TableName` varchar(45) COLLATE utf8_persian_ci NOT NULL,
  `PersonID` int(10) unsigned NOT NULL,
  `RecID` int(10) unsigned NOT NULL,
  `FieldName` varchar(45) COLLATE utf8_persian_ci NOT NULL,
  `AccessType` varchar(45) COLLATE utf8_persian_ci NOT NULL,
  PRIMARY KEY (`PersonPermissionsOnFieldID`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PersonPermissionsOnTable`
--

DROP TABLE IF EXISTS `PersonPermissionsOnTable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonPermissionsOnTable` (
  `PersonPermissionsOnTableID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `TableName` varchar(45) COLLATE utf8_persian_ci NOT NULL,
  `PersonID` int(10) unsigned NOT NULL,
  `RecID` int(10) unsigned NOT NULL,
  `DetailTableName` varchar(45) COLLATE utf8_persian_ci NOT NULL,
  `AddAccessType` varchar(45) COLLATE utf8_persian_ci NOT NULL,
  `RemoveAccessType` varchar(45) COLLATE utf8_persian_ci NOT NULL,
  `UpdateAccessType` varchar(45) COLLATE utf8_persian_ci NOT NULL,
  `ViewAccessType` varchar(45) COLLATE utf8_persian_ci NOT NULL,
  PRIMARY KEY (`PersonPermissionsOnTableID`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PhysicalServers`
--

DROP TABLE IF EXISTS `PhysicalServers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PhysicalServers` (
  `PhysicalServerID` int(11) NOT NULL AUTO_INCREMENT,
  `ServerName` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نام سرور',
  `HardwareInfo` varchar(1000) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'اطلاعات پردازنده',
  `description` varchar(2000) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'اطلاعات هارد',
  `administrator` int(11) DEFAULT NULL,
  PRIMARY KEY (`PhysicalServerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PrivateMessageFollows`
--

DROP TABLE IF EXISTS `PrivateMessageFollows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PrivateMessageFollows` (
  `PrivateMessageFollowID` int(11) NOT NULL AUTO_INCREMENT,
  `PrivateMessageID` int(11) DEFAULT NULL,
  `comment` varchar(3000) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'یادداشت',
  `ReferTime` datetime DEFAULT NULL COMMENT 'زمان ارجاع',
  `FromPersonID` int(11) DEFAULT NULL COMMENT 'از کاربر',
  `ToPersonID` int(11) DEFAULT NULL COMMENT 'به کاربر',
  `UpperLevelID` int(11) DEFAULT NULL COMMENT 'کد ارجاع بالاتر',
  `ReferFileName` varchar(145) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نام فایل',
  `ReferFileContent` longblob COMMENT 'محتوای فایل ضمیمه',
  `ReferStatus` enum('NOT_READ','READ','REFER','ARCHIVE') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'وضعیت\n',
  `ArchiveFolderID` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'پوشه بایگانی',
  PRIMARY KEY (`PrivateMessageFollowID`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='گردش پیامهای شخصی';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PrivateMessages`
--

DROP TABLE IF EXISTS `PrivateMessages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PrivateMessages` (
  `PrivateMessageID` int(11) NOT NULL AUTO_INCREMENT,
  `MessageTitle` varchar(1000) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'عنوان',
  `MessageBody` varchar(4000) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'متن',
  `SenderID` int(11) DEFAULT NULL COMMENT 'فرستنده',
  `CreateTime` datetime DEFAULT NULL COMMENT 'تاریخ ارسال',
  `FileName` varchar(145) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نام فایل مرتبط',
  `FileContent` longblob COMMENT 'محتوای فایل\n',
  PRIMARY KEY (`PrivateMessageID`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='پیامهای شخصی';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProgramLevelProjects`
--

DROP TABLE IF EXISTS `ProgramLevelProjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProgramLevelProjects` (
  `ProgramLevelProjectID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProgramLevelID` int(10) unsigned NOT NULL COMMENT 'مرحله',
  `ProjectID` int(11) NOT NULL COMMENT 'پروژه',
  PRIMARY KEY (`ProgramLevelProjectID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='پروژه های مرحله';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProgramLevels`
--

DROP TABLE IF EXISTS `ProgramLevels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProgramLevels` (
  `ProgramLevelID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProgramID` int(10) unsigned NOT NULL COMMENT 'کد برنامه',
  `LevelNo` int(10) unsigned NOT NULL COMMENT 'شماره ردیف',
  `title` varchar(500) COLLATE utf8_persian_ci NOT NULL COMMENT 'عنوان',
  `description` varchar(2000) COLLATE utf8_persian_ci NOT NULL COMMENT 'شرح مرحله',
  `EstimatedStartTime` datetime NOT NULL COMMENT 'زمان تخمینی شروع',
  `EstimatedEndTime` datetime NOT NULL COMMENT 'زمان تخمینی شروع',
  `StartTime` datetime NOT NULL COMMENT 'زمان واقعی شروع',
  `EndTime` datetime NOT NULL COMMENT 'زمان واقعی خاتمه',
  `ProjectLevelStatus` enum('NOT_START','PROGRESSING','ENDED','SUPSPENDED') COLLATE utf8_persian_ci NOT NULL COMMENT 'وضعیت مرحله',
  `EstimatedWorkHours` int(10) unsigned NOT NULL COMMENT 'میزان تخمینی نفر/ساعت',
  `ExpreTime` datetime NOT NULL COMMENT 'مهلت انجام',
  PRIMARY KEY (`ProgramLevelID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='مراحل برنامه';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProgramUnitSubGroups`
--

DROP TABLE IF EXISTS `ProgramUnitSubGroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProgramUnitSubGroups` (
  `ProgramUnitSubGroupID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ouid` int(10) unsigned NOT NULL COMMENT 'کد واحد سازمانی',
  `SubGroupTitle` varchar(200) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام حوزه',
  PRIMARY KEY (`ProgramUnitSubGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='حوزه های برنامه ریزی در واحد';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectAgreements`
--

DROP TABLE IF EXISTS `ProjectAgreements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectAgreements` (
  `ProjectAgreementID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProjectID` int(10) unsigned NOT NULL COMMENT 'کد پروژه مربوطه',
  `AgreementType` enum('EXECUTIVE','CONSULT','SELLER') COLLATE utf8_persian_ci NOT NULL COMMENT 'نوع قرارداد',
  `AgreementSubject` varchar(500) COLLATE utf8_persian_ci NOT NULL COMMENT 'موضوع قرارداد',
  `AgreementLengthYear` int(11) DEFAULT '0' COMMENT 'سال',
  `AgreementLengthMonth` int(11) NOT NULL DEFAULT '0' COMMENT 'مدت قرارداد ماه',
  `AgreementLengthDay` int(11) NOT NULL DEFAULT '0' COMMENT 'مدت قرارداد روز',
  `CompanyName` varchar(1000) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام شرکت/فرد',
  `AgreementDate` datetime NOT NULL COMMENT 'تاریخ عقد قرارداد',
  `AgreementCost` decimal(15,6) NOT NULL COMMENT 'مبلغ قرارداد',
  `CostQuata` int(11) NOT NULL COMMENT 'ضریب تعدیل قرارداد',
  `AgreementStatus` enum('REAL','PREDICT') COLLATE utf8_persian_ci NOT NULL COMMENT 'وضعیت قرارداد',
  `AgreementFileName` varchar(250) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام فایل قرارداد',
  `AgreementFileContent` longblob NOT NULL COMMENT 'فایل قرارداد',
  `AgreementNumber` varchar(50) COLLATE utf8_persian_ci NOT NULL COMMENT 'شماره قرارداد',
  `AgreementSubType` enum('PEYMAN_KOL','ESTERDAD_PEYMAN','METERI','AMANI','OTHER') COLLATE utf8_persian_ci NOT NULL COMMENT 'نوع فرعی قرارداد',
  `StudyPhase` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL COMMENT 'مطالعه و طراحی',
  `ExecutivePlanPhase` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL COMMENT 'تهیه نقشه های اجرایی',
  `ObservationPhase` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL COMMENT 'نظارت بر اجرا',
  `ContractorsAndConsultantsID` int(11) NOT NULL COMMENT 'مشاور/پیمانکار',
  `AgreementDescription` varchar(500) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'توضیحات',
  `PaymanKol` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'نوع پیمان - پیمان کل',
  `EsterdadPayman` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'نوع پیمان - استرداد پیمان',
  `Metri` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'نوع پیمان - متری',
  `other` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'نوع پیمان - سایر',
  `amani` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'نوع قرارداد - امانی',
  `payman` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'نوع قرارداد - پیمانکاری',
  `consult` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'نوع قرارداد - مشاوره',
  `seller` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'نوع قرارداد - خرید تجهیزات',
  PRIMARY KEY (`ProjectAgreementID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='قراردادهای پروژه';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectDocumentTypes`
--

DROP TABLE IF EXISTS `ProjectDocumentTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectDocumentTypes` (
  `ProjectDocumentTypeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8_persian_ci NOT NULL COMMENT 'عنوان',
  `ProjectID` int(10) unsigned NOT NULL COMMENT 'پروژه مربوطه',
  `CreatorID` int(10) unsigned NOT NULL COMMENT 'ایجاد کننده',
  PRIMARY KEY (`ProjectDocumentTypeID`),
  KEY `new_index` (`ProjectID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='انواع سند پروژه ها';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectDocuments`
--

DROP TABLE IF EXISTS `ProjectDocuments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectDocuments` (
  `ProjectDocumentID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProjectID` int(10) unsigned NOT NULL COMMENT 'پروژه',
  `ProjectDocumentTypeID` int(11) NOT NULL COMMENT 'نوع سند',
  `FileContent` longblob NOT NULL COMMENT 'فایل',
  `FileName` varchar(200) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام فایل',
  `description` varchar(1000) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'شرح',
  `CreatorID` int(10) unsigned NOT NULL COMMENT 'ایجاد کننده',
  `CreateDate` datetime NOT NULL COMMENT 'تاریخ ایجاد',
  PRIMARY KEY (`ProjectDocumentID`),
  KEY `new_index` (`ProjectID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='مستندات';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectExternalMembers`
--

DROP TABLE IF EXISTS `ProjectExternalMembers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectExternalMembers` (
  `ProjectExternalMemberID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProjectID` int(11) NOT NULL COMMENT 'کد پروژه',
  `CreatorID` int(11) NOT NULL COMMENT 'ایجاد کننده',
  `FName` varchar(50) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام',
  `LName` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام خانوادگی',
  `MemberType` enum('MEMBER','VIEWER','MANAGER') COLLATE utf8_persian_ci NOT NULL COMMENT 'نوع عضویت',
  PRIMARY KEY (`ProjectExternalMemberID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='اعضای پروژه غیر عضو دانشگاه';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectGroups`
--

DROP TABLE IF EXISTS `ProjectGroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectGroups` (
  `ProjectGroupID` int(11) NOT NULL AUTO_INCREMENT,
  `RelatedUnitID` int(11) NOT NULL COMMENT 'کد واحد سازمانی مربوطه',
  `ProjectGroupName` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام گروه پروژه',
  PRIMARY KEY (`ProjectGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci ROW_FORMAT=DYNAMIC COMMENT='گروه های پروژه';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectHistory`
--

DROP TABLE IF EXISTS `ProjectHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectHistory` (
  `ProjectHistoryID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProjectID` int(10) unsigned NOT NULL COMMENT 'کار مربوطه',
  `PersonID` int(10) unsigned NOT NULL COMMENT 'شخص',
  `ActionDesc` varchar(500) COLLATE utf8_persian_ci NOT NULL COMMENT 'شرح کار',
  `ChangedPart` enum('MAIN_PROJECT','MEMBER','MILESTONE','DOCUMENT','DOCUMENT_TYPE','ACTIVITY_TYPE','TASK_TYPE') COLLATE utf8_persian_ci NOT NULL COMMENT 'بخش مربوطه',
  `RelatedItemID` int(10) unsigned NOT NULL COMMENT 'کد آیتم مربوطه',
  `ActionType` enum('ADD','REMOVE','UPDATE','DELETE','VIEW') COLLATE utf8_persian_ci NOT NULL COMMENT 'نوع عمل',
  `ActionTime` datetime NOT NULL COMMENT 'زمان انجام',
  PRIMARY KEY (`ProjectHistoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='تاریخچه';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectMembers`
--

DROP TABLE IF EXISTS `ProjectMembers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectMembers` (
  `ProjectmemberID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProjectID` int(10) unsigned NOT NULL COMMENT 'پروژه',
  `PersonID` int(10) unsigned NOT NULL COMMENT 'کد شخص',
  `AccessType` enum('MANAGER','MEMBER','VIEWER','PMMANAGER') COLLATE utf8_persian_ci NOT NULL DEFAULT 'MEMBER' COMMENT 'نوع دسترسی',
  `ParticipationPercent` int(11) NOT NULL DEFAULT '100' COMMENT 'درصد مشارکت در پروژه',
  `CreatorID` int(10) unsigned NOT NULL COMMENT 'کد شخص ایجاد کننده',
  PRIMARY KEY (`ProjectmemberID`),
  KEY `new_index` (`ProjectID`),
  KEY `new_index2` (`PersonID`,`AccessType`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='اعضای پروژه';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectMilestones`
--

DROP TABLE IF EXISTS `ProjectMilestones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectMilestones` (
  `ProjectMilestoneID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProjectID` int(11) NOT NULL COMMENT 'پروژه مربوطه',
  `MilestoneDate` datetime NOT NULL COMMENT 'تاریخ',
  `description` varchar(500) COLLATE utf8_persian_ci NOT NULL COMMENT 'شرح',
  `CreatorID` int(10) unsigned NOT NULL COMMENT 'ایجاد کننده',
  PRIMARY KEY (`ProjectMilestoneID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='تاریخهای مهم';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectProgressTable`
--

DROP TABLE IF EXISTS `ProjectProgressTable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectProgressTable` (
  `ProjectProgressTableID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProjectID` int(10) unsigned NOT NULL COMMENT 'کد پروژه مربوطه',
  `EvaluateDate` datetime NOT NULL COMMENT 'تاریخ',
  `ProgressPercent` int(11) NOT NULL COMMENT 'درصد پیشرفت',
  `MatchSchedule` enum('AHEADSCHEDULE','SCHEDULE','BEHINDPROGRAM') COLLATE utf8_persian_ci NOT NULL DEFAULT 'AHEADSCHEDULE' COMMENT 'تطابق با برنامه زمانبندی\n',
  `Executive` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'علل تاخیر-دستگاه اجرایی',
  `Contractor` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'علل تاخیر-پیمانکار',
  `DesignConsultant` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'علل تاخیر-مشاور طراح',
  `ConsultantSupervisor` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'علل تاخیر-مشاور ناظر',
  `LackCredit` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'علل تاخیر-کمبود اعتبار',
  `LackMaterials` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'علل تاخیرکمبود مصالح',
  `SocialIssues` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'علل تاخیر-مسايل اجتماعی',
  `PreparingGround` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'علل تاخیر-تهیه زمین',
  `Other` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'علل تاخیر-سایر',
  `supervision` varchar(500) COLLATE utf8_persian_ci NOT NULL COMMENT 'نظارت وتعهدات',
  `CivilActivityID` int(10) unsigned NOT NULL COMMENT 'شرح عملیات فعلی',
  `ProgressDescription` varchar(500) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'توضیحات',
  PRIMARY KEY (`ProjectProgressTableID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='جدول پیشرفت فیزیکی پروژه ها';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectResourceTypes`
--

DROP TABLE IF EXISTS `ProjectResourceTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectResourceTypes` (
  `ProjectResourceTypeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT 'توضیحات',
  PRIMARY KEY (`ProjectResourceTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='منابع تامین اعتبار پروژه ها';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectResponsibles`
--

DROP TABLE IF EXISTS `ProjectResponsibles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectResponsibles` (
  `ProjectResponsibleID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProjectID` int(10) unsigned NOT NULL,
  `PersonID` int(10) unsigned NOT NULL,
  `ouid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`ProjectResponsibleID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='پاسخگویان به درخواستهای خارجی در پروژه';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectTaskActivities`
--

DROP TABLE IF EXISTS `ProjectTaskActivities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectTaskActivities` (
  `ProjectTaskActivityID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProjectTaskID` int(10) unsigned NOT NULL COMMENT 'کار مربوطه',
  `CreatorID` int(10) unsigned NOT NULL COMMENT 'ایجاد کننده',
  `ActivityDate` datetime NOT NULL COMMENT 'تاریخ اقدام',
  `ProjectTaskActivityTypeID` int(10) unsigned NOT NULL COMMENT 'نوع اقدام',
  `ActivityLength` int(10) unsigned NOT NULL COMMENT 'زمان مصرفی',
  `ProgressPercent` int(10) unsigned NOT NULL COMMENT 'درصد پیشرفت',
  `ActivityDescription` varchar(1000) COLLATE utf8_persian_ci NOT NULL COMMENT 'شرح',
  `FileContent` longblob NOT NULL COMMENT 'فایل ضمیمه',
  `FileName` varchar(200) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام فایل',
  `ChangedTables` varchar(2000) COLLATE utf8_persian_ci NOT NULL COMMENT 'جداول تغییر داده شده',
  `ChangedPages` varchar(2000) COLLATE utf8_persian_ci NOT NULL COMMENT 'صفحات تغییر داده شده',
  PRIMARY KEY (`ProjectTaskActivityID`),
  KEY `new_index` (`ProjectTaskID`)
) ENGINE=InnoDB AUTO_INCREMENT=184 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='اقدامات';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectTaskActivityTypes`
--

DROP TABLE IF EXISTS `ProjectTaskActivityTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectTaskActivityTypes` (
  `ProjectTaskActivityTypeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8_persian_ci NOT NULL COMMENT 'عنوان',
  `ProjectID` int(10) unsigned NOT NULL COMMENT 'پروژه مربوطه',
  `CreatorID` int(10) unsigned NOT NULL COMMENT 'ایجاد کننده',
  PRIMARY KEY (`ProjectTaskActivityTypeID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='انواع اقدامات';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectTaskAssignedUsers`
--

DROP TABLE IF EXISTS `ProjectTaskAssignedUsers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectTaskAssignedUsers` (
  `ProjectTaskAssignedUserID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProjectTaskID` int(10) unsigned NOT NULL COMMENT 'کار مربوطه',
  `PersonID` int(11) NOT NULL COMMENT 'شخص مربوطه',
  `AssignDescription` varchar(500) COLLATE utf8_persian_ci NOT NULL COMMENT 'شرح انتساب',
  `ParticipationPercent` int(11) NOT NULL COMMENT 'درصد مشارکت',
  `CreatorID` int(10) unsigned NOT NULL,
  `AssignType` enum('EXECUTOR','VIEWER') COLLATE utf8_persian_ci NOT NULL DEFAULT 'EXECUTOR' COMMENT 'نوع انتساب',
  `ProcessJudge` enum('0','1') COLLATE utf8_persian_ci DEFAULT '0',
  PRIMARY KEY (`ProjectTaskAssignedUserID`),
  KEY `new_index` (`ProjectTaskID`)
) ENGINE=InnoDB AUTO_INCREMENT=193 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='کاربران منتسب به کار';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectTaskComments`
--

DROP TABLE IF EXISTS `ProjectTaskComments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectTaskComments` (
  `ProjectTaskCommentID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProjectTaskID` int(11) NOT NULL COMMENT 'کار مربوطه',
  `CreatorID` int(11) NOT NULL COMMENT 'ایجاد کننده',
  `CreateTime` datetime NOT NULL COMMENT 'زمان ایجاد',
  `CommentBody` varchar(3000) COLLATE utf8_persian_ci NOT NULL COMMENT 'متن',
  PRIMARY KEY (`ProjectTaskCommentID`),
  KEY `new_index` (`ProjectTaskID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='یادداشتها';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectTaskDocuments`
--

DROP TABLE IF EXISTS `ProjectTaskDocuments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectTaskDocuments` (
  `ProjectTaskDocumentID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProjectTaskID` int(10) unsigned NOT NULL COMMENT 'کار مربوطه',
  `CreatorID` int(10) unsigned NOT NULL COMMENT 'ایجاد کننده',
  `CreateTime` datetime NOT NULL COMMENT 'تاریخ ایجاد',
  `DocumentDescription` varchar(1000) COLLATE utf8_persian_ci NOT NULL COMMENT 'شرح',
  `FileContent` longblob NOT NULL COMMENT 'فایل ضمیمه',
  `FileName` varchar(200) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام فایل',
  PRIMARY KEY (`ProjectTaskDocumentID`),
  KEY `new_index` (`ProjectTaskID`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='اسناد کارها';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectTaskGroups`
--

DROP TABLE IF EXISTS `ProjectTaskGroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectTaskGroups` (
  `ProjectTaskGroupID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProjectID` int(11) NOT NULL COMMENT 'کد پروژه',
  `TaskGroupName` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT 'عنوان',
  PRIMARY KEY (`ProjectTaskGroupID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='گروه های کار داخل پروژه';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectTaskHistory`
--

DROP TABLE IF EXISTS `ProjectTaskHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectTaskHistory` (
  `ProjectTaskHistoryID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProjectTaskID` int(10) unsigned NOT NULL COMMENT 'کار مربوطه',
  `PersonID` int(10) unsigned NOT NULL COMMENT 'شخص',
  `ActionDesc` varchar(500) COLLATE utf8_persian_ci NOT NULL COMMENT 'شرح کار',
  `ChangedPart` enum('MAIN_TASK','COMMENT','DOCUMENT','ACTIVITY','REQUISITE','USER','VIEWER') COLLATE utf8_persian_ci NOT NULL COMMENT 'بخش مربوطه',
  `RelatedItemID` int(10) unsigned NOT NULL COMMENT 'کد آیتم مربوطه',
  `ActionType` enum('ADD','REMOVE','UPDATE','DELETE','VIEW') COLLATE utf8_persian_ci NOT NULL COMMENT 'نوع عمل',
  `ActionTime` datetime NOT NULL COMMENT 'زمان انجام',
  PRIMARY KEY (`ProjectTaskHistoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=854 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='تاریخچه';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectTaskRequisites`
--

DROP TABLE IF EXISTS `ProjectTaskRequisites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectTaskRequisites` (
  `ProjectTaskRequisiteID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProjectTaskID` int(10) unsigned NOT NULL COMMENT 'کار مربوطه',
  `RequisiteTaskID` int(10) unsigned NOT NULL COMMENT 'کار پیشنیاز',
  `CreatorID` int(10) unsigned NOT NULL COMMENT 'ایجاد کننده',
  PRIMARY KEY (`ProjectTaskRequisiteID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='پیشنیازها';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectTaskTypes`
--

DROP TABLE IF EXISTS `ProjectTaskTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectTaskTypes` (
  `ProjectTaskTypeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT 'عنوان',
  `ProjectID` int(11) NOT NULL COMMENT 'پروژه مربوطه',
  `CreatorID` int(10) unsigned NOT NULL COMMENT 'ایجاد کننده',
  PRIMARY KEY (`ProjectTaskTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='انواع کارها';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProjectTasks`
--

DROP TABLE IF EXISTS `ProjectTasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProjectTasks` (
  `ProjectTaskID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProjectID` int(10) unsigned NOT NULL COMMENT 'پروژه مربوطه',
  `ProjectTaskTypeID` int(10) unsigned NOT NULL COMMENT 'نوع کار',
  `title` varchar(1000) COLLATE utf8_persian_ci NOT NULL COMMENT 'عنوان',
  `description` varchar(2000) COLLATE utf8_persian_ci NOT NULL COMMENT 'شرح',
  `CreatorID` int(10) unsigned NOT NULL COMMENT 'ایجاد کننده',
  `CreateDate` datetime NOT NULL COMMENT 'تاریخ ایجاد',
  `PeriodType` enum('ONCE','EVERYDAY','EVERYWEEK','EVERYMONTH') COLLATE utf8_persian_ci NOT NULL DEFAULT 'ONCE' COMMENT 'پریود انجام',
  `CountOfDone` smallint(6) NOT NULL DEFAULT '1' COMMENT 'تعداد دفعات انجام',
  `EstimatedStartTime` datetime NOT NULL COMMENT 'زمان تخمینی شروع',
  `RealStartTime` datetime NOT NULL COMMENT 'زمان واقعی شروع',
  `EstimatedRequiredTimeDay` int(10) unsigned NOT NULL COMMENT 'زمان مورد نیاز - روز',
  `EstimatedRequiredTimeHour` int(10) unsigned NOT NULL COMMENT 'زمان مورد نیاز - ساعت',
  `EstimatedRequitedTimeMin` int(10) unsigned NOT NULL COMMENT 'زمان مورد نیاز - دقیقه',
  `HasExpireTime` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL COMMENT 'مهلت اقدام دارد؟',
  `ExpireTime` datetime NOT NULL COMMENT 'مهلت اقدام',
  `TaskPeriority` int(5) unsigned NOT NULL COMMENT 'اولویت',
  `TaskStatus` enum('NOT_START','PROGRESSING','DONE','SUSPENDED','REPLYED','READY_FOR_TEST','CONFWAIT','EXECUTECONF','NOCONF') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NOT_START' COMMENT 'وضعیت',
  `ParentID` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'کار پدر',
  `UpdateReason` varchar(500) COLLATE utf8_persian_ci NOT NULL COMMENT 'دلیل بروزرسانی',
  `DeleteFlag` enum('NO','YES') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'حذف منطقی',
  `DoneDate` datetime NOT NULL COMMENT 'زمان پایان کار - اقدام',
  `ProgramLevelID` int(11) NOT NULL COMMENT 'مرحله از برنامه اصلی',
  `TaskGroupID` int(11) NOT NULL COMMENT 'گروه کار',
  `ControllerID` int(11) NOT NULL COMMENT 'کاربر کنترل کننده',
  `StartTime` varchar(5) COLLATE utf8_persian_ci DEFAULT '00:00' COMMENT 'زمان شروع انجام کار',
  `EndTime` varchar(5) COLLATE utf8_persian_ci DEFAULT '00:00' COMMENT 'زمان پایان انجام کار',
  `study` enum('YES','NO') COLLATE utf8_persian_ci DEFAULT 'NO',
  PRIMARY KEY (`ProjectTaskID`),
  KEY `new_index` (`ProjectID`),
  KEY `new_index2` (`CreatorID`),
  KEY `new_index3` (`ProjectTaskTypeID`),
  KEY `new_index4` (`DeleteFlag`),
  KEY `new_index5` (`CreateDate`),
  KEY `new_index6` (`DoneDate`),
  KEY `new_index7` (`TaskStatus`),
  KEY `new_index8` (`ProgramLevelID`),
  KEY `new_index9` (`ProgramLevelID`,`TaskStatus`)
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='کار';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `RDB2OntoLog`
--

DROP TABLE IF EXISTS `RDB2OntoLog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RDB2OntoLog` (
  `RDB2OntoLogID` int(11) NOT NULL AUTO_INCREMENT,
  `OntoEntityID` int(11) DEFAULT NULL,
  `OntoEntityType` enum('CLASS','DATAPROP','OBJPROP') COLLATE utf8_persian_ci DEFAULT NULL,
  `RDBEntityID` int(11) DEFAULT NULL,
  `RDBEntityType` enum('TABLE','FIELD') COLLATE utf8_persian_ci DEFAULT NULL,
  `TargetOntologyID` int(11) DEFAULT NULL,
  PRIMARY KEY (`RDB2OntoLogID`)
) ENGINE=InnoDB AUTO_INCREMENT=152793 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='سابقه تبدیل عنصر RDB به Onto';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `RefrenceTypes`
--

DROP TABLE IF EXISTS `RefrenceTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RefrenceTypes` (
  `RefrenceTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `ResearchProjectID` int(11) DEFAULT NULL,
  `RefrenceTypeTitle` varchar(245) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'عنوان',
  PRIMARY KEY (`RefrenceTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='انواع مراجع';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SpecialPages`
--

DROP TABLE IF EXISTS `SpecialPages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SpecialPages` (
  `SpecialPageID` int(11) NOT NULL AUTO_INCREMENT,
  `PageName` varchar(245) COLLATE utf8_persian_ci DEFAULT NULL,
  PRIMARY KEY (`SpecialPageID`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SysAudit`
--

DROP TABLE IF EXISTS `SysAudit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SysAudit` (
  `RecID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `UserID` varchar(15) COLLATE utf8_persian_ci DEFAULT NULL,
  `ActionType` tinyint(3) unsigned DEFAULT NULL,
  `ActionDesc` varchar(500) COLLATE utf8_persian_ci DEFAULT NULL,
  `IPAddress` bigint(20) DEFAULT NULL,
  `SysCode` tinyint(3) unsigned DEFAULT NULL,
  `IsSecure` tinyint(3) unsigned DEFAULT NULL,
  `ATS` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=33888 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SystemDBLog`
--

DROP TABLE IF EXISTS `SystemDBLog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SystemDBLog` (
  `RecID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `page` varchar(200) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'صفحه',
  `query` text COLLATE utf8_persian_ci COMMENT 'پرس و جو',
  `SerializedParam` text COLLATE utf8_persian_ci COMMENT 'پارامتر پرس و جو',
  `UserID` varchar(15) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'شناسه کاربر',
  `IPAddress` bigint(20) DEFAULT NULL COMMENT 'آدرس IP',
  `SysCode` tinyint(3) unsigned DEFAULT NULL COMMENT 'کد سیستم',
  `ATS` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'تاریخ اجرا',
  `ExecuteTime` float(14,10) NOT NULL COMMENT 'مدت زمان اجرا',
  `QueryStatus` enum('SUCCESS','FAILED') COLLATE utf8_persian_ci DEFAULT 'SUCCESS' COMMENT 'وضعیت پرس و جو',
  `DBName` varchar(30) COLLATE utf8_persian_ci DEFAULT '' COMMENT 'نام پایگاه داده',
  PRIMARY KEY (`RecID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=233 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SystemFacilities`
--

DROP TABLE IF EXISTS `SystemFacilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SystemFacilities` (
  `FacilityID` int(11) NOT NULL AUTO_INCREMENT,
  `FacilityName` varchar(245) COLLATE utf8_persian_ci DEFAULT NULL,
  `GroupID` int(11) DEFAULT NULL,
  `OrderNo` int(11) DEFAULT NULL,
  `PageAddress` varchar(345) COLLATE utf8_persian_ci DEFAULT NULL,
  `EFacilityName` varchar(245) COLLATE utf8_persian_ci DEFAULT NULL,
  PRIMARY KEY (`FacilityID`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SystemFacilityGroups`
--

DROP TABLE IF EXISTS `SystemFacilityGroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SystemFacilityGroups` (
  `GroupID` int(11) NOT NULL AUTO_INCREMENT,
  `GroupName` varchar(145) COLLATE utf8_persian_ci DEFAULT NULL,
  `EGroupName` varchar(145) COLLATE utf8_persian_ci DEFAULT NULL,
  `OrderNo` int(11) DEFAULT NULL,
  PRIMARY KEY (`GroupID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TemporarySavedData`
--

DROP TABLE IF EXISTS `TemporarySavedData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TemporarySavedData` (
  `TemporarySavedDataID` int(11) NOT NULL AUTO_INCREMENT,
  `PersonID` int(11) NOT NULL,
  `FieldName` varchar(50) COLLATE utf8_persian_ci NOT NULL,
  `FieldValue` varchar(4000) COLLATE utf8_persian_ci NOT NULL,
  PRIMARY KEY (`TemporarySavedDataID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='داده های ذخیره موقت';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TermEquivalentEnglishTerms`
--

DROP TABLE IF EXISTS `TermEquivalentEnglishTerms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TermEquivalentEnglishTerms` (
  `TermEquivalentEnglishTermID` int(11) NOT NULL AUTO_INCREMENT,
  `TermID` int(11) DEFAULT NULL COMMENT 'اصطلاح',
  `EnglishTerm` varchar(250) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'معادل انگلیسی',
  PRIMARY KEY (`TermEquivalentEnglishTermID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='معادل انگلیسی اصطلاحات';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TermMappingTargetOntology`
--

DROP TABLE IF EXISTS `TermMappingTargetOntology`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TermMappingTargetOntology` (
  `TermMappingTargetOntologyID` int(11) NOT NULL AUTO_INCREMENT,
  `OntologyID` int(11) DEFAULT NULL,
  PRIMARY KEY (`TermMappingTargetOntologyID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='هستان نگار مقصد مفهوم سازی بر اساس محتوا';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TermOntologyElementMapping`
--

DROP TABLE IF EXISTS `TermOntologyElementMapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TermOntologyElementMapping` (
  `TermOntologyElementMappingID` int(11) NOT NULL AUTO_INCREMENT,
  `TermID` int(11) DEFAULT NULL COMMENT 'اصطلاح',
  `OntologyEntityID` int(11) DEFAULT NULL COMMENT 'موجودیت هستان نگار',
  `EntityType` enum('CLASS','OBJECT_PROPERTY','DATA_PROPERTY','INSTANCE','DATA_RANGE') COLLATE utf8_persian_ci DEFAULT NULL,
  `DataValue` varchar(300) COLLATE utf8_persian_ci DEFAULT NULL,
  `OntologyPropertyPermittedValueID` int(11) DEFAULT NULL,
  PRIMARY KEY (`TermOntologyElementMappingID`),
  KEY `idx1` (`OntologyPropertyPermittedValueID`),
  KEY `idx2` (`TermID`),
  KEY `idx3` (`OntologyEntityID`)
) ENGINE=InnoDB AUTO_INCREMENT=1102 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='نگاشت اصطلاحات و عناصر هستان نگار';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TermReferenceContent`
--

DROP TABLE IF EXISTS `TermReferenceContent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TermReferenceContent` (
  `TermReferenceContentID` int(11) NOT NULL AUTO_INCREMENT,
  `TermReferenceID` int(11) DEFAULT NULL,
  `PageNum` int(11) NOT NULL DEFAULT '0',
  `content` varchar(4000) COLLATE utf8_persian_ci DEFAULT NULL,
  PRIMARY KEY (`TermReferenceContentID`)
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='محتوای پاراگرافهای مراجع';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TermReferenceMapping`
--

DROP TABLE IF EXISTS `TermReferenceMapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TermReferenceMapping` (
  `TermReferenceMappingID` int(11) NOT NULL AUTO_INCREMENT,
  `TermReferenceID` int(11) DEFAULT NULL,
  `TermID` int(11) DEFAULT NULL,
  `PageNum` int(11) DEFAULT NULL COMMENT 'شماره صفحه',
  `CreatorUserID` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'ایجاد کننده',
  `CreateDate` datetime DEFAULT NULL COMMENT 'تاریخ ایجاد',
  `MappingComment` varchar(1000) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'یادداشت',
  `ParagraphNo` int(11) DEFAULT NULL COMMENT 'شماره پاراگراف',
  `SentenceNo` int(11) DEFAULT NULL COMMENT 'شماره جمله',
  PRIMARY KEY (`TermReferenceMappingID`),
  KEY `idx1` (`TermID`),
  KEY `idx2` (`TermReferenceID`)
) ENGINE=InnoDB AUTO_INCREMENT=2455 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='ارتباط اصطلاحات و مراجع';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TermReferences`
--

DROP TABLE IF EXISTS `TermReferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TermReferences` (
  `TermReferenceID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'عنوان',
  `FileContent` longblob COMMENT 'فایل',
  `RelatedFileName` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL,
  PRIMARY KEY (`TermReferenceID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='مراجع اصطلاحات';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TermRelations`
--

DROP TABLE IF EXISTS `TermRelations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TermRelations` (
  `TermRelationID` int(11) NOT NULL AUTO_INCREMENT,
  `TermID1` int(11) DEFAULT NULL COMMENT 'اصطلاح اول',
  `TermID2` int(11) DEFAULT NULL COMMENT 'اصطلاح دوم',
  `RelationTermID` int(11) DEFAULT NULL COMMENT 'ارتباط',
  PRIMARY KEY (`TermRelationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='ارتباط بین اصطلاحات';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TermsCoOccure`
--

DROP TABLE IF EXISTS `TermsCoOccure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TermsCoOccure` (
  `TermsCoOccureID` int(11) NOT NULL AUTO_INCREMENT,
  `TermID1` int(11) DEFAULT NULL COMMENT 'اصطلاح اول',
  `TermID2` int(11) DEFAULT NULL COMMENT 'اصطلاح دوم',
  `frequency` int(11) DEFAULT NULL COMMENT 'تعداد دفعات هم رخدادی',
  PRIMARY KEY (`TermsCoOccureID`),
  KEY `idx1` (`TermID1`),
  KEY `idx2` (`TermID2`),
  KEY `idx3` (`frequency`)
) ENGINE=InnoDB AUTO_INCREMENT=564144 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='اصطلاحات هم رخداد';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TermsManipulationHistory`
--

DROP TABLE IF EXISTS `TermsManipulationHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TermsManipulationHistory` (
  `TermsManipulationHistoryID` int(11) NOT NULL AUTO_INCREMENT,
  `PreTermTitle` varchar(145) COLLATE utf8_persian_ci DEFAULT NULL,
  `NewTermTitle` varchar(145) COLLATE utf8_persian_ci DEFAULT NULL,
  `ActionType` enum('INSERT','DELETE','UPDATE','REPLACE') COLLATE utf8_persian_ci DEFAULT NULL,
  `PreTermID` int(11) DEFAULT NULL,
  `NewTermID` int(11) DEFAULT NULL,
  `PersonID` int(11) DEFAULT NULL,
  `ATS` datetime DEFAULT NULL,
  PRIMARY KEY (`TermsManipulationHistoryID`),
  KEY `idx1` (`PreTermID`),
  KEY `idx2` (`NewTermID`),
  KEY `idx3` (`ActionType`),
  KEY `idx5` (`PersonID`),
  KEY `idx6` (`ATS`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='تاریخچه تغییرات روی واژگان استخراج شده';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `TermsReferHistory`
--

DROP TABLE IF EXISTS `TermsReferHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TermsReferHistory` (
  `TermsReferHistoryID` int(11) NOT NULL AUTO_INCREMENT,
  `TermID` int(11) DEFAULT NULL,
  `TermTitle` varchar(145) COLLATE utf8_persian_ci DEFAULT NULL,
  `TermReferenceID` int(11) DEFAULT NULL,
  `TermReferenceTitle` varchar(245) COLLATE utf8_persian_ci DEFAULT NULL,
  `ActionType` enum('INSERT','REMOVE','REPLACE') COLLATE utf8_persian_ci DEFAULT NULL,
  `ReplacedTermID` int(11) DEFAULT NULL,
  `ReplacedTermTitle` varchar(145) COLLATE utf8_persian_ci DEFAULT NULL,
  `PersonID` int(11) DEFAULT NULL,
  `ATS` datetime DEFAULT NULL,
  `PageNum` int(11) DEFAULT NULL,
  `ParagraphNo` int(11) DEFAULT NULL,
  PRIMARY KEY (`TermsReferHistoryID`),
  KEY `idx1` (`TermID`),
  KEY `idx2` (`TermReferenceID`),
  KEY `idx3` (`ActionType`),
  KEY `idx4` (`ReplacedTermID`),
  KEY `idx5` (`PersonID`),
  KEY `idx6` (`ATS`)
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='تاریخچه کار بر روی ارجاعات واژگان';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `UserFacilities`
--

DROP TABLE IF EXISTS `UserFacilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UserFacilities` (
  `FacilityPageID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL,
  `FacilityID` int(11) DEFAULT NULL,
  PRIMARY KEY (`FacilityPageID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `UserProjectScopes`
--

DROP TABLE IF EXISTS `UserProjectScopes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UserProjectScopes` (
  `UserProjectScopesID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UserID` varchar(50) COLLATE utf8_persian_ci NOT NULL COMMENT 'کد کاربر',
  `PermittedUnitID` int(11) NOT NULL COMMENT 'کد واحد سازمانی مجاز',
  PRIMARY KEY (`UserProjectScopesID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='سازمانهایی که کاربر مجاز به مدیریت پروژه های آنهاست';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `domains`
--

DROP TABLE IF EXISTS `domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domains` (
  `DomainName` varchar(30) COLLATE utf8_persian_ci NOT NULL DEFAULT '',
  `description` varchar(150) COLLATE utf8_persian_ci DEFAULT NULL,
  `eDescription` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL,
  `DomainValue` int(11) unsigned NOT NULL DEFAULT '0',
  `unc_code` int(10) unsigned NOT NULL COMMENT 'کد موسسه',
  `ActiveDomain` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL COMMENT 'آیا دامنه فعال است ؟',
  PRIMARY KEY (`DomainName`,`DomainValue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `MessageID` int(11) NOT NULL AUTO_INCREMENT,
  `MessageBody` varchar(3000) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'متن پیام',
  `RelatedFileName` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL,
  `FileContent` longblob,
  `ImageFileName` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL,
  `ImageFileContent` longblob,
  `CreatorID` int(11) DEFAULT NULL COMMENT 'ایجاد کننده',
  `StartDate` datetime DEFAULT NULL COMMENT 'زمان شروع نمایش',
  `EndDate` datetime DEFAULT NULL COMMENT 'زمان پایان نمایش',
  `CreateDate` datetime DEFAULT NULL COMMENT 'زمان ایجاد',
  PRIMARY KEY (`MessageID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='پیامها';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ontologies`
--

DROP TABLE IF EXISTS `ontologies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ontologies` (
  `OntologyID` int(11) NOT NULL AUTO_INCREMENT,
  `OntologyTitle` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'عنوان',
  `OntologyURI` varchar(200) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'مسیر اینترنتی',
  `FileName` varchar(145) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نام فایل',
  `FileContent` longblob COMMENT 'محتوا',
  `comment` varchar(1000) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'یادداشت',
  PRIMARY KEY (`OntologyID`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='هستان نگار';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `PaymentID` int(11) NOT NULL AUTO_INCREMENT,
  `PersonID` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL COMMENT 'مبلغ',
  `PaymentDate` datetime DEFAULT NULL COMMENT 'تاریخ',
  `PayType` enum('CASH','CHECK','TRANSFER') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نوع پرداخت',
  `PaymentDescription` varchar(1000) COLLATE utf8_persian_ci DEFAULT NULL,
  `PaymentFile` blob COMMENT 'فایل رسید',
  `PaymentFileName` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نام فایل رسید',
  PRIMARY KEY (`PaymentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='پرداختی ها';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `persons`
--

DROP TABLE IF EXISTS `persons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `persons` (
  `PersonID` int(11) NOT NULL AUTO_INCREMENT,
  `pfname` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL,
  `plname` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL,
  `CardNumber` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL,
  `EnterExitTypeID` int(11) DEFAULT NULL,
  `person_type` int(10) unsigned NOT NULL DEFAULT '1',
  `AccountInfo` varchar(700) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'حساب بانکی',
  `mobile` varchar(15) COLLATE utf8_persian_ci NOT NULL COMMENT 'موبایل',
  PRIMARY KEY (`PersonID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `photos`
--

DROP TABLE IF EXISTS `photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `photos` (
  `PhotoID` int(11) NOT NULL AUTO_INCREMENT,
  `PersonID` int(11) DEFAULT NULL,
  `picture` longblob,
  PRIMARY KEY (`PhotoID`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plans`
--

DROP TABLE IF EXISTS `plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plans` (
  `PlanID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PlanNumber` varchar(50) COLLATE utf8_persian_ci NOT NULL DEFAULT '0' COMMENT 'شماره طبقه بندی طرح',
  `PlanType` enum('STATE','NATIONAL','NATIONAL_STATE','CENTERALIZED','OTHER') COLLATE utf8_persian_ci NOT NULL DEFAULT 'STATE' COMMENT 'نوع',
  `title` varchar(200) COLLATE utf8_persian_ci NOT NULL COMMENT 'عنوان',
  `PlanResourceTypeID` int(11) NOT NULL COMMENT 'منبع تامین اعتبار',
  `PlanResourceTypeID2` int(11) NOT NULL COMMENT 'منبع تامین اعتبار ۲',
  `PlanResourceTypeID3` int(11) NOT NULL COMMENT 'منبع تامین اعتبار ۳',
  `PlanResourceTypeID4` int(11) NOT NULL COMMENT 'منبع تامین اعتبار ۴',
  `PlanStatus` enum('PROGRESSING','CONTINUOUS','FINISHED','FINISHED_BY_DEBT','PAUSED') COLLATE utf8_persian_ci NOT NULL COMMENT 'وضعیت طرح',
  `StartDate` datetime NOT NULL COMMENT 'شروع طرح',
  `EndDate` datetime NOT NULL COMMENT 'پایان طرح',
  PRIMARY KEY (`PlanID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='طرحها';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `programs`
--

DROP TABLE IF EXISTS `programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `programs` (
  `ProgramID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ProgramUnitSubGroupID` int(11) NOT NULL COMMENT 'حوزه',
  `OrderNo` int(10) unsigned NOT NULL COMMENT 'شماره ردیف',
  `title` varchar(500) COLLATE utf8_persian_ci NOT NULL COMMENT 'عنوان',
  `EstimatedStarTime` datetime NOT NULL COMMENT 'زمان شروع',
  `EstimatedEndTime` datetime NOT NULL COMMENT 'زمان پایان',
  `StartTime` datetime NOT NULL COMMENT 'زمان شروع واقعی',
  `EndTime` datetime NOT NULL COMMENT 'زمان پایان واقعی',
  `description` varchar(2000) COLLATE utf8_persian_ci NOT NULL COMMENT 'شرح برنامه',
  `priority` smallint(6) NOT NULL COMMENT 'اولویت',
  `ProgramStatus` enum('NOT_START','PROGRESSING','ENDED','SUSPENDED') COLLATE utf8_persian_ci NOT NULL DEFAULT 'PROGRESSING' COMMENT 'وضعیت',
  PRIMARY KEY (`ProgramID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='برنامه های کاری';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `ProjectID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ouid` int(11) NOT NULL COMMENT 'واحد سازمانی',
  `ProjectGroupID` int(11) NOT NULL COMMENT 'کد گروه پروژه (کلید به جدول ProjectGroups)',
  `title` varchar(500) COLLATE utf8_persian_ci NOT NULL COMMENT 'عنوان',
  `description` varchar(1000) COLLATE utf8_persian_ci NOT NULL COMMENT 'شرح',
  `StartTime` datetime NOT NULL COMMENT 'شروع',
  `EndTime` datetime NOT NULL COMMENT 'پایان',
  `SysCode` int(11) NOT NULL COMMENT 'سیستم مربوطه',
  `ProjectPriority` smallint(6) NOT NULL COMMENT 'اولویت',
  `ProjectStatus` enum('NOT_STARTED','DEVELOPING','MAINTENANCE','FINISHED','SUSPENDED','FINISHED_BY_DEBT','CONTINUOUS') COLLATE utf8_persian_ci NOT NULL COMMENT 'وضعیت',
  `DeleteFlag` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'فلگ حذف منطقی',
  `Achievable` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NO' COMMENT 'قابل دستیابی برای کلیه اعضای دانشگاه ,هیات علمی- کارمندان و دانشجویان',
  PRIMARY KEY (`ProjectID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='پروژه';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `systems`
--

DROP TABLE IF EXISTS `systems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `systems` (
  `SysCode` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(145) COLLATE utf8_persian_ci NOT NULL,
  PRIMARY KEY (`SysCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `terms`
--

DROP TABLE IF EXISTS `terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `terms` (
  `TermID` int(11) NOT NULL AUTO_INCREMENT,
  `TermTitle` varchar(1000) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'عنوان',
  `comment` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'یادداشت',
  `CreatorUserID` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'ایجاد کننده',
  `CreateDate` datetime DEFAULT NULL COMMENT 'تاریخ ایجاد',
  `TF` decimal(5,2) DEFAULT NULL,
  `IDF` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`TermID`)
) ENGINE=InnoDB AUTO_INCREMENT=1105 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='اصطلاحات';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-09-02 16:32:52

use baseinfo;

-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: 172.20.8.186    Database: baseinfo
-- ------------------------------------------------------
-- Server version	5.5.46-0+deb8u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-09-02 16:42:51

use sessionmanagement;

-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: 172.20.8.186    Database: sessionmanagement
-- ------------------------------------------------------
-- Server version	5.5.46-0+deb8u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ActRegister`
--

DROP TABLE IF EXISTS `ActRegister`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ActRegister` (
  `RowID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `SessionPreCommandID` int(11) DEFAULT NULL COMMENT 'کلید خارجی',
  `ActReg` varchar(1000) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'ثبت اقدامات',
  PRIMARY KEY (`RowID`),
  KEY `FK_SessionPreCommands` (`SessionPreCommandID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='ثبت اقدامات در خصوص هر یک از بندهای صورتجلسه';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `EMonArray`
--

DROP TABLE IF EXISTS `EMonArray`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EMonArray` (
  `_id` int(11) NOT NULL,
  `emon` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FMonArray`
--

DROP TABLE IF EXISTS `FMonArray`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FMonArray` (
  `_id` int(11) NOT NULL,
  `fmon` int(11) DEFAULT NULL,
  PRIMARY KEY (`_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `MemberRoles`
--

DROP TABLE IF EXISTS `MemberRoles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MemberRoles` (
  `MemberRoleID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام نقش',
  PRIMARY KEY (`MemberRoleID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='نقشهای افراد در جلسات';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PersonPermissionsOnFields`
--

DROP TABLE IF EXISTS `PersonPermissionsOnFields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonPermissionsOnFields` (
  `PersonPermissionsOnFieldsID` int(11) NOT NULL AUTO_INCREMENT,
  `PersonID` int(10) unsigned NOT NULL COMMENT 'کد شخص',
  `TableName` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام جدول',
  `FieldName` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام فیلد',
  `AccessType` enum('READ','WRITE','NONE') COLLATE utf8_persian_ci NOT NULL DEFAULT 'READ' COMMENT 'نوع دسترسی',
  `RecID` int(11) NOT NULL COMMENT 'کد رکورد مربوطه (۰ برای حالت ایجاد - منهای یک برای همه رکوردها)',
  PRIMARY KEY (`PersonPermissionsOnFieldsID`),
  KEY `idx` (`RecID`,`TableName`,`FieldName`,`PersonID`)
) ENGINE=InnoDB AUTO_INCREMENT=207954 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='دسترسیهای افراد روی فیلدهای جدول';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PersonPermissionsOnTable`
--

DROP TABLE IF EXISTS `PersonPermissionsOnTable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonPermissionsOnTable` (
  `PersonPermissionsOnTableID` int(10) unsigned NOT NULL,
  `PersonID` int(11) NOT NULL COMMENT 'کد شخص',
  `RecID` int(11) NOT NULL COMMENT 'کد رکورد جدول اصلی - (منهای یک برای همه رکوردها)',
  `TableName` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام جدول اصلی',
  `DetailTableName` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT 'نام جدول جزییات',
  `AddAccessType` enum('YES','NO') COLLATE utf8_persian_ci NOT NULL DEFAULT 'YES' COMMENT 'دسترسی اضافه',
  `RemoveAccessType` enum('PRIVATE','PUBLIC','NONE') COLLATE utf8_persian_ci NOT NULL DEFAULT 'PUBLIC' COMMENT 'دسترسی حذف',
  `UpdateAccessType` enum('PRIVATE','PUBLIC','NONE') COLLATE utf8_persian_ci NOT NULL DEFAULT 'PUBLIC' COMMENT 'دسترسی بروزرسانی',
  `ViewAccessType` enum('PRIVATE','PUBLIC','NONE') COLLATE utf8_persian_ci NOT NULL DEFAULT 'PUBLIC' COMMENT 'دسترسی مشاهده'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='دسترسی افراد روی جداول (جزییات)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PersonPermittedSessionTypes`
--

DROP TABLE IF EXISTS `PersonPermittedSessionTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonPermittedSessionTypes` (
  `PersonPermittedSessionTypeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PersonID` int(10) unsigned DEFAULT NULL COMMENT 'کد شخصی',
  `SessionTypeID` int(10) unsigned DEFAULT NULL COMMENT 'کد الگوی جلسه',
  PRIMARY KEY (`PersonPermittedSessionTypeID`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='کاربران مجاز الگوهای جلسات';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SessionDecisions`
--

DROP TABLE IF EXISTS `SessionDecisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SessionDecisions` (
  `SessionDecisionID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UniversitySessionID` int(10) unsigned DEFAULT NULL COMMENT 'کد جلسه',
  `OrderNo` int(10) unsigned DEFAULT NULL COMMENT 'ردیف',
  `description` varchar(8000) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'شرح',
  `ResponsiblePersonID` int(10) unsigned DEFAULT NULL COMMENT 'کد شخص مسوول',
  `RepeatInNextSession` enum('YES','NO') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'در جلسه بعدی تکرار شود',
  `RelatedFile` longblob COMMENT 'فایل ضمیمه',
  `RelatedFileName` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نام فایل ضمیمه',
  `HasDeadline` enum('YES','NO') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'مهلت اقدام دارد؟',
  `DeadlineDate` datetime DEFAULT NULL COMMENT 'مهلت اقدام',
  `CreatorPersonID` int(11) NOT NULL COMMENT 'ایجاد کننده',
  `SessionPreCommandID` int(11) NOT NULL DEFAULT '0' COMMENT 'دستور کار مربوطه',
  `SessionControl` enum('NOT_START','DONE') COLLATE utf8_persian_ci NOT NULL DEFAULT 'NOT_START' COMMENT 'کنترل مصوبه',
  `DeadLine` date DEFAULT NULL,
  `priority` int(10) DEFAULT NULL,
  PRIMARY KEY (`SessionDecisionID`)
) ENGINE=InnoDB AUTO_INCREMENT=3906 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='مصوبات جلسه';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SessionDocuments`
--

DROP TABLE IF EXISTS `SessionDocuments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SessionDocuments` (
  `SessionDocumentID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UniversitySessionID` int(10) unsigned DEFAULT NULL COMMENT 'کد جلسه',
  `CreatorPersonID` int(10) unsigned DEFAULT NULL COMMENT 'کد شخص ایجاد کننده',
  `CreateTime` datetime DEFAULT NULL COMMENT 'تاریخ ایجاد',
  `DocumentFile` mediumblob COMMENT 'فایل',
  `DocumentFileName` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نام فایل',
  `DocumentDescription` varchar(500) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'شرح',
  `InputOrOutput` enum('INPUT','OUTPUT') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نوع',
  PRIMARY KEY (`SessionDocumentID`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='مستندات';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SessionHistory`
--

DROP TABLE IF EXISTS `SessionHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SessionHistory` (
  `SessionHistoryID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UniversitySessionID` int(10) unsigned DEFAULT NULL COMMENT 'کد جلسه',
  `ItemID` int(11) NOT NULL COMMENT 'کد آیتم',
  `ItemType` enum('MAIN','PRECOMMAND','DECISION','DOCUMENT','MEMBER','USER','OTHER','PAList') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نوع آیتم',
  `description` varchar(1000) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'مقدار قبلی',
  `PersonID` int(10) unsigned DEFAULT NULL COMMENT 'کد شخص',
  `ActionType` enum('ADD','EDIT','REMOVE','VIEW','CONFIRM','REJECT','SIGN') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نوع عمل',
  `ActionTime` datetime DEFAULT NULL COMMENT 'زمان عمل',
  `IPAddress` int(11) DEFAULT NULL COMMENT 'آدرس آی پی',
  PRIMARY KEY (`SessionHistoryID`)
) ENGINE=InnoDB AUTO_INCREMENT=47874 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='سابقه';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SessionMembers`
--

DROP TABLE IF EXISTS `SessionMembers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SessionMembers` (
  `SessionMemberID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `MemberRow` int(11) NOT NULL DEFAULT '0' COMMENT 'شماره ردیف شخص',
  `UniversitySessionID` int(10) unsigned DEFAULT NULL COMMENT 'کد جلسه',
  `MemberPersonType` enum('PERSONEL','OTHER') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نوع عضو',
  `MemberPersonID` int(10) unsigned DEFAULT NULL COMMENT 'کد شخصی عضو',
  `FirstName` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نام',
  `LastName` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نام خانوادگی',
  `MemberRole` int(10) unsigned DEFAULT NULL COMMENT 'نقش ',
  `NeedToConfirm` enum('YES','NO') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'برگزاری جلسه منوط به تایید این کاربر است',
  `AccessSign` enum('YES','NO') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'اجازه امضای صورتجلسه',
  `ConfirmStatus` enum('RAW','ACCEPT','REJECT') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'وضعیت تایید درخواست جلسه',
  `RejectDescription` varchar(500) COLLATE utf8_persian_ci NOT NULL COMMENT 'دلیل رد درخواست',
  `SignStatus` enum('YES','NO') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'وضعیت امضای فرد',
  `SignDescription` varchar(200) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'شرح امضا',
  `SignTime` datetime DEFAULT NULL COMMENT 'زمان امضای صورتجلسه',
  `PresenceType` enum('PRESENT','ABSENT') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نوع حضور',
  `PresenceTime` int(10) unsigned DEFAULT NULL COMMENT 'مدت حضور',
  `TardinessTime` int(10) unsigned DEFAULT NULL COMMENT 'غیبت',
  `canvasimg` blob NOT NULL,
  PRIMARY KEY (`SessionMemberID`),
  KEY `MemberPersonID_index` (`MemberPersonID`)
) ENGINE=InnoDB AUTO_INCREMENT=18643 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='اعضا';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SessionOtherUsers`
--

DROP TABLE IF EXISTS `SessionOtherUsers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SessionOtherUsers` (
  `SessionOtherUserID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UniversitySessionID` int(10) unsigned DEFAULT NULL COMMENT 'کد جلسه',
  `PersonID` int(10) unsigned DEFAULT NULL COMMENT 'کد شخص',
  PRIMARY KEY (`SessionOtherUserID`),
  KEY `PersonID_index` (`PersonID`)
) ENGINE=InnoDB AUTO_INCREMENT=2093 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='سایر کاربران';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SessionPreCommands`
--

DROP TABLE IF EXISTS `SessionPreCommands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SessionPreCommands` (
  `SessionPreCommandID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UniversitySessionID` int(10) unsigned DEFAULT NULL COMMENT 'کد جلسه',
  `OrderNo` int(10) unsigned DEFAULT NULL COMMENT 'ردیف',
  `description` varchar(500) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'شرح',
  `ResponsiblePersonID` int(10) unsigned DEFAULT NULL COMMENT 'کد مسوول پیگیری',
  `RepeatInNextSession` enum('YES','NO') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'در دستور کاری بعدی تکرار شود؟',
  `RelatedFile` mediumblob COMMENT 'فایل ضمیمه',
  `RelatedFileName` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نام فایل ضمیمه',
  `CreatorPersonID` int(11) NOT NULL COMMENT 'ایجاد کننده',
  `DeadLine` date DEFAULT NULL COMMENT 'مهلت زمانی',
  `priority` int(10) unsigned DEFAULT NULL COMMENT 'اولویت',
  PRIMARY KEY (`SessionPreCommandID`)
) ENGINE=InnoDB AUTO_INCREMENT=11307 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='دستور کار';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SessionTypeMembers`
--

DROP TABLE IF EXISTS `SessionTypeMembers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SessionTypeMembers` (
  `SessionTypeMemberID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `MemberRow` int(11) NOT NULL DEFAULT '0' COMMENT 'شماره ردیف شخص',
  `SessionTypeID` int(10) unsigned DEFAULT NULL COMMENT 'کپ نوع جلسه',
  `MemberPersonType` enum('PERSONEL','OTHER') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نوع عضو',
  `MemberPersonID` int(10) unsigned DEFAULT NULL COMMENT 'کد شخصی عضو',
  `FirstName` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نام',
  `LastName` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نام خانوادگی',
  `MemberRoleID` int(10) unsigned DEFAULT NULL COMMENT 'کد نقش',
  `NeedToConfirm` enum('YES','NO') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'برگزاری جلسه منوط به تایید این کاربر است',
  `AccessSign` enum('YES','NO') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'اجازه امضای صورتجلسه',
  `NeedToSignSessionDecisions` enum('YES','NO') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'برای قطعی شدن صورتجلسه نیاز به امضای الکترونیکی فرد می باشد',
  `NeedToConfirmPresence` enum('YES','NO') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'مدعو باید درخواست حضور را تایید نماید',
  `AccessFinalAccept` enum('READ','WRITE','NONE') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نحوه دسترسی به تایید نهایی صورتجلسه',
  `AccessRejectSign` enum('READ','WRITE','NONE') COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نحوه دسترسی به امکان لغو امضای اعضای جلسه',
  PRIMARY KEY (`SessionTypeMemberID`)
) ENGINE=InnoDB AUTO_INCREMENT=901 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='اعضای الگوهای جلسه';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SessionTypes`
--

DROP TABLE IF EXISTS `SessionTypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SessionTypes` (
  `SessionTypeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SessionTypeTitle` varchar(500) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'عنوان',
  `SessionTypeLocation` varchar(200) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'محل تشکیل',
  `SessionTypeStartTime` int(10) unsigned DEFAULT NULL COMMENT 'زمان شروع',
  `SessionTypeDurationTime` int(10) unsigned DEFAULT NULL COMMENT 'مدت جلسه',
  PRIMARY KEY (`SessionTypeID`)
) ENGINE=InnoDB AUTO_INCREMENT=179 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='الگوهای جلسه';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SysAudit`
--

DROP TABLE IF EXISTS `SysAudit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SysAudit` (
  `RecID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `UserID` varchar(15) COLLATE utf8_persian_ci DEFAULT NULL,
  `ActionType` tinyint(3) unsigned DEFAULT NULL,
  `ActionDesc` varchar(500) COLLATE utf8_persian_ci DEFAULT NULL,
  `IPAddress` bigint(20) DEFAULT NULL,
  `SysCode` tinyint(3) unsigned DEFAULT NULL,
  `IsSecure` tinyint(3) unsigned DEFAULT NULL,
  `ATS` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `UniversitySessions`
--

DROP TABLE IF EXISTS `UniversitySessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UniversitySessions` (
  `UniversitySessionID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SessionTypeID` int(10) unsigned DEFAULT NULL COMMENT 'کد الگو',
  `SessionNumber` int(10) unsigned DEFAULT NULL COMMENT 'شماره جلسه',
  `SessionTitle` varchar(500) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'عنوان',
  `SessionDate` datetime DEFAULT NULL COMMENT 'تاریخ',
  `SessionLocation` varchar(200) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'مکان',
  `SessionStartTime` int(10) unsigned DEFAULT NULL COMMENT 'زمان شروع',
  `SessionDurationTime` int(10) unsigned DEFAULT NULL COMMENT 'مدت جلسه',
  `SessionStatus` int(10) unsigned DEFAULT NULL COMMENT 'وضعیت',
  `SessionDescisionsFile` mediumblob COMMENT 'فایل صورتجلسه',
  `SessionDescisionsFileName` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL COMMENT 'نام فایل صورتجلسه',
  `DescisionsFileStatus` enum('RAW','CONFIRMED') COLLATE utf8_persian_ci NOT NULL DEFAULT 'RAW' COMMENT 'وضعیت صورتجلسه',
  `CreatorPersonID` int(11) NOT NULL COMMENT 'ایجاد کننده جلسه',
  `DescisionsListStatus` enum('RAW','CONFIRMED') COLLATE utf8_persian_ci NOT NULL DEFAULT 'RAW' COMMENT 'وضعیت لیست مصوبات',
  PRIMARY KEY (`UniversitySessionID`),
  KEY `CreatorPersonID_index` (`CreatorPersonID`)
) ENGINE=InnoDB AUTO_INCREMENT=1572 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='جلسات';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-09-02 16:58:00

-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: 172.20.8.186    Database: sessionmanagement
-- ------------------------------------------------------
-- Server version	5.5.46-0+deb8u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `ActRegister`
--

LOCK TABLES `ActRegister` WRITE;
/*!40000 ALTER TABLE `ActRegister` DISABLE KEYS */;
/*!40000 ALTER TABLE `ActRegister` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `EMonArray`
--

LOCK TABLES `EMonArray` WRITE;
/*!40000 ALTER TABLE `EMonArray` DISABLE KEYS */;
INSERT INTO `EMonArray` VALUES (1,31),(2,28),(3,31),(4,30),(5,31),(6,30),(7,31),(8,31),(9,30),(10,31),(11,30),(12,31);
/*!40000 ALTER TABLE `EMonArray` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `FMonArray`
--

LOCK TABLES `FMonArray` WRITE;
/*!40000 ALTER TABLE `FMonArray` DISABLE KEYS */;
INSERT INTO `FMonArray` VALUES (1,31),(2,31),(3,31),(4,31),(5,31),(6,31),(7,30),(8,30),(9,30),(10,30),(11,30),(12,29);
/*!40000 ALTER TABLE `FMonArray` ENABLE KEYS */;
UNLOCK TABLES;


-- Dump completed on 2018-09-02 16:52:32

use projectmanagement;

--
-- Dumping routines for database 'projectmanagement'
--
/*!50003 DROP FUNCTION IF EXISTS `g2j` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE FUNCTION `g2j`(_edate  date) RETURNS varchar(10) CHARSET utf8
    DETERMINISTIC
BEGIN
declare gy,gm,gd    int ;
declare  g_day_no   int ;
declare  i  int;

declare j_day_no ,  j_np ,  jy , jm , jd  int ;
set gy  = year(_edate)-1600;
set gm = month(_edate)-1;
set gd  = day(_edate)-1;

if  (year(_edate) < 1900  or year(_edate) > 2100  )  or (month(_edate) <1  or month(_edate)  > 12 )   or  (day(_edate) < 1 or day(_edate) > 31 )  then
return 'date-error';
end if;

set g_day_no = 365 * gy + floor((gy+3) /  4) - floor((gy+99) / 100) + floor((gy+399)/ 400);

set i=0;
while i < gm do
  set g_day_no=g_day_no+(select  emon from EMonArray  where _id=i+1);
  set i = i + 1;
end while;
if  gm >1  and ((gy % 4 =0 and gy % 100 !=0)  or  (gy%400=0))   then
  set g_day_no = g_day_no + 1 ;
end if;
set  g_day_no = g_day_no + gd;
set  j_day_no =  g_day_no-79;
set  j_np = floor(j_day_no /  12053);
set  j_day_no = j_day_no % 12053;
set  jy = 979+33 *  j_np + 4  *  floor(j_day_no /  1461);
set j_day_no = j_day_no % 1461;

if   j_day_no >= 366  then
  set jy = jy + floor((j_day_no-1) /  365);
  set j_day_no = (j_day_no-1) % 365;
end if;

set  i=0;
while  i < 11  and j_day_no >=  ( select fmon from FMonArray  where _id= i + 1)  do
  set j_day_no = j_day_no - ( select fmon from FMonArray  where _id = i + 1);
  set  i = i + 1;
end while;

set jm = i+1;
set jd = j_day_no+1;

return  concat_ws('/',jy,if(jm < 10 , concat('0',jm) , jm)    ,if(jd < 10 , concat('0',jd) , jd ));
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `j2g` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE FUNCTION `j2g`(j_y int , j_m int , j_d  int ) RETURNS varchar(10) CHARSET utf8
    DETERMINISTIC
BEGIN

declare  jy,jm,jd  int ;
declare  j_day_no , g_day_no   , gy,gm,gd  int ;
declare  i int ;
declare  leap  bool;

if  (j_y < 1300  or  j_y > 1450  )  or (j_m <1  or j_m  > 12 )   or  (j_d < 1 or j_d > 31 )  then
return 'date-error';
end if;


set  jy = j_y-979;
set  jm = j_m-1;
set  jd = j_d-1;

set j_day_no = 365 * jy + floor(jy/33) * 8 + floor(((jy%33)+3) /  4);
set i  = 0;
while  i < jm  do
  set j_day_no = j_day_no + (select fmon from FMonArray  where  _id=i+1);
  set i = i+1;
end while;
set  j_day_no = j_day_no + jd;
set  g_day_no = j_day_no+79;
set  gy = 1600 + 400 *  floor(g_day_no /  146097);
set  g_day_no = g_day_no % 146097;
set  leap = true;
if  g_day_no >= 36525  then   
  set g_day_no = g_day_no - 1;
  set gy = gy + 100 * floor(g_day_no /  36524);
  set g_day_no = g_day_no % 36524;
  if  g_day_no >= 365  then
    set g_day_no  =  g_day_no + 1;
  else
    set leap = false;
  end if;
end if;
set gy = gy + 4 *  floor(g_day_no / 1461);
set g_day_no = g_day_no % 1461;
if  g_day_no >= 366  then
  set leap = false;
  set g_day_no = g_day_no - 1 ;
  set gy = gy + floor(g_day_no /  365);
  set g_day_no = g_day_no % 365;
end if;
set  i = 0;
while  g_day_no >= ( select  emon from EMonArray  where _id = i + 1 ) + ( select if(i = 1 and  leap = true , 1 , 0) )   do
  set g_day_no = g_day_no - (( select  emon from EMonArray  where _id = i + 1)  + ( select if ( i = 1 and  leap= true ,1,0)));
  set i = i + 1;
end while;
set gm = i+1;
set gd = g_day_no+1;
return  concat_ws('-',gy , gm , gd );
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-09-02 17:12:15


-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: 172.20.8.186    Database: projectmanagement
-- ------------------------------------------------------
-- Server version	5.5.46-0+deb8u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `AccountSpecs`
--

LOCK TABLES `AccountSpecs` WRITE;
/*!40000 ALTER TABLE `AccountSpecs` DISABLE KEYS */;
INSERT INTO `AccountSpecs` VALUES (1,'omid','30f9dd65de612bfe458a871b90eabb3026c9fb29',1,'omid'),(3,'kahani','349648028f8ddc45a10801fe72ff04dd897d377e',4,NULL),(9,'sara','4418c0c9f1ae6e9e7ad3732bf7936d14f0afd5d9',9,NULL);
/*!40000 ALTER TABLE `AccountSpecs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `domains`
--

LOCK TABLES `domains` WRITE;
/*!40000 ALTER TABLE `domains` DISABLE KEYS */;
/*!40000 ALTER TABLE `domains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `EMonArray`
--

LOCK TABLES `EMonArray` WRITE;
/*!40000 ALTER TABLE `EMonArray` DISABLE KEYS */;
INSERT INTO `EMonArray` VALUES (1,31),(2,28),(3,31),(4,30),(5,31),(6,30),(7,31),(8,31),(9,30),(10,31),(11,30),(12,31);
/*!40000 ALTER TABLE `EMonArray` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `FacilityPages`
--

LOCK TABLES `FacilityPages` WRITE;
/*!40000 ALTER TABLE `FacilityPages` DISABLE KEYS */;
INSERT INTO `FacilityPages` VALUES (1,1,'/ManageSystemFacilityGroups.php'),(2,2,'/ManageSystemFacilities.php'),(3,3,'/Managepersons.php'),(4,4,'/ManageAccountSpecs.php'),(5,3,'/ManagePersonAgreements.php'),(6,3,'/ManagePayments.php'),(7,4,'/ManageUserPermissions.php'),(8,2,'/ManageUserFacilities.php'),(9,2,'/ManageFacilityPages.php'),(10,5,'/Manageontologies.php'),(11,5,'/ManageOntologyMergeProject.php'),(12,5,'/ManageOntologyClasses.php'),(13,5,'/ManageOntologyClassHirarchy.php'),(14,5,'/ShowOntologyClassTree.php'),(15,5,'/ManageOntologyClassLabels.php'),(16,5,'/ManageOntologyProperties.php'),(17,5,'/ManageOntologyPropertyLabels.php'),(18,5,'/PrintOntologyDetails.php'),(19,5,'/PrintOntologyDetails2.php'),(20,5,'/ManageOntologyMergeProjectMembers.php'),(21,6,'/ManageTermReferences.php'),(22,6,'/ManageTermReferenceContent.php'),(23,6,'/ManageTermReferenceMapping.php'),(24,7,'/Manageterms.php'),(25,7,'/ShowTermReferenceMapping.php'),(26,7,'/TermFrequency.ph'),(27,7,'/TermOntologyPage.php'),(28,8,'/TermFrequency.php'),(29,8,'/ShowTermReferenceMapping.php'),(31,9,'/ManageProjects.php'),(32,9,'/Newprojects.php'),(33,9,'/GetProjectGroupsList.php'),(34,9,'/ManageProjectMembers.php'),(35,9,'/ManageProjectResponsibles.php'),(36,9,'/ManageProjectDocuments.php'),(37,9,'/ManageProjectMilestones.php'),(38,9,'/ManageProjectDocumentTypes.php'),(39,9,'/ManageProjectTaskActivityTypes.php'),(40,9,'/ManageProjectTaskTypes.php'),(41,9,'/ManageProjectTaskGroups.php'),(42,9,'/ManageProjectHistory.php'),(43,9,'/ShowProjectActivities.php'),(44,9,'/ShowProjectOverview.php'),(45,10,'/TasksKartable.php'),(46,10,'/TasksMessages.php'),(47,10,'/ViewerTasks.php'),(48,10,'/TasksForControl.php'),(49,10,'/LastCreatedTasks.php'),(50,10,'/LastDoneTasks.php'),(51,10,'/ShowAllPersonStatus.php'),(52,10,'/ShowLastChanges.php'),(53,10,'/NewProjectTasks.php'),(54,10,'/GetTaskTypesList.php'),(55,10,' /GetTaskGroupsList.php'),(56,10,'/GetTaskGroupsList.php'),(57,10,'/ManageProjectTaskAssignedUsers.php'),(58,10,'/ManageProjectTaskActivities.php'),(59,10,'/ManageProjectTaskComments.php'),(60,10,'/ManageProjectTaskDocuments.php'),(61,10,'/ManageProjectTaskRequisites.php'),(62,10,'/ManageProjectTaskHistory.php'),(63,10,'/NewProjectTaskActivities.php'),(64,10,'/SearchTasks.php'),(65,11,'/ManageProjectTasks.php'),(66,10,'/SelectStaff.php'),(69,14,'/Managemessages.php'),(70,15,'/SendMessage.php'),(71,15,'/SelectMultiStaff.php'),(72,16,'/MailBox.php'),(73,16,'/ShowMessage.php'),(74,16,'/SelectStaff.php'),(75,17,'/SentBox.php'),(76,18,'/SearchMessage.php'),(77,19,'/TimeReport.php'),(78,20,'/MyTimeReport.php'),(79,21,'/ManageUniversitySessions.php'),(80,22,'/ManageSessionTypes.php'),(82,22,'/NewSessionTypes.php'),(83,22,'/ManageSessionTypeMembers.php'),(84,22,'/ManagePersonPermittedSessionTypes.php'),(85,22,'/SessionTypesSetSecurity.php'),(86,21,'/NewUniversitySessions.php'),(87,21,'/UpdateUniversitySessions.php'),(88,21,'/ManageSessionPreCommands.php'),(89,21,'/ManageMembersPAList.php'),(90,21,'/ManageSessionDecisions.php'),(91,21,'/ManageSessionDocuments.php'),(92,21,'/ManageSessionMembers.php'),(93,21,'/ManageSessionOtherUsers.php'),(94,21,'/UniversitySessionsSetSecurity.php'),(95,21,'/ManageSessionHistory.php'),(96,21,'/NewSessionPreCommands.php'),(97,21,'/HistorySession.php'),(98,21,'/PrintSession.php'),(99,5,'/loader.php'),(100,21,'/NewSessionDecisions.php'),(101,21,'/NewSessionDocuments.php'),(102,21,'/NewSessionMembers.php'),(103,23,'/RequestedSessions.php'),(104,24,'/ReadyForSignSessions.php'),(105,24,'/PrintSessionNew.php'),(106,25,'/NewQuestionnaire.php'),(107,25,'/ManageQuestionnaireFields.php'),(108,25,'/NewQuestionnaireField.php'),(109,25,'/SelectDefaultKey.php'),(110,25,'/CreateSelectOptionsForQuestionnaire.php'),(111,25,'/LookUpPageHelp.php'),(112,25,'/NewQuestionnaireLabel.php'),(113,25,'/ManageQuestionnaires.php'),(114,25,'/ManageFormsSections.php'),(115,25,'/ManageQuestionnaireDetailTables.php'),(116,25,'/ManageQuestionnaireManagers.php'),(117,25,'/ManageQuestionnaireUsers.php'),(118,26,'/NewQuestionnaire.php'),(119,26,'/ManageQuestionnaireFields.php'),(120,26,'/NewQuestionnaireField.php'),(121,26,'/SelectDefaultKey.php'),(122,26,'/CreateSelectOptionsForQuestionnaire.php'),(123,26,'/LookUpPageHelp.php'),(124,26,'/NewQuestionnaireLabel.php'),(125,26,'/ManageQuestionnaires.php'),(126,26,'/ManageFormsSections.php'),(127,26,'/ManageQuestionnaireDetailTables.php'),(128,26,'/ManageQuestionnaireManagers.php'),(129,26,'/ManageQuestionnaireUsers.php'),(133,26,'/ManageQuestionnaires.php'),(134,8,'/CoOccuranceAnalysis.php'),(135,10,'/ManageProjectPercentages.php'),(136,9,'/projectsSetSecurity.php'),(137,21,'/SelectFromSessionPreCommands.php'),(138,10,'/ShowMessagePhoto.php'),(139,5,'/AnalyzeOntologies.php'),(140,5,'/ManageOntologyValidationExperts.php'),(141,8,'/ManageOntologyPropertyPermittedValues.php'),(142,5,'/ManageOntologyClassProperties.php'),(143,27,'/ShowTermsManipulationHistory.php'),(144,28,'/ShowTermReferHistory.php'),(145,5,'/ManageOntologyClassParents.php'),(146,5,'/ManageOntologyClassChilds.php'),(147,5,'/ShowExpertsResult.php'),(149,5,'/ShowGraph.php'),(212,41,'/ShowTableInfo.php'),(213,41,'/ManageFieldsDataSematics.php'),(214,41,'/SearchTables.php'),(215,5,'/CompareOntologies.php'),(216,5,'/MetaData2Onto.php'),(217,5,'/ShowOntologyClassNProps.php'),(218,43,'/CompareAllOntos.php'),(219,5,'/EditOntologyLabels.php'),(220,5,'/ShowSimilarClassRelations.php'),(221,5,'/ShowSimilarClassProperties.php'),(222,5,'/GetOwl.php'),(223,5,'/GetER.php'),(224,5,'/PrintOntologyDetails3.php'),(225,5,'/PrintOntologyDetails4.php'),(226,5,'/OntologyMergeProperties.php'),(227,5,'/OntologyMergeClasses.php'),(228,41,'/ManageTables.php'),(229,41,'/EditTableInfo.php'),(230,41,'/ShowTopRecords.php'),(231,41,'/SetForignKey.php'),(232,5,'/ShowClassesAnalysis.php'),(233,46,'/TransferTables.php'),(234,26,'/ManageSystemFacilityGroups.php');
/*!40000 ALTER TABLE `FacilityPages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `FMonArray`
--

LOCK TABLES `FMonArray` WRITE;
/*!40000 ALTER TABLE `FMonArray` DISABLE KEYS */;
INSERT INTO `FMonArray` VALUES (1,31),(2,31),(3,31),(4,31),(5,31),(6,31),(7,30),(8,30),(9,30),(10,30),(11,30),(12,29);
/*!40000 ALTER TABLE `FMonArray` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `persons`
--

LOCK TABLES `persons` WRITE;
/*!40000 ALTER TABLE `persons` DISABLE KEYS */;
INSERT INTO `persons` VALUES (1,'امید','میلانی فرد','milanifard.o@gmail.com',NULL,1,'','');
/*!40000 ALTER TABLE `persons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `SystemFacilities`
--

LOCK TABLES `SystemFacilities` WRITE;
/*!40000 ALTER TABLE `SystemFacilities` DISABLE KEYS */;
INSERT INTO `SystemFacilities` VALUES (1,'مدیریت گروه منو',1,1,'ManageSystemFacilityGroups.php','Manage Menu Groups'),(2,'مدیریت منو',1,2,'ManageSystemFacilities.php', 'Manage Menues'),(3,'اطلاعات اشخاص',1,3,'Managepersons.php','Manage Persons'),(4,'مدیریت کاربران',1,4,'ManageAccountSpecs.php', 'Manage Users'),(5,'مدیریت هستان نگارها',3,1,'Manageontologies.php', 'Manage Ontologies'),(6,'مدیریت منابع استخراج واژگان',3,2,'ManageTermReferences.php', 'Manage Text Resources'),(7,'مدیریت واژگان استخراج شده',3,3,'Manageterms.php', 'Manage Extracted Terms'),(8,'مفهوم سازی واژگان',3,4,'TermFrequency.php','Terms Conceptualization'),(9,'مدیریت پروژه ها',2,1,'ManageProjects.php','Manage Projects'),(10,'کارتابل کار',2,2,'TasksKartable.php','Tasks Dashboard'),(11,'جستجوی کار',2,3,'ManageProjectTasks.php','Search Tasks'),(14,'مدیریت پیامهای عمومی',1,10,'Managemessages.php','Manage Public Messages'),(15,'ارسال نامه',4,1,'SendMessage.php','Send Letter'),(16,'نامه های رسیده',4,2,'MailBox.php','Inbox'),(17,'نامه های ارسالی',4,3,'SentBox.php','Sent Box'),(18,'جستجوی نامه',4,4,'SearchMessage.php','Search Letters'),(19,'گزارش زمانی-مالی',5,1,'TimeReport.php','Time-Financial Report'),(20,'گزارش اقدامات من در پروژه',5,2,'MyTimeReport.php','My Actions Report'),(21,'مدیریت جلسات',6,1,'../SessionManagement/ManageUniversitySessions.php','Manage Meetings'),(22,'مدیریت الگوهای جلسه',6,0,'../SessionManagement/ManageSessionTypes.php','Manage Meeting Templates'),(23,'درخواستهای حضور در جلسه',6,2,'../SessionManagement/RequestedSessions.php','Meeting Requests'),(24,'صورتجلسات آماده امضا',6,3,'../SessionManagement/ReadyForSignSessions.php','Ready For Sign Meeting Result'),(25,'ایجاد پرسشنامه',7,1,'../FormsGenerator/NewQuestionnaire.php','Create Questionnaire'),(26,'مدیریت پرسشنامه ها',7,2,'../FormsGenerator/ManageQuestionnaires.php','Manage Questionnaire'),(27,'سابقه تغییر فهرست واژگان',3,10,'ShowTermsManipulationHistory.php','Terms Changing History'),(28,'سابقه تغییرات در ارجاعات واژگان',3,11,'ShowTermReferHistory.php','Terms Reference History'),(41,'مدیریت فراداده پایگاه داده',3,12,'../ManageInfo/ManageTables.php', 'Manage DB MetaData'),(42,'نگاشت هستان نگارها',3,1,'CompareOntologies.php','Ontology Mapping'),(43,'مقایسه هستان نگارها',5,10,'CompareAllOntos.php','Ontology Comparing'),(44,'ادغام هستان نگارها',3,1,'ManageOntologyMergeProject.php','Ontology Merging'),(45,'اجرای مهندسی معکوس پایگاه داده',3,14,'MetaData2Onto.php','DB Re-Engineering');
/*!40000 ALTER TABLE `SystemFacilities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `SystemFacilityGroups`
--

LOCK TABLES `SystemFacilityGroups` WRITE;
/*!40000 ALTER TABLE `SystemFacilityGroups` DISABLE KEYS */;
INSERT INTO `SystemFacilityGroups` VALUES (1,'مدیریت','Administration',1),(2,'مدیریت پروژه','Project Management', 2),(3,'هستان نگار','Ontology',5),(4,'مکاتبات','Communication',4),(5,'گزارشات','Report',10),(6,'مدیریت جلسات','Session Management',6),(7,'پرسشنامه الکترونیکی','e-questionnaire',7);
/*!40000 ALTER TABLE `SystemFacilityGroups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `UserFacilities`
--

LOCK TABLES `UserFacilities` WRITE;
/*!40000 ALTER TABLE `UserFacilities` DISABLE KEYS */;
INSERT INTO projectmanagement.`UserFacilities` VALUES (2,'omid',1),(3,'omid',2),(4,'omid',3),(5,'omid',4),(6,'omid',14),(7,'omid',5),(8,'omid',42),(9,'omid',6),(10,'omid',7),(11,'omid',8),(12,'omid',27),(13,'omid',28),(14,'omid',41),(15,'omid',19),(16,'omid',20),(17,'omid',43),(18,'omid',44),(19,'omid',45),(20,'omid',9),(21,'omid',10),(22,'omid',11),(23,'omid',15),(24,'omid',16),(25,'omid',17),(26,'omid',18),(27,'omid',21),(28,'omid',21),(29,'omid',22),(30,'omid',23),(31,'omid',24),(32,'omid',25),(33,'omid',26),(34,'omid',46);
/*!40000 ALTER TABLE `UserFacilities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `SpecialPages`
--

LOCK TABLES `SpecialPages` WRITE;
/*!40000 ALTER TABLE `SpecialPages` DISABLE KEYS */;
INSERT INTO `SpecialPages` VALUES (1,'/main.php'),(2,'/ManagerDesktop.php'),(3,'/HomePage.php'),(4,'/ChangePassword.php'),(5,'/Menu.php'),(6,'/MyActions.phpp'),(7,'/MyActions.php'),(8,'/DownloadFile.php'),(9,'/FillQuestionnaire.php'),(10,'/ViewQuestionnaire.php'),(11,'/ShowQuestionnaireDetailTable.php'),(12,'/PrintQuestionnaireData.php'),(13,'/ShowPersonPhoto.php');
/*!40000 ALTER TABLE `SpecialPages` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-09-02 17:17:35

CREATE TABLE `projectmanagement`.`org_units` (
  `ouid` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `ptitle` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`ouid`)
)
ENGINE = InnoDB
COMMENT = 'واحد سازمانی';

INSERT INTO `org_units` VALUES ('1', 'مدیریت');

ALTER TABLE `projectmanagement`.`projecttasks` MODIFY COLUMN `UpdateReason` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_persian_ci NOT NULL DEFAULT '-' COMMENT 'دلیل بروزرسانی';

ALTER TABLE `projectmanagement`.`projecttasks` MODIFY COLUMN `DoneDate` DATETIME NOT NULL DEFAULT '0000-00-00' COMMENT 'زمان پایان کار - اقدام';

ALTER TABLE `projectmanagement`.`projecttasks` MODIFY COLUMN `EstimatedStartTime` DATETIME NOT NULL DEFAULT '0000-00-00' COMMENT 'زمان تخمینی شروع',
 MODIFY COLUMN `RealStartTime` DATETIME NOT NULL DEFAULT '0000-00-00' COMMENT 'زمان واقعی شروع',
 MODIFY COLUMN `EstimatedRequiredTimeDay` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'زمان مورد نیاز - روز',
 MODIFY COLUMN `EstimatedRequiredTimeHour` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'زمان مورد نیاز - ساعت',
 MODIFY COLUMN `EstimatedRequitedTimeMin` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'زمان مورد نیاز - دقیقه',
 MODIFY COLUMN `ExpireTime` DATETIME NOT NULL DEFAULT '0000-00-00' COMMENT 'مهلت اقدام',
 MODIFY COLUMN `TaskPeriority` INT(5) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'اولویت',
 MODIFY COLUMN `ProgramLevelID` INT(11) NOT NULL DEFAULT 0 COMMENT 'مرحله از برنامه اصلی',
 MODIFY COLUMN `TaskGroupID` INT(11) NOT NULL DEFAULT 0 COMMENT 'گروه کار',
 MODIFY COLUMN `ControllerID` INT(11) NOT NULL DEFAULT 0 COMMENT 'کاربر کنترل کننده';

 CREATE TABLE `projectmanagement`.`ProjectTaskRefers` (
  `ReferID` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `RelatedRefer` INTEGER UNSIGNED NOT NULL,
  `Status` VARCHAR(45) NOT NULL,
  `TaskID` VARCHAR(45) NOT NULL,
  `Title` VARCHAR(255) NOT NULL,
  `ToPerson` INTEGER UNSIGNED NOT NULL,
  `ToPersonType` INTEGER UNSIGNED NOT NULL,
  `DateTime` DATETIME NOT NULL,
  `Deadline` DATETIME NOT NULL,
  `Description` VARCHAR(4000) NOT NULL,
  `FromPerson` INTEGER UNSIGNED NOT NULL,
  `FromPersonType` INTEGER UNSIGNED NOT NULL,
  `IsHidden` ENUM('YES','NO') NOT NULL DEFAULT 'NO',
  `Priprity` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`ReferID`)
)
ENGINE = InnoDB
COMMENT = 'ارجاعات کارها';


DROP TABLE IF EXISTS `mis`.`MIS_TableChangeLog`;
CREATE TABLE  `mis`.`MIS_TableChangeLog` (
  `MIS_TableChangeLogID` int(11) NOT NULL AUTO_INCREMENT,
  `DBName` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL COMMENT '???????',
  `TableName` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL COMMENT '????',
  `ChangeType` enum('ADD','REMOVE','UPDATE') COLLATE utf8_persian_ci DEFAULT NULL COMMENT '??? ?????',
  `ChangeDate` datetime DEFAULT NULL COMMENT '????',
  PRIMARY KEY (`MIS_TableChangeLogID`)
) ENGINE=InnoDB AUTO_INCREMENT=2327 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='????? ??????? ?????? ?????';

DROP TABLE IF EXISTS `mis`.`MIS_SuggestedFK`;
CREATE TABLE  `mis`.`MIS_SuggestedFK` (
  `MIS_SuggestedFKID` int(11) NOT NULL AUTO_INCREMENT,
  `PKTableName` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL COMMENT '???? ???? ????',
  `PKFieldName` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL,
  `FKTableName` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL,
  `FKFieldName` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL,
  `status` enum('ACCEPT','REJECT','UNDECIDE') COLLATE utf8_persian_ci DEFAULT 'UNDECIDE',
  `PKDBName` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL,
  `FKDBName` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL,
  PRIMARY KEY (`MIS_SuggestedFKID`),
  KEY `idx` (`PKTableName`,`PKFieldName`,`FKTableName`,`FKFieldName`)
) ENGINE=InnoDB AUTO_INCREMENT=34747 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

DROP TABLE IF EXISTS `mis`.`MIS_CodingTables`;
CREATE TABLE  `mis`.`MIS_CodingTables` (
  `MIS_CodingTablesID` int(11) NOT NULL AUTO_INCREMENT,
  `DBName` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL COMMENT '?????? ????',
  `TableName` varchar(145) COLLATE utf8_persian_ci DEFAULT NULL COMMENT '????',
  `CodeFieldName` varchar(145) COLLATE utf8_persian_ci DEFAULT NULL COMMENT '??? ???? ???? ??',
  `DescriptionFieldName` varchar(145) COLLATE utf8_persian_ci DEFAULT NULL COMMENT '??? ???? ???? ???',
  PRIMARY KEY (`MIS_CodingTablesID`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='????? ???? ???? ? ??? ????';

DROP TABLE IF EXISTS `mis`.`EnumFieldDescription`;
CREATE TABLE  `mis`.`EnumFieldDescription` (
  `EnumFieldDescriptionID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FieldName` varchar(200) COLLATE utf8_persian_ci NOT NULL COMMENT '??? ????',
  `FieldValue` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT '????? ????',
  `FieldDescription` varchar(300) COLLATE utf8_persian_ci NOT NULL COMMENT '??? ????',
  PRIMARY KEY (`EnumFieldDescriptionID`)
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='??? ?????? ??????? ??????';

DROP TABLE IF EXISTS `mis`.`FieldsDataMapping`;
CREATE TABLE  `mis`.`FieldsDataMapping` (
  `FieldsDataMappingID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `DBName` varchar(50) COLLATE utf8_persian_ci NOT NULL COMMENT '??? ???????',
  `TableName` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT '??? ????',
  `FieldName` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT '??? ????',
  `ActualValue` varchar(100) COLLATE utf8_persian_ci NOT NULL COMMENT '????? ????? ????? ??? ?? ????',
  `ShowValue` varchar(255) COLLATE utf8_persian_ci NOT NULL COMMENT '?????? ?? ???? ?? ????? ????? ???? ???',
  PRIMARY KEY (`FieldsDataMappingID`)
) ENGINE=InnoDB AUTO_INCREMENT=3625 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='???? ????????? ???? ??? ?? ????';

DROP TABLE IF EXISTS `mis`.`DatabaseInfo`;
CREATE TABLE  `mis`.`DatabaseInfo` (
  `DBName` varchar(50) COLLATE utf8_persian_ci NOT NULL,
  `TableName` varchar(50) COLLATE utf8_persian_ci NOT NULL,
  `FieldName` varchar(50) COLLATE utf8_persian_ci NOT NULL,
  `FieldType` varchar(20) COLLATE utf8_persian_ci NOT NULL,
  `SysFlag` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`DBName`,`TableName`,`FieldName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='??????? ?????? ????????';

DROP TABLE IF EXISTS `mis`.`MIS_RejectedFK`;
CREATE TABLE  `mis`.`MIS_RejectedFK` (
  `MIS_RejectedFKID` int(11) NOT NULL AUTO_INCREMENT,
  `PKTableName` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL COMMENT '???? ???? ????',
  `PKFieldName` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL,
  `FKTableName` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL,
  `FKFieldName` varchar(45) COLLATE utf8_persian_ci DEFAULT NULL,
  PRIMARY KEY (`MIS_RejectedFKID`),
  KEY `idx` (`PKTableName`,`PKFieldName`,`FKTableName`,`FKFieldName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='????????? ?? ??? ???? ??????? ?????';

DROP TABLE IF EXISTS `mis`.`MIS_TableFieldsChangeLog`;
CREATE TABLE  `mis`.`MIS_TableFieldsChangeLog` (
  `MIS_TableFieldsChangeLogID` int(11) NOT NULL AUTO_INCREMENT,
  `DBName` varchar(50) COLLATE utf8_persian_ci DEFAULT NULL COMMENT '???????',
  `TableName` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL COMMENT '????',
  `FieldName` varchar(100) COLLATE utf8_persian_ci DEFAULT NULL COMMENT '????',
  `ChangeType` enum('ADD','REMOVE','UPDATE') COLLATE utf8_persian_ci DEFAULT NULL COMMENT '??? ?????',
  `ChangeDate` datetime DEFAULT NULL COMMENT '???? ?????',
  PRIMARY KEY (`MIS_TableFieldsChangeLogID`)
) ENGINE=InnoDB AUTO_INCREMENT=23766 DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci COMMENT='????? ??????? ??????? ???? ?????';

DROP TABLE IF EXISTS `mis`.`PageContent`;
CREATE TABLE  `mis`.`PageContent` (
  `PageID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_persian_ci DEFAULT '',
  `server` enum('SADAF','POOYA','MPOOYA','MIS','FUMSERVICES','UNVPORTAL') CHARACTER SET utf8 DEFAULT NULL COMMENT '''MIS'', ''FUMSERVICES'', ''UNVPORTAL''\n',
  `path` varchar(200) COLLATE utf8_persian_ci DEFAULT '',
  `type` enum('php','inc') COLLATE utf8_persian_ci DEFAULT NULL,
  `content` text COLLATE utf8_persian_ci,
  `StaticContent` text COLLATE utf8_persian_ci,
  `LastModifyDate` datetime DEFAULT NULL,
  `ModifierUserID` varchar(30) COLLATE utf8_persian_ci DEFAULT NULL,
  `LinesCount` int(11) DEFAULT '-1',
  `ATS` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`PageID`),
  KEY `name_index` (`name`),
  KEY `path_index` (`path`),
  KEY `type_index` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;