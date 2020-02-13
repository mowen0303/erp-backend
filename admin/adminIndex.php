<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/_template/header.php"?>
<?php
try {
    global $userModel;
    $companyModel = new \model\CompanyModel();


    $clientNumber = $userModel->getALLClientNumber();
    $companyNumber = $companyModel->getALLCompanyNumber();
} catch (Exception $e) {
    Helper::echoJson(0, $e->getMessage());
    die();
}
?>

    <!--header start-->
    <div class="row bg-title">
        <div class="col-md-4">
            <h4 class="page-title">Dashboard</h4>
        </div>
        <div class="col-md-8">
        </div>
    </div>
    <!--header end-->

    <div class="row">
        <div class="col-lg-4 col-sm-6 col-xs-12">
            <div class="white-box">
                <h3 class="box-title">NEW Order</h3>
                <ul class="list-inline two-part">
                    <li><i class="icon-folder text-purple"></i></li>
                    <li class="text-right"><span class="counter">0</span></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 col-xs-12">
            <div class="white-box">
                <h3 class="box-title">ALL CLIENTS</h3>
                <ul class="list-inline two-part">
                    <li><i class="icon-people text-info"></i></li>
                    <li class="text-right"><span class="counter"><?=$clientNumber?></span></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4 col-sm-6 col-xs-12">
            <div class="white-box">
                <h3 class="box-title">ALL COMPANY</h3>
                <ul class="list-inline two-part">
                    <li><i class="icon-folder-alt text-danger"></i></li>
                    <li class="text-right"><span class=""><?=$companyNumber?></span></li>
                </ul>
            </div>
        </div>
    </div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/_template/footer.php"?>