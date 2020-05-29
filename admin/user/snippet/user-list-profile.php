<?php
try {
    global $userModel;
    $userModel->isCurrentUserHasAuthority("USER","VIEW_OTHER") or Helper::throwException(null,403);
    $userId = (int) Helper::get('userId',"User Id can not be null");
    $user = $userModel->getProfileOfUserById($userId,true);
    $company = $user;
    $companyLocation = $user;
    $profileTitle = "User Profile";
    $companyTitle = "User Company Info";
    $companyLocationTitle = "User Company Location Info";
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">User / Profile</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(2);?>
    </label>
</div>
<!--header end-->
<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/user/snippet/user-info.php" ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/company/snippet/company-info.php" ?>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/company/snippet/company-location-info.php" ?>
