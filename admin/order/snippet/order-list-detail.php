<?php
try {
    global $userModel;
    global $inventoryModel;
    $orderModel = new \model\OrderModel();
    $warehouseArr = $inventoryModel->getWarehouses([0],['sort'=>'asc']);
    $userId = $userModel->getCurrentUserId();
    $cartId = Helper::get('orderId','Cart id is required');
    $billingAddressModel = new \model\BillingAddressModel();
    $billingAddressArr = $billingAddressModel->getBillingAddress([0],['userId'=>$userId]);
    $currentUserId = $userModel->getCurrentUserId();

//    $orderModel->generateOrder(16);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>

<script>
$(document).ready(function(){
    const $shippingAddressBox = $("#shipping-address-box");
    const $warehouseBox = $("#warehouse-box");
    $('.shipping').change(function(){
        if($(this).val() == "shipping"){
            $shippingAddressBox.slideDown();
            $warehouseBox.slideUp();
        }else if($(this).val() == "pickup"){
            $shippingAddressBox.slideUp();
            $warehouseBox.slideDown();
        }
    });

    $("#addBillingAddressBtn").click(function(){
        const html =$(`
        <div class="modal-box">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">ADD A NEW BILLING ADDRESS</h3>
            </div>
            <div class="modal-body">
                <?php require_once $_SERVER['DOCUMENT_ROOT']."/admin/setting/component/billing-address-form.php"?>
            </div>
            <div class="modal-footer">
                <div class="lds-dual-ring"></div>
                <button id="doneBtn" type="button" class="btn btn-danger"><div class="lds-dual-ring loadingIcon"></div>Add</button>
            </div>
        </div>
        `);
        Swal.fire({html:html, width:640, showConfirmButton: false});
        $(".close").click(function(){
            Swal.close();
        });
        $("#doneBtn").click(function(){
            let url = `/restAPI/billingAddressController.php?action=modifyBillingAddress&dataType=json`;
            let params = new URLSearchParams();
            params.append('billing_address_first_name',$("[name='billing_address_first_name']").val());
            params.append('billing_address_last_name',$("[name='billing_address_last_name']").val());
            params.append('billing_address_address',$("[name='billing_address_address']").val());
            params.append('billing_address_city',$("[name='billing_address_city']").val());
            params.append('billing_address_province',$("[name='billing_address_province']").val());
            params.append('billing_address_postal_code',$("[name='billing_address_postal_code']").val());
            params.append('billing_address_country',$("[name='billing_address_country']").val());
            params.append('billing_address_phone_number',$("[name='billing_address_phone_number']").val());
            params.append('billing_address_phone_number_ext',$("[name='billing_address_phone_number_ext']").val());
            axios.post(url,params)
            .then(res=>{
                if(res.data.code==200){
                    Swal.fire({type: 'success', title: res.data.message, showConfirmButton: false, timer: 1500});
                    $("#address-radio").prepend($(`
                        <div class="radio radio-info">
                            <input type="radio" name="address" id="radio${res.data.result.billing_address_id}" checked value="option5">
                            <label for="radio${res.data.result.billing_address_id}"> ${res.data.secondResult} </label>
                        </div>
                    `));
                }else{
                    showAlert(res.data.message,'error');
                    // Swal.fire({type: 'error', title: res.data.message, showConfirmButton: true})
                }
            })
            .catch(error=>{
                showAlert(error,'error');
            })
        })

    })
})
</script>

<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">Order confirmation</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(1);?>
    </label>
</div>
<!--header end-->

<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div class="panel-heading"></div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <div class="col-md-12">
                        <div class="wizard-box">
                            <div class="wizard-step active"><div class="c">1</div><div>Place order</div></div>
                            <div class="wizard-line"></div>
                            <div class="wizard-step"><div class="c">2</div><div>Make payment</div></div>
                            <div class="wizard-line"></div>
                            <div class="wizard-step"><div class="c">3</div><div>Delivery goods</div></div>
                            <div class="wizard-line"></div>
                            <div class="wizard-step"><div class="c">4</div><div>Done</div></div>
                        </div>
                    </div>
                    <form action="/restAPI/billingAddressController.php?action=modifyBillingAddress" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="billing_address_id" value="<?=$row['billing_address_id']?>">
                        <div class="col-md-12">

                            <div class="form-group">
                                <label>1. Select a goods delivery way</label>
                                <div>
                                    <div class="radio radio-info">
                                        <input class="shipping" type="radio" name="shipping" id="shipping" value="shipping">
                                        <label for="shipping"> Shipping </label>
                                    </div>
                                    <div class="radio radio-info">
                                        <input  class="shipping"  type="radio" name="shipping" id="pickup" value="pickup">
                                        <label for="pickup"> Pick it up at warehouse </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group" id="shipping-address-box" style="display: none">
                                <label>2. Select a shipping address</label>
                                <div id="address-radio">
                                    <?php foreach ($billingAddressArr as $billingAddress) {?>
                                        <div class="radio radio-info">
                                            <input type="radio" name="address" id="radio<?=$billingAddress['billing_address_id']?>" value="option5">
                                            <label for="radio<?=$billingAddress['billing_address_id']?>"> <?=$billingAddressModel->getFullAddress($billingAddress)?> </label>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="m-l-20">
                                    <a href="#" id="addBillingAddressBtn">+ Add a new address</a>
                                </div>
                            </div>

                            <div class="form-group" id="warehouse-box" style="display: none">
                                <label>2. Select a warehouse</label>
                                <div>
                                    <?php foreach ($warehouseArr as $warehouse) {?>
                                        <div class="radio radio-info">
                                            <input type="radio" name="warehouse" id="radio<?=$warehouse['warehouse_id']?>" value="option5">
                                            <label for="radio<?=$warehouse['warehouse_id']?>"><?=$inventoryModel->getWarehouseFullAddress($warehouse)?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">

                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="application/javascript" src="/admin/order/js/order.js"></script>