<?php
try {
    global $userModel;
    global $productModel;
    $itemModel = new \model\ItemModel();
    $itemStyleArr = $itemModel->getItemStyles([0],[],false);
    $productCategoryId = (int) $_GET['productCategoryId'] ?: 0;
    $productCategoryArr = $productModel->getProductCategories([0]);
    $currentProductCategory = $productModel->getProductCategories([$productCategoryId])[0];
    $_GET['join'] = true;
    $_GET['withInventory'] = true;
    $arr = $productModel->getProducts([0],$_GET);
    $isAbleViewInventory = $userModel->isCurrentUserHasAuthority("PRODUCT","VIEW_INVENTORY");
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>

<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">PRODUCT</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(1);?>
        <?php if($userModel->isCurrentUserHasAuthorities([["PRODUCT","ADD"]])){?>
            <a href="<?=$_SERVER['REQUEST_URI']?>&s=product-list-form" class="btn btn-danger pull-right">Add product</a>
        <?php } ?>
        <?php if($userModel->isCurrentUserHasAuthorities([["PRODUCT_CATEGORY","ADD"],["PRODUCT_CATEGORY","UPDATE"]])){?>
            <a href="<?=$_SERVER['REQUEST_URI']?>&s=product-category-list" class="btn btn-danger pull-right m-r-5">Manage category</a>
        <?php } ?>
    </label>
</div>
<!--header end-->

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h3 class="box-title">Search Product</h3>
            <form class="" action="/admin/product/index.php" method="get">
                <input type="hidden" name="s" value="product-list">
                <input type="hidden" name="productCategoryId" value="0">
                <div class="row">
                    <div class="<?=$_GET['searchValue']?'col-sm-8':'col-sm-10'?>">
                        <input class="form-control" placeholder="Item Key Words" type="text" name="searchValue" value="<?=$_GET['searchValue']?>">
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-block btn-info waves-effect waves-light" type="submit">Search</button>
                    </div>
                    <?if($_GET['searchValue']){?>
                        <div class="col-sm-2">
                            <a href="/admin/product/index.php?s=product-list&productCategoryId=0" class="btn btn-block btn-danger waves-effect waves-light" type="submit">Clear</a>
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
                    <h3 class="box-title"><?=$currentProductCategory['product_category_title']?:'All product'?></h3>
                </div>
            </div>
            <div class="row m-b-20">
                <form action="/admin/product/index.php" method="get">
                    <input type="hidden" name="s" value="product-list">
                    <div class="col-sm-5">
                        <select name="productCategoryId" class="form-control" data-defvalue="<?=$_GET['productCategoryId']?>">
                            <option value="0">All</option>
                            <?php
                            foreach ($productCategoryArr as $category){
                                echo "<option value=\"{$category['product_category_id']}\">{$category['product_category_title']}</option>";
                            }
                            ?>
                        </select>
                        <span class="help-block"><small>Filter by category</small></span>
                    </div>
                    <div class="col-sm-5">
                        <select name="itemStyleId" class="form-control" data-defvalue="<?=$_GET['itemStyleId']?>">
                            <option value="">All</option>
                            <?php
                            foreach ($itemStyleArr as $itemStyle){
                                echo "<option value=\"{$itemStyle['item_style_id']}\">{$itemStyle['item_style_title']}</option>";
                            }
                            ?>
                        </select>
                        <span class="help-block"><small>Filter by style</small></span>
                    </div>
                    <div class="col-sm-2 text-right">
                        <button type="submit" class="btn btn-block btn-info waves-effect waves-light">Filter</button>
                    </div>
                </form>
            </div>
            <form action="/restAPI/productController.php?action=deleteProductByIds" method="post">
                <div class="table-responsive">
                    <table class="table orderTable color-table dark-table table-hover">
                        <thead>
                            <tr>
                                <th width="21"><input id="cBoxAll" type="checkbox"></th>
                                <th><a <?=$productModel->getProductListOrderUrl('sku')?>>SKU#</a></th>
                                <th><a <?=$productModel->getProductListOrderUrl('style')?>>STYLE</a></th>
                                <th><a <?=$productModel->getProductListOrderUrl('category')?>>CATEGORY</a></th>
                                <th><a <?=$productModel->getProductListOrderUrl('width')?>>W</a> x <a <?=$productModel->getProductListOrderUrl('height')?>>H</a> x <a <?=$productModel->getProductListOrderUrl('length')?>>D</a> (Inch)</th>
                                <th><a <?=$productModel->getProductListOrderUrl('weight')?>>WEIGHT (KG)</a></th>
                                <th><a <?=$productModel->getProductListOrderUrl('price')?>>PRICE</a></th>
                                <th><a <?=$productModel->getProductListOrderUrl('inventory')?>>STOCK STATUS</a></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($arr as $row) {
                            ?>
                            <tr>

                                <td><input type="checkbox" class="cBox" name="id[]" value="<?=$row['product_id']?>"></td>
                                <td>
                                    <a data-hl-search href="/admin/product/index.php?s=product-list&productCategoryId=<?=$productCategoryId?>&s=product-detail&productId=<?=$row['product_id']?>"><?=$row['product_sku']?></a>
                                    <br>
                                    <span data-hl-search><?=$row['product_name'] ?></span>
                                </td>
                                <td data-hl-search><?=$row['item_style_title'] ?></td>
                                <td><?=$row['product_category_title'] ?></td>
                                <td><?=floatval($row['product_w'])?> x <?=floatval($row['product_h'])?> x <?=floatval($row['product_l'])?></td>
                                <td><?=floatval($row['product_weight'])?></td>
                                <td>$<?=Helper::priceOutput($row['product_price'])?></td>
                                <td><?=$productModel->echoInventoryLabel($row['product_inventory_count'],$isAbleViewInventory)?></td>
                                <td>
                                    <button
                                            data-product-id="<?=$row['product_id']?>"
                                            data-product-name="<?=$row['product_name']?>"
                                            data-product-sku="<?=$row['product_sku']?>"
                                            data-product-price="<?=$row['product_price']?>"
                                            class="btn btn-info m-r-5" type="button">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-sm-8"><?=$productModel->echoPageList()?></div>
                    <div class="col-sm-4 text-right">
                        <button id="deleteBtn" style="display: none" type="submit" class="btn btn-info waves-effect waves-light m-t-10" onclick="return confirm('Are you sure to delete?')">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/admin/product/js/product-add-cart.js"></script>