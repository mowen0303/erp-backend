<?php
try {
    global $userModel;
    $userModel->isCurrentUserHasAuthority("INVENTORY","ITEM_IMPORT_REFERENCE") or Helper::throwException(null,403);
    $inventoryModel = new \model\InventoryModel();
    $arr = $inventoryModel->getImportReference();
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
        <?php Helper::echoBackBtn(1);?>
    </label>
</div>
<!--header end-->

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h3 class="box-title">IMPORT REFERENCE</h3>
            <div class="table-responsive">
                <table class="table color-table dark-table">
                    <thead>
                    <tr>
                        <th>ITEM</th>
                        <th>IMPORT QUANTITY</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($arr as $sku => $amount) {
                        ?>
                        <tr>
                            <td><?=$sku?></td>
                            <td><?=$amount?></td>
                        </tr>
                    <?php }  ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>