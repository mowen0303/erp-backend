<?php
namespace model;
use Exception;
use \Model as Model;
use \Helper as Helper;

class InventoryModel extends Model
{
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
        $arr['warehouse_latitude'] = (float) Helper::post('warehouse_longitude', 'Latitude can not be null');

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
        $deletedRows = $this->deleteByIDsReally('warehouse', $ids);
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
        $joinCondition = "";
        $whereCondition = "";
        $pageSize = $option['pageSize'] ?: 40;

        if (array_sum($ids) != 0) {
            $ids = Helper::convertIDArrayToString($ids);
            $whereCondition .= " AND warehouse_id IN ($ids)";
        }

        $sql = "SELECT * FROM warehouse WHERE true {$whereCondition} ORDER BY warehouse_id DESC";

        if (array_sum($ids) != 0 || !$enablePage) {
            return $this->sqltool->getListBySql($sql, $bindParams);
        } else {
            return $this->getListWithPage('warehouse', $sql, $bindParams, $pageSize);
        }
    }
}

?>
