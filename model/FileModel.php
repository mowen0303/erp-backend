<?php
namespace model;
use \Model as Model;
use \Helper as Helper;
use \Exception as Exception;

class FileModel extends Model {

    const MAX_URL_LENGTH = 155;
    const MAX_WIDTH_LENGTH = 5;
    const MAX_HEIGHT_LENGTH = 5;
    const MAX_SECTION_NAME_LENGTH = 30;
    const MAX_UPLOAD_FILE_SIZE = 300*1000;
    const MAX_UPLOAD_IMAGE_SIDE = 800;
    const MAX_UPLOAD_ORIGINAL_FILE_SIZE = 4*1000*1000;
    const MAX_UPLOAD_ORIGINAL_IMAGE_SIDE = 8000;

    const GAUSSIAN_KERNEL = array(
        array(1,2,1),
        array(2,4,2),
        array(1,2,1)
    );


    public function addFile(
        string $sectionName,
        int $sectionId,
        string $url,
        int $width,
        int $height,
        string $originalUrl = null,
        string $type = 'img'
    ) {
        $sectionName = Helper::trimData($sectionName,"File section name cannot be empty", null, FileModel::MAX_SECTION_NAME_LENGTH);
        $sectionId = Helper::trimData($sectionId,"File section id cannot be empty", null, 11);
        $url = Helper::trimData($url,"File url cannot be empty", null, FileModel::MAX_URL_LENGTH);
        $width = Helper::trimData($width,"File width cannot be empty", null, FileModel::MAX_WIDTH_LENGTH);
        $height = Helper::trimData($height,"File height cannot be empty", null, FileModel::MAX_HEIGHT_LENGTH);
        $originalUrl = Helper::trimData($originalUrl);

        $arr = [
            "file_section_name" => $sectionName,
            "file_section_id" => $sectionId,
            "file_url" => $url,
            "file_original_url" => $originalUrl,
            "file_width" => $width,
            "file_height" => $height,
            "file_type" => $type
        ];

        return $this->addRow("file", $arr);
    }

    public function deleteFileByPath($path) {
        return unlink($_SERVER["DOCUMENT_ROOT"] . $path);
    }

    public function isUploadImages($inputName){
        return (bool) $_FILES[$inputName]['name'][0] || (bool) $_FILES[$inputName]['type'][0];
    }

    public function getNumOfUploadImages($inputName,$allowedFileType=['image','pdf']) {
        $count = 0;
        if(!$this->isUploadImages($inputName)){
            return 0;
        }else{
            $validateFileTypes = [];
            foreach ($allowedFileType as $type){
                if($type == 'image'){
                    $validateFileTypes = ['image/gif','image/png','image/jpeg','image/pjpeg'];
                }
                if($type == 'pdf'){
                    $validateFileTypes[] = 'application/pdf';
                }
            }
            $files = $_FILES[$inputName];
            $total = count(array_filter($files['tmp_name']));
            if ($total === 0) return 0;
            for($i=0; $i<$total; $i++) {
                $tmpFilePath = $_FILES[$inputName]['tmp_name'][$i];
                $size = $_FILES[$inputName]['size'][$i];
                $fileType = $_FILES[$inputName]['type'][$i];
                $fileError = $_FILES[$inputName]["error"][$i];        //错误信息
                //检测文件是否成功获取
                !$fileError > 0 or Helper::throwException("File upload error: " . $fileError);
                //Make sure we have a filepath and size
                if ($tmpFilePath and $size > 0){
                    //检测文件类型是否合法
                    in_array($fileType,$validateFileTypes) or Helper::throwException($inputName." Error : File type can not accept");
                    $count++;
                }
            }
            return $count;
        }
    }

    public function modifyFileAndTableData($table,int $tableId, $fileField, $inputName,array $allowedFileType=['image'],$uploadOriginalImage=false,$maxFileSize=3000000,$maxLength=4000,$isAllowedToDeleteOriginalImg=true){
        try{
            $tableId > 0 or Helper::throwException('No Id');
            $fileArr = [];
            $sql = "SELECT {$fileField} FROM {$table} WHERE {$table}_id = {$tableId}";
            $result = $this->sqltool->getRowBySql($sql);
            $oldFile = $result[$fileField];

            if($_POST[$inputName] == -1){
                $fileArr[$fileField] = "";
                $this->updateRowById($table,$tableId,$fileArr);
                if($oldFile){
                    $this->deleteFileByPath($oldFile);
                }
                return " (Image status: Image was deleted)";
            }else{
                //删除图片
                $fileArr[$fileField] = $this->uploadFile($inputName,$uploadOriginalImage,$allowedFileType,false,null,null,$maxFileSize,$maxLength)[0]['url'];
                $this->updateRowById($table,$tableId,$fileArr);
                if($oldFile && $isAllowedToDeleteOriginalImg){
                    $this->deleteFileByPath($oldFile);
                }
                return " (Image status: Success update)";
            }
        }catch (\Exception $e){
            $this->deleteFileByPath($fileArr[$fileField]);
            return " (Image status: {$e->getMessage()})";
        }
    }

