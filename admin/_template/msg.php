
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
    <!-- Menu CSS -->
    <link href="/admin/resource/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css" rel="stylesheet">
    <!-- animation CSS -->
    <link href="/admin/resource/css/animate.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/admin/resource/css/style.css" rel="stylesheet">
    <!-- color CSS -->
    <link href="/admin/resource/css/colors/megna-dark.css" id="theme" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- jQuery -->
    <script src="/admin/resource/plugins/bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="/admin/resource/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Menu Plugin JavaScript -->
    <script src="/admin/resource/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js"></script>
    <!--slimscroll JavaScript -->
    <script src="/admin/resource/js/jquery.slimscroll.js"></script>
    <script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js?autoload=true&amp;lang=css" defer="defer"></script>
    <!--Wave Effects -->
    <script src="/admin/resource/js/waves.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="/admin/resource/js/custom.min.js"></script>
    <!--Style Switcher -->
    <script src="/admin/resource/plugins/bower_components/styleswitcher/jQuery.style.switcher.js"></script>
    <style>
        html,body {background: #edf1f5}
    </style>
</head>

<body>
<div class="row">
    <div class="col-sm-2"></div>
    <div class="col-sm-8">
        <div class="panel panel-default m-t-40">
            <div class="panel-heading">System Notification
                <div class="panel-action"><a href="#" data-perform="panel-collapse"><i class="ti-minus"></i></a> <a href="#" data-perform="panel-dismiss"><i class="ti-close"></i></a></div>
            </div>
            <div class="panel-wrapper collapse in" aria-expanded="true">
                <div class="panel-body">
                    <?php
                    echo "<p>{$msg}</p>";
                    if($url){
                        echo '<a class="btn btn-custom m-t-10" href="'.$url.'">'.$urlTxt.'</a>';
                    }else {
                        echo '<button type="submit" onclick="javascript:history.go(-1);" class="btn btn-info waves-effect waves-light m-t-10">'.$urlTxt.'</button>';
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