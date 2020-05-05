<?php
try {
    global $userModel;
    $userModel->isCurrentUserHasAuthority("INVENTORY","GET_LIST") or Helper::throwException(null,403);
    $warehouseId = (int) $_GET['warehouseId'] or Helper::throwException("Warehouse Id can not be null");
    $inventoryModel = new \model\InventoryModel();
    $warehouse = $inventoryModel->getWarehouses([$warehouseId])[0];
    $arr = $inventoryModel->getInventoryWarehouse([0],['warehouseId'=>$warehouseId],false);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-xs-4">
        <h4 class="page-title">WAREHOUSE / ITEM LOCATION</h4>
    </div>
    <div class="col-xs-8">
        <?php Helper::echoBackBtn(2);?>
        <a href="/admin/inventory/index.php?s=inventory-warehouse-item-map-form&warehouseId=<?=$warehouseId?>" class="btn btn-danger pull-right"><i class="fas fa-plus-circle"></i>  Add Item Location</a>
    </div>
</div>
<!--header end-->


<div class="row">
    <div class="col-md-6 col-sm-12 col-lg-4">
        <div class="panel">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-8"><i class="mdi mdi-city fa-fw"></i> WAREHOUSE INFO</div>
                    <div class="col-md-4 text-right">
                        <a href="/admin/inventory/index.php?s=inventory-warehouse-form&warehouseId=<?=$warehouseId?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Edit"><i class="ti-marker-alt"></i></a>
                        <a onclick="return confirm('Are you sure to delete?')" href="/restAPI/inventoryController.php?action=deleteWarehouseByIds&id=<?=$warehouseId?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Delete"><i class="ti-trash"></i></a>
                    </div>
                </div>
            </div>
            <hr class="m-t-0 m-b-0">
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
                    <form class="form-horizontal" role="form">
                        <div class="form-body">
                            <table class="table3">
                                <tr>
                                    <td>Address</td>
                                    <td><?=$inventoryModel->getWarehouseFullAddress($warehouse)?></td>
                                </tr>
                                <tr>
                                    <td>Phone</td>
                                    <td><?=$warehouse['warehouse_phone']?></td>
                                </tr>
                                <tr>
                                    <td>Fax</td>
                                    <td><?=$warehouse['warehouse_fax']?></td>
                                </tr>
                                <tr>
                                    <td>Description</td>
                                    <td><?=$warehouse['warehouse_description']?></td>
                                </tr>
                                <tr>
                                    <td>Latitude, Longitude</td>
                                    <td><a href="https://www.google.com/maps/search/?api=1&query=<?=$warehouse['warehouse_latitude']?>,<?=$warehouse['warehouse_longitude']?>" target="_blank"><?=$warehouse['warehouse_latitude']?>,<?=$warehouse['warehouse_longitude']?></a></td>
                                </tr>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-8 col-sm-12">
        <div class="white-box">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="box-title">ITEM INVENTORY</h3>
                </div>
            </div>
            <form action="/restAPI/inventoryController.php?action=deleteInventoryWarehouseByIds" method="post">
                <div class="table-responsive">
                    <table class="table color-table dark-table table-hover">
                        <thead>
                        <tr>
                            <th width="21"><input id="cBoxAll" type="checkbox"></th>
                            <th width="40">IMAGE</th>
                            <th>SKU#</th>
                            <th>ITEM NAME</th>
                            <th>Aisle</th>
                            <th>Column</th>
                            <th>Quantity</th>
                            <th width="50"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($arr as $row) {
                            ?>
                            <tr>
                                <td><input type="checkbox" class="cBox" name="id[]" value="<?=$row['inventory_warehouse_id']?>"></td>
                                <td><a href="<?=$row['item_image']?:NO_IMG?>" data-toggle="lightbox"  data-title="<?=$row['item_sku']?>"><div class="avatar avatar-40 img-rounded" style="background-image: url('<?=$row['item_image']?:NO_IMG?>')"></div></a></td>
                                <td><?=$row['item_sku'] ?></td>
                                <td><?=$row['item_name'] ?></td>
                                <td><?=$row['inventory_warehouse_aisle'] ?></td>
                                <td><?=$row['inventory_warehouse_column'] ?></td>
                                <td><?=$row['inventory_warehouse_count'] ?></td>
                                <td style="text-align: center">
                                    <a href="/admin/inventory/index.php?s=inventory-warehouse-item-map-form&inventoryWarehouseId=<?=$row['inventory_warehouse_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Edit"><i class="ti-marker-alt"></i></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-8"><?=$inventoryModel->echoPageList()?></div>
                        <div class="col-sm-4 text-right">
                            <button id="deleteBtn" style="display: none" type="submit" class="btn btn-info waves-effect waves-light m-t-10" onclick="return confirm('Are you sure to delete?')">Delete</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


</div>
