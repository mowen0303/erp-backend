<?php

class Helper
{

    public static $minLengthRestrict = null;
    public static $maxLengthRestrict = null;
    public static $sensitiveWords = ['<','>','.createElement(','eval(','onfocus='];

    /**
     * @param $fieldName
     * @param null $notNullErrorMessage 如果inputValue是必填项,而用户没有没有填写时的错误提示信息。6
     * @param null $minLengthRestrict 最低字符限制
     * @param null $maxLengthRestrict 最高字符限制
     * @return string | array
     * @throws Exception
     */
    public static function get($fieldName, $notNullErrorMessage = null, $minLengthRestrict = null, $maxLengthRestrict = null, $checkSensitiveWords = true) {
        $inputValue = @$_GET[$fieldName];
        return self::trimData($inputValue, $notNullErrorMessage, $minLengthRestrict, $maxLengthRestrict, $checkSensitiveWords,$fieldName);
    }

    /**
     * @param $fieldName
     * @param null $notNullErrorMessage 如果inputValue是必填项,而用户没有没有填写时的错误提示信息。6
     * @param null $minLengthRestrict 最低字符限制
     * @param null $maxLengthRestrict 最高字符限制
     * @return string | array
     * @throws Exception
     */
    public static function post($fieldName, $notNullErrorMessage = null, $minLengthRestrict = null, $maxLengthRestrict = null, $checkSensitiveWords = true) {
        $inputValue = @$_POST[$fieldName];
        return self::trimData($inputValue, $notNullErrorMessage, $minLengthRestrict, $maxLengthRestrict, $checkSensitiveWords,$fieldName);
    }

    /**
     * @param $fieldName
     * @param null $notNullErrorMessage 如果inputValue是必填项,而用户没有没有填写时的错误提示信息。6
     * @param null $minLengthRestrict 最低字符限制
     * @param null $maxLengthRestrict 最高字符限制
     * @return string | array
     * @throws Exception
     */
    public static function request($fieldName, $notNullErrorMessage = null, $minLengthRestrict = null, $maxLengthRestrict = null, $checkSensitiveWords = true) {
        $inputValue = @$_REQUEST[$fieldName];
        return self::trimData($inputValue, $notNullErrorMessage, $minLengthRestrict, $maxLengthRestrict, $checkSensitiveWords,$fieldName);
    }

    /**
     * @param $fieldName
     * @param $inputValue
     * @param null $notNullErrorMessage
     * @param null $minLengthRestrict
     * @param null $maxLengthRestrict
     * @param bool $checkSensitiveWords
     * @return array|bool|string|null
     * @throws Exception
     */
    public static function trimData($inputValue, $notNullErrorMessage = null, $minLengthRestrict = null, $maxLengthRestrict = null, $checkSensitiveWords = true,$fieldName = ""){
        if ($inputValue === null || $inputValue === '' || $inputValue === [] || $inputValue === false) {
            if ($notNullErrorMessage) {
                Helper::throwException("{$fieldName} Error : {$notNullErrorMessage}", 400);
            } else {
                return '';
            }
        }
        if($checkSensitiveWords){
            $inputStr = is_array($inputValue)?implode($inputValue):$inputValue;
            $inputStr = Helper::removeStringSpace($inputStr);
            foreach (Helper::$sensitiveWords as $word){
                stripos($inputStr, $word)===false or Helper::throwException("{$fieldName} Error : System not accept characters '<','>' or other restricted characters");
            }
        }
        Helper::$minLengthRestrict = $minLengthRestrict;
        Helper::$maxLengthRestrict = $maxLengthRestrict;
        if (!function_exists('__trim')) {
            function __trim(&$value, $fieldName) {
                $value = trim($value);
                $strLength = strlen($value);
                if (Helper::$minLengthRestrict !== null) {
                    $strLength >= Helper::$minLengthRestrict or Helper::throwException("{$fieldName} Error : minimum string limitation is " . Helper::$minLengthRestrict . ". but you got {$strLength}", 400);
                }
                if (Helper::$maxLengthRestrict !== null) {
                    $strLength <= Helper::$maxLengthRestrict or Helper::throwException("{$fieldName} Error : maximum string limitation is " . Helper::$maxLengthRestrict. " but you got {$strLength}", 400);
                }
            }
        }
        if (is_array($inputValue)) {
            array_walk_recursive($inputValue, '__trim');
        } else {
            __trim($inputValue, $fieldName);
        }
        return $inputValue;
    }

    public static function removeStringSpace($str) {
        $oldChar=array(" ","　","\t","\n","\r");
        $newChar=array("","","","","");
        return str_replace($oldChar,$newChar,$str);
    }

