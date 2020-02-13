<?php
ob_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/commonServices/config.php";
try {
    $userModel = new \model\UserModel();
    $currentUserId = $userModel->getCurrentUserId();
} catch (Exception $e) {
    Helper::echoJson($e->getCode(), $e->getMessage(),null,null,null,'/admin/adminLogin.php');
    die();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="/admin/resource/plugins/images/favicon.png">
    <title>WoodWorth ERP System</title>
    <link href="/admin/resource/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/admin/resource/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css" rel="stylesheet">
    <link href="/admin/resource/css/animate.css" rel="stylesheet">
    <link href="/admin/resource/css/style.css" rel="stylesheet">
    <link href="/admin/resource/css/colors/megna-dark.css" id="theme" rel="stylesheet">
    <script src="/admin/resource/plugins/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="/admin/resource/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="/admin/resource/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js"></script>
    <script src="/admin/resource/js/jquery.slimscroll.js"></script>
    <script src="/admin/resource/js/run_prettify.js?autoload=true&amp;lang=css" defer="defer"></script>
    <script src="/admin/resource/js/waves.js"></script>
    <script src="/admin/resource/js/custom.min.js"></script>
    <script src="/admin/resource/plugins/bower_components/styleswitcher/jQuery.style.switcher.js"></script>
    <link href="/admin/resource/plugins/bower_components/multiselect/css/multi-select.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="/admin/resource/plugins/bower_components/multiselect/js/jquery.multi-select.js"></script>
    <link href="/admin/resource/plugins/bower_components/dropify/dist/css/dropify.css" rel="stylesheet">
    <script src="/admin/resource/plugins/bower_components/dropify/dist/js/dropify.js"></script>
    <link href="/admin/resource/plugins/bower_components/custom-select/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <script src="/admin/resource/plugins/bower_components/custom-select/dist/js/select2.full.min.js" type="text/javascript"></script>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="/admin/resource/js/erp-custom.js"></script>
    <link href="/admin/resource/css/erp-custom.css" rel="stylesheet">
</head>

<body class="fix-header">
<div id="adminAlert" class="myadmin-alert alert-success myadmin-alert-top alerttop"> <i class="ti-announcement m-r-10"></i><span>This is an example top alert. You can edit what u wish.</span> <a href="#" class="closed">&times;</a> </div>
<!-- ============================================================== -->
<!-- Wrapper -->
<!-- ============================================================== -->
<div id="wrapper">
    <!-- ============================================================== -->
    <!-- Topbar header - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <nav class="navbar navbar-default navbar-static-top m-b-0">
        <div class="navbar-header">
            <div class="top-left-part">
                <!-- Logo -->
                <a class="logo" href="/admin/adminIndex.php">
                    <!-- Logo icon image, you can use font-icon also --><b>
                        <!--This is dark logo icon--><img src="/admin/resource/plugins/images/admin-logo.png" alt="home" class="dark-logo" /><!--This is light logo icon--><img src="/admin/resource/plugins/images/admin-logo-dark.png" alt="home" class="light-logo" />
                    </b>
                    <!-- Logo text image you can use text also --><span class="hidden-xs">
                        <!--This is dark logo text--><img src="/admin/resource/plugins/images/admin-text.png" alt="home" class="dark-logo" /><!--This is light logo text--><img src="/admin/resource/plugins/images/admin-text-dark.png" alt="home" class="light-logo" />
                     </span> </a>
            </div>
            <!-- /Logo -->
            <!-- Search input and Toggle icon -->
            <ul class="nav navbar-top-links navbar-left">
                <li><a href="javascript:void(0)" class="open-close waves-effect waves-light"><i class="ti-menu"></i></a></li>
            </ul>
        </div>
        <!-- /.navbar-header -->
        <!-- /.navbar-top-links -->
        <!-- /.navbar-static-side -->
    </nav>
    <!-- End Top Navigation -->
    <!-- ============================================================== -->
    <!-- Left Sidebar - style you can find in sidebar.scss  -->
    <!-- ============================================================== -->
    <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav slimscrollsidebar">
            <div class="sidebar-head">
                <h3><span class="fa-fw open-close"><i class="ti-close ti-menu"></i></span> <span class="hide-menu">Navigation</span></h3> </div>
                <ul class="nav" id="side-menu">
                    <li class="user-pro">
                        <a href="javascript:void(0)" class="waves-effect" style="cursor: default"><img src="/admin/resource/img/head-default.png" alt="user-img" class="img-circle"> <span class="hide-menu"> Steve Gection</span></a>
                    </li>
                    <li><a href="/admin/adminIndex.php" class="waves-effect"><i class="mdi mdi-av-timer fa-fw"></i> <span class="hide-menu">Dashboard</span></a></li>
                    <?php if($userModel->isCurrentUserHasAuthority("USER","GET_LIST")){?>
                        <li><a href="/admin/user/index.php" class="waves-effect"><i class="mdi mdi-account-circle  fa-fw"></i> <span class="hide-menu">User<span class="fa arrow"></span></span></a>
                            <ul class="nav nav-second-level">
                                <li><a href="/admin/user/index.php?s=user-list"><i class="mdi mdi-account-multiple fa-fw"></i><span class="hide-menu">All User</span></a></li>
                            </ul>
                        </li>
                    <?php } ?>
                    <?php if($userModel->isCurrentUserHasAuthority("COMPANY","GET_LIST")){?>
                        <li><a href="/admin/company/index.php" class="waves-effect"><i class="mdi mdi-city fa-fw"></i> <span class="hide-menu">Company</span></a></li>
                    <?php } ?>
                    <li><a href="/admin/system/index.php" class="waves-effect"><i class="mdi mdi-settings-box fa-fw"></i> <span class="hide-menu">Setting<span class="fa arrow"></span></span></a>
                        <ul class="nav nav-second-level">
                            <?php if($userModel->isCurrentUserHasAuthority('SYSTEM_SETTING','USER_CATEGORY')){?>
                                <li><a href="/admin/system/index.php?s=system-user-category-list"><i class="mdi mdi-certificate fa-fw"></i><span class="hide-menu">User Authority</span></a></li>
                            <?php }?>
                            <li><a href="/admin/system/index.php?s=system-my-profile"><i class="mdi mdi-account-settings-variant fa-fw"></i><span class="hide-menu">My Profile</span></a></li>
                        </ul>
                    </li>
                    <li class="devider"></li>
                    <li><a href="/restAPI/userController.php?action=logout" class="waves-effect"><i class="mdi mdi-logout fa-fw"></i> <span class="hide-menu">Log out</span></a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- End Left Sidebar -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Page Content -->
    <!-- ============================================================== -->
    <div id="page-wrapper">
        <div class="container-fluid">
