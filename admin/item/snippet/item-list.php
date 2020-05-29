<?php
try {
    global $userModel;
    $userModel->isCurrentUserHasAuthority("COMPANY","GET_LIST") or Helper::throwException(null,403);
    $itemModel = new \model\ItemModel();
    $itemCategoryArr = $itemModel->getItemCategories([0],[],false);
    $itemStyleArr = $itemModel->getItemStyles([0],[],false);
    $_GET['join'] = true;
    $arr = $itemModel->getItems([0],$_GET);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">ITEM</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(1);?>
        <a href="/admin/item/index.php?s=item-list-form" class="btn btn-danger pull-right"><i class="fas fa-plus-circle"></i>  Add Item</a>
    </label>
</div>
<!--header end-->

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h3 class="box-title">Search ITEM</h3>
            <form class="" action="/admin/item/index.php" method="get">
                <input type="hidden" name="s" value="item-list">
                <div class="row">
                    <div class="col-sm-10">
                        <input class="form-control" placeholder="Item Key Words" type="text" name="searchValue" value="<?=$_GET['searchValue']?>">
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-block btn-info waves-effect waves-light" type="submit">Search</button>
                    </div>
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
                    <h3 class="box-title">Item List</h3>
                </div>
            </div>
            <div class="row m-b-20">
                <form action="/admin/item/index.php" method="get">
                    <input type="hidden" name="s" value="item-list">
                    <div class="col-sm-3">
                        <select name="itemCategoryId" class="form-control" data-defvalue="<?=$_GET['itemCategoryId']?>">
                            <option value="">All</option>
                            <?php
                            foreach ($itemCategoryArr as $itemCategory){
                                echo "<option value=\"{$itemCategory['item_category_id']}\">{$itemCategory['item_category_title']}</option>";
                            }
                            ?>
                        </select>
                        <span class="help-block"><small>Filter by category</small></span>
                    </div>
                    <div class="col-sm-3">
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
                    <div class="col-sm-2">
                        <select name="orderBy" class="form-control" data-defvalue="<?=$_GET['orderBy']?>">
                            <option value="">Default</option>
                            <option value="price">Price</option>
                            <option value="sku">SKU</option>
                            <option value="length">Length</option>
                            <option value="width">Width</option>
                            <option value="height">Height</option>
                            <option value="style">Style</option>
                        </select>
                        <span class="help-block"><small>Order by</small></span>
                    </div>
                    <div class="col-sm-2">
                        <select name="sort" class="form-control" data-defvalue="<?=$_GET['sort']?>">
                            <option value="desc">▾ Descending</option>
                            <option value="asc">▴ Ascending</option>
                        </select>
                        <span class="help-block"><small>Sort</small></span>
                    </div>
                    <div class="col-sm-2 text-right">
                        <button type="submit" class="btn btn-block btn-info waves-effect waves-light">Filter</button>
                    </div>
                </form>
            </div>
            <form action="/restAPI/itemController.php?action=deleteItemByIds" method="post">
                <div class="table-responsive">
                    <table class="table color-table dark-table table-hover">
                        <thead>
                            <tr>
                                <th width="21"><input id="cBoxAll" type="checkbox"></th>
                                <th>#</th>
                                <th width="40">IMAGE</th>
                                <th>SKU#</th>
                                <th>NAME</th>
                                <th>L (M)</th>
                                <th>W (M)</th>
                                <th>H (M)</th>
                                <th>STYLE</th>
                                <th>PRICE</th>
                                <th>DESCRIPTION</th>
                                <th>CATEGORY</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($arr as $row) {
                            ?>
                            <tr>
                                <td><input type="checkbox" class="cBox" name="id[]" value="<?=$row['item_id']?>"></td>
                                <td><?=$row['item_id'] ?></td>
                                <td><a href="<?=$row['item_image']?:NO_IMG?>" data-toggle="lightbox"  data-title="<?=$row['item_sku']?>"><div class="avatar avatar-40 img-rounded" style="background-image: url('<?=$row['item_image']?:NO_IMG?>')"></div></a></td>
                                <td data-hl-orderby="sku" data-hl-search><?=$row['item_sku'] ?></td>
                                <td><?=$row['item_name']?></td>
                                <td data-hl-orderby="length"><?=floatval($row['item_l'])?></td>
                                <td data-hl-orderby="width"><?=floatval($row['item_w'])?></td>
                                <td data-hl-orderby="height"><?=floatval($row['item_h'])?></td>
                                <td data-hl-orderby="style"><?=$row['item_style_title']?></td>
                                <td data-hl-orderby="price">$<?=$row['item_price']?></td>
                                <td><?=$row['item_description']?></td>
                                <td><?=$row['item_category_title'] ?></td>
                                <td style="text-align: center">
                                    <a href="/admin/item/index.php?s=item-list-form&itemId=<?=$row['item_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Edit"><i class="ti-marker-alt"></i></a>
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
            </div>
            </form>
        </div>
    </div>
</div>