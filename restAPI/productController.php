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
 * ===========================   Product   =========================
 * =================================================================
 * =================================================================
 * =================================================================
 * =================================================================
 */

function getAllSKU() {
    try {
        $productModel = new \model\ProductModel();
        $result = $productModel->getAllSKU();
        Helper::echoJson(200, "Success!", $result);
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()} {$productModel->imgError}");
    }
}

function getProductBySKU() {
    try {
        $sku = Helper::post('sku','SKU is required');
        $productModel = new \model\ProductModel();
        $inventoryModel = new \model\InventoryModel();
        $product = $productModel->getProducts([0],['sku'=>$sku])[0] or Helper::throwException("not found",404);

        $result = [];
        $result['product_sku'] = $product['product_sku'];
        $result['product_name'] = $product['product_name'];
        $result['product_price'] = $product['product_price'];
        $result['product_inventory_count'] = $product['product_inventory_count'];

        $productRelationArr = $productModel->getProductRelations($product['product_id'],['join'=>true]);
        $component = [];
        $inventoryInWarehouse = [];
        foreach ($productRelationArr as $productRelation){
            $item = [];
            $item['item_sku'] = $productRelation['item_sku'] ;
            $item['item_needed'] = $productRelation['product_relation_item_count'] ;
            $item['inventory'] = [] ;
            $inventoryArr = $inventoryModel->getInventoryWarehouse([0],['itemId'=>$productRelation['item_id']]);
            foreach ($inventoryArr as $inventory){
                $inventoryTem = [];
                $inventoryTem['warehouse'] = $inventory['warehouse_address'];
                $inventoryTem['count'] = $inventory['inventory_warehouse_count'];
                $inventoryTem['aisle']  = $inventory['inventory_warehouse_aisle'];
                $inventoryTem['column']  = $inventory['inventory_warehouse_column'];
                array_push($item['inventory'],$inventoryTem);
            }
            
            array_push($component,$item);
        }

        Helper::echoJson(200, "Success!", $result,$component,$inventoryInWarehouse);
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()} {$productModel->imgError}");
    }
}

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
            $productId = $productModel->modifyProduct();
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

function updatePriceOfAllProducts() {
    try {
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority("PRODUCT","UPDATE") or Helper::throwException(null,403);
        $productModel = new \model\ProductModel();
        $productModel->updatePriceOfAllProducts();
        Helper::echoJson(200, "Success", null, null, null, $_SESSION['back_url_1']);
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage());
    }
}

function updateInventoryOfAllProducts() {
    try {
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority("INVENTORY","STOCK_IN") or Helper::throwException(null,403);
        $productModel = new \model\ProductModel();
        $productModel->updateInventoryOfAllProducts();
        Helper::echoJson(200, "Success", null, null, null, $_SESSION['back_url_1']);
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage());
    }
}

function updateInventoryThreshold(){
    try {
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority("SYSTEM_SETTING","PRODUCT_INVENTORY_THRESHOLD") or Helper::throwException(null,403);
        $productModel = new \model\ProductModel();
        $productModel->updateInventoryThreshold(Helper::post('label'),Helper::post('threshold'));
        Helper::echoJson(200, "Success", null, null, null, $_SESSION['back_url_1']);
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage());
    }
}


?>
