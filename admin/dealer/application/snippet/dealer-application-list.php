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
    <div class="col-sm-4">
        <h4 class="page-title">Dealer Application</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(1);?>
    </label>
</div>
<!--header end-->

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <form action="/restAPI/registerController.php?action=deleteApplicationByIds" method="post">
                <div class="table-responsive">
                    <table class="table color-table dark-table table-hover">
                        <thead>
                        <tr>
                            <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                            <th width="21px">#</th>
                            <th>CONTACT NAME</th>
                            <th>EMAIL</th>
                            <th>PHONE</th>
                            <th>COMPANY</th>
                            <th>COUNTRY</th>
                            <th>STATUS</th>
                            <th width="30px"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($arr as $row) {
                        ?>
                            <tr>
                                <td><input type="checkbox" class="cBox" name="id[]" value="<?=$row['register_id']?>"></td>
                                <td><?=$row['register_id']?></td>
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
                </div>
                <div class="row">
                    <div class="col-sm-8"><?=$registerModel->echoPageList()?></div>
                    <div class="col-sm-4 text-right">
                        <button id="deleteBtn" style="display: none" type="submit" class="btn btn-info waves-effect waves-light m-t-10" onclick="return confirm('Are you sure to delete?')">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>