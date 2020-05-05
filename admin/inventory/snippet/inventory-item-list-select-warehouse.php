<?php
try {
    global $userModel;
    $inventoryModel = new \model\InventoryModel();
    $warehouseArr = $inventoryModel->getWarehouses([0],['sort'=>'asc'],false);
} catch (Exception $e) {
    Helper::echoJson(0, $e->getMessage());
    die();
}
?>

<!--header start-->
<div class="row bg-title">
    <div class="col-xs-4">
        <h4 class="page-title">INVENTORY / MANAGE STOCK</h4>
    </div>
    <div class="col-xs-8">
        <?php Helper::echoBackBtn(2);?>
    </div>
</div>
<!--header end-->


<!--<a href="/admin/inventory/index.php?s=inventory-item-list-warehouse&itemId=--><?//=$row['item_id']?><!--">-->

<?php
$index = 0;
foreach ($warehouseArr as $warehouse){
    echo $index++ % 3 == 0 ? '<div class="row"></div>' : '';
?>
    <div class="col-md-4 col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">Warehouse (#<?=$warehouse['warehouse_id']?>)</div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <p><?=$inventoryModel->getWarehouseFullAddress($warehouse)?></p>
                    <p>
                        <a href="/admin/inventory/index.php?s=inventory-item-list-form&type=in&warehouseId=<?=$warehouse['warehouse_id']?>" class="btn btn-info m-r-10"><i class="fas fa-plus-circle"></i>  Stock-in</a>
                        <a href="/admin/inventory/index.php?s=inventory-item-list-form&type=out&warehouseId=<?=$warehouse['warehouse_id']?>" class="btn btn-danger"><i class="fas fa-minus-circle"></i>  Stock-out</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>
