<?php
try {
    global $userModel;
    global $productModel;
    $userModel->isCurrentUserHasAuthorities([
            ["PRODUCT_CATEGORY","ADD"]
    ]) or Helper::throwException(null,403);
    $arr = $productModel->getProductCategories([0]);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">Product Category</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(1);?>
        <a href="/admin/product/index.php?s=product-category-list-form" class="btn btn-danger pull-right"><i class="fas fa-plus-circle"></i>  Add Category</a>
    </label>
</div>
<!--header end-->

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="box-title">Item List</h3>
                </div>
            </div>
            <form action="/restAPI/itemController.php?action=deleteItemByIds" method="post">
                <div class="table-responsive">
                    <table class="table color-table dark-table table-hover">
                        <thead>
                            <tr>
                                <th width="21"><input id="cBoxAll" type="checkbox"></th>
                                <th width="40">#</th>
                                <th width="40">IMAGE</th>
                                <th>CATEGORY NAME</th>
                                <th>DESCRIPTION</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($arr as $row) {
                            ?>
                            <tr>
                                <td><input type="checkbox" class="cBox" name="id[]" value="<?=$row['product_category_id']?>"></td>
                                <td><?=$row['product_category_id'] ?></td>
                                <td><a href="<?=$row['product_category_img']?:NO_IMG?>" data-toggle="lightbox"  data-title="<?=$row['product_category_title']?>"><div class="avatar avatar-40 img-rounded" style="background-image: url('<?=$row['product_category_img']?:NO_IMG?>')"></div></a></td>
                                <td><?=$row['product_category_title']?></td>
                                <td><?=$row['product_category_des']?></td>
                                <td style="text-align: center">
                                    <a href="/admin/product/index.php?s=product-category-list-form&id=<?=$row['product_category_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Edit"><i class="ti-marker-alt"></i></a>
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