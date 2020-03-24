<?php
try {
    global $userModel;
    $itemModel = new \model\ItemModel();
    $itemCategoryId = (int) $_GET['itemCategoryId'];
    if ($itemCategoryId) {
        //修改
        $userModel->isCurrentUserHasAuthority("ITEM","UPDATE") or Helper::throwException(null,403);
        $row =  $itemModel->getItemCategories([$itemCategoryId])[0] or Helper::throwException(null,404);
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
        <h4 class="page-title">ITEM / CATEGORY / <?=$flag?></h4>
    </div>
    <div class="col-xs-8">
        <?php Helper::echoBackBtn();?>
    </div>
</div>
<!--header end-->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading"><?=$flag?> Item Category</div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form class="form-horizontal" action="/restAPI/itemController.php?action=modifyItemCategory" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="item_category_id" value="<?php echo $row['item_category_id']?>">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Category Name *</label>
                            <div class="col-sm-9">
                                <input type="text" name="item_category_title" value="<?php echo $row['item_category_title']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Image</label>
                            <div class="col-sm-9">
                                <div style="width: 150px">
                                    <input type="file" name="file[]" class="dropify" data-height="106" data-default-file="<?php echo $row["item_category_image"]?>"/>
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