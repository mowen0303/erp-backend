<?php
try {
    global $userModel;
    $orderModel = new \model\OrderModel();
    $productModel = new \model\ProductModel();
    $isAbleViewInventory = $isAbleViewInventory = $userModel->isCurrentUserHasAuthority("PRODUCT","VIEW_INVENTORY");
    $currentUserId = $userModel->getCurrentUserId();
    if($userModel->isCurrentUserHasAuthority("ORDER_MANAGEMENT_ADMIN","SUPER_ORDER_ADMIN_FOR_ALL_ORDERS")){
        $arr = $orderModel->getOrders([0],['withProducts'=>true, 'type'=>'order']);
    }else{
        $arr = $orderModel->getOrders([0],['withProducts'=>true, 'sellerIds'=>[$currentUserId], 'type'=>'order']);
    }


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
                    $orderDetailUrl = "/admin/order/index.php?s=order-management-list&s=order-list-detail&orderId=".$order['orders_id'];
            ?>
                    <? require $_SERVER['DOCUMENT_ROOT'].'/admin/order/component/order-product-table-header.component.php';?>
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
            <div class="col-sm-12"><?=$orderModel->echoPageList()?></div>
        </div>
    </div>
</div>