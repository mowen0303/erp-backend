<?php
namespace model;
use Exception;
use \Model as Model;
use \Helper as Helper;

class InventoryModel extends Model
{


    /**
     * =================================================================
     * =================================================================
     * =================================================================
     * =================================================================
     * =======================   Warehouse  ============================
     * =================================================================
     * =================================================================
     * =================================================================
     * =================================================================
     */


    /**
     * @param int $id
     * @return int
     * @throws Exception
     */
    public function modifyWarehouse($id = 0)
    {
        $arr['warehouse_address'] = Helper::post('warehouse_address', 'Address can not be null', 1, 255);
        $arr['warehouse_city'] = Helper::post('warehouse_city', 'City can not be null', 1, 50);
        $arr['warehouse_province'] = Helper::post('warehouse_province', 'Province can not be null', 1, 50);
        $arr['warehouse_country'] = Helper::post('warehouse_country', 'Country can not be null', 1, 50);
        $arr['warehouse_post_code'] = Helper::post('warehouse_post_code', 'Post Code can not be null', 1, 10);
        $arr['warehouse_phone'] = Helper::post('warehouse_phone', 'Phone can not be null', 1, 15);
        $arr['warehouse_fax'] = Helper::post('warehouse_fax', null, 1, 15);
        $arr['warehouse_description'] = Helper::post('warehouse_description', null)?:"";
        $arr['warehouse_longitude'] = (float) Helper::post('warehouse_longitude', 'Longitude can not be null');
        $arr['warehouse_latitude'] = (float) Helper::post('warehouse_latitude', 'Latitude can not be null');
        if ($id) {
            //修改
            !$this->isExistByFieldValue('warehouse', 'warehouse_address', $arr['warehouse_address'], $id) or Helper::throwException('Address has already existed', 400);
            $this->updateRowById('warehouse', $id, $arr, false);
        } else {
            //添加
            !$this->isExistByFieldValue('warehouse', 'warehouse_address', $arr['warehouse_address']) or Helper::throwException('Address has already existed', 400);
            $id = $this->addRow('warehouse', $arr);
        }
        return $id;
    }

    public function deleteWarehouseByIds()
    {
        $ids = Helper::request('id', 'Id can not be null');
        if (!is_array($ids)) $ids = [$ids];
        //检查是否可以删除
        $idsStr = Helper::convertIDArrayToString($ids);
        $sql = "SELECT inventory_warehouse_id FROM inventory_warehouse WHERE inventory_warehouse_warehouse_id IN ({$idsStr})";
        $result = $this->sqltool->getListBySql($sql);
        if($result){
            Helper::throwException("Can not delete the Warehouse you selected because there are Item Location information belongs to it.<br> Please remove all the data within the warehouse first.");
        }else{
            return $this->deleteByIDsReally('warehouse', $ids);
        }
    }

    /**
     * @param array $ids
     * @param array $option
     * @param bool $enablePage
     * @return array
     * @throws Exception
     */
    public function getWarehouses(array $ids, array $option = [], $enablePage = true)
    {
        $bindParams = [];
        $joinCondition = "";
        $whereCondition = "";
        $pageSize = $option['pageSize'] ?: 40;
        $orderCondition = "";

        if (array_sum($ids) != 0) {
            $ids = Helper::convertIDArrayToString($ids);
            $whereCondition .= " AND warehouse_id IN ($ids)";
        }

        //SORT
        $sort = $option['sort'] == "asc"?"ASC":"DESC";

        $sql = "SELECT * FROM warehouse WHERE true {$whereCondition} ORDER BY {$orderCondition}  warehouse_id {$sort}";

        if (array_sum($ids) != 0 || !$enablePage) {
            return $this->sqltool->getListBySql($sql, $bindParams);
        } else {
            return $this->getListWithPage('warehouse', $sql, $bindParams, $pageSize);
        }
    }

    public function getWarehouseFullAddress(array $warehouse){
        return "{$warehouse['warehouse_address']}, {$warehouse['warehouse_city']}, {$warehouse['warehouse_province']}, {$warehouse['warehouse_post_code']}, {$warehouse['warehouse_country']}";
    }

    public function echoInventoryType($type){
        echo $type=="in"?'<span class="label label-info">Stock-in</span>':'<span class="label label-danger">Stock-out</span>';
    }

