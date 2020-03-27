<?php
namespace model;
use Exception;
use \Model as Model;
use \Helper as Helper;

class WarehouseModel extends Model
{
    /**
     * @param int $id
     * @return int
     * @throws Exception
     */
    public function modifyWarehouse($id = 0)
    {
        $arr['item_item_category_id'] = Helper::post('item_item_category_id', 'Category can not be null', 1, 11);
        $arr['item_item_style_id'] = Helper::post('item_item_style_id', 'Style can not be null', 1, 11);
        $arr['item_sku'] = Helper::post('item_sku', 'SKU can not be null', 1, 50);
        $arr['item_w'] = Helper::post('item_w', 'Item Width can not be null', 1, 9);
        $arr['item_h'] = Helper::post('item_h', 'Item Height can not be null', 1, 9);
        $arr['item_d'] = Helper::post('item_d', 'Item Depth can not be null', 1, 9);
        $arr['item_description'] = Helper::post('item_description', null, 1, 255) ?: "";
        $arr['item_price'] = Helper::post('item_price', 'Price can not be null', 1, 10);
        if ($id) {
            //修改
            !$this->isExistByFieldValue('item', 'item_sku', $arr['item_sku'], $id) or Helper::throwException('SKU has already existed', 400);
            $this->updateRowById('item', $id, $arr, false);
        } else {
            //添加
            !$this->isExistByFieldValue('item', 'item_sku', $arr['item_sku']) or Helper::throwException('SKU has already existed', 400);
            $id = $this->addRow('item', $arr);
        }
        return $id;
    }

    public function deleteWarehouseByIds()
    {
        $ids = Helper::request('id', 'Id can not be null');
        if (!is_array($ids)) $ids = [$ids];
        $deletedRows = $this->deleteByIDsReally('item', $ids);
        return $deletedRows;
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
        $selectFields = "";
        $joinCondition = "";
        $whereCondition = "";
        $orderCondition = "";
        $pageSize = $option['pageSize'] ?: 40;

        if (array_sum($ids) != 0) {
            $ids = Helper::convertIDArrayToString($ids);
            $whereCondition .= " AND item_id IN ($ids)";
        }

        if ($option['join']) {
            $joinCondition .= "LEFT JOIN item_category ON item_item_category_id = item_category_id LEFT JOIN item_style ON item_item_style_id = item_style_id";
        }

        if ($option['itemCategoryId']) {
            $itemCategoryId = (int)$option['itemCategoryId'];
            $whereCondition .= " AND item_item_category_id IN ({$itemCategoryId})";
        }

        //SORT
        $sort = $option['sort'] == "asc" ? "ASC" : "DESC";
        if ($option['orderBy'] == 'price') {
            $orderCondition = "item_price {$sort},";
        } else if ($option['orderBy'] == 'sku') {
            $orderCondition = "item_sku {$sort},";
        } else if ($option['orderBy'] == 'width') {
            $orderCondition = "item_w {$sort},";
        } else if ($option['orderBy'] == 'height') {
            $orderCondition = "item_h {$sort},";
        } else if ($option['orderBy'] == 'depth') {
            $orderCondition = "item_d {$sort},";
        } else if ($option['orderBy'] == 'style' && $option['join']) {
            $orderCondition = "item_style_title {$sort},";
        }

        $sql = "SELECT * FROM item {$joinCondition} WHERE true {$whereCondition} ORDER BY {$orderCondition} item_id DESC";

        if (array_sum($ids) != 0 || !$enablePage) {
            return $this->sqltool->getListBySql($sql, $bindParams);
        } else {
            return $this->getListWithPage('item', $sql, $bindParams, $pageSize);
        }
    }
}

?>
