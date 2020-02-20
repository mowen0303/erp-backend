<?php
namespace model;
use \Model as Model;
use \Helper as Helper;

class CompanyModel extends Model
{
    public function getCompanies(array $companyIds,array $option=[]){
        $bindParams = [];
        $selectFields = "";
        $whereCondition = "";
        $orderCondition = "";

        $orderBy    = $option['orderBy'];
        $sequence   = $option['sequence']?:'DESC';
        $pageSize   = $option['pageSize']?:20;

        if(array_sum($companyIds)!=0){
            $companyIds = Helper::convertIDArrayToString($companyIds);
            $whereCondition .= " AND company_id IN ($companyIds)";
        }

        if($option['searchValue']){
            $whereCondition .= "AND company_name LIKE '%{$option['searchValue']}%'";
        }

        if ($orderBy) {
           $orderCondition = "? ?";
           $bindParams[] = $orderBy;
           $bindParams[] = $sequence;
        }
        $sql = "SELECT * FROM company WHERE true {$whereCondition} ORDER BY {$orderCondition} company_id DESC";
        if(array_sum($companyIds)!=0){
            return $this->sqltool->getListBySql($sql,$bindParams);
        }else{
            return $this->getListWithPage('company',$sql,$bindParams,$pageSize);
        }
    }

    public function modifyCompany($id=0){
        $arr['company_name'] = ucfirst(strtolower(Helper::post('company_name','Company Name can not be null',1,150)));
        $arr['company_country'] = ucfirst(strtolower(Helper::post('company_country','Country Name can not be null',1,50)));
        $arr['company_owner_name'] = ucfirst(strtolower(Helper::post('company_owner_name','Owner Name can not be null',1,150)));
        $arr['company_business_number'] = Helper::post('company_business_number','Business Number can not be null',1,24);
        $arr['company_startup_year'] = Helper::post('company_startup_year')?:"";
        $arr['company_type'] = Helper::post('company_type')?:"";
        $arr['company_role'] = Helper::post('company_role')?:"";
        //validate
        if ($id) {
            //修改
            $this->updateRowById('company', $id, $arr,false);
        } else {
            //添加
            //validate
            $sql = "SELECT company_id FROM company WHERE company_name = '{$arr['company_name']}' AND company_business_number = '{$arr['company_business_number']}'";
            $result = $this->sqltool->getRowBySql($sql);
            !$result or  Helper::throwException('Company has already existed',400);
            $id = $this->addRow('company', $arr);
        }

        if($id){
            //上传图片
            try{
                $fileArr = [];
                $oldFile = $this->getCompanies([$id])[0]['company_license_file'];
                $fileModel = new FileModel();
                $fileArr['company_license_file'] = $fileModel->uploadFile('file',false,['image','pdf'],false,null,null,3*1000*1000,4000,700)[0]['url'];
                $this->updateRowById('company',$id,$fileArr);
                $fileModel->deleteFileByPath($oldFile);
            }catch (\Exception $e){
                $fileModel->deleteFileByPath($fileArr['company_license_file']);
                $this->imgError = " (Image status: {$e->getMessage()})";
            }
        }
        return $id;
    }


    public function deleteCompanyByIds(){
        $ids = Helper::request('id','Id can not be null');
        if(!is_array($ids)) $ids = [$ids];
        $idsStr = Helper::convertIDArrayToString($ids);
        $sql = "SELECT store_id FROM store WHERE store_company_id IN ({$idsStr})";
        $result = $this->sqltool->getListBySql($sql);
        if($result){
            Helper::throwException("Can not delete the Company you selected because there are stores belongs to the Company.<br> If you want to delete the company, please remove all the store within the company first.");
        }else{
            $companyArr = $this->getCompanies($ids);
            $fileArr = [];
            foreach ($companyArr as $company){
                $fileArr[] = $company['company_license_file'];
            }
            $deletedRows = $this->deleteByIDsReally('company', $ids);
            $fileModel = new FileModel();
            foreach ($fileArr as $file){
                $fileModel->deleteFileByPath($file);
            };
            return $deletedRows;
        }
    }

