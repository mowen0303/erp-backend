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
        //SELECT * FROM `company` left join company_location on company_location_company_id = company_id where company_location_is_head_office IN (1) or company_location_is_head_office IS NULL
        $sql = "SELECT * FROM company LEFT JOIN company_location ON company_id = company_location_company_id WHERE (company_location_is_head_office IN (1) or company_location_is_head_office IS NULL) {$whereCondition} ORDER BY {$orderCondition} company_id DESC";
        if(array_sum($companyIds)!=0){
            return $this->sqltool->getListBySql($sql,$bindParams);
        }else{
            return $this->getListWithPage('company',$sql,$bindParams,$pageSize);
        }
    }

    public function modifyCompany($id=0){
        $arr['company_name'] = Helper::post('company_name','Company Name can not be null',1,150);
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
        $sql = "SELECT company_location_id FROM company_location WHERE company_location_company_id IN ({$idsStr})";
        $result = $this->sqltool->getListBySql($sql);
        if($result){
            Helper::throwException("Can not delete the Company you selected because there are company_locations belongs to the Company.<br> If you want to delete the company, please remove all the company_location within the company first.");
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
     * ============   company location  ===========
     * ============================================
     * ============================================
     * ============================================
     * ============================================
     *
     */


    /**
     * @param array $company_locationId
     * @param array $option
     * @return array
     * @throws \Exception
     */
    public function getCompanyLocations(array $company_locationId, array $option=[]){
        $bindParams = [];
        $selectFields = "";
        $whereCondition = "";
        $orderCondition = "";

        $orderBy    = $option['orderBy'];
        $sequence   = $option['sequence']?:'DESC';
        $pageSize   = $option['pageSize']?:20;

        if(array_sum($company_locationId)!=0){
            $company_locationId = Helper::convertIDArrayToString($company_locationId);
            $whereCondition .= " AND company_location_id IN ($company_locationId)";
        }

        if($option['companyId']){
            $whereCondition .= " AND company_location_company_id IN ({$option['companyId']})";
        }

        if ($orderBy) {
            $orderCondition = "? ?";
            $bindParams[] = $orderBy;
            $bindParams[] = $sequence;
        }
        $sql = "SELECT * FROM company_location WHERE true {$whereCondition} ORDER BY {$orderCondition} company_location_id DESC";
        if(array_sum($company_locationId)!=0){
            return $this->sqltool->getListBySql($sql,$bindParams);
        }else{
            return $this->getListWithPage('company_location',$sql,$bindParams,$pageSize);
        }
    }

    public function modifyCompanyLocation($company_locationId=0){
        $arr['company_location_company_id'] = (int) Helper::post('company_location_company_id','Company Id can not be null');
        $arr['company_location_is_head_office'] = (int) Helper::post('company_location_is_head_office','Please select the company location type');
        $arr['company_location_country'] = ucfirst(strtolower(Helper::post('company_location_country','Country Name can not be null',1,50)));
        $arr['company_location_province'] = ucfirst(strtolower(Helper::post('company_location_province','Province can not be null')));
        $arr['company_location_city'] = ucfirst(strtolower(Helper::post('company_location_city','City can not be null')));
        $arr['company_location_address'] = ucfirst(strtolower(Helper::post('company_location_address','Address can not be null')));
        $arr['company_location_post_code'] = Helper::removeStringSpace(strtoupper(Helper::post('company_location_post_code','Post Code can not be null',3,8)));
        $arr['company_location_phone'] = Helper::post('company_location_phone') ?: "";
        $arr['company_location_email'] = Helper::post('company_location_email') ?: "";
        $arr['company_location_fax'] = Helper::post('company_location_fax') ?: "";
        $arr['company_location_website'] = strtolower(Helper::post('company_location_website',null,0,255)) ?: "";
        //validate
        if ($company_locationId) {
            //修改
            $result = $this->updateRowById('company_location', $company_locationId, $arr);
            if($result && $arr['company_location_is_head_office']==1){
                $this->resetHeadOffice($company_locationId,$arr['company_location_company_id']);
            }
            return $result;
        } else {
            //添加
            //validate
            !$this->isExistByFieldValue('company_location','company_location_address',$arr['company_location_address']) or Helper::throwException('company location Address has already existed',400);
            $company_locationId = $this->addRow('company_location', $arr);
            if($company_locationId && $arr['company_location_is_head_office']==1){
                $this->resetHeadOffice($company_locationId,$arr['company_location_company_id']);
            }
            return $company_locationId;
        }
    }

    private function resetHeadOffice(int $headOfficecompany_locationId,int $companyId){
        $sql = "UPDATE company_location SET company_location_is_head_office = 0 WHERE company_location_company_id = {$companyId} AND company_location_id NOT IN ($headOfficecompany_locationId)";
        $this->sqltool->query($sql);
    }

    public function deleteCompanyLocationByIds(){
        $ids = Helper::request('id','Id can not be null');
        $sql = "SELECT user_id FROM user WHERE user_company_location_id IN ($ids)";
        $result = $this->sqltool->getListBySql($sql);
        if($result){
            Helper::throwException("Can not delete the company location you selected because there are users belongs to the company location.<br> If you want to delete the company location data, please remove the user who within the company location first.");
        }else{
            return $this->deleteByIDsReally('company_location', $ids);
        }
    }

}


?>
