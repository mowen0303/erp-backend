<table class="table table2 table-bordered">
    <thead>
    <tr>
        <th>SKU#</th>
        <th>Name & Attributes (Size inch)</th>
        <th>Components</th>
        <th>Quantity</th>
        <?if($isShowPrice){?>
        <th>Price/Unite</th>
        <th>Amount</th>
        <?}?>
    </tr>
    </thead>
    <tbody>
    <?php
    $amount = 0;
    foreach ($order['products'] as $product){
        $amount = $product['orders_product_snapshot_price']*$product['orders_product_count'];
    ?>
    <tr>
        <td class="sku"><a href="/admin/product/index.php?s=product-list&productCategoryId=0&s=product-detail&productId=<?=$product['orders_product_product_id']?>"><?=$product['orders_product_snapshot_sku']?></a></td>
        <td><?=$product['orders_product_snapshot_name']?><br><?=$product['orders_product_snapshot_attrs']?></td>
        <td><?=$product['orders_product_snapshot_component']?></td>
        <td><?=$product['orders_product_count'];?></td>
        <?if($isShowPrice){?>
        <td>$<?=Helper::priceOutput($product['product_price'])?></td>
        <td>$<?=Helper::priceOutput($amount)?></td>
        <?}?>
    </tr>
    <?php
    }
    ?>
    <?if($isShowPrice){?>
    <tr>
        <td align="right" colspan="5">Subtotal</td>
        <td>$<?=Helper::priceOutput($order['orders_price_original'])?></td>
    </tr>
    <?
    $deduction = $order['orders_price_final'] - $order['orders_price_original'];
    if($deduction!=0){
    ?>
    <tr>
        <td align="right" colspan="5">Deduction</td>
        <td>$<?=Helper::priceOutput($deduction)?></td>
    </tr>
    <tr>
        <td align="right" colspan="5">Subtotal after</td>
        <td class="font-medium">$<?=Helper::priceOutput($order['orders_price_final'])?></td>
    </tr>
    <?}?>
    </tr>
    <tr>
        <td align="right" colspan="5">HST (<?=HST_STR?>)</td>
        <td>$<?=Helper::priceOutput($order['orders_price_tax'])?></td>
    </tr>
    <tr>
        <td align="right" colspan="5">Total</td>
        <td class="font-medium">$<?=Helper::priceOutput($order['orders_price_final']+$order['orders_price_tax'])?></td>
    </tr>
    <?}?>
    </tbody>
</table>