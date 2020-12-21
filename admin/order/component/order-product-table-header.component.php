<div class="col-sm-12 table4-header">
    <div class="col-sm-3"><em>ORDER PLACE</em><br><?=$order['orders_date']?></div>
    <div class="col-sm-3"><em>DELIVER</em><br><?php $orderModel->echoDeliveryToolTipHTML($order)?></div>
    <div class="col-sm-2"><em>ORDER STATUS</em><br><?= $orderModel->echoOrderStatus($order['orders_status'])?></div>
    <div class="col-sm-4"><em>ORDER # </em><?=$order['orders_id']?><br><a href="<?=$orderDetailUrl?>">Order Details</a></div>
</div>