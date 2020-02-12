<?php
try {
    global $userModel;
    $companyModel = new \model\CompanyModel();
    $companyId = Helper::get('companyId','Company Id can not be null');
    $company = $companyModel->getCompanies([$companyId])[0];
    $companyTitle = "Company Info";
    $arr = $companyModel->getStores([0],['companyId'=>$companyId]);
} catch (Exception $e) {
    Helper::echoJson($e->getCode(),$e->getMessage());
    die();
}
?>
<!--header start-->
<div class="row bg-title">
    <div class="col-md-4">
        <h4 class="page-title">Company / Manage</h4>
    </div>
    <div class="col-md-8">
        <?php Helper::echoBackBtn(2);?>
        <a href="/admin/company/index.php?s=company-store-form&companyId=<?=$company['company_id']?>" class="btn btn-danger pull-right"><i class="fas fa-plus-circle"></i>  Add Store</a>
    </div>
</div>
<!--header end-->

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/company/snippet/company-info.php" ?>
<form action="/restAPI/userController.php?action=deleteUserByIds" method="post">
    <?php
    foreach ($arr as $store) {
        $storeTitle = "Store Info #".$store['store_id'];
        require $_SERVER['DOCUMENT_ROOT'] . "/admin/company/snippet/store-info.php";
    }
    ?>
</form>