<?php
try {
    global $userModel;
    global $productModel;
    $itemModel = new \model\ItemModel();
    $itemStyleArr = $itemModel->getItemStyles([0],[],false);
    $productId = (int) $_GET['productId'];
    $_GET['join'] = true;
    $row = $productModel->getProducts([$productId],$_GET)[0];
    $productRelationArr = $productModel->getProductRelations($productId,["join"=>true]);
    $isAbleViewInventory = $userModel->isCurrentUserHasAuthority("PRODUCT","VIEW_INVENTORY");
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">PRODUCT / <?=$row['product_name']?></h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(0);?>
        <?php if($userModel->isCurrentUserHasAuthorities([["PRODUCT","UPDATE"]])){?>
            <a href="<?=$_SERVER['REQUEST_URI']?>&s=product-list-form" class="btn btn-danger pull-right">Edit product</a>
        <?php } ?>
    </label>
</div>
<!--header end-->

<div class="row">
    <div class="col-lg-12">
        <div class="white-box">
            <div class="">
                <h2 class="m-b-0 m-t-0"><?=$row['product_name']?></h2> <small class="text-muted db">Product Name</small>
                <hr>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-6">

                        <div class="product-img-large-box">
                            <?php
                                for($i=0; $i<=8; $i++) {
                            ?>
                                <a class="large-img-box" href="<?=$row['product_img_'.$i]?:NO_IMG?>" data-toggle="lightbox" data-gallery="multiimages" data-title="<?=$row['item_sku']?>" target="_blank">
                                    <img id="product-img-large" src="<?=$row['product_img_'.$i]?:NO_IMG?>"/>
                                </a>
                            <?php
                            }
                            ?>
                        </div>
                        <div class="product-img-small-box m-t-10 m-b-20">
                            <?php
                            for($i=0; $i<=8; $i++) {
                                if($row['product_img_'.$i]) {
                                    echo "<div><img src='{$row['product_img_'.$i]}' class='product-img-small'/></div>";
                                }

                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-6">
                        <h4 class="box-title">SKU#</h4>
                        <p><?=$row['product_sku']?></p>
                        <h4 class="box-title m-t-30">Description</h4>
                        <p><?=$row['product_des']?></p>
                        <h2 class="m-t-40">MSRP <span class="">$<?=Helper::priceOutput($row['product_price'])?></span></h2>
                        <p>
                            <button data-product-id="<?=$row['product_id']?>"
                                    data-product-name="<?=$row['product_name']?>"
                                    data-product-sku="<?=$row['product_sku']?>"
                                    data-product-price="<?=$row['product_price']?>"
                                    class="btn btn-info m-r-5" type="button">
                                <i class="fas fa-cart-plus"></i> &nbsp; Add to your quotation
                            </button>
                        </p>
                        <h4 class="box-title m-t-30">Stock status</h4>
                        <p><?=$productModel->echoInventoryLabel((int)$row['product_inventory_count'],$isAbleViewInventory)?></p>
                        <h3 class="box-title m-t-30">Features</h3>
                        <ul class="list-icons">
                            <?php
                                for($i=1; $i<=5; $i++) {
                                    if($row['product_feature_'.$i]) {
                                        echo "<li><i class='fa fa-check text-success'></i> {$row['product_feature_'.$i]}</li>";
                                    }

                                }
                            ?>
                        </ul>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <h3 class="box-title m-t-40">General Info</h3>
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td width="390">Style</td>
                                    <td> <?=$row['item_style_title']?> </td>
                                </tr>
                                <tr>
                                    <td>Category</td>
                                    <td> <?=$row['product_category_title']?> </td>
                                </tr>
                                <tr>
                                    <td>Dimension <br/> WxHxD</td>
                                    <td> <?=floatval($row['product_w'])?> x <?=floatval($row['product_h'])?> x <?=floatval($row['product_l'])?> Inch</td>
                                </tr>
                                <tr>
                                    <td>Weight</td>
                                    <td> <?=floatval($row['product_weight'])?> KG</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <h3 class="box-title m-t-40">Components</h3>
                        <div class="table-responsive">
                            <table class="table color-table dark-table table-hover">
                                <thead>
                                    <tr>
                                        <th>COMPONENT SKU#</th>
                                        <th>STYLE</th>
                                        <th>CATEGORY</th>
                                        <th>DESCRIPTION</th>
                                        <th>QUANTITY</th>
                                        <?php
                                            if($isAbleViewInventory){
                                        ?>
                                        <th>PRICE/UNITE</th>
                                        <th>INVENTORY</th>
                                        <th>TOTAL</th>
                                        <?php
                                            }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($productRelationArr as $row){ ?>
                                    <tr>
                                        <td><?=$row['item_sku']?></td>
                                        <td><?=$row['item_style_title']?></td>
                                        <td><?=$row['item_category_title']?></td>
                                        <td><?=$row['item_description']?></td>
                                        <td><?=$row['product_relation_item_count']?></td>
                                        <?php
                                        if($isAbleViewInventory){
                                        ?>
                                        <td>$<?=Helper::priceOutput($row['item_price'])?></td>
                                        <td><a href="/admin/inventory/index.php?s=inventory-item-list-warehouse&itemId=<?=$row['item_id']?>"><?=$row['inventory_count']?></a></td>
                                        <td>$<?=$row['product_relation_item_count']*Helper::priceOutput($row['item_price'])?></td>
                                        <?php
                                        }
                                        ?>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/admin/product/js/product-add-cart.js"></script>