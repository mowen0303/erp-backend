<?php
try {
    global $userModel;
    $billingAddressModel = new \model\BillingAddressModel();
    $arr = $billingAddressModel->getBillingAddress([0],['userId'=>$userModel->getCurrentUserId()]);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>

<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">Setting / billing address</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(1);?>
        <a href="/admin/setting/index.php?s=billing-address-list-form" class="btn btn-danger pull-right"><i class="fas fa-plus-circle"></i>  Add a billing address</a>
    </label>
</div>
<!--header end-->

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="box-title">My Billing address</h3>
                </div>
            </div>
            <form action="/restAPI/billingAddressController.php?action=deleteBillingAddressByIds" method="post">
                <div class="table-responsive">
                    <table class="table orderTable color-table dark-table table-hover">
                        <thead>
                        <tr>
                            <th width="21"><input id="cBoxAll" type="checkbox"></th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>City</th>
                            <th>Province</th>
                            <th>Postal code</th>
                            <th>Country</th>
                            <th>Phone</th>
                            <th>Ext.</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($arr as $row) {
                            ?>
                            <tr>
                                <td><input type="checkbox" class="cBox" name="id[]" value="<?=$row['billing_address_id']?>"></td>
                                <td><?=$row['billing_address_first_name'] ?> <?=$row['billing_address_last_name'] ?></td>
                                <td><?=$row['billing_address_address'] ?></td>
                                <td><?=$row['billing_address_city'] ?></td>
                                <td><?=$row['billing_address_province'] ?></td>
                                <td><?=$row['billing_address_postal_code'] ?></td>
                                <td><?=$row['billing_address_country'] ?></td>
                                <td><?=$row['billing_address_phone_number'] ?></td>
                                <td><?=$row['billing_address_phone_number_ext'] ?></td>
                                <td><a href="/admin/setting/index.php?s=billing-address-list-form&id=<?=$row['billing_address_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Edit"><i class="ti-marker-alt"></i></a></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-sm-8"><?=$billingAddressModel->echoPageList()?></div>
                    <div class="col-sm-4 text-right">
                        <button id="deleteBtn" style="display: none" type="submit" class="btn btn-info waves-effect waves-light m-t-10" onclick="return confirm('Are you sure to delete?')">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>