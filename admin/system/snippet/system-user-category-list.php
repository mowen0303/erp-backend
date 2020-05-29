<?php
try {
    global $userModel;
    $userModel->isCurrentUserHasAuthority('SYSTEM_SETTING','USER_CATEGORY') or Helper::throwException(null,403);
    $arr = $userModel->getUserCategories();
} catch (Exception $e) {
    Helper::echoJson($e->getCode(), $e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">User Authority</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(1);?>
        <a href="index.php?s=system-user-category-form" class="btn btn-danger pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i class="fas fa-plus-circle"></i> Create User Category</a>
    </label>
</div>
<!--header end-->

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h3 class="box-title m-b-0">User Category</h3>
            <p class="text-muted m-b-30 font-13"> All user category list </p>
            <form action="/restAPI/userController.php?action=deleteUserCategoryByIds" method="post">
            <div class="table-responsive">
                <table class="table table-hover color-table dark-table">
                    <thead>
                    <tr>
                        <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                        <th>#</th>
                        <th>LEVEL</th>
                        <th>CATEGORY TITLE</th>
                        <th width="50"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($arr as $row) {
                    ?>
                        <tr>
                            <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['user_category_id']?>"></td>
                            <td><?php echo $row['user_category_id']?></td>
                            <td><div class="label label-table label-info">Level <?php echo $row['user_category_level']?></div></td>
                            <td><?php echo $row['user_category_title']?></td>
                            <td>
                                <a  href="/admin/system/index.php?s=system-user-category-form&userCategoryId=<?php echo $row['user_category_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Edit"><i class="ti-marker-alt"></i></a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-sm-8"></div>
                    <div class="col-sm-4 text-right">
                        <button id="deleteBtn" style="display: none" type="submit" class="btn btn-info waves-effect waves-light m-t-10" onclick="return confirm('Are you sure to delete?')">Delete</button>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>