    /**
     * 输出一个json格式的结果
     * @param $code
     * @param $message
     * @param $result
     */
    public static function echoJson($code, $message, $result = null, $secondResult = null, $thirdResult = null, $jumpToUrl = null, $jumpToUrlText = 'Back',$secondUrl = null,$secondUrlText = null){
        $dataType = $_GET['dataType'];
        if (Helper::isRequestFromCMS() && !Helper::isRequestFromAjax() && $dataType!='json') {
            Helper::echoMessage($message, $jumpToUrl, $jumpToUrlText,$secondUrl,$secondUrlText);
        } else {
            echo json_encode(array('code' => $code, 'message' => $message, 'result' => $result, 'secondResult' => $secondResult, 'thirdResult' => $thirdResult));
        };
    }

    /**
     * 将id数组，转成字符串格式，做了防注入处理：[1,2,3] => '1,2,3'
     * @param mixed $IDs
     * @return string
     */
    public static function convertIDArrayToString($IDs){
        if (is_array($IDs)) {
            $idsStr = "";
            foreach ($IDs as $value) {
                $idsStr .= (int) $value . ",";
            }
            $idsStr = substr($idsStr, 0, -1);
        }else{
            $idsStr = $IDs;
        }
        return $idsStr;
    }

    /**
     * 判断HTTP REQUEST请求是否来自于CMS系统
     * @return bool
     */
    public static function isRequestFromCMS(){
        $sourceUrl = $_SERVER['HTTP_REFERER'];
        $currentUrl = $_SERVER['PHP_SELF'];
        return (bool)strpos($sourceUrl, 'admin') || strpos($currentUrl, 'admin');
    }

