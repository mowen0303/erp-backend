<?php
try {
    global $userModel;
    global $productModel;
    $id = (int) $_GET['id'];
    if ($id) {
        //修改
        $userModel->isCurrentUserHasAuthorities([['PRODUCT_CATEGORY', 'UPDATE']]) or Helper::throwException(null, 403);
        $row =  $productModel->getProductCategories([$id])[0] or Helper::throwException(null,404);
    }else{
        $userModel->isCurrentUserHasAuthorities([['PRODUCT_CATEGORY', 'ADD']]) or Helper::throwException(null, 403);
    }
    $flag = $row?'Edit':'Add';
} catch (Exception $e) {
    Helper::echoJson(0, $e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">Product / Category</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn();?>
    </label>
</div>
<!--header end-->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading"><?=$flag?> product category</div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form class="form-horizontal" action="/restAPI/productController.php?action=modifyProductCategory" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="product_category_id" value="<?=$row['product_category_id']?>">

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Category name *</label>
                            <div class="col-sm-9">
                                <input type="text" name="product_category_title" value="<?=$row['product_category_title']?>" class="form-control" placeholder="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Description</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" rows="3" name="product_category_des"><?=$row['product_category_des']?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Category Image</label>
                            <div class="col-sm-9">
                                <div style="width: 150px">
                                    <input type="file" name="file[]" class="dropify" data-height="106" data-default-file="<?=$row["product_category_img"]?>"/>
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
