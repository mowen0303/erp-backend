<?php
namespace model;
use Exception;
use \Model as Model;
use \Helper as Helper;

class ProductModel extends Model
{
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

    public $SETTING_JSON = null;
    public $INVENTORY_LEVEL_1 = 0;

    public function __construct(){
        parent::__construct();
        $this->SETTING_JSON = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].SETTING_JSON));
        $this->INVENTORY_LEVEL_1 = $this->SETTING_JSON->inventoryLabel->threshold;
    }

    /**
     * @param int $id
     * @return int
     * @throws Exception
     */
    public function modifyProductCategory($id=0){
        $arr['product_category_title'] = Helper::post('product_category_title','Title can not be null',1,255);
        $arr['product_category_des'] = Helper::post('product_category_des',null,0,255);
        if ($id) {
            //修改
            $this->updateRowById('product_category', $id, $arr,false);
        } else {
            //添加
            $id = $this->addRow('product_category', $arr);
        }
        $fileModel = new FileModel();
        $this->imgError = $fileModel->modifyFileAndTableData('product_category',$id,'product_category_img','file',['image'],false,10000000,40000);
        return $id;
    }

    /**
     * @param array $ids
     * @param array $option
     * @param bool $enablePage
     * @return array
     * @throws Exception
     */
    public function getProductCategories(array $ids,array $option = [],$enablePage=false){
        $bindParams = [];
        $orderByParams = [];
        $whereCondition = "";
        $pageSize   = $option['pageSize']?:40;

        if(array_sum($ids)!=0){
            $ids = Helper::convertIDArrayToString($ids);
            $whereCondition .= " AND product_category_id IN ($ids)";
        }

        $sql = "SELECT * FROM product_category WHERE true {$whereCondition}";

        $bindParams = array_merge($bindParams,$orderByParams);
        if(array_sum($ids)!=0 || !$enablePage){
            return $this->sqltool->getListBySql($sql,$bindParams);
        }else{
            return $this->getListWithPage('item',$sql,$bindParams,$pageSize);
        }
    }

    public function updateInventoryThreshold($label,$threshold){
        $this->SETTING_JSON->inventoryLabel->threshold = $threshold;
        $this->SETTING_JSON->inventoryLabel->label = $label;
        if(file_put_contents($_SERVER['DOCUMENT_ROOT'].SETTING_JSON,json_encode($this->SETTING_JSON))){
            return true;
        }else{
            Helper::throwException('Failed to Update threshold.');
            return false;
        }
    }


    //TODO: 增加删除限制
    public function deleteProductCategoryByIds(){
        $ids = Helper::request('id','Id can not be null');
        if(!is_array($ids)) $ids = [$ids];
        $arr = $this->getItems($ids);
        $deletedRows = $this->deleteByIDsReally('item', $ids);

        $fileModel = new FileModel();
        foreach ($arr as $row){
            $fileModel->deleteFileByPath($row['item_image']);
        }
        return $deletedRows;
    }


    /**
     * =================================================================
     * =================================================================
     * =================================================================
     * =================================================================
     * =========================   Product  ============================
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
    public function modifyProduct($id=0){
        $tableName = "product";

        $arr['product_sku'] = Helper::post('product_sku','SKU can not be null',1,50);
        $arr['product_name'] = Helper::post('product_name',null,1,255);
        $arr['product_product_category_id'] = Helper::post('product_product_category_id','Category can not be null',1,11);
        $arr['product_item_style_id'] = Helper::post('product_item_style_id','Style can not be null',1,11);
        $arr['product_w'] = Helper::post('product_w') ?: 0;
        $arr['product_h'] = Helper::post('product_h') ?: 0;
        $arr['product_l'] = Helper::post('product_l') ?: 0;
        $arr['product_expected_count'] = Helper::post('product_expected_count') ?: 0;
        $arr['product_des'] = Helper::post('product_des',null,1,65535) ?: "";

        $itemIdArr = array_filter(Helper::post("item_id"));
        $itemQuantityArr = Helper::post("item_quantity");


        $featureArr = Helper::post('product_feature');
        $featureArr = array_values(array_filter($featureArr));
        for($i=1;$i<=5;$i++){
            if($featureArr[$i-1]){
                $arr["product_feature_{$i}"] = $featureArr[$i-1];
            }else{
                $arr["product_feature_{$i}"] = "";
            }
        }

        try {
            $this->sqltool->turnOnRollback();

            if ($id) {
                //修改
                !$this->isExistByFieldValue($tableName,'product_sku',$arr['product_sku'],$id) or Helper::throwException('SKU has already existed',400);
                $this->updateRowById($tableName, $id, $arr,false);
            } else {
                //添加
                !$this->isExistByFieldValue($tableName,'product_sku',$arr['product_sku']) or Helper::throwException('SKU has already existed',400);
                $id = $this->addRow($tableName, $arr);
            }

            $this->modifyProductRelation($id,$itemIdArr,$itemQuantityArr);

            //multi image update
            $imgCount = 5;
            $fieldPrefix = "product_img_";
            $imgArr = [];
            $newArr = [];
            $fileModel = new FileModel();
            for($i = 0; $i < $imgCount; $i++){
                $this->imgError .= $fileModel->modifyFileAndTableData($tableName,$id,$fieldPrefix.$i,'img_'.$i,['image'],false,10000000,40000);
            }
            $row = $this->getRowById($tableName,$id);
            for($i = 0; $i < $imgCount; $i++){
                if($row[$fieldPrefix.$i]){
                    $imgArr[] = $row[$fieldPrefix.$i];
                }
            }
            for($i = 0; $i < $imgCount; $i++){
                if($imgArr[$i]){
                    $newArr[$fieldPrefix.$i] = $imgArr[$i];
                }else{
                    $newArr[$fieldPrefix.$i] = "";
                }
            }
            $this->updateRowById($tableName, $id, $newArr,false);
            $this->updateProductPriceByProductId($id);

            $this->sqltool->commit();

            return $id;

        } catch (Exception $e) {
            $this->sqltool->rollback();
            Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()}");
        }


    }

    /**
     * @param array $ids
     * @param array $option
     * @param bool $enablePage
     * @return array
     * @throws Exception
     */
    public function getProducts(array $ids,array $option = [],$enablePage=true){
        $bindParams = [];
        $orderByParams = [];
        $selectFields = "";
        $joinCondition = "";
        $whereCondition = "";
        $orderCondition = "";
        $pageSize   = $option['pageSize']?:20;

        if(array_sum($ids)!=0){
            $ids = Helper::convertIDArrayToString($ids);
            $whereCondition .= " AND product_id IN ($ids)";
        }

        if($option['join']){
            $joinCondition .= "LEFT JOIN product_category ON product_product_category_id = product_category_id LEFT JOIN item_style ON product_item_style_id = item_style_id";
        }

        if($option['productCategoryId']){
            $productCategoryId = (int) $option['productCategoryId'];
            $whereCondition .= " AND product_product_category_id IN ({$productCategoryId})";
        }

        if($option['itemStyleId']){
            $itemStyleId = (int) $option['itemStyleId'];
            $whereCondition .= " AND product_item_style_id IN ({$itemStyleId})";
        }

        //sort
        $orderBy = $option['orderBy'];
        $sort   = $option['sort'] == "asc"?"ASC":"DESC";
        if($orderBy == 'price'){
            $orderCondition = "product_price {$sort},";
        }else if($orderBy == 'sku'){
            $orderCondition = "product_sku {$sort},";
        }else if($orderBy == 'weight'){
            $orderCondition = "product_weight {$sort},";
        }else if($orderBy == 'length'){
            $orderCondition = "product_l {$sort},";
        }else if($orderBy == 'width'){
            $orderCondition = "product_w {$sort},";
        }else if($orderBy == 'height'){
            $orderCondition = "product_h {$sort},";
        }else if($orderBy == 'category'){
            $orderCondition = "product_category_title {$sort},";
        }else if($orderBy == 'style' && $option['join']){
            $orderCondition = "item_style_title {$sort},";
        }else if($orderBy == 'inventory'){
            $orderCondition = "product_inventory_count {$sort},";
        }

        //search
        $searchValue = $option['searchValue'];
        if($searchValue){
            $searchStatement = "(product_sku like ?) * 2048 + (product_sku like ?) * 1024 + (product_sku like ?) * 516 + (product_name like ?) * 516";
            $whereCondition .=  " AND {$searchStatement}";
            $param = ["{$searchValue}","{$searchValue}%","%{$searchValue}%","%{$searchValue}%"];
            $bindParams = array_merge($bindParams,$param);
            if($orderBy){
                $orderCondition .= " {$searchStatement} DESC,";
                $orderByParams = array_merge($orderByParams,$param);
            }
        }

        $sql = "SELECT * FROM product {$joinCondition} WHERE true {$whereCondition} ORDER BY {$orderCondition} product_id DESC";
        $bindParams = array_merge($bindParams,$orderByParams);
        $result = null;
        if(array_sum($ids)!=0 || !$enablePage){
            $result = $this->sqltool->getListBySql($sql,$bindParams);
        }else{
            $result = $this->getListWithPage('product',$sql,$bindParams,$pageSize);
        }
        return $result;
    }


    public function echoInventoryLabel(int $count){
        if($count == 0){
            echo "<span class='label label-danger'>Sold out</span>";
        }else if($count <= $this->INVENTORY_LEVEL_1){
            echo "<span class='label label-warning'>{$this->SETTING_JSON->inventoryLabel->label}</span>";
        }else{
            echo "<span class='label label-success'>Available</span>";
        }
    }

    public function updateProductInventoryByProductId(int $productId){
        $sql = "SELECT product_relation_id,product_relation_item_id,product_relation_item_count,inventory_count FROM product_relation LEFT JOIN inventory ON product_relation_item_id = inventory_item_id WHERE product_relation_product_id IN ($productId)";
        $result = $this->sqltool->getListBySql($sql);
        $finalInventory = 99999999;
        foreach ($result as $row){
            $currentInventory = floor((int) $row['inventory_count'] / (int) $row['product_relation_item_count']);
            if($finalInventory > $currentInventory){
                $finalInventory = $currentInventory;
            }
        }
        $arr['product_inventory_count'] = $finalInventory;
        $this->updateRowById('product',$productId,$arr,false);
    }

    public function updateProductInventoryByItemId(int $id){
        $sql = "SELECT product_relation_product_id FROM product_relation WHERE product_relation_item_id IN ($id)";
        $result = $this->sqltool->getListBySql($sql);
        $productIdArr = [];
        foreach ($result as $row){
            $productIdArr[] = $row['product_relation_product_id'];
        }
        //键值互换去重
        $productIdArr = array_flip($productIdArr);
        $productIdArr = array_flip($productIdArr);
        foreach ($productIdArr as $productId){
            $this->updateProductInventoryByProductId($productId);
        }
    }

    public function deleteProductByIds(){
        $ids = Helper::request('id','Id can not be null');
        if(!is_array($ids)) $ids = [$ids];
        $arr = $this->getProducts($ids);
        try {
            $this->sqltool->turnOnRollback();
            $deletedRows = $this->deleteByIDsReally('product', $ids);
            $this->deleteProductRelation( $ids);

            $fileModel = new FileModel();
            foreach ($arr as $row){
                for($i=0;$i<=4;$i++){
                    $fileModel->deleteFileByPath($row['product_img_'.$i]);
                }
            }
            $this->sqltool->commit();

            return $deletedRows;
        } catch (Exception $e) {
            $this->sqltool->rollback();
            Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()}");
        }
    }

    function getProductListOrderUrl($orderBy){
        $sort = $_GET['sort'];
        $urlOrderBy = $_GET['orderBy'];
        if($urlOrderBy == $orderBy){
            $sort = $sort=="asc"?"desc":"asc";
        }else{
            $sort = "desc";
        }
        return " href='/admin/product/index.php?s=product-list&productCategoryId={$_GET['productCategoryId']}&searchValue={$_GET['searchValue']}&itemStyleId={$_GET['itemStyleId']}&orderBy={$orderBy}&sort={$sort}&page={$_GET['page']}' data-hl-orderby='{$orderBy}' ";
    }

    public function updateProductPriceByProductId(int $id){
        $sql = "SELECT item_sku,item_price,product_relation_item_count FROM product_relation LEFT JOIN item ON product_relation_item_id = item_id WHERE product_relation_product_id IN ({$id})";
        $result = $this->sqltool->getListBySql($sql);
        $amount = 0;
        if($result){
            foreach ($result as $row){
                $amount += ($row['item_price']*$row['product_relation_item_count']);
            }
        }
        $arr['product_price'] = $amount;
        $this->updateRowById('product',$id,$arr,false);
        return $id;
    }

    public function updateProductPriceByItemId(int $id){
        $sql = "SELECT product_relation_product_id FROM product_relation WHERE product_relation_item_id IN ($id)";
        $result = $this->sqltool->getListBySql($sql);
        $productIdArr = [];
        foreach ($result as $row){
            $productIdArr[] = $row['product_relation_product_id'];
        }
        //键值互换去重
        $productIdArr = array_flip($productIdArr);
        $productIdArr = array_flip($productIdArr);
        foreach ($productIdArr as $productId){
            $this->updateProductPriceByProductId($productId);
        }
    }

    public function updatePriceOfAllProducts(){
        $sql = "SELECT product_id FROM product";
        $result = $this->sqltool->getListBySql($sql);
        foreach ($result as $row){
            $productId = (int) $row['product_id'];
            $this->updateProductPriceByProductId($productId);
        }
    }

    public function updateInventoryOfAllProducts(){
        $sql = "SELECT product_id FROM product";
        $result = $this->sqltool->getListBySql($sql);
        foreach ($result as $row){
            $productId = (int) $row['product_id'];
            $this->updateProductInventoryByProductId($productId);
        }
    }


    /**
     * =================================================================
     * =================================================================
     * =================================================================
     * =================================================================
     * ====================   Product relation  ========================
     * =================================================================
     * =================================================================
     * =================================================================
     * =================================================================
     */
    private function modifyProductRelation(int $productId,$itemIdArr,$itemQuantityArr){

        //verify
        array_sum($itemIdArr) > 0 or Helper::throwException("Please select at least one item");
        $repeatVal = Helper::getRepeat($itemIdArr);
        count($repeatVal) == 0 or Helper::throwException("You have duplicate item ids : ".implode(',',$repeatVal));

        //delete old relation
        $this->deleteProductRelation($productId);

        //add new relation
        for($i=0; $i<count($itemIdArr); $i++){
            if($itemQuantityArr[$i]>0){
                $arr = [];
                $arr['product_relation_product_id'] = $productId;
                $arr['product_relation_item_id'] = (int) $itemIdArr[$i];
                $arr['product_relation_item_count'] = (int) $itemQuantityArr[$i];
                $this->addRow('product_relation',$arr);
            }
        }
    }

    private function deleteProductRelation($productIds){
        $ids = $productIds;
        if(!is_array($ids)) $ids = [$ids];
        $idStr = Helper::convertIDArrayToString($ids);
        $sql = "DELETE FROM product_relation WHERE product_relation_product_id IN ({$idStr})";
        $this->sqltool->query($sql);
    }

    public function getProductRelations(int $productId,array $option = []){
        if($option['join']){
            $sql = "SELECT * FROM product_relation LEFT JOIN item ON product_relation_item_id = item_id LEFT JOIN item_category ON item_category_id = item_item_category_id LEFT JOIN item_style ON item_style_id = item_item_style_id LEFT JOIN inventory ON item_id = inventory_item_id WHERE product_relation_product_id = '{$productId}'";
        }else{
            $sql = "SELECT * FROM product_relation WHERE product_relation_product_id = '{$productId}'";
        }
        return $this->sqltool->getListBySql($sql);
    }

}


?>