    /**
     * =================================================================
     * =================================================================
     * =================================================================
     * =================================================================
     * ===================   Warehouse  manager ========================
     * =================================================================
     * =================================================================
     * =================================================================
     * =================================================================
     */

    /**
     * @param $warehouseId
     * @return array|null
     * @throws Exception
     */
    public function getWarehouseManager($warehouseId){
        $sql = "SELECT * FROM warehouse_manager LEFT JOIN user ON user_id = warehouse_manager_user_id WHERE warehouse_manager_warehouse_id IN ({$warehouseId})";
        return $this->sqltool->getListBySql($sql);
    }

    /**
     * @param $userId
     * @return array|null
     * @throws Exception
     */
    public function getMyWarehouse($userId){
        $sql = "SELECT * FROM warehouse_manager LEFT JOIN warehouse ON warehouse_id = warehouse_manager_warehouse_id WHERE warehouse_manager_user_id IN ({$userId})";
        return $this->sqltool->getListBySql($sql);
    }

    /**
     * @return int
     * @throws Exception
     */
    public function addWarehouseManager(){

        $warehouseId = (int) Helper::post('warehouse_manager_warehouse_id', 'warehouse id can not be null');
        $userId = (int) Helper::post('warehouse_manager_user_id', 'user id can not be null');

        //validate
        $warehouseId >0 or Helper::throwException('warehouse id is invalid');
        $userId >0 or Helper::throwException('user id is invalid');
        $sql = "SELECT * FROM warehouse_manager WHERE warehouse_manager_warehouse_id IN ({$warehouseId}) AND warehouse_manager_user_id IN ({$userId})";
        $result = $this->sqltool->getRowBySql($sql);
        !$result or Helper::throwException('Manager has already exist');

        $arr = [];
        $arr['warehouse_manager_warehouse_id'] = $warehouseId;
        $arr['warehouse_manager_user_id'] = $userId;

        return $this->addRow('warehouse_manager',$arr);
    }

    public function deleteWarehouseManager(){
        $id = (int) Helper::request('id', 'Id can not be null');
        //validate
        $id >0 or Helper::throwException('Id is invalid');
        return $this->deleteByIDsReally('warehouse_manager',$id);
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

    /**
     * @return int
     * @throws Exception
     */
    public function modifyInventory(){
        $userModel = new UserModel();
        $operatorId = $userModel->getCurrentUserId();
        $deliverId = (int) Helper::post('inventory_log_deliver_id', null, 1, 11);
        $logType = Helper::post('inventory_log_type',"Inventory Type can not be null",1,10);
        $logNote = Helper::post('inventory_log_note')?:"";
        $itemIdArr = array_filter(Helper::post("item_id"));
        $itemQuantityArr = Helper::post("item_quantity");
        $warehouseId = (int) Helper::post("warehouse_id",'Pleases select a warehouse');

        //verify
        array_sum($itemIdArr) > 0 or Helper::throwException("Please select at least one item");
        $repeatVal = Helper::getRepeat($itemIdArr);
        count($repeatVal) == 0 or Helper::throwException("You have duplicate item ids : ".implode(',',$repeatVal));
        if($logType=="out"){
            $this->validateEnoughStockForStockOut($itemIdArr,$itemQuantityArr,$warehouseId);
        }
        
        try {
            $this->sqltool->turnOnRollback();
            //execute
            //add inventory log
            $inventoryLogId = $this->modifyInventoryLog(0,$logType,$warehouseId,$operatorId,$deliverId,$logNote);
            //add inventory log warehouse

            for($i=0; $i<count($itemIdArr); $i++){
                $itemId = (int) $itemIdArr[$i];
                $quantity = (int) $itemQuantityArr[$i];
                if($logType=="in"){
                    $quantity = abs($quantity);
                }else{
                    $quantity = abs($quantity) * -1;
                }
                $this->modifyInventoryWarehouseLog(0,$inventoryLogId,$itemId,$warehouseId,$quantity);
            }
            $this->sqltool->commit();
            return $i;

        } catch (Exception $e) {
            $this->sqltool->rollback();
            Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()}");
        }
    }

