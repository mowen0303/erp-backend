<?php
try {
    global $userModel;
    global $inventoryModel;
    $orderModel = new \model\OrderModel();
    $orderId = Helper::get('orderId','Order Id is required');
    $order = $orderModel->getOrders([$orderId],['withProducts'=>true,'type'=>'order'])[0] or Helper::throwException(null,404);
    $ownerId = $order['orders_user_id'];
    $hasRightToUpdateOrder = $userModel->isCurrentUserHasAnyOneOfAuthorities([
        ["ORDER_MANAGEMENT_ADMIN","ADD_ORDER_FOR_OTHERS"],
        ["ORDER_MANAGEMENT_ADMIN","UPDATE_ORDER_FOR_OTHERS"],
        ["ORDER_MANAGEMENT_ADMIN","CONFIRM_PAYMENT_FOR_OTHERS"],
        ["ORDER_MANAGEMENT_ADMIN","DELETE_ORDER_FOR_OTHERS"]
    ]);
    $ownerId == $userModel->getCurrentUserId() || $hasRightToUpdateOrder or Helper::throwException(null,403);
    $deliver = $orderModel->getDeliver($order);
    $seller = $order['orders_seller_id'] ? $userModel->getProfileOfUserById($order['orders_seller_id']) : null;
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>

<script>
    $(document).ready(function(){

        $("#editOrderPriceBtn").click(function(){
            let html =`
                <div class="modal-box">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title">Edit order price</h3>
                    </div>
                    <div class="modal-body form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Original price</label>
                            <div class="col-sm-6"><p class="form-control-static">$<?=Helper::priceOutput($order['orders_price_original'])?></p></div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Final price</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="input-group-addon"><i class="ti-money"></i></div>
                                    <input type="number" step="0.01" name="orders_price_final" value="<?=Helper::priceOutput($order['orders_price_final'])?>" class="form-control" placeholder="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="lds-dual-ring"></div>
                        <button id="done" type="button" class="btn btn-danger"><div class="lds-dual-ring loadingIcon"></div>Done</button>
                    </div>
                </div>
            `;

            Swal.fire({html:html, width:500, showConfirmButton: false,})

            $(".close").click(function(){Swal.close();})
            $("#done").click(function(){
                let params = new URLSearchParams();
                params.append('orders_id',$('#orderId').html())
                params.append('orders_price_final',$('input[name=orders_price_final]').val())
                let url = `/restAPI/orderController.php?action=updateOrderFinalPrice&dataType=json`;
                axios.post(url,params)
                    .then(res=>{
                        if(res.data.code==200){
                            showAlert(res.data.message);
                            Swal.close();
                            window.location.reload();
                        }else{
                            Swal.fire('Oops...', res.data.message, 'warning')
                        }
                    })
                    .catch(error=>Swal.fire('Oops...', error, 'error'))
                return false;
            })

            return false;
        })


        $("#transferOwnerBtn").click(function(){
            let html =`
                <div class="modal-box">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title">Transfer the order to a new user</h3>
                    </div>
                    <div class="modal-body form-horizontal">
                        <div class="form-group">
                            <div class="col-sm-12"><select name="orders_user_id" class="user-search-select-ajax form-control"></select></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="lds-dual-ring"></div>
                        <button id="done" type="button" class="btn btn-danger"><div class="lds-dual-ring loadingIcon"></div>Done</button>
                    </div>
                </div>
            `;

            Swal.fire({html:html, width:500, showConfirmButton: false,});

            registerUserSearchSelectAjax();

            $(".close").click(function(){Swal.close();})
            $("#done").click(function(){
                let params = new URLSearchParams();
                params.append('orders_id',$('#orderId').html())
                params.append('orders_user_id',$('select[name=orders_user_id]').val());
                let url = `/restAPI/orderController.php?action=updateOrderOwner&dataType=json`;
                axios.post(url,params)
                    .then(res=>{
                        if(res.data.code==200){
                            showAlert(res.data.message);
                            Swal.close();
                            window.location.reload();
                        }else{
                            Swal.fire('Oops...', res.data.message, 'warning')
                        }
                    })
                    .catch(error=>Swal.fire('Oops...', error, 'error'))
                return false;
            })

            return false;
        })

        $(".statusChangeBtn").click(function(){
            const status = $(this).attr('data-status');
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to update the order status",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                console.log(status)
                if (result.value == true) {
                    let url = `/restAPI/orderController.php?action=changeOrderStatus&dataType=json`;
                    let params = new URLSearchParams();
                    params.append('orders_id',$('#orderId').html());
                    params.append('orders_status',status);
                    axios.post(url,params)
                        .then(res=>{
                            if(res.data.code==200){
                                showAlert(res.data.message);
                                Swal.close();
                                window.location.reload();
                            }else{
                                Swal.fire('Oops...', res.data.message, 'warning')
                            }
                        })
                        .catch(error=>{
                            Swal.fire('Oops...', error, 'warning')
                        })
                }
            })
            return false;
        })
    })
