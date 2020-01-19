#!/bin/bash

DB_NAME="ontirandoc"
DB_USERNAME="root"
DB_PASSWORD="fumcloud"
DB_HOST="172.240.0.2"
DEPLOY_PATH="/var/www/html/"

LANG="EN"

unrar x ferdowsnet.rar
mysql -u"$DB_USERNAME" -p"$DB_PASSWORD" -h"$DB_HOST" < ontirandoc_database.sql
mkdir -p $DEPLOY_PATH
cp -R ./www/* $DEPLOY_PATH

#Change config.class.php file based on your environment
sed -i "s/'host'.*$/'host' => '${DB_HOST}',/" "${DEPLOY_PATH}shares/config.class.php"
sed -i "s/\"lab_user\".*$/\"lab_user\" => '${DB_USERNAME}',/" "${DEPLOY_PATH}shares/config.class.php"
sed -i "s/\"dataanalysis_user\".*$/\"dataanalysis_user\" => '${DB_USERNAME}',/" "${DEPLOY_PATH}shares/config.class.php"
sed -i "s/\"formsgenerator_user\".*$/\"formsgenerator_user\" => '${DB_USERNAME}',/" "${DEPLOY_PATH}shares/config.class.php"
sed -i "s/\"lab_pass\".*$/\"lab_pass\" => '${DB_PASSWORD}',/" "${DEPLOY_PATH}shares/config.class.php"
sed -i "s/\"dataanalysis_pass\".*$/\"dataanalysis_pass\" => '${DB_PASSWORD}',/" "${DEPLOY_PATH}shares/config.class.php"
sed -i "s/\"formsgenerator_pass\".*$/\"formsgenerator_pass\" => '${DB_PASSWORD}',/" "${DEPLOY_PATH}shares/config.class.php"

#Change MySql.config.php file based on your environment
sed -i "s/MYSQL_NAME.*$/MYSQL_NAME', '${DB_HOST}');/" "${DEPLOY_PATH}shares/MySql.config.php"
sed -i "s/MYSQL_USERNAME.*$/MYSQL_USERNAME', '${DB_USERNAME}');/" "${DEPLOY_PATH}shares/MySql.config.php"
sed -i "s/MYSQL_PASSWORD.*$/MYSQL_PASSWORD', '${DB_PASSWORD}');/" "${DEPLOY_PATH}shares/MySql.config.php"

#Change .htaccess file based on your environment
# mghayour: i added relative path. it does not need anymore
# sed -i "s%include_path.*$%include_path \".:${DEPLOY_PATH}adodb:${DEPLOY_PATH}shares:${DEPLOY_PATH}sharedClasses\"%" "${DEPLOY_PATH}pm/.htaccess"
# sed -i "s%include_path.*$%include_path \".:${DEPLOY_PATH}adodb:${DEPLOY_PATH}shares:${DEPLOY_PATH}sharedClasses\"%" "${DEPLOY_PATH}ManageInfo/.htaccess"

#Change definitions.php file based on your environment
sed -i "s:define(\"UI_LANGUAGE.*$:define(\"UI_LANGUAGE\", \"$LANG\");:" "${DEPLOY_PATH}shares/definitions.php"