    /**
     * Upload Images
     * @param string $inputName the image input tag name. <br/>
     * For instance: <br/>
     * `< input name="image[]" type="file" / >`, the $inputName is "image".
     * @param bool $uploadOriginalImage generate thumbnail image or not
     * @param array $allowedFileType
     * @param bool $storeInDB store image info into database image table or not
     * @param string|null $sectionName associated database table name
     * @param int|null $sectionId associated database table row id
     * @param int $maxFileSize maximum allowed file size
     * @param int $maxLength maximum allowed image side in pixel
     * @param int $maxOriginalFileSize maximum allowed thumbnail file size
     * @param int $maxOriginalImageLength maximum allowed thumbnail image side in pixel
     * @return array all uploaded images information
     * @throws Exception
     */
    public function uploadFile(
        string $inputName,
        bool $uploadOriginalImage = false,
        array $allowedFileType = ['image','pdf'],
        bool $storeInDB = true,
        $sectionName = null,
        $sectionId = null,
        int $maxFileSize = FileModel::MAX_UPLOAD_FILE_SIZE,
        int $maxLength = FileModel::MAX_UPLOAD_IMAGE_SIDE,
        int $maxOriginalFileSize = FileModel::MAX_UPLOAD_ORIGINAL_FILE_SIZE,
        int $maxOriginalImageLength = FileModel::MAX_UPLOAD_ORIGINAL_IMAGE_SIDE
    ) {
        // make sure if user wants to store into db, sectionName and sectionId must be provided
        if($storeInDB) {
            ($sectionName && $sectionId) or Helper::throwException('Invalid sectionName or sectionId');
        }
        $count = $this->getNumOfUploadImages($inputName,$allowedFileType) or Helper::throwException("No image to upload.");   //获取文件上传数量
        $file = $_FILES[$inputName];
        $results = [];  // successfully uploaded images information

        //文件路径
        $thumbnailUploadsDir = UPLOAD_FOLDER."/thumbnail/";
        $rawUploadsDir = UPLOAD_FOLDER."/raw/";
        $pdfUploadsDir = UPLOAD_FOLDER."/pdf/";

        //检查目录的读写权限
        $folderArr = [$thumbnailUploadsDir,$rawUploadsDir,$pdfUploadsDir];
        foreach ($folderArr as $folder){
            if(!is_writable($_SERVER['DOCUMENT_ROOT'].$folder)){
                mkdir($_SERVER['DOCUMENT_ROOT'].$folder, 0777);
                chmod($_SERVER['DOCUMENT_ROOT'].$folder, 0777) or Helper::throwException("文件夹权限修改失败:".$folder);
            }
        }

        for ($i = 0; $i < $count; $i++) {
            //初始化参数
            $fileTmpName = $file['tmp_name'][$i];   //临时文件
            $fileName = $file['name'][$i] ?: $fileTmpName . '.jpg';
            $fileType = $file["type"][$i];          //文件类型
            $fileSize = $file["size"][$i];          //文件大小

            // get file extension
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);

            //配置上传文件名
            $newFileName = $_COOKIE['cc_id']."_".uniqid(time(), true).".".$ext;

            if ($fileType == "application/pdf") {
                //上传pdf文件
                $item = ["url" => $pdfUploadsDir.$newFileName,'type'=>'pdf'];
                move_uploaded_file($fileTmpName, $_SERVER['DOCUMENT_ROOT'].$pdfUploadsDir.$newFileName);
            }else{
                //上传图片
                $thumbnailImageName = $thumbnailUploadsDir.$newFileName;
                $rawImageName = $rawUploadsDir.$newFileName;
                list($width, $height) = getimagesize($fileTmpName);
                $item = ["url" => $thumbnailImageName,'type'=>'img'];
                //上传缩略图
                $dimension = $this->calculateImageDimension($fileSize, $width, $height, $maxFileSize, $maxLength);
                $newWidth = $dimension['width'];
                $newHeight = $dimension['height'];
                $this->resizeImageAndSave($fileTmpName,$thumbnailImageName,$fileType, $fileSize, $maxFileSize, $newWidth, $newHeight, $width, $height);
                $item = array_merge(["width" => $newWidth, "height" => $newHeight], $item);
                //上传大图
                if ($uploadOriginalImage) {
                    $originalDimension = $this->calculateImageDimension($fileSize, $width, $height, $maxOriginalFileSize, $maxOriginalImageLength);
                    $this->resizeImageAndSave($fileTmpName,$rawImageName,$fileType, $fileSize, $maxOriginalFileSize, $originalDimension['width'], $originalDimension['height'], $width, $height);
                    $item['originalUrl'] = $rawImageName;
                }
            }
            array_push($results, $item);
        }

