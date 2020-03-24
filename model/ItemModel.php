<?php
namespace model;
use Exception;
use \Model as Model;
use \Helper as Helper;

class ItemModel extends Model
{
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

    /**
     * @param int $id
     * @return int
     * @throws Exception
     */
    public function modifyItem($id=0){
        $arr['item_category_title'] = Helper::post('item_category_title','Item Category Title can not be null',1,255);
        if ($id) {
            //修改
            $this->updateRowById('item_category', $id, $arr,false);
        } else {
            //添加
            !$this->isExistByFieldValue('item_category','item_category_title',$arr['item_category_title']) or Helper::throwException('Item Category Title has already existed',400);
            $id = $this->addRow('item_category', $arr);
        }
        $fileModel = new FileModel();
        $this->imgError = $fileModel->modifyFileAndTableData('item_category',$id,'item_category_image','file');
        return $id;
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

    /**
     * @param int $id
     * @return int
     * @throws Exception
     */
    public function modifyItemCategory($id=0){
        $arr['item_category_title'] = Helper::post('item_category_title','Item Category Title can not be null',1,255);
        if ($id) {
            //修改
            $this->updateRowById('item_category', $id, $arr,false);
        } else {
            //添加
            !$this->isExistByFieldValue('item_category','item_category_title',$arr['item_category_title']) or Helper::throwException('Item Category Title has already existed',400);
            $id = $this->addRow('item_category', $arr);
        }
        $fileModel = new FileModel();
        $this->imgError = $fileModel->modifyFileAndTableData('item_category',$id,'item_category_image','file');
        return $id;
    }

    /**
     * @param array $ids
     * @param array $option
     * @param bool $enablePage
     * @return array
     * @throws Exception
     */
    public function getItemCategories(array $ids,array $option = [],$enablePage=true){
        $bindParams = [];
        $selectFields = "";
        $whereCondition = "";
        $orderCondition = "";
        $pageSize   = $option['pageSize']?:40;

        if(array_sum($ids)!=0){
            $ids = Helper::convertIDArrayToString($ids);
            $whereCondition .= " AND item_category_id IN ($ids)";
        }

        $sql = "SELECT * FROM item_category WHERE true {$whereCondition} ORDER BY item_category_id DESC";

        if(array_sum($ids)!=0 || !$enablePage){
            return $this->sqltool->getListBySql($sql,$bindParams);
        }else{
            return $this->getListWithPage('item_category',$sql,$bindParams,$pageSize);
        }
    }

    /**
     * @return null |null
     * @throws Exception
     */
    public function deleteItemCategoryByIds(){
        $ids = Helper::request('id','Id can not be null');
        if(!is_array($ids)) $ids = [$ids];
        $idsStr = Helper::convertIDArrayToString($ids);
        //TODO 增加删除限制，查询item是否在用
//        $sql = "SELECT store_id FROM store WHERE store_company_id IN ({$idsStr})";
//        $result = $this->sqltool->getListBySql($sql);
        $result = false;
        if($result){
            Helper::throwException("Can not delete the Company you selected because there are stores belongs to the Company.<br> If you want to delete the company, please remove all the store within the company first.");
        }else{
            $fileModel = new FileModel();
            $arr = $this->getItemCategories($ids);
            foreach ($arr as $row){
                $fileModel->deleteFileByPath($row['item_category_image']);
            }
            $deletedRows = $this->deleteByIDsReally('item_category', $ids);
            return $deletedRows;
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

    /**
     * @param int $id
     * @return int
     * @throws Exception
     */
    public function modifyItemStyle($id=0){
        $arr['item_style_title'] = Helper::post('item_style_title','Item Style Title can not be null',1,255);
        if ($id) {
            //修改
            $this->updateRowById('item_style', $id, $arr,false);
        } else {
            //添加
            !$this->isExistByFieldValue('item_style','item_style_title',$arr['item_style_title']) or Helper::throwException('Item Style has already existed',400);
            $id = $this->addRow('item_style', $arr);
        }
        return $id;
    }

    public function getItemStyles(array $ids,array $option = [],$enablePage=true){
        $bindParams = [];
        $selectFields = "";
        $whereCondition = "";
        $orderCondition = "";
        $pageSize   = $option['pageSize']?:40;

        if(array_sum($ids)!=0){
            $ids = Helper::convertIDArrayToString($ids);
            $whereCondition .= " AND item_style_id IN ($ids)";
        }

        $sql = "SELECT * FROM item_style WHERE true {$whereCondition} ORDER BY item_style_id DESC";

        if(array_sum($ids)!=0 || !$enablePage){
            return $this->sqltool->getListBySql($sql,$bindParams);
        }else{
            return $this->getListWithPage('item_style',$sql,$bindParams,$pageSize);
        }
    }

    /**
     * @return null |null
     * @throws Exception
     */
    public function deleteItemStyleByIds(){
        $ids = Helper::request('id','Id can not be null');
        if(!is_array($ids)) $ids = [$ids];
        $idsStr = Helper::convertIDArrayToString($ids);
        //TODO 增加删除限制，查询item是否在用
//        $sql = "SELECT store_id FROM store WHERE store_company_id IN ({$idsStr})";
//        $result = $this->sqltool->getListBySql($sql);
        $result = false;
        if($result){
            Helper::throwException("Can not delete the Company you selected because there are stores belongs to the Company.<br> If you want to delete the company, please remove all the store within the company first.");
        }else{
            $deletedRows = $this->deleteByIDsReally('item_style', $ids);
            return $deletedRows;
        }
    }

}


?>
