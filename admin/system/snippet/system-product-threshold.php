<?php
try {
    global $userModel;
    $productModel = new \model\ProductModel();
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">PRODUCT INVENTORY THRESHOLD</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(1);?>
    </label>
</div>
<!--header end-->
<!--header end-->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading">UPDATE PRODUCT INVENTORY THRESHOLD</div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form class="form-horizontal" action="/restAPI/productController.php?action=updateInventoryThreshold" method="post">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Inventory threshold label *</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="label" value="<?=$productModel->SETTING_JSON->inventoryLabel->label?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Inventory threshold *</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="threshold" value="<?=$productModel->INVENTORY_LEVEL_1?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-info waves-effect waves-light m-t-10">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