    public function getALLCompanyNumber(){
        $sql ="SELECT count(company_id) as count FROM company";
        return (int) $this->sqltool->getRowBySql($sql)['count'];
    }


    /**
     * ============================================
     * ============================================
     * ============================================
     * ============================================
     * ================   store  ==================
     * ============================================
     * ============================================
     * ============================================
     * ============================================
     *
     */


    /**
     * @param array $storeId
     * @param array $option
     * @return array
     * @throws \Exception
     */
    public function getStores(array $storeId,array $option=[]){
        $bindParams = [];
        $selectFields = "";
        $whereCondition = "";
        $orderCondition = "";

        $orderBy    = $option['orderBy'];
        $sequence   = $option['sequence']?:'DESC';
        $pageSize   = $option['pageSize']?:20;

        if(array_sum($storeId)!=0){
            $storeId = Helper::convertIDArrayToString($storeId);
            $whereCondition .= " AND store_id IN ($storeId)";
        }

        if($option['companyId']){
            $whereCondition .= " AND store_company_id IN ({$option['companyId']})";
        }

        if ($orderBy) {
            $orderCondition = "? ?";
            $bindParams[] = $orderBy;
            $bindParams[] = $sequence;
        }
        $sql = "SELECT * FROM store WHERE true {$whereCondition} ORDER BY {$orderCondition} store_id DESC";
        if(array_sum($storeId)!=0){
            return $this->sqltool->getListBySql($sql,$bindParams);
        }else{
            return $this->getListWithPage('store',$sql,$bindParams,$pageSize);
        }
    }

    public function modifyStore($storeId=0){
        $arr['store_company_id'] = (int) Helper::post('store_company_id','Company Id can not be null');
        $arr['store_is_head_office'] = (int) Helper::post('store_is_head_office','Please select the store type');
        $arr['store_country'] = ucfirst(strtolower(Helper::post('store_country','Country Name can not be null',1,50)));
        $arr['store_province'] = ucfirst(strtolower(Helper::post('store_province','Province can not be null')));
        $arr['store_city'] = ucfirst(strtolower(Helper::post('store_city','City can not be null')));
        $arr['store_address'] = ucfirst(strtolower(Helper::post('store_address','Address can not be null')));
        $arr['store_post_code'] = Helper::removeStringSpace(strtoupper(Helper::post('store_post_code','Post Code can not be null',3,8)));
        $arr['store_phone'] = Helper::post('store_phone') ?: "";
        $arr['store_email'] = Helper::post('store_email') ?: "";
        $arr['store_fax'] = Helper::post('store_fax') ?: "";
        $arr['store_website'] = strtolower(Helper::post('store_website')) ?: "";
        //validate
        if ($storeId) {
            //修改
            $result = $this->updateRowById('store', $storeId, $arr);
            if($result && $arr['store_is_head_office']==1){
                $this->resetHeadOffice($storeId,$arr['store_company_id']);
            }
            return $result;
        } else {
            //添加
            //validate
            !$this->isExistByFieldValue('store','store_address',$arr['store_address']) or Helper::throwException('Store Address has already existed',400);
            $storeId = $this->addRow('store', $arr);
            if($storeId && $arr['store_is_head_office']==1){
                $this->resetHeadOffice($storeId,$arr['store_company_id']);
            }
            return $storeId;
        }
    }

    private function resetHeadOffice(int $headOfficeStoreId,int $companyId){
        $sql = "UPDATE store SET store_is_head_office = 0 WHERE store_company_id = {$companyId} AND store_id NOT IN ($headOfficeStoreId)";
        $this->sqltool->query($sql);
    }

    public function deleteStoreByIds(){
        $ids = Helper::request('id','Id can not be null');
        $sql = "SELECT user_id FROM user WHERE user_store_id IN ($ids)";
        $result = $this->sqltool->getListBySql($sql);
        if($result){
            Helper::throwException("Can not delete the store you selected because there are users belongs to the store.<br> If you want to delete the store data, please remove the user who within the store first.");
        }else{
            return $this->deleteByIDsReally('store', $ids);
        }
    }

}


?>
