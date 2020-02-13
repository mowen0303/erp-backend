<?php
namespace model;
use \Model as Model;
use \Helper as Helper;

class UserModel extends Model
{
    public $defaultAvatar = '/upload/head-default.png';
    public $currentUserProfile = null;


    public function __construct() {
        parent::__construct();
        //如果用户已登录,则验证秘钥是否合法
        if (@$_COOKIE['cc_id']) {
            $cc = md5($_COOKIE['cc_id'] . $_COOKIE['cc_uc'] . $_COOKIE['cc_na'] . $_COOKIE['cc_ul'] . USER_PK);
            if ($cc !== $_COOKIE['cc_cc']) {
                $this->logout();
                Helper::throwException("Your user key is not valid, please re-login", 403);
            }
        }
    }

    /**
     * 用户登录，并返回 用户信息array
     * @param string $name
     * @param string $pwd
     * @return array(一维)
     * @throws \Exception
     */
    public function login() {
        $email = Helper::post('user_email', "user name can not be empty");
        $pwd = Helper::post('user_pwd', "password can not be empty",6);
        $pwd = md5($pwd);
        $sql = "SELECT user_id FROM user WHERE user_email = ? AND user_pwd = ?  AND user_status > 0";
        $row = $this->sqltool->getRowBySql($sql, [$email, $pwd]);
        $row or Helper::throwException("Username or password is incorrect", 400);
        $this->updateUserCheckinLastTime($row['user_id']);
        return self::setCookie($row['user_id']);
    }


    /**
     * 更新用户Cookie，并返回用户信息array
     * @param int $userId
     * @return array(一维)
     * @throws \Exception
     */
    private function setCookie(int $userId) {
        $row = $this->getProfileOfUserById($userId, true);
        $time = Helper::isRequestFromCMS() ? 0 : time() + 3600 * 24 * 1;
        //设置Cookie
        $arr['cc_id'] = $row['user_id'];                //保护
        $arr['cc_uc'] = $row['user_user_category_id'];  //保护
        $arr['cc_na'] = $row['user_email'];              //保护
        $arr['cc_ul'] = $row['user_category_level'];    //保护
        $arr['cc_cc'] = md5($arr['cc_id'] . $arr['cc_uc'] . $arr['cc_na'] . $arr['cc_ul'] . USER_PK);
        $arr['user_alias'] = $row['user_alias'];
        //验证码
        foreach ($arr as $k => $v) {
            setcookie($k, $v, $time, '/');
        }
        return $row;
    }

    /**
     * 用户登出
     * @throws \Exception
     */
    public function logout(){
        foreach ($_COOKIE as $k => $v) {
            setcookie($k, "", time() - 10000000, '/');
        }
        return true;
    }

