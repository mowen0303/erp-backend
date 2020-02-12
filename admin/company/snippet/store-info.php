<!--.row-->
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-black">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-8"><?=$storeTitle?></div>
                    <div class="col-md-4 text-right">
                        <a href="/admin/company/index.php?s=company-store-form&storeId=<?=$store['store_id']?>&companyId=<?=$store['store_company_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Edit"><i class="ti-marker-alt"></i></a>
                        <a  onclick="return confirm('Are you sure to delete?')" href="/restAPI/companyController.php?action=deleteStoreByIds&id=<?php echo $store['store_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Delete"><i class="ti-trash"></i></a>
                    </div>
                </div>
            </div>
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
                    <div class="form-horizontal">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Address:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$store['store_address']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Store Type:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static">
                                                <?=$store['store_is_head_office']==1?
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
                                        <label class="control-label col-md-5">City:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$store['store_city']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Store Phone:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$store['store_phone']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Province:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$store['store_province']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Store Fax:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$store['store_fax']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Post Code:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$store['store_post_code']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Store Email:</label>
                                        <div class="col-md-7">
                                            <p class="form-constore-listtrol-static"><?=$store['store_email']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Country:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$store['store_country']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Website:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$store['store_website']?></p>
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
                                            <a class="btn btn-outline btn-rounded btn-default" href="#"><i class="ti-user"></i> View All Users In The Company</a>
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