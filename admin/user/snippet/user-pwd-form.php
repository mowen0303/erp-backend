<?php
try {
    global $userModel;
    $targetUserId = (int) Helper::get('uid','User Id can not be null');
    $userModel->validateCurrentUserHasAuthorityToManageTargetUser($targetUserId);
    $isAdminManage = (int) $userModel->isCurrentUserHasAuthority("USER","UPDATE");
} catch (Exception $e) {
    Helper::echoJson(0, $e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">User / Password</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn();?>
    </label>
</div>
<!--header end-->

<!--header end-->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading">Update Password</div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form class="form-horizontal" action="/restAPI/userController.php?action=updatePassword" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="user_id" value="<?=$targetUserId?>">
                        <input type="hidden" name="isAdminManage" value="<?=$isAdminManage?>">
                        <?php if($isAdminManage != 1) {?>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Old password *</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" name="pw0" value="">
                                <span class="help-block"><small>Type your current password</small></span>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">New password *</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" name="pw1" value="">
                                <span class="help-block"><small>Make sure It's at least 6 characters</small></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Confirm new password *</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" name="pw2" value="">
                                <span class="help-block"><small>Retype your new password</small></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-info waves-effect waves-light m-t-10">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>