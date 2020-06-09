<?php
try {
    global $userModel;
    $companyModel = new \model\CompanyModel();
    $companyId = Helper::get('companyId','Company Id can not be null');
    $company = $companyModel->getCompanies([$companyId])[0];
    $companyTitle = "Company Info";
    $arr = $companyModel->getCompanyLocations([0],['companyId'=>$companyId]);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-sm-4">
        <h4 class="page-title">Company / Manage</h4>
    </div>
    <label class="col-sm-8 control-label">
        <?php Helper::echoBackBtn(2);?>
        <a href="/admin/dealer/company/index.php?s=company-location-form&companyId=<?=$company['company_id']?>" class="btn btn-danger pull-right"><i class="fas fa-plus-circle"></i>  Add Company Location</a>
    </label>
</div>
<!--header end-->

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/dealer/company/snippet/company-info.php" ?>
<form action="/restAPI/userController.php?action=deleteUserByIds" method="post">
    <?php
    foreach ($arr as $companyLocation) {
        $companyLocationTitle = "Location - ".$companyLocation['company_location_address'];
        require $_SERVER['DOCUMENT_ROOT'] . "/admin/dealer/company/snippet/company-location-info.php";
    }
    ?>
</form>