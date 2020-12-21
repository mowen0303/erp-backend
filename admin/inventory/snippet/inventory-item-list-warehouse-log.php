<?php
try {
    global $userModel;
    $warehouseId = (int) $_GET['warehouseId'] or Helper::throwException("Warehouse Id can not be null");
    $itemId = (int) $_GET['itemId'] or Helper::throwException("Item Id can not be null");
    $userModel->isCurrentUserHasAuthority("INVENTORY","GET_LIST")
    ||  $userModel->isCurrentUserHasWarehouseManagementAuthority($warehouseId) or Helper::throwException(null,403);
    $inventoryModel = new \model\InventoryModel();
    $arr = $inventoryModel->getInventoryWarehouseLog([0],['itemId'=>$itemId,'warehouseId'=>$warehouseId]);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">INVENTORY / WAREHOUSE / STOCK LOG</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(3);?>
    </label>
</div>
<!--header end-->


<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="box-title">INVENTORY WAREHOUSE STOCK LOG LIST</h3>
                </div>
            </div>
            <form action="/restAPI/inventoryController.php?action=deleteWarehouseByIds" method="post">
                <div class="table-responsive">
                    <table class="table color-table dark-table table-hover">
                        <thead>
                            <tr>
                                <th>DATE</th>
                                <th>WAREHOUSE</th>
                                <th>OPERATOR</th>
                                <th>DELIVER</th>
                                <th>SKU#</th>
                                <th>STOCK TYPE</th>
                                <th>QUANTITY</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($arr as $row) {
                        ?>
                            <tr>
                                <td><?=$row['inventory_log_time']?></td>
                                <td><?=$inventoryModel->getWarehouseFullAddress($row)?></td>
                                <td><a href="/admin/user/index.php?s=user-list-profile&userId=<?=$row['operator_id']?>"><?=$row['operator_first_name']?> <?=$row['operator_last_name']?></a><br><?=$row['operator_email']?></td>
                                <td><a href="/admin/user/index.php?s=user-list-profile&userId=<?=$row['deliver_id']?>"><?=$row['deliver_first_name']?> <?=$row['deliver_last_name']?></a><br><?=$row['deliver_email']?></td>
                                <td data-hl-orderby="sku" data-hl-search><?=$row['item_sku'] ?></td>
                                <td><?=$inventoryModel->echoInventoryType($row['inventory_log_type'])?></td>
                                <td><?=$row['inventory_warehouse_log_count']?></td>
                            </tr>
                        <?php }  ?>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-sm-8"><?=$inventoryModel->echoPageList()?></div>
                    <div class="col-sm-4 text-right">
                        <button id="deleteBtn" style="display: none" type="submit" class="btn btn-info waves-effect waves-light m-t-10" onclick="return confirm('Are you sure to delete?')">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>