</script>

<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">Order / Order Details</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(2);?>
    </label>
</div>
<!--header end-->


<div class="row">
    <div class="col-sm-9">

        <div class="white-box">
            <h3 class="box-title">Order status</h3>
            <?
            if($order['orders_status'] == $orderModel->orderStatus['canceled']){
                $orderModel->echoOrderStatus($order['orders_status']);
                echo "<p>&nbsp;</p>";
            }else{
            if($order['orders_deliver_type']=='shipping'){?>
                <div class="wizard-box">
                    <div class="wizard-step <?$orderModel->echoActiveHTML($orderModel->orderStatus['make_payment'],$order)?>"><div class="c">1</div><div class="t">Make<br>payment</div></div>
                    <div class="wizard-line"></div>
                    <div class="wizard-step <?$orderModel->echoActiveHTML($orderModel->orderStatus['order_confirmed'],$order)?>"><div class="c">2</div><div class="t">Order<br>Confirmed</div></div>
                    <div class="wizard-line"></div>
                    <div class="wizard-step <?$orderModel->echoActiveHTML($orderModel->orderStatus['shipped'],$order)?>"><div class="c">3</div><div class="t">Shipped</div></div>
                    <div class="wizard-line"></div>
                    <div class="wizard-step <?$orderModel->echoActiveHTML($orderModel->orderStatus['delivered'],$order)?>"><div class="c">4</div><div class="t">Delivered</div></div>
                </div>
            <?}else{?>
                <div class="wizard-box">
                    <div class="wizard-step <?$orderModel->echoActiveHTML($orderModel->orderStatus['make_payment'],$order)?>"><div class="c">1</div><div class="t">Make<br>payment</div></div>
                    <div class="wizard-line"></div>
                    <div class="wizard-step <?$orderModel->echoActiveHTML($orderModel->orderStatus['order_confirmed'],$order)?>"><div class="c">2</div><div class="t">Order<br>Confirmed</div></div>
                    <div class="wizard-line"></div>
                    <div class="wizard-step <?$orderModel->echoActiveHTML($orderModel->orderStatus['ready_for_pick_up'],$order)?>"><div class="c">3</div><div class="t">Ready for<br>pick up</div></div>
                    <div class="wizard-line"></div>
                    <div class="wizard-step <?$orderModel->echoActiveHTML($orderModel->orderStatus['picked_up'],$order)?>"><div class="c">4</div><div class="t">Picked up</div></div>
                </div>
            <?}}?>
            <?if($order['orders_status']==$orderModel->orderStatus['make_payment']){?>
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="box-title">next step instruction </h3>
                    <p>You have successfully placed the order! Please make payment for the order as soon as possible. Once our accountant confirmed your payment, the order process will move forward.</p>
                </div>
                <div class="col-sm-6">
                    <h3 class="box-title">payment information </h3>
                    <p><span class="text-muted">Pay to : </span>Woodworth Cabinetry</p>
                    <p><span class="text-muted">Payment method : </span>Bank draft, money order, EMT (sales@de-valor.ca), Cheque, Cash, Debit. </p>
                </div>
            </div>
            <?}?>
            <div class="row">
                <div class="col-sm-12">
                    <?
                        echo $order['orders_payment_date']?"<p>[{$order['orders_payment_date']}] Payment has been received and the order has been confirmed.</p>":"";
                        echo $order['orders_ready_for_pick_up_date']?"<p>[{$order['orders_ready_for_pick_up_date']}] Your product is ready for pick up.</p>":"";
                        echo $order['orders_picked_up_date']?"<p>[{$order['orders_picked_up_date']}] Products have been picked up.</p>":"";
                        echo $order['orders_shipped_date']?"<p>[{$order['orders_shipped_date']}] Products have been shipped out.</p>":"";
                        echo $order['orders_delivered_date']?"<p>[{$order['orders_delivered_date']}] Products have been delivered.</p>":"";
                        echo $order['orders_canceled_date']?"<p>[{$order['orders_canceled_date']}] The order has been canceled.</p>":"";
                    ?>
                </div>

            </div>
        </div>


        <div class="white-box">
            <h3 class="box-title">Order Details</h3>
            <div class="row">
                <div class="col-sm-4">
                    <p><span class="text-muted">Order # : </span> <span id="orderId"><?=$order['orders_id']?></span></p>
                    <p><span class="text-muted">Order title # : </span> <?=$order['orders_name']?></p>
                    <?if($hasRightToUpdateOrder){?>
                    <p><span class="text-muted">Order account : </span> <a href="/admin/user/index.php?s=user-list-profile&userId=<?=$order['orders_user_id']?>"><?=$order['user_first_name']?> <?=$order['user_last_name']?></a> </p>
                    <?}?>
                </div>
                <div class="col-sm-4">
                    <p><span class="text-muted">Order place : </span> <?=$order['orders_date']?></p>
                    <p><span class="text-muted">Payment confirmed : </span><?=$order['orders_payment_date']?></p>
                </div>
                <div class="col-sm-4">
                    <p><span class="text-muted"><?=$deliver['type']?> : </span><?=$deliver['address']?></p>
                </div>

            </div>
            <hr class="m-b-30">
            <div class="table-responsive">
                <? require $_SERVER['DOCUMENT_ROOT'].'/admin/order/component/order-product-table.component.php';?>
            </div>
        </div>
    </div>

    <div class="col-sm-3">


        <div class="white-box">
            <h3 class="box-title">Order management</h3>
            <hr>
            <div class="row">

                <?if($order['orders_status']==$orderModel->orderStatus['make_payment']){?>

                    <?if($hasRightToUpdateOrder = $userModel->isCurrentUserHasAnyOneOfAuthorities([["ORDER_MANAGEMENT_ADMIN","UPDATE_ORDER_FOR_OTHERS"]])){?>
                        <div class="col-sm-12"><a href="/admin/order/index.php?s=order-management-list-form&orderId=<?=$order['orders_id']?>" class="btn btn-info btn-block m-b-10">Edit products</a></div>
                        <div class="col-sm-12"><a href="#" id="editOrderPriceBtn" class="btn btn-info btn-block m-b-10"">Edit price</a></div>
                    <?}?>

                    <?if($hasRightToUpdateOrder = $userModel->isCurrentUserHasAnyOneOfAuthorities([["ORDER_MANAGEMENT_ADMIN","ADD_ORDER_FOR_OTHERS"],])){?>
                        <div class="col-sm-12"><a href="#" id="transferOwnerBtn" class="btn btn-info btn-block m-b-10"">Transfer owner</a></div>
                    <?}?>

                    <?if($userModel->isCurrentUserHasAuthority("ORDER_MANAGEMENT_ADMIN","CONFIRM_PAYMENT_FOR_OTHERS")){?>
                        <div class="col-sm-12"><a href="#" class="btn btn-info btn-block m-b-10 statusChangeBtn" data-status="order_confirmed">Confirm payment</a></div>
                    <?}?>

                <?}?>

                <?if($order['orders_status']==$orderModel->orderStatus['order_confirmed'] && $order['orders_deliver_type']=='shipping'){?>
                    <?if($hasRightToUpdateOrder = $userModel->isCurrentUserHasAnyOneOfAuthorities([["ORDER_MANAGEMENT_ADMIN","UPDATE_ORDER_FOR_OTHERS"]])){?>
                        <div class="col-sm-12"><a href="#" class="btn btn-info btn-block m-b-10 statusChangeBtn" data-status="shipped">Shipped</a></div>
                    <?}?>
                <?}?>

                <?if($order['orders_status']==$orderModel->orderStatus['shipped'] && $order['orders_deliver_type']=='shipping'){?>
                    <?if($hasRightToUpdateOrder = $userModel->isCurrentUserHasAnyOneOfAuthorities([["ORDER_MANAGEMENT_ADMIN","UPDATE_ORDER_FOR_OTHERS"]])){?>
                        <div class="col-sm-12"><a href="#" class="btn btn-info btn-block m-b-10 statusChangeBtn" data-status="delivered">Delivered</a></div>
                    <?}?>
                <?}?>

                <?if($order['orders_status']==$orderModel->orderStatus['order_confirmed'] && $order['orders_deliver_type']=='pickup'){?>
                    <?if($hasRightToUpdateOrder = $userModel->isCurrentUserHasAnyOneOfAuthorities([["ORDER_MANAGEMENT_ADMIN","UPDATE_ORDER_FOR_OTHERS"]])){?>
                        <div class="col-sm-12"><a href="#" class="btn btn-info btn-block m-b-10 statusChangeBtn" data-status="ready_for_pick_up">Ready for pick up</a></div>
                    <?}?>
                <?}?>

                <?if($order['orders_status']==$orderModel->orderStatus['ready_for_pick_up'] && $order['orders_deliver_type']=='pickup'){?>
                    <?if($hasRightToUpdateOrder = $userModel->isCurrentUserHasAnyOneOfAuthorities([["ORDER_MANAGEMENT_ADMIN","UPDATE_ORDER_FOR_OTHERS"]])){?>
                        <div class="col-sm-12"><a href="#" class="btn btn-info btn-block m-b-10 statusChangeBtn" data-status="picked_up">Picked up</a></div>
                    <?}?>
                <?}?>


                <?if($order['orders_status']==$orderModel->orderStatus['picked_up'] || $order['orders_status']==$orderModel->orderStatus['delivered'] ){?>
                    <div class="col-sm-12"><p>The order has completed.</p></div>
                <?}?>

                <?if($order['orders_status']==$orderModel->orderStatus['canceled']){?>
                    <div class="col-sm-12"><p>The order has been canceled.</p></div>
                <?}?>

                <?if($order['orders_status']==$orderModel->orderStatus['make_payment'] ||
                    ($userModel->isCurrentUserHasAuthority("ORDER_MANAGEMENT_ADMIN","CANCEL_ORDER_FOR_OTHERS")) &&
                    $order['orders_status']!=$orderModel->orderStatus['picked_up'] &&
                    $order['orders_status']!=$orderModel->orderStatus['delivered'] &&
                    $order['orders_status']!=$orderModel->orderStatus['canceled']
                ){?>
                    <div class="col-sm-12"><a href="#" class="btn btn-danger btn-block m-b-10 statusChangeBtn" data-status="canceled">Cancel the order</a></div>
                <?}?>

            </div>
        </div>

        <?if($seller){?>
            <div class="white-box">
                <h3 class="box-title">Order assistant</h3>
                <hr>
                <h4><?=$seller['user_first_name']?> <?=$seller['user_last_name']?></h4>
                <h5><i class="ti-mobile"></i> <?=$seller['user_phone']?></h5>
                <h5><i class="ti-email"></i> <?=$seller['user_email']?></h5>
                <small>Please contact with your order assistant if you have any questions.</small>
            </div>
        <?}?>

        <div class="white-box">
            <h3 class="box-title">For Any Support</h3>
            <hr>
            <h5><i class="ti-mobile"></i> (905) 670-8787</h5>
            <h5><i class="ti-email"></i> info@de-valor.ca</h5>
            <small>Please contact with us if you have any help.</small>
        </div>

    </div>



</div>
