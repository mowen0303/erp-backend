<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonServices/config.php";

/**
 * =================================================================
 * =================================================================
 * =================================================================
 * =================================================================
 * ==========================   Item  ==============================
 * =================================================================
 * =================================================================
 * =================================================================
 * =================================================================
 */

function modifyItem() {
    try {
        $userModel = new \model\UserModel();
        $itemModel = new \model\ItemModel();
        $itemId = (int) Helper::post('item_id');
        if($itemId){
            //修改
            $userModel->isCurrentUserHasAuthority('ITEM', 'UPDATE') or Helper::throwException(null, 403);
            $itemModel->modifyItem($itemId);
        }else{
            //添加
            $userModel->isCurrentUserHasAuthority('ITEM', 'ADD') or Helper::throwException(null, 403);
            $itemModel->modifyItem();
        }
        Helper::echoJson(200, "Success! {$itemModel->imgError}", null, null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()} {$itemModel->imgError}");
    }
}

/**
 * =================================================================
 * =================================================================
 * =================================================================
 * =================================================================
 * ======================   Item Category  =========================
 * =================================================================
 * =================================================================
 * =================================================================
 * =================================================================
 */

function modifyItemCategory() {
    try {
        $userModel = new \model\UserModel();
        $itemModel = new \model\ItemModel();
        $itemCategoryId = (int) Helper::post('item_category_id');
        if($itemCategoryId){
            //修改
            $userModel->isCurrentUserHasAuthority('ITEM', 'UPDATE') or Helper::throwException(null, 403);
            $itemModel->modifyItemCategory($itemCategoryId);
        }else{
            //添加
            $userModel->isCurrentUserHasAuthority('ITEM', 'ADD') or Helper::throwException(null, 403);
            $itemModel->modifyItemCategory();
        }
        Helper::echoJson(200, "Success! {$itemModel->imgError}", null, null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()} {$itemModel->imgError}");
    }
}

function deleteItemCategoryByIds() {
    try {
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority("ITEM","DELETE") or Helper::throwException(null,403);
        $itemController = new \model\ItemModel();
        $effectRows = $itemController->deleteItemCategoryByIds();
        Helper::echoJson(200, "{$effectRows} rows data has been deleted", null, null, null, $_SESSION['back_url_1']);
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage());
    }
}

/**
 * =================================================================
 * =================================================================
 * =================================================================
 * =================================================================
 * =======================   Item Style  ===========================
 * =================================================================
 * =================================================================
 * =================================================================
 * =================================================================
 */

function modifyItemStyle() {
    try {
        $userModel = new \model\UserModel();
        $itemModel = new \model\ItemModel();
        $itemStyleId = (int) Helper::post('item_style_id');
        if($itemStyleId){
            //修改
            $userModel->isCurrentUserHasAuthority('ITEM', 'UPDATE') or Helper::throwException(null, 403);
            $itemModel->modifyItemStyle($itemStyleId);
        }else{
            //添加
            $userModel->isCurrentUserHasAuthority('ITEM', 'ADD') or Helper::throwException(null, 403);
            $itemModel->modifyItemStyle();
        }
        Helper::echoJson(200, "Success! {$itemModel->imgError}", null, null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()} {$itemModel->imgError}");
    }
}

function deleteItemStyleByIds() {
    try {
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority("ITEM","DELETE") or Helper::throwException(null,403);
        $itemController = new \model\ItemModel();
        $effectRows = $itemController->deleteItemStyleByIds();
        Helper::echoJson(200, "{$effectRows} rows data has been deleted", null, null, null, $_SESSION['back_url_1']);
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage());
    }
}


?>
