<?php
try {
    global $userModel;
    $itemModel = new \model\ItemModel();
    $itemId = (int) $_GET['itemId'];
    $categoryArr = $itemModel->getItemCategories([0]);
    $styleArr = $itemModel->getItemStyles([0]);
    if ($itemId) {
        //修改
        $userModel->isCurrentUserHasAuthority("ITEM","UPDATE") or Helper::throwException(null,403);
        $row =  $itemModel->getItemCategories([$itemId])[0] or Helper::throwException(null,404);
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
                                <select class="form-control select2" data-defvalue="<?=$row["item_category_id"]?>">
                                    <option>-- Select --</option>
                                    <?php
                                    foreach ($categoryArr as $category){
                                        echo "<option value='{$category['item_category_id']}' data-image='{$category['item_category_image']}'>{$category['item_category_title']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Style *</label>
                            <div class="col-sm-9">
                                <select class="form-control select2" data-defvalue="<?=$row["company_id"]?>">
                                    <option>-- Select --</option>
                                    <?php
                                    foreach ($styleArr as $style){
                                        echo "<option value='{$style['item_style_id']}'>{$style['item_style_title']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Width *</label>
                            <div class="col-sm-3">
                                <input type="number" name="item_w" value="<?php echo $row['item_w']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Height *</label>
                            <div class="col-sm-3">
                                <input type="number" name="item_h" value="<?php echo $row['item_h']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Depth *</label>
                            <div class="col-sm-3">
                                <input type="number" name="item_d" value="<?php echo $row['item_d']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Price *</label>
                            <div class="col-sm-3">
                                <input type="number" name="item_price" value="<?php echo $row['item_price']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Description *</label>
                            <div class="col-sm-9">
                                <input type="text" name="item_description" value="<?php echo $row['item_description']?>" class="form-control" placeholder="">
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
