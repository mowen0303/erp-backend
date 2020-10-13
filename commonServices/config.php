<?php
require_once $_SERVER['DOCUMENT_ROOT']."/commonServices/serverKey.php";
session_start();
@header("Content-type:text/html; charset=utf-8");
@header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
@header('Access-Control-Allow-Credentials: true');
error_reporting(E_ALL^E_WARNING^E_NOTICE^E_STRICT);
date_default_timezone_set("America/Toronto");
$dbInfo = array('host'=>SERVER_KEY['db_host'],'user'=>SERVER_KEY['db_user'],'password'=>SERVER_KEY['db_password'],'database'=>SERVER_KEY['db_database']);
const DEV_MODEL = true;
const USER_PK = 'pss';
const UPLOAD_FOLDER = "/upload";
const NO_IMG = "/admin/resource/img/noimg.png";
const INVENTORY_LEVEL_1 = 100;
const INVENTORY_LEVEL_2 = 50;
const INVENTORY_LEVEL_3 = 25;
require_once $_SERVER['DOCUMENT_ROOT']."/commonServices/SqlTool.php";
require_once $_SERVER['DOCUMENT_ROOT']."/commonServices/Helper.php";
require_once $_SERVER['DOCUMENT_ROOT']."/commonServices/Model.php";
require_once $_SERVER['DOCUMENT_ROOT']."/commonServices/authority.php";
spl_autoload_register(function($name){$name = str_replace("\\","/",$name);include_once $_SERVER['DOCUMENT_ROOT']."/{$name}.php";});
call_user_func(@$_GET['action']);
?>