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
    <div class="col-xs-4">
        <h4 class="page-title">ITEM / STYLE</h4>
    </div>
    <div class="col-xs-8">
        <?php Helper::echoBackBtn(1);?>
        <a href="/admin/item/index.php?s=item-style-form" class="btn btn-danger pull-right"><i class="fas fa-plus-circle"></i>  Add Style</a>
    </div>
</div>
<!--header end-->

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">

            <div class="table-responsive">
                <form action="/restAPI/itemController.php?action=deleteItemStyleByIds" method="post">
                    <table class="table color-table dark-table table-hover">
                        <thead>
                        <tr>
                            <th width="21"><input id="cBoxAll" type="checkbox"></th>
                            <th>STYLE NAME</th>
                            <th width="70"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($arr as $row) {
                            ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="cBox" name="id[]" value="<?=$row['item_style_id']?>">
                                </td>
                                <td><?=$row['item_style_title'] ?></td>
                                <td>
                                    <a href="/admin/item/index.php?s=item-style-form&itemStyleId=<?=$row['item_style_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Edit"><i class="ti-marker-alt"></i></a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
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
</div>