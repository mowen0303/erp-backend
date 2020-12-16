<?php
namespace model;
use Exception;
use \Model as Model;
use \Helper as Helper;

class BillingAddressModel extends Model
{

    public function modifyBillingAddress($id=0){
        $userModel = new UserModel();
        $currentUserId = $userModel->getCurrentUserId();
        $arr = [];
        $arr['billing_address_first_name'] = Helper::post('billing_address_first_name','First name is required',0,100);
        $arr['billing_address_last_name'] = Helper::post('billing_address_last_name','Last name is required',0,100);
        $arr['billing_address_address'] = Helper::post('billing_address_address','Address is required',0,255);
        $arr['billing_address_city'] = Helper::post('billing_address_city','City is required',0,100);
        $arr['billing_address_province'] = Helper::post('billing_address_province','Province is required',0,100);
        $arr['billing_address_postal_code'] = Helper::post('billing_address_postal_code','Postal code is required',0,100);
        $arr['billing_address_country'] = Helper::post('billing_address_country','Country is required',0,100);
        $arr['billing_address_phone_number'] = Helper::post('billing_address_phone_number','Phone number is required',0,100);
        $arr['billing_address_phone_number_ext'] = Helper::post('billing_address_phone_number_ext',null,0,10);
        if ($id) {
            //修改
            $sql = "SELECT * FROM billing_address WHERE billing_address_id IN ({$id}) AND billing_address_user_id IN ({$currentUserId})";
            $result = $this->sqltool->getRowBySql($sql) or Helper::throwException(null,403);
            $this->updateRowById('billing_address', $id, $arr,false);
        } else {
            //添加
            $arr['billing_address_user_id'] = $currentUserId;
            $id = $this->addRow('billing_address', $arr);
        }
        $sql = "SELECT * FROM billing_address WHERE billing_address_id IN ({$id})";
        return $this->sqltool->getRowBySql($sql);
    }

    public function getBillingAddress(array $id,array $option=[]){
        $bindParams = [];
        $selectFields = "";
        $whereCondition = "";
        $orderCondition = "";
        $joinCondition = "";
        $result = null;

        $orderBy    = $option['orderBy'];
        $sequence   = $option['sequence']?:'DESC';
        $pageSize   = $option['pageSize']?:20;

        if(array_sum($id)!=0){
            $id = Helper::convertIDArrayToString($id);
            $whereCondition .= " AND billing_address_id IN ($id)";
        }

        if($option['userId']){
            $userId = (int) $option['userId'];
            $whereCondition .= " AND billing_address_user_id IN ({$userId})";
        }

        if ($orderBy) {
            $orderCondition = "? ?";
            $bindParams[] = $orderBy;
            $bindParams[] = $sequence;
        }
        $sql = "SELECT * FROM billing_address {$joinCondition} WHERE true {$whereCondition} ORDER BY {$orderCondition} billing_address_id DESC";
        if(array_sum($id)!=0){
            $result = $this->sqltool->getListBySql($sql,$bindParams);
        }else{
            $result = $this->getListWithPage('billing_address',$sql,$bindParams,$pageSize);
        }
        return $result;
    }

    public function deleteBillingAddressByIds($ids){
        return $this->deleteByIDsReally('billing_address',$ids);
    }

    public function getFullAddress($billingAddress){
        $ext = "";
        if($billingAddress['billing_address_phone_number_ext']){
            $ext = "-".$billingAddress['billing_address_phone_number_ext'];
        }
        return "{$billingAddress['billing_address_first_name']} {$billingAddress['billing_address_last_name']}, {$billingAddress['billing_address_phone_number']}, {$billingAddress['billing_address_address']}, {$billingAddress['billing_address_city']}, {$billingAddress['billing_address_province']} , {$billingAddress['billing_address_country']}, {$billingAddress['billing_address_postal_code']}".$ext;
    }
}



?>
