<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonServices/config.php";

function modifyBillingAddress() {
    try {
        $billingAddressModel = new \model\BillingAddressModel();
        $id = Helper::post("billing_address_id");
        $result = $billingAddressModel->modifyBillingAddress($id);
        $fullAddress = $billingAddressModel->getFullAddress($result);
        Helper::echoJson(200, "Success!", $result, $fullAddress, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()}");
    }
}

function deleteBillingAddressByIds(){
    try {
        $billingAddressModel = new \model\BillingAddressModel();
        $ids = Helper::request('id','Billing address id is required');
        $effectRows = $billingAddressModel->deleteBillingAddressByIds($ids);
        Helper::echoJson(200, "{$effectRows} product has been deleted", null, null, null, $_SESSION['back_url_1']);
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "Failed : {$e->getMessage()}");
    }
}

?>
