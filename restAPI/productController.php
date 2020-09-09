<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonServices/config.php";

/**
 * =================================================================
 * =================================================================
 * =================================================================
 * =================================================================
 * =====================   Product Category  =======================
 * =================================================================
 * =================================================================
 * =================================================================
 * =================================================================
 */

function modifyProductCategory() {
    try {
        $userModel = new \model\UserModel();
        $productModel = new \model\ProductModel();
        $id = (int) Helper::post('product_category_id');
        if($id){
            //修改
            $userModel->isCurrentUserHasAuthorities([['PRODUCT_CATEGORY', 'UPDATE']]) or Helper::throwException(null, 403);
            $productModel->modifyProductCategory($id);
        }else{
            //添加
            $userModel->isCurrentUserHasAuthorities([['PRODUCT_CATEGORY', 'ADD']]) or Helper::throwException(null, 403);
            $productModel->modifyProductCategory();
        }
        Helper::echoJson(200, "Success! {$productModel->imgError}", null, null, null, Helper::echoBackBtn(0,true),'Back','/admin/item/index.php?s=item-list-form','Add a new item');
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()} {$productModel->imgError}");
    }
}


/**
 * =================================================================
 * =================================================================
 * =================================================================
 * =================================================================
 * =====================   Product Category  =======================
 * =================================================================
 * =================================================================
 * =================================================================
 * =================================================================
 */
function modifyProduct() {
    try {
        $userModel = new \model\UserModel();
        $productModel = new \model\ProductModel();
        $productId = (int) Helper::post('product_id');
        if($productId){
            //修改
            $userModel->isCurrentUserHasAuthority('PRODUCT', 'UPDATE') or Helper::throwException(null, 403);
            $productModel->modifyProduct($productId);
        }else{
            //添加
            $userModel->isCurrentUserHasAuthority('PRODUCT', 'ADD') or Helper::throwException(null, 403);
            $productModel->modifyProduct();
        }
        Helper::echoJson(200, "Success!", null, null, null, Helper::echoBackBtn(0,true),'Back','/admin/product/index.php?s=product-list-form','Add a new product');
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()} {$productModel->imgError}");
    }
}

function deleteProductByIds() {
    try {
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority("PRODUCT","DELETE") or Helper::throwException(null,403);
        $productModel = new \model\ProductModel();
        $effectRows = $productModel->deleteProductByIds();
        Helper::echoJson(200, "{$effectRows} rows data has been deleted", null, null, null, $_SESSION['back_url_1']);
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage());
    }
}


?>
