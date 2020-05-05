<?php
try {
    global $userModel;
    $userModel->isCurrentUserHasAuthority("INVENTORY","GET_LIST") or Helper::throwException(null,403);
    $inventoryModel = new \model\InventoryModel();
    $arr = $inventoryModel->getInventory([0],$_GET);
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
        <a href="/admin/inventory/index.php?s=inventory-item-list-select-warehouse" class="btn btn-danger pull-right m-l-10">Inventory Management</a>
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
            <form action="/restAPI/inventoryController.php?action=deleteWarehouseByIds" method="post">
                <div class="table-responsive">
                    <table class="table color-table dark-table table-hover">
                    <thead>
                    <tr>
                        <th width="40">IMAGE</th>
                        <th>SKU#</th>
                        <th>NAME</th>
                        <th>L (M)</th>
                        <th>W (M)</th>
                        <th>H (M)</th>
                        <th>STYLE</th>
                        <th>CATEGORY</th>
                        <th>QUANTITY</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($arr as $row) {
                    ?>
                        <tr>
                            <td><a href="<?=$row['item_image']?:NO_IMG?>" data-toggle="lightbox"  data-title="<?=$row['item_sku']?>"><div class="avatar avatar-40 img-rounded" style="background-image: url('<?=$row['item_image']?:NO_IMG?>')"></div></a></td>
                            <td data-hl-orderby="sku" data-hl-search><?=$row['item_sku'] ?></td>
                            <td><?=$row['item_name']?></td>
                            <td data-hl-orderby="length"><?=floatval($row['item_l'])?></td>
                            <td data-hl-orderby="width"><?=floatval($row['item_w'])?></td>
                            <td data-hl-orderby="height"><?=floatval($row['item_h'])?></td>
                            <td data-hl-orderby="height"><?=$row['item_style_title']?></td>
                            <td data-hl-orderby="height"><?=$row['item_category_title']?></td>
                            <td data-hl-orderby="style"><a href="/admin/inventory/index.php?s=inventory-item-list-warehouse&itemId=<?=$row['item_id']?>"><?=intval($row['inventory_count'])?></a> </td>
                        </tr>
                    <?php }  ?>
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