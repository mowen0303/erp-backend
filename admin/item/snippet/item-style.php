<?php
try {
    global $userModel;
    $userModel->isCurrentUserHasAuthority("ITEM","GET_LIST") or Helper::throwException(null,403);
    $itemModel = new \model\ItemModel();
    $arr = $itemModel->getItemStyles([0],$_GET);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">ITEM / STYLE</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(1);?>
        <a href="/admin/item/index.php?s=item-style-form" class="btn btn-danger pull-right"><i class="fas fa-plus-circle"></i>  Add Style</a>
    </label>
</div>
<!--header end-->

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <form action="/restAPI/itemController.php?action=deleteItemStyleByIds" method="post">
                <div class="table-responsive">
                    <table class="table color-table dark-table table-hover">
                        <thead>
                        <tr>
                            <th width="21"><input id="cBoxAll" type="checkbox"></th>
                            <th width="40">COVER</th>
                            <th>STYLE NAME</th>
                            <th>DESCRIPTION</th>
                            <th>DESCRIPTION IMAGE</th>
                            <th width="70"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($arr as $row) {
                            ?>
                            <tr>
                                <td><input type="checkbox" class="cBox" name="id[]" value="<?=$row['item_style_id']?>"></td>
                                <td><img class="avatar-30 img-circle" src="<?=$row['item_style_image_cover']?:NO_IMG?>" alt="user" width="40" class="img"></td>
                                <td><a href="/admin/item/index.php?s=item-list&itemStyleId=<?=$row['item_style_id']?>"><?=$row['item_style_title']?></a></td>
                                <td><?=$row['item_style_description'] ?></td>
                                <td>
                                    <?php if($row['item_style_image_description']){?>
                                        <a href="<?=$row['item_style_image_description']?>" data-toggle="lightbox" data-gallery="multiimages" data-title="<?=$row['item_style_title']?>"><div class="avatar avatar-40" style="background-image: url('<?=$row['item_style_image_description']?>')"></div></a>
                                    <?php }?></td>
                                <td>
                                    <a href="/admin/item/index.php?s=item-style-form&itemStyleId=<?=$row['item_style_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Edit"><i class="ti-marker-alt"></i></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-sm-8"><?=$itemModel->echoPageList()?></div>
                    <div class="col-sm-4 text-right">
                        <button id="deleteBtn" style="display: none" type="submit" class="btn btn-info waves-effect waves-light m-t-10" onclick="return confirm('Are you sure to delete?')">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>