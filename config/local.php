<?php

/**
HOST
*/
define('HOST', 'localhost');
define('DOMAIN', 'tira.com');
define('BASEURL', 'tira.com');
define('SITE_NAME', 'tira.com');

/**
SYS
*/
define('SEND_MAIL', true);
define('LEGACY_FS', true);
define('SYS_UID', '1007');
define('SYS_GID', '1007');
//define('ANTIVIRCMD','clamscan --quiet --remove=yes --max-filesize=10M'); // -scan-html[=yes --scan-archive[=yes --scan-ole2=yes --scan-pdf=yes --recursive --quiet --infected (Only print infected files)

/**
NET
*/
define('COMMANDLETS_HOST', '192.168.1.109');
define('COMMANDLETS_PORT', '50008');
define('COMMANDLETS_SECRETHASH', '');

/**
DB
*/

define('USEPOSTGRESQL',true);
if (USEPOSTGRESQL === true)
    define('SQLQT','"');
else
    define('SQLQT','`');

define('MYSQL_PERSISTENT', false); // mysqli host = "p:host" (mysqlnd fearture(bsd/win std), problem on libmysql(std for debian/centos)

/**
MEDIA
*/
define('IMAGE_CONVERTER', 'gm'); // GraphicsMagick
define('JPEG_QUALITY', 100);

/**
SHOP
*/
// начальный депозит при регистрации (бонус)
define('INITIAL_DEPOSIT', 0); # UAH
define('INITIAL_BONUS', 0);

/**
CACHE
*/
define('ENABLE_CACHE', false);

/**
LIST DB
*/
define('ENABLE_LISTDB', false);

/**
ENV
*/
define('ENV', 'DEVELOPMENT');
//define('ENV', 'PRODUCTION');
 define('LOG_ENV', 'DEVELOPMENT');
// define('LOG_ENV', 'PRODUCTION');

//require __DIR__."env.php";

/**
DEBUG
*/
define('DEBUG_LISTDB', true);
//define('DEBUG_SQL', true);
//define('DEBUG_CACHE', true);
//define('DEBUG_IMAGE_LOGIC', true);

/**
PAYMENTS
*/


define('SIMPLEPASSWORDS', true);


define('FS_MODE_DIR', '0755');
define('FS_MODE_FILE', '0644');

// define('REGISTER_USE_CAPTCHA', false);


define('STANDARTFORMREGISTERDISABLE', true);

/**
TIME
*/
//date_default_timezone_set('Europe/Moscow');
date_default_timezone_set('Europe/Kiev');

/**
ACCESS
*/
define('ROOT_PASS', 'wb2015');

define('EMAILORDERSTO','pasutavitaliy@gmail.com');

?>
