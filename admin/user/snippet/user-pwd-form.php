<?php
try {
    global $userModel;
    $targetUserId = (int) get('uid');
    $user = $userModel->getProfileOfUserById($targetUserId);
    $userModel->isCurrentUserHasAuthorityAdvanced("USER","UPDATE",$user['user_school_id'],$targetUserId) or Helper::throwException("Have no permission",403);
    $currentUserId = $userModel->getCurrentUserId();
} catch (Exception $e) {
    Helper::echoJson(0, $e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-md-4">
        <h4 class="page-title">修改密码</h4>
    </div>
    <div class="col-md-8">

    </div>
</div>
<!--header end-->
<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h3 class="box-title m-b-0"><?php echo $userId?'Edit':'Add' ?> User</h3>
            <p class="text-muted m-b-30 font-13">User management </p>
            <form class="form-horizontal" action="/restAPI/userController.php?action=updateUserPasswordByAdmin" method="post" enctype="multipart/form-data">
                <input type="hidden" name="targetUserId" value="<?php echo $targetUserId?>">
                <div class="form-group">
                    <label class="col-sm-4 control-label">新密码</label>
                    <div class="col-sm-4">
                        <input type="text" name="pwd" value="" class="form-control" placeholder="">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-4 col-sm-10">
                        <button type="submit" class="btn btn-info waves-effect waves-light m-t-10">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>