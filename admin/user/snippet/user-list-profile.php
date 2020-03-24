<?php
try {
    global $userModel;
    $userModel->isCurrentUserHasAuthority("USER","VIEW_OTHER") or Helper::throwException(null,403);
    $userId = (int) Helper::get('userId',"User Id can not be null");
    $user = $userModel->getProfileOfUserById($userId,true);
    $company = $user;
    $store = $user;
    $profileTitle = "User Profile";
    $companyTitle = "User Company Info";
    $storeTitle = "User Store Info";
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
        <?php Helper::echoBackBtn(2);?>
    </div>
</div>
<!--header end-->
<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/user/snippet/user-info.php" ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/company/snippet/company-info.php" ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/company/snippet/store-info.php" ?>
