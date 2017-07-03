<?php

/**
DB/ENTITY
*/
define('FORCE32BIT', true);

/**
NEW USER MODEL (with OAuth 2.0)
*/
define('NEWUSERMODEL', true);
define('LEGACY_ENTITY_CONFIGS_ASPHPSRC', false);
define('SECURITY_SALT_STATIC', '123456789');

/**
Error reporting
*/
ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
//error_reporting(E_ALL ^ E_NOTICE); // warnings included

/**
Core constants
*/
define('DEBUG', false);
define('MEDIA_TRANSCODING', true);
define('AUDIO_ENABLED', false);
define('VIDEO_ENABLED', true);
define('DOCUMENT_ENABLED', false);
define('ANY_AS_ATTACH', true); 

define('MAXVIDEOTIME', '0:1:0'); // 1 minute

//размер для видео в формате 4:3
define('VIDEO_SIZE_STD', '320x240');
//размер для видео в формате 16:9
define('VIDEO_SIZE_WIDE', '460x260');
//для дев режима конвертировать только первые 5 секунд всех видео
// define('TEST_VIDEO_MODE_5SEC', true);

/**
Lang
*/
mb_internal_encoding("UTF-8");	
setlocale(LC_ALL, 'ru_RU');
define('DEFAULT_LANG', 'ru');

/**
CACHE
*/
define('cacheAdminSelectors', false);
define('DATAHISTORYTOCACHE', 3600*24*30);

/**
PROJECT PARAMS
*/
#define('ZFS_ROOT','/files'); # default. Tank provides it
# define('INITIAL_DEPOSIT', 100); # RUR // MOVE TO local.php
define('COPY_SKEL', true); // siteroot/skel/*
define('SNAPSHOTS_LIMIT', 3); # Affects only on new users
define('USE_NGINX_INTERNAL_REDIRECT', false);
define('USE_NGINX_UPLOAD_MODULE', false);


define('RELATED_WIDGET_MORPH_ON', 3);


define('CURRSYM', 'грн');

define('GLOBAL_PER_PAGE', 50);

define('taggedEntities', 'news post history library press');

define('REGISTER_USE_CAPTCHA', false);
define('RECAPTCHA_PUBLIC', '6LcZRNkSAAAAAEyN9eLsCCDs_Q_j1epUEokBt_mo');
define('RECAPTCHA_PRIVATE', '6LcZRNkSAAAAAGCXVrTN4gpILiSMVsfsWZRk-hE0');



?>