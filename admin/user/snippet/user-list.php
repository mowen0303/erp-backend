<?php
try {
    global $userModel;
    $userModel->isCurrentUserHasAuthority("USER","VIEW_OTHER") or Helper::throwException(null,403);
    $userArr = $userModel->getUsers([0]);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-md-4">
        <h4 class="page-title">User / All Users</h4>
    </div>
    <div class="col-md-8">
        <?php Helper::echoBackBtn(1);?>
        <a href="/admin/user/index.php?s=user-form" class="btn btn-danger pull-right"><i class="fas fa-plus-circle"></i>  Add User</a>
    </div>
</div>

<!--header end-->
<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h3 class="box-title">Search User</h3>
            <form class="" action="/admin/user/index.php?s=listUser" method="get">
                <input type="hidden" name="s" value="listUser">
                <input type="hidden" name="flag" value="search">
                <div class="row">
                    <div class="col-sm-2">
                        <select class="form-control" name="searchType" data-defvalue="<?php echo $searchType?>">
                            <option value="username" selected="selected">Username</option>
                            <option value="alias">Alias</option>
                        </select>
                    </div>
                    <div class="col-sm-8">
                        <input class="form-control" placeholder="" type="text" name="searchValue" value="<?php echo $searchValue?>">
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-block btn-info waves-effect waves-light" type="submit">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <div class="row m-b-20">
                <form action="/admin/user/index.php" method="get">
                    <input type="hidden" name="s" value="user-list">
                    <div class="col-sm-3">
                        <h3 class="box-title m-b-0">User List</h3>
                    </div>
                    <div class="col-sm-3 text-right">
                        <select name="userType" class="form-control" data-defvalue="<?php echo $userType ?>">
                            <option value="">All User</option>
                            <option value="deleted">Deleted User</option>
                        </select>
                    </div>
                    <div class="col-sm-2 text-right">
                        <select name="orderBy" class="form-control" data-defvalue="<?php echo $orderBy ?>">
                            <option value="user_register_time">Register Time</option>
                            <option value="user_last_login_time">Last Login Time</option>
                        </select>
                    </div>
                    <div class="col-sm-2 text-right">
                        <select name="orderTurn" class="form-control" data-defvalue="<?php echo $orderTurn ?>">
                            <option value="">Descending	order</option>
                            <option value="asc">Ascending order</option>
                        </select>
                    </div>
                    <div class="col-sm-2 text-right">
                        <button type="submit" class="btn btn-block btn-info waves-effect waves-light">Filter</button>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <form action="/restAPI/userController.php?action=deleteUserByIds" method="post">
                    <table class="table color-table dark-table table-hover">
                        <thead>
                        <tr>
                            <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                            <th>#</th>
                            <th>AVATAR</th>
                            <th>EMAIL</th>
                            <th>ALIAS</th>
                            <th>Status</th>
                            <th>Register Time</th>
                            <th>Last Login Time</th>
                            <th>MANAGE</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($userArr as $row) {
                            ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="cBox" name="id[]" value="<?=$row['user_id']?>">
                                </td>
                                <td><?php echo $row['user_id'] ?></td>
                                <td><img width="36" height="36" src="<?php echo $row['user_avatar'] ?>"></td>
                                <td><a href="/admin/user/index.php?s=user-list-profile&userId=<?=$row['user_id']?>"><?=$row['user_email'] ?></a></td>
                                <td><?=$row['user_alias'] ?></td>
                                <td><?=$row['user_status']==1?'<span class="label label-success">normal</span>':'<span class="label label-danger">Deleted</span>' ?></td>
                                <td><?=$row['user_register_time']?></td>
                                <td><?=$row['user_last_login_time']?></td>
                                <td>
                                    <a href="index.php?s=user-form&uid=<?=$row['user_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Edit"><i class="ti-marker-alt"></i></a>
                                    <a href="index.php?s=formPwd&uid=<?=$row['user_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Change Password"><i class="ti-key"></i></a>
                                    <a  onclick="return confirm('Are you sure to delete?')" href="/restAPI/userController.php?action=deleteUserByIds&id=<?php echo $row['user_category_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Delete"><i class="ti-trash"></i></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-8"><?=$userModel->echoPageList()?></div>
                        <div class="col-sm-4 text-right">
                            <button id="deleteBtn" style="display: none" type="submit" class="btn btn-info waves-effect waves-light m-t-10" onclick="return confirm('Are you sure to delete?')">Delete</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>