<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonServices/config.php";

function modifyRegister() {
    try {
        $registerModel = new \model\RegisterModel();
        $id = (int) Helper::post('register_id');
        if($id){
            //修改
            $userModel = new \model\UserModel();
            $userModel->isCurrentUserHasAuthority('DEALER_APPLICATION', 'REVIEW') or Helper::throwException(null, 403);
            $registerModel->modifyRegister($id);
        }else{
            //添加
            $registerModel->modifyRegister();
            Helper::mailTo('sales@de-valor.ca',"New register application - ".Helper::post('register_email'),"<p>Please login WLINK system to review the application</p>");
        }
        Helper::echoJson(200, "Success submit application! Please wait our dealer assistant to contact with you. You also can check your email to get the latest status of your application", null, null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "{$e->getMessage()} {$registerModel->imgError}");
    }
}

function passApplication() {
    try {
        $id = (int) Helper::post('id','Application Id can not be null');
        $username = Helper::post('username','User Name Id can not be null');
        $password = Helper::post('password','Password Id can not be null');
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority('DEALER_APPLICATION', 'REVIEW') or Helper::throwException(null, 403);
        $registerModel = new \model\RegisterModel();
        $registerModel->passApplication($id,$username,$password);
        Helper::echoJson(200, "Success", null, null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "{$e->getMessage()} {$registerModel->imgError}");
    }
}

function rejectApplication() {
    try {
        $id = (int) Helper::post('id','Application Id can not be null');
        $msg = Helper::post('msg');
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority('DEALER_APPLICATION', 'REVIEW') or Helper::throwException(null, 403);
        $registerModel = new \model\RegisterModel();
        $registerModel->rejectApplication($id,$msg);
        Helper::echoJson(200, "Success", null, null, null, Helper::echoBackBtn(0,true));
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), "{$e->getMessage()} {$registerModel->imgError}");
    }
}

?>
