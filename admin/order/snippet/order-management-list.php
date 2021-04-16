<?php
try {
    global $userModel;
    $orderModel = new \model\OrderModel();
    $productModel = new \model\ProductModel();
    $isAbleViewInventory = $isAbleViewInventory = $userModel->isCurrentUserHasAuthority("PRODUCT","VIEW_INVENTORY");
    $currentUserId = $userModel->getCurrentUserId();
    $orderOption = [];
    $orderOption['withProducts'] = true;
    $orderOption['type'] = 'order';
    $orderOption['searchValue'] = $_GET['searchValue'];

    if ($userModel->isCurrentUserHasAuthorities([["ORDER_MANAGEMENT_ADMIN","EDIT_ORDER_FOR_OTHERS"]])){
        $orderOption['sellerIds'] = [$currentUserId];
    }

    if ($userModel->isCurrentUserHasAuthorities([["ORDER_MANAGEMENT_ADMIN","STATUS_CHANGE_FOR_CONFIRM_PAYMENT"]])){
        $orderOption['sellerIds'] = null;
    }

    if ($userModel->isCurrentUserHasAuthorities([["ORDER_MANAGEMENT_ADMIN","STATUS_CHANGE_FOR_READY_FOR_PICK_UP"]])){
        $orderOption['sellerIds'] = null;
        $orderOption['status'][] = 'order_confirmed';
        $orderOption['deliverType'][] = 'pickup';
    }

    if ($userModel->isCurrentUserHasAuthorities([["ORDER_MANAGEMENT_ADMIN","STATUS_CHANGE_FOR_PICKED_UP"]])){
        $orderOption['sellerIds'] = null;
        $orderOption['status'][] = 'ready_for_pick_up';
        $orderOption['deliverType'][] = 'pickup';
    }

    if ($userModel->isCurrentUserHasAuthorities([["ORDER_MANAGEMENT_ADMIN","STATUS_CHANGE_FOR_SHIPPED"]])){
        $orderOption['sellerIds'] = null;
        $orderOption['status'][] = 'order_confirmed';
        $orderOption['deliverType'][] = 'shipping';
    }

    if ($userModel->isCurrentUserHasAuthorities([["ORDER_MANAGEMENT_ADMIN","STATUS_CHANGE_FOR_DELIVERED"]])){
        $orderOption['sellerIds'] = null;
        $orderOption['status'][] = 'shipped';
        $orderOption['deliverType'][] = 'shipping';
    }

    if ($userModel->isCurrentUserHasAuthorities([["ORDER_MANAGEMENT_ADMIN","SUPER_ORDER_ADMIN_FOR_ALL_ORDERS"]])){
        $orderOption['sellerIds'] = null;
        $orderOption['status'] = null;
        $orderOption['deliverType'] = null;
    }

    $orderOption['status'] = Helper::get('status');

    $arr = $orderModel->getOrders([0],$orderOption);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">Order / management</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(1);?>
    </label>
</div>
<!--header end-->

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h3 class="box-title">Search order number</h3>
            <form class="" action="/admin/order/index.php" method="get">
                <input type="hidden" name="s" value="order-management-list">
                <div class="row">
                    <div class="<?=$_GET['searchValue']?'col-sm-8':'col-sm-10'?>">
                        <input class="form-control" placeholder="Order Id" type="text" name="searchValue" value="<?=$_GET['searchValue']?>">
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-block btn-info waves-effect waves-light" type="submit">Search</button>
                    </div>
                    <?if($_GET['searchValue']){?>
                        <div class="col-sm-2">
                            <a href="/admin/order/index.php?s=order-management-list" class="btn btn-block btn-danger waves-effect waves-light" type="submit">Clear</a>
                        </div>
                    <?}?>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">

            <div class="row">
                <div class="col-sm-12">
                    <h3 class="box-title">orders list under your management</h3>
                </div>
            </div>

            <div class="row m-b-20">
                <form action="/admin/order/index.php" method="get">
                    <input type="hidden" name="s" value="order-management-list">
                    <div class="col-sm-12 p-l-0 p-r-0">
                        <div class="col-sm-10">
                            <select name="status" class="form-control" data-defvalue="<?=$_GET['status']?>">
                                <option value="">All</option>
                                <option value="make_payment">Make payment</option>
                                <option value="order_confirmed">Order confirmed</option>
                                <option value="ready_for_pick_up">Ready for pick up</option>
                                <option value="picked_up">Order finished (picked up)</option>
                                <option value="shipped">Shipping</option>
                                <option value="delivered">Delivered</option>
                                <option value="canceled">Canceled</option>
                            </select>
                            <span class="help-block"><small>Filter by order status</small></span>
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-block btn-info waves-effect waves-light">Filter</button>
                        </div>
                    </div>
                </form>
            </div>

            <?php
            if($arr){
            ?>

            <div class="table-responsive">
                <table class="table orderTable color-table dark-table table-hover">
                    <thead>
                    <tr>
                        <th>ORDER #</th>
                        <th>ORDER FROM</th>
                        <th>DELIVER/PICK UP</th>
                        <th>ORDER PLACE</th>
                        <th>STATUS</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($arr as $row) {
                        $deliver = $orderModel->getDeliver($row);
                        $orderDetailUrl = "/admin/order/index.php?s=order-management-list&s=order-list-detail&orderId=".$row['orders_id'];
                        ?>
                        <tr>
                            <td><a data-hl-search href="<?=$orderDetailUrl?>"><?=$row['orders_id'] ?></a></td>
                            <td><?=$row['company_name'] ?></td>
                            <td><span class="text-muted"><?=$deliver['type']?> : </span> <br> </span><?=$deliver['address']?></td>
                            <td><?=$row['orders_date']?></td>
                            <td><?= $orderModel->echoOrderStatus($row['orders_status'])?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-sm-12"><?=$orderModel->echoPageList()?></div>
            </div>
            <?php
            }else{
                Helper::echo404Page('NO ORDER FOUND!','YOU DID NOT PLACE A ORDER YET');
            }
            ?>
        </div>
    </div>
</div>