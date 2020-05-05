<?php
try {
    global $userModel;
    $userModel->isCurrentUserHasAuthority("INVENTORY","GET_LIST") or Helper::throwException(null,403);
    $inventoryModel = new \model\InventoryModel();
    $arr = $inventoryModel->getInventoryLog([0],$_GET);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-xs-4">
        <h4 class="page-title">INVENTORY</h4>
    </div>
    <div class="col-xs-8">
        <?php Helper::echoBackBtn(1);?>
    </div>
</div>
<!--header end-->


<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="box-title">WAREHOUSE List</h3>
                </div>
            </div>
                <div class="table-responsive">
                    <table class="table color-table dark-table table-hover">
                    <thead>
                    <tr>
                        <th>DATE</th>
                        <th>OPERATOR</th>
                        <th>DELIVER</th>
                        <th>Warehouse</th>
                        <th>INVENTORY TYPE</th>
                        <th width="70"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($arr as $row) {
                    ?>
                        <tr>
                            <td data-hl-orderby="date"><?=$row['inventory_log_time']?></td>
                            <td><a href="/admin/user/index.php?s=user-list-profile&userId=<?=$row['operator_id']?>"><?=$row['operator_first_name']?> <?=$row['operator_last_name']?></a><br><?=$row['operator_email']?></td>
                            <td><a href="/admin/user/index.php?s=user-list-profile&userId=<?=$row['deliver_id']?>"><?=$row['deliver_first_name']?> <?=$row['deliver_last_name']?></a><br><?=$row['deliver_email']?></td>
                            <td><?=$row['warehouse_address']?></td>
                            <td data-hl-orderby="type"><?=$inventoryModel->echoInventoryType($row['inventory_log_type'])?></td>
                            <td><a href="/admin/inventory/index.php?s=inventory-log-list-detail&inventoryLog=<?=$row['inventory_log_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="View log detail"><i class="ti-receipt"></i></a></td>
                        </tr>
                    <?php }  ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-sm-8"><?=$inventoryModel->echoPageList()?></div>
                    <div class="col-sm-4 text-right"></div>
                </div>
            </div>
        </div>
    </div>
</div>