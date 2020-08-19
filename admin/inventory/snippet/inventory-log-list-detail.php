<?php
try {
    global $userModel;
    $inventoryLogId = (int) $_GET['inventoryLog'] or Helper::throwException("Inventory Log Id can not be null");
    $userModel->isCurrentUserHasAuthority("INVENTORY","GET_LIST") or Helper::throwException(null,403);
    $inventoryModel = new \model\InventoryModel();
    $inventoryLog = $inventoryModel->getInventoryLog([$inventoryLogId])[0];
    $arr = $inventoryModel->getInventoryWarehouseLog([0],["inventoryLogId"=>$inventoryLogId],false);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">INVENTORY</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(2);?>
        <a href="/admin/inventory/index.php?s=inventory-item-list-form-update&inventoryLogId=<?=$inventoryLogId?>" class="btn btn-danger pull-right m-l-10">Edit</a>
    </label>
</div>
<!--header end-->

<div class="row">
    <div class="col-md-12">
        <div class="white-box printableArea">
            <h3><b>INVENTORY RECORD</b> <span class="pull-right">#<?=$inventoryLog['inventory_log_id']?></span></h3>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <div class="pull-left">
                        <address>
                            <h3>Warehouse:</h3>
                            <p class="m-l-5"><?=$inventoryModel->getWarehouseFullAddress($inventoryLog)?></p>
                            <p class="text-muted m-l-5"><?=$inventoryLog['warehouse_phone']?></p>
                        </address>
                    </div>
                    <div class="pull-right text-right">
                        <h3>&nbsp;</h3>
                        <p><?=$inventoryModel->echoInventoryType($inventoryLog['inventory_log_type'])?></p>
                        <p><?=$inventoryLog['inventory_log_time']?></p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="pull-left">
                        <address>
                            <h3>Operator:</h3>
                            <h3> &nbsp;<b class="text-danger"><a href="/admin/user/index.php?s=user-list-profile&userId=<?=$inventoryLog['operator_id']?>"><?=$inventoryLog['operator_first_name']?> <?=$inventoryLog['operator_last_name']?></a></b></h3>
                            <p class="text-muted m-l-5">
                                <?=$inventoryLog['operator_email']?>
                                <br> <?=$inventoryLog['operator_phone']?>
                                <br> <?=$inventoryLog['operator_address']?>
                            </p>
                        </address>
                    </div>
                    <div class="pull-right text-right">
                        <address>
                            <h3>Deliver:</h3>
                            <h4 class="font-bold"><a href="/admin/user/index.php?s=user-list-profile&userId=<?=$inventoryLog['deliver_id']?>"><?=$inventoryLog['deliver_first_name']?> <?=$inventoryLog['deliver_last_name']?></a></h4>
                            <p class="text-muted m-l-30">
                                <?=$inventoryLog['deliver_email']?>
                                <br> <?=$inventoryLog['deliver_phone']?>
                                <br> <?=$inventoryLog['deliver_address']?>
                            </p>
                        </address>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="table-responsive m-t-20" style="clear: both;">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>SKU#</th>
                                <th class="text-right">L (M)</th>
                                <th class="text-right">W (M)</th>
                                <th class="text-right">H (M)</th>
                                <th class="text-right">Quantity</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $index = 0;
                            $total = 0;
                            foreach ($arr as $row) {
                                $index++;
                                $total += $row['inventory_warehouse_log_count'];
                            ?>
                                <tr>
                                    <td class="text-center"><?=$index?></td>
                                    <td><?=$row['item_sku']?></td>
                                    <td class="text-right"><?=floatval($row['item_l'])?></td>
                                    <td class="text-right"><?=floatval($row['item_w'])?></td>
                                    <td class="text-right"><?=floatval($row['item_h'])?></td>
                                    <td class="text-right"><?=$row['inventory_warehouse_log_count']?></td>
                                </tr>
                            <?php }  ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="pull-right m-t-30 text-right">
                        <h3><b>Total : </b><?=$total?> Unites</h3> </div>
                    <div class="clearfix"></div>
                    <hr>
                    <div class="text-right">
                        <button id="print" class="btn btn-default btn-outline" type="button"> <span><i class="fa fa-print"></i> Print</span> </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>