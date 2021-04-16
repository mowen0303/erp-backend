<?php
try {
    global $userModel;
    $userModel->isCurrentUserHasAuthority("DEALER_APPLICATION","REVIEW") or Helper::throwException(null,403);
    $registerModel = new \model\RegisterModel();
    $status = Helper::get('status');
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
            <div class="row m-b-20">
                <div class="col-sm-12">
                    <h3 class="box-title m-b-0">Dealer Application List</h3>
                </div>
            </div>
            <div class="row m-b-20">
                <form action="/admin/dealer/application/index.php" method="get">
                    <input type="hidden" name="s" value="dealer-application-list">
                    <div class="col-sm-12 p-l-0 p-r-0">
                        <div class="col-sm-10">
                            <select name="status" class="form-control" data-defvalue="<?=$_GET['status']?>">
                                <option value="">All</option>
                                <option value="waiting_in_review">Waiting & In review</option>
                                <option value="passed">Passed</option>
                                <option value="rejected">Rejected</option>
                            </select>
                            <span class="help-block"><small>Filter by application status</small></span>
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-block btn-info waves-effect waves-light">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
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
                            <th>APPLY TIME</th>
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
                                <td><?=$row['register_time'] ?></td>
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