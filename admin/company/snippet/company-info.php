<!--.row-->
<div class="row">
    <div class="col-md-12">
        <div class="panel">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-8"><i class="mdi mdi-city fa-fw"></i> <?=$companyTitle?></div>
                    <div class="col-md-4 text-right">
                        <a href="/admin/company/index.php?s=company-form&companyId=<?=$company['company_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Edit"><i class="ti-marker-alt"></i></a>
                        <?php
                            global $userModel;
                            if($userModel->isCurrentUserHasAuthority("COMPANY","DELETE")){
                        ?>
                            <a  onclick="return confirm('Are you sure to delete?')" href="/restAPI/companyController.php?action=deleteCompanyByIds&id=<?php echo $company['company_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Delete"><i class="ti-trash"></i></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <hr class="m-t-0 m-b-0">
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
                    <form class="form-horizontal" role="form">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Company Name:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$company['company_name']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Country:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$company['company_country']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Owner Name:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$company['company_owner_name']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Business Number:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$company['company_business_number']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5"></label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Members:</label>
                                        <div class="col-md-7">
                                            <a class="btn btn-outline btn-rounded btn-default" href="/admin/user/index.php?s=user-list&companyId=<?=$company['company_id']?>" target="_blank"><i class="ti-user"></i> View All Users In The Company</a>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--./row-->
