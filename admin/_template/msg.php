<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="/admin/resource/plugins/images/favicon.png">
    <title>Ample Admin Template - The Ultimate Multipurpose admin template</title>
    <!-- Bootstrap Core CSS -->
    <link href="/admin/resource/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/admin/resource/css/style.css" rel="stylesheet">
    <!-- color CSS -->
    <style>
        html,body {background: #edf1f5}
    </style>
</head>

<body>
<div class="row">
    <div class="col-sm-2"></div>
    <div class="col-sm-8">
        <div class="panel panel-default m-t-40">
            <div class="panel-heading">System Notification</div>
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
                    <?php
                    echo "<p>{$msg}</p>";
                    if($url){
                        echo '<a class="btn btn-custom m-t-10" href="'.$url.'">'.$urlTxt.'</a>';
                    }else {
                        echo '<button type="submit" onclick="javascript:history.go(-1);" class="btn btn-info waves-effect waves-light m-t-10">'.$urlTxt.'</button>';
                    }
                    if($secondUrl && $secondUrlText){
                        echo '<a class="btn btn-success m-t-10 m-l-10" href="'.$secondUrl.'">'.$secondUrlText.'</a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-2"></div>
</div>
</body>

</html>