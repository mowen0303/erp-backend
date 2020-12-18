<?php
try {
    global $userModel;
    global $inventoryModel;
    $orderModel = new \model\OrderModel();
    $warehouseArr = $inventoryModel->getWarehouses([0],['sort'=>'asc']);
    $userId = $userModel->getCurrentUserId();
    $orderId = Helper::get('orderId','Cart id is required');
    $order = $orderModel->getOrders([$orderId],[
        'userIds'=>[$userModel->getCurrentUserId()],
        'type'=>'cart',
        'withProducts'=>true
    ])[0] or Helper::throwException(null,404);
    $billingAddressModel = new \model\BillingAddressModel();
    $billingAddressArr = $billingAddressModel->getBillingAddress([0],['userId'=>$userId]);
    $currentUserId = $userModel->getCurrentUserId();
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>

<script>
$(document).ready(function(){
    const $shippingAddressBox = $("#shipping-address-box");
    const $warehouseBox = $("#warehouse-box");
    const $makePaymentBtn = $("#make-payment-btn")

    function checkPaymentBtnStatus(){
        let deliveryWay = $('input[name=shipping]:checked').val();
        let billingAddress = $('input[name=address]:checked').val();
        let warehouseAddress = $('input[name=warehouse]:checked').val();
        if(deliveryWay == "shipping"){
            if(billingAddress){
                $makePaymentBtn.attr('disabled',false);
            }else{
                $makePaymentBtn.attr('disabled',true);
            }
        }else if(deliveryWay == "pickup"){
            if(warehouseAddress){
                $makePaymentBtn.attr('disabled',false);
            }else{
                $makePaymentBtn.attr('disabled',true);
            }
        }
        return {deliveryWay,billingAddress,warehouseAddress}
    }


    $('input[name=shipping]').change(function(){
        if($(this).val() == "shipping"){
            $shippingAddressBox.slideDown();
            $warehouseBox.slideUp();
            checkPaymentBtnStatus();
        }else if($(this).val() == "pickup"){
            $shippingAddressBox.slideUp();
            $warehouseBox.slideDown();
            checkPaymentBtnStatus();
        }
    });

    $('input[name=address],input[name=warehouse]').change(function(){
        checkPaymentBtnStatus();
    })

    $makePaymentBtn.click(function(){
        let orderId = $('input[name=orders_id]').val();
        let {deliveryWay,billingAddress,warehouseAddress} = checkPaymentBtnStatus();
        let url = `/restAPI/orderController.php?action=placeOrder&dataType=json`;
        let params = new URLSearchParams();
        params.append('orders_id',orderId);
        params.append('orders_deliver_type',deliveryWay);
        params.append('orders_billing_address',billingAddress);
        params.append('orders_warehouse_address',warehouseAddress);
        axios.post(url,params)
        .then(res=>{
            if(res.data.code==200){
                const url = `/admin/order/index.php?s=order-list-detail&orderId=${orderId}`;
                window.location.href = url;
            }else{
                Swal.fire('Oops...', `${res.data.message}`, 'error');
            }
        })
        .catch(error=>{
            Swal.fire('Oops...', `${error}`, 'warning');
        })
    })


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
                            <input type="radio" name="address" id="radio${res.data.result.billing_address_id}" checked value="${res.data.secondResult}">
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
            .then(()=>{
                checkPaymentBtnStatus();
            })
        })
    })
})
</script>

<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">MY QUOTATION / CHECKOUT</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(2);?>
    </label>
</div>
<!--header end-->

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h3 class="box-title">Product Summary</h3>
            <input type="hidden" name="orders_id" value="<?=$orderId?>"/>
            <div class="table-responsive">
                <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/order/component/cart-product-table.component.php' ?>
            </div>

            <hr class="m-t-40 m-b-40">


            <h3 class="box-title m-t-40">Select delivery way</h3>
            <div class="p-b-40">
                <div class="radio radio-info">
                    <input type="radio" name="shipping" id="shipping" value="shipping">
                    <label for="shipping"> Shipping </label>
                </div>
                <div class="radio radio-info">
                    <input   type="radio" name="shipping" id="pickup" value="pickup">
                    <label for="pickup"> Pick it up at warehouse </label>
                </div>
            </div>

            <div class="form-group" id="shipping-address-box" style="display: none">
                <hr style="margin: 0; padding:20px">
                <h3 class="box-title" style="margin:0; padding:0">Select a shipping address</h3>
                <div id="address-radio">
                    <?php foreach ($billingAddressArr as $billingAddress) {?>
                        <div class="radio radio-info">
                            <input type="radio" name="address" id="radio<?=$billingAddress['billing_address_id']?>" value="<?=$billingAddressModel->getFullAddress($billingAddress)?>">
                            <label for="radio<?=$billingAddress['billing_address_id']?>"> <?=$billingAddressModel->getFullAddress($billingAddress)?> </label>
                        </div>
                    <?php } ?>
                </div>
                <div class="m-l-20">
                    <a href="#" id="addBillingAddressBtn">+ Add a new address</a>
                </div>
            </div>

            <div class="form-group" id="warehouse-box" style="display: none">
                <hr style="margin: 0; padding:20px">
                <h3 class="box-title" style="margin:0; padding:0">Select a warehouse</h3>
                <div>
                    <?php foreach ($warehouseArr as $warehouse) {?>
                        <div class="radio radio-info">
                            <input type="radio" name="warehouse" id="radio<?=$warehouse['warehouse_id']?>" value="<?=$inventoryModel->getWarehouseFullAddress($warehouse)?>">
                            <label for="radio<?=$warehouse['warehouse_id']?>"><?=$inventoryModel->getWarehouseFullAddress($warehouse)?></label>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <hr>
            <div class="text-right">
                <button id="make-payment-btn" class="btn btn-info" disabled></i>Place order and Make payment</button>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript" src="/admin/order/js/order.js"></script>