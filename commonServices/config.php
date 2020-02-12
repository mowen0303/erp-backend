<?php
session_start();
@header("Content-type:text/html; charset=utf-8");
@header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
@header('Access-Control-Allow-Credentials: true');
error_reporting(E_ALL^E_WARNING^E_NOTICE^E_STRICT);
date_default_timezone_set("America/Toronto");
$dbInfo = array('host'=>'localhost','user'=>'root','password'=>'','database'=>'erp');
const DEV_MODEL = true;
const USER_PK = 'pss';
require_once $_SERVER['DOCUMENT_ROOT']."/commonServices/SqlTool.php";
require_once $_SERVER['DOCUMENT_ROOT']."/commonServices/Helper.php";
require_once $_SERVER['DOCUMENT_ROOT']."/commonServices/Model.php";
require_once $_SERVER['DOCUMENT_ROOT']."/commonServices/authority.php";
spl_autoload_register(function($name){$name = str_replace("\\","/",$name);include_once $_SERVER['DOCUMENT_ROOT']."/{$name}.php";});
call_user_func(@$_GET['action']);
?>