    public static function isRequestFromAjax(){
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * 加载指定的代码片段
     * @param string $snippetName 根据地址栏传值,地址栏变量s
     * @param string $pageTitle 页面标题
     * @param string $defaultSnippet 设置不传值默认显示的代码片段
     */
    public static function loadSnippet($snippetName, $pageTitle, $defaultSnippet)
    {
        if ($snippetName == null) {
            $snippetName = $defaultSnippet;
        }
        include_once "snippet/{$snippetName}.php";
    }

    /**
     * 输出一个提示页
     * @param string $msg 提示文本
     * @param string $url 跳转地址
     * @param string $urlTxt 跳转按钮标题
     */
    public static function echoMessage($msg, $url = null, $urlTxt = "Back",$secondUrl=null,$secondUrlText=null)
    {
        include_once $_SERVER['DOCUMENT_ROOT'] . "/admin/_template/msg.php";
        die();
    }

    /**
     * @param $url
     * @param string $frame
     */
    public static function jumpTo($url, $frame = 'self')
    {
        echo "<script>{$frame}.location='{$url}'</script>";
        exit();
    }

    /**
     * @param $email
     * @return bool
     * @throws Exception
     */
    public static function validateEmail($email)
    {
        if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email)) {
            Helper::throwException('Email format is incorrect');
        }
        return true;
    }

    public static function echoLabel($num,$type='danger'){
        if($num>0){
            echo "<span class=\"label label-rouded label-{$type} pull-right\">{$num}</span></span>";
        }
    }


    public static function throwException($message, $code = 400){
        if($code == 403 && $message == null){
            throw new Exception("Permission denied", 403);
        }else if($code == 404 && $message == null){
            throw new Exception("No Data", 404);
        }else{
            throw new Exception($message, $code);
        }
    }

    public static function saveBackUrl($level){
        if($level==1){
            $_SESSION["back_url_1"]=$_SERVER['REQUEST_URI'];
            $_SESSION["back_url_2"]="";
        }else if($level==2){
            $_SESSION["back_url_2"]=$_SERVER['REQUEST_URI'];
        }
    }

    public static function getRepeat($arr){
        $arr = array_filter($arr);
        $uniqueArr = array_unique ( $arr );
        return array_diff_assoc( $arr, $uniqueArr );
    }

    public static function echoBackBtn($level=0,$returnLink=false){
        //save url
        if($level==1){
            $_SESSION["back_url_1"]=$_SERVER['REQUEST_URI'];
            $_SESSION["back_url_2"]="";
        }else if($level==2){
            $_SESSION["back_url_2"]=$_SERVER['REQUEST_URI'];
        }

        //echo url
        if($_SESSION['back_url_2'] && ($_SESSION['back_url_2'] != $_SERVER['REQUEST_URI'])){
            if($returnLink){
                return $_SESSION['back_url_2'];
            }else{
                echo "<a href='{$_SESSION['back_url_2']}' class='btn btn-info pull-right m-l-10'>Back</a>";
            }
        }else if($_SESSION['back_url_1'] != $_SERVER['REQUEST_URI']){
            if($returnLink){
                return $_SESSION['back_url_1'];
            }else{
                echo "<a href='{$_SESSION['back_url_1']}' class='btn btn-info pull-right m-l-10'>Back</a>";
            }
        }
    }

    public static function echoCountryOption(){
        $countryArr = ['Canada','China','United States'];
        echo '<option value="">-- Select --</option>';
        foreach ($countryArr as $country){
            echo "<option value='{$country}'>{$country}</option>";
        }
    }


    //office365服务器
    public static function mailTo($mailAddress, $mailTitle, $mailBody,$alertError=false)
    {
        require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/resource/tools/email/class.phpmailer.php";
        $mail = new PHPMailer();
        $mail->CharSet = "UTF-8";
        $mail->setLanguage('zh_cn');

        //配置发送邮件的账号密码
        $email =  "info@woodworthcabinetry.com";
        $password = "854Westport";

        //配置邮局
        $mail->IsSMTP();
        $mail->Host = "smtp.office365.com";
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->Username = $email;
        $mail->Password = $password;
        $mail->From = $email;
        $mail->FromName = "Wood Worth Cabinetry WLINK System";
        if($alertError){
            $mail->SMTPDebug  = 3;
        }else{
            $mail->SMTPDebug  = 0;
        }

        //配置邮件内容
        $mail->AddAddress($mailAddress, "");    //收件人email,收件人姓名
        $mail->IsHTML(true);                    //是否使用HTML格式
        $mail->Subject = $mailTitle;            //邮件标题
        $mail->Body = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
            <meta name="viewport" content="width=device-width" />
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <title>WoodWorth Cabinetry</title>
            </head>
            <body style="margin:0px; background: #f8f8f8; ">
            <div width="100%" style="background: #f8f8f8; padding: 0px 0px; font-family:arial; line-height:28px; height:100%;  width: 100%; color: #514d6a;">
              <div style="max-width: 700px; padding:50px 0;  margin: 0px auto; font-size: 14px">
                <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 20px">
                  <tbody>
                    <tr>
                      <td style="vertical-align: top; padding-bottom:20px;" align="center">
                      <h2 style="color: #444">WOODWORTH CABINETRY</h2>
                      <h2 style="color: #444">WLINK System</h2>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <div style="padding: 40px; background: #fff;">
                  <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                    <tbody>
                      <tr>
                        <td>
                        '.$mailBody.'
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div style="text-align: center; font-size: 12px; color: #b2b2b5; margin-top: 20px">
                  <p>WoodWorth Cabinetry</p>
                </div>
              </div>
            </div>
            </body>
            </html>
        ';
        //$mail->AltBody = "This is the body in plain text for non-HTML mail clients"; //附加信息，可以省略
        $mail->Send();
        if($alertError){
            print_r('<br><br>---------<br><br>');
            print_r($mail->ErrorInfo);
        }
    }

    static public function priceInput($price){
        return (int) ($price*100);
    }

    static public function priceOutput($price){
        return round($price/100,2);
    }

    /**
     * @param int $number
     * @throws Exception
     */
    static public function validatePhoneNumber($number){
        is_numeric($number) && strlen($number)>8 && strlen($number)<16 or Helper::throwException('Phone number format is incorrect',405);
    }

    static public function exportExcel($fileName,array $headArr, array $bodyArr){
        // 输出Excel文件头，可把user.csv换成你要的文件名
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'.csv"');
        header('Cache-Control: max-age=0');
        // 打开PHP文件句柄，php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');
        // 输出Excel列名信息
        $head = $headArr;
        foreach ($head as $i => $v) {
            // CSV的Excel支持GBK编码，一定要转换，否则乱码
            $head[$i] = iconv('utf-8', 'gbk', $v);
        }
        // 将数据通过fputcsv写到文件句柄
        fputcsv($fp, $head);
        // 计数器
        $cnt = 0;
        // 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 100000;
        // 逐行取出数据，不浪费内存
        while ($row = $bodyArr[$cnt]) {
            $newRow = [];
            $cnt ++;
            if ($limit == $cnt) {
                //刷新一下输出buffer，防止由于数据过多造成问题
                ob_flush();
                flush();
                $cnt = 0;
            }

            foreach ($head as $i => $v) {
                $newRow[$i] = iconv('utf-8', 'gbk', $row[$i]);
            }
            fputcsv($fp, $newRow);
        }
    }

}


?>
