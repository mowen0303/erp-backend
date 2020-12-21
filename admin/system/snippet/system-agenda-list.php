<?php
try {
    global $userModel;
    $userModel->isCurrentUserHasAuthority('AGENDA','GET_LIST') or Helper::throwException(null,403);
    $agendaModel = new \model\AgendaModel();
    $arr = $agendaModel->getAgendas([0],$_GET);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(), $e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">AGENDA</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(1);?>
    </label>
</div>
<!--header end-->

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h3 class="box-title m-b-0">AGENDA JOB LIST</h3>
            <p class="text-muted m-b-30 font-13"> All agenda job </p>

            <div class="row m-b-20">
                <form action="/admin/system/index.php" method="get">
                    <input type="hidden" name="s" value="system-agenda-list">
                    <div class="col-sm-12 p-l-0 p-r-0">
                        <div class="col-sm-10">
                            <select name="status" class="form-control" data-defvalue="<?=$_GET['status']?>">
                                <option value="">All</option>
                                <option value="non-execution">non-execution</option>
                                <option value="success">Success</option>
                                <option value="failure">Failure</option>
                                <option value="unknown">Unknown</option>
                            </select>
                            <span class="help-block"><small>Filter by agenda job status</small></span>
                        </div>
                        <div class="col-sm-2">
                            <button type="submit" class="btn btn-block btn-info waves-effect waves-light">Filter</button>
                        </div>
                    </div>
                </form>
            </div>

            <form action="/restAPI/agendaController.php?action=deleteAgendaByIds" method="post">
                <div class="table-responsive">
                    <table class="table table-hover color-table dark-table">
                        <thead>
                        <tr>
                            <th width="21px"><input id="cBoxAll" type="checkbox"></th>
                            <th width="21px">#</th>
                            <th>Controller</th>
                            <th>Action Name</th>
                            <th>Action Param</th>
                            <th>Run At</th>
                            <th>Last Run At</th>
                            <th>Last Run Status</th>
                            <th>Last Run Note</th>
                            <th>User</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($arr as $row) {
                        ?>
                            <tr>
                                <td><input type="checkbox" class="cBox" name="id[]" value="<?php echo $row['agenda_id']?>"></td>
                                <td><?=$row['agenda_id']?></td>
                                <td><?=$row['agenda_controller_name']?></td>
                                <td><?=$row['agenda_action_name']?></td>
                                <td><?=$row['agenda_action_param']?></td>
                                <td>
                                    <?=Helper::revertTime($row['agenda_run_at'])?><br>
                                    <small><?=Helper::getTimeDiffDesc(time(),$row['agenda_run_at'])?></small>
                                </td>
                                <td>
                                    <?=Helper::revertTime($row['agenda_last_run_at'])?><br>
                                    <small><?=Helper::getTimeDiffDesc(time(),$row['agenda_last_run_at'])?></small>
                                </td>
                                <td><?=$agendaModel->echoStatus($row['agenda_last_run_status'])?></td>
                                <td><?=$row['agenda_last_run_note']?></td>
                                <td><a href="/admin/user/index.php?s=user-list-profile&userId=<?=$row['user_id']?>"><?=$row['user_first_name'] ?> <?=$row['user_last_name'] ?></a></td>
                            </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-sm-8"><?=$agendaModel->echoPageList()?></div>
                    <div class="col-sm-4 text-right">
                        <button id="deleteBtn" style="display: none" type="submit" class="btn btn-info waves-effect waves-light m-t-10" onclick="return confirm('Are you sure to delete?')">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>