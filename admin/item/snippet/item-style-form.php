<?php
try {
    global $userModel;
    $itemModel = new \model\ItemModel();
    $itemStyleId = (int) $_GET['itemStyleId'];
    if ($itemStyleId) {
        //修改
        $userModel->isCurrentUserHasAuthority("ITEM","UPDATE") or Helper::throwException(null,403);
        $row =  $itemModel->getItemStyles([$itemStyleId])[0] or Helper::throwException(null,404);
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
        <h4 class="page-title">ITEM / STYLE / <?=$flag?></h4>
    </div>
    <div class="col-xs-8">
        <?php Helper::echoBackBtn();?>
    </div>
</div>
<!--header end-->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading"><?=$flag?> Item Style</div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form class="form-horizontal" action="/restAPI/itemController.php?action=modifyItemStyle" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="item_style_id" value="<?php echo $row['item_style_id']?>">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Style Name *</label>
                            <div class="col-sm-9">
                                <input type="text" name="item_style_title" value="<?php echo $row['item_style_title']?>" class="form-control" placeholder="">
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
