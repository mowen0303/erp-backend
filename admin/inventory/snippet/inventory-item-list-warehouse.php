<?php
try {
    global $userModel;
    $userModel->isCurrentUserHasAuthority("INVENTORY","GET_LIST") or Helper::throwException(null,403);
    $inventoryModel = new \model\InventoryModel();
    $itemId = (int) $_GET['itemId'] or Helper::throwException("Item Id can not be null");
    $arr = $inventoryModel->getInventoryWarehouse([0],['itemId'=>$itemId]);
    $item = $arr[0];
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">INVENTORY / WAREHOUSE</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(2);?>
    </label>
</div>
<!--header end-->


<div class="row">
    <div class="col-md-8 col-lg-9 col-sm-12">
        <div class="white-box">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="box-title">INVENTORY WAREHOUSE LIST</h3>
                </div>
            </div>
            <form action="/restAPI/inventoryController.php?action=deleteWarehouseByIds" method="post">
                <div class="table-responsive">
                    <table class="table color-table dark-table table-hover">
                        <thead>
                            <tr>
                                <th>WAREHOUSE</th>
                                <th>LOCATION</th>
                                <th>QUANTITY</th>
                                <th width="40"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($arr as $row) {
                        ?>
                            <tr>
                                <td><?=$inventoryModel->getWarehouseFullAddress($row)?></td>
                                <td><?=$row['inventory_warehouse_aisle']?>-<?=$row['inventory_warehouse_column']?></td>
                                <td><?=$row['inventory_warehouse_count']?></td>
                                <td><a href="/admin/inventory/index.php?s=inventory-item-list-warehouse-log&itemId=<?=$itemId?>&warehouseId=<?=$row['inventory_warehouse_warehouse_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="View stock logs"><i class="ti-receipt"></i></a></td>
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
    <div class="col-md-4 col-lg-3 col-sm-12">
        <div class="panel">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-8"><i class="mdi mdi-tag fa-fw"></i> ITEM INFO</div>
                </div>
            </div>
            <hr class="m-t-0 m-b-0">
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
                    <table class="table3">
                        <tr>
                            <td width="90">Image</td>
                            <td><a href="<?=$item['item_image']?:NO_IMG?>" data-toggle="lightbox"  data-title="<?=$item['item_sku']?>"><div class="avatar avatar-40 img-rounded" style="background-image: url('<?=$item['item_image']?:NO_IMG?>')"></div></a></td>
                        </tr>
                        <tr>
                            <td>SKU#</td>
                            <td><?=$item['item_sku']?></td>
                        </tr>
                        <tr>
                            <td>L (M)</td>
                            <td><?=$warehouse['item_l']?></td>
                        </tr>
                        <tr>
                            <td>W (M)</td>
                            <td><?=$warehouse['item_w']?></td>
                        </tr>
                        <tr>
                            <td>H (M)</td>
                            <td><?=$warehouse['item_h']?></td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td><?=$warehouse['item_description']?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>