<?php
try {
    global $userModel;
    global $productModel;
    $orderModel = new \model\OrderModel();
    $orderId = Helper::get('orderId','Order id is required');
    $order = $orderModel->getOrders([$orderId],['withProducts'=>true])[0] or Helper::throwException(null,404);
    $orderModel->isAbleUpdateOrder($order) or Helper::throwException("The order status is not able to update",403);
    $isFullOrderTable = true;
} catch (Exception $e) {
    Helper::echoJson(0, $e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">Order / Order edit</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(3);?>
    </label>
</div>
<!--header end-->


<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading">Order # <?=$order['orders_id']?></div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <div class="table-responsive m-t-20">
                        <?php require $_SERVER['DOCUMENT_ROOT'] . "/admin/order/component/cart-product-table.component.php";?>
                    </div>

                    <div class="form-actions m-t-40">
                        <a href="/admin/order/index.php?s=order-management-list&s=order-list-detail&orderId=<?=$orderId?>" style="color: #fff" type="submit" class="btn btn-info pull-right m-l-10">Done</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="application/javascript" src="/admin/order/js/order.js"></script>´