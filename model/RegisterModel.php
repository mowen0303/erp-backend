<?php
namespace model;
use \Model as Model;
use \Helper as Helper;

class RegisterModel extends Model
{

    public function getRegisters(array $id,array $option=[]){
        $bindParams = [];
        $selectFields = "";
        $whereCondition = "";
        $orderCondition = "";

        $orderBy    = $option['orderBy'];
        $sequence   = $option['sequence']?:'DESC';
        $pageSize   = $option['pageSize']?:20;

        if(array_sum($id)!=0){
            $id = Helper::convertIDArrayToString($id);
            $whereCondition .= " AND register_id IN ($id)";
        }

        if ($orderBy) {
            $orderCondition = "{$orderBy} {$sequence},";
        }
        $sql = "SELECT * FROM register WHERE true {$whereCondition} ORDER BY {$orderCondition} register_id DESC";
        if(array_sum($id)!=0){
            return $this->sqltool->getListBySql($sql,$bindParams);
        }else{
            return $this->getListWithPage('register',$sql,$bindParams,$pageSize);
        }
    }

    public function getAmountOfProcessing(){
        $sql = "SELECT count(register_id) as amount FROM register WHERE register_status IN (1,2)";
        return (int) $this->sqltool->getRowBySql($sql)['amount'];
    }

    public function modifyRegister($id=0){
        $fileModel = new FileModel();
        $arr['register_company_name'] = ucfirst(strtolower(Helper::post('register_company_name','Company Name can not be null',0,255)));
        $arr['register_company_number'] = ucfirst(strtolower(Helper::post('register_company_number','Company Number can not be null',0,255)));
        $arr['register_company_owner_first_name'] = ucfirst(strtolower(Helper::post('register_company_owner_first_name','Owner First Name can not be null',0,255)));
        $arr['register_company_owner_last_name'] = ucfirst(strtolower(Helper::post('register_company_owner_last_name','Owner Last Name can not be null',0,255)));
        $arr['register_company_address'] = ucfirst(strtolower(Helper::post('register_company_address','Company Address can not be null',0,255)));
        $arr['register_company_city'] = ucfirst(strtolower(Helper::post('register_company_city','Company City can not be null',0,255)));
        $arr['register_company_province'] = ucfirst(strtolower(Helper::post('register_company_province','Company Province can not be null',0,255)));
        $arr['register_company_postcode'] = ucfirst(strtolower(Helper::post('register_company_postcode','Company Post Code can not be null',0,255)));
        $arr['register_company_country'] = ucfirst(strtolower(Helper::post('register_company_country','Country can not be null',0,255)));
        $arr['register_company_phone'] = ucfirst(strtolower(Helper::post('register_company_phone','Company Phone can not be null',0,255)));
        $arr['register_company_fax'] = ucfirst(strtolower(Helper::post('register_company_fax',null,0,255)))?:"";
        $arr['register_company_email'] = ucfirst(strtolower(Helper::post('register_company_email','Company Email can not be null',0,255)));
        $arr['register_company_website'] = ucfirst(strtolower(Helper::post('register_company_website',null,0,255)));
        $arr['register_first_name'] = ucfirst(strtolower(Helper::post('register_first_name','Contact First Name can not be null',0,255)));
        $arr['register_last_name'] = ucfirst(strtolower(Helper::post('register_last_name','Contact Last Name can not be null',0,255)));
        $arr['register_role'] = ucfirst(strtolower(Helper::post('register_role','Contact Position can not be null',0,255)));
        $arr['register_phone'] = ucfirst(strtolower(Helper::post('register_phone','Contact Phone can not be null',0,255)));
        $arr['register_email'] = ucfirst(strtolower(Helper::post('register_email','Contact Email can not be null',0,255)));
        $fileModel->getNumOfUploadImages('register_businessCardFile',['image','pdf']);
        $fileModel->getNumOfUploadImages('register_driverLicenseFile',['image','pdf']);
        $arr['register_company_start_year'] = ucfirst(strtolower(Helper::post('register_company_start_year','Company Startup Year can not be null',0,255)));
        $arr['register_company_type'] = ucfirst(strtolower(Helper::post('register_company_type','Type of Company can not be null',0,255)));
        $arr['register_company_role'] = ucfirst(strtolower(Helper::post('register_company_role','Type of Business can not be null',0,255)));
        $fileModel->getNumOfUploadImages('register_businessLicenseFile',['image','pdf']);
        $arr['register_refer_name'] = ucfirst(strtolower(Helper::post('register_refer_name',null,0,255)));
        $arr['register_refer_media'] = Helper::post('register_refer_media',null,0,255);
        $arr['register_refer_media'] = implode(",",$arr['register_refer_media'])?:"";
        //validate
        if ($id) {
            //修改
            $this->updateRowById('register', $id, $arr,false);
        } else {
            //添加
            $arr['register_status'] = 1;
            //validate
            !$this->isExistByFieldValue('register','register_email',$arr['register_email']) or Helper::throwException('Register Contact Email has already existed',400);
            $id = $this->addRow('register', $arr);
            Helper::mailTo($arr['register_email'],"Dealer Application Status : Waiting for review","<b>Dear Sir/Madam,</b><p>This is to inform you that, Your dealer application has been submitted successfully. Please waiting for our dealer assistant to review your information and contact with you.</p>");
        }

        //上传图片
        if($id){
            $result = $this->getRegisters([$id])[0];
            $old_register_businessCardFile = $result['register_businessCardFile'];
            $old_register_driverLicenseFile = $result['register_driverLicenseFile'];
            $old_register_businessLicenseFile = $result['register_businessLicenseFile'];

            try{
                $imgArr = [];
                $imgArr['register_businessCardFile'] = $fileModel->uploadFile('register_businessCardFile',false,['image','pdf'],false,null,null,300*1000,700,700)[0]['url'];
                $this->updateRowById('register',$id,$imgArr);
                $fileModel->deleteFileByPath($old_register_businessCardFile);
            }catch (\Exception $e){
                $fileModel->deleteFileByPath($imgArr['register_businessCardFile']);
                $this->imgError = " (Image status: {$e->getMessage()})";
            }

            try{
                $imgArr = [];
                $imgArr['register_driverLicenseFile'] = $fileModel->uploadFile('register_driverLicenseFile',false,['image','pdf'],false,null,null,300*1000,700,700)[0]['url'];
                $this->updateRowById('register',$id,$imgArr);
                $fileModel->deleteFileByPath($old_register_driverLicenseFile);
            }catch (\Exception $e){
                $fileModel->deleteFileByPath($imgArr['register_driverLicenseFile']);
                $this->imgError = " (Image status: {$e->getMessage()})";
            }

            try{
                $imgArr = [];
                $imgArr['register_businessLicenseFile'] = $fileModel->uploadFile('register_businessLicenseFile',false,['image','pdf'],false,null,null,300*1000,700,700)[0]['url'];
                $this->updateRowById('register',$id,$imgArr);
                $fileModel->deleteFileByPath($old_register_businessLicenseFile);
            }catch (\Exception $e){
                $fileModel->deleteFileByPath($imgArr['register_businessLicenseFile']);
                $this->imgError = " (Image status: {$e->getMessage()})";
            }
        }
        return $id;
    }

