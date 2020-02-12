<?php
try {
    global $userModel;
    $companyModel = new \model\CompanyModel();
    $storeId = (int) $_GET['storeId'];
    $companyId = (int) Helper::get('companyId','Company Id can not be null');
    if ($storeId) {
        //修改
        $userModel->isCurrentUserHasAuthority("COMPANY","UPDATE") or Helper::throwException(null,403) ;
        $row =  $companyModel->getStores([$storeId])[0] or Helper::throwException(null,404);
    }else{
        $userModel->isCurrentUserHasAuthority("COMPANY","ADD") or Helper::throwException(null,403) ;
    }
    $flag = $row?'Edit':'Add';
} catch (Exception $e) {
    Helper::echoJson(0, $e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-md-4">
        <h4 class="page-title">COMPANY / Store / <?=$flag?></h4>
    </div>
    <div class="col-md-8">
        <?php Helper::echoBackBtn();?>
    </div>
</div>
<!--header end-->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading"><?php echo $userId?'Edit':'Add' ?> Store</div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form class="form-horizontal" action="/restAPI/companyController.php?action=modifyStore" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="store_company_id" value="<?=$companyId?>">
                        <input type="hidden" name="store_id" value="<?=$storeId?>">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Is Head Office *</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="store_is_head_office" data-defvalue="<?=$row['store_is_head_office']?>">
                                    <option value=""> -- Select --</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Address *</label>
                            <div class="col-sm-9">
                                <input type="text" name="store_address" value="<?=$row['store_address']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Country *</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="store_country" data-defvalue="<?=$row['store_country']?>">
                                    <?php Helper::echoCountryOption();?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Province *</label>
                            <div class="col-sm-9">
                                <input type="text" name="store_province" value="<?=$row['store_province']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">City *</label>
                            <div class="col-sm-9">
                                <input type="text" name="store_city" value="<?=$row['store_city']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Post Code *</label>
                            <div class="col-sm-9">
                                <input type="text" name="store_post_code" value="<?=$row['store_post_code']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Store Website</label>
                            <div class="col-sm-9">
                                <input type="text" name="store_website" value="<?=$row['store_website']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Store Phone</label>
                            <div class="col-sm-9">
                                <input type="text" name="store_phone" value="<?=$row['store_phone']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Store Fax</label>
                            <div class="col-sm-9">
                                <input type="text" name="store_fax" value="<?=$row['store_fax']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Store Email</label>
                            <div class="col-sm-9">
                                <input type="text" name="store_email" value="<?=$row['store_email']?>" class="form-control" placeholder="">
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
