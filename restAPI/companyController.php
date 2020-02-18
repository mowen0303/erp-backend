<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonServices/config.php";

function modifyCompany() {
    try {
        $userModel = new \model\UserModel();
        $companyModel = new \model\CompanyModel();
        $companyId = (int) Helper::post('company_id');
        if($companyId){
            //修改
            $userModel->isCurrentUserHasAuthority('COMPANY', 'UPDATE') or Helper::throwException(null, 403);
            $companyModel->modifyCompany($companyId);
        }else{
            //添加
            $userModel->isCurrentUserHasAuthority('COMPANY', 'ADD') or Helper::throwException(null, 403);
            $companyModel->modifyCompany();
        }
        Helper::echoJson(200, "Success! {$companyModel->imgError}", null, null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()} {$companyModel->imgError}");
    }
}

function deleteCompanyByIds() {
    try {
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority("COMPANY","DELETE") or Helper::throwException(null,403);
        $companyModel = new \model\CompanyModel();
        $effectRows = $companyModel->deleteCompanyByIds();
        Helper::echoJson(200, "{$effectRows} rows data has been deleted", null, null, null, $_SESSION['back_url_1']);
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage());
    }
}


/**
 * ============================================
 * ==============     Store     ===============
 * ============================================
 */

function getStoresByCompanyId() {
    try {
        $companyId = Helper::get('companyId','Company Id can not be null');
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority('COMPANY', 'GET_LIST') or Helper::throwException(null, 403);
        $companyModel = new \model\CompanyModel();
        $result = $companyModel->getStores([0],['companyId'=>$companyId]) or Helper::throwException("There is no store data in your selected company",404);
        Helper::echoJson(200, 'Success', $result);
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage());
    }
}

function modifyStore() {
    try {
        $userModel = new \model\UserModel();
        $companyModel = new \model\CompanyModel();
        $storeId = (int) Helper::post('store_id');
        if($storeId){
            //修改
            $userModel->isCurrentUserHasAuthority('COMPANY', 'UPDATE') or Helper::throwException(null, 403);
            $companyModel->modifyStore($storeId);
        }else{
            //添加
            $userModel->isCurrentUserHasAuthority('COMPANY', 'ADD') or Helper::throwException(null, 403);
            $companyModel->modifyStore();
        }
        Helper::echoJson(200, 'Success', null, null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage());
    }
}

function deleteStoreByIds() {
    try {
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority("COMPANY","DELETE") or Helper::throwException(null,403);
        $companyModel = new \model\CompanyModel();
        $effectRows = $companyModel->deleteStoreByIds();
        Helper::echoJson(200, "{$effectRows} rows data has been deleted", null, null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage());
    }
}



?>
