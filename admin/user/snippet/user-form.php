<?php
try {
    global $userModel;
    $userId = (int) $_GET['uid'];
    $currentUserId = $userModel->getCurrentUserId();
    if ($userId) {
        //修改
        $userModel->validateCurrentUserHasAuthorityToManageTargetUser($userId) or Helper::throwException(null,403);
        $row =  $userModel->getProfileOfUserById($userId, true);
        $isAdminManage = (int) $userModel->isCurrentUserHasAuthority("USER","UPDATE");
    }else{
        $isAdminManage = (int) $userModel->isCurrentUserHasAuthority("USER","ADD") or Helper::throwException(null,403);
    }
    $flag = $row?'Edit':'Add';
    $userCategoryArr = $userModel->getUserCategories(true);
    $companyModel = new \model\CompanyModel();
    $companyArr = $companyModel->getCompanies([0],['pageSize'=>100000]);
} catch (Exception $e) {
    Helper::echoJson(0, $e->getMessage());
    die();
}
?>
<script>
    $(document).ready(function(){
        let $storeRow = $("#storeRow");
        let $companySelect = $("#companySelect");
        let $storeSelect = $("#storeSelect");
        let iniCompanyId = parseInt($("#companySelect").val());

        if(iniCompanyId>0){
            $storeRow.slideDown();
            getStoreData(iniCompanyId,function(){setDefaultValue()});
        };

        $companySelect.change(function(){
            hideAlert();
            let selectedCompanyId = parseInt($(this).val());
            if(selectedCompanyId>0){
                $storeRow.slideDown();
                getStoreData(selectedCompanyId);
            }else{
                $storeRow.slideUp();
            }
        })

        function getStoreData(companyId,successCallback){
            let url = `/restAPI/companyController.php?action=getCompanyLocationsByCompanyId&companyId=${companyId}&dataType=json`;
            let options = {
                method: "GET",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                credentials: 'same-origin',
            };
            fetch(url, options)
                .then(response => response.json())
                .then(json => {
                    if (json.code === 200) {
                        $storeSelect.html("");
                        json.result.forEach(function(item){
                            $storeSelect.append($(`<option value='${item.company_location_id}'>${item.company_location_address} - ${item.company_location_country}</option>`));
                        })
                        successCallback && successCallback();
                    } else {
                        $storeSelect.html("");
                        showAlert(json.message,'error');
                    }
                })
                .catch(error => alert(error))
        }

    })
</script>
<!--header start-->
<div class="row bg-title">
    <div class="col-xs-4">
        <h4 class="page-title">User / <?=$flag?></h4>
    </div>
    <div class="col-xs-8">
        <?php Helper::echoBackBtn();?>
    </div>
</div>
<!--header end-->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-info">
            <div class="panel-heading"><?=$flag?> User</div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <form class="form-horizontal" action="/restAPI/userController.php?action=modifyUser" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="user_id" value="<?=$row['user_id']?>">
                        <input type="hidden" name="isAdminManage" value="<?=$isAdminManage?>">

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Email *</label>
                            <div class="col-sm-9">
                                <input type="text" <?php echo $userId?'disabled':''?> name="user_email" value="<?php echo $row['user_email']?>" class="form-control" placeholder="Email">
                            </div>
                        </div>

                        <?php if($isAdminManage){ ?>
                            <?php if(!$userId){ ?>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Password *</label>
                                    <div class="col-sm-9">
                                        <input type="password" name="user_pwd" value="" class="form-control" placeholder="Password">
                                        <span class="help-block"><small>At least 6 character</small></span>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if($userId != $currentUserId){ ?>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">User Group *</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="user_user_category_id" data-defvalue="<?php echo $row['user_user_category_id']?>">
                                            <?php
                                            foreach ($userCategoryArr as $userCategory) {
                                                echo "<option value='{$userCategory['user_category_id']}'>Level {$userCategory['user_category_level']} - {$userCategory['user_category_title']} </option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <?php if($userModel->isCurrentUserHasAuthority("USER","BIND_DEALER_TO_SELLER")){?>
                                    <hr class="m-t-30 m-b-30">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Bind Dealer to Seller</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="user_reference_user_id" placeholder="Seller ID" value="<?php echo $row['user_reference_user_id']?>">
                                            <span class="help-block"><small>If this is a dealer account, you can bind the dealer to a Seller. Please input a seller's user id.</small></span>
                                        </div>
                                    </div>
                                <?php } ?>

                            <?php } ?>

                            <hr class="m-t-30 m-b-30">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Company *</label>
                                <div class="col-sm-9">
                                    <select id="companySelect" class="form-control erpSelect2" data-defvalue="<?=$row["company_id"]?>">
                                        <option value="">-- Select --</option>
                                        <?php
                                        foreach ($companyArr as $company){
                                            echo "<option value='{$company['company_id']}'>{$company['company_name']} - {$company['company_country']}  (Business Number: {$company['company_business_number']})</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" id="storeRow" style="display: none">
                                <label class="col-sm-3 control-label">Store *</label>
                                <div class="col-sm-9">
                                    <select id="storeSelect" name="user_company_location_id" class="form-control" data-defvalue="<?=$row["user_company_location_id"]?>">
                                    </select>
                                </div>
                            </div>

                        <?php } ?>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Role *</label>
                            <div class="col-sm-9">
                                <select class="form-control" name="user_role" data-defvalue="<?php echo $row['user_role']?>">
                                    <option>-- Select --</option>
                                    <option value="President">President</option>
                                    <option value="Vice President">Vice President</option>
                                    <option value="Manager">Manager</option>
                                    <option value="Sales Rep.">Sales Rep.</option>
                                    <option value="Others">Others</option>
                                </select>
                            </div>
                        </div>
                        <hr class="m-t-30 m-b-30">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Full Name *</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="user_last_name" value="<?php echo $row['user_last_name']?>">
                                <span class="help-block"><small>Last Name</small></span>
                            </div>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="user_first_name" value="<?php echo $row['user_first_name']?>">
                                <span class="help-block"><small>First Name</small></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Avatar</label>
                            <div class="col-sm-10" style="width: 150px">
                                <input type="file" name="imgFile[]" class="dropify" data-height="106" data-default-file="<?php echo $row["user_avatar"] ?: $userModel->defaultAvatar?>"/>
                            </div>
                        </div>
                        <hr class="m-t-30 m-b-30">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Phone *</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="user_phone" value="<?php echo $row['user_phone']?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Fax</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="user_fax" value="<?php echo $row['user_fax']?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Mail Address</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" rows="3" name="user_address"><?php echo $row['user_address']?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Business Hours</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" rows="5" name="user_business_hour"><?php echo $row['user_business_hour']?></textarea>
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
