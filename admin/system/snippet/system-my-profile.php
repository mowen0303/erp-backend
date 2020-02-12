<?php
try {
    global $userModel;
    $user = $userModel->getProfileOfUserById($userModel->getCurrentUserId(),true);
    $company = $user;
    $store = $user;
    $profileTitle = "My Profile";
    $companyTitle = "My Company Info";
    $storeTitle = "My Store Info";
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-md-4">
        <h4 class="page-title">User / Profile</h4>
    </div>
    <div class="col-md-8">
        <?php Helper::echoBackBtn(1);?>
    </div>
</div>
<!--header end-->
<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/user/snippet/user-info.php" ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/company/snippet/company-info.php" ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/company/snippet/store-info.php" ?>
