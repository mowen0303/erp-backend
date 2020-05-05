<?php
try {
    global $userModel;
    $inventoryModel = new \model\InventoryModel();
    $inventoryWarehouseId = (int) $_GET['inventoryWarehouseId'];
    $itemModel = new \model\ItemModel();
    if ($inventoryWarehouseId) {
        //修改
        $option = [];
        $option['join']=true;
        $userModel->isCurrentUserHasAuthority("WAREHOUSE","UPDATE") or Helper::throwException(null,403);
        $row =  $inventoryModel->getInventoryWarehouse([$inventoryWarehouseId])[0] or Helper::throwException(null,404);
        $warehouseId = $row['inventory_warehouse_warehouse_id'];
        $itemArr = $itemModel->getItems([$row['inventory_warehouse_item_id']],$option,false);
    }else{
        $userModel->isCurrentUserHasAuthority("WAREHOUSE","ADD") or Helper::throwException(null,403) ;
        $warehouseId = (int) $_GET['warehouseId'] or Helper::throwException("Warehouse Id can not be null");
        $option = [];
        $option['join']=true;
        $option['warehouseMapStatus']='unAdded';
        $option['warehouseId']=$warehouseId;
        $itemArr = $itemModel->getItems([0],$option,false);
    }
    $warehouse = $inventoryModel->getWarehouses([$warehouseId])[0];
    $flag = $row?'Edit':'Add';
} catch (Exception $e) {
    Helper::echoJson(0, $e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-xs-4">
        <h4 class="page-title">WAREHOUSE / ITEM Location / <?=$flag?></h4>
    </div>
    <div class="col-xs-8">
        <?php Helper::echoBackBtn();?>
    </div>
</div>
<!--header end-->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading"><?=$flag?> Item Location</div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form class="form-horizontal" action="/restAPI/inventoryController.php?action=modifyInventoryMap" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="inventory_warehouse_id" value="<?=$row['inventory_warehouse_id']?>">
                        <input type="hidden" name="inventory_warehouse_warehouse_id" value="<?=$warehouseId?>">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Warehouse</label>
                            <div class="col-sm-9">
                                <p class="form-control-static"><?=$warehouse['warehouse_address']?>, <?=$warehouse['warehouse_city']?>, <?=$warehouse['warehouse_province']?>, <?=$warehouse['warehouse_post_code']?>, <?=$warehouse['warehouse_country']?></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Item *</label>
                            <div class="col-sm-6">
                                <select <?=$inventoryWarehouseId?"disabled":""?> name="inventory_warehouse_item_id" class="form-control erpSelect2" data-defvalue="<?=$row["inventory_warehouse_item_id"]?>">
                                    <option value="">-- Select --</option>
                                    <?php
                                    foreach ($itemArr as $item){
                                        echo "<option value='{$item['item_id']}' data-image='{$item['item_image']}'>{$item['item_sku']} | {$item['item_name']} | {$item['item_category_title']}</option>";
                                    }
                                    ?>
                                </select>
                                <span class="help-block"><small>Notice: Only show item which have no location yet</small></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Location *</label>
                            <div class="col-sm-3">
                                <input type="text" name="inventory_warehouse_aisle" value="<?=$row['inventory_warehouse_aisle']?>" class="form-control" placeholder="">
                                <span class="help-block"><small>Aisle</small></span>
                            </div>
                            <div class="col-sm-3">
                                <input type="text" name="inventory_warehouse_column" value="<?=$row['inventory_warehouse_column']?>" class="form-control" placeholder="">
                                <span class="help-block"><small>Column</small></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-info waves-effect waves-light m-t-10">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