    /**
     * @return int
     * @throws Exception
     */
    public function updateInventory(){
        $userModel = new UserModel();
        $inventoryLogId = (int) Helper::post('inventory_log_id', "inventory Log Id can not be null", 1, 11);
        $operatorId = (int) Helper::post('inventory_log_operator_id', "Operator User Id can not be nulls", 1, 11);
        $deliverId = (int) Helper::post('inventory_log_deliver_id', null, 1, 11);
        $logNote = Helper::post('inventory_log_note')?:"";
        $itemIdArr = array_filter(Helper::post("item_id"));
        $itemQuantityArr = Helper::post("item_quantity");
        $inventoryWarehouseLogIdArr = Helper::post("inventory_warehouse_log_id","inventory Warehouse Log Id can not be null");
        $warehouseId = (int) Helper::post("warehouse_id",'Pleases select a warehouse');

        $oldInventoryLog = $this->getInventoryLog([$inventoryLogId])[0] or Helper::throwException("Can not find inventory log",404);
        $logType = $oldInventoryLog['inventory_log_type'];

        //verify
        array_sum($itemIdArr) > 0 or Helper::throwException("Please select at least one item");
        $repeatVal = Helper::getRepeat($itemIdArr);
        count($repeatVal) == 0 or Helper::throwException("You have duplicate item ids : ".implode(',',$repeatVal));

        try {
            $this->sqltool->turnOnRollback();
            //execute
            //add inventory log
            $inventoryLogId = $this->modifyInventoryLog($inventoryLogId,$logType,$warehouseId,$operatorId,$deliverId,$logNote);

            //add inventory log warehouse
            for($i=0; $i<count($itemIdArr); $i++){
                $itemId = (int) $itemIdArr[$i];
                $inventoryWarehouseLogId = (int) $inventoryWarehouseLogIdArr[$i];
                $quantity = (int) $itemQuantityArr[$i];
                if($logType=="in"){
                    $quantity = abs($quantity);
                }else{
                    $quantity = abs($quantity) * -1;
                }
                $this->modifyInventoryWarehouseLog($inventoryWarehouseLogId,$inventoryLogId,$itemId,$warehouseId,$quantity);
            }
            $this->sqltool->commit();
//            return $i;

        } catch (Exception $e) {
            $this->sqltool->rollback();
            Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()}");
        }
    }

    /**
     * @param array $itemIdArr
     * @param array $itemQuantityArr
     * @param int $warehouseId
     * @throws Exception
     */
    public function validateEnoughStockForStockOut(array $itemIdArr, array $itemQuantityArr, int $warehouseId){
        $itemIdStr = Helper::convertIDArrayToString($itemIdArr);
        $sql = "SELECT item_id,item_sku,inventory_warehouse_count FROM inventory_warehouse LEFT JOIN item ON inventory_warehouse_item_id = item_id WHERE inventory_warehouse_item_id IN ({$itemIdStr}) AND inventory_warehouse_warehouse_id = '{$warehouseId}'";
        $result = $this->sqltool->getListBySql($sql);
        if($result){
            for($i=0;$i<count($itemIdArr);$i++){
                if($result[$i]['inventory_warehouse_count'] < $itemQuantityArr[$i]){
                    Helper::throwException("The item SKU# <b>{$result[$i]['item_sku']}</b> has no enough stock quantity in the current warehouse");
                }
            }
        }
    }

    /**
     * @param $itemId
     * @return int
     * @throws Exception
     */
    public function updateInventoryQuantity($itemId){
        //get item amount
        $sql = "SELECT sum(inventory_warehouse_log_count) as amount FROM inventory_warehouse_log WHERE inventory_warehouse_log_item_id IN ({$itemId})";
        $itemAmount = $this->sqltool->getRowBySql($sql)['amount'];

        //check inventory exist
        $sql = "SELECT inventory_id FROM inventory WHERE inventory_item_id = '{$itemId}'";
        $inventoryId = (int) $this->sqltool->getRowBySql($sql)['inventory_id'];
        if($inventoryId){
            //inventory id exist, then update the count
            $arr = [];
            $arr['inventory_count'] = $itemAmount;
            return $this->updateRowById('inventory',$inventoryId,$arr,false);
        }else{
            //inventory id does not exist, then add one new record
            $arr = [];
            $arr['inventory_item_id'] = $itemId;
            $arr['inventory_count'] = $itemAmount;
            return $this->addRow('inventory',$arr);
        }
    }

    /**
     * @param array $ids
     * @param array $option
     * @param bool $enablePage
     * @return array|null
     * @throws Exception
     */
    public function getInventory(array $ids, array $option = [], $enablePage = true)
    {
        $bindParams = [];
        $joinCondition = "";
        $whereCondition = "";
        $orderCondition = "";
        $orderByParams = [];
        $pageSize = $option['pageSize'] ?: 40;

        if (array_sum($ids) != 0) {
            $ids = Helper::convertIDArrayToString($ids);
            $whereCondition .= " AND inventory_id IN ($ids)";
        }

        if($option['itemCategoryId']){
            $itemCategoryId = (int) $option['itemCategoryId'];
            $whereCondition .= " AND item_item_category_id IN ({$itemCategoryId})";
        }

        if($option['itemStyleId']){
            $itemStyleId = (int) $option['itemStyleId'];
            $whereCondition .= " AND item_item_style_id IN ({$itemStyleId})";
        }

        //search
        $searchValue = $option['searchValue'];
        if($searchValue){
            $searchStatement = "(item_sku like ?) * 2048 + (item_sku like ?) * 1024 + (item_sku like ?) * 516";
            $whereCondition .=  " AND {$searchStatement}";
            $orderCondition .= " {$searchStatement} DESC,";
            $param = ["{$searchValue}","{$searchValue}%","%{$searchValue}%"];
            $bindParams = array_merge($bindParams,$param);
            $orderByParams = array_merge($orderByParams,$param);
        }

        //sort
        $orderBy = $option['orderBy'];
        $sort   = $option['sort'] == "asc"?"ASC":"DESC";
        if($orderBy == 'sku'){
            $orderCondition = "item_sku {$sort},";
        }else if($orderBy == 'weight'){
            $orderCondition = "item_weight {$sort},";
        }else if($orderBy == 'length'){
            $orderCondition = "item_l {$sort},";
        }else if($orderBy == 'width'){
            $orderCondition = "item_w {$sort},";
        }else if($orderBy == 'height'){
            $orderCondition = "item_h {$sort},";
        }else if($orderBy == 'style'){
            $orderCondition = "item_style_title {$sort},";
        }

        $sql = "SELECT * FROM inventory LEFT JOIN item ON inventory_item_id = item_id LEFT JOIN item_category ON item_item_category_id = item_category_id LEFT JOIN item_style ON item_item_style_id = item_style_id  WHERE true {$whereCondition} ORDER BY {$orderCondition} inventory_id DESC";

        $bindParams = array_merge($bindParams,$orderByParams);
        if (array_sum($ids) != 0 || !$enablePage) {
            return $this->sqltool->getListBySql($sql, $bindParams);
        } else {
            return $this->getListWithPage('item', $sql, $bindParams, $pageSize);
        }
    }

    /**
     * @param $type
     * @throws Exception
     */
    public function verifyInventoryType($type){
        in_array($type,['in','out']) or Helper::throwException('Type is invalidate');
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

    /**
     * @param array $ids
     * @param array $option
     * @param bool $enablePage
     * @return array
     * @throws Exception
     */
    public function getInventoryWarehouse(array $ids, array $option = [], $enablePage = true)
    {
        $bindParams = [];
        $joinCondition = "";
        $whereCondition = "";
        $orderCondition = "";
        $orderByParams = [];
        $pageSize = $option['pageSize'] ?: 40;

        if (array_sum($ids) != 0) {
            $ids = Helper::convertIDArrayToString($ids);
            $whereCondition .= " AND inventory_warehouse_id IN ($ids)";
        }

        if($option['warehouseId']){
            $id = (int) $option['warehouseId'];
            $whereCondition .= " AND inventory_warehouse_warehouse_id IN ($id)";
        }else if($option['itemId']){
            $id = (int) $option['itemId'];
            $whereCondition .= " AND inventory_warehouse_item_id IN ($id)";
        }

        if($option['itemCategoryId']){
            $itemCategoryId = (int) $option['itemCategoryId'];
            $whereCondition .= " AND item_item_category_id IN ({$itemCategoryId})";
        }

        if($option['itemStyleId']){
            $itemStyleId = (int) $option['itemStyleId'];
            $whereCondition .= " AND item_item_style_id IN ({$itemStyleId})";
        }

        //search
        $searchValue = $option['searchValue'];
        if($searchValue){
            $searchStatement = "(item_sku like ?) * 2048 + (item_sku like ?) * 1024 + (item_sku like ?) * 516";
            $whereCondition .=  " AND {$searchStatement}";
            $orderCondition .= " {$searchStatement} DESC,";
            $param = ["{$searchValue}","{$searchValue}%","%{$searchValue}%"];
            $bindParams = array_merge($bindParams,$param);
            $orderByParams = array_merge($orderByParams,$param);
        }


        //sort
        $orderBy = $option['orderBy'];
        $sort   = $option['sort'] == "asc"?"ASC":"DESC";
        if($orderBy == 'sku'){
            $orderCondition = "item_sku {$sort},";
        }else if($orderBy == 'weight'){
            $orderCondition = "item_weight {$sort},";
        }else if($orderBy == 'length'){
            $orderCondition = "item_l {$sort},";
        }else if($orderBy == 'width'){
            $orderCondition = "item_w {$sort},";
        }else if($orderBy == 'height'){
            $orderCondition = "item_h {$sort},";
        }else if($orderBy == 'style'){
            $orderCondition = "item_style_title {$sort},";
        }

        $sql = "SELECT * FROM inventory_warehouse LEFT JOIN warehouse ON inventory_warehouse_warehouse_id = warehouse_id LEFT JOIN item ON inventory_warehouse_item_id = item_id LEFT JOIN item_category ON item_category_id = item_item_category_id LEFT JOIN item_style ON item_style_id = item_item_style_id WHERE true {$whereCondition} ORDER BY {$orderCondition} inventory_warehouse_id DESC";
        $bindParams = array_merge($bindParams,$orderByParams);

        if (array_sum($ids) != 0 || !$enablePage) {
            return $this->sqltool->getListBySql($sql, $bindParams);
        } else {
            return $this->getListWithPage('inventory_warehouse', $sql, $bindParams, $pageSize);
        }
    }

    /**
     * @param $itemId
     * @param $warehouseId
     * @return int
     * @throws Exception
     */
    public function updateInventoryWarehouseQuantity($itemId,$warehouseId){
        //get item amount
        $sql = "SELECT sum(inventory_warehouse_log_count) as amount FROM inventory_warehouse_log LEFT JOIN inventory_log ON inventory_warehouse_log_inventory_log_id = inventory_log_id  WHERE inventory_warehouse_log_item_id IN ({$itemId}) AND inventory_log_warehouse_id IN ({$warehouseId})";
        $itemAmount = $this->sqltool->getRowBySql($sql)['amount'];

        //check inventory exist
        $sql = "SELECT inventory_warehouse_id FROM inventory_warehouse WHERE inventory_warehouse_item_id = '{$itemId}' AND inventory_warehouse_warehouse_id = '{$warehouseId}'";
        $inventoryWarehouseId = (int) $this->sqltool->getRowBySql($sql)['inventory_warehouse_id'];
        if($inventoryWarehouseId){
            //inventory warehouse id exist, then update the count
            $arr = [];
            $arr['inventory_warehouse_count'] = $itemAmount;
            return $this->updateRowById('inventory_warehouse',$inventoryWarehouseId,$arr,false);
        }else{
            //inventory id does not exist, then add one new record
            $arr = [];
            $arr['inventory_warehouse_item_id'] = $itemId;
            $arr['inventory_warehouse_warehouse_id'] = $warehouseId;
            $arr['inventory_warehouse_count'] = $itemAmount;
            return $this->addRow('inventory_warehouse',$arr);
        }
    }

    /**
     * @param int $id
     * @return int
     * @throws Exception
     */
    public function modifyInventoryMap($id = 0)
    {
        $arr['inventory_warehouse_warehouse_id'] = (int) Helper::post('inventory_warehouse_warehouse_id', 'Warehouse Id can not be null', 1, 11);
        $arr['inventory_warehouse_aisle'] = Helper::post('inventory_warehouse_aisle', 'Aisle can not be null', 1, 10);
        $arr['inventory_warehouse_column'] = Helper::post('inventory_warehouse_column', 'Column Id can not be null', 1, 10);

        if ($id) {
            //修改
            $isExistArr = [];
            $isExistArr['inventory_warehouse_warehouse_id'] = $arr['inventory_warehouse_warehouse_id'];
            $isExistArr['inventory_warehouse_item_id'] = $arr['inventory_warehouse_item_id'];
            !$this->isExist('inventory_warehouse', $isExistArr, $id) or Helper::throwException('The Item map location has already existed in the Warehouse', 400);
            $this->updateRowById('inventory_warehouse', $id, $arr, false);
        } else {
            //添加
            $arr['inventory_warehouse_item_id'] = (int) Helper::post('inventory_warehouse_item_id', 'Item Id can not be null', 1, 11);
            $isExistArr = [];
            $isExistArr['inventory_warehouse_warehouse_id'] = $arr['inventory_warehouse_warehouse_id'];
            $isExistArr['inventory_warehouse_item_id'] = $arr['inventory_warehouse_item_id'];
            !$this->isExist('inventory_warehouse', $isExistArr) or Helper::throwException('The Item map location has already existed in the Warehouse', 400);
            $id = $this->addRow('inventory_warehouse', $arr);
        }
        return $id;
    }

    public function deleteInventoryWarehouseByIds()
    {
        $ids = Helper::request('id', 'Id can not be null');
        if (!is_array($ids)) $ids = [$ids];

        $idsStr = Helper::convertIDArrayToString($ids);
        $sql = "SELECT * FROM inventory_warehouse LEFT JOIN item ON inventory_warehouse_item_id = item_id WHERE inventory_warehouse_id IN ({$idsStr})";
        $result = $this->sqltool->getListBySql($sql);
        if($result){
            foreach ($result as $row){
                (int) $row['inventory_warehouse_count'] <= 0 or Helper::throwException("You can not delete item {$row['item_sku']} in the current warehouse because there still have stocks.<br> Please stock-out the item before delete it.");
            }
        }
        $deletedRows = $this->deleteByIDsReally('inventory_warehouse', $ids);
        return $deletedRows;
    }

    /**
     * =================================================================
     * =================================================================
     * =================================================================
     * =================================================================
     * =====================   Inventory Log  ==========================
     * =================================================================
     * =================================================================
     * =================================================================
     * =================================================================
     */

    /**
     * @param array $ids
     * @param array $option
     * @param bool $enablePage
     * @return array|null
     * @throws Exception
     */
    public function getInventoryLog(array $ids, array $option = [], $enablePage = true)
    {
        $bindParams = [];
        $joinCondition = "";
        $whereCondition = "";
        $pageSize = $option['pageSize'] ?: 40;

        if (array_sum($ids) != 0) {
            $ids = Helper::convertIDArrayToString($ids);
            $whereCondition .= " AND inventory_log_id IN ($ids)";
        }

        $sql = "SELECT 
                    inventory_log.*,
                    warehouse.*,
                    operator.user_id as operator_id,
                    operator.user_first_name as operator_first_name,
                    operator.user_last_name as operator_last_name, 
                    operator.user_email as operator_email,
                    deliver.user_id as deliver_id,
                    deliver.user_first_name as deliver_first_name,
                    deliver.user_last_name as deliver_last_name, 
                    deliver.user_email as deliver_email            
                FROM inventory_log 
                LEFT JOIN user as operator ON inventory_log_operator_id = operator.user_id 
                LEFT JOIN user as deliver ON inventory_log_deliver_id = deliver.user_id 
                LEFT JOIN warehouse ON inventory_log_warehouse_id = warehouse_id
                WHERE true {$whereCondition} 
                ORDER BY inventory_log_id DESC";

        if (array_sum($ids) != 0 || !$enablePage) {
            return $this->sqltool->getListBySql($sql, $bindParams);
        } else {
            return $this->getListWithPage('inventory_log', $sql, $bindParams, $pageSize);
        }
    }

    /**
     * @param int $inventoryLogId
     * @param $logType
     * @param int $warehouseId
     * @param int $operatorId
     * @param int $deliverId
     * @param $logNote
     * @return int
     * @throws Exception
     */
    public function modifyInventoryLog(int $inventoryLogId, $logType, int $warehouseId, int $operatorId, int $deliverId, $logNote)
    {
        $arr = [];
        $arr['inventory_log_type'] = $logType;
        $arr['inventory_log_warehouse_id'] = $warehouseId;
        $arr['inventory_log_operator_id'] = $operatorId;
        $arr['inventory_log_deliver_id'] = $deliverId;
        $arr['inventory_log_note'] = $logNote;

        //verify
        $this->verifyInventoryType($logType);

        if($inventoryLogId){
            //update
            return $this->updateRowById('inventory_log',$inventoryLogId, $arr,false);
        }else{
            //add
            return $this->addRow('inventory_log', $arr);
        }
    }

    /**
     * =================================================================
     * =================================================================
     * =================================================================
     * =================================================================
     * ================   Inventory Warehouse Log =====================
     * =================================================================
     * =================================================================
     * =================================================================
     * =================================================================
     */

    /**
     * @param array $ids
     * @param array $option
     * @param bool $enablePage
     * @return array|null
     * @throws Exception
     */
    public function getInventoryWarehouseLog(array $ids, array $option = [], $enablePage = true){
        $bindParams = [];
        $joinCondition = "";
        $whereCondition = "";
        $pageSize = $option['pageSize'] ?: 40;

        if (array_sum($ids) != 0) {
            $ids = Helper::convertIDArrayToString($ids);
            $whereCondition .= " AND inventory_warehouse_log_id IN ($ids)";
        }

        if($option['itemId'] && $option['warehouseId']){
            $itemId = (int) $option['itemId'];
            $warehouseId = (int) $option['warehouseId'];
            $whereCondition .= " AND inventory_log_warehouse_id IN ($warehouseId) AND inventory_warehouse_log_item_id IN ({$itemId})";
        }else if($option['inventoryLogId']){
            $id = (int) $option['inventoryLogId'];
            $whereCondition .= " AND inventory_warehouse_log_inventory_log_id IN ($id)";
        }

        $sql = "
                SELECT 
                    inventory_warehouse_log.*,
                    inventory_log.*,
                    warehouse.*,
                    item.*,
                    operator.user_id as operator_id,
                    operator.user_first_name as operator_first_name,
                    operator.user_last_name as operator_last_name, 
                    operator.user_email as operator_email,
                    operator.user_phone as operator_phone,
                    operator.user_address as operator_address,
                    deliver.user_id as deliver_id,
                    deliver.user_first_name as deliver_first_name,
                    deliver.user_last_name as deliver_last_name, 
                    deliver.user_email as deliver_email,
                    deliver.user_phone as deliver_phone,
                    deliver.user_address as deliver_address    
                FROM inventory_warehouse_log 
                LEFT JOIN inventory_log ON inventory_warehouse_log_inventory_log_id = inventory_log_id 
                LEFT JOIN warehouse ON inventory_log_warehouse_id = warehouse_id 
                LEFT JOIN item ON inventory_warehouse_log_item_id = item_id 
                LEFT JOIN user as operator ON inventory_log_operator_id = operator.user_id 
                LEFT JOIN user as deliver ON inventory_log_deliver_id = deliver.user_id 
                WHERE true {$whereCondition} 
                ORDER BY inventory_warehouse_log_id DESC
                ";

        if (array_sum($ids) != 0 || !$enablePage) {
            return $this->sqltool->getListBySql($sql, $bindParams);
        } else {
            return $this->getListWithPage('inventory_warehouse_log', $sql, $bindParams, $pageSize);
        }
    }

    /**
     * @param int $inventoryWarehouseLogId
     * @param int $inventoryLogId
     * @param int $itemId
     * @param int $warehouseId
     * @param int $quantity
     * @return int
     * @throws Exception
     */
    public function modifyInventoryWarehouseLog(int $inventoryWarehouseLogId, int $inventoryLogId, int $itemId, int $warehouseId, int $quantity){
        $arr = [];
        $arr['inventory_warehouse_log_count'] = $quantity;
        if($inventoryWarehouseLogId){
            $oldInventoryWarehouseLog = $this->getInventoryWarehouseLog([$inventoryWarehouseLogId])[0] or Helper::throwException("Can not find the Inventory Warehouse Log",404);
            $oldInventoryWarehouseLog['inventory_warehouse_log_item_id'] == $itemId or Helper::throwException("Can not change item id in the inventory log");
            $id = $this->updateRowById('inventory_warehouse_log',$inventoryWarehouseLogId,$arr,false);
        }else{
            $inventoryLogId != 0 && $itemId != 0 or Helper::throwException("invalidated inventory Log Id or item id");
            $arr['inventory_warehouse_log_inventory_log_id'] = $inventoryLogId;
            $arr['inventory_warehouse_log_item_id'] = $itemId;
            $id = $this->addRow('inventory_warehouse_log',$arr);
        }
        $this->updateInventoryQuantity($itemId);
        $this->updateInventoryWarehouseQuantity($itemId,$warehouseId);
        return $id;
    }

}

?>