    /**
     * 更新用户
     * @param int $userId
     * @throws \Exception
     */
    public function updateUserCheckinLastTime(int $userId){
        $sql = "UPDATE user SET user_last_login_time = current_timestamp() WHERE user_id = {$userId}";
        $this->sqltool->query($sql);
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getCurrentUserId() {
        $userId = @$_COOKIE['cc_id'] or Helper::throwException('You did not login yet', 403);
        return (int)$userId;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getCurrentUserCategoryLevel(){
        $userCategoryLevel = @$_COOKIE['cc_ul'] or Helper::throwException('You did not login yet', 403);
        return (int)$userCategoryLevel;
    }

    /**
     * @param int $id
     * @param bool $joinCompany
     * @return array
     * @throws \Exception
     */
    public function getProfileOfUserById(int $id,$joinCompany=false) {
        $id > 0 or Helper::throwException('user Id is not valid');
        $joinCondition = "";
        if($joinCompany){
            $joinCondition .= " LEFT JOIN store ON store_id = user_store_id LEFT JOIN company ON company_id = store_company_id ";
        }
        $sql = "SELECT * FROM user INNER JOIN user_category ON user_user_category_id = user_category_id {$joinCondition} WHERE user_id IN (?) AND user_status > 0";
        $row = $this->sqltool->getRowBySql($sql, [$id]);
        $row or Helper::throwException("can not find user id: $id");
        unset($row['user_pwd']);
        return $row;
    }

    /**
     * 验证权限，输入权限配置表的二维数组的两个key即可
     * @param string $subSystemName 子系统名称 ex. 'USER', 'COURSE', 'FORUM', etc
     * @param string $subSystemAction 执行名 ex. 'GET', 'POST', etc
     * @return bool
     * @throws \Exception
     */
    public function isCurrentUserHasAuthority(string $subSystemName, string $subSystemAction, int $ownerId = null) {
        global $_AUT;
        @$_COOKIE['cc_id'] or Helper::throwException('Please sign in first', 403);
        if(!$this->currentUserProfile){
            $this->currentUserProfile = $this->getProfileOfUserById($_COOKIE['cc_id']) or Helper::throwException("Can not find the account", 403);
        }
        $this->currentUserProfile ['user_status'] == 1 or Helper::throwException("Your account has been terminated", 403);
        $authority = json_decode($this->currentUserProfile ['user_category_authority'], true);
        if ($authority[$subSystemName] & $_AUT[$subSystemName][$subSystemAction] || $_COOKIE['cc_id'] == $ownerId) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param array $userIds
     * @param array $option
     * @return array
     * @throws \Exception
     */
    public function getUsers(array $userIds, array $option=[]){

        $bindParams = [];
        $selectFields = "";
        $whereCondition = "";
        $orderCondition = "";

        $orderBy    = $option['orderBy'];
        $sequence   = $option['sequence']?:'DESC';
        $pageSize   = $option['pageSize']?:20;

        if(array_sum($userIds)!=0){
            $userIds = Helper::convertIDArrayToString($userIds);
            $whereCondition .= " AND user_id IN ($userIds)";
        }

        if($option['searchValue']){
            $whereCondition .= "AND (user_email LIKE '%{$option['searchValue']}%' OR concat(user_first_name,' ',user_last_name) LIKE '%{$option['searchValue']}%')";
        }

        if($option['userCategoryId']){
            $whereCondition .= "AND user_category_id IN ({$option['userCategoryId']})";
        }

        if($option['companyId']){
            $whereCondition .= "AND company_id IN ({$option['companyId']})";
        }

        if($option['storeId']){
            $whereCondition .= "AND store_id IN ({$option['storeId']})";
        }

        if ($orderBy) {
            $orderCondition = "{$orderBy} {$sequence},";
        }
        $sql = "SELECT * {$selectFields} FROM user INNER JOIN user_category ON user_user_category_id = user_category_id LEFT JOIN store ON user_store_id = store_id LEFT JOIN company ON store_company_id = company_id WHERE true {$whereCondition} ORDER BY {$orderCondition} user_id DESC";
        if(array_sum($userIds)!=0){
            return $this->sqltool->getListBySql($sql,$bindParams);
        }else{
            return $this->getListWithPage('user',$sql,$bindParams,$pageSize);
        }

        if($result){
            foreach ($result as $key => $row){
                unset($result[$key]['user_pwd']);
            }
        }
        return $result;

    }


    /**
     * @return int
     * @throws \Exception
     */
    public function modifyUser(int $userId=null){
        $isAdminManage = (int) Helper::post('isAdminManage');
        if($isAdminManage){
            $arr['user_user_category_id'] = (int) Helper::post('user_user_category_id', 'User Category Id can not be null');
            $arr['user_store_id'] = (int) Helper::post('user_store_id', 'User Store Id can not be null');
            $targetUserCategoryLevel = $this->getUserCategoryById($arr['user_user_category_id'])['user_category_level'] or Helper::throwException("User category does not exist",404);
            $this->getCurrentUserCategoryLevel() < $targetUserCategoryLevel or Helper::throwException("You can not add/update a user who has the same or higher than you");
        }
        $arr['user_last_name'] = ucfirst(strtolower(Helper::post('user_last_name','Last Name can not be null')));
        $arr['user_first_name'] = ucfirst(strtolower(Helper::post('user_first_name','First Name can not be null')));
        $arr['user_role'] = Helper::post('user_role','Role can not be null');
        $arr['user_phone'] = Helper::post('user_phone','Phone can not be null');
        $arr['user_fax'] = Helper::post('user_fax');
        $arr['user_address'] = Helper::post('user_address');
        $arr['user_business_hour'] = Helper::post('user_business_hour');
        $arr['user_status'] = 1;
        //validate
        Helper::validatePhoneNumber($arr['user_phone']);
        if ($userId) {
            //修改
            return $this->updateRowById('user', $userId, $arr);
        } else {
            /**
             * ==============================
             * ==========  添加  =============
             * ==============================
             */
            $arr['user_avatar'] = $this->defaultAvatar;
            $arr['user_email'] = Helper::post('user_email','Email can not be null',6);
            $arr['user_pwd'] = md5(Helper::post('user_pwd','Password can not be null',6));
            //validate
            Helper::validateEmail($arr['user_email']);
            !$this->isExistByFieldValue('user','user_email',$arr['user_email']) or Helper::throwException('Email  has already existed',400);
            $userId = $this->addRow('user', $arr);
            //上传图片
            $uploadedImg = null;
            try{
                $imageModel = new ImageModel();
                $uploadedImg = $imageModel->uploadImage('imgFile',false,false,false,null,null,300*1000,700,700)[0]['url'];
                $this->updateAvatar($userId,$uploadedImg);
            }catch (\Exception $e){
                $this->imgError = " (Image status: {$e->getMessage()})";
            }
            return $userId;
        }
    }

    public function updateAvatar($userId,$img){
        $arr = [];
        $arr['user_avatar'] = $img;
        $this->updateRowById('user',$userId,$arr);
    }

    public function updatePassword($userId){
        $arr = [];
        $password1 = Helper::post('pw1',"New password can not be null");
        $password2 = Helper::post('pw2',"Confirm new password can not be null");
        $isAdminManage = (int) Helper::post('isAdminManage');
        if(!$isAdminManage){
            $password0 = md5(Helper::post('pw0',"Old password can not be null"));
            $sql = "SELECT user_id FROM user WHERE user_id IN ({$userId}) AND user_pwd = '{$password0}'";
            $this->sqltool->getRowBySql($sql) or Helper::throwException("Your old password is not correct");

        }
        $password1 == $password2 or Helper::throwException("Your new password an confirm new password are not same");
        $arr['user_pwd'] = md5($password1);
        $this->updateRowById('user',$userId,$arr);
    }

    /**
     * 判断当前用户是否有权管理另一个用户
     * @param $targetUserId
     * @return bool
     * @throws \Exception
     */
    public function validateCurrentUserHasAuthorityToManageTargetUser($targetUserId){
        if ($this->getCurrentUserId() == $targetUserId) {
            //自己,可以修改
            return true;
        } else {
            if ($this->isCurrentUserHasAuthority('USER', 'UPDATE')) {
                $targetUser = $this->getProfileOfUserById($targetUserId);
                $this->getCurrentUserCategoryLevel() < $targetUser['user_category_level'] or Helper::throwException("You can not modify a user within higher or same level");
            }else{
                Helper::throwException(null,403);
            }
        }
        return true;
    }

    public function getALLClientNumber(){
        $sql ="SELECT count(user_id) as count FROM user WHERE user_status = 1";
        return (int) $this->sqltool->getRowBySql($sql)['count'];
    }

    public function deleteUserByIds(){
        $currentUserCategoryLevel = $this->getCurrentUserCategoryLevel();
        $userIds = Helper::request('id','Id can not be null');
        if(!is_array($userIds)){
            $userIds = [$userIds];
        }
        $userArr = $this->getUsers($userIds);
        foreach ($userArr as $user){
            $avatarArr[] = $user['user_avatar'];
            $user['user_category_level']>$currentUserCategoryLevel or Helper::throwException("You can not delete a user whose admin lever is same or higher that you.",403);
        }
        $deletedRows = $this->deleteByIDsReally('user', $userIds);
        $imageModel = new ImageModel();
        foreach ($avatarArr as $avatar){
            if($avatar != $this->defaultAvatar){
                $imageModel->deleteImgByPath($avatar);
            }
        };
        return $deletedRows;
    }

    /**
     * ============================================
     * ============================================
     * ============================================
     * ============================================
     * ============================================
     * ============================================
     * ============================================
     * ============================================
     * ============================================
     * ============================================
     * ============== User Category ===============
     * ============================================
     * ============================================
     * ============================================
     * ============================================
     * ============================================
     * ============================================
     * ============================================
     * ============================================
     * ============================================
     * ============================================
     */


    /**
     * 获得用户的分类列表
     * @param int $pageSize
     * @return array
     * @throws \Exception
     */
    public function getUserCategories($onlyShowLowerLevel=false) {
        $where = "WHERE true ";
        if($onlyShowLowerLevel){
            $currentUserLevel = $this->getCurrentUserCategoryLevel();
            $where .= " AND user_category_level > {$currentUserLevel}";
        }
        $sql = "SELECT * FROM user_category {$where} ORDER BY user_category_id DESC";
        return $this->sqltool->getListBySql($sql, null);
    }

    /**
     * 获取用户分类
     * @param $userCategoryId
     * @return array
     * @throws \Exception
     */
    public function getUserCategoryById($userCategoryId){
        return $this->getRowById('user_category', $userCategoryId);
    }

    /**
     * 添加/更新`用户分类`
     * @param user_category_id
     * @param user_category_title
     * @param user_category_level
     * @param AUT_KEY
     * @throws \Exception
     */
    public function modifyUserCategory() {
        $userCategoryId             = (int) Helper::post('user_category_id');
        $arr['user_category_title'] = Helper::post('user_category_title', 'Category Title can not be null', 0, 40);
        $arr['user_category_level'] = Helper::post('user_category_level', 'Is Admin can not be null');
        $autKey                     = Helper::post('AUT_KEY', 'Authority can not be null');

        $autValueKV = [];
        $autKey = array_filter(explode(',',$autKey));
        foreach ($autKey as $key => $value) {
            $autValueKV[$value] = (int)array_sum($_POST["AUT_{$value}"]);
        }
        $arr['user_category_authority'] = json_encode($autValueKV);
        if ($userCategoryId) {
            //修改
            $this->updateRowById('user_category', $userCategoryId, $arr);
        } else {
            //添加
            $this->addRow('user_category', $arr);
        }
    }

    /**
     * @param id
     * @return |null
     * @throws \Exception
     */
    public function deleteUserCategoryByIds(){
        $categoryIds = Helper::request('id','Id can not be null');
        return $this->deleteByIDsReally('user_category', $categoryIds);
    }
}


?>