        // store info to image table (if not in db, use 'none' and 0 as section name and id)
        if($storeInDB){
            foreach ($results as $k => $result) {
                $newId = $this->addFile($sectionName ?: "none", $sectionId ?: 0, $result['url'], $result['width'], $result['height'], $result['originalUrl'],$result['type']);
                $results[$k]['file_id'] = $newId;
            }
        }
        return $results;
    }

    private function resizeImageAndSave($originalFileName, $newFileName, $fileType, $fileSize, $maxFileSize, $newWidth, $newHeight, $originalWidth, $originalHeight) {
        if($originalWidth>$newWidth && $originalHeight>$newHeight){
            $src_im = null;
            $dst_im = null;
            //压缩
            ini_set('memory_limit','4000M');
            if ($fileType == "image/jpeg") {
                //压缩JPG
                $src_im = imagecreatefromjpeg($originalFileName);
                if (function_exists("imagecopyresampled")) {
                    //高保真压缩
                    $dst_im = imagecreatetruecolor($newWidth, $newHeight);
                    imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
                } else {
                    //快速压缩
                    $dst_im = imagecreate($newWidth, $newHeight);
                    imagecopyresized($dst_im, $src_im, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
                }
                imagejpeg($dst_im, $_SERVER['DOCUMENT_ROOT'].$newFileName, 100) or Helper::throwException("图片存储失败:" . $newFileName . "newwidth:" . $newWidth . "newheight:" . $newHeight . "width:" . $newWidth . "height:" . $newHeight);     //输出压缩后的图片
            } else if ($fileType == "image/png") {
                //压缩PNG
                $src_im = imagecreatefrompng($originalFileName);
                $dst_im = imagecreatetruecolor($newWidth, $newHeight);
                $alpha = imagecolorallocatealpha($dst_im, 0, 0, 0, 127);
                imagefill($dst_im, 0, 0, $alpha);
                imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
                imagesavealpha($dst_im,true);
                imagepng($dst_im, $_SERVER['DOCUMENT_ROOT'].$newFileName) or Helper::throwException("图片存储失败:" . $newFileName . "newwidth:" . $newWidth . "newheight:" . $newHeight . "width:" . $newWidth . "height:" . $newHeight);     //输出压缩后的图片
            } else if ($fileType == "image/gif") {
                move_uploaded_file($originalFileName, $_SERVER['DOCUMENT_ROOT'].$newFileName) or Helper::throwException("图片存储失败:" . $newFileName);
//            //压缩GIF
//            $src_im = imagecreatefromgif($originalFileName);
//            $dst_im = imagecreatetruecolor($newWidth, $newHeight);
//            imagealphablending($dst_im, false);
//            imagesavealpha($dst_im,true);
//            $transparent = imagecolorallocatealpha($dst_im, 255, 255, 255, 127);
//            imagefilledrectangle($dst_im, 0, 0, $newWidth, $newHeight, $transparent);
//            imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
//            imagegif($dst_im, $_SERVER['DOCUMENT_ROOT'].$newFileName) or Helper::throwException("图片存储失败:" . $newFileName . "newwidth:" . $newWidth . "newheight:" . $newHeight . "width:" . $newWidth . "height:" . $newHeight);     //输出压缩后的图片
            } else {
                Helper::throwException("Invalid Image format.");
            }
            imagedestroy($src_im);  //销毁缓存
            imagedestroy($dst_im);  //销毁缓存
            ini_set('memory_limit','256M');
        }else{
            move_uploaded_file($originalFileName, $_SERVER['DOCUMENT_ROOT'].$newFileName) or Helper::throwException("图片存储失败:" . $newFileName);
        }
    }


    /**
     * =======================================================
     * =======================================================
     * =======================================================
     * =======================================================
     * =======================================================
     * =======================================================
     * =======================================================
     * =======================================================
     * =======================================================
     * =======================================================
     * =======================================================
     * =======================================================
     * =======================================================
     * =======================================================
     * =======================================================
     */

    /**
     * Get image(s)
     * @param array $query
     * [
     *     'id' => 1,
     *     'sectionName" => 'abc',
     *     'sectionId' => 1,
     *     'status' => 1,
     *     'row' => 1,
     *     'size' => 20
     * ]
     * @return array
     * @throws Exception
     */
    public function getImage(array $query = []) {
        $sql = "SELECT * FROM file";
        $conditions = [];
        $params = [];
        $id = Helper::trimData($query['id'], null, null, 11);
        $sectionName = Helper::trimData($query['sectionName'], null, null, self::MAX_SECTION_NAME_LENGTH);
        $sectionId = Helper::trimData($query['sectionId'], null, null, 11);
        $status = 1;
        if ($query['status'] !== null) {
            $status = Helper::trimData($query['status']);
        }
        $conditions[] = "file_status IN (".Helper::convertIDArrayToString($status).")";

        if ($id) {
            $conditions[] = "file_id IN (".Helper::convertIDArrayToString($id).")";
        }
        if ($sectionName) {
            $conditions[] = "file_section_name = ?";
            $params[] = $sectionName;
            if ($sectionId) {
                $conditions[] = "file_section_id IN (".Helper::convertIDArrayToString($sectionId).")";
            }
        }

        if ($conditions) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        // configure orders
        $order = [];
        $orderQuery = $query['order'];
        if (is_array($orderQuery)) {
            foreach($orderQuery as $item) {
                foreach($item as $k => $v) {
                    if ($k === "postTime") {
                        $direct = $v === 'DESC' ? 'DESC' : 'ASC';
                        $order[] = "file_post_time {$direct}";
                    }
                    if ($k === "id") {
                        $direct = $v === 'DESC' ? 'DESC' : 'ASC';
                        $order[] = "file_id {$direct}";
                    }
                }
            }
        } else {
            $order[] = 'file_id DESC';
        }

        // combine sorting sql string
        if (sizeof($order) > 0) {
            $sql .= " ORDER BY " . implode(",", $order);
        }

        $row = (bool) Helper::trimData($query['row']) ?: false;
        if ($row) {
            return $this->sqltool->getRowBySql($sql, $params);
        } else {
            $pageSize = (int) Helper::trimData($query['size']) ?: 80;
            if ($query['pagination']) {
                return $this->getListWithPage('file', $sql, $params, $pageSize);
            } else {
                $sql .= " LIMIT {$pageSize}";
                return $this->sqltool->getListBySql($sql, $params);
            }
        }
    }

    /**
     * Update image status by id
     * @param int $status
     * @param int|array $id
     * @return null
     * @throws Exception
     */
    private function updateImageStatusById(int $status, $id) {
        $id = Helper::trimData($id, "Image id cannot be empty.", null, 11);
        $status === 0 || $status === 1 || Helper::throwException('Invalid image status.');
        $sql = "UPDATE file SET file_status = {$status} WHERE file_id IN (".Helper::convertIDArrayToString($id).")";
        $this->sqltool->query($sql);
        return $this->sqltool->affectedRows;
    }

    /**
     * Restore deleted image(s) from database by id(s)
     * @param $id
     * @return null
     * @throws Exception
     */
    public function restoreImageById($id) {
        return $this->updateImageStatusById(1, $id);
    }

    /**
     * [Logically] Delete image(s) by image id(s)
     * @param $id
     * @return int
     * @throws Exception
     */
    public function deleteImageById($id) {
        return $this->updateImageStatusById(0, $id);

    }



    /**
     * [Permanently] Delete image(s) by image id(s)
     * @param $id
     * @param bool $deleteFile delete file from s3
     * @return int
     * @throws Exception
     */
    public function purgeImageById($id, bool $deleteFile=true) {
        $id = Helper::trimData($id, "Image id cannot be empty.", null, 11);
        // get images from database
        $sql = "SELECT file_id, image_url, image_original_url FROM image WHERE file_id IN (".Helper::convertIDArrayToString($id).")";
        $result = $this->sqltool->getListBySql($sql) or Helper::throwException("No image found.", 404);
        $num = $this->deleteByIDsReally('image', $id);
        $deleteFile && $this->deleteImagesFromS3($result);
        return $num;
    }

    /**
     * update image status by section name and section id
     * @param int $status 0|1
     * @param string $sectionName
     * @param int|array $sectionId
     * @return null
     * @throws Exception
     */
    private function updateImageStatusBySectionNameAndId(int $status, string $sectionName, $sectionId) {
        $sectionName = Helper::trimData($sectionName, "Section name cannot be empty.", null, FileModel::MAX_SECTION_NAME_LENGTH);
        $sectionId = Helper::trimData($sectionId, "Section id cannot be empty.", null, 11);
        $status === 0 || $status === 1 || Helper::throwException('Invalid image status.');
        $sql = "UPDATE image SET file_status = {$status} WHERE file_section_name = ? AND file_section_id IN (".Helper::convertIDArrayToString($sectionId).")";
        $this->sqltool->query($sql, [$sectionName]);
        return $this->sqltool->affectedRows;
    }

    /**
     * Restore image(s) by given section name and section id(s)
     * @param string $sectionName
     * @param int|array $sectionId
     * @return null
     * @throws Exception
     */
    public function restoreImageBySectionNameAndId(string $sectionName, $sectionId) {
        return $this->updateImageStatusBySectionNameAndId(1, $sectionName, $sectionId);
    }

    /**
     * [Logically] Delete image(s) by given section name and section id(s)
     * @param string $sectionName
     * @param int|array $sectionId
     * @return false|int
     * @throws Exception
     */
    public function deleteImageBySectionNameAndId(string $sectionName, $sectionId) {
        return $this->updateImageStatusBySectionNameAndId(0, $sectionName, $sectionId);
    }

    /**
     * Modify images from an existing section row
     * @param string $sectionName
     * @param int $sectionId
     * @param $modifiedImageIds
     * @param int $maxNum
     * @param bool $uploadInputName
     * @param bool $generateThumbnail
     * @param bool $enableBlur
     * @param int $maxFileSize
     * @param int $maxLength
     * @param int $maxThumbnailFileSize
     * @param int $maxThumbnailImageLength
     * @return array
     * @throws Exception
     */
    public function modifyImageBySectionNameAndId(
        string $sectionName,
        int $sectionId,
        $modifiedImageIds,
        $maxNum=5,
        $uploadInputName=false,
        bool $uploadOriginalFile = false,
        bool $enableBlur = false,
        int $maxFileSize = FileModel::MAX_UPLOAD_FILE_SIZE,
        int $maxLength = FileModel::MAX_UPLOAD_IMAGE_SIDE,
        int $maxThumbnailFileSize = FileModel::MAX_UPLOAD_ORIGINAL_FILE_SIZE,
        int $maxThumbnailImageLength = FileModel::MAX_UPLOAD_ORIGINAL_IMAGE_SIDE
    ) {
        $result = $this->getImage(['sectionName'=>$sectionName, 'sectionId'=>$sectionId, 'size'=>50]);

        $currentImageIds = [];
        if ($result) {
            $currentImageIds = array_map(function($v){return $v['file_id'];}, $result);
        }
        $modifiedImageIds = array_unique(array_filter($modifiedImageIds, function($v){return ((int) $v) > 0;}));

        // 上传新图片并添加新图id到$imgArr
        if($uploadInputName) {
            $numOfNewImages = $this->getNumOfUploadImages($uploadInputName);
            if ($numOfNewImages > 0) {
                ($numOfNewImages + count($modifiedImageIds) <= $maxNum) or Helper::throwException("Too many images, you can only upload up to {$maxNum} images.");
                $uploadResult = $this->uploadFile(
                    $uploadInputName,
                    $uploadOriginalFile,
                    ['image'],
                    true,
                    $sectionName,
                    $sectionId,
                    $maxFileSize,
                    $maxLength,
                    $maxThumbnailFileSize,
                    $maxThumbnailImageLength
                );
                $newImageIds = array_map(function($v){return $v['file_id'];}, $uploadResult);
                $modifiedImageIds = array_merge($modifiedImageIds, $newImageIds);
            }
        }
        // 检查并删除替代掉的图片
        if($modifiedImageIds != $currentImageIds) {
            $needDeletedIds = [];
            foreach($currentImageIds as $v) {
                if(!in_array($v, $modifiedImageIds)) {
                    $needDeletedIds[] = $v;
                }
            }
            if(count($needDeletedIds) > 0) {
                $this->deleteImageById($needDeletedIds);
            }
        }
        return $modifiedImageIds;
    }

    /**
     * Delete image(s) from S3 by full url with given bucket
     * @param string|array $url
     * @return \Aws\Result
     * @throws Exception
     */
    public function deleteImageFromS3ByUrl($url) {
        if (is_array($url) && sizeof($url) > 0) {
            $url = array_map(function($v){
                if(strpos($v, IMG_SERVER) !== false) {
                    return substr($v, strlen(IMG_SERVER));
                }
                Helper::throwException('Invalid S3 URL.');
            }, $url);
            self::deleteImageDataByUrl($url);
            return $this->awsModel->deleteListOfS3Object(AwsModel::IMAGE_BUCKET, $url);
        } else if(strpos($url, IMG_SERVER) !== false) {
            self::deleteImageDataByUrl($url);
            return $this->awsModel->deleteS3Object(AwsModel::IMAGE_BUCKET, substr($url, strlen(IMG_SERVER)));
        }
        Helper::throwException('Invalid S3 URL.');
    }

    /**
     * 删除数据库的图片
     * @param $url
     * @throws Exception
     */
    private function deleteImageDataByUrl($url){
        $sql = "DELETE FROM image WHERE image_url = ? or image_original_url = ? ";
        $this->sqltool->query($sql,[$url,$url]);
    }


    /**======================**/
    /**   Private Functions  **/
    /**======================**/


    /**
     * Get Image Data in String
     * @param $img
     * @param $fileType
     * @return false|string
     * @throws Exception
     */
    private function getImageData($img, $fileType) {
        $fn = function($image){};
        if ($fileType === 'image/jpeg' || $fileType === 'image/pjpeg') {
            $fn = function($image){return imagejpeg($image);};
        } else if ($fileType === 'image/png') {
            $fn = function($image){return imagepng($image);};
        } else if ($fileType === 'image/gif') {
            $fn = function($image){return imagegif($image);};
        } else {
            Helper::throwException("Invalid file type.");
        }

        ob_start();
        $fn($img);
        $imageData = ob_get_clean() or Helper::throwException("Image failed to upload");
        return $imageData;
    }

    /**
     * Calculate an image size
     * @param $fileSize
     * @param $width
     * @param $height
     * @param $maxFileSize
     * @param $maxLength
     * @return array
     */
    private function calculateImageDimension($fileSize, $width, $height, $maxFileSize, $maxLength) {
        $newWidth = $width;
        $newHeight = $height;
//        ini_set('memory_limit','6000M');
        //需要压缩图片,重新计算图片尺寸
        if ($fileSize > $maxFileSize || ($width>$maxLength || $height>$maxLength)) {
            $imgRatio = sprintf("%.2f", $width / $height);
            if ($imgRatio == 1) {
                //正方形图片
                $newWidth = $maxLength;
                $newHeight = $maxLength;
            } else if ($imgRatio > 1) {
                //横图
                $newWidth = $maxLength;
                $newHeight = floor($maxLength / $imgRatio);
            }else{
                //竖
                $newHeight = $maxLength;
                $newWidth = floor($maxLength * $imgRatio);
            }
        }
        return ["width" => $newWidth, "height" => $newHeight];
    }


    /**
     * Get an unique file key to store in S3
     * @param $fileExt
     * @param string $prefixPath
     * @return string
     */
    private function getUniqueImageName($fileExt, string $prefixPath='thumbnail') {
        return $_SERVER['DOCUMENT_ROOT'].UPLOAD_FOLDER . uniqid(time(), true) . "." . $fileExt;
    }

    /**
     * Delete images from database and aws s3
     * @param $images
     * @throws Exception
     */
    private function deleteImagesFromS3($images) {
        if (is_array($images) && sizeof($images) > 0) {
            $urls = [];
            foreach ($images as $image) {
                array_push($urls, $image['image_url']);
                if ($image['image_original_url']) {
                    array_push($urls, $image['image_original_url']);
                }
            }
            $this->deleteImageFromS3ByUrl($urls);
        }
    }
}