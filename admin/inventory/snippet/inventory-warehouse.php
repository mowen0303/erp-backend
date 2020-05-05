<?php
try {
    global $userModel;
    $userModel->isCurrentUserHasAuthority("WAREHOUSE","GET_LIST") or Helper::throwException(null,403);
    $inventoryModel = new \model\InventoryModel();
    $arr = $inventoryModel->getWarehouses([0],$_GET);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-xs-4">
        <h4 class="page-title">WAREHOUSE</h4>
    </div>
    <div class="col-xs-8">
        <?php Helper::echoBackBtn(1);?>
        <a href="/admin/inventory/index.php?s=inventory-warehouse-form" class="btn btn-danger pull-right"><i class="fas fa-plus-circle"></i>  Add Warehouse</a>
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
                        <th>ADDRESS</th>
                        <th>CITY</th>
                        <th>PROVINCE</th>
                        <th>COUNTRY</th>
                        <th>POST CODE</th>
                        <th>PHONE</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($arr as $row) {
                        ?>
                        <tr>
                            <td><a href="/admin/inventory/index.php?s=inventory-warehouse-item-map&warehouseId=<?=$row['warehouse_id']?>"><?=$row['warehouse_address'] ?></a></td>
                            <td><?=$row['warehouse_city'] ?></td>
                            <td><?=$row['warehouse_province'] ?></td>
                            <td><?=$row['warehouse_country'] ?></td>
                            <td><?=$row['warehouse_post_code'] ?></td>
                            <td><?=$row['warehouse_phone'] ?></td>
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