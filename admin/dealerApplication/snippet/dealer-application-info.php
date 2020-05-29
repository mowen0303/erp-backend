<?php
try {
    global $userModel;
    $userModel->isCurrentUserHasAuthority("DEALER_APPLICATION","REVIEW") or Helper::throwException(null,403);
    $registerId = (int) Helper::get('id',"Application Id can not be null");
    $registerModel = new \model\RegisterModel();
    $row = $registerModel->getRegisters([$registerId])[0] or Helper::throwException(null,404);
    if($row['register_status']==1){
        $registerModel->startReview($registerId);
        $row = $registerModel->getRegisters([$registerId])[0] or Helper::throwException(null,404);
    }

} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">User / Profile</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(2);?>
    </label>
</div>
<!--header end-->

<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel1">Approve the application</h4>
            </div>
            <form action="/restAPI/registerController.php?action=passApplication" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?=$row['register_id']?>">
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">Note:</label>
                        <p>When you click <b>Pass Button</b>, the system will sent an email to <b><?=$row['register_email']?></b> with the account and password information. User can use the Account and Password you provided to login the ERP system. Please make sure the account has been created.</p>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">Account (user name):</label>
                        <input type="text" name="username" class="form-control" value="<?=$row['register_email']?>" id="recipient-name1">
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="control-label">Password:</label>
                        <input type="text" name="password" class="form-control" id="recipient-name1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Pass the application and sent email</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel1">Reject the application</h4> </div>
            <form action="/restAPI/registerController.php?action=rejectApplication" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?=$row['register_id']?>">
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">Note:</label>
                        <p>When you click <b>Reject Button</b>, the system will sent an email to <b><?=$row['register_email']?></b> to notify user the latest status.</p>
                    </div>
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">Leave a message to user (option):</label>
                        <textarea class="form-control" name="msg" id="message-text1"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Reject the application and sent email</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php if($row['register_status']==2) {?>
<div class="row">
    <div class="col-md-12">
        <div class="white-box">
            <h3 class="box-title m-b-0">Update Application Status</h3>
            <hr class="m-t-10 m-b-20">
            <div>
                <button type="button" class="btn btn-success m-r-20" data-toggle="modal" data-target="#modal" data-whatever="@mdo">Pass the application</button>
                <button type="button" class="btn btn-danger m-r-20" data-toggle="modal" data-target="#rejectModal" data-whatever="@mdo">Reject the application</button>
<!--                <a href="/admin/dealerApplication/index.php" class="btn btn-danger">Reject the application</a>-->
            </div>
        </div>
    </div>
</div>
<?php } ?>

<!--.row-->
<div class="row">
    <div class="col-md-12">
        <div class="panel">
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
                    <form class="form-horizontal" role="form">
                        <div class="form-body">

                            <h2>Application Status</h2>
                            <hr class="m-t-0 m-b-20">

                            <div class="form-group">
                                <label class="control-label col-sm-4">Current Status</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?php $registerModel->echoStatus($row['register_status'])?></p>
                                </div>
                            </div>

                            <h2 class="p-t-30">Company Info</h2>
                            <hr class="m-t-0 m-b-20">

                            <div class="form-group">
                                <label class="control-label col-sm-4">Company Name</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_company_name']?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Business Number</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_company_number']?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Owner Name</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_company_owner_first_name']?> <?=$row['register_company_owner_last_name']?></p>
                                </div>

                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">Address</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_company_address']?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">City</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_company_city']?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Province</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_company_province']?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Postcode</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_company_postcode']?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Country</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_company_country']?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Company Phone</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_company_phone']?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Company Fax</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_company_fax']?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Company Email</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_company_email']?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Company Website</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_company_website']?></p>
                                </div>
                            </div>

                            <h2 class="p-t-30">Other Company Detail</h2>
                            <hr class="m-t-0 m-b-20">


                            <div class="form-group">
                                <label class="control-label col-sm-4">Startup Year</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_company_start_year']?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Type of Company</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_company_type']?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Type of Business</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_company_role']?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Business License File</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $row['register_businessLicenseFile'] ? "<a href=\"{$row['register_businessLicenseFile']}\" target=\"_blank\"></a>" : "Not Upload" ?></p>
                                </div>
                            </div>

                            <h2 class="p-t-30">Contact Info</h2>
                            <hr class="m-t-0 m-b-20">

                            <div class="form-group">
                                <label class="control-label col-sm-4">Contact Name</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_first_name']?> <?=$row['register_last_name']?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Position</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_role']?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Phone</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_phone']?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Email</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_email']?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Business Card File</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $row['register_businessCardFile'] ? "<a href=\"{$row['register_businessCardFile']}\" target=\"_blank\"></a>" : "Not Upload" ?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Driver License File</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $row['register_driverLicenseFile'] ? "<a href=\"{$row['register_driverLicenseFile']}\" target=\"_blank\"></a>" : "Not Upload" ?></p>
                                </div>
                            </div>

                            <h2 class="p-t-30">How did you hear about us?</h2>
                            <hr class="m-t-0 m-b-20">

                            <div class="form-group">
                                <label class="control-label col-sm-4">Referred by Person</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_refer_name']?></p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-sm-4">Referred by Media</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?=$row['register_refer_media']?></p>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--./row-->
