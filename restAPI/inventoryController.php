<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonServices/config.php";

function modifyWarehouse() {
    try {
        $userModel = new \model\UserModel();
        $warehouseModel = new \model\InventoryModel();
        $warehouseId = (int) Helper::post('warehouse_id');
        if($warehouseId){
            //修改
            $userModel->isCurrentUserHasAuthority('WAREHOUSE', 'UPDATE') or Helper::throwException(null, 403);
            $warehouseModel->modifyWarehouse($warehouseId);
        }else{
            //添加
            $userModel->isCurrentUserHasAuthority('WAREHOUSE', 'ADD') or Helper::throwException(null, 403);
            $warehouseModel->modifyWarehouse();
        }
        Helper::echoJson(200, "Success! {$warehouseModel->imgError}", null, null, null, Helper::echoBackBtn(0,true),'Back','/admin/item/index.php?s=item-form','Add a new item');
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()} {$warehouseModel->imgError}");
    }
}

function deleteWarehouseByIds() {
    try {
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority("WAREHOUSE","DELETE") or Helper::throwException(null,403);
        $inventoryModel = new \model\InventoryModel();
        $effectRows = $inventoryModel->deleteWarehouseByIds();
        Helper::echoJson(200, "{$effectRows} rows data has been deleted", null, null, null, $_SESSION['back_url_1']);
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage());
    }
}

?>
