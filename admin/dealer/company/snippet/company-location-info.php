<!--.row-->
<div class="row">
    <div class="col-md-12">
        <div class="panel">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-8"><i class="mdi mdi-store fa-fw"></i> <?=$companyLocationTitle?></div>
                    <div class="col-md-4 text-right">
                        <a href="/admin/dealer/company/index.php?s=company-location-form&companyLocationId=<?=$companyLocation['company_location_id']?>&companyId=<?=$companyLocation['company_location_company_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Edit"><i class="ti-marker-alt"></i></a>
                        <?php
                            global $userModel;
                            if($userModel->isCurrentUserHasAuthority("COMPANY","DELETE")){
                        ?>
                            <a  onclick="return confirm('Are you sure to delete?')" href="/restAPI/companyController.php?action=deleteCompanyLocationByIds&id=<?php echo $companyLocation['company_location_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Delete"><i class="ti-trash"></i></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <hr class="m-t-0 m-b-0">
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
                    <div class="form-horizontal">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Address:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$companyLocation['company_location_address']?>, <?=$companyLocation['company_location_city']?>, <?=$companyLocation['company_location_province']?>, <?=$companyLocation['company_location_post_code']?>, <?=$companyLocation['company_location_country']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Location Type:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static">
                                                <?=$companyLocation['company_location_is_head_office']==1?
                                                    '<span class="label label-rouded label-info">Head Office</span>'
                                                    :
                                                    '<span class="label label-rouded label-default">Brance</span>'
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Phone:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$companyLocation['company_location_phone']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Fax:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$companyLocation['company_location_fax']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Email:</label>
                                        <div class="col-md-7">
                                            <p class="form-constore-listtrol-static"><?=$companyLocation['company_location_email']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Website:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$companyLocation['company_location_website']?></p>
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
                                            <a class="btn btn-outline btn-rounded btn-default" href="/admin/user/index.php?s=user-list&companyLocationId=<?=$companyLocation['company_location_id']?>" target="_blank"><i class="ti-user"></i> View All Users In The Location</a>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--./row-->