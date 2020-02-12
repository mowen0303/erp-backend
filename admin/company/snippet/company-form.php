<?php
try {
    global $userModel;
    $companyModel = new \model\CompanyModel();
    $companyId = (int) $_GET['companyId'];
    if ($companyId) {
        //修改
        $userModel->isCurrentUserHasAuthority("COMPANY","UPDATE") or Helper::throwException(null,403) ;
        $row =  $companyModel->getCompanies([$companyId])[0] or Helper::throwException(null,404);
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
        <h4 class="page-title">COMPANY / <?=$flag?></h4>
    </div>
    <div class="col-md-8">
        <?php Helper::echoBackBtn();?>
    </div>
</div>
<!--header end-->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading"><?=$flag?> Company</div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form class="form-horizontal" action="/restAPI/companyController.php?action=modifyCompany" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="company_id" value="<?php echo $row['company_id']?>">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Company Name *</label>
                            <div class="col-sm-9">
                                <input type="text" name="company_name" value="<?php echo $row['company_name']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Country *</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="company_country" data-defvalue="<?php echo $row['company_country']?>">
                                    <?php Helper::echoCountryOption();?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Owner Name *</label>
                            <div class="col-sm-9">
                                <input type="text" name="company_owner_name" value="<?php echo $row['company_owner_name']?>" class="form-control" placeholder="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Business Number *</label>
                            <div class="col-sm-9">
                                <input type="text" name="company_business_number" value="<?php echo $row['company_business_number']?>" class="form-control" placeholder="">
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
