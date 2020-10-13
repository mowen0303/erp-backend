<?php
try {
    global $userModel;
    global $productModel;
    $itemModel = new \model\ItemModel();
    $productId = (int) $_GET['productId'];
    $fileModel = new \model\FileModel();
    $categoryArr = $productModel->getProductCategories([0],[],false);
    $styleArr = $itemModel->getItemStyles([0],[],false);
    if ($productId) {
        //修改
        $userModel->isCurrentUserHasAuthority("PRODUCT","UPDATE") or Helper::throwException(null,403);
        $images = $fileModel->getImage(['sectionName'=>'product','sectionId'=>$productId]) ?: [];
        $row =  $productModel->getProducts([$productId])[0] or Helper::throwException(null,404);
    }else{
        $userModel->isCurrentUserHasAuthority("PRODUCT","ADD") or Helper::throwException(null,403) ;
    }
    $flag = $row?'Edit':'Add';
    $itemArr = $itemModel->getItems([0],[],false);
    $productRelationArr = $productModel->getProductRelations($productId);
} catch (Exception $e) {
    Helper::echoJson(0, $e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">ITEM / <?=$flag?></h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn();?>
    </label>
</div>
<!--header end-->

<!--item-box-ini-data start-->
<div style="display: none">
    <?php
        foreach($productRelationArr as $productRelation){
            echo "<span class='item-box-ini-data' data-item-id='{$productRelation['product_relation_item_id']}' data-item-quantity='{$productRelation['product_relation_item_count']}'></span>";
        }
    ?>

</div>
<!--item-box-data start-->

<!--item-box-template start-->
<div id="item-box-template" class="row m-b-10 item-box" style="display: none">
    <div class="col-sm-4">
        <div class="input-group">
            <div class="input-group-addon"><span class="num-box">#<span class="num">0</span></span></div>
            <select name="item_id[]" class="form-control select3 has-error">
                <option value="">-- Select --</option>
                <?php
                foreach ($itemArr as $item){
                    echo "<option data-quantity='{$item['inventory_warehouse_count']}' value='{$item['item_id']}' data-image='{$item['item_image']}'>{$item['item_sku']}</option>";
                }
                ?>
            </select>
        </div>
    </div>
    <div class="col-sm-2">
        <input type="number" disabled max="" name="item_quantity[]" value="" class="quantity-input form-control" placeholder="0">
    </div>
</div>
<!--item-box-template end-->

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading"><?=$flag?> Product</div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form action="/restAPI/productController.php?action=modifyProduct" method="post" enctype="multipart/form-data">

                        <div class="form-body">

                            <input type="hidden" class="dropify_old" name="product_id" value="<?=$row['product_id']?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Product SKU# *</label>
                                        <input type="text" name="product_sku" value="<?=$row['product_sku']?>" class="form-control" placeholder="">
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Product Name</label>
                                        <input type="text" name="product_name" value="<?=$row['product_name']?>" class="form-control" placeholder="">
                                    </div>
                                </div>
                                <!--/span-->
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Category *</label>
                                        <select name="product_product_category_id" class="form-control erpSelect2" data-defvalue="<?=$row["product_product_category_id"]?>">
                                            <option value="">-- Select --</option>
                                            <?php
                                            foreach ($categoryArr as $category){
                                                echo "<option value='{$category['product_category_id']}'>{$category['product_category_title']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Style *</label>
                                        <select name="product_item_style_id" class="form-control erpSelect2" data-defvalue="<?=$row["product_item_style_id"]?>">
                                            <option value="">-- Select --</option>
                                            <?php
                                            foreach ($styleArr as $style){
                                                echo "<option value='{$style['item_style_id']}' data-image='{$style['item_style_image_cover']}'>{$style['item_style_title']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Product Assembied Dimension (Inch)</label>
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="ti-ruler-alt"></i></div>
                                                    <input type="number" step="0.0001" name="product_w" value="<?=floatval($row['product_w'])?>" class="form-control" placeholder="">
                                                </div>
                                                <span class="help-block"><small>Width</small></span>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="ti-ruler-alt"></i></div>
                                                    <input type="number" step="0.0001" name="product_h" value="<?=floatval($row['product_h'])?>" class="form-control" placeholder="">
                                                </div>
                                                <span class="help-block"><small>Height</small></span>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <div class="input-group-addon"><i class="ti-ruler-alt"></i></div>
                                                    <input type="number" step="0.0001" name="product_l" value="<?=floatval($row['product_l'])?>" class="form-control" placeholder="">
                                                </div>
                                                <span class="help-block"><small>Depth</small></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Expected inventory number</label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="ti-layout-grid3"></i></div>
                                            <input type="number" name="product_expected_count" value="<?php echo $row['product_expected_count']?>" class="form-control" placeholder="">
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>



                            <h3 class="box-title m-t-20">Items</h3>
                            <div id="inventory-box"></div>

                            <div class="row m-t-20">
                                <div class="col-md-6">
                                    <button id="add-item-btn" type="button" class="btn btn-outline btn-info waves-effect waves-light m-r-10"><i class="fa fa-plus-circle m-r-5"></i> Add 5 more</button>
                                </div>
                            </div>


                            <h3 class="box-title m-t-40">Product Description</h3>
                            <div class="row">
                                <div class="col-md-12 ">
                                    <div class="form-group">
                                        <textarea class="form-control" rows="3" name="product_des"><?=$row['product_des']?></textarea>
                                    </div>
                                </div>
                            </div>

                            <h3 class="box-title  m-t-30">Product Images</h3>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div style="max-width:150px" class="pull-left m-r-5 m-b-5">
                                        <input type="file" data-height="100" data-width="40" data-name="img_0" name="img_0[]" class="dropify" <?php echo $row["product_img_0"] ? "data-default-file='{$row['product_img_0']}'":"" ?> />
                                    </div>
                                    <div style="max-width:150px" class="pull-left m-r-5 m-b-5">
                                        <input type="file" data-height="100" data-width="40" data-name="img_1" name="img_1[]" class="dropify" <?php echo $row["product_img_1"] ? "data-default-file='{$row['product_img_1']}'":"" ?> />
                                    </div>
                                    <div style="max-width:150px" class="pull-left m-r-5 m-b-5">
                                        <input type="file" data-height="100" data-width="40" data-name="img_2" name="img_2[]" class="dropify" <?php echo $row["product_img_2"] ? "data-default-file='{$row['product_img_2']}'":"" ?> />
                                        <input type="hidden" class="dropify_old" name="product_img_2_old" value="<?php echo $row["product_img_2"]?>">
                                    </div>
                                    <div style="max-width:150px" class="pull-left m-r-5 m-b-5">
                                        <input type="file" data-height="100" data-width="40" data-name="img_3" name="img_3[]" class="dropify" <?php echo $row["product_img_3"] ? "data-default-file='{$row['product_img_3']}'":"" ?> />
                                        <input type="hidden" class="dropify_old" name="product_img_3_old" value="<?php echo $row["product_img_3"]?>">
                                    </div>
                                    <div style="max-width:150px" class="pull-left m-r-5 m-b-5">
                                        <input type="file" data-height="100" data-width="40" data-name="img_4" name="img_4[]" class="dropify" <?php echo $row["product_img_4"] ? "data-default-file='{$row['product_img_4']}'":"" ?> />
                                        <input type="hidden" class="dropify_old" name="product_img_4_old" value="<?php echo $row["product_img_4"]?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h3 class="box-title m-t-40">Features</h3>
                                    <div class="table-responsive">
                                        <table class="table table-bordered td-padding">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <input type="text" name="product_feature[]" value="<?=$row['product_feature_1']?>" class="form-control" placeholder="Feature 1">
                                                </td>
                                                <td>
                                                    <input type="text" name="product_feature[]" value="<?=$row['product_feature_2']?>" class="form-control" placeholder="Feature 2">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="text" name="product_feature[]" value="<?=$row['product_feature_3']?>" class="form-control" placeholder="Feature 3">
                                                </td>
                                                <td>
                                                    <input type="text" name="product_feature[]" value="<?=$row['product_feature_4']?>" class="form-control" placeholder="Feature 4">
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-actions m-t-40">
                            <button type="submit" class="btn btn-info waves-effect waves-light m-t-10"><i class="fa fa-check"></i> Submit</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
