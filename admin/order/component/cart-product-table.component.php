<table class="table table2 table-striped table-bordered" data-order-id="<?=$order['orders_id']?>">
    <thead>
    <tr>
        <th>SKU#</th>
        <th>Product Name</th>
        <th>Style</th>
        <th>Category</th>
        <th>WxHxD(Inch)</th>
        <th>Price/Unite</th>
        <th>Quantity</th>
        <th width="200px">Amount</th>
        <? if($isFullOrderTable){?>
        <th class="dis-print">Stock</th>
        <th style="text-align:center"  class="dis-print">Action</th>
        <?}?>
    </tr>
    </thead>
    <tbody>
    <?php
    $amount = 0;
    foreach ($order['products'] as $product){
        $amount = $product['product_price']*$product['orders_product_count'];
    ?>
    <tr data-product-id="<?=$product['product_id']?>">
        <td width="180px"><a class="sku"  href="/admin/product/index.php?s=product-list&productCategoryId=0&s=product-detail&productId=<?=$product['orders_product_product_id']?>"><?=$product['product_sku']?></a></td>
        <td><?=$product['product_name']?></td>
        <td><?=$product['item_style_title']?></td>
        <td><?=$product['product_category_title']?></td>
        <td><?=$product['product_w']?>x<?=$product['product_h']?>x<?=$product['product_l']?></td>
        <td class="price" data-price="<?=$product['product_price']?>">$<?=Helper::priceOutput($product['product_price'])?></td>
        <td>
            <? if($isFullOrderTable){?>
            <input style="width: 80px" type="number" min="1" max="1000" class="quantity form-control" value="<?=$product['orders_product_count']?>">
            <?}else{
                echo $product['orders_product_count'];
            }?>
        </td>
        <td class="amount" data-amount="<?=$amount?>">$<?=Helper::priceOutput($amount)?></td>
        <? if($isFullOrderTable){?>
        <td class="stock dis-print" data-stock="<?=$product['product_inventory_count']?>"> <?=$productModel->echoInventoryLabel($product['product_inventory_count'],$isAbleViewInventory)?></td>
        <td align="center" class="dis-print"><a class="deleteProductBtn" data-order-id="<?=$order['orders_id']?>" data-product-id="<?=$product['product_id']?>" href="#" class="text-inverse" title="" data-toggle="tooltip" data-original-title="Remove the product"><i class="ti-trash text-dark"></i></a></td>
        <?}?>
    </tr>
    <?php
    }
    ?>
    <tr>
        <td align="right" colspan="7">Subtotal</td>
        <td class="subtotal"></td>
        <? if($isFullOrderTable){?>
        <td class="dis-print"></td>
        <td class="dis-print"></td>
        <?}?>
    </tr>
    </tr>
    <tr>
        <td align="right" colspan="7">HST (<?=HST_STR?>)</td>
        <td class="tax"></td>
        <? if($isFullOrderTable){?>
        <td class="dis-print"></td>
        <td class="dis-print"></td>
        <?}?>
    </tr>
    <tr>
        <td align="right" colspan="7">Total</td>
        <td class="total font-medium"></td>
        <? if($isFullOrderTable){?>
        <td class="dis-print"></td>
        <td class="dis-print"></td>
        <?}?>
    </tr>
    </tbody>
</table>