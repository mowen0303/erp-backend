<?php
try {
    global $userModel;
    $user = $userModel->getProfileOfUserById($userModel->getCurrentUserId(),true);
    $company = $user;
    $companyLocation = $user;
    $profileTitle = "My Profile";
    $companyTitle = "My Company Info";
    $companyLocationTitle = "My Company Location Info";
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-xs-4">
        <h4 class="page-title">User / Profile</h4>
    </div>
    <div class="col-xs-8">
        <?php Helper::echoBackBtn(1);?>
    </div>
</div>
<!--header end-->
<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/user/snippet/user-info.php" ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/company/snippet/company-info.php" ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/company/snippet/company-location-info.php" ?>
