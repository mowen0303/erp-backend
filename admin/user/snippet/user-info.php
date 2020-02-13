<!--.row-->
<div class="row">
    <div class="col-md-12">
        <div class="panel">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-md-8"><?=$profileTitle?></div>
                    <div class="col-md-4 text-right">
                        <a href="/admin/user/index.php?s=user-form&uid=<?=$user['user_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Edit"><i class="ti-marker-alt"></i></a>
                        <a href="/admin/user/index.php?s=user-pwd-form&uid=<?=$user['user_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Change Password"><i class="ti-key"></i></a>
                    </div>
                </div>
            </div>
            <hr class="m-t-0 m-b-0">
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
                    <div class="form-horizontal" role="form">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Name:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$user['user_last_name']?> <?=$user['user_first_name']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Email:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$user['user_email']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Business Role:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$user['user_role']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">System Group:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><span class="label label-rouded label-info"><?=$user['user_category_title']?></span></p>
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
                                            <p class="form-control-static"><?=$user['user_phone']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Fax:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$user['user_fax']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Mail Address:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$user['user_address']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Business Hour:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$user['user_business_hour']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Register Time:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$user['user_register_time']?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-5">Last Login Time:</label>
                                        <div class="col-md-7">
                                            <p class="form-control-static"><?=$user['user_last_login_time']?></p>
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