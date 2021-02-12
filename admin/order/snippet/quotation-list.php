<?php
try {
    global $userModel;
    $orderModel = new \model\OrderModel();
    $productModel = new \model\ProductModel();
    $isAbleViewInventory = $isAbleViewInventory = $userModel->isCurrentUserHasAuthority("PRODUCT","VIEW_INVENTORY");
    $currentUserId = $userModel->getCurrentUserId();
    $isFullOrderTable = true;
    $arr = $orderModel->getOrders([0],[
        'userIds'=>[$userModel->getCurrentUserId()],
        'type'=>'cart',
        'withProducts'=>true
    ]);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">My quotation</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(1);?>
    </label>
</div>
<!--header end-->


<div class="row">
    <div class="col-sm-12">
        <?php
        if($arr){
            foreach ($arr as $order){
        ?>
        <div class="panel panel-info printableArea">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-8">
                        <span class="cartTitle"><?=$order['orders_name']?></span>
                        <span style="font-size:12px; font-weight: normal; padding-left:10px; color:rgba(255,255,255,0.8)"> <?=$order['orders_date']?></span>
                    </div>
                    <div class="col-md-4 text-right dis-print">
                        <a href="#" class="modifyCartBtn text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Edit" data-order-id="<?=$order['orders_id']?>"><i class="ti-marker-alt"></i></a>
                        <a href="#" class="deleteCartBtn text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Delete the quotation" data-order-id="<?=$order['orders_id']?>"><i class="ti-trash"></i></a>
                    </div>
                </div>
            </div>
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
                    <div class="table-responsive">
                    <?php require $_SERVER['DOCUMENT_ROOT'] . "/admin/order/component/cart-product-table.component.php";?>
                    </div>
                    <div class="p-t-20">
                        <button class="btn btn-default btn-outline pull-left print" type="button"> <span><i class="fa fa-print"></i> Print</span> </button>
                        <button class="btn btn-info pull-right dis-print checkout-btn"><i class="fa fa fa-shopping-cart"></i> Checkout</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
        }
        }else{
            Helper::echo404Page('NO QUOTATION FOUND!','YOU MAY WANT TO GO TO PRODUCT PAGE TO ADD STUFF TO YOUR QUOTATION','/admin/product/index.php?s=product-list&productCategoryId=0','Go to create my quotation');
        }
        ?>
        <div class="row">
            <div class="col-sm-12"><?=$orderModel->echoPageList()?></div>
        </div>
    </div>
</div>

<script type="application/javascript" src="/admin/order/js/order.js"></script>