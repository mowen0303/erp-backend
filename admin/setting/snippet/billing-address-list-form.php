<?php
try {
    global $userModel;
    $billingAddressModel = new \model\BillingAddressModel();
    $id = (int) $_GET['id'];
    if ($id) {
        //修改
        $row =  $billingAddressModel->getBillingAddress([0])[0] or Helper::throwException(null,404);
    }
    $flag = $row?'Edit':'Add';
} catch (Exception $e) {
    Helper::echoJson(0, $e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">Setting / billing address / <?=$flag?></h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn();?>
    </label>
</div>
<!--header end-->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading"><?=$flag?> billing address</div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form action="/restAPI/billingAddressController.php?action=modifyBillingAddress" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="billing_address_id" value="<?=$row['billing_address_id']?>">
                        <div class="col-md-6">
                            <?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/setting/component/billing-address-form.php"?>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-info waves-effect waves-light m-t-10">Submit</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
