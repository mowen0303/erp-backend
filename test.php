<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonServices/config.php";

Helper::mailTo('mowen0303@gmail.com',"New register application - ".Helper::post('register_email'),"<p>Please login to review the application ERP system</p>",true);

?>
