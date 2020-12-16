<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonServices/config.php";


function executeAgenda() {
    try {
        $agendaModel = new \model\AgendaModel();
        $agendaModel->executeAgenda();
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage());
    }
}

function addAgenda() {
    try {
        $agendaModel = new \model\AgendaModel();
        $agendaModel->modifyAgenda(1,"companyController.php","test","a=1&b=4",5);
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage());
    }
}

function deleteAgendaByIds() {
    try {
        $userModel = new \model\UserModel();
        $userModel->isCurrentUserHasAuthority("AGENDA","DELETE") or Helper::throwException(null,403);
        $agendaModel = new \model\AgendaModel();
        $effectRows = $agendaModel->deleteAgendaByIds();
        Helper::echoJson(200, "{$effectRows} rows data has been deleted", null, null, null, $_SESSION['back_url_1']);
    } catch (Exception $e) {
        Helper::echoJson($e->getCode(), $e->getMessage());
    }
}

?>
