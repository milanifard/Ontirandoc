# Ontirandoc
A web-based ontology development tools

Installation guide:
0- extract ontirandoc_database.rar, wordnet.rar, ferdowsnet.rar
1- create database (using ontirandoc_database.sql)
2- populate wordnet and ferdowsnet databases (using wordnet.sql and ferdowsnet.sql)
3- copy web files in wwwroot
4- set database username and password in shares/config.class.php and Mysql.config.php
5- set include_path in .htacess in "pm" and "ManageInfo" folders
6- you can change "UI_LANGUAGE" constant in shares/definitions.php to select Persian or English user interface 
6- use login.php page for login (default username: omid, password: omid3000)

** This software has a persian user manual: OntirandocUserManual.docx