    function echoStatus($status){
        switch ($status){
            case 1:
                echo "<span class=\"label label-table label-warning\">Waiting for Review</span>";
                break;
            case 2:
                echo "<span class=\"label label-table label-info\">In reviewing</span>";
                break;
            case 3:
                echo "<span class=\"label label-table label-success\">Passed</span>";
                break;
            case 4:
                echo "<span class=\"label label-table label-danger\">Rejected</span>";
                break;
        }
    }

    function startReview($id){
        $arr = [];
        $arr['register_status'] = 2;
        $this->updateRowById('register',$id,$arr,false);
        $email = $this->getRegisters([$id])[0]['register_email'];
        Helper::mailTo($email,"Dealer Application Status : In review","<b>Dear Sir/Madam,</b><p>This is to inform you that, Your dealer application is being in reviewing.</p>");
    }

    function passApplication($id,$username,$password){
        $arr = [];
        $arr['register_status'] = 3;
        $this->updateRowById('register',$id,$arr,false);
        $email = $this->getRegisters([$id])[0]['register_email'];
        Helper::mailTo($email,"Dealer Application Status : Passed","<b>Dear Sir/Madam,</b><p>This is to inform you that, Your dealer application is passed the review. Now you can login to ERP system.</p><p>ERP system login url: <a href='http://erp.woodworthcabinetry.com'>http://erp.woodworthcabinetry.com</a></p><p>Your user name: {$username}</p><p>Your password: {$password}</p>");
    }

    function rejectApplication($id,$msg=""){
        $arr = [];
        $arr['register_status'] = 4;
        $arr['register_reject_reason'] = $msg;
        $this->updateRowById('register',$id,$arr,false);
        $email = $this->getRegisters([$id])[0]['register_email'];
        $mailBody = "<b>Dear Sir/Madam,</b><p>This is to inform you that, Your dealer application has been reject.</p>";
        if($msg){
            $mailBody .= "<p>The reason is that {$msg}</p>";
        }
        Helper::mailTo($email,"Dealer Application Status : Rejected",$mailBody);
    }


}


?>
