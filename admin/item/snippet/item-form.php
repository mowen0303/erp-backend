<?php
try {
    global $userModel;
    $itemModel = new \model\ItemModel();
    $itemId = (int) $_GET['itemId'];
    $categoryArr = $itemModel->getItemCategories([0],[],false);
    $styleArr = $itemModel->getItemStyles([0],[],false);
    if ($itemId) {
        //修改
        $userModel->isCurrentUserHasAuthority("ITEM","UPDATE") or Helper::throwException(null,403);
        $row =  $itemModel->getItems([$itemId])[0] or Helper::throwException(null,404);
    }else{
        $userModel->isCurrentUserHasAuthority("ITEM","ADD") or Helper::throwException(null,403) ;
    }
    $flag = $row?'Edit':'Add';
} catch (Exception $e) {
    Helper::echoJson(0, $e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-xs-4">
        <h4 class="page-title">ITEM / <?=$flag?></h4>
    </div>
    <div class="col-xs-8">
        <?php Helper::echoBackBtn();?>
    </div>
</div>
<!--header end-->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading"><?=$flag?> Item</div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form class="form-horizontal" action="/restAPI/itemController.php?action=modifyItem" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="item_id" value="<?php echo $row['item_id']?>">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">SKU# *</label>
                            <div class="col-sm-9">
                                <input type="text" name="item_sku" value="<?php echo $row['item_sku']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Category *</label>
                            <div class="col-sm-9">
                                <select name="item_item_category_id" class="form-control select2" data-defvalue="<?=$row["item_item_category_id"]?>">
                                    <option value="">-- Select --</option>
                                    <?php
                                    foreach ($categoryArr as $category){
                                        echo "<option value='{$category['item_category_id']}'>{$category['item_category_title']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Style *</label>
                            <div class="col-sm-9">
                                <select name="item_item_style_id" class="form-control select2" data-defvalue="<?=$row["item_item_style_id"]?>">
                                    <option value="">-- Select --</option>
                                    <?php
                                    foreach ($styleArr as $style){
                                        echo "<option value='{$style['item_style_id']}' data-image='{$style['item_style_image_cover']}'>{$style['item_style_title']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Package Dimension (Meter) *</label>
                            <div class="col-sm-2">
                                <input type="number" step="0.0001" name="item_l" value="<?=floatval($row['item_l'])?>" class="form-control" placeholder="">
                                <span class="help-block"><small>Length</small></span>
                            </div>
                            <div class="col-sm-2">
                                <input type="number" step="0.0001" name="item_w" value="<?=floatval($row['item_w'])?>" class="form-control" placeholder="">
                                <span class="help-block"><small>Width</small></span>
                            </div>
                            <div class="col-sm-2">
                                <input type="number" step="0.0001" name="item_h" value="<?=floatval($row['item_h'])?>" class="form-control" placeholder="">
                                <span class="help-block"><small>Height</small></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Price ($) *</label>
                            <div class="col-sm-3">
                                <input type="number" name="item_price" value="<?=$row['item_price']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Description</label>
                            <div class="col-sm-9">
                                <input type="text" name="item_description" value="<?php echo $row['item_description']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Item Image</label>
                            <div class="col-sm-9">
                                <div style="width: 150px">
                                    <input type="file" name="file[]" class="dropify" data-height="106" data-default-file="<?=$row["item_image"]?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-info waves-effect waves-light m-t-10">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
