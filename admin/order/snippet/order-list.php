<?php
try {
    global $userModel;
    $ordersModel = new \model\OrderModel();
    $productModel = new \model\ProductModel();
    $isAbleViewInventory = $isAbleViewInventory = $userModel->isCurrentUserHasAuthority("PRODUCT","VIEW_INVENTORY");
    $currentUserId = $userModel->getCurrentUserId();
    $arr = $ordersModel->getOrders([0],[
        'withProducts'=>true,
        'userIds'=>[$currentUserId],
        'type'=>'order'
    ]);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">My orders</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(1);?>
    </label>
</div>
<!--header end-->

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <?php
            if($arr){
            ?>
                <h3 class="box-title">orders list</h3>
            <?php
                foreach($arr as $order){
            ?>
                    <div class="col-sm-12 table4-header">
                        <div class="col-sm-3">ORDER PLACED<br><?=$order['orders_date']?></div>
                        <div class="col-sm-3">ORDER # <br><?=$order['orders_id']?></div>
                        <div class="col-sm-3">DELIVERY<br><?php $ordersModel->echoDelivery($order)?></div>
                        <div class="col-sm-2">ORDER STATUS<br><?= $ordersModel->echoOrderStatus($order['orders_status'])?></div>
                    </div>
                    <div class="col-sm-12 p-l-0 p-r-0 m-b-40">
                        <div class="table-responsive">
                            <? require $_SERVER['DOCUMENT_ROOT'].'/admin/order/component/order-product-table.component.php';?>
                        </div>
                    </div>
            <?php
                }
            ?>

            <?php
            }else{
                Helper::echo404Page('NO ORDER FOUND!','YOU DID NOT PLACE A ORDER YET');
            }
            ?>
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="col-sm-12"><?=$ordersModel->echoPageList()?></div>
        </div>
    </div>
</div>