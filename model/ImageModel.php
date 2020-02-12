<?php
namespace model;
use \Model as Model;
use \Helper as Helper;
use \Exception as Exception;

class ImageModel extends Model {

    const MAX_URL_LENGTH = 155;
    const MAX_SIZE_LENGTH = 8;
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

    private $awsModel;

    public function __construct() {
        parent::__construct();
        $this->awsModel = new AwsModel();
    }

    /**
     * Add an image
     * @param string $sectionName
     * @param int $sectionId
     * @param string $url
     * @param int $size
     * @param int $width
     * @param int $height
     * @param string|null $originalUrl
     * @return int
     * @throws Exception
     */
    public function addImage(
        string $sectionName,
        int $sectionId,
        string $url,
        int $size,
        int $width,
        int $height,
        string $originalUrl = null
    ) {
        $sectionName = Helper::trimData($sectionName,"Image section name cannot be empty", null, ImageModel::MAX_SECTION_NAME_LENGTH);
        $sectionId = Helper::trimData($sectionId,"Image section id cannot be empty", null, 11);
        $url = Helper::trimData($url,"Image url cannot be empty", null, ImageModel::MAX_URL_LENGTH);
        $size = Helper::trimData($size,"Image size cannot be empty", null, ImageModel::MAX_SIZE_LENGTH);
        $width = Helper::trimData($width,"Image width cannot be empty", null, ImageModel::MAX_WIDTH_LENGTH);
        $height = Helper::trimData($height,"Image height cannot be empty", null, ImageModel::MAX_HEIGHT_LENGTH);
        $originalUrl = Helper::trimData($originalUrl);
        $publishTime = time();

        $arr = [
            "image_section_name" => $sectionName,
            "image_section_id" => $sectionId,
            "image_url" => $url,
            "image_original_url" => $originalUrl,
            "image_size" => $size,
            "image_width" => $width,
            "image_height" => $height,
            "image_post_time" => $publishTime
        ];

        return $this->addRow("image", $arr);
    }

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
        $sql = "SELECT * FROM image";
        $conditions = [];
        $params = [];
        $id = Helper::trimData($query['id'], null, null, 11);
        $sectionName = Helper::trimData($query['sectionName'], null, null, self::MAX_SECTION_NAME_LENGTH);
        $sectionId = Helper::trimData($query['sectionId'], null, null, 11);
        $status = 1;
        if ($query['status'] !== null) {
            $status = Helper::trimData($query['status']);
        }
        $conditions[] = "image_status IN ({Helper::convertIDArrayToString($status)})";

