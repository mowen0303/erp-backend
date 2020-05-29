<?php
try {
    global $userModel;
    $warehouseId = (int) $_GET['warehouseId'] or Helper::throwException("Warehouse Id can not be null");
    $userModel->isCurrentUserHasAuthority("INVENTORY","GET_LIST")
    ||  $userModel->isCurrentUserHasWarehouseManagementAuthority($warehouseId) or Helper::throwException(null,403);
    $inventoryModel = new \model\InventoryModel();
    $itemModel = new \model\ItemModel();
    $warehouse = $inventoryModel->getWarehouses([$warehouseId])[0];
    $_GET['warehouseId'] = $warehouseId;
    $arr = $inventoryModel->getInventoryWarehouse([0],$_GET,false);
    $managerArr = $inventoryModel->getWarehouseManager($warehouseId);
    $itemStyleArr = $itemModel->getItemStyles([0],[],false);
    $itemCategoryArr = $itemModel->getItemCategories([0],[],false);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">WAREHOUSE / INVENTORY</h4>
    </div>
    <label class="col-sm-8 control-label">
        <a href="/admin/inventory/index.php?s=inventory-item-list-form&type=in&warehouseId=<?=$warehouse['warehouse_id']?>" class="btn btn-success m-r-10"><i class="fas fa-plus-circle"></i>  Stock-in</a>
        <a href="/admin/inventory/index.php?s=inventory-item-list-form&type=out&warehouseId=<?=$warehouse['warehouse_id']?>" class="btn btn-danger m-r-10"><i class="fas fa-minus-circle"></i>  Stock-out</a>
        <a href="/admin/inventory/index.php?s=inventory-warehouse-item-map-form&warehouseId=<?=$warehouseId?>" class="btn btn-info m-r-10"><i class="fas fa-plus-circle"></i>  Add Item Location</a>
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modal" data-whatever="@mdo"><i class="fas fa-info-circle"></i>  Warehouse Info</button>
        <?php Helper::echoBackBtn(1);?>
    </label>
</div>
<!--header end-->

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h3 class="box-title">Search INVENTORY</h3>
            <form class="" action="/admin/inventory/index.php" method="get">
                <input type="hidden" name="s" value="inventory-warehouse-item">
                <input type="hidden" name="warehouseId" value="<?=$warehouseId?>">
                <div class="row">
                    <div class="col-sm-10">
                        <input class="form-control" placeholder="Item Key Words" type="text" name="searchValue" value="<?=$_GET['searchValue']?>">
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-block btn-info waves-effect waves-light" type="submit">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel1">Approve the application</h4>
            </div>
            <div class="row">
                <div class="col-sm-12">
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
                                                <td>Warehouse Managers</td>
                                                <td>
                                                    <table>
                                                        <tr>
                                                            <?php foreach ($managerArr as $manager){?>
                                                                <td>
                                                                    <div class="avatar-container">
                                                                        <a onclick="return confirm('Are you sure to remove the manager?')" href="/restAPI/inventoryController.php?action=deleteWarehouseManager&id=<?=$manager['warehouse_manager_id']?>" class="avatar-close">x</a>
                                                                        <a href="/admin/user/index.php?s=user-list-profile&userId=<?=$manager['user_id']?>" target="_blank" class="avatar avatar-40 circle" title="<?=$manager['user_last_name']?> <?=$manager['user_first_name']?>" data-toggle="tooltip" style="background-image: url('<?=$manager['user_avatar']?>')">&nbsp;</a>
                                                                    </div>
                                                                </td>
                                                            <?php } ?>
                                                            <td>
                                                                <a class="button-circle-dash" href="/admin/inventory/index.php?s=inventory-warehouse-manager-form&warehouseId=<?=$warehouseId?>" target="_blank">+</a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
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
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="box-title"><?=$warehouse['warehouse_address']?> / ITEM LIST</h3>
                </div>
            </div>
            <div class="row m-b-20">
                <form action="/admin/inventory/index.php" method="get">
                    <input type="hidden" name="s" value="inventory-warehouse-item">
                    <input type="hidden" name="warehouseId" value="<?=$warehouseId?>">
                    <div class="col-sm-3">
                        <select name="itemCategoryId" class="form-control" data-defvalue="<?=$_GET['itemCategoryId']?>">
                            <option value="">All</option>
                            <?php
                            foreach ($itemCategoryArr as $itemCategory){
                                echo "<option value=\"{$itemCategory['item_category_id']}\">{$itemCategory['item_category_title']}</option>";
                            }
                            ?>
                        </select>
                        <span class="help-block"><small>Filter by category</small></span>
                    </div>
                    <div class="col-sm-3">
                        <select name="itemStyleId" class="form-control" data-defvalue="<?=$_GET['itemStyleId']?>">
                            <option value="">All</option>
                            <?php
                            foreach ($itemStyleArr as $itemStyle){
                                echo "<option value=\"{$itemStyle['item_style_id']}\">{$itemStyle['item_style_title']}</option>";
                            }
                            ?>
                        </select>
                        <span class="help-block"><small>Filter by style</small></span>
                    </div>
                    <div class="col-sm-2">
                        <select name="orderBy" class="form-control" data-defvalue="<?=$_GET['orderBy']?>">
                            <option value="">Default</option>
                            <option value="sku">SKU</option>
                            <option value="length">Length</option>
                            <option value="width">Width</option>
                            <option value="height">Height</option>
                            <option value="style">Style</option>
                        </select>
                        <span class="help-block"><small>Order by</small></span>
                    </div>
                    <div class="col-sm-2">
                        <select name="sort" class="form-control" data-defvalue="<?=$_GET['sort']?>">
                            <option value="desc">▾ Descending</option>
                            <option value="asc">▴ Ascending</option>
                        </select>
                        <span class="help-block"><small>Sort</small></span>
                    </div>
                    <div class="col-sm-2 text-right">
                        <button type="submit" class="btn btn-block btn-info waves-effect waves-light">Filter</button>
                    </div>
                </form>
            </div>
            <form action="/restAPI/inventoryController.php?action=deleteInventoryWarehouseByIds" method="post">
                <div class="table-responsive">
                    <table class="table color-table dark-table table-hover">
                        <thead>
                        <tr>
                            <th width="21"><input id="cBoxAll" type="checkbox"></th>
                            <th width="40">IMAGE</th>
                            <th>SKU#</th>
                            <th>NAME</th>
                            <th>L (M)</th>
                            <th>W (M)</th>
                            <th>H (M)</th>
                            <th>STYLE</th>
                            <th>CATEGORY</th>
                            <th>LOCATION</th>
                            <th>QUANTITY</th>
                            <th width="80"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($arr as $row) {
                            ?>
                            <tr>
                                <td><input type="checkbox" class="cBox" name="id[]" value="<?=$row['inventory_warehouse_id']?>"></td>
                                <td><a href="<?=$row['item_image']?:NO_IMG?>" data-toggle="lightbox"  data-title="<?=$row['item_sku']?>"><div class="avatar avatar-40 img-rounded" style="background-image: url('<?=$row['item_image']?:NO_IMG?>')"></div></a></td>
                                <td data-hl-orderby="sku" data-hl-search><?=$row['item_sku'] ?></td>
                                <td><?=$row['item_name'] ?></td>
                                <td data-hl-orderby="length"><?=floatval($row['item_l'])?></td>
                                <td data-hl-orderby="width"><?=floatval($row['item_w'])?></td>
                                <td data-hl-orderby="height"><?=floatval($row['item_h'])?></td>
                                <td data-hl-orderby="style"><?=$row['item_style_title']?></td>
                                <td><?=$row['item_category_title']?></td>
                                <td><?=$row['inventory_warehouse_aisle'] ?>-<?=$row['inventory_warehouse_column'] ?></td>
                                <td><?=$row['inventory_warehouse_count'] ?></td>
                                <td style="text-align: center">
                                    <a href="/admin/inventory/index.php?s=inventory-item-list-warehouse-log&itemId=<?=$row['item_id']?>&warehouseId=<?=$warehouseId?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="View stock logs"><i class="ti-receipt"></i></a>
                                    <a href="/admin/inventory/index.php?s=inventory-warehouse-item-map-form&inventoryWarehouseId=<?=$row['inventory_warehouse_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Edit Item Location"><i class="ti-marker-alt"></i></a>
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
