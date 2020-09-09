<?php
try {
    global $userModel;
    $inventoryModel = new \model\InventoryModel();
    $warehouseId = (int) $_GET['warehouseId'] or Helper::throwException("Warehouse Id can not be null");
    $type = $_GET['type'];
    $inventoryModel->verifyInventoryType($type);
    $flag = $type=='in'?'Stock-in':'Stock-out';
    if($type=="in"){
        $userModel->isCurrentUserHasAuthority("INVENTORY","STOCK_IN")
        ||  $userModel->isCurrentUserHasWarehouseManagementAuthority($warehouseId)
        or Helper::throwException(null,403);
        $flag = 'Stock-in';
    }else{
        $userModel->isCurrentUserHasAuthority("INVENTORY","STOCK_OUT")
        ||  $userModel->isCurrentUserHasWarehouseManagementAuthority($warehouseId)
        or Helper::throwException(null,403);
        $flag = 'Stock-out';
    }
    $warehouse = $inventoryModel->getWarehouses([$warehouseId],[],false)[0] or Helper::throwException("Warehouse id do NOT exist");
    $itemArr = $inventoryModel->getInventoryWarehouse([0],['warehouseId'=>$warehouseId],false);
} catch (Exception $e) {
    Helper::echoJson(0, $e->getMessage());
    die();
}
?>

<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">INVENTORY / <?=$flag?></h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(3);?>
    </label>
</div>
<!--header end-->

<!--item-box-template start-->
<div id="item-box-template" class="row m-b-10 item-box" style="display: none">
    <div class="col-sm-4">
        <div class="input-group">
            <div class="input-group-addon"><span class="num-box">#<span class="num">0</span></span></div>
            <select name="item_id[]" class="form-control select3 has-error">
                <option value="">-- Select --</option>
                <?php
                foreach ($itemArr as $item){
                    echo "<option data-quantity='{$item['inventory_warehouse_count']}' value='{$item['item_id']}' data-image='{$item['item_image']}'>{$item['item_sku']} ({$item['inventory_warehouse_count']})</option>";
                }
                ?>
            </select>
        </div>
    </div>
    <div class="col-sm-2">
        <input type="number" disabled max="" name="item_quantity[]" value="" class="quantity-input form-control" placeholder="0">
    </div>
</div>
<!--item-box-template end-->

<div class="row">
    <div class="col-sm-12">
        <div class="panel <?=$type=="in"?"panel-info":"panel-danger"?>">
            <div class="panel-heading"><?=$flag?></div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form action="/restAPI/inventoryController.php?action=modifyInventory" method="post" enctype="multipart/form-data">

                        <div class="form-body">
                            <input type="hidden" name="warehouse_id" value="<?=$warehouse['warehouse_id']?>">
                            <input type="hidden" name="inventory_log_type" value="<?=$type?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Warehouse</label>
                                        <input type="text" disabled value="<?=$inventoryModel->getWarehouseFullAddress($warehouse)?>" class="form-control" placeholder="">
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Deliver</label>
                                        <select name="inventory_log_deliver_id" class="user-search-select-ajax form-control"></select>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>

                            <h3 class="box-title m-t-20">Inventory Items</h3>
                            <div id="inventory-box"></div>

                            <div class="row m-t-20">
                                <div class="col-sm-12">
                                    <button id="add-item-btn" type="button" class="btn btn-outline btn-info waves-effect waves-light m-r-10"><i class="fa fa-plus-circle m-r-5"></i> Add 5 more</button>
                                    <?=$type=='in'?'<a class="text-info" target="_blank" href="/admin/inventory/index.php?s=inventory-warehouse-item-map-form&warehouseId='.$warehouseId.'">Did NOT find item in the select list? Try add ITEM LOCATION first</a>':''?>
                                </div>
                            </div>

                            <h3 class="box-title m-t-20">Note</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <textarea class="form-control" rows="3" name="inventory_log_note"></textarea>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>

                        </div>

                        <div class="form-actions">
                            <button type="submit" id="submitBtn" class="btn btn-info waves-effect waves-light m-t-10">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>