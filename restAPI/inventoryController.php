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

/**
 * =================================================================
 * =================================================================
 * =================================================================
 * =================================================================
 * =======================   Inventory  ============================
 * =================================================================
 * =================================================================
 * =================================================================
 * =================================================================
 */

function modifyInventory() {
    try {
        $userModel = new \model\UserModel();
        $warehouseId = (int) Helper::post("warehouse_id",'Pleases select a warehouse');
        $logType = Helper::post('inventory_log_type',"Inventory Type can not be null",1,10);
        if($logType=="in"){
            $userModel->isCurrentUserHasAuthority("INVENTORY","STOCK_IN")
            ||  $userModel->isCurrentUserHasWarehouseManagementAuthority($warehouseId)
            or Helper::throwException(null,403);
        }else{
            $userModel->isCurrentUserHasAuthority("INVENTORY","STOCK_OUT")
            ||  $userModel->isCurrentUserHasWarehouseManagementAuthority($warehouseId)
            or Helper::throwException(null,403);
        }
        $warehouseModel = new \model\InventoryModel();
        $warehouseModel->modifyInventory();
        Helper::echoJson(200, "Success!", null, null, null);
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()} {$warehouseModel->imgError}");
    }
}

function updateInventory() {
    try {
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority("INVENTORY_LOG","UPDATE") or Helper::throwException(null,403);
        $warehouseModel = new \model\InventoryModel();
        $warehouseModel->updateInventory();
        Helper::echoJson(200, "Success!", null, null, null);
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()} {$warehouseModel->imgError}");
    }
}

/**
 * =================================================================
 * =================================================================
 * =================================================================
 * =================================================================
 * ===================   Warehouse Manager  ========================
 * =================================================================
 * =================================================================
 * =================================================================
 * =================================================================
 */

function addWarehouseManager() {
    try {
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority("WAREHOUSE","ADD") or Helper::throwException(null,403);
        $inventoryModel = new \model\InventoryModel();
        $inventoryModel->addWarehouseManager();
        Helper::echoJson(200, "Success!");
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()}");
    }
}

function deleteWarehouseManager() {
    try {
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority("WAREHOUSE","ADD") or Helper::throwException(null,403);
        $inventoryModel = new \model\InventoryModel();
        $inventoryModel->deleteWarehouseManager();
        Helper::echoJson(200, "Success!");
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()}");
    }
}


/**
 * =================================================================
 * =================================================================
 * =================================================================
 * =================================================================
 * ==================   Inventory Warehouse  =======================
 * =================================================================
 * =================================================================
 * =================================================================
 * =================================================================
 */

function modifyInventoryMap() {
    try {
        $userModel = new \model\UserModel();
        $warehouseModel = new \model\InventoryModel();
        $inventoryWarehouseId = (int) Helper::post('inventory_warehouse_id');
        $warehouseId = (int) Helper::post('inventory_warehouse_warehouse_id');
        if($inventoryWarehouseId){
            //修改
            $userModel->isCurrentUserHasAuthority('WAREHOUSE', 'UPDATE')
            ||  $userModel->isCurrentUserHasWarehouseManagementAuthority($warehouseId) or Helper::throwException(null, 403);
            $warehouseModel->modifyInventoryMap($inventoryWarehouseId);
        }else{
            //添加
            $userModel->isCurrentUserHasAuthority('WAREHOUSE', 'ADD')
            ||  $userModel->isCurrentUserHasWarehouseManagementAuthority($warehouseId) or Helper::throwException(null, 403);
            $warehouseModel->modifyInventoryMap();
        }
        Helper::echoJson(200, "Success! {$warehouseModel->imgError}", null, null, null, Helper::echoBackBtn(0,true),'Back',"/admin/inventory/index.php?s=inventory-warehouse-item-map-form&warehouseId={$warehouseId}",'Add a new item Map');
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()} {$warehouseModel->imgError}");
    }
}


function deleteInventoryWarehouseByIds() {
    try {
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority("WAREHOUSE","DELETE") or Helper::throwException(null,403);
        $inventoryModel = new \model\InventoryModel();
        $effectRows = $inventoryModel->deleteInventoryWarehouseByIds();
        Helper::echoJson(200, "{$effectRows} rows data has been deleted", null, null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage());
    }
}






?>
