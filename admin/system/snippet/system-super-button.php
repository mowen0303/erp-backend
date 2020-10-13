<?php
try {
    global $userModel;
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">Super button</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(1);?>
    </label>
</div>
<!--header end-->
<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <div class="row">
                <div class="col-sm-4">
                    <a href="/restAPI/productController.php?action=updatePriceOfAllProducts" class="btn btn-block btn-info">Update price of all prodcuts</a>
                </div>
                <div class="col-sm-4">
                    <a href="/restAPI/productController.php?action=updateInventoryOfAllProducts" class="btn btn-block btn-info">Update inventory of all prodcuts</a>
                </div>
            </div>
        </div>
    </div>
</div>