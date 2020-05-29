<?php
try {
    global $userModel;
    $InventoryModel = new \model\InventoryModel();
    $warehouseId = (int) $_GET['warehouseId'];
    $userModel->isCurrentUserHasAuthority("WAREHOUSE","ADD") or Helper::throwException(null,403);
    $warehouse =  $InventoryModel->getWarehouses([$warehouseId])[0] or Helper::throwException(null,404);
} catch (Exception $e) {
    Helper::echoJson(0, $e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">WAREHOUSE / Manager</h4>
    </div>
    <label class="col-sm-8 control-label">
    </label>
</div>
<!--header end-->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading">Add a warehouse manager</div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form class="form-horizontal" action="/restAPI/inventoryController.php?action=addWarehouseManager" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="warehouse_manager_warehouse_id" value="<?=$warehouseId?>">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Warehouse</label>
                            <div class="col-sm-9">
                                <p class="form-control-static"><?=$InventoryModel->getWarehouseFullAddress($warehouse)?></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Assign a manager *</label>
                            <div class="col-sm-6">
                                <select name="warehouse_manager_user_id" class="user-search-select-ajax form-control">
                                    <option value="<?=$inventoryLog['deliver_id']?>" selected="selected"><?=$inventoryLog['deliver_first_name']?> <?=$inventoryLog['deliver_last_name']?> (<?=$inventoryLog['deliver_email']?>)</option>
                                </select>
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
