<?php
try {
    global $userModel;
    $userModel->isCurrentUserHasAuthority('SYSTEM_SETTING','USER_CATEGORY') or Helper::throwException(null,403);
    $userCategoryId = Helper::get('userCategoryId');
    if($userCategoryId){
        $row = $userModel->getUserCategoryById($userCategoryId);
    }
    $flag = $row?'Edit':'Add';
    $authority = $row ? json_decode($row['user_category_authority'], true) : null;
} catch (Exception $e) {
    Helper::echoJson($e->getCode(), $e->getMessage(),null,null,null,'/adminPSCMS/user/index.php?s=listCategory');
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">User Authority / <?=$flag?></h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn();?>
    </label>
</div>
<!--header end-->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading"><?=$flag?> User Category</div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form class="form-horizontal" action="/restAPI/userController.php?action=modifyUserCategory" method="post">
                        <input name="user_category_id" value="<?php echo $userCategoryId ?>" type="hidden">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Category Title*</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="user_category_title" value="<?php echo $row['user_category_title'] ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Category Level*</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="user_category_level" value="<?php echo $row['user_category_level'] ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Authority*</label>
                            <div class="col-sm-9">
                                <div class="authorityBox">
                                    <?php
                                    global $_AUT;
                                    $AUT_KEY = "";
                                    foreach ($_AUT as $groupKey => $groupValue) {
                                        $AUT_KEY .= ($groupKey . ',');
                                        echo '<div class="col-md-4 col-sm-6 col-xs-12">';
                                        echo "<h4>{$groupKey}</h4>";
                                        foreach ($groupValue as $key => $value) {
                                            ?>
                                            <P><input name="AUT_<?php echo $groupKey ?>[]"
                                                      value="<?php echo $value ?>" <?php echo $authority[$groupKey] & $value ? 'checked' : null ?>
                                                      type="checkbox"> <?php echo $key ?></P>
                                            <?php
                                        }
                                        echo '</div>';
                                    }
                                    ?>
                                    <input name="AUT_KEY" value="<?php echo $AUT_KEY ?>" type="hidden">
                                </div>
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
