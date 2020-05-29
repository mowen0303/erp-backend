<?php
try {
    global $userModel;
    $inventoryModel = new \model\InventoryModel();
    $inventoryLogId = (int) $_GET['inventoryLogId'] or Helper::throwException("inventory Log Id can not be null");
    $userModel->isCurrentUserHasAuthority("INVENTORY_LOG","UPDATE") or Helper::throwException(null,403);
    $inventoryLog = $inventoryModel->getInventoryLog([$inventoryLogId])[0] or Helper::throwException("Can not find the inventory log record.");
    $warehouseId = $inventoryLog['inventory_log_warehouse_id'];
    $inventoryWarehouseLog = $inventoryModel->getInventoryWarehouseLog([0],["inventoryLogId"=>$inventoryLogId],false);
    $warehouseArr = $inventoryModel->getWarehouses([0],[],false) or Helper::throwException("Warehouse do NOT exist");
} catch (Exception $e) {
    Helper::echoJson(0, $e->getMessage());
    die();
}
?>

<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">INVENTORY / LOG / UPDATE</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(3);?>
    </label>
</div>
<!--header end-->

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-warning">
            <div class="panel-heading">UPDATE</div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form class="form-horizontal" action="/restAPI/inventoryController.php?action=updateInventory" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="warehouse_id" value="<?=$warehouse['warehouse_id']?>">
                        <input type="hidden" name="inventory_log_id" value="<?=$inventoryLogId?>">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Warehouse *</label>
                            <div class="col-sm-9">
                                <select name="warehouse_id" class="erpSelect2 form-control" data-defvalue="<?=$warehouseId?>">
                                    <?php foreach ($warehouseArr as $warehouse) {?>
                                        <option value="<?=$warehouse['warehouse_id']?>" selected="selected"><?=$inventoryModel->getWarehouseFullAddress($warehouse)?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Operator</label>
                            <div class="col-sm-6">
                                <select name="inventory_log_operator_id" class="user-search-select-ajax form-control">
                                    <option value="<?=$inventoryLog['operator_id']?>" selected="selected"><?=$inventoryLog['operator_first_name']?> <?=$inventoryLog['operator_last_name']?> (<?=$inventoryLog['operator_email']?>)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Deliver</label>
                            <div class="col-sm-6">
                                <select name="inventory_log_deliver_id" class="user-search-select-ajax form-control">
                                    <option value="<?=$inventoryLog['deliver_id']?>" selected="selected"><?=$inventoryLog['deliver_first_name']?> <?=$inventoryLog['deliver_last_name']?> (<?=$inventoryLog['deliver_email']?>)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Stock type</label>
                            <div class="col-sm-6">
                                <p class="form-control-static"><?=$inventoryModel->echoInventoryType($inventoryLog['inventory_log_type'])?></p>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Inventory form</label>
                            <div class="col-sm-9">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-3">Item</div>
                            <div class="col-sm-2">Quantity</div>
                        </div>

                        <?php foreach ($inventoryWarehouseLog as $row) {?>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"><span class="num-box">#<span class="num">0</span></span></label>
                            <div class="col-sm-3">
                                <input name="inventory_warehouse_log_id[]" value="<?=$row['inventory_warehouse_log_id']?>" type="hidden">
                                <select name="item_id[]" class="form-control">
                                    <option value="<?=$row['item_id']?>"><?=$row['item_sku']?></option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <input type="number" name="item_quantity[]" value="<?=$row['inventory_warehouse_log_count']?>" class="quantity-input form-control" placeholder="0">
                            </div>
                        </div>
                        <?php } ?>

                        <hr>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Notes</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" rows="3" name="inventory_log_note"><?=$inventoryLog['inventory_log_note']?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-9">
                                <button type="submit" id="submitBtn" class="btn btn-info waves-effect waves-light m-t-10">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>