        if ($id) {
            $conditions[] = "image_id IN ({Helper::convertIDArrayToString($id)})";
        }
        if ($sectionName) {
            $conditions[] = "image_section_name = ?";
            $params[] = $sectionName;
            if ($sectionId) {
                $conditions[] = "image_section_id IN ({Helper::convertIDArrayToString($sectionId)})";
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
                        $order[] = "image_post_time {$direct}";
                    }
                    if ($k === "id") {
                        $direct = $v === 'DESC' ? 'DESC' : 'ASC';
                        $order[] = "image_id {$direct}";
                    }
                }
            }
        } else {
            $order[] = 'image_id DESC';
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
                return $this->getListWithPage('image', $sql, $params, $pageSize);
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
        $sql = "UPDATE image SET image_status = {$status} WHERE image_id IN ({Helper::convertIDArrayToString($id)})";
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
        $sql = "SELECT image_id, image_url, image_original_url FROM image WHERE image_id IN ({Helper::convertIDArrayToString($id)})";
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
        $sectionName = Helper::trimData($sectionName, "Section name cannot be empty.", null, ImageModel::MAX_SECTION_NAME_LENGTH);
        $sectionId = Helper::trimData($sectionId, "Section id cannot be empty.", null, 11);
        $status === 0 || $status === 1 || Helper::throwException('Invalid image status.');
        $sql = "UPDATE image SET image_status = {$status} WHERE image_section_name = ? AND image_section_id IN ({Helper::convertIDArrayToString($sectionId)})";
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
     * Upload Images
     * @param string $inputName the image input tag name. <br/>
     * For instance: <br/>
     * `< input name="image[]" type="file" / >`, the $inputName is "image".
     * @param bool $uploadOriginalImage generate thumbnail image or not
     * @param bool $enableBlur blur image or not
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
    public function uploadImage(
        string $inputName,
        bool $uploadOriginalImage = false,
        bool $enableBlur = false,
        bool $storeInDB = true,
        $sectionName = null,
        $sectionId = null,
        int $maxFileSize = ImageModel::MAX_UPLOAD_FILE_SIZE,
        int $maxLength = ImageModel::MAX_UPLOAD_IMAGE_SIDE,
        int $maxOriginalFileSize = ImageModel::MAX_UPLOAD_ORIGINAL_FILE_SIZE,
        int $maxOriginalImageLength = ImageModel::MAX_UPLOAD_ORIGINAL_IMAGE_SIDE
    ) {
        // make sure if user wants to store into db, sectionName and sectionId must be provided
        if($storeInDB) {
            ($sectionName && $sectionId) or Helper::throwException('Invalid sectionName or sectionId');
        }
        $count = $this->getNumOfUploadImages($inputName) or Helper::throwException("No image to upload.");   //获取文件上传数量
        $file = $_FILES[$inputName];
        $results = [];  // successfully uploaded images information
        for ($i = 0; $i < $count; $i++) {
            //初始化参数
            $fileTmpName = $file['tmp_name'][$i];   //临时文件
            $fileName = $file['name'][$i] ?: $fileTmpName . '.jpg';
            $fileType = $file["type"][$i];          //文件类型
            $fileSize = $file["size"][$i];          //文件大小

            // get file extension
//            $pathInfo = pathinfo($fileTmpName);
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);

//            // convert image to jpg
//            $tmpJPGFileName = $pathInfo['dirname'] . '/reformatted_' . $pathInfo['filename'] . '.jpeg';
//            $this->convertImage($fileTmpName, $tmpJPGFileName) or Helper::throwException('Image failed to upload.', 500);
//            $ext = "jpg";
//            $fileTmpName = $tmpJPGFileName;
//            $fileType = mime_content_type($tmpJPGFileName);
//            $fileSize = filesize($tmpJPGFileName);

            // check if new file name exists in S3
            $newFileName = ImageModel::getUniqueImageName($ext);

            // Calculate allowed new image width and height
            list($width, $height) = getimagesize($fileTmpName);
            $item = ["url" => IMG_SERVER . $newFileName];
            if ($width < $maxLength && $height < $maxLength && $fileSize < $maxFileSize) {
                $this->uploadImageFile($newFileName, $fileTmpName, ['type'=>$fileType,'size'=>$fileSize,'width'=>$width,'height'=>$height]);
                $item = array_merge(["size" => $fileSize, "width" => $width, "height" => $height], $item);
            } else {
                // too large, need resize
                $dimension = $this->calculateImageDimension($fileSize, $width, $height, $maxFileSize, $maxLength);
                $newWidth = $dimension['width'];
                $newHeight = $dimension['height'];

//                echo $newWidth."<br>";
//                echo $newHeight."<br>";

                // resize image into string
                $imageData = $this->resizeImage($fileTmpName, $fileType, $fileSize, $maxFileSize, $newWidth, $newHeight, $width, $height, $enableBlur);
                $imageSize = mb_strlen($imageData, '8bit');
                $this->uploadImageData($newFileName, $imageData, $fileType);
                $item = array_merge(["size" => $imageSize, "width" => $newWidth, "height" => $newHeight], $item);
            }

            // Upload original image if required
            if ($uploadOriginalImage) {
                $newOriginalFileName = ImageModel::getUniqueImageName($ext, 'original');
                if ($width < $maxOriginalImageLength && $height < $maxOriginalFileSize && $fileSize < $maxFileSize) {
                    $this->uploadImageFile($newOriginalFileName, $fileTmpName, ['type'=>$fileType,'size'=>$fileSize,'width'=>$width,'height'=>$height]);
                } else {
                    // too large, need resize
                    $originalDimension = $this->calculateImageDimension($fileSize, $width, $height, $maxOriginalFileSize, $maxOriginalImageLength);
                    $originalImageData = $this->resizeImage($fileTmpName, $fileType, $fileSize, $maxOriginalFileSize, $originalDimension['width'], $originalDimension['height'], $width, $height, $enableBlur);
                    $this->uploadImageData($newOriginalFileName, $originalImageData, $fileType);
                }
                $item['originalUrl'] = IMG_SERVER . $newOriginalFileName;
            }
            array_push($results, $item);
        }

        // store info to image table (if not in db, use 'none' and 0 as section name and id)
        foreach ($results as $k => $result) {
            $newId = $this->addImage($sectionName ?: "none", $sectionId ?: 0, $result['url'], $result['size'], $result['width'], $result['height'], $result['originalUrl']);
            $results[$k]['image_id'] = $newId;
        }
        return $results;
    }

    /**
     * Upload an Image file to S3 Image bucket
     * @param string $key s3 key
     * @param string $path local file absolute path
     * @param array $metadata
     * @throws Exception
     */
    private function uploadImageFile(string $key, string $path, $metadata) {
        $ops = ['ACL' => 'public-read', 'SourceFile' => $path, 'Metadata'=> $metadata, 'ContentType' => $metadata['type']];
        $result = $this->awsModel->putS3Object(AwsModel::IMAGE_BUCKET, $key, null, $ops);
        $result->hasKey('ObjectURL') or Helper::throwException("Internal Error.", 500);
    }

    private function uploadImageData(string $key, string $imageData, string $fileType) {
        $ops = ['ACL' => 'public-read', 'ContentType' => $fileType, 'Body'=> $imageData];
        $result = $this->awsModel->putS3Object(AwsModel::IMAGE_BUCKET, $key, null, $ops);
        $result->hasKey('ObjectURL') or Helper::throwException("Internal Error.", 500);
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
        int $maxFileSize = ImageModel::MAX_UPLOAD_FILE_SIZE,
        int $maxLength = ImageModel::MAX_UPLOAD_IMAGE_SIDE,
        int $maxThumbnailFileSize = ImageModel::MAX_UPLOAD_ORIGINAL_FILE_SIZE,
        int $maxThumbnailImageLength = ImageModel::MAX_UPLOAD_ORIGINAL_IMAGE_SIDE
    ) {
        $result = $this->getImage(['sectionName'=>$sectionName, 'sectionId'=>$sectionId, 'size'=>50]);

        $currentImageIds = [];
        if ($result) {
            $currentImageIds = array_map(function($v){return $v['image_id'];}, $result);
        }
        $modifiedImageIds = array_unique(array_filter($modifiedImageIds, function($v){return ((int) $v) > 0;}));

        // 上传新图片并添加新图id到$imgArr
        if($uploadInputName) {
            $numOfNewImages = $this->getNumOfUploadImages($uploadInputName);
            if ($numOfNewImages > 0) {
                ($numOfNewImages + count($modifiedImageIds) <= $maxNum) or Helper::throwException("Too many images, you can only upload up to {$maxNum} images.");
                $uploadResult = $this->uploadImage(
                    $uploadInputName,
                    $uploadOriginalFile,
                    $enableBlur,
                    true,
                    $sectionName,
                    $sectionId,
                    $maxFileSize,
                    $maxLength,
                    $maxThumbnailFileSize,
                    $maxThumbnailImageLength
                );
                $newImageIds = array_map(function($v){return $v['image_id'];}, $uploadResult);
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

    public function isUploadImages($inputName){
        return (bool) $_FILES[$inputName]['name'][0] || (bool) $_FILES[$inputName]['type'][0];
    }

    /**
     * 获取上传图片数量, 同时检测是否所有上传图片成功获取且合法
     * @param $inputName
     * @return int
     * @throws Exception
     *      upload_error 如果任何文件上传出错
     *      invalid_format 如果任何文件类型不符合要求 (png | jpeg | gif)
     */
    public function getNumOfUploadImages($inputName) {
        $count = 0;
        if(!$this->isUploadImages($inputName)){
            return 0;
        }else{
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
                    (($fileType == "image/gif") || ($fileType == "image/png") || ($fileType == "image/jpeg") || ($fileType == "image/pjpeg")) or Helper::throwException("Only support jpg|png|gif format");
                    $count++;
                }
            }
            return $count;
        }
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
        //需要压缩图片,重新计算图片尺寸
        if ($fileSize > $maxFileSize && ($width>$maxLength || $height>$maxLength)) {
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
     * Convert image to jpg
     * @param $originalImage
     * @param $outputImage
     * @return int
     */
    function convertImage($originalImage, $outputImage) {

        switch (exif_imagetype($originalImage)) {
            case IMAGETYPE_PNG:
                $imageTmp=imagecreatefrompng($originalImage);
                break;
            case IMAGETYPE_JPEG:
                $imageTmp=imagecreatefromjpeg($originalImage);
                break;
            case IMAGETYPE_GIF:
                $imageTmp=imagecreatefromgif($originalImage);
                break;
            case IMAGETYPE_BMP:
                $imageTmp=imagecreatefrombmp($originalImage);
                break;
            // Defaults to JPG
            default:
                $imageTmp=imagecreatefromjpeg($originalImage);
                break;
        }

        // quality is a value from 0 (worst) to 100 (best)
        imagejpeg($imageTmp, $outputImage, 90);
        imagedestroy($imageTmp);
        return 1;
    }

    /**
     * Resize an image
     * @param $fileTmpName
     * @param $fileType
     * @param $fileSize
     * @param $maxFileSize
     * @param $newWidth
     * @param $newHeight
     * @param $originalWidth
     * @param $originalHeight
     * @param $enableBlur
     * @return false|string resized image data in string
     * @throws Exception
     */
    private function resizeImage($fileTmpName, $fileType, $fileSize, $maxFileSize, $newWidth, $newHeight, $originalWidth, $originalHeight, $enableBlur) {
        $src_im = null;
        $dst_im = null;
        //压缩
        if ($fileType == "image/jpeg") {
            //压缩JPG
            $src_im = imagecreatefromjpeg($fileTmpName);
            if (function_exists("imagecopyresampled")) {
                //高保真压缩
                $dst_im = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
            } else {
                //快速压缩
                $dst_im = imagecreate($newWidth, $newHeight);
                imagecopyresized($dst_im, $src_im, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
            }
        } else if ($fileType == "image/png") {
            //压缩PNG
            $src_im = imagecreatefrompng($fileTmpName);
            $dst_im = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        } else if ($fileType == "image/gif") {
            if ($fileSize > ImageModel::MAX_UPLOAD_FILE_SIZE) {
                Helper::throwException("Only support gif upload size less than " . ($maxFileSize / 1000000) . "MB");
            }
            //压缩GIF
            $src_im = imagecreatefromgif($fileTmpName);
            $dst_im = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($dst_im, false);
            imagesavealpha($dst_im,true);
            $transparent = imagecolorallocatealpha($dst_im, 255, 255, 255, 127);
            imagefilledrectangle($dst_im, 0, 0, $newWidth, $newHeight, $transparent);
            imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        } else {
            Helper::throwException("Invalid format.");
        }

        if ($enableBlur) {
            //模糊压缩过的图片
            for ($i=0;$i<60;$i++)
                imageconvolution($dst_im, ImageModel::GAUSSIAN_KERNEL, 16, 0);
        }

        imagedestroy($src_im);  //销毁缓存
        $imageData = $this->getImageData($dst_im, $fileType);
        imagedestroy($dst_im);  //销毁缓存
        return $imageData;
    }

    /**
     * Get an unique file key to store in S3
     * @param $fileExt
     * @param string $prefixPath
     * @return string
     */
    private function getUniqueImageName($fileExt, string $prefixPath='thumbnail') {
        while (true) {
            $newFileName = AwsModel::getS3KeyPrefix() . $prefixPath . "/" . uniqid(time(), true) . "." . $fileExt;
            if (!$this->awsModel->isS3ObjectExisted(AwsModel::IMAGE_BUCKET, $newFileName)) {
                return $newFileName;
            }
        }
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