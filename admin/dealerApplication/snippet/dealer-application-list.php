<?php
try {
    global $userModel;
    $userModel->isCurrentUserHasAuthority("DEALER_APPLICATION","REVIEW") or Helper::throwException(null,403);
    $registerModel = new \model\RegisterModel();
    $arr = $registerModel->getRegisters([0],$_GET);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-md-4">
        <h4 class="page-title">Dealer Application</h4>
    </div>
    <div class="col-md-8">
        <?php Helper::echoBackBtn(1);?>
    </div>
</div>
<!--header end-->

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <div class="row m-b-20">
                <div class="col-sm-12">
                    <h3 class="box-title m-b-0">Application List</h3>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table color-table dark-table table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>CONTACT NAME</th>
                        <th>EMAIL</th>
                        <th>PHONE</th>
                        <th>COMPANY</th>
                        <th>COUNTRY</th>
                        <th>STATUS</th>
                        <th>MANAGE</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($arr as $row) {
                    ?>
                        <tr>
                            <td><?php echo $row['register_id'] ?></td>
                            <td><?=$row['register_first_name']?> <?=$row['register_last_name']?></td>
                            <td><?=$row['register_email']?></td>
                            <td><?=$row['register_phone']?></td>
                            <td><?=$row['register_company_name'] ?></td>
                            <td><?=$row['register_company_number'] ?></td>
                            <td><?php $registerModel->echoStatus($row['register_status']) ?></td>
                            <td><a href="index.php?s=dealer-application-info&id=<?=$row['register_id']?>" class="text-inverse p-r-10" data-toggle="tooltip" title="" data-original-title="Review"><i class=" ti-blackboard"></i></a></td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-sm-8"><?=$registerModel->echoPageList()?></div>
                    <div class="col-sm-4 text-right">
                        <button id="deleteBtn" style="display: none" type="submit" class="btn btn-info waves-effect waves-light m-t-10" onclick="return confirm('Are you sure to delete?')">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>