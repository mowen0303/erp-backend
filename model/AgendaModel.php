<?php
namespace model;
use \Model as Model;
use \Helper as Helper;

class AgendaModel extends Model
{
    public function modifyAgenda(int $userId,$controllerName,$actionName,$actionParam,$inSeconds=0){
        $arr = [];
        $arr['agenda_run_at'] = time()+$inSeconds;
        $sql = "SELECT * FROM agenda WHERE agenda_user_id IN ($userId) AND agenda_action_name = '{$actionName}' AND agenda_controller_name = '{$controllerName}' AND agenda_action_param = '{$actionParam}' AND agenda_last_run_at = 0";
        $result = $this->sqltool->getRowBySql($sql);
        if($result){
            //修改
            $id = $result['agenda_id'];
            $this->updateRowById('agenda',$id,$arr,false);
        }else{
            //添加
            $arr['agenda_user_id'] = $userId;
            $arr['agenda_controller_name'] = $controllerName;
            $arr['agenda_action_name'] = $actionName;
            $arr['agenda_action_param'] = $actionParam;
            $id = $this->addRow('agenda',$arr);
        }
        return $id;
    }

    public function executeAgenda(){
        $currentTime = time();
        $sql = "SELECT * FROM agenda WHERE agenda_last_run_at = 0 AND agenda_run_at <= {$currentTime}";
        $result = $this->sqltool->getListBySql($sql);
        foreach($result as $row){
            $arr['agenda_last_run_at'] = time();
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
            $domain = $_SERVER['HTTP_HOST'];
            $controller = "/restAPI/".$row['agenda_controller_name'];
            $action = "?action=".$row['agenda_action_name'];
            $params = "&".$row['agenda_action_param'];
            $url = $protocol.$domain.$controller.$action.$params;
            $result = file_get_contents($url);
            if($result){
                $result = json_decode($result);
                if($result->code == 200){
                    $arr['agenda_last_run_status'] = 'Success';
                }else{
                    $arr['agenda_last_run_status'] = 'Failure';
                }
                $arr['agenda_last_run_note'] = $result->message;
            }else{
                $arr['agenda_last_run_status'] = 'Unknown';
                $arr['agenda_last_run_note'] = 'no result';
            }
            $this->updateRowById('agenda',$row['agenda_id'],$arr);
        }
    }

    public function getAgendas(array $idArr, array $option=[]){
        $bindParams = [];
        $selectFields = "";
        $whereCondition = "";
        $orderCondition = "";

        $orderBy    = $option['orderBy'];
        $sequence   = $option['sequence']?:'DESC';
        $pageSize   = $option['pageSize']?:20;

        if(array_sum($idArr)!=0){
            $idArr = Helper::convertIDArrayToString($idArr);
            $whereCondition .= " AND agenda_id IN ($idArr)";
        }

        $status = $option['status'];
        if($status == "non-execution"){
            $whereCondition .= " AND agenda_last_run_at = 0";
        }else if($status == "success"){
            $whereCondition .= " AND agenda_last_run_status = 'Success'";
        }else if($status == "failure"){
            $whereCondition .= " AND agenda_last_run_status = 'Failure'";
        }else if($status == "unknown"){
            $whereCondition .= " AND agenda_last_run_status = 'Unknown'";
        }

        if ($orderBy) {
            $orderCondition = "? ?";
            $bindParams[] = $orderBy;
            $bindParams[] = $sequence;
        }
        $sql = "SELECT agenda.*,{$this->userFieldSample1} FROM agenda LEFT JOIN user ON agenda_user_id = user_id WHERE true {$whereCondition} ORDER BY {$orderCondition} agenda_id DESC";
        if(array_sum($idArr)!=0){
            return $this->sqltool->getListBySql($sql,$bindParams);
        }else{
            return $this->getListWithPage('agenda',$sql,$bindParams,$pageSize);
        }
    }

    public function echoStatus($val){
        if($val == "Success"){
            echo "<span class='label label-success'>Success</span>";
        }else if($val == "Failure"){
            echo "<span class='label label-danger'>Failure</span>";
        }else if($val == "Unknown"){
            echo "<span class='label label-warning'>Unknown</span>";
        }else{
            echo "<span class='label label-primary'>Non-execution</span>";
        }
    }

    public function deleteAgendaByIds(){
        $ids = Helper::request('id','Id can not be null');
        return $this->deleteByIDsReally('agenda', $ids);
    }

}


?>
