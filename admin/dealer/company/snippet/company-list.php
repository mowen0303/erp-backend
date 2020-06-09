<?php
try {
    global $userModel;
    $userModel->isCurrentUserHasAuthority("COMPANY","GET_LIST") or Helper::throwException(null,403);
    $companyModel = new \model\CompanyModel();
    $arr = $companyModel->getCompanies([0],$_GET);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">Company</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(1);?>
        <a href="/admin/dealer/company/index.php?s=company-form" class="btn btn-danger pull-right"><i class="fas fa-plus-circle"></i>  Add Company</a>
    </label>
</div>
<!--header end-->

<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <h3 class="box-title">Search Company</h3>
            <form class="" action="/admin/dealer/company/index.php" method="get">
                <input type="hidden" name="s" value="company-list">
                <div class="row">
                    <div class="col-sm-10">
                        <input class="form-control" placeholder="Search Company Name" type="text" name="searchValue" value="<?=$_GET['searchValue']?>">
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-block btn-info waves-effect waves-light" type="submit">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <div class="white-box">
            <div class="row m-b-20">
                <div class="col-sm-12">
                    <h3 class="box-title m-b-0">COMPANY List</h3>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table color-table dark-table table-hover">
                    <thead>
                    <tr>
                        <th>COUNTRY</th>
                        <th>CITY (HEAD OFFICE)</th>
                        <th>COMPANY NAME</th>
                        <th>TYPE</th>
                        <th>OWNER NAME</th>
                        <th>BUSINESS NUMBER</th>
                        <th>STARTUP YEAR</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($arr as $row) {
                        ?>
                        <tr>
                            <td><?=$row['company_country'] ?></td>
                            <td><?=$row['company_location_city'] ?></td>
                            <td data-hl-search><a href="index.php?s=company-location-list&companyId=<?=$row['company_id']?>"><?=$row['company_name'] ?></a></td>
                            <td><?=$row['company_type'] ?></td>
                            <td><?=$row['company_owner_name']?></td>
                            <td><?=$row['company_business_number']?></td>
                            <td><?=$row['company_startup_year']?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-sm-8"><?=$companyModel->echoPageList()?></div>
                    <div class="col-sm-4 text-right">
                        <button id="deleteBtn" style="display: none" type="submit" class="btn btn-info waves-effect waves-light m-t-10" onclick="return confirm('Are you sure to delete?')">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>