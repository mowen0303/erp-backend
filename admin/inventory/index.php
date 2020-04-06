<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/_template/header.php";
    Helper::loadSnippet($_GET[s],'WAREHOUSE','warehouse-list');
    require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/_template/footer.php";
?>
