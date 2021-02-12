<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/_template/header.php"?>
<?php
try {
    global $userModel;
} catch (Exception $e) {
    Helper::echoJson(0, $e->getMessage());
    die();
}
?>

    <!--header start-->
    <div class="row bg-title">
        <div class="col-sm-4">
            <h4 class="page-title">Dashboard</h4>
        </div>
        <label class="col-sm-8 control-label">
        </label>
    </div>
    <!--header end-->



<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/_template/footer.php"?>