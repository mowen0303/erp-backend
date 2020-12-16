<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonServices/config.php";

function getMyCarts() {
    try {
        $orderModel = new \model\OrderModel();
        $userModel = new \model\UserModel();
        $currentUserId = $userModel->getCurrentUserId();
        $result = $orderModel->getOrders([0],['type'=>'cart','userIds'=>[$currentUserId]]);
        Helper::echoJson(200, "Success!", $result, null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()}");
    }
}

function modifyCartTitle() {
    try {
        $orderModel = new \model\OrderModel();
        $userModel = new \model\UserModel();
        $orderId = Helper::post('orders_id','Cart Id is required');
        $result = $orderModel->modifyCartTitle($orderId);
        Helper::echoJson(200, "Success!", $result, null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()}");
    }
}

function placeOrder() {
    try {
        $orderModel = new \model\OrderModel();
        $orderId = Helper::post('orders_id','Order Id is required');
        $result = $orderModel->placeOrder($orderId);
        Helper::echoJson(200, "Success!", $result, null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()}");
    }
}


function addCart() {
    try {
        $orderModel = new \model\OrderModel();
        $result = $orderModel->addCart();
        Helper::echoJson(200, "Success!", $result, null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()}");
    }
}


function modifyOrderProduct() {
    try {
        $orderModel = new \model\OrderModel();
        $orderId = Helper::post('order_id','Cart Id is required');
        $productId = (int) Helper::post('product_id','Product Id is required');
        $count = (int) Helper::post('product_count','Quantity is required');
        $isAccumulate = Helper::post('is_accumulate')?: true;
        $result = $orderModel->modifyOrderProduct($orderId,$productId,$count,$isAccumulate);
        Helper::echoJson(200, "{$count} products has been add to your quotation!", $result, null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()}");
    }
}

function deleteOrderProductByIds() {
    try {
        $orderModel = new \model\OrderModel();
        $orderId = Helper::post('orders_id','Orders Id is required');
        $productId = (int) Helper::post('product_id','Product Id is required');
        $result = $orderModel->deleteOrderProductByIds($orderId,$productId);
        Helper::echoJson(200, "deleted",$result , null, null, $_SESSION['back_url_1']);
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()}");
    }
}

function deleteOrderByIds() {
    try {
        $orderModel = new \model\OrderModel();
        $orderId = Helper::request('orders_id','Cart Id is required');
        $result = $orderModel->deleteOrderByIds($orderId);
        Helper::echoJson(200, "Success!", "result", null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()}");
    }
}

?>
