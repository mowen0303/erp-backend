<?php
try {
    global $userModel;
    $userModel->isCurrentUserHasAuthority("WAREHOUSE","GET_LIST") or Helper::throwException(null,403);
    $warehouseModel = new \model\InventoryModel();
    $arr = $warehouseModel->getWarehouses([0],$_GET);
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
                        <th width="21"><input id="cBoxAll" type="checkbox"></th>
                        <th>#</th>
                        <th>ADDRESS</th>
                        <th>CITY</th>
                        <th>PROVINCE</th>
                        <th>COUNTRY</th>
                        <th>POST CODE</th>
                        <th>PHONE</th>
                        <th width="50"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($arr as $row) {
                        ?>
                        <tr>
                            <td><input type="checkbox" class="cBox" name="id[]" value="<?=$row['warehouse_id']?>"></td>
                            <td><?=$row['warehouse_id'] ?></td>
                            <td><?=$row['warehouse_address'] ?></td>
                            <td><?=$row['warehouse_city'] ?></td>
                            <td><?=$row['warehouse_province'] ?></td>
                            <td><?=$row['warehouse_country'] ?></td>
                            <td><?=$row['warehouse_post_code'] ?></td>
                            <td><?=$row['warehouse_phone'] ?></td>
                            <td style="text-align: center">
                                <a href="/admin/inventory/index.php?s=inventory-warehouse-form&warehouseId=<?=$row['warehouse_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Edit"><i class="ti-marker-alt"></i></a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-sm-8"><?=$warehouseModel->echoPageList()?></div>
                    <div class="col-sm-4 text-right">
                        <button id="deleteBtn" style="display: none" type="submit" class="btn btn-info waves-effect waves-light m-t-10" onclick="return confirm('Are you sure to delete?')">Delete</button>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>