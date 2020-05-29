<?php
try {
    global $userModel;
    $companyModel = new \model\CompanyModel();
    $companyLocationId = (int) $_GET['companyLocationId'];
    $companyId = (int) Helper::get('companyId','Company Id can not be null');
    if ($companyLocationId) {
        //修改
        $userModel->isCurrentUserHasAuthority("COMPANY","UPDATE") or Helper::throwException("You can not change the location info by yourself. <br> Please contact your <b>Sales Assistant</b> to change it.",403);
        $row =  $companyModel->getCompanyLocations([$companyLocationId])[0] or Helper::throwException(null,404);
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
    <div class="col-sm-4">
        <h4 class="page-title">COMPANY / LOCATION / <?=$flag?></h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn();?>
    </label>
</div>
<!--header end-->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading"><?php echo $userId?'Edit':'Add' ?> Location</div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form class="form-horizontal" action="/restAPI/companyController.php?action=modifyCompanyLocation" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="company_location_company_id" value="<?=$companyId?>">
                        <input type="hidden" name="company_location_id" value="<?=$companyLocationId?>">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Is Head Office *</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="company_location_is_head_office" data-defvalue="<?=$row['company_location_is_head_office']?>">
                                    <option value=""> -- Select --</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Address *</label>
                            <div class="col-sm-9">
                                <input type="text" name="company_location_address" value="<?=$row['company_location_address']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Country *</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="company_location_country" data-defvalue="<?=$row['company_location_country']?>">
                                    <?php Helper::echoCountryOption();?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Province *</label>
                            <div class="col-sm-9">
                                <input type="text" name="company_location_province" value="<?=$row['company_location_province']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">City *</label>
                            <div class="col-sm-9">
                                <input type="text" name="company_location_city" value="<?=$row['company_location_city']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Post Code *</label>
                            <div class="col-sm-9">
                                <input type="text" name="company_location_post_code" value="<?=$row['company_location_post_code']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Website</label>
                            <div class="col-sm-9">
                                <input type="text" name="company_location_website" value="<?=$row['company_location_website']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Phone</label>
                            <div class="col-sm-9">
                                <input type="text" name="company_location_phone" value="<?=$row['company_location_phone']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Fax</label>
                            <div class="col-sm-9">
                                <input type="text" name="company_location_fax" value="<?=$row['company_location_fax']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Email</label>
                            <div class="col-sm-9">
                                <input type="text" name="company_location_email" value="<?=$row['company_location_email']?>" class="form-control" placeholder="">
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
