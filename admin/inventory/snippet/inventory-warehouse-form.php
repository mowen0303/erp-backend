<?php
try {
    global $userModel;
    $warehouseModel = new \model\InventoryModel();
    $warehouseId = (int) $_GET['warehouseId'];
    if ($warehouseId) {
        //修改
        $userModel->isCurrentUserHasAuthority("WAREHOUSE","UPDATE") or Helper::throwException(null,403);
        $row =  $warehouseModel->getWarehouses([$warehouseId])[0] or Helper::throwException(null,404);
    }else{
        $userModel->isCurrentUserHasAuthority("WAREHOUSE","ADD") or Helper::throwException(null,403) ;
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
        <h4 class="page-title">WAREHOUSE / <?=$flag?></h4>
    </div>
    <div class="col-xs-8">
        <?php Helper::echoBackBtn();?>
    </div>
</div>
<!--header end-->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading"><?=$flag?> Warehouse</div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form class="form-horizontal" action="/restAPI/inventoryController.php?action=modifyWarehouse" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="warehouse_id" value="<?php echo $row['warehouse_id']?>">

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Address *</label>
                            <div class="col-sm-9">
                                <input type="text" name="warehouse_address" value="<?php echo $row['warehouse_address']?>" class="form-control" placeholder="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">City *</label>
                            <div class="col-sm-9">
                                <input type="text" name="warehouse_city" value="<?php echo $row['warehouse_city']?>" class="form-control" placeholder="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Province *</label>
                            <div class="col-sm-9">
                                <input type="text" name="warehouse_province" value="<?php echo $row['warehouse_province']?>" class="form-control" placeholder="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Country *</label>
                            <div class="col-sm-9">
                                <input type="text" name="warehouse_country" value="<?php echo $row['warehouse_country']?>" class="form-control" placeholder="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Post Code *</label>
                            <div class="col-sm-9">
                                <input type="text" name="warehouse_post_code" value="<?php echo $row['warehouse_post_code']?>" class="form-control" placeholder="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Phone *</label>
                            <div class="col-sm-9">
                                <input type="text" name="warehouse_phone" value="<?php echo $row['warehouse_phone']?>" class="form-control" placeholder="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Fax</label>
                            <div class="col-sm-9">
                                <input type="text" name="warehouse_fax" value="<?php echo $row['warehouse_fax']?>" class="form-control" placeholder="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Latitude *</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="warehouse_latitude" value="<?php echo $row['warehouse_latitude']?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Longitude *</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="warehouse_longitude" value="<?php echo $row['warehouse_longitude']?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Description</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" rows="5" name="warehouse_description"><?php echo $row['warehouse_description']?></textarea>
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
