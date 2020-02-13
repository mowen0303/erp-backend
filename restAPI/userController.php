<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonServices/config.php";
/**
 * 登录
 */
function login(){
    try {
        $userModel = new \model\UserModel();
        $userModel->login();
        Helper::jumpTo('/admin/adminIndex.php');
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage());
    }
}

/**
 * 登出
 */
function logout() {
    try {
        $userModel = new \model\UserModel();
        $userModel->logout();
        if (Helper::isRequestFromCMS()) {
            Helper::jumpTo('/admin/adminLogin.php');
        } else {
            Helper::echoJson(200, 'OK');
        }
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage());
    }
}

function modifyUser() {
    try {
        $userModel = new \model\UserModel();
        $userId = (int) Helper::post('user_id');
        if($userId){
            //修改
            $userModel->isCurrentUserHasAuthority('USER', 'UPDATE', $userId) or Helper::throwException(null, 403);
            $userModel->modifyUser($userId);
        }else{
            //添加
            $userModel->isCurrentUserHasAuthority('USER', 'ADD') or Helper::throwException(null, 403);
            $userModel->modifyUser();
        }
        Helper::echoJson(200, "Success! {$userModel->imgError}", null, null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()} {$userModel->imgError}");
    }
}

function updatePassword() {
    try {
        $userModel = new \model\UserModel();
        $targetUserId = Helper::post('user_id','User Id can not be null');
        $userModel->validateCurrentUserHasAuthorityToManageTargetUser($targetUserId);
        $userModel->updatePassword($targetUserId);
        Helper::echoJson(200, 'Success! You have changed the user password!', null, null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage());
    }
}

function deleteUserByIds() {
    try {
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority("USER","DELETE") or Helper::throwException(null,403);
        $effectRows = $userModel->deleteUserByIds();
        Helper::echoJson(200, "{$effectRows} user(s) has been deleted", null, null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage());
    }
}



/**
 * ============================================
 * ============== User Category ===============
 * ============================================
 */

/**
 * 添加、更新用户分类
 */
function modifyUserCategory() {
    try {
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority('SYSTEM_SETTING', 'USER_CATEGORY') or Helper::throwException(null, 403);
        $userModel->modifyUserCategory();
        Helper::echoJson(200, "Success", null, null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage(), null, null, null);
    }
}

/**
 * `删除`用户分类（只有GOD可以操作）
 */
function deleteUserCategoryByIds() {
    try {
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority('SYSTEM_SETTING', 'USER_CATEGORY') or Helper::throwException(null, 403);
        $effectRows = $userModel->deleteUserCategoryByIds();
        Helper::echoJson(200, "Success : {$effectRows} rows data has been deleted", null, null, null, $_SESSION['back_url_1']);
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()}");
    }
}